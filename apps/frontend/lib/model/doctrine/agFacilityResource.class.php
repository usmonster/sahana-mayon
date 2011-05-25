<?php

/**
 * agFacilityResource extends the base FacilityResource class
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author Ilya Gulko, CUNY SPS
 * @author Charles Wisniewski, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
class agFacilityResource extends BaseagFacilityResource
{

  /**
   * Builds an index for facility resource
   *
   * The Lucene Facility Index allows for a facility to be searched by:
   * id, Facility Code, and extends to:
   * Facility Name, Facility Resource Type, Facility e-mail,
   * and Facility Phone, which are indexed in the agFacility class
   *
   * @return Zend_Search_Lucene_Document $doc
   *
   */
  public function updateLucene()
  {
    $doc = new Zend_Search_Lucene_Document();
    $doc->addField(Zend_Search_Lucene_Field::Keyword('Id', $this->id, 'utf-8'));
  }

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
    parent::setTableDefinition();

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
    try {
//      $rawQuery = new Doctrine_RawSql();
//      $rawQuery->select('{sub.id},{sub.person_id},{sub.person_name_type_id},{sub.priority},{sub.person_name_id}, {pn.person_name}, {pnt.person_name_type}')
      $query = Doctrine_Core::getTable('agFacilityResource')
              ->createQuery('fr')
              ->select('fr.*, f.*, frt.*, frs.*')
              ->innerJoin('fr.agFacility AS f')
              ->innerJoin('fr.agFacilityResourceType AS frt')
              ->innerJoin('fr.agFacilityResourceStatus AS frs')
              ->where('1=1');

      if (is_array($facilityResourceIds) and count($facilityResourceIds) > 0) {
        $query->whereIn('fr.id', $facilityResourceIds);
      }

      $resultSet = $query->execute();

      $facilityResourceSet = array();
      foreach ($resultSet as $rslt) {
        $facility_resource_id = $rslt->getId();
        $facility_id = $rslt->getFacilityId();
        $facility_name = $rslt->getAgFacility()->getFacilityName();
        $facility_code = $rslt->getFacilityCode();
        $facility_resource_type_id = $rslt->getFacilityResourceTypeId();
        $facility_resource_type = $rslt->getAgFacilityResourceType()->getFacilityResourceType();
        $facility_resource_status_id = $rslt->getFacilityResourceStatusId();
        $facility_resource_status = $rslt->getAgFacilityResourceStatus()->getFacilityResourceStatus();

        $facilityResourceSet[$facility_resource_id] = array('facility_id' => $facility_id,
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
    } catch (\Doctrine\ORM\ORMException $e) {
      return NULL;
    }
  }

  public static function getFacilityResourceQuery()
  {
    $q = agDoctrineQuery::create()
      ->select('fr.id AS facility_resource_id')
          ->addSelect('f.id AS facility_id')
          ->addSelect('sfr.id')
          ->addSelect('sfg.id')
          ->addSelect('e.id')
          ->addSelect('s.id')
          ->addSelect('a.id')
          ->addSelect('f.facility_name')
          ->addSelect('pc.phone_contact')
          ->addSelect('ec.email_contact')
          ->addSelect('frt.facility_resource_type_abbr')
          ->addSelect('sfg.scenario_facility_group')
          ->addSelect('epc.id')
          ->addSelect('pc.id')
          ->addSelect('eec.id')
          ->addSelect('ec.id')
        ->from('agFacility AS f')
          ->innerJoin('f.agSite AS s')
          ->innerJoin('s.agEntity AS e')
          ->innerJoin('f.agFacilityResource AS fr')
          ->innerJoin('fr.agFacilityResourceType AS frt')
          ->innerJoin('fr.agScenarioFacilityResource AS sfr')
          ->innerJoin('sfr.agScenarioFacilityGroup AS sfg')
          ->leftJoin('e.agEntityPhoneContact AS epc')
          ->leftJoin('epc.agPhoneContact AS pc')
          ->leftJoin('epc.agPhoneContactType AS pct WITH pct.phone_contact_type = ?', 'work')
          ->leftJoin('e.agEntityEmailContact AS eec')
          ->leftJoin('eec.agEmailContact AS ec')
          ->leftJoin('eec.agEmailContactType AS ect WITH ect.email_contact_type = ?', 'work');

    $emailWhere = '(' .
        '(EXISTS (' .
        'SELECT subE.id ' .
        'FROM agEntityEmailContact AS subE ' .
        'WHERE subE.entity_id = eec.entity_id ' .
        'HAVING MIN(subE.priority) = eec.priority' .
        ')) ' .
        'OR (eec.id IS NULL)' .
        ')';
    $q->where($emailWhere);

    $phoneWhere = '(' .
        '(EXISTS (' .
        'SELECT subP.id ' .
        'FROM agEntityPhoneContact AS subP ' .
        'WHERE subP.entity_id = epc.entity_id ' .
        'HAVING MIN(subP.priority) = epc.priority' .
        ')) ' .
        'OR (epc.id IS NULL)' .
        ')';
    $q->andWhere($phoneWhere);

    $results = $q->getSqlQuery();
    #print_r($results);
    return $results;
  }

}