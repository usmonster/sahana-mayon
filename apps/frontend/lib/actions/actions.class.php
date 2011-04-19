<?php

/**
 * extends sfActions for common functionality
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author Charles Wisniewski, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
class agActions extends sfActions
{

  protected $_searchedModels;

  public function __construct($context, $moduleName, $actionName)
  {
    parent::__construct($context, $moduleName, $actionName);
    if (empty($this->_searchedModels)) {
      // this is the original line below: $this->getModuleName(),
      // what exists now is a hack
      $this->_searchedModels = array('agStaff');
    }
  }

  public function executeSearch(sfWebRequest $request)
  {

    self::doSearch($request->getParameter('query'));
    $this->setTemplate(sfConfig::get('sf_app_dir') . DIRECTORY_SEPARATOR . 'modules/search/templates/search');
    //$this->setTemplate('global/search');
  }

  public function doSearch($searchquery, $isFuzzy = TRUE, $widget = NULL)
  {
    $models = $this->getSearchedModels();

    $this->searchquery = $searchquery;
    $this->getResponse()->setTitle('Search results for: ' . $this->searchquery);
    $query = LuceneSearch::find($this->searchquery);
    if ($isFuzzy == TRUE) {
      $query->fuzzy();
    }
    $query->in($models);
    $this->results = $query->getRecords();
    $this->hits = $query->getHits();
    $this->widget = $widget;

    $this->pager = new agArrayPager(null, 10);

    $searchResult = $query->getRecords(); //agStaff should be $models
    //if($models == 'agStaff'){
    if ($staffCollection = $searchResult['agStaff']) {
      $this->target_module = 'staff';
      $staff_ids = $staffCollection->getKeys(); // toArray();
      $query = Doctrine::getTable('agStaff')
              ->createQuery('a')
              //                  namejoin.*,
              //                  name.*,
              //                  nametype.*,
              ->select(
                  'p.id,
                    s.id,
                    ss.staff_status,
                    stfrsco.id,
                    stfrsc.staff_resource_type_id,
                    srt.staff_resource_type,
                    o.organization'
              )
              ->from(
                  'agStaff s,
                    s.agPerson p,
                    s.agStaffResource stfrsc,
                    s.agStaffStatus ss,
                    stfrsc.agStaffResourceOrganization stfrsco,
                    stfrsc.agStaffResourceType srt,
                    stfrsco.agOrganization o'
              )
              ->whereIn('s.id', $staff_ids);

      //if we do a good full select of as many values as possible, we can order by
      $ag_staffQuery = $query->getSqlQuery();
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
        $resultArray[] = array(
          'id' => $value['s_id'],
          'fn' => $person_names[$value['p_id']]['given'],
          'ln' => $person_names[$value['p_id']]['family'],
          'agency' => $value['o_organization'],
          'classification' => $value['srt_staff_resource_type'],
          'phones' => $person_phones[$value['p_id']], // only for testing, prefer the above
          'emails' => $person_emails[$value['p_id']],
          'staff_status' => $value['ss_staff_status']
            //'ess_staff_allocation_status_id' => $value['ess_staff_allocation_status_id']
            /** @todo benchmark scale */
        );
      }
    } elseif ($facilityCollection = $searchResult['agFacility']) {
      $this->target_module = 'facility';
      $staff_ids = $facilityCollection->getKeys(); // toArray();
      $query = Doctrine::getTable('agFacility')
              ->createQuery('a')
              //                  namejoin.*,
              //                  name.*,
              //                  nametype.*,
            ->select('f.*, fr.*')
            ->from('agFacility f, f.agFacilityResource fr')
          ->select(
                  'p.id,
                    s.id,
                    ss.staff_status,
                    stfrsco.id,
                    stfrsc.staff_resource_type_id,
                    srt.staff_resource_type,
                    o.organization'
              )
              ->from(
                  'agStaff s,
                    s.agPerson p,
                    s.agStaffResource stfrsc,
                    s.agStaffStatus ss,
                    stfrsc.agStaffResourceOrganization stfrsco,
                    stfrsc.agStaffResourceType srt,
                    stfrsco.agOrganization o'
              )
              ->whereIn('s.id', $staff_ids);

      //if we do a good full select of as many values as possible, we can order by
      $ag_staffQuery = $query->getSqlQuery();
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
        $staffArray[] = array(
          'id' => $value['s_id'],
          'fn' => $person_names[$value['p_id']]['given'],
          'ln' => $person_names[$value['p_id']]['family'],
          'agency' => $value['o_organization'],
          'classification' => $value['srt_staff_resource_type'],
          'phones' => $person_phones[$value['p_id']], // only for testing, prefer the above
          'emails' => $person_emails[$value['p_id']],
          'staff_status' => $value['ss_staff_status']
            //'ess_staff_allocation_status_id' => $value['ess_staff_allocation_status_id']
            /** @todo benchmark scale */
        );
      }
    } else {
      $resultArray = '';
    }
    $this->pager->setResultArray($resultArray);
    $this->pager->setResultArray($staffArray);
    $this->pager->setPage($this->getRequestParameter('page', 1));
    $this->pager->init();
    /** @todo in template, display the pager links.
     *
     *
     */
  }

  public function getSearchedModels()
  {
    return $this->_searchedModels;
  }

}
