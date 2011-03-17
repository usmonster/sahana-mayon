<?php

/**
 * PluginagStaff is an extension of base class agStaff
 *
 * LICENSE: This source file is subject to LGPLv3.0 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 *
 * @author     Shirley Chan, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
abstract class PluginagStaff extends BaseagStaff
{

  public function updateLucene()
  {

    //Charles Wisniewski @ 00:11 03/08/2011: perhaps this should be abstracted to a 'staff helper'
    //or parts of it, to be called when updating lucene index for eventstaff AND regular staff
    $doc = new Zend_Search_Lucene_Document();
    $doc->addField(Zend_Search_Lucene_Field::Keyword('Id', $this->id, 'utf-8'));
    $doc->addField(Zend_Search_Lucene_Field::UnStored('staff_status', $this->getAgStaffStatus()->staff_status, 'utf-8'));

    // Saving staff info after staff record is created.
    // Make staff searchable by name.
    $query = agDoctrineQuery::create()
            ->select('s.id, p.id, pmn.id, pn.id, pn.person_name')
            ->from('agStaff s')
            ->innerJoin('s.agPerson p')
            ->innerJoin('p.agPersonMjAgPersonName pmn')
            ->innerJoin('pmn.agPersonName pn')
            ->where('s.id=?', $this->id);
    $queryString = $query->getSQLQuery();
    $staffNames = $query->execute(array(), Doctrine_Core::HYDRATE_SCALAR);

    $staff_name = null;
    foreach ($staffNames as $stf) {
      $staff_name .= ' ' . $stf['pn_person_name'];
    }
    if (isset($staff_name)) {
      $doc->addField(Zend_Search_Lucene_Field::unStored('staff_name', $staff_name, 'utf-8'));
    }

    // Make staff searchable by phone.
    $query = agDoctrineQuery::create()
            ->select('s.id, p.id, e.id, epc.id, pc.id, pc.phone_contact')
            ->from('agStaff s')
            ->innerJoin('s.agPerson p')
            ->innerJoin('p.agEntity e')
            ->innerJoin('e.agEntityPhoneContact epc')
            ->innerJoin('epc.agPhoneContact pc')
            ->where('s.id=?', $this->id);
    $staffPhones = $query->execute(array(), Doctrine_Core::HYDRATE_SCALAR);

    $staff_phone = null;
    foreach ($staffPhones as $stf) {
      $staff_phone .= ' ' . $stf['pc_phone_contact'];
    }
    if (isset($staff_phone)) {
      $doc->addField(Zend_Search_Lucene_Field::unStored('staff_phone', $staff_phone, 'utf-8'));
    }

    // Make staff searchable by email.
    $query = agDoctrineQuery::create()
            ->select('s.id, p.id, e.id, eec.id, ec.id, ec.email_contact')
            ->from('agStaff s')
            ->innerJoin('s.agPerson p')
            ->innerJoin('p.agEntity e')
            ->innerJoin('e.agEntityEmailContact eec')
            ->innerJoin('eec.agEmailContact ec')
            ->where('s.id=?', $this->id);
    $queryString = $query->getSQLQuery();
    $staffEmails = $query->execute(array(), Doctrine_Core::HYDRATE_SCALAR);

    $staff_email = null;
    foreach ($staffEmails as $stf) {
      $staff_email .= ' ' . $stf['ec_email_contact'];
    }
    if (isset($staff_email)) {
      $doc->addField(Zend_Search_Lucene_Field::unStored('staff_email', $staff_email, 'utf-8'));
    }

    // Make staff searchable by address
    $query = agDoctrineQuery::create()
            ->select('s.id, p.id, e.id, eac.id, a.id, ama.id, av.id, aa.id, aa.alias, av.value')
            ->from('agStaff s')
            ->innerJoin('s.agPerson p')
            ->innerJoin('p.agEntity e')
            ->innerJoin('e.agEntityAddressContact eac')
            ->innerJoin('eac.agAddress a')
            ->innerJoin('a.agAddressMjAgAddressValue ama')
            ->innerJoin('ama.agAddressValue av')
            ->leftJoin('av.agAddressAlias aa')
            ->where('s.id=?', $this->id);
    $queryString = $query->getSQLQuery();
    $staffAddresses = $query->execute(array(), Doctrine_Core::HYDRATE_SCALAR);

    $staff_address = null;
    foreach ($staffAddresses as $stf) {
      $staff_address .= ' ' . $stf['av_value'];
      $aliasAddress = $stf['aa_alias'];
      if ($aliasAddress != null) {
        $staff_address .= ' ' . $aliasAddress;
      }
    }
    if (isset($staff_address)) {
      $doc->addField(Zend_Search_Lucene_Field::unStored('staff_address', $staff_address, 'utf-8'));
    }

    // Make staff searchable by language
    $query = agDoctrineQuery::create()
            ->select('s.id, p.id, pml.id, l.language')
            ->from('agStaff s')
            ->innerJoin('s.agPerson p')
            ->innerJoin('p.agPersonMjAgLanguage pml')
            ->innerJoin('pml.agLanguage l')
            ->where('s.id=?', $this->id);

    $staffLangs = $query->execute(array(), Doctrine_Core::HYDRATE_SCALAR);

    $staff_lang = null;
    foreach ($staffLangs as $stf) {
      $staff_lang .= ' ' . $stf['l_language'];
    }
    if (isset($staff_lang)) {
      $doc->addField(Zend_Search_Lucene_Field::unStored('staff_lang', $staff_lang, 'utf-8'));
    }

    $query = agDoctrineQuery::create()
            ->select('s.id, sr.id, st.id, st.staff_resource_type, sro.id, o.id, o.organization, o.description')
            ->from('agStaff s')
            ->innerJoin('s.agStaffResource sr')
            ->innerJoin('sr.agStaffResourceType st')
            ->innerJoin('sr.agStaffResourceOrganization sro')
            ->innerJoin('sro.agOrganization o')
            ->where('s.id=?', $this->id);

    $staffOrgs = $query->execute(array(), Doctrine_Core::HYDRATE_SCALAR);

    $staff_org = null;
    $staff_type = null;
    foreach ($staffOrgs as $stf) {
      if ($stf['o_organization'] != null) {
        $staff_org .= ' ' . $stf['o_organization'];
        $org_desc = $stf['o_description'];
        if ($org_desc != null) {
          $staff_org .= ' ' . $org_desc;
        }
      }
      if ($stf['st_staff_resource_type'] != null) {
        $staff_type .= ' ' . $stf['st_staff_resource_type'];
      }
    }
    if (isset($staff_org)) {
      $doc->addField(Zend_Search_Lucene_Field::unStored('staff_org', $staff_org, 'utf-8'));
    }
    if (isset($staff_type)) {
      $doc->addField(Zend_Search_Lucene_Field::unStored('staff_type', $staff_type, 'utf-8'));
    }

    # Make a staff searchable by profession.
    $query = agDoctrineQuery::create()
            ->select('s.id, p.id, pmp.id, pf.id, pf.profession')
            ->from('agStaff s')
            ->innerJoin('s.agPerson p')
            ->innerJoin('p.agPersonMjAgProfession pmp')
            ->innerJoin('pmp.agProfession pf')
            ->where('s.id=?', $this->id);

    $staffProfs = $query->execute(array(), Doctrine_Core::HYDRATE_SCALAR);

    $staff_prof = null;
    foreach ($staffProfs as $stf) {
      $staff_prof .= ' ' . $stf['pf_profession'];
    }
    if (isset($staff_prof)) {
      $doc->addField(Zend_Search_Lucene_Field::unStored('staff_prof', $staff_prof, 'utf-8'));
    }

    return $doc;
  }

  /**
   * getUniqueStaffCount() is a static method that returns different result sets depending on the pass-in groupByMode:
   *   groupByMode 1 - returns an integer total count of unique staff.
   *   groupByMode 2 - returns an array of array( organization id => array( staff resource type id => unique staff count ) )
   *   groupByMode 3 - returns an array of array( organization id => unique staff count )
   *   groupByMode 4 - returns an array of array( staff resource type id => unique staff count )
   *
   * NOTE: Method returns 0 if no staff is defined in agStaff table.
   *
   * @param integer $groupByMode Accepts integer value ranging from 1-4.
   * @param array $organizationIds (Optional) Queries staff count for the specified organizations only.
   *   Note: This param is ignored in groupByMode 1 and 4.
   * @param array $staffResourceTypeIds (Optional) Queries staff count for the specified staff resource type only.
   *   Note: This param is ignored in groupByMode 1 and 3.
   */
  static public function getUniqueStaffCount($groupByMode, $organizationIds = array(),
                                             $staffResourceTypeIds = array())
  { // Need to combine staffcount and newStaffCount to one function.
    try {
      // Should check table agStaff is emtpy.  If empty, there is no staff.
//      $staffCount = 0;
//      $staff = Doctrine_Core::getTable('agStaff')
//        ->createQuery('s')
//        ->select('count(*) as count')
//        ->execute();
//
//      /*
//       * Return 0 staff count if agStaff table is empty.
//       */
//      if ($staff[0]['count'] == 0)
//      {
//        return $staffCount;
//      }

      switch ($groupByMode) {
        case 1:
          /**
           * Returns an integer of the total staff count.
           */
          $staffCount = agDoctrineQuery::create()
                  ->select('count(*) as count')
                  ->from('agStaff as s')
                  ->execute(array(), Doctrine::HYDRATE_SINGLE_SCALAR);
          return $staffCount;

        case 2:
          $query = agDoctrineQuery::create()
                  ->select('sro.organization_id as orgId, sr.staff_resource_type_id as stfRsrcTypId, count(distinct sr.staff_id) as count')
                  ->from('agStaff as s')
                  ->innerJoin('s.agStaffResource as sr')
                  ->innerJoin('sr.agStaffResourceOrganization as sro')
                  ->where('1=1');

          /*
           * Append a where clause to query for specific organizations if an
           * array of organziation id is passed in as argument.
           */
          if (is_array($organizationIds) and count($organizationIds) > 0) {
            $query->whereIn('sro.organization_id', $organizationIds);
          }

          /*
           * Append a where clause to query for specific staff resource type if
           * an array of staff resource type id is passed in as argument.
           */
          if (is_array($staffResourceTypeIds) and count($staffResourceTypeIds) > 0) {
            $query->whereIn('sr.staff_resource_type_id', $staffResourceTypeIds);
          }

          $query->groupBy('orgId, stfRsrcTypId');

          break;

        case 3:
          $query = agDoctrineQuery::create()
                  ->select('o.id as orgId, count(distinct sr.staff_id) as count')
                  ->from('agOrganization as o')
                  ->leftJoin('o.agStaffResourceOrganization as sro')
                  ->leftJoin('sro.agStaffResource as sr')
                  ->where('1=1');

          /*
           * Append a where clause to query for specific organizations if an
           * array of organziation id is passed in as argument.
           */
          if (is_array($organizationIds) and count($organizationIds) > 0) {
            $query->whereIn('o.id', $organizationIds);
          }

          $query->groupBy('orgId');

          break;

        case 4:
          $query = agDoctrineQuery::create()
                  ->select('srt.id as stfRsrcTypId, count(distinct s.id) as count')
                  ->from('agStaffResourceType as srt')
                  ->leftJoin('srt.agStaff as s')
                  ->where('1=1');

          /*
           * Append a where clause to query for specific staff resource type if
           * an array of staff resource type id is passed in as argument.
           */
          if (is_array($staffResourceTypeIds) and count($staffResourceTypeIds) > 0) {
            $query->whereIn('srt.id', $staffResourceTypeIds);
          }

          $query->groupBy('srt.id');

          break;

        default:
          // Returns a string of error message if an invalid groupByMode is passed-in.
          return 'Invalid mode.';
      }

      $resultSet = $query->execute(array(), Doctrine::HYDRATE_SCALAR);

      $staffCount = array();
      switch ($groupByMode) {
        // Populates $staffCount = array( organization id => array( staff resource type id => staff count by organization and staff resource type ) ).
        case 2:
          foreach ($resultSet as $rslt) {
            $key1 = $rslt['sro_orgId'];
            $key2 = $rslt['sr_stfRsrcTypId'];
            $value = $rslt['sr_count'];
            $tempArray = array($key2 => $value);
            if (array_key_exists($key1, $staffCount)) {
              $oldArray = $staffCount[$key1];
              $newArray = $oldArray + $tempArray;
              $staffCount[$key1] = $newArray;
            } else {
              $staffCount[$key1] = array($key2 => $value);
            }
          }
          break;

        // Populates $staffCount = array( organization id => staff count by organization ).
        case 3:
          foreach ($resultSet as $rslt) {
            $key = $rslt['o_orgId'];
            $value = $rslt['sr_count'];
            $staffCount[$key] = $value;
          }
          break;

        // Populates $staffCount = array( staff resource type id => staff count by staff resource type ).
        case 4:
          foreach ($resultSet as $rslt) {
            $key = $rslt['srt_stfRsrcTypId'];
            $value = $rslt['s_count'];
            $staffCount[$key] = $value;
          }
          break;
      }

      return $staffCount;
    } catch (\Doctrine\ORM\ORMException $e) {
      return NULL;
    }
  }

}