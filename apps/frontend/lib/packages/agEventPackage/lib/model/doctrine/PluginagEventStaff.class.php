<?php

/**
 * PluginagEventStaff is an extension of base class agEventStaff
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author     Charles Wisniewski, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
abstract class PluginagEventStaff extends BaseagEventStaff
{

  public function updateLucene()
  {

    //Charles Wisniewski @ 00:11 03/08/2011: perhaps this should be abstracted to a 'staff helper'
    //or parts of it, to be called when updating lucene index for eventstaff AND regular staff
    $doc = new Zend_Search_Lucene_Document();
    $doc->addField(Zend_Search_Lucene_Field::Keyword('Id', $this->id, 'utf-8'));
    //$doc->addField(Zend_Search_Lucene_Field::UnStored('staff_status', $this->getAgStaffStatus()->staff_status, 'utf-8'));
    $query = agDoctrineQuery::create()
            ->select('e.id, s.id, sr.id, st.id, st.staff_resource_type, sro.id, o.id, o.organization, o.description')
             ->from('agEventStaff e')
            ->innerJoin('e.agStaff s')
            ->innerJoin('s.agStaffResource sr')
            ->innerJoin('sr.agStaffResourceType st')
            ->innerJoin('sr.agStaffResourceOrganization sro')
            ->innerJoin('sro.agOrganization o')
            ->where('e.id = ?', $this->id);

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
    return $doc;

  }


}