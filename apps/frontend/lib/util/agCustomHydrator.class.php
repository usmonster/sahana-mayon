<?php

/**
 * KeyValuePairHydrator this class extends the Doctrine_Hydrator_Abstract class.
 *
 * LICENSE: This source file is subject to LGPLv3.0 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 *
 * @author     Shirley Chan, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
class KeyValuePairHydrator extends Doctrine_Hydrator_Abstract
{
  /**
   * Defines the result set as an associative array.
   * 
   * @param <type> $stmt
   * @return array An associate array.
   */
  public function hydrateResultSet($stmt)
  {
    $results = $stmt->fetchAll(Doctrine_Core::FETCH_NUM);
    $array = array();
    foreach ($results as $result)
    {
      $array[$result[0]] = $result[1];
    }

    return $array;
  }
}

/**
 * KeyValuePairHydrator this class extends the Doctrine_Hydrator_Abstract class.
 *
 * LICENSE: This source file is subject to LGPLv3.0 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 *
 * @author     Shirley Chan, CUNY SPS
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
    foreach ($results as $result)
    {
      $array[$result[0]] = array_slice($result, -1, -1);
    }

    return $array;
  }
}

class GisPointCoordinateHydrator extends Doctrine_Hydrator_Abstract
{
  /**
   * Defines the result set as a positional array.
   *
   * @param <type> $stmt
   * @return array An associate array.
   */
  public function hydrateResultSet($stmt)
  {
    $results = $stmt->fetchAll(Doctrine_Core::FETCH_NUM);
    $array = array();
    foreach ($results as $result)
    {
      $array[] = array($result[0], $result[3], $result[4]);
    } 

    return $array;
  }
}

class StatusHydrator extends Doctrine_Hydrator_Abstract
{
  /**
   * Defines the result set as a nested associative array.
   *
   * @param <type> $stmt
   * @return array An associative array.
   */
  public function hydrateResultSet($stmt)
  {
    $results = $stmt->fetchAll(Doctrine_Core::FETCH_NUM);
    $array = array();
    foreach ($results as $result)
    {
      $array[$result[0]] = array($result[1], $result[2]);
    }

    return $array;
  }
}

class SingleValueArrayHydrator extends Doctrine_Hydrator_Abstract
{
  /**
   * Defines the result set as a positional array.
   *
   * @param <type> $stmt
   * @return array An associate array.
   */
  public function hydrateResultSet($stmt)
  {
    $results = $stmt->fetchAll(Doctrine_Core::FETCH_NUM);
    $array = array();
    foreach ($results as $result)
    {
      $array[] = $result[0] ;
    }

    return $array;
  }
}