<?php
/**
 * This class is used to provide paging for array-based datasets.
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author     Found at http://snippets.symfony-project.org/snippet/177 by CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
class agArrayPager extends sfPager
{
  protected $resultsArray = null;

  public function __construct($class = null, $maxPerPage = 10)
  {
    parent::__construct($class, $maxPerPage);
  }

  public function init()
  {
    $this->setNbResults(count($this->resultsArray));

    if (($this->getPage() == 0 || $this->getMaxPerPage() == 0))
    {
     $this->setLastPage(0);
    }
    else
    {
     $this->setLastPage(ceil($this->getNbResults() / $this->getMaxPerPage()));
    }
  }

  public function setResultArray($array)
  {
    $this->resultsArray = $array;
  }

  public function getResultArray()
  {
    return $this->resultsArray;
  }

  public function retrieveObject($offset) {
    return $this->resultsArray[$offset];
  }

  public function getResults()
  {
    return array_slice($this->resultsArray, ($this->getPage() - 1) * $this->getMaxPerPage(), $this->maxPerPage);
  }

}
