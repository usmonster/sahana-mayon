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
  protected   $tempTable,
              $_conn,
              $_PDO;

  public function __construct()
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

  /*
   * Method to ensure that PDO queries pass through the _PDO property
   */
  protected function executePdoQuery($query, $params = array())
  {
    $this->_PDO = $this->_conn->execute($query, $params);
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