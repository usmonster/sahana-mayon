<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class agListHelper
{
  public static function getStaffList($staff_ids = null, $staffStatus = 'active', 
                                       $sort = null, $order = null)
  {
    //TODO set limit and offset, default to null
    
    $person_array = array();
    $entity_array = array();
    $resultArray = array();
    $person_emails = array();
    $person_phones = array();

    // Define staff resource query.
    $query = agDoctrineQuery::create()
              ->select('e.id')
                  ->addSelect('p.id')
                  ->addSelect(' s.id')
                  ->addSelect('stfrsc.staff_resource_type_id')
                  ->addSelect('srt.staff_resource_type')
                  ->addSelect('srs.staff_resource_status')
                  ->addSelect('o.organization')
                ->from('agStaff s')
                  ->innerJoin('s.agPerson p')
                  ->innerJoin('p.agEntity e')
                  ->innerJoin('s.agStaffResource stfrsc')
                  ->innerJoin('stfrsc.agStaffResourceType srt')
                  ->innerJoin('stfrsc.agStaffResourceStatus srs')
                  ->innerJoin('stfrsc.agOrganization o')
                ->where('1 = ?', 1); //there must be a better way to do this :)

    if ($staff_ids !== null) {
      $query->andWhereIn('s.id', $staff_ids);
    }
    if ($staffStatus != 'all') {
      $query->andWhere('srs.staff_resource_status = ?', $staffStatus);
    }
    if ($sort == 'organization') {
      $sortField = 'o.organization';
    }
    elseif($sort == 'Resource') {
      $sortField = 'srt.staff_resource_type';
    }
    else {
      $sortField = 's.id';
      $order = 'ASC';
    }
    $query->orderBy($sortField . ' ' . $order);

    // Execute the staff resource query and grab person and entity ids into their respective array
    // variable for later use.
    $ag_staff = $query->execute(array(), Doctrine_Core::HYDRATE_SCALAR);
    foreach ($ag_staff as $key => $value)
    {
      $person_array[] = $value['p_id'];
      $entity_array[$value['p_id']] = $value['e_id'];
    }

    // Grab person's primary name by type.
    $names = new agPersonNameHelper($person_array);
    //^we need to get persons from the event staff ids that are returned here
    $person_names = $names->getPrimaryNameByType();

    // Grab person's primary emails by type.
    // Note the helper takes entity ids instead of person ids.
    $emailHelper = new agEntityEmailHelper();
    $emailByType = $emailHelper->getEntityEmailByType($entity_array, TRUE, TRUE, agEmailHelper::EML_GET_VALUE);
    foreach ($emailByType as $entity_id => $email_type)
    {
      $person_id = array_search($entity_id, $entity_array);
      // = $email_type[0][0][0]; //get the primary email
      $email_vals = array_values($email_type); //this is crazy. [0];
      $person_emails[$person_id] = $email_vals[0][0][0];
    }

    // Grab person's primary phones by type.
    // Note the helper takes entity ids instead of person ids.
    $phoneHelper = new agEntityPhoneHelper();
    $phoneByType = $phoneHelper->getEntityPhoneByType($entity_array, TRUE, TRUE, agPhoneHelper::PHN_GET_FORMATTED);
    foreach ($phoneByType as $entity_id => $phone_type)
    {
      $person_id = array_search($entity_id, $entity_array);
      $phone_vals = array_values($phone_type);
      $person_phones[$person_id] = $phone_vals[0][0][0];
    }

    foreach ($ag_staff as $staff => $value)
    {
      if (array_key_exists($value['p_id'], $person_phones)) {
        $person_phone = $person_phones[$value['p_id']];
      }
      else {
        $person_phone = '---';
      }

      if (array_key_exists($value['p_id'], $person_emails)) {
        $person_email = $person_emails[$value['p_id']];
      }
      else {
        $person_email = '---';
      }

      if (array_key_exists('given', $person_names[$value['p_id']])) {
        $person_first_name = $person_names[$value['p_id']]['given'];
      }
      else {
        $person_first_name = '---';
      }

      if(array_key_exists('family', $person_names[$value['p_id']])) {
        $person_last_name = $person_names[$value['p_id']]['family'];
      }
      else {
        $person_last_name = '---';
      }

      $resultArray[] = array(
        'id' => $value['s_id'],
        'fn' => $person_first_name,
        'ln' => $person_last_name,
        'organization' => $value['o_organization'],
        'resource' => $value['srt_staff_resource_type'],
        'phones' => $person_phone, // only for testing, prefer the above
        'emails' => $person_email,
        'staff_status' => $value['srs_staff_resource_status']
          /** @todo benchmark scale */
      );
    }
    return $resultArray;
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
