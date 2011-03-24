<?php

/**
 * agQueryHelper this class defines queries associated custom functions.
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 *
 * @author     Shirley Chan, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
class agQueryHelper
{
  /**
   * Method to execute the query.  It will either return the query's row count or the result set returned from the query.
   *
   * @param string $returnType Possible values are count and results.
   * @param string $query A query string.
   * @return int|array Method can return either query's row count or the query's result set.
   */
  public static function singleScalarReturns($returnType, $query)
  {
    try
    {
      switch ($returnType) {
        case 'results':
          $resultSet = $query->execute(array(), 'key_value_pair');
          return $resultSet;
        case 'count':
          $resultSet = $query->execute(array(), Doctrine_Core::HYDRATE_RECORD);
          $rowCount = $resultSet->count();
          return $rowCount;
        default:
          throw new sfException('An error occurred. Please pass in an accepted parameter.');
      }
    }catch (\Doctrine\ORM\ORMException $e) {
      return NULL;
    }
  }
}