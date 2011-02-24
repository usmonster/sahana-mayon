<?php

/**
 * agGlobal this class provides static access to the system-wide global parameters
 *
 * LICENSE: This source file is subject to LGPLv3.0 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 *
 * @author     Charles Wisniewski, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
class agGlobal
{
  public static $param = array() ;

  /**
   * This function initializes all global parameters into a static, public associative array
   * @todo Create a function to limit by hostname and use the $hostname argument
   */
  public static function initGlobal()
  {
    $query = Doctrine_Query::create()
      ->select('gp.datapoint')
          ->addSelect('gp.value')
        ->from('agGlobalParam gp') ;
//        ->where('gp.hostname = ?', $hostname) ;

   self::$param = $query->execute(array(), 'key_value_pair') ;
  }
} agGlobal::initGlobal() ;
