<?php

/** 
 * Provides person name helper functions and inherits several methods and properties from the
 * bulk record helper.
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

class agPersonNameHelper extends agBulkRecordHelper
{
  const     NAME_BY_ID = 'getNameById',
            NAME_BY_TYPE = 'getNameByType',
            NAME_BY_TYPE_AS_STRING = 'getNameByTypeAsString',
            PRIMARY_INITIALS = 'getPrimaryNameAsInitials',
            PRIMARY_AS_STRING = 'getPrimaryNameAsString',
            PRIMARY_BY_ID = 'getPrimaryNameById',
            PRIMARY_BY_TYPE = 'getPrimaryNameByType';

  public    $defaultNameComponents = array(),
            $invertLastComponent = FALSE,
            $delimiters = array('invert' => ',', 'component' => ' ', 'initial' => '.');

  protected $_globalDefaultNameComponents = 'default_name_components' ;

  /**
   * This is the classes' constructor and is used to set up class properties, where appropriate.
   *
   * @param array $personIds A single-dimension array of person id values.
   */
  public function __construct($personIds = NULL)
  {
    // set our person ids if passed any at construction
    parent::__construct($personIds) ;

    // set the default name components
    $this->setDefaultNameComponents() ;
  }

  /**
   * Helper method to retrieve and set the defaultNameComponents class property.
   */
  public function setDefaultNameComponents($strNameComponents = NULL)
  {
    // always good to define this first
    $results = array() ;

    // fetch and decode our global param
    if (is_null($strNameComponents))
    {
      $strNameComponents = json_decode(agGlobal::getParam($this->_globalDefaultNameComponents)) ;
    }
 
    // grab all of the name types into an array (once) for quick-reference later on
    $nameTypes = agDoctrineQuery::create()
      ->select('pnt.person_name_type')
          ->addSelect('pnt.id')
        ->from('agPersonNameType pnt')
        ->execute(array(), 'key_value_pair') ;

    // quickly dash in and build our results array, replacing the string name type with the id
    foreach ($strNameComponents as $component)
    {
      $results[] = array($nameTypes[$component[0]], $component[1]) ;
    }

    $this->defaultNameComponents = $results ;
  }

  /**
   * Method to construct and return a basic name query object.
   *
   * @param array $personIds A single-dimension array of person id values. Default is NULL.
   * @param boolean $primary A boolean that controls whether or not the query constructor will
   * build a query that only returns primary names or all names.
   * @return Doctrine_Query An instantiated doctrine query object.
   */
  protected function _getNameComponents($personIds = NULL, $primary = TRUE)
  {
    $personIds = $this->getRecordIds($personIds) ;
    
    $q = agDoctrineQuery::create()
      ->select('pmpn.person_id')
          ->addSelect('pmpn.person_name_type_id')
          ->addSelect('pn.person_name')
        ->from('agPersonMjAgPersonName pmpn')
          ->innerJoin('pmpn.agPersonName pn')
        ->whereIn('pmpn.person_id', $personIds)
        ->orderBy('pmpn.priority ASC') ;

    if ($primary)
    {
      $q->andwhere(' EXISTS (
        SELECT p.id
        FROM agPersonMjAgPersonName p
        WHERE p.person_id = pmpn.person_id
          AND p.person_name_type_id = pmpn.person_name_type_id
        HAVING MIN(p.priority) = pmpn.priority)') ;
    }

    return $q ;
  }

  /**
   * Method to return primary person names in an array keyed by the person_name_type_id.
   *
   * @param array $personIds A single-dimension array of person id values. Default is NULL.
   * @return array A two-dimensional associative array keyed by person id and name_type_id.
   */
  public function getPrimaryNameById($personIds = NULL)
  {
    // build our components query
    $q = $this->_getNameComponents($personIds, TRUE) ;

    // execute and return our results
    return $q->execute(array(), 'assoc_two_dim') ;
  }

  /**
   * Method to return person names in an array keyed by the person_name_type_id.
   *
   * @param array $personIds A single-dimension array of person id values. Default is NULL.
   * @param boolean $primary A boolean that controls whether or not the query constructor will
   * build a query that only returns primary names or all names.
   * @return array A three-dimensional associative array keyed by person id and name_type_id.
   */
  public function getNameById($personIds = NULL, $primary = FALSE)
  {
    // build our components query
    $q = $this->_getNameComponents($personIds, $primary) ;

    // execute and return our results
    return $q->execute(array(), 'assoc_three_dim') ;
  }

  /**
   * Method to return person names, ordered by most important in an array keyed by the
   * person_id => person_name_type (string) => array(person_names).
   *
   * @param array $personIds A single-dimension array of person id values. Default is NULL.
   * @param boolean $primary A boolean that controls whether or not the query constructor will
   * build a query that only returns primary names or all names.
   * @return array A three-dimensional associative array keyed by person id and person_name_type.
   */
  public function getNameByType($personIds = NULL, $primary = FALSE)
  {
    // always good to declare results first
    $results = array() ;

    // build our components query
    $q = $this->_getNameComponents($personIds, $primary) ;
    $q->addSelect('pnt.person_name_type')
      ->innerJoin('pmpn.agPersonNameType pnt') ;


    // execute, keeping in mind that we have to use custom hydration because of the new components
    $rows = $q->execute(array(), Doctrine_Core::HYDRATE_NONE) ;

    // otherwise iterate and place our results into a positional array
    foreach ($rows as $row)
    {
      $results[$row[0]][$row[3]][] = $row[2] ;
    }

    return $results ;
  }

  /**
   * Method to return primary person names in an array keyed by the person_name_type (string).
   *
   * @param array $personIds A single-dimension array of person id values. Default is NULL.
   * @return array A two-dimensional associative array keyed by person id and person_name_type.
   */
  public function getPrimaryNameByType($personIds = NULL)
  {
    // Get our names by type
    $personNames = $this->getNameByType($personIds, TRUE) ;

    // loop our nested array and pick up the name array as a single value
    foreach ($personNames as $person => $types)
    {
      foreach ($types as $type => $names)
      {
        $personNames[$person][$type] = $names[0] ;
      }
    }

    return $personNames ;
  }

  public function getNameByTypeAsString ($personIds = NULL)
  {
    $personNames = $this->getNameByType($personIds) ;
    
    // loop our nested array and pick up the name array as an imploded string
    foreach ($personNames as $person => $types)
    {
      foreach ($types as $type => $names)
      {
        $personNames[$person][$type] = implode(agGlobal::getParam('lucene_delimiter'), $names) ;
      }
    }

    return $personNames ;
  }

  /**
   * Method to return primary names as a single string.
   *
   * @param array $personIds A single-dimension array of person id values. Default is NULL.
   * @param boolean $invertLast Optional component to override the class property
   * $invertLastComponent.
   * @param array $delimiters An optional associative array that can override the defaults set in
   * the $delimiters class property.
   * @return array A single-dimensional associative array keyed by person id with a value as name
   * string.
   */
  public function getPrimaryNameAsString($personIds = NULL, $invertLast = NULL, $delimiters = NULL)
  {
    // define our results set
    $results = array() ;

    // pick up our class defaults
    if (is_null($invertLast)) { $invertLast = $this->invertLastComponent ; }
    if (is_null($delimiters)) { $delimiters = $this->delimiters ; }

    // grab our name formatting components
    $defaultNameComponents = $this->defaultNameComponents ;

    // re-order the formatting components if we're inverting the last to be first
    if ($invertLast)
    {
      $lastComponent = array_pop($defaultNameComponents) ;
      array_unshift($defaultNameComponents, $lastComponent) ;
    }

    // get all of our primary name components by ID and loop
    $personNameComonents = $this->getPrimaryNameById($personIds) ;
    foreach ($personNameComonents as $personId => $nameComponents)
    {
      $strName = '' ;
      $i = 0 ;

      // now we loop our default components
      foreach ($defaultNameComponents as $component)
      {
        // check to make sure this component exists in the parent
        if (array_key_exists($component[0], $nameComponents))
        {
          // assign it to a variable so we can manipulate without constantly referencing it ;
          $namePart = $nameComponents[$component[0]] ;

          // don't bother doing anything if it's null or zero-length, otherwise append
          if (! is_null($namePart) && $namePart != '')
          {
            $namePart = trim($namePart) ;

            // if the component calls for an initial, only, we extract that here
            if ($component[1]) { $namePart = substr($namePart, 0, 1) . $delimiters['initial'] ; }

            $strName = $strName . $namePart ;

            // add an inverted delimiter on the first component
            if ($invertLast && $i == 0) { $strName = $strName . $delimiters['invert'] ; }

            // and always add our final delimiter (usually a space)
            $strName = $strName . $delimiters['component'] ;

            $i++ ;
          }
        }
      }

      // append to our results array
      $results[$personId] = rtrim($strName) ;
    }

    return $results ;
  }

  /**
   * Method to return primary names as an initials string.
   *
   * @param array $personIds A single-dimension array of person id values. Default is NULL.
   * @return array A single-dimensional associative array keyed by person id with a value as name
   * string.
   */
  public function getPrimaryNameAsInitials($personIds = NULL)
  {
    // define a specific set of delimiters used just for initials
    $initialDelimeters = array('invert' => '', 'component' => '', 'initial' => '');

    // grab our originals (so we can return them!)
    $origNameComponents = $this->defaultNameComponents ;

    // create our new component array
    $newComponents = array() ;
    foreach ($this->defaultNameComponents as $component)
    {
      $newComponents[] = array($component[0], TRUE) ;
    }

    // override the old component array
    $this->defaultNameComponents = $newComponents ;

    // execute and return the name as a string
    $results = $this->getPrimaryNameAsString($personIds, FALSE, $initialDelimeters) ;

    // return the original components
    $this->defaultNameComponents = $origNameComponents ;

    return $results ;
  }
}
