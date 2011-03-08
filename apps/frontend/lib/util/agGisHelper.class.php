<?php

/**
 *
 * Provides bulk-gis manipulation methods
 *
 * PHP Version 5
 *
 * LICENSE: This source file is subject to LGPLv3.0 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 *
 * @author Shirley Chan, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 *
 */
class agGisHelper
{

  public static function getGeoMatchScores()
  {
    $geoMatchSources = agDoctrineQuery::create()
                    ->select('id, geo_match_score')
                    ->from('agGeoMatchScore')
                    ->execute(array(), 'key_value_pair');
    return $geoMatchSources;
  }

  public static function getGeoSources()
  {
    $geoSources = agDoctrineQuery::create()
                    ->select('id, geo_source')
                    ->from('agGeoSource')
                    ->execute(array(), 'key_value_pair');
    return $geoSources;
  }

}
