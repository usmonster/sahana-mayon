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
  protected   $defaultFetchMode = Doctrine_Core::FETCH_ASSOC,
              $tempTable,
              $_conn,
              $_PDO = array();

  protected function __construct()
  {
    // Sets a new connection.
    $this->setConnection();
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
    $this->_conn['temp_read'] = Doctrine_Manager::connection(NULL, 'import_temp_read');
    $this->_conn['temp_write'] = Doctrine_Manager::connection(NULL, 'import_temp_write');
    $this->_conn['normalize_write'] = Doctrine_Manager::connection(NULL, 'normalize_write');
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
  protected function executePdoQuery( $conn,
                                      $query,
                                      $params = array(),
                                      $fetchMode = NULL,
                                      $pdoName = NULL)
  {
    // execute the method
    $pdo = $conn->execute($query, $params);

    // set fetch mode
    $pdo->setFetchMode($fetchMode);

    // only save those pdo queries we have decided to name in the _PDO array
    // set this by reference so we can expire the $pdo variable and persist the object / connection
    if (! is_null($pdoName))
    {
      $this->_PDO[$pdoName] =& $pdo;
    }

    return $pdo ;
  }

  protected function dropTempTable()
  {
    //@todo create this method
  }

  protected function createTempTable()
  {
    //@todo create this method
  }
}