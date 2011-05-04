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

class OLERead extends phpExcelReader
{

  var $data = '';

  function OLERead()
  {

  }

  function read($sFileName)
  {
    // check if file exist and is readable (Darko Miljanovic)
    if (!is_readable($sFileName)) {
      $this->error = 1;
      return false;
    }
    $this->data = @file_get_contents($sFileName);
    if (!$this->data) {
      $this->error = 1;
      return false;
    }
    if (substr($this->data, 0, 8) != IDENTIFIER_OLE) {
      $this->error = 1;
      return false;
    }
    $this->numBigBlockDepotBlocks = GetInt4d($this->data, NUM_BIG_BLOCK_DEPOT_BLOCKS_POS);
    $this->sbdStartBlock = GetInt4d($this->data, SMALL_BLOCK_DEPOT_BLOCK_POS);
    $this->rootStartBlock = GetInt4d($this->data, ROOT_START_BLOCK_POS);
    $this->extensionBlock = GetInt4d($this->data, EXTENSION_BLOCK_POS);
    $this->numExtensionBlocks = GetInt4d($this->data, NUM_EXTENSION_BLOCK_POS);

    $bigBlockDepotBlocks = array();
    $pos = BIG_BLOCK_DEPOT_BLOCKS_POS;
    $bbdBlocks = $this->numBigBlockDepotBlocks;
    if ($this->numExtensionBlocks != 0) {
      $bbdBlocks = (BIG_BLOCK_SIZE - BIG_BLOCK_DEPOT_BLOCKS_POS) / 4;
    }

    for ($i = 0; $i < $bbdBlocks; $i++) {
      $bigBlockDepotBlocks[$i] = GetInt4d($this->data, $pos);
      $pos += 4;
    }


    for ($j = 0; $j < $this->numExtensionBlocks; $j++) {
      $pos = ($this->extensionBlock + 1) * BIG_BLOCK_SIZE;
      $blocksToRead = min($this->numBigBlockDepotBlocks - $bbdBlocks, BIG_BLOCK_SIZE / 4 - 1);

      for ($i = $bbdBlocks; $i < $bbdBlocks + $blocksToRead; $i++) {
        $bigBlockDepotBlocks[$i] = GetInt4d($this->data, $pos);
        $pos += 4;
      }

      $bbdBlocks += $blocksToRead;
      if ($bbdBlocks < $this->numBigBlockDepotBlocks) {
        $this->extensionBlock = GetInt4d($this->data, $pos);
      }
    }

    // readBigBlockDepot
    $pos = 0;
    $index = 0;
    $this->bigBlockChain = array();

    for ($i = 0; $i < $this->numBigBlockDepotBlocks; $i++) {
      $pos = ($bigBlockDepotBlocks[$i] + 1) * BIG_BLOCK_SIZE;
      //echo "pos = $pos";
      for ($j = 0; $j < BIG_BLOCK_SIZE / 4; $j++) {
        $this->bigBlockChain[$index] = GetInt4d($this->data, $pos);
        $pos += 4;
        $index++;
      }
    }

    // readSmallBlockDepot();
    $pos = 0;
    $index = 0;
    $sbdBlock = $this->sbdStartBlock;
    $this->smallBlockChain = array();

    while ($sbdBlock != -2) {
      $pos = ($sbdBlock + 1) * BIG_BLOCK_SIZE;
      for ($j = 0; $j < BIG_BLOCK_SIZE / 4; $j++) {
        $this->smallBlockChain[$index] = GetInt4d($this->data, $pos);
        $pos += 4;
        $index++;
      }
      $sbdBlock = $this->bigBlockChain[$sbdBlock];
    }


    // readData(rootStartBlock)
    $block = $this->rootStartBlock;
    $pos = 0;
    $this->entry = $this->__readData($block);
    $this->__readPropertySets();
  }

  function __readData($bl)
  {
    $block = $bl;
    $pos = 0;
    $data = '';
    while ($block != -2) {
      $pos = ($block + 1) * BIG_BLOCK_SIZE;
      $data = $data . substr($this->data, $pos, BIG_BLOCK_SIZE);
      $block = $this->bigBlockChain[$block];
    }
    return $data;
  }

  function __readPropertySets()
  {
    $offset = 0;
    while ($offset < strlen($this->entry)) {
      $d = substr($this->entry, $offset, PROPERTY_STORAGE_BLOCK_SIZE);
      $nameSize = ord($d[SIZE_OF_NAME_POS]) | (ord($d[SIZE_OF_NAME_POS + 1]) << 8);
      $type = ord($d[TYPE_POS]);
      $startBlock = GetInt4d($d, START_BLOCK_POS);
      $size = GetInt4d($d, SIZE_POS);
      $name = '';
      for ($i = 0; $i < $nameSize; $i++) {
        $name .= $d[$i];
      }
      $name = str_replace("\x00", "", $name);
      $this->props[] = array(
        'name' => $name,
        'type' => $type,
        'startBlock' => $startBlock,
        'size' => $size);
      if ((strtolower($name) == "workbook") || ( strtolower($name) == "book")) {
        $this->wrkbook = count($this->props) - 1;
      }
      if ($name == "Root Entry") {
        $this->rootentry = count($this->props) - 1;
      }
      $offset += PROPERTY_STORAGE_BLOCK_SIZE;
    }
  }

  function getWorkBook()
  {
    if ($this->props[$this->wrkbook]['size'] < SMALL_BLOCK_THRESHOLD) {
      $rootdata = $this->__readData($this->props[$this->rootentry]['startBlock']);
      $streamData = '';
      $block = $this->props[$this->wrkbook]['startBlock'];
      $pos = 0;
      while ($block != -2) {
        $pos = $block * SMALL_BLOCK_SIZE;
        $streamData .= substr($rootdata, $pos, SMALL_BLOCK_SIZE);
        $block = $this->smallBlockChain[$block];
      }
      return $streamData;
    } else {
      $numBlocks = $this->props[$this->wrkbook]['size'] / BIG_BLOCK_SIZE;
      if ($this->props[$this->wrkbook]['size'] % BIG_BLOCK_SIZE != 0) {
        $numBlocks++;
      }

      if ($numBlocks == 0)
        return '';
      $streamData = '';
      $block = $this->props[$this->wrkbook]['startBlock'];
      $pos = 0;
      while ($block != -2) {
        $pos = ($block + 1) * BIG_BLOCK_SIZE;
        $streamData .= substr($this->data, $pos, BIG_BLOCK_SIZE);
        $block = $this->bigBlockChain[$block];
      }
      return $streamData;
    }
  }
}
