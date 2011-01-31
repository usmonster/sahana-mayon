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
class scenarioActions extends agActions
{

  protected $searchedModels = array('agScenarioFacilityGroup', 'agScenario', 'agStaff');

  public function executeListgroups(sfWebRequest $request)
  {
    $query = Doctrine_Core::getTable('agScenarioFacilityGroup')
            ->createQuery('a')
            ->select('a.*, afr.*, afgt.*, afgas.*, fr.*')
            ->from('agScenarioFacilityGroup a, a.agScenarioFacilityResource afr, a.agFacilityGroupType afgt, a.agFacilityGroupAllocationStatus afgas, a.agFacilityResource fr');

    if ($request->hasParameter('scenario')) {
      $query->where('a.scenario_id=?', $request->getParameter('id'));
    }

    $this->ag_scenario_facility_groups = $query->execute();
//$this->forward($module, $action) i think we need to forward here instead of just listfacilitygroup template because we have to
    $this->setTemplate(sfConfig::get('sf_app_template_dir') . DIRECTORY_SEPARATOR . 'listFacilityGroup');
  }

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
    //get the needed variables regardless of what action you are performing to staff resources
    $this->scenario = Doctrine::getTable('agScenario')
            ->findByDql('id = ?', $request->getParameter('id'))
            ->getFirst();
    $this->ag_scenario_facility_group = Doctrine_Core::getTable('agScenarioFacilityGroup')
            ->find(array($request->getParameter('id')));
    $this->scenarioFacilityGroups = $this->scenario->getAgScenarioFacilityGroup();
    $this->ag_staff_resources = Doctrine_Core::getTable('agScenarioFacilityResource')
            ->createQuery('agSFR')
            ->select('agSFR.*')
            ->from('agScenarioFacilityResource agSFR')
            ->where('scenario_facility_group_id = ?', $request->getParameter('id'))
            ->execute();
    $this->staffresourceform = new agStaffResourceRequirementForm();

//create / process form

    if ($request->isMethod('post')) {  //OR sfRequest::POST
      $facilityGroups = $request->getPostParameters();
//continuing workflow?
      if ($this->ag_scenario_facility_group) {
        foreach ($facilityGroups as $facilityGroup) {
          foreach ($facilityGroup as $facility) {
//$facilityGroupId = $facility->getId();
//are we editing or updating?
            foreach ($facility as $facilityStaffResource) {
// The '$CSRFSecret = false' argument is used to prevent the missing CSRF token from invalidating the form.
              $existing = Doctrine_Core::getTable('AgFacilityStaffResource')
                      ->createQuery('agSFR')
                      ->select('agFSR.*')
                      ->from('agFacilityStaffResource agFSR')
                      ->where('agFSR.staff_resource_type_id = ?', $facilityStaffResource['staff_resource_type_id'])
                      ->andWhere('agFSR.scenario_facility_resource_id = ?', $facilityStaffResource['scenario_facility_resource_id'])
                      ->fetchOne();
              if (!$existing) {
                $facilityStaffResourceForm = new agEmbeddedAgFacilityStaffResourceForm($object = null, $options = array(), $CSRFSecret = false);
              } else {
                $facilityStaffResourceForm = new agEmbeddedAgFacilityStaffResourceForm($existing, $options = array(), $CSRFSecret = false);
              }
              $facilityStaffResourceForm->bind($facilityStaffResource, null);
//$facilityStaffResourceForm->updateObjectEmbeddedForms();
//$facilityStaffResourceForm->updateObject($facilityStaffResourceForm->getTaintedValues()); //this fails
              if ($facilityStaffResourceForm->isValid() && $facilityStaffResource['minimum_staff'] && $facilityStaffResource['maximum_staff']) {
                /**
                 * @todo clean up for possible dirty data
                 *  This will not work cleanly, if someone hasn't entered a minimum AND maximum and the record exists it
                 *  will be deleted
                 */
                $savedResources[] = $facilityStaffResourceForm->save();
              } else {
                $facilityStaffResourceForm->getObject()->delete();
              }
            }
          }
        }
      }
//if ($request->getParameter('Continue')) {};
//were there any changes?
      if ($request->hasParameter('Continue')) {
        $this->redirect('scenario/shifttemplates?id=' . $request->getParameter('id'));
      } else {
        $this->redirect('scenario/staffresources?id=' . $request->getParameter('id'));
      }
    } else {

// Query to get all staff resource types.
      $this->staffResourceTypes = Doctrine_Query::create()
              ->select('a.id, a.staff_resource_type')
              ->from('agStaffResourceType a')
              ->execute();
// Set $group to the user attribute to the 'scenarioFacilityGroup' attribute that came in through the request.
      $groups = Doctrine::getTable('agScenarioFacilityGroup')
              ->findByDql('scenario_id = ?', $request->getParameter('id'))
              ->getData();

      if (!is_array($groups)) {
        $this->array = false;
        $this->scenarioFacilityGroup = $groups;
        $this->scenarioFacilityResources = $this->scenarioFacilityGroup->getAgScenarioFacilityResource();
        $this->staffresourceform = new agScenarioFacilityResourceForm();
      } else {
        foreach ($groups as $scenarioFacilityGroup) {
          $facilitygroups[] = $scenarioFacilityGroup;
        }
        $this->array = true;
        $this->scenarioFacilityGroup = $facilitygroups;
//$this->scenarioFacilityResources = $this->scenarioFacilityGroup->getAgScenarioFacilityResource();
//$this->staffresourceform = new agScenarioFacilityResourceForm();
      }

      if ($this->array == true) {
        $this->arrayBool = true;
        foreach ($this->scenarioFacilityGroup as $group) {
          foreach ($group->getAgScenarioFacilityResource() as $scenarioFacilityResource) {
            foreach ($this->staffResourceTypes as $srt) {
              $subKey = $group['scenario_facility_group'];
              $subSubKey = $scenarioFacilityResource->getAgFacilityResource()->getAgFacility()->facility_name . ': ' . ucwords($scenarioFacilityResource->getAgFacilityResource()->getAgFacilityResourceType()->facility_resource_type);
//this existing check should be refactored to be more efficient
              $existing = Doctrine_Core::getTable('AgFacilityStaffResource')
                      ->createQuery('agSFR')
                      ->select('agFSR.*')
                      ->from('agFacilityStaffResource agFSR')
                      ->where('agFSR.staff_resource_type_id = ?', $srt->id)
                      ->andWhere('agFSR.scenario_facility_resource_id = ?', $scenarioFacilityResource->id)
                      ->fetchOne();
//a better way to do this would be to follow the same array structure, so we could do something like
//$existing[$subKey][$subSubKey][$srt

              if ($existing) {
                $formsArray[$subKey][$subSubKey][$srt->staff_resource_type] =
                    new agEmbeddedAgFacilityStaffResourceForm($existing);
              } else {
                $formsArray[$subKey][$subSubKey][$srt->staff_resource_type] =
                    new agEmbeddedAgFacilityStaffResourceForm();
              }
              $staffResourceFormDeco = new agWidgetFormSchemaFormatterInlineLabels($formsArray[$subKey][$subSubKey][$srt->staff_resource_type]->getWidgetSchema());
              $formsArray[$subKey][$subSubKey][$srt->staff_resource_type]->getWidgetSchema()->addFormFormatter('staffResourceFormDeco', $staffResourceFormDeco);
              $formsArray[$subKey][$subSubKey][$srt->staff_resource_type]->getWidgetSchema()->setFormFormatterName('staffResourceFormDeco');
              $formsArray[$subKey][$subSubKey][$srt->staff_resource_type]->setDefault('scenario_facility_resource_id', $scenarioFacilityResource->getId());
              $formsArray[$subKey][$subSubKey][$srt->staff_resource_type]->setDefault('staff_resource_type_id', $srt->getId());
            }
          }
        }
      } else {
//single group or an array?
        $this->arrayBool = false;
        foreach ($this->scenarioFacilityGroup->getAgScenarioFacilityResource() as $scenarioFacilityResource) {
          foreach ($this->staffResourceTypes as $srt) {
            $subKey = $scenarioFacilityGroup['scenario_facility_group'];
            $subSubKey = $scenarioFacilityResource->getAgFacilityResource()->getAgFacility()->facility_name . ': ' . ucwords($scenarioFacilityResource->getAgFacilityResource()->getAgFacilityResourceType()->facility_resource_type);

            $formsArray[$subKey][$subSubKey][$srt->staff_resource_type] =
                new agEmbeddedAgFacilityStaffResourceForm();

            $staffResourceFormDeco = new agWidgetFormSchemaFormatterInlineLabels($formsArray[$subKey][$subSubKey][$srt->staff_resource_type]->getWidgetSchema());
            $formsArray[$subKey][$subSubKey][$srt->staff_resource_type]->getWidgetSchema()->addFormFormatter('staffResourceFormDeco', $staffResourceFormDeco);
            $formsArray[$subKey][$subSubKey][$srt->staff_resource_type]->getWidgetSchema()->setFormFormatterName('staffResourceFormDeco');
            $formsArray[$subKey][$subSubKey][$srt->staff_resource_type]->setDefault('scenario_facility_resource_id', $scenarioFacilityResource->getId());
            $formsArray[$subKey][$subSubKey][$srt->staff_resource_type]->setDefault('staff_resource_type_id', $srt->getId());
          }
        }
      }
//this is not right, but works (we can't directly modify a property of $this in our loop above)
      $this->formsArray = $formsArray;
    }
  }

  /**
   * calls index template
   * @param sfWebRequest $request
   */
  public function executeIndex(sfWebRequest $request)
  {
//here we can use $request to better form the index page for scenario
  }

  /**
   * calls up the review confirmation at end of the Scenario Creator
   * @param sfWebRequest $request
   */
  public function executeReview(sfWebRequest $request)
  {
    if ($this->scenario_id = $request->getParameter('id')) {
      $this->scenario_name = Doctrine_Core::getTable('agScenario')->find($this->scenario_id)->getScenario();
          $this->ag_scenario_facility_groups = Doctrine_Core::getTable('agScenarioFacilityGroup')
            ->createQuery('a')
            ->select('a.*, afr.*, afgt.*, afgas.*, fr.*')
            ->from('agScenarioFacilityGroup a, a.agScenarioFacilityResource afr, a.agFacilityGroupType afgt, a.agFacilityGroupAllocationStatus afgas, a.agFacilityResource fr')
            ->where('a.scenario_id = ?', $this->scenario_id)
            ->execute();
    }
  }

  public function executePre(sfWebRequest $request)
  {

  }

  /**
   *
   * @param sfWebRequest $request
   * making a staff pool is fun and easy with Agasti,
   */
  public function executeStaffpool(sfWebRequest $request)
  {
    $this->scenario_id = $request->getParameter('id');
    $this->saved_searches = $existing = Doctrine_Core::getTable('AgScenarioStaffGenerator')
            ->createQuery('agSSG')
            ->select('agSSG.*')
            ->from('agScenarioStaffGenerator agSSG')
            ->where('agSSG.scenario_id = ?', $this->scenario_id) //join up to see what staff pool
            ->execute();
    if ($request->getParameter('search_id')) {
      $this->search_id = $request->getParameter('search_id');
      $this->poolform = new agStaffPoolForm($this->search_id);
    } else {
      $this->poolform = new agStaffPoolForm();
    }

    if ($request->isMethod(sfRequest::POST)) {
      //$request->checkCSRFProtection();
      //OR if coming from an executed search
      if ($request->getParameter('Preview')) {
        $postParam = $request->getPostParameter('staff_pool');
        $lucene_search = $postParam['lucene_search'];
        $lucene_query = json_decode($lucene_search['query_condition']);

        parent::doSearch($lucene_query[0]); //eventually we should add a for each loop here to get ALL filters coming in and constructa a good search string
      } elseif ($request->getParameter('Delete')) {

        $ag_staff_gen = Doctrine_Core::getTable('agScenarioStaffGenerator')->find(array($request->getParameter('search_id'))); //maybe we should do a forward404unless, although no post should come otherwise
        $luceneQuery = $ag_staff_gen->getAgLuceneSearch();
        //get the related lucene search
        $ag_staff_gen->delete();
        $luceneQuery->delete();
        $this->redirect('scenario/staffpool?id=' . $request->getParameter('id'));
      } elseif ($request->getParameter('Save')) {
        //otherwise, we're SAVING/UPDATING
        $this->poolform = new agStaffPoolForm();
        $this->poolform->scenario_id = $request->getParameter('id');
        $this->poolform->bind($request->getParameter($this->poolform->getName()), $request->getFiles($this->poolform->getName()));

        if ($this->poolform->isValid()) {
          $ag_staff_pool = $this->poolform->saveEmbeddedForms();
          $this->redirect('scenario/staffpool?id=' . $request->getParameter('id'));
        }
      } else {

        //or, just make a new form
        $this->poolform = new agStaffPoolForm();
        $this->redirect('scenario/staffpool?id=' . $request->getParameter('id'));
      }
    }

    $this->filterForm = new sfForm();
    $this->filterForm->setWidgets(array(
      'staff_type' => new sfWidgetFormDoctrineChoice(array('model' => 'agStaffResourceType')),
      'organization' => new sfWidgetFormDoctrineChoice(array('model' => 'agOrganization', 'method' => 'getOrganization')),
    ));
//    $filterDeco = new agWidgetFormSchemaFormatterRow($luceneForm->getWidgetSchema());
//    $luceneForm->getWidgetSchema()->addFormFormatter('row', $luceneDeco);
//    $luceneForm->getWidgetSchema()->setFormFormatterName('row');
//    $this->filterForm->getWidget('staff_type')->setAttribute('class', 'filter');
    $this->filterForm->getWidget('organization')->setAttribute('class', 'filter');
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
    if ($request->getParameter('id')) {
      $this->groupform = new agScenarioFacilityGroupForm();
      //$this->getUser()->getAttribute('scenario_id')
      $this->groupform->setDefault('scenario_id', $request->getParameter('id'));
      // Hide the scenario field if this group is being created through scenario workflow.
      $this->groupform->setWidget('scenario_id', new sfWidgetFormInputHidden());
    } else {
      $this->groupform = new agScenarioFacilityGroupForm();
    }
    $this->scenarioName = Doctrine::getTable('agScenario')
            ->findByDql('id = ?', $request->getParameter('id'))
            ->getFirst()->scenario;
    $this->scenarioFacilityGroups = Doctrine::getTable('agScenarioFacilityGroup')
            ->findByDql('scenario_id = ?', $request->getParameter('id'))
            ->getData();
    $this->ag_allocated_facility_resources = '';
    $this->ag_facility_resources = Doctrine_Query::create()
            ->select('a.facility_id, af.*, afrt.*')
            ->from('agFacilityResource a, a.agFacility af, a.agFacilityResourceType afrt')
            ->execute();
  }

  /**
   *
   * @param sfWebRequest $request
   */
  public function executeShifttemplates(sfWebRequest $request)
  {
    if ($this->scenario_id = $request->getParameter('id')) {
      $this->scenario_name = Doctrine_Core::getTable('agScenario')->find($this->scenario_id)->getScenario();
      $this->shifttemplateform = new agShiftGeneratorForm(array(), array('scenario_id' => $this->scenario_id)); //this was from edit
    }
    if ($request->isMethod(sfRequest::POST)) {
      $this->shifttemplateform->bind($request->getParameter($this->shifttemplateform->getName()), $request->getFiles($this->shifttemplateform->getName()));
      if ($this->shifttemplateform->isValid()) {
        $ag_shift_template = $this->shifttemplateform->saveEmbeddedForms();
        if ($request->hasParameter('Continue')) {
          $generatedResult = agScenarioGenerator::shiftGenerator();
          //should be a try/catch here
          $this->redirect('scenario/scenarioshiftlist?id=' . $request->getParameter('id'));
        } else {
          $this->redirect('scenario/shifttemplates?id=' . $request->getParameter('id'));
        }
      }
    }
  }

  /**
   * @method executeEditshifttemplate()
   * Generates a new shit template form
   * @param sfWebRequest $request
   */
  public function executeEditshifttemplates(sfWebRequest $request)
  {
    $this->scenario_id = $request->getParameter('id');
    $this->scenario_name = Doctrine_Core::getTable('agScenario')->find($this->scenario_id)->getScenario();
    $this->shifttemplateform = new agShiftGeneratorForm(array(), array('scenario_id' => $this->scenario_id));
  }

  /**
   * @method executeNewshifttemplate()
   * Generates a new shit template form
   * @param sfWebRequest $request
   */
  public function executeNewshifttemplates(sfWebRequest $request)
  {
    $this->scenario_id = $request->getParameter('id');
    $this->scenario_name = Doctrine_Core::getTable('agScenario')->find($this->scenario_id)->getScenario();
    $this->shifttemplateform = new agShiftGeneratorForm(array(), array('scenario_id' => $this->scenario_id));
  }

  public function executeScenarioshifts(sfWebRequest $request)
  {
    $this->scenarioshiftform = new agScenarioShiftForm();
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
    $this->scenario_id = $request->getParameter('id');
    $this->scenario_name = Doctrine_Core::getTable('agScenario')->find($this->scenario_id)->getScenario();
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
    foreach ($results as $scenShifts) {
      $scenShiftId = $scenShifts['ss_id'];

      $newRecord = array('scenario' => $scenShifts['s_scenario'],
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
      if (array_key_exists($scenShiftId, $this->scenarioShifts)) {
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
            ->where('sfg.scenario_id=?', $this->scenarioId)
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

    if ($request->getParameter('facilitygroup')) {
      $this->ag_facility_resources = Doctrine_Query::create()
              ->select('a.facility_id, af.*, afrt.*')
              ->from('agFacilityResource a, a.agFacility af, a.agFacilityResourceType afrt')
              ->execute();
      $this->groupform = new agScenarioFacilityGroupForm();
      $this->groupform->getObject()->setAgScenario()->id = $
          $this->redirect('scenario/newgroup');
    } else {
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

    //somewhere here we need to forward, because we need to pass scenario

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
    $this->scenario_id = $request->getParameter('id');
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
    foreach ($this->ag_scenario_facility_groups as $ag_scenario_facility_group) {
      $current = $ag_scenario_facility_group->getAgScenarioFacilityResource();
      $current->setKeyColumn('activation_sequence');
      //index these by the activation sequence
      $currentoptions = array();
      foreach ($current as $curopt) {
        $currentoptions[$curopt->facility_resource_id] = $curopt->getAgFacilityResource()->getAgFacility()->facility_name . " : " . $curopt->getAgFacilityResource()->getAgFacilityResourceType()->facility_resource_type; //$curopt->getAgFacility()->facility_name . " : " . $curopt->getAgFacilityResourceType()->facility_resource_type;
        /**
         * @todo [$curopt->activation_sequence] needs to still be applied to the list,
         */
      }

//      $this->ag_allocated_facility_resources[] = $current;
      //    $this->ag_facility_resources[] = Doctrine_Query::create()
      //          ->select('a.facility_id, af.*, afrt.*')
      //        ->from('agFacilityResource a, a.agFacility af, a.agFacilityResourceType afrt')
      //      ->whereNotIn('a.id', array_keys($currentoptions))->execute();
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
            ->fetchOne(), sprintf('Object ag_scenario_facility_group does not exist (%s).', $request->getParameter('groupid')));

    $this->groupform = new agScenarioFacilityGroupForm($ag_scenario_facility_group);

    $current = $ag_scenario_facility_group->getAgScenarioFacilityResource();
    $current->setKeyColumn('activation_sequence');
    //index these by the activation sequence
    $currentoptions = array();
    foreach ($current as $curopt) {
      $currentoptions[$curopt->facility_resource_id] = $curopt->getAgFacilityResource()->getAgFacility()->facility_name . " : " . $curopt->getAgFacilityResource()->getAgFacilityResourceType()->facility_resource_type; //$curopt->getAgFacility()->facility_name . " : " . $curopt->getAgFacilityResourceType()->facility_resource_type;
      /**
       * @todo [$curopt->activation_sequence] needs to still be applied to the list,
       */
    }

    $this->ag_allocated_facility_resources = $current;

    $this->ag_facility_resources = Doctrine_Query::create()
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
    $this->forward404Unless($shftTmpGrp = Doctrine_Core::getTable('agShiftTemplate')->createQuery('st')->where('st.scenario_id=?', $request->getParameter('id'))->execute(), sprintf('Object shift_template_group does not exist for scenario (%s).', $request->getParameter('id')));

    //Delete all scenario shift relating to the scenario.
    $scenShftGrp = Doctrine_Core::getTable('agScenarioShift')->createQuery('ss')->innerJoin('ss.agScenarioFacilityResource AS sfr')->innerJoin('sfr.agScenarioFacilityGroup AS sfg')->where('sfg.scenario_id=?', $request->getParameter('id'))->execute();
    foreach ($scenShftGrp as $scenShft) {
      $scenShft->delete();
    }

    //Delete all shift templates relating to the scenario.
    foreach ($shftTmpGrp as $shftTmp) {
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
    $this->forward404Unless($scenShftGrp = Doctrine_Core::getTable('agScenarioShift')->createQuery('ss')->innerJoin('ss.agScenarioFacilityResource AS sfr')->innerJoin('sfr.agScenarioFacilityGroup AS sfg')->where('sfg.scenario_id=?', $request->getParameter('scenId'))->execute(), sprintf('Object ag_scenario_shift_group does not exist for scenario (%s).', $request->getParameter('scenId')));

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
      $ag_scenario->updateLucene();
      if ($request->hasParameter('Continue')) {
        $this->ag_facility_resources = Doctrine_Query::create()
                ->select('a.facility_id, af.*, afrt.*')
                ->from('agFacilityResource a, a.agFacility af, a.agFacilityResourceType afrt')
                ->execute();
        $this->groupform = new agScenarioFacilityGroupForm();
//        $this->setTemplate('scenario/newgroup');
        $this->redirect('scenario/newgroup?id=' . $ag_scenario->getId());
      } else {
        $this->redirect('scenario/edit?id=' . $ag_scenario->getId());
      }
    }
  }

  /**
   *
   * @param sfWebRequest $request
   * @param sfForm $grouptypeform
   *
   * processing the facility group type form
   */
  protected function processGrouptypeform(sfWebRequest $request, sfForm $grouptypeform)
  {
    $grouptypeform->bind($request->getParameter($grouptypeform->getName()), $request->getFiles($grouptypeform->getName()));
    if ($grouptypeform->isValid()) {
      $ag_facility_group_type = $grouptypeform->save();

      $this->redirect('scenario/grouptype');
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
      LuceneRecord::updateLuceneRecord($ag_scenario_facility_group);

//      Keep these lines here. They are the first steps of investigation into solving the permissions
//      issue on reindex.
//
//      Zend_Search_Lucene_Storage_Directory_Filesystem::setDefaultFilePermissions('0775');
//      chdir(sfConfig::get('sf_root_dir')); // Trick plugin into thinking you are in a project directory
//      $task = new luceneReindexTask($this->dispatcher, new sfFormatter());
//      $task->run(array(), array('connection' => 'doctrine'));

      $scenario_id = $ag_scenario_facility_group->getAgScenario()->getId();
      $c = $ag_scenario_facility_group->getAgScenarioFacilityResource();
      // The Group object has been created here.
      if ($request->hasParameter('Continue')) {
        //$this->getUser()->setAttribute('staffResourceTypes', $this->staffResourceTypes);
        $this->redirect('scenario/staffresources?id=' . $scenario_id);
//        $this->executeNewstaffresources($request);
      } elseif ($request->hasParameter('Another')) {
        $this->redirect('scenario/newgroup?id=' . $scenario_id);
      } elseif ($request->hasParameter('AssignAll')) {
        $groups = Doctrine::getTable('agScenarioFacilityGroup')
                ->findByDql('scenario_id = ?', $scenario_id)
                ->getData();
        $this->redirect('scenario/staffresources?id=' . $scenario_id);
      } else {
        $this->redirect('scenario/edit?id=' . $scenario_id);
      }
      $this->redirect('scenario/editgroup?groupid=' . $ag_scenario_facility_group->getId());
    }
  }

  public function processShifttemplateform(sfWebRequest $request, sfForm $shiftTemplateForm)
  {
    $shiftTemplateForm->bind($request->getParameter($shiftTemplateForm->getName()), $request->getFiles($shiftTemplateForm->getName()));
    if ($shiftTemplateForm->isValid()) {
      $ag_shift_template = $this->shiftGeneratorForm->saveEmbeddedForms();
      $this->redirect('scenario/newshifttemplates?id=' . $request->getParameter('id'));
    }
  }

  protected function processScenarioshiftform(sfWebRequest $request, sfForm $scenarioshiftform)
  {
    $scenarioshiftform->bind($request->getParameter($scenarioshiftform->getName()), $request->getFiles($scenarioshiftform->getName()));
    if ($scenarioshiftform->isValid()) {
      $ag_scenario_shift = $scenarioshiftform->save();
      $this->redirect('scenario/editscenarioshift?id=' . $ag_scenario_shift->getId());
    }
    $this->redirect('scenario/scenarioshiftlist');
  }

  public function executeGeneratescenarioshift()
  {
    $generatedResult = agScenarioGenerator::shiftGenerator();
    $this->redirect('scenario/scenarioshiftlist');
  }

}
