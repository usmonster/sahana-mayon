<?php

class agEventStaff extends BaseagEventStaff
{
//  public function updateLucene()
//  {
//    $doc = new Zend_Search_Lucene_Document();
//    $doc->addField(Zend_Search_Lucene_Field::Keyword('Id', $this->id, 'utf-8'));
//    return $doc;
//  }
  public function returnEventStaffList($facilityResource = NULL)
  {
    
  }
}

//Charles Wisniewski 11:45
//event staff first name, last name
//org 11:45
//status 11:45
//type 11:45
//shifts 11:45
//just keep in mind that i want to be able to filter 11:46
//so... 11:46
//$query = agDoctrineQuery::create()
//->select('a.*, afr.*, afgt.*, fr.*')
//->from('agEventStaff es, es.agStaffResource sr, sr.agStaff, FacilityGroup a, a.agEventFacilityResource afr, a.agFacilityGroupType afgt, a.agFacilityResource fr');
//
//// If the request has a faciliy resource id
//if ($this->facility_resource_id != "") {
//
//$query->where('a.facility_resource_id = ?', $this->facility_resource_id);
//i am going to filter by staff type, organization and facility resource
// $filters = array();
//foreach($request->getParameterHolder() as $parameter => $filter)
//{
//if($parameter == 'fr')
//{
//$filters['fr'] = $filter;
//}
//if($parameter == 'type')
//{
//$filters['type'] = $filter;
//}
//if($parameter == 'org')
//{
//$filters['org'] = $filter;
//}
//}

//$filters = array();
//foreach($request->getParameterHolder() as $parameter => $filter)
//{
//if($parameter == 'fr')
//{
//$filters['es.event_facility_resource_id'] = $filter;
//}
//if($parameter == 'type')
//{
//$filters['sr.staff_resource_type_id'] = $filter;
//}
//if($parameter == 'org')
//{
//$filters['sro.organization_id'] = $filter;
//}
//}
//
//$query = agDoctrineQuery::create()
//->select('es.*, sr.*, s.*, ess.*')
//->from('agEventStaff es, es.agStaffResource sr, sr.agStaffResourceOrganization sro, sr.agStaff s, es.agEventStaffShift ess, ess.agEventShift esh')
//->where('es.event_id = ?', $this->event_id);
//
//// If the request has a faciliy resource id
//if (sizeof($filters) > 0) {
//foreach($filters as $field => $filter)
//{
//$query->andWhere($field . ' = ?', $filter);
//}
//}