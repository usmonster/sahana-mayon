<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class agListHelper
{
  public static function getStaffList($staff_ids = null, $staffStatus = 'active',
                                       $sort = null, $order = null, $staffIdType = 'staff')
  {
    list($headers, $query) = agStaffResource::getStaffResourceQuery();

    if ($staff_ids !== null) {
      if ($staffIdType == 'staff')
      {
        $query->andWhereIn('s.id', $staff_ids);
      } elseif ($staffIdType == 'staffresource') {
        $query->andWhereIn('agStaffResource.id', $staff_ids);
      }
    }
    if ($staffStatus != 'all') {
      $query->andWhere('agStaffResourceStatus.staff_resource_status = ?', $staffStatus);
    }
    if ($sort == 'organization') {
      $sortField = 'agOrganization.organization';
    }
    elseif($sort == 'resource') {
      $sortField = 'agStaffResourceType.staff_resource_type';
    }
    elseif($sort == 'fn') {
      $sortField = $headers['given'][0];//'srt.staff_resource_type';
    }
    elseif($sort == 'ln') {
      $sortField = $headers['family'][0];//'srt.staff_resource_type';
    }    
    else {
      $sortField = 's.id';
      $order = 'ASC';
    }
    $query->orderBy($sortField . ' ' . $order);

    $genericDisplayColumns = array(
      'id' => array('title' => '', 'sortable' => false, 'index' => 's_id'),
      'fn' => array('title' => 'First Name', 'sortable' => true, 'index' => 'pn1_name1'),
      'ln' => array('title' => 'Last Name', 'sortable' => true, 'index' => 'pn3_name3'),
      'organization' => array('title' => 'Organization', 'sortable' => true,
        'index' => 'agOrganization_organization'),
      'resource' => array('title' => 'Resource', 'sortable' => true, 
        'index' => 'agStaffResourceType_staff_resource_type'),
      'phones' => array('title' => 'Phone', 'sortable' => false, 'index' => 'pc_phone_contact'),
      'emails' => array('title' => 'Email', 'sortable' => false, 'index' => 'ec_email_contact'),
      'staff_status' => array('title' => 'Status', 'sortable' => false,
        'index' => 'agStaffResourceStatus_staff_resource_status'),
    );

    return array($genericDisplayColumns, $query);
  }

  private static function formatPhone($phoneContact)
  {
    $formatters = agDoctrineQuery::create()
                  ->select('pc.id')
                  ->addSelect('pf.id')
                  ->addSelect('pft.id')
                  ->addSelect('pft.replacement_pattern')
                  ->addSelect('pft.match_pattern')
                  ->from('agPhoneContact pc')
                  ->leftJoin('pc.agPhoneFormat pf')
                  ->leftJoin('pf.agPhoneFormatType pft')
                  ->where('pc.phone_contact = ?', $phoneContact)
                  ->execute(array(), Doctrine_Core::HYDRATE_SCALAR);
    return preg_replace($formatters[0]['pft_match_pattern'], $formatters[0]['pft_replacement_pattern'], $phoneContact);
  }

  public static function getFacilityList($facility_ids = null, $sort = null, $order = null)
  {

    $facility_array = array();
    $resultArray = array();
    $facility_emails = array();
    $facility_phones = array();

    // Define staff resource query.
    $query = agDoctrineQuery::create()
              ->select('f.id')
                  ->addSelect('f.facility_name')
                  ->addSelect('f.facility_code')
                  ->addSelect('fr.id')
                  ->addSelect('frt.id')
                  ->addSelect('frt.facility_resource_type')
                ->from('agFacility f')
                  ->innerJoin('f.agFacilityResource fr')
                  ->innerJoin('fr.agFacilityResourceType frt')
                ->where('1 = ?', 1); //there must be a better way to do this :)

    if ($facility_ids !== null) {
      $query->andWhereIn('f.id', $facility_ids);
    }
    if ($sort == 'facility_name') {
      $sortField = 'f.facility_name';
    }
    elseif($sort == 'facility_codes') {
      $sortField = 'f.facility_code';
    }
    else {
      $sortField = 'f.id';
      $order = 'ASC';
    }
    $query->orderBy($sortField . ' ' . $order);

    $displayColumns = array(
        'id' => array('title' => 'Id', 'sortable' => false, 'index' => 'f_id'),
        'facility_name' => array('title' => 'Facility Name', 'sortable' => true, 'index' => 'f_facility_name'),
        'services' => array('title' => 'Services', 'sortable' => false, 'index' => 'frt_facility_resource_type'),
        'facility_code' => array('title' => 'Facility Code', 'sortable' => true, 'index' => 'f_facility_code')
    );

    return array($displayColumns, $query);
  }

  public static function getOrganizationList($organization_ids = null, $sort = null, $order = null)
  {
    $organization_array = array();
    $resultArray = array();

    // Define staff resource query.
    $query = agDoctrineQuery::create()
              ->select('o.id')
                  ->addSelect('o.organization')
                  ->addSelect('o.description')
                ->from('agOrganization o')
                ->where('1 = ?', 1); //there must be a better way to do this :)

    if ($organization_ids !== null) {
      $query->andWhereIn('o.id', $organization_ids);
    }

    if ($sort == 'organization') {
      $sortField = 'o.organization';
    }
    else {
      $sortField = 'o.id';
      $order = 'ASC';
    }
    $query->orderBy($sortField . ' ' . $order);

    $displayColumns = array(
        'id' => array('title' => 'Id', 'sortable' => false, 'index' => 'o_id'),
        'organization' => array('title' => 'Organization', 'sortable' => true, 'index' => 'o_organization'),
        'description' => array('title' => 'Description', 'sortable' => true, 'index' => 'o_description')
    );

    return array($displayColumns, $query);


//    // Execute the organization query
//    $ag_organization = $query->execute(array(), Doctrine_Core::HYDRATE_SCALAR);
//
//    foreach ($ag_organization as $organization => $value)
//    {
//      $resultArray[] = array(
//        'id' => $value['o_id'],
//        'organization' => $value['o_organization'],
//        'description' => $value['o_description']
//      );
//    }
//    return $resultArray;
  }
}

?>
