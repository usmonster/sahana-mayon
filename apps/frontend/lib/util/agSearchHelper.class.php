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
   * Simple method to hash search conditions consistently.
   * @param array $condition An array of search conditions
   * @return string An md5summed hash string.
   */
  public static function hashSearchCondition($condition)
  {
    // first sort our inner elements to ensure consistent hashes
    ksort($searchCondition);
    foreach ($searchCondition as &$condition) {
      ksort($condition);
    }

    // encode and hash
    $searchCondition = json_encode($searchCondition);
    $searchHash = md5($searchCondition);

    return $searchHash;
  }

  /**
   * Method to get a search and return its search id. If a search is not found with the provided
   * $searchCondition and $createNew is set to TRUE, a new search will be created with the provided
   * $searchTypeStr and $searchName.
   * @param array $searchCondition An array of search conditions (example below).
   * <code>array(
   *   array('condition' => $condition, 'field' => $fieldName, 'operator' => $operator),
   *   ...
   * )</code>
   * @param boolean $createNew Boolean to direct getSearchId to optionally create a new search if
   * $searchCondition is not found.
   * @param string $searchName A unique name for this search. (Only used if $createNew is TRUE)
   * @param integer $searchTypeId Search type integer id. (Only used if $createNew is TRUE)
   * @param Doctrine_Connection $conn An optional Doctrine connection object.
   * @return integer The searchId
   */
  public static function getSearchId( $searchCondition,
                                         $createNew = FALSE,
                                         $searchName = NULL,
                                         $searchTypeId = NULL,
                                         Doctrine_Connection $conn = NULL)
  {
    // first we need to hash our search condition so we can quickly search for it, sorting is
    // important to generating consistent hashes, so we do that too
    // we could use our helper method for hashing, but since we want to intercept $searchCondition
    // post-sort, we do it manually
    ksort($searchCondition);
    foreach($searchCondition as &$condition)
    {
      ksort($condition);
    }
    $searchCondition = json_encode($searchCondition);
    $searchHash = md5($searchCondition);

    // see if this search already exists
    $searchId = agSearchHelper::getSearchIdByHash($searchHash);

    // if it doesn't and the parameters are ripe, make a new search
    if ($createNew && empty($searchId))
    {
      $searchId = agSearchHelper::setNewSearch($searchCondition, $searchHash, $searchName,
        $searchTypeId, $conn);
    }

    return $searchId ;
  }

  /**
   * Method to create a new search entity in agSearch. Intended to be called from getSearchId.
   * @param string $searchCondition A json-encoded search array.
   * @param string $searchHash An md5-summed hash of the search condition.
   * @param string $searchName A unique name for this search.
   * @param integer $searchTypeId Search type integer id.
   * @param Doctrine_Connection $conn An optional Doctrine connection object.
   * @return integer The newly inserted searchId.
   */
  protected static function setNewSearch( $searchCondition,
                                          $searchHash,
                                          $searchName,
                                          $searchTypeId,
                                          Doctrine_Connection $conn = NULL)
  {
    // set up some defaults
    if (is_null($conn)) { $conn = Doctrine_Manager::connection() ; }
    $useSavepoint = ($conn->getTransactionLevel() > 0) ? TRUE : FALSE ;
    if ($useSavepoint)
    {
      $conn->beginTransaction(__FUNCTION__) ;
    }
    else
    {
      $conn->beginTransaction() ;
    }

    // create a new search object
    $newRec = new agSearch();
    $newRec['search_type_id'] = $searchTypeId;
    $newRec['search_name'] = $searchName;
    $newRec['search_condition'] = $searchCondition;
    $newRec['search_hash'] = $searchHash;

    try
    {
      $newRec->save($conn);
      if ($useSavepoint) { $conn->commit(__FUNCTION__); } else { $conn->commit(); }
    }
    catch(Exception $e)
    {
      // log our error
      $errMsg = sprintf('%s failed to commit new record: %s',
        __FUNCTION__, json_encode($newRec->toArray()));
      sfContext::getInstance()->getLogger()->err($errMsg);

      // rollback and re-throw
      if ($useSavepoint) { $conn->rollback(__FUNCTION__); } else { $conn->rollback(); }
      throw $e;
    }

    // return our results
    return $newRec->getId();
  }

  /**
   * Simple method to execute a search for a searchId by its conditions hash value.
   * @param string $searchHash A hash of the unique search conditions.
   * @return integer An empty array (if no results were found) or the integer searchId.
   */
  protected static function getSearchIdByHash($searchHash)
  {
    $q = agDoctrineQuery::create()
      ->select('s.id')
        ->from('agSearch s')
        ->where('s.search_hash = ?', $searchHash);
        //->useResultCache(TRUE, 3600, __FUNCTION__);

    $result = $q->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);

    // release the cache if nothing is returned
    if (empty($result))
    {
      $q->clearResultCache();
    }

    return $result;
  }

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
    $conditions = json_decode($searchParams['s_search_condition'],TRUE);
    $method = self::$constructorMethods[$searchType];

    return self::$method($query, $conditions);
  }

  /**
   * Method to parse a simple doctrine query search.
   * @param agDoctrineQuery $query An agDoctrineQuery object
   * @param array $conditions An array of query conditions to process
   * @return agDoctrineQuery An agDoctrineQuery object
   */
  protected static function parseDoctrineQuerySimple($query, $conditions = null)
  {
    // decode the conditions parameter and loop
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
   * @param array $conditions An array of search parameters
   * @return query A lucene query string.
   * @deprecated This is planned but currently unimplemented functionality. The data design of
   * lucene search conditions has not been completed.
   */
  protected static function parseLuceneQuery($query, $conditions)
  {
    foreach ($conditions as $condition)
    {
      $query = $query . ' ' . $condition['condition'];
    }
    return $query;
  }
}