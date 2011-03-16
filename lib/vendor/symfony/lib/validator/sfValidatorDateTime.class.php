<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfValidatorDateTime validates a date and a time. It also converts the input value to a valid date.
 *
 * @package    symfony
 * @subpackage validator
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfValidatorDateTime.class.php 5581 2007-10-18 13:56:14Z fabien $
 */
class sfValidatorDateTime extends sfValidatorDate
{
  /**
   * @see sfValidatorDate
   */
  protected function configure($options = array(), $messages = array())
  {
    parent::configure($options, $messages);

    $this->setOption('with_time', true);
  }
  protected function doClean($value)
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
      $value = $this->convertDateArrayToString($value);
    }

    // convert timestamp to date number format
    if (is_numeric($value))
    {
      $cleanTime = (integer) $value;
      $clean     = date('YmdHis', $cleanTime);
    }
    // convert string to date number format
    else
    {
      try
      {
        $date = new DateTime($value);
        $date->setTimezone(new DateTimeZone(date_default_timezone_get()));
        $clean = $date->format('YmdHis');
      }
      catch (Exception $e)
      {
        throw new sfValidatorError($this, 'invalid', array('value' => $value));
      }
    }

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
      else
      {
        $dateMax  = new DateTime($max);
        $max      = $dateMax->format('YmdHis');
        $maxError = $dateMax->format($this->getOption('date_format_range_error'));
      }

      if ($clean > $max)
      {
        throw new sfValidatorError($this, 'max', array('value' => $value, 'max' => $maxError));
      }
    }

    // check min
    if ($min = $this->getOption('min'))
    {
      // convert timestamp to date number
      if (is_numeric($min))
      {
        $minError = date($this->getOption('date_format_range_error'), $min);
        $min      = date('YmdHis', $min);
      }
      // convert string to date number
      else
      {
        $dateMin  = new DateTime($min);
        $min      = $dateMin->format('YmdHis');
        $minError = $dateMin->format($this->getOption('date_format_range_error'));
      }

      if ($clean < $min)
      {
        throw new sfValidatorError($this, 'min', array('value' => $value, 'min' => $minError));
      }
    }

    if ($clean === $this->getEmptyValue())
    {
      return $cleanTime;
    }

    $format = $this->getOption('with_time') ? $this->getOption('datetime_output') : $this->getOption('date_output');

    return isset($date) ? $date->format($format) : date($format, $cleanTime);
  }

}
