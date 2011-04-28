<?php

/**
 * A class for reading Microsoft Excel (97/2003) Spreadsheets.
 *
 * Version 2.21
 *
 * Enhanced and maintained by Matt Kruse < http://mattkruse.com >
 * Maintained at http://code.google.com/p/php-excel-reader/
 *
 * Format parsing and MUCH more contributed by:
 *    Matt Roxburgh < http://www.roxburgh.me.uk >
 *
 * DOCUMENTATION
 * =============
 *   http://code.google.com/p/php-excel-reader/wiki/Documentation
 *
 * CHANGE LOG
 * ==========
 *   http://code.google.com/p/php-excel-reader/wiki/ChangeHistory
 *
 * DISCUSSION/SUPPORT
 * ==================
 *   http://groups.google.com/group/php-excel-reader-discuss/topics
 *
 * --------------------------------------------------------------------------
 *
 * Originally developed by Vadim Tkachenko under the name PHPExcelReader.
 * (http://sourceforge.net/projects/phpexcelreader)
 * Based on the Java version by Andy Khan (http://www.andykhan.com).  Now
 * maintained by David Sanders.  Reads only Biff 7 and Biff 8 formats.
 *
 * PHP versions 4 and 5
 *
 * LICENSE: This source file is subject to version 3.0 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_0.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category   Spreadsheet
 * @package	Spreadsheet_Excel_Reader
 * @author	 Vadim Tkachenko <vt@apachephp.com>
 * @license	http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version	CVS: $Id: reader.php 19 2007-03-13 12:42:41Z shangxiao $
 * @link	   http://pear.php.net/package/Spreadsheet_Excel_Reader
 * @see		OLE, Spreadsheet_Excel_Writer
 * --------------------------------------------------------------------------
 */

abstract class phpExcelReader
{
  public function __construct()
  {
    // @todo it would be preferred to set these up as class constants anc change calls to them
    define('NUM_BIG_BLOCK_DEPOT_BLOCKS_POS', 0x2c);
    define('SMALL_BLOCK_DEPOT_BLOCK_POS', 0x3c);
    define('ROOT_START_BLOCK_POS', 0x30);
    define('BIG_BLOCK_SIZE', 0x200);
    define('SMALL_BLOCK_SIZE', 0x40);
    define('EXTENSION_BLOCK_POS', 0x44);
    define('NUM_EXTENSION_BLOCK_POS', 0x48);
    define('PROPERTY_STORAGE_BLOCK_SIZE', 0x80);
    define('BIG_BLOCK_DEPOT_BLOCKS_POS', 0x4c);
    define('SMALL_BLOCK_THRESHOLD', 0x1000);

    // property storage offsets
    define('SIZE_OF_NAME_POS', 0x40);
    define('TYPE_POS', 0x42);
    define('START_BLOCK_POS', 0x74);
    define('SIZE_POS', 0x78);
    define('IDENTIFIER_OLE', pack("CCCCCCCC", 0xd0, 0xcf, 0x11, 0xe0, 0xa1, 0xb1, 0x1a, 0xe1));

    define('SPREADSHEET_EXCEL_READER_BIFF8', 0x600);
    define('SPREADSHEET_EXCEL_READER_BIFF7', 0x500);
    define('SPREADSHEET_EXCEL_READER_WORKBOOKGLOBALS', 0x5);
    define('SPREADSHEET_EXCEL_READER_WORKSHEET', 0x10);
    define('SPREADSHEET_EXCEL_READER_TYPE_BOF', 0x809);
    define('SPREADSHEET_EXCEL_READER_TYPE_EOF', 0x0a);
    define('SPREADSHEET_EXCEL_READER_TYPE_BOUNDSHEET', 0x85);
    define('SPREADSHEET_EXCEL_READER_TYPE_DIMENSION', 0x200);
    define('SPREADSHEET_EXCEL_READER_TYPE_ROW', 0x208);
    define('SPREADSHEET_EXCEL_READER_TYPE_DBCELL', 0xd7);
    define('SPREADSHEET_EXCEL_READER_TYPE_FILEPASS', 0x2f);
    define('SPREADSHEET_EXCEL_READER_TYPE_NOTE', 0x1c);
    define('SPREADSHEET_EXCEL_READER_TYPE_TXO', 0x1b6);
    define('SPREADSHEET_EXCEL_READER_TYPE_RK', 0x7e);
    define('SPREADSHEET_EXCEL_READER_TYPE_RK2', 0x27e);
    define('SPREADSHEET_EXCEL_READER_TYPE_MULRK', 0xbd);
    define('SPREADSHEET_EXCEL_READER_TYPE_MULBLANK', 0xbe);
    define('SPREADSHEET_EXCEL_READER_TYPE_INDEX', 0x20b);
    define('SPREADSHEET_EXCEL_READER_TYPE_SST', 0xfc);
    define('SPREADSHEET_EXCEL_READER_TYPE_EXTSST', 0xff);
    define('SPREADSHEET_EXCEL_READER_TYPE_CONTINUE', 0x3c);
    define('SPREADSHEET_EXCEL_READER_TYPE_LABEL', 0x204);
    define('SPREADSHEET_EXCEL_READER_TYPE_LABELSST', 0xfd);
    define('SPREADSHEET_EXCEL_READER_TYPE_NUMBER', 0x203);
    define('SPREADSHEET_EXCEL_READER_TYPE_NAME', 0x18);
    define('SPREADSHEET_EXCEL_READER_TYPE_ARRAY', 0x221);
    define('SPREADSHEET_EXCEL_READER_TYPE_STRING', 0x207);
    define('SPREADSHEET_EXCEL_READER_TYPE_FORMULA', 0x406);
    define('SPREADSHEET_EXCEL_READER_TYPE_FORMULA2', 0x6);
    define('SPREADSHEET_EXCEL_READER_TYPE_FORMAT', 0x41e);
    define('SPREADSHEET_EXCEL_READER_TYPE_XF', 0xe0);
    define('SPREADSHEET_EXCEL_READER_TYPE_BOOLERR', 0x205);
    define('SPREADSHEET_EXCEL_READER_TYPE_FONT', 0x0031);
    define('SPREADSHEET_EXCEL_READER_TYPE_PALETTE', 0x0092);
    define('SPREADSHEET_EXCEL_READER_TYPE_UNKNOWN', 0xffff);
    define('SPREADSHEET_EXCEL_READER_TYPE_NINETEENFOUR', 0x22);
    define('SPREADSHEET_EXCEL_READER_TYPE_MERGEDCELLS', 0xE5);
    define('SPREADSHEET_EXCEL_READER_UTCOFFSETDAYS', 25569);
    define('SPREADSHEET_EXCEL_READER_UTCOFFSETDAYS1904', 24107);
    define('SPREADSHEET_EXCEL_READER_MSINADAY', 86400);
    define('SPREADSHEET_EXCEL_READER_TYPE_HYPER', 0x01b8);
    define('SPREADSHEET_EXCEL_READER_TYPE_COLINFO', 0x7d);
    define('SPREADSHEET_EXCEL_READER_TYPE_DEFCOLWIDTH', 0x55);
    define('SPREADSHEET_EXCEL_READER_TYPE_STANDARDWIDTH', 0x99);
    define('SPREADSHEET_EXCEL_READER_DEF_NUM_FORMAT', "%s");
}

  public function GetInt4d($data, $pos)
  {
    $value = ord($data[$pos]) | (ord($data[$pos + 1]) << 8) | (ord($data[$pos + 2]) << 16) | (ord($data[$pos + 3]) << 24);
    if ($value >= 4294967294) {
      $value = -2;
    }
    return $value;
  }

  // http://uk.php.net/manual/en/function.getdate.php
  public function gmgetdate($ts = null)
  {
    $k = array('seconds', 'minutes', 'hours', 'mday', 'wday', 'mon', 'year', 'yday', 'weekday', 'month', 0);
    return(array_comb($k, split(":", gmdate('s:i:G:j:w:n:Y:z:l:F:U', is_null($ts) ? time() : $ts))));
  }

  // Added for PHP4 compatibility
  public function array_comb($array1, $array2)
  {
    $out = array();
    foreach ($array1 as $key => $value) {
      $out[$value] = $array2[$key];
    }
    return $out;
  }

  public function v($data, $pos)
  {
    return ord($data[$pos]) | ord($data[$pos + 1]) << 8;
  }
}
