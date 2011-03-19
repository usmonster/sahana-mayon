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
  const     PHN_GET_FORMATTED = 'getFormattedPhone',
            PHN_GET_COMPONENT_SEGMENTS = 'getPhoneComponentSegments',
            PHN_GET_COMPONENT = 'getPhoneComponent',
            PHN_AREA_CODE = '_phn_area_code',
            PHN_FIRST_THREE = '_phn_first_three',
            PHN_LAST_FOUR = '_phn_last_four',
            PHN_EXTENSION = '_phn_extension',
            PHN_DEFAULT = '_phn_default';

  protected $_globalDefaultPhoneFormatType = 'default_phone_format_type',
            $_phoneValidation = array(),
            $_phoneDisplayFormat = array(),
            $_returnFormatId,
            $_startIndex = 0,
            $_phn_area_code = array('startIndex' => 0, 'length' => 3),
            $_phn_first_three = array('startIndex' => 3, 'length' => 3),
            $_phn_last_four = array('startIndex' => 6, 'length' => 4),
            $_phn_extension = array('startIndex' => 10, 'length' => NULL),
            $_phn_default = array('startIndex' => 0, 'length' => NULL);

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
   * Method to get the explicit part of the phone (either area code, extension, etc.)
   *
   * @param array $phones An two-dimension associative array, keyed by phone id and valued with
   * phone info.
   * @param boolean $keepAsDefault Determine whether or not to remove all non-numeric characters from phone.
   * @param integer $startIndex An integer used as the first param of the substr method.
   * @param integer $length An optional integer used as the second param of the substr method.  If
   * NULL, pass in the first param and leave out the second param of substr method.
   * @return array A mono-dimensional associative array, where
   * array( phoneId => A phone segment)
   */
  protected function _getPhoneComponent($phones = NULL, $keepAsDefault, $startIndex = NULL, $length = NULL )
  {
    // always a good idea to explicitly declare this
    $results = array();

    // if no (null) start index is passed, get the start index from the class property
    if (is_null($startIndex)) {
      $startIndex = $this->_startIndex;
    }

    foreach($phones as $phoneId => $phone) {
      if ($keepAsDefault) { $numericPhone = $phone[0]; }
      else { $numericPhone = preg_replace('/[^0-9]/', '', $phone[0]); }
      $results[$phoneId] = (is_null($length)) ? substr($numericPhone, $startIndex) :
                                                substr($numericPhone, $startIndex, $length);
    }
    return $results;
  }

  /**
   * Method to call another method to get the explicit part of 
   * the phone (either area code, extension, etc.)
   * 
   * @param array $phoneIds An optional single-dimension array of phone id's. If NULL, the
   * classes' $phoneIds property is used.
   * @param array $strSubsetType An optional associative array defining the param for the method substr.
   * @return array A mono-dimensional associative array, where
   * array( phoneId => A phone segment)
   */
  public function getPhoneComponent( $phoneIds = NULL, $strSubsetType = NULL)
  {
    // always a good idea to explicitly declare this
    $results = array();

    // now we grab all of our phones.
    $phones = $this->getPhone($phoneIds);

    $strSubsetInfo = $this->_setPhoneComponent($strSubsetType);

    $strSubset = $strSubsetInfo[1];
    if ($strSubsetInfo[0] == self::PHN_DEFAULT) { $keepAsDefault = 1; }
    else { $keepAsDefault = 0; }
    $results = $this->_getPhoneComponent($phones, $keepAsDefault, $strSubset['startIndex'], $strSubset['length']);

    return $results;
  }

  /**
   * Method to return the appropriate phone component substr array set.
   *
   * @param string $strSubsetType A string to determine the phone substr array.  If NULL, return class
   * default property.
   * @return array $this->array An associative array of params for the method substr, which is
   * defined in class properties.
   */
  protected function _setPhoneComponent( $strSubsetType = NULL )
  {
    switch($strSubsetType) {
      case self::PHN_AREA_CODE:
        return array(self::PHN_AREA_CODE, $this->_phn_area_code);

      case self::PHN_FIRST_THREE:
        return array(self::PHN_FIRST_THREE, $this->_phn_first_three);

      case self::PHN_LAST_FOUR:
        return array(self::PHN_LAST_FOUR, $this->_phn_last_four);

      case self::PHN_EXTENSION:
        return array(self::PHN_EXTENSION, $this->_phn_extension);
        
      default:
        return array(self::PHN_DEFAULT, $this->_phn_default);
    }
  }

  /**
   * Method to return phone in segments.
   *
   * @param array $phoneIds An optional single-dimension array of phone id's. If NULL, the
   * classes' $phoneIds property is used.
   * @return array $results A two-dimensional associative array, where
   * array( phoneId => array( 'area code' => area code of phone number,
   *                          'phone'     => body of phone number,
   *                          'extension  => extension of phone number ))
   */
  public function getPhoneComponentSegments( $phoneIds = NULL )
  {
    // always a good idea to explicitly declare this
    $results = array();

    // now we grab all of our phones.
    $phones = $this->getPhone($phoneIds);

    foreach($phones as $phoneId => $phone) {
      $numericPhone = preg_replace('/[^0-9]/', '', $phone[0]);
      $areaCode = substr($numericPhone, 0, 3);
      $phoneNum = substr($numericPhone, 3, 10);
      $extNum = substr($numericPhone, 10);
      $results[$phoneId] = array('area code' => $areaCode, 'phone' => $phoneNum, 'extension' => $extNum);
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
