<?php
/**
 * Provides bulk-address manipulation methods
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author Chad Heuschober, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
class agAddressHelper extends agBulkRecordHelper
{
  // these constants map to the address get types that are supported in other function calls
  const     ADDR_GET_TYPEID = 'getAddressComponentsById',
            ADDR_GET_GEO = 'getAddressCoordinates',
            ADDR_GET_TYPE = 'getAddressComponentsByName',
            ADDR_GET_LINE = 'getAddressComponentsByLine',
            ADDR_GET_STRING = 'getAddressAsString',
            ADDR_GET_NATIVE_LINE = 'getNativeAddressComponentsByLine',
            ADDR_GET_NATIVE_STRING = 'getNativeAddressAsString' ;


  public    $lineDelimiter = "<br />",
            $enforceComplete = FALSE,
            $enforceLineNumber = FALSE,
            $checkValuesForCompleteness = FALSE,
            $agGeoHelper ;

  protected $_globalDefaultAddressStandard = 'default_address_standard',
            $_globalDefaultAddressGeoType = 'default_address_geo_type',
            $_startingLineNumber = 1,
            $_addressFormatComponents = array(),
            $_addressFormatRequired = array(),
            $_addressAllowedElements = array(),
            $_returnStandardId,
            $_addressGeoTypeId;

  /**
   * Overloaded magic call method to provide access to the getNativeAddress* variants.
   *
   * @param string $method The method being called.
   * @param array $arguments The arguments being passed.
   * @return function call
   */
  public function __call($method, $arguments)
  {
    $nativePreg = '/^getNativeAddress/i' ;
    
    // check to see if our method exists in our helpers
    if (preg_match($nativePreg, $method))
    {
      try
      {
        // parse out the function that's *really* being called
        $returnMethod = array($this, preg_replace($nativePreg, 'getAddress', $method)) ;
        $nativeMethod = array($this, '_getNativeAddress') ;

        // execute and return
        return call_user_func_array($nativeMethod, array($returnMethod, $arguments)) ;
      }
      catch (Exception $e)
      {
        // if there's an error, write to log and return
        $notice = sprintf('Execution of the %s method, found in %s failed. Attempted to use the
          parent class.', $method, $helperClass) ;
        sfContext::getInstance()->getLogger()->notice($notice) ;
      }
    }
    
    return parent::__call($method, $arguments) ;
  }

  /**
   * This is the class's constructor whic pre-loads the formatting elements according to the default
   * return standard.
   *
   * @param array $addressIds A single dimension array of address id values.
   */
  public function __construct(array $addressIds = NULL)
  {
    // if passed an array of address id's, set them as a class property
    parent::__construct($addressIds);

    // set our default address standard and pick up the formatting components
    $this->_setDefaultReturnStandard() ;
  }

  /**
   * Method to lazily load the $agAddressHelper class property (an instance of agAddressHelper)
   * @return object The instantiated agAddressHelper object
   */
  public function getAgGeoHelper()
  {
    if (! isset($this->agGeoHelper)) { $this->agGeoHelper = new agGeoHelper() ; }
    return $this->agGeoHelper ;
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

  /**
   *  Small helper function to pick up the default geo-type for addresses.
   */
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
          ->addSelect('ae.address_element')
        ->from('agAddressFormat af')
          ->leftJoin('af.agFieldType ft')
          ->innerJoin('af.agAddressElement ae')
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
      
      // also create our simple allowed elements array
      $this->_addressAllowedElements[$fc[0]] = $fc[7] ;
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
   * Method to wrap the non-native address functions and process addresses per-address standard so
   * that results are returned in the standard in which they were submitted.
   *
   * @param string $returnMethod The address return method to wrap.
   * @param array $arguments The arguments to be passed to the address return method.
   * @return array The results of the $returnMethod.
   */
  protected function _getNativeAddress($returnMethod, array $arguments)
  {
    // always nice to have results, don'cha think?
    $results = array() ;

    // pick this up so we can reset it when done!
    $origStandardId = $this->_returnStandardId ;

    // pick up our all of our address ids
    $addressIds = array_shift($arguments) ;
    $addressIds = $this->getRecordIds($addressIds);

    // seems a little insane but we do this so our foreach can always replace [0]
    array_unshift($arguments, array()) ;

    // construct our standards query
    $q = agDoctrineQuery::create()
      ->select('a.address_standard_id')
          ->addSelect('a.id')
        ->from('agAddress a')
        ->whereIn('a.id', $addressIds) ;

    // execute the standards query and return a grouped array
    $addrStandards = $q->execute(array(), agDoctrineQuery::HYDRATE_ASSOC_ONE_DIM) ;

    // loop through the standards and set the formatting components appropriately
    foreach ($addrStandards as $standardId => $addressIds)
    {
      // start by setting the new standard to be processed (use the if to avoid spurious sets)
      if ($standardId != $this->_returnStandardId) { $this->setReturnStandard($standardId) ; }

      // set the addressIds argument (always first)
      $arguments[0] = $addressIds ;

      // append the call for just those standards to our results set
      $subResults = call_user_func_array($returnMethod, $arguments) ;
      $results = $results + $subResults ;

      // release the resources for this standard
      unset($addrStandards[$standardId]) ;
    }

    // reset our original standard (use the if to avoid spurious sets)
    if ($origStandardId != $this->_returnStandardId) { $this->setReturnStandard($origStandardId) ; }

    return $results ;
  }

  /**
   * Method used to construct the base query object used by other objects in this class.
   *
   * @param array $addressIds A single-dimension array of address  id's.
   * @return agDoctrineQuery An extended doctrine query object.
   */
  protected function _getAddressComponents(array $addressIds)
  {
    // if no (null) ID's are passed, get the addressId's from the class property
    $addressIds = $this->getRecordIds($addressIds) ;

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
  public function getAddressComponentsById(array $addressIds = NULL)
  {
    // return our base query object
    $q = $this->_getAddressComponents($addressIds) ;

    return $q->execute(array(), agDoctrineQuery::HYDRATE_ASSOC_TWO_DIM);
  }

  /**
   * Method to return the latitude and longitude coordinates of an address.
   *
   * @param array $addressIds An optional single-dimension array of address  id's. If NULL, the
   * classes' $addressIds property is used.
   * @return array A two-dimensional associative array, keyed by address id, that has key/value
   * pairs representing latitude and longitude.
   */
  public function getAddressCoordinates(array $addressIds = NULL)
  {
    // always a good idea to set this at the top
    $results = array() ;

    // get our default class-property-provided addressIds if none are passed
    $addressIds = $this->getRecordIds($addressIds) ;

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
  public function getAddressComponentsByName(array $addressIds = NULL, $getGeoCoordinates = TRUE)
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

    // release the rows resource
    unset($rows) ;

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
  public function isCompleteAddress(array $addressComponentArray, $checkValues = NULL)
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
  public function getIncompleteAddresses(array $addressIds = NULL)
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
  public function getAddressComponentsByLine(array $addressIds = NULL, $enforceComplete = NULL)
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
  public function getAddressAsString(array $addressIds = NULL,
                                      $enforceComplete = NULL,
                                      $enforceLineNumber = NULL)
  {
    // grab our default if not explicitly passed a line number parameter
    if (is_null($enforceLineNumber)) { $enforceLineNumber = $this->enforceLineNumber ; }

    // now we grab all of our addresses and return them in an array sorted per-line
    $addresses = $this->getAddressComponentsByLine($addressIds, $enforceComplete) ;

    // release the addressId array
    unset($addressIds) ;

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
      $addresses[$addressId] = $strAddr ;
    }

    return $addresses ;
  }

  /**
   * Method to return a flattened address query object, based on the standard being applied.
   * @return Doctrine Query A doctrine query object.
   * @deprecated This function was never used/implemented.
   */
  protected function _getFlatAddresses()
  {
    // start a basic query object with just the address
    $q = agDoctrineQuery::create()
      ->select('a.id')
        ->from('agAddress a') ;

    // had to be a little creative here using subqueries in the select because doctrine's rather
    // fussy, but the workaround suits the need
    // basically, we just loop through 'all' of the elements in our current standard and build a
    // pivoted (aka crosstab) variant of the address with a column for each of those elements
    foreach ($this->_addressAllowedElements as $elemId => $elem)
    {
      // don't ask me why, but doctrine freaks if you try to move the ON clause to a new line
      $selectStatement = '(SELECT av%1$02d.id
        FROM agAddressMjAgAddressValue amav%1$02d
          INNER JOIN amav%1$02d.agAddressValue av%1$02d ON amav%1$02d.address_value_id = av%1$02d.id
          WHERE amav%1$02d.address_id = a.id
            AND av%1$02d.address_element_id = %1$02d) AS %s' ;
      $selectStatement = sprintf($selectStatement, $elemId, $elemId) ;
      $q->addSelect($selectStatement);
    }

    return $q ;
  }

  /**
   * Method to update the address hash values of existing addresses.
   *
   * @param array $addressIds A single dimension array of addressIds.
   * @param Doctrine_Connection $conn An optional doctrine connection object.
   * @deprecated This should not normally be necessary as address hashes should be generated
   * at address creation.
   */
  public function updateAddressHashes(array $addressIds = NULL, $conn = NULL)
  {
    // what is our transaction called?
    $savepoint = 'updateAddrHash' ;

    // reflect our addressIds
    $addressIds = $this->getRecordIds($addressIds) ;

    // pick up a default conneciton if none is passed
    if (is_null($conn)) { $conn = Doctrine_Manager::connection() ; }

    // use our componentsId getter to get all of the relevant components for these ids
    $addressComponents = $this->getAddressComponentsById($addressIds) ;

    // here we set up our collection of address records, selecting only those with the addressIds
    // we're affecting. Note: INDEXBY is very important if we intend to access this collection via
    // array access as we do later.
    $q = agDoctrineQuery::create($conn)
      ->select('a.*')
        ->from('agAddress a INDEXBY a.id')
        ->whereIn('a.id', $addressIds) ;
    $addressCollection = $q->execute() ;

    foreach ($addressComponents as $addressId => $components)
    {
      // calculate the component hash
      $addrHash = agBulkRecordHelper::getRecordComponentsHash($components) ;

      // update the address hash value of this addressId by array access
      $addressCollection[$addressId]['address_hash'] = $addrHash ;
    }

    // start our transaction
    $conn->beginTransaction() ;
    try
    {
     $addressCollection->save() ;
     $conn->commit() ;
    }
    catch(Exception $e)
    {
      // if that didn't pan out so well, execute a rollback and log our woes
      $conn->rollback() ;
      
      $message = ('One of the addresses in your addressId collection could not be updated.
        No changes were applied.') ;
      sfContext::getInstance()->getLogger()->err($message) ;
      throw new sfException($message, $e) ;
    }
  }

  /**
   * A quick helper method to take in an array address hashes and return an array of address ids.
   * @param array $addressHashes A monodimensional array of md5sum, json_encoded address hashes.
   * @return array An associative array, keyed by address hash, with a value of address_id.
   */
  public function getAddressIdsByHash(array $addressHashes)
  {
    $q = agDoctrineQuery::create()
      ->select('a.id')
        ->from('agAddress a')
      ->useResultCache(TRUE, 1800);
    
    $results = array();
    foreach ($addressHashes as $hash)
    {
      $q->where('a.address_hash = ?',$hash);

      $result = $q->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);

      // clear the cache if we had no result
      if (empty($result) || is_null($result))
      {
        $cacheDriver = Doctrine_Manager::getInstance()->getAttribute(Doctrine_Core::ATTR_RESULT_CACHE);
        $cacheDriver->delete($q->getResultCacheHash());
      }
      else
      {
        $results[$hash] = $result;
      }
    }

    return $results;
  }

  /**
   * Method to return address contact type ids from address contact type values.
   * @param array $addressTypes An array of address_contact_types
   * @return array An associative array of address contact type ids keyed by address contact type.
   */
  static public function getAddressContactTypeIds(array $addressTypes)
  {
    return agDoctrineQuery::create()
      ->select('act.address_contact_type')
          ->addSelect('act.id')
        ->from('agAddressContactType act')
        ->whereIn('act.address_contact_type', $addressTypes)
      ->useResultCache(TRUE, 3600)
      ->execute(array(), agDoctrineQuery::HYDRATE_KEY_VALUE_PAIR);
  }

  /**
   * Method to return address element ids from address element values.
   * @param array $addressElements An array of address_elements
   * @return array An associative array of address element ids keyed by address element.
   */
  static public function getAddressElementIds(array $addressElements)
  {
    return agDoctrineQuery::create()
      ->select('ae.address_element')
          ->addSelect('ae.id')
        ->from('agAddressElement ae')
        ->whereIn('ae.address_element', $addressElements)
      ->useResultCache(TRUE, 3600)
      ->execute(array(), agDoctrineQuery::HYDRATE_KEY_VALUE_PAIR);
  }
  
  /**
   * Method to return address standard ids from address standard values.
   * @param array $addressStandards An array of address_standards
   * @return array An associative array of address standard ids keyed by address standard.
   */
  static public function getAddressStandardIds(array $addressStandards)
  {
    return agDoctrineQuery::create()
      ->select('as.address_standard')
          ->addSelect('as.id')
        ->from('agAddressStandard as')
        ->whereIn('as.address_standard', $addressStandards)
      ->useResultCache(TRUE, 3600)
      ->execute(array(), agDoctrineQuery::HYDRATE_KEY_VALUE_PAIR);
  }

  /**
   * Method to take in address components and return address ids, inserting new addresses OR
   * address components as necessary.
   *
   * NOTE: This method does not fail fast. Addresses for which address id's could not be returned,
   * either by failed search or failed insert, are returned by index as part of the results set.
   *
   * @param array $addresses This multi-dimensional array of address data is keyed by an arbitrary
   * index. The values of each index are: an array of address components, keyed by element id, and the
   * default address standard of this address.
   * <code>
   * array(
   *    [$index] => array(
   *      [0] => array( [$elementId] => $valueString, ...),
   *      [1] => $addressStandardId
   *    ),
   *    ...
   * )
   * </code>
   * @param array $addressGeo An array of address geo elements.
   * NOTE: Must use the same $index ID's as the $addresses array.
   * <code>
   * array(
   *    [$index] => array(
   *      [0] => array( [0]=> array($latitude, $longitude), [1] => ...),
   *      [1] => $matchScoreId
   *    ),
   *    ...
   * )
   * </code>
   * @param boolean $enforceComplete Determines whether or not only complete addresses will be
   * processed and set. Warning! It still won't process addresses that were previously allowed to
   * be incomplete meaning users should attempt to be consistent in the use of this parameter.
   * @param boolean $throwOnError A boolean to determine whether or not errors will trigger an
   * exception or be silently ignored (rendering an address 'optional'.
   * @param Doctrine_Connection $conn A doctrine connection object.
   * @return array A two dimensional array. The first array element ([0]), returns an array of
   * address indexes and the newly inserted addressIds. The second array element [1], returns all
   * address indexes that could not be inserted.
   * <code>
   * array(
   *  [0] => array( [$addressIndex] => [$addressId], ... )
   *  [1] => array( $addressIndex, ... )
   * )
   * </code>
   * @todo Pass the addressGeo array through
   */
  public function setAddresses( array $addresses,
                                array $addressIndexGeo = array(),
                                $geoSourceId = NULL,
                                $enforceComplete = NULL,
                                $throwOnError = NULL,
                                Doctrine_Connection $conn = NULL)
  {
    $addressIdGeo = array();
    $err = NULL;
    $results = array(array(), array());

    // get some defaults if not explicitly passed
    if (is_null($conn)) { $conn = Doctrine_Manager::connection() ; }
    if (is_null($enforceComplete)) { $enforceComplete = $this->enforceComplete ; }
    if (is_null($throwOnError)) { $throwOnError = $this->throwOnError ; }

    // fail out if not provided a geoSourceId but expected to process geo
    if (! empty($addressIndexGeo) && is_null($geoSourceId))
    {
      $errMsg = "Geo coordinates provided, missing geoSourceId.";
      throw new Exception($errMsg);
    }

    // set up the incompletes (non-processed) array
    $incompleteAddresses = array() ;

    // if we're going to do this, let's set up our required elements and kick out incompletes
    if ($enforceComplete)
    {
      // we'll want this flipped array to process all address standards one at a time
      $addressesByStandard = array() ;
      foreach ($addresses as $index => $components)
      {
        $addressesByStandard[$components[1]][$index] = $components[0] ;
      }

      // remember what our default standard was
      $origStandard = $this->_returnStandardId ;

      // now loop through our flipped array and test
      foreach ($addressesByStandard as $standardId => $addrComponents)
      {
        // we do this to avoid spurious sets of the required components
        if ($standardId != $this->_returnStandardId) { $this->setReturnStandard($standardId) ; }

        // now loop the addresses individually
        foreach ($addrComponents as $index => $components)
        {
          if (! $this->isCompleteAddress($components))
          {
            // add it to our incompletes
            $incompleteAddresses[] = $index ;

            // also remove it from the group to be processed
            unset($addresses[$index]) ;

            // if we're being strict with error throws, let's throw on a problem
            if ($throwOnError)
            {
              $errMsg = sprintf('Address \'%s\' with components %s failed the test for completeness
                for address standard id \'%s\'.', $index, json_encode($components), $standardId) ;

              // log our error
              sfContext::getInstance()->getLogger()->err($errMsg) ;

              // throw the exception we promised in our boolean
              throw new Exception($errMsg) ;
            }
          }
          else
          {
            // Trim leading and trailing spaces from contact values.
            foreach ($components as $elem => $val)
            {
              $addresses[$index][0][$elem] = trim($val);
            }
          }

          // release the array!
          unset($addrComponents[$index]) ;
        }

        // oh heck, release this one too
        unset($addressesByStandard[$standardId]) ;
      }

      // return the original standard
      if ($origStandard != $this->_returnStandardId) { $this->setReturnStandard($origStandard) ; }
    }
    else {
      // Trim address values.
      foreach ($addresses as $index => $components)
      {
        foreach ($components[0] as $elem => $val)
        {
          $addresses[$index][0][$elem] = trim($val);
        }
      }
    }

    // here we check our current transaction scope and create a transaction or savepoint
    $useSavepoint = ($conn->getTransactionLevel() > 0) ? TRUE : FALSE ;
    if ($useSavepoint)
    {
      $conn->beginTransaction(__FUNCTION__) ;
    }
    else
    {
      $conn->beginTransaction() ;
    }

    try
    {
      // either way, we eventually pass the 'cleared' addresses to our setter
      $results = $this->_setAddresses($addresses, $throwOnError, $conn) ;
    }
    catch(Exception $e)
    {
     // log our error
      $errMsg = sprintf('Failed to execute _setAddresses!  %s', $e->getMessage());

      // capture our exception for a later throw and break out of this loop
      $err = $e ;
    }

    if (is_null($err))
    {
      // CALLERS OF THE GEOCODE WOULD LIVE HERE AND USE $results
      // @todo wrap $results & geocode in try/catch to keep them synced together

      // Resetting an address geo array with address id as index.
      foreach ($results[0] as $index => $addrId)
      {
        if (array_key_exists($index, $addressIndexGeo))
        {
          $addressIdGeo[$addrId] = $addressIndexGeo[$index];
          unset($addressIndexGeo[$index]);
        }
      }

      //Lazy load agGeoHelper.
      $geoHelper = $this->getAgGeoHelper();
      try
      {
        $geoHelper->setAddressGeo($addressIdGeo, $geoSourceId, NULL, $throwOnError, $conn);
        // most excellent! no errors at all, so we commit... finally!
        if ($useSavepoint) { $conn->commit(__FUNCTION__) ; } else { $conn->commit() ; }
      }
      catch(Exception $e)
      {
       // log our error
        $errMsg = sprintf('Failed to execute setAddressGeo!  %s', $e->getMessage());

        // capture our exception for a later throw and break out of this loop
        $err = $e ;
      }
    }

    if (!is_null($err))
    {
      // log our error
      sfContext::getInstance()->getLogger()->err($errMsg) ;

      // rollback
      if ($useSavepoint) { $conn->rollback(__FUNCTION__) ; } else { $conn->rollback() ; }

      // capture failed address index in results.
      $results[1] = $results[1] + array_keys($results[0]) ;
      $results[0] = array();

      // ALWAYS throw an error, it's like stepping on a crack if you don't
      if ($throwOnError) { throw $err ; }
    }
    
    // append our incompletes to the other failed addresses
    $results[1] = $results[1] + $incompleteAddresses ;

    return $results ;
  }
  
  /**
   * Method to take in address components and return address ids, inserting new addresses OR
   * address components as necessary.
   *
   * @param array $addresses This multi-dimensional array of address data is keyed by an arbitrary
   * index. The values of each index are: an array of address components, keyed by element id, and the
   * default address standard of this address.
   * <code>
   * array(
   *    [$index] => array(
   *      [0] => array( [$elementId] => $valueString, ...),
   *      [1] => $addressStanardId
   *    ),
   *    ...
   * )
   * </code>
   * @param boolean $throwOnError A boolean to determine whether or not errors will trigger an
   * exception or be silently ignored (rendering an address 'optional'.
   * @param Doctrine_Connection $conn A doctrine connection object.
   * @return array A two dimensional array. The first array element ([0]), returns an array of
   * address indexes and the newly inserted addressIds. The second array element [1], returns all
   * address indexes that could not be inserted.
   * <code>
   * array(
   *  [0] => array( [$addressIndex] => [$addressId], ... )
   *  [1] => array( $addressIndex, ... )
   * )
   * </code>
   */
  protected function _setAddresses(array $addresses,
                                    $throwOnError = NULL,
                                    Doctrine_Connection $conn = NULL)
  {
    // declare our results array
    $results = array() ;

    // declare the flipped array we use for the first pass search
    $searchArray = array() ;

    // declare the addrHashes array explicitly too
    $addrHashes = array() ;

    // loop through the addresses, hash the components, and build the hash-keyed search array
    foreach($addresses as $index => $addressComponents)
    {
      $hash = agBulkRecordHelper::getRecordComponentsHash($addressComponents[0]) ;
      $addrHashes[$index] = $hash ;
    }

    // return any found hashes
    $dbHashes = $this->getAddressIdsByHash(array_unique(array_values($addrHashes))) ;

    // loop through the generated hashes and build a couple of arrays
    foreach ($addrHashes as $addrIndex => $addrHash)
    {
      // if we found an address id already, our life is much easier
      if (array_key_exists($addrHash, $dbHashes))
      {
        // for each of the addresses with that ID, build our results set and
        // unset the value from the stuff left to be processed (we're going to use that later!)
        $results[$addrIndex] = $dbHashes[$addrHash] ;
        unset($addresses[$addrIndex]) ;
      }
      else
      {
        // if that didn't work out for us we move the hash to the addresses array for pass-through
        $addresses[$addrIndex][2] = $addrHash ;
      }

      // either way, we've already processed this address hash, so we can release it
      unset($addrHashes[$addrIndex]) ;
    }

    // just 'cause this is going to be a very memory-hungry method, we'll unset the hashes too
    unset($dbHashes) ;

    // pick up the default connection if one is not passed
    if (is_null($conn)) { $conn = Doctrine_Manager::connection() ; }

    // now that we have all of the 'existing' addresses, let's build the new ones
    $newAddresses = $this->setNewAddresses($addresses, $throwOnError, $conn) ;
    $successes = array_shift($newAddresses) ;

    // we don't need this anymore!
    unset($newAddresses) ;

    foreach ($successes as $index => $addrId)
    {
      // add our successes to the final results set
      $results[$index] = $addrId ;

      // release the address from our initial input array
      unset($addresses[$index]) ;

      // release it from the successes array while we're at it
      unset($successes[$index]) ;
    }

    // and finally we return our results, both the successes and the failures
    return array($results, array_keys($addresses)) ;
  }

  /**
   * A big honkin' method to create a new address and, if also necessary, the address elements that
   * don't yet exist to support the address.
   *
   * NOTE: This method does not fail fast. Failed address inserts are returned by index as part of
   * the results set.
   *
   * @param array $addresses This multi-dimensional array of address data is keyed by an arbitrary
   * index. The values of each index are: an array of address components, keyed by element id, the
   * default address standard of this address, and the address hash of the components.
   * <code>
   * array(
   *    [$index] => array(
   *      [0] => array( [$elementId] => $valueString, ...),
   *      [1] => $addressStanardId,
   *      [2] => $addressHash
   *    ),
   *    ...
   * )
   * </code>
   * @param boolean $throwOnError A boolean to determine whether or not errors will trigger an
   * exception or be silently ignored (rendering an address 'optional'.
   * @param Doctrine_Connection $conn A doctrine connection object.
   * @return array A two dimensional array. The first array element ([0]), returns an array of
   * address indexes and the newly inserted addressIds. The second array element [1], returns all
   * address indexes that could not be inserted.
   * <code>
   * array(
   *  [0] => array( [$addressIndex] => [$addressId], ... )
   *  [1] => array( $addressIndex, ... )
   * )
   * </code>
   * @todo add new geo's or attach old ones (as appropriate)
   * @todo optimize for APC to do the results caching
   */
  protected function setNewAddresses(array $addresses,
                                      $throwOnError = NULL,
                                      Doctrine_Connection $conn = NULL)
  {
    // declare our results array
    $results = array() ;

    // pick up the default connection and error throw prerogative if one is not passed
    if (is_null($conn)) { $conn = Doctrine_Manager::connection() ; }
    if (is_null($throwOnError)) { $throwOnError = $this->throwOnError ; }

    // loop through our addresses and the components
    foreach ($addresses as $index => $components)
    {
      // we do this so we only have to call rollback / unset once, plus it's nice to have a bool to
      // check on our own
      $err = NULL ;
      $errMsg = 'This is a generic error message for setNewAddresses. You should never receive this
        error. If you are recieving this error, there is an ERROR in your error-handling code.' ;

      // if for whatever reason we're not passed a standard, pick up the default
      if (! isset($components[1])) { $components[1] = $this->_returnStandardId ; }

      // here we check our current transaction scope and create a transaction or savepoint
      $useSavepoint = ($conn->getTransactionLevel() > 0) ? TRUE : FALSE ;
      if ($useSavepoint)
      {
        $conn->beginTransaction(__FUNCTION__) ;
      }
      else
      {
        $conn->beginTransaction() ;
      }

      // build a results cache so we commit entire addresses at once, not just individual elements
      $resultsCache = array() ;

      foreach($components[0] as $elementId => $value)
      {
        // since we didn't find it in cache, we'll try to grab it from the db
        $valueId = $this->getAddressValueId($elementId, $value) ;

        // unfortunately, if we didn't get value we've got to add it!
        if (empty($valueId))
        {

          $addrValue = new agAddressValue();
          $addrValue['address_element_id'] = $elementId ;
          $addrValue['value'] = $value ;
          try
          {
            // save the address
            $addrValue->save($conn) ;
            $valueId = $addrValue->getId() ;

            // and since that went right, add it to our results arrays
            $resultsCache[$elementId] = $valueId ;
          }
          catch(Exception $e)
          {
           // log our error
            $errMsg = sprintf('Couldn\'t insert address value %s of element %s!
              Rolled back changes!', $value, $elementId) ;

            // capture our exception for a later throw and break out of this loop
            $err = $e ;
            break ;
          }
        }
      }

      // now we attempt to insert the new address_id with all of our value bits, again only useful
      // if we've not already had an error
      if (is_null($err))
      {
        // attempt to insert the actual address
        $newAddr = new agAddress() ;
        $newAddr['address_standard_id'] = $components[1] ;
        $newAddr['address_hash'] = $components[2] ;

        try
        {
          // save the address
          $newAddr->save($conn) ;
          $addrId = $newAddr->getId() ;
        }
        catch(Exception $e)
        {
          // log our error
          $errMsg = sprintf('Couldn\'t insert address with standard %s and hash %s!
            Rolled back changes!', $components[1], $components[2]) ;

          // hold onto this exception for later
          $err = $e ;
        }
      }

      // the final step!!! inserting into agAddressMjAgAddressValue
      foreach ($resultsCache as $rElem => $rValueId)
      {
        // if we at any point pick up an error, don't bother
        if (! is_null($err)) { break ; }

        $newAmav = new agAddressMjAgAddressValue() ;
        $newAmav['address_id'] = $addrId ;
        $newAmav['address_value_id'] = $rValueId ;

        try
        {
          // save the address
          $newAmav->save($conn) ;
        }
        catch(Exception $e)
        {
          // log our error
          $errMsg = sprintf('Couldn\'t insert address (%s) + value (%s) mapping join!
            Rolled back changes!', $addrId, $rValueId) ;

          // hold onto this exception for later
          $err = $e ;
        }
      }

      // if there's been an error, at any point, we rollback any transactions for this address
      if (is_null($err))
      {
        // most excellent! no errors at all, so we commit... finally!
       if ($useSavepoint) { $conn->commit(__FUNCTION__) ; } else { $conn->commit() ; }

        // commit our results to our final results array
        $results[$index] = $addrId ;

        // release the value on our input array
        unset($addresses[$index]) ;
      }
      else
      {
        // log our error
        sfContext::getInstance()->getLogger()->err($errMsg) ;

        // rollback
        if ($useSavepoint) { $conn->rollback(__FUNCTION__) ; } else { $conn->rollback() ; }

        // ALWAYS throw an error, it's like stepping on a crack if you don't
        if ($throwOnError) { throw $err ; }
      }
    }

    // Whew!! Now that that's over, let's just return our two results
    return array($results, array_keys($addresses)) ;
  }

  /**
   * Simple method to retrieve an address value / element.
   *
   * @param integer $elementId The element id being queried.
   * @param string $value String value being searched for.
   * @param Doctrine_Connection $conn A doctrine connection object.
   * @return integer|array This is a little funny behaviour or HYDRATE_SINGLE_SCALAR's part. If no
   * value is returned from the query it actually returns an empty array instead of a NULL (which
   * would seem more appropriate). Use empty() to check if there's an actual value.
   */
  public function getAddressValueId($elementId, $value, $conn = NULL)
  {
    $q = agDoctrineQuery::create()
      ->select('av.id')
        ->from('agAddressValue av')
        ->where('av.address_element_id = ?', $elementId)
          ->andWhere('av.value = ?', $value) ;

    if (! is_null($conn)) { $q->setConnection($conn) ; }

    // set up our initial cache
    $q->useResultCache(TRUE, 1800);
    
    // execute
    $result = $q->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);

    // clear the cache if we had no result
    if (empty($result) || is_null($result))
    {
      $cacheDriver = Doctrine_Manager::getInstance()->getAttribute(Doctrine_Core::ATTR_RESULT_CACHE);
      $cacheDriver->delete($q->getResultCacheHash());
    }

    return $result;
  }

  /**
   * Simple method to return the current working address standard id.
   *
   * @return integer address_standard_id
   */
  public function getAddressStandardId()
  {
    return $this->_returnStandardId;
  }

  /**
   * A simple getter to return the current working elements allowed for this address.
   *
   * @return array $result An associative array (address_element_id => address_element).
   */
  public function getAddressAllowedElements()
  {
    return $this->_addressAllowedElements ;
  }
}
