<?php
/**
 *
 * Provides Event Staff Data sets and Manipulation functions
 *
 * PHP Version 5
 *
 * LICENSE: This source file is subject to LGPLv3.0 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 *
 * @author Charles Wisniewski, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 *
 * @property array $addressIds A single-dimension array of address id values.
 */


class agEventStaffHelper
{
  public    $addressIds = array(),
            $lineDelimeter = "\n",
            $enforceComplete = TRUE,
            $enforceLineNumber = FALSE,
            $checkValuesForCompleteness = FALSE ;

  protected $_globalDefaultAddressStandard = 'default_address_standard',
            $_addressGeoType = 'point',
            $_startingLineNumber = 1,
            $_addressFormatComponents = array(),
            $_addressFormatRequired = array(),
            $_returnStandardId ;

  /**
   * This is the class's constructor which currently does nothing
   */
  public function __construct($eventStaffIds = NULL)
  {
    // if passed an array of event staff ids, set them as a class property
    $this->setAddressIds($eventStaffIds);
  }

  /**
   * Static method used to instantiate the agAddress class.
   * @param array $addressIds A single dimension array of address ids.
   * @return object A new instance of agAddress.
   */
  public static function init($addressIds = NULL)
  {
    $class = new agAddress($addressIds) ;
    return $class ;
  }

  /**
   * Protected method to set the default address standard / format used for returning addresses.
   */
  protected function _setDefaultReturnStandard()
  {
    $standardName = agGlobal::getParam($this->_globalDefaultAddressStandard) ;
    $standardId = agDoctrineQuery::create()
      ->select('as.id')
        ->from('agAddressStandard as')
        ->where('as.address_standard = ?', $standardName)
        ->execute(array(),DOCTRINE_CORE::HYDRATE_SINGLE_SCALAR) ;

    $this->setReturnStandard($standardId) ;
  }

  /**
   * Helper method used to set the current batch of address id's.
   *
   * @param array $addressIds A single-dimension array of address  id's.
   */
  public function setAddressIds ($addressIds)
  {
    if (! is_null($addressIds)) { $this->addressIds = $addressIds ; }
  }

  /**
   * Helper method to retrieve the $addressId properties in the event that it is passed a null
   * parameter. For use inside functions that optionally allow addresses to be specified.
   *
   * @param array $addressIds A single-dimension array of address  id's.
   * @return array $addressIds A single-dimension array of address  id's.
   */
  protected function _getAddressIds ($addressIds = NULL)
  {
    if (is_null($addressIds)) { $addressIds = $this->addressIds ; }
    return $addressIds ;
  }

  /**
   * A simple method to set the current return standard and force the re-generation of the
   * formatting components.
   * @param integer $standardId The address_standard_id to set.
   */
  public function setReturnStandard($standardId)
  {
    $this->_returnStandardId = $standardId ;
    $this->_setAddressFormatComponents() ;
  }
  /**
  * @return array A mono-dimensional associative array of event staff.
   */
  public function getEventStaff ()
  {
    // always a good idea to explicitly declare this
    $results = array() ;

    // grab our default if not explicitly passed a line number parameter
    if (is_null($enforceLineNumber)) { $enforceLineNumber = $this->enforceLineNumber ; }

    // now we grab all of our addresses and return them in an array sorted per-line
    $addresses = $this->getAddressComponentsByLine($addressIds, $enforceComplete) ;

    // start by iterating over the individual addresses
    foreach ($addresses as $addressId => $addrLines)
    {
      $strAddr = '' ;
      $line = $this->_startingLineNumber ;

      // sort the address lines to make sure we can iterate over them safely
      ksort($addrLines) ;

      // grab the last key so as not to add a delimeter
      $lineIds = array_keys($addrLines) ;
      $lastLineId = array_pop($lineIds) ;

      // now iterate per-line
      foreach ($addrLines as $lineId => $lineStr)
      {
        // if enforcing line-numbers, append additional line delimeters until $lineId and $line match
        while ($enforceLineNumber && $line < $lineId)
        {
          $strAddr = $strAddr . $this->lineDelimeter ;
          $line++ ;
        }

        // append our address string
        $strAddr = $strAddr . $lineStr ;

        // append a delimeter if one needs to be added
        if ($lineId != $lastLineId) { $strAddr = $strAddr . $this->lineDelimeter ; }
        $line++ ;
      }

      // add it to our results array
      $results[$addressId] = $strAddr ;
    }

    return $results ;
  }
}
