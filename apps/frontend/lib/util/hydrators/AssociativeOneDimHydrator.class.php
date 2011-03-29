<?php

/**
 * Extends the Doctrine_Hydrator_Abstract class.
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author     Chad Heuschober, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
class AssociativeOneDimHydrator extends Doctrine_Hydrator_Abstract
{

  /**
   * Defines the result set as an associative array and groups, assuming the first result position
   * is an associative key
   *
   * @param <type> $stmt
   * @return array A two-dimensional associative array with an inner positional array.
   */
  public function hydrateResultSet($stmt)
  {
    $results = $stmt->fetchAll(Doctrine_Core::FETCH_NUM);
    $array = array();

    foreach ($results as $result) {
      $array[$result[0]][] = $result[1];
    }

    return $array;
  }

}