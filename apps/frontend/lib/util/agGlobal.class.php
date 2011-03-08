<?php

/**
 * Provides static access to the system-wide global parameters
 *
 * LICENSE: This source file is subject to LGPLv3.0 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 *
 * @author     Charles Wisniewski, CUNY SPS
 * @author     Usman Akeju, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 *
 * @property array $_params The global parameters, as fetched from the database, as an associative
 * array.
 */
class agGlobal
{

  private static $_params;

  /**
   * Empty private constructor prevents class instantiation; should never be called.
   */
  private function __construct()
  {

  }

  /**
   * Static method to return the requested parameter. If parameters have not be loaded, also loads
   * the parameters array.
   * 
   * @param string $key The string representation of the parameter name (datapoint) being fetched.
   * @return string The string value of the global parameter.
   * @todo consider adding _magic get* functionality
   */
  public static function getParam($key)
  {
    if (! isset(self::$_params))
    {
      self::loadParams();
    }

    return self::$_params[$key];
  }

  /**
   * Method to load the global parameters from the global parameters table and load the parameters
   * array.
   *
   * @param sfEvent $event The symfony event used to call the listener
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