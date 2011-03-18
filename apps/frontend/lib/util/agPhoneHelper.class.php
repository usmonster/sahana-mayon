<?php
/**
 *
 * Provides bulk-phone manipulation methods
 *
 * PHP Version 5
 *
 * LICENSE: This source file is subject to LGPLv3.0 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 *
 * @author Shirley Chan, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
class agPhoneHelper extends agBulkRecordHelper
{
  // these constants map to the phone get types that are supported in other function calls
  const     PHN_GET_FORMATTED = 'getFormattedPhone';

  protected $_globalDefaultPhoneFormatType = 'default_phone_format_type',
            $_phoneValidation = array(),
            $_phoneDisplayFormat = array(),
            $_returnFormatId;

  /**
   * This is the class's constructor whic pre-loads the formatting elements according to the default
   * return format.
   *
   * @param array $phoneIds A single dimension array of phone id values.
   */
  public function __construct($phoneIds = NULL)
  {
    // if passed an array of phone id's, set them as a class property
    parent::__construct($phoneIds);

    // set our default phone standard and pick up the formatting components
    $this->_setDefaultReturnFormat();
  }

  /**
   * Method used to construct the base query object used by other objects in this class.
   *
   * @param string $phoneFormatType A string value of phone format type.
   * @return agDoctrineQuery An extended doctrine query object.
   */
  protected function _getPhoneFormatType($phoneFormatType)
  {
    // construct our base query object
    $q = agDoctrineQuery::create()
      ->select('pf.id')
        ->from('agPhoneFormat pf')
            ->innerJoin('pf.agPhoneFormatType pft')
        ->where('pft.phone_format_type = ?', $phoneFormatType);

    return $q;
  }

  /**
   * Protected method to set the default phone format used for returning phone.
   */
  protected function _setDefaultReturnFormat()
  {
    $phoneFormatType = agGlobal::getParam($this->_globalDefaultPhoneFormatType);
    $q = $this->_getPhoneFormatType($phoneFormatType);
    $formatId = $q->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);

    $this->setReturnFormat($formatId);
  }

  /**
   * A simple method to set the current return format and force the re-generation of the
   * formatting components.
   * @param integer $formatId The phone_format_id to set.
   */
  public function setReturnFormat($formatId)
  {
    $this->_returnFormatId = $formatId;
    $this->_setPhoneFormatComponent();
  }

  /**
   * This method queries the database for the required formatting components and loads several
   * properties with quick, referenceable information used in formatting endeavors.
   */
  protected function _setPhoneFormatComponent()
  {
    $q = agDoctrineQuery::create()
      ->select('pf.id')
          ->addSelect('pft.validation')
          ->addSelect('pft.match_pattern')
          ->addSelect('pft.replacement_pattern')
        ->from('agPhoneFormat pf')
            ->innerJoin('pf.agPhoneFormatType pft')
          ->whereIn('pf.id', $this->_returnFormatId);

    // here we choose a custom hydration method to allow us to manipulate the results data twice
    $formatComponents = $q->execute(array(), DOCTRINE_CORE::HYDRATE_NONE);
    foreach($formatComponents as $fc)
    {
      // create a simple phone validation array.
      $this->_phoneValidation[$fc[0]] = $fc[1];

      // create a simple display format array.
      $this->_phoneDisplayFormat[$fc[0]] = array($fc[2], $fc[3]);
    }
  }

  /**
   * Method used to construct the base query object used by other objects in this class.
   *
   * @param array $phoneIds A single-dimension array of phone id's.
   * @return agDoctrineQuery An extended doctrine query object.
   */
  protected function _getPhone($phoneIds)
  {
    // if no (null) ID's are passed, get the phoneId's from the class property
    $phoneIds = $this->getRecordIds($phoneIds);

    // construct our base query object
    $q = agDoctrineQuery::create()
      ->select('pc.id')
          ->addSelect('pc.phone_contact')
          ->addSelect('pft.match_pattern')
          ->addSelect('pft.replacement_pattern')
          ->addSelect('pf.id')
          ->addSelect('pft.id')
        ->from('agPhoneContact pc')
          ->innerJoin('pc.agPhoneFormat pf')
          ->innerJoin('pf.agPhoneFormatType pft')
        ->whereIn('pc.id', $phoneIds);

    return $q;
  }

  /**
   * A workhorse method useful for returning phone value and it's format id.
   *
   * @param array $phoneIds An optional single-dimension array of phone id's.
   * If NULL, the classes' $phoneIds property is used.
   * @return array An associative array,
   * array( phone_id => array(phone, match_pattern, replacement_pattern)).
   */
  public function getPhone( $phoneIds = NULL)
  {
    // return our base query object
    $q = $this->_getPhone($phoneIds);

    $results = $q->execute(array(), 'key_value_array');
    return $results;
  }

  /**
   * Method to return a formatted phone as a string.
   * 
   * @param array $phoneIds An optional single-dimension array of phone id's. If NULL, the
   * classes' $phoneIds property is used.
   * @return array A mono-dimensional associative array keyed by phone_id with the formatted
   * phone as the value.
   */
  public function getFormattedPhone( $phoneIds = NULL )
  {
    // always a good idea to explicitly declare this
    $results = array();

    // now we grab all of our phones.
    $phones = $this->getPhone($phoneIds);

    // start by iterating over the individual phones
    foreach ($phones as $phoneId => $phone)
    {
      // format phone
      $formattedPhone = preg_replace($phone[2],
                                     $phone[3],
                                     $phone[0]);

      // add formatted phone to our results array
      $results[$phoneId] = $formattedPhone;
    }

    return $results;
  }

  /**
   * Simple method to return the current phone format id.
   *
   * @return integer phone_format_id
   */
  public function getFormatId()
  {
    return $this->_returnFormatId;
  }

  /**
   * A simple getter to return the current phone validation.
   *
   * @return array $result An associative array (phone_format_id => validation).
   */
  public function getPhoneValidation()
  {
    return $this->_phoneValidation;
  }

  /**
   * A simple getter to return the current phone format display.
   *
   * @return array $result A two dimensional array (phone_format_id => array(match_pattern string, replacement_pattern string)).
   */
  public function getPhoneDisplayFormat()
  {
    return $this->_phoneDisplayFormat;
  }

}
