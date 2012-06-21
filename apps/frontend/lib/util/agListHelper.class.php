<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class agListHelper
{
  public static function getStaffList($staff_ids = null, $staffStatus = 'active',
                                        $sort = null, $order = null, $staffIdType = 'staff',
                                        $where = NULL)
  {
    $nameTypes = array('given', 'family');
    $nameTypes = agDoctrineQuery::create()
      ->select('pnt.person_name_type')
        ->addSelect('pnt.id')
      ->from('agPersonNameType pnt')
      ->whereIn('pnt.person_name_type', $nameTypes)
      ->execute(array(), agDoctrineQuery::HYDRATE_KEY_VALUE_PAIR);

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

    if ($where !== NULL) {
      // the searchable fields
      $likeSearches = array('agOrganization.organization',
        'pc.phone_contact', 'ec.email_contact', 'agStaffResourceType.staff_resource_type');
      foreach($nameTypes as $nameTypeId) {
        $likeSearches[] = 'pn' . $nameTypeId . '.person_name';
      }

      // create an equal number of parameters and clauses
      $likeParams = array_fill(0, count($likeSearches), '%' . $where . '%');
      $likeClause = '(lcase(' . implode(') LIKE ?) OR (lcase(', $likeSearches) . ') LIKE ?)';

      $query->andWhere('(' . $likeClause . ')', $likeParams);
    }

    $genericDisplayColumns = array('id' => array('title' => '', 'sortable' => false, 'index' => 's_id'));

    $genericDisplayColumns['fn'] = array('title' => 'First Name', 'sortable' => true, 'index' => 'pn' . $nameTypes['given'] . '_name' . $nameTypes['given']);
    $genericDisplayColumns['ln'] = array('title' => 'Last Name', 'sortable' => true, 'index' => 'pn' . $nameTypes['family'] . '_name' . $nameTypes['family']);

    $genericDisplayColumns['organization'] = array('title' => 'Organization', 'sortable' => true,
      'index' => 'agOrganization_organization');
    $genericDisplayColumns['resource'] = array('title' => 'Resource', 'sortable' => true,
      'index' => 'agStaffResourceType_staff_resource_type');
    $genericDisplayColumns['phones'] = array('title' => 'Phone', 'sortable' => false, 'index' => 'pc_phone_contact');
    $genericDisplayColumns['emails'] = array('title' => 'Email', 'sortable' => false, 'index' => 'ec_email_contact');
    $genericDisplayColumns['staff_status'] = array('title' => 'Status', 'sortable' => false,
      'index' => 'agStaffResourceStatus_staff_resource_status');

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


  public static function getFacilityList($sort = NULL, $order = NULL, $where = NULL)
  {
    // Define the basic facility resource query.
    $query = agDoctrineQuery::create()
      ->select('f.id')
          ->addSelect('f.facility_name')
          ->addSelect('f.facility_code')
          ->addSelect('fr.id')
          ->addSelect('frt.id')
          ->addSelect('frt.facility_resource_type')
        ->from('agFacility f')
          ->innerJoin('f.agFacilityResource fr')
          ->innerJoin('fr.agFacilityResourceType frt');

    // add in sort / order logic
    if ($sort == 'facility_name') {
      $sortField = 'f.facility_name';
    } else if ($sort == 'facility_code') {
      $sortField = 'f.facility_code';
    } else if ($sort == 'resource_type') {
      $sortField = 'frt.facility_resource_type';
    } else {
      $sortField = 'f.id';
      $order = 'ASC';
    }

    // attach the order by
    $query->orderBy($sortField . ' ' . $order);

    if ($where !== NULL) {
      // the searchable fields
      $likeSearches = array('f.facility_name', 'f.facility_code', 'frt.facility_resource_type',);

      // create an equal number of parameters and clauses
      $likeParams = array_fill(0, count($likeSearches), '%' . $where . '%');
      $likeClause = '(lcase(' . implode(') LIKE ?) OR (lcase(', $likeSearches) . ') LIKE ?)';

      $query->andWhere('(' . $likeClause . ')', $likeParams);
    }

    // set our column headers
    $columnHeaders = array(
      'id' => array('title' => '', 'sortable' => false, 'index' => 'f_id'),
      'facility_name' => array('title' => 'Facility Name', 'sortable' => true, 'index' => 'f_facility_name'),
      'resource_type' => array('title' => 'Resource Type', 'sortable' => true, 'index' => 'frt_facility_resource_type'),
      'facility_code' => array('title' => 'Facility Code', 'sortable' => true, 'index' => 'f_facility_code'),
    );


    // return the query
    return array($columnHeaders, $query);
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
