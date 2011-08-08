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
class agStaffGeneratorHelper extends agSearchHelper
{
  const             CONN_GENERATE_STAFF = 'conn_generate_staff';

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
    // some initial variable declarations
    $err = FALSE;
    $updated = 0;
    $inserted = 0;
    $deleted = 0;
    $batchTime = agGlobal::getParam('bulk_operation_max_batch_time');
    $batchSize = agGlobal::getParam('default_batch_size');

    // get all of the searches we're expected to execute
    $searches = self::getScenarioSearches($scenarioId);

    // first we need to pick up a connection if not passed one
    if (is_null($conn)) {
        // need this guy for all the heavy lifting
        $dm = Doctrine_Manager::getInstance();

        // save for re-setting
        $currConn = $dm->getCurrentConnection();
        $currConnName = $dm->getConnectionName($currConn);
        $adapter = $currConn->getDbh();
        unset($currConn);

        // always re-parent properly
        $conn = Doctrine_Manager::connection($adapter, self::CONN_GENERATE_STAFF);
        $conn->setAttribute(Doctrine_Core::ATTR_AUTO_FREE_QUERY_OBJECTS, TRUE);
        $conn->setAttribute(Doctrine_Core::ATTR_AUTOCOMMIT, FALSE);
        $conn->setAttribute(Doctrine_Core::ATTR_VALIDATE, FALSE);
        $conn->setAttribute(Doctrine_Core::ATTR_CASCADE_SAVES, FALSE);
    }

    // set up our transaction jazz
    $useSavepoint = ($conn->getTransactionLevel() > 0) ? TRUE : FALSE;
    if ($useSavepoint)
    {
      $conn->beginTransaction(__FUNCTION__);
    }
    else
    {
      $conn->beginTransaction();
    }

    if (!$keepExisting) {
      $udq = Doctrine_Query::create($conn)
        ->update('agScenarioStaffResource ssr')
          ->set('ssr.delete_flag', '?', TRUE)
          ->where('ssr.scenario_id = ?', $scenarioId);
      try {
        $udq->execute();
      } catch(Exception $e) {
        $err = TRUE;
      }
    }


    if (!$err) {
      // loop through our searches and execute, storing the results in $staffResources
      foreach($searches as $searchId => $searchWeight) {
        // give us a little breathing room for each search
        set_time_limit($batchTime);

        $uq = Doctrine_Query::create($conn)
          ->update('agScenarioStaffResource ssr');

        // prepare our subquery
        $uqSub = $uq->createSubquery();
        $uqSub = self::parseQuery(self::getStaffSearch($uqSub), $searchId);
        $uqSubParams = $uqSub->getParams();

        // add our subquery to our main query and execute our update
        $uq->where('ssr.staff_resource_id IN (' . $uqSub->getDql() . ')', $uqSubParams['where']);
        $uq->andWhere('ssr.scenario_id = ?', $scenarioId);

        $uq->set('ssr.deployment_weight', '?', $searchWeight);
        $uq->set('ssr.delete_flag', '?', FALSE);


        // now execute the updates all in one block
        try {
          $sql = $uq->getSqlQuery();
          $updated = $uq->execute();
        } catch(Exception $e) {
          $err = TRUE;
          break;
        }

        // create our query to add new members
        $iq = agDoctrineQuery::create();
        $iq = self::parseQuery(self::getStaffSearch($iq), $searchId);
        $iq->leftJoin('agStaffResource.agScenarioStaffResource AS ssr WITH ssr.scenario_id = ?',
          $scenarioId);
        $iq->andWhere('ssr.id IS NULL');
        $irs = $iq->execute(array(), Doctrine_Core::HYDRATE_ON_DEMAND);

        $ssrTable = $conn->getTable('agScenarioStaffResource');
        $coll = new Doctrine_Collection('agScenarioStaffResource');

        foreach ($irs as $index => $ir) {
          $ssrRec = new agScenarioStaffResource($ssrTable, TRUE);
          $ssrRec['scenario_id'] = $scenarioId;
          $ssrRec['staff_resource_id'] = $ir['id'];
          $ssrRec['deployment_weight'] = $searchWeight;
          $ssrRec['delete_flag'] = FALSE;
          $coll->add($ssrRec);

          // try to save at batch intervals
          if ($index % $batchSize == 0) {
            try {
              $coll->save($conn);
              $inserted += count($coll);
            } catch(Exception $e) {
              $err=TRUE;
              break;
            }

            // regain a little memory
            $coll->free();
            $coll = new Doctrine_Collection('agScenarioStaffResource');
          }

          // if we've encountered an error, let's break out of this loop
          if ($err) {
            break;
          }
        }

        // if we have holdover records that didn't make it into the last batch
        if ($coll->isModified()) {
          try {
            $coll->save($conn);
            $inserted += count($coll);
           } catch(Exception $e) {
            $err=TRUE;
            break;
          }

          $coll->free();
          unset($coll);
        }
      }
    }

    if (!$err) {
      $dq = Doctrine_Query::create($conn)
        ->delete('agScenarioStaffResource ssr')
          ->where('ssr.delete_flag = ?', TRUE)
          ->andWhere('ssr.scenario_id = ?', $scenarioId);
      try {
        $deleted = $dq->execute();
      } catch(Exception $e) {
        $err = TRUE;
      }
    }

    // if we don't have any errors, continue happily along and commit
    if (!$err) {
      try
      {
        if ($useSavepoint) {
          $conn->commit(__FUNCTION__);
        } else {
          $conn->commit();
        }
      }
      catch(Exception $e)
      {
        $err = TRUE;
      }
    }

    // if there have been errors, rollback and re-throw
    if ($err) {
       // if that didn't work, log our error
      $errMsg = sprintf('%s failed to generate a staff pool for scenarioId: %s',
        __FUNCTION__, $scenarioId);
      sfContext::getInstance()->getLogger()->err($errMsg);

      // rollback and re-throw
      if ($useSavepoint) { $conn->rollback(__FUNCTION__); } else { $conn->rollback(); }
    }

    if (isset($currConnName)) {
      $dm->setCurrentConnection($currConnName);
      $dm->closeConnection($conn);
    }

    if ($err) {
      throw $e;
    }

    // if all went well, we'll return the number of records in our new staff pool
    return $updated + $inserted;
  }

  /**
   * Method to execute a preview search by simply passing the conditions array.
   * @param agDoctrineQuery $query An ag doctrine query object
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
  public static function executeStaffPreview(agDoctrineQuery $query, $conditions)
  {
    // pick up our default search type method
    $method = self::$constructorMethods[self::$defaultSearchType];

    // build a base query object and add our conditions
    $query = self::$method($query, $conditions);

    return $query;
  }

  /**
   * Method to return base staff search query object.
   * @param agDoctrineQuery an agDoctrineQuery object
   * @return agDoctrineQuery An agDoctrineQuery object
   */
  public static function getStaffSearch(agDoctrineQuery $q)
  {
    // build our basic staff search query
    $q->select('agStaffResource.id')
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