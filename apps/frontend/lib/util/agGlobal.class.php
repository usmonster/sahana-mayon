<?php

/**
 * Provides static access to the system-wide global parameters
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author     Charles Wisniewski, CUNY SPS
 * @author     Usman Akeju, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 *
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
  
  public static function clearParams()
  {
    self::$_params = array();
  }
}
