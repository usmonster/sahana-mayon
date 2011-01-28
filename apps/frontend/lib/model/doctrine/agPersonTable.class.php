<?php
/** 
* The table representation of a person.
*
* PHP Version 5
*
* LICENSE: This source file is subject to LGPLv3.0 license
* that is available through the world-wide-web at the following URI:
* http://www.gnu.org/copyleft/lesser.html
*
* @author Charles Wisniewski, http://sps.cuny.edu
*
* Copyright of the Sahana Software Foundation, sahanafoundation.org
*/

class agPersonTable extends Doctrine_Table
{
/**
* getForLuceneQuery gets the search terms from the front end for processing a search.
*
* @param $query
* @return $q->execute  An executed search query
*
**/
  public function getForLuceneQuery($query)
  {
    $hits = self::getLuceneIndex()->find($query);

    $pks = array();
    foreach($hits as $hit)
    {
      $pks[] = $hit->pk;
    }

    if(empty($pks))
    {
      return array();
    }

    $q = $this->createQuery('j')
      ->whereIn('j.id', $pks)
      ->limit(20);

    return $q->execute();
  }

/**
* getLuceneQuery uses the results of the Lucene query to construct a Doctrine query.
*
* @param $query
* @return $q, a constructed Doctrine query
*
**/
  public function getLuceneQuery($query)
  {
    $hits = self::getLuceneIndex()->find($query);

    $pks = array();
    foreach($hits as $hit)
    {
      $pks[] = $hit->pk;
    }

    if(empty($pks))
    {
      return array();
    }

    $q = $this->createQuery('j')
      ->whereIn('j.id', $pks)
      ->limit(20);

    return $q;
  }

/**
* getLuceneIndex creates a new Lucene index directory, or opens one if it already exists. Permissions
* in the data/[index] directory are important here, as the webserver will be writing there.
*
* @return Zend_Search_Lucene::create($index) or Zend_Search_Lucene::open($index)
*
**/
  static public function getLuceneIndex()
  {
    //ProjectConfiguration::registerZend();

    if (file_exists($index = self::getLuceneIndexFile()))
    {
      return Zend_Search_Lucene::open($index);
    }

    return Zend_Search_Lucene::create($index);
  }

/**
* getLuceneIndexFile accesses a Lucene index file within the directory accessed with getLuceneIndex.
* @return sfConfig::get('sf_data_dir').'/indexes/person.'.sfConfig::get('sf_environment').'.index'
*
**/
  static public function getLuceneIndexFile()
  {
//    return sfConfig::get('sf_data_dir').'/indexes/person.index';
    return sfConfig::get('sf_data_dir').'/search/lucene.index';
  }
}
