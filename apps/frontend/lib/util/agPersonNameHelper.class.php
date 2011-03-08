<?php

/** 
 * Provides person name helper functions
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
 * @property array $_personIds A single-dimension array of person id values.
 */

class agPersonNameHelper
{
  protected $_personIds = array() ;

  /**
   * This is the classes' constructor and is used to set up class properties, where appropriate.
   *
   * @param array $personIds A single-dimension array of person id values.
   */
  public function __construct($personIds = NULL)
  {
    // set our person ids if passed any at construction
    if (! is_null($personIds)) { $this->setPersonIds($personIds) ; }
  }

  /**
   * Static method to quickly instantiate the agPersonNameHelper class.
   *
   * @param array $personIds A single-dimension array of person id values.
   * @return object An instantiated agPersonNameHelper object.
   */
  public static function init($personIds = NULL)
  {
    $class = new self($personIds) ;
    return $class ;
  }

  /**
   * Explicit method to set the protected _personIds class property.
   *
   * @param array $personIds A single-dimension array of person id values.
   */
  public function setPersonIds($personIds)
  {
    if (! isset($personIds) && is_array($personIds))
    {
      $this->_personIds = $personIds ;
    }
  }

  /**
   * Explicit getter to return an array of personIds. Reflects any arrays passed to it but will
   * return the class property _personIds if not passed a parameter.
   *
   * @param array $personIds A single-dimension array of person id values. Default is NULL.
   * @return * A single-dimension array of person id values.
   */
  public function getPersonIds($personIds = NULL)
  {
    if (is_null($personIds))
    {
      return $this->personIds ;
    }
    else
    {
      return $personIds ;
    }
  }

  /**
   * Method to return the primary names of all or specific persons
   *
   * @param array $personIds An array of personId's to query. If not specified, defaults to the
   * $_personIds class property.
   * @return array A two dimensional associative array, keyed by person_id,  with a secondary key
   * of person_name_type and value of person_name.
   */
  public function getPrimary($personIds = NULL)
  {
    $personIds = $this->getPersonIds($personIds) ;

    // build our query
    $query = agDoctrineQuery::create()
      ->select('pmpn.person_id')
          ->addSelect('pnt.person_name_type')
          ->addSelect('pn.person_name')
        ->from('agPersonMjAgPersonName pmpn')
          ->innerJoin('pmpn.agPersonNameType pnt')
          ->innerJoin('pmpn.agPersonName pn')
        ->where(' EXISTS (
            SELECT p.id
            FROM agPersonMjAgPersonName p
            WHERE p.person_id = pmpn.person_id
              AND p.person_name_type_id = pmpn.person_name_type_id
            HAVING MIN(p.priority) = pmpn.priority)')
          ->andWhereIn('pmpn.person_id', $personIds) ;

    // execute and return our results
    $results = $query->execute(array(), 'assoc_two_dim') ;
    return $results ;
  }
}
