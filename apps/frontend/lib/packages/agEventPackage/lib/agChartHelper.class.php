<?php
/**
 * provides event chart helper methods
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author Chad Heuschober, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
class agChartHelper
{
  CONST CHART_STAFF_STATUS_PIE = 'staffStatusPie';
  CONST CHART_STAFF_REQUIRED_BAR = 'staffRequiredBar';
  CONST CHART_STAFFTYPE_REQUIRED_BAR = 'stafftypeRequiredBar';
  CONST CHART_STAFFTYPE_STATUS_PIE = 'stafftypeStatusPie';

  protected static $dataDescs = array(
      self::CHART_STAFF_STATUS_PIE => array(
          'Format' => array('X' => 'number', 'Y' => 'number'),
          'Unit' => array('X' => NULL, 'Y' => NULL),
          'Values' => array(0 => 'Values', 1 => 'Status'),
          'Position' => 'Status'
        ),
      self::CHART_STAFF_REQUIRED_BAR => array(
          'Position' => 'Name',
          'Values' => array('max', 'min', 'staff',),
          'Description' => array(
              'max' => 'Max Required', 'min' => 'Min Required', 'staff' => 'Staff',
            ),
          'Format' => array('X' => 'number', 'Y' => 'number'),
          'Unit' => array('X' => NULL, 'Y' => NULL),
          'Axis' => array(),
        ),
      self::CHART_STAFFTYPE_REQUIRED_BAR => array(
          'Position' => 'Name',
          'Values' => array('max', 'min', 'committed', ),
          'Description' => array(
            'max' => 'Max Required', 'min' => 'Min Required', 'committed' => 'Commmitted',
            ),
          'Format' => array('X' => 'number', 'Y' => 'number'),
          'Unit' => array('X' => NULL, 'Y' => NULL),
          'Axis' => array('Y' => 'Staff'),
        ),
      self::CHART_STAFFTYPE_STATUS_PIE => array(
          'Format' => array('X' => 'number', 'Y' => 'number'),
          'Unit' => array('X' => NULL, 'Y' => NULL),
          'Values' => array(0 => 'Values', 1 => 'Status'),
          'Position' => 'Status'
        ),
    );

  /**
   * Simple method to return the chart data description
   * @param string $chartId One of the CHART_ constants
   * @return array A chart data description
   */
  public static function getDataDesc($chartId)
  {
    return self::$dataDescs[$chartId];
  }

  public static function getChartDataFile($chartId)
  {
    return sfConfig::get('sf_xspchart_data_dir') . DIRECTORY_SEPARATOR . $chartId . '.yml';
  }

  public static function getChartData($chartId)
  {
    return sfYaml::load(self::getChartDataFile($chartId));
  }
  
  public static function setChartData($chartId, $dataArray)
  {
    $dataFile = self::getChartDataFile($chartId);
    if (!file_exists($dataFile)) {
      touch($dataFile);
    }

    file_put_contents($dataFile, sfYaml::dump($dataArray, 1));
  }

  public static function getChart($chartId, $subChartId = NULL)
  {
    if (isset(self::$dataDescs[$chartId])) {
      $data = self::getChartData($chartId);
      $desc = self::$dataDescs[$chartId];
    } else {
      return FALSE;
    }
    
    if (!is_null($subChartId) && isset($data[$subChartId])) {
      $data = $data[$subChartId];
    }

    switch ($chartId) {
      case self::CHART_STAFF_STATUS_PIE:
        $chart = self::getStaffStatusPie($data, $desc);
        break;
      case self::CHART_STAFF_REQUIRED_BAR:
        $chart = self::getStaffRequiredBar($data, $desc);
        break;
      case self::CHART_STAFFTYPE_REQUIRED_BAR:
        $chart = self::getStafftypeRequiredBar($data, $desc);
        break;
      case self::CHART_STAFFTYPE_STATUS_PIE:
        $chart = self::getStafftypeStatusPie($data, $desc);
        break;
    }


  }
}
