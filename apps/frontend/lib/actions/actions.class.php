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

  public function executeStatus(sfWebRequest $request)
  {
    //TODO: module_ACTION_status instead? -UA
    $statusId = implode('_', array($this->moduleName, 'status'));
    //$context = $this->getContext();
    ////$context = sfContext::getInstance();
    //$status = $context->has($statusId) ? $this->getContext()->get($statusId) : $statusId/*array(0, 0, 0)*/;

    //TODO: get import data directory root info from global param
    $importDataRoot = sfConfig::get('sf_upload_dir');
    $importDir = $importDataRoot . DIRECTORY_SEPARATOR . $this->moduleName;
    $statusFile = $importDir . DIRECTORY_SEPARATOR . 'status.yml';
    $status = is_readable($statusFile) ? sfYaml::load($statusFile) : $statusId/* array(0, 0, 0) */;
    $format = $request->getRequestFormat();
    if ('json' == $format) {
      $this->getResponse()->setHttpHeader('Content-Type', 'application/json; charset=utf-8');
      $status = json_encode($status);
    }
    //TODO: else, use partial instead of returning?
    //TODO: the else block below is for testing -- remove when finished
    else {
      $this->getResponse()->setHttpHeader('Content-Type', 'text/plain; charset=utf-8');
      $status = json_encode($status);
    }

    return $this->renderText($status);
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
    $this->limit = null;
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
        // @todo change the above to use a FacilityList return
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
