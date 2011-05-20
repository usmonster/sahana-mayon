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
class agGeoHelper extends agBulkRecordHelper
{
  public    $enforceGeoType = TRUE ;

  protected $_globalDefaultAddressGeoType = 'default_address_geo_type',
            $_globalDefaultGeoMatchScore = 'default_geo_match_score',
            $_agGeoTypeData,
            $_addressGeoTypeId,
            $_defaultGeoMatchScoreId;

  /**
   * Minimalist helper function to return the _agGeoTypeData property and lazy-load its values
   * from the table if not currently instantiated.
   * @param integer $geoTypeId As a
   * @return array $_agGeoTypeData
   * <code>
   * array([$geoTypeId] => array([0] => $minCoordinates, [1] => $maxCoordinates), ...)
   * </code>
   */
  public function getAgGeoTypeData($geoTypeId)
  {
    if (! isset($this->_agGeoTypeData)) { $this->setAgGeoTypeData() ; }
    return $this->_agGeoTypeData[$geoTypeId] ;
  }

  /**
   * Minimalist helper function to load agGeoTypeData with data from the agGeoType table.
   */
  protected function setAgGeoTypeData()
  {
    // create our query object
    $q = agDoctrineQuery::create()
      ->select('gt.id')
          ->addSelect('minimum_coordinate_points')
          ->addSelect('maximum_coordinate_points')
        ->from('agGeoType gt') ;

    // execute and set our property
    $this->_agGeoTypeData = $q->execute(array(), agDoctrineQuery::HYDRATE_KEY_VALUE_ARRAY) ;
  }

  /**
   * Small getter to lazily load and return the default address geo-type id.
   * @return integer Address Geo-Type Id. 
   */
  public function getAddressGeoTypeId()
  {
    if (! isset($this->_addressGeoTypeId)) { $this->setAddressGeoTypeId() ; }
    return $this->_addressGeoTypeId ;
  }

  /**
   *  Minimalist helper function to load the default geo-type for addresses.
   */
  protected function setAddressGeoTypeId()
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
   * Small getter to lazily load and return the default geoMatchScoreId.
   * @return integer Default geoMatchScoreId.
   */
  public function getDefaultGeoMatchScoreId()
  {
    if (! isset($this->_defaultGeoMatchScoreId)) { $this->setDefaultGeoMatchScoreId() ; }
    return $this->_defaultGeoMatchScoreId ;
  }

  /**
   * Minimalist function to load and set the _defaultGeoMatchScoreId class property.
   */
  protected function setDefaultGeoMatchScoreId()
  {
    $geoMatchScore = agGlobal::getParam($this->_globalDefaultGeoMatchScore) ;
    $geoMatchScoreId = agDoctrineQuery::create()
      ->select('gms.id')
        ->from('agGeoMatchScore gms')
        ->where('gms.geo_match_score = ?', $geoMatchScore)
        ->execute(array(),DOCTRINE_CORE::HYDRATE_SINGLE_SCALAR) ;
  }

  /**
   * Method to test whether a geoFeature has the correct number of coordinate points.
   * @param integer $geoTypeId The geoTypeId being tested
   * @param array $geoCoordinates An array of geo coordinates
   * @return boolean Boolean indicating whether or not the geo feature is valid.
   */
  public function isValidGeoFeature($geoTypeId, array $geoCoordinates)
  {
   
    // grab our coordinate counts and our requirements
    $gcCount = count($geoCoordinates) ;
    $gcReqs = $this->getAgGeoTypeData($geoTypeId) ;

    // do our comparison and return results
    if (($gcReqs[0] != -1 && $gcCount < $gcReqs[0]) || ($gcReqs[1] != -1 && $gcCount > $gcReqs[1]))
    {
      return FALSE ;
    }

    return TRUE ;
  }

  /**
   * A method uniquely reduce the $geoCoordinate array before passing it to _setGeo
   * @param array $geoCoordinates An array of geo information keyed by an index
   * <code>
   * array([$index] =>
   *   array(
   *     array(
   *       array($latitude, $longitude),
   *       ...),
   *     $matchScoreId)
   *   ...)
   * </code>
   * @param integer $geoTypeId An integer value of the geoTypeId
   * @param integer $geoSourceId An integer value describing the geoSource
   * @param boolean $throwOnError A boolean to determine whether or not errors will trigger an
   * exception or be silently ignored (rendering an address 'optional'.
   * @param Doctrine_Connection $conn A doctrine connection object.
   * @return array An array of geoIds keyed by the same $index passed in the $geoCoordinates param.
   */
  public function setGeo( array $geoCoordinates,
                          $geoTypeId,
                          $geoSourceId,
                          $throwOnError = NULL,
                          Doctrine_Connection $conn = NULL)
  {
    // this array will store our uniq coordinates (so we don't over-process)
    $uniqCoordinates = array() ;
    $geoIds = array() ;
    
    // loop through the coordinate array and make our unique coordinate array
    foreach ($geoCoordinates as $index => $coordinateArray)
    {
      $uniqIndex = array_search($coordinateArray, $uniqCoordinates, TRUE) ;
     
      // if the coordinate array doesn't exist yet, add it
      if ($uniqIndex === FALSE)
      {
        $uniqCoordinates[] = $coordinateArray ;
        $uniqIndex = max(array_keys($uniqCoordinates)) ;
      }

      // either way, return the new unique index to the geoCoordinates array to map back later
      $geoCoordinates[$index] = $uniqIndex ;
    }

    // s/get unique geo values
    $geoIds = $this->_setGeo($uniqCoordinates, $geoTypeId, $geoSourceId, $throwOnError, $conn) ;

   // loop through the coordinates outer array and attach the new, unique coordinates
   foreach($geoCoordinates as $index => &$uniqIndex)
   {
     if (array_key_exists($uniqIndex, $geoIds))
     {
       $uniqIndex = $geoIds[$uniqIndex] ;
     }
     else
     {
       // we can't return broken / unsettable coordinates so we'll unset them
       unset($geoIds[$uniqIndex]) ;
     }
   }

   return $geoCoordinates ;

  }
  
  /**
   * A method set and/or retrieve geoIds based on their coordinate values or the hash thereof.
   *
   * @param array $geoCoordinates An array of geo information keyed by an index
   * <code>
   * array([$index] =>
   *   array(
   *     array(
   *       array($latitude, $longitude),
   *       ...),
   *     $matchScoreId)
   *   ...)
   * </code>
   * @param integer $geoTypeId An integer value of the geoTypeId
   * @param integer $geoSourceId An integer value describing the geoSource
   * @param boolean $throwOnError A boolean to determine whether or not errors will trigger an
   * exception or be silently ignored (rendering an address 'optional'.
   * @param Doctrine_Connection $conn A doctrine connection object.
   * @return array An array of geoIds keyed by the same $index passed in the $geoCoordinates param.
   */
  protected function _setGeo( array $geoCoordinates,
                              $geoTypeId,
                              $geoSourceId,
                              $throwOnError = NULL,
                              Doctrine_Connection $conn = NULL)
  {
    // explicit declarations of our interim and results arrays
    $geoIds = array() ;
    $geoHashIds = array() ;
    $gcHashes = array() ;

    // determine whether or not we'll explicitly throw exceptions on error
    if (is_null($throwOnError)) { $throwOnError = $this->throwOnError ; }

    foreach($geoCoordinates as $index => $gc)
    {
      // if we're enforcing types, do the check now and remove unsettable coordinates
      if ($this->enforceGeoType && ! $this->isValidGeoFeature($geoTypeId, $gc))
      {
        // prepare our error message
        $geoTypeMsg = sprintf('The coordinates, %s, are invalid for geo type %s and could not be set.',
            json_encode($gc), $geoTypeId );

        if ($throwOnError)
        {
          throw new Exception($geoTypeMsg) ;
        }
        else
        {
          sfContext::getInstance()->getLogger()->warning($geoTypeMsg) ;
        }

        unset($geoCoordinates[$index]) ;
        continue ;
      }
      
      // calculate our coordinate hashes
      $gcHashes[$index] = agBulkRecordHelper::getRecordComponentsHash($gc, TRUE);
    }

    // grab any existing ids from the db
    $geoHashIds = $this->getGeoIdsByHash(array_values($gcHashes)) ;

    // loop through our returned results and separate the existing from the non-
    foreach ($geoCoordinates as $index => &$gc)
    {
      $gcHash = $gcHashes[$index] ;
      if (array_key_exists($gcHash, $geoHashIds))
      {
        // add it to our final results array and release our resource
        $geoIds[$index] = $geoHashIds[$gcHash] ;
        unset($gcHashes[$index]) ;
        unset($geoCoordinates[$index]) ;
      }
      else
      {
        // if it wasn't set, let's reconfigure our coordinates values so we can pass them forward
        $gc = array($gc, $gcHash) ;
      }
        // either way, we don't need this resource anymore
        unset($geoHashIds[$gcHash]) ;
    }

    // set up our transaction
    if (is_null($conn)) { $conn = Doctrine_Manager::connection() ; }
    $useSavepoint = ($conn->getTransactionLevel() > 0) ? TRUE : FALSE ;
    if ($useSavepoint)
    {
      $conn->beginTransaction(__FUNCTION__) ;
    }
    else
    {
      $conn->beginTransaction() ;
    }

    // set our new geo data
    try
    {
      $geoIds = $geoIds + $this->setNewGeo($geoCoordinates, $geoTypeId, $geoSourceId,
          $throwOnError, $conn) ;
      if ($useSavepoint) { $conn->commit(__FUNCTION__) ; } else { $conn->commit() ; }
    }
    catch(Exception $e)
    {
      // log our error and rollback
      $errMsg = 'Could not set all geo coordinates. Rolling back the batch!' ;
      sfContext::getInstance()->getLogger()->err($errMsg) ;
      if ($useSavepoint) { $conn->rollback(__FUNCTION__) ; } else { $conn->rollback() ; }

      // throw if directed to do so
      if ($throwOnError) { throw $e ; }
    }
        
    return $geoIds ;
  }

  /**
   * A method to set new geoIds based on a set of geo coordinate data.
   *
   * @param array $geoCoordinates An array of geo information keyed by an index
   * <code>
   * array([$index] =>
   *   array(
   *     array(
   *       array($latitude, $longitude),
   *       ...),
   *     $matchScoreId)
   *   ...)
   * </code>
   * @param integer $geoTypeId An integer value of the geoTypeId
   * @param integer $geoSourceId An integer value describing the geoSource
   * @param boolean $throwOnError A boolean to determine whether or not errors will trigger an
   * exception or be silently ignored (rendering an address 'optional'.
   * @param Doctrine_Connection $conn A doctrine connection object.
   * @return array An array of geoIds keyed by the same $index passed in the $geoCoordinates param.
   */
  public function setNewGeo(array $geoCoordinates,
                            $geoTypeId,
                            $geoSourceId,
                            $throwOnError = NULL,
                            Doctrine_Connection $conn = NULL)
  {
    // set up some explict vars for later use
    $geoIds = array() ;

    // pick up the default connection and error throw prerogative if one is not passed
    if (is_null($conn)) { $conn = Doctrine_Manager::connection() ; }
    if (is_null($throwOnError)) { $throwOnError = $this->throwOnError ; }

    // loop through all the coordinate data
    foreach ($geoCoordinates as $index => $geoValues)
    {
      $err = NULL ;
      $errMsg = 'This is a generic error message for setNewGeo. You should never receive this
        message. If you are recieving this error, there is a problem in your error-handling code.' ;

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

      // first create a new geoId that we can later use when setting coordinates
      $gObj = new agGeo() ;
      $gObj['geo_coordinate_hash'] = $geoValues[1] ;
      $gObj['geo_type_id'] = $geoTypeId ;
      $gObj['geo_source_id'] = $geoSourceId ;

      // while we're at it, let's create our collection for geoFeature
      $gfColl = new Doctrine_Collection('agGeoFeature') ;

      // save and/or catch issues
      try
      {
        $gObj->save($conn) ;
        $gId = $gObj->getId() ;
      }
      catch(Exception $e)
      {
        $err = $e ;
        $errMsg = sprintf('Unable to create new geoId identified by hash %s', $geoValues[1]) ;
      }

      if (is_null($err))
      {
        // loop through each individual coordinate pair
        foreach($geoValues[0] as $order => $coordinates)
        {
          $gcId = $this->getGeoCoordinateId($coordinates[0], $coordinates[1]) ;

          // if we didn't get a value, we need to create one
          if (empty($gcId))
          {
            // set up our new geo-coordinate object
            $gcObj = new agGeoCoordinate() ;
            $gcObj['latitude'] = $coordinates[0] ;
            $gcObj['longitude'] = $coordinates[1] ;

            // save and/or catch issues
            try
            {
              $gcObj->save($conn) ;
              $gcId = $gcObj->getId() ;
            }
            catch(Exception $e)
            {
              $err = $e ;
              $errMsg = sprintf('Unable to create new coordinate pair identified by %s',
                json_encode($coordinates)) ;
              break ;
            }
          }
          // now, let's create a geo feature record
          $gfObj = new agGeoFeature() ;
          $gfObj['geo_id'] = $gId ;
          $gfObj['geo_coordinate_id'] = $gcId ;
          $gfObj['geo_coordinate_order'] = $order ;

          // and add it to our geoFeature collection, then continue the loop (without saving)
          $gfColl->add($gfObj) ;
        }
      }
      // alright, now that geoFeature is complete, let's save geoFeature and try a commit
      if (is_null($err))
      {
        try
        {
          $gfColl->save($conn) ;
          if ($useSavepoint) { $conn->commit(__FUNCTION__) ; } else { $conn->commit() ; }
        }
        catch(Exception $e)
        {
          $err = $e ;
          $errMsg = sprintf('Unable to create new geoFeature identified by hash %s', $geoValues[1]);
        }
      }
      // if it all went well, we add geoId to our results and if not, we rollback
      if (is_null($err))
      {
        $geoIds[$index] = $gId ;
        unset($geoCoordinates[$index]) ;
      }
      else
      {
        sfContext::getInstance()->getLogger()->err($errMsg) ;
        if ($useSavepoint) { $conn->rollback(__FUNCTION__) ; } else { $conn->rollback() ; }

        // throw if directed to do so
        if ($throwOnError) { throw $err ; }
      }
    }
    // nothing fancy here, just the end of the function and a chance to return our values
    return $geoIds ;
  }

  /**
   * Method to return a geoId based on a coordinate hash.
   * @param string(128) $gcHashes A monodimensional array of md5-summed, json-encoded hashes of
   * ordered coordinates.
   * @return array The a monodimensional associative array keyed by geo_coordinate_hash with geoid
   * as the value.
   */
  public function getGeoIdsByHash($gcHashes)
  {
    $results = array();
    $cacheDriver = Doctrine_Manager::getInstance()->getAttribute(Doctrine_Core::ATTR_RESULT_CACHE);

    $q = agDoctrineQuery::create()
      ->select('g.id')
        ->from('agGeo g')
      ->useResultCache(TRUE, 1800);

    foreach ($gcHashes as $index => $hash)
    {
      $q->where('g.geo_coordinate_hash = ?', $hash);
      $gId = $q->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);

      if (empty($gId))
      {
        $cacheDriver->delete($q->getResultCacheHash());
      }
      else
      {
        $results[$hash] = $gId;
      }
      unset($gcHashes[$index]);
    }
 
    return $results;
  }

  /**
   * Method to return geo coordinate id's
   * @param decimal $latitude The coordinate latitude
   * @param decimal $longitude The coordinate longitude
   * @return integer The coordinate Id
   */
  public function getGeoCoordinateId($latitude, $longitude)
  {
    $q = agDoctrineQuery::create()
      ->select('gc.id')
        ->from('agGeoCoordinate gc')
        ->where('gc.latitude = ?', $latitude)
          ->andWhere('gc.longitude = ?', $longitude)
      ->useResultCache(TRUE, 1800);
    
    $result = $q->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
    
    if (empty($result) || is_null($result))
    {
      $cacheDriver = Doctrine_Manager::getInstance()->getAttribute(Doctrine_Core::ATTR_RESULT_CACHE);
      $cacheDriver->delete($q->getResultCacheHash());
    }

    return $result;
  }

  /**
   * Method to update all geo hashes in the database.
   * @deprecated This should never be used as it does not scale well and is unnecessary. It was
   * created to update the initial coordinate hashes for example files.
   */
  protected function updateGeoHashes()
  {
    $coordinateData = agDoctrineQuery::create()
      ->select('gf.geo_id')
          ->addSelect('gc.latitude')
          ->addSelect('gc.longitude')
        ->from('agGeoFeature gf')
          ->innerJoin('gf.agGeoCoordinate gc')
        ->orderBy('gf.geo_coordinate_order')
      ->execute(array(), Doctrine_Core::HYDRATE_NONE);

    $groupedData = array() ;
    foreach ($coordinateData as $data)
    {
      $groupedData[$data[0]][] = array($data[1], $data[2]) ;
    }

    foreach ($groupedData as $geoId => $data)
    {
      $hash = agBulkRecordHelper::getRecordComponentsHash($data, TRUE);
      $q = agDoctrineQuery::create()
        ->update('agGeo')
          ->set('geo_coordinate_hash', "'".$hash."'")
          ->where('id = ?', $geoId) ;
      $results = $q->execute() ;
    }

    return $results ;
  }

  /**
   * A method to set address geo many-to-many table based on the set of address id and geo id.
   *
   * @param array $addrCoordinates An array of geo information keyed by addressId
   * <code>
   * array([$addressId] =>
   *   array(
   *     array(
   *       array($latitude, $longitude),
   *       ...),
   *     $matchScoreId)
   *   ...)
   * </code>
   * @param integer $geoSourceId An integer value describing the geoSource
   * @param boolean $throwOnError A boolean to determine whether or not errors will trigger an
   * exception or be silently ignored (rendering an address 'optional'.
   * @param Doctrine_Connection $conn A doctrine connection object.
   * @param Doctrine_Connection $conn
   * @return integer The number of collection of the size of the collection.
   */
  public function setAddressGeo(array $addrCoordinates,
                                $geoSourceId,
                                $geoTypeId = NULL,
                                $throwOnError = NULL,
                                Doctrine_Connection $conn = NULL)
  {
    // array($addrId => array(array(array($latitude, $longitude), ...), $matchScoreId), ...)
    // set up some important buckets
    $addrMatchScores = array() ;
    $err = NULL ;
    $results = 0 ;

    // pick up global defaults if not passed parameters
    $defaultMatchScoreId = $this->getDefaultGeoMatchScoreId() ;
    if (is_null($geoTypeId)) { $geoTypeId = $this->getAddressGeoTypeId() ; }
    if (is_null($throwOnError)) { $throwOnError = $this->throwOnError ; }
    if (is_null($conn)) { $conn = Doctrine_Manager::connection() ; }

    // fire up our savepoint
    $useSavepoint = ($conn->getTransactionLevel() > 0) ? TRUE : FALSE ;
    if ($useSavepoint)
    {
      $conn->beginTransaction(__FUNCTION__) ;
    }
    else
    {
      $conn->beginTransaction() ;
    }

    // reduce the complexity of our addrCoordinates array and separate match scores
    foreach ($addrCoordinates as $addrId => &$coordinates)
    {
      if (array_key_exists(1, $coordinates))
      {
        $addrMatchScores[$addrId] = $coordinates[1] ;
      }

      $coordinates = $coordinates[0] ;
    }

    try
    {
      $addrCoordinates = $this->setGeo($addrCoordinates, $geoTypeId, $geoSourceId,
        $throwOnError, $conn) ;
    }
    catch(Exception $e)
    {
      $err = $e ;
      $errMsg = sprintf('Failed to execute setGeo in %s.', __FUNCTION__) ;
    }


    if (is_null($err))
    {
      $q = agDoctrineQuery::create()
        ->select('ag.*')
          ->from('agAddressGeo ag INDEXBY ag.address_id')
          ->whereIn('ag.address_id', array_keys($addrCoordinates));
      $coll = $q->execute() ;

      // loop through the collection first, updating match score and geo_id
      foreach ($coll as $addrId => &$agRec)
      {
        // deal with match scores first since differences in old/new geo need to be detected
        if (array_key_exists($addrId, $addrMatchScores))
        {
          // if we were originally passed a match score for this geo, set it
          $agRec['geo_match_score_id'] = $addrMatchScores[$addrId] ;
          unset($addrMatchScores[$addrId]) ;
        }
        else
        {
          // otherwise, only set the default of geoId's are not equal (eg, new)
          if ($agRec['geo_id'] != $addrCoordinates[$addrId])
          {
            $agRec['geo_match_score_id'] = $defaultMatchScoreId ;
          }
        }

        // set our geo and release these resources from our coordinate / final arrays
        $agRec['geo_id'] = $addrCoordinates[$addrId] ;
        unset($addrCoordinates[$addrId]) ;
      }

      // loop through the remaining $addrGeoIds and make new entries
      foreach ($addrCoordinates as $addrId => $geoId)
      {
        $newRec = new agAddressGeo() ;
        $newRec['address_id'] = $addrId ;
        $newRec['geo_id'] = $geoId ;

        if (array_key_exists($addrId, $addrMatchScores))
        {
          // if we were originally passed a match score for this geo, set it
          $newRec['geo_match_score_id'] = $addrMatchScores[$addrId] ;
          unset($addrMatchScores[$addrId]) ;
        }
        else
        {
          // otherwise we'll use the default match score
          $newRec['geo_match_score_id'] = $defaultMatchScoreId ;
        }
        // add the new record to our collection and release the resource
        $coll->add($newRec) ;
        unset($addrCoordinates[$addrId]) ;
      }

      try
      {
        $coll->save($conn) ;
        if ($useSavepoint) { $conn->commit(__FUNCTION__) ; } else { $conn->commit() ; }
      }
      catch(Exception $e)
      {
        $err = $e ;
        $errMsg = sprintf('Failed to upsert into table agAddressGeo in function %s.', __FUNCTION__);
      }
    }

    // if nothing is awry, let's count how many objects are in our collection, otherwise deal
    // with our errors
    if (is_null($err))
    {
      $results = count($coll) ;
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

    // many happy returns
    return $results ;
  }

  /**
   * Method to return geo match score ids from geo match score values.
   * @param array $geoMatchScores An array of geo_match_scores
   * @return array An associative array of geo match score ids keyed by geo match score.
   */
  static public function getGeoMatchScoreIds(array $geoMatchScores)
  {
    return agDoctrineQuery::create()
      ->select('gms.geo_match_score')
          ->addSelect('gms.id')
        ->from('agGeoMatchScore gms')
        ->whereIn('gms.geo_match_score', $geoMatchScores)
      ->useResultCache(TRUE, 3600)
      ->execute(array(), agDoctrineQuery::HYDRATE_KEY_VALUE_PAIR);
  }
}