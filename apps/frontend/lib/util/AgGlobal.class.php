<?php

/**
 * AgGlobal this class provides static access to the system-wide global parameters
 *
 * LICENSE: This source file is subject to LGPLv3.0 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 *
 * @author     Charles Wisniewski, CUNY SPS
 * @author     Usman Akeju, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
class AgGlobal
{

  private static $_params;

  /**
   * Empty private constructor to prevent class instantiation; should never be called.
   */
  private function __construct()
  {

  }

  //TODO: consider adding _magic set*/get* functionality?

  public static function getParam($key, $isBool = false)
  {
    if (!isset(self::$_params)) {
      self::loadParams();
    }
    if (empty(self::$_params)) {
      echo 'empty!';
    }
    if ($isBool) {
      return (bool) self::$_params[$key];
    }
    return self::$_params[$key];
  }

  /**
   * Initializes all global parameters into a static associative array.
   * @todo Create a function to limit by hostname and use the $hostname argument
   */
  public static function loadParams(sfEvent $event = null)
  {
    $query = agDoctrineQuery::create()
            ->select('gp.datapoint')
              ->addSelect('gp.value')
            ->from('agGlobalParam gp');
//        ->where('gp.hostname = ?', $hostname);

    self::$_params = $query->execute(array(), 'key_value_pair');
  }

  /**
   * Method to return a boolean value from a string 1/0 representation in agGlobalParam
   * @deprecated Don't use this; prefer getParam($key, TRUE) instead.
   * @param string $paramString The parameter being returned.
   * @return boolean The boolean value of $paramString
   */
  public static function returnBool($paramString)
  {
    $val = self::$_params[$paramString];

    if ($val == '0') {
      $result = FALSE;
    } else if ($val == '1') {
      $result = TRUE;
    } else {
      throw new Doctrine_DataDict_Exception("The parameter value '" . $val . "' is not of the correct type (boolean).");
    }

    return $result;
  }

}