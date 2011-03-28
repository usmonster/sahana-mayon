<?php

/**
 * Provides Scenario Facility Group info
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author Charles Wisniewski, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 *
 */

class agScenarioFacilityGroup extends BaseagScenarioFacilityGroup
{
  public function __toString()
  {
    return $this->getScenarioFacilityGroup();
  }

  public function updateLucene()
  {
    $doc = new Zend_Search_Lucene_Document();
    $doc->addField(Zend_Search_Lucene_Field::Keyword('Id', $this->id, 'utf-8'));
    $doc->addField(Zend_Search_Lucene_Field::unStored('group', $this->getScenarioFacilityGroup(), 'utf-8'));
    $doc->addField(Zend_Search_Lucene_Field::unStored('scenario', $this->getAgScenario()->scenario . ' ' .$this->getAgScenario()->description, 'utf-8'));
    $doc->addField(Zend_Search_Lucene_Field::unStored('group_type', $this->getAgFacilityGroupType()->getFacilityGroupType(), 'utf-8'));
    $doc->addField(Zend_Search_Lucene_Field::unStored('group_status', $this->getAgFacilityGroupAllocationStatus()->facility_group_allocation_status, 'utf-8'));
    $query = agDoctrineQuery::create()
        ->select('g.id, sfr.id, fr.id, f.facility_name, frt.facility_resource_type')
        ->from('agScenarioFacilityGroup g')
        ->innerJoin('g.agScenarioFacilityResource sfr')
        ->innerJoin('sfr.agFacilityResource fr')
        ->innerJoin('fr.agFacility f')
        ->innerJoin('fr.agFacilityResourceType frt')
        ->where('g.id = ?', $this->id)
        ->execute(array(), Doctrine_Core::HYDRATE_SCALAR);
    $facilityName ='';
    $facilityResourceType ='';
    foreach($query as $result) {
      $facilityName .=  ' ' . $result['f_facility_name'];
      $facilityResourceType .= ' ' . $result['frt_facility_resource_type'];
    }
    if(isset($facilityName)) {
      $doc->addField(Zend_Search_Lucene_Field::unStored('facility_name', $facilityName, 'utf-8'));
    }
    if(isset($facilityResourceType)) {
      $doc->addField(Zend_Search_Lucene_Field::unStored('facility_resource_type', $facilityResourceType, 'utf-8'));
    }
    return $doc;
  }
}
