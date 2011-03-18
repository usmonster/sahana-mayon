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
 */
class agAddressHelper extends agBulkRecordHelper
{
  // these constants map to the address get types that are supported in other function calls
  const     ADDR_GET_TYPEID = 'getAddressComponentsById',
            ADDR_GET_GEO = 'getAddressCoordinates',
            ADDR_GET_TYPE = 'getAddressComponentsByName',
            ADDR_GET_LINE = 'getAddressComponentsByLine',
            ADDR_GET_STRING = 'getAddressAsString';

  public    $lineDelimiter = "<br />",
            $enforceComplete = TRUE,
            $enforceLineNumber = FALSE,
            $checkValuesForCompleteness = FALSE ;

  protected $_globalDefaultAddressStandard = 'default_address_standard',
            $_globalDefaultAddressGeoType = 'default_address_geo_type',
            $_startingLineNumber = 1,
            $_addressFormatComponents = array(),
            $_addressFormatRequired = array(),
            $_addressAllowedElements = array(),
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
    parent::__construct($addressIds);

    // set our default address standard and pick up the formatting components
    $this->_setDefaultReturnStandard() ;
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
   * Method used to construct the base query object used by other objects in this class.
   *
   * @param array $addressIds A single-dimension array of address  id's.
   * @return agDoctrineQuery An extended doctrine query object.
   */
  protected function _getAddressComponents($addressIds)
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
  public function updateAddressHashes($addressIds = NULL, $conn = NULL)
  {
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
      $addrHash = $this->hashAddress($components) ;

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
   * Method to take an address component array and return a json encoded, md5sum'ed address hash.
   * @param array $addressComponentArray An associative array of address components keyed by
   * elementId with the string value.
   * @return string(128) A 128-bit md5sum string.
   */
  protected function hashAddress($addressComponentArray)
  {
    // first off, we don't trust the sorting of the address components so we do our own
    ksort($addressComponentArray) ;

    // we json encode the return to
    return md5(json_encode($addressComponentArray)) ;
  }

  /**
   * A quick helper method to take in an array address hashes and return an array of address ids.
   * @param array $addressHashes A monodimensional array of md5sum, json_encoded address hashes.
   * @return array An associative array, keyed by address hash, with a value of address_id.
   */
  public function getAddressIdsByHash($addressHashes)
  {
    $q = agDoctrineQuery::create()
      ->select('a.address_hash')
          ->addSelect('a.id')
        ->from('agAddress a')
        ->whereIn('a.address_hash',$addressHashes) ;

    return $q->execute(array(), 'key_value_pair') ;
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
   *      [1] => $addressStanardId
   *    ),
   *    ...
   * )
   * </code>
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
  public function setAddresses($addresses, Doctrine_Connection $conn = NULL)
  {
    // declare our results array
    $results = array() ;

    // declare the flipped array we use for the first pass search
    $searchArray = array() ;
    
    // loop through the addresses, hash the components, and build the hash-keyed search array
    foreach($addresses as $index => $addressComponents)
    {
      $hash = $this->hashAddress($addressComponents[0]) ;
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

    // now that we have all of the 'existing' addresses, let's build the new ones
    $newAddresses = $this->setNewAddresses($addresses) ;
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
  protected function setNewAddresses($addresses, Doctrine_Connection $conn = NULL)
  {
    // we'll use this like a cache and check against it with each successive execution
    $valuesCache = array() ;

    // declare our results array
    $results = array() ;

    // pick up the default connection if one is not passed
    if (is_null($conn)) { $conn = Doctrine_Manager::connection() ; }

    // loop through our addresses and the components
    foreach ($addresses as $index => $components)
    {
      // we do this so we only have to call rollback / unset once, plus it's nice to have a bool to
      // check on our own
      $err = FALSE ;

      // if for whatever reason we're not passed a standard, pick up the default
      if (! isset($components[1])) { $components[1] = $this->_returnStandardId ; }


      // similarly, we want to wrap this whole sucker in a transaction
      $conn->beginTransaction() ;

      // build a results cache so we commit entire addresses at once, not just individual elements
      $resultsCache = array() ;

      foreach($components[0] as $elementId => $value)
      {
        // if we've already picked up this address value, GREAT! just load it from our
        // cache and keep going!
        if (isset($valuesCache[$elementId][$value]))
        {
          $resultsCache[$elementId] = $valuesCache[$elementId][$value] ;
        }
        else
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
              $valuesCache[$elementId][$value] = $valueId ;
              $resultsCache[$elementId] = $valueId ;
            }
            catch(Exception $e)
            {
              // if we run into a problem, set this once rollback will roll it all back at the end
              $err = TRUE ;
              break ;

            }
          }
        }
      }

      // now we attempt to insert the new address_id with all of our value bits, again only useful
      // if we've not already had an error
      if (! $err)
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
          // if we run into a problem, set this once rollback will roll it all back at the end
          $err = TRUE ;
        }
      }

      // the final step!!! inserting into agAddressMjAgAddressValue
      foreach ($resultsCache as $rElem => $rValueId)
      {
        // if we at any point pick up an error, don't bother
        if ($err) { break ; }

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
          // if we run into a problem, set this once rollback will roll it all back at the end
          $err = TRUE ;
        }
      }

      // if there's been an error, at any point, we rollback any transactions for this address
      if ($err)
      {
        $conn->rollback() ;
      }
      else
      {
        // most excellent! no erors at all, so we commit... finally!
        $conn->commit() ;

        // commit our results to our final results array
        $results[$index] = $addrId ;

        // release the value on our input array
        unset($addresses[$index]) ;
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

    return $q->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
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
