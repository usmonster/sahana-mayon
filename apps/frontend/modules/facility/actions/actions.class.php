<?php

/**
 * Facility Actions extends sfActions
 *
 * PHP Version 5
 *
 * LICENSE: This source file is subject to LGPLv3.0 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 *
 * @author     Charles Wisniewski, CUNY SPS
 * @author     Nils Stolpe, CUNY SPS
 * @author     Ilya Gulko, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
class facilityActions extends agActions
{

  protected $searchedModels = array('agFacility');

  /**
   * executeIndex()
   *
   * Placeholder for the index action. Only the template is
   * needed, so this method is empty.
   *
   * @param $request sfWebRequest object for current page request
   */
  public function executeIndex(sfWebRequest $request)
  {
    //do some index stuff
  }

  /**
   * executeList()
   *
   * Executes the list action for facility module
   *
   * @param $request sfWebRequest object for current page request
   */
  public function executeList(sfWebRequest $request)
  {
    /**
     * Query the database for agFacility records joined with
     * agFacilityResource records
     */
    $query = agDoctrineQuery::create()
            ->select('f.*, fr.*')
            ->from('agFacility f, f.agFacilityResource fr');

    /**
     * Create pager
     */
    $this->pager = new sfDoctrinePager('agFacility', 5);

    /**
     * Check if the client wants the results sorted, and set pager
     * query attributes accordingly
     */
    $this->sortColumn = $request->getParameter('sort') ? $request->getParameter('sort') : 'facility_name';
    $this->sortOrder = $request->getParameter('order') ? $request->getParameter('order') : 'ASC';

    if ($request->getParameter('sort')) {
      $query = $query->orderBy($request->getParameter('sort', 'facility_name') . ' ' . $request->getParameter('order', 'ASC'));
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
   * executeShow()
   *
   * Show an individual facility record
   *
   * @param $request sfWebRequest object for current page request
   */
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
  }

  /**
   * executeNew()
   *
   * Show form for creating a new facility record
   *
   * @param $request sfWebRequest object for current page request
   */
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
   */
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

//  //->getAgSite()->getAgEntity()->getAgEntityEmailContact()
//
//  $this->ag_entity_emails = $ag_facility->getAgSite()->getAgEntity()->getEntityEmails();
//
//  foreach($this->ag_entity_emails as $key => $email1) {
//    echo(get_class($email1).','.$key . ','. $email1->getAgEmailContact());
//    echo("<br>");
//  }

    $this->form = new agFacilityForm($ag_facility);
    $facilityResourceIds  = Doctrine::getTable('agFacilityResource')
            ->createQuery('agFR')
            ->select('agFR.*')
            ->from('agFacilityResource agFR')
            ->where('facility_id = ?', $request->getParameter('id'))
            ->execute(array(), 'key_value_array');


    $events = agFacilityHelper::returnActionableResources($facilityResourceIds, FALSE);
$foo = 'boo';
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
   * @todo: make this more robust and create meaningful error messages for failed import fiels and records.
   * */
  public function executeImport()
  {

    $uploadedFile = $_FILES["import"];

    $uploadDir = sfConfig::get('sf_upload_dir') . '/';
    move_uploaded_file($uploadedFile["tmp_name"], $uploadDir . $uploadedFile["name"]);
    $this->importPath = $uploadDir . $uploadedFile["name"];

    // fires event so listener will process the file (see ProjectConfiguration.class.php)
    $this->dispatcher->notify(new sfEvent($this, 'import.facility_file_ready'));
    // TODO: eventually use this ^^^ to replace this vvv.

    $import = new AgImportXLS();
    $returned = $import->createTempTable();

    $import->processImport($this->importPath);
    $this->numRecordsImported = $import->numRecordsImported;
    $this->events = $import->events;

    // Setting scenario id and source table for testing purposes.
    // Source table should be returned from AgImportXLS class.
    $scenarioId = 1;
    $sourceTable = 'temp_facilityImport';
    $dataNorm = new agImportNormalization($scenarioId, $sourceTable, 'facility');

    $format="%d/%m/%Y %H:%M:%S";
    echo strftime($format);

    $dataNorm->normalizeImport();
    $this->summary = $dataNorm->summary;

    echo strftime($format);
  }

  /**
   * executeDelete()
   *
   * Delete a facility record. Redirects to facility list page
   * when it is done.
   *
   * @param $request sfWebRequest object for current page request
   */
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
     */
    if ($agEntity = $ag_facility->getAgSite()->getAgEntity()) {
      $agEntity->delete();
    } else {
      /**
       * If there was no related agEntity record found, something is
       * wrong.
       */
      throw new LogicException('agFacility is expected to have an agSite and an agEntity.');
    }

    /**
     *  Redirect to facility/list when done.
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
   */
  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $poo = $form->getName();
    $values = $request->getParameter($poo);
    $files = $request->getFiles($form->getName());
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid()) {
      $ag_facility = $form->save();
      LuceneRecord::updateLuceneRecord($ag_facility);

      $this->redirect('facility/edit?id=' . $ag_facility->getId());
    }
  }

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

  /*
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
   */
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
      $thy = $lookUpQuery->getSqlQuery();
      $returnedLookups[$key] = $lookUpQuery->execute(null,'single_value_array');
    }
    return $returnedLookups;
  }
}
