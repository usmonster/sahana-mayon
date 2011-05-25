<?php
/**
 * Provides dummy data creation capabilities
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author Chad Heuschober, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
class agStaffCreator {
  public static function createDummyStaffData($multiplier = 1)
  {
    $inserted = 0;
    $staffResourceTypes = agDoctrineQuery::create()
      ->select('srt.id')
        ->from('agStaffResourceType srt')
        ->execute(array(), agDoctrineQuery::HYDRATE_SINGLE_VALUE_ARRAY);

    $organizations = agDoctrineQuery::create()
      ->select('o.id')
        ->from('agOrganization o')
        ->execute(array(), agDoctrineQuery::HYDRATE_SINGLE_VALUE_ARRAY);

    $staffResourceStatuses = agDoctrineQuery::create()
      ->select('srs.id')
        ->from('agStaffResourceStatus srs')
        ->execute(array(), agDoctrineQuery::HYDRATE_SINGLE_VALUE_ARRAY);

    $nameTypes = agDoctrineQuery::create()
      ->select('pnt.id')
        ->from('agPersonNameType pnt')
        ->execute(array(), agDoctrineQuery::HYDRATE_SINGLE_VALUE_ARRAY);

    $names = agDoctrineQuery::create()
      ->select('pn.id')
        ->from('agPersonName pn')
        ->execute(array(), agDoctrineQuery::HYDRATE_SINGLE_VALUE_ARRAY);
    shuffle($names);
    $nameCt = count($names);
    $nameCtr = 0 ;

    $phoneContactTypes = agDoctrineQuery::create()
      ->select('pct.id')
        ->from('agPhoneContactType pct')
        ->execute(array(), agDoctrineQuery::HYDRATE_SINGLE_VALUE_ARRAY);

    $phoneFormat = agDoctrineQuery::create()
      ->select('MAX(pf.id)')
        ->from('agPhoneFormat pf')
        ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);

    $emailContactTypes = agDoctrineQuery::create()
      ->select('ect.id')
        ->from('agEmailContactType ect')
        ->execute(array(), agDoctrineQuery::HYDRATE_SINGLE_VALUE_ARRAY);

    foreach ($staffResourceTypes as $srt)
    {
      foreach ($organizations as $o)
      {
        foreach ($staffResourceStatuses as $srs)
        {
          $conn = Doctrine_Manager::connection();
          $conn->beginTransaction();
          try
          {
            $r = new agEntity();
            $r->save($conn);
            $eid = $r->getId();

            $i = 1;
            $emails = array();
            shuffle($emailContactTypes);
            foreach($emailContactTypes as $ect)
            {
              $r = new agEmailContact();
              $r['email_contact'] = $eid . '-' . $ect . '@testdata.com' ;
              $r->save($conn);
              $emailId = $r->getId();

              $r = new agEntityEmailContact();
              $r['entity_id'] = $eid;
              $r['email_contact_id'] = $emailId ;
              $r['email_contact_type_id'] = $ect;
              $r['priority'] = $i;
              $r->save($conn);

              $i++;
            }

            $i = 1;
            $phones = array();
            shuffle($phoneContactTypes);
            foreach($phoneContactTypes as $pct)
            {
             if (($phoneNumberCtr +1) == $phoneNumberCt)
             {
               shuffle($phoneNumbers);
               $phoneNumberCtr = 0;
             }
              $r = new agPhoneContact();
              $ph = sprintf('%08d%02d', $eid, $pct);
              $r['phone_contact'] = $ph;
              $r['phone_format_id'] = $phoneFormat;
              $r->save($conn);
              $phoneId = $r->getId();

              $phoneNumberCt++;

              $r = new agEntityPhoneContact();
              $r['entity_id'] = $eid;
              $r['phone_contact_id'] = $phoneId ;
              $r['phone_contact_type_id'] = $pct;
              $r['priority'] = $i;
              $r->save($conn);

              $i++;
            }
            
            $r = new agPerson();
            $r['entity_id'] = $eid;
            $r->save($conn);
            $pId = $r->getId();

            $i = 0;
            foreach ($nameTypes as $nt)
            {
              if (($nameCtr + 1) == $nameCt)
              {
                shuffle($names);
                $nameCtr = 0;
              }
              $r = new agPersonMjAgPersonName();
              $r['person_id'] = $pId;
              $r['person_name_id'] = $names[$nameCtr];
              $r['person_name_type_id'] = $nt;
              $r['priority'] = $i;
              $r->save($conn);

              $nameCtr++;
              $i++;
            }

            $r = new agStaff();
            $r['person_id'] = $pId;
            $r->save($conn);
            $sId = $r->getId();

            $r = new agStaffResource();
            $r['staff_id'] = $sId;
            $r['staff_resource_type_id'] = $srt ;
            $r['organization_id'] = $o;
            $r['staff_resource_status_id'] = $srs;
            $r->save($conn);

            $conn->commit();
            $inserted++;
          }
          catch(Exception $e)
          {
            $conn->rollback();
            throw $e ;
          }
        }
      }
    }
    return $inserted;
  }
}