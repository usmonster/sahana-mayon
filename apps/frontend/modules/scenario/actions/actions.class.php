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

  protected function wizardHandler(sfWebRequest $request)
  {

    $encodedWizard = $request->getCookie('wizardOp');
    $wizardOp = json_decode($encodedWizard, true);
    $wizardOp['scenario_id'] = $request->getParameter('id');
    if (!isset($wizardOp['step']))
      $wizardOp['step'] = 1;

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
    $this->ag_staff_resources = agDoctrineQuery::create()
            ->select('agSFR.*')
            ->from('agScenarioFacilityResource agSFR')
            ->where('scenario_facility_group_id = ?', $request->getParameter('id'))
            ->execute();
    $this->staffresourceform = new agStaffResourceRequirementForm();

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
//if ($request->getParameter('Continue')) {};
//were there any changes?
      if ($request->hasParameter('Continue')) {
        $this->redirect('scenario/shifttemplates?id=' . $request->getParameter('id'));
      } else {
        $this->redirect('scenario/staffresources?id=' . $request->getParameter('id'));
      }
    } else {

// Query to get all staff resource types.
      $this->staffResourceTypes = agDoctrineQuery::create()
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
              $existing = agDoctrineQuery::create()
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
              $staffResourceFormDeco = new agFormFormatterInlineLabels($formsArray[$subKey][$subSubKey][$srt->staff_resource_type]->getWidgetSchema());
              $formsArray[$subKey][$subSubKey][$srt->staff_resource_type]->getWidgetSchema()->addFormFormatter('staffResourceFormDeco', $staffResourceFormDeco);
              $formsArray[$subKey][$subSubKey][$srt->staff_resource_type]->getWidgetSchema()->setFormFormatterName('staffResourceFormDeco');
              $formsArray[$subKey][$subSubKey][$srt->staff_resource_type]->setDefault('scenario_facility_resource_id', $scenarioFacilityResource->getId());
              $formsArray[$subKey][$subSubKey][$srt->staff_resource_type]->setDefault('staff_resource_type_id', $srt->getId());
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

            $formsArray[$subKey][$subSubKey][$srt->staff_resource_type] =
                new agEmbeddedAgFacilityStaffResourceForm();

            $staffResourceFormDeco = new agFormFormatterInlineLabels($formsArray[$subKey][$subSubKey][$srt->staff_resource_type]->getWidgetSchema());
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
    $this->facilityStaffResourceContainer = new agFacilityStaffResourceContainerForm($formsArray);

    //p-code
    $this->getResponse()->setTitle('Sahana Agasti Edit ' . $this->scenario['scenario'] . ' Scenario');
    //end p-code
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
    $this->wizardHandler($request);
    $this->scenario_staff_count = Doctrine_Core::getTable('AgScenarioStaffResource')
            ->findby('scenario_id', $this->scenario_id)->count();
    $this->target_module = 'staff';

    $this->saved_searches = $existing = Doctrine_Core::getTable('AgScenarioStaffGenerator')
            ->findby('scenario_id', $this->scenario_id);
    //get all available staff
    $this->total_staff = Doctrine_Core::getTable('agStaff')->count();
    $this->total_resources = Doctrine_Core::getTable('agStaffResource')->count();
    $inputs = array(
      'type' => new sfWidgetFormDoctrineChoice(
          array(
            'model' => 'agStaffResourceType',
            'label' => 'Staff Type',
            'add_empty' => true)
      ),
      // 'class' => 'filter')),
      'org' => new sfWidgetFormDoctrineChoice(
          array(
            'model' => 'agOrganization',
            'method' => 'getOrganization',
            'label' => 'Staff Organization',
            'add_empty' => true
          )
      )
        //, 'class' => 'filter'))
    );
    //set up inputs for form
    $this->filterForm = new sfForm();
    foreach ($inputs as $key => $input) {
      $input->setAttribute('class', 'filter');
      $this->filterForm->setWidget($key, $input);
    }

    if ($request->getParameter('search_id')) {
      $this->search_id = $request->getParameter('search_id');
      $this->poolform = new agStaffPoolForm($this->search_id);

      //
      $query_condition = $this->poolform->getEmbeddedForm('lucene_search')->getObject()->query_condition;
      if ($query_condition != '%') {
        $queryparts = explode(" AND ", $query_condition);
        foreach ($queryparts as $querypart) {
          $filterType = preg_split("/:/", $querypart, 2);
          //these search definitions should be stored in 'search type' table maybe?
          if ($filterType[0] == 'staff_type') {
            $defaultValue = agDoctrineQuery::create()->select('id')->from('agStaffResourceType')
                    ->where('staff_resource_type=?', $filterType[1])->execute(array(), 'single_value_array');
          } else {
            $defaultValue = agDoctrineQuery::create()->select('id')->from('agOrganization')
                    ->where('organization=?', $filterType[1])->execute(array(), 'single_value_array');
          }
          $this->filterForm->setDefault($filterType[0], $defaultValue[0]);
        }
      }
    } else {
      $this->poolform = new agStaffPoolForm(); //this is redeclared below to construct the form with an
    }

    if ($request->isMethod(sfRequest::POST) || $request->getParameter('search_id')) {
      //$request->checkCSRFProtection();
//PREVIEW
      if ($request->getParameter('Preview') || $request->getParameter('search_id')) {

        $postParam = $request->getPostParameter('staff_pool');
        $staff_generator = $postParam['staff_generator'];
        $lucene_search = $postParam['lucene_search'];
        $lucene_query = $lucene_search['query_condition'];
#        $lucene_query = "\"Specialist ASPCA\"";
        #$lucene_query = 'staff_pool:GeneralistOther';
        $values = array('sg_values' =>
          array('search_weight' => $staff_generator['search_weight']),
          'ls_values' =>
          array('query_name' => $lucene_search['query_name'],
            'lucene_search_type_id' => $lucene_search['lucene_search_type_id'])
        );
        $this->poolform = new agStaffPoolForm(null, $values);
        $incomingFields = $this->filterForm->getWidgetSchema()->getFields();
        foreach ($incomingFields as $key => $incomingField) {
          $this->filterForm->setDefault($key, $request->getPostParameter($key)); //inccomingField->getName ?
        }

        if ($request->getParameter('search_id'))
          $lucene_query = $query_condition;
        $this->searchedModels = 'agStaff';

        parent::doSearch($lucene_query, FALSE); //eventually we should add a for each loop here to get ALL filters coming in and constructa a good search string
      } elseif ($request->getParameter('Delete')) {

        $ag_staff_gen = Doctrine_Core::getTable('agScenarioStaffGenerator')->find(array($request->getParameter('search_id'))); //maybe we should do a forward404unless, although no post should come otherwise
        $luceneQuery = $ag_staff_gen->getAgLuceneSearch();
//get the related lucene search
        $ag_staff_gen->delete();
        $luceneQuery->delete();
        $this->redirect('scenario/staffpool?id=' . $request->getParameter('id'));
      }
//SAVE
      elseif ($request->getParameter('Save')) { //otherwise, we're SAVING/UPDATING
        if ($request->getParameter('search_id')) {
          $this->search_id = $request->getParameter('search_id');
          $this->poolform = new agStaffPoolForm($this->search_id);
        } else {
          $this->poolform = new agStaffPoolForm();
        }

        $this->poolform->scenario_id = $request->getParameter('id');
        $this->poolform->bind($request->getParameter($this->poolform->getName()), $request->getFiles($this->poolform->getName()));

        if ($this->poolform->isValid()) {
          $ag_staff_pool = $this->poolform->saveEmbeddedForms();
          $postParam = $request->getPostParameter('staff_pool');
          $staff_generator = $postParam['staff_generator'];
          $lucene_search = $postParam['lucene_search'];
          $lucene_query = $lucene_search['query_condition'];

          $staff_resource_ids = agScenarioGenerator::staffPoolGenerator($lucene_query, $this->scenario_id);
          $addedStaff = agScenarioGenerator::saveStaffPool($staff_resource_ids, $this->scenario_id, $staff_generator['search_weight']);
          $this->redirect('scenario/staffpool?id=' . $request->getParameter('id'));
        }
      } else {  //or, just make a new form
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
   * sets up a new scenario form
   * @param sfWebRequest $request
   */
  public function executeNew(sfWebRequest $request)
  {
    $this->form = new agScenarioForm();
  }

  public function executeMeta(sfWebRequest $request)
  {
    $this->wizardHandler($request);
    if ($request->getParameter('id')) {
      $ag_scenario = Doctrine_Core::getTable('agScenario')->find(array($request->getParameter('id')));
      $this->form = new agScenarioForm($ag_scenario);
      $this->metaAction = 'Edit';
      $this->getResponse()->setTitle('Sahana Agasti Edit ' . $ag_scenario . ' Scenario');
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
          $this->redirect('scenario/fgroup?id=' . $ag_scenario->getId());
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
    $this->wizardHandler($request);
    $staffResourceTypeDefaults = null; //new agDoctrineQuery();
    $facilityResourceTypeDefaults = null; //new agDoctrineQuery();
    //we must have an id coming in if this is an existing scenario
    if ($request->getParameter('id')) {
      $this->resourceForm = new agDefaultResourceTypeForm($staffResourceTypeDefaults, $facilityResourceTypeDefaults);

      $this->getResponse()->setTitle('Scenario Creation Wizard - set Default Resource Types needed for ' . $this->scenarioName . ' Scenario');
    }

    if ($request->isMethod(sfRequest::POST)) {
      $this->form->bind($request->getParameter($this->form->getName()), $request->getFiles($this->form->getName()));
      if ($this->form->isValid()) {
        $ag_scenario = $this->form->save();
        $ag_scenario->updateLucene();
        if ($request->hasParameter('Continue')) {

          //do some stuff
//        $this->setTemplate('scenario/newgroup');
          $this->redirect('scenario/fgroup?id=' . $ag_scenario->getId());
        } else {
          $this->redirect('scenario/meta?id=' . $ag_scenario->getId());
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
    $this->scenarioFacilityGroups = Doctrine::getTable('agScenarioFacilityGroup')
            ->findByDql('scenario_id = ?', $this->scenario_id)
            ->getData();

    $this->ag_allocated_facility_resources = '';  //set this default incase none exist

    $this->ag_facility_resources = agDoctrineQuery::create()
            ->select('a.facility_id, af.*, afrt.*')
            ->from('agFacilityResource a, a.agFacility af, a.agFacilityResourceType afrt')
            ->execute();
// Testing//////////////////////////////////////////////////////////////////////////////////////////
    // Get the facility resource types available to this scenario.
    $this->facilityResourceTypes = agDoctrineQuery::create()
            ->select('frt.id, frt.facility_resource_type, facility_resource_type_abbr')
            ->from('agFacilityResourceType frt')
            ->innerJoin('frt.agDefaultScenarioFacilityResourceType dsfrt')
            ->where('dsfrt.scenario_id =?', $this->scenario_id)
            ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
    // Get all the facility resources available to this scenario (those that aren't already in use
    // in this scenario).
    $this->availableFacilityResources = agDoctrineQuery::create()
            ->select('fr.id')
              ->addSelect('f.facility_name')
              ->addSelect('f.facility_code')
              ->addSelect('frt.facility_resource_type')
            ->from('agFacilityResource fr')
              ->innerJoin('fr.agFacility f')
              ->innerJoin('fr.agFacilityResourceType frt')
            ->where('NOT EXISTS (
              SELECT s1.id
                FROM agFacilityResource s1
                  INNER JOIN s1.agScenarioFacilityResource s2
                  INNER JOIN s2.agScenarioFacilityGroup s3
              WHERE s3.scenario_id = ?
                AND s1.id = fr.id)',
                $this->scenario_id)
            ->execute(array(), Doctrine_Core::HYDRATE_SCALAR);
    $b = $this->availableFacilityResources;
// End testing//////////////////////////////////////////////////////////////////////////////////////
    if ($request->getParameter('groupid')) {
//EDIT
      $this->group_id = $request->getParameter('groupid');
      $ag_scenario_facility_group = agDoctrineQuery::create()
              ->select('a.*, afr.*, afgt.*, afrt.*, afgas.*, fr.*, af.*, s.*')
              ->from('agScenarioFacilityGroup a, a.agScenarioFacilityResource afr, a.agFacilityGroupType afgt, a.agFacilityGroupAllocationStatus afgas, afr.agFacilityResource fr, fr.agFacility af, a.agScenario s, fr.agFacilityResourceType afrt')
              ->where('a.id = ?', $request->getParameter('groupid'))
              ->fetchOne();

      $this->groupform = new agScenarioFacilityGroupForm($ag_scenario_facility_group);

      $current = $ag_scenario_facility_group->getAgScenarioFacilityResource();
      $current->setKeyColumn('activation_sequence');
      //index these by the activation sequence
      $currentoptions = array();
      foreach ($current as $curopt) {
        $currentoptions[$curopt->facility_resource_id] = $curopt->getAgFacilityResource()->getAgFacility()->facility_name . " : " . $curopt->getAgFacilityResource()->getAgFacilityResourceType()->facility_resource_type; //$curopt->getAgFacility()->facility_name . " : " . $curopt->getAgFacilityResourceType()->facility_resource_type;
      }

      $this->ag_allocated_facility_resources = agDoctrineQuery::create()
              ->select('a.facility_id, af.*, afrt.*')
              ->from('agFacilityResource a, a.agFacility af, a.agFacilityResourceType afrt')
              ->whereIn('a.id', array_keys($currentoptions))->execute();

      $this->ag_facility_resources = agDoctrineQuery::create()
              ->select('a.facility_id, af.*, afrt.*')
              ->from('agFacilityResource a, a.agFacility af, a.agFacilityResourceType afrt')
              ->whereNotIn('a.id', array_keys($currentoptions))->execute();
    } else {
//NEW
      $this->groupform = new agScenarioFacilityGroupForm();
      $this->groupform->setDefault('scenario_id', $request->getParameter('id'));
    }
    if ($request->isMethod(sfRequest::POST)) {
//DELETE
      if ($request->getParameter('Delete')) {

        $ag_scenario_facility_group = Doctrine_Core::getTable('agScenarioFacilityGroup')->find(array($request->getParameter('groupid')));

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

        $this->redirect('scenario/fgroup?id=' . $request->getParameter('id'));
      }

//SAVE
      $this->groupform->bind($request->getParameter($this->groupform->getName()), $request->getFiles($this->groupform->getName()));

      if ($this->groupform->isValid()) {
        $ag_scenario_facility_group = $this->groupform->save();          // The Group object has been created here.
        LuceneRecord::updateLuceneRecord($ag_scenario_facility_group);

        if ($request->hasParameter('Continue')) {

          $this->redirect('scenario/staffresources?id=' . $this->scenario_id);
        } elseif ($request->hasParameter('Another')) {
          $this->redirect('scenario/fgroup?id=' . $this->scenario_id);
        } elseif ($request->hasParameter('AssignAll')) {
          $groups = Doctrine::getTable('agScenarioFacilityGroup')
                  ->findByDql('scenario_id = ?', $this->scenario_id)
                  ->getData();
          $this->redirect('scenario/staffresources?id=' . $this->scenario_id);
        } elseif ($request->hasParameter('groupid')) {
          //if this is an existing facility group we are editing.
          $ag_scenario_facility_group = Doctrine_Core::getTable('agScenarioFacilityGroup')->find(array($request->getParameter('groupid')));
          $this->groupform = new agScenarioFacilityGroupForm($ag_scenario_facility_group);
          $this->redirect('scenario/fgroup?id=' . $this->scenario_id); //redirect the user to edit the facilitygroup
        } else {
          $this->redirect('scenario/edit?id=' . $this->scenario_id);
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
  public function executeShifttemplates(sfWebRequest $request)
  {
    $this->setScenarioBasics($request);

    //the shift template step, so there may need to be some manual shift template creation, i.e. i didn't say i need at least 2 nurses in a hurricane shelter
    //get all possible staff resource types
//
//            $undefinedShiftsQuery = agDoctrineQuery::create()
//            ->select('f.id, f.facility_name, frt.id, frt.facility_resource_type,
//                      sfr.id, srt.id, srt.staff_resource_type,
//                      fsr.id, fsr.minimum_staff, fsr.maximum_staff,
//                      st.id')
//            ->addSelect('fr.id')
//            ->from('agFacility f')
//            ->innerJoin('f.agFacilityResource fr')
//            ->innerJoin('fr.agFacilityResourceType frt')
//            ->innerJoin('fr.agScenarioFacilityResource sfr')
//            ->innerJoin('sfr.agScenarioFacilityGroup sfg')
//            ->innerJoin('sfr.agFacilityStaffResource fsr')
//            ->innerJoin('fsr.agStaffResourceType srt')
//            ->leftJoin('frt.agShiftTemplate st ON st.facility_resource_type_id = frt.id AND st.staff_resource_type_id = srt.id')
//            ->Where('sfg.scenario_id = ?', $scenarioId)
//            ->andWhere('st.id IS NULL');
    //this query above, should return to us all undefined staff resources... and exclude shift templates



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

  /**
   * @method executeScenarioshifts()
   * Generates a new scenario shift form
   * @param sfWebRequest $request
   */
  public function executeShifts(sfWebRequest $request)
  {

//CREATE  / UPDATE
    //$request->getParameter('shiftid') == '' ? $this->shiftid = 'new' : $this->shiftid = $request->getParameter('shiftid');

    $this->scenario_id = $request->getParameter('id');
    $this->scenario_name = Doctrine_Core::getTable('agScenario')->find($this->scenario_id)->getScenario();
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
      $this->ag_facility_resources = agDoctrineQuery::create()
              ->select('a.facility_id, af.*, afrt.*')
              ->from('agFacilityResource a, a.agFacility af, a.agFacilityResourceType afrt')
              ->execute();
      $this->groupform = new agScenarioFacilityGroupForm();
      $this->groupform->getObject()->setAgScenario()->id = $
          $this->redirect('scenario/group');
    } else {
      $this->setTemplate('new');
    }
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
   *
   * @param sfWebRequest $request
   * creates a scenario form populated with info for the requested scenario
   */
  public function executeEdit(sfWebRequest $request)
  {
    $this->forward404Unless(
        $ag_scenario = Doctrine_Core::getTable('agScenario')->find(array($request->getParameter('id'))), sprintf('Object ag_scenario does not exist (%s).', $request->getParameter('id')));
    $this->ag_scenario_facility_groups = agDoctrineQuery::create()
            ->select('a.*')
            ->from('agScenarioFacilityGroup a')
            ->where('a.scenario_id = ?', $request->getParameter('id'))
            ->execute();
    $this->form = new agScenarioForm($ag_scenario);
    //p-code
    $this->getResponse()->setTitle('Sahana Agasti Edit ' . $ag_scenario . ' Scenario');
    //end p-code
  }

  public function executeEditgroup(sfWebRequest $request)
  {
    
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
