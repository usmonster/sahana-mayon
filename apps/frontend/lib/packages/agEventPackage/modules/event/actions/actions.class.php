<?php

/**
 * extends agActions for event
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author Shirley Chan, CUNY SPS
 * @author Charles Wisniewski, CUNY SPS
 * @author Nils Stolpe, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
class eventActions extends agActions
{

  public static $event_id;
  public static $event_name;
  public static $event;
  protected $searchedModels = array('agEventStaff');

  /**
   * Import Replies is used in event messaging to receive input from a messaging vendor 
   * in the form of a spreadsheet with responses of Yes or No to messages
   * @param sfWebRequest $request
   * @return results
   */
  
  public function executeImportreplies(sfWebRequest $request)
  {
     $uploadedFile = $_FILES['import'];

    $importPath = sfConfig::get('sf_upload_dir') . DIRECTORY_SEPARATOR . $uploadedFile['name'];
    if (!move_uploaded_file($uploadedFile['tmp_name'], $importPath)) {
      //return sfView::ERROR;
    }
    $this->importPath = $importPath;

    // fires event so listener will process the file (see ProjectConfiguration.class.php)
    $this->dispatcher->notify(new sfEvent($this, 'import.staff_file_ready'));
    // TODO: eventually use this ^^^ to replace this vvv.

    //$import = new agStaffImportNormalization(NULL, agEventHandler::EVENT_DEBUG);
    //$import->importStaffFromExcel($this->importPath);
    //$import->processBatch();
    //$import->processBatch();

    // removes the file from the server
    //unlink($this->importPath);
    
    $this->renderPartial('global/Header');

    unset($import);
  }
  
  /**
   * Displays the index page for the event module.
   *
   * Users will see a list of existing events and be given the option to create
   * a new event from a list of existing scenarios.
   *
   * @param sfWebRequest $request
   * */
  public function executeIndex(sfWebRequest $request)
  {
    $this->scenarioForm = new sfForm();
    $this->scenarioForm->setWidgets(
        array(
          'ag_scenario_list' => new sfWidgetFormDoctrineChoice(
              array('multiple' => false, 'model' => 'agScenario')
          )
        )
    );


    $this->scenarioForm->getWidgetSchema()->setLabel('ag_scenario_list', false);
    $this->ag_events = agDoctrineQuery::create()
        ->select('a.*')
        ->from('agEvent a')
        ->execute();
  }

  public function executeFacilityresource(sfWebRequest $request)
  {
    $this->setEventBasics($request);
    $this->xmlHttpRequest = $request->isXmlHttpRequest();
    // This is the event_facility_resource.
    $this->event_facility_resource = agDoctrineQuery::create()
            ->select()
            ->from('agEventFacilityResource')
            ->where('id = ?', $request->getParameter('eventFacilityResourceId'))
            ->execute()->getFirst();
    $groupIds = agDoctrineQuery::create()
        ->select('id')
        ->from('agEventFacilityGroup')
        ->where('event_id = ?', $this->event_id)
        ->execute(array(), 'single_value_array');
    // This is the actual facility resource that will have access to names and other information.
    $this->facility_resource = agDoctrineQuery::create()
            ->select('')
            ->from('agFacilityResource')
            ->where('id = ?', $this->event_facility_resource['facility_resource_id'])
            ->execute()->getFirst();
    $this->facilityResourceActivationTimeForm = new agSingleEventFacilityResourceActivationTimeForm(); //new agFacilityResourceAcvitationForm($this->event_facility_resource);
    $this->facilityResourceActivationTimeForm->setDefault(
        'event_facility_resource_id', $this->event_facility_resource['id']
    );
  }

  /**
   * the event/deploy action provides a user with pre-deployment check information and the ability
   * to deploy an event if a scenario was given.
   * @param sfWebRequest $request
   */
  public function executeDeploy(sfWebRequest $request)
  {
    $this->setEventBasics($request);
    $this->scenario_id = agDoctrineQuery::create()
        ->select('scenario_id')
        ->from('agEventScenario')
        ->where('event_id = ?', $this->event_id)
        ->execute(array(), Doctrine_CORE::HYDRATE_SINGLE_SCALAR);
    if ($this->scenario_id) {
      $this->scenarioName = Doctrine::getTable('agScenario')
              ->findByDql('id = ?', $this->scenario_id)
              ->getFirst()->scenario;

      if ($request->isMethod(sfRequest::POST)) {
        agEventMigrationHelper::migrateScenarioToEvent($this->scenario_id, $this->event_id);
        $this->redirect('event/active?event=' . urlencode($this->event_name));
      } else {
        $this->checkResults = agEventMigrationHelper::preMigrationCheck($this->scenario_id);
        $this->getResponse()->setTitle('Sahana Agasti ' . $this->event_name . ' Deploy');
      }
    } else {
      $this->forward404('you cannot deploy an event without a scenario.');
    }
  }

  /**
   * setEventBasics sets up basic information used across most event actions
   * @param sfWebRequest $request
   */
  private function setEventBasics(sfWebRequest $request)
  {
//    if ($request->getParameter('id')) {
//      $this->event_id = $request->getParameter('id');
//      if ($this->event_id != "") {
//        $this->event_name = Doctrine_Core::getTable('agEvent')
//                ->findByDql('id = ?', $this->event_id)
//                ->getFirst()->getEventName();
//      }
//      //TODO step through to check and see if the second if is needed
//    }

    if ($request->getParameter('event')) {
      $this->event = agDoctrineQuery::create()
              ->select()
              ->from('agEvent')
              ->where('event_name = ?', urldecode($request->getParameter('event')))
              ->execute()->getFirst();

      $this->event_id = $this->event->id;
      $this->event_name = $this->event->event_name;
    }
  }

  /**
   * the meta action (event/meta) gives the user CRU functionality of event meta information
   * @param sfWebRequest $request
   */
  public function executeMeta(sfWebRequest $request)
  {
    $this->setEventBasics($request);
    //if someone is coming here from an edit context...
    if ($this->event_id != "") {
      $eventMeta = Doctrine::getTable('agEvent')
          ->findByDql('id = ?', $this->event_id)
          ->getFirst();
    } else {
      // ...if not.
      $eventMeta = null;
    }

    if ($request->isMethod(sfRequest::POST) && !$request->getParameter('ag_scenario_list')) {
      //if someone has posted, but is not creating an event from a scenario.
      $this->metaForm = new PluginagEventDefForm($eventMeta);
      $this->metaForm->bind(
          $request->getParameter($this->metaForm->getName()),
                                 $request->getFiles($this->metaForm->getName())
      );
      if ($this->metaForm->isValid()) {

        $ag_event = $this->metaForm->save();

        $eventStatusObject = agDoctrineQuery::create()
                ->from('agEventStatus a')
                ->where('a.id =?', $ag_event->getId())
                ->execute()->getFirst();

        $ag_event_status = isset($eventStatusObject) ? $eventStatusObject : new agEventStatus();
        $defaultEventStatusType = agEventHelper::returnDefaultEventStatus();
        $ag_event_status->setEventStatusTypeId($defaultEventStatusType);

        $ag_event_status->setEventId($ag_event->getId());
        $ag_event_status->time_stamp = new Doctrine_Expression('CURRENT_TIMESTAMP');
        $ag_event_status->save();

        //have to do this for delete also, i.e. delete the event_scenario object
        if ($request->getParameter('scenario_id') && $request->getParameter('scenario_id') != "") {
          //the way this is constructed we will always have a scenario_id
          $ag_event_scenario = new agEventScenario();
          $ag_event_scenario->setScenarioId($request->getParameter('scenario_id'));
          $ag_event_scenario->setEventId($ag_event->getId());
          $ag_event_scenario->save();
          $this->redirect('event/deploy?event=' . urlencode($ag_event->getEventName()));
        }
        $this->blackOutFacilities = agEventFacilityHelper::returnActivationBlacklistFacilities($ag_event->getId(),
                                                                                               $ag_event->getZeroHour());
        $this->redirect('event/active?event=' . urlencode($ag_event->getEventName()));
      }
    } elseif ($request->getParameter('ag_scenario_list')) {
      //get scenario information passed from previous form
      //we should save the scenario that this event is based on

      $this->scenario_id = $request->getParameter('ag_scenario_list');
      $this->scenarioName = Doctrine::getTable('agScenario')
              ->findByDql('id = ?', $this->scenario_id)
              ->getFirst()->scenario;

      $this->metaForm = new PluginagEventDefForm($eventMeta);
    }

    //p-code
    if (isset($eventMeta->event_name)) {
      $this->getResponse()->setTitle('Sahana Agasti ' . $this->event_name . ' Event Management');
    } else {
      $this->getResponse()->setTitle('Sahana Agasti: New Event');
    }
    //end p-code
  }

  /**
   * event/list shows a listing of events, this provides the list data to the listSuccess template
   * @param sfWebRequest $request
   */
  public function executeList(sfWebRequest $request)
  {
    $this->ag_events = agDoctrineQuery::create()
        ->select('a.*')
        ->from('agEvent a')
        ->execute();
  }

  public function executeExportcontacts(sfWebRequest $request)
  {
    $this->setEventBasics($request);

    $unAllocatedStaffStatus = agEventStaffHelper::returnDefaultEventStaffStatus();

    $eventStaff = agDoctrineQuery::create()
        ->select('es.id, ess.id, sr.id, s.id, p.entity_id')
        ->from('agEventStaff es')
        ->addFrom('es.agEventStaffStatus ess')
        ->addFrom('es.agStaffResource sr')
        ->addFrom('sr.agStaff s')
        ->addFrom('s.agPerson p')
        ->where('ess.staff_allocation_status_id = ?', $unAllocatedStaffStatus)
        ->andWhere('es.event_id = ?', $this->event_id)
        ->execute(array(), Doctrine_Core::HYDRATE_SCALAR);
    //get all event staff that are still marked as unavailable
    foreach ($eventStaff as $staffRecord) {
      $eventStaffEntities[$staffRecord['es_id']] = $staffRecord['p_entity_id'];
    }

    $staffMembers = agDoctrineQuery::create()
        ->from('agPerson a')
        ->whereIn('a.entity_id', $eventStaffEntities)
        ->execute();

    $nameTypes = Doctrine::getTable('agPersonNameType')
        ->createQuery('a')
        ->execute();
    $phoneTypes = Doctrine::getTable('agPhoneContactType')
        ->createQuery('a')
        ->execute();
    $emailTypes = Doctrine::getTable('agEmailContactType')
        ->createQuery('a')
        ->execute();
    $addressTypes = Doctrine::getTable('agAddressContactType')
        ->createQuery('a')
        ->execute();
    $languageFormats = Doctrine::getTable('agLanguageFormat')
        ->createQuery('a')
        ->execute();
    $row = 2;
    foreach ($staffMembers as $staffMember) {
      $headings = array('ID');
      $eventStaffIds = array_keys($eventStaffEntities, $staffMember->entity_id);
      $eventStaffId = $eventStaffIds[0];
      //the staffMembers array should be keyed on staff member.  Also, for speed we should directly
      //access as much of this as possible, or use helpers.
      $content[$row] = array('ID' => $eventStaffId);

      foreach ($nameTypes as $nameType) {
        $headings[] = ucwords($nameType->person_name_type) . ' Name';
        $j = Doctrine::getTable('AgPersonMjAgPersonName')->findByDql('person_id = ? AND person_name_type_id = ?',
                                                                     array($staffMember->id, $nameType->id))->getFirst();
        $content[$row][ucwords($nameType->person_name_type) . ' Name']
            = ($j ? $j->getAgPersonName()->person_name : '');
      }
      $h = array('Sex', 'Date of Birth', 'Profession', 'Nationality',
        'Ethnicity', 'Religion', 'Marital Status');
      $headings = array_merge($headings, $h);
      foreach ($h as $head) {
        //if($head == 'Date of Birth') $head = 'Person Date Of Birth';
        $table = ($head == 'Date of Birth') ?
            'agPersonDateOfBirth' : 'ag' . str_replace(' ', '', $head);
        //$table = 'ag' . str_replace(' ', '', $head);
        $fetcher = 'get' . ucwords($table);
        $column = sfInflector::underscore(str_replace(' ', '', ucwords($head)));
        $objects = $staffMember->$fetcher();
        $i = 1;
        $c = count($objects);
        $results = null;
        if ($objects instanceof Doctrine_Collection) {
          foreach ($objects as $object) {
            if ($i <> $c) {
              $results = $results . $object->$column . "\n";
            } else {
              $results = $results . $object->$column;
            }
            $i++;
          }
        } else {
          $results = $results . $objects->$column;
        }
        $values[$head] = ucwords($results);
      }
      $content[$row] = array_merge($content[$row], $values);
      foreach ($phoneTypes as $phoneType) {
        $headings[] = ucwords($phoneType->phone_contact_type) . ' Phone';
        $j = Doctrine::getTable('AgEntityPhoneContact')
                ->findByDql(
                    'entity_id = ? AND phone_contact_type_id = ?',
                    array($staffMember->entity_id, $phoneType->id)
                )->getFirst();
        $content[$row][ucwords($phoneType->phone_contact_type) . ' Phone'] =
            (
            $j ? preg_replace(
                    $j
                        ->getAgPhoneContact()
                        ->getAgPhoneFormat()
                        ->getAgPhoneFormatType()->match_pattern,
                    $j
                        ->getAgPhoneContact()
                        ->getAgPhoneFormat()
                        ->getAgPhoneFormatType()->replacement_pattern,
                    $j
                        ->getAgPhoneContact()->phone_contact) :
                ''
            );
      }

      foreach ($emailTypes as $emailType) {
        $headings[] = ucwords($emailType->email_contact_type) . ' Email';
        $j = Doctrine::getTable('AgEntityEmailContact')
            ->findByDql(
                'entity_id = ? AND email_contact_type_id = ?',
                array($staffMember->entity_id, $emailType->id)
            )
            ->getFirst();
        $content[$row][ucwords($emailType->email_contact_type) . ' Email'] =
            ($j ? $j->getAgEmailContact()->email_contact : '');
      }

      foreach ($addressTypes as $addressType) {
        $headings[] = ucwords($addressType->address_contact_type) . ' Address';
        $j = Doctrine::getTable('AgEntityAddressContact')
            ->findByDql(
                'entity_id = ? AND address_contact_type_id = ?',
                array($staffMember->entity_id, $addressType->id)
            )
            ->getFirst();
        $tempContainer = array();
        if ($j) {
          foreach ($j->getAgAddress()->getAgAddressStandard()->getAgAddressFormat()
          as $addressFormat) {
            foreach ($j->getAgAddress()->getAgAddressMjAgAddressValue() as $mj) {
              if ($addressFormat->getAgAddressElement()->id ==
                  $mj->getAgAddressValue()->address_element_id) {
                $i = 1;
                $c = count($j);
                if (isset($tempContainer['Line ' . $addressFormat->line_sequence])) {
                  $tempContainer['Line ' . $addressFormat->line_sequence] =
                      $tempContainer['Line ' . $addressFormat->line_sequence] .
                      $addressFormat->pre_delimiter . $mj->getAgAddressValue()->value .
                      $addressFormat->post_delimiter;
                } else {
                  $tempContainer['Line ' . $addressFormat->line_sequence] =
                      $addressFormat->pre_delimiter .
                      $mj->getAgAddressValue()->value .
                      $addressFormat->post_delimiter;
                }
              }
            }
          }
        } else {
          $tempContainer[ucwords($addressType->address_contact_type) . ' Address'] = null;
        }
        $content[$row][ucwords($addressType->address_contact_type) . ' Address'] =
            implode("\n", $tempContainer);
      }
      $headings[] = 'Language';

      foreach ($languageFormats as $languageFormat) {
        $headings[] = ucwords($languageFormat->language_format);
        $competencies = null;
        $languages = null;
        $c = count($staffMember->getAgPersonMjAgLanguage());
        $i = 1;
        foreach ($staffMember->getAgPersonMjAgLanguage() as $languageJoin) {
          $compQuery = Doctrine::getTable('agPersonLanguageCompetency')->createQuery('a')
              ->select('a.*')
              ->from('agPersonLanguageCompetency a')
              ->where('a.person_language_id = ?', $languageJoin->id)
              ->andWhere('a.language_format_id = ?', $languageFormat->id);
          if ($i <> $c) {
            $languages = $languages . $languageJoin->getAgLanguage()->language . "\n";
          } else {
            $languages = $languages . $languageJoin->getAgLanguage()->language;
          }
          if ($compEx = $compQuery->fetchOne()) {
            if ($i <> $c) {
              $competencies =
                  $competencies . $compEx->getAgLanguageCompetency()->language_competency . "\n";
            } else {
              $competencies =
                  $competencies . $compEx->getAgLanguageCompetency()->language_competency;
            }
          } else {
            $competencies = $competencies . "\n";
          }
          $i++;
        }
        $content[$row]['Language'] = $languages;
        $content[$row][ucwords($languageFormat->language_format)] = $competencies;
      }

      $content[$row]['Created'] = $staffMember->created_at;
      $content[$row]['Updated'] = $staffMember->updated_at;
      array_push($headings, 'Created', 'Updated');
      $row++;
    }

    require_once 'PHPExcel/Cell/AdvancedValueBinder.php';
    PHPExcel_Cell::setValueBinder(new PHPExcel_Cell_AdvancedValueBinder());
    $objPHPExcel = new sfPhpExcel();
    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $objPHPExcel->setActiveSheetIndex(0);
    // Set properties
    $objPHPExcel->getProperties()->setCreator("Agasti 2.0");
    $objPHPExcel->getProperties()->setLastModifiedBy("Agasti 2.0");
    $objPHPExcel->getProperties()->setTitle("Staff List");
    // Set default font
    $objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setName('Times');
    $objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setSize(12);
    foreach ($headings as $hKey => $heading) {
      $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($hKey, 1)->setValue($heading);
      foreach ($content as $rowKey => $rowValue) {

        foreach ($rowValue as $eKey => $entry) {
          if ($eKey == $heading) {
            $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($hKey, $rowKey)->setValue($entry);
          }
        }
      }
    }


    //This should be assigning an auto-width to each column that will fit the largest data in it. For some reason, it's not working.
    $highestColumn = $objPHPExcel->getActiveSheet()->getHighestColumn();
    $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);

    for ($i = $highestColumnIndex; $i >= 0; $i--) {
      $objPHPExcel
          ->getActiveSheet()
          ->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($i))
          ->setAutoSize(true);
    }
    // Save Excel 2007 file

    $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
    $todaydate = date("d-m-y");
    $todaydate = $todaydate . '-' . date("H-i-s");
    $filename = 'Staff';
    $filename = $filename . '-' . $todaydate;
    $filename = $filename . '.xls';
    $filePath = realpath(sys_get_temp_dir()) . '/' . $filename;
    $objWriter->save($filePath);
    $foo = new finfo();
    $bar = $foo->file($filePath, FILEINFO_MIME_TYPE);

    $this->getResponse()->setHttpHeader('Content-Type', 'application/vnd.ms-excel');
    $this->getResponse()->setHttpHeader(
        'Content-Disposition', 'attachment;filename="' . $filename . '"'
    );

    $exportFile = file_get_contents($filePath);

    //$this->getResponse()->setContent($objWriter);
    //$this->getResponse()->setContent($exportFile);

    $this->getResponse()->setHeaderOnly(true);
    $this->getResponse()->send();
    $objWriter->save('php://output');
    unlink($filePath);
    
    $this->exportComplete = sizeof($content);
    $this->redirect('event/messaging?event=' . urlencode($this->event_name)); //need to pass in event id
  }

  /**
   * provides event staff pool management functions.
   * @param sfWebRequest $request request coming from web
   */
  public function executeStaffpool(sfWebRequest $request)
  {
    $this->setEventBasics($request);

    $this->saved_searches = $existing = Doctrine_Core::getTable('AgScenarioStaffGenerator')->findAll();

    //get the possible filters from our request eg. &fr=1&type=generalist&org=volunteer
    $filters = array();
    foreach ($request->getParameterHolder() as $parameter => $filter) {
      if ($parameter == 'fr') {
        $filters['essh.event_facility_resource_id'] = $filter;
      }
      if ($parameter == 'st') {
        $filters['sr.staff_resource_type_id'] = $filter;
      }
      if ($parameter == 'so') {
        $filters['sr.organization_id'] = $filter;
      }
    }
    //set up inputs for filter form
    $inputs = array(
      'st' => new sfWidgetFormDoctrineChoice(
          array(
            'model' => 'agStaffResourceType',
            'label' => 'Staff Type',
            'add_empty' => true
          )
      ),
      // 'class' => 'filter')),
      'so' => new sfWidgetFormDoctrineChoice(
          array(
            'model' => 'agOrganization',
            'method' => 'getOrganization',
            'label' => 'Staff Organization',
            'add_empty' => true
          )
      ),
      //, 'class' => 'filter'))
      'fr' => new sfWidgetFormDoctrineChoice(
          array(
            'model' => 'agEventFacilityResource',
            'label' => 'Facility Resource',
            'add_empty' => true
          )
      )
    );
    /** @todo set defaults from the request */
    $this->filterForm = new sfForm(null, array(), false);
    //$this->filterForm->getWidgetSchema()->setNameFormat('filter[%s]');
    foreach ($inputs as $key => $input) {
      $input->setAttribute('class', 'filter');
      $this->filterForm->setWidget($key, $input);
    }

    //begin construction of query used for listing
    $query = agDoctrineQuery::create()
        ->select(
            'es.id,
                  essh.id,
                  esh.event_facility_resource_id,
                  efr.facility_resource_id,
                  fr.facility_id,
                  f.facility_name,
                  sr.id,
                  srt.staff_resource_type,
                  o.organization,
                  sr.staff_resource_status_id,
                  srs.staff_resource_status,
                  p.id,
                  ess.staff_allocation_status_id'
        )//, sas.staff_allocation_status')
        //maybe we should only get the id since it's needed for dropdown
        ->from(
            'agEventStaff es,
              es.agEventStaffShift essh,
              essh.agEventShift esh,
              esh.agEventFacilityResource efr,
              efr.agFacilityResource fr,
              fr.agFacility f,
              es.agStaffResource sr,
              sr.agStaffResourceType srt,
              sr.agOrganization o,
              sr.agStaffResourceStatus srs,
              sr.agStaff s,
              s.agPerson p,
              es.agEventStaffStatus ess'
        )
        //ess.agStaffAllocationStatus sas')
        ->where('es.event_id = ?', $this->event_id);

    if (sizeof($filters) > 0) {
      foreach ($filters as $field => $filter) {
        $query->andWhere($field . ' = ?', $filter);
      }
    }

    if ($request->isMethod(sfRequest::POST)) {
      if ($request->getParameter('event_status')) {
        foreach ($request->getParameter('event_status') as $event_status) {
          //this is inefficient here as we are executing the same query in a loop to get associated objects
//check to see if this staff member already has a status set.
          $eventStaffStatusObject = agDoctrineQuery::create()
              ->from('agEventStaffStatus a')
              ->where('a.event_staff_id =?', $event_status['event_staff_id'])
              ->fetchOne();
//NEW
          if (!$eventStaffStatusObject) {
            $eventStaffStatusObject = new agEventStaffStatus();
            $eventStaffStatusObject->time_stamp = date('Y-m-d H:i:s', time());
            $eventStaffStatusObject->event_staff_id = $event_status['event_staff_id'];
            $eventStaffStatusObject->staff_allocation_status_id = $event_status['status'];
            $eventStaffStatusObject->save();
          } else {
//UPDATE  ONLY IF staff_allocation_status has changed
            //technically this should always be an update, by the time a staff member is in an event
            if ($eventStaffStatusObject->staff_allocation_status_id != $event_status['status']) {
              $eventStaffStatusObject->time_stamp = date('Y-m-d H:i:s', time());
              //$eventStaffStatusObject->event_staff_id = $event_status['event_staff_id'];
              $eventStaffStatusObject->staff_allocation_status_id = $event_status['status'];
              $eventStaffStatusObject->save();
            }
          }
          //we should throw a check here to see if the most recent status is the same as incoming
        }
      }
    }
    $eventStaff = array();
    $person_array = array();
    $ag_event_staff = $query->execute(array(), Doctrine_Core::HYDRATE_SCALAR);
    foreach ($ag_event_staff as $key => $value) {
      $person_array[] = $value['p_id'];
      //$remapped_array[$ag_event_staff['es_id']] = $
    }
    $names = new agPersonNameHelper($person_array); //we need to get persons from the event staff ids that are returned here
    $person_names = $names->getPrimaryNameByType();

    //$names->
    //this is the desired format of the return array:
    $this->widget = new sfForm();
    $this->widget->getWidgetSchema()->setNameFormat('event_status[][%s]');
    $this->widget->setWidget('status',
                             new sfWidgetFormDoctrineChoice(array('model' => 'agStaffAllocationStatus', 'method' => 'getStaffAllocationStatus')));

    //the agStaffAllocationStatus ID coming from each of the selections will be saved to ag_Event_staff_status.
    $this->widget->getWidgetSchema()->setLabel('status', false);
    /** @todo set defaults for each status drop down from the web request */
    $this->form_action = 'event/staffpool?event=' . $this->event_name;
    $result_array = array();
    foreach ($ag_event_staff as $staff => $value) {
      $result_array[] = array(
        'fn' => $person_names[$value['p_id']]['given'],
        'ln' => $person_names[$value['p_id']]['family'],
        'organization_name' => $value['o_organization'],
        'status' => $value['srs_staff_resource_status'],
        'type' => $value['srt_staff_resource_type'],
        'facility' => $value['f_facility_name'],
        'es_id' => $value['es_id'],
        'ess_staff_allocation_status_id' => $value['ess_staff_allocation_status_id']
      );
    }

    $this->ag_event_staff = $result_array;
//    foreach ($this->ag_event_staff as $eventFacilityGroup) {
//      $tempArray = $this->groupResourceQuery($eventFacilityGroup->id);
//      foreach ($tempArray as $ta) {
//        array_push($eventStaff, $ta);
//      }
//    }
    //$this->facilityGroupArray = $facilityResourceArray;
    $this->pager = new agArrayPager(null, 10);


    $this->pager->setResultArray($this->ag_event_staff);
    $this->pager->setPage($this->getRequestParameter('page', 1));
    $this->pager->init();
    //set up the widget for use in the ' list form '
    //p-code
    $this->getResponse()->setTitle('Sahana Agasti ' . $this->event_name . ' Staff');
    //end p-code
  }

  /**
   * provide event shift CRUD
   * @param sfWebRequest $request
   */
  public function executeShifts(sfWebRequest $request)
  {
    $this->setEventBasics($request);
//CREATE  / UPDATE
    if ($request->isMethod(sfRequest::POST)) {
      if ($request->getParameter('shiftid') && $request->getParameter('shiftid') == 'new') {
        $this->eventshiftform = new agEventShiftForm();
      } elseif ($request->getParameter('shiftid') && is_numeric($request->getParameter('shiftid'))) {
        $ag_event_shift = Doctrine_Core::getTable('agEventShift')
            ->findByDql('id = ?', $request->getParameter('shiftid'))
            ->getFirst();
        $this->eventshiftform = new agEventShiftForm($ag_event_shift);
      } elseif ($request->getParameter('delete')) {
//DELETE
      }
      $this->eventshiftform->bind($request->getParameter($this->eventshiftform->getName()),
                                                         $request->getFiles($this->eventshiftform->getName()));
      $formvalues = $request->getParameter($this->eventshiftform->getName());
      if ($this->eventshiftform->isValid()) { //form is not passing validation because the bind is failing?
        $ag_event_shift = $this->eventshiftform->save();
        $this->generateUrl('event_shifts',
                           array('module' => 'event',
          'action' => 'shifts', 'event' => $this->event_name, 'shiftid' => $ag_event_shift->getId()));
      }
      $this->redirect('event/shifts?event=' . urlencode($this->event_name)); //need to pass in event id
    } else {
//READ
      if ($request->getParameter('shiftid') && $request->getParameter('shiftid') == 'new') {
        $this->eventshiftform = new agEventShiftForm();
        $this->setTemplate('editshift');
      } elseif ($request->getParameter('shiftid') && is_numeric($request->getParameter('shiftid'))) {

        $ag_event_shift = Doctrine_Core::getTable('agEventShift')
            ->findByDql('id = ?', $request->getParameter('shiftid'))
            ->getFirst();

        $this->eventshiftform = new agEventShiftForm($ag_event_shift);
        $this->setTemplate('editshift');
      } else {
//LIST
        $query = agDoctrineQuery::create()
            ->select('es.*, efr.*, efg.id, efg.event_facility_group, e.*, af.*, fr.*, frt.*, srt.*, ess.*, est.*')
            ->from('agEventShift as es')
            ->leftJoin('es.agEventStaffShift ess')
            ->leftJoin('ess.agEventStaff est')
            ->leftJoin('es.agStaffResourceType srt')
            ->leftJoin('es.agEventFacilityResource AS efr')
            ->leftJoin('efr.agEventFacilityGroup AS efg')
            ->leftJoin('efr.agFacilityResource fr, fr.agFacility af, fr.agFacilityResourceType frt')
            ->leftJoin('efg.agEvent AS e')
            ->where('e.id = ?', $this->event_id);

        /**
         * Create pager
         */
        $this->pager = new sfDoctrinePager('agEventShift', 20);

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
    $this->getResponse()->setTitle('Sahana Agasti ' . $this->event_name . ' Shifts');
    //end p-code
  }

  /**
   * provides basic information about an active event, the template gives links to event management
   * @param sfWebRequest $request
   */
  public function executeActive(sfWebRequest $request)
  {
    $this->setEventBasics($request);
    $availableStaffStatus = agEventStaffHelper::returnAvailableEventStaffStatus();

    $eventAvailableStaff = agDoctrineQuery::create()
        ->select('es.id, COUNT(es.id), ess.id, sr.id')
        ->from('agEventStaff es')
        ->addFrom('es.agEventStaffStatus ess')
        ->where('ess.staff_allocation_status_id = ?', $availableStaffStatus)
        ->andWhere('es.event_id = ?', $this->event_id)
        ->execute(array(), Doctrine_Core::HYDRATE_SCALAR);
    $this->eventAvailableStaff = $eventAvaibleStaff->es_count;
    
    $this->eventStaffPool = agDoctrineQuery::create()
          ->select('COUNT(es.id)')
          ->from('agEventStaff es')
          ->where('es.event_id = ?', $this->event_id)
          ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
      if (empty($this->eventStaffPool)) {
        $this->eventStaffPool = 0;
      }
      
      $this->event_description = agDoctrineQuery::create()
          ->select('ed.description, e.id')
          ->from('agEvent e')
          ->addFrom('e.agEventDescription ed')
          ->where('e.id = ?', $this->event_id)
          ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
      if (empty($this->event_description)) {
        $this->event_description = 0;
      }          

      
      
    //p-code
    $this->getResponse()->setTitle('Sahana Agasti ' . $this->event_name . ' Management');
    //end p-code
  }

  /**
   * provides basic staff management in an event
   * @param sfWebRequest $request
   */
  public function executeStaff(sfWebRequest $request)
  {
    $this->setEventBasics($request);

    //p-code
    $this->getResponse()->setTitle('Sahana Agasti ' . $this->event_name . ' Staff');
    //end p-code
  }

  /**
   * provides a list of facility groups in an event, or all events
   * also lists the facility resources in those facility groups and provides the ability to modify
   * each resource's status
   * @param sfWebRequest $request
   */
  public function executeListgroups(sfWebRequest $request)
  {
    if ($request->getParameter('event') != null && Doctrine::getTable('agEvent')->findByDql('where event_name = ?',
                                                                                            $request->getParameter('event'))->getFirst() == false) {
      $this->redirect('event/listgroups');
    }
    if ($request->getParameter('event') == null) {
      $this->missingEvent = true;
    }
    $this->setEventBasics($request);

    $query = agDoctrineQuery::create()
        ->select('efg.id')
        ->addSelect('efg.event_facility_group')
        ->addSelect('fgt.facility_group_type')
        ->addSelect('fgas.id')
        ->addSelect('fgas.facility_group_allocation_status')
        ->addSelect('ev.event_name')
        ->addSelect('count(efr.event_facility_group_id)')
        ->from('agEventFacilityGroup efg')
        ->innerJoin('efg.agEventFacilityGroupStatus efgs')
        ->innerJoin('efg.agFacilityGroupType fgt')
        ->innerJoin('efgs.agFacilityGroupAllocationStatus fgas')
        ->innerJoin('efg.agEvent ev')
        ->innerJoin('efg.agEventFacilityResource efr')
        ->where('EXISTS (
              SELECT s.id
                FROM agEventFacilityGroupStatus s
                WHERE s.event_facility_group_id = efgs.event_facility_group_id
                  AND s.time_stamp <= CURRENT_TIMESTAMP
                HAVING MAX(s.time_stamp) = efgs.time_stamp)')
        ->groupBy('efg.event_facility_group');
    // If the request has an event parameter, get only the agEventFacilityGroups for that event. Otherwise, all in the system will be returned.
    if ($this->event != "") {
      $query->andWhere('efg.event_id = ?', $this->event_id);
    }

    $facilityResourceArray = array();
    $this->facilityGroupArray = $query->execute(array(), Doctrine_Core::HYDRATE_SCALAR);

    foreach ($this->facilityGroupArray as $eventFacilityGroup) {
      $facilityResourceArray[$eventFacilityGroup['efg_id']] = $this->groupResourceQuery($eventFacilityGroup['efg_id']);
    }
    $this->facilityResourceArray = $facilityResourceArray;
    $this->pager = new agArrayPager(null, 10);

    if ($request->getParameter('sort') && $request->getParameter('order')) {
      $sortColumns = array(
        'group' => 'efg_event_facility_group',
        'type' => 'fgt_facility_group_type',
        'status' => 'fgas_facility_group_allocation_status',
        'count' => 'efr_count',
        'event' => 'ev_event_name');
      $sort = $sortColumns[$request->getParameter('sort')];
      agArraySort::$sort = $sort;
      usort($this->facilityGroupArray, array('agArraySort', 'arraySort'));
      if ($request->getParameter('order') == 'DESC') {
        $this->facilityGroupArray = array_reverse($this->facilityGroupArray);
      }
    }
    $this->pager->setResultArray($this->facilityGroupArray);
    $this->pager->setPage($this->getRequestParameter('page', 1));
    $this->pager->init();

    //p-code
    $this->getResponse()->setTitle('Sahana Agasti ' . $this->event_name . ' Facility Groups');
    //end p-code
  }

  public function executeEventfacilityresource(sfWebRequest $request)
  {
    // Load Table
    //
    // This first conditional check is for displaying the facility resource table for a specific facility group.
    // It is rendered inside of the expandable facility group table from listgroupsSuccess.
    // This should be the only use of a GET request.
    if ($request->isMethod(sfRequest::GET)) {
      $this->facilityResourceArray = $this->groupResourceQuery($request->getParameter('eventFacResId'));
      return $this->renderPartial('eventFacResTable',
                                  array('facilityResourceArray' => $this->facilityResourceArray));
    }
    // POST handles all the rest. Loading the forms to replace the TD html, saving the forms, etc.
    elseif ($request->isMethod(sfRequest::POST)) {
      $params = $request->getPostParameters();

      // Render the Forms
      if ($params['type'] == 'resourceStatus') {
        // This case is for loading the event-facility-resource-status form to replace the link that sumbitted
        // the request.
        $facilityResourceStatus = new agEventFacilityResourceStatus();
        $facilityResourceStatus['event_facility_resource_id'] = ltrim($params['id'], 'res_stat_id_');
        $facilityResourceStatus['facility_resource_allocation_status_id'] =
            agDoctrineQuery::create()
            ->select('id')
            ->from('agFacilityResourceAllocationStatus')
            ->where('facility_resource_allocation_status = ?', $params['current'])
            ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);

        $resourceStatusForm = new agTinyEventFacilityResourceStatusForm($facilityResourceStatus);
        $resourceStatusForm->getWidget('facility_resource_allocation_status_id')->setAttribute('class',
                                                                                               'inputGray submitTextToForm set100');

        return $this->renderPartial('global/includeForm',
                                    array(
          'form' => $resourceStatusForm,
          'set' => $params['type'],
          'id' => $params['id'],
          'url' => 'event/eventfacilityresource?eventFacilityResourceId=' . ltrim($params['id'],
                                                                                  'res_stat_id_')
            )
        );
      } elseIf ($params['type'] == 'resourceActivationTime') {
        // This case is for loading the even-facility-resource-activation-time form in place of the submittal link.
        if ($params['current'] != '----') {
          $eventFacilityResourceActivationTime = agDoctrineQuery::create()
              ->select()
              ->from('agEventFacilityResourceActivationTime')
              ->where('event_facility_resource_id = ?', ltrim($params['id'], 'res_act_id_'))
              ->fetchOne(array(), Doctrine_Core::HYDRATE_RECORD);
          $eventFacilityResourceActivationTime['activation_time'] = date('m/d/Y H:i',
                                                                         $eventFacilityResourceActivationTime['activation_time']);
        } else {
          $eventFacilityResourceActivationTime = new agEventFacilityResourceActivationTime();
          $eventFacilityResourceActivationTime['event_facility_resource_id'] = ltrim($params['id'],
                                                                                     'res_act_id_');
        }
        $eventFacilityResourceActivationTimeForm = new agTinyEventFacilityResourceActivationTimeForm($eventFacilityResourceActivationTime);
        $eventFacilityResourceActivationTimeForm->getWidget('activation_time')->setAttribute('class',
                                                                                             'inputGray submitTextToForm set110');

        return $this->renderPartial('global/includeForm',
                                    array('form' => $eventFacilityResourceActivationTimeForm,
          'set' => $params['type'],
          'id' => $params['id'],
          'url' => 'event/eventfacilityresource?eventFacilityResourceId=' . ltrim($params['id'],
                                                                                  'res_act_id_')
            )
        );
      }
      // Save Forms/Objects
      if ($request->getParameter('ag_event_facility_resource_status')) {
        // This first condition handles ag_event_facility_resource_status objects. No update here, only creation of new objects.
        $eventFacilityActivationStatus = new agEventFacilityResourceStatus();
        $eventFacilityActivationStatus['facility_resource_allocation_status_id'] = $params['ag_event_facility_resource_status']['facility_resource_allocation_status_id'];
        $eventFacilityActivationStatus['event_facility_resource_id'] = $params['ag_event_facility_resource_status']['event_facility_resource_id'];
        $eventFacilityActivationStatus['time_stamp'] = date('Y-m-d H:i:s', time());
        $eventFacilityActivationStatus->save();

        $refresh = agDoctrineQuery::create()
            ->select('facility_resource_allocation_status')
            ->from('agFacilityResourceAllocationStatus')
            ->where('id = ?',
                    $params['ag_event_facility_resource_status']['facility_resource_allocation_status_id'])
            ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
        return $this->renderText(json_encode(array('status' => 'success', 'refresh' => $refresh)));
      } elseIf ($request->getParameter('ag_event_facility_resource_activation_time')) {
        // This condition handes ag_event_facility_resource_activation_time objects. This is almost always
        // an update, unless the resource has no activation time already.
        $params['ag_event_facility_resource_activation_time']['activation_time'] = strtotime($params['ag_event_facility_resource_activation_time']['activation_time']);
        // Catch if an invalid date/time has been entered into the activation form and return response
        // text to be rendered inside the input if it is invalid.
        if ($params['ag_event_facility_resource_activation_time']['activation_time'] == false) {
//          return $this->renderText('Invalid Date-Time');
          return $this->renderText(json_encode(array('status' => 'failure', 'refresh' => 'Invalid Date-Time')));
        }
        // See if there's already and activation time set for this fac-res.
        $eventFacilityResourceActivationTime = agDoctrineQuery::create()
            ->select()
            ->from('agEventFacilityResourceActivationTime')
            ->where('event_facility_resource_id = ?',
                    $params['ag_event_facility_resource_activation_time']['event_facility_resource_id'])
            ->fetchOne(array(), Doctrine_Core::HYDRATE_RECORD);
        // If there isn't, create a new object, assign values.
        if ($eventFacilityResourceActivationTime == false) {
          $eventFacilityResourceActivationTime = new agEventFacilityResourceActivationTime();
          $eventFacilityResourceActivationTime['event_facility_resource_id'] = $params['ag_event_facility_resource_activation_time']['event_facility_resource_id'];
        }
        // In any case, set the time to whatever the new time is.
        $eventFacilityResourceActivationTime['activation_time'] = $params['ag_event_facility_resource_activation_time']['activation_time'];

        $eventFacilityResourceActivationTime->save();
        return $this->renderText(json_encode(array('status' => 'success', 'refresh' => date('m/d/Y H:i',
                                                                                            $params['ag_event_facility_resource_activation_time']['activation_time']))));
      }
    }
  }

  public function executeEventfacilitygroup(sfWebRequest $request)
  {
    // Get the incoming params.
    $params = $request->getPostParameters();

    if ($params['type'] == 'groupStatus') {
      // Build an agEventFacilityGroupStatus object from incoming params, then stick it in a form.
      $groupAllocationStatus = new agEventFacilityGroupStatus();
      $groupAllocationStatus->event_facility_group_id = ltrim($params['id'], 'group_id_');
      $groupAllocationStatus->facility_group_allocation_status_id =
          agDoctrineQuery::create()
          ->select('id')
          ->from('agFacilityGroupAllocationStatus')
          ->where('facility_group_allocation_status = ?', $params['current'])
          ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
      $groupAllocationStatus->time_stamp = date('Y-m-d H:i:s', time());
      $groupAllocationStatusForm = new agTinyEventFacilityGroupStatusForm($groupAllocationStatus);
      $groupAllocationStatusForm->getWidget('facility_group_allocation_status_id')->setAttribute('class',
                                                                                                 'inputGray submitTextToForm set100');
      return $this->renderPartial('global/includeForm',
                                  array('form' => $groupAllocationStatusForm,
        'set' => $params['type'],
        'id' => $params['id'],
        'url' => 'event/eventfacilitygroup'
          )
      );
    }
    // This checks for an incoming form. We'll always be saving here, not updating.
    if ($request->getParameter('ag_event_facility_group_status')) {
      $groupAllocationStatus = new agEventFacilityGroupStatus();
      $groupAllocationStatus['event_facility_group_id'] = $params['ag_event_facility_group_status']['event_facility_group_id'];
      $groupAllocationStatus['event_facility_group_id'] = $params['ag_event_facility_group_status']['event_facility_group_id'];
      $groupAllocationStatus['facility_group_allocation_status_id'] = $params['ag_event_facility_group_status']['facility_group_allocation_status_id'];
      $groupAllocationStatus['time_stamp'] = date('Y-m-d H:i:s', time());

      $groupAllocationStatus->save();

      $refresh = agDoctrineQuery::create()
          ->select('facility_group_allocation_status')
          ->from('agFacilityGroupAllocationStatus')
          ->where('id = ?',
                  $params['ag_event_facility_group_status']['facility_group_allocation_status_id'])
          ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
      return $this->renderText(json_encode(array('status' => 'success', 'refresh' => $refresh)));
//      return $this->renderText('success');
    }
  }

  /**
   * Gets facility resource information for facility resources within the facility group with the id
   * passed to the function.
   *
   * @param int      $eventFacilityGroupId     The id of an agEventFacilityGroup.
   *                                           Passed in from executeGroupDetail.
   *                                           If this isn't present, all facilities
   *                                           for an event will be displayed.
   * @return array() $results                  A multidimensional array of facility
   *                                           information. Each top level element
   *                                           corresponds to a returned facility.
   *
   * */
  private function groupResourceQuery($eventFacilityGroupId = null)
  {
    $query = agDoctrineQuery::create()
        ->select('efr.id')
        ->addSelect('f.facility_name')
        ->addSelect('f.facility_code')
        ->addSelect('frt.facility_resource_type')
        ->addSelect('frt.facility_resource_type_abbr')
        ->addSelect('ras.facility_resource_allocation_status')
        ->addSelect('f.id')
        ->addSelect('fr.id')
        ->addSelect('frt.id')
        ->addSelect('ras.id')
        ->addSelect('ers.id')
        ->addSelect('es.id')
        ->addSelect('efg.event_facility_group')
        ->addSelect('efg.id')
        ->addSelect('fgt.facility_group_type')
        ->addSelect('e.event_name')
        ->addSelect('efrat.id')
        ->addSelect('efrat.activation_time')
        ->from('agEventFacilityResource efr')
        ->innerJoin('efr.agFacilityResource fr')
        ->innerJoin('fr.agFacilityResourceStatus frs')
        ->innerJoin('fr.agFacilityResourceType frt')
        ->innerJoin('fr.agFacility f')
        ->innerJoin('efr.agEventFacilityResourceStatus ers')
        ->innerJoin('ers.agFacilityResourceAllocationStatus ras')
        ->innerJoin('efr.agEventFacilityGroup efg')
        ->innerJoin('efg.agFacilityGroupType fgt')
        ->innerJoin('efg.agEvent e')
        ->leftJoin('efr.agEventFacilityResourceActivationTime efrat')
        ->where('EXISTS (
          SELECT efrs.id
            FROM agEventFacilityResourceStatus efrs
            WHERE efrs.event_facility_resource_id = ers.event_facility_resource_id
              AND efrs.time_stamp <= CURRENT_TIMESTAMP
            HAVING MAX(efrs.time_stamp) = ers.time_stamp)');

    if (isset($eventFacilityGroupId)) {
      $query->andWhere('efg.id = ?', $eventFacilityGroupId);
    }
    $results = $query->execute(array(), Doctrine_Core::HYDRATE_SCALAR);
    return $results;
  }

  /**
   * provides the ability to give a description of an event, and close the event
   * @todo other resolution operations
   * @param sfWebRequest $request
   */
  public function executeResolution(sfWebRequest $request)
  {
    $this->setEventBasics($request);
    if ($request->isMethod(sfRequest::POST)) {
      //never going to be updating, will always be 'setting' the status, with the current timestamp
      $ag_event_status = new agEventStatus();

      $ag_event_status->setEventStatusTypeId($request->getParameter('event_status'));
      $ag_event_status->setEventId($this->event_id);
      $ag_event_status->time_stamp = new Doctrine_Expression('CURRENT_TIMESTAMP');
      $ag_event_status->save();
      $this->redirect('event');
    }
    $this->active_facility_groups = agEventFacilityHelper::returnEventFacilityGroups($this->event_id,
                                                                                     TRUE);
    $this->resForm = new sfForm();
    $this->resForm->setWidgets(array(
      'event_status' => new sfWidgetFormDoctrineChoice(array('multiple' => false, 'model' => 'agEventStatusType', 'method' => 'getEventStatusType')),
//      'event_id'     => new sfWidgetFormInputHidden()
    ));
    $currentStatus = agEventFacilityHelper::returnCurrentEventStatus($this->event_id);
    $this->resForm->setDefault('event_status', $currentStatus);
  }

  /**
   * @todo todo
   * @param sfWebRequest $request
   */
  public function executePost(sfWebRequest $request)
  {
    
  }

  /**
   * deletes an event
   * @param sfWebRequest $request
   */
  public function executeDelete(sfWebRequest $request)
  {
    $request->checkCSRFProtection();

    $this->forward404Unless($ag_event = Doctrine_Core::getTable('agEvent')->find(array($request->getParameter('id'))),
                                                                                                              sprintf('Object ag_event does not exist (%s).',
                                                                                                                      $request->getParameter('id')));
    $ag_event->delete();

    $this->redirect('event/index');
  }

  /**
   * provides the ability to add staff members into a shift
   * @param sfWebRequest $request
   */
  public function executeStaffshift(sfWebRequest $request)
  {
    $this->setEventBasics($request);
    $this->xmlHttpRequest = $request->isXmlHttpRequest();
    $this->shift_id = $request->getParameter('shiftid');

    $inputs = array('staff_type' => new sfWidgetFormDoctrineChoice(array('model' => 'agStaffResourceType', 'label' => 'Staff Type', 'add_empty' => TRUE)), // 'class' => 'filter')),
      'staff_org' => new sfWidgetFormDoctrineChoice(array('model' => 'agOrganization', 'method' => 'getOrganization', 'label' => 'Staff Organization', 'add_empty' => TRUE)),
      'query_condition' => new sfWidgetFormInputHidden()
        ////, 'class' => 'filter'))
    ); //will have to set the class for the form elements elsewhere
    //set up inputs for form
    $filterForm = new sfForm();

    foreach ($inputs as $key => $input) {
      $input->setAttribute('class', 'filter');
      $filterForm->setWidget($key, $input);
    }
    $this->filterForm = $filterForm;

    if ($request->getParameter('Search')) {

      $this->staffSearchForm = new sfForm();
      $this->staffSearchForm->setWidget('add',
                                        new agWidgetFormSelectCheckbox(array('choices' => array(null)), array()));
      $this->staffSearchForm->getWidgetSchema()->setLabel('add', false);
      $lucene_query = $request->getParameter('query_condition');
      //$lucene_query = $filter_form['query_condition'];
      $incomingFields = $this->filterForm->getWidgetSchema()->getFields();

      /**
       * @todo abstract the common operations here that are used in staff pool mangement to a helper class
       */
      $this->searchedModels = array('agEventStaff');  //we want the search model to be agEventStaff
      //note, this does not provide ability to add event
      parent::doSearch($lucene_query, FALSE, $this->staffSearchForm);
      return $this->renderPartial('search/resultform', array(
                                                         'hits' => $this->hits,
                                                         'searchquery' => $this->searchquery,
                                                         'results' => $this->results,
                                                         'widget' => $this->widget,
                                                         'shift_id' => $this->shift_id,
                                                         'event_id' => $this->event_id
                                                       ));
    } elseif ($request->getParameter('Add')) {
      $staffPotentials = $request->getPostParameter('resultform'); //('staff_list'); //ideally get only the widgets whose corresponding checkbox
      foreach ($staffPotentials as $key => $staffAdd) {
        //see if staff member exists in this shift already
        $existing = Doctrine::getTable('agEventStaffShift')
            ->findByDql('event_staff_id = ?', $this->shift_id)
            ->getFirst();
        if (!$existing) {
          $existing = new agEventStaffShift();
          $existing->setEventStaffId($key);
          $existing->setEventShiftId($this->shift_id);
        }
        $existing->save();
      }
    } elseif ($request->getParameter('Remove')) {
      //remove this staff member!
    }

    //p-code
    $this->getResponse()->setTitle('Sahana Agasti ' . $this->event_name . ' Staff Shift');
    //end p-code
  }

  public function executeMessaging(sfWebRequest $request)
  {
    $this->setEventBasics($request);
    //$this->eventName = urlencode($request->getParameter('event'));
  }

  public function executeDeploystaff(sfWebRequest $request)
  {
    $this->setEventBasics($request);
  }

}
