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
**/
class facilityActions extends agActions
{

  protected $searchedModels = array('agFacility');

  public function executeSearch(sfWebRequest $request)
  {

    parent::doSearch($request->getParameter('query'));
    $this->target_module = 'facility';
    $this->setTemplate(sfConfig::get('sf_app_dir') . DIRECTORY_SEPARATOR . 'modules/search/templates/search');
    //$this->setTemplate('global/search');
  }

  /**
  * executeIndex()
  *
  * Placeholder for the index action. Only the template is
  * needed, so this method is empty.
  *
  * @param $request sfWebRequest object for current page request
  **/
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
    /**
    * Query the database for agFacility records joined with
    * agFacilityResource records
    **/
    $query = agDoctrineQuery::create()
            ->select('f.*, fr.*')
            ->from('agFacility f, f.agFacilityResource fr');

    /**
    * Create pager
    **/
    $this->pager = new sfDoctrinePager('agFacility', 5);

    /**
    * Check if the client wants the results sorted, and set pager
    * query attributes accordingly
    **/
    $this->sortColumn = $request->getParameter('sort') ? $request->getParameter('sort') : 'facility_name';
    $this->sortOrder = $request->getParameter('order') ? $request->getParameter('order') : 'ASC';

    if ($request->getParameter('sort')) {
      $query = $query->orderBy($request->getParameter('sort', 'facility_name') . ' ' . $request->getParameter('order', 'ASC'));
    }

    /**
    * Set pager's query to our final query including sort
    * parameters
    **/
    $this->pager->setQuery($query);

    /**
    * Set the pager's page number, defaulting to page 1
    **/
    $this->pager->setPage($request->getParameter('page', 1));
    $this->pager->init();
  }

  /**
  * executeShow()
  *
  * Show an individual facility record
  *
  * @param $request sfWebRequest object for current page request
  **/
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
  **/
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
  **/
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
    $facilityResourceIds  = Doctrine::getTable('agFacilityResource')
            ->createQuery('agFR')
            ->select('agFR.*')
            ->from('agFacilityResource agFR')
            ->where('facility_id = ?', $request->getParameter('id'))
            ->execute(array(), 'key_value_array');


    $this->events = null;//agFacilityHelper::returnActionableResources($facilityResourceIds, FALSE);

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

  /**
  * Imports facility records from a properly formatted XLS file.
  *
  * @todo: define a standard import format and document it for the end user.
  * @todo: make this more robust and create meaningful error messages for failed import fields and records.
  **/
  public function executeImport(sfWebRequest $request)
  {
    $this->forward404Unless($scenarioId = $request->getParameter('scenario_id'));

    $uploadedFile = $_FILES["import"];

    $uploadDir = sfConfig::get('sf_upload_dir') . '/';
    move_uploaded_file($uploadedFile["tmp_name"], $uploadDir . $uploadedFile["name"]);
    $this->importPath = $uploadDir . $uploadedFile["name"];

    // fires event so listener will process the file (see ProjectConfiguration.class.php)
    $this->dispatcher->notify(new sfEvent($this, 'import.facility_file_ready'));
    // TODO: eventually use this ^^^ to replace this vvv.

    $import = new AgImportXLS();
//    $returned = $import->createTempTable();

    $processedToTemp = $import->processImport($this->importPath);
    $this->numRecordsImported = $import->numRecordsImported;
    $this->events = $import->events;

    // Normalize imported temp data only if import is successful.
    if ($processedToTemp)
    {
      // Grab table name from AgImportXLS class.
      $sourceTable = $import->tempTable ;
      $dataNorm = new agImportNormalization($scenarioId, $sourceTable, 'facility');

      $format="%d/%m/%Y %H:%M:%S";
//    echo strftime($format);

      $dataNorm->normalizeImport();
      $this->summary = $dataNorm->summary;
    }


    //this below block is a bit hard coded and experimental, it should be changed to use gparams

      $agLuceneIndex = new agLuceneIndex('agFacility');
      $indexResult = $agLuceneIndex->indexAll();

//      chdir(sfConfig::get('sf_root_dir')); // Trick plugin into thinking you are in a project directory
//      $dispatcher = sfContext::getInstance()->getEventDispatcher();
//      $task = new luceneReindexTask($dispatcher, new sfFormatter()); //this->dispatcher
//      $task->run(array('model' => 'agFacility'), array('env' => 'all', 'connection' => 'doctrine', 'application' => 'frontend'));

//    echo strftime($format);
  }

  /**
  * executeDisable()
  *
  * Delete a facility record's associated facility resources. Redirects to facility edit page
  * when it is done.
  *
  * @param $request sfWebRequest object for current page request
  **/
  public function executeDisable(sfWebRequest $request)
  {
    $request->checkCSRFProtection();
    $this->forward404Unless($ag_facility = Doctrine_Core::getTable('agFacility')->find(array($request->getParameter('id'))), sprintf('Object ag_facility does not exist (%s).', $request->getParameter('id')));
    
    foreach($ag_facility->getAgFacilityResource() as $agFR){
      $agFR->set('facility_resource_status_id', 1);//agGlobal::getParam('facility_resource_disabled_status'));
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
  **/

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
    **/
    if ($agEntity = $ag_facility->getAgSite()->getAgEntity()) {
        if($agF = $ag_facility->getAgFacilityResource()){
          foreach($agF as $agFR){
            $agSFR = $agFR->getAgScenarioFacilityResource()->getData();
            $agEFR = $agFR->getAgEventFacilityResource()->getData();
            if(empty($agSFR) && empty($agEFR) ){
              $agEntity->delete();
              $this->redirect('facility/list');
             }
              else{
                throw new sfDoctrineException('This facility is currently used in scenarios/events, try disabling it instead.');
              }
          }
        }
        else{
          $agEntity->delete();
          $this->redirect('facility/list');
        }
        $agEntity->delete();
    } else {

      /**
      * If there was no related agEntity record found, something is
      * wrong.
      **/
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
  **/
  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $formName = $form->getName();
    $values = $request->getParameter($formName);
    $files = $request->getFiles($form->getName());
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid()) {
      $ag_facility = $form->save();
      LuceneRecord::updateLuceneRecord($ag_facility);

      $this->redirect('facility/edit?id=' . $ag_facility->getId());
    }
  }

  /**
  * Instantiates agFacilityExporter() and calls its functions to export all facilities
  * in the system. After the exporter returns, the instance is unset to free up memory,
  * then the browser directs to the exported xls.
  **/
  public function executeFacilityExport()
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
  **/
  public function gatherLookupValues($lookUps = null)
  {
    if(!isset($lookUps) || !is_array($lookUps)) {
      return null;
    }
    foreach($lookUps as $key => $lookUp) {
      $lookUpQuery = agDoctrineQuery::create()
        ->select($lookUp['selectColumn'])
        ->from($lookUp['selectTable']);
      if(isset($lookUp['whereColumn']) && isset($lookUp['whereValue'])) {
        $lookUpQuery->where($lookUp['whereColumn'] . '=' . $lookUp['whereValue']);
      }
      $returnedLookups[$key] = $lookUpQuery->execute(null,'single_value_array');
    }
    return $returnedLookups;
  }
}
