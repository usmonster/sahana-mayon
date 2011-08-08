<?php

/**
 * Abstract class to provide bulk-address manipulation methods
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
abstract class agBulkRecordHelper
{

  const       REGEX_MULTISPACE_PATTERN = '/  +/';
  const       REGEX_MULTISPACE_REPLACE = ' ';

  public      $strictBatchSize = FALSE,
              $throwOnError = TRUE,
              $purgeOrphans = FALSE,
              $keepHistory = TRUE;
  
  protected   $recordIds = array(),
              $_batchSizeModifier = 1,
              $_recordCount,
              $_defaultBatchSize,
              $enforceStrict,
              $createEdgeTableValues;

  /**
   * This is the class's constructor which loads the $_recordIds property.
   *
   * @param array $_recordIds A single dimension array of address id values.
   */
  public function __construct(array $recordIds = NULL)
  {
    // pick up our default batch size
    $batchSize = ((agGlobal::getParam('default_batch_size')) / $this->_batchSizeModifier);
    $this->_defaultBatchSize = abs($batchSize);
    $this->enforceStrict = agGlobal::getParam('enforce_strict_contact_formatting');
    $this->createEdgeTableValues = agGlobal::getParam('create_edge_table_values');

    // if passed an array of address id's, set them as a class property
    $this->setRecordIds($recordIds);
  }

  /**
   * Static method used to instantiate the class.
   * @param array $recordIds A single dimension array of record ids.
   * @return object A new instance of this class.
   */
  public static function init(array $recordIds = NULL)
  {
    $childClass = get_called_class();
    $class = new $childClass($recordIds);
    return $class;
  }

  /**
   * Helper method used to set the current batch of record id's.
   *
   * @param array $recordIds A single-dimension array of record id's.
   */
  public function setRecordIds(array $recordIds = NULL)
  {
    // as long as $recordIds exists, set the class property
    if (isset($recordIds)) {
      // just because it might be useful, we'll pick up the count as well
      $this->_recordCount = count($recordIds);

      // we'll also make a call to show a warning of the default record count has been exceeded
      $this->_logBatchSizeExceeded();

      // if all is well, set our recordIds property
      $this->recordIds = $recordIds;
    }
  }

  /**
   * Helper method to retrieve the $_recordId property in the event that it is passed a null
   * parameter. For use inside functions that optionally allow recordIds to be specified.
   *
   * @param array $recordIds A single-dimension array of record id's.
   * @return array $recordIds A single-dimension array of record id's.
   */
  public function getRecordIds(array $recordIds = NULL)
  {
    if (!isset($recordIds)) {
      return $this->recordIds;
    } else {
      return $recordIds;
    }
  }

  /**
   * Method to log warnings if the batch size is exceeded by the number of variables returned.
   */
  private function _logBatchSizeExceeded()
  {
    if ($this->_recordCount > $this->_defaultBatchSize) {
      // calculate the # records exceeded and built our notice
      $recordsExceeded = ($this->_recordCount - $this->_defaultBatchSize);
      $notice = sprintf(
              'You have exceeded the maximum recommended batch size of %d by %d records.' .
              ' Please consider reducing your record size.',
              $this->_defaultBatchSize,
              $recordsExceeded
      );

      // log the event
      sfContext::getInstance()->getLogger()->notice($notice);

      if ($this->strictBatchSize) {
        throw new sfException($notice);
      }
    }
  }

  /**
   * Method to take a single variable and encapsulate it in an array.
   * 
   * @param array|variable $var An array or variable
   * @return array An array.
   */
  protected function varToArray($var)
  {
    // check to see if the variable is already an array and reflect if true
    if (is_array($var)) {
      return $var;
    }

    // if that didn't pan-out, wrap it in an array and pass it through.
    return array($var);
  }

  /**
   * Method to take a record component array and return a json encoded, md5sum'ed record component hash.
   * @param array $recordComponentsArray An associative array of record components keyed by
   * elementId with the string value.
   * @param boolean $isKeyOrderTrusted A flag to specify whether or not the component array already
   * has a trusted order. A value of TRUE will skip the ordering step of hash generation.
   * @return string(128) A 128-bit md5sum string.
   */
  public static function getRecordComponentsHash(array $recordComponentsArray,
                                                 $isKeyOrderTrusted = FALSE)
  {
    // first off, we don't trust the sorting of the components so we do our own
    if (!$isKeyOrderTrusted) {
      ksort($recordComponentsArray);
    }

    // we json encode the return to
    return md5(json_encode($recordComponentsArray));
  }

  /**
   * Method to take a value and return it trimmed of undesirable characters and strToLowered
   * @return string
   */
  public static function ucTrim($value)
  {
    return strtolower(self::fullTrim($value));
  }

  /**
   * Method to take a value and return it trimmed of undesirable characters and strToLowered
   * @return string
   */
  public static function fullTrim($value)
  {
    return trim(
      preg_replace(self::REGEX_MULTISPACE_PATTERN, self::REGEX_MULTISPACE_REPLACE, $value)
    );
  }
}