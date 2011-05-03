<?php

/**
 * extends agActions for scenario
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author Charles Wisniewski, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
class scenarioActions extends agActions
{

  protected $_searchedModels = array('agScenarioFacilityGroup', 'agScenario', 'agStaff');
  public static $scenario_id;
  public static $scenarioName;
  public static $wizardOp;

  /**
   * sets up basic scenario information from a web request, used in most actions here
   * @param <type> $request a web request
   * 
   */
  protected function setScenarioBasics($request)
  {
    $this->scenario_id = $request->getParameter('id');
    $this->scenarioName = Doctrine_Core::getTable('agScenario')->find($this->scenario_id)->scenario;
  }

  protected function wizardHandler(sfWebRequest $request, $step = null)
  {
    $encodedWizard = $request->getCookie('wizardOp'); //we stil want to keep the cookie get/set
                                                      //methods incase the frontend shorts something
    $wizardOp = json_decode($encodedWizard, true);
    $wizardOp['scenario_id'] = $request->getParameter('id');
    if (!isset($wizardOp['step'])) $wizardOp['step'] = 1;
    if ($step != null) $wizardOp['step'] = $step;
    $this->wizard = new agScenarioWizard($wizardOp);
    $this->wizardDiv = $this->wizard->getList();
  }

  /**
   *
   * @param sfWebRequest $request
   * generates a list of scenarios that are passed to the view
   */
  public function executeList(sfWebRequest $request)
  {
    $this->ag_scenarios = agDoctrineQuery::create()
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
    $this->setScenarioBasics($request);
    $this->ag_scenario_facility_groups = agDoctrineQuery::create()
            ->select('a.*, afr.*, afgt.*, afgas.*, fr.*')
            ->from(
                'agScenarioFacilityGroup a,
                  a.agScenarioFacilityResource afr,
                  a.agFacilityGroupType afgt,
                  a.agFacilityGroupAllocationStatus afgas,
                  a.agFacilityResource fr'
            )
            ->where('a.scenario_id = ?', $request->getParameter('id'))
            ->execute();
    $this->getResponse()->setTitle('Sahana Agasti Edit ' . $this->scenarioName . ' Scenario Facility Groups');
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
   * Define Staff Resource Requirements for all facility groups in a scenario facility group
   * @todo this page/action could also handle a '1.5' workflow, where you would edit staff resource
   *        requirements for an individual facility group, then make another facility group, etc.
   * @param sfWebRequest $request
   * generates and passes a new scenario form to the view
   */
  public function executeStaffresources(sfWebRequest $request)
  {
//get the needed variables regardless of what action you are performing to staff resources
    $formsArray = array();
    $this->setScenarioBasics($request);
    $this->wizardHandler($request, 5);
    //the above should not fail.
    $this->scenario = Doctrine::getTable('agScenario')
            ->findByDql('id = ?', $request->getParameter('id'))
            ->getFirst();
    $this->ag_scenario_facility_group = Doctrine_Core::getTable('agScenarioFacilityGroup')
            ->find(array($request->getParameter('id')));
    $this->scenarioFacilityGroups = $this->scenario->getAgScenarioFacilityGroup();
    $this->ag_staff_resources = agDoctrineQuery::create()
            ->select('agSFR.*')
            ->from('agScenarioFacilityResource agSFR')
            ->where('scenario_facility_group_id = ?', $request->getParameter('id'))
            ->execute();
    //$this->staffresourceform = new agStaffResourceRequirementForm($this->scenario_id);
//this came from the _staffresources partial
//construct our top level form, with all forms contained, and pass to the view
//create / process form

    if ($request->isMethod(sfRequest::POST)) {
//      $facilityGroups = $request->getPostParameters();
      $facilityGroups = $request->getParameter('staff_resource');
      unset($facilityGroups['_csrf_token']);/** @todo ??? unsetting csrf token, this should be fixed. */
//continuing workflow?
      if ($this->ag_scenario_facility_group) {
        foreach ($facilityGroups as $facilityGroup) {
          foreach ($facilityGroup as $facility) {
//are we editing or updating?
            foreach ($facility as $facilityStaffResource) {
// The '$CSRFSecret = false' argument is used to prevent the missing CSRF token from invalidating the form.
              $existing = agDoctrineQuery::create()
                      ->select('agFSR.*')
                      ->from('agFacilityStaffResource agFSR')
                      ->where(
                          'agFSR.staff_resource_type_id = ?',
                          $facilityStaffResource['staff_resource_type_id']
                      )
                      ->andWhere(
                          'agFSR.scenario_facility_resource_id = ?',
                          $facilityStaffResource['scenario_facility_resource_id']
                      )
                      ->fetchOne();
              if (!$existing) {
                $facilityStaffResourceForm = new agEmbeddedAgFacilityStaffResourceForm($object = null, $options = array(), $CSRFSecret = false);
              } else {
                $facilityStaffResourceForm = new agEmbeddedAgFacilityStaffResourceForm($existing, $options = array(), $CSRFSecret = false);
              }
              $facilityStaffResourceForm->bind($facilityStaffResource, null);
//$facilityStaffResourceForm->updateObjectEmbeddedForms();
//$facilityStaffResourceForm->updateObject($facilityStaffResourceForm->getTaintedValues()); //this fails
              if ($facilityStaffResourceForm->isValid() && isset($facilityStaffResource['minimum_staff']) && isset($facilityStaffResource['maximum_staff'])) {
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
//were there any changes?
      if ($request->hasParameter('Continue')) {
        $this->redirect('scenario/shifttemplates?id=' . $this->scenario_id);
      } else {
        $this->redirect('scenario/staffresources?id=' . $this->scenario_id);
      }
    } else {

// Query to get all staff resource types.
      $dsrt = agScenarioResourceHelper::returnDefaultStaffResourceTypes($this->scenario_id);
      if (count($dsrt) > 1) {
        $this->staffResourceTypes = $dsrt;
      } else {
        $this->staffResourceTypes =
                agDoctrineQuery::create()
                ->select('srt.id, srt.staff_resource_type')
                ->from('agStaffResourceType srt')
                ->execute(array(), Doctrine_Core::HYDRATE_SCALAR);
      }
// Set $group to the user attribute to the 'scenarioFacilityGroup' attribute that came in through the request.
      $groups = Doctrine::getTable('agScenarioFacilityGroup')
              ->findByDql('scenario_id = ?', $this->scenario_id)
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
              $existing = agDoctrineQuery::create()
                      ->select('agFSR.*')
                      ->from('agFacilityStaffResource agFSR')
                      ->where('agFSR.staff_resource_type_id = ?', $srt['srt_id'])
                      ->andWhere('agFSR.scenario_facility_resource_id = ?', $scenarioFacilityResource->id)
                      ->fetchOne();
//a better way to do this would be to follow the same array structure, so we could do something like
//$existing[$subKey][$subSubKey][$srt

              if ($existing) {
                $formsArray[$subKey][$subSubKey][$srt['srt_staff_resource_type']] =
                    new agEmbeddedAgFacilityStaffResourceForm($existing);
              } else {
                $formsArray[$subKey][$subSubKey][$srt['srt_staff_resource_type']] =
                    new agEmbeddedAgFacilityStaffResourceForm();
              }
//              $staffResourceFormDeco = new agFormFormatterInlineLabels($formsArray[$subKey][$subSubKey][$srt->staff_resource_type]->getWidgetSchema());
//              $formsArray[$subKey][$subSubKey][$srt->staff_resource_type]->getWidgetSchema()->addFormFormatter('staffResourceFormDeco', $staffResourceFormDeco);
//              $formsArray[$subKey][$subSubKey][$srt->staff_resource_type]->getWidgetSchema()->setFormFormatterName('staffResourceFormDeco');
              $formsArray[$subKey][$subSubKey][$srt['srt_staff_resource_type']]->setDefault('scenario_facility_resource_id', $scenarioFacilityResource->getId());
              $formsArray[$subKey][$subSubKey][$srt['srt_staff_resource_type']]->setDefault('staff_resource_type_id', $srt['srt_id']);
            }
          }
        }
      } else {
//single group or an array? at this point should always be an array... or not matter
        $this->arrayBool = false;
        foreach ($this->scenarioFacilityGroup->getAgScenarioFacilityResource() as $scenarioFacilityResource) {
          foreach ($this->staffResourceTypes as $srt) {
            $subKey = $scenarioFacilityGroup['scenario_facility_group'];
            $subSubKey = $scenarioFacilityResource->getAgFacilityResource()->getAgFacility()->facility_name . ': ' . ucwords($scenarioFacilityResource->getAgFacilityResource()->getAgFacilityResourceType()->facility_resource_type);

            $formsArray[$subKey][$subSubKey][$srt['staff_resource_type']] =
                new agEmbeddedAgFacilityStaffResourceForm();

//            $staffResourceFormDeco = new agFormFormatterInlineLabels($formsArray[$subKey][$subSubKey][$srt->staff_resource_type]->getWidgetSchema());
//            $formsArray[$subKey][$subSubKey][$srt->staff_resource_type]->getWidgetSchema()->addFormFormatter('staffResourceFormDeco', $staffResourceFormDeco);
//            $formsArray[$subKey][$subSubKey][$srt->staff_resource_type]->getWidgetSchema()->setFormFormatterName('staffResourceFormDeco');
            $formsArray[$subKey][$subSubKey][$srt['staff_resource_type']]->setDefault('scenario_facility_resource_id', $scenarioFacilityResource->getId());
            $formsArray[$subKey][$subSubKey][$srt['staff_resource_type']]->setDefault('staff_resource_type_id', $srt['srt_id']);
          }
        }
      }
      $this->formsArray = $formsArray;
    }
    $this->facilityStaffResourceContainer = new agFacilityStaffResourceContainerForm($formsArray);

    $this->getResponse()->setTitle('Sahana Agasti Edit ' . $this->scenario['scenario'] . ' Scenario');

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
      $this->scenario_description = Doctrine_Core::getTable('agScenario')->find($this->scenario_id)->getDescription();
      $this->ag_scenario_facility_groups = agDoctrineQuery::create()
              ->select('a.*, afr.*, afgt.*, afgas.*, fr.*')
              ->from(
                  'agScenarioFacilityGroup a,
                    a.agScenarioFacilityResource afr,
                    a.agFacilityGroupType afgt,
                    a.agFacilityGroupAllocationStatus afgas,
                    a.agFacilityResource fr'
              )
              ->where('a.scenario_id = ?', $this->scenario_id)
              ->execute();
    }
//p-code
    $this->getResponse()->setTitle('Sahana Agasti Edit ' . $this->scenario_name . ' Scenario');
//end p-code
  }

  public function executePre(sfWebRequest $request)
  {

  }

  /**
   * set up the form to define staff pools, via saved search
   * this function handles all CRUD operations for the 'scenario staff pool'
   * @param sfWebRequest $request
   *
   */
  public function executeStaffpool(sfWebRequest $request)
  {
    $this->setScenarioBasics($request);
    $this->wizardHandler($request,4);
    $this->scenario_staff_count = Doctrine_Core::getTable('AgScenarioStaffResource')
            ->findby('scenario_id', $this->scenario_id)->count();
    $this->target_module = 'staff';

    $this->saved_searches = $existing = agDoctrineQuery::create()
      ->select('ssg.*')
          ->addSelect('s.*')
        ->from('agScenarioStaffGenerator ssg')
          ->innerJoin('ssg.agSearch s')
        ->where('ssg.scenario_id = ?', $this->scenario_id)
        ->orderBy('ssg.search_weight DESC, s.search_name ASC')
        ->execute();
//get all available staff
    $this->total_staff = Doctrine_Core::getTable('agStaff')->count();
    $this->total_resources = Doctrine_Core::getTable('agStaffResource')->count();

//set up inputs for form
    $this->filterForm = new agStaffPoolFilterForm($this->scenario_id);
//EDIT
    if ($request->getParameter('search_id')) {
      $this->search_id = $request->getParameter('search_id');
      $this->poolform = new agStaffPoolForm($this->search_id);

      $search_condition = $this->poolform->getEmbeddedForm('search')->getObject()->search_condition;

      if ($search_condition != '[]') {
        $query_array = json_decode($search_condition, true);
        foreach ($query_array as $querypart) {
          $querypart['condition'];
          $querypart['field'];
//these search definitions should be stored in 'search type' table maybe?
          if ($querypart['field'] == 'agStaffResourceType.staff_resource_type') {
            $defaultValue = agDoctrineQuery::create()->select('id')->from('agStaffResourceType')
                    ->where('staff_resource_type=?', $querypart['condition'])->execute(array(), 'single_value_array');
          } else {
            $defaultValue = agDoctrineQuery::create()->select('id')->from('agOrganization')
                    ->where('organization=?', $querypart['condition'])->execute(array(), 'single_value_array');
          }
          $this->filterForm->setDefault($querypart['field'], $defaultValue[0]);
        }
      }
    } elseif($request->getParameter('Preview')) {
          $postParam = $request->getPostParameter('staff_pool');
          $search = $postParam['search'];
          $staff_generator = $postParam['staff_generator'];
          $values = array('sg_values' =>
            array('search_weight' => $staff_generator['search_weight']),
            's_values' =>
            array('search_name' => $search['search_name'],
              'search_type_id' => $search['search_type_id']));

      $this->poolform = new agStaffPoolForm(null,$values); //construct our pool form with POST data
    }
    else{
      $this->poolform = new agStaffPoolForm();
    }

    if ($request->isMethod(sfRequest::POST) || $request->getParameter('search_id')) {

//PREVIEW
      if ($request->getParameter('Preview') || $request->getParameter('search_id')) {
//fix this.
        $incomingFields = $this->filterForm->getWidgetSchema()->getFields();
        if ($request->isMethod(sfRequest::POST)) {


          //update the default values of our form from the web request POST data
//          $updatedSearchForm = $this->poolform->getEmbeddedForm('search');
//          $updatedSearchForm->setDefault('search_name',$search['search_name']);
//          $this->poolform->embedForm('search', $updatedSearchForm);
//          $this->poolform->getWidgetSchema()->setLabel('search', false);
          $search_condition = json_decode($search['search_condition'], true);

          foreach ($search_condition as $querypart) {
            //these search definitions should be stored in 'search type' table maybe?
            if ($querypart['field'] == 'agStaffResourceType.staff_resource_type') {
              $defaultValue = agDoctrineQuery::create()->select('id')->from('agStaffResourceType')
                      ->where('staff_resource_type=?', $querypart['condition'])->execute(array(), 'single_value_array');
            } else {
              $defaultValue = agDoctrineQuery::create()->select('id')->from('agOrganization')
                      ->where('organization=?', $querypart['condition'])->execute(array(), 'single_value_array');
            }
            $this->filterForm->setDefault($querypart['field'], $defaultValue[0]);
          }
        } elseif ($request->getParameter('search_id')) {
          $search_id = $request->getParameter('search_id');
          $search_condition = json_decode($this->poolform->getEmbeddedForm('search')->getObject()->search_condition, true);
        } else {
          $search_id = null;
        }

        $staff_ids = agScenarioStaffGeneratorHelper::executeStaffPreview($search_condition);
        
        $resultArray = agListHelper::getStaffList($staff_ids);
        $this->status = 'active';
        $this->pager = new agArrayPager(null, 10);
        $this->pager->setResultArray($resultArray);
        $this->pager->setPage($this->getRequestParameter('page', 1));
        $this->pager->init();
      } elseif ($request->getParameter('Delete')) {
//DELETE
        $ag_staff_gen = Doctrine_Core::getTable('agScenarioStaffGenerator')->find(array($request->getParameter('search_id'))); //maybe we should do a forward404unless, although no post should come otherwise
        $searchQuery = $ag_staff_gen->getAgSearch();
        //get the related lucene search`
        $ag_staff_gen->delete();
        $searchQuery->delete();
        $this->redirect('scenario/staffpool?id=' . $request->getParameter('id'));
      }
//SAVE
      elseif ($request->getParameter('Save') || $request->getParameter('Continue')) { //otherwise, we're SAVING/UPDATING
        if ($request->getParameter('staff_gen_id')) {
          $this->search_id = $request->getParameter('search_id');
          $this->poolform = new agStaffPoolForm($this->search_id); //make spForm with staff_gen_id
        } else {
          $this->poolform = new agStaffPoolForm();
        }

        $this->poolform->scenario_id = $request->getParameter('id');
        $this->poolform->bind($request->getParameter($this->poolform->getName()), $request->getFiles($this->poolform->getName()));

        if ($this->poolform->isValid()) {
          $ag_staff_pool = $this->poolform->saveEmbeddedForms();

          agScenarioStaffGeneratorHelper::generateStaffPool($this->scenario_id);
          //$staff_resource_ids = agScenarioGenerator::staffPoolGenerator($search_condition, $this->scenario_id);
          //$addedStaff = agScenarioGenerator::saveStaffPool($staff_resource_ids, $this->scenario_id, $staff_generator['search_weight']);
          if($request->getParameter('Continue')){
            $this->redirect('scenario/shifttemplates?id=' . $request->getParameter('id'));
          }else{
             $this->redirect('scenario/staffpool?id=' . $request->getParameter('id'));
          }
        }
      } else {
//NEW
        $this->poolform = new agStaffPoolForm();
        //$this->redirect('scenario/staffpool?id=' . $request->getParameter('id'));
      }
    }
//p-code
    $this->getResponse()->setTitle('Sahana Agasti Edit ' . $this->scenarioName . ' Scenario');
//end p-code
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
   * sets up a new scenario meta information form
   * @param sfWebRequest $request
   */
  public function executeMeta(sfWebRequest $request)
  {
    $this->wizardHandler($request,1);
    if ($request->getParameter('id')) {
      $this->setScenarioBasics($request);
      $ag_scenario = Doctrine_Core::getTable('agScenario')->find(array($this->scenario_id));
      $this->form = new agScenarioForm($ag_scenario);
      $this->metaAction = 'Edit';
      $this->getResponse()->setTitle('Sahana Agasti Edit ' . $this->scenarioName . ' Scenario');
    } else {
      $this->metaAction = 'Create New';
      $this->form = new agScenarioForm();
    }
    if ($request->isMethod(sfRequest::POST)) {
      $this->form->bind($request->getParameter($this->form->getName()), $request->getFiles($this->form->getName()));
      if ($this->form->isValid()) {
        $ag_scenario = $this->form->save();
        $ag_scenario->updateLucene();
        if ($request->hasParameter('Continue')) {
          $this->ag_facility_resources = agDoctrineQuery::create()
                  ->select('a.facility_id, af.*, afrt.*')
                  ->from('agFacilityResource a, a.agFacility af, a.agFacilityResourceType afrt')
                  ->execute();
          $this->groupform = new agScenarioFacilityGroupForm();
//        $this->setTemplate('scenario/newgroup');
          $this->redirect('scenario/resourcetypes?id=' . $ag_scenario->getId());
        } else {
          $this->redirect('scenario/meta?id=' . $ag_scenario->getId());
        }
      }
    }
  }

  /**
   * Default Resource Type definition for a scenario
   * @param sfWebRequest $request request coming from the client
   */
  public function executeResourcetypes(sfWebRequest $request)
  {
    $this->setScenarioBasics($request);
    $this->wizardHandler($request, 2) ;
    $this->resourceForm = new agDefaultResourceTypeForm($this->scenario_id);
    $this->getResponse()->setTitle('Scenario Creation Wizard - set Default Resource Types needed for ' . $this->scenarioName . ' Scenario');
    if ($request->isMethod(sfRequest::POST)) {
      $staffDefaults = Doctrine::getTable('agDefaultScenarioStaffResourceType')
              ->findByDql('scenario_id = ?', $this->scenario_id);

      $facilityDefaults = Doctrine::getTable('agDefaultScenarioFacilityResourceType')
              ->findByDql('scenario_id = ?', $this->scenario_id);


      $topForm = $request->getParameter($this->resourceForm->getName());

      $staffParams = $topForm['staff_types'];
      $facilityParams = $topForm['facility_types'];
//update and remove
      foreach ($staffDefaults as $index => &$record) {
        if (array_key_exists($index, $staffParams)) {
          $staffDefaultResourceId = $record['staff_resource_type_id'];
          //update
        }
//remove items from the collection that are not in our post params
        else {
          $staffDefaults->remove($index);
        }
      }
      foreach ($facilityDefaults as $index => &$record) {
        if (array_key_exists($index, $facilityParams)) {
          $facilityDefaultResourceId = $record['facility_resource_id'];
          //update
        }
//remove items from the collection that are not in our post params
        else {
          $facilityDefaults->remove($index);
        }
      }



//inserts
      foreach ($facilityParams['facility_resource_type_id'] as $key => $value) {
        $newRec = new agDefaultScenarioFacilityResourceType();

        $newRec['scenario_id'] = $this->scenario_id;
        $newRec['facility_resource_type_id'] = $value;

        $facilityDefaults->add($newRec);
        unset($facilityParams[$key]);
      }
      foreach ($staffParams['staff_resource_type_id'] as $key => $value) {
        $newRec = new agDefaultScenarioStaffResourceType();

        $newRec['scenario_id'] = $this->scenario_id;
        $newRec['staff_resource_type_id'] = $value;

        $staffDefaults->add($newRec);
        unset($staffParams[$key]);
      }
      //$conn = Doctrine_Manager::connection();
      $staffDefaults->save();
      $facilityDefaults->save();
      //$conn->commit();

      if ($request->hasParameter('Continue')) {

//do some stuff
//        $this->setTemplate('scenario/newgroup');
        $this->redirect('scenario/fgroup?id=' . $this->scenario_id);
      } else {
        $this->redirect('scenario/resourcetypes?id=' . $this->scenario_id);
      }
    }
  }

  /**
   *
   * @param sfWebRequest $request holds request data
   * this function sets up and processes the facility group form, providing all needed data to the groupform
   * template, depending on what CRUD operation the user is performing,
   */
  public function executeFgroup(sfWebRequest $request)
  {
//if you are coming here from not within the workflow you fail and get 404
    $this->forward404Unless($this->scenario_id = $request->getParameter('id'));
//set up some things to show up on the screen for our form
    $this->setScenarioBasics($request);
    $this->wizardHandler($request, 3) ;
    $this->scenarioFacilityGroups = Doctrine::getTable('agScenarioFacilityGroup')
            ->findByDql('scenario_id = ?', $this->scenario_id)
            ->getData();

    $this->allocatedFacilityResources = '';  //set this default incase none exist

//    $this->ag_facility_resources = agDoctrineQuery::create()
//            ->select('a.facility_id, af.*, afrt.*')
//            ->from('agFacilityResource a, a.agFacility af, a.agFacilityResourceType afrt')
//            ->execute();
// Get the facility resource types available to this scenario.
    $this->facilityResourceTypes = agDoctrineQuery::create()
            ->select('frt.id, frt.facility_resource_type, facility_resource_type_abbr')
            ->from('agFacilityResourceType frt')
            ->innerJoin('frt.agDefaultScenarioFacilityResourceType dsfrt')
            ->where('dsfrt.scenario_id =?', $this->scenario_id)
            ->orderBy('frt.facility_resource_type')
            ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
// Get all the facility resources available to this scenario (those that aren't already in use
// in this scenario).
    $this->availableFacilityResources = agDoctrineQuery::create()
            ->select('fr.id')
            ->addSelect('f.facility_name')
            ->addSelect('f.facility_code')
            ->addSelect('frt.facility_resource_type')
            ->addSelect('frt.id')
            ->addSelect('frt.facility_resource_type_abbr')
            ->from('agFacilityResource fr')
            ->innerJoin('fr.agFacility f')
            ->innerJoin('fr.agFacilityResourceType frt')
            ->innerJoin('frt.agDefaultScenarioFacilityResourceType dsfrt')
            ->where('NOT EXISTS (
              SELECT s1.id
                FROM agFacilityResource s1
                  INNER JOIN s1.agScenarioFacilityResource s2
                  INNER JOIN s2.agScenarioFacilityGroup s3
              WHERE s3.scenario_id = ?
                AND s1.id = fr.id)',
                $this->scenario_id)
            ->andWhere('dsfrt.scenario_id = ?', $this->scenario_id)
            ->execute(array(), Doctrine_Core::HYDRATE_SCALAR);

    $this->facilityStatusForm = new sfForm();
    $this->facilityStatusForm->setWidgets(array(
      'status' => new sfWidgetFormDoctrineChoice(array('model' => 'agFacilityResourceStatus', 'method' => 'getFacilityResourceStatus'), array())
    ));

    // Query to get the resource allocation statuses that will be used as table headers.
    $this->selectStatuses = agDoctrineQuery::create()
            ->select('fras.id, fras.facility_resource_allocation_status')
            ->from('agFacilityResourceAllocationStatus fras')
            ->where('scenario_display = true')
            ->orderBy('fras.facility_resource_allocation_status')
            ->execute(array(), Doctrine_Core::HYDRATE_SCALAR);
    $this->facilityGroups = agDoctrineQuery::create()
        ->select('sfg.id, sfg.scenario_facility_group')
        ->from('agScenarioFacilityGroup sfg')
        ->where('sfg.scenario_id = ?', $this->scenario_id)
        ->execute(array(), Doctrine_Core::HYDRATE_SCALAR);
//    if(empty($this->facilityGroups)) {
//      $groupSelector = false;
//    } else {
//      $groupSelector = new sfWidgetFormChoice
//    }
    if ($request->getParameter('groupid')) {
//EDIT
      $this->groupId = $request->getParameter('groupid');
      $ag_scenario_facility_group = agDoctrineQuery::create()
              ->select('a.*, afr.*, afgt.*, afrt.*, afgas.*, fr.*, af.*, s.*')
              ->from('agScenarioFacilityGroup a, a.agScenarioFacilityResource afr, a.agFacilityGroupType afgt, a.agFacilityGroupAllocationStatus afgas, afr.agFacilityResource fr, fr.agFacility af, a.agScenario s, fr.agFacilityResourceType afrt')
              ->where('a.id = ?', $request->getParameter('groupid'))
              ->fetchOne();

      $agScenarioFacilityGroup = agDoctrineQuery::create()
        ->select()
        ->from('agScenarioFacilityGroup')
        ->where('id = ?', $this->groupId)
        ->fetchOne();

      $this->groupform = new agScenarioFacilityGroupForm($ag_scenario_facility_group);

//index these by the activation sequence
      $allocatedFacilityResources = agDoctrineQuery::create()
              ->select('fr.id')
              ->addSelect('f.facility_name')
              ->addSelect('f.facility_code')
              ->addSelect('frt.facility_resource_type')
              ->addSelect('frt.id')
              ->addSelect('frt.facility_resource_type_abbr')
              ->addSelect('sfr.id')
              ->addSelect('sfr.activation_sequence')
              ->addSelect('fras.id')
              ->addSelect('fras.facility_resource_allocation_status')
              ->from('agFacilityResource fr')
              ->innerJoin('fr.agFacility f')
              ->innerJoin('fr.agFacilityResourceType frt')
              ->innerJoin('fr.agScenarioFacilityResource sfr')
              ->innerJoin('sfr.agFacilityResourceAllocationStatus fras')
              ->where('sfr.scenario_facility_group_id = ?', $this->groupId)
              ->execute(array(), Doctrine_Core::HYDRATE_SCALAR);

      //make the allocated array three dimensional, with the top key set to status.
      foreach ($this->selectStatuses as $selectStatus) {
        foreach ($allocatedFacilityResources as $afr) {
          if ($afr['fras_id'] == $selectStatus['fras_id']) {
            $this->allocatedFacilityResources[$selectStatus['fras_facility_resource_allocation_status']][] = $afr;
          }
        }
        // Create an empty array under the $selectStatus key so there's no missing index warning on render.
        if(!isset($this->allocatedFacilityResources[$selectStatus['fras_facility_resource_allocation_status']])) {
          $this->allocatedFacilityResources[$selectStatus['fras_facility_resource_allocation_status']] = array();
        }
      }
    } else {
//NEW
      $this->groupform = new agScenarioFacilityGroupForm();
      $this->groupform->setDefault('scenario_id', $request->getParameter('id'));
    }
    if ($request->isMethod(sfRequest::POST)) {
//DELETE
//      if ($request->getParameter('Delete')) {
//
//        $ag_scenario_facility_group = Doctrine_Core::getTable('agScenarioFacilityGroup')->find(array($request->getParameter('groupid')));
//
//        $fgroupRes = $ag_scenario_facility_group->getAgScenarioFacilityResource();
//
////get all facility resources associated with the facility group
//        foreach ($fgroupRes as $fRes) {
////get all shifts associated with each facility resource associated with the facility group
//          $fResShifts = $fRes->getAgScenarioShift();
//          foreach ($fResShifts as $resShift) {
//            $resShift->delete();
//          }
//          $fRes->delete();
//        }
//        $ag_scenario_facility_group->delete();
//
//        $this->redirect('scenario/fgroup?id=' . $request->getParameter('id'));
//      }

//SAVE
      $this->groupform->bind($request->getParameter($this->groupform->getName()), $request->getFiles($this->groupform->getName()));
      $params = $request->getParameter('ag_scenario_facility_group');
      $facilityResources = json_decode($params['values'], true);
      
      if ($this->groupform->isValid()) {
        $agScenarioFacilityGroup = $this->groupform->save();          // The Group object has been created here.
        foreach($facilityResources as $facilityResource) {
          $incomingFrIds[] = $facilityResource['frId'];
        }
        if(isset($allocatedFacilityResources)) {
          foreach($allocatedFacilityResources as $allocatedFacilityResource) {
            $preSaveFrIds[$allocatedFacilityResource['fr_id']] = $allocatedFacilityResource['sfr_id'];
          }
        }
        // Find existing scen fac resources and update them. Or make new ones and populate.
        foreach($facilityResources as $facilityResource) {
          if(in_array($facilityResource['frId'], array_keys($preSaveFrIds))) {
            $scenarioFacRes = agDoctrineQuery::create()
              ->select()
                ->from('agScenarioFacilityResource')
                ->where('facility_resource_id = ?', $facilityResource['frId'])
                ->fetchOne();
            // Knock off the fac res id. Those that are left will be used for a delete query.
            unset($preSaveFrIds[$facilityResource['frId']]);
          } else {
            $scenarioFacRes = new agScenarioFacilityResource();
            $scenarioFacRes['facility_resource_id'] = $facilityResource['frId'];
          }
          $scenarioFacRes['scenario_facility_group_id'] = $agScenarioFacilityGroup['id'];
          $scenarioFacRes['facility_resource_allocation_status_id'] = agDoctrineQuery::create()
                                                                       ->select('id')
                                                                       ->from('agFacilityResourceAllocationStatus')
                                                                       ->where('facility_resource_allocation_status = ?',$facilityResource['actStat'])
                                                                       ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
          $scenarioFacRes['activation_sequence'] = ($facilityResource['actSeq'] != null ? $facilityResource['actSeq']: 100);
          $scenarioFacRes->save();
        }
        if(count($preSaveFrIds > 0)) {
          foreach($preSaveFrIds as $deletable) {
//            deleteScenarioFacilityResource($deletable);
            agScenarioFacilityHelper::deleteScenarioFacilityResource($deletable);
            
          }
        }

        LuceneRecord::updateLuceneRecord($agScenarioFacilityGroup);

        if ($request->hasParameter('Continue')) {

          $this->redirect('scenario/staffresources?id=' . $this->scenario_id);
        } elseif ($request->hasParameter('Another')) {
          $this->redirect('scenario/fgroup?id=' . $this->scenario_id);
        } elseif ($request->hasParameter('AssignAll')) {
          $groups = Doctrine::getTable('agScenarioFacilityGroup')
                  ->findByDql('scenario_id = ?', $this->scenario_id)
                  ->getData();
          $this->redirect('scenario/staffpool?id=' . $this->scenario_id);
        } elseif ($request->hasParameter('groupid')) {
//if this is an existing facility group we are editing.
          $agScenarioFacilityGroup = Doctrine_Core::getTable('agScenarioFacilityGroup')->find(array($request->getParameter('groupid')));
          $this->groupform = new agScenarioFacilityGroupForm($agScenarioFacilityGroup);
          $this->redirect('scenario/fgroup?id=' . $this->scenario_id); //redirect the user to edit the facilitygroup
        } else {
          $this->redirect('scenario/meta?id=' . $this->scenario_id);
//save and bring back to scenario edit page? or should goto review page.
        }
      }
    }

//p-code
    $this->getResponse()->setTitle('Sahana Agasti Edit ' . $this->scenarioName . ' Scenario');
//end p-code
  }

  /**
   *
   * @param sfWebRequest $request
   */
  public function executeOldShifttemplates(sfWebRequest $request)
  {
    $this->setScenarioBasics($request);

    $facility_staff_resources = agDoctrineQuery::create()
            ->select('fsr.staff_resource_type_id, fr.facility_resource_type_id') // we want distinct
//->from('agShiftTemplate st, agFacilityStaffResource fsr')
            ->from('agFacilityStaffResource fsr')
//joined to the facility groups in this scenario
            ->leftJoin('agScenarioFacilityResource sfr, sfr.agFacilityResource fr, sfr.agScenarioFacilityGroup sfg')
//->innerJoin('st.scenario_id = sfg.scenario_id')
//->leftJoin('agScenarioFacilityGroup sfg, sfg.agScenarioFacilityResource sfr, sfr.agFacilityResource fr')
//scenario facility resource id
//where facility staff resource .staff resource type =
            ->where('sfg.scenario_id = ?', $this->scenario_id)
//->andWhere('st.scenario_id = ' $this->scenario_id) //this makes a fun cartesian product
            ->distinct()  //need to be keyed by the possibly existing shift template record..
            ->execute(array(), Doctrine_Core::HYDRATE_SCALAR); //if these items were keyed better, in the shift template form step(next) we could remove existing templates by that key
    $this->shifttemplateform = new agShiftGeneratorForm($facility_staff_resources, $this->scenario_id); //sfForm(); //agShiftGeneratorContainerForm ??
//for shift template workflow,
//get current facility_staff_resource,
//get the facility resource type ids and staff_resource_type

    if ($request->isMethod(sfRequest::POST)) {
      $this->shifttemplateform->bind($request->getParameter($this->shifttemplateform->getName()), $request->getFiles($this->shifttemplateform->getName()));
      if ($this->shifttemplateform->isValid()) {
        $ag_shift_template = $this->shifttemplateform->saveEmbeddedForms();
        if ($request->hasParameter('Continue')) {
          $generatedResult = agScenarioGenerator::shiftGenerator();
//should be a try/catch here
          $this->redirect('scenario/shifts?id=' . $request->getParameter('id'));
        } else {
          $this->redirect('scenario/shifttemplates?id=' . $request->getParameter('id'));
        }
      }
    }

//p-code
    $this->getResponse()->setTitle('Sahana Agasti Edit ' . $this->scenarioName . ' Scenario');
//end p-code
  }


  	public function executeAddshifttemplate($request)
	{
        $this->forward404unless($request->isXmlHttpRequest());
        $number = intval($request->getParameter("num"));
        $shiftTemplate = new agShiftTemplate();
        $shiftTemplateForm = new agSingleShiftTemplateForm($request->getParameter('id'), $shiftTemplate);
        $shiftTemplateForm->getWidgetSchema()->setNameFormat('shift_template[' . $number . '][%s]');
        //$shiftTemplateForm->getWidgetSchema()->setIdFormat($number . '%s');
        unset($shiftTemplateForm['_csrf_token']);
        return $this->renderPartial('newshifttemplateform', array('shifttemplateform' => $shiftTemplateForm, 'number' => $number
                                                  )
                                   );

	}


  /**
   * @method executeScenarioshifts()
   * Generates a new scenario shift form
   * @param sfWebRequest $request
   */
  public function executeShifttemplates(sfWebRequest $request)
  {
    $this->setScenarioBasics($request);
    $this->wizardHandler($request,6);

    $facility_staff_resources = agDoctrineQuery::create()
            ->select('fsr.staff_resource_type_id, fr.facility_resource_type_id') // we want distinct
            ->from('agFacilityStaffResource fsr')
            ->leftJoin('agScenarioFacilityResource sfr, sfr.agFacilityResource fr, sfr.agScenarioFacilityGroup sfg')
            ->where('sfg.scenario_id = ?', $this->scenario_id)
            ->distinct()  //need to be keyed by the possibly existing shift template record..
            ->execute(array(), Doctrine_Core::HYDRATE_SCALAR); //if these items were keyed better, in the shift template form step(next) we could remove existing templates by that key
    $this->shifttemplateforms =  new agShiftTemplateContainerForm($this->scenario_id); //$object, $options, $CSRFSecret) ShiftGeneratorForm($facility_staff_resources, $this->scenario_id); //sfForm(); //agShiftGeneratorContainerForm ??
    unset($this->shifttemplateforms['_csrf_token']);
    if ($request->isMethod(sfRequest::POST)) {
      //foreach $this->shifttemplateforms...
      $this->shifttemplateforms->bind($request->getParameter('shift_template'), $request->getFiles($this->shifttemplateforms->getName()));
      if ($this->shifttemplateforms->isValid()) {
        $ag_shift_template = $this->shifttemplateforms->saveEmbeddedForms();
        if ($request->hasParameter('Continue')) {
          $generatedResult = agScenarioGenerator::shiftGenerator();
//should be a try/catch here
          $this->redirect('scenario/shifts?id=' . $request->getParameter('id'));
        } else {
          $this->redirect('scenario/shifttemplates?id=' . $request->getParameter('id'));
        }
      }
    }
    $this->getResponse()->setTitle('Sahana Agasti Edit ' . $this->scenarioName . ' Scenario');
  }
/**
 * CRUDL for shifts (create update delete and list)
 * @param sfWebRequest $request
 */
  public function executeShifts(sfWebRequest $request)
  {
//CREATE  / UPDATE
    $this->setScenarioBasics($request);
    $this->wizardHandler($request,7);
    if ($request->isMethod(sfRequest::POST)) {

      if ($request->getParameter('shiftid') && $request->getParameter('shiftid') == 'new') {

        $this->scenarioshiftform = new agScenarioShiftForm();
      } elseif ($request->getParameter('shiftid') && is_numeric($request->getParameter('shiftid'))) {
        $ag_scenario_shift = Doctrine_Core::getTable('agScenarioShift')
                ->findByDql('id = ?', $request->getParameter('shiftid'))
                ->getFirst();
        $this->scenarioshiftform = new agScenarioShiftForm($ag_scenario_shift);
      } elseif ($request->getParameter('delete')) {
//DELETE
      }
      $this->scenarioshiftform->bind($request->getParameter($this->scenarioshiftform->getName()), $request->getFiles($this->scenarioshiftform->getName()));
//$formvalues = $request->getParameter($this->scenarioshiftform->getName());
      if ($this->scenarioshiftform->isValid()) { //form is not passing validation because the bind is failing?
        $ag_scenario_shift = $this->scenarioshiftform->save();
        $this->generateUrl('scenario_shifts', array('module' => 'scenario',
          'action' => 'shifts', 'id' => $this->scenario_id, 'shiftid' => $ag_scenario_shift->getId()));
//       $this->redirect('scenario/shifts?id=' . $this->scenario_id . '&shiftid=' . $ag_scenario_shift->getId());
      }
      $this->redirect('scenario/shifts?id=' . $this->scenario_id); //need to pass in scenario id
    } else {
//READ
      if ($request->getParameter('shiftid') == 'new') {
        $this->scenarioshiftform = new agScenarioShiftForm();
        $this->setTemplate('editshift');
      } elseif ($request->getParameter('shiftid') && is_numeric($request->getParameter('shiftid'))) {

        $ag_scenario_shift = Doctrine_Core::getTable('agScenarioShift')
                ->findByDql('id = ?', $request->getParameter('shiftid'))
                ->getFirst();

        $this->scenarioshiftform = new agScenarioShiftForm($ag_scenario_shift);
        $this->setTemplate('editshift');
      } else {
//LIST
        $query = agDoctrineQuery::create()
                ->select('ss.*, s.id, s.scenario, sfg.id, sfg.scenario_facility_group, sfr.id')
                ->from('agScenarioShift as ss')
                ->leftJoin('ss.agScenarioFacilityResource AS sfr')
                ->leftJoin('sfr.agScenarioFacilityGroup AS sfg')
                ->leftJoin('sfg.agScenario AS s')
                ->where('s.id = ?', $this->scenario_id);

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
    }

//p-code
    $this->getResponse()->setTitle('Sahana Agasti Edit ' . $this->scenario_name . ' Scenario');
//end p-code
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

    $query = agDoctrineQuery::create()
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
   * Provides the edit group type form to the browser
   * @param sfWebRequest $request is the web request
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
   * processing the update of a facility group type
   * @param sfWebRequest $request
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
   *
   * @param sfWebRequest $request
   * processing the deletion of a scenario
   */
  public function executeDelete(sfWebRequest $request)
  {
    $request->checkCSRFProtection();

    $this->forward404Unless($ag_scenario = Doctrine_Core::getTable('agScenario')->find(array($request->getParameter('id'))), sprintf('Object ag_scenario does not exist (%s).', $request->getParameter('id')));

    $shiftTemplates = $ag_scenario->getAgShiftTemplate();
//get all scenario shift templates
    foreach ($shiftTemplates as $shiftTemplate) {
      $shiftTemplate->delete();
    }

    $scenarioStaffGenerators = $ag_scenario->getAgScenarioStaffGenerator();
//get all scenario staff generator records (staff pool definitions) associated with the scenario
    foreach ($scenarioStaffGenerators as $scenarioStaffGenerator) {
      $scenarioStaffGenerator->delete();
    }

    $scenarioStaffResources = $ag_scenario->getAgScenarioStaffResource();
//get all scenario staff resources
    foreach ($scenarioStaffResources as $scenarioStaffResource) {
      $scenarioStaffResource->delete();
    }

    $scenarioStaffResources = $ag_scenario->getAgScenarioStaffResource();
//get all scenario staff resources
    foreach ($scenarioStaffResources as $scenarioStaffResource) {
      $scenarioStaffResource->delete();
    }

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
        $this->ag_facility_resources = agDoctrineQuery::create()
                ->select('a.facility_id, af.*, afrt.*')
                ->from('agFacilityResource a, a.agFacility af, a.agFacilityResourceType afrt')
                ->execute();
        $this->groupform = new agScenarioFacilityGroupForm();
//        $this->setTemplate('scenario/newgroup');
        $this->redirect('scenario/fgroup?id=' . $ag_scenario->getId());
      } else {
        $this->redirect('scenario/meta?id=' . $ag_scenario->getId());
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

  public function processShifttemplateform(sfWebRequest $request, sfForm $shiftTemplateForm)
  {
    $shiftTemplateForm->bind($request->getParameter($shiftTemplateForm->getName()), $request->getFiles($shiftTemplateForm->getName()));
    if ($shiftTemplateForm->isValid()) {
      $ag_shift_template = $this->shiftGeneratorForm->saveEmbeddedForms();
      $this->redirect('scenario/newshifttemplates?id=' . $request->getParameter('id'));
    }
  }

  public function executeGeneratescenarioshift()
  {
    $generatedResult = agScenarioGenerator::shiftGenerator();
    $this->redirect('scenario/scenarioshiftlist');
  }

}
