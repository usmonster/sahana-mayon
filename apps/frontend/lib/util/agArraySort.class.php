<?php
/**
 * provides a sorting function for arrays that will be passed to agArrayPager.
 *
 * PHP Version 5
 *
 * LICENSE: This source file is subject to LGPLv3.0 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
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