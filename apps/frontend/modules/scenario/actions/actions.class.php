<?php

/**
 * extends sfActions for scenario
 *
 * PHP Version 5
 *
 * LICENSE: This source file is subject to LGPLv3.0 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 *
 * @author Full Name, Organization
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
class scenarioActions extends sfActions
{

  /**
   *
   * @param sfWebRequest $request
   * generates a list of scenarios that are passed to the view
   */
  public function executeList(sfWebRequest $request)
  {
    $this->ag_scenarios = Doctrine_Core::getTable('agScenario')
            ->createQuery('a')
            ->select('a.*, b.*')
            ->from('agScenario a, a.agScenarioFacilityGroup b')
            ->execute();
  }

  /**
   *
   * @param sfWebRequest $request
   * generates a list of facility groups that are passed to the view
   */
  public function executeListgroup(sfWebRequest $request)
  {
    $this->ag_scenario_facility_groups = Doctrine_Core::getTable('agScenarioFacilityGroup')
            ->createQuery('a')
            ->select('a.*, afr.*, afgt.*, afgas.*, fr.*')
            ->from('agScenarioFacilityGroup a, a.agScenarioFacilityResource afr, a.agFacilityGroupType afgt, a.agFacilityGroupAllocationStatus afgas, a.agFacilityResource fr')
            ->execute();
  }

  /**
   * @method executeListshifttemplate()
   * List of scenario with defined shift templates and the total count of shift templates.
   * @param sfWebRequest $requst
   *
   */
  public function executeListshifttemplate(sfWebRequest $request)
  {
    $query = Doctrine_Core::getTable('agShiftTemplate')
               ->createQuery('st')
               ->select('st.scenario_id, s.scenario, count(*) AS count')
               ->innerJoin('st.agScenario AS s')
               ->groupBy('st.scenario_id, s.scenario');


     /**
     * Create pager
     */
    $this->pager = new sfDoctrinePager('agShiftTemplate', 10);

    /**
     * Check if the client wants the results sorted, and set pager
     * query attributes accordingly
     */
    $this->sortColumn = $request->getParameter('sort') ? $request->getParameter('sort') : 'scenario';
    $this->sortOrder = $request->getParameter('order') ? $request->getParameter('order') : 'ASC';

    if ($request->getParameter('sort')) {
      $query = $query->orderBy($request->getParameter('sort', 'scenario') . ' ' . $request->getParameter('order', 'ASC'));
    }

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

  /**
   *
   * @param sfWebRequest $request
   * generates and passes a new scenario form to the view
   */
  public function executeStaffresources(sfWebRequest $request)
  {
    $this->forward404Unless(
        $this->ag_scenario_facility_group = Doctrine_Core::getTable('agScenarioFacilityGroup')->find(array($request->getParameter('id'))), sprintf('This Facility Group does not exist.', $request->getParameter('id')));

    $this->ag_staff_resources = Doctrine_Core::getTable('agScenarioFacilityResource')
            ->createQuery('agSFR')
            ->select('agSFR.*')
            ->from('agScenarioFacilityResource agSFR')
            ->where('scenario_facility_group_id = ?', $request->getParameter('id'))
            ->execute();
    $this->staffresourceform = new agStaffResourceRequirementForm();
  }




  /**
   * @todo what's this do?
   * @param sfWebRequest $request
   */
  public function executeIndex(sfWebRequest $request)
  {
    //here we can use $request to better form the index page for scenario
  }

  /**
   *
   * @param sfWebRequest $request
   * generates and passes a new scenario form to the view
   */
  public function executeGrouptype(sfWebRequest $request)
  {
    $this->ag_facility_group_types = Doctrine_Core::getTable('agFacilityGroupType')
            ->createQuery('a')
            ->execute();
    $this->grouptypeform = new agFacilityGroupTypeForm();
  }

  /**
   * @todo what's this do?
   * @param sfWebRequest $request
   */
  public function executeNew(sfWebRequest $request)
  {
    $this->form = new agScenarioForm();
  }

  /**
   *
   * @param sfWebRequest $request
   * generates and passes a new facility group form to the view
   */
  public function executeNewgroup(sfWebRequest $request)
  {
    if($this->getUser()->getAttribute('scenario_id')){
      $this->groupform = new agScenarioFacilityGroupForm();
      //$this->getUser()->getAttribute('scenario_id')
      $this->groupform->setDefault('scenario_id', $this->getUser()->getAttribute('scenario_id'));
      // Hide the scenario field if this group is being created through scenario workflow.
      $this->groupform->setWidget('scenario_id', new sfWidgetFormInputHidden());
    } else {
      $this->groupform = new agScenarioFacilityGroupForm();
    }
    $this->ag_allocated_facility_resources = '';
    $this->ag_facility_resources =  Doctrine_Query::create()
      ->select('a.facility_id, af.*, afrt.*')
      ->from('agFacilityResource a, a.agFacility af, a.agFacilityResourceType afrt')
      ->execute();
  }

  /**
   * @method executeEditshifttemplate()
   * Generates a new shit template form
   * @param sfWebRequest $request
   */
  public function executeEditshifttemplate(sfWebRequest $request)
  {
    $this->scenario_id = $request->getParameter('scenId');
    $this->scenario_name = Doctrine_Core::getTable('agScenario')->find($this->scenario_id)->getScenario();
    $this->shifttemplateform = new agShiftGeneratorForm(array(), array('scenario_id' => $this->scenario_id));
  }

  /**
   * @method executeNewshifttemplate()
   * Generates a new shit template form
   * @param sfWebRequest $request
   */
  public function executeNewshifttemplate(sfWebRequest $request)
  {
    $this->scenario_id = $request->getParameter('scenId');
    $this->scenario_name = Doctrine_Core::getTable('agScenario')->find($this->scenario_id)->getScenario();
    $this->shifttemplateform = new agShiftGeneratorForm(array(), array('scenario_id' => $this->scenario_id));
  }

  /**
   * @method executeNewscenarioshift()
   * Generates a new scenario shift form
   * @param sfWebRequest $request
   */
  public function executeNewscenarioshift(sfWebRequest $request)
  {
    $this->scenarioshiftform = new agScenarioShiftForm();
  }

  /**
   * @method executeScenarioshiftlist()
   * Method display a list of available scenario shifts.
   * @param sfWebRequest $request
   * @return None
   */
  public function executeScenarioshiftlist(sfWebRequest $request)
  {
    $arrayQuery = Doctrine_Core::getTable('agScenarioShift')
      ->createQuery('ss')
      ->select('ss.*, s.id, s.scenario, sfg.id, sfg.scenario_facility_group, sfr.id')
      ->leftJoin('ss.agScenarioFacilityResource AS sfr')
      ->leftJoin('sfr.agScenarioFacilityGroup AS sfg')
      ->leftJoin('sfg.agScenario AS s')
      ->orderBy('s.scenario, sfg.scenario_facility_group, sfr.facility_resource_id');

    $queryString = $arrayQuery->getSqlQuery();
    $results = $arrayQuery->execute(array(), Doctrine::HYDRATE_SCALAR);
//    $this->scenarioshiftform = new agScenarioShiftForm();
//    $this->myRandomParam = "This is my random Param.";
//    $this->outputResults = $results;

    $this->scenarioShifts = array();
    foreach ($results as $scenShifts)
    {
      $scenShiftId = $scenShifts['ss_id'];

      $newRecord = array( 'scenario' => $scenShifts['s_scenario'],
                          'scenario_facility_group' => $scenShifts['sfg_scenario_facility_group'],
                          'facility_resource_id' => $scenShifts['ss_scenario_facility_resource_id'],
                          'staff_resource_type_id' => $scenShifts['ss_staff_resource_type_id'],
                          'task_id' => $scenShifts['ss_task_id'],
                          'task_length_minutes' => $scenShifts['ss_task_length_minutes'],
                          'break_length_minutes' => $scenShifts['ss_break_length_minutes'],
                          'minutes_start_to_facility_activation' => $scenShifts['ss_minutes_start_to_facility_activation'],
                          'minimum_staff' => $scenShifts['ss_minimum_staff'],
                          'maximum_staff' => $scenShifts['ss_maximum_staff'],
                          'staff_wave' => $scenShifts['ss_staff_wave'],
                          'shift_status_id' => $scenShifts['ss_shift_status_id'],
                          'deployment_algorithm_id' => $scenShifts['ss_deployment_algorithm_id']
                         );
      if (array_key_exists($scenShiftId, $this->scenarioShifts))
      {
        $tempArray = $this->scenarioShifts[$scenShiftId];
        $newArray = $tempArray . $newRecord;
        $this->scenarioShifts[$scenShiftId] = $newArray;
      } else {
        $this->scenarioShifts[$scenShiftId] = $newRecord;
      }
    }

//    $this->facilityResourceInfo = agFacilityResource::facilityResourceInfo();

    $query = Doctrine_Query::create()
    ->select('ss.*')
    ->from('agScenarioShift as ss');

    /**
     * Create pager
     */
    $this->pager = new sfDoctrinePager('agScenarioShift', 20);

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

  /**
   * @method executeScenarioshiftgroup()
   * Display a list of scenario with scenario shifts defined.
   * @param sfWebRequest $request
   * @return None
   */
  public function executeScenarioshiftgroup(sfWebRequest $request)
  {
    $query = Doctrine_Core::getTable('agScenario')
      ->createQuery('s')
      ->select('s.id, s.scenario,count(*) AS count')
      ->from('agScenario AS s')
      ->innerJoin('s.agScenarioFacilityGroup AS sfg')
      ->innerJoin('sfg.agScenarioFacilityResource AS sfr')
      ->innerJoin('sfr.agScenarioShift AS ss')
      ->groupBy('s.id, s.scenario');

    /**
     * Create pager
     */
    $this->pager = new sfDoctrinePager('agScenario', 20);

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

  /**
   * executeShowscenarioshiftgroup() defines the list of scenario shifts by scenario.
   * @param sfWebRequest $request
   */
  public function executeShowscenarioshiftgroup(sfWebRequest $request)
  {
    $this->scenarioId = $request->getParameter('scenId');
    $this->scenarioName = ucwords(Doctrine_Core::getTable('agScenario')->find($this->scenarioId)->getScenario());

    $query = Doctrine_Query::create()
    ->select('ss.*')
    ->from('agScenarioShift AS ss')
    ->innerJoin('ss.agScenarioFacilityResource AS sfr')
    ->innerJoin('sfr.agScenarioFacilityGroup AS sfg')
    ->where('sfg.scenario_id=?',$this->scenarioId)
    ;

    /**
     * Create pager
     */
    $this->pager = new sfDoctrinePager('agScenarioShift', 20);

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

  /**
   *
   * @param sfWebRequest $request
   * triggers the creation of a scenario
   */
  public function executeCreate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST));

    $this->form = new agScenarioForm();

    $this->processForm($request, $this->form);

    if($request->getParameter('facilitygroup'))
    {
      $this->ag_facility_resources =  Doctrine_Query::create()
      ->select('a.facility_id, af.*, afrt.*')
      ->from('agFacilityResource a, a.agFacility af, a.agFacilityResourceType afrt')
      ->execute();
      $this->groupform = new agScenarioFacilityGroupForm();
      $this->groupform->getObject()->setAgScenario()->id = $
      $this->redirect('scenario/newgroup');
    }
    else
    {
      $this->setTemplate('new');
    }
  }

  /**
   *
   * @param sfWebRequest $request
   * triggers the creation of a facility group
   */
  public function executeGroupcreate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST));

    $this->groupform = new agScenarioFacilityGroupForm();

    $this->processGroupform($request, $this->groupform);

    $this->setTemplate('newgroup');
  }

  /**
   * @todo what's this do?
   * @param sfWebRequest $request
   */
  public function executeGrouptypecreate(sfWebRequest $request)
  {
    $this->ag_facility_group_types = Doctrine_Core::getTable('agFacilityGroupType')
            ->createQuery('a')
            ->execute();
    $this->forward404Unless($request->isMethod(sfRequest::POST));

    $this->grouptypeform = new agFacilityGroupTypeForm();

    $this->processGrouptypeform($request, $this->grouptypeform);

    $this->setTemplate('grouptype');
  }

  /**
   * @method executeShiftTempaltecreate()
   * Create a new shift template
   * @param sfWebRequest $request
   */
  public function executeCreateshifttemplate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST));
//    $this->shiftgeneratorform = new agShiftGeneratorForm();
    $this->scenario_id = $request->getParameter('scenario_id');
    $this->getUser()->setAttribute('scenario_id', $this->scenario_id);
//    $this->shiftGeneratorForm = new agShiftGeneratorForm($object = null, $options = array(), $CSRFSecret = false);
    $this->shiftGeneratorForm = new agShiftGeneratorForm(array(), array('scenario_id' => $this->scenario_id));
    $this->processShifttemplateform($request, $this->shiftGeneratorForm);
//    $this->setTemplate('shifttemplate');
  }

  /**
   * @method executeCreatescenarioshift()
   * Create a new scenario shift
   * @param sfWebRequest $request
   * @return none
   */
  public function executeCreatescenarioshift(sfWebRequest $request)
  {
    $this->ag_scenario_shifts = Doctrine_Core::getTable('agScenarioShift')
            ->createQuery('ss')
            ->execute();
    $this->forward404Unless($request->isMethod(sfRequest::POST));
    $this->scenarioshiftform = new agScenarioShiftForm();
    $this->processScenarioshiftform($request, $this->scenarioshiftform);
    $this->setTemplate('scenarioShift');
  }

  /**
   *
   * @param sfWebRequest $request
   * creates a scenario form populated with info for the requested scenario
   */
  public function executeEdit(sfWebRequest $request)
  {
    $this->forward404Unless(
        $ag_scenario = Doctrine_Core::getTable('agScenario')->find(array($request->getParameter('id'))), sprintf('Object ag_scenario does not exist (%s).', $request->getParameter('id')));
    $this->ag_scenario_facility_groups = Doctrine_Core::getTable('agScenarioFacilityGroup')
            ->createQuery('a')
            ->select('a.*')
            ->from('agScenarioFacilityGroup a')
            ->where('a.scenario_id = ?', $request->getParameter('id'))
            ->execute();
    $this->form = new agScenarioForm($ag_scenario);

    //have to put a for each loop in here.
    //we need data for each of the facility_groups.
    foreach($this->ag_scenario_facility_groups as $ag_scenario_facility_group)
    {
      $current = $ag_scenario_facility_group->getAgScenarioFacilityResource();
      $current->setKeyColumn('activation_sequence');
      //index these by the activation sequence
      $currentoptions = array();
      foreach($current as $curopt)
      {
        $currentoptions[$curopt->facility_resource_id] = $curopt->getAgFacilityResource()->getAgFacility()->facility_name . " : " . $curopt->getAgFacilityResource()->getAgFacilityResourceType()->facility_resource_type; //$curopt->getAgFacility()->facility_name . " : " . $curopt->getAgFacilityResourceType()->facility_resource_type;
        /**
         * @todo [$curopt->activation_sequence] needs to still be applied to the list,
         */
      }

      $this->ag_allocated_facility_resources[] = $current;

      $this->ag_facility_resources[] =  Doctrine_Query::create()
        ->select('a.facility_id, af.*, afrt.*')
        ->from('agFacilityResource a, a.agFacility af, a.agFacilityResourceType afrt')
        ->whereNotIn('a.id', array_keys($currentoptions))->execute();
    }

  }

  /**
   *
   * @param sfWebRequest $request
   * creates a facility group form populated with info for the requested facility group
   */
  public function executeEditgroup(sfWebRequest $request)
  {
    $this->forward404Unless($ag_scenario_facility_group = Doctrine_Core::getTable('agScenarioFacilityGroup')
            ->createQuery('a')
            ->select('a.*, afr.*, afgt.*, afrt.*, afgas.*, fr.*, af.*, s.*')
            ->from('agScenarioFacilityGroup a, a.agScenarioFacilityResource afr, a.agFacilityGroupType afgt, a.agFacilityGroupAllocationStatus afgas, afr.agFacilityResource fr, fr.agFacility af, a.agScenario s, fr.agFacilityResourceType afrt')
            ->where('a.id = ?', $request->getParameter('id'))
            ->fetchOne(), sprintf('Object ag_scenario_facility_group does not exist (%s).', $request->getParameter('id')));

    $this->groupform = new agScenarioFacilityGroupForm($ag_scenario_facility_group);

    $current = $ag_scenario_facility_group->getAgScenarioFacilityResource();
    $current->setKeyColumn('activation_sequence');
    //index these by the activation sequence
    $currentoptions = array();
    foreach($current as $curopt)
    {
      $currentoptions[$curopt->facility_resource_id] = $curopt->getAgFacilityResource()->getAgFacility()->facility_name . " : " . $curopt->getAgFacilityResource()->getAgFacilityResourceType()->facility_resource_type; //$curopt->getAgFacility()->facility_name . " : " . $curopt->getAgFacilityResourceType()->facility_resource_type;
      /**
       * @todo [$curopt->activation_sequence] needs to still be applied to the list,
       */
    }

    $this->ag_allocated_facility_resources = $current;

    $this->ag_facility_resources =  Doctrine_Query::create()
      ->select('a.facility_id, af.*, afrt.*')
      ->from('agFacilityResource a, a.agFacility af, a.agFacilityResourceType afrt')
      ->whereNotIn('a.id', array_keys($currentoptions))->execute();
  }

  /**
   * @todo what's this do?
   * @param sfWebRequest $request
   */
  public function executeEditgrouptype(sfWebRequest $request)
  {
    $this->forward404Unless($ag_facility_group_type = Doctrine_Core::getTable('agFacilityGroupType')->find(array($request->getParameter('id'))), sprintf('Object ag_facility_group_type does not exist (%s).', $request->getParameter('id')));
    $this->ag_facility_group_types = Doctrine_Core::getTable('agFacilityGroupType')
            ->createQuery('a')
            ->execute();
    $this->grouptypeform = new agFacilityGroupTypeForm($ag_facility_group_type);
  }

  /**
   * @method executeEditscenarioshift()
   * Generate form for ag_scenario_shift table per record edit.
   * @param sfWebRequest $request
   * @return None
   */
  public function executeEditscenarioshift(sfWebRequest $request)
  {
    $this->forward404Unless($ag_scenario_shift = Doctrine_Core::getTable('agScenarioShift')->find(array($request->getParameter('id'))), sprintf('Object ag_scenario_shift does not exist (%s).', $request->getParameter('id')));
//    $this->ag_scenario_shift = Doctrine_Core::getTable('agScenarioShift')
//            ->createQuery('a')
//            ->execute();
    $this->scenarioshiftform = new agScenarioShiftForm($ag_scenario_shift);

  }

  /**
   *
   * @param sfWebRequest $request
   * processing the update of a scenario
   */
  public function executeUpdate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));
    $this->forward404Unless($ag_scenario = Doctrine_Core::getTable('agScenario')->find(array($request->getParameter('id'))), sprintf('Object ag_scenario does not exist (%s).', $request->getParameter('id')));
    $this->form = new agScenarioForm($ag_scenario);

    $this->processForm($request, $this->form);

    $this->setTemplate('edit');
  }

  /**
   *
   * @param sfWebRequest $request
   * processing the update of a facility group
   */
  public function executeGroupupdate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));
    $this->forward404Unless($ag_scenario_facility_group = Doctrine_Core::getTable('agScenarioFacilityGroup')->find(array($request->getParameter('id'))), sprintf('Object ag_scenario_facility_group does not exist (%s).', $request->getParameter('id')));
    $this->groupform = new agScenarioFacilityGroupForm($ag_scenario_facility_group);

    $this->processGroupform($request, $this->groupform);

    $this->setTemplate('editgroup');
  }

  /**
   *
   * @param sfWebRequest $request
   * processing the update of a facility group
   */
  public function executeGrouptypeupdate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));
    $this->forward404Unless($ag_facility_group_type = Doctrine_Core::getTable('agFacilityGroupType')->find(array($request->getParameter('id'))), sprintf('Object ag_facility_group_type does not exist (%s).', $request->getParameter('id')));
    $this->grouptypeform = new agFacilityGroupTypeForm($ag_facility_group_type);

    $this->processGrouptypeform($request, $this->grouptypeform);

    $this->setTemplate('grouptype');
  }

  /**
   * @method executeUpdatescenarioshift(sfWebRequest $request)
   * Process the update of a scenario shift.
   * @param sfWebRequest $request
   */
  public function executeUpdatescenarioshift(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));
    $this->forward404Unless($ag_scenario_shift = Doctrine_Core::getTable('agScenarioShift')->find(array($request->getParameter('id'))), sprintf('Object ag_scenario_shift does not exist (%s).', $request->getParameter('id')));
    $this->scenarioshiftform = new agScenarioShiftForm($ag_scenario_shift);

    $this->processScenarioshiftform($request, $this->scenarioshiftform);

    $this->setTemplate('scenarioshiftlist');
  }

  /**
   *
   * @param sfWebRequest $request
   * processing the deletion of a scenario
   */
  public function executeDelete(sfWebRequest $request)
  {
    $request->checkCSRFProtection();

    $this->forward404Unless($ag_scenario = Doctrine_Core::getTable('agScenario')->find(array($request->getParameter('id'))), sprintf('Object ag_scenario does not exist (%s).', $request->getParameter('id')));
    $sfGroups = $ag_scenario->getAgScenarioFacilityGroup();

    //get all facility groups associated with the scenario
    foreach ($sfGroups as $sfRes) {
      $groupRes = $sfRes->getAgScenarioFacilityResource();
      //get all facility resources associated with the facility group
      foreach ($groupRes as $gRes) {
        $gRes->delete();
      }
      $sfRes->delete();
    }
    $ag_scenario->delete();


    $this->redirect('scenario/list');
  }

  /**
   *
   * @param sfWebRequest $request
   * processing the deletion of a facility group
   */
  public function executeDeletegroup(sfWebRequest $request)
  {
    $request->checkCSRFProtection();

    $this->forward404Unless($ag_scenario_facility_group = Doctrine_Core::getTable('agScenarioFacilityGroup')->find(array($request->getParameter('id'))), sprintf('Object ag_scenario_facility_group does not exist (%s).', $request->getParameter('id')));

    $fgroupRes = $ag_scenario_facility_group->getAgScenarioFacilityResource();

    //get all facility resources associated with the facility group
    foreach ($fgroupRes as $fRes) {
      //get all shifts associated with each facility resource associated with the facility group
      $fResShifts = $fRes->getAgScenarioShift();
      foreach ($fResShifts as $resShift) {
        $resShift->delete();
      }
      $fRes->delete();
    }
    $ag_scenario_facility_group->delete();

    $this->redirect('scenario/listgroup');
  }

  /**
   * @method executeDeletegrouptype executes the logic to delete a facility group type
   * @param sfWebRequest $request
   */
  public function executeDeletegrouptype(sfWebRequest $request)
  {
    $request->checkCSRFProtection();

    $this->forward404Unless($ag_facility_group_type = Doctrine_Core::getTable('agFacilityGroupType')->find(array($request->getParameter('id'))), sprintf('Object ag_facility_group_type does not exist (%s).', $request->getParameter('id')));
    $ag_facility_group_type->delete();

    $this->redirect('scenario/grouptype');
  }

  /**
   * @method executeDeletescenarioshift executes the logic to delete a facility group type
   * @param sfWebRequest $request
   */
  public function executeDeletescenarioshift(sfWebRequest $request)
  {
    $request->checkCSRFProtection();

    $this->forward404Unless($ag_scenario_shift = Doctrine_Core::getTable('agScenarioShift')->find(array($request->getParameter('id'))), sprintf('Object ag_scenario_shift does not exist (%s).', $request->getParameter('id')));
    $ag_scenario_shift->delete();

    $this->redirect('scenario/scenarioshiftlist');
  }

  /**
   * executeDeleteshifttemplategroup()
   * @param sfWebRequest $request
   */
  public function executeDeleteshifttemplategroup(sfWebRequest $request)
  {
    $this->forward404Unless($shftTmpGrp = Doctrine_Core::getTable('agShiftTemplate')->createQuery('st')->where('st.scenario_id=?',$request->getParameter('scenId'))->execute(), sprintf('Object shift_template_group does not exist for scenario (%s).', $request->getParameter('scenId')));

    //Delete all scenario shift relating to the scenario.
    $scenShftGrp = Doctrine_Core::getTable('agScenarioShift')->createQuery('ss')->innerJoin('ss.agScenarioFacilityResource AS sfr')->innerJoin('sfr.agScenarioFacilityGroup AS sfg')->where('sfg.scenario_id=?',$request->getParameter('scenId'))->execute();
    foreach ($scenShftGrp as $scenShft) {
      $scenShft->delete();
    }

    //Delete all shift templates relating to the scenario.
    foreach ($shftTmpGrp as $shftTmp)
    {
      $shftTmp->delete();
    }

    $this->redirect('scenario/listshifttemplate');
  }

  /**
   * executeDeletescenarioshiftgroup()
   * @param sfWebRequest $request
   */
  public function executeDeletescenarioshiftgroup(sfWebRequest $request)
  {
//    $request->checkCSRFProtection();

//    $this->forward404Unless($ag_scenario_shift_group = Doctrine_Core::getTable('agScenarioShift')->find(array($request->getParameter('scenId'))), sprintf('Object ag_scenario_shift_group does not exist for scenario (%s).', $request->getParameter('scenId')));
    $this->forward404Unless($scenShftGrp = Doctrine_Core::getTable('agScenarioShift')->createQuery('ss')->innerJoin('ss.agScenarioFacilityResource AS sfr')->innerJoin('sfr.agScenarioFacilityGroup AS sfg')->where('sfg.scenario_id=?',$request->getParameter('scenId'))->execute(), sprintf('Object ag_scenario_shift_group does not exist for scenario (%s).', $request->getParameter('scenId')));

    //Delete all scenario shift relating to the scenario.
    foreach ($scenShftGrp as $scenShft) {
      $scenShft->delete();
    }

    $this->redirect('scenario/scenarioshiftgroup');
  }

  /**
   *
   * @param sfWebRequest $request
   * @param sfForm $form
   *
   * processing the scenario form
   */
  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid()) {

      $ag_scenario = $form->save();
      if($request->hasParameter('Continue'))
      {
        $this->ag_facility_resources = Doctrine_Query::create()
        ->select('a.facility_id, af.*, afrt.*')
        ->from('agFacilityResource a, a.agFacility af, a.agFacilityResourceType afrt')
        ->execute();
        $this->groupform = new agScenarioFacilityGroupForm();
//        $this->setTemplate('scenario/newgroup');
        $this->getUser()->setAttribute('scenario_id',$ag_scenario->getId());
        $this->redirect('scenario/newgroup');
        $foo = $this->getUser()->getAttribute('scenario_id');

      }else{
        $boo = $form->getValue('Save and Continue');
        $this->redirect('scenario/edit?id=' . $ag_scenario->getId());
      }
    }
  }

  /**
   *
   * @param sfWebRequest $request
   * @param sfForm $groupform
   *
   * processing the facility group form
   */
  protected function processGroupform(sfWebRequest $request, sfForm $groupform)
  {
    $groupform->bind($request->getParameter($groupform->getName()), $request->getFiles($groupform->getName()));
    if ($groupform->isValid()) {
      $ag_scenario_facility_group = $groupform->save();
      // The Group object has been created here.
      if($request->hasParameter('Continue'))
      {
        //$this->getUser()->setAttribute('staffResourceTypes', $this->staffResourceTypes);
        $this->getUser()->setAttribute('scenarioFacilityGroup', $ag_scenario_facility_group);
        $this->redirect('scenario/newstaffresources');
//        $this->executeNewstaffresources($request);
      } elseif($request->hasParameter('Another')) {
        $this->redirect('scenario/newgroup');
      } elseif($request->hasParameter('AssignAll')) {
        $groups = Doctrine::getTable('agScenarioFacilityGroup')
            ->findByDql('scenario_id = ?', $this->getUser()->getAttribute('scenario_id'))
            ->getData();
        $this->getUser()->setAttribute('scenarioFacilityGroup', $groups);
        $this->redirect('scenario/newstaffresources');
      }else{
        $this->redirect('scenario/edit?id=' . $ag_scenario_facility_group->scenario_id);
      }
      $this->redirect('scenario/editgroup?id=' . $ag_scenario_facility_group->getId());
    }
  }

/**
* Show page to create new staff resource requirements for facilities.
*
* @param sfWebRequest $request
**/
  public function executeNewstaffresources(sfWebRequest $request)
  {
    //Query to get the active scenario
    $this->scenario = Doctrine::getTable('agScenario')
        ->findByDql('id = ?', $this->getUser()->getAttribute('scenario_id'))
        ->getFirst();
    // Query to get all staff resource types.
    $this->staffResourceTypes = Doctrine_Query::create()
        ->select('a.id, a.staff_resource_type')
        ->from('agStaffResourceType a')
        ->execute();
    // Set $group to the user attribute to the 'scenarioFacilityGroup' attribute that came in through the request.
    $group = $this->getUser()->getAttribute('scenarioFacilityGroup');
    if (!is_array($group)) {
      $this->array = false;
      $this->scenarioFacilityGroup = $group;
      $this->scenarioFacilityResources = $this->scenarioFacilityGroup->getAgScenarioFacilityResource();
      $this->staffresourceform = new agScenarioFacilityResourceForm();
    } else {
      foreach ($group as $scenarioFacilityGroup) {
        $groups[] = $scenarioFacilityGroup;
      }
      $this->array = true;
      $this->scenarioFacilityGroup = $groups;
      //$this->scenarioFacilityResources = $this->scenarioFacilityGroup->getAgScenarioFacilityResource();
      //$this->staffresourceform = new agScenarioFacilityResourceForm();
    }
  }
    public function executeEditstaffresources(sfWebRequest $request)
  {
    //Query to get the active scenario
    $this->scenario = Doctrine::getTable('agScenario')
        ->findByDql('id = ?', $this->getUser()->getAttribute('scenario_id'))  //instead of relying on the user session's variable, let's use a param in the request
        ->getFirst();
    // Query to get all staff resource types.
    $this->staffResourceTypes = Doctrine_Query::create()
        ->select('a.id, a.staff_resource_type')
        ->from('agStaffResourceType a')
        ->execute();
    // Set $group to the user attribute to the 'scenarioFacilityGroup' attribute that came in through the request.
    $group = $this->getUser()->getAttribute('scenarioFacilityGroup');
    if (!is_array($group)) {
      $this->array = false;
      $this->scenarioFacilityGroup = $group;
      $this->scenarioFacilityResources = $this->scenarioFacilityGroup->getAgScenarioFacilityResource();
      $this->staffresourceform = new agScenarioFacilityResourceForm();
    } else {
      foreach ($group as $scenarioFacilityGroup) {
        $groups[] = $scenarioFacilityGroup;
      }
      $this->array = true;
      $this->scenarioFacilityGroup = $groups;
      //$this->scenarioFacilityResources = $this->scenarioFacilityGroup->getAgScenarioFacilityResource();
      //$this->staffresourceform = new agScenarioFacilityResourceForm();
    }
  }


  public function executeShowFacilityStaffResource(sfWebRequest $request)
  {
    $this->scenario = Doctrine::getTable('agScenario')
        ->findByDql('id = ?', $request->getParameter('id'))
        ->getFirst();
    $this->scenarioFacilityGroups = $this->scenario->getAgScenarioFacilityGroup();

    $this->facilityResources = Doctrine::getTable('agScenarioFacilityResource')
        ->findByDql('scenario_facility_group_id = ?', $request->getParameter('id'))
        ->getData();
    $this->facilityGroup = Doctrine::getTable('agScenarioFacilityGroup')
        ->findByDql('id = ?', $request->getParameter('id'))
        ->getFirst();
  }

  /**
  * Function to create the new facility staff resources.
  *
  * @param sfWebRequest $request
  **/
  public function executeFacilityStaffResourceCreate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST));
    $facilityGroups = $request->getPostParameters();
    // $groupName is passed in by the request to get the post paramater corresponding to the facility group name.
    // This can be refactored to get an array of groupnames, if acting on all facility groups for a scenario.
    $groupName = $request->getParameter('groupName');
    $scenarioId = $request->getParameter('scenarioId');
    $facilityGroup = $request->getPostParameter($groupName);
    $this->processFacilityStaffResourceForm($request, $facilityGroups, $scenarioId);
  }

  public function processFacilityStaffResourceForm($request, $facilityGroups, $scenarioId)
  {
    foreach ($facilityGroups as $facilityGroup) {
      foreach ($facilityGroup as $facility) {
        //$facilityGroupId = $facility->getId();
        foreach ($facility as $facilityStaffResource) {
          // The '$CSRFSecret = false' argument is used to prevent the missing CSRF token from invalidating the form.
          $facilityStaffResourceForm = new agEmbeddedAgFacilityStaffResourceForm($object = null, $options = array(), $CSRFSecret = false);
          $facilityStaffResourceForm->bind($facilityStaffResource, null);
          $facilityStaffResourceForm->updateObject($facilityStaffResourceForm->getTaintedValues());
          if ($facilityStaffResourceForm->isValid()) {
            // Pass all $savedResources to the view. They're agFacilityStaffResource obs. Get rest of data through them.
            $savedResources[] = $facilityStaffResourceForm->save();
          }
          
        }
      }
    }
    $this->redirect('scenario/showFacilityStaffResource?id=' . $request->getParameter('scenarioId'));
  }

  protected function processGrouptypeform(sfWebRequest $request, sfForm $grouptypeform)
  {
    $grouptypeform->bind($request->getParameter($grouptypeform->getName()), $request->getFiles($grouptypeform->getName()));
    if ($grouptypeform->isValid()) {
      $ag_facility_group_type = $grouptypeform->save();

      $this->redirect('scenario/grouptype');
    }
  }

  public function processShifttemplateform(sfWebRequest $request, sfForm $shiftTemplateForm)
  {
    $shiftTemplateForm->bind($request->getParameter($shiftTemplateForm->getName()), $request->getFiles($shiftTemplateForm->getName()));
    if ($shiftTemplateForm->isValid())
    {
      $ag_shift_template = $this->shiftGeneratorForm->saveEmbeddedForms();
      $this->redirect('scenario/newshifttemplate?scenId=' . $this->getUser()->getAttribute('scenario_id'));
    }
  }

  protected function processScenarioshiftform(sfWebRequest $request, sfForm $scenarioshiftform)
  {
    $scenarioshiftform->bind($request->getParameter($scenarioshiftform->getName()), $request->getFiles($scenarioshiftform->getName()));
    if ($scenarioshiftform->isValid())
    {
      $ag_scenario_shift = $scenarioshiftform->save();
      $this->redirect('scenario/editscenarioshift?id='.$ag_scenario_shift->getId());
    }
    $this->redirect('scenario/scenarioshiftlist');
  }

  public function executeGeneratescenarioshift()
  {
//    $generatedResult = agScenarioGenerator::shiftGenerator();
//    $this->queryString = $generatedResult[0];
//    $this->resultSet = $generatedResult[1];
//    $this->numRowsReturned = $this->resultSet->count();
    $generatedResult = agScenarioGenerator::shiftGenerator();
    print_r($generatedResult->toArray());
  }
}
