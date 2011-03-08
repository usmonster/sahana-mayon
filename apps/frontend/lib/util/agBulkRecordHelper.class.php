<?php
/**
 *
 * Provides bulk-address manipulation methods
 *
 * PHP Version 5
 *
 * LICENSE: This source file is subject to LGPLv3.0 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 *
 * @author Chad Heuschober, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 *
 * @property array $_recordIds A single-dimension array of record id values.
 */

abstract class agBulkRecordHelper
{
  public    $strictBatchSize = FALSE ;

  protected $_recordIds = array(),
            $_recordCount,
            $_defaultBatchSize ;

  /**
   * This is the class's constructor which loads the $_recordIds property.
   *
   * @param array $_recordIds A single dimension array of address id values.
   */
  public function __construct($recordIds = NULL)
  {
    // pick up our default batch size
    $this->_defaultBatchSize = agGlobal::getParam('default_batch_size') ;

    // if passed an array of address id's, set them as a class property
    $this->setRecordIds($recordIds);
  }

  /**
   * Static method used to instantiate the class.
   * @param array $recordIds A single dimension array of record ids.
   * @return object A new instance of this class.
   */
  public static function init($recordIds = NULL)
  {
    $childClass = get_called_class() ;
    $class = new $childClass($recordIds) ;
    return $class ;
  }

  /**
   * Helper method used to set the current batch of record id's.
   *
   * @param array $recordIds A single-dimension array of record id's.
   */
  public function setRecordIds($recordIds)
  {
    // as long as $recordIds exists, set the class property
    if (isset($recordIds) && is_array($recordIds))
    {
      $this->_recordIds = $recordIds ;
    }

    // just because it might be useful, we'll pick up the count as well
    $this->_recordCount = count($this->_recordIds) ;

    // we'll also make a call to show a warning of the default record count has been exceeded
    $this->__logWarningBatchSizeExceeded() ;
  }

  /**
   * Helper method to retrieve the $_recordId property in the event that it is passed a null
   * parameter. For use inside functions that optionally allow recordIds to be specified.
   *
   * @param array $recordIds A single-dimension array of record id's.
   * @return array $recordIds A single-dimension array of record id's.
   */
  public function getRecordIds($recordIds = NULL)
  {
    if (is_null($recordIds))
    {
      return $this->_recordIds ;
    }
    else
    {
      return $recordIds ;
    }
  }

 private function __logWarningBatchSizeExceeded()
 {
  if ($this->_recordCount > $this->_defaultBatchSize)
  {
    // calculate the # records exceeded and built our notice
    $recordsExceeded = ($this->_recordCount - $this->_defaultBatchSize) ;
    $notice = sprintf('You have exceeded the maximum recommended batch size of %d by %d records.
    Please consider reducing your record size.', $this->_defaultBatchSize, $recordsExceeded) ;

    // log the event
    sfContext::getInstance()->getLogger()->notice($notice) ;

    if ($this->strictBatchSize)
    {
      throw new sfException($notice) ;
    }
  }
 }

}