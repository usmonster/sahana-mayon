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

    /**
     * Import Replies is used in event messaging to receive input from a messaging vendor
     * in the form of a spreadsheet with responses of Yes or No to messages
     * @param sfWebRequest $request
     * @return results
     */
    public function executeImportreplies(sfWebRequest $request)
    {
      $this->setEventBasics($request);

        $this->startTime = microtime(true);

        $uploadedFile = $_FILES['import'];

        //print("<pre>" . print_r($_FILES, true) . "</pre>");

        $this->importPath = sfConfig::get('sf_upload_dir') . DIRECTORY_SEPARATOR . $uploadedFile['name'];


        if (!move_uploaded_file($uploadedFile['tmp_name'], $this->importPath)) {
            return sfView::ERROR;
        }
        //$this->dispatcher->notify(new sfEvent($this, 'import.start'));

        $this->importer = agMessageResponseHandler::getInstance($this->event_id, NULL, agEventHandler::EVENT_NOTICE);

        $this->importer->processXlsImportFile($this->importPath);

        $left = 1;
        while ($left > 0) {
            $left = $this->importer->processBatch();
            // print_r($left);
        }
        $iterData = $this->importer->getIterData();
        $this->totalRecords = $iterData['fetchCount'];
        $this->successful = $iterData['processedSuccessful'];
        $this->failed = $iterData['processedFailed'];
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
        $this->getResponse()->setHttpHeader("Cache-Control", "must-revalidate, post-check=0, pre-check=0");

        // Provide application and file info headers
        $this->getResponse()->setHttpHeader("Content-Type", "application/zip");
        $this->getResponse()->setHttpHeader("Content-Disposition", "attachment; filename='" . $fileName . ".zip'");
        $this->getResponse()->setHttpHeader("Content-Transfer-Encoding", "binary");
        $this->getResponse()->setHttpHeader("Content-Length", "" . filesize($filePath));

        $this->getResponse()->sendHttpHeaders();
        $this->getResponse()->setContent(readfile($filePath));
        $this->getResponse()->send();


        return sfView::NONE;
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

        // Query all existing events and their info for display.
        $query = agDoctrineQuery::create()
            ->select('e.event_name')
            ->addSelect('esc.id')
            ->addSelect('s.scenario')
            ->addSelect('es.id')
            ->addSelect('est.event_status_type')
            ->from('agEvent e')
            ->innerJoin('e.agEventStatus es')
            ->innerJoin('es.agEventStatusType est')
            ->innerJoin('e.agEventScenario esc')
            ->innerJoin('esc.agScenario s')
            ->where(
                'EXISTS (
                  SELECT s.id
                    FROM agEventStatus es2
                    WHERE es2.event_id = es.event_id
                      AND es2.time_stamp <= CURRENT_TIMESTAMP
                    HAVING MAX(es2.time_stamp) = es.time_stamp)'
        );
        $this->allEvents = $query->execute(array(), Doctrine_Core::HYDRATE_SCALAR);
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
            if ($request->isMethod(sfRequest::POST)) {
                $mh = new agEventMigrationHelper();
                $this->migrationCount = $mh->migrateScenarioToEvent($this->scenario_id, $this->event_id);
                $this->redirect('event/active?event=' . urlencode($this->event_name));
            } else {
                $this->redirect('event/active?event=' . urlencode($this->event_name));
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
      if ($request->getParameter('event')) {
          $this->event = agDoctrineQuery::create()
                  ->select()
                  ->from('agEvent')
                  ->where('event_name = ?', urldecode($request->getParameter('event')))
                  ->execute()->getFirst();

          $this->event_id = $this->event['id'];
          $this->event_name = $this->event['event_name'];
          $this->event_zero_hour = $this->event['zero_hour'];
          $this->organization_name = agGlobal::getParam('organization_name');
          $this->vesuvius_address = agGlobal::getParam('vesuvius_address');
          $this->event_zero_hour_str = date('Y-m-d H:i:s T', $this->event_zero_hour);
          $this->current_event_status = strtoupper(agEventStatus::getStatusType($this->event->getCurrentStatus()));
      }
    }

    /**
     * the meta action (event/meta) gives the user CRU functionality of event meta information
     * @param sfWebRequest $request
     */
    public function executeMeta(sfWebRequest $request)
    {
        $this->setEventBasics($request);
        $this->isPreDeploy = 1;
        $this->eventStatusType = NULL;
        $this->eventStatusTypeId = NULL;
        $this->checkResults = NULL;
        $conn = Doctrine_Manager::connection();

        if (empty($this->event_id)) {
            $eventMeta = NULL;

            if ($request->hasParameter('ag_scenario_list')) {
                $this->scenario_id = $request->getParameter('ag_scenario_list');
            } elseif ($request->hasParameter('scenario_id')) {
                $this->scenario_id = $request->getParameter('scenario_id');
            } else {
                $this->scenario_id = NULL;
            }
        } else {

            $eventMeta = agDoctrineQuery::create($conn)
                ->from('agEvent e')
                ->where('e.id = ?', $this->event_id)
                ->execute();
            $eventMeta = $eventMeta[0];

            $this->scenario_id = agDoctrineQuery::create()
                ->select('scenario_id')
                ->from('agEventScenario')
                ->where('event_id = ?', $this->event_id)
                ->execute(array(), Doctrine_CORE::HYDRATE_SINGLE_SCALAR);

            $eventStatus = agDoctrineQuery::create()
                ->select('es.id')
                ->addSelect('est.id')
                ->addSelect('est.event_status_type')
                ->addSelect('est.active')
                ->from('agEvent AS e')
                ->innerJoin('e.agEventStatus AS es')
                ->innerJoin('es.agEventStatusType AS est')
                ->where('e.id = ?', $this->event_id)
                ->andWhere('EXISTS (SELECT es1.id
                                FROM agEventStatus AS es1
                                WHERE es1.event_id = es.event_id
                                HAVING MAX(es1.time_stamp) = es.time_stamp)')
                ->execute(array(), Doctrine_Core::HYDRATE_NONE);

            $eventStatusId = (empty($eventStatus)) ? null : $eventStatus[0][0];
            $eventStatusTypeId = (empty($eventStatus)) ? null : $eventStatus[0][1];
            $eventStatusType = (empty($eventStatus)) ? null : $eventStatus[0][2];
        }

        $this->metaForm = new PluginagEventDefForm($eventMeta);

        // Query for pre-migration statistics if the event is associated to a scenario.
        if (!empty($this->scenario_id)) {
            $this->scenarioName = Doctrine::getTable('agScenario')
                    ->findByDql('id = ?', $this->scenario_id)
                    ->getFirst()->scenario;

            $this->checkResults = agEventMigrationHelper::preMigrationCheck($this->scenario_id);
        }

        // Saving event meta.
        if ($request->isMethod(sfRequest::POST)) {
            try {
                // here we check our current transaction scope and create a transaction or savepoint
                $useSavepoint = ($conn->getTransactionLevel() > 0) ? TRUE : FALSE;
                if ($useSavepoint) {
                    $conn->beginTransaction(__FUNCTION__);
                } else {
                    $conn->beginTransaction();
                }

                if ($request->hasParameter('Deploy') || $request->hasParameter('Save')) {

                    $deployStatus = agGlobal::getParam('event_deploy_status');
                    $deployStatusId = Doctrine_Core::getTable('agEventStatusType')
                            ->findBy('event_status_type', $deployStatus)->getFirst()->id;

                    $this->metaForm->bind(
                        $request->getParameter($this->metaForm->getName()), $request->getFiles($this->metaForm->getName())
                    );

                    $ag_event = $this->metaForm->save($conn);

                    if ($this->metaForm->isValid()) {

                        // Save event meta updates as deploy event status.
                        if ($request->hasParameter('Deploy')) {
                            $event_status_type_id = $deployStatusId;
                        } else { // Save event meta updates as default event status.
                            $event_status_type_id = (empty($eventStatusTypeId)) ?
                                agEventHelper::returnDefaultEventStatus() :
                                $eventStatusTypeId;
                        }

                        $agEventStatusTable = $conn->getTable('agEventStatus');
                        if (empty($eventStatusId)) {
                            $agEventStatus = new agEventStatus($agEventStatusTable, TRUE);
                        } else {
                            $agEventStatus = $agEventStatusTable->find($eventStatusId);
                        }
                        $agEventStatus->setEventId($ag_event->getId());
                        $agEventStatus->setTimeStamp(new Doctrine_Expression('CURRENT_TIMESTAMP'));
                        $agEventStatus->setEventStatusTypeId($event_status_type_id);
                        $agEventStatus->save($conn);

                        $eventScenarioId = agDoctrineQuery::create()
                            ->select('es.id')
                            ->from('agEventScenario AS es')
                            ->where('es.event_id = ?', $this->event_id)
                            ->andWhere('es.scenario_id = ?', $request->getParameter('scenario_id'))
                            ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);

                        // Save event-scenario entry
                        if (!empty($this->scenario_id)) {
                            $eventScenTable = $conn->getTable('agEventScenario');
                            if (empty($eventScenarioId)) {
                                $ag_event_scenario = new agEventScenario($eventScenTable, TRUE);
                            } else {
                                $ag_event_scenario = $eventScenTable->find($eventScenarioId);
                            }

                            $ag_event_scenario->setScenarioId($this->scenario_id);
                            $ag_event_scenario->setEventId($ag_event->getId());
                            $ag_event_scenario->save($conn);
                        }
                    }
                }
                if ($useSavepoint) {
                    $conn->commit(__FUNCTION__);
                } else {
                    $conn->commit();
                }
            } catch (Exception $e) {
                // rollback
                if ($useSavepoint) {
                    $conn->rollback(__FUNCTION__);
                } else {
                    $conn->rollback();
                }

                $this->errMsg = 'Cannot save event.  Error Msg: ' . $e->getMessage();
                return sfView::SUCCESS;
            }
        }

        if ($request->isMethod(sfRequest::POST)) {
            if ($request->hasParameter('Deploy')) {
                $eventName = $this->metaForm->getObject()->getEventName();
                $this->getRequest()->setParameter('event', $eventName);
                $this->forward($request->getParameter('module'), 'deploy');
            } elseif ($request->hasParameter('Save')) {
                $this->redirect('event/active?event=' . urlencode($ag_event->getEventName()));
            }
        }

        //p-code
        if (isset($eventMeta->event_name)) {

            // Redirect back to the active page if the existing event is not in pre-deploy state.
            if (strtoupper(agGlobal::getParam('event_pre_deploy_status')) <> strtoupper($eventStatusType)) {
                $this->redirect('event/active?event=' . urlencode($eventMeta->event_name));
            }

            $this->getResponse()->setTitle('Sahana Agasti ' . $this->event_name . ' Event Management');
        } else {
            $this->getResponse()->setTitle('Sahana Agasti: New Event');
        }

        //end p-code
    }

    public function preExecute()
    {
        // The code inserted here is executed at the beginning of each action call
        //$this->getUser()->signin($sfGuardUserObject);
        //$this->getUser()->signin();

    }

    public function executeExportfacilities(sfWebRequest $request)
    {

        //TODO put switch in here to check for type of request
        $this->setEventBasics($request);

        /*
          $event_facility_query = agEventFacilityResource::getEventFacilityResourceQuery($this->event_id);

          $this->event_facility_resources =
          $event_facility_query->execute(array(), Doctrine_Core::HYDRATE_SCALAR);
         */


        $facilityData = new agEventFacilityExporter($this->event_id);
        $jsonData = $facilityData->getJsonExport();


        // Make sure the browser doesn't try to deliver a chached version
        $this->getResponse()->setHttpHeader("Pragma", "public");
        $this->getResponse()->setHttpHeader("Expires", "0");
        $this->getResponse()->setHttpHeader("Cache-Control", "must-revalidate, post-check=0, pre-check=0");

        // Provide application and file info headers
        $this->getResponse()->setHttpHeader("Content-Type", "application/json");



        $this->renderText($jsonData);

        return sfView::NONE;
    }

  public function executeExportcontacts(sfWebRequest $request)
  {
    $this->setEventBasics($request);
    $this->info = array();

    if ($request->hasParameter('type')) {
      $type = $request->getParameter('type');

      switch ($type) {
        case 'pre':
          $exporter = agSendWordNowPreDeploy::getInstance($this->event_id, 'staff_contacts_predeploy');
          break;
        case 'post':
          $exporter = agSendWordNowPostDeploy::getInstance($this->event_id, 'staff_contacts_postdeploy');
          break;
      }

      $this->fileInfo = $exporter->getExport();

      $this->getResponse()->setHttpHeader('Content-Type', 'application/zip');
      $this->getResponse()->setHttpHeader('Content-Disposition', 'attachment;filename="' .
        $this->fileInfo['filename'] . '"');

      $exportFile = file_get_contents($this->fileInfo['path'] . DIRECTORY_SEPARATOR .
        $this->fileInfo['filename']);

      $this->getResponse()->setContent($exportFile);
      $this->getResponse()->send();
    }

    $this->redirect('event/messaging?event=' . urlencode($this->event_name));
  }

    /**
     * provides basic information about an active event, the template gives links to event management
     * @param sfWebRequest $request
     */
    public function executeActive(sfWebRequest $request)
    {
        $this->setEventBasics($request);
        $availableStaffStatus = agEventStaffHelper::returnAvailableEventStaffStatus();

        $this->eventAvailableStaff = agEventStaff::getActiveEventStaffQuery($this->event_id)
           ->select('COUNT(evs.id)')
          ->andWhere('ess.staff_allocation_status_id = ?', $availableStaffStatus)
          ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);

        $this->eventCommittedStaff = agEventStaff::getActiveEventStaffQuery($this->event_id)
          ->select('COUNT(evs.id)')
          ->andWhere('sas.committed = ?', TRUE)
          ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);

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
            ->execute(array(), agDoctrineQuery::HYDRATE_KEY_VALUE_PAIR);
        if (empty($this->event_description[1])) {
            $this->event_description = '---';
        } else {
            $this->event_description = $this->event_description[1];
        }

        $this->event_facility_groups = agDoctrineQuery::create()
            ->select('COUNT(efg.id)')
            ->from('agEventFacilityGroup efg')
            ->where('efg.event_id = ?', $this->event_id)
            ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
        $this->event_facility_resources = agDoctrineQuery::create()
            ->select('COUNT(efr.id)')
            ->from('agEventFacilityResource efr')
            ->leftJoin('efr.agEventFacilityGroup efg')
            ->where('efg.event_id = ?', $this->event_id)
            ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);

        //p-code
        $this->getResponse()->setTitle('Sahana Agasti ' . $this->event_name . ' Management');


        $current_status = agEventFacilityHelper::returnCurrentEventStatus($this->event_id);
        if ($current_status != "") {
            $cur_status = Doctrine::getTable('agEventStatusType')
                    ->findByDql('id = ?', $current_status)
                    ->getFirst()->event_status_type;
        }

        $this->eventShiftStaffCount = agEvent::getEventShiftStaffCount($this->event_id);

        //end p-code
    }

    /**
     * provides basic staff management in an event
     * @param sfWebRequest $request
     */
    public function executeStaff(sfWebRequest $request)
    {
        $this->setEventBasics($request);
        $this->results = array();

        $this->subForm = new agReportTimeForm();
        unset($this->subForm['_csrf_token']);
        $this->reportTime = NULL;

        if ($request->isMethod(sfRequest::POST))
        {
          $this->subForm->bind($request->getParameter('reportTime'));
          if ($this->subForm->isValid())
          {
            $formArray = $request->getParameter('reportTime');
            $this->reportTime = strtotime($formArray['report_time']);
            $this->results = agEvent::getShiftsSummary($this->event_id, $this->reportTime);
            $this->test = agEvent::getShiftEstimates($this->event_id, $this->reportTime);
          }
        }

        //p-code
        $this->getResponse()->setTitle('Sahana Agasti ' . $this->event_name . ' Staff');
        //end p-code
    }

    /**
     * provides basic staff management in an event
     * @param sfWebRequest $request
     */
    public function executeStaffingestimates(sfWebRequest $request)
    {
        $this->setEventBasics($request);
        $this->results = array();

        $this->subForm = new agReportTimeForm();
        unset($this->subForm['_csrf_token']);
        $this->reportTime = NULL;

        if ($request->isMethod(sfRequest::POST))
        {
          $this->subForm->bind($request->getParameter('reportTime'));
          if ($this->subForm->isValid())
          {
            $formArray = $request->getParameter('reportTime');
            $this->reportTime = strtotime($formArray['report_time']);
            $this->results = agEvent::getShiftsSummary($this->event_id, $this->reportTime);
          }
        }

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
        if ($request->getParameter('event') != null && Doctrine::getTable('agEvent')->findByDql('where event_name = ?', $request->getParameter('event'))->getFirst() == false) {
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
            return $this->renderPartial('eventFacResTable', array('facilityResourceArray' => $this->facilityResourceArray));
        }
        // POST handles all the rest. Loading the forms to replace the TD html, saving the forms, etc.
        elseif ($request->isMethod(sfRequest::POST)) {
            $params = $request->getPostParameters();

            // Render the Forms
            if (isset($params['type']) && $params['type'] == 'resourceStatus') {
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
                $resourceStatusForm->getWidget('facility_resource_allocation_status_id')->setAttribute('class', 'inputGray submitTextToForm set100');

                return $this->renderPartial('global/includeForm', array(
                  'form' => $resourceStatusForm,
                  'set' => $params['type'],
                  'id' => $params['id'],
                  'url' => 'event/eventfacilityresource?eventFacilityResourceId=' . ltrim($params['id'], 'res_stat_id_')
                    )
                );
            } elseIf (isset($params['type']) && $params['type'] == 'resourceActivationTime') {
                // This case is for loading the even-facility-resource-activation-time form in place of the submittal link.
                if ($params['current'] != '----') {
                    $eventFacilityResourceActivationTime = agDoctrineQuery::create()
                        ->select()
                        ->from('agEventFacilityResourceActivationTime')
                        ->where('event_facility_resource_id = ?', ltrim($params['id'], 'res_act_id_'))
                        ->fetchOne(array(), Doctrine_Core::HYDRATE_RECORD);
                    $eventFacilityResourceActivationTime['activation_time'] = date('m/d/Y H:i', $eventFacilityResourceActivationTime['activation_time']);
                } else {
                    $eventFacilityResourceActivationTime = new agEventFacilityResourceActivationTime();
                    $eventFacilityResourceActivationTime['event_facility_resource_id'] = ltrim($params['id'], 'res_act_id_');
                }
                $eventFacilityResourceActivationTimeForm = new agTinyEventFacilityResourceActivationTimeForm($eventFacilityResourceActivationTime);
                $eventFacilityResourceActivationTimeForm->getWidget('activation_time')->setAttribute('class', 'inputGray submitTextToForm set110');

                return $this->renderPartial('global/includeForm', array('form' => $eventFacilityResourceActivationTimeForm,
                  'set' => $params['type'],
                  'id' => $params['id'],
                  'url' => 'event/eventfacilityresource?eventFacilityResourceId=' . ltrim($params['id'], 'res_act_id_')
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
                    ->where('id = ?', $params['ag_event_facility_resource_status']['facility_resource_allocation_status_id'])
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
                    ->where('event_facility_resource_id = ?', $params['ag_event_facility_resource_activation_time']['event_facility_resource_id'])
                    ->fetchOne(array(), Doctrine_Core::HYDRATE_RECORD);
                // If there isn't, create a new object, assign values.
                if ($eventFacilityResourceActivationTime == false) {
                    $eventFacilityResourceActivationTime = new agEventFacilityResourceActivationTime();
                    $eventFacilityResourceActivationTime['event_facility_resource_id'] = $params['ag_event_facility_resource_activation_time']['event_facility_resource_id'];
                }
                // In any case, set the time to whatever the new time is.
                $eventFacilityResourceActivationTime['activation_time'] = $params['ag_event_facility_resource_activation_time']['activation_time'];

                $eventFacilityResourceActivationTime->save();
                return $this->renderText(json_encode(array('status' => 'success', 'refresh' => date('m/d/Y H:i', $params['ag_event_facility_resource_activation_time']['activation_time']))));
            }
        }
    }

    public function executeEventfacilitygroup(sfWebRequest $request)
    {
        // Get the incoming params.
        $params = $request->getPostParameters();

        if (isset($params['type']) && $params['type'] == 'groupStatus') {
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
            $groupAllocationStatusForm->getWidget('facility_group_allocation_status_id')->setAttribute('class', 'inputGray submitTextToForm set100');
            return $this->renderPartial('global/includeForm', array('form' => $groupAllocationStatusForm,
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
                ->where('id = ?', $params['ag_event_facility_group_status']['facility_group_allocation_status_id'])
                ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
            return $this->renderText(json_encode(array('status' => 'success', 'refresh' => $refresh)));
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
        $this->active_facility_groups = agEventFacilityHelper::returnEventFacilityGroups($this->event_id, TRUE);
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

        $this->forward404Unless($ag_event = Doctrine_Core::getTable('agEvent')->find(array($request->getParameter('id'))), sprintf('Object ag_event does not exist (%s).', $request->getParameter('id')));
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
            $this->staffSearchForm->setWidget('add', new agWidgetFormSelectCheckbox(array('choices' => array(null)), array()));
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
      $this->staffingSummary = array();
      $this->setEventBasics($request);

      $staffDeployer = agEventStaffDeploymentHelper::getInstance($this->event_id);
      $continue = TRUE;
      while ($continue == TRUE) {
          $batch = $staffDeployer->processBatch();
          $continue = $batch['continue'];

          $this->batchResults = $batch;
      }

      //if the entire process is complete, give us some GOOD results
      $this->batchResults = $staffDeployer->save();
      unset($staffDeployer);

      $this->strStart = date('Y:m:d H:i:s T', $this->batchResults['start']);
      $this->strEnd =  date('Y:m:d H:i:s T', $this->batchResults['end']);
      $this->strDuration = date('H:i:s', mktime(0, 0, round(($this->batchResults['duration']), 0), 0, 0, 2000));

      // Format memory
      $bytes = array('KB', 'KB', 'MB', 'GB', 'TB');
      $peakMemory = $batchResults['profiler']['maxMem'];
      if ($peakMemory <= 999) {
        $peakMemory = 1;
      }
      for ($i = 0; $peakMemory > 999; $i++) {
        $peakMemory /= 1024;
      }
      $this->peakMemory = ceil($peakMemory) . " " . $bytes[$i];
      
      if (!$this->batchResults['err']) {
        $this->staffingSummary = agEvent::getShiftsSummary($this->event_id, $this->event_zero_hour);
        $this->strZeroHour = date('Y-m-d H:i:s T', $this->event_zero_hour);
      }
    }

}
