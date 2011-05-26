<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class agListHelper
{
  public static function getStaffList($staff_ids = null, $staffStatus = 'active', 
                                       $sort = null, $order = null, $limit = null, $offset = null)
  {

    $newquery = agStaffResource::getStaffResourceQuery();
    $headers = $newquery[0];
    $query = $newquery[1];

    if ($staff_ids !== null) {
      $query->andWhereIn('s.id', $staff_ids);
    }
    if ($staffStatus != 'all') {
      $query->andWhere('srs.staff_resource_status = ?', $staffStatus);
    }
    if ($sort == 'organization') {
      $sortField = 'o.organization';
    }
    elseif($sort == 'resource') {
      $sortField = 'srt.staff_resource_type';
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

    if($limit !=null) $query->limit($limit);
    if($offset !=null) $query->offset($offset);
    
    $ag_staff = $query->execute(array(), Doctrine_Core::HYDRATE_SCALAR);


    foreach ($ag_staff as $staff => $value)
    {
      $given_return = $headers['given'][1];
      $family_return = $headers['family'][1];
      $resultArray[] = array(
        'id' => $value['s_id'],
        'fn' => $value[$given_return],
        'ln' => $value[$family_return],
        'organization' => $value['o_organization'],
        'resource' => $value['srt_staff_resource_type'],
        'phones' => ($value['pc_phone_contact'] == null ? $value['pc_phone_contact'] : agListHelper::formatPhone($value['pc_phone_contact'])),
        'emails' => $value['ec_email_contact'],
        'staff_status' => $value['srs_staff_resource_status']
          /** @todo benchmark scale */
      );
    }
    return $resultArray;
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

    // Execute the facility resource query
    $ag_facility = $query->execute(array(), Doctrine_Core::HYDRATE_SCALAR);

    foreach ($ag_facility as $facility => $value)
    {
      $resultArray[] = array(
        'id' => $value['f_id'],
        'facility_name' => $value['f_facility_name'],
        'services' => $value['frt_facility_resource_type'],
        'facility_codes' => $value['f_facility_code']
      );
    }
    return $resultArray;
  }

}

?>
