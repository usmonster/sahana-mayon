<?php

/**
 * agDateTimeHelper
 *
 * Converts a number of seconds or milliseconds into an array of hours,
 * days, minutes and seconds.
 *
 * LICENSE: This source file is subject to LGPLv3.0 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 *
 * @author     Antonio Estrada, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
class agDateTimeHelper
{
  // Conversion Constants
  const MSEC_IN_DAY = 86400000;
  const SEC_IN_DAY = 86400;

  const MSEC_IN_HOUR = 3600000;
  const SEC_IN_HOUR = 3600;

  const MSEC_IN_MINUTE = 60000;
  const SEC_IN_MINUTE = 60;

  const MSEC_IN_SEC = 1000;

  /**
   *
   * Returns a parsed time interval in usable form.
   *
   * parsedTime() takes and integer representing a time interval and a string
   * representing a unit of time, and returns an array that contains the same
   * time interval parsed into days, hours, minutes, and seconds.
   *
   * @param integer $timeInterval An integer time interval in seconds or
   * milliseconds
   * @param string $unitsOfTime $unitsofTime is either 's' for seconds, or
   * 'ms' for milliseconds
   * @return array An array containing the number of days, hours minutes,
   * seconds, and milliseconds.
   *
   */
  public static function parsedTime($timeInterval, $unitsOfTime)
  {
      $dateTimeHelperInstance = new DateTimeHelper($timeInterval, $unitsOfTime);

      return $dateTimeHelperInstance->getParsedTime();
  }

  // INSTANCE (OBJECT) MEMBERS
  // =========================

  // Stores our parsed time
  private $parsedTimeArrayInstance = array(

      "days"          =>      null,
      "hours"         =>      null,
      "minutes"       =>      null,
      "seconds"       =>      null,
      "milliseconds"  =>      null

  );


  /**
   *
   * The constructor initializes the object and calls the appropriate
   * time parsing function based on the arguments supplied. This constructor
   * is also used by Static functions to emulate functionality.
   *
   * @param integer $timeInterval An integer time interval in seconds or milliseconds
   * @param string $unitsOfTime $unitsofTime is either 's' for seconds, or 'ms' for milliseconds
   *
   */
  public function __construct($timeInterval, $unitsOfTime)
  {

      if ($unitsOfTime == 'ms')
          $this->setMillisecondsToParsedTime($timeInterval);
      else if ($unitsOfTime == 's')
          $this->setSecondsToParsedTime($timeInterval);

  }

  /**
   *
   * Takes an integer time interval in seconds and converts it.
   *
   * Takes an integer number of seconds as an argument and parses the time
   * interval into a corresponding number of days, hours, minutes and seconds.
   * Leaves $this->parsedTimeArrayInstance['milliseconds'] set to NULL.
   *
   * @param integer $seconds
   *
   *
   */
  public function setSecondsToParsedTime($seconds)
  {
      $this->parsedTimeArrayInstance['days'] = (int) ($seconds / DateTimeHelper::SEC_IN_DAY);
      $hours_remaining = $seconds % DateTimeHelper::SEC_IN_DAY;

      $this->parsedTimeArrayInstance['hours'] = (int) ($hours_remaining / DateTimeHelper::SEC_IN_HOUR);
      $minutes_remaining = $hours_remaining % DateTimeHelper::SEC_IN_HOUR;

      $this->parsedTimeArrayInstance['minutes'] = (int) ($minutes_remaining / DateTimeHelper::SEC_IN_MINUTE);
      $seconds_remaining = $minutes_remaining % DateTimeHelper::SEC_IN_MINUTE;

      $this->parsedTimeArrayInstance['seconds'] = $seconds_remaining;
  }

  /**
   *
   * Takes an integer time interval in seconds and converts it.
   *
   * Takes an integer number of milliseconds as an argument and parses the
   * time interval into a corresponding number of days, hours, minutes,
   * seconds, and milliseconds.
   *
   * @param integer $milliseconds
   *
   */
  public function setMillisecondsToParsedTime($milliseconds)
  {
      $this->parsedTimeArrayInstance['days'] = (int) ($milliseconds / DateTimeHelper::MSEC_IN_DAY);
      $hours_remaining = $milliseconds % DateTimeHelper::MSEC_IN_DAY;

      $this->parsedTimeArrayInstance['hours'] = (int) ($hours_remaining / DateTimeHelper::MSEC_IN_HOUR);
      $minutes_remaining = $hours_remaining % DateTimeHelper::MSEC_IN_HOUR;

      $this->parsedTimeArrayInstance['minutes'] = (int) ($minutes_remaining / DateTimeHelper::MSEC_IN_MINUTE);
      $seconds_remaining = $minutes_remaining % DateTimeHelper::MSEC_IN_MINUTE;

      $this->parsedTimeArrayInstance['seconds'] = (int) ($seconds_remaining / DateTimeHelper::MSEC_IN_SEC);
      $milliseconds_remaining = $seconds_remaining % DateTimeHelper::MSEC_IN_SEC;

      $this->parsedTimeArrayInstance['milliseconds'] = $milliseconds_remaining;
  }

  /**
   * Accessor method to return array containing parsed times.
   *
   * @return array Array containing the number of days, minutes, seconds, and milliseconds
   */
  public function getParsedTime()
  {
      return $this->parsedTimeArrayInstance;
  }

  /**
   * Static function to return a phptimestamp from a time string and, if the time parameter is null
   * assign it a default value of the current timestamp.
   *
   * @param string $time A string representation of time to be converted to a timestamp
   * @return timestamp A php datetime (unix epoch) value
   */
  public static function defaultTimestampFormat($time = NULL)
  {
    $timestamp = (is_null($time)) ? time() : strtotime($time) ;
    return $timestamp ;
  }

  /**
   * Static function to return a MySQL-formatted time string from a php timestamp
   *
   * @param timestamp A php datetime (unix epoch) value
   * @return string A MySQL-formatted time string
   */
  public static function tsToString($timestamp)
  {
    $tsTimeString = date ('Y-m-d H:i:s', $timestamp) ;
    return $tsTimeString ;
  }
}
