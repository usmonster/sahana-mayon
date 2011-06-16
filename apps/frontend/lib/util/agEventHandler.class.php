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

  const       EVENT_SORT_TYPE = 1;
  const       EVENT_SORT_TS = 2;

  private     $events,
              $lastEvent,
              $lastEventByType,
              $errCount,
              $logLevelValue,
              $logEventLevel,
              $errThreshold = 1000,
              $sfContext;

  /**
   * This class's constructor.
   * @param string $tempTable The name of the temporary import table to use
   * @param string $logEventLevel An optional parameter dictating the event level logging to be used
   * @param sfContext $sfContext An instance of the sfContext to use
   */
  public function __construct($logEventLevel = NULL,
                              $minEventLevel = self::EVENT_EMERG,
                              sfContext $sfContext = NULL)
  {
    if (empty($sfContext)) { $sfContext = sfContext::getInstance(); }
    $this->sfContext = $sfContext;

    // reset any events (or initialize the handlers)
    $this->resetEvents();

    // check to see if a log level was passed at construction and, if so, set it
    if (is_null($logEventLevel)) { $logEventLevel = self::EVENT_ERR; }

    // test to see if the log level was set too low
    $logLevelValue = self::getEventLevel($logEventLevel);
    $minLevelValue = self::getEventLevel($minEventLevel);
    if ($logLevelValue < $minLevelValue)
    {
      $this->setLogEventLevel($minEventLevel);
      $this->logWarning('Event level was set too low at construction. Used hinted min level');
    }
    else
    {
      $this->setLogEventLevel($logEventLevel);
    }
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
  public function resetEvents()
  {
    $this->events = array();
    $this->events[self::EVENT_EMERG] = array();
    $this->events[self::EVENT_ALERT] = array();
    $this->events[self::EVENT_CRIT] = array();
    $this->events[self::EVENT_ERR] = array();
    $this->events[self::EVENT_WARNING] = array();
    $this->events[self::EVENT_NOTICE] = array();
    $this->events[self::EVENT_INFO] = array();
    $this->events[self::EVENT_DEBUG] = array();

    $this->lastEvent = array();

    $this->lastEventByType = array();
    $this->lastEventByType[self::EVENT_EMERG] = array();
    $this->lastEventByType[self::EVENT_ALERT] = array();
    $this->lastEventByType[self::EVENT_CRIT] = array();
    $this->lastEventByType[self::EVENT_ERR] = array();
    $this->lastEventByType[self::EVENT_WARNING] = array();
    $this->lastEventByType[self::EVENT_NOTICE] = array();
    $this->lastEventByType[self::EVENT_INFO] = array();
    $this->lastEventByType[self::EVENT_DEBUG] = array();

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

    $this->logLevelValue = self::getEventLevel($logEventLevel);
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
    if ($this->logLevelValue >= self::getEventLevel($eventType))
    {
      $logger = strtolower($eventType);
      $this->sfContext->getLogger()->$logger($eventMsg);
      $timestamp = microtime(TRUE);
      $event = array('ts' => $timestamp, 'msg' => $eventMsg);
      $this->events[$eventType][] = $event;
      $this->lastEventByType[$eventType] = $event;
      $this->lastEvent = ($event + array('type' => $eventType));
    }
  }

  /**
   * Method to log an event of type debug
   * @param $string $eventMsg An event message
   */
  public function logDebug($eventMsg)
  {
    $this->logEvent(self::EVENT_DEBUG, $eventMsg);
  }

  /**
   * Method to log an event of type Info
   * @param $string $eventMsg An event message
   */
  public function logInfo($eventMsg)
  {
    $this->logEvent(self::EVENT_INFO, $eventMsg);
  }

  /**
   * Method to log an event of type notice
   * @param $string $eventMsg An event message
   */
  public function logNotice($eventMsg)
  {
    $this->logEvent(self::EVENT_NOTICE, $eventMsg);
  }

  /**
   * Method to log an event of type WARN and log the results to file
   * @param $string $eventMsg An event message
   */
  public function logWarning($eventMsg)
  {
    $this->logEvent(self::EVENT_WARNING, $eventMsg);
  }

  /**
   * Method to explicitly log an error message and up our error counter.
   * @param string $eventMsg An error message
   * @param integer $errCount The number of failures encountered
   */
  public function logErr($eventMsg, $errCount = 1)
  {
    $this->logEvent(self::EVENT_ERR, $eventMsg);
    $this->errCount = $this->errCount + $errCount;
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
    $this->checkErrThreshold();
  }

  /**
   * Method to explicitly log an emerg message and throw an exception.
   * @param string $eventMsg An emerg message
   * @param integer $errCount The number of failures encountered
   */
  public function logEmerg($eventMsg, $errCount = 1)
  {
    $emergMsg = 'Application encountered an emergency at: ' . $eventMsg . PHP_EOL .
      'Dumping entire event log to file.';

    $this->logEvent(self::EVENT_EMERG, $emergMsg);
    $this->errCount = $this->errCount + $errCount;

    // dump all of our events to the symfony logger
    $this->dumpEventsToFile('emerg');

    self::__destruct();
    throw new Exception($eventMsg);
  }

  /**
   * Method to return the current error threshold
   */
  public function getErrThreshold()
  {
    return $this->errThreshold;
  }

  /**
   * Method to set an error threshold.
   * @param integer $integer
   */
  public function setErrThreshold($integer)
  {
    if (is_int($integer))
    {
      $this->errThreshold = $integer;
    }
    else
    {
      $this->logAlert('Error threshold {' . $integer . '} is not an integer.');
    }
  }

  /**
   * Method to check against our error threshold and update whether we should continue or not
   */
  public function checkErrThreshold()
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
      $eventLog = 'Dumping import events of type {' . $level . "}:\n";
      foreach ($events as $index => $event)
      {
        $event = sprintf("\t%s (%s): %s\n", $level, $event['ts'], $event['msg']);
        $eventLog = $eventLog . $event;
      }
      $this->sfContext->getLogger()->$logType($eventLog);
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
  public function getEvents($sortType = self::EVENT_SORT_TYPE)
  {
    if ($sortType == self::EVENT_SORT_TYPE)
    {
      return $this->events;
    }

    $sorted = array();
    foreach ($this->events as $type => $events)
    {
      foreach($events as $event)
      {
        $tsKeyed = array_merge(array('type' => $type, 'lvl' => self::getEventLevel($type)), $event);
        $sorted[] = $tsKeyed;
      }
    }
    usort($sorted, array('self', 'tsCompare'));
    return $sorted;
  }

  /**
   * Timestamp comparison method
   * @param array $a
   * @param array $b
   * @return integer
   */
  public static function tsCompare(array $a, array $b)
  {
    if ($a['ts'] == $b['ts'])
    {
      return 0;
    }
    return ($a['ts'] > $b['ts']) ? -1 : +1;
  }


  /**
   * Small helper function to return an event type's event level
   * @param string $eventType One of the EVENT_* types
   * @return integer The integer value of the event type
   */
  public static function getEventLevel($eventType)
  {
    return constant('self::EVENT_' . $eventType . '_LEVEL');
  }

  /**
   * Method to return the last event.
   * @param $eventTypeConst Constant following the EVENT_$EVENTTYPE pattern.
   * @return array An array of datapoints concerning the last inserted event.
   * <code> array('type' => $eventType, 'ts' => $timestamp, 'msg' => $eventMsg)</code>
   */
  public function getLastEvent( $eventTypeConst = NULL)
  {
    if (is_null($eventTypeConst))
    {
      return $this->lastEvent;
    }

    return $this->lastEventByType[$eventTypeConst];
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
