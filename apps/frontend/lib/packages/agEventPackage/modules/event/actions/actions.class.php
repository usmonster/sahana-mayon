<?php

/**
 * extends agActions for event
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
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
  public static $event_name;
  public static $event;
  protected $searchedModels = array('agEventStaff');

  /**
   * Displays the index page for the event module.
   *
   * Users will see a list of existing events and be given the option to create
   * a new event from a list of existing scenarios.
   *
   * @param sfWebRequest $request
   * */
  public function executeIndex(sfWebRequest $request)
  {
    $this->scenarioForm = new sfForm();
    $this->scenarioForm->setWidgets(
        array(
          'ag_scenario_list' => new sfWidgetFormDoctrineChoice(
              array('multiple' => false, 'model' => 'agScenario')
          )
        )
    );


    $this->scenarioForm->getWidgetSchema()->setLabel('ag_scenario_list', false);
    $this->ag_events = agDoctrineQuery::create()
            ->select('a.*')
            ->from('agEvent a')
            ->execute();
  }

  public function executeFacilityresource(sfWebRequest $request)
  {
    $this->setEventBasics($request);
    $this->xmlHttpRequest = $request->isXmlHttpRequest();
    // This is the event_facility_resource.
    $this->event_facility_resource = agDoctrineQuery::create()
            ->select()
            ->from('agEventFacilityResource')
            ->where('id = ?', $request->getParameter('eventFacilityResourceId'))
            ->execute()->getFirst();
    $groupIds = agDoctrineQuery::create()
            ->select('id')
            ->from('agEventFacilityGroup')
            ->where('event_id = ?', $this->event_id)
            ->execute(array(), 'single_value_array');
    // This is the actual facility resource that will have access to names and other information.
    $this->facility_resource = agDoctrineQuery::create()
            ->select('')
            ->from('agFacilityResource')
            ->where('id = ?', $this->event_facility_resource['facility_resource_id'])
            ->execute()->getFirst();
    $this->facilityResourceActivationTimeForm = new agSingleEventFacilityResourceActivationTimeForm(); //new agFacilityResourceAcvitationForm($this->event_facility_resource);
    $this->facilityResourceActivationTimeForm->setDefault(
        'event_facility_resource_id',
        $this->event_facility_resource['id']
    );
  }

  /**
   * event/facilitygroups provides the means to manage activation time for facility
   * resources that are in active groups which do not have activation times set 
   * @param sfWebRequest $request
   */
  public function executeFacilitygroups(sfWebRequest $request)
  {
    $this->setEventBasics($request);

    $this->active_facility_groups = agEventFacilityHelper::returnEventFacilityGroups($this->event_id, TRUE);
    $this->facility_group = NULL;
    $facility_groups = array(' ' => ' ');
    foreach ($this->active_facility_groups as $event_fgroup) {
      $facility_groups[$event_fgroup['efg_id']] = $event_fgroup['efg_event_facility_group'];
    }

    $this->facilitygroupsForm = new sfForm();
    $this->facilitygroupsForm->setWidgets(
        array(
          'facility_group_list' => new sfWidgetFormChoice(
              array('multiple' => false,
                'choices' => $facility_groups)
          ),
        // 'add_empty' => true))// ,'onClick' => 'submit()'))
        )
    );

    $this->xmlHttpRequest = $request->isXmlHttpRequest();
    //the facility group choices above (if selected) will pare down
    //the returned facility resources below FOR a facility group
    if ($request->isMethod(sfRequest::POST)) {
      if ($request->getParameter('facility_group_filter')) {
        $this->facility_group = $request->getParameter('facility_group_list');
        $b = $this->facility_group;
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

            //agEventFacilityHelper::setFacilityActivationTime(
            //  $eventFacilityResourceIds,
            //  $activationTime,
            //  $shiftChangeRestriction,
            //  $releaseStaff,
            //  $conn
            //  );
          }
        }
      }
    }
    //$this->event_facility_resources = null;
    if (count($facility_groups) > 1) {
      //return only facility resources with no activation times for active facility groups
      $this->event_facility_resources =
          agEventFacilityHelper::returnFacilityResourceActivation(
              $this->event_id,
              $this->facility_group,
              null,
              1
      );
      $this->fgroupForm = new agFacilityResourceAcvitationForm($this->event_facility_resources);
    } else {
      $this->fgroupForm = null;
    }
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
      //p-code
      $this->getResponse()->setTitle('Sahana Agasti ' . $this->event['event_name'] . ' Deploy');
      //end p-code
      if ($request->isMethod(sfRequest::POST)) {
        agEventMigrationHelper::migrateScenarioToEvent($this->scenario_id, $this->event_id);
        $this->redirect('event/active?event=' . urlencode($this->event_name));
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
//    if ($request->getParameter('id')) {
//      $this->event_id = $request->getParameter('id');
//      if ($this->event_id != "") {
//        $this->event_name = Doctrine_Core::getTable('agEvent')
//                ->findByDql('id = ?', $this->event_id)
//                ->getFirst()->getEventName();
//      }
//      //TODO step through to check and see if the second if is needed
//    }
    if ($request->getParameter('event')) {
      $this->event = agDoctrineQuery::create()
              ->select()
              ->from('agEvent')
              ->where('event_name = ?', urldecode($request->getParameter('event')))
              ->execute()->getFirst();

      $this->event_id = $this->event->id;
      $this->event_name = $this->event->event_name;
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
      $this->metaForm->bind(
          $request->getParameter($this->metaForm->getName()),
          $request->getFiles($this->metaForm->getName())
      );
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
          $this->redirect('event/deploy?event=' . urlencode($ag_event->getEventName()));
        }
        $this->blackOutFacilities = agEventFacilityHelper::returnActivationBlacklistFacilities($ag_event->getId(), $ag_event->getZeroHour());
        $this->redirect('event/active?event=' . urlencode($ag_event->getEventName()));
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
    //p-code
    if (isset($eventMeta->event_name)) {
      $this->getResponse()->setTitle('Sahana Agasti ' . $this->event_name . ' Event Management');
    } else {
      $this->getResponse()->setTitle('Sahana Agasti New Event Management');
    }
    //end p-code
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

    //get the possible filters from our request eg. &fr=1&type=generalist&org=volunteer
    $filters = array();
    foreach ($request->getParameterHolder() as $parameter => $filter) { //('filter')
      if ($parameter == 'fr') {
        $filters['essh.event_facility_resource_id'] = $filter;
      }
      if ($parameter == 'st') {
        $filters['sr.staff_resource_type_id'] = $filter;
      }
      if ($parameter == 'so') {
        $filters['sro.organization_id'] = $filter;
      }
    }
    //set up inputs for filter form
    $inputs = array(
      'st' => new sfWidgetFormDoctrineChoice(
          array(
            'model' => 'agStaffResourceType',
            'label' => 'Staff Type',
            'add_empty' => true
          )
      ),
      // 'class' => 'filter')),
      'so' => new sfWidgetFormDoctrineChoice(
          array(
            'model' => 'agOrganization',
            'method' => 'getOrganization',
            'label' => 'Staff Organization',
            'add_empty' => true
          )
      ),
      //, 'class' => 'filter'))
      'fr' => new sfWidgetFormDoctrineChoice(
          array(
            'model' => 'agEventFacilityResource',
            'label' => 'Facility Resource',
            'add_empty' => true
          )
      )
    );
    /** @todo set defaults from the request */
    $this->filterForm = new sfForm(null, array(), false);
    //$this->filterForm->getWidgetSchema()->setNameFormat('filter[%s]');
    foreach ($inputs as $key => $input) {
      $input->setAttribute('class', 'filter');
      $this->filterForm->setWidget($key, $input);
    }

    //begin construction of query used for listing
    $query = agDoctrineQuery::create()
            ->select(
                'es.id,
                  essh.id,
                  esh.event_facility_resource_id,
                  efr.facility_resource_id,
                  fr.facility_id,
                  f.facility_name,
                  sr.id,
                  srt.staff_resource_type,
                  sro.id, o.organization,
                  s.id, s.staff_status_id,
                  ss.staff_status,
                  p.id,
                  ess.staff_allocation_status_id'
            )//, sas.staff_allocation_status')
            //maybe we should only get the id since it's needed for dropdown
            ->from(
                'agEventStaff es,
              es.agEventStaffShift essh,
              essh.agEventShift esh,
              esh.agEventFacilityResource efr,
              efr.agFacilityResource fr,
              fr.agFacility f,
              es.agStaffResource sr,
              sr.agStaffResourceType srt,
              sr.agStaffResourceOrganization sro,
              sro.agOrganization o,
              sr.agStaff s,
              s.agStaffStatus ss,
              s.agPerson p,
              es.agEventStaffStatus ess'
            )
            //ess.agStaffAllocationStatus sas')
            ->where('es.event_id = ?', $this->event_id);

    if (sizeof($filters) > 0) {
      foreach ($filters as $field => $filter) {
        $query->andWhere($field . ' = ?', $filter);
      }
    }

    if ($request->isMethod(sfRequest::POST)) {
      if ($request->getParameter('event_status')) {
        foreach ($request->getParameter('event_status') as $event_status) {
          //this is inefficient here as we are executing the same query in a loop to get associated objects
//check to see if this staff member already has a status set.
          $eventStaffStatusObject = agDoctrineQuery::create()
                  ->from('agEventStaffStatus a')
                  ->where('a.event_staff_id =?', $event_status['event_staff_id'])
                  ->fetchOne();
//NEW
          if (!$eventStaffStatusObject) {
            $eventStaffStatusObject = new agEventStaffStatus();
            $eventStaffStatusObject->time_stamp = date('Y-m-d H:i:s', time());
            $eventStaffStatusObject->event_staff_id = $event_status['event_staff_id'];
            $eventStaffStatusObject->staff_allocation_status_id = $event_status['status'];
            $eventStaffStatusObject->save();
          } else {
//UPDATE  ONLY IF staff_allocation_status has changed
            //technically this should always be an update, by the time a staff member is in an event
            if ($eventStaffStatusObject->staff_allocation_status_id != $event_status['status']) {
              $eventStaffStatusObject->time_stamp = date('Y-m-d H:i:s', time());
              //$eventStaffStatusObject->event_staff_id = $event_status['event_staff_id'];
              $eventStaffStatusObject->staff_allocation_status_id = $event_status['status'];
              $eventStaffStatusObject->save();
            }
          }
          //we should throw a check here to see if the most recent status is the same as incoming
        }
      }
    }
    $eventStaff = array();
    $ag_event_staff = $query->execute(array(), Doctrine_Core::HYDRATE_SCALAR);
    foreach ($ag_event_staff as $key => $value) {
      $person_array[] = $value['p_id'];
      //$remapped_array[$ag_event_staff['es_id']] = $
    }
    $names = new agPersonNameHelper($person_array); //we need to get persons from the event staff ids that are returned here
    $person_names = $names->getPrimaryNameByType();

    //$names->
    //this is the desired format of the return array:
    $this->widget = new sfForm();
    $this->widget->getWidgetSchema()->setNameFormat('event_status[][%s]');
    $this->widget->setWidget('status', new sfWidgetFormDoctrineChoice(array('model' => 'agStaffAllocationStatus', 'method' => 'getStaffAllocationStatus')));

    //the agStaffAllocationStatus ID coming from each of the selections will be saved to ag_Event_staff_status.
    $this->widget->getWidgetSchema()->setLabel('status', false);
    /** @todo set defaults for each status drop down from the web request */
    $this->form_action = 'event/staffpool?event=' . $this->event_name;
    foreach ($ag_event_staff as $staff => $value) {
      $result_array[] = array(
        'fn' => $person_names[$value['p_id']]['given'],
        'ln' => $person_names[$value['p_id']]['family'],
        'organization_name' => $value['o_organization'],
        'status' => $value['ss_staff_status'],
        'type' => $value['srt_staff_resource_type'],
        'facility' => $value['f_facility_name'],
        'es_id' => $value['es_id'],
        'ess_staff_allocation_status_id' => $value['ess_staff_allocation_status_id']
      );
    }

    $this->ag_event_staff = $result_array;
//    foreach ($this->ag_event_staff as $eventFacilityGroup) {
//      $tempArray = $this->groupResourceQuery($eventFacilityGroup->id);
//      foreach ($tempArray as $ta) {
//        array_push($eventStaff, $ta);
//      }
//    }
    //$this->facilityGroupArray = $facilityResourceArray;
    $this->pager = new agArrayPager(null, 10);


    $this->pager->setResultArray($this->ag_event_staff);
    $this->pager->setPage($this->getRequestParameter('page', 1));
    $this->pager->init();
    //set up the widget for use in the ' list form '
    //p-code
    $this->getResponse()->setTitle('Sahana Agasti ' . $this->event_name . ' Staff');
    //end p-code
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
      $this->eventshiftform->bind($request->getParameter($this->eventshiftform->getName()), $request->getFiles($this->eventshiftform->getName()));
      $formvalues = $request->getParameter($this->eventshiftform->getName());
      if ($this->eventshiftform->isValid()) { //form is not passing validation because the bind is failing?
        $ag_event_shift = $this->eventshiftform->save();
        $this->generateUrl('event_shifts', array('module' => 'event',
          'action' => 'shifts', 'event' => $this->event_name, 'shiftid' => $ag_event_shift->getId()));
      }
      $this->redirect('event/shifts?event=' . urlencode($this->event_name)); //need to pass in event id
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

    //p-code
    $this->getResponse()->setTitle('Sahana Agasti ' . $this->event_name . ' Shifts');
    //end p-code
  }

  /**
   * provides the ability to add staff members into a shift
   * @param sfWebRequest $request
   */
  public function executeStaffshift(sfWebRequest $request)
  {
    $this->setEventBasics($request);
    $this->xmlHttpRequest = $request->isXmlHttpRequest();
    $this->shift_id = $request->getParameter('shiftid');

    $inputs = array('staff_type' => new sfWidgetFormDoctrineChoice(array('model' => 'agStaffResourceType', 'label' => 'Staff Type', 'add_empty' => TRUE)), // 'class' => 'filter')),
      'staff_org' => new sfWidgetFormDoctrineChoice(array('model' => 'agOrganization', 'method' => 'getOrganization', 'label' => 'Staff Organization', 'add_empty' => TRUE)),
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

    //p-code
    $this->getResponse()->setTitle('Sahana Agasti ' . $this->event_name . ' Staff Shift');
    //end p-code
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

    //p-code
    $this->getResponse()->setTitle('Sahana Agasti ' . $this->event_name . ' Management');
    //end p-code
  }

  /**
   * provides basic staff management in an event
   * @param sfWebRequest $request
   */
  public function executeStaff(sfWebRequest $request)
  {
    $this->setEventBasics($request);

    //p-code
    $this->getResponse()->setTitle('Sahana Agasti ' . $this->event_name . ' Staff');
    //end p-code
  }

  /**
   * provides a list of facility groups in an event, or all events
   * also lists the facility resources in those facility groups and provides the ability to modify
   * each resource's status
   * @param sfWebRequest $request
   */
  public function executeListgroups(sfWebRequest $request)
  {
    // Check to see if there is an event parameter and, if so, if the parameter is a valid event
    // name. If it exists but is invalid, redirect to the eventless listgroups.
    // Commented out for now, as groupDetail won't work right now w/o an event in the URL.
//    if($request->getParameter('event') != null && Doctrine::getTable('agEvent')->findByDql('where event_name = ?', $request->getParameter('event'))->getFirst() == false) {
//      $this->redirect('event/listgroups');
//    }
    if ($request->getParameter('event') == null) {
      $this->missingEvent = true;
    }
    $this->setEventBasics($request);

    $query = agDoctrineQuery::create()
            ->select('efg.id')
            ->addSelect('efg.event_facility_group')
            ->addSelect('fgt.facility_group_type')
            ->addSelect('fgas.id')
            ->addSelect('fgas.facility_group_allocation_status')
            ->addSelect('ev.event_name')
            ->addSelect('count(efr.event_facility_group_id)')
            ->from('agEventFacilityGroup efg')
            ->innerJoin('efg.agEventFacilityGroupStatus efgs')
            ->innerJoin('efg.agFacilityGroupType fgt')
            ->innerJoin('efgs.agFacilityGroupAllocationStatus fgas')
            ->innerJoin('efg.agEvent ev')
            ->innerJoin('efg.agEventFacilityResource efr')
            ->where('EXISTS (
              SELECT s.id
                FROM agEventFacilityGroupStatus s
                WHERE s.event_facility_group_id = efgs.event_facility_group_id
                  AND s.time_stamp <= CURRENT_TIMESTAMP
                HAVING MAX(s.time_stamp) = efgs.time_stamp)')
            ->groupBy('efg.event_facility_group');
    // If the request has an event parameter, get only the agEventFacilityGroups for that event. Otherwise, all in the system will be returned.
    if ($this->event != "") {
      $query->andWhere('efg.event_id = ?', $this->event_id);
    }

    $facilityResourceArray = array();
    $this->facilityGroupArray = $query->execute(array(), Doctrine_Core::HYDRATE_SCALAR);

    foreach ($this->facilityGroupArray as $eventFacilityGroup) {
      $facilityResourceArray[$eventFacilityGroup['efg_id']] = $this->groupResourceQuery($eventFacilityGroup['efg_id']);
    }
    $this->facilityResourceArray = $facilityResourceArray;
    $this->pager = new agArrayPager(null, 10);

    if ($request->getParameter('sort') && $request->getParameter('order')) {
      $sortColumns = array(
        'group' => 'efg_event_facility_group',
        'type' => 'fgt_facility_group_type',
        'status' => 'fgas_facility_group_allocation_status',
        'count' => 'efr_count',
        'event' => 'ev_event_name');
      $sort = $sortColumns[$request->getParameter('sort')];
      agArraySort::$sort = $sort;
      usort($this->facilityGroupArray, array('agArraySort', 'arraySort'));
      if ($request->getParameter('order') == 'DESC') {
        $this->facilityGroupArray = array_reverse($this->facilityGroupArray);
      }
    }
    $this->pager->setResultArray($this->facilityGroupArray);
    $this->pager->setPage($this->getRequestParameter('page', 1));
    $this->pager->init();

    //p-code
    $this->getResponse()->setTitle('Sahana Agasti ' . $this->event_name . ' Facility Groups');
    //end p-code
  }

  public function executeEventfacilityresource(sfWebRequest $request)
  {
    $this->facilityResourceArray = $this->groupResourceQuery($request->getParameter('eventFacResId'));
    return $this->renderPartial('eventFacResTable', array('facilityResourceArray' => $this->facilityResourceArray));
  }

  public function executeEventfacilitygroup(sfWebRequest $request)
  {
    // Get the incoming params.
    $params = $request->getPostParameters();

    if(array_key_exists('groupStatus', $params) && array_key_exists('groupId', $params)) {
      // Build an agEventFacilityGroupStatus object from incoming params, then stick it in a form.
      $groupAllocationStatus = new agEventFacilityGroupStatus();
      $groupAllocationStatus->event_facility_group_id = ltrim($params['groupId'], 'group_id_');
      $groupAllocationStatus->facility_group_allocation_status_id = 
          agDoctrineQuery::create()
            ->select('id')
            ->from('agFacilityGroupAllocationStatus')
            ->where('facility_group_allocation_status = ?', $params['groupStatus'])
            ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
      $groupAllocationStatus->time_stamp = date('Y-m-d H:i:s', time());
      $groupAllocationStatusForm = new agTinyEventFacilityGroupStatusForm($groupAllocationStatus);

      return $this->renderText($groupAllocationStatusForm->__toString());
    }
    $a = 6;
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
   * */
  public function executeGroupdetail(sfWebRequest $request)
  {
// General
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
    $this->xmlHttpRequest = $request->isXmlHttpRequest();
// Create
    if ($request->isMethod(sfRequest::POST)) {
      // Check which type of data is coming through. Are you changing resource status
      // or group status? Then build an object with the incoming values and some
      // $request parameters.
      if ($request->getParameter('resource_allocation_status')) {
        // Find the most recent activation status for the facility resource.
        // $staffed and $unstaffed will also be set for use in the if below.
        $activationStatus = agEventFacilityHelper::getCurrentEventFacilityResourceStatus($this->event['id'], array($request->getParameter('event_facility_resource_id')));
        $staffed = agEventFacilityHelper::getFacilityResourceAllocationStatus('staffed', 1);
        $unstaffed = agEventFacilityHelper::getFacilityResourceAllocationStatus('staffed', 0);

        $resourceAllocation = new agEventFacilityResourceStatus();
        $resourceAllocation->event_facility_resource_id = $request->getParameter('event_facility_resource_id');
        $resourceAllocation->facility_resource_allocation_status_id = $request->getParameter('resource_allocation_status');
        $resourceAllocation->time_stamp = date('Y-m-d H:i:s', time());
        if (in_array($activationStatus[$request->getParameter('event_facility_resource_id')], $unstaffed)) {
          $resourceAllocation->save();
          return $this->renderText('facilityresource/' . $request->getParameter('event_facility_resource_id'));
        }
        $resourceAllocation->save();
      } elseif ($request->getParameter('group_allocation_status')) {
        $activationStatus = agEventFacilityHelper::getCurrentEventFacilityGroupStatus($this->event['id'], array($request->getParameter('event_facility_group_id')));
        $active = agEventFacilityHelper::getFacilityGroupOrResourceAllocationStatus('group', 'active', 1);
        $inactive = agEventFacilityHelper::getFacilityGroupOrResourceAllocationStatus('group', 'active', 0);
        $groupAllocation = new agEventFacilityGroupStatus();
        $groupAllocation->event_facility_group_id = $this->eventFacilityGroup->id;
        $groupAllocation->facility_group_allocation_status_id = $request->getParameter('group_allocation_status');
        $groupAllocation->time_stamp = date('Y-m-d H:i:s', time());
        if (in_array($activationStatus[$request->getParameter('event_facility_group_id')], $inactive)) {
          $groupAllocation->save();
          return $this->renderText('facilitygroups');
        }
        $groupAllocation->save();
      }
    }
    $this->results = $this->groupResourceQuery($this->eventFacilityGroup->id);

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
   * Gets facility resource information for facility resources within the facility group with the id
   * passed to the function.
   *
   * @param int      $eventFacilityGroupId     The id of an agEventFacilityGroup.
   *                                           Passed in from executeGroupDetail.
   *                                           If this isn't present, all facilities
   *                                           for an event will be displayed.
   * @return array() $results                  A multidimensional array of facility
   *                                           information. Each top level element
   *                                           corresponds to a returned facility.
   *
   * */
  private function groupResourceQuery($eventFacilityGroupId = null)
  {
    $query = agDoctrineQuery::create()
            ->select('efr.id')
            ->addSelect('f.facility_name')
            ->addSelect('f.facility_code')
            ->addSelect('frt.facility_resource_type')
            ->addSelect('frt.facility_resource_type_abbr')
            ->addSelect('ras.facility_resource_allocation_status')
            ->addSelect('f.id')
            ->addSelect('fr.id')
            ->addSelect('frt.id')
            ->addSelect('ras.id')
            ->addSelect('ers.id')
            ->addSelect('es.id')
            ->addSelect('efg.event_facility_group')
            ->addSelect('efg.id')
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
    $this->setEventBasics($request);
    if ($request->isMethod(sfRequest::POST)) {
      //never going to be updating, will always be 'setting' the status, with the current timestamp
      $ag_event_status = new agEventStatus();

      $ag_event_status->setEventStatusTypeId($request->getParameter('event_status'));
      $ag_event_status->setEventId($this->event_id);
      $ag_event_status->time_stamp = new Doctrine_Expression('CURRENT_TIMESTAMP');
      $ag_event_status->save();
    }
    $this->active_facility_groups = agEventFacilityHelper::returnEventFacilityGroups($this->event_id, TRUE);
    $this->resForm = new sfForm();
    $this->resForm->setWidgets(array(
      'event_status' => new sfWidgetFormDoctrineChoice(array('multiple' => false, 'model' => 'agEventStatusType', 'method' => 'getEventStatusType')),
//      'event_id'     => new sfWidgetFormInputHidden()
    ));
    $currentStatus = agEventFacilityHelper::returnCurrentEventStatus($this->event_id);
    $this->resForm->setDefault('event_status', $currentStatus);
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
