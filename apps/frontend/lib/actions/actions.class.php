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
      $this->_searchedModels = array('agStaff', 'agFacility');
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
    $this->target_module = 'staff';
    $this->searchquery = $searchquery;
    $this->getResponse()->setTitle('Search results for: ' . $this->searchquery);

    Zend_Search_Lucene_Analysis_Analyzer::setDefault(new Zend_Search_Lucene_Analysis_Analyzer_Common_TextNum_CaseInsensitive());
    $query = LuceneSearch::find($this->searchquery);
    if ($isFuzzy == TRUE) {
      $query->fuzzy();
    }
    $query->in($models);
    $this->results = $query->getRecords();
    $this->hits = $query->getHits();
    $this->widget = $widget;
    $resultArray = array();

    $this->pager = new agArrayPager(null, 10);

    $searchResult = $query->getRecords(); //agStaff should be $models
    
    // TODO
    //a) we can return the results hydrated as scalar
    // (only get the PK's[person,entity,staff,facility,etc])
    //if($models == 'agStaff'){

    if (count($searchResult) > 0) {
      if (isset($searchResult['agStaff'])) {
        $this->target_module = 'staff';
        $staffCollection = $searchResult['agStaff'];
        $staff_ids = $staffCollection->getKeys(); // toArray();
        $resultArray = agListHelper::getStaffList($staff_ids);
      } elseif (isset($searchResult['agFacility'])) {
        $this->target_module = 'facility';
        $facilityCollection = $searchResult['agFacility'];
        $facility_ids = $facilityCollection->getKeys(); // toArray();
        $resultArray = agListHelper::getFacilityList($facility_ids);

        /** @todo change the above to use a FacilityList return
         * 
         */
      } else {
        //$resultArray = array();
      }
    }

    $this->pager->setResultArray($resultArray);
    // $this->pager->setResultArray($staffArray);
    $this->pager->setPage($this->getRequestParameter('page', 1));
    $this->pager->init();
  }

  public function getSearchedModels()
  {
    return $this->_searchedModels;
  }

}
