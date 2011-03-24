<?php
/**
 *
 * Class to provide helper methods for class manipulation
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 *
 * @author Chad Heuschober, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */

class agClassHelper
{

  /**
   * Method to return only the methods explicitly defined within the given class
   *
   * @param class|object $class An object or class.
   * @return array An array of class names declared within the class.
   */
  public static function getExplicitClassMethods($class)
  {
    // set up our explict methods
    $results = array() ;

    // get all the methods for $class
    $classMethods = get_class_methods($class) ;

    // get the classes' parent
    $parentClass = get_parent_class($class) ;

    // if the class has a parent pick up its methods
    if ($parentClass)
    {
      $parentMethods = get_class_methods($parentClass) ;

      // get our explicit child methods by diffing the two sets of methods
      $results = array_diff($classMethods, $parentMethods) ;
    }
    else
    {
      $results = $classMethods ;
    }

    return $results ;
  }
}

