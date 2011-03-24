<?php
/**
 * DatabaseHelper this class defines queries associated custom functions.
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 *
 * @author     Chad Heuschober, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */

class agDatabaseHelper
{
  public static $dbEngine ;

  /**
   * Function to return the current database engine.
   *
   * @return string
   */
  protected static function databaseEngine()
  {
    $conn = Doctrine_Manager::connection() ;
    $driver = $conn->getDriverName() ;
    $driver = strtolower($driver) ;

    return $driver ;
  }

  /**
   * Function to test the current database engine against the parameter.
   *
   * @param string $dbTestEngine The user-submitted engine to test against.
   * @return boolean
   */
  public static function testDbEngine($dbTestEngine)
  {
    self::$dbEngine = self::databaseEngine() ;
    $result = ((self::$dbEngine == $dbTestEngine) ? true : false) ;

    return $result ;
  }

  /**
   * Function to trigger an error if $codeEngine does not match the current $dbEngine.
   *
   * @param string $dbTestEngine A lowercase string representing the database engine currently being tested.
   */
  public static function displayPortabilityWarning($dbTestEngine)
  {
    if (! self::testDbEngine($dbTestEngine))
    {
      $notice = sprintf('This code might have a database portability issue. It was tested on "%s" but you are trying to run it on "%s".', $dbTestEngine, self::$dbEngine) ;
      trigger_error($notice, E_USER_NOTICE);
    }
  }
} 
