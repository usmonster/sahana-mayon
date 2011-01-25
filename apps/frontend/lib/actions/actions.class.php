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

  public function executeSearch(sfWebRequest $request)
  {
    self::doSearch($request->getParameter('query'));
    
    $this->setTemplate(sfConfig::get('sf_app_template_dir') . DIRECTORY_SEPARATOR . 'search');
    //$this->setTemplate('global/search');
  }

  public function doSearch($searchquery)
  {
    if (isset($this->searchedModels)) {
      $models = implode(',', $this->searchedModels);
    } else {
      $models = $this->getModuleName();
    }

    $this->searchquery = $searchquery;
    $this->getResponse()->setTitle('Search results for: ' . $this->searchquery);
    $query = LuceneSearch::find($this->searchquery)
            ->fuzzy()
            ->in($models);
    $this->results = $query->getRecords();
    $this->hits = $query->getHits();
  }

}
