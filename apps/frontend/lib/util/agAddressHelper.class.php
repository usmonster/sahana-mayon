<?php
/**
 *
 * Provides bulk-address manipulation methods
 *
 * PHP Version 5
 *
 * LICENSE: This source file is subject to LGPLv3.0 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 *
 * @author Chad Heuschober, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 *
 * @property string $lineDelimiter The Delimiter used between lines of an address.
 * @property boolean $enforceComplete Boolean determining whether or not only complete addreses will
 * be returned by the address to string methods.
 * @property boolean $enforceLineNumber Boolean that determines whether or not line numbers will be
 * strictly enforced, sometimes causing blank lines to appear between or before lines with values.
 * @param boolean $checkValuesForCompleteness Boolean parameter to control whether or not the
 * addressIsComplete method only searches for keys (FALSE) or checks the actual values for
 * zero-length strings and/or nulls.
 *
 * @property array $_addressIds A single-dimension array of address id values.
 * @property string $_globalDefaultAddressStandard The value of the default standard address global
 * parameter.
 * @property string $_globalDefaultAddressGeoType The value of the default address geo type as
 * represented in global parameters.
 * @property integer $_startingLineNumber The value of the default starting line number for address
 * strings (eg, 1 or 0).
 * @property array $_addressFormatComponents A constructed array of the default address formatting
 * components. This is directly tied to the instantiated classes' $_returnStandardId.
 * @property array $_addressFormatRequired A constructed array containing just the required address
 * elements for the current standard.
 * @property integer $_returnStandardId The address standard currently in-use by this class.
 * Defaults to the value provided by the global parameter.
 * @property string $_addressGeoTypeId The value of the default address geo type. Defaults to the
 * value provided by the global parameter but not instantiated by default (only if geo is used).
 */

class agAddressHelper
{
  public    $lineDelimiter = "\n",
            $enforceComplete = TRUE,
            $enforceLineNumber = FALSE,
            $checkValuesForCompleteness = FALSE ;

  protected $_addressIds = array(),
            $_globalDefaultAddressStandard = 'default_address_standard',
            $_globalDefaultAddressGeoType = 'default_address_geo_type',
            $_startingLineNumber = 1,
            $_addressFormatComponents = array(),
            $_addressFormatRequired = array(),
            $_returnStandardId,
            $_addressGeoTypeId;

  /**
   * This is the class's constructor whic pre-loads the formatting elements according to the default
   * return standard.
   *
   * @param array $addressIds A single dimension array of address id values.
   */
  public function __construct($addressIds = NULL)
  {
    // if passed an array of address id's, set them as a class property
    $this->setAddressIds($addressIds);

    // set our default address standard and pick up the formatting components
    $this->_setDefaultReturnStandard() ;
  }

  /**
   * Static method used to instantiate the class.
   * @param array $addressIds A single dimension array of address ids.
   * @return object A new instance of agAddressHelper.
   */
  public static function init($addressIds = NULL)
  {
    $class = new self($addressIds) ;
    return $class ;
  }

  /**
   * Protected method to set the default address standard / format used for returning addresses.
   */
  protected function _setDefaultReturnStandard()
  {
    $standardName = agGlobal::getParam($this->_globalDefaultAddressStandard) ;
    $standardId = agDoctrineQuery::create()
      ->select('as.id')
        ->from('agAddressStandard as')
        ->where('as.address_standard = ?', $standardName)
        ->execute(array(),DOCTRINE_CORE::HYDRATE_SINGLE_SCALAR) ;

    $this->setReturnStandard($standardId) ;
  }

  protected function _setDefaultAddressGeoType()
  {
    $geoType = agGlobal::getParam($this->_globalDefaultAddressGeoType) ;
    $geoTypeId = agDoctrineQuery::create()
      ->select('gt.id')
        ->from('agGeoType gt')
        ->where('gt.geo_type = ?', $geoType)
        ->execute(array(),DOCTRINE_CORE::HYDRATE_SINGLE_SCALAR) ;

    $this->_addressGeoTypeId = $geoTypeId ;
  }

  /**
   * Helper method used to set the current batch of address id's.
   * 
   * @param array $addressIds A single-dimension array of address id's.
   */
  public function setAddressIds($addressIds)
  {
    if (isset($addressIds) && is_array($addressIds))
    {
      $this->_addressIds = $addressIds ;
    }
  }

  /**
   * Helper method to retrieve the $addressId properties in the event that it is passed a null
   * parameter. For use inside functions that optionally allow addresses to be specified.
   *
   * @param array $addressIds A single-dimension array of address  id's.
   * @return array $addressIds A single-dimension array of address  id's.
   */
  public function getAddressIds($addressIds = NULL)
  {
    if (is_null($addressIds))
    {
      return $this->_addressIds ;
    }
    else
    {
      return $addressIds ;
    }
  }

  /**
   * A simple method to set the current return standard and force the re-generation of the
   * formatting components.
   * @param integer $standardId The address_standard_id to set.
   */
  public function setReturnStandard($standardId)
  {
    $this->_returnStandardId = $standardId ;
    $this->_setAddressFormatComponents() ;
  }

  /**
   * This method queries the database for the required formatting components and loads several
   * properties with quick, referenceable information used in formatting endeavors.
   */
  protected function _setAddressFormatComponents()
  {
    $q = agDoctrineQuery::create()
      ->select('af.address_element_id')
          ->addSelect('af.line_sequence')
          ->addSelect('af.inline_sequence')
          ->addSelect('af.pre_delimiter')
          ->addSelect('af.post_delimiter')
          ->addSelect('af.is_required')
          ->addSelect('ft.field_type')
        ->from('agAddressFormat af')
          ->innerJoin('af.agFieldType ft')
        ->where('af.address_standard_id = ?', $this->_returnStandardId) ;

    // here we choose a custom hydration method to allow us to manipulate the results data twice
    $formatComponents = $q->execute(array(), DOCTRINE_CORE::HYDRATE_NONE) ;
    foreach($formatComponents as $fc)
    {

      // pull the end-values into an array so we can walk them quickly for a transform into
      // zero-length strings (a safety measure to ensure pure concatenation)
      $valueArray = array($fc[0], $fc[3], $fc[4], $fc[5], $fc[6]) ;
      array_walk($valueArray,
        function(&$val, $key) { $val = $val = (is_null($val)) ? '' : $val ; }
      );

      // bring it all into an array keyed by line and inline sequence (so we can walk these values)
      $this->_addressFormatComponents[$fc[1]][$fc[2]] = $valueArray ;

      // check just for the required elements and build a flat array of them for quick diffs
      if ($fc[5])
      {
        $this->_addressFormatRequired[] = $fc[0] ;
      }
    }

    // Because this becomes super important later on, we'll sort the results now so sorting isn't
    // forgetten and/or ends up inside a loop
    ksort($this->_addressFormatComponents) ;
    foreach ($this->_addressFormatComponents as $inlineComponents)
    {
      ksort($inlineComponents) ;
    }
  }

  /**
   * Method used to construct the base query object used by other objects in this class.
   *
   * @param array $addressIds A single-dimension array of address  id's.
   * @return agDoctrineQuery An extended doctrine query object.
   */
  protected function _getAddressComponents($addressIds)
  {
    // if no (null) ID's are passed, get the addressId's from the class property
    $addressIds = $this->getAddressIds($addressIds) ;

    // construct our base query object
    $q = agDoctrineQuery::create()
      ->select('amav.address_id')
          ->addSelect('av.address_element_id')
          ->addSelect('av.value')
        ->from('agAddressMjAgAddressValue amav')
          ->innerJoin('amav.agAddressValue av')
        ->whereIn('amav.address_id', $addressIds) ;

    return $q ;
  }

  /**
   * A workhorse method useful for returning address elements that need to be related back to
   * formatting components.
   *
   * @param array $addressIds An optional single-dimension array of address  id's. If NULL, the
   * classes' $addressIds property is used.
   * @return array A two dimensional array keyed by address_id, then by address_element_id, and
   * containing the address value.
   */
  public function getAddressComponentsById($addressIds = NULL)
  {
    // return our base query object
    $q = $this->_getAddressComponents($addressIds) ;

    $results = $q->execute(array(), 'assoc_two_dim');
    return $results ;
  }

  /**
   * Method to return the latitude and longitude coordinates of an address.
   *
   * @param array $addressIds An optional single-dimension array of address  id's. If NULL, the
   * classes' $addressIds property is used.
   * @return array A two-dimensional associative array, keyed by address id, that has key/value
   * pairs representing latitude and longitude.
   */
  public function getAddressCoordinates($addressIds = NULL)
  {
    // always a good idea to set this at the top
    $results = array() ;

    // get our default class-property-provided addressIds if none are passed
    $addressIds = $this->getAddressIds($addressIds) ;

    // if we've not yet set our address geo-type, then let's do-so
    if (! isset($this->_addressGeoTypeId)) { $this->_setDefaultAddressGeoType() ; }

    // create the monster query
    $q = agDoctrineQuery::create()
      ->select('a.id')
          ->addSelect('gc.latitude')
          ->addSelect('gc.longitude')
        ->from('agAddress a')
          ->innerJoin('a.agAddressGeo ag')
          ->innerJoin('ag.agGeo g')
          ->innerJoin('g.agGeoFeature gf')
          ->innerJoin('gf.agGeoCoordinate gc')
        ->whereIn('a.id', $addressIds)
          ->andWhere('g.geo_type_id = ?', $this->_addressGeoTypeId)
          ->andWhere(' EXISTS (
            SELECT sq.id
            FROM agGeoFeature sq
            WHERE sq.geo_id = gf.geo_id
            HAVING MIN(sq.geo_coordinate_order) = gf.geo_coordinate_order)') ;

    $rows = $q->execute(array(), Doctrine_Core::HYDRATE_NONE) ;
    foreach ($rows as $row)
    {
      $results[$row[0]] = array('latitude' => $row[1], 'longitude' => $row[2]) ;
    }

    return $results ;
  }

  /**
   * Method to return address components similar to the getAddressComponentsById method, however,
   * the keys of the second dimension array are strings (for easier access). It also can optionally
   * include addresses' latitudes and longitudes.
   *
   * @param array $addressIds An optional single-dimension array of address  id's. If NULL, the
   * classes' $addressIds property is used.
   * @param boolean $getGeoCoordinates Optional boolean used to control whether or not the latitude
   * and longitude coordinates are also returned. Defaults to TRUE.
   * @return array A two-dimensional associative array, keyed by address_id and the string
   * representation of the address component.
   */
  public function getAddressComponentsByName($addressIds = NULL, $getGeoCoordinates = TRUE)
  {
    $results = array() ;

    // return our base query object
    $q = $this->_getAddressComponents($addressIds) ;

    // add the address 'by name' components
    $q->addSelect('ae.address_element')
      ->innerJoin('av.agAddressElement ae');
    
    // we have to use our own hydration here to skip over the address_element_id in $row[1]
    $rows = $q->execute(array(), Doctrine_Core::HYDRATE_NONE) ;
    foreach ($rows as $row)
    {
      $results[$row[0]][$row[3]] = $row[2] ;
    }

    // grab our geo coordinates and merge them to the array
    if ($getGeoCoordinates) {
      $geoCoordinates = $this->getAddressCoordinates($addressIds) ;

      foreach ($results as $key => $value)
      {
        if (array_key_exists($key, $geoCoordinates))
        {
          $results[$key] = array_merge($value, $geoCoordinates[$key]) ;
        }
      }
    }

    return $results ;
  }

  /**
   * A super-helpful (and super-quick) function to tell a user if the address components being
   * passed represent a complete address (one with values in all required element fields) according
   * to the returnStandard.
   * 
   * @param array $addressComponentArray An associative, mono-dimensional array keyed by
   * address_element_id. This is, in-effect, the same output as the second-dimension of
   * getAddressComponentsById (hint, hint, hint...)
   * @param boolean $checkValues Boolean parameter to control whether or not the method only
   * searches for keys (FALSE) or checks the actual values for zero-length strings and/or nulls.
   * Defaults to the class parameter $checkValuesForCompleteness.
   * @return boolean Is it a complete address? True or False. 
   */
  public function isCompleteAddress($addressComponentArray, $checkValues = NULL)
  {
    // get our class-default checkValues value
    if (is_null($checkValues)) { $checkValues = $this->checkValuesForCompleteness ; }

    // loop through the required components
    foreach ($this->_addressFormatRequired as $reqComponent)
    {
      // first check if the component array even has the required component
      if (! isset($addressComponentArray[$reqComponent]))
      {
        return FALSE ;
      }
      elseif ($checkValues)
      {
        // we only want to do the array search once to satisfy either condition
        $val = $addressComponentArray[$reqComponent] ;

        // check for null or zero-length
        if (is_null($val) || $val == '')
        {
          return FALSE ;
        }
      }
    }

    // if everything's hunky-dory give it the 10-4 go-ho good buddy
    return TRUE ;
  }

  /**
   * Method to return an array of address_id's that are not complete. Potentially useful for
   * large operations like imports.
   * 
   * @param array $addressIds An optional single-dimension array of address  id's. If NULL, the 
   * classes' $addressIds property is used.
   * @return array A mono-dimesional array of address_ids.
   */
  public function getIncompleteAddresses($addressIds = NULL)
  {
    $results = array() ;
    $addresses = $this->getAddressComponentsById($addressIds) ;

    foreach ($addresses as $address => $componentArray)
    {
      if (! $this->isCompleteAddress($componentArray))
      {
        $results[] = $address ;
      }
    }

    return $results ;
  }

  /**
   * Method to return address components as an associative array keyed by line number.
   *
   * @param array $addressIds An optional single-dimension array of address  id's. If NULL, the
   * classes' $addressIds property is used.
   * @param boolean $enforceComplete An optional boolean to control whether or not only complete
   * addresses will be returned. Defaults to using the class property of the same name.
   * @return array A two-dimensional associative array, keyed by address_id and line-number with a
   * string representation of that line's combined elements as the value.
   *
   * @todo This could *perhaps* be turned into some sort of super-efficient walk method
   */
  public function getAddressComponentsByLine($addressIds = NULL, $enforceComplete = NULL)
  {
    // always a good idea to explicitly declare this
    $results = array() ;

    // grab our default if not explicitly passed a completeness parameter
    if (is_null($enforceComplete)) { $enforceComplete = $this->enforceComplete ; }

    // grab all of our address components by element id
    $addressComponents = $this->getAddressComponentsById($addressIds) ;

    // start by iterating over the individual addresses
    foreach ($addressComponents as $addressId => $addrComponents)
    {
      // test to see if we're only returning complete addresses
      if (! $enforceComplete || ($enforceComplete && $this->isCompleteAddress($addrComponents)))
      {
        // now we traverse our lovely, ordered, format array by line
        foreach ($this->_addressFormatComponents as $lineId => $lineComponents)
        {
          $strLine = '' ;

          // traverse by inline
          foreach ($lineComponents as $inlineId => $inlineComponents)
          {
            // if we find an address component fitting our spec, we add it in order
            if (key_exists($inlineComponents[0], $addrComponents))
            {
              $addrValue = $addrComponents[$inlineComponents[0]] ;

              // assuming this actually has a value, we concatenate these suckers together
              if (isset($addrValue))
              {
                $strLine = $strLine . $inlineComponents[1] ;
                $strLine = $strLine . $addrValue ;
                $strLine = $strLine . $inlineComponents[2] ;
              }
            }
          }

          // build a results array by address by line, excluding empty lines
          if ($strLine != '') { $results[$addressId][$lineId] = $strLine ; }
        }
      }

    }

    return $results ;
  }

  /**
   * Method to return an address as a singled formatted string. Uses the $lineDelimiter public class
   * property as a line Delimiter which can be changed to suit the needs of the output.
   * 
   * @param array $addressIds An optional single-dimension array of address  id's. If NULL, the 
   * classes' $addressIds property is used.
   * @param boolean $enforceComplete An optional boolean to control whether or not only complete
   * addresses will be returned. Defaults to using the class property of the same name.
   * @param boolean $enforceLineNumber An optional boolean to control whether or not line numbers
   * will be strictly enforced. Defaults to using the class property of the same name.
   * @return array A mono-dimensional associative array keyed by address_id with the combined
   * address string as a value.
   */
  public function getAddressAsString( $addressIds = NULL,
                                      $enforceComplete = NULL,
                                      $enforceLineNumber = NULL)
  {
    // always a good idea to explicitly declare this
    $results = array() ;

    // grab our default if not explicitly passed a line number parameter
    if (is_null($enforceLineNumber)) { $enforceLineNumber = $this->enforceLineNumber ; }

    // now we grab all of our addresses and return them in an array sorted per-line
    $addresses = $this->getAddressComponentsByLine($addressIds, $enforceComplete) ;

    // start by iterating over the individual addresses
    foreach ($addresses as $addressId => $addrLines)
    {
      $strAddr = '' ;
      $line = $this->_startingLineNumber ;

      // sort the address lines to make sure we can iterate over them safely
      ksort($addrLines) ;

      // grab the last key so as not to add a Delimiter
      $lineIds = array_keys($addrLines) ;
      $lastLineId = array_pop($lineIds) ;

      // now iterate per-line
      foreach ($addrLines as $lineId => $lineStr)
      {
        // if enforcing line-numbers, append additional line Delimiters until $lineId and $line match
        while ($enforceLineNumber && $line < $lineId)
        {
          $strAddr = $strAddr . $this->lineDelimiter ;
          $line++ ;
        }

        // append our address string
        $strAddr = $strAddr . $lineStr ;

        // append a Delimiter if one needs to be added
        if ($lineId != $lastLineId) { $strAddr = $strAddr . $this->lineDelimiter ; }
        $line++ ;
      }

      // add it to our results array
      $results[$addressId] = $strAddr ;
    }

    return $results ;
  }

  /**
   * @return address_standard_id.
   */
  public function getAddressStandardId() {
    return $this->_returnStandardId;
  }

  /**
   * @return array $result An associative array,
   * array(address_element_id => address_element).
   */
  public function getAddressElements() {
    $result = array();
    $result = agDoctrineQuery::create()
            ->select('ae.id, ae.address_element')
            ->from('agAddressElement ae')
            ->innerJoin('ae.agAddressFormat af')
            ->where('address_standard_id =?', $this->_returnStandardId)
            ->execute(array(), 'key_value_pair');
    return $result;
  }
}
