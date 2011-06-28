<?php
/**
 * Provides methods related to record export
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
abstract class agExportHelper extends agPdoHelper {
  const     CONN_EXPORT_READ = 'export_id_read';

  const     EXPORT_QUERY = 'export_query';

  protected $exportBaseName,
            $helperObjects = array(),
            $exportSpec = array(),
            $exportComponents = array(),
            $exportData = array(),
            $exportDataRowTemplate = array(),
            $exportRawData = array(),
            $exportFileInfo = array('filename' => NULL, 'path' => NULL),
            $zipContents = array(),
            $zipFile,
            $tempPath;

  protected $XlsMaxExportSize,
            $totalFetches = 0,
            $fetchPosition = 0;

  /**
   * Method to get the base doctrine query object used in export
   * @return agDoctrineQuery A doctrine query object
   */
  abstract protected function getDoctrineQuery();

  /**
   * Method to set the export data specification
   */
  abstract protected function setExportSpec();

  /**
   * Method to set export components
   */
  abstract protected function setExportComponents();

  /**
   * A method to act like this classes' own constructor
   * @param integer $eventId An event ID
   * @param string The export basename (as it would appear in an export)
   */
  protected function __init($exportBaseName)
  {
    // set some useful global parameters
    $this->XlsMaxExportSize = agGlobal::getParam('xls_max_export_size');

    // set our export basename
    $this->exportBaseName = $exportBaseName;

    // get our paths
    $this->tempPath = realpath(sys_get_temp_dir());
    $this->exportFileInfo['path'] = sfConfig::get('sf_download_dir');
  }

  /**
   * Method to set connection objects. (Includes both parent connections and normalization
   * connections.
   */
  protected function setConnections()
  {
    $dm = Doctrine_Manager::getInstance();

    $dm->setCurrentConnection('doctrine');
    $adapter = $dm->getCurrentConnection()->getDbh();
    $conn = Doctrine_Manager::connection($adapter, self::CONN_EXPORT_READ);
    $this->_conn[self::CONN_EXPORT_READ] = $conn;

    $dm->setCurrentConnection('doctrine');
  }

  /**
   * Method to lazily load helper objects
   * @param string $helperClassName Name of the helper class to load
   */
  protected function getHelperObject($helperClassName)
  {
    if (!array_key_exists($helperClassName, $this->helperObjects)) {
      $this->helperObjects[$helperClassName] = new $helperClassName();
    }

    return $this->helperObjects[$helperClassName];
  }

  /**
   * Method to execute an export query and return the filename and path info
   * @return array An array with filename and path info
   */
  public function getExport()
  {
    // just reset everything
    $this->resetExport();

    // execute our query
    $this->executeQuery();

    // start a zip file
    $this->createExportZip();

    // loop the processing of batches
    while ($this->fetchPosition != $this->totalFetches) {
      $this->processBatch();
    }

    // perform a little general cleanup
    $this->clearConnections();
    $this->closeConnections();

    // add the contents files to the zip file
    $this->addContentsToZip();

    // close out the zip file
    $this->closeExportZip();

    return $this->exportFileInfo;
  }

  /**
   * Method to effectively zero-state an export
   */
  protected function resetExport()
  {
    // initialize other variables
    $this->setExportSpec();
    $this->setExportComponents();

    // reset our data row template
    $this->exportDataRowTemplate = array();
    foreach (array_keys($this->exportSpec) as $column) {
      $this->exportDataRowTemplate['column'] = NULL;
    }

    // reset our counters
    $this->fetchPosition = 0;
    $this->totalFetches = 0;

    // clean our our export data
    $this->resetExportData();
  }

  /**
   * Method to execute the data retrieval (ID) query
   */
  protected function executeQuery()
  {
    // get our query basics and transform to SQL
    $query = $this->getDoctrineQuery();
    $sql = $query->getSqlQuery();
    $params = $query->getParams();
    $conn = $this->getConnection(self::CONN_EXPORT_READ);
    
    // create a count-query to give me the results
    $ctQuery = 'SELECT COUNT(*) FROM (' . $sql . ') AS t;';
    $ctResults = $this->executePdoQuery($conn, $ctQuery, $params['where']);
    $this->totalFetches = $ctResults->fetchColumn();

    // start the real export query
    $this->executePdoQuery($conn, $sql, $params['where'],
      Doctrine_Core::FETCH_ASSOC, self::EXPORT_QUERY);
  }

  /**
   * Method to instantiate a zipfile
   */
  protected function createExportZip()
  {
    // first remove any unnecessary (old) files
    $paths = array ($this->tempPath, $this->exportFileInfo['path']);
    foreach ($paths as $path)
    {
      foreach(glob(($path . DIRECTORY_SEPARATOR . $this->exportBaseName . '*')) as $filename) {
        unlink($filename);
      }
      unset($path); // this could be confusing / dangerous if left in the reach of children
    }
    unset($paths); // same goes for here

    // set some smart variables
    $date = date('Ymd_His');
    $this->exportFileInfo['filename'] = $this->exportBaseName . '_' . $date . '.zip';

    $zipPath = $this->tempPath . DIRECTORY_SEPARATOR . $this->exportFileInfo['filename'];

    // Create zip file
    $this->zipFile = new ZipArchive();

    if ($this->zipFile->open($zipPath, ZIPARCHIVE::CREATE) !== TRUE) {
      $msg = 'Export: Could not create the output zip file' .  $zipPath .
        '. Check your permissions to make sure you have write access.';
      throw new sfException($msg);
    }
  }

  protected function processBatch()
  {
    // start by resetting our export data
    $this->resetExportData();

    // fetch ID records from the database into exportData
    $this->getNextBatch();

    // run any component methods that are required to complete export data
    $this->getExportData();

    // make an xls file from the export data and add it to the zipContents array
    $this->createXlsFromExportData();
  }

  /**
   * Method to reset export data
   */
  protected function resetExportData()
  {
    $this->exportData = array();
    $this->exportRawData = array();
  }

  /**
   * Method to fill up the exportData array with the next batch of data
   */
  protected function getNextBatch()
  {
    // grab our PDO object
    $pdo = $this->_PDO[self::EXPORT_QUERY];


    // iterate the first fetch of a batch
    while($row = $pdo->fetch()) {
      // increment the fetch position
      $this->fetchPosition++;

      // add the data to our exportData array
      $this->exportRawData[$this->fetchPosition] = $row;
      $this->exportData[$this->fetchPosition] = $this->exportDataRowTemplate;

      while((($this->fetchPosition % $this->XlsMaxExportSize) != 0) && ($row = $pdo->fetch())) {
        // increment the fetch position
        $this->fetchPosition++;

        // add the data to our exportData array
        $this->exportRawData[$this->fetchPosition] = $row;
        $this->exportData[$this->fetchPosition] = $this->exportDataRowTemplate;
      }
    }
  }

  /**
   * Method to return batch export data and fill the $exportData array
   */
  protected function getExportData()
  {
    
  }

  /**
   * Method to add the collected zipfile contents to the zip file
   */
  protected function addContentsToZip()
  {
    foreach ($this->zipContents as $fileInfo) {
      $fullPath = $fileInfo['path'] . DIRECTORY_SEPARATOR . $fileInfo['filename'];
      $this->zipFile->addFile($fullPath, $fileInfo['filename']);
    }
  }

  /**
   * Method to close an export zipfile and remove temporary files
   */
  protected function closeExportZip()
  {
    // get our two respective move paths
    $downloadPath = $this->exportFileInfo['path'] . DIRECTORY_SEPARATOR .
      $this->exportFileInfo['filename'];
    $tempPath = $this->tempPath . DIRECTORY_SEPARATOR .
      $this->exportFileInfo['filename'];

    // close the zip
    $this->zipFile->close();

    // remove the individual xls files
    foreach ($this->zipContents as $fileInfo) {
      $fullPath = $fileInfo['path'] . DIRECTORY_SEPARATOR . $fileInfo['filename'];
      unlink($fullPath);
    }

    // finally, move the zip file to its final web-accessible location
    if (! rename($tempPath, $downloadPath)) {
      $msg = 'Export: Unable to move ' . $this->exportFileInfo['filename'] . ' to the specified ' .
        'upload directory. Check your sf_upload_dir configuration to ensure you have write ' .
        'permissions.';
      throw new sfException($msg);
    }
  }

  /**
   * Method to construct an XLS file and add it to the zipContents array
   */
  protected function createXlsFromExportData()
  {


    // get our temporary table read connection
    $conn = $this->getConnection(self::CONN_TEMP_READ);
    $columnHeaders = array_keys($this->importSpec);
    $selectCols = 't.' . implode(', t.', $columnHeaders);

    // build our query statement
    $this->eh->logDebug('Export: Establishing fetch from the database.');
    $q = 'SELECT ' . $selectCols . ' FROM ' . $this->tempTable . ' AS t WHERE t.' .
      $this->successColumn . ' != ? OR ' . $this->successColumn . ' IS NULL;';
    $pdo = $this->executePdoQuery($conn, $q, array(TRUE));
    $this->eh->logDebug('Export: PDO object successfully created.');

    // set counters
    $i = 1;
    $fetchPosition = 0;

    // begin fetching from the database, starting with our first record
    while ($row = $pdo->fetch()) {
      // start by incrementing our fetch position
      $fetchPosition++;

      // reset our exportData array and add our first row to it
      $exportData = array();
      $exportData[] = $row;

      // continue fetching until we either run out of records or hit our batch limit
      while (($fetchPosition % $this->XlsMaxExportSize != 0) && ($row = $pdo->fetch())) {
        // always increment fetch position immediately
        $fetchPosition++;

        // add the row to our $rows array
        $exportData[] = $row;
      }

      // set our xlsname and pass it and our export data to the buildXls method
      $xlsName = $this->unprocessedBaseName . '_' . $date . '_' . $i . '.xls';
      $this->eh->logInfo('Export: Fetched ' . count($rows) . ' records from the database into ' .
        'batch ' . $i . '. Now adding records to ' . $xlsName . '.');
      $xlsPath = realpath(sys_get_temp_dir()) . DIRECTORY_SEPARATOR . $xlsName;

      // check for successful creation of the xlsfile (both soft and hard)
      try {
        if (! $this->buildXls($xlsPath, $exportData)) {
          break;
        }
      } catch (Exception $e) {
        $this->eh->logErr($e->getMessage());
        break;
      }

      // if all went well, add it to the exportFiles array
      $exportFiles[$i] = array($xlsPath, $xlsName);

      // iterate our batch counter
      $i++;
    }

    // we do this to check if any records were processed at all
    if ($fetchPosition == 0 || count($exportFiles) == 0) {

      // if none were, log a warning
      $this->eh->logWarning('Export No unprocessed records could be retrieved. Could not create' .
        'unprocessed records export.');

      // close out and remove our zipfile
      $zipFile->close();
      unlink($zipPath);

      // then return false
      return FALSE;
    }

    // otherwise, add our xls files to the zip
    foreach ($exportFiles as $xlsFileInfo) {
      $this->eh->logDebug('Export: Adding ' . $xlsFileInfo[1] . ' to zip file.');
      $zipFile->addFile($xlsFileInfo[0], $xlsFileInfo[1]);
    }
    $this->eh->logInfo('Export: Successfully added ' . count($exportFiles) .
      ' xls files to zip file.');

    // close the zip
    $zipFile->close();

    // remove the individual xls files
    foreach ($exportFiles as $xlsFileInfo) {
      $this->eh->logDebug('Export: Removing ' . $xlsFileInfo[1] . ' from the temp directory.');
      unlink($xlsFileInfo[0]);
    }
    $this->eh->logInfo('Export: Successfully removed xls files from the temp directory.');

    // finally, move the zip file to its final web-accessible location
    $this->eh->logDebug('Export: Moving ' . $downloadFile . ' to user-accesible directory.');
    $downloadPath = sfConfig::get('sf_download_dir') . DIRECTORY_SEPARATOR . $downloadFile;
    if (! rename($zipPath, $downloadPath)) {
      $this->eh->logErr('Export: Unable to move ' . $downloadFile . ' to the specified upload ' .
        'directory. Check your sf_upload_dir configuration to ensure you have write permissions.');
    }

    $eventMsg = "Export: Successfully created export file of unprocessed records.";
    $this->eh->logNotice($eventMsg);


    return $downloadFile;

  }

  /**
   * Method to create an xls import file and return TRUE if successful
   * @param string $xlsPath Path of the export file to create
   * @param array $exportData An array of export data to write to the xls file
   * @return boolean Returns TRUE if successful
   */
  protected function buildXls($xlsPath, array $exportData)
  {
    // load the Excel writer object
    $sheet = new agExcel2003ExportHelper($this->exportBaseName);

    // Write the column headers
    foreach (array_keys($exportData[0]) as $columnHeader) {
      $sheet->label($columnHeader);
      $sheet->right();
    }

    foreach ($exportData as $exportRowData) {
      $sheet->down();
      $sheet->home();

      // Write values
      foreach ($exportRowData as $value) {
        $sheet->label($value);
        $sheet->right();
      }
    }

    $sheet->save($xlsPath);
    return TRUE;
  }
}