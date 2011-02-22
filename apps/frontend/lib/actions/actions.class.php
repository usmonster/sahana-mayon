<?php

/**
 * extends sfActions for common functionality
 *
 * PHP Version 5
 *
 * LICENSE: This source file is subject to LGPLv3.0 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 *
 * @author Full Name, Organization
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
      $this->searchedModels = array($this->getModuleName());
    }
  }

  public function executeSearch(sfWebRequest $request)
  {
    self::doSearch($request->getParameter('query'));

    $this->setTemplate(sfConfig::get('sf_app_template_dir') . DIRECTORY_SEPARATOR . 'search');
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
