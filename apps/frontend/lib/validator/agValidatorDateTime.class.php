<?php

/**
 *
 * agValidatorDateTime validates an integer. It also converts the input value to minutes
 * assuming that the user inputs a number of day(s)
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @package    agasti
 * @subpackage validator
 * @author     Charles Wisniewski, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
class agValidatorDateTime extends sfValidatorDateTime{

  public function convertDateArrayToString($value){
    return parent::convertDateArrayToString($value);
  }
  public function convertDateArrayToUnix($value){
    return strtotime(parent::convertDateArrayToString($value));
  }

  //if this validator is used, it accepts a front end datetime input.... converting that data to
  public function doClean($value)
  {
    // check date format
    if (is_string($value) && $regex = $this->getOption('date_format'))
    {
      if (!preg_match($regex, $value, $match))
      {
        throw new sfValidatorError($this, 'bad_format', array('value' => $value, 'date_format' => $this->getOption('date_format_error') ? $this->getOption('date_format_error') : $this->getOption('date_format')));
      }

      $value = $match;
    }

    // convert array to date string
    
    if (is_array($value))
    {
      $oldvalue = $this->convertDateArrayToString($value);
      $value = $this->convertDateArrayToUnix($value);
    }

    // convert timestamp to date number format
//    if (is_numeric($oldvalue))
//    {
//      $cleanTime = (integer) $oldvalue;
//      $clean     = date('YmdHis', $cleanTime);
//    }
//    // convert string to date number format
//    else
//    {
      try
      {
        $date = new DateTime($oldvalue);
        $date->setTimezone(new DateTimeZone(date_default_timezone_get()));
        $clean = $date->format('YmdHis');
      }
      catch (Exception $e)
      {
        throw new sfValidatorError($this, 'invalid', array('value' => $oldvalue));
      }
    //}

    // check max
    if ($max = $this->getOption('max'))
    {
      // convert timestamp to date number format
      if (is_numeric($max))
      {
        $maxError = date($this->getOption('date_format_range_error'), $max);
        $max      = date('YmdHis', $max);
      }
      // convert string to date number
      else {
        $dateMax = new DateTime($max);
        $max = $dateMax->format('YmdHis');
        $maxError = $dateMax->format($this->getOption('date_format_range_error'));
      }

      if ($clean > $max) {
        throw new sfValidatorError($this, 'max', array('value' => $value, 'max' => $maxError));
      }
    }

    // check min
    if ($min = $this->getOption('min')) {
      // convert timestamp to date number
      if (is_numeric($min)) {
        $minError = date($this->getOption('date_format_range_error'), $min);
        $min = date('YmdHis', $min);
      }
      // convert string to date number
      else {
        $dateMin = new DateTime($min);
        $min = $dateMin->format('YmdHis');
        $minError = $dateMin->format($this->getOption('date_format_range_error'));
      }

      if ($clean < $min) {
        throw new sfValidatorError($this, 'min', array('value' => $value, 'min' => $minError));
      }
    }

    if ($clean === $this->getEmptyValue())
    {
      return $cleanTime;
    }

    $format = $this->getOption('with_time') ? $this->getOption('datetime_output') : $this->getOption('date_output');

    //return isset($date) ? $date->format($format) : date($format, $cleanTime);
    return $value; 
  }
}
?>
