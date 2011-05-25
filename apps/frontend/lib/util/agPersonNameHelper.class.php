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
            NAME_BY_TYPE_ID = 'getNameByTypeId',
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
  public function __construct(array $personIds = NULL)
  {
    // set our person ids if passed any at construction
    parent::__construct($personIds) ;

    // set the default name components
    $this->setDefaultNameComponents() ;
  }

  /**
   * Helper method to retrieve and set the defaultNameComponents class property.
   */
  public function setDefaultNameComponents(array $strNameComponents = NULL)
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
        ->execute(array(), agDoctrineQuery::HYDRATE_KEY_VALUE_PAIR) ;

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
  protected function _getNameComponents(array $personIds = NULL, $primary = TRUE)
  {
    $personIds = $this->getRecordIds($personIds) ;

    //TODO: if empty, skip all this overhead and just return some default query

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
  public function getPrimaryNameById(array $personIds = NULL)
  {
    // build our components query
    $q = $this->_getNameComponents($personIds, TRUE) ;

    // execute and return our results
    return $q->execute(array(), agDoctrineQuery::HYDRATE_ASSOC_TWO_DIM) ;
  }

  /**
   * Method to return person names in an array keyed by the person_name_type_id.
   *
   * @param array $personIds A single-dimension array of person id values. Default is NULL.
   * @param boolean $primary A boolean that controls whether or not the query constructor will
   * build a query that only returns primary names or all names.
   * @return array A three-dimensional associative array keyed by person id and name_type_id.
   */
  public function getNameById(array $personIds = NULL, $primary = FALSE)
  {
    // build our components query
    $q = $this->_getNameComponents($personIds, $primary) ;

    // execute and return our results
    return $q->execute(array(), agDoctrineQuery::HYDRATE_ASSOC_THREE_DIM) ;
  }

  /**
   * Method to return person nameIds in an array keyed by the person_name_type_id.
   *
   * @param array $personIds A single-dimension array of person id values. Default is NULL.
   * @param boolean $primary A boolean that controls whether or not the query constructor will
   * build a query that only returns primary names or all names.
   * @return array A three-dimensional associative array keyed by person id and name_type_id.
   */
  public function getNameByTypeId(array $personIds = NULL, $primary = FALSE)
  {
    // always good to declare results first
    $results = array() ;

    // build our components query
    $q = $this->_getNameComponents($personIds, $primary) ;
    $q->addSelect('pn.id') ;

    // execute, keeping in mind that we have to use custom hydration because of the new components
    $rows = $q->execute(array(), Doctrine_Core::HYDRATE_NONE) ;

    // otherwise iterate and place our results into a positional array
    foreach ($rows as $row)
    {
      $results[$row[0]][$row[1]][] = $row[3] ;
    }

    return $results ;
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
  public function getNameByType(array $personIds = NULL, $primary = FALSE)
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
  public function getPrimaryNameByType(array $personIds = NULL)
  {

    // sets $personIds to $this->recordIds if $personIds is NULL or not specified
    if(!isset($personIds)) {
      $personIds = $this->recordIds;
    }

    if(empty($personIds)) {
      return array();
    }

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

    return $personNames;
  }

  public function getNameByTypeAsString (array $personIds = NULL)
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
  public function getPrimaryNameAsString(array $personIds = NULL, $invertLast = NULL, array $delimiters = NULL)
  {
    // define our results set
    $results = array() ;
    
    $personIds = $this->getRecordIds($personIds);

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
  public function getPrimaryNameAsInitials(array $personIds = NULL)
  {
    $personIds = $this->getRecordIds($personIds);
    
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

  /**
   * Method to return name ids from a collection of name values.
   * @param array $nameValues A single dimension array of name values.
   * @return array An associative array of nameIds keyed by name value
   */
  public function getNameIds(array $nameValues)
  {
    // ONLY return the ID, not the value because of casing (for comparison)
    $q = agDoctrineQuery::create()
      ->select('pn.id')
        ->from('agPersonName pn')
      ->useResultCache(TRUE, 1800);

    $results = array();
    $cacheDriver = Doctrine_Manager::getInstance()->getAttribute(Doctrine_Core::ATTR_RESULT_CACHE);
    foreach ($nameValues as $index => $name)
    {
      $q->where('pn.person_name = ?',$name);

      $result = $q->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);

      // clear the cache if we had no result
      if (empty($result))
      {
        $cacheDriver->delete($q->getResultCacheHash());
      }
      else
      {
        $results[$name] = $result;
      }
      unset($nameValues[$index]);
    }

    return $results;
  }

  /**
   * Method to return name type ids from name type values.
   * @param array $nameTypes An array of person_name_types
   * @return array An associative array of person name type ids keyed by person name type.
   */
  public function getNameTypeIds(array $nameTypes)
  {
    $q = agDoctrineQuery::create()
      ->select('pnt.id')
        ->from('agPersonNameType pnt')
        ->useResultCache(TRUE, 3600);
    
    $results = array();
    foreach ($nameTypes as $nameType)
    {
      $typeId = $q->where('pnt.person_name_type', $nameType)
        ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
      
      if (!empty($typeId)) { $results[$nameType] = $typeId; }
    }
    return $results;
  }

  /**
   * Method to set person names and automatically prioritize them accordingly. This method also
   * creates new names if names do not yet exist.
   *
   * @param array $personNames A three-dimesional array of person names similar to the output of
   * getNamesById
   * <code> array( $personId =>
   *   array( $nameTypeId =>
   *     array($firstPriorityName, $secondPriorityName, ...),
   *   ... ),
   * ... )
   * @param boolean $keepHistory Boolean to control whether or not current names will be retained
   * or whether all names will be replaced. Defaults to class property.
   * @param boolean $throwOnError Boolean to control whether or not failures will throw
   * an exception. Defaults to the class property.
   * @param Doctrine_Connection $conn An optional Doctrine Connection object.
   * @return array An associative array with counts of the operations performed and/or personId's
   * for which no operations could be performed.
   */
  public function setPersonNames(array $personNames,
                                  $keepHistory = NULL,
                                  $throwOnError = NULL,
                                  Doctrine_Connection $conn = NULL)
  {
    // explicit declarations are nice
    $results = array() ;
    $uniqNames = array() ;
    $err = NULL ;

    // pick up some defaults if not passed anything
    if (is_null($conn)) { $conn = Doctrine_Manager::connection() ; }

    // reduce our person names to just the uniques
    foreach ($personNames as $personId => $nameTypes)
    {
      foreach ($nameTypes as $nameTypeId => $names)
      {
        foreach ($names as $priority => $name)
        {
          // we do this at this early stage so we're always dealing with a post-trimmed value
          $name = trim($name);

          // add it to our unique contacts array
          $uniqNames[] = $name ;

          // either way we'll have to point the entities back to their addresses
          $personNames[$personId][$nameTypeId][$priority] = $name ;
        }
      }
    }

    // actually make these names unique
    $uniqNames = array_unique($uniqNames) ;

    // this speeds up comparisons later
    sort($uniqNames) ;

    // get existing names
    $nameIds = $this->getNameIds($uniqNames) ;
    
    // let's just pull out the values so we can compare against what's left to create
    $foundNames = array_keys($nameIds) ;

    // again, sorting before diffing makes the diff faster
    sort($foundNames) ;

    // diff, then release the two component arrays -- all we care about is the diff
    $newNames = array_diff($uniqNames, $foundNames) ;
    unset($uniqNames) ;
    unset($foundNames) ;

    // here we check our current transaction scope and create a transaction or savepoint
    $useSavepoint = ($conn->getTransactionLevel() > 0) ? TRUE : FALSE ;
    if ($useSavepoint)
    {
      $conn->beginTransaction(__FUNCTION__) ;
    }
    else
    {
      $conn->beginTransaction() ;
    }

    try
    {
      // set new names / return their ids
      $nameIds = ($nameIds + ($this->setNewPersonNames($newNames, $throwOnError, $conn))) ;
    }
    catch(Exception $e)
    {
      // log our error
      $errMsg = sprintf('%s failed to execute. (%s).', __FUNCTION__, $e->getMessage()) ;

      // capture the error for comparison later
      $err = $e ;
    }

    // set names + types
    if (is_null($err))
    {
      // release a little resource
      unset($newNames) ;

      // recombine all names and their person owners
      foreach ($personNames as $personId => $nameTypes)
      {
        foreach ($nameTypes as $nameTypeId => $names)
        {
          foreach ($names as $priority => $name)
          {
            $personNames[$personId][$nameTypeId][$priority] = $nameIds[$name] ;
          }
        }
      }

      try
      {
        // connect our new names to their owners
        $results = $this->_setPersonNames($personNames, $keepHistory, $throwOnError, $conn) ;
      }
      catch(Exception $e)
      {
        // log our error
        $errMsg = sprintf('%s failed to execute. (%s).', '_setPersonNames', $e->getMessage()) ;

        // capture the exception for later
        $err = $e ;
      }
    }

    if (is_null($err))
    {
      // yay, no problems, now we commit
      if ($useSavepoint) { $conn->commit(__FUNCTION__) ; } else { $conn->commit() ; }
    }
    else
    {
      // log whichever error we received
      sfContext::getInstance()->getLogger()->err($errMsg) ;

      // rollback
      if ($useSavepoint) { $conn->rollback(__FUNCTION__) ; } else { $conn->rollback() ; }

      // ALWAYS throw an error, it's like stepping on a crack if you don't
      if ($throwOnError) { throw $e ; }
    }

    return $results ;
  }

  /**
   * Protected method that creates new person name entries in the many-to-many person name table.
   *
   * @param array $personNames A three-dimesional array of person names similar to the output of
   * getNamesByTypeId
   * <code> array( $personId =>
   *   array( $nameTypeId =>
   *     array($firstPriorityNameId, $secondPriorityNameId, ...),
   *   ... ),
   * ... )
   * @param boolean $keepHistory Boolean to control whether or not current names will be retained
   * or whether all names will be replaced. Defaults to class property.
   * @param boolean $throwOnError Boolean to control whether or not failures will throw
   * an exception. Defaults to the class property.
   * @param Doctrine_Connection $conn An optional Doctrine Connection object.
   * @return array An associative array with counts of the operations performed and/or personId's
   * for which no operations could be performed.
   */
  protected function _setPersonNames (array $personNames,
                                      $keepHistory = NULL,
                                      $throwOnError = NULL,
                                      Doctrine_Connection $conn = NULL)
  {
    // explicit results declaration
    $results = array('upserted'=>0, 'removed'=>0, 'failures'=>array()) ;
    $currNames = array() ;

    // get some defaults if not explicitly passed
    if (is_null($keepHistory)) { $keepHistory = $this->keepHistory ; }
    if (is_null($throwOnError)) { $throwOnError = $this->throwOnError ; }
    if (is_null($conn)) { $conn = Doctrine_Manager::connection() ; }

    if ($keepHistory)
    {
      // if we're going to process existing names and keep them, then hold on
      $currNames = $this->getNameByTypeId(array_keys($personNames), FALSE) ;
    }

    // execute the reprioritization helper and pass it our current addresses as found in the db
    $personNames = $this->reprioritizePersonNames($personNames, $currNames ) ;

    // define our blank collection
    $coll = new Doctrine_Collection('agPersonMjAgPersonName') ;

    // loop through our persons and build our collection
    foreach($personNames as $personId => $nameTypes)
    {
      foreach($nameTypes as $nameTypeId => $names)
      {
        foreach($names as $index => $nameId)
        {
          // create a doctrine record with this info
          $newRec = new agPersonMjAgPersonName() ;
          $newRec['person_id'] = $personId ;
          $newRec['person_name_id'] = $nameId ;
          $newRec['person_name_type_id'] = $nameTypeId ;
          $newRec['priority'] = ($index + 1) ;

          // add the record to our collection
          $coll->add($newRec) ;
        }
        
        // release a few resources
        unset($personNames[$personId][$nameTypeId]) ;
      }
    }

   // here we check our current transaction scope and create a transaction or savepoint
    $useSavepoint = ($conn->getTransactionLevel() > 0) ? TRUE : FALSE ;
    if ($useSavepoint)
    {
      $conn->beginTransaction(__FUNCTION__) ;
    }
    else
    {
      $conn->beginTransaction() ;
    }

    try
    {
      // if we're not keeping our history, just blow them all out!
      if (! $keepHistory)
      {
        $results['removed'] = $this->purgePersonNames(array_keys($personNames), NULL, $throwOnError,
          $conn) ;
      }

      // execute our commit and, while we're at it, add our successes to the bin
      $coll->replace($conn) ;

      // commit, being sensitive to our nesting
      if ($useSavepoint) { $conn->commit(__FUNCTION__) ; } else { $conn->commit() ; }

      // append to our results array
      $results['upserted'] = $results['upserted'] + count($coll) ;
    }
    catch(Exception $e)
    {
      // log our error
      $errMsg = sprintf('%s failed at: %s', __FUNCTION__, $e->getMessage()) ;
      sfContext::getInstance()->getLogger()->err($errMsg) ;

      // rollback
      if ($useSavepoint) { $conn->rollback(__FUNCTION__) ; } else { $conn->rollback() ; }

      // ALWAYS throw an error, it's like stepping on a crack if you don't
      if ($throwOnError) { throw $e ; }

      $results['failures'] = array_keys($personNames) ;
    }

    return $results ;
  }

  /**
   * Method to remove person name entries from a person. If $purgeOrphans is passed as TRUE, also
   * executes an orphan name purge at the close of this method. Note: This purges ALL orphan names,
   * not just ones associated with the $personIds passed as parameters.
   *
   * @param array $personIds A single-dimension array of personIds
   * @param boolean $purgeOrphans Boolean field to determine whether or not orphan person names will
   * be also removed as part of this operation. Note: this removes ALL orphan person names, not just
   * ones formerly associated with the $personIds passed to this method. Defaults to the class
   * property value.
   * @param boolean $throwOnError Boolean to control whether or not any errors will trigger an
   * exception throw. Defaults to the class property value.
   * @param Doctrine_Connection $conn An optional doctrine connection object.
   * @todo Check our isolation level and how it affects the orphan purge
   */
  protected function purgePersonNames(array $personIds,
                                      $purgeOrphans = NULL,
                                      $throwOnError = NULL,
                                      Doctrine_Connection $conn = NULL)
  {
    // explicit declarations are good!
    $results = 0 ;
    $err = NULL ;

    // pick up some defaults if not passed anything
    if (is_null($throwOnError)) { $throwOnError = $this->throwOnError ;}
    if (is_null($purgeOrphans)) { $purgeOrphans = $this->purgeOrphans ;}
    if (is_null($conn)) { $conn = Doctrine_Manager::connection() ; }

    // build our query object
    $q = agDoctrineQuery::create()
      ->delete('agPersonMjAgPersonName')
        ->whereIn('person_id', $personIds) ;

    // here we check our current transaction scope and create a transaction or savepoint
    $useSavepoint = ($conn->getTransactionLevel() > 0) ? TRUE : FALSE ;
    if ($useSavepoint)
    {
      $conn->beginTransaction(__FUNCTION__) ;
    }
    else
    {
      $conn->beginTransaction() ;
    }

    try
    {
      // execute our person name mapping join purge
      $results = $q->execute() ;

      // if orphan control has been enforce, we'll execute this too
      if ($purgeOrphans)
      {
        $results = $results + ($this->purgeOrhpanPersonNames($throwOnError, $conn)) ;
      }

      // most excellent! no errors at all, so we commit... finally!
      if ($useSavepoint) { $conn->commit(__FUNCTION__) ; } else { $conn->commit() ; }
    }
    catch(Exception $e)
    {
      $errMsg = sprintf('Failed to purge person names for persons %s. Rolled back changes!',
        json_encode($personIds)) ;

      // log our error
      sfContext::getInstance()->getLogger()->err($errMsg) ;

      // rollback
      if ($useSavepoint) { $conn->rollback(__FUNCTION__) ; } else { $conn->rollback() ; }

      // throw an error
      if ($throwOnError) { throw $e ; }
    }

    return $results ;
  }

  /**
   * Method to purge all orphaned person names in the database.
   *
   * @param boolean $throwOnError A boolean to control whether or not failed transactions produce
   * an exception.
   * @param Doctrine_Connection $conn An optional Doctrine Connection object.
   * @return integer The count of operations performed.
   */
  public function purgeOrhpanPersonNames( $throwOnError = NULL,
                                          Doctrine_Connection $conn = NULL)
  {
    // explicit delcarations are nice
    $results = 0 ;

    // pick up our default connection / transaction objects if not passed anything
    if (is_null($throwOnError)) { $throwOnError = $this->throwOnError ; }
    if (is_null($conn)) { $conn = Doctrine_Manager::connection() ; }

//    @todo Make this 'correct' implementation work somehow instead of the one below
//    $q = agDoctrineQuery::create($conn)
//      ->delete('agPersonName')
//        ->where('NOT EXISTS (
//          SELECT ppn.id
//            FROM agPersonMjAgPersonName ppn
//            WHERE (ppn.person_name_id = agPersonName.id))') ;

    //construct our delete query
    $q = agDoctrineQuery::create($conn)
      ->delete('agPersonName')
        ->where('id NOT IN (SELECT DISTINCT ppn.person_name_id FROM agPersonMjAgPersonName ppn)') ;

    // here we check our current transaction scope and create a transaction or savepoint
    $useSavepoint = ($conn->getTransactionLevel() > 0) ? TRUE : FALSE ;
    if ($useSavepoint)
    {
      $conn->beginTransaction(__FUNCTION__) ;
    }
    else
    {
      $conn->beginTransaction() ;
    }

    try
    {
      $results = $q->execute() ;

      // if that went well, we'll commit
      if ($useSavepoint) { $conn->commit(__FUNCTION__) ; } else { $conn->commit() ; }
    }
    catch(Exception $e)
    {
      // log our error
      $errMsg = sprintf('%s failed to execute. (%s).', __FUNCTION__, $e->getMessage()) ;
      sfContext::getInstance()->getLogger()->err($errMsg) ;

      // rollback
      if ($useSavepoint) { $conn->rollback(__FUNCTION__) ; } else { $conn->rollback() ; }

      // ALWAYS throw an error, it's like stepping on a crack if you don't
      if ($throwOnError) { throw $e ; } 
    }

    return $results ;
  }

  /**
   * Method to prioritize person names.
   *
   * @param array $newNames A three-dimesional array of person names similar to the output of
   * getNamesByTypeId
   * <code> array( $personId =>
   *   array( $nameTypeId =>
   *     array($firstPriorityNameId, $secondPriorityNameId, ...),
   *   ... ),
   * ... )
   * @param array $currNames A three-dimesional array of person names similar to the output of
   * getNamesByTypeId
   * <code> array( $personId =>
   *   array( $nameTypeId =>
   *     array($firstPriorityNameId, $secondPriorityNameId, ...),
   *   ... ),
   * ... )
   * @return array A three-dimensional array of person names similar to the output of
   * getNamesByTypeId
   * <code> array( $personId =>
   *   array( $nameTypeId =>
   *     array($firstPriorityNameId, $secondPriorityNameId, ...),
   *   ... ),
   * ... )
   */
  protected function reprioritizePersonNames(array $newNames, array $currNames)
  {
    // loop through and do an inner array diff
    foreach($newNames as $personId => $nameTypes)
    {
      foreach($nameTypes as $nameTypeId => $names)
      {
        //explicit declarations are good
        $newName = array() ;
        $currName = array() ;

        // check to see if this is brand-spankin' new person or old
        if (array_key_exists($personId, $currNames)
          && array_key_exists($nameTypeId, $currNames[$personId]))
        {
          // we'll reuse this at the end so lets grab it once
          $currName = $currNames[$personId][$nameTypeId] ;

          // here we do a little something crazy; we add ALL of the old name info to the array
          foreach ($currName as $name)
          {
            $names[] = $name ;
          }
        }

        // now that we've got allname info on our $names array, let's reshape and exclude dupes
        // we intentionally don't use array_unique() here because types might differ and it's strict
        foreach ($names as $name)
        {
          if (! in_array($name, $newName))
          {
            $newName[] = $name ;
          }
        }

        // now that we're out of the contact de-dupe loop we exclude the ones that haven't changed
        foreach($newName as $nnKey => $nnValue)
        {
          if (array_key_exists($nnKey, $currName) && $nnValue == $currName[$nnKey])
          {
            unset($newName[$nnKey]) ;
          }
        }

        // add our results to our final results array
        $newNames[$personId][$nameTypeId] = $newName ;
      }

      // might as well re-claim our memory here too
      unset($currNames[$personId]) ;
    }

    return $newNames ;
  }

  /**
   * Method to create new person names.
   *
   * @param array $newNames A single dimension array of person name strings.
   * @param boolean $throwOnError Boolean to control whether or not failures trigger exceptions.
   * Defaults to class property.
   * @param Doctrine_Connection $conn An optional doctrine connection object.
   * @return array An associative array keyed by the name string with the nameId as a value.
   */
  protected function setNewPersonNames(array $newNames,
                                    $throwOnError = NULL,
                                    Doctrine_Connection $conn = NULL)
  {
    // explicit delcarations are nice
    $results = array() ;
    $err = NULL ;

    // pick up our default connection / transaction objects if not passed anything
    if (is_null($throwOnError)) { $throwOnError = $this->throwOnError ; }
    if (is_null($conn)) { $conn = Doctrine_Manager::connection() ; }

    // here we check our current transaction scope and create a transaction or savepoint
    $useSavepoint = ($conn->getTransactionLevel() > 0) ? TRUE : FALSE ;
    if ($useSavepoint)
    {
      $conn->beginTransaction(__FUNCTION__) ;
    }
    else
    {
      $conn->beginTransaction() ;
    }

    foreach ($newNames as $name)
    {
      $newRec = new agPersonName() ;
      $newRec['person_name'] = $name ;

      try
      {
        $newRec->save($conn) ;
        $results[$name] = $newRec->getId() ;
      }
      catch(Exception $e)
      {
        // log our error
        $errMsg = sprintf('Couldn\'t insert name value %s. Rolled back changes!', $name) ;

        // capture our exception for a later throw and break out of this loop
        $err = $e ;
        break ;
      }
    }

    if (is_null($err))
    {
      // yay, it all went well so let's commit!
      if ($useSavepoint) { $conn->commit(__FUNCTION__) ; } else { $conn->commit() ; }
    }
    else
    {
      // log our error
      sfContext::getInstance()->getLogger()->err($errMsg) ;

      // rollback
      if ($useSavepoint) { $conn->rollback(__FUNCTION__) ; } else { $conn->rollback() ; }

      // throw an error if directed to do so
      if ($throwOnError) { throw $err ; }
    }

    return $results ;
  }
}
