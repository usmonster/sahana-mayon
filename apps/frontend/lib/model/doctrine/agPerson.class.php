<?php

/**
 * Extends the person object for various purposes
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author     Charles Wisniewski, CUNY SPS
 * @author     Nils Stolpe, CUNY SPS
 * @author     Shirley Chan, CUNY SPS
 * @author     Chad Heuschober, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 *
 */
class agPerson extends BaseagPerson
{

  public $luceneSearchFields = array('id' => 'keyword');
  protected $helperClasses = array('agPersonNameHelper' => 'id',
                                    'agEntityAddressHelper' => 'entity_id',
                                    'agEntityPhoneHelper' => 'entity_id',
                                    'agEntityEmailHelper' => 'entity_id');
  private $_helperObjects = array(),
  $_helperMethods;

  // @deprecated
  protected $isAutoIndexed;


  /**
   * This classes' constructor.
   */
  public function __construct($table = null, $isNewEntry = false, $isAutoIndexed = true)
  {
    // call the parent's constructor
    parent::__construct($table, $isNewEntry);
    $this->isAutoIndexed = $isAutoIndexed;

    // pre-load any helper methods we might want to look for in __call()
    $this->loadHelperMethods();
  }

  /**
   * Overloaded magic call method to provide access to helper class functions.
   *
   * @param string $method The method being called.
   * @param array $arguments The arguments being provided to additional functions
   * (not used by helpers).
   * @return function call
   */
  public function __call($method, $arguments)
  {
    // check to see if our method exists in our helpers
    if (array_key_exists($method, $this->_helperMethods)) {
      try {
        // discover the class that owns the method being called
        $helperClass = $this->_helperMethods[$method];

        // lazily load that helper class
        $this->loadHelperClass($helperClass);

        // get our object out of the objects array and the string value of the id to use
        $helperObject = $this->_helperObjects[$helperClass];
        $classId = $this->helperClasses[$helperClass];
        $id = $this->$classId;

        // set up our args
        array_unshift($arguments, array($id));

        // execute and return
        $results = call_user_func_array(array($helperObject, $method), $arguments);
        // stop an undefined index notice in case results is unpopulated.
        if (isset($results[$id])) {
          return $results[$id];
        } else {
          return;
        }
      } catch (Exception $e) {
        // if there's an error, write to log and return
        $notice = sprintf('Execution of the %s method, found in %s failed. Attempted to use the
          parent class.', $method, $helperClass);
        sfContext::getInstance()->getLogger()->notice($notice);
      }
    }
    // since no method matched, call the parent's methods
    return parent::__call($method, $arguments);
  }

  /**
   * A happy little helper function to return all methods explicitly
   * (publicly) defined by a helper class.
   */
  private function loadHelperMethods()
  {
    // iterate through all the helper classes defined
    foreach ($this->helperClasses as $class => $id) {
      // get just the explicit children of those classes
      $methods = agClassHelper::getExplicitClassMethods($class);

      // build our methods array and assign the owner of each method
      // @note This is *extremely* naive and if more than one helper
      // provides the same method no guarantees will be made as to
      // which will load it. Lesson: don't use the same method name!
      foreach ($methods as $method) {
        $this->_helperMethods[$method] = $class;
      }
    }
  }

  /**
   * Method to instantiate a helper class object as an array member
   * of the _helperObjects property.
   *
   * @param string $class The helper class being loaded.
   */
  private function loadHelperClass($class)
  {
    if (!isset($this->_helperObjects[$class])) {
      $this->_helperObjects[$class] = new $class();
    }
  }

  public function getSex()
  {
    $sexstore = $this->getAgPersonSex();
    foreach ($sexstore as $gender) {
      $sex = $gender->getAgSex()->sex;
    }

    return $sex;
  }

  public function getNationality()
  {
    $nationalities = array();
    foreach ($this->getAgPersonMjAgNationality() as $nationality)
      $nationalities[] = $nationality->getAgNationality()->nationality;

    return $nationalities;
  }

  public function getEthnicity()
  {
    $ethnicities = array();
    foreach ($this->getAgPersonEthnicity() as $ethnicity) {
      $ethnicities = $ethnicity->getAgEthnicity()->ethnicity;
    }
    return $ethnicities;
  }

  public function getLanguages()
  {
    $languages = array();
    foreach ($this->getAgPersonMjAgLanguage() as $languageCompetency) {
      $languages = $languageCompetency->getAgLanguage()->language;
    }
    return $languages;
  }

  /**
   * sets the person's name for the supplied name type
   */
  function setPersonNameByType($agPersonNameTypeId, $newName)
  {
    /* Does the name already exist in agPersonName? */
    if (!$newAgPersonName = Doctrine::getTable('agPersonName')
            ->createQuery('getPersonNameByName')
            ->select('pn.*')
            ->from('agPersonName pn')
            ->where('pn.person_name = ?', $newName)
            ->fetchOne()
    ) {
      /* If it doesn't already exist, create it */
      $newAgPersonName = new agPersonName();
      $newAgPersonName->setPersonName($newName);
      $newAgPersonName->save();
    }
    /* At this point, whether the name existed or not before, it should exist
     * and be represented by $newAgPersonName */

    /* Does this person already have a name of the same type? */
    if ($exJoin = Doctrine::getTable('agPersonMjAgPersonName')
            ->createQuery('getNameJoin')
            ->select('nj.*')
            ->from('agPersonMjAgPersonName nj')
            ->where('nj.person_id = ?', $this->getId())
            ->andWhere('nj.person_name_type_id = ?', $agPersonNameTypeId)
            ->fetchOne()
    ) {
      /* Retrieve all joins to this agPersonName */
      $otherJoins = $exJoin->getAgPersonName()->getAgPersonMjAgPersonName();

      if (count($otherJoins) > 1) {
        /* If there is more than one join, delete just the join */
        $exJoin->delete();
      } else {
        /* If this is the last join, first delete the join, then the orphaned name */
        //$exName = $exJoin->getAgPersonName();
        $exJoin->delete();
        //$exName->delete();
      }
    }

    if ($newType = Doctrine::getTable('agPersonNameType')
            ->createQuery('getNametype')
            ->select('nt.*')
            ->from('agPersonNameType nt')
            ->where('nt.id = ?', $agPersonNameTypeId)
            ->fetchOne()
    ) {
      $newJoin = new agPersonMjAgPersonName();
      $newJoin->setAgPerson($this);
      $newJoin->setAgPersonName($newAgPersonName);
      $newJoin->setAgPersonNameType($newType);
      $newJoin->setPriority(1);
      $newJoin->save();
    }

    return null;
  }

}
