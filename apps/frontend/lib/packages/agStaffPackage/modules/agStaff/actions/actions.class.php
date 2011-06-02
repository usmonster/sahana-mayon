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

//  /**
//   *
//   * @param sfWebRequest $request
//   * generates and passes a new scenario form to the view
//   */
//  public function executeGrouptype(sfWebRequest $request)
//  {
//    $this->ag_facility_group_types = Doctrine_Core::getTable('agFacilityGroupType')
//        ->createQuery('a')
//        ->execute();
//    $this->grouptypeform = new agFacilityGroupTypeForm();
//  }

  public function executeList(sfWebRequest $request)
  {
    $this->status = 'active';
    $this->sort = null;
    $this->order = null;
    $this->limit = null;

    //the next few lines could be abstracted to agActions as they are request params that may be
    //used for any list
    if ($request->getGetParameter('status'))
      $this->status = $request->getGetParameter('status');
    if ($request->getGetParameter('sort'))
      $this->sort = $request->getGetParameter('sort');
    if ($request->getGetParameter('order'))
      $this->order = $request->getGetParameter('order');
    if ($request->getGetParameter('limit'))
      $this->limit = $request->getGetParameter('limit');

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

//    if(apc_exists('staffArray')){
//      $staffArray = apc_fetch('staffArray');
//    }
//    else{
    $staffArray = agListHelper::getStaffList(null, $this->status, $this->sort, $this->order,
                                             $this->limit);
//      apc_store('staffArray', $staffArray);
//    }  this will not work currently on update, there needs to be a hook/callback

    $this->pager = new agArrayPager(null, 10);
    $this->pager->setResultArray($staffArray);

    $this->pager->setPage($this->getRequestParameter('page', 1));
    $this->pager->init();


    $this->ag_phone_contact_types = Doctrine::getTable('agPhoneContactType')
        ->createQuery('c')
        ->execute();
    $this->ag_email_contact_types = Doctrine::getTable('agEmailContactType')
        ->createQuery('d')
        ->execute();
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
//    $query = Doctrine::getTable('agStaff')
//            ->createQuery('a')
//            ->select(
//                'p.*,
//                  st.*,
//                  ps.*,
//                  s.*,
//                  pn.*,
//                  n.*,
//                  e.*,
//                  lang.*,
//                  religion.*,
//                  namejoin.*,
//                  name.*,
//                  nametype.*'
//            )
//            ->from(
//                'agStaff st,
//                  st.agPerson p,
//                  p.agPersonSex ps,
//                  ps.agSex s,
//                  p.agPersonMjAgNationality pn,
//                  pn.agNationality n,
//                  p.agEthnicity e,
//                  p.agLanguage lang,
//                  p.agReligion religion,
//                  p.agPersonMjAgPersonName namejoin,
//                  namejoin.agPersonName name,
//                  name.agPersonNameType nametype'
//    );
    //$this->pager = new sfDoctrinePager('agStaff', 1);
    //if we have exceucted a search
//    if ($request['query']) {
//      $lqResults = Doctrine_core::getTable('agStaff')->getForLuceneQuery($request['query']);
//
//      $i = 0;
//      $lqIds = array();
//
//      foreach ($lqResults as $lqResult) {
//        $lqIds[$i] = $lqResult->getId();
//        $i++;
//      }
//
//      if (count($lqIds) > 0) {
//        $q = Doctrine::getTable('agStaff')
//                ->createQuery('a')
//                ->select('s.*')
//                ->from('agStaff s')
//                ->where('s.id IN (' . implode(',', $lqIds) . ')');
//        $this->pager->setQuery($q);
//        $this->pager->setPage($request->getParameter('page', 1));
//        $this->pager->init();
//
//        $this->query = $request['query'];
//      }
//    } else {
//      if ($request->getParameter('sort')) {
//        if (substr($request->getParameter('sort'), 0, 11) == 'person_name') {
//          $nameId = substr($request->getParameter('sort'), 12);
//          $sortOrder = $request->getParameter('order', 'DESC');
//          $this->pager->setQuery($query->orderBy('namejoin.person_name_type_id = ' . $nameId . ' ' . $sortOrder . ', person_name ' . $sortOrder));
//        } else {
//          $this->pager->setQuery($query->orderBy($request->getParameter('sort', 'person_name') . ' ' . $request->getParameter('order',
//                      'DESC')));
//        }
//        //$this->sortAppend = $sortOrder;
//      } else {
//        //$this->pager->setQuery(Doctrine::getTable('agPerson')->createQuery('a'));
//        $this->pager->setQuery($query);
//      }
//      $this->pager->setPage($request->getParameter('page', 1));
//      $this->pager->init();
//    }


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
  public function executeExport()
  {
    $staffExporter = new agStaffExporter();
    $exportResponse = $staffExporter->export();
    // Free up some memory by getting rid of the agFacilityExporter object.
    unset($staffExporter);
    $this->getResponse()->setHttpHeader('Content-Type', 'application/vnd.ms-excel');
    $this->getResponse()->setHttpHeader('Content-Disposition',
                                        'attachment;filename="' . $exportResponse['fileName'] . '"');

    $exportFile = file_get_contents($exportResponse['filePath']);

    $this->getResponse()->setContent($exportFile);
    $this->getResponse()->send();
    unlink($exportResponse['filePath']);

    $this->redirect('staff/index');
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
    //    $this->timer = time();

    $uploadedFile = $_FILES['import'];

    print("<pre>" . print_r($_FILES, true) . "</pre>");

    $this->importPath = sfConfig::get('sf_upload_dir') . DIRECTORY_SEPARATOR . $uploadedFile['name'];

    print("<pre>Move {$uploadedFile['tmp_name']} to " . $this->importPath . "</pre>");
    if (!move_uploaded_file($uploadedFile['tmp_name'], $this->importPath)) {
      print("<pre>Failed to move {$uploadedFile['tmp_name']} to ".$this->importPath."</pre>");
      //return sfView::ERROR;
    }


    // fires event so listener will process the file (see ProjectConfiguration.class.php)
    //$this->dispatcher->notify(new sfEvent($this, 'import.staff_file_ready'));
    // TODO: eventually use this ^^^ to replace this vvv.

    $this->importer = agStaffImportNormalization::getInstance(NULL, agEventHandler::EVENT_DEBUG);
    
    $this->importer->processXlsImportFile($this->importPath);

    $left = 1;
    while ($left > 0) {
      $left = $this->importer->processBatch();
      print_r($left);
    }
    $this->importer->concludeImport();

    // removes the file from the server
    //unlink($this->importPath);
    
    //$this->dispatcher->notify(new sfEvent($this, 'import.start'));
    //unset($this->importer);
    //$this->timer = (time() - $this->timer);
  }

}
