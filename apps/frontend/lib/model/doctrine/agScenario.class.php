<?php

/** 
* extends BaseagScenario
*
* PHP Version 5
*
* LICENSE: This source file is subject to LGPLv3.0 license
* that is available through the world-wide-web at the following URI:
* http://www.gnu.org/copyleft/lesser.html
*
* @author CUNY SPS
*
* Copyright of the Sahana Software Foundation, sahanafoundation.org
*/

/**
* @todo finish description of class in header
*/

class agScenario extends BaseagScenario
{
/**
* <description>
* @todo add description of function above and details below
* @return ???
*/
  public function __toString()
  {
    return $this->getScenario();
  }

  /**
   * Builds an index for facility.
   *
   * @return Zend_Search_Lucene_Document $doc
   */
  public function updateLucene()
  {
    $doc = new Zend_Search_Lucene_Document();
    $doc->addField(Zend_Search_Lucene_Field::Keyword('Id', $this->id, 'utf-8'));
    $doc->addField(Zend_Search_Lucene_Field::unStored('scenario', $this->scenario, 'utf-8'));
    $doc->addField(Zend_Search_Lucene_Field::unStored('description', $this->description, 'utf-8'));

    return $doc;
  }

}
