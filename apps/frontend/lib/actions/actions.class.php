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

  protected $_search;

  public function __construct($context, $moduleName, $actionName)
  {
    parent::__construct($context, $moduleName, $actionName);
    if (!isset($this->_search)) {
      $this->_search = 'staff';
    }
  }

  public function executeSearch(sfWebRequest $request)
  {
    // build our url
    $url = $this->_search . '/list';
    if ($request->getPostParameter('query')) {
      $url .= '?' . http_build_query(array_merge($request->getGetParameters(),
        array('query' => strtolower(trim($request->getPostParameter('query'))),)));
    }
    $this->redirect($url);
  }



//  public function executeSearch(sfWebRequest $request)
//  {
//    $this->targetAction = 'search';
//    $string = $request->getParameter('query');
//    $pattern = "/\W/";
//    $replace = " ";
//    $this->params = '?query=' . urlencode(trim(preg_replace($pattern, $replace, $string), '+'));
////    $this->params = '?query=' . $request->getParameter('query');
//    $currentPage = ($request->hasParameter('page')) ? $request->getParameter('page') : 1;
//    self::doSearch($request->getParameter('query'), $currentPage);
//    $this->setTemplate(sfConfig::get('sf_app_dir') . DIRECTORY_SEPARATOR . 'modules/search/templates/search');
//    //$this->setTemplate('global/search');
//  }

  public function executeStatus(sfWebRequest $request)
  {
    ////TODO: module_ACTION_status instead? -UA
    //$statusId = implode('_', array($this->moduleName, 'status'));
    //$context = $this->getContext();
    ////$context = sfContext::getInstance();
    //$status = $context->has($statusId) ? $this->getContext()->get($statusId) : $statusId/*array(0, 0, 0)*/;

    //TODO: get import data directory root info from global param
    $importDataRoot = sfConfig::get('sf_upload_dir');
    $importDir = $importDataRoot . DIRECTORY_SEPARATOR . $this->moduleName;
    $statusFile = $importDir . DIRECTORY_SEPARATOR . 'status.yml';
    $status = is_readable($statusFile) ? sfYaml::load($statusFile) : array()/* array(0, 0, 0) */;
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

//  public function doSearch($searchquery, $currentPage, $isFuzzy = TRUE, $widget = NULL)
//  {
//    $models = $this->getSearchedModels();
//    $this->targetModule = 'staff';
//    $this->searchquery = $searchquery;
//    $this->getResponse()->setTitle('Search results for: ' . $this->searchquery);
//    $resultsPerPage = agGlobal::getParam('search_list_results_per_page');
//    $this->searchLimit = agGlobal::getParam('search_result_limit');
//    $this->numOfResults = 0;
//
//    Zend_Search_Lucene_Analysis_Analyzer::setDefault(new Zend_Search_Lucene_Analysis_Analyzer_Common_TextNum_CaseInsensitive());
//    $query = LuceneSearch::find($this->searchquery);
//    if ($isFuzzy == TRUE) {
//      $query->fuzzy();
//    }
//    $query->in($models);
//
//    $searchResult = $query->getRecords($this->searchLimit); //agStaff should be $models
//    // TODO
//    //a) we can return the results hydrated as scalar
//    // (only get the PK's[person,entity,staff,facility,etc])
//
//    if ($models[0] == 'agStaff')
//    {
//      $staff_ids = array();
//      $this->targetModule = 'staff';
//      if ( (isset($searchResult['agStaff']) || ($models[0] == 'agStaff')) && (count($searchResult) != 0) )
//      {
//        $staffCollection = $searchResult['agStaff'];
//        $staff_ids = $staffCollection->getKeys();
//        $this->numOfResults = count($staff_ids);
//      }
//      list($this->displayColumns, $pagerQuery) = agListHelper::getStaffList($staff_ids);
//    } elseif ($models[0] == 'agFacility') {
//      $facility_ids = array();
//      $this->targetModule = 'facility';
//      if ( (isset($searchResult['agFacility']) || ($models[0] == 'agFacility')) && (count($searchResult) != 0) )
//      {
//        $facilityCollection = $searchResult['agFacility'];
//        $facility_ids = $facilityCollection->getKeys();
//        $this->numOfResults = count($facility_ids);
//      }
//      list($this->displayColumns, $pagerQuery) = agListHelper::getFacilityList($facility_ids);
//    } elseif($models[0] == 'agOrganization') {
//      $organization_ids = array();
//      $this->targetModule = 'organization';
//      if ( (isset($searchResult['agOrganization']) || ($models[0] == 'agOrganization')) && (count($searchResult) != 0) )
//      {
//        $organizationCollection = $searchResult['agOrganization'];
//        $organization_ids = $organizationCollection->getKeys();
//        $this->numOfResults = count($organization_ids);
//      }
//      list($this->displayColumns, $pagerQuery) = agListHelper::getOrganizationList($organization_ids);
//    } else {
//      $this->forward404("Search Module Undefined.");
//    }
//
//    $this->pager = new Doctrine_Pager($pagerQuery, $currentPage, $resultsPerPage);
//    $this->data = $this->pager->execute(array(), Doctrine_Core::HYDRATE_SCALAR);
//  }

  public function getSearchedModels()
  {
    return $this->_searchedModels;
  }

}
