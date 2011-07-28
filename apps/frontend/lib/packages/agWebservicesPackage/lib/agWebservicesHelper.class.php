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

    public static function getStaff()
    {
        // Incomplete yet
        $staff_dql = agDoctrineQuery::create()
            ->select('st.*, p.*, srt.*, pn.*, pdb.*')
            ->from('agStaff st')
            ->innerJoin('st.agPerson p')
            ->innerJoin('p.agPersonName pn')
            ->innerJoin('p.agPersonDateOfBirth pdb')
            ->leftJoin('st.agStaffResourceType srt');
        $v = $staff_dql->execute()->toArray();
        return self::asStaffArray($staff_dql->execute());
    }

    public static function getEvents()
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

    public static function getEventFacilities($eventId)
    {
        // Example:
        // http://localhost/mayon/webservices/getevent/:token/:event/eventFacilities.json
        // Let's use Chad's magic method for getting event facilities
        $facilityData = agEvent::getEventFacilities($eventId);

        return self::asFacilityArray($facilityData);
    }

    public static function getOrganizations()
    {
        // Incomplete yet
        $organization_dql = agDoctrineQuery::create()
            ->select('ent.*, org.*, bra.*, email.*, phone.*')
            ->from('agOrganization org')
            ->innerJoin('org.agEntity ent')
            ->leftJoin('org.agBranch bra')
            ->leftJoin('ent.agPhoneContact phone')
            ->leftJoin('ent.agEmailContact email')
            ->leftJoin('ent.agEntityAddressContact eaddr');
        return self::asOrganizationArray($organization_dql->execute());
    }

    private static function asStaffArray($result)
    {
        $results = $result->toArray();
        $response = array();
        foreach ($results as $k => $array) {
            $staffResourceTypes = array();
            $staffResourceTypesAbbr = array();
            $descriptions = array();
            $names = array();

            // foreach: adds the values in the same order for the type of staff resource,
            // its abbreviation and description. Here those three share the same keys
            foreach ($array['agStaffResourceType'] as $key => $staffResourceType) {
                $staffResourceTypes[$key] = $staffResourceType['staff_resource_type'];
                $staffResourceTypesAbbr[$key] = $staffResourceType['staff_resource_type_abbr'];
                $descriptions[$key] = $staffResourceType['description'];
            }

            // foreach: some as above, but only for names, in order to get all staff's names
            // in a single array of names
            foreach ($array['agPerson']['agPersonName'] as $key => $name) {
                $names[$key] = $name['person_name'];
            }

            // at least, we create an associative array to facilitate the creation
            // of the documents in json and xml
            $response[$k] = array(
              'id' => $array['id'],
              'person_id' => $array['person_id'],
              'created_at' => $array['created_at'],
              'updated_at' => $array['updated_at'],
              'person_names' => $names,
              'staff_resource_type' => $staffResourceTypes,
              'staff_resource_type_abbr' => $staffResourceTypesAbbr,
              'staff_resource_type_description' => $descriptions,
              'date_of_birth' => $array['agPerson']['agPersonDateOfBirth']['date_of_birth'],
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
              'branch' => (!isset($array['agBranch'])) ? $array['agBranch']['branch'] : null,
              // avoid empty values or get subvalues of the result array
              'phone' => (!isset($array['agEntity']['agPhoneContact'])) ? $array['agEntity']['agPhoneContact'] : null,
              // avoid empty values or get subvalues of the result array
              'email' => (!isset($array['agEntity']['agEmailContact'])) ? $array['agEntity']['agEmailContact'] : null
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

}