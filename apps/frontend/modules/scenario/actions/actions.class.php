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
    if (!isset($wizardOp['step']))
      $wizardOp['step'] = 1;
    if ($step != null)
      $wizardOp['step'] = $step;
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
    $this->wizardHandler($request, 3);
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
      $query = $query->orderBy($request->getParameter('sort', 'scenario') . ' ' . $request->getParameter('order',
                                                                                                         'ASC'));
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
    $formsArray = array();
    $this->setScenarioBasics($request);
    $this->wizardHandler($request, 4);
    //the above should not fail.
    $this->scenario = Doctrine::getTable('agScenario')
        ->findByDql('id = ?', $this->scenario_id)
        ->getFirst();
    $this->ag_scenario_facility_group = Doctrine_Core::getTable('agScenarioFacilityGroup')
        ->find(array($request->getParameter('id')));
    $this->ag_staff_resources = agDoctrineQuery::create()
        ->select('agSFR.*')
        ->from('agScenarioFacilityResource agSFR')
        ->where('scenario_facility_group_id = ?', $request->getParameter('id'))
        ->execute();

    if ($request->isMethod(sfRequest::POST)) {
      $facilityGroups = $request->getParameter('staff_resource');
      unset($facilityGroups['_csrf_token']);/** @todo unsetting csrf token should be fixed. */
//continuing workflow?
      if ($this->ag_scenario_facility_group) {
        foreach ($facilityGroups as $facilityGroup) {
          foreach ($facilityGroup as $facility) {
//are we editing or updating?
            foreach ($facility as $facilityStaffResource) {
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
              /**
               * @todo clean up for possible dirty data
               * 
               */
              if ($facilityStaffResourceForm->isValid() && isset($facilityStaffResource['minimum_staff']) && isset($facilityStaffResource['maximum_staff'])) {
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
        $this->redirect('scenario/staffpool?id=' . $this->scenario_id);
      } else {
        $this->redirect('scenario/staffresources?id=' . $this->scenario_id);
      }
//READ/LIST Present forms
    } else {
// Query to get all staff resource types.
      $dsrt = agScenarioResourceHelper::returnDefaultStaffResourceTypes($this->scenario_id);
      if (count($dsrt) > 0) {
        $this->staffResourceTypes = $dsrt;
      } else {
        $this->staffResourceTypes =
            agDoctrineQuery::create()
            ->select('srt.id, srt.staff_resource_type')
            ->from('agStaffResourceType srt')
            ->execute(array(), Doctrine_Core::HYDRATE_SCALAR);
      }
      $groups = Doctrine::getTable('agScenarioFacilityGroup')
          ->findByDql('scenario_id = ?', $this->scenario_id)
          ->getData();

      foreach ($groups as $scenarioFacilityGroup) {
        $facilitygroups[] = $scenarioFacilityGroup;
      }
      $this->scenarioFacilityGroup = $facilitygroups;

      foreach ($this->scenarioFacilityGroup as $group) {
        foreach ($group->getAgScenarioFacilityResource() as $scenarioFacilityResource) {
          foreach ($this->staffResourceTypes as $srt) {
            $subKey = $group['scenario_facility_group'];
            $subSubKey = $scenarioFacilityResource
                    ->getAgFacilityResource()
                    ->getAgFacility()->facility_name;


//this existing check should be refactored to be more efficient
            $existing = agDoctrineQuery::create()
                ->select('agFSR.*')
                ->from('agFacilityStaffResource agFSR')
                ->where('agFSR.staff_resource_type_id = ?', $srt['srt_id'])
                ->andWhere('agFSR.scenario_facility_resource_id = ?', $scenarioFacilityResource->id)
                ->fetchOne();

            if ($existing) {
              $formsArray[$subKey][$subSubKey][$srt['srt_staff_resource_type']] =
                  new agEmbeddedAgFacilityStaffResourceForm($existing);
            } else {
              $formsArray[$subKey][$subSubKey][$srt['srt_staff_resource_type']] =
                  new agEmbeddedAgFacilityStaffResourceForm();
            }
            $facilityLabels[$subKey][$subSubKey] = $scenarioFacilityResource
                    ->getAgFacilityResource()
                    ->getAgFacility()->facility_name .
                ': ' . ucwords($scenarioFacilityResource
                        ->getAgFacilityResource()
                        ->getAgFacilityResourceType()->facility_resource_type) .
                ' (' . $scenarioFacilityResource
                    ->getAgFacilityResource()
                    ->getAgFacility()->facility_code . ')';
            $formsArray[$subKey][$subSubKey][$srt['srt_staff_resource_type']]->setDefault('scenario_facility_resource_id',
                                                                                          $scenarioFacilityResource->getId());
            $formsArray[$subKey][$subSubKey][$srt['srt_staff_resource_type']]->setDefault('staff_resource_type_id',
                                                                                          $srt['srt_id']);
            //$formsArray[$subKey]->setLabel($subSubKey, $subSubKeyLabel);
          }
        }
      }

      $this->formsArray = $formsArray;
    }
    $this->facilityStaffResourceContainer = new agFacilityStaffResourceContainerForm($formsArray,
            $facilityLabels);

    $this->getResponse()->setTitle('Sahana Agasti Edit ' . $this->scenario['scenario'] . ' Scenario');
  }

  /*   * ***********************************************************************************************
   * This function will handle export facilities. It takes a scenario id as a param.
   * *********************************************************************************************** */

  public function executeFacilityexport(sfWebRequest $request)
  {
    $this->setScenarioBasics($request);

    $scenarioId = $request->getParameter('id');

    $facilityExporter = new agFacilityExporter($scenarioId);
    $exportResponse = $facilityExporter->export();
    // Free up some memory by getting rid of the agFacilityExporter object.
    unset($facilityExporter);
    $this->getResponse()->setHttpHeader('Content-Type', 'application/vnd.ms-excel');
    $this->getResponse()->setHttpHeader('Content-Disposition', 'attachment;filename="' . $exportResponse['fileName'] . '"');

    $exportFile = file_get_contents($exportResponse['filePath']);

    $this->getResponse()->setContent($exportFile);
    $this->getResponse()->send();
    unlink($exportResponse['filePath']);

    $this->redirect('facility/index');
  }

  /*   * ***********************************************************************************************
   * This function will handle incoming facilities. Scenario ID comes in as a param.
   * setScenarioBasics() will use the $request and the ID contained within.
   * *********************************************************************************************** */

  public function executeFacilityimport(sfWebRequest $request)
  {
    $this->setScenarioBasics($request);
    $this->returnPage = $this->getUser()->getAttribute('returnPage');
    $this->getUser()->getAttributeHolder()->remove('returnPage');

    $this->forward404Unless($scenarioId = $this->scenario_id);
    $this->form = new agImportForm();

    $uploadedFile = $_FILES['import'];
    
    // Create import object
    $import = new agFacilityImportXLS();

    //TODO: get import data directory root info from global param
    $importDataRoot = sfConfig::get('sf_upload_dir');
    $importDir = $importDataRoot . DIRECTORY_SEPARATOR . $this->moduleName;
    if (!file_exists($importDir)) {
      mkdir($importDir);
    }
    $importPath = $importDir . DIRECTORY_SEPARATOR . 'import.xls' /* $uploadedFile['name'] */;
    
    if (!move_uploaded_file($uploadedFile['tmp_name'], $importPath)) {
      return sfView::ERROR;
    }

    $this->importPath = $importPath;
    
    $this->timer = time();
    $processedToTemp = $import->processImport($this->importPath);
    $this->timer = (time() - $this->timer);

    $this->numRecordsImported = $import->numRecordsImported;
    $this->events = $import->events;

    // Normalizes imported temp data only if import is successful.
    if ($processedToTemp) {
      // Grab table name from import class.
      $sourceTable = $import->tempTable;

      $this->importer = new agFacilityImportNormalization($this->scenario_id, $sourceTable, 'facility');
      //TODO: $this->dispatcher->notify(new sfEvent($this, 'import.start'));

      $this->timer = time();
      $this->importer->normalizeImport();

      $this->summary = $this->importer->summary;
      
    }

    $this->dispatcher->notify(new sfEvent($this, 'import.do_reindex'));
    $this->timer = (time() - $this->timer);
  }

  /**
   * calls index template
   * @param sfWebRequest $request
   */
  public function executeIndex(sfWebRequest $request)
  {
    $inputs = array('scenario_id' => new sfWidgetFormDoctrineChoice(array('model' => 'agScenario', 'label' => 'scenario', 'add_empty' => true)),
    );
    //set up inputs for form
    $this->filterForm = new sfForm();
    foreach ($inputs as $key => $input) {
      $input->setAttribute('class', 'filter');
      $this->filterForm->setWidget($key, $input);
    }
  }

  /**
   * calls up the review confirmation at end of the Scenario Creator
   * @param sfWebRequest $request
   */
  public function executeReview(sfWebRequest $request)
  {
    $this->getUser()->setAttribute('returnPage', 'scenarioReview');

    if ($this->scenario_id = $request->getParameter('id')) {
      if ($request->getCookie('wizardOp')) {
        $this->setScenarioBasics($request);
        $this->wizardHandler($request, 8);
      }

      $this->scenario_name = Doctrine_Core::getTable('agScenario')->find($this->scenario_id)->getScenario();
      $this->scenario_description = Doctrine_Core::getTable('agScenario')->find($this->scenario_id)->getDescription();

      // default resource types
      $this->staffResourceTypeCt = agDoctrineQuery::create()
          ->select('COUNT(dssrt.id)')
          ->from('agDefaultScenarioStaffResourceType dssrt')
          ->where('dssrt.scenario_id = ?', $this->scenario_id)
          ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
      if (empty($this->staffResourceTypeCt)) {
        $this->staffResourceTypeCt = 0;
      }

      $this->facilityResourceTypeCt = agDoctrineQuery::create()
          ->select('COUNT(dsfrt.id)')
          ->from('agDefaultScenarioFacilityResourceType dsfrt')
          ->where('dsfrt.scenario_id = ?', $this->scenario_id)
          ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
      if (empty($this->facilityResourceTypeCt)) {
        $this->facilityResourceTypeCt = 0;
      }

      // Facility groups
      $this->facilityGroups = agDoctrineQuery::create()
          ->select('COUNT(sfg.id)')
          ->from('agScenarioFacilityGroup sfg')
          ->where('sfg.scenario_id = ?', $this->scenario_id)
          ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
      if (empty($this->facilityGroups)) {
        $this->facilityGroups = 0;
      }

      $this->facilities = agDoctrineQuery::create()
          ->select('COUNT(sfr.id)')
          ->from('agScenarioFacilityResource sfr')
          ->innerJoin('sfr.agScenarioFacilityGroup sfg')
          ->where('sfg.scenario_id = ?', $this->scenario_id)
          ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
      if (empty($this->facilities)) {
        $this->facilities = 0;
      }

      // resource requirements
      $this->completedResourceReqs = agDoctrineQuery::create()
          ->select('COUNT(fsr.id)')
          ->from('agFacilityStaffResource fsr')
          ->innerJoin('fsr.agScenarioFacilityResource sfr')
          ->innerJoin('sfr.agScenarioFacilityGroup sfg')
          ->where('sfg.scenario_id = ?', $this->scenario_id)
          ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
      if (empty($this->completedResourceReqs)) {
        $this->completedResourceReqs = 0;
      }

      // staff searches
      $this->staffSearches = agDoctrineQuery::create()
          ->select('COUNT(ssg.id)')
          ->from('agScenarioStaffGenerator ssg')
          ->where('ssg.scenario_id = ?', $this->scenario_id)
          ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
      if (empty($this->staffSearches)) {
        $this->staffSearches = 0;
      }
      // @todo make a method on search helper to return counts and report that here as well
      // shift templates
      $this->shiftTemplates = agDoctrineQuery::create()
          ->select('COUNT(st.id)')
          ->from('agShiftTemplate st')
          ->where('st.scenario_id = ?', $this->scenario_id)
          ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
      if (empty($this->shiftTemplates)) {
        $this->shiftTemplates = 0;
      }

      // shifts
      $shiftsData = agDoctrineQuery::create()
          ->select('COUNT(ss.id) AS ctShifts')
          ->addSelect('MIN(ss.minutes_start_to_facility_activation) AS minStart')
          ->addSelect('MAX((ss.minutes_start_to_facility_activation + ss.task_length_minutes
              + ss.break_length_minutes)) AS maxEnd')
          ->from('agScenarioShift ss')
          ->innerJoin('ss.agScenarioFacilityResource sfr')
          ->innerJoin('sfr.agScenarioFacilityGroup sfg')
          ->where('sfg.scenario_id = ?', $this->scenario_id)
          ->execute(array(), Doctrine_Core::HYDRATE_SCALAR);

      if (array_key_exists(0, $shiftsData)) {
        $this->shifts = $shiftsData[0]['ss_ctShifts'];
        $this->operationTime = agDateTimeHelper::minsToComponentsStr(($shiftsData[0]['ss_maxEnd']
                - $shiftsData[0]['ss_minStart']), TRUE);
      } else {
        $this->shifts = 0;
        $this->operationTime = 0;
      }
    }
//p-code
    $this->getResponse()->setTitle('Sahana Agasti Review ' . $this->scenario_name . ' Scenario');
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
    $this->wizardHandler($request, 5);
    $this->scenario_staff_count = Doctrine_Core::getTable('agScenarioStaffResource')
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

//EDIT
    if ($request->getParameter('search_id') && !($request->getParameter('Delete'))) {
      $this->search_id = $request->getParameter('search_id');
      $this->poolform = new agStaffPoolForm($this->search_id);
      $search_condition = json_decode(
          $this->poolform->getEmbeddedForm('search')->getObject()->search_condition, true);
    } elseif ($request->getParameter('Preview')) {
      $postParam = $request->getPostParameter('staff_pool');
      $search = $postParam['search'];
      $search_condition = json_decode($search['search_condition'], true);
      $staff_generator = $postParam['staff_generator'];
      $values = array('sg_values' =>
        array('search_weight' => $staff_generator['search_weight']),
        's_values' =>
        array('search_name' => $search['search_name'],
          'search_type_id' => $search['search_type_id']));
      $this->poolform = new agStaffPoolForm(null, $values); //construct our pool form with POST data
    } else {
      $this->poolform = new agStaffPoolForm();
    }
    $this->filterForm = new agStaffPoolFilterForm($this->scenario_id);

    if (isset($search_condition)) {
      foreach ($search_condition as $querypart) {
        //these search definitions should be stored in 'search type' table maybe?
        if ($querypart['field'] == 'agStaffResourceType.staff_resource_type') {
          $defaultValue = agDoctrineQuery::create()->select('id')->from('agStaffResourceType')
                  ->where('staff_resource_type=?', $querypart['condition'])->execute(array(),
                                                                                     'single_value_array');
        } else {
          $defaultValue = agDoctrineQuery::create()->select('id')->from('agOrganization')
                  ->where('organization=?', $querypart['condition'])->execute(array(),
                                                                              'single_value_array');
        }
        $this->filterForm->setDefault($querypart['field'], $defaultValue[0]);
      }
    }


    if ($request->getParameter('Delete')) {
//DELETE
      $ag_staff_gen = Doctrine_Core::getTable('agScenarioStaffGenerator')->find(array($request->getParameter('search_id'))); //maybe we should do a forward404unless, although no post should come otherwise
      $searchQuery = $ag_staff_gen->getAgSearch();
      //get the related lucene search
      $ag_staff_gen->delete();
      $searchQuery->delete();
      $this->redirect('scenario/staffpool?id=' . $request->getParameter('id'));
    }
//PREVIEW    
    elseif ($request->getParameter('Preview') ||
        $request->getParameter('search_id')
        && !($request->getParameter('Continue'))) {

      $search_id = null;

      $staff_ids = agStaffGeneratorHelper::executeStaffPreview($search_condition);

      $resultArray = agListHelper::getStaffList($staff_ids);
      $this->limit = null;
      $this->status = 'active';
      $this->pager = new agArrayPager(null, 10);
      $this->pager->setResultArray($resultArray);
      $this->pager->setPage($this->getRequestParameter('page', 1));
      $this->pager->init();
    }
//SAVE
    elseif ($request->getParameter('Save') || $request->getParameter('Continue')) { //otherwise, we're SAVING/UPDATING
      if ($request->getParameter('search_id')) {
        $this->search_id = $request->getParameter('search_id');
        $this->poolform = new agStaffPoolForm($this->search_id); //make sfForm with search_id
      } else {
        $this->poolform = new agStaffPoolForm();
      }

      $this->poolform->scenario_id = $request->getParameter('id');
      $this->poolform->bind($request->getParameter($this->poolform->getName()),
                                                   $request->getFiles($this->poolform->getName()));

      if ($this->poolform->isValid()) {
        $ag_staff_pool = $this->poolform->saveEmbeddedForms();
      }
      agStaffGeneratorHelper::generateStaffPool($this->scenario_id);
      if ($request->getParameter('Continue')) {
        $this->redirect('scenario/shifttemplates?id=' . $request->getParameter('id'));
      } else {
        $this->redirect('scenario/staffpool?id=' . $request->getParameter('id'));
      }
    }
    $this->getResponse()->setTitle('Sahana Agasti Edit ' . $this->scenarioName . ' Scenario Staff Pool');
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
    $this->wizardHandler($request, 1);
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
      $this->form->bind($request->getParameter($this->form->getName()),
                                               $request->getFiles($this->form->getName()));
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
    $this->wizardHandler($request, 2);
    
    $this->getUser()->setAttribute('returnPage', 'scenarioResourceTypes');

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
      if (isset($facilityParams['facility_resource_type_id'])) {
        foreach ($facilityParams['facility_resource_type_id'] as $key => $value) {
          $newRec = new agDefaultScenarioFacilityResourceType();

          $newRec['scenario_id'] = $this->scenario_id;
          $newRec['facility_resource_type_id'] = $value;

          $facilityDefaults->add($newRec);
          unset($facilityParams[$key]);
        }
      }
      if (isset($staffParams['staff_resource_type_id'])) {
        foreach ($staffParams['staff_resource_type_id'] as $key => $value) {
          $newRec = new agDefaultScenarioStaffResourceType();

          $newRec['scenario_id'] = $this->scenario_id;
          $newRec['staff_resource_type_id'] = $value;

          $staffDefaults->add($newRec);
          unset($staffParams[$key]);
        }
      }
      //$conn = Doctrine_Manager::connection();

      $staffDefaults->save();
      $facilityDefaults->save();
      //$conn->commit();
      if (!isset($staffParams['staff_resource_type_id']) || !isset($facilityParams['facility_resource_type_id'])) {
        $this->noDefaults = true;
        $this->resourceForm = new agDefaultResourceTypeForm($this->scenario_id);
      } else {
        if ($request->hasParameter('Continue')) {

          // count our facility groups and redirect to new fgroup or list appropriately
          $fgroupCt = agDoctrineQuery::create()
              ->select('count(sfg.id) AS sfg')
              ->from('agScenarioFacilityGroup sfg')
              ->where('sfg.scenario_id = ?', $this->scenario_id)
              ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);

          if ($fgroupCt < 1 || empty($fgroupCt)) {
            $this->redirect('scenario/fgroup?id=' . $this->scenario_id);
          } else {
            $this->redirect('scenario/listgroup?id=' . $this->scenario_id);
          }
        } else {
          $this->redirect('scenario/resourcetypes?id=' . $this->scenario_id);
        }
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
    $this->wizardHandler($request, 3);
    $this->scenarioFacilityGroups = Doctrine::getTable('agScenarioFacilityGroup')
        ->findByDql('scenario_id = ?', $this->scenario_id)
        ->getData();

    $this->allocatedFacilityResources = '';  //set this default incase none exist
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
// For facility group selection.
    $facilityGroupChoices = agDoctrineQuery::create()
        ->select('sfg.id, sfg.scenario_facility_group')
        ->from('agScenarioFacilityGroup sfg')
        ->where('sfg.scenario_id = ?', $this->scenario_id)
        ->orderBy('sfg.scenario_facility_group')
        ->execute(array(), 'key_value_pair');
    if (empty($facilityGroupChoices)) {
      $this->groupSelector = false;
    } else {
      $this->groupSelector = new sfForm();
      $this->groupSelector->setWidget('Change Facility Group:',
                                      new sfWidgetFormChoice(
              array('choices' => ($request->getParameter('groupid') ? $facilityGroupChoices : array(0 => null) + $facilityGroupChoices)),
              array('class' => 'inputGray')
      ));
      if ($request->getParameter('groupid')) {
        $this->groupSelector->getWidget('Change Facility Group:')->setDefault($request->getParameter('groupid'));
      }
    }

    if ($request->getParameter('groupid')) {
//EDIT
      $this->groupId = $request->getParameter('groupid');

      $agScenarioFacilityGroup = agDoctrineQuery::create()
          ->select()
          ->from('agScenarioFacilityGroup')
          ->where('id = ?', $this->groupId)
          ->fetchOne();

      $this->groupform = new agScenarioFacilityGroupForm($agScenarioFacilityGroup);
      $allocatedFacilityResources = $this->getAllocatedFacilityResources($this->groupId);
      $this->allocatedFacilityResources = $this->parseAllocatedFacilityResources($this->selectStatuses,
                                                                                 $allocatedFacilityResources);
    } else {
//NEW
      $this->groupform = new agScenarioFacilityGroupForm();
      $this->groupform->setDefault('scenario_id', $request->getParameter('id'));
    }
    if ($request->isMethod(sfRequest::POST)) {
//SAVE
      if ($request->hasParameter('Another') || $request->hasParameter('AssignAll')) {
//      if($request->hasParameter('saveJSON')) {
        $this->groupform->bind($request->getParameter($this->groupform->getName()),
                                                      $request->getFiles($this->groupform->getName()));
        $params = $request->getParameter('ag_scenario_facility_group');
        $facilityResources = json_decode($params['values'], true);

        if ($this->groupform->isValid()) {
          unset($this->groupform['ag_facility_resource_list']);
          $agScenarioFacilityGroup = $this->groupform->save();          // The Group object has been created here.
          foreach ($facilityResources as $facilityResource) {
            $incomingFrIds[] = $facilityResource['frId'];
          }
          $preSaveFrIds = array();
          if (isset($allocatedFacilityResources)) {
            foreach ($allocatedFacilityResources as $allocatedFacilityResource) {
              $preSaveFrIds[$allocatedFacilityResource['fr_id']] = $allocatedFacilityResource['sfr_id'];
            }
          }
          // Find existing scen fac resources and update them. Or make new ones and populate.
          foreach ($facilityResources as $facilityResource) {
            if (in_array($facilityResource['frId'], array_keys($preSaveFrIds))) {
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
                ->where('facility_resource_allocation_status = ?', $facilityResource['actStat'])
                ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
            $scenarioFacRes['activation_sequence'] = ($facilityResource['actSeq'] != null ? $facilityResource['actSeq'] : 100);
            $scenarioFacRes->save();
          }
          if (count($preSaveFrIds > 0)) {
            foreach ($preSaveFrIds as $deletable) {
              agScenarioFacilityHelper::deleteScenarioFacilityResource($deletable);
            }
          }

          LuceneRecord::updateLuceneRecord($agScenarioFacilityGroup);

          if ($request->hasParameter('Continue')) {
            $return = json_encode(array(
              'response' => url_for('scenario/staffresources?id=' . $this->scenario_id),
              'redirect' => true)
            );
            return $this->renderText($return);
//            $this->redirect('scenario/staffresources?id=' . $this->scenario_id);
          } elseif ($request->hasParameter('Another')) {
            $return = json_encode(array(
              'response' => url_for('scenario/fgroup?id=' . $this->scenario_id),
              'redirect' => true)
            );
            return $this->renderText($return);
//            $this->redirect('scenario/fgroup?id=' . $this->scenario_id);
          } elseif ($request->hasParameter('AssignAll')) {
//            $groups = Doctrine::getTable('agScenarioFacilityGroup')
//                    ->findByDql('scenario_id = ?', $this->scenario_id)
//                    ->getData();
            $return = json_encode(array(
              'response' => url_for('scenario/staffresources?id=' . $this->scenario_id),
              'redirect' => true)
            );
            return $this->renderText($return);
//            $this->redirect('scenario/staffresources?id=' . $this->scenario_id);
          } elseif ($request->hasParameter('groupid')) {
            //if this is an existing facility group we are editing.
            $agScenarioFacilityGroup = Doctrine_Core::getTable('agScenarioFacilityGroup')->find(array($request->getParameter('groupid')));
            $this->groupform = new agScenarioFacilityGroupForm($agScenarioFacilityGroup);
            $this->redirect('scenario/fgroup?id=' . $this->scenario_id); //redirect the user to edit the facilitygroup
          } else {
            $this->redirect('scenario/meta?id=' . $this->scenario_id);
            //save and bring back to scenario edit page? or should goto review page.
          }
        } else {
          if ($this->groupform->hasErrors()) {
            $errors = $this->groupform->getErrorSchema()->getErrors();
//            $errors = $this->groupform->getErrorSchema();
//            $b = $errors->getMessage();
            foreach ($errors as $error) {
              $message .= $error->getMessage() . PHP_EOL;
            }
          }
          $return = json_encode(array('response' => $message . 'Please see the field highlighted in red below.', 'redirect' => false));
          return $this->renderText($return);
        }
      }
// END SAVE
// LOAD NEW GROUP
      if ($request->hasParameter('change')) {
        return $this->renderPartial('groupform',
                                    array('facilityStatusForm' => $this->facilityStatusForm,
          'groupform' => $this->groupform,
          'availableFacilityResources' => $this->availableFacilityResources,
          'allocatedFacilityResources' => $this->parseAllocatedFacilityResources($this->selectStatuses,
                                                                                 $this->getAllocatedFacilityResources($this->groupId)
          ),
          'scenario_id' => $this->scenario_id,
          'selectStatuses' => $this->selectStatuses,
          'facilityResourceTypes' => $this->facilityResourceTypes));
      }
    }
// END LOAD NEW GROUP
//p-code
    $this->getResponse()->setTitle('Sahana Agasti Edit ' . $this->scenarioName . ' Scenario');
//end p-code
  }

  public function executeFacilityGroupDelete(sfWebRequest $request)
  {
    $agScenarioFacilityGroup = Doctrine_Core::getTable('agScenarioFacilityGroup')
        ->find(array($request->getParameter('groupId')));
    $scenarioId = $agScenarioFacilityGroup['scenario_id'];
    $scenarioFacilityResources = $agScenarioFacilityGroup->getAgScenarioFacilityResource();

    // Get all facility resources associated with the facility group
    foreach ($scenarioFacilityResources as $scenarioFacilityResource) {
      // Get all shifts associated with each facility resource associated with the facility group
      $scenarioShifts = $scenarioFacilityResource->getAgScenarioShift();
      foreach ($scenarioShifts as $scenarioShift) {
        $scenarioShifts->delete();
      }

      $facilityStaffResources = $scenarioFacilityResource->getAgFacilityStaffResource();
      foreach ($facilityStaffResources as $facilityStaffResource) {
        $facilityStaffResource->delete();
      }
      $scenarioFacilityResource->delete();
    }

    $scenarioFacilityDistributions = $agScenarioFacilityGroup->getAgScenarioFacilityDistribution();
    foreach ($scenarioFacilityDistributions as $scenarioFacilityDistribution) {
      $scenarioFacilityDistribution->delete();
    }
    $agScenarioFacilityGroup->delete();
    $this->redirect('scenario/listgroup?id=' . $scenarioId);
  }

  private function getAllocatedFacilityResources($groupId)
  {
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
        ->where('sfr.scenario_facility_group_id = ?', $groupId)
        ->execute(array(), Doctrine_Core::HYDRATE_SCALAR);
    return $allocatedFacilityResources;
  }

  /**
   * make the allocated array three dimensional, with the top key set to status.
   *
   * @param <type> $selectStatuses
   * @param <type> $allocatedFacilityResources
   * @return array
   * */
  private function parseAllocatedFacilityResources($selectStatuses, $allocatedFacilityResources)
  {

    foreach ($selectStatuses as $selectStatus) {
      foreach ($allocatedFacilityResources as $afr) {
        if ($afr['fras_id'] == $selectStatus['fras_id']) {
          $parsedAllocatedFacilityResources[$selectStatus['fras_facility_resource_allocation_status']][] = $afr;
        }
      }
      // Create an empty array under the $selectStatus key so there's no missing index warning on render.
      if (!isset($parsedAllocatedFacilityResources[$selectStatus['fras_facility_resource_allocation_status']])) {
        $parsedAllocatedFacilityResources[$selectStatus['fras_facility_resource_allocation_status']] = array();
      }
    }
    return $parsedAllocatedFacilityResources;
  }

  /**
   * deleteShiftTemplate AJAX called method
   * @param sfWebRequest $request
   * @return response success or failure string to ouput in feedback flasher.
   */
  public function executeDeleteshifttemplate(sfWebRequest $request)
  {
    $this->forward404unless($request->isXmlHttpRequest());

    $shiftTemplateId = $request->getParameter('stId');
    $shiftTemplate = Doctrine_Core::getTable('agShiftTemplate')
        ->findByDql('id = ?', $shiftTemplateId)
        ->getFirst();

    $scenShifts = Doctrine_Core::getTable('agScenarioShift')
        ->findByDql('originator_id = ?', $shiftTemplateId);

    $scenShifts->delete();

    $result = $shiftTemplate->delete();

    //we assume here that if no error was thrown the shift template and associated shifts
    //have been deleted
    return $this->renderText('Shift Template Deleted');
  }

  /**
   * addShiftTemplate AJAX called method to add a shift template
   * @param sfWebRequest $request
   * @return response render partial to show up a new form in the container form
   */
  public function executeAddshifttemplate(sfWebRequest $request)
  {
    $this->forward404unless($request->isXmlHttpRequest());
    $number = intval($request->getParameter('num'));
    $shiftTemplate = new agShiftTemplate();
    $shiftTemplateForm = new agSingleShiftTemplateForm($request->getParameter('id'), $shiftTemplate);
    $shiftTemplateForm->getWidgetSchema()->setNameFormat('shift_template[' . $number . '][%s]');
    $shiftTemplateForm->setDefault('task_length_minutes',
                                   agGlobal::getParam('default_shift_task_length'));
    $shiftTemplateForm->setDefault('break_length_minutes',
                                   agGlobal::getParam('default_shift_break_length'));
    $shiftTemplateForm->setDefault('minutes_start_to_facility_activation',
                                   agGlobal::getParam('default_shift_minutes_start_to_facility_activation'));
    $shiftTemplateForm->setDefault('days_in_operation',
                                   agGlobal::getParam('default_days_in_operation'));
    $shiftTemplateForm->setDefault('max_staff_repeat_shifts',
                                   agGlobal::getParam('default_shift_max_staff_repeat_shifts'));
    //$shiftTemplateForm->getWidgetSchema()->setIdFormat($number . '%s');
    unset($shiftTemplateForm['_csrf_token']);
    return $this->renderPartial('shifttemplateform',
                                array('shifttemplateform' => $shiftTemplateForm, 'number' => $number
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
    $this->wizardHandler($request, 6);
    $requiredResourceCombo = array();

    if ($request->isMethod(sfRequest::POST) and $request->hasParameter('Predefined'))
    {
      // Query for all predefined staff resource and faciltiy resource combinations.
      $requiredResourceCombo = agDoctrineQuery::create()
        ->select('fsr.staff_resource_type_id')
            ->addSelect('fr.facility_resource_type_id')
          ->from('agFacilityStaffResource AS fsr')
            ->innerJoin('fsr.agScenarioFacilityResource AS sfr')
            ->innerJoin('sfr.agFacilityResource AS fr')
            ->innerJoin('sfr.agScenarioFacilityGroup AS sfg')
          ->where('sfg.scenario_id = ?', $this->scenario_id)
          ->orderBy('fsr.staff_resource_type_id, fr.facility_resource_type_id')
       ->execute(array(), Doctrine_Core::HYDRATE_NONE);
    }
    $this->shifttemplateforms = new agShiftTemplateContainerForm($this->scenario_id,
                                                                 $requiredResourceCombo);
    unset($this->shifttemplateforms['_csrf_token']);
    if ($request->isMethod(sfRequest::POST))
    {
      //foreach $this->shifttemplateforms...
      $this->shifttemplateforms->bind($request->getParameter('shift_template'),
                                                             $request->getFiles($this->shifttemplateforms->getName()));
      if ($this->shifttemplateforms->isValid())
      {
        if ($request->hasParameter('Continue'))
        {
          $ag_shift_template = $this->shifttemplateforms->saveEmbeddedForms();
          $generatedResult = agShiftGeneratorHelper::shiftGenerator($this->scenario_id);
          //if this is a long process should be a try/catch here
          $this->redirect('scenario/shifts?id=' . $request->getParameter('id'));
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
    $this->wizardHandler($request, 7);
    if ($request->isMethod(sfRequest::POST)) {

      if ($request->getParameter('shiftid') && $request->getParameter('shiftid') == 'new') {

        $this->scenarioshiftform = new agScenarioShiftForm($this->scenario_id);
      } elseif ($request->getParameter('shiftid') && is_numeric($request->getParameter('shiftid'))) {
        $ag_scenario_shift = Doctrine_Core::getTable('agScenarioShift')
            ->findByDql('id = ?', $request->getParameter('shiftid'))
            ->getFirst();
        $this->scenarioshiftform = new agScenarioShiftForm($this->scenario_id, $ag_scenario_shift);
      }

      if ($request->getParameter('Save')) {

        //SAVE
        unset($this->scenarioshiftform['_csrf_token']);
        $this->scenarioshiftform->bind($request->getParameter('shift_template'),
                                                              $request->getFiles($this->scenarioshiftform->getName()));
//$formvalues = $request->getParameter($this->scenarioshiftform->getName());
        if ($this->scenarioshiftform->isValid()) { //form is not passing validation because the bind is failing?
          $ag_scenario_shift = $this->scenarioshiftform->save();
          $this->generateUrl('scenario_shifts',
                             array('module' => 'scenario',
            'action' => 'shifts', 'id' => $this->scenario_id, 'shiftid' => $ag_scenario_shift->getId()));
          $this->redirect('scenario/shifts?id=' . $this->scenario_id . '&shiftid=' . $ag_scenario_shift->getId());
        } else {
          throw new Doctrine_Exception('Invalid data caught, please go back and try again.');
        }
        //$this->redirect('scenario/shifts?id=' . $this->scenario_id); //need to pass in scenario id
      } elseif ($request->getParameter('Delete')) {
        $ag_scenario_shift = $this->scenarioshiftform->getObject();
        $ag_scenario_shift->delete();
        $this->redirect('scenario/shifts?id=' . $this->scenario_id);

        //DELETE
      }
    } else {
//READ
      if ($request->getParameter('shiftid') == 'new') {
        $this->scenarioshiftform = new agScenarioShiftForm($this->scenario_id);
        $this->setTemplate('editshift');
      } elseif ($request->getParameter('shiftid') && is_numeric($request->getParameter('shiftid'))) {

        $ag_scenario_shift = Doctrine_Core::getTable('agScenarioShift')
            ->findByDql('id = ?', $request->getParameter('shiftid'))
            ->getFirst();

        $this->scenarioshiftform = new agScenarioShiftForm($this->scenario_id, $ag_scenario_shift);
        $this->setTemplate('editshift');
      } else {
//LIST
        // shifts
        $shiftsData = agDoctrineQuery::create()
            ->select('COUNT(ss.id) AS ctShifts')
            ->addSelect('MIN(ss.minutes_start_to_facility_activation) AS minStart')
            ->addSelect('MAX((ss.minutes_start_to_facility_activation + ss.task_length_minutes
              + ss.break_length_minutes)) AS maxEnd')
            ->from('agScenarioShift ss')
            ->innerJoin('ss.agScenarioFacilityResource sfr')
            ->innerJoin('sfr.agScenarioFacilityGroup sfg')
            ->where('sfg.scenario_id = ?', $this->scenario_id)
            ->execute(array(), Doctrine_Core::HYDRATE_SCALAR);

        if (array_key_exists(0, $shiftsData)) {
          $this->shifts = $shiftsData[0]['ss_ctShifts'];
          $this->operationTime = agDateTimeHelper::minsToComponentsStr(($shiftsData[0]['ss_maxEnd']
                  - $shiftsData[0]['ss_minStart']), TRUE);
        } else {
          $this->shifts = 0;
          $this->operationTime = 0;
        }


        $query = agDoctrineQuery::create()
            ->select('ss.*, sfg.id, sfg.scenario_facility_group, sfr.id')
            ->from('agScenarioShift as ss')
            ->leftJoin('ss.agScenarioFacilityResource AS sfr')
            ->leftJoin('sfr.agScenarioFacilityGroup AS sfg')
            //->leftJoin('sfg.agScenario AS s')
            ->where('sfg.scenario_id = ?', $this->scenario_id);

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
   * Provides the edit group type form to the browser
   * @param sfWebRequest $request is the web request
   */
  public function executeEditgrouptype(sfWebRequest $request)
  {
    $this->forward404Unless($ag_facility_group_type = Doctrine_Core::getTable('agFacilityGroupType')->find(array($request->getParameter('id'))),
                                                                                                                                        sprintf('Object ag_facility_group_type does not exist (%s).',
                                                                                                                                                $request->getParameter('id')));
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
    $this->forward404Unless($ag_facility_group_type = Doctrine_Core::getTable('agFacilityGroupType')->find(array($request->getParameter('id'))),
                                                                                                                                        sprintf('Object ag_facility_group_type does not exist (%s).',
                                                                                                                                                $request->getParameter('id')));
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

    $this->forward404Unless($ag_scenario = Doctrine_Core::getTable('agScenario')->find(array($request->getParameter('id'))),
                                                                                                                    sprintf('Object ag_scenario does not exist (%s).',
                                                                                                                            $request->getParameter('id')));

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

    $this->forward404Unless($ag_facility_group_type = Doctrine_Core::getTable('agFacilityGroupType')->find(array($request->getParameter('id'))),
                                                                                                                                        sprintf('Object ag_facility_group_type does not exist (%s).',
                                                                                                                                                $request->getParameter('id')));
    $facilityGroupTypeId = $request->getParameter('id');
    $scenarioFacilityGroupByTypeCount = agDoctrineQuery::create()
                                        ->select('COUNT(g.id)')
                                        ->from('agScenarioFacilityGroup AS g')
                                        ->where('g.facility_group_type_id = ?', $facilityGroupTypeId)
                                        ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);

    $eventFacilityGroupByTypeCount = agDoctrineQuery::create()
                                     ->select('COUNT(g.id)')
                                     ->from('agEventFacilityGroup AS g')
                                     ->where('g.facility_group_type_id = ?', $facilityGroupTypeId)
                                     ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);

    if ( ($scenarioFacilityGroupByTypeCount == 0) && ($eventFacilityGroupByTypeCount == 0) )
    {
      $ag_facility_group_type->delete();

      $this->redirect('scenario/grouptype');
    }
    $this->facilityGroupType = Doctrine_Core::getTable('agFacilityGroupType')->find($facilityGroupTypeId)->getFacilityGroupType();
  }

  /**
   * executeDeleteshifttemplategroup()
   * @param sfWebRequest $request
   */
  public function executeDeleteshifttemplategroup(sfWebRequest $request)
  {
    $this->forward404Unless($shftTmpGrp = Doctrine_Core::getTable('agShiftTemplate')->createQuery('st')->where('st.scenario_id=?',
                                                                                                               $request->getParameter('id'))->execute(),
                                                                                                                                      sprintf('Object shift_template_group does not exist for scenario (%s).',
                                                                                                                                              $request->getParameter('id')));

//Delete all scenario shift relating to the scenario.
    $scenShftGrp = Doctrine_Core::getTable('agScenarioShift')->createQuery('ss')->innerJoin('ss.agScenarioFacilityResource AS sfr')->innerJoin('sfr.agScenarioFacilityGroup AS sfg')->where('sfg.scenario_id=?',
                                                                                                                                                                                            $request->getParameter('id'))->execute();
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
    $this->forward404Unless($scenShftGrp = Doctrine_Core::getTable('agScenarioShift')->createQuery('ss')->innerJoin('ss.agScenarioFacilityResource AS sfr')->innerJoin('sfr.agScenarioFacilityGroup AS sfg')->where('sfg.scenario_id=?',
                                                                                                                                                                                                                    $request->getParameter('scenId'))->execute(),
                                                                                                                                                                                                                                           sprintf('Object ag_scenario_shift_group does not exist for scenario (%s).',
                                                                                                                                                                                                                                                   $request->getParameter('scenId')));

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
    $grouptypeform->bind($request->getParameter($grouptypeform->getName()),
                                                $request->getFiles($grouptypeform->getName()));
    if ($grouptypeform->isValid()) {
      $ag_facility_group_type = $grouptypeform->save();

      $this->redirect('scenario/grouptype');
    }
  }

  public function processShifttemplateform(sfWebRequest $request, sfForm $shiftTemplateForm)
  {
    $shiftTemplateForm->bind($request->getParameter($shiftTemplateForm->getName()),
                                                    $request->getFiles($shiftTemplateForm->getName()));
    if ($shiftTemplateForm->isValid()) {
      $ag_shift_template = $this->shiftGeneratorForm->saveEmbeddedForms();
      $this->redirect('scenario/newshifttemplates?id=' . $request->getParameter('id'));
    }
  }

}
