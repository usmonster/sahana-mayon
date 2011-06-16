<?php
/**
 * Provides methods for implementing PDO queries
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

abstract class agPdoHelper
{
  /**
   * @var array An array used to store connection objects
   */
  protected $_conn = array();

  /**
   * @var array An array used to store PDO queries
   */
  protected $_PDO = array();

  /**
   * @var string The PDO fetch mode to be used by the execute PDO query method
   */
   protected $defaultFetchMode = Doctrine_Core::FETCH_ASSOC;

  /*
   * Method to set the import connection object property.
   */
  abstract protected function setConnections();

  /**
   * This classes' constructor
   */
  protected function __construct()
  {
    // Sets a new connection.
    $this->setConnections();
  }

  /**
   * This classes' destructor. Mostly performs a santiy check on the connections for
   * any open transactions and rolls back if the transactions have not completed then
   * closes any open connections.
   */
  public function __destruct()
  {
    $this->closeConnections();
  }

  /**
   * Method to safely rollback a transaction by first checking the transaction level.
   * @param Doctrine_Connection $conn
   * @return boolean Whether or not a rollback was performed.
   */
  public static function rollbackSafe(Doctrine_Connection $conn)
  {
    // check for open transactions and kill them
    if ($conn->getTransactionLevel() > 0)
    {
      $conn->rollback();
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Method to get (and lazy load) a doctrine connection object
   * @return Doctrine_Connection A doctrine connection object
   */
  protected function getConnection($conn)
  {
    // Lazy load and return pdo connection.
    return $this->_conn[$conn];
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
                                      array $params = array(),
                                      $fetchMode = NULL,
                                      $pdoName = NULL)
  {
    // first prepare the sql statement
    $docStmt = $conn->prepare($query);
    $pdoStmt = $docStmt->getStatement();

    // then execute the query
    $pdoStmt->execute($params);

    // set fetch mode the the one we are passed or our default
    if (is_null($fetchMode)) { $fetchMode = $this->defaultFetchMode; }
    $pdoStmt->setFetchMode($fetchMode);

    // only save those pdo queries we have decided to name in the _PDO array
    // set this by reference so we can expire the $pdo variable and persist the object / connection
    if (! is_null($pdoName))
    {
      $this->_PDO[$pdoName] =& $pdoStmt;
    }

    return $pdoStmt ;
  }

  /**
   * Method to clear a connection object of memory and evict tables
   */
  protected static function clearConnection(Doctrine_Connection $conn)
  {
    $conn->flush();
    $conn->clear();
    $conn->evictTables();
    $conn->clear();
    return $conn;
  }

  /**
   * Method to clear all connections
   */
  protected function clearConnections()
  {
    foreach ($this->_conn as $conn)
    {
      self::clearConnection($conn);
    }
  }

  /**
   * Method to close a doctrine connection safely
   * @param Doctrine_Connection $conn A doctrine connection object
   * @param Doctrine_Manager $dm An optional doctrine manager object to use
   * @return boolean Indicates success or failure
   */
  protected function closeConnection(Doctrine_Connection $conn, Doctrine_Manager $dm = NULL)
  {
    if (is_null($dm)) { $dm = Doctrine_Manager::getInstance(); }
    if ($conn == $dm->getCurrentConnection())
    {
      $eventMsg = 'Trying to close the current connection is not allowed. Skipping request.';
      sfContext::getInstance()->getLogger()->alert($eventMsg);
      return FALSE;
    }

    $idx = array_search($conn, $this->_conn, TRUE);

    // check for open transactions and rollback
    if (self::rollbackSafe($conn))
    {
      $eventMsg = 'Found unresolved transaction on connection ' . $conn->getName() . ' during ' .
        __CLASS__ . 'class destruction. Rolled back changes.';
      sfContext::getInstance()->getLogger()->alert($eventMsg);
    }

    $dm->closeConnection($conn);
    unset($conn);
    if ($idx !== FALSE) { unset($this->_PDO[$idx]); }
    return TRUE;
  }

  /**
   * Method to close all connections open in this object
   */
  protected function closeConnections()
  {
    $dm = Doctrine_Manager::getInstance();
    $dm->setCurrentConnection('doctrine');

    foreach($this->_conn as $idx => $conn)
    {
      $this->closeConnection($conn,$dm);
      unset($this->_conn[$idx]);
    }
  }
}
