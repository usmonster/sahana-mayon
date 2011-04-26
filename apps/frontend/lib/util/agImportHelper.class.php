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
              $_PDO;

  protected function __construct()
  {
    // Sets a new connection.
    $this->setConnection();
  }

  /**
   * Method to get (and lazy load) a doctrine connection object
   * @return Doctrine_Connection A doctrine connection object
   */
  protected function getConnection()
  {
    // Lazy load and return pdo connection.
    if (!isset($this->_conn)) { $this->setConnection(); }
    return $this->_conn;
  }

  /*
   * Method to set the import connection object property
   */
  protected function setConnection()
  {
    $this->_conn = Doctrine_Manager::connection(NULL, 'import_data_load');
  }

  /**
   * Method to execute a PDO query and bind it to the class parameter.
   * @param string $query A SQL query string
   * @param array $params An optional array of query parameters
   * @param string $fetchMode The PDO fetch mode to be used. Defaults to class property default.
   * @return Doctrine_Connection A PDO object after execution of the query.
   */
  protected function executePdoQuery($query, $params = array(), $fetchMode = NULL)
  {
    // execute the method
    $this->_PDO = $this->_conn->execute($query, $params);

    // set fetch mode
    $this->_PDO->setFetchMode($fetchMode);

    return $this->_PDO ;
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