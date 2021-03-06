<?php

/**
 * Facility Actions extends sfActions
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author     Charles Wisniewski, CUNY SPS
 * @author     Nils Stolpe, CUNY SPS
 * @author     Ilya Gulko, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 * */
class facilityActions extends agActions
{

  protected $_search = 'facility';

//  public function executeSearch(sfWebRequest $request)
//  {
//    $this->targetAction = 'search';
//    $string = $request->getParameter('query');
//    $pattern = "/\W/";
//    $replace = " ";
//    $this->params = '?query=' . urlencode(trim(preg_replace($pattern, $replace, $string), '+'));
////    $this->params = '?query=' . $request->getParameter('query');
//    $currentPage = ($request->hasParameter('page')) ? $request->getParameter('page') : 1;
//    parent::doSearch($request->getParameter('query'), $currentPage);
//    $this->setTemplate(sfConfig::get('sf_app_dir') . DIRECTORY_SEPARATOR . 'modules/search/templates/search');
//    //$this->setTemplate('global/search');
//  }

  /**
   * executeIndex()
   * provides the user with a scenario drop down for facility import and the facility landing page
   *
   * @param $request sfWebRequest object for current page request
   * */
  public function executeIndex(sfWebRequest $request)
  {
    //add scenario
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
  * executeList()
  *
  * Executes the list action for facility module
  *
  * @param $request sfWebRequest object for current page request
  **/
  public function executeList(sfWebRequest $request)
  {
    // we use the get parameters to manage most of this action's methods
    $this->listParams = $request->getGetParameters();

    // here are the post params we're looking for
    if ($request->getPostParameter('query')) {
      // if found, we trigger our redirect and add it to our listParams
      $param = str_replace('*', '%', strtolower(trim($request->getPostParameter('query'))));

      // merge the results together
      $this->listParams = array_merge($this->listParams, array($postParam => $param));
      
      // if a post was found we redirect and add everything via http_build_query
      $this->redirect(($this->moduleName . '/' . $this->actionName . '?' .
        http_build_query($this->listParams)));
    }

    // if a post was not found, we happily continue on with variable declarations
    $this->targetAction = 'list';
    $this->targetModule = $this->_search;

    // these are the 'normal' get params we're looking for
    foreach (array('sort', 'order', 'query') as $getParam) {
      $$getParam = ($request->getParameter($getParam)) ? $request->getParameter($getParam) : NULL;
    }

    // $sort, $order, and $query are magically created above ($$getParam)
    list($this->displayColumns, $doctrineQuery) = agListHelper::getFacilityList($sort, $order, $query);

    $currentPage = ($request->hasParameter('page')) ? $request->getParameter('page') : 1;
    $resultsPerPage = agGlobal::getParam('default_list_page_size');
    $this->pager = new Doctrine_Pager($doctrineQuery, $currentPage, $resultsPerPage);
    $this->data = $this->pager->execute(array(), Doctrine_Core::HYDRATE_SCALAR);



// ------------------------
//    /**
//     * Query the database for agFacility records joined with
//     * agFacilityResource records
//     * */
//    $query = agDoctrineQuery::create()
//            ->select('f.*, fr.*')
//            ->from('agFacility f, f.agFacilityResource fr');
//
//    /**
//     * Create pager
//     * */
//    $this->target_module = 'facility';
//    $request->getParameter('limit') ? $limit = $request->getParameter('limit') : $limit = 10;
//    //instead of 10 for the limit, we should pull from a global parameter
//    $this->pager = new sfDoctrinePager('agFacility', $limit);
//
//    /**
//     * Check if the client wants the results sorted, and set pager
//     * query attributes accordingly
//     * */
//    $this->sortColumn = $request->getParameter('sort') ? $request->getParameter('sort') : 'facility_name';
//    $this->sortOrder = $request->getParameter('order') ? $request->getParameter('order') : 'ASC';
//
//    if ($request->getParameter('sort')) {
//      $query = $query->orderBy($request->getParameter('sort', 'facility_name') . ' ' . $request->getParameter('order', 'ASC'));
//    }
//
//    /**
//     * Set pager's query to our final query including sort
//     * parameters
//     * */
//    $this->pager->setQuery($query);
//
//    /**
//     * Set the pager's page number, defaulting to page 1
//     * */
//    $this->pager->setPage($request->getParameter('page', 1));
//    $this->pager->init();
  }

  /**
   * executeShow()
   *
   * Show an individual facility record
   *
   * @param $request sfWebRequest object for current page request
   * */
  public function executeShow(sfWebRequest $request)
  {
    $this->ag_facility = Doctrine_Core::getTable('agFacility')->find(array($request->getParameter('id')));
    $this->forward404Unless($this->ag_facility);

    $this->ag_phone_contact_types = Doctrine::getTable('agPhoneContactType')
            ->createQuery('c')
            ->execute();
    $this->ag_email_contact_types = Doctrine::getTable('agEmailContactType')
            ->createQuery('d')
            ->execute();
    $this->ag_address_contact_types = Doctrine::getTable('agAddressContactType')
            ->createQuery('f')
            ->execute();

//  $query = Doctrine_Core::getTable('agFacility')->find(array($request->getParameter('id')));
//
//  $this->pager = new sfDoctrinePager('agPerson', 1);
    //p-code
    $this->getResponse()->setTitle('Sahana Agasti Facility - ' . $this->ag_facility->getFacilityName());
    //end p-code
  }

  /**
   * executeNew()
   *
   * Show form for creating a new facility record
   *
   * @param $request sfWebRequest object for current page request
   * */
  public function executeNew(sfWebRequest $request)
  {
    $this->form = new agFacilityForm();
  }

  /**
   * executeCreate()
   *
   * Create a new facility record (called from executeNew)
   *
   * @param $request sfWebRequest object for current page request
   * */
  public function executeCreate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST));

    $this->form = new agFacilityForm();

    $ent = new agEntity();
    $ent->save();
    $site = new agSite();
    $site->setAgEntity($ent);
    $site->save();
    $this->form->getObject()->setAgSite($site);

    $this->form->getObject()->getAgSite()->setAgEntity($ent);

    $this->processForm($request, $this->form);

    $this->setTemplate('new');
  }

  /**
   * executeEdit()
   *
   * Edit existing facility record. Results in a new instance of
   * agFacilityForm, which contains most of the logic for this
   * action.
   *
   * @param $request sfWebRequest object for current page request
   */
  public function executeEdit(sfWebRequest $request)
  {
    $this->forward404Unless(
        $ag_facility = Doctrine_Core::getTable('agFacility')->find(array($request->getParameter('id'))),
        sprintf('Object ag_facility does not exist (%s).', $request->getParameter('id'))
    );

    $this->form = new agFacilityForm($ag_facility);
    $facilityResourceIds = Doctrine::getTable('agFacilityResource')
            ->createQuery('agFR')
            ->select('agFR.*')
            ->from('agFacilityResource agFR')
            ->where('facility_id = ?', $request->getParameter('id'))
            ->execute(array(), 'key_value_array');


    $this->events = null; //agFacilityHelper::returnActionableResources($facilityResourceIds, FALSE);
    //p-code
    $this->getResponse()->setTitle('Sahana Agasti Facility Edit');
    //end p-code
  }

  /**
   * executeUpdate()
   *
   * Update an existing facility record (called from executeEdit).
   * Most of the logic is contained in agFacilityForm.
   *
   * Redirects back to /edit when it is done.
   *
   * @param $request sfWebRequest object for current page request
   */
  public function executeUpdate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));
    $this->forward404Unless($ag_facility = Doctrine_Core::getTable('agFacility')->find(array($request->getParameter('id'))), sprintf('Object ag_facility does not exist (%s).', $request->getParameter('id')));
    $this->form = new agFacilityForm($ag_facility);

    $this->processForm($request, $this->form);

    $this->setTemplate('edit');
  }

  public function executePoll(sfWebRequest $request)
  {
    $this->getResponse()->setHttpHeader('Content-Type', 'application/json; charset=utf-8');
    return $this->renderText(json_encode($this->getContext()->get('job_statuses')));
  }

  /**
   * Imports facility records from a properly formatted XLS file.
   *
   * @todo: define a standard import format and document it for the end user.
   * @deprecated moved to scenario
   */
  public function executeImport(sfWebRequest $request)
  {
    $this->forward404Unless($scenarioId = $request->getParameter('scenario_id'));
    $this->form = new agImportForm();

    $uploadedFile = $_FILES['import'];

    //TODO: get import data directory root info from global param
    $importDataRoot = sfConfig::get('sf_upload_dir');
    $importDir = $importDataRoot . DIRECTORY_SEPARATOR . $this->moduleName;
    if (!file_exists($importDir)) {
      mkdir($importDir);
    }
    $importPath = $importDir . DIRECTORY_SEPARATOR . 'import.xls' /*$uploadedFile['name']*/;
    if (!move_uploaded_file($uploadedFile['tmp_name'], $importPath)) {
      return sfView::ERROR;
    }

    $this->importPath = $importPath;

    //$this->redirect('facility/import');
    $import = new agFacilityImportXLS();

    $this->timer = time();
    $processedToTemp = $import->processImport($this->importPath);
    $this->timer = (time() - $this->timer);

    // removes the file from the server
    unlink($this->importPath);

    $this->numRecordsImported = $import->numRecordsImported;
    $this->events = $import->events;

    // Normalizes imported temp data only if import is successful.
    if ($processedToTemp) {
      // Grab table name from AgImportXLS class.
      $sourceTable = $import->tempTable;

      $this->importer = new agFacilityImportNormalization($scenarioId, $sourceTable, 'facility');
      //TODO: $this->dispatcher->notify(new sfEvent($this, 'import.start'));

      $this->timer = time();
      $this->importer->normalizeImport();

      $this->summary = $this->importer->summary;
    }

    $this->timer = (time() - $this->timer);

  }

  /**
   * executeDisable()
   *
   * Delete a facility record's associated facility resources. Redirects to facility edit page
   * when it is done.
   *
   * @param $request sfWebRequest object for current page request
   * */
  public function executeDisable(sfWebRequest $request)
  {
    $request->checkCSRFProtection();
    $this->forward404Unless($ag_facility = Doctrine_Core::getTable('agFacility')->find(array($request->getParameter('id'))), sprintf('Object ag_facility does not exist (%s).', $request->getParameter('id')));

    foreach ($ag_facility->getAgFacilityResource() as $agFR) {
      $agFR->set('facility_resource_status_id', 1); //agGlobal::getParam('facility_resource_disabled_status'));
      $agFR->save();
    }


    $this->redirect('facility/edit?id=' . $ag_facility->getId());
  }

  /**
   * executeDelete()
   *
   * Delete a facility record. Redirects to facility list page
   * when it is done.
   *
   * @param $request sfWebRequest object for current page request
   * */
  public function executeDelete(sfWebRequest $request)
  {
    $request->checkCSRFProtection();

    $this->forward404Unless($ag_facility = Doctrine_Core::getTable('agFacility')->find(array($request->getParameter('id'))), sprintf('Object ag_facility does not exist (%s).', $request->getParameter('id')));
    /**
     * Locate the associated agEntity record through the associated
     * agSite record. Call the agEntity object's delete() method,
     * which results in the deletion of all of the records
     * associated with this agFacility record, including contact
     * information.
     *
     * agEntity will call its agSite object's delete() method which
     * will, in turn, call our agFacility object's delete() method.
     * The agFacility object will also delete the associated
     * agFacilityResource records.
     * */
    if ($agEntity = $ag_facility->getAgSite()->getAgEntity()) {
      if ($agF = $ag_facility->getAgFacilityResource()) {
        foreach ($agF as $agFR) {
          $agSFR = $agFR->getAgScenarioFacilityResource()->getData();
          $agEFR = $agFR->getAgEventFacilityResource()->getData();
          if (empty($agSFR) && empty($agEFR)) {
            $agEntity->delete();
            $this->redirect('facility/list');
          } else {
            throw new sfDoctrineException('This facility is currently used in scenarios/events, try disabling it instead.');
          }
        }
      } else {
        $agEntity->delete();
        $this->redirect('facility/list');
      }
      $agEntity->delete();
    } else {

      /**
       * If there was no related agEntity record found, something is
       * wrong.
       * */
      throw new LogicException('agFacility is expected to have an agSite and an agEntity.');
    }

    /**
     *  Redirect to facility/list when done if it hasn't happened already
     */
    $this->redirect('facility/list');
  }

  /**
   * processForm()
   *
   * Generated by Symfony/Doctrine. Called by relevant methods
   * which require form processing.
   *
   * @param $request sfWebRequest object for current page request
   * @param $form sfForm object to process
   * */
  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $formName = $form->getName();
    $values = $request->getParameter($formName);
    $files = $request->getFiles($form->getName());
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid()) {
      $ag_facility = $form->save();
      $this->redirect('facility/edit?id=' . $ag_facility->getId());
    }
  }

  /**
   * Instantiates agFacilityExporter() and calls its functions to export all facilities
   * in the system. After the exporter returns, the instance is unset to free up memory,
   * then the browser directs to the exported xls.
   *
   * */
  public function executeExport(sfWebRequest $request)
  {

    $facilityExporter = new agFacilityExporter();
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

  /**
   * This function constructs a Doctrine Query based on the values of the parameter passed in.
   *
   * The query will return the values from a single column of a table, with the possiblity to
   * add a where clause to the query.
   *
   * @param $lookups array()  gatherLookupValues expects $lookups to be a two-dimensional array.
   *                          Keys of the outer level are expected to be column headers for a
   *                          lookup column, or some other kind of organized data list. However,
   *                          submitting a non-associative array will not cause any errors.
   *
   *                          The expected structure of the array is something like this:
   *
   *                          $lookUps = array(
   *                                       'Facility Resource Status' => array(
   *                                           'selectTable'  => 'agFacilityResourceStatus',
   *                                           'selectColumn' => 'facility_resource_status',
   *                                           'whereColumn'  => null,
   *                                           'whereValue' => null
   *                                       ),
   *                                       'Facility Resource Status' => array(
   *                                           'selectTable'  => 'agFacilityResourceStatus',
   *                                           'selectColumn' => 'facility_resource_status',
   *                                           'whereColumn'  => null,
   *                                           'whereValue' => null
   *                                       )
   *                          );
   *
   *                          Additional values of the $lookUps array can also be included.
   *                          The keys of the inner array musy be set to selectTable, selectColumn,
   *                          whereColumn, and whereValue.
   * */
  public function gatherLookupValues($lookUps = null)
  {
    if (!isset($lookUps) || !is_array($lookUps)) {
      return null;
    }
    foreach ($lookUps as $key => $lookUp) {
      $lookUpQuery = agDoctrineQuery::create()
              ->select($lookUp['selectColumn'])
              ->from($lookUp['selectTable']);
      if (isset($lookUp['whereColumn']) && isset($lookUp['whereValue'])) {
        $lookUpQuery->where($lookUp['whereColumn'] . '=' . $lookUp['whereValue']);
      }
      $returnedLookups[$key] = $lookUpQuery->execute(null, 'single_value_array');
    }
    return $returnedLookups;
  }

}
