<?php

/**
 * agWebservices
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author Fabio Albuquerque <fabiocbalbuquerque@gmail.com>
 * @author Clayton Kramer <clayton.kramer@mail.cuny.edu>
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 * */
class agWebservicesHelper
{
    public static function getStaff(array $getParameters)
    {
         
        $staff_dql = agDoctrineQuery::create()
        ->select('st.*, p.*, sr.id, srs.*, srt.*, org.*')
        ->from('agStaff st')
        ->innerJoin('st.agPerson p')
        ->leftJoin('st.agStaffResource sr')
        ->leftJoin('sr.agStaffResourceStatus srs')
        ->leftJoin('sr.agOrganization org')
        ->leftJoin('sr.agStaffResourceType srt');

        return self::asStaffArray($staff_dql->execute());
    }

    /**
     * Prototype for persons - should be edited to 'public' when is done
     * @param $personId
     */
    private static function getPerson($personId) 
    {

        $person_dql = agDoctrineQuery::create()
        ->select('st.*, p.*, e.*, pmpn.id, pn.*, epc.id, pc.*, eec.id, ec.*')
        ->addSelect('pml.id, pmp.id, prof.id, peth.id, pnat.id, prel.id, pmar.id, psex.id, presid.id')
        ->addSelect('st.*, p.*, l.*, nat.*, rel.*, mar.*, sex.*, resid.*, nat.*, eth.*, pdb.*')
        ->from('agPerson p')
        ->innerJoin('p.agPersonMjAgPersonName pmpn')
        ->innerJoin('pmpn.agPersonName pn')

        ->innerJoin('p.agEntity e')
        ->innerJoin('e.agEntityPhoneContact epc')
        ->innerJoin('epc.agPhoneContact pc')
        ->innerJoin('e.agEntityEmailContact eec')
        ->innerJoin('eec.agEmailContact ec')

        ->innerJoin('p.agPersonMjAgLanguage pml')
        ->innerJoin('pml.agLanguage l')
        ->innerJoin('p.agPersonMjAgProfession pmp')
        ->innerJoin('pmp.agProfession prof')
        ->innerJoin('p.agPersonEthnicity peth')
        ->innerJoin('peth.agEthnicity eth')
        ->innerJoin('p.agPersonMjAgNationality pnat')
        ->innerJoin('pnat.agNationality nat')
        ->innerJoin('p.agPersonMjAgReligion prel')
        ->innerJoin('prel.agReligion rel')
        ->innerJoin('p.agPersonMaritalStatus pmar')
        ->innerJoin('pmar.agMaritalStatus mar')
        ->innerJoin('p.agPersonSex psex')
        ->innerJoin('psex.agSex sex')
        ->innerJoin('p.agPersonResidentialStatus presid')
        ->innerJoin('presid.agResidentialStatus resid')
        ->innerJoin('p.agPersonDateOfBirth pdb')

        ->where('p.id = ?', $personId);

        return self::asPersonArray($person_dql->execute());
    }


    public static function getEvents(array $getParameters)
    {

        $whereStr = 'EXISTS (SELECT subEs.id
            FROM agEventStatus subEs
            WHERE subEs.time_stamp <= CURRENT_TIMESTAMP 
            AND subEs.event_id = a.id
            HAVING MAX(subEs.time_stamp) = st.time_stamp)';

        $ag_events = agDoctrineQuery::create()
        ->select('a.*')
        ->addSelect('s.scenario')
        ->addSelect('est.event_status_type, est.description')
        ->from('agEvent a')
        ->innerJoin('a.agEventScenario es')
        ->innerJoin('es.agScenario s')
        ->innerJoin('a.agEventStatus st')
        ->innerJoin('st.agEventStatusType est')
        ->andWhere($whereStr)
        ->execute(array(), Doctrine_Core::HYDRATE_SCALAR);


        return $ag_events;
    }
    
    public static function getEventStaffTypes($eventId) {
        
        $staffTypes = agDoctrineQuery::create()
            ->select('DISTINCT s.event_id, rt.staff_resource_type')
            ->from('agEventStaff s')
            ->innerJoin('s.agStaffResource sr')
            ->innerJoin('sr.agStaffResourceType rt')
            ->where('s.event_id = ' . $eventId)
            ->execute(array(), Doctrine_Core::HYDRATE_SCALAR);
        
        return $staffTypes;
    }
    

    public static function getEventFacilities($eventId)
    {
        // Example:
        // http://localhost/mayon/webservices/getevent/:token/:event/eventFacilities.json
        // Let's use Chad's magic method for getting event facilities
        $facilityData = agEvent::getEventFacilities($eventId);

        return self::asFacilityArray($facilityData);
    }

    public static function getOrganizations(array $getParameters)
    {
        $organization_dql = agDoctrineQuery::create()
        ->select('ent.*, org.*, bran.*, enemail.*, email.*, enphone.*, phone.*, enaddr.*, addr.*')
        ->from('agOrganization org')
        ->leftJoin('org.agBranch bran')
        ->leftJoin('org.agEntity ent')
        ->leftJoin('ent.agEntityEmailContact enemail')
        ->leftJoin('enemail.agEmailContact email')
        ->leftJoin('ent.agEntityPhoneContact enphone')
        ->leftJoin('enphone.agPhoneContact phone')
        ->leftJoin('ent.agEntityAddressContact enaddr');

        return self::asOrganizationArray($organization_dql->execute());
    }

    private static function asStaffArray($result)
    {
        $results = $result->toArray();
        $response = array();
        foreach ($results as $result) {
            // Resources
            $srType = array();
            $srStatus = array();
            $srOrganization = array();
            foreach ($result['agStaffResource'] as $key => $resource) {
                // Type
                $srType['resource_type_'.$key]       = $resource['agStaffResourceType'];
                // Status
                $srStatus['resource_status_'.$key]   = $resource['agStaffResourceStatus'];
                // Organizations
                $srOrganization['organization_'.$key] = $resource['agOrganization'];
            }

            // at last, we create an associative array to facilitate the creation of json and xml documents
            $response[$result['id']] = array(
             'id' => $result['id'],
             'person' => $result['agPerson'],
             'id' => $result['id'],
             'created_at' => $result['created_at'],
             'updated_at' => $result['updated_at'],
             'resources' => array('resource_type' => $srType, 'resource_status' => $srStatus),
             'organizations' => $srOrganization
            );
        }
        return $response;
    }

    private static function asOrganizationArray($result)
    {
        $results = $result->toArray();
        $response = array();
        foreach ($results as $k => $array) {
            // an associative array to facilitate the creation
            // of the documents in json and xml
            $response[$k] = array(
              'id' => $array['id'],
              'entity_id' => $array['entity_id'],
              'organization' => $array['organization'],
              'description' => $array['description'],
              'created_at' => $array['created_at'],
              'updated_at' => $array['updated_at'],
              // avoid empty values or get subvalues of the result array
              'branch' => (!empty($array['agBranch'])) ? $array['agBranch']['branch'] : null,
              'phone' => (!empty($array['agEntity']['agEntityPhoneContact']['agPhoneContact'])) ? $array['agEntity']['agEntityPhoneContact']['agPhoneContact']['phone_contact'] : null,
              'email' => (!empty($array['agEntity']['agEntityEmailContact']['agEmailContact'])) ? $array['agEntity']['agEntityEmailContact']['agEmailContact']['email_contact'] : null,
            	'address' => (!empty($array['agEntity']['agEntityAddressContact']['agEmailContact'])) ? $array['agEntity']['agEntityEmailContact']['agEmailContact']['email_contact'] : null
            //
            );
        }
        return $response;
    }

    private static function asFacilityArray($results)
    {
        $response = array();

        foreach ($results as $k => $array) {
            // an associative array to facilitate the creation
            // of the documents in json and xml
            // Alter the street lines to match export spec
            if (isset($array['line 1'])) {
                $array['street_1'] = $array['line 1'];
                unset($array['line 1']);
            }

            if (isset($array['line 2'])) {
                $array['street_2'] = $array['line 2'];
                unset($array['line 2']);
            }

            $response[$k] = $array;
        }
        return $response;
    }

    private static function asEventArray($result)
    {
        $results = $result->toArray();
        $response = array();
        $i = 0;
    }

  public static function getFacilityEventStaff($getParameters)
  {
    $eventFacilityResourceId = $getParameters['evfac'];
    $startTime = $getParameters['start'];
    $endTime = $getParameters['end'];

    $q = agEventFacilityResource::getFacilityEventStaff($eventFacilityResourceId, $startTime,
      $endTime);

    $orderBy = 'shift_start, shift_end, ss.shift_status, srt.staff_resource_type_abbr, es.id, ' .
      'o.organization, pn_family.person_name, pn_given.person_name';
    $q->orderBy($orderBy);

    return self::asFacilityEventShiftArray($q->execute(array(), Doctrine_Core::HYDRATE_SCALAR));
  }

  public static function getFacilityShifts($getParameters)
  {
    $eventFacilityResourceId = $getParameters['evfac'];

    if (!isset($getParameters['start']) || !isset($getParameters['end'])) {
      $getParameters['start'] = NULL;
      $getParameters['end'] = NULL;
    }

    $q = agEventFacilityResource::getFacilityShifts($eventFacilityResourceId,
      $getParameters['start'], $getParameters['end']);

    return self::asFacilityEventShiftArray($q->execute(array(), Doctrine_Core::HYDRATE_SCALAR), FALSE);
  }

  private static function asFacilityEventShiftArray(array $results, $includeStaff = TRUE)
  {
    $rGrouped = array();
    $lastShiftId = NULL;
    foreach($results as $key => $r) {
      $rGrouped[$r['es_id']]['shift_status'] = $r['ss_shift_status'];
      $rGrouped[$r['es_id']]['staffing_requirements']['min'] = $r['es_minimum_staff'];
      $rGrouped[$r['es_id']]['staffing_requirements']['max'] = $r['es_maximum_staff'];
      $rGrouped[$r['es_id']]['staffing_requirements']['resource_type'] = $r['srt_staff_resource_type_abbr'];
      $rGrouped[$r['es_id']]['relative_times']['task_length'] = $r['es_task_length_minutes'];
      $rGrouped[$r['es_id']]['relative_times']['break_length'] = $r['es_break_length_minutes'];
      $rGrouped[$r['es_id']]['relative_times']['unit'] = 'minutes';
      $rGrouped[$r['es_id']]['absolute_times']['shift_start']['formatted'] = date('Y-m-d H:i:s', $r['es_shift_start']);
      $rGrouped[$r['es_id']]['absolute_times']['shift_start']['raw'] = $r['es_shift_start'];
      $rGrouped[$r['es_id']]['absolute_times']['break_start']['formatted'] = date('Y-m-d H:i:s', $r['es_break_start']);
      $rGrouped[$r['es_id']]['absolute_times']['break_start']['raw'] = $r['es_break_start'];
      $rGrouped[$r['es_id']]['absolute_times']['shift_end']['formatted'] = date('Y-m-d H:i:s', $r['es_shift_end']);
      $rGrouped[$r['es_id']]['absolute_times']['shift_end']['raw'] = $r['es_shift_end'];
      $rGrouped[$r['es_id']]['absolute_times']['timezone']['string'] = date('e');
      $rGrouped[$r['es_id']]['absolute_times']['timezone']['utc_offset'] = date('Z');
      $rGrouped[$r['es_id']]['absolute_times']['timezone']['utc_offset_unit'] = 'seconds';

      if($lastShiftId != $r['es_id']) {
        $rGrouped[$r['es_id']]['staffing_requirements']['count'] = 0;
        $lastShiftId = $r['es_id'];
        $rGrouped[$r['es_id']]['staff'] = array();
      }

      if (isset($r[12]) && $includeStaff) {
        $rGrouped[$r['es_id']]['staffing_requirements']['count']++;
        $rGrouped[$r['es_id']]['staff'][$r['est_id']]['entity_id'] = $r['e_id'];
        $rGrouped[$r['es_id']]['staff'][$r['est_id']]['organization'] = $r['o_organization'];
        $rGrouped[$r['es_id']]['staff'][$r['est_id']]['name']['given'] = $r['pn_given_given_name'];
        $rGrouped[$r['es_id']]['staff'][$r['est_id']]['name']['family'] = $r['pn_family_family_name'];
        $rGrouped[$r['es_id']]['staff'][$r['est_id']]['contacts']['email']['type'] = $r['ect_email_contact_type'];
        $rGrouped[$r['es_id']]['staff'][$r['est_id']]['contacts']['email']['value'] = $r['ec_email_contact'];
        $rGrouped[$r['es_id']]['staff'][$r['est_id']]['contacts']['phone']['type'] = $r['pct_phone_contact_type'];
        $rGrouped[$r['es_id']]['staff'][$r['est_id']]['contacts']['phone']['value'] = $r['pc_phone_contact'];
      }
      unset($results[$key]);
    }

    return $rGrouped;
  }

  public static function xml_encode($array, $rootNode, $xml = false)
  {
    if($xml === false){
      $xml = new SimpleXMLElement($rootNode);
    }
    foreach($array as $key => $value){
      if(is_array($value)){
        if(is_numeric($key)){
          $rec = $xml->addChild('element');
          $rec->addAttribute('id', $key);
          self::xml_encode($value, $rootNode, $rec);
        }else{
          self::xml_encode($value, $rootNode, $xml->addChild($key));
        }
      }else{
        if (is_numeric($key)){
          $rec = $xml->addAttribute('id', $key);
          $rec->addChild('element', $value);
        }else{
          $xml->addChild($key, $value);
        }
      }
    }

    return $xml->asXML();
  }
}