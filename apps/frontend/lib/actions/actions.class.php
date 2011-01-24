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
    $this->searchquery = $request->getParameter('query');
    $this->getResponse()->setTitle('Search results for: ' . $this->searchquery);
    $query = LuceneSearch::find($this->searchquery)
            ->fuzzy()
            ->in($this->getModuleName());
    $this->results = $query->getRecords();
    $this->hits = $query->getHits();
    $this->setTemplate(sfConfig::get('sf_app_template_dir') . DIRECTORY_SEPARATOR . 'search');
    //return sfView::$this->renderPartial('search', array('hits' => $this->hits));
    //$this->setTemplate(sfConfig::get('sf_app_template_dir') . DIRECTORY_SEPARATOR . 'search');
  }

}
