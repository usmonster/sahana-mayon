<?php

/*
 * Class to export all staffs and their latest resource types.
 *
 * It is called by the staff action class.
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author     Shirley Chan, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */

class agStaffExporter
{

  /**
   * The construct() method sets up array variables for header mappings and lookup definitions.
   */
  function __construct()
  {
    // The array index specify the order of the header in the export file.
    $this->exportHeaders = array(
      'Entity ID',
      'First Name',
      'Middle Name',
      'Last Name',
      'Mobile Phone',
      'Home Phone',
      'Home Email',
      'Work Phone',
      'Work Email',
      'Home Address Line 1',
      'Home Address Line 2',
      'Home Address City',
      'Home Address State',
      'Home Address Zip',
      'Home Address Country',
      'Home Latitude',
      'Home Longitude',
      'Work Address Line 1',
      'Work Address Line 2',
      'Work Address City',
      'Work Address State',
      'Work Address State',
      'Work Address Zip',
      'Work Address Country',
      'Work Latitude',
      'Work Longitude',
      'Organization',
      'Resource Type',
      'Resource Status',
      'Language 1',
      'L1 Speak',
      'L1 Read',
      'L1 Write',
      'Language 2',
      'L2 Speak',
      'L2 Read',
      'L2 Write',
      'Language 3',
      'L3 Speak',
      'L3 Read',
      'L3 Write',
      'Drivers License Class',
      'PMS ID',
      'Civil Service Title'
    );

    $this->lookUps = array(
      'state' => array(
        'selectTable' => 'agAddressValue',
        'selectColumn' => 'value',
        'whereColumn' => 'address_element_id',
        'whereValue' => 4
      ),
      'resourcetype' => array(
        'selectTable' => 'agStaffResourceType',
        'selectColumn' => 'staff_resource_type',
        'whereColumn' => null,
        'whereValue' => null
      ),
      'resourcestatus' => array(
        'selectTable' => 'agStaffResourceStatus',
        'selectColumn' => 'staff_resource_status',
        'whereColumn' => null,
        'whereValue' => null
      ),
      'language' => array(
        'selectTable' => 'agLanguage',
        'selectColumn' => 'language',
        'whereColumn' => null,
        'whereValue' => null
      ),
      'competency' => array(
        'selectTable' => 'agLanguageCompetency',
        'selectColumn' => 'language_competency',
        'whereColumn' => null,
        'whereValue' => null
      ),
      'organization' => array(
        'selectTable' => 'agOrganization',
        'selectColumn' => 'organization',
        'whereColumn' => null,
        'whereValue' => null
      )
    );

    // This array is reconstructed below when retrieving staff resoure information.
    $this->staffResourceHeaderMapping = array();

    $this->nameHeaderMapping = array(
      'given' => 'First Name',
      'middle' => 'Middle Name',
      'family' => 'Last Name'
    );

    $this->phoneHeaderMapping = array(
      'mobile' => 'Mobile Phone',
      'home' => 'Home Phone',
      'work' => 'Work Phone'
    );

    $this->emailHeaderMapping = array(
      'personal' => 'Home Email',
      'work' => 'Work Email'
    );

    $this->addressLineHeaderMapping = array(
      'line 1' => 'Address Line 1',
      'line 2' => 'Address Line 2',
      'city' => 'Address City',
      'state' => 'Address State',
      'zip5' => 'Address Zip',
      'country' => 'Address Country',
      'latitude' => 'Latitude',
      'longitude' => 'Longitude'
    );

    $this->addressTypeHeaderRequirements = array('home' => 'Home', 'work' => 'Work');

    $this->languageFormatTypeHeaderRequirements = array('read' => 'Read',
      'write' => 'Write',
      'speak' => 'Speak');

    // Setup some debugging
    $this->logger = sfContext::getInstance()->getLogger();
    $this->peakMemory = 0;
  }

  /**
   * Returns an array of Organizations 
   * 
   * @return type array
   */
  private function getOrganizationList()
  {

    // Query for organizations
    $q = agDoctrineQuery::create()
        ->select('o.id, o.organization')
        ->from('agOrganization as o');

    $results = $q->execute(array(), agDoctrineQuery::HYDRATE_KEY_VALUE_PAIR);

    return $results;
  }

  /**
   * A quick method to take in an array of person custom fields and return an array of person
   * custom field ids.
   *
   * @param array $personCustomFields A simple array of person custom fields.  Limit the select
   * query to the fields specified in the array.  If NULL, return all person custom fields.
   * @return array $results An associative array, keyed by person custom field, with a value of
   * person_custom_field_id.
   */
  public function getPersonCustomFields(array $personCustomFields = NULL)
  {
    $q = agDoctrineQuery::create()
        ->select('pcf.person_custom_field')
        ->addSelect('pcf.id')
        ->from('agPersonCustomField AS pcf');

    if (!is_null($personCustomFields)) {
      $q->whereIn('pcf.person_custom_field', $personCustomFields);
    }

    $results = $q->execute(array(), agDoctrineQuery::HYDRATE_KEY_VALUE_PAIR);
    return $results;
  }

  /**
   * Method to collect all associating staff information and return them in an associative array.
   *
   * @param array $staff_ids An array of staff ids.
   * @return array $staffInfo An associative array of staff information.
   */
  private function getStaffResourceGeneralInfo(array $staff_ids = NULL)
  {
    $staffInfo = array();

    $this->staffResourceHeaderMapping = array(
      'Entity ID' => 'p_entity_id',
      'Organization' => 'o_organization',
      'Resource Type' => 'srt_staff_resource_type',
      'Resource Status' => 'srs_staff_resource_status'
    );

    $q = agDoctrineQuery::create()
        ->select('sr.id')
        ->addSelect('sr.staff_id')
        ->addSelect('s.person_id')
        ->addSelect('p.entity_id')
        ->addSelect('srt.staff_resource_type')
        ->addSelect('o.id')
        ->addSelect('o.organization')
        ->addSelect('srs.id')
        ->addSelect('srs.staff_resource_status')
        ->addSelect('pcfv1.value')
        ->addSelect('pcfv2.value')
        ->addSelect('pcfv3.value')
        ->from('agStaffResource AS sr')
        ->innerJoin('sr.agStaff AS s')
        ->innerJoin('s.agPerson AS p')
        ->innerJoin('sr.agStaffResourceType AS srt')
        ->innerJoin('sr.agStaffResourceStatus AS srs')
        ->innerJoin('sr.agOrganization AS o');

    // Query for person_custom_field and their ids.
    $personCustomFields = array('Drivers License Class',
      'PMS ID',
      'Civil Service Title');
    $personCustomFieldIds = $this->getPersonCustomFields($personCustomFields);

    // Add person custom fields to query.
    $cnt = 1;
    foreach ($personCustomFields AS $customField) {
      $tblAlias = 'pcfv' . $cnt++;
      $q->leftJoin('p.agPersonCustomFieldValue AS ' . $tblAlias .
          ' WITH ' . $tblAlias . '.person_custom_field_id = ' .
          $personCustomFieldIds[$customField]);
      $this->staffResourceHeaderMapping[$customField] = $tblAlias . '_value';
    }

    if (!is_null($staff_ids)) {
      $q->whereIn('sr.staff_id', $staff_ids);
    }

    $results = $q->execute(array(), DOCTRINE_CORE::HYDRATE_SCALAR);

    // Construct the array of the staff's custom fields.
    foreach ($results AS $row) {
      $cnt = 1;
      foreach ($personCustomFields AS $customField) {
        $tblAlias = 'pcfv' . $cnt++;
        $staffCustomFields[$row['sr_id']][$customField] = $row[$tblAlias . '_value'];
      }
    }

    // Construct arrays for later use from the query results
    foreach ($results AS $index => $staffResource) {
      $staffResourceId = array_shift($staffResource);
      $staffInfo[$staffResourceId] = $staffResource;
    }

    return $staffInfo;
  }

  /**
   * A method to build the associate array for exporting staff information.
   *
   * @return array $content An associate array of staff resource information in the export format.
   */
  private function buildExportRecords($orgId)
  {
    $staffResources = agDoctrineQuery::create()
        ->select('sr.id')
        ->addSelect('sr.staff_id')
        ->addSelect('s.person_id')
        ->addSelect('p.entity_id')
        ->from('agStaffResource sr')
        ->innerJoin('sr.agStaff s')
        ->innerJoin('s.agPerson p')
        ->innerJoin('sr.agOrganization o')
        ->where('o.id = ' . $orgId)
        ->execute(array(), Doctrine_Core::HYDRATE_SCALAR);

    // Build person id and entity id arrays for later use to retrieve persons names and contacts.
    $staff_ids = array();
    $entity_ids = array();
    $person_ids = array();
    foreach ($staffResources AS $stfRsc) {
      $staff_ids[] = $stfRsc['sr_staff_id'];
      $person_ids[] = $stfRsc['s_person_id'];
      $entity_ids[] = $stfRsc['p_entity_id'];
    }
    unset($staffResources, $stfRsc);

    // Process records by batch.
    $content = array();
    $defaultBatchSize = agGlobal::getParam('default_batch_size');
    while (!empty($staff_ids)) {
      // Process by batches.
      $subsetSIds = array_slice($staff_ids, 0, $defaultBatchSize);
      array_splice($staff_ids, 0, $defaultBatchSize);
      $subsetPIds = array_slice($person_ids, 0, $defaultBatchSize);
      array_splice($person_ids, 0, $defaultBatchSize);
      $subsetEIds = array_slice($entity_ids, 0, $defaultBatchSize);
      array_splice($entity_ids, 0, $defaultBatchSize);

      $staffInfo = $this->getStaffResourceGeneralInfo($subsetSIds);

      // Collect staffs' names, their contact information, and their language competency.
      $personNameHelper = new agPersonNameHelper();
      $staffNames = $personNameHelper->getPrimaryNameByType($subsetPIds);
      $phoneHelper = new agEntityPhoneHelper();
      $staffPhones = $phoneHelper->getEntityPhoneByType($subsetEIds, TRUE, TRUE,
                                                        agPhoneHelper::PHN_GET_COMPONENT);
      $emailHelper = new agEntityEmailHelper();
      $staffEmails = $emailHelper->getEntityEmailByType($subsetEIds, TRUE, TRUE,
                                                        agEmailHelper::EML_GET_VALUE);
      $addressHelper = new agEntityAddressHelper();
      $staffAddresses = $addressHelper->getEntityAddressByType($subsetEIds, TRUE, TRUE,
                                                               agAddressHelper::ADDR_GET_TYPE);
      $languageHelper = new agPersonLanguageHelper();
      $staffLanguages = $languageHelper->getPersonLanguage($subsetPIds, FALSE);

      foreach ($staffInfo AS $stfRscIds => $stfRsc) {
        $row = array();
        foreach ($this->exportHeaders AS $header) {
          $row[$header] = null;
        }

        // Populate staff resource information into $row array.
        foreach ($this->staffResourceHeaderMapping AS $header => $field) {
          $row[$header] = $stfRsc[$field];
        }

        // Populate staff's name into $row array.
        if (array_key_exists($stfRsc['s_person_id'], $staffNames)) {
          foreach ($staffNames[$stfRsc['s_person_id']] AS $nameType => $name) {
            // Populate the row with name components only for the ones that we care for.
            if (array_key_exists($nameType, $this->nameHeaderMapping)) {
              $row[$this->nameHeaderMapping[$nameType]] = $name;
            }
          }
          unset($staffNames['s_person_id']);
        }

        // Populate staff's emails into $row array.
        if (array_key_exists($stfRsc['p_entity_id'], $staffPhones)) {
          foreach ($staffPhones[$stfRsc['p_entity_id']] AS $phoneType => $phone) {
            // Populate the row with email only for the email type that we care for.
            if (array_key_exists($phoneType, $this->phoneHeaderMapping)) {
              $row[$this->phoneHeaderMapping[$phoneType]] = $phone[0][0];
            }
          }
          unset($staffPhones['s_entity_id']);
        }

        // Populate staff's phone numbers into $row array.
        if (array_key_exists($stfRsc['p_entity_id'], $staffEmails)) {
          foreach ($staffEmails[$stfRsc['p_entity_id']] AS $emailType => $email) {
            // Populate the row with email only for the email type that we care for.
            if (array_key_exists($emailType, $this->emailHeaderMapping)) {
              $row[$this->emailHeaderMapping[$emailType]] = $email[0][0];
            }
          }
          unset($staffEmails['s_entity_id']);
        }

        // Populate staff's address & geo info into $row array.
        if (array_key_exists($stfRsc['p_entity_id'], $staffAddresses)) {
          foreach ($staffAddresses[$stfRsc['p_entity_id']] AS $addressType => $address) {
            // Populate the row with address & geo info only for the address types and address
            // elements that we care for.
            if (array_key_exists($addressType, $this->addressTypeHeaderRequirements)) {

              $foo = $address[0][0];

              //$this->logger->debug(print_r($address[0][0], true));
              if (is_array($address[0][0])) {
                foreach ($address[0][0] AS $elem => $value) {
                  if (array_key_exists($elem, $this->addressLineHeaderMapping)) {
                    $type = $this->addressTypeHeaderRequirements[$addressType];
                    $component = $this->addressLineHeaderMapping[$elem];
                    $header = $type . ' ' . $component;
                    $row[$header] = $value;
                  }
                }
              } else {
                $foo = $address[0][0];
              }
            }
          }
          unset($staffAddresses['p_entity_id']);
        }

        // Populate staff's languages into $row.
        $iteration = 1;
        $max_language_count = 3;
        if (array_key_exists($stfRsc['s_person_id'], $staffLanguages)) {
          foreach ($staffLanguages[$stfRsc['s_person_id']] AS $priority => $languageComponents) {
            if ($iteration <= $max_language_count) {
              $language = $languageComponents[0];
              $languageHeader = 'Language ' . $iteration;
              $row[$languageHeader] = $language;

              if (isset($languageComponents[1])) {
                $languageFormats = $languageComponents[1];
                foreach ($languageFormats AS $format => $competency) {
                  $formatType = $this->languageFormatTypeHeaderRequirements[$format];
                  $languageFormatHeader = 'L' . $iteration . ' ' . $formatType;
                  $row[$languageFormatHeader] = $competency;
                }
              }
            } else {
              break;
            }
            $iteration++;
          }
        }

        // Add row to $content for exporting.
        $content[] = $row;
      }
    }

    return $content;
  }

  /**
   * This function calls the other functions needed to export staff data and
   * returns the constructed XLS file.
   *
   * @return array() $exportResponse     An associative array of two elements,
   *                                     fileName and filePath. fileName is the name
   *                                     of the XLS file that has been constructed
   *                                     and is held in temporary storage.
   *                                     filePath is the path to that file.
   */
  public function export()
  {
    // Get list of orgs
    $orgList = $this->getOrganizationList();

    // Populate lookups
    $lookUps = $this->gatherLookupValues($this->lookUps);

    // set some smart variables
    $date = date('Ymd_His');
    $downloadFile = sprintf("staff_export_%s.zip", $date);
    $zipPath = realpath(sys_get_temp_dir()) . DIRECTORY_SEPARATOR . $downloadFile;
    $exportFiles = array();

    // Get our max export size
    $XlsMaxExportSize = agGlobal::getParam('xls_max_export_size');
    
    // first search our directory for existing unprocessed files and remove
    $pathBase = sfConfig::get('sf_download_dir') . DIRECTORY_SEPARATOR . 'staff_export_';
    foreach(glob(($pathBase . '*')) as $filename) {
      $eventMsg = 'Export: Removing old export file ' . $filename . '.';
      $this->logger->debug($eventMsg);
      unlink($filename);
      $eventMsg = 'Export: Successfully removed old export file ' . $filename . '.';
      $this->logger->info($eventMsg);
    }
    

    // Create zip file
    $zipFile = new ZipArchive();
    
    $tmpPath = realpath(sys_get_temp_dir()) . "/$downloadFile";

    if ($zipFile->open($tmpPath, ZIPARCHIVE::CREATE) !== TRUE) {
      $this->logger->err("Export: Could not create zip file $tmpPath. Check permissions.");
    } else {
      $this->logger->debug("Export: Successfully created $tmpPath.");
    }

    // Interate through each organization
    foreach ($orgList as $key => $orgName) {

      $this->logger->debug("Export: Collecting staff for $orgName");
      $staffExportRecords = $this->buildExportRecords($key);

      if (count($staffExportRecords) > 0) {

        // Chunk the records
        $xlsBatch = array_chunk($staffExportRecords, $XlsMaxExportSize);

        for ($i = 0; $i < count($xlsBatch); $i++) {

          // load the Excel writer object
          $this->xlsWb = new agExcel2003ExportHelper($orgName);

          $fileName = $this->xlsWb->filename($orgName);

          if (count($xlsBatch) > 1) {
            $fileName .= "-" . ($i + 1);
          }

          $fileName = sprintf("staff_%s_%s.xls", $fileName, $date);
          $filePath = realpath(sys_get_temp_dir()) . '/' . $fileName;

          // Generate the Excel file
          $this->buildExcelWorkbook($xlsBatch[$i], $lookUps, $filePath, $orgName);
          $exportFiles[] = array($filePath, $fileName);
        }

        // Capture peak memory
        $currentMemoryUsage = memory_get_usage();

        if ($currentMemoryUsage > $this->peakMemory) {
          $this->peakMemory = memory_get_usage();
        }

        // Cleanup
        unset($this->xlsWb);
      }
    }

    foreach ($exportFiles as $file) {
      // Add export file to zip
      $zipFile->addFile($file[0], $file[1]);
    }

    // Close up the zip
    $zipFile->close();
    
    // finally, move the zip file to its final web-accessible location
    $this->logger->debug('Export: Moving ' . $downloadFile . ' to user-accesible directory.');
    $downloadPath = sfConfig::get('sf_download_dir') . DIRECTORY_SEPARATOR . $downloadFile;
    if (! rename($zipPath, $downloadPath)) {
      $this->logger->err('Export: Unable to move ' . $downloadFile . ' to the specified upload ' .
        'directory. Check your sf_upload_dir configuration to ensure you have write permissions.');
    }

    // Delete raw files
    foreach ($exportFiles as $file) {
      unlink($file[0]);
    }

    return $downloadFile;
  }

  /**
   *
   * @param array() $staffExportRecords   Complete set of staff export records.
   *                                      from buildExportRecords().
   *
   * @param array() $lookUpContent           Values for the lookup columns in the last
   *                                         sheet of the generated XLS file. From
   *                                         gatherLookupValues().
   *
   *  @return array                          An associative array of two elements,
   *                                         fileName and filePath. fileName is the
   *                                         name  of the XLS file that has been
   *                                         constructed and is held in temporary
   *                                         storage. filePath is the path to that file.
   */
  private function buildExcelWorkbook(array $staffExportRecords, array $lookUpContent, $filePath,
                                      $orgName)
  {

    /**
      // Populate the lookup sheet.
      $c = 0;
      foreach($lookUpContent as $key => $column) {
      $lookUpSheet->getCellByColumnAndRow($c, 1)->setValue($key);
      foreach($column as $k => $value) {
      $lookUpSheet->getCellByColumnAndRow($c, ($k + 2))->setValue($value);
      }
      $c++;
      }
     * 
     */
    // Write the column headers
    $keys = array_keys($staffExportRecords[0]);

    if (!isset($keys)) {
      $this->logger->err(print_r($staffExportRecords, true));
    }

    foreach ($keys as $columnHeader) {
      $this->xlsWb->label($columnHeader);
      $this->xlsWb->right();
    }

    foreach ($staffExportRecords as $staffExportRecord) {

      // Move to next row
      $this->xlsWb->down();
      $this->xlsWb->home();

      // Write values
      foreach ($staffExportRecord as $value) {
        $this->xlsWb->label($value);
        $this->xlsWb->right();
      }
    }
    $this->xlsWb->save($filePath);
    return $filePath;
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
   *                                       'Staff Resource Status' => array(
   *                                           'selectTable'  => 'agStaffResourceStatus',
   *                                           'selectColumn' => 'staff_resource_status',
   *                                           'whereColumn'  => null,
   *                                           'whereValue' => null
   *                                       ),
   *                                       'Staff Resource Type' => array(
   *                                           'selectTable'  => 'agStaffResourceType',
   *                                           'selectColumn' => 'staff_resource_type',
   *                                           'whereColumn'  => null,
   *                                           'whereValue' => null
   *                                       )
   *                          );
   *
   *                          Additional values of the $lookUps array can also be included.
   *                          The keys of the inner array musy be set to selectTable, selectColumn,
   *                          whereColumn, and whereValue.
   */
  private function gatherLookupValues($lookUps = null)
  {
    foreach ($lookUps as $key => $lookUp) {
      $lookUpQuery = agDoctrineQuery::create()
          ->select($lookUp['selectColumn'])
          ->from($lookUp['selectTable']);
      if (isset($lookUp['whereColumn']) && isset($lookUp['whereValue'])) {
        //$lookUpQuery->where("'" . $lookUp['whereColumn'] . " = ?', " . $lookUp['whereValue']);
        $lookUpQuery->where($lookUp['whereColumn'] . " = " . $lookUp['whereValue']);
      }
      $returnedLookups[$key] = $lookUpQuery->execute(null, 'single_value_array');
    }
    return $returnedLookups;
  }

}