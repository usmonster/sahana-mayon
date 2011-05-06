<?php
/**
 *
 * Provides bulk-phone manipulation methods
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
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
            $_globalDefaultCountry = 'default_country',
            $_phoneFormatComponentsByCountry = array(),
            $_phoneValidation = array(),
            $_phoneDisplayFormat = array(),
            $_returnFormatId,
            $_returnFormatIdsByCountry,
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
  public function __construct(array $phoneIds = NULL)
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
   * Method used to construct the base query object used by other objects in this class.
   *
   * @param string $country A string value of a country.
   * @return agDoctrineQuery An extended doctrine query object.
   */
  protected function _getPhoneFormatTypeByCountry($country)
  {
    // construct our base query object
    $q = agDoctrineQuery::create()
      ->select('pf.id')
        ->from('agPhoneFormat pf')
            ->innerJoin('pf.agPhoneFormatType pft')
            ->innerJoin('pf.agCountry c')
        ->where('c.country = ?', $country);

    return $q;
  }

  /**
   * Method to return phone contact type ids from phone contact type values.
   * @param array $phoneTypes An array of phone_contact_types
   * @return array An associative array of phone contact type ids keyed by phone contact type.
   */
  static public function getPhoneContactTypeIds(array $phoneTypes)
  {
    return agDoctrineQuery::create()
      ->select('ept.phone_contact_type')
          ->addSelect('ept.id')
        ->from('agPhoneContactType ept')
        ->whereIn('ept.phone_contact_type', $phoneTypes)
      ->useResultCache(TRUE, 3600, __FUNCTION__)
      ->execute(array(), agDoctrineQuery::HYDRATE_KEY_VALUE_PAIR);
  }

  /**
   * This method queries the database for the required formatting components by country and loads
   * it to a class property with quick, referencable information used in validating and formatting
   * endeavors.
   *
   * @param string $country A string value of a country. Defaults to the global country's phone
   * formats if non is provided.
   */
  public function _setPhoneFormatComponentByCountry($country = NULL)
  {
    if (is_null($country)) { $country = agGlobal::getParam($this->_globalDefaultCountry); }
    $q = $this->_getPhoneFormatTypeByCountry($country);
    $this->_returnFormatIdsByCountry = $q->execute(array(), agDoctrineQuery::HYDRATE_SINGLE_VALUE_ARRAY);
    $q = $this->_getPhoneFormatComponent($this->_returnFormatIdsByCountry);
    // here we choose a custom hydration method to allow us to manipulate the results data
    $formatComponents = $q->execute(array(), DOCTRINE_CORE::HYDRATE_NONE);
    foreach($formatComponents as $fc)
    {
      // create a simple phone validation and display format array.
      $this->_phoneFormatComponentsByCountry[$fc[0]] = array($fc[1], $fc[2], $fc[3]);
    }
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
    $this->_setPhoneFormatComponentByCountry();
  }

  protected function _getPhoneFormatComponent($pfid)
  {
    $q = agDoctrineQuery::create()
      ->select('pf.id')
          ->addSelect('pft.validation')
          ->addSelect('pft.match_pattern')
          ->addSelect('pft.replacement_pattern')
        ->from('agPhoneFormat pf')
            ->innerJoin('pf.agPhoneFormatType pft')
          ->whereIn('pf.id', $pfid);

    return $q;
  }

  /**
   * This method queries the database for the required formatting components and loads several
   * properties with quick, referenceable information used in formatting endeavors.
   */
  protected function _setPhoneFormatComponent()
  {
    $q = $this->_getPhoneFormatComponent($this->_returnFormatId);

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
  protected function _getPhone(array $phoneIds)
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
  public function getPhone(array $phoneIds = NULL)
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
  public function getFormattedPhone(array $phoneIds = NULL)
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
  protected function _getPhoneComponent(array $phones = NULL, $keepAsDefault, $startIndex = NULL, $length = NULL )
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
  public function getPhoneComponent(array $phoneIds = NULL, array $strSubsetType = NULL)
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
  public function getPhoneComponentSegments(array $phoneIds = NULL)
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

  public function getPhoneFormatComponents()
  {
    return $this->_phoneFormatComponentsByCountry;
  }

   /**
   * A quick helper method to take in an array phones and return an array of phone ids.
   * @param array $phones A monodimensional array of phones.
   * @return array An associative array, keyed by phone, with a value of phone_id.
   *
   * @TODO May also need to return phone format info.
   *
   */
  public function getPhoneIds(array $phones)
  {
    $q = agDoctrineQuery::create()
      ->select('e.phone_contact')
          ->addSelect('e.id')
        ->from('agPhoneContact e')
        ->whereIn('e.phone_contact', $phones);

    return $q->execute(array(), agDoctrineQuery::HYDRATE_KEY_VALUE_PAIR);
  }

  /**
   * Method to create a new phone.
   *
   * @param array $phones A multi-dimensional array of arbitrary index keys and value of an array of
   * phone and phone format id.
   * <code>
   * array(
   *  [$index] => array(
   *    [0] => $phoneString,
   *    [1] => $phoneFormatId
   *   ),
   *   ...
   * )
   * </code>
   * @param boolean $throwOnError A boolean to determine whether or not errors will trigger an
   * exception or be silently ignored (rendering an phone 'optional'). Defaults to the class
   * property of the same name.
   * @param Doctrine_Connection $conn A doctrine connection object.
   * @return array A two dimensional array. The first array element ([0]), returns an array of
   * phone indexes and the newly inserted phoneIds. The second array element [1], returns all
   * phone indexes that could not be inserted.
   * <code>
   * array(
   *  [0] => array( [$phoneIndex] => [$phoneId], ... )
   *  [1] => array( $phoneIndex, ... )
   * )
   * </code>
   */
  protected function setNewPhones(array $phones, $throwOnError, $conn)
  {
    // declare our results array
    $results = array();

    // pick up the default connection and error throw prerogative if one is not passed
    if (is_null($conn)) { $conn = Doctrine_Manager::connection(); }
    if (is_null($throwOnError)) { $throwOnError = $this->throwOnError; }

    $phoneFormatComponents = $this->_phoneFormatComponentsByCountry;

    foreach ($phones as $index => $phone)
    {
      // we do this so we only have to call rollback / unset once, plus it's nice to have a bool to
      // check on our own
      $err = NULL;
      $errMsg = 'This is a generic error message for setNewPhones. You should never receive this
        error. If you are recieving this error, there is an ERROR in your error-handling code.';

      // here we check our current transaction scope and create a transaction or savepoint
      $useSavepoint = ($conn->getTransactionLevel() > 0) ? TRUE : FALSE;
      if ($useSavepoint)
      {
        $conn->beginTransaction(__FUNCTION__);
      }
      else
      {
        $conn->beginTransaction();
      }

      // Check if phone format id is passed in.  If not, assign a phone format id where matches
      // the validation regexg.  If no match, assign default phone format id.
      if (isset($phone[1]))
      {
        $phoneFormatId = $phone[1];
      }
      else
      {
        $phoneFormatId = $this->_returnFormatId;

        foreach($phoneFormatComponents as $pfId => $pfComp)
        {
          if (preg_match($pfComp[0], $phone[0]))
          {
            $phoneFormatId = $pfId;
            break;
          }
        }
      }

      $newPhone = new agPhoneContact();
      $newPhone['phone_contact'] = $phone[0];
      $newPhone['phone_format_id'] = $phoneFormatId;

      try
      {
        $newPhone->save($conn);
        $phoneId = $newPhone->getId();
        if ($useSavepoint) { $conn->commit(__FUNCTION__) ; } else { $conn->commit(); }
      }
      catch (Exception $e)
      {
        // log our error
        $errMsg = sprintf('Couldn\'t insert phone contact %s!  Rolled back changes!', $phone[0]);

        // log our error
        sfContext::getInstance()->getLogger()->err($errMsg);

        // rollback
        if ($useSavepoint) { $conn->rollback(__FUNCTION__); } else { $conn->rollback(); }

        // ALWAYS throw an error, it's like stepping on a crack if you don't
        if ($throwOnError) { throw $e; }
        continue;
      }

      // commit our results to our final results array
      $results[$index] = $phoneId;

      // release the value on our input array
      unset($phones[$index]);
    }
    return array($results, array_keys($phones));
  }

  /**
   * Method to set phones and return phone ids, inserting new phones as necessary.
   *
   * @param array $phones A multi-dimensional array of arbitrary index keys and value of an array of
   * phone  and phone format id.
   * <code>
   * array(
   *  [$index] => array(
   *    [0] => $phoneString,
   *    [1] => $phoneFormatId
   *   ),
   *   ...
   * )
   * </code>
   * @param boolean $throwOnError A boolean to determine whether or not errors will trigger an
   * exception or be silently ignored (rendering an phone 'optional'). Defaults to the class
   * property of the same name.
   * @param Doctrine_Connection $conn A doctrine connection object.
   * @return array A two dimensional array. The first array element ([0]), returns an array of
   * phone indexes and the newly inserted phoneIds. The second array element [1], returns all
   * phone indexes that could not be inserted.
   * <code>
   * array(
   *  [0] => array( [$phoneIndex] => [$phoneId], ... )
   *  [1] => array( $phoneIndex, ... )
   * )
   * </code>
   */
  protected function _setPhones(array $phones, $throwOnError = NULL, Doctrine_Connection $conn = NULL)
  {
    // declare our results array
    $results = array();

    // Extract only the phone numbers into a simple array for later use to retrieve phone contact
    // ids if a match found in db.
    $phonesOnly = array();
    foreach ($phones as $p)
    {
      $phonesOnly[] = $p[0];
    }

    // return any found phones
    $dbPhones = $this->getPhoneIds(array_unique(array_values($phonesOnly)));

    // loop through the pass-in phones and build a couple of arrays
    foreach ($phones as $phoneIndex => $phone)
    {
      // Check if phone already exists in db.
      if (array_key_exists($phone[0], $dbPhones))
      {
        // for each of the phones with that ID, build our results set and
        // unset the value from the stuff left to be processed (we're going to use that later!)
        $results[$phoneIndex] = $dbPhones[$phone[0]];
        unset($phones[$phoneIndex]);
      }
    }

    // just 'cause this is going to be a very memory-hungry method, we'll unset the dbPhones too
    unset($dbPhones);

    // pick up the default connection if one is not passed
    if (is_null($conn)) { $conn = Doctrine_Manager::connection(); }

    // now that we have all of the 'existing' phones, let's build the new ones
    $newPhones = $this->setNewPhones($phones, $throwOnError, $conn);
    $successes = array_shift($newPhones);

    // we don't need this anymore!
    unset($newPhones);

    foreach ($successes as $index => $phoneId)
    {
      // add our successes to the final results set
      $results[$index] = $phoneId;

      // release the phone from our initial input array
      unset($phones[$index]);

      // release it from the successes array while we're at it
      unset($successes[$index]);
    }

    // and finally we return our results, both the successes and the failures
    return array($results, array_keys($phones));
  }

  /**
   * Method to call other method for phone setters to insert new phones as necessary
   * and return phone ids.
   *
   * @param array $phones A multi-dimensional array of arbitrary index keys and value of an array of
   * phone  and phone format id.
   * <code>
   * array(
   *  [$index] => array(
   *    [0] => $phoneString,
   *    [1] => $phoneFormatId
   *   ),
   *   ...
   * )
   * </code>
   * @param boolean $throwOnError A boolean to determine whether or not errors will trigger an
   * exception or be silently ignored (rendering a phone 'optional'). Defaults to the class
   * property of the same name.
   * @param Doctrine_Connection $conn A doctrine connection object.
   * @return array A two dimensional array. The first array element ([0]), returns an array of
   * phone indexes and the newly inserted phoneIds. The second array element [1], returns all
   * phone indexes that could not be inserted.
   * <code>
   * array(
   *  [0] => array( [$phoneIndex] => [$phoneId], ... )
   *  [1] => array( $phoneIndex, ... )
   * )
   * </code>
   */
  public function setPhones(array $phones, $throwOnError = NULL, $conn = NULL)
  {
    // either way, we eventually pass the 'cleared' phones to our setter
    $results = $this->_setPhones($phones, $throwOnError, $conn);

    return $results;
  }

}
