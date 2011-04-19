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
    $query = agDoctrineQuery::create()
            ->select('e.id, sr.id, s.id')
             ->from('agEventStaff e')
            ->innerJoin('e.agStaffResource sr')
            ->innerJoin('sr.agStaff s')
            ->where('e.id = ?', $this->id);

    $staffResources = $query->execute(array(), Doctrine_Core::HYDRATE_SCALAR);

    //the event staff member will ALWAYS be a staff resource first

    foreach ($staffResources as $stf) {
      $doc->addField(Zend_Search_Lucene_Field::unStored('staff', $stf['s_id'], 'utf-8'));
    }

    return $doc;

  }


}