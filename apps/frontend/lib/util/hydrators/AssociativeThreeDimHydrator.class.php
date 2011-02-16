<?php

/**
 * AssociativeThreeDimHydrator this class extends the Doctrine_Hydrator_Abstract class.
 *
 * LICENSE: This source file is subject to LGPLv3.0 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 *
 * @author     Chad Heuschober, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
class AssociativeThreeDimHydrator extends Doctrine_Hydrator_Abstract
{
  /**
   * Defines the result set as an associative array and groups, assuming the first result position
   * is an associative key
   *
   * @param <type> $stmt
   * @return array An three-dimensional associative array.
   */
  public function hydrateResultSet($stmt)
  {
    $results = $stmt->fetchAll(Doctrine_Core::FETCH_NUM);
    $array = array();
    $arrayLen = (count($results[0]) - 1) ;

    foreach ($results as $result)
    {
      $array[$result[0]][] = array_slice($result, 1, $arrayLen);
    }

    return $array;
  }
}