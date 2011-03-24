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

  protected $searchedModels;

  public function __construct($context, $moduleName, $actionName)
  {
    parent::__construct($context, $moduleName, $actionName);
    if (empty($this->searchedModels)) {
      $this->searchedModels = array('agStaff'); // this is the original: $this->getModuleName(), what exists now is a hack
    }
  }

  public function executeSearch(sfWebRequest $request)
  {

    self::doSearch($request->getParameter('query'));

    $this->setTemplate(sfConfig::get('sf_app_dir') . DIRECTORY_SEPARATOR . 'modules/search/templates/search');
    //$this->setTemplate('global/search');
  }

  public function doSearch($searchquery, $is_fuzzy = TRUE, $widget = NULL)
  {
    $models = $this->getSearchedModels();

    $this->searchquery = $searchquery;
    $this->getResponse()->setTitle('Search results for: ' . $this->searchquery);
    $query = LuceneSearch::find($this->searchquery);
    if ($is_fuzzy == TRUE) {
      $query->fuzzy();
    }
    $query->in($models);
    $this->results = $query->getRecords();
    $this->hits = $query->getHits();
    $this->widget = $widget;
  }

  public function getSearchedModels()
  {
    return $this->searchedModels;
  }

}
