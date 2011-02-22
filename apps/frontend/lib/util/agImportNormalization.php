<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of agImportNormalization
 *
 * @author shirley.chan
 */
class agImportNormalization
{
  public $events = array();
  public $numRecordsNormalized;
  public $numRecordsFailed;

  function __construct($scenarioId, $sourceTable, $XLSColumnHeader)
  {
    $this->scenarioId = $scnearioId;
    $this->sourceTable = $sourceTable;
    $this->XLSColumnHeader = is_array($XLSColumnHeader) ? $XLSColumnHeader : array();
  }

  function __destruct()
  {
  }

  private function dataValidation()
  {
    // Do data validations here.
  }

  public function normalizeImport()
  {
    try {
      $this->dataValidation();

//      $query = 'SELECT f.site_id AS site_id,
//                       i.facility_name AS facility_name,
//                       i.facility_code AS facility_code
//                FROM ' . $this->sourceTable . ' AS i
//                LEFT JOIN ag_facility AS f
//                  ON f.facility_code = i.facility_code';

      $query = 'SELECT f.id AS facility_id, 
                       f.site_id AS site_id,
                       i.facility_name AS facility_name,
                       i.facility_code AS facility_code,
                       frt.id AS facility_resource_type_id,
                       i.facility_resource_type_abbr,
                       frs.id AS facility_resource_status_id,
                       fr.id AS faciltiy_resource_id,
                       i.facility_resource_status,
                       i.facility_capacity
                FROM ' . $this->sourceTable . ' AS i
                LEFT JOIN ag_facility AS f
                  ON f.facility_code = i.facility_code
                LEFT JOIN ag_facility_resource_type AS frt
                  ON frt.facility_resource_type_abbr = i.facility_resource_type_abbr
                LEFT JOIN ag_facility_resource_status AS frs
                  ON frs.facility_resource_status = i.facility_resource_status
                LEFT JOIN ag_facility_resource fr
                  ON fr.facility_id = f.id AND fr.facility_resource_type_id = frt.id';

      echo "<BR><BR>query: $query<br><BR>";
      $conn = Doctrine_Manager::connection();
      $pdo = $conn->execute($query);
      $pdo->setFetchMode(Doctrine_Core::FETCH_ASSOC);
      $sourceRecords = $pdo->fetchAll();
      print_r($sourceRecords);

      // set up a new collection.
      $facilityCollection = new Doctrine_Collection('agFacility');

      $conn->beginTransaction();

      foreach ($sourceRecords as $record)
      {
        // try to find an existing record based on a unique identifier.
        $facility = Doctrine_Core::getTable('agFacility')->findOneByFacilityCode($record['facility_code']);

        // create a new user record if no existing record is found.
//        if (!$facility instanceof agFacility) {
        if ($record['facility_id'] == NULL) {
          $entity = new agEntity();
          $entity->save();
          $site = new agSite();
          $site->set('entity_id', $entity->id);
          $site->save();
          $record['site_id'] = $site->id;
          $facility = new agFacility();
        }

//        $facility = Doctrine_Query::create()
//                ->select('f.*, fr.*')
//                ->from('agFacility f')
//                ->leftJoin('f.agFacilityResource fr ON fr.facility_id = f.id AND fr.facility_resource_type_id = ?', $record['facility_resource_type_id'])
//                ->where('f.facility_code = ?', $record['facility_code'])
//                ->fetchOne();
//
//        if (!$facility instanceof agFacility) {
//          $facArray = array();
//          $entity = new agEntity();
//          $entity->save();
//          $site = new agSite();
//          $site->set('entity_id', $entity->id);
//          $site->save();
//          $facArray['site_id'] = $site->id;
//          $facArray['facility_name'] = $record['facility_name'];
//          $facArray['facility_code'] = $record['facility_code'];
//          $facArray['agFacilityResource'][] = array('facility_resource_type_id' => $record['facility_resource_type_id'],
//                                                    'facility_resource_status_id' => $record['facility_resource_status_id'],
//                                                    'capacity' => $record['facility_capacity']);
//
//        } else {
//          $facArray = $facility->toArray(TRUE);
//          if ($facArray['agFacilityResource'] == NULL )
//          {
//            $facArray['agFacilityResource'][] = array('facility_resource_type_id' => $record['facility_resource_type_id'],
//                                                      'facility_resource_status_id' => $record['facility_resource_status_id'],
//                                                      'capacity' => $record['facility_capacity']);
//          }
//        }

        // sync record with current data.
        $facility->synchronizeWithArray($record);
//        $facility->synchronizeWithArray($facArray);
//        $facility->save();
//        $facilityResource->synchronizeWithArray($record);

        // add to collection.
        $facilityCollection->add($facility);
//        $facilityResCollection->add($facilityResource)
      }

//      // done. save collection.
      $facilityCollection->save();

    } catch (Exception $e) {
      echo '<BR><BR>Unable to normalize data.  Exception error mesage: ' . $e;
      $this->event[] = array('type' => 'ERROR', 'message' => 'Unable to normalize data.  Exception error mesage: ' . $e);
      $conn->rollBack();
    }
  }
}
