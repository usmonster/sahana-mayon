<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class agListHelper
{
  public static function getStaffList($staff_ids = null, $staffStatus = 'active', $sort = null, $order = null){
    
    $person_array = array();
    $resultArray = array();
    $person_emails = array();
    $person_phones = array();

    $query = Doctrine::getTable('agStaff')
              ->createQuery('a')
              //                  namejoin.*,
              //                  name.*,
              //                  nametype.*,
              ->select(
                  'p.id,
                    s.id,
                    stfrsc.staff_resource_type_id,
                    srt.staff_resource_type,
                    srs.staff_resource_status,
                    o.organization'
              )
              ->from(
                  'agStaff s,
                    s.agPerson p,
                    s.agStaffResource stfrsc,
                    stfrsc.agStaffResourceType srt,
                    stfrsc.agStaffResourceStatus srs,
                    stfrsc.agOrganization o'
              )
             ->where('1 = ?', 1); //there must be a better way to do this :)

if ($staff_ids != null) {
        $query->andWhereIn('s.id', $staff_ids);
}
    if ($staffStatus != 'all') {
      $query->andWhere('srs.staff_resource_status = ?', $staffStatus);
    }
   if ($sort == 'agency') {
      $sortField = 'o.organization';
      $query->orderBy($sortField . ' ' . $order);
    }
    elseif($sort == 'classification') {
      $sortField = 'srt.staff_resource_type';
      $query->orderBy($sortField . ' ' . $order);
    }
      $ag_staff = $query->execute(array(), Doctrine_Core::HYDRATE_SCALAR);
      foreach ($ag_staff as $key => $value) {
        $person_array[] = $value['p_id'];
        //$remapped_array[$ag_event_staff['es_id']] = $
      }
      $names = new agPersonNameHelper($person_array);
      //^we need to get persons from the event staff ids that are returned here    $person_names = $names->getPrimaryNameByType();
      $person_names = $names->getPrimaryNameByType();
      $emailHelper = new agEntityEmailHelper();
      $emailByType = $emailHelper->getEntityEmailByType($person_array, TRUE, TRUE, agEmailHelper::EML_GET_VALUE);
      /** @todo handle people with no email
       */
      foreach ($emailByType as $person_id => $email_type) {
        // = $email_type[0][0][0]; //get the primary email
        $email_vals = array_values($email_type); //this is crazy. [0];
        $person_emails[$person_id] = $email_vals[0][0][0];
      }

      $phoneHelper = new agEntityPhoneHelper();
      $phoneByType = $phoneHelper->getEntityPhoneByType($person_array, TRUE, TRUE, agPhoneHelper::PHN_GET_FORMATTED);
      foreach ($phoneByType as $person_id => $phone_type) {
        // = $email_type[0][0][0]; //get the primary email
        $phone_vals = array_values($phone_type); //this is crazy. [0];
        $person_phones[$person_id] = $phone_vals[0][0][0];
      }
      foreach ($ag_staff as $staff => $value) {

        if(array_key_exists($value['p_id'], $person_phones)){
          $person_phone = $person_phones[$value['p_id']];
        }
        else{
          $person_phone = '---';
        }
        if(array_key_exists($value['p_id'], $person_emails)){
          $person_email = $person_emails[$value['p_id']];
        }
        else{
          $person_email = '---';
        }
        $resultArray[] = array(
          'id' => $value['s_id'],
          'fn' => $person_names[$value['p_id']]['given'],
          'ln' => $person_names[$value['p_id']]['family'],
          'agency' => $value['o_organization'],
          'classification' => $value['srt_staff_resource_type'],
          'phones' => $person_phone, // only for testing, prefer the above
          'emails' => $person_email,
          'staff_status' => $value['srs_staff_resource_status']
            //'ess_staff_allocation_status_id' => $value['ess_staff_allocation_status_id']
            /** @todo benchmark scale */
        );
      }
    return $resultArray;


  }


}

?>
