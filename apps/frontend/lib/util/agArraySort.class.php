<?php
/**
 * provides a sorting function for arrays that will be passed to agArrayPager.
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author     Charles Wisniewski, CUNY SPS
 * @author     Nils Stolpe, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
class agArraySort {

  public static $sort;

  public static function arraySort($a, $b) {
    if ($a[self::$sort] == $b[self::$sort]) {
      return 0;
    }
    return ($a[self::$sort] < $b[self::$sort]) ? -1 : 1;
  }
}