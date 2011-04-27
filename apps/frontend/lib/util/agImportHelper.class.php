<?php
/**
 * Abstract class to provide import helper methods
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author Chad Heuschober, CUNY SPS
 * @author Shirley Chan, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 *
 */
abstract class agImportHelper
{
  // used for event reporting
  const       EVENT_INFO = 'info';
  const       EVENT_WARN = 'warn';
  const       EVENT_ERR = 'err';

  const       CONN_TEMP_READ = 'import_temp_read';
  const       CONN_TEMP_WRITE = 'import_temp_write';

  protected   $errThreshold,
              $successColumn = '_import_success',
              $defaultFetchMode = Doctrine_Core::FETCH_ASSOC,
              $tempTable,
              $tempTableOptions = array('type' => 'MYISAM', 'charset' => 'utf8'),
              $iterData,
              $importSpec,
              $_conn,
              $_PDO = array();

  private     $events,
              $lastEvent;

  /**
   * This class's constructor.
   * @param string $tempTable The name of the temporary import table to use
   */
  protected function __construct($tempTable)
  {
    // Sets a new connection.
    $this->setConnections();
    
    // establishes the name of our temp table
    $this->tempTable = $tempTable;

    // set our error threshold
    $this->errThreshold = intval(agGlobal::getParam('import_error_threshold'));

    // reset our events and iterators
    $this->resetIterData();
    $this->resetEvents();
  }

  /**
   * Method to reset ALL iter data.
   */
  protected function resetIterData()
  {
    $this->iterData = array();
    $this->iterData['errorCount'] = 0;
    $this->resetTempIterData();
  }

  /**
   * Method to reset the temp iterator data
   */
  protected function resetTempIterData()
  {
    $this->iterData['tempPosition'] = 0;
    $this->iterData['tempCount'] = 0;
  }

  /**
   * Method to reset the events array.
   */
  protected function resetEvents()
  {
    $this->lastEvent = array();

    $this->events = array();
    $this->events[self::EVENT_INFO] = array();
    $this->events[self::EVENT_WARN] = array();
    $this->events[self::EVENT_ERR] = array();
  }

  /**
   * Method to get (and lazy load) a doctrine connection object
   * @return Doctrine_Connection A doctrine connection object
   */
  protected function getConnection($conn)
  {
    // Lazy load and return pdo connection.
    if (!isset($this->_conn)) { $this->setConnections(); }
    return $this->_conn[$conn];
  }

  /*
   * Method to set the import connection object property
   */
  protected function setConnections()
  {
    $this->_conn = array();
    $this->_conn[self::CONN_TEMP_READ] = Doctrine_Manager::connection(NULL, self::CONN_TEMP_READ);
    $this->_conn[self::CONN_TEMP_WRITE] = Doctrine_Manager::connection(NULL, self::CONN_TEMP_WRITE);
  }

  /**
   * Method to log a new import event.
   * @param constant $eventType The event type being set. Must be one of the defined EVENT_*
   * constants defined in agImportHelper.
   * @param <type> $eventMsg The event message being set.
   */
  protected function logEvent($eventType, $eventMsg)
  {
    // just to make sure we only keep to our defined event types
    if (! defined("self::$eventType"))
    {
      throw new Exception("Undefined event constant: $eventType.");
    }

    // add our event to the events array
    $timestamp = microtime(TRUE);
    $this->events[$eventType][] = array('ts' => $timestamp, 'msg' => $eventMsg);
    $this->lastEvent = array('type' => $eventType, 'ts' => $timestamp, 'msg' => $eventMsg);
  }

  /**
   * Method to get events property information
   * @return array An array of import / export events
   * <code>
   * array(
   *   $eventType => array(
   *     $index => array('ts' => $timestamp, 'msg' => $eventMsg),
   *     $index => array('ts' => $timestamp, 'msg' => $eventMsg)
   *     ...
   *   ),
   *   $eventType => array(
   *     $index => array('ts' => $timestamp, 'msg' => $eventMsg),
   *     $index => array('ts' => $timestamp, 'msg' => $eventMsg),
   *     ...
   *   ),
   *   ...
   * )
   * </code>
   */
  public function getEvents()
  {
    return $this->events;
  }

  /**
   * Method to return the last event.
   * @return array An array of datapoints concerning the last inserted event.
   * <code> array('type' => $eventType, 'ts' => $timestamp, 'msg' => $eventMsg)</code>
   */
  public function getLastEvent()
  {
    return $this->lastEvent;
  }

  /**
   * Method to execute a PDO query and optionally bind it to the class parameter.
   * @param <type> $conn A doctrine connection object
   * @param string $query A SQL query string
   * @param array $params An optional array of query parameters
   * @param string $fetchMode The PDO fetch mode to be used. Defaults to class property default.
   * @param <type> $pdoName An optional name for this query. If provided, it will save this object
   * in the _PDO collection.
   * @return Doctrine_Connection A PDO object after execution of the query.
   */
  protected function executePdoQuery( Doctrine_Connection $conn,
                                      $query,
                                      $params = array(),
                                      $fetchMode = NULL,
                                      $pdoName = NULL)
  {
    // first prepare the sql statement
    $qStatement = $conn->prepare($query);

    // then execute the query
    $pdo = $qStatement->execute($params);

    // set fetch mode the the one we are passed or our default
    if (is_null($fetchMode)) { $fetchMode = $this->defaultFetchMode; }
    $pdo->setFetchMode($fetchMode);

    // only save those pdo queries we have decided to name in the _PDO array
    // set this by reference so we can expire the $pdo variable and persist the object / connection
    if (! is_null($pdoName))
    {
      $this->_PDO[$pdoName] =& $pdo;
    }

    return $pdo ;
  }

  /**
   * Method to drop temporary table
   * @todo Replace the unweildy handling of the exceptions properly check for existence
   */
  protected function dropTempTable()
  {
    // get a connection
    $conn = $this->_conn[self::CONN_TEMP_WRITE];

    // drop the table
    try
    {
      $conn->export->dropTable($this->tempTable);

      // log this info event
      $eventMsg = "Dropped temporary table $this->tempTable";
      $this->logEvent(self::EVENT_INFO, $eventMsg);
    }
    catch(Doctrine_Connection_Exception $e)
    {
      // we only want to silence 'no such table' errors
      if ($e->getPortableCode() !== Doctrine_Core::ERR_NOSUCHTABLE)
      {
        throw new Doctrine_Export_Exception($e->getMessage());
      }
    }
  }

  /**
   * Method to create an import temp table.
   */
  protected function createTempTable()
  {
    // get a connection
    $conn = $this->_conn[self::CONN_TEMP_WRITE];

    // Drop temp if it exists
    $this->dropTempTable();

    $options = array(
      'type' => 'MYISAM',
      'charset' => 'utf8'
    );

    // Create the table
    try {

      $conn->export->createTable($this->tempTable, $this->importSpec, $options);
      $this->events[] = array("type" => "OK", "message" => "Successfully created temp table.");
    } catch (Doctrine_Exception $e) {

      //todo find a more descriptive way of displaying MySQL error messages.
      //  The Doctrine exceptions are not very helpful.
      $this->events[] = array("type" => "ERROR", "message" => "Error creating temp table for import.");
      $this->events[] = array("type" => "ERROR", "message" => $e->errorMessage());
    }
  }
}