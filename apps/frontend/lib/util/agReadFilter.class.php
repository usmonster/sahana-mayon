<?php
class agReadFilter implements PHPExcel_Reader_IReadFilter
{
  public static $lRow;
  public static $hRow;
  public function readCell($column, $row, $worksheetName = '') {
    // Read title row and rows 20 - 30
//    if ($row == 1 || ($row >= self::$lRow && $row <= self::$hRow)) {
    if ($row >= self::$lRow && $row <= self::$hRow) {
      return true;
    }
    return false;
  }
}
