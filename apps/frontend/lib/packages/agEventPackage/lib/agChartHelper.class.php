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

  CONST CHART_CACHE_DIR = 'sf_xspchart_cache_dir';
  CONST CHART_DATA_DIR = 'sf_xspchart_data_dir';

  CONST INVALID_UNIQUE_IDENT = '[^-\w]';
  CONST YAML_INLINE = 1;

  protected static $xsPCache;

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
   * Method to return (or instantiate) the xsPCache object
   * @return xsPCache An xsPCache object
   */
  protected static function getXsPCache()
  {
    if (!isset(self::$xsPCache)) {
      self::$xsPCache = new xsPCache(sfConfig::get(self::CHART_CACHE_DIR) . DIRECTORY_SEPARATOR);
    }

    return self::$xsPCache;
  }

  /**
   * Simple method to return the chart data description
   * @param string $chartId One of the CHART_ constants
   * @return array A chart data description
   */
  protected static function getDataDesc($chartId)
  {
    return self::$dataDescs[$chartId];
  }

  /**
   * Returns the data file responsible for creating chart data
   * @param mixed $uniqueIdent A string (unique) identifier for a chart
   * @param string $chartId One of the CHART_ constants
   * @return string A url to the chart data file
   */
  protected static function getChartDataFile($uniqueIdent, $chartId)
  {
    if (preg_match(self::INVALID_UNIQUE_IDENT, $uniqueIdent)) {
      return FALSE;
    }

    return sfConfig::get(self::CHART_DATA_DIR) . DIRECTORY_SEPARATOR .
      $uniqueIdent . '_' . $chartId . '.yml';
  }

  /**
   * Returns the raw chart data from its parent file
   * @param string $chartDataFile The uri of a chart data file
   * @return array An array of chart data
   */
  protected static function getChartDataFromFile($chartDataFile)
  {
    return sfYaml::load($chartDataFile);
  }

  /**
   * Returns properly reduced chart data as an array
   * @param mixed $uniqueIdent A string (unique) identifier for a chart
   * @param string $chartId One of the CHART_ constants
   * @param mixed $subChartId A unique sub-chart identifier
   * @return array An array of chart data
   */
  protected static function getChartData($uniqueIdent, $chartId, $subChartId = NULL)
  {
    $data = self::getChartDataFromFile(self::getChartDataFile($uniqueIdent, $chartId));

    if (!is_null($subChartId) && isset($data[$subChartId])) {
      return $data[$subChartId];
    }

    return $data;
  }
  
  /**
   * A method to set chart data. It automatically checks whether or not the data file
   * exists and, if-so, whether the current data file matches the passed data array (avoiding
   * unnecessary writes). If no data file exists it attempts to create one.
   * @param mixed $uniqueIdent A string (unique) identifier for a chart
   * @param string $chartId One of the CHART_ constants
   * @param array $dataArray An array of chart data
   * @return boolean Returns false if the chart is not known to the class
   */
  public static function setChartData($uniqueIdent, $chartId, array $dataArray)
  {
    if (!isset(self::$dataDescs[$chartId])) {
      return FALSE;
    }

    $dataFile = self::getChartDataFile($uniqueIdent, $chartId);
    if (file_exists($dataFile)) {
      if ($dataArray == self::getChartDataFromFile($dataFile)) {
        return TRUE;
      }
    } else {
      touch($dataFile);
    }

    file_put_contents($dataFile, sfYaml::dump($dataArray, self::YAML_INLINE));
    return TRUE;
  }

  /**
   * Method to return streamed PNG chart data
   * @param mixed $uniqueIdent A string (unique) identifier for a chart
   * @param string $chartId One of the CHART_ constants
   * @param mixed $subChartId A unique sub-chart identifier
   * @return binary A PNG file (not a url, the actual PNG content)
   */
  public static function getChart($uniqueIdent, $chartId, $subChartId = NULL)
  {
    if (preg_match(self::INVALID_UNIQUE_IDENT, $uniqueIdent) || 
        !isset(self::$dataDescs[$chartId])) {
      return FALSE;
    }

    $data = self::getChartData($uniqueIdent, $chartId, $subChartId);

    $cacheId = $uniqueIdent . '-' . $chartId;
    if (!is_null($subChartId)) {
      $cacheId .= '-' . $subChartId;
    }

    $cache = self::getXsPCache();
    $cache->GetFromCache($cacheId, $data);
//    if ($cache->IsInCache($cacheId, $data)) {
//
//    }

    $desc = self::$dataDescs[$chartId];

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

    $cache->WriteToCache($cacheId, $data, $chart);
    $cache->GetFromCache($cacheId, $data);
  }

  /**
   * Method to construct a chart object
   * @param array $data An array of chart data
   * @param array $desc An array of chart description data
   * @return xsPChart An xsPChart object
   */
  protected static function getStaffStatusPie(array $data, array $desc)
  {
    $chart = new xsPChart(390,195);
    $chart->setColorPalette(0, 255, 145, 22);
    $chart->setColorPalette(1, 255, 67, 22);
    $chart->setColorPalette(2, 33, 188, 255);
    $chart->setColorPalette(3, 11, 119, 166);
    $chart->xsSetFontProperties('DejaVuSans.ttf', 8);
    $chart->drawPieGraph($data, $desc, 150,90,110, PIE_PERCENTAGE,TRUE,60,20,5);
    $chart->drawPieLegend(283,20,$data,$desc,250,250,250);
    $chart->xsSetFontProperties('DejaVuSans-Bold.ttf', 10);

    return $chart;
  }

  /**
   * Method to construct a chart object
   * @param array $data An array of chart data
   * @param array $desc An array of chart description data
   * @return xsPChart An xsPChart object
   */
  protected static function getStaffRequiredBar(array $data, array $desc)
  {
    $chart = new xsPChart(320, 195);
    $chart->setGraphArea(60, 10, 220, 175);
    $chart->xsSetFontProperties('DejaVuSans.ttf', 9);
    $chart->setColorPalette(0, 33, 188, 255);
    $chart->setColorPalette(1, 255, 145, 22);
    $chart->setColorPalette(2, 11, 119, 166);
    $chart->drawScale($data, $desc, SCALE_NORMAL, 150,150,150, TRUE, 0, 0, TRUE);
    $chart->drawGrid(4, TRUE, 230, 230, 230, 50);
    $chart->drawTreshold(0, 143, 55, 72, TRUE, TRUE);
    $chart->drawOverlayBarGraph($data, $desc, 100);
    $chart->drawLegend(210, 20, $desc, 255, 255, 255);
    $chart->xsSetFontProperties('DejaVuSans-Bold.ttf', 10);

    return $chart;
  }

  /**
   * Method to construct a chart object
   * @param array $data An array of chart data
   * @param array $desc An array of chart description data
   * @return xsPChart An xsPChart object
   */
  protected static function getStafftypeRequiredBar(array $data, array $desc)
  {
    $chart = new xsPChart(720, 320);
    $chart->setGraphArea(50, 30, 680, 200);
    $chart->xsSetFontProperties('DejaVuSans.ttf', 9);
    $chart->setColorPalette(0, 22,255,117);
    $chart->setColorPalette(1, 255, 145, 22);
    $chart->setColorPalette(2, 11, 119, 166);
    $chart->drawScale($data, $desc, SCALE_NORMAL, 150,150,150, TRUE, 60, 0, TRUE);
    $chart->drawGrid(4, TRUE, 230, 230, 230, 50);
    $chart->drawTreshold(0, 143, 55, 72, TRUE, TRUE);
    $chart->drawOverlayBarGraph($data, $desc, 100);
    $chart->drawLegend(570, 250, $desc, 255, 255, 255);

    return $chart;
  }

  /**
   * Method to construct a chart object
   * @param array $data An array of chart data
   * @param array $desc An array of chart description data
   * @return xsPChart An xsPChart object
   */
  protected static function getStafftypeStatusPie(array $data, array $desc)
  {
    $chart = new xsPChart(310, 230);
    $chart->setColorPalette(0, 33, 188, 255);
    $chart->setColorPalette(1, 11, 119, 166);
    $chart->setColorPalette(2, 255, 145, 22);
    $chart->xsSetFontProperties('DejaVuSans.ttf', 8);
    $chart->drawFlatPieGraph($data, $desc, 120,112,87, PIE_PERCENTAGE, 7);
    $chart->drawPieLegend(220,172,$data, $desc,255,255,255);

    return $chart;
  }

  /**
   * Method to get a chart URL
   * @param string $baseUrl The base URL to which this should be appended
   * @param mixed $uniqueIdent A unique chart identifier
   * @param mixed $chartId A chartId
   * @param mixed $subChartId A subchartId
   * @return string A unique chart url
   */
  public static function getChartUrl($baseUrl, $uniqueIdent, $chartId, $subChartId = NULL)
  {
    $firstOperand = (strpos($baseUrl, '?') === FALSE) ? '&' : '?';
    $subChart = (is_null($subChartId)) ? '' : '&subChartId=' . $subChartId;
    $configuration = sfContext::getInstance()->getConfiguration();
    $configuration->loadHelpers(array('Url', 'Tag'));
    return url_for($baseUrl) . $firstOperand . 'uniqueIdent=' . $uniqueIdent . '&chartId=' .
      $chartId . $subChart;
  }
}
