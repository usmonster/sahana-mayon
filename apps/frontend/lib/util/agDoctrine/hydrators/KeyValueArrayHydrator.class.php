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
class KeyValueArrayHydrator extends Doctrine_Hydrator_Abstract
{

  /**
   * Defines the result set as an associative array.
   *
   * @param <type> $stmt
   * @return array An associative array.
   */
  public function hydrateResultSet($stmt)
  {
    $results = $stmt->fetchAll(Doctrine_Core::FETCH_NUM);
    $array = array();
    if (!empty($results)) {
      $arrayLen = (count($results[0]) - 1);

      foreach ($results as $result) {
        $array[$result[0]] = array_slice($result, 1, $arrayLen);
      }
    }
    return $array;
  }

}