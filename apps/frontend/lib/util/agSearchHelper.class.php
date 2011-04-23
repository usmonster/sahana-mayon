<?php
/**
 * Provides generic search helper features.
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author Chad Heuschober, CUY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 *
 */
class agSearchHelper
{
  protected static $constructorMethods = array(
    'doctrine_query_simple' => 'parseDoctrineQuerySimple',
    'lucene_query' => 'parseLuceneQuery') ;

  /**
   * Method to return a searchTypeId from a search type string.
   * @param string $searchTypeStr Search type string value
   * @return integer Search type ID
   */
  protected static function getSearchTypeId($searchTypeStr)
  {
    $q = agDoctrineQuery::create()
      ->select('st.id')
        ->from('agSearchType st')
        ->where('st.search_type = ?', $searchTypeStr)
        ->useResultCache(TRUE, 3600, __FUNCTION__);

    return $q->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR) ;
  }

  /**
   * Method to return a search record by its ID.
   * @param integer $searchId An individual search id.
   * @return array Search parameters.
   */
  public static function getSearchParams($searchId)
  {
    $q = agDoctrineQuery::create()
      ->select('s.id')
          ->addSelect('s.search_name')
          ->addSelect('s.search_condition')
          ->addSelect('st.id')
          ->addSelect('st.search_type')
        ->from('agSearch s')
          ->innerJoin('s.agSearchType st')
        ->where('s.id = ?', $searchId)
        ->useResultCache(TRUE, 3600, __FUNCTION__);


    // we're only getting one row back so we can reduce our return array nesting by one
    $params = $q->execute(array(), Doctrine_Core::HYDRATE_SCALAR);
    return $params[0];
  }

  /**
   * Method to parse a query and return the parsed query object. Serves as a routing method to
   * chain queries to the correct method based on $searchType.
   * @param variable A query variable. This can be an object, string, or any other data type a query
   * can inhabit.
   * @param integer $searchId The search_id being called.
   * @return <type>
   */
  public static function parseQuery($query, $searchId )
  {
    $searchParams = self::getSearchParams($searchId);
    $searchType = $searchParams['st_search_type'];
    $method = self::$constructorMethods[$searchType];
    return self::$method($query, $searchParams);
  }

  protected static function parseDoctrineQuerySimple($query, $searchParams)
  {
    // decode the conditions parameter and loop
    $conditions = json_decode($searchParams['s_search_condition'],TRUE);
    foreach ($conditions as $condition)
    {
      // build the left-side of the condition as a string and add it and the actual condition to our
      // doctrine query object
      $leftCondition = sprintf('%s %s (?)', $condition['field'], $condition['operator']);
      $query = $query->andWhere($leftCondition, $condition['condition']);
    }

    // return the doctrine query object
    return $query;
  }

  /**
   * Method to parse a lucene query and a set of search parameters, returning an amended query with
   * the search parameters added.
   * @param string $query A lucene query string.
   * @param array $searchParams An array of search parameters
   * @return query A lucene query string.
   * @deprecated This is planned but currently unimplemented functionality. The data design of
   * lucene search conditions has not been completed.
   */
  protected static function parseLuceneQuery($query, $searchParams)
  {
    $conditions = json_decode($searchParams['s_search_condition'], TRUE);
    foreach ($conditions as $condition)
    {
      $query = $query . ' ' . $condition['condition'];
    }
    return $query;
  }
}