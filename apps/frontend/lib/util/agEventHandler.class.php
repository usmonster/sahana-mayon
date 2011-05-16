<?php
/**
 * Abstract class to provide event and log handling methods. These log levels follow the same
 * paradigm as the PEAR log levels. See http://www.indelible.org/php/Log/guide.html#log-levels for
 * more information.
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
class agEventHandler
{
  // used for event reporting
  const       EVENT_EMERG = 'EMERG';      // System is unusable
  const       EVENT_ALERT = 'ALERT';      // Immediate action required
  const       EVENT_CRIT = 'CRIT';        // Critical conditions
  const       EVENT_ERR = 'ERR';          // Error conditions
  const       EVENT_WARNING = 'WARNING';  // Warning conditions
  const       EVENT_NOTICE = 'NOTICE';    // Normal but significant (aka, OK)
  const       EVENT_INFO = 'INFO';        // Informational
  const       EVENT_DEBUG = 'DEBUG';      // Debug-level messages

  const       EVENT_EMERG_LEVEL = 1;      // System is unusable
  const       EVENT_ALERT_LEVEL = 2;      // Immediate action required
  const       EVENT_CRIT_LEVEL = 4;       // Critical conditions
  const       EVENT_ERR_LEVEL = 8;        // Error conditions
  const       EVENT_WARNING_LEVEL = 16;   // Warning conditions
  const       EVENT_NOTICE_LEVEL = 32;    // Normal but significant (aka, OK)
  const       EVENT_INFO_LEVEL = 64;      // Informational
  const       EVENT_DEBUG_LEVEL = 128;    // Debug-level messages

  private     $events,
              $lastEvent,
              $errCount,
              $logLevelValue,
              $logEventLevel = self::EVENT_ERR;

  protected   $errThreshold = 100;

  /**
   * This class's constructor.
   * @param string $tempTable The name of the temporary import table to use
   * @param string $logEventLevel An optional parameter dictating the event level logging to be used
   */
  public function __construct($logEventLevel = NULL)
  {
    // check to see if a log level was passed at construction and, if so, set it
    if (! is_null($logEventLevel)) { $this->setLogEventLevel($logEventLevel); }

    // reset any events (or initialize the handlers)
    $this->resetEvents();
  }

  /**
   * This classes' destructor.
   */
  public function __destruct()
  {
    // an empty destructor called in logFatal()
  }

  /**
   * Method to reset the events array.
   */
  protected function resetEvents()
  {
    $this->lastEvent = array();

    $this->events = array();
    $this->events[self::EVENT_EMERG] = array();
    $this->events[self::EVENT_ALERT] = array();
    $this->events[self::EVENT_CRIT] = array();
    $this->events[self::EVENT_ERR] = array();
    $this->events[self::EVENT_WARNING] = array();
    $this->events[self::EVENT_NOTICE] = array();
    $this->events[self::EVENT_INFO] = array();
    $this->events[self::EVENT_DEBUG] = array();

    $this->errCount = 0;
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

    $this->logLevelValue = constant('self::EVENT_' . $logEventLevel . '_LEVEL');
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
    if ($this->logLevelValue >= constant('self::EVENT_' . $eventType . '_LEVEL'))
    {
      $timestamp = microtime(TRUE);
      $this->events[$eventType][] = array('ts' => $timestamp, 'msg' => $eventMsg);
      $this->lastEvent = array('type' => $eventType, 'ts' => $timestamp, 'msg' => $eventMsg);
    }
  }

  /**
   * Method to log an event of type debug
   * @param $string $eventMsg An event message
   */
  public function logDebug($eventMsg)
  {
    $this->logEvent(self::EVENT_DEBUG, $eventMsg);
    sfContext::getInstance()->getLogger()->debug($eventMsg);
  }

  /**
   * Method to log an event of type Info
   * @param $string $eventMsg An event message
   */
  public function logInfo($eventMsg)
  {
    $this->logEvent(self::EVENT_INFO, $eventMsg);
    sfContext::getInstance()->getLogger()->info($eventMsg);
  }

  /**
   * Method to log an event of type notice
   * @param $string $eventMsg An event message
   */
  protected function logNotice($eventMsg)
  {
    $this->logEvent(self::EVENT_NOTICE, $eventMsg);
    sfContext::getInstance()->getLogger()->notice($eventMsg);
  }

  /**
   * Method to log an event of type WARN and log the results to file
   * @param $string $eventMsg An event message
   */
  protected function logWarning($eventMsg)
  {
    $this->logEvent(self::EVENT_WARNING, $eventMsg);
    sfContext::getInstance()->getLogger()->warning($eventMsg);
  }

  /**
   * Method to explicitly log an error message and up our error counter.
   * @param string $eventMsg An error message
   * @param integer $errCount The number of failures encountered
   */
  protected function logErr($eventMsg, $errCount = 1)
  {
    $this->logEvent(self::EVENT_ERR, $eventMsg);
    $this->errCount = $this->errCount + $errCount;
    sfContext::getInstance()->getLogger()->err($eventMsg);

    $this->checkErrThreshold();
  }

  /**
   * Method to explicitly log a critical message and up our error counter.
   * @param string $eventMsg A critical message
   * @param integer $errCount The number of failures encountered
   */
  public function logCrit($eventMsg, $errCount = 1)
  {
    $this->logEvent(self::EVENT_CRIT, $eventMsg);
    $this->errCount = $this->errCount + $errCount;
    sfContext::getInstance()->getLogger()->crit($eventMsg);

    $this->checkErrThreshold();
  }

  /**
   * Method to explicitly log an alert message and up our error counter.
   * @param string $eventMsg An alert message
   * @param integer $errCount The number of failures encountered
   */
  public function logAlert($eventMsg, $errCount = 1)
  {
    $this->logEvent(self::EVENT_ALERT, $eventMsg);
    $this->errCount = $this->errCount + $errCount;
    sfContext::getInstance()->getLogger()->alert($eventMsg);

    $this->checkErrThreshold();
  }

  /**
   * Method to explicitly log an emerg message and throw an exception.
   * @param string $eventMsg An emerg message
   * @param integer $errCount The number of failures encountered
   */
  public function logEmerg($eventMsg, $errCount = 1)
  {
    $this->logEvent(self::EVENT_EMERG, $eventMsg);
    $this->errCount = $this->errCount + $errCount;

    // dump all of our events to the symfony logger
    $err = 'Import failed at: ' . $eventMsg . PHP_EOL . 'Dumping event log to file.';
    sfContext::getInstance()->getLogger()->emerg($err);
    $this->dumpEventsToFile('err');

    self::__destruct();
    throw new Exception($eventMsg);
  }

  /**
   * Method to check against our error threshold and update whether we should continue or not
   */
  private function checkErrThreshold()
  {
    // continue only if our error count is below our error threshold
    if (0 < $this->errThreshold && $this->errThreshold < $this->getErrCount())
    {
      $errMsg = 'Import error threshold ({' . $this->errThreshold . '}) has been exceeded.';
      $this->logEmerg($errMsg);
    }
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
