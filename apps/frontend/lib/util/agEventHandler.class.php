<?php
/**
 * Abstract class to provide event and log handling methods
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
 *
 */
abstract class agEventHandler
{
  // used for event reporting
  const       EVENT_FATAL = 'FATAL';
  const       EVENT_OK = 'OK';
  const       EVENT_ERR = 'ERR';
  const       EVENT_WARN = 'WARN';
  const       EVENT_INFO = 'INFO';

  private     $events,
              $lastEvent,
              $errCount,
              $logLevelValues = array(),
              $logLevelValue,
              $logEventLevel = self::EVENT_ERR;

  /**
   * This class's constructor.
   * @param string $tempTable The name of the temporary import table to use
   * @param string $logEventLevel An optional parameter dictating the event level logging to be used
   */
  public function __construct($logEventLevel = NULL)
  {
    // set our default log levels
    $this->setLogLevelValues();

    // check to see if a log level was passed at construction and, if so, set it
    if (! is_null($logEventLevel)) { $this->setLogEventLevel($logEventLevel); }

    // reset any events (or initialize the handlers)
    $this->resetEvents();
  }
  /**
   * Method to reset the events array.
   */
  protected function resetEvents()
  {
    $this->lastEvent = array();

    $this->events = array();
    $this->events[self::EVENT_FATAL] = array();
    $this->events[self::EVENT_ERR] = array();
    $this->events[self::EVENT_WARN] = array();
    $this->events[self::EVENT_OK] = array();
    $this->events[self::EVENT_INFO] = array();

    $this->errCount = 0;
  }

  /**
   * Method to map log levels to a numeric hierarchy and store as a class property.
   */
  private function setLogLevelValues()
  {
    $this->logLevelValues = array();
    $this->logLevelValues[self::EVENT_FATAL] = 0;
    $this->logLevelValues[self::EVENT_ERR] = 1;
    $this->logLevelValues[self::EVENT_WARN] = 2;
    $this->logLevelValues[self::EVENT_OK] = 3;
    $this->logLevelValues[self::EVENT_INFO] = 4;
  }

  /**
   * Method to explicitly set the classes' log level
   * @param string $logLevel The log level to set
   */
  public function setLogEventLevel($logEventLevel)
  {
    $const = 'self::EVENT_' . $logEventLevel;
    if (! defined($const))
    {
      $e = 'Undefined event constant: ' . $logEventLevel;
      throw new Exception($e);
    }

    $this->logLevelValue = $this->logLevelValues[$logEventLevel];
    $this->logEventLevel = $logEventLevel;
  }

  /**
   * Method to log a new import event.
   * @param constant $eventType The event type being set. Must be one of the defined EVENT_*
   * constants defined in agImportHelper.
   * @param <type> $eventMsg The event message being set.
   */
  private function logEvent($eventType, $eventMsg)
  {
    // just to make sure we only keep to our defined event types
    $const = 'self::EVENT_' . $eventType;
    if (! defined($const))
    {
      $e = 'Undefined event constant: ' . $eventType;
      throw new Exception($e);
    }

    // if our log level has been set high enough to capture these events, do so
    if ($this->logLevelValue >= $this->logLevelValues[$eventType])
    {
      $timestamp = microtime(TRUE);
      $this->events[$eventType][] = array('ts' => $timestamp, 'msg' => $eventMsg);
      $this->lastEvent = array('type' => $eventType, 'ts' => $timestamp, 'msg' => $eventMsg);
    }
  }

  /**
   * Method to log an event of type Info
   * @param $string $eventMsg An event message
   */
  protected function logInfo($eventMsg)
  {
    $this->logEvent(self::EVENT_OK, $eventMsg);
  }

  /**
   * Method to log an event of type OK
   * @param $string $eventMsg An event message
   */
  protected function logOk($eventMsg)
  {
    $this->logEvent(self::EVENT_OK, $eventMsg);
  }

  /**
   * Method to log an event of type WARN and log the results to file
   * @param $string $eventMsg An event message
   */
  protected function logWarn($eventMsg)
  {
    $this->logEvent(self::EVENT_WARN, $eventMsg);
    sfContext::getInstance()->getLogger()->warning($eventMsg);
  }

  /**
   * Method to explicitly log an error message and up our error counter.
   * @param string $errMsg An error message
   * @param integer $errCount The number of failures encountered
   */
  protected function logErr($errMsg, $errCount = 1)
  {
    $this->logEvent(self::EVENT_ERR, $errMsg);
    $this->errCount = $this->errCount + $errCount;
    sfContext::getInstance()->getLogger()->err($errMsg);

    $this->checkErrThreshold();
  }

  /**
   * Method to explicitly log a fatal error message and throw an exception.
   * @param string $errMsg A fatal error message
   */
  protected function logFatal($errMsg, $errCount = 1)
  {
    $this->logEvent(self::EVENT_FATAL, $errMsg);
    $this->errCount = $this->errCount + $errCount;

    // dump all of our events to the symfony logger
    $err = 'Import failed at: ' . $errMsg . PHP_EOL . 'Dumping event log to file.';
    sfContext::getInstance()->getLogger()->err($err);
    $this->dumpEventsToFile('err');

    throw new Exception($errMsg);
  }

  /**
   * Method to dump the event log to a symfony logger
   * @param string $logType The symfony log type to invoke
   */
  public function dumpEventsToFile($logType)
  {
    // iterate through our events log
    foreach($this->events as $level => $events)
    {
      $eventLog = "Dumping import events of type {$level}:\n";
      foreach ($this->events as $index => $event)
      {
        $event = sprintf("%s (%s): %s\n", $level, $event['ts'], $event['msg']);
        $eventLog = $eventLog . $event;
      }
      sfContext::getInstance()->getLogger()->$logType($eventLog);
    }
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
   * Method to return the current errCount
   * @return integer The current error count
   */
  public function getErrCount()
  {
    return $this->errCount;
  }
}
