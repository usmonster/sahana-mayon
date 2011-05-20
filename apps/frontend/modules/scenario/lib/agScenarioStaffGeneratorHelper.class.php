<?php
/**
 * Provides methods for the saving and generation of ScenarioStaff queries.
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author Chad Heuschober, CUY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 *
 */
class agScenarioStaffGeneratorHelper extends agSearchHelper
{
  protected static  $defaultSearchType = 'doctrine_query_simple',
                    $defaultSearchTypeId ;

  /**
   * Simple static getter to get and/or lazily load the $defaultSearchTypeId property.
   * @return integer default search type id. 
   */
  public static function getDefaultSearchTypeId()
  {
    // if it's not set, lazy load it
    if (! isset(self::$defaultSearchTypeId))
    {
      self::$defaultSearchTypeId = self::getSearchTypeId(self::$defaultSearchType);
    }
    // either way, return it
    return self::$defaultSearchTypeId ;
  }

  public static function setScenarioStaffGenerator( $scenarioId,
                                                    $searchName,
                                                    $searchWeight,
                                                    $searchCondition,
                                                    Doctrine_Connection $conn = NULL)
  {
    // get our default settings
    $searchTypeId = self::getDefaultSearchTypeId();
    if (is_null($conn)) { $conn = Doctrine_Manager::connection(); }
    $useSavepoint = ($conn->getTransactionLevel() > 0) ? TRUE : FALSE;
    $err = NULL;

    // spin up a new transaction / savepoint
    if ($useSavepoint)
    {
      $conn->beginTransaction(__FUNCTION__);
    }
    else
    {
      $conn->beginTransaction();
    }

    // create a new scenario staff generator object
    $newSsg = new agScenarioStaffGenerator();
    $newSsg['scenario_id'] = $scenarioId;
    $newSsg['search_weight'] = $searchWeight;

    // get and/or create our search id
    try
    {
      $newSsg['search_id'] = self::getSearchId($searchCondition, TRUE, $searchName, $searchTypeId,
        $conn);
    }
    catch(Exception $e)
    {
      $err = $e;
    }

    // we pull this record into a collection so we can
    $newColl = Doctrine_Collection::create('agScenarioStaffGenerator');
    $newColl->add($newSsg);

    // get our current record (if one exists)
    $coll = agDoctrineQuery::create()
      ->select('ssg.*')
        ->from('agScenarioStaffGenerator ssg')
        ->where('ssg.scenario_id = ?', $newSsg['scenario_id'])
          ->addWhere('ssg.scenario_id = ?', $newSsg['search_id'])
      ->execute();

    // merge the two
    $coll = $coll->merge($newColl);

    try
    {
      $coll->save($conn);
      if ($useSavepoint) { $conn->commit(__FUNCTION__); } else { $conn->commit(); }
    }
    catch(Exception $e)
    {
      $err = $e;
    }

    if (! is_null($err))
    {
      // log our error
      $errMsg = sprintf('%s failed to create new generator record with conditions %s',
        __FUNCTION__, json_encode($searchCondition));
      sfContext::getInstance()->getLogger()->err($errMsg);

      // rollback and re-throw
      if ($useSavepoint) { $conn->rollback(__FUNCTION__); } else { $conn->rollback(); }
      throw $e;
    }

    // return
    return $newSsg->getId();
  }

  /**
   * Method to generate a scenario staff pool using searches stored in the staff generator.
   * @param integer $scenarioId A scenario id
   * @param boolean $keepExisting Optional boolean to control whether or not existing pool entries
   * will be retained (and only updated for weight)
   * @param Doctrine_Connection $conn An optional doctrine connection object
   * @return integer The record count for the newly created scenario staff pool
   */
  public static function generateStaffPool( $scenarioId,
                                            $keepExisting = FALSE,
                                            Doctrine_Connection $conn = NULL)
  {
    // not exactly our final results set, but a pretty important interim one
    $staffResources = array();

    // get all of the searches we're expected to execute
    $searches = self::getScenarioSearches($scenarioId);

    // loop through our searches and execute, storing the results in $staffResources
    foreach($searches as $searchId => $searchWeight)
    {
      foreach (self::executeStaffSearch($searchId) as $staffResourceId)
      {
        $staffResources[$staffResourceId] = $searchWeight ;
      }
    }

    // now it's time to process the differences between the existing data collection and the new
    $coll = self::getScenarioStaffResource($scenarioId);
    foreach($coll as $index => &$record)
    {
      // not necessary, but it sure makes it easier to read
      $staffResourceId = $record['staff_resource_id'];

      if (array_key_exists($staffResourceId, $staffResources))
      {
        // if the scenario staff resource exists, update its search weight (which may have changed)
        $record['deployment_weight'] = $staffResources[$staffResourceId];
        unset($staffResources[$staffResourceId]);
      }
      elseif (! $keepExisting)
      {
        // if it doesn't exist, we remove the entry from the collection (effectively deleting it)
        $coll->remove($index);
      }
    }

    // we've now done updates and deletes, let's handle our inserts
    foreach ($staffResources as $staffResourceId => $searchWeight)
    {
      // create a new record
      $newRec = new agScenarioStaffResource();
      $newRec['scenario_id'] = $scenarioId;
      $newRec['staff_resource_id'] = $staffResourceId;
      $newRec['deployment_weight'] = $searchWeight;

      // add it to our collection and remove it from the $staffResources array
      $coll->add($newRec);
      unset($staffResources[$staffResourceId]);
    }

    // our staff resource collection has been updated, all that's left is to save
    // take note: the doctrine method for processing diffs incurs minimal I/O, however it may cause
    // confusion, if you're examining query output since very few may be run at all

    // first we need to pick up a connection if not passed one and start a transaction
    if (is_null($conn)) { $conn = Doctrine_Manager::connection(); }
    $useSavepoint = ($conn->getTransactionLevel() > 0) ? TRUE : FALSE;
    if ($useSavepoint)
    {
      $conn->beginTransaction(__FUNCTION__);
    }
    else
    {
      $conn->beginTransaction();
    }

    try
    {
      // save and commit
      $coll->save($conn);
      if ($useSavepoint) { $conn->commit(__FUNCTION__); } else { $conn->commit(); }
    }
    catch(Exception $e)
    {
      // if that didn't work, log our error
      $errMsg = sprintf('%s failed to generate a staff pool for scenarioId: %s',
        __FUNCTION__, $scenarioId);
      sfContext::getInstance()->getLogger()->err($errMsg);

      // rollback and re-throw
      if ($useSavepoint) { $conn->rollback(__FUNCTION__); } else { $conn->rollback(); }
      throw $e;
    }

    // if all went well, we'll return the number of records in our new staff pool
    return count($coll);
  }

  /**
   * Method to execute a staff search and return its results set.
   * @param <type> $searchId An agSearch id value.
   * @return array A single-dimension array of staff resource ids.
   */
  public static function executeStaffSearch($searchId)
  {
    // build our basic staff search query
    $q = self::returnBaseStaffSearch();

    // parse the query and add search conditions
    $q = self::parseQuery($q, $searchId);

    // execute and return
    return $q->execute(array(), agDoctrineQuery::HYDRATE_SINGLE_VALUE_ARRAY);
  }

  /**
   * Method to execute a preview search by simply passing the conditions array.
   * @param array $conditions An array of search conditions for staff preview.
   * <code>array(
   *   array(
   *     'condition'=>'4',
   *     'operator'=>'!=',
   *     'field'=>'agStaffResourceType.staff_resource_type_id'
   *   ), ...
   * )</code>
   * @return array A monodimensional array of staffIds.
   */
  public static function executeStaffPreview($conditions)
  {
    // pick up our default search type method
    $method = self::$constructorMethods[self::$defaultSearchType];

    // build a base query object and add our conditions
    $q = self::returnBaseStaffSearch();
    $q = self::$method($q, $conditions);

    return $q->execute(array(), agDoctrineQuery::HYDRATE_SINGLE_VALUE_ARRAY);
  }

  /**
   * Method to return base staff search query object.
   * @return agDoctrineQuery An agDoctrineQuery object
   */
  public static function returnBaseStaffSearch()
  {
    // build our basic staff search query
    $q = agDoctrineQuery::create()
        ->select('agStaffResource.staff_id')
        ->from('agStaffResource agStaffResource')
          ->innerJoin('agStaffResource.agOrganization agOrganization')
          ->innerJoin('agStaffResource.agStaffResourceType agStaffResourceType')
          ->innerJoin('agStaffResource.agStaffResourceStatus agStaffResourceStatus')
        ->where('agStaffResourceStatus.is_available = ?', TRUE);

    return $q;
  }

  /**
   * Simple method to return an array of all the searches for a given scenarioId
   * @param integer $scenarioId A scenario Id
   * @return array An associative array of searchIds and search weights.
   * <code>array($searchId => $searchWeight, ...)</code>
   */
  protected static function getScenarioSearches($scenarioId)
  {
    $q = agDoctrineQuery::create()
      ->select('ssg.search_id')
          ->addSelect('ssg.search_weight')
        ->from('agScenarioStaffGenerator ssg')
        ->where('ssg.scenario_id = ?', $scenarioId)
        ->orderBy('ssg.search_weight ASC');

    return $q->execute(array(), agDoctrineQuery::HYDRATE_KEY_VALUE_PAIR);
  }

  /**
   * Method to return a Doctrine_Collection for scenario staff resource.
   * @param integer $scenarioId The scenarioId being queried.
   * @return Doctrine_Collection A doctrine collection object.
   */
  protected static function getScenarioStaffResource($scenarioId)
  {
    $q = agDoctrineQuery::create()
      ->select('ssr.*')
        ->from('agScenarioStaffResource ssr')
        ->where('ssr.scenario_id = ?', $scenarioId);
    return $q->execute();
  }
}