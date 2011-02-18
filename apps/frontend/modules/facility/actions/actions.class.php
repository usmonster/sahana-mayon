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
    $query = Doctrine_Core::getTable('agFacility')
            ->createQuery('a')
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

  /**
   * executeExport()
   *
   * Export all facility data to an Excel file using PHPExcel
   * plugin.
   *
   * @param $request sfWebRequest object for current page request
   */
  public function executeExport()
  {
    $facilities = Doctrine::getTable('agFacility')
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

    require_once 'PHPExcel/Cell/AdvancedValueBinder.php';
    PHPExcel_Cell::setValueBinder(new PHPExcel_Cell_AdvancedValueBinder());

    $objPHPExcel = new sfPhpExcel();
    $objPHPExcel->setActiveSheetIndex(0);

    $objPHPExcel->getProperties()->setCreator("Agasti 2.0");
    $objPHPExcel->getProperties()->setLastModifiedBy("Agasti 2.0");
    $objPHPExcel->getProperties()->setTitle("Facility List");

    $objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setName('Arial');
    $objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setSize(12);

    $row = 2;
    foreach ($facilities as $facility) {
      $headings = array('Code', 'Name', 'Resource Type', 'Resource Status', 'Resource Capacity');
      $content = array('Code' => $facility->facility_code, 'Name' => $facility->facility_name);

      $types = null;
      $statuses = null;
      $capacities = null;
      $c = count($facility->getAgFacilityResource());
      $i = 1;
      foreach ($facility->getAgFacilityResource() as $res) {
        if ($i <> $c) {
          $types = $types . ucwords($res->getAgFacilityResourceType()->facility_resource_type) . "\n";
          $statuses = $statuses . ucwords($res->getAgFacilityResourceStatus()->facility_resource_status) . "\n";
          $capacities = $capacities . $res->capacity . "\n";
        } else {
          $types = $types . ucwords($res->getAgFacilityResourceType()->facility_resource_type);
          $statuses = $statuses . ucwords($res->getAgFacilityResourceStatus()->facility_resource_status);
          $capacities = $capacities . $res->capacity;
        }
        $i++;
      }
      $content['Resource Type'] = $types;
      $content['Resource Status'] = $statuses;
      $content['Resource Capacity'] = $capacities;

      foreach ($phoneTypes as $phoneType) {
        $headings[] = ucwords($phoneType->phone_contact_type) . ' Phone';
        $j = Doctrine::getTable('AgEntityPhoneContact')->findByDql('entity_id = ? AND phone_contact_type_id = ?', array($facility->getAgSite()->entity_id, $phoneType->id))->getFirst();
        $content[ucwords($phoneType->phone_contact_type) . ' Phone'] = ($j ? preg_replace($j->getAgPhoneContact()->getAgPhoneFormat()->getAgPhoneFormatType()->match_pattern, $j->getAgPhoneContact()->getAgPhoneFormat()->getAgPhoneFormatType()->replacement_pattern, $j->getAgPhoneContact()->phone_contact) : '');
      }
      foreach ($emailTypes as $emailType) {
        $headings[] = ucwords($emailType->email_contact_type) . ' Email';
        $j = Doctrine::getTable('AgEntityEmailContact')->findByDql('entity_id = ? AND email_contact_type_id = ?', array($facility->getAgSite()->entity_id, $emailType->id))->getFirst();
        $content[ucwords($emailType->email_contact_type) . ' Email'] = ($j ? $j->getAgEmailContact()->email_contact : '');
      }
      foreach ($addressTypes as $addressType) {
        $headings[] = ucwords($addressType->address_contact_type) . ' Address';
        $j = Doctrine::getTable('AgEntityAddressContact')->findByDql('entity_id = ? AND address_contact_type_id = ?', array($facility->getAgSite()->entity_id, $addressType->id))->getFirst();
        $tempContainer = array();
        if ($j) {
          foreach ($j->getAgAddress()->getAgAddressStandard()->getAgAddressFormat() as $addressFormat) {
            foreach ($j->getAgAddress()->getAgAddressMjAgAddressValue() as $mj) {
              if ($addressFormat->getAgAddressElement()->id == $mj->getAgAddressValue()->address_element_id) {
                $i = 1;
                $c = count($j);
                if (isset($tempContainer['Line ' . $addressFormat->line_sequence])) {
                  $tempContainer['Line ' . $addressFormat->line_sequence] =
                      $tempContainer['Line ' . $addressFormat->line_sequence] . $addressFormat->pre_delimiter . $mj->getAgAddressValue()->value . $addressFormat->post_delimiter;
                } else {
                  $tempContainer['Line ' . $addressFormat->line_sequence] = $addressFormat->pre_delimiter . $mj->getAgAddressValue()->value . $addressFormat->post_delimiter;
                }
              }
            }
          }
        } else {
          $tempContainer[ucwords($addressType->address_contact_type) . ' Address'] = null;
        }
        $content[ucwords($addressType->address_contact_type) . ' Address'] = implode("\n", $tempContainer);
      }

      $content['Created'] = $facility->created_at;
      $content['Updated'] = $facility->updated_at;
      array_push($headings, 'Created', 'Updated');
      foreach ($headings as $hKey => $heading) {
        $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($hKey, 1)->setValue($heading);
        foreach ($content as $eKey => $entry) {
          if ($eKey == $heading) {
            $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($hKey, $row)->setValueExplicit($entry);
          }
        }
      }
      $row++;
    }
    $highestColumn = $objPHPExcel->getActiveSheet()->getHighestColumn();
    $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);

    for ($i = $highestColumnIndex; $i >= 0; $i--) {
      $objPHPExcel->getActiveSheet()->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($i))->setAutoSize(true);
    }
    // Save Excel 2007 file
    $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
    $todaydate = date("d-m-y");
    $todaydate = $todaydate . '-' . date("H-i-s");
    $filename = 'Facilities';
    $filename = $filename . '-' . $todaydate;
    $filename = $filename . '.xls';
    $filePath = realpath(sys_get_temp_dir()) . '/' . $filename;
    $objWriter->save($filePath);

    $this->getResponse()->setHttpHeader('Content-Type', 'application/vnd.ms-excel');
    $this->getResponse()->setHttpHeader('Content-Disposition', 'attachment;filename="' . $filename . '"');

    $exportFile = file_get_contents($filePath);

    $this->getResponse()->setContent($exportFile);
    $this->getResponse()->send();
    unlink($filePath);
  }

  public function executeFacilityExport()
  {
    // Define some variables and definitions.
//    $primaryOnly = TRUE;
//    $contactType = 'work';
//    $addressStandard = 'us standard';
//
//    // This is the start of the definition list for XLS headers.
//    $exportHeaders = array('Facility Name', 'Facility Code', 'Facility Resource Type Abbr',
//        'Facility Resource Status', 'Facility Capacity', 'Facility Activation Sequence',
//        'Facility Allocation Status', 'Facility Group', 'Facility Group Type',
//        'Facility Group Allocation Status', 'Faciltiy Group Activation Sequence',
//        'Work Email', 'Work Phone');
//
//    $addressFormat = Doctrine_Query::create()
//            ->select('ae.address_element')
//            ->from('agAddressElement ae')
//            ->innerJoin('ae.agAddressFormat af')
//            ->innerJoin('af.agAddressStandard astd')
//            ->where('astd.address_standard=?', $addressStandard)
//            ->andWhere('ae.address_element<>?', 'zip+4')
//            ->orderBy('af.line_sequence, af.inline_sequence')
//            ->execute(array(), 'single_value_array');
//
//    // Construct header values for address fields.
//    $addressHeaders = array();
//    foreach ($addressFormat as $add)
//    {
//      switch ($add)
//      {
//        case 'line 1':
//          $addressHeaders[] = 'Street 1';
//          break;
//        case 'line 2':
//          $addressHeaders[] = 'Street 2';
//          break;
//        case 'zip5':
//          $addressHeaders[] = 'Postal Code';
//        default:
//          $addressHeaders[] = ucwords($add);
//       }
//    }
//    // Add the address headers to the list already defined, then add the geo headers.
//    $exportHeaders = array_merge($exportHeaders,$addressHeaders);
//    array_push($exportHeaders, "longitude", "latitude");
//
//    // Query the staff resource type table, then build headers for the staff
//    // type minimum and maximums, then append to the header list.
//    $staffResourceTypes = Doctrine_Query::create()
//            ->select('srt.staff_resource_type, srt.id')
//            ->from('agStaffResourceType srt')
//            ->execute(array(), 'single_value_array');
//    $stfHeaders = array();
//    foreach($staffResourceTypes as $stfResType)
//    {
//      if (strtolower($stfResType) == 'staff')
//      {
//        $stfResType = 'generalist';
//      } else {
//        $stfResType = strtolower(str_replace(' ', '_', $stfResType));
//      }
//      $stfHeaders[] = $stfResType . '_min';
//      $stfHeaders[] = $stfResType . '_max';
//    }
//    $exportHeaders = array_merge($exportHeaders, $stfHeaders);
//
//    // Use the agFacilityHelper to gather information specific to facilities.
//    $facilityGeneralInfo = agFacilityHelper::facilityGeneralInfo('Scenario');
//    $facilityAddress = agFacilityHelper::facilityAddress($addressStandard, $primaryOnly, $contactType);
//    $facilityGeo = agFacilityHelper::facilityGeo($primaryOnly, $contactType);
//    $facilityEmail = agFacilityHelper::facilityEmail($primaryOnly, $contactType);
//    $facilityPhone = agFacilityHelper::facilityPhone($primaryOnly, $contactType);
//    $facilityStaffResource = agFacilityHelper::facilityStaffResource();
//
//    $facilityExportRecords = array();
//    foreach ($facilityGeneralInfo as $fac)
//    {
//      $i = 0;
//      $entry = array();
//      $entry['Facility Name'] = $fac['f_facility_name'];
//      $entry['Facility Code'] = $fac['f_facility_code'];
//      $entry['Facility Resource Type Abbr'] = $fac['frt_facility_resource_type_abbr'];
//      $entry['Facility Resource Status'] = $fac['frs_facility_resource_status'];
//      $entry['Facility Capacity'] = $fac['fr_capacity'];
//      $entry['Facility Activation Sequence'] = $fac['sfr_activation_sequence'];
//      $entry['Facility Allocation Status'] = $fac['fras_facility_resource_allocation_status'];
//      $entry['Facility Group'] = $fac['sfg_scenario_facility_group'];
//      $entry['Facility Group Type'] = $fac['fgt_facility_group_type'];
//      $entry['Facility Group Allocation Status'] = $fac['fgas_facility_group_allocation_status'];
//      $entry['Facility Group Activation Sequence'] = $fac['sfg_activation_sequence'];
//
//      // Facility email
//      if (array_key_exists($fac['f_id'], $facilityEmail))
//      {
//        $priorityNumber = key($facilityEmail[$fac['f_id']][$contactType]);
//        $entry['Work Email'] = $facilityEmail[$fac['f_id']][$contactType][$priorityNumber];
//      } else {
//        $entry['Work Email'] = null;
//      }
//
//      // Facility phone numbers
//      if (array_key_exists($fac['f_id'], $facilityPhone))
//      {
//        $priorityNumber = key($facilityPhone[$fac['f_id']][$contactType]);
//        $entry['Work Phone'] = $facilityPhone[$fac['f_id']][$contactType][$priorityNumber];
//      } else {
//        $entry['Work Phone'] = null;
//      }
//
//      // Facility address
//      $addressId = null;
//      if (array_key_exists($fac['f_id'], $facilityAddress))
//      {
//        $priorityNumber = key($facilityAddress[ $fac['f_id'] ][$contactType]);
//        $addressId = $facilityAddress[ $fac['f_id'] ][$contactType][$priorityNumber]['address_id'];
//
//        foreach($addressFormat as $key => $addr)
//        {
//          switch ($addr)
//          {
//            case 'line 1':
//              $exp_index = 'Street 1';
//              break;
//            case 'line 2':
//              $exp_index = 'Street 2';
//              break;
//            case 'zip5':
//              $exp_index = 'Postal Code';
//            default:
//              $exp_index = ucwords($addr);
//           }
//
//
//          if (array_key_exists($addr, $facilityAddress[ $fac['f_id'] ][$contactType][$priorityNumber]))
//          {
//            $entry[ $exp_index ] = $facilityAddress[ $fac['f_id'] ][$contactType][$priorityNumber][$addr];
//          } else {
//            $entry[ $exp_index ] = null;
//          }
//        }
//      } else {
//        $entry = $entry + array_combine($addressHeaders, array_fill(count($entry), count($addressHeaders), NULL));
//      }
//
//      // facility geo.
//      if (array_key_exists($fac['f_id'], $facilityGeo))
//      {
//        if (isset($addressId))
//        {
//          if (array_key_exists($addressId, $facilityGeo[ $fac['f_id'] ]))
//          {
//            $entry['Longitude'] = $facilityGeo[ $fac['f_id'] ][$addressId]['longitude'];
//            $entry['Latitude'] = $facilityGeo[ $fac['f_id'] ][$addressId]['latitude'];
//          } else {
//            $entry = $entry + array_combine(array('Longitude', 'Latitude'), array_fill(count($entry), 2, NULL));
//          }
//        } else {
//          $entry = $entry + array_combine(array('Longitude', 'Latitude'), array_fill(count($entry), 2, NULL));
//        }
//      } else {
//        $entry = $entry + array_combine(array('Longitude', 'Latitude'), array_fill(count($entry), 2, NULL));
//      }
//
//      // Use the staff resource types returned from the query above to get the actual
//      // staff type minimums and maximums.
//      foreach($staffResourceTypes as $stfResType)
//      {
//        if (strtolower($stfResType) == 'staff')
//        {
//          $exp_index = 'generalist';
//        } else {
//          $exp_index = strtolower(str_replace(' ', '_', $stfResType));
//        }
//
//        if (array_key_exists($fac['sfr_id'], $facilityStaffResource))
//        {
//          if(array_key_exists($stfResType, $facilityStaffResource[ $fac['sfr_id'] ]))
//          {
//            $entry[$exp_index . '_min'] = $facilityStaffResource[ $fac['sfr_id'] ][$stfResType]['minimum staff'];
//            $entry[$exp_index . '_max'] = $facilityStaffResource[ $fac['sfr_id'] ][$stfResType]['maximum staff'];
//          } else {
//            $entry = $entry + array_combine(array($exp_index . '_min', $exp_index . '_max'), array_fill(count($entry), 2, NULL));
//          }
//        } else {
//          $entry = $entry + array_combine(array($exp_index . '_min', $exp_index . '_max'), array_fill(count($entry), 2, NULL));
//        }
//      }
//
//      // Append the array just built to the total list of records.
//      $facilityExportRecords[] = $entry;
//    }
//$a = 3;
//$lookUps = array(
//    'Facility Resource Status' => array(
//        'selectTable'  => 'agFacilityResourceStatus',
//        'selectColumn' => 'facility_resource_status',
//        'whereColumn'  => null,
//        'whereValue' => null
//    ),
//    'Facility Resource Type Abbr' => array(
//        'selectTable'  => 'agFacilityResourceType',
//        'selectColumn' => 'facility_resource_type_abbr',
//        'whereColumn'  => null,
//        'whereValue' => null
//    ),
//    'Facility Allocation Status' => array(
//        'selectTable'  => 'agFacilityResourceAllocationStatus',
//        'selectColumn' => 'facility_resource_allocation_status',
//        'whereColumn'  => null,
//        'whereValue' => null
//    ),
//    'Facility Group Allocation Status' => array(
//        'selectTable'  => 'agFacilityGroupAllocationStatus',
//        'selectColumn' => 'facility_group_allocation_status',
//        'whereColumn'  => null,
//        'whereValue' => null
//    ),
//    'Borough' => array(
//        'selectTable'  => 'agAddressValue',
//        'selectColumn' => 'value',
//        'whereColumn'  => 'address_element_id',
//        'whereValue' => Doctrine_Query::create()
//                          ->select('id')
//                          ->from('agAddressElement')
//                          ->where('address_element = ?', 'borough')
//                          ->execute(null, Doctrine_Core::HYDRATE_SINGLE_SCALAR)
//    ),
//    'State' => array(
//        'selectTable'  => 'agAddressValue',
//        'selectColumn' => 'value',
//        'whereColumn'  => 'address_element_id',
//        'whereValue' => Doctrine_Query::create()
//                          ->select('id')
//                          ->from('agAddressElement')
//                          ->where('address_element = ?', 'state')
//                          ->execute(null, Doctrine_Core::HYDRATE_SINGLE_SCALAR)
//    ),
//    'Facility Group Type' => array(
//        'selectTable'  => 'agFacilityGroupType',
//        'selectColumn' => 'facility_group_type',
//        'whereColumn'  => null,
//        'whereValue' => null
//    )
//);
//$lookUpContent = $this->gatherLookupValues($lookUps);
$facilityExportRecords = agFacilityExportHelper::export();
echo '<pre>';
print_r($facilityExportRecords);
echo '</pre>';




//    /** Error reporting */
//    error_reporting(E_ALL);
//
//    /** Include path **/
//    ini_set('include_path', ini_get('include_path').';../Classes/');
//
//    /** PHPExcel */
//    include 'PHPExcel.php';
//
//    /** PHPExcel_Writer_Excel2007.php */
//    include 'PHPExcel/Writer/Excel2007.php';
//
//    // Create new PHPExcel object
//    echo date('H:i:s') . "Create new PHPExcel Object \n";
//    $objPHPExcel = new PHPExcel();
//
//
//    // Set properties
//    echo date('H:i:s') . " Set properties\n";
//    $objPHPExcel->getProperties()->setCreator("Agasti 2.0");
//    $objPHPExcel->getProperties()->setLastModifiedBy("Agasti 2.0");
//    $objPHPExcel->getProperties()->setTitle("Facility List");
//    $objPHPExcel->getProperties()->setSubject("Facility List");
//    $objPHPExcel->getProperties()->setDescription("Facility List");
//
//    // Set active sheet and style format
//    echo date('H:i:s') . " Set active sheet and style format\n";
//    $objPHPExcel->setActiveSheetIndex(0);
//    $objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setName('Arial');
//    $objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setSize(12);
//
//    // Add some data
//    echo date('H:i:s') . " Add some data\n";
//    $objPHPExcel->getActiveSheet()->SetCellValue('A1', 'Hello');
//    $objPHPExcel->getActiveSheet()->SetCellValue('B2', 'world!');
//    $objPHPExcel->getActiveSheet()->SetCellValue('C1', 'Hello');
//    $objPHPExcel->getActiveSheet()->SetCellValue('D2', 'world!');
//
//    // Rename sheet
//    echo date('H:i:s') . " Rename sheet\n";
//    $objPHPExcel->getActiveSheet()->setTitle('Simple');
//
//    // Save Excel 2007 file
//    echo date('H:i:s') . " Write to Excel2007 format\n";
//    $todaydate = date("d-m-y");
//    $todaydate = $todaydate . '-' . date("H-i-s");
//    $filename = 'Facilities';
//    $filename = $filename . '-' . $todaydate;
//    $filename = $filename . '.xlsx';
//    $filePath = realpath(sys_get_temp_dir()) . '/' . $filename;
//    echo $filePath;
//    $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
//    $objWriter->save($filePath);
//
//    $this->getResponse()->setHttpHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'true');
//    $this->getResponse()->setHttpHeader('Content-Disposition', 'attachment;filename="' . $filename . '"');
//
//    $exportFile = file_get_contents($filePath);
//
//    $this->getResponse()->setContent($exportFile);
//    $this->getResponse()->send();
//    unlink($filePath);
//
//    // Echo done
//    echo date('H:i:s') . " Done writing file.\r\n";
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
      $lookUpQuery = Doctrine_Query::create()
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
