<?php

/**
 * Staff Actions extends agActions
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
 */
class agStaffActions extends agActions
{

  protected $_searchedModels = array('agStaff');

  /**
   * executeIndex is currently used to execute the index action
   *
   */
  public function executeIndex(sfWebRequest $request)
  {
    //do some index stuff here.
  }

  public function executeStafftypes(sfWebRequest $request)
  {
    $this->ag_staff_types = Doctrine_Core::getTable('agStaffResourceType')
        ->createQuery('a')
        ->execute();

    $this->staffTypeForm = new PluginagStaffTypeForm();

    if ($request->isMethod(sfRequest::POST)) {
      $this->staffTypeForm->bind($request->getParameter($this->staffTypeForm->getName()),
                                                        $request->getFiles($this->staffTypeForm->getName()));
      if ($this->staffTypeForm->isValid()) {
        $this->staffTypeForm->save();
      }
    }
  }

  public function executeList(sfWebRequest $request)
  {
    $this->targetAction = 'list';
    $this->status = 'all';
    $this->sort = NULL;
    $this->order = NULL;

    //the next few lines could be abstracted to agActions as they are request params that may be
    //used for any list
    if ($request->getGetParameter('status'))
      $this->status = $request->getGetParameter('status');
    if ($request->getGetParameter('sort'))
      $this->sort = $request->getGetParameter('sort');
    if ($request->getGetParameter('order'))
      $this->order = $request->getGetParameter('order');

    /** @todo take into consideration app_display */
    $staffStatusOptions = agDoctrineQuery::create()
        ->select('s.staff_resource_status, s.staff_resource_status')
        ->from('agStaffResourceStatus s')
        ->execute(array(), 'key_value_pair');
    //the above query returns an array of keys matching their values.
    //ideally the above should exist in a global param,
    //so the database is not queried all the time
    $staffStatusOptions['all'] = 'all';
    if ($request->getParameter('status') && in_array($request->getParameter('status'),
                                                                            $staffStatusOptions)) {
      $this->status = $request->getParameter('status');
    }
    $this->statusWidget = new sfForm();
    $this->statusWidget->setWidgets(
        array(
          'status' => new sfWidgetFormChoice(
              array(
                'multiple' => false,
                'choices' => $staffStatusOptions,
                'label' => 'Staff Status'
              ),
              array('onchange' => 'submit();')
          ),
        // 'add_empty' => true))// ,'onClick' => 'submit()'))
        )
    );
    $this->statusWidget->setDefault('status', $this->status);

    $inlineDeco = new agWidgetFormSchemaFormatterInlineLeftLabel($this->statusWidget->getWidgetSchema());
    $this->statusWidget->getWidgetSchema()->addFormFormatter('inline', $inlineDeco);
    $this->statusWidget->getWidgetSchema()->setFormFormatterName('inline');

    list($this->displayColumns, $query) = agListHelper::getStaffList(null, $this->status, $this->sort, $this->order);

    $currentPage = ($request->hasParameter('page')) ? $request->getParameter('page') : 1;
    $resultsPerPage = agGlobal::getParam('staff_list_results_per_page');
    $this->pager = new Doctrine_Pager($query, $currentPage, $resultsPerPage);
    $this->data = $this->pager->execute(array(), Doctrine_Core::HYDRATE_SCALAR);

    //p-code
    //$this->getResponse()->setTitle('Sahana Agasti ' . $this->event_name . ' ');
    //end p-code
  }

  /**
   * executeShow displays an individual agPerson/staff-member entry. It uses an sfDoctrinePager
   * to navigate
   *
   * the list of all staff members and to display the current staff member.
   *
   * The use of Doctrine::getTable for various _types and _formats tables is used to generate
   * table labels for the staff module's showSuccess.php.
   *
   * executeShow has also been modified so it can handle requests called from indexSuccess or
   * searchSuccess. In the former case, pagination is done for the entire list of agPersons, and
   * users can browse through every one in the DB. In the later case, a check is made to see if
   * $request has a query (it will only have one if it has come from searchSuccess). If $request
   * has query, paginated results will only include those agPersons returned by the Lucene query.
   * If not, each agPerson object will be paginated.
   *
   * @param $request - a query.
   * @todo refactor the _types and _formats queries into one larger query.
   */
  public function executeShow(sfWebRequest $request)
  {
    $this->forward404Unless(
        $this->agStaff = Doctrine::getTable('AgStaff')->find($request->getParameter('id')),
                                                                                    sprintf('Object ag_staff does not exist (%s).',
                                                                                            $request->getParameter('id'))
    );

    $this->ag_person_name_types = Doctrine::getTable('agPersonNameType')
        ->createQuery('b')
        ->execute();
    $this->ag_phone_contact_types = Doctrine::getTable('agPhoneContactType')
        ->createQuery('c')
        ->execute();
    $this->ag_email_contact_types = Doctrine::getTable('agEmailContactType')
        ->createQuery('d')
        ->execute();
    $this->ag_language_formats = Doctrine::getTable('agLanguageFormat')
        ->createQuery('e')
        ->execute();
    $this->ag_address_contact_types = Doctrine::getTable('agAddressContactType')
        ->createQuery('f')
        ->execute();


    //$this->agStaff = $this->pager->getResults()->getFirst();
    $agPerson = $this->agStaff->getAgPerson();
    $this->addressArray = $agPerson->getEntityAddressByType(
            true, true, agAddressHelper::ADDR_GET_NATIVE_STRING
    );

    //p-code
    $names_title = new agPersonNameHelper(array($agPerson->getId()));
    $person_names_title = $names_title->getPrimaryNameByType();
    $this->getResponse()
        ->setTitle(
            'Sahana Agasti Staff - ' . (
            isset($person_names_title[$agPerson->getId()]['given']) ?
                $person_names_title[$agPerson->getId()]['given'] : ''
            )
            . ' ' . (
            isset($person_names_title[$agPerson->getId()]['family']) ?
                $person_names_title[$agPerson->getId()]['family'] : ''
            )
    );
    //end p-code
  }

  /**
   * Creates a blank agPerson form to create and save a new agPerson/staff-member
   *
   * @param sfWebRequest $request - A request object
   * */
  public function executeNew(sfWebRequest $request)
  {
    $this->form = new PluginagStaffPersonForm();
  }

  public function executeAddstaffresource($request)
  {
    $this->forward404unless($request->isXmlHttpRequest());
    $number = intval($request->getParameter("num"));

    $resourceForm = new PluginagEmbeddedAgStaffResourceForm();
    unset($resourceForm['_csrf_token']);
    //$resourceForm->disableLocalCSRFProtection();

    $resourceForm->getWidgetSchema()->setNameFormat('ag_person[staff][type][' . $number . ']' . '[%s]');
    //$resourceForm->getWidgetSchema()->setIdFormat('%s_');

    return $this->renderPartial('staffForm', array('form' => $resourceForm)
    );
  }

  public function executeDeletestaffresource($request)
  {
    $this->forward404unless($request->isXmlHttpRequest());
    if ($request->hasParameter('staffResourceId')) {
      $staffResource = agDoctrineQuery::create()
          ->delete('agStaffResource sr')
          ->where('id = ?', $request->getParameter('staffResourceId'))
          ->execute();
    }
  }

  /**
   * Saves the new agPerson from data entered into the form generated by executeNew
   *
   * A new agEntity is also created and assigned to the agPerson being created. This
   * ensures that the new agPerson gets a new entity_id and that creation does not fail.
   *
   * @param sfWebRequest $request - A request object
   * */
  public function executeCreate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST));

    $this->form = new PluginagStaffPersonForm();
    $ent = new agEntity();
    $ent->save();

    $this->form->getObject()->setAgEntity($ent);
    //$this->form should now have in it an entity and a staff person related.
    $this->processForm($request, $this->form);

    $this->setTemplate('new');
  }

  /**
   * Creates a form prepopulated with the current information of the staff member to be edited.
   *
   * @param sfWebRequest $request - This function expects an ag_person.id as pass-in parameter.
   *
   * */
  public function executeEdit(sfWebRequest $request)
  {
    $this->forward404Unless(
        $ag_staff = Doctrine::getTable('AgStaff')->find($request->getParameter('id')),
                                                                               sprintf('Object ag_staff does not exist (%s).',
                                                                                       $request->getParameter('id'))
    );

    $ag_person = $ag_staff->getAgPerson();
    $this->form = new PluginagStaffPersonForm($ag_person);

    //p-code
    $this->getResponse()->setTitle('Sahana Agasti Staff Edit');
    //end p-code
  }

  /**
   * Processes the form created by executeEdit to update the agPerson/staff-member being edited.
   * @param sfWebRequest $request - This function expects an ag_person.id as a pass-in parameter.
   *
   * */
  public function executeUpdate(sfWebRequest $request)
  {
    $this->forward404Unless(
        $request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT)
    );
    $this->forward404Unless(
        $ag_staff = Doctrine::getTable('AgStaff')->find($request->getParameter('id')),
                                                                               sprintf('Object ag_staff does not exist (%s).',
                                                                                       $request->getParameter('id'))
    );

    $ag_person = $ag_staff->getAgPerson();
    $this->form = new PluginagStaffPersonForm($ag_person);

    $this->processForm($request, $this->form);

    $this->setTemplate('edit');
  }

  /**
   * Deletes the selected agPerson/staff-member.
   * @param sfWebRequest $request - This function expects an ag_person.id as a pass-in parameter.
   * @todo implement soft delete
   *
   *
   * The getting and deleting of various $ag_person properties is necessary to ensure that
   * the agPerson's relations are all properly deleted as well.
   *
   * As values for agPersonLanguageCompetency are not directly availabe to an agPerson,
   * it is necessary to iterate through the agPerson's agPersonMjAgPersonAgLanguage values
   * and delete their related agPersonLanguageCompetency values before deleting the actual
   * agPersonMjAgPerosnLanguage values.
   *
   * Last to be deleted is the actual agPerson.
   */
  public function executeDelete(sfWebRequest $request)
  {
    $request->checkCSRFProtection();

    $this->forward404Unless(
        $ag_staff = Doctrine::getTable('AgStaff')->find($request->getParameter('id')),
                                                                               sprintf('Object ag_staff does not exist (%s).',
                                                                                       $request->getParameter('id'))
    );

    $ag_person = $ag_staff->getAgPerson();

    foreach ($ag_staff->getAgStaffResource() as $ag_staff_resource) {
      $ag_staff_resource->getAgEventStaff()->delete();
      $ag_staff_resource->getAgScenarioStaffResource()->delete();
      $ag_staff_resource->delete();
    }

    $ag_staff->delete();
    $ag_person->getAgPersonMjAgNationality()->delete();
    $ag_person->getAgPersonDateOfBirth()->delete();
    $ag_person->getAgPersonEthnicity()->delete();
    $ag_person->getAgPersonMaritalStatus()->delete();
    foreach ($ag_person->getAgPersonMjAgLanguage() as $lang) {
      $lang->getAgPersonLanguageCompetency()->delete();
    }
    foreach ($ag_person->getAgEntity()->getAgEntityEmailContact() as $email) {
      $email->delete();
    }
    foreach ($ag_person->getAgEntity()->getAgEntityPhoneContact() as $phone) {
      $phone->delete();
    }
    $ag_person->getAgEntity()->getAgEntityEmailContact()->delete();
    $ag_person->getAgPersonMjAgLanguage()->delete();
    $ag_person->getAgPersonMjAgPersonName()->delete();
    $ag_person->getAgPersonMjAgProfession()->delete();
    $ag_person->getAgPersonResidentialStatus()->delete();
    $ag_person->getAgPersonSex()->delete();
    //$ag_person->getAgScenarioStaff()->delete();
    $ag_person->getAgPersonMjAgReligion()->delete();
    //$ag_person->getAgPersonMjAgSiteContact()->delete();
    foreach ($ag_person->getAgEntity()->getAgEntityAddressContact() as $contact) {
      $contact->delete();
    }
    $ent = $ag_person->getAgEntity();
    $ag_person->delete();
    $ent->delete();

    $this->redirect('staff/list');
  }

  /**
   * Processes one of the forms generated in the above actions.
   *
   * @param sfWebRequest $request - A request object
   * @param sfForm $form - A form object
   *
   */
  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    //if count of staff form data is greater than count in this person's form, assume an add
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid()) {
      //are our values bound at this point?
      // The two lines below are needed to prevent an attempted delete of the agStaff object
      // attached to this person. Symfony/Doctrine seems to see agStaff as a join object in
      // this case, since it holds keys from person and staff status.
      // doSave() has also been overridden in agPersonForm.class.php so that the
      // saveStaffStatusList function is not called. Calling that refreshes the relation
      // and will cause failure on an update of an existing staff.
      //$form->getObject()->clearRelated('agStaffStatus');
      $form->getObject()->clearRelated('agStaff');

      $ag_staff = $form->save();
      $refAgStaff = $ag_staff->getAgStaff();
      $staffObj = agDoctrineQuery::create()
          ->from('agStaff s')
          ->where('s.person_id=?', $ag_staff->id)
          ->fetchOne();
      LuceneRecord::updateLuceneRecord($staffObj);

      //$staff_id = $ag_staff->getAgStaff()->getFirst()->getId();
//not an object? first it's a collection, now not an object if i just get one

      $staff_id = Doctrine::getTable('agStaff')->createQuery('a')
          ->select('a.id')
          ->from('agStaff a')
          ->where('a.person_id = ?', $ag_staff->id)
          ->fetchOne();

      //get id of STAFF person from the saved, extended agpersonform.
      // Check if the Save and Create Another button was used to submit. If it was, redirect to staff/new.
      if ($request->getParameter('CreateAnother')) {
        $this->redirect('agStaff/new');
      }
      $this->redirect('agStaff/list');
    }
  }

  /**
   * Export the entire staff list of an Agasti installation as an XLS and offer the option to open the file.
   *
   * @todo: allow export of group selections or search results, implement export for other modules and models.
   * @todo: add some more formatting to the file that is output to make reading easier.
   * */
  public function executeExport(sfWebRequest $request)
  {
    $this->startTime = microtime(true);

    $staffExporter = new agStaffExporter();
    $this->exportFile = $staffExporter->export();

    // Get the memory usage
    $peakMemory = $staffExporter->peakMemory;

    // Free up some memory by getting rid of the agFacilityExporter object.
    unset($staffExporter);
    
      // Report elapsed time
    $this->endTime = microtime(true);
    $time = mktime(0, 0, round(($this->endTime - $this->startTime), 0), 0, 0, 2000);
    $this->importTime = date("H:i:s", $time);

    // Format memory
    $bytes = array('KB', 'KB', 'MB', 'GB', 'TB');
    if ($peakMemory <= 999) {
      $peakMemory = 1;
    }
    for ($i = 0; $peakMemory > 999; $i++) {
      $peakMemory /= 1024;
    }
    $this->peakMemory = ceil($peakMemory) . " " . $bytes[$i];

  }

  //TODO: put this in the global actions file?
  public function executeCancelimport(sfWebRequest $request)
  {
    $abortFlagId = 'aborted'; //implode('_', array('abort', $this->moduleName, 'import'));
    //$this->getContext()->set($abortFlagId, TRUE);
    //TODO: get import data directory root info from global param
    $importDataRoot = sfConfig::get('sf_upload_dir');
    $importDir = $importDataRoot . DIRECTORY_SEPARATOR . $this->moduleName;
    $statusFile = $importDir . DIRECTORY_SEPARATOR . 'status.yml';
    if (is_writable($statusFile)) {
      $status = sfYaml::load($statusFile);
      $status[$abortFlagId] = TRUE;
      file_put_contents($statusFile, sfYaml::dump($status), LOCK_EX);
    }

    return sfView::NONE;
  }

  public function executeClearimport(sfWebRequest $request)
  {
    agImportNormalization::resetImportStatus($this->moduleName);
    return sfView::NONE;
  }

  public function executeImport(sfWebRequest $request)
  {
    $this->startTime = microtime(true);

    $uploadedFile = $_FILES['import'];

    //print("<pre>" . print_r($_FILES, true) . "</pre>");

    $this->importPath = sfConfig::get('sf_upload_dir') . DIRECTORY_SEPARATOR . $uploadedFile['name'];


    if (!move_uploaded_file($uploadedFile['tmp_name'], $this->importPath)) {
      return sfView::ERROR;
    }
    //$this->dispatcher->notify(new sfEvent($this, 'import.start'));

    $this->importer = agStaffImportNormalization::getInstance(NULL, agEventHandler::EVENT_NOTICE);

    $this->importer->processXlsImportFile($this->importPath);

    $left = 1;
    while ($left > 0) {
      $left = $this->importer->processBatch();
      // print_r($left);
    }
    $iterData = $this->importer->getIterData();
    $this->totalRecords = $iterData['fetchCount'];
    $this->successful = $iterData['processedSuccessful'];
    $this->failed = $iterData['processedFailed'] + $iterData['tempErrCt'];
    $this->unprocessed = $iterData['unprocessed'];

    // Report elapsed time
    $this->endTime = microtime(true);
    $time = mktime(0, 0, round(($this->endTime - $this->startTime), 0), 0, 0, 2000);
    $this->importTime = date("H:i:s", $time);

    // Get the memory usage
    $peakMemory = $this->importer->getPeakMemoryUsage();

    // Format memory
    $bytes = array('KB', 'KB', 'MB', 'GB', 'TB');
    if ($peakMemory <= 999) {
      $peakMemory = 1;
    }
    for ($i = 0; $peakMemory > 999; $i++) {
      $peakMemory /= 1024;
    }
    $this->peakMemory = ceil($peakMemory) . " " . $bytes[$i];

    // close out import components and create an xls if needed
    $this->importer->concludeImport();
    $downloadFile = $this->importer->getUnprocessedXLS();
    $this->unprocessedXLS = $downloadFile;
  }

  public function executeDownload(sfWebRequest $request)
  {
    // being sure no other content wil be output
    $this->setLayout(false);
    //sfConfig::set('sf_web_debug', false);

    $fileName = preg_replace("/\.zip/", "", $request->getParameter('filename'));

    $filePath = sfConfig::get('sf_root_dir') . DIRECTORY_SEPARATOR
        . 'data/downloads' . DIRECTORY_SEPARATOR
        . $fileName . '.zip';


    // check if the file exists
    $this->forward404Unless(file_exists($filePath));

    // Make sure the browser doesn't try to deliver a chached version
    $this->getResponse()->setHttpHeader("Pragma", "public");
    $this->getResponse()->setHttpHeader("Expires", "0");
    $this->getResponse()->setHttpHeader("Cache-Control",
                                        "must-revalidate, post-check=0, pre-check=0");

    // Provide application and file info headers
    $this->getResponse()->setHttpHeader("Content-Type", "application/zip");
    $this->getResponse()->setHttpHeader("Content-Disposition",
                                        'attachment; filename=' . $fileName . '.zip');
    $this->getResponse()->setHttpHeader("Content-Transfer-Encoding", "binary");
    $this->getResponse()->setHttpHeader("Content-Length", "" . filesize($filePath));

    $this->getResponse()->sendHttpHeaders();
    $this->getResponse()->setContent(readfile($filePath));
    $this->getResponse()->send();


    return sfView::NONE;

  }

}
