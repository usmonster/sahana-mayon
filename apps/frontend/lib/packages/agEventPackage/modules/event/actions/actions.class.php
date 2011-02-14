<?php

/**
 * extends agActions for event
 *
 * PHP Version 5
 *
 * LICENSE: This source file is subject to LGPLv3.0 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 *
 * @author Shirley Chan, CUNY SPS
 * @author Charles Wisniewski, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
class eventActions extends agActions
{

  public function executeIndex(sfWebRequest $request)
  {
    $this->scenarioForm = new sfForm();
    $this->scenarioForm->setWidgets(array(
      'ag_scenario_list' => new sfWidgetFormDoctrineChoice(array('multiple' => false, 'model' => 'agScenario'))
    ));

    $this->scenarioForm->getWidgetSchema()->setLabel('ag_scenario_list', false);
    $this->ag_events = Doctrine_Core::getTable('agEvent')
            ->createQuery('a')
            ->select('a.*')
            ->from('agEvent a')
            ->execute();
  }

  public function executeFgroup(sfWebRequest $request)
  {
    $this->event_id = $request->getParameter('id');
    $this->eventName = Doctrine::getTable('agEvent')
            ->findByDql('id = ?', $this->event_id)
            ->getFirst()->event_name;

    $this->active_facility_groups = agEventFacilityHelper::returnActiveFacilityGroups($this->event_id);
    $this->facility_group = '%';

    foreach ($this->active_facility_groups as $event_fgroup) {
      $facility_groups[$event_fgroup['efg_id']] = $event_fgroup['efg_event_facility_group'];
    }

    $this->facilitygroupsForm = new sfForm();
    $this->facilitygroupsForm->setWidgets(array(
      'facility_group_list' => new sfWidgetFormChoice(array('multiple' => false, 'choices' => $facility_groups))// ,'onClick' => 'submit()'))
    ));

    //the facility group choices above (if selected) will pare down the returned facility resources below FOR a facility group
    if ($request->isMethod(sfRequest::POST)) {

      if ($request->getParameter('facility_group_filter')) {
        $this->facility_group = $request->getParameter('facility_group_list');
        $this->facilitygroupsForm->setDefault('facility_group_list', $this->facility_group);
      } else {
        $fac_activation = $request->getPostParameters();
        $timeconverter = new agValidatorDateTime();
        $timeconverted = $timeconverter->convertDateArrayToString($fac_activation['facility_resource_activation']['activation_time']);
        foreach ($fac_activation['facility_resource_activation'] as $fac_activate) {
          if (is_array($fac_activate) && isset($fac_activate['operate_on'])) {
            $eFacResActivation = new agEventFacilityResourceActivationTime();

            $eFacResActivation->setActivationTime($timeconverted);
            $eFacResActivation->setEventFacilityResourceId($fac_activate['event_facility_resource_id']);
            $eFacResActivation->save();
          }
        }
      }
    }

    $this->event_facility_resources = agEventFacilityHelper::returnFacilityResourceActivation($this->event_id, $this->facility_group);

    $this->fgroupForm = new agFacilityResourceAcvitationForm($this->event_facility_resources);
  }

  public function executeDeploy(sfWebRequest $request)
  {
    $this->event_id = $request->getParameter('id');
    $this->eventName = Doctrine::getTable('agEvent')
            ->findByDql('id = ?', $this->event_id)
            ->getFirst()->event_name;
    $this->scenario_id = Doctrine_Query::create()
            ->select('scenario_id')
            ->from('agEventScenario')
            ->where('event_id = ?', $this->event_id)
            ->execute(array(), Doctrine_CORE::HYDRATE_SINGLE_SCALAR);
    $this->scenarioName = Doctrine::getTable('agScenario')
            ->findByDql('id = ?', $this->scenario_id)
            ->getFirst()->scenario;
    $this->checkResults = $this->preMigrationCheck($this->scenario_id);

    if ($request->isMethod(sfRequest::POST)) {
      $this->migrateScenarioToEvent($this->scenario_id, $this->event_id);
      $this->redirect('event/active?id=' . $this->event_id);
    }
  }

  public function executeMeta(sfWebRequest $request)
  {
    if ($request->isMethod(sfRequest::POST) && !$request->getParameter('ag_scenario_list')) {
      $this->form = new PluginagEventDefForm();
      $this->form->bind($request->getParameter($this->form->getName()), $request->getFiles($this->form->getName()));
      if ($this->form->isValid()) {
        $ag_event = $this->form->save();

        $ag_event_scenario = new agEventScenario();
        $ag_event_scenario->setScenarioId($request->getParameter('scenario_id'));
        $ag_event_scenario->setEventId($ag_event->getId());
        $ag_event_scenario->save();

        //$this->migrateScenarioToEvent($request->getParameter('scenario_id'), $ag_event->getId()); //this will create mapping from scenario to event

        if (isset($updating)) { //replace with usable check to update
          $eventStatusObject = Doctrine_Query::create()
                  ->from('agEventStatus a')
                  ->where('a.id =?', $ag_event->getId())
                  ->execute()->getFirst();
        }
        $ag_event_status = isset($eventStatusObject) ? $eventStatusObject : new agEventStatus();

        $ag_event_status->setEventStatusTypeId(3);
        $ag_event_status->setEventId($ag_event->getId());
        $ag_event_status->time_stamp = new Doctrine_Expression('CURRENT_TIMESTAMP');
        $ag_event_status->save();

        //have to do this for delete also, i.e. delete the event_scenario object
        $this->redirect('event/deploy?id=' . $ag_event->getId());
      }
    } else {
      //get scenario information passed from previous form
      //we should save the scenario that this event is based on
      if ($request->getParameter('ag_scenario_list')) {
        $this->scenario_id = $request->getParameter('ag_scenario_list');
        $this->scenarioName = Doctrine::getTable('agScenario')
                ->findByDql('id = ?', $this->scenario_id)
                ->getFirst()->scenario;
      }
      $this->metaForm = new PluginagEventDefForm();
    }
    //as a rule of thumb, actions should post to themself and then redirect
  }

  public function facilityGroupCheck($scenario_id)
  {
    $facilityGroupQuery = Doctrine_Query::create()
            ->select('aFG.id, aFG.scenario_facility_group')
            ->from('agScenarioFacilityGroup aFG')
            ->leftJoin('aFG.agScenarioFacilityResource aFR')
            ->where('aFR.id is NULL');
    $returnvalue = $facilityGroupQuery->execute(array(), 'key_value_pair');
    $facilityGroupQuery->free();
    return $returnvalue;
  }

  public function undefinedShiftCheck($scenario_id)
  {
    $undefinedFacilityShiftQuery = Doctrine_Query::create()
            ->select('aFR.id')
            ->from('agScenarioFacilityResource aFR')
            ->innerJoin('aFR.agScenarioFacilityGroup aFG')
            ->leftJoin('aFR.agScenarioShift aSS')
            ->where('aSS.id is NULL')
            ->andWhere('aFG.scenario_id =?', $scenario_id); //returns the facility resources without shift
    $facilityShiftReturn = $undefinedFacilityShiftQuery->execute(array(), 'single_value_array');
    $undefinedFacilityShiftQuery->free();

    $undefinedStaffShiftQuery = Doctrine_Query::create()
            ->select('aSRT.id, aSSR.*, aSR.id')
            ->from('agScenarioStaffResource aSSR')
            ->innerJoin('aSSR.agStaffResource aSR')
            ->innerJoin('aSR.agStaffResourceType aSRT')
            ->leftJoin('aSRT.agScenarioShift aSS')
            ->where('aSSR.scenario_id =?', $scenario_id)
            ->andWhere('aSRT.id is NULL'); //returns the staff resource types without a shift
    $staffShiftReturn = $undefinedStaffShiftQuery->execute(array(), 'single_value_array');
    $undefinedStaffShiftQuery->free();

    return array($facilityShiftReturn, $staffShiftReturn);
  }

  public function staffPoolCheck($scenario_id)
  {
    $staffPoolQuery = Doctrine_Query::create()
            ->from('agScenarioStaffResource')
            ->where('scenario_id =?', $scenario_id);
    return $staffPoolQuery->count();
  }

  public function preMigrationCheck($scenario_id)
  {
    // 0. Pre check: check event status (only proceed if event status is pre-deploy), clean event related tables in pre-deploy state, empty facility group, undefined staff pool rules making sure pools not empty, undefined shifts for staff/facility resource.
    $facilityGroupCheck = $this->facilityGroupCheck($scenario_id);
    $staffPoolCheck = $this->staffPoolCheck($scenario_id);
    $undefinedShiftCheck = $this->undefinedShiftCheck($scenario_id);
    $undefinedFacilityShifts = $undefinedShiftCheck[0];
    $undefinedStaffShifts = $undefinedShiftCheck[1];

    return array('Empty facility groups' => $facilityGroupCheck, 'Staff pool count' => $staffPoolCheck, 'Undefined facility shifts' => $undefinedFacilityShifts, 'Undefined staff shifts' => $undefinedStaffShifts);
  }

  public function migrateFacilityGroups($scenario_id, $event_id)
  {
    $existingScenarioFacilityGroups = Doctrine_Core::getTable('agScenarioFacilityGroup')->findBy('scenario_id', $scenario_id);

    foreach ($existingScenarioFacilityGroups as $scenFacGrp) {
      $eventFacilityGroup = new agEventFacilityGroup();
      $eventFacilityGroup->set('event_id', $event_id)
          ->set('event_facility_group', $scenFacGrp->scenario_facility_group)
          ->set('facility_group_type_id', $scenFacGrp->facility_group_type_id)
          ->set('activation_sequence', $scenFacGrp->activation_sequence);
      $eventFacilityGroup->save();

      $eventFacilityGroupStatus = new agEventFacilityGroupStatus();
      $eventFacilityGroupStatus->set('event_facility_group_id', $eventFacilityGroup->id)
          ->set('time_stamp', new Doctrine_Expression('CURRENT_TIMESTAMP'))
          ->set('facility_group_allocation_status_id', $scenFacGrp->facility_group_allocation_status_id);
      $eventFacilityGroupStatus->save();

      $existingFacilityResources = $this->migrateFacilityResources($scenFacGrp, $eventFacilityGroup->id);

      $eventFacilityGroup->free(TRUE);
      $eventFacilityGroupStatus->free(TRUE);
    }
    $existingScenarioFacilityGroups->free(TRUE);
  }

  public function migrateFacilityResources($scenarioFacilityGroup, $event_facility_group_id)
  {
    $existingScenarioFacilityResources = $scenarioFacilityGroup->getAgScenarioFacilityResource();
    foreach ($existingScenarioFacilityResources as $scenFacRes) {
      $eventFacilityResource = new agEventFacilityResource();
      $eventFacilityResource->set('event_facility_group_id', $event_facility_group_id)
          ->set('facility_resource_id', $scenFacRes->facility_resource_id)
          ->set('activation_sequence', $scenFacRes->activation_sequence);
      $eventFacilityResource->save();

      $eventFacilityResourceStatus = new agEventFacilityResourceStatus();
      $eventFacilityResourceStatus->set('event_facility_resource_id', $eventFacilityResource->id)
          ->set('time_stamp', new Doctrine_Expression('CURRENT_TIMESTAMP'))
          ->set('facility_resource_allocation_status_id', $scenFacRes->facility_resource_allocation_status_id);
      $eventFacilityResourceStatus->save();

      // Should the shifts be process as the facility resources get processed?  Another solution is to create a temp table mapping the scenario to event faciltiy resources?
      $this->migrateShifts($scenFacRes->id, $eventFacilityResource->id);

      $eventFacilityResource->free(TRUE);
      $eventFacilityResourceStatus->free(TRUE);
    }

    $existingScenarioFacilityResources->free(TRUE);
    return 1;
  }

  public function migrateShifts($scenarioFacilityResourceId, $eventFacilityResourceId)
  {
    $scenarioShifts = Doctrine_Core::getTable('agScenarioShift')->findby('scenario_facility_resource_id', $scenarioFacilityResourceId);
    foreach ($scenarioShifts as $scenShift) {
      // At this point all fields in agEventShifts will be populated with agScenarioShifts.  Only
      // the real time fields in agEvnetShifts will not be populated.  It will be done so at a later
      // time when agEventFacilityActivationTime is populated.
      $eventShift = new agEventShift();
      $eventShift->set('event_facility_resource_id', $eventFacilityResourceId)
          ->set('staff_resource_type_id', $scenShift->staff_resource_type_id)
          ->set('minimum_staff', $scenShift->minimum_staff)
          ->set('maximum_staff', $scenShift->maximum_staff)
          ->set('minutes_start_to_facility_activation', $scenShift->minutes_start_to_facility_activation)
          ->set('task_length_minutes', $scenShift->task_length_minutes)
          ->set('break_length_minutes', $scenShift->break_length_minutes)
          ->set('task_id', $scenShift->task_id)
          ->set('shift_status_id', $scenShift->shift_status_id)
          ->set('staff_wave', $scenShift->staff_wave)
          ->set('deployment_algorithm_id', $scenShift->deployment_algorithm_id);
      $eventShift->save();
      $eventShift->free(TRUE);
    }
    $scenarioShifts->free(TRUE);
    return 1;
  }

  public function migrateStaffPool($scenario_id, $event_id)
  {
    $existingScenarioStaffPools = Doctrine_Query::create()
            ->from('agScenarioStaffResource ssr')
            ->where('scenario_id', $scenario_id)
            ->orderBy('deployment_weight')
            ->execute();
    foreach ($existingScenarioStaffPools AS $scenStfPool) {
      $eventStaff = new agEventStaff();
      $eventStaff->set('event_id', $event_id)
          ->set('staff_resource_id', $scenStfPool->staff_resource_id);
      $eventStaff->save();

      // @TODO Staff allocation status should be determine by the message responses.  Currently it is hard-coded to 1 as available.
      $unAvailableStaffStatus = Doctrine_Core::getTable('agStaffAllocationStatus')->findby('staff_allocation_status', 'unavailable');
      $eventStaffStatus = new agEventStaffStatus();
      $eventStaffStatus->set('event_staff-id', $eventStaff->id)
          ->set('time_stamp', new Doctrine_Expression('CURRENT_TIMESTAMP'))
          ->set('staff_allocation_status_id', $unAvailableStaffStatus);
      $eventStaffStatus->free(TRUE);

      $eventStaff->free(TRUE);
    }

    $existingScenarioStaffPools->free(TRUE);
    return 1;
  }

  public function migrateScenarioToEvent($scenario_id, $event_id)
  {

    $con = Doctrine_Manager::getInstance()->getConnectionForComponent('agEvent');

    try {
      $con->beginTransaction();

      /**
       * @todo
       * 0a. Check event status.  Event status must be 'pre-deploy' state.  DO NOT migrate scenario for any other event status.
       * 0b. Clean-out event related tables prior to migrating any scenario related tables.
       * 1a. Regenerate scenario shift
       */
      agScenarioGenerator::shiftGenerator();
      /**
       * @todo
       * 1b. Copy Faciltiy Group
       * 1c. Copy Facility Resource
       * 1d. Copy over scenario shift
       */
      $this->migrateFacilityGroups($scenario_id, $event_id);

      /**
       * @todo
       * 2. Populate facility start time, update event shift with real time, update facility resource/group status.
       * 3. Regenerate staff pool
       */
      Doctrine_query::create()->from('agScenarioStaffResource')->delete();
      Doctrine_query::create()->from('agEventStaff')->delete();
      /**
       * @todo Wrap in an event helper class.
       */
      $lucene_queries = Doctrine_Query::create()
              ->select('ssg.id, ssg.scenario_id, ls.query_condition, ls.id')
              ->from('agScenarioStaffGenerator ssg')
              ->innerJoin('ssg.agLuceneSearch ls')
              ->execute(array(), Doctrine_Core::HYDRATE_SCALAR);
      foreach ($lucene_queries as $lucene_query) {
        $staff_resource_ids = agScenarioGenerator::staffPoolGenerator($lucene_query['ls_query_condition'], $lucene_query['ssg_scenario_id']);
        agScenarioGenerator::saveStaffPool($staff_resource_ids);
      }

      // 4. Copy over staff pool
      $this->migrateStaffPool($scenario_id, $event_id);
      // 5. Populate agEventStaffShift (assigning event staffs to shifts).
      // 6. Update event status to deployed/active?

      $con->commit();
    } catch (Exception $e) {
      $con->rollBack();

      throw $e;
    }

    return $migrationResult;
  }

  public function executeList(sfWebRequest $request)
  {
    $this->ag_events = Doctrine_Core::getTable('agEvent')
            ->createQuery('a')
            ->select('a.*')
            ->from('agEvent a')
            ->execute();

    /**
     * @todo make query better above to reduce db calls
     */
  }

  public function executeReview(sfWebRequest $request)
  {

  }

  public function executeGis(sfWebRequest $request)
  {
    
  }

  public function executeDashboard(sfWebRequest $request)
  {
    
  }

  public function executeActive(sfWebRequest $request)
  {
    $this->event_id = $request->getParameter('id');
    $this->eventName = Doctrine::getTable('agEvent')
            ->findByDql('id = ?', $this->event_id)
            ->getFirst()->event_name;
  }

  public function executeStaff(sfWebRequest $request)
  {
    
  }

  public function executeStaffin(sfWebRequest $request)
  {

  }

  public function executeInconfirm(sfWebRequest $request)
  {

  }

  public function executeStaffout(sfWebRequest $request)
  {
    
  }

  public function executeOutconfirm(sfWebRequest $request)
  {

  }

  public function executeListgroups(sfWebRequest $request)
  {
    $query = Doctrine_Core::getTable('agEventFacilityGroup')
            ->createQuery('a')
            ->select('a.*, afr.*, afgt.*, fr.*')
            ->from('agEventFacilityGroup a, a.agEventFacilityResource afr, a.agFacilityGroupType afgt, a.agFacilityResource fr');

    // If the request has an event parameter, get only the agEventFacilityGroups for that event. Otherwise, all in the system will be returned.
    if ($request->hasParameter('event')) {
      $this->event = Doctrine_Query::create()
              ->select()
              ->from('agEvent')
              ->where('event_name = ?', urldecode($request->getParameter('event')))
              ->execute()->getFirst();
      $query->where('a.event_id = ?', $this->event->id);
    }
    $facilityGroupArray = array();
    $this->ag_event_facility_groups = $query->execute();
    foreach ($this->ag_event_facility_groups as $eventFacilityGroup) {
      $tempArray = $this->queryForTable($eventFacilityGroup->id);
      foreach ($tempArray as $ta) {
        array_push($facilityGroupArray, $ta);
      }
    }



    $this->facilityGroupArray = $facilityGroupArray;
    $this->pager = new agArrayPager(null, 10);

    if ($request->getParameter('sort') && $request->getParameter('order')) {
      $sortColumns = array('group' => 'efg_event_facility_group',
        'name' => 'f_facility_name',
        'code' => 'f_facility_code',
        'status' => 'ras_facility_resource_allocation_status',
        'time' => 'efrat_activation_time',
        'type' => 'fgt_facility_group_type',
        'event' => 'e_event_name');
      $sort = $sortColumns[$request->getParameter('sort')];
      agArraySort::$sort = $sort;
      usort($facilityGroupArray, array('agArraySort', 'arraySort'));
      if ($request->getParameter('order') == 'DESC') {
        $facilityGroupArray = array_reverse($facilityGroupArray);
      }
    }
    $this->pager->setResultArray($facilityGroupArray);
    $this->pager->setPage($this->getRequestParameter('page', 1));
    $this->pager->init();
  }

  public function executeGroupdetail(sfWebRequest $request)
  {
    $this->eventFacilityGroup = Doctrine_Query::create()
            ->select()
            ->from('agEventFacilityGroup')
            ->where('event_facility_group = ?', urldecode($request->getParameter('group')))
            ->fetchOne();
    $this->event = Doctrine_Query::create()
            ->select()
            ->from('agEvent')
            ->where('event_name = ?', urldecode($request->getParameter('event')))
            ->fetchOne();
    $this->results = $this->queryForTable($this->eventFacilityGroup->id);
    $statusQuery = agEventFacilityHelper::returnCurrentEventFacilityGroupStatus($this->event->id);
    $statusId = $statusQuery[$this->eventFacilityGroup->id];
//    $this->statusWidget = new sfWidgetFormDoctrineChoice(array('model' => 'agFacilityGroupAllocationStatus', 'add_empty' => false));
//    $r = $this->statusWidget->getOption('choices');
    $this->form = new sfForm();
    $this->form->setWidgets(array(
      'group_allocation_status' => new sfWidgetFormDoctrineChoice(array('model' => 'agFacilityGroupAllocationStatus', 'method' => 'getFacilityGroupAllocationStatus')),
      'resource_allocation_status' => new sfWidgetFormDoctrineChoice(array('model' => 'agFacilityResourceAllocationStatus', 'method' => 'getFacilityResourceAllocationStatus')),
     ));
    if ($request->isMethod(sfRequest::POST)) {

      if ($request->getParameter('resource_allocation_status')) {
        $resourceAllocation = new agEventFacilityResourceStatus();
        $resourceAllocation->event_facility_resource_id = $request->getParameter('event_facility_resource_id');
        $resourceAllocation->facility_resource_allocation_status_id = $request->getParameter('resource_allocation_status');
        $resourceAllocation->time_stamp = new Doctrine_Expression('CURRENT_TIMESTAMP');
        $resourceAllocation->save();
      }
    }

  }

  private function queryForTable($eventFacilityGroupId = null)
  {
    $query = Doctrine_Query::create()
            ->select('efr.id')
            ->addSelect('f.facility_name')
            ->addSelect('f.facility_code')
            ->addSelect('frt.facility_resource_type')
            ->addSelect('ras.facility_resource_allocation_status')
            ->addSelect('f.id')
            ->addSelect('fr.id')
            ->addSelect('frt.id')
            ->addSelect('ras.id')
            ->addSelect('ers.id')
            ->addSelect('es.id')
            ->addSelect('efg.event_facility_group')
            ->addSelect('fgt.facility_group_type')
            ->addSelect('e.event_name')
            ->addSelect('efrat.id')
            ->addSelect('efrat.activation_time')
            ->from('agEventFacilityResource efr')
            ->innerJoin('efr.agFacilityResource fr')
            ->innerJoin('fr.agFacilityResourceStatus frs')
            ->innerJoin('fr.agFacilityResourceType frt')
            ->innerJoin('fr.agFacility f')
            ->innerJoin('efr.agEventFacilityResourceStatus ers')
            ->innerJoin('ers.agFacilityResourceAllocationStatus ras')
            ->innerJoin('efr.agEventFacilityGroup efg')
            ->innerJoin('efg.agFacilityGroupType fgt')
            ->innerJoin('efg.agEvent e')
            ->leftJoin('efr.agEventFacilityResourceActivationTime efrat')
            ->where('EXISTS (
          SELECT efrs.id
            FROM agEventFacilityResourceStatus efrs
            WHERE efrs.event_facility_resource_id = ers.event_facility_resource_id
              AND efrs.time_stamp <= CURRENT_TIMESTAMP
            HAVING MAX(efrs.time_stamp) = ers.time_stamp)');
    if (isset($eventFacilityGroupId)) {
      $query->andWhere('efg.id = ?', $eventFacilityGroupId);
    }
    $results = $query->execute(array(), Doctrine_Core::HYDRATE_SCALAR);
    return $results;
  }

  public function executeFacility(sfWebRequest $request)
  {
    
  }

  public function executeReport(sfWebRequest $request)
  {

  }

  public function executeFacilityupdate(sfWebRequest $request)
  {

  }

  public function executeResolution(sfWebRequest $request)
  {
    
  }

  public function executePost(sfWebRequest $request)
  {
    
  }

  public function executeDelete(sfWebRequest $request)
  {
    $request->checkCSRFProtection();

    $this->forward404Unless($ag_event = Doctrine_Core::getTable('agEvent')->find(array($request->getParameter('id'))), sprintf('Object ag_event does not exist (%s).', $request->getParameter('id')));
    $ag_event->delete();

    $this->redirect('event/index');
  }

}
