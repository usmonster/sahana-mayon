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
 * @author Nils Stolpe, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
class eventActions extends agActions
{

  public static $event_id;
  public static $eventName;
  public static $event;
  protected $searchedModels = array('agEventStaff');

  /**
  * Displays the index page for the event module.
  *
  * Users will see a list of existing events and be given the option to create
  * a new event from a list of existing scenarios.
  *
  * @param sfWebRequest $request
  **/
  public function executeIndex(sfWebRequest $request)
  {
    $this->scenarioForm = new sfForm();
    $this->scenarioForm->setWidgets(array(
      'ag_scenario_list' => new sfWidgetFormDoctrineChoice(array('multiple' => false, 'model' => 'agScenario'))
    ));


    $this->scenarioForm->getWidgetSchema()->setLabel('ag_scenario_list', false);
    $this->ag_events = agDoctrineQuery::create()
            ->select('a.*')
            ->from('agEvent a')
            ->execute();
  }

  /**
   * event/fgroup provides the means to manage activation time for facility
   * resources that are in active groups which do not have activation times set 
   * @param sfWebRequest $request
   */

  public function executeFgroup(sfWebRequest $request)
  {
    $this->setEventBasics($request);

    $this->active_facility_groups = agEventFacilityHelper::returnEventFacilityGroups($this->event_id, TRUE);
    $this->facility_group = NULL;
    $facility_groups = array();
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
        $timeconverted = $timeconverter->convertDateArrayToUnix($fac_activation['facility_resource_activation']['activation_time']);


        foreach ($fac_activation['facility_resource_activation'] as $fac_activate) {
          if (is_array($fac_activate) && isset($fac_activate['operate_on'])) {
            $eFacResActivation = new agEventFacilityResourceActivationTime();

            $eFacResActivation->setActivationTime($timeconverted);
            $eFacResActivation->setEventFacilityResourceId($fac_activate['event_facility_resource_id']);
            $eFacResActivation->save();

            //agEventFacilityHelper::setFacilityActivationTime($eventFacilityResourceIds, $activationTime, $shiftChangeRestriction, $releaseStaff, $conn);
          }
        }
      }
    }

    $this->event_facility_resources = agEventFacilityHelper::returnFacilityResourceActivation($this->event_id, $this->facility_group);

    $this->fgroupForm = new agFacilityResourceAcvitationForm($this->event_facility_resources);
  }
/**
 * the event/deploy action provides a user with pre-deployment check information and the ability
 * to deploy an event if a scenario was given.
 * @param sfWebRequest $request
 */
  public function executeDeploy(sfWebRequest $request)
  {
    $this->setEventBasics($request);
    $this->scenario_id = agDoctrineQuery::create()
            ->select('scenario_id')
            ->from('agEventScenario')
            ->where('event_id = ?', $this->event_id)
            ->execute(array(), Doctrine_CORE::HYDRATE_SINGLE_SCALAR);
    if ($this->scenario_id) {
      $this->scenarioName = Doctrine::getTable('agScenario')
              ->findByDql('id = ?', $this->scenario_id)
              ->getFirst()->scenario;

      $this->checkResults = agEventMigrationHelper::preMigrationCheck($this->scenario_id);

      if ($request->isMethod(sfRequest::POST)) {
        agEventMigrationHelper::migrateScenarioToEvent($this->scenario_id, $this->event_id);
        $this->redirect('event/active?id=' . $this->event_id);
      }
    } else {
      $this->forward404('you cannot deploy an event without a scenario.');
    }
  }
/**
 * setEventBasics sets up basic information used across most event actions
 * @param sfWebRequest $request 
 */
  private function setEventBasics(sfWebRequest $request)
  {
    if ($request->getParameter('event')) {
      $this->event_id = $request->getParameter('id');
      if ($this->event_id != "") {
        $this->eventName = Doctrine_Core::getTable('agEvent')
                ->findByDql('id = ?', $this->event_id)
                ->getFirst()->getEventName();
      }
      //TODO step through to check and see if the second if is needed
    }
    if ($request->getParameter('event')) {
      $this->event = agDoctrineQuery::create()
              ->select()
              ->from('agEvent')
              ->where('event_name = ?', urldecode($request->getParameter('event')))
              ->execute()->getFirst();

      $this->event_id = $this->event->id;
      $this->eventName = $this->event->event_name;
      //TODO step through to check and see if the second if is needed
    }
  }

  /**
   * the meta action (event/meta) gives the user CRU functionality of event meta information
   * @param sfWebRequest $request
   */
  public function executeMeta(sfWebRequest $request)
  {
    $this->setEventBasics($request);
    if ($this->event_id != "") { //if someone is coming here from an edit context
      $eventMeta = Doctrine::getTable('agEvent')
              ->findByDql('id = ?', $this->event_id)
              ->getFirst();
    } else {
      $eventMeta = null;
    }

    if ($request->isMethod(sfRequest::POST) && !$request->getParameter('ag_scenario_list')) {
      //if someone has posted, but is not creating an event from a scenario.
      $this->metaForm = new PluginagEventDefForm($eventMeta);
      $this->metaForm->bind($request->getParameter($this->metaForm->getName()), $request->getFiles($this->metaForm->getName()));
      if ($this->metaForm->isValid()) {

        $ag_event = $this->metaForm->save();

        $eventStatusObject = agDoctrineQuery::create()
                ->from('agEventStatus a')
                ->where('a.id =?', $ag_event->getId())
                ->execute()->getFirst();

        $ag_event_status = isset($eventStatusObject) ? $eventStatusObject : new agEventStatus();

        $ag_event_status->setEventStatusTypeId(3);
        $ag_event_status->setEventId($ag_event->getId());
        $ag_event_status->time_stamp = new Doctrine_Expression('CURRENT_TIMESTAMP');
        $ag_event_status->save();

        //have to do this for delete also, i.e. delete the event_scenario object
        if ($request->getParameter('scenario_id') && $request->getParameter('scenario_id') != "") { 
          //the way this is constructed we will always have a scenario_id
          $ag_event_scenario = new agEventScenario();
          $ag_event_scenario->setScenarioId($request->getParameter('scenario_id'));
          $ag_event_scenario->setEventId($ag_event->getId());
          $ag_event_scenario->save();
          $this->redirect('event/deploy?id=' . $ag_event->getId());
        }
        $this->blackOutFacilities = agEventFacilityHelper::returnActivationBlacklistFacilities($ag_event->getId(), $ag_event->getZeroHour());
        $this->redirect('event/active?id=' . $ag_event->getId());
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
      $this->metaForm = new PluginagEventDefForm($eventMeta);
    }

    //as a rule of thumb, actions should post to themself and then redirect
  }

  /**
   * event/list shows a listing of events, this provides the list data to the listSuccess template
   * @param sfWebRequest $request
   */
  public function executeList(sfWebRequest $request)
  {
    $this->ag_events = agDoctrineQuery::create()
            ->select('a.*')
            ->from('agEvent a')
            ->execute();
  }

  /**
   * provides event staff pool management functions.
   * @param sfWebRequest $request request coming from web
   */
  public function executeStaffpool(sfWebRequest $request)
  {
    $this->setEventBasics($request);

    $this->saved_searches = $existing = Doctrine_Core::getTable('AgScenarioStaffGenerator')
            ->findAll();

    //get the possible filters from our request 
    // eg. &fr=1&type=generalist&org=volunteer
    $filters = array();
    foreach ($request->getParameterHolder() as $parameter => $filter) {
      if ($parameter == 'fr') {
        $filters['es.event_facility_resource_id'] = $filter;
      }
      if ($parameter == 'type') {
        $filters['sr.staff_resource_type_id'] = $filter;
      }
      if ($parameter == 'org') {
        $filters['sro.organization_id'] = $filter;
      }
    }
    //set up inputs for form
    $inputs = array(
      'staff_type' => new sfWidgetFormDoctrineChoice(array('model' => 'agStaffResourceType', 'label' => 'Staff Type')), // 'class' => 'filter')),
      'staff_org' => new sfWidgetFormDoctrineChoice(array('model' => 'agOrganization', 'method' => 'getOrganization', 'label' => 'Staff Organization')),//, 'class' => 'filter'))
      'facility_resource' => new sfWidgetFormDoctrineChoice(array('model' => 'agEventFacilityResource', 'label' => 'Facility Resource'))
    );

    $this->filterForm = new sfForm();
    foreach ($inputs as $key => $input) {
      $input->setAttribute('class', 'filter');
      $this->filterForm->setWidget($key, $input);
    }

    $query = agDoctrineQuery::create()
            ->select('es.id, sr.id, s.id, ess.*')
            ->from('agEventStaff es, es.agStaffResource sr, sr.agStaffResourceOrganization sro, sr.agStaff s, es.agEventStaffShift ess, ess.agEventShift esh')
            ->where('es.event_id = ?', $this->event_id);

    // If the request has a faciliy resource id
    if (sizeof($filters) > 0) {
      foreach ($filters as $field => $filter) {
        $query->andWhere($field . ' = ?', $filter);
      }
    }

    $eventStaff = array();
    $this->ag_event_staff = $query->execute(array(), Doctrine_Core::HYDRATE_SCALAR);
    //$thingy = $query->getSqlQuery();
//    foreach ($this->ag_event_staff as $eventFacilityGroup) {
//      $tempArray = $this->queryForTable($eventFacilityGroup->id);
//      foreach ($tempArray as $ta) {
//        array_push($eventStaff, $ta);
//      }
//    }
    //$this->facilityGroupArray = $facilityGroupArray;
    $this->pager = new agArrayPager(null, 10);


    $this->pager->setResultArray($this->ag_event_staff);
    $this->pager->setPage($this->getRequestParameter('page', 1));
    $this->pager->init();
    $this->widget = new sfForm();

    $this->widget->setWidget('add', new agWidgetFormSelectCheckbox(array('choices' => array(null)), array()));

    $this->widget->getWidgetSchema()->setLabel('add', false);
    $this->form_action = 'event/staffpool?id=' . $this->event_id;
  }
/**
 * provide event shift CRUD
 * @param sfWebRequest $request
 */
  public function executeShifts(sfWebRequest $request)
  {
    $this->setEventBasics($request);
//CREATE  / UPDATE
    if ($request->isMethod(sfRequest::POST)) {
      if ($request->getParameter('shiftid') && $request->getParameter('shiftid') == 'new') {
        $this->eventshiftform = new agEventShiftForm();
      } elseif ($request->getParameter('shiftid') && is_numeric($request->getParameter('shiftid'))) {
        $ag_event_shift = Doctrine_Core::getTable('agEventShift')
                ->findByDql('id = ?', $request->getParameter('shiftid'))
                ->getFirst();
        $this->eventshiftform = new agEventShiftForm($ag_event_shift);
      } elseif ($request->getParameter('delete')) {
//DELETE
      }
      $poo = $this->eventshiftform->getName(); //unset the staff list
      $this->eventshiftform->bind($request->getParameter($this->eventshiftform->getName()), $request->getFiles($this->eventshiftform->getName()));
      $formvalues = $request->getParameter($this->eventshiftform->getName());
      if ($this->eventshiftform->isValid()) { //form is not passing validation because the bind is failing?
        $ag_event_shift = $this->eventshiftform->save();
        $this->generateUrl('event_shifts', array('module' => 'event',
          'action' => 'shifts', 'id' => $this->event_id, 'shiftid' => $ag_event_shift->getId()));
        //       $this->redirect('event/shifts?id=' . $this->event_id . '&shiftid=' . $ag_event_shift->getId());
      }
      $this->redirect('event/shifts?id=' . $this->event_id); //need to pass in event id
    } else {
//READ
      if ($request->getParameter('shiftid') && $request->getParameter('shiftid') == 'new') {
        $this->eventshiftform = new agEventShiftForm();
        $this->setTemplate('editshift');
      } elseif ($request->getParameter('shiftid') && is_numeric($request->getParameter('shiftid'))) {

        $ag_event_shift = Doctrine_Core::getTable('agEventShift')
                ->findByDql('id = ?', $request->getParameter('shiftid'))
                ->getFirst();

        $this->eventshiftform = new agEventShiftForm($ag_event_shift);
        $this->setTemplate('editshift');
      } else {
//LIST
        $query = agDoctrineQuery::create()
                ->select('es.*, efr.*, efg.id, efg.event_facility_group, e.*, af.*, fr.*, frt.*, srt.*, ess.*, est.*')
                ->from('agEventShift as es')
                ->leftJoin('es.agEventStaffShift ess')
                ->leftJoin('ess.agEventStaff est')
                ->leftJoin('es.agStaffResourceType srt')
                ->leftJoin('es.agEventFacilityResource AS efr')
                ->leftJoin('efr.agEventFacilityGroup AS efg')
                ->leftJoin('efr.agFacilityResource fr, fr.agFacility af, fr.agFacilityResourceType frt')
                ->leftJoin('efg.agEvent AS e')
                ->where('e.id = ?', $this->event_id);

        /**
         * Create pager
         */
        $this->pager = new sfDoctrinePager('agEventShift', 20);

        /**
         * Set pager's query to our final query including sort
         * parameters
         */
        $this->pager->setQuery($query);

        /**
         * Set the pager's page number, defaulting to page 1
         */
        $this->pager->setPage($request->getParameter('page', 1));
        $this->pager->init();
      }
    }
  }
/**
 * provides the ability to add staff members into a shift
 * @param sfWebRequest $request
 */
  public function executeStaffshift(sfWebRequest $request)
  {
    $this->setEventBasics($request);
    if ($request->isXmlHttpRequest()) {
      $this->XmlHttpRequest = true;
    }
    $this->shift_id = $request->getParameter('shiftid');
    $inputs = array('staff_type' => new sfWidgetFormDoctrineChoice(array('model' => 'agStaffResourceType', 'label' => 'Staff Type')), // 'class' => 'filter')),
      'staff_org' => new sfWidgetFormDoctrineChoice(array('model' => 'agOrganization', 'method' => 'getOrganization', 'label' => 'Staff Organization')),
      'query_condition' => new sfWidgetFormInputHidden()
        ////, 'class' => 'filter'))
    ); //will have to set the class for the form elements elsewhere
    //set up inputs for form
    $filterForm = new sfForm();

    foreach ($inputs as $key => $input) {
      $input->setAttribute('class', 'filter');
      $filterForm->setWidget($key, $input);
    }
    $this->filterForm = $filterForm;

    if ($request->getParameter('Search')) {

      $this->staffSearchForm = new sfForm();
      $this->staffSearchForm->setWidget('add', new agWidgetFormSelectCheckbox(array('choices' => array(null)), array()));
      $this->staffSearchForm->getWidgetSchema()->setLabel('add', false);
      $lucene_query = $request->getParameter('query_condition');
      //$lucene_query = $filter_form['query_condition'];
      $incomingFields = $this->filterForm->getWidgetSchema()->getFields();

/**
 * @todo abstract the common operations here that are used in staff pool mangement to a helper class
 */
      $this->searchedModels = array('agEventStaff');  //we want the search model to be agEventStaff
      //note, this does not provide ability to add event
      parent::doSearch($lucene_query, FALSE, $this->staffSearchForm);
    } elseif ($request->getParameter('Add')) {
      $staffPotentials = $request->getPostParameter('resultform'); //('staff_list'); //ideally get only the widgets whose corresponding checkbox
      foreach ($staffPotentials as $key => $staffAdd) {
        //see if staff member exists in this shift already
        $existing = Doctrine::getTable('agEventStaffShift')
                ->findByDql('event_staff_id = ?', $this->shift_id)
                ->getFirst();
        if (!$existing) {
          $existing = new agEventStaffShift();
          $existing->setEventStaffId($key);
          $existing->setEventShiftId($this->shift_id);
        }
        $existing->save();
      }
    } elseif ($request->getParameter('Remove')) {
      //remove this staff member!
    }
  }
/**
 * @todo todo
 * @param sfWebRequest $request
 */
  public function executeDashboard(sfWebRequest $request)
  {
    
  }
/**
 * provides basic information about an active event, the template gives links to event management
 * @param sfWebRequest $request 
 */
  public function executeActive(sfWebRequest $request)
  {
    $this->setEventBasics($request);
  }
/**
 * provides basic staff management in an event
 * @param sfWebRequest $request
 */
  public function executeStaff(sfWebRequest $request)
  {
    $this->setEventBasics($request);
  }
/**
 * provides a list of facility groups in an event, or all events
 * also lists the facility resources in those facility groups and provides the ability to modify
 * each resource's status
 * @param sfWebRequest $request
 */
  public function executeListgroups(sfWebRequest $request)
  {
    $this->setEventBasics($request);
    $query = agDoctrineQuery::create()
            ->select('a.*, afr.*, afgt.*, fr.*')
            ->from('agEventFacilityGroup a, a.agEventFacilityResource afr, a.agFacilityGroupType afgt, a.agFacilityResource fr');

    // If the request has an event parameter, get only the agEventFacilityGroups for that event. Otherwise, all in the system will be returned.
    if ($this->event != "") {
      $query->where('a.event_id = ?', $this->event_id);
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
        'code' => 'fr_facility_resource_code',
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

  /**
  * Calls the groupdetailSuccess template.
  *
  * The template is used to display information about all event facilities in
  * an event facility group. The status of any of those facilities or of the group
  * can also be changed. This action will normally post to a modal dialog, but can
  * also post to the main browser window.
  *
  * @param sfWebRequest $request
  **/
  public function executeGroupdetail(sfWebRequest $request)
  {
    $this->event = agDoctrineQuery::create()
            ->select()
            ->from('agEvent')
            ->where('event_name = ?', urldecode($request->getParameter('event')))
            ->fetchOne();
    $this->eventFacilityGroup = agDoctrineQuery::create()
            ->select()
            ->from('agEventFacilityGroup')
            ->where('event_facility_group = ?', urldecode($request->getParameter('group')))
            ->andWhere('event_id = ?', $this->event->id)
            ->fetchOne();
    // Check for AJAX here. Set XmlHttpRequest to true so it can be used to determine functionality
    // in the templates and partials.
    if ($request->isXmlHttpRequest()) {
      $this->XmlHttpRequest = true;
    }
    if ($request->isMethod(sfRequest::POST)) {
      // Check which type of data is coming through. Are you changing resource status
      // or group status? Then build an object with the incoming values and some
      // $request parameters.
      if ($request->getParameter('resource_allocation_status')) {
        $resourceAllocation = new agEventFacilityResourceStatus();
        $resourceAllocation->event_facility_resource_id = $request->getParameter('event_facility_resource_id');
        $resourceAllocation->facility_resource_allocation_status_id = $request->getParameter('resource_allocation_status');
        $resourceAllocation->time_stamp = new Doctrine_Expression('CURRENT_TIMESTAMP');
        $resourceAllocation->save();
      } elseif ($request->getParameter('group_allocation_status')) {
        $groupAllocation = new agEventFacilityGroupStatus();
        $groupAllocation->event_facility_group_id = $this->eventFacilityGroup->id;
        $groupAllocation->facility_group_allocation_status_id = $request->getParameter('group_allocation_status');
        $groupAllocation->time_stamp = new Doctrine_Expression('CURRENT_TIMESTAMP');
        $groupAllocation->save();
      }
    }
    $this->results = $this->queryForTable($this->eventFacilityGroup->id);

    $statusIds = agEventFacilityHelper::returnCurrentEventFacilityGroupStatus($this->event->id);

    $query = agDoctrineQuery::create()
            ->select('s.event_facility_group_id')
            ->addSelect('s.facility_group_allocation_status_id')
            ->from('agEventFacilityGroupStatus s')
            ->whereIn('s.id', array_keys($statusIds));
    // Returns fgroup_id as key, status_id as val
    $statusQuery = $query->execute(array(), 'key_value_pair');

    $this->statusId = $statusQuery[$this->eventFacilityGroup->id];
    $this->form = new sfForm();
    $this->form->setWidgets(array(
      'group_allocation_status' => new sfWidgetFormDoctrineChoice(array('model' => 'agFacilityGroupAllocationStatus', 'method' => 'getFacilityGroupAllocationStatus')),
      'resource_allocation_status' => new sfWidgetFormDoctrineChoice(array('model' => 'agFacilityResourceAllocationStatus', 'method' => 'getFacilityResourceAllocationStatus')),
    ));
  }

  /**
  * Gets facility information to display in the groupdetailSuccess template.
  *
  * @param int      $eventFacilityGroupId     The id of an agEventFacilityGroup.
  *                                           Passed in from executeGroupDetail.
  *                                           If this isn't present, all facilities
  *                                           for an event will be displayed.
  * @return array() $results                  A multidimensional array of facility
  *                                           information. Each top level element
  *                                           corresponds to a returned facility.
  *
  **/
  private function queryForTable($eventFacilityGroupId = null)
  {
    $query = agDoctrineQuery::create()
            ->select('efr.id')
            ->addSelect('f.facility_name')
            ->addSelect('fr.facility_resource_code')
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

/**
 * provides the ability to give a description of an event, and close the event
 * @todo other resolution operations
 * @param sfWebRequest $request
 */
  public function executeResolution(sfWebRequest $request)
  {
    if ($request->isMethod(sfRequest::POST)) {
      //never going to be updating, will always be 'setting' the status, with the current timestamp
      $ag_event_status = new agEventStatus();

      $ag_event_status->setEventStatusTypeId($request->getParameter('event_status'));
      $ag_event_status->setEventId($this->event_id);
      $ag_event_status->time_stamp = new Doctrine_Expression('CURRENT_TIMESTAMP');
      $ag_event_status->save();
    }
    $this->setEventBasics($request);
    $this->eventName = urldecode($request->getParameter('event'));
    $this->event_id = Doctrine::getTable('agEvent')
            ->findByDql('event_name = ?', $this->eventName)
            ->getFirst()->id;
    $this->active_facility_groups = agEventFacilityHelper::returnEventFacilityGroups($this->event_id, TRUE);
    $this->resForm = new sfForm();
    $this->resForm->setWidgets(array(
      'event_status' => new sfWidgetFormDoctrineChoice(array('multiple' => false, 'model' => 'agEventStatusType', 'method' => 'getEventStatusType'))
    ));
    $currentStatus = agEventFacilityHelper::returnCurrentEventStatus($this->event_id);
    foreach ($currentStatus as $current_stat) {
      $current_status = $current_stat[0];
      //this works, but is awful.
    }

    $this->resForm->setDefault('event_status', $current_status);
  }
/**
 * @todo todo
 * @param sfWebRequest $request
 */
  public function executePost(sfWebRequest $request)
  {
    
  }
/**
 * deletes an event
 * @param sfWebRequest $request
 */
  public function executeDelete(sfWebRequest $request)
  {
    $request->checkCSRFProtection();

    $this->forward404Unless($ag_event = Doctrine_Core::getTable('agEvent')->find(array($request->getParameter('id'))), sprintf('Object ag_event does not exist (%s).', $request->getParameter('id')));
    $ag_event->delete();

    $this->redirect('event/index');
  }

}
