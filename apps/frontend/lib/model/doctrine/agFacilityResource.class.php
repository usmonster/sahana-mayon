<?php

/**
 * agFacilityResource
 *
 * extends the base FacilityResource class for added
 * functionality
 *
 * PHP Version 5
 *
 * LICENSE: This source file is subject to LGPLv3.0 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 *
 * @author Ilya Gulko, CUNY SPS
 * @author Charles Wisniewski, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
class agFacilityResource extends BaseagFacilityResource
{

  /**
   *
   * @return a string representation of the facility resource in question, this
   * string is comprised of the facility's name and the facility's resource type
   */
  public function __toString()
  {
    return $this->getAgFacility()->facility_name . " : " . $this->getAgFacilityResourceType()->facility_resource_type;
  }

  /**
   * overloads the setTableDefinition for the baseFacilityResource class, adding a listener
   */
  public function setTableDefinition()
  {
    parent::setTableDefinition() ;

    $this->addListener(new agFacilityResourceListener());
  }


  /**
   * delete()
   *
   * Before deleting this agFacilityResource record, first delete
   * all agScenarioFacilityResource records that are joined to it
   */
  public function delete(Doctrine_Connection $conn = null)
  {
    /* @todo notify user that facility resources are used by scenario(s) */
    foreach ($this->getAgScenarioFacilityResource() as $agSFR) {
      $agSFR->delete();
    }

    return parent::delete($conn);
  }

  /**
   * @method facilityResourceInfo()
   * A static method to return a double array of facility resource info in the format of 
   * array( facility resource id =>  array(facility id, facility name, facility code, facility resource type id, facility resource type, facility resource status id, facility resource status))
   * 
   * @param array $facilityResourceIds - Optional.  If param is passed in, the 
   * will only query for the specified facility resource.  If none is passed in, 
   * it will query for all facility resource.
   * 
   */
  static public function facilityResourceInfo($facilityResourceIds = null)
  {
    try
    {
//      $rawQuery = new Doctrine_RawSql();
//      $rawQuery->select('{sub.id},{sub.person_id},{sub.person_name_type_id},{sub.priority},{sub.person_name_id}, {pn.person_name}, {pnt.person_name_type}')
      $query = Doctrine_Core::getTable('agFacilityResource')
        ->createQuery('fr')
        ->select('fr.*, f.*, frt.*, frs.*')
        ->innerJoin('fr.agFacility AS f')
        ->innerJoin('fr.agFacilityResourceType AS frt')
        ->innerJoin('fr.agFacilityResourceStatus AS frs')
        ->where('1=1');

      if (is_array($facilityResourceIds) and count($facilityResourceIds) > 0)
      {
        $query->whereIn('fr.id', $facilityResourceIds);
      }

      $resultSet = $query->execute();

      $facilityResourceSet = array();
      foreach ($resultSet as $rslt)
      {
        $facility_resource_id = $rslt->getId();
        $facility_id = $rslt->getFacilityId();
        $facility_name = $rslt->getAgFacility()->getFacilityName();
        $facility_code = $rslt->getAgFacility()->getFacilityCode();
        $facility_resource_type_id = $rslt->getFacilityResourceTypeId();
        $facility_resource_type = $rslt->getAgFacilityResourceType()->getFacilityResourceType();
        $facility_resource_status_id = $rslt->getFacilityResourceStatusId();
        $facility_resource_status = $rslt->getAgFacilityResourceStatus()->getFacilityResourceStatus();

       $facilityResourceSet[$facility_resource_id] = array( 'facility_id' => $facility_id,
                                                        'facility_name' => $facility_name,
                                                        'facility_code' => $facility_code,
                                                        'facility_resource_type_id' => $facility_resource_type_id,
                                                        'facility_resource_type' => $facility_resource_type,
                                                        'facility_resource_status_id' => $facility_resource_status_id,
                                                        'facility_resource_status' => $facility_resource_status
                                                      );
      }

//      print_r($personNameArray);
      return $facilityResourceSet;
    }catch (\Doctrine\ORM\ORMException $e) {
      return NULL;
    }

  }
}