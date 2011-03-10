<?php

/**
 * agPerson this class extends the person object for various purposes
 *
 * LICENSE: This source file is subject to LGPLv3.0 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 *
 * @author     Charles Wisniewski, CUNY SPS
 * @author     Nils Stolpe, CUNY SPS
 * @author     Shirley Chan, CUNY SPS
 * @author     Chad Heuschober, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 *
 * @property array $helperClasses An array of helper class names and the ids used to access them.
 * @property array $_helperObjects An array of helper objects, lazily loaded upon request.
 * @property array $_helperMethods A constructed array of methods provided by all named helper
 * classes.
 */
class agPerson extends BaseagPerson
{
  public    $luceneSearchFields = array('id' => 'keyword');

  protected $helperClasses = array('agPersonNameHelper' => 'id') ;

  private   $_helperObjects = array(),
            $_helperMethods ;

  /**
   * This classes' constructor.
   */
  public function construct()
  {
    // call the parent's constructor
    parent::construct() ;

    // pre-load any helper methods we might want to look for in __call()
    $this->loadHelperMethods() ;
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
    if (array_key_exists($method, $this->_helperMethods))
    {
      try
      {
        // discover the class that owns the method being called
        $helperClass = $this->_helperMethods[$method] ;

        // lazily load that helper class
        $this->loadHelperClass($helperClass) ;
        
        // get our object out of the objects array and the string value of the id to use
        $helperObject = $this->_helperObjects[$helperClass] ;
        $classId = $this->helperClasses[$helperClass] ;
        $id = $this->$classId ;

        // execute and return
        $results = $helperObject->$method($id) ;
        return $results[$id] ;
      }
      catch (Exception $e)
      {
        // if there's an error, write to log and return
        $notice = sprintf('Execution of the %s method, found in %s failed. Attempted to use the
          parent class.', $method, $helperClass) ;
        sfContext::getInstance()->getLogger()->notice($notice) ;
      }
    }
    // since no method matched, call the parent's methods
    return parent::__call($method, $arguments) ;
  }

  /**
   * A happy little helper function to return all methods explicitly (publicly) defined by a
   * helper class.
   */
  private function loadHelperMethods()
  {
    // iterate through all the helper classes defined
    foreach ($this->helperClasses as $class => $id)
    {
      // get just the explicit children of those classes
      $methods = agClassHelper::getExplicitClassMethods($class) ;

      // build our methods array and assign the owner of each method
      // @note This is *extremely* naive and if more than one helper provides the same method no
      // guarantees will be made as to which will load it. Lesson: don't use the same method name!
      foreach ($methods as $method)
      {
        $this->_helperMethods[$method] = $class ;
      }
    }
  }

  /**
   * Method to instantiate a helper class object as an array member of the _helperObjects property.
   *
   * @param string $class The helper class being loaded.
   */
  private function loadHelperClass($class)
  {
    if (! isset($this->_helperObjects[$class]))
    {
      $this->_helperObjects[$class] = new $class() ;
    }
  }

  public function updateLucene()
  {
    $doc = new Zend_Search_Lucene_Document();
    //$doc = Zend_Search_Lucene_Document_Html::loadHTML($this->getBody());
    $doc->addField(Zend_Search_Lucene_Field::Keyword('id', $this->getId(), 'utf-8'));

    // uses the agPersonNameHelper method that includes all names / aliases of type in a string
    $names = $this->getNameByTypeAsString();
    foreach ($names as $key => $name) {
      $doc->addField(Zend_Search_Lucene_Field::Unstored($key . ' name', $name, 'utf-8'));
    }

    $sex = $this->getSex();
      $doc->addField(Zend_Search_Lucene_Field::Unstored('sex', $sex, 'utf-8'));
    
      $nationalities = $this->getNationality();
      foreach($nationalities as $nationality)
      {
        $doc->addField(Zend_Search_Lucene_Field::Unstored('nationality', $nationality, 'utf-8'));
      }
   
    $ethnicity = $this->getEthnicity();
    $doc->addField(Zend_Search_Lucene_Field::Unstored('ethnicity', $ethnicity, 'utf-8'));

    return $doc;
  }

  public function getSex()
  {
      $sexstore = $this->getAgPersonSex();
      foreach ($sexstore as $gender)
      {
        $sex = $gender->getAgSex()->sex;
      }

      return $sex;
  }

  public function getNationality()
  {
      foreach($this->getAgPersonMjAgNationality() as $nationality)
             $nationalities[] = $nationality->getAgNationality()->nationality;

      return $nationalities;
  }

  public function getEthnicity()
  {
      foreach ($this->getAgPersonEthnicity() as $ethnicity)
      {
          $ethnicities = $ethnicity->getAgEthnicity()->ethnicity;
      }
      return $ethnicities;
  }

  public function getLanguages()
  {
      foreach($this->getAgPersonMjAgLanguage() as $languageCompetency)
      {
          $languages = $languageCompetency->getAgLanguage()->language;
      }
      return $languages;
  }

  /**
   * getPersonPrimaryNames() is a static method to return a multi layer array of person's primary name.
   * @return array( person id => name type id => array( mj person name id,
   * person name type, person name id, person name, priority) )
   *
   * @param array $personIds - Queries the primary names for the specified person only.
   * @deprecated Replaced by agPersonNameHelper->getPrimaryNameByType()
   */
  static public function getPersonPrimaryNames($personIds = NULL)
  {
    try
    {
      $rawQuery = new Doctrine_RawSql();
      $rawQuery->select('{sub.id},{sub.person_id},{sub.person_name_type_id},{sub.priority},{sub.person_name_id}, {pn.person_name}, {pnt.person_name_type}')
        ->from('(select smn.id, smn.person_id, smn.person_name_type_id,  smn.priority, smn.person_name_id FROM ag_person_mj_ag_person_name AS smn ORDER BY smn.person_id, smn.person_name_type_id, smn.priority) as sub ')
        ->innerJoin('ag_person_name AS pn ON pn.id=sub.person_name_id')
        ->innerJoin('ag_person_name_type As pnt ON pnt.id=sub.person_name_type_id')
        ->where('1=1');

      if (is_array($personIds) and count($personIds) > 0)
      {
        $rawQuery->whereIn('sub.person_id', $personIds);
      }

      $rawQuery->groupBY('sub.person_id, sub.person_name_type_id')
        ->addComponent('sub', 'agPersonMjAgPersonName m')
        ->addComponent('pn', 'm.agPersonName pn')
        ->addComponent('pnt', 'm.agPersonNameType pnt');
      
      $resultSet = $rawQuery->execute();

      $personNameArray = array();
      foreach ($resultSet as $rslt)
      {
        $person_id = $rslt->getPersonId();
        $join_table_id = $rslt->getId();
        $person_name_type_id = $rslt->getPersonNameTypeId();
        $person_name_id = $rslt->getPersonNameId();
        $priority = $rslt->getPriority();
        $person_name = $rslt['agPersonName']['person_name'];
        $person_name_type = $rslt['agPersonNameType']['person_name_type'];

        $newRecord = array ( $person_name_type_id => array ( 'join_table_id' => $join_table_id,
                                                             'name_type' => $person_name_type,
                                                             'name_id' => $person_name_id,
                                                             'name' => $person_name,
                                                             'priority' =>$priority
                                                        )
                           );

        if (array_key_exists($person_id, $personNameArray))
        {
          $tempArray = $personNameArray[$person_id];
          $newArray = $tempArray + $newRecord;
          $personNameArray[$person_id] = $newArray;

        } else {
          $personNameArray[$person_id] = $newRecord;
        }
      }

      return $personNameArray;
    }catch (\Doctrine\ORM\ORMException $e) {
      return NULL;
    }
  }

  /**
   * getPersonFullName() is a static method to return an array of person's full name where person_id is the array key.
   * @return array(person_id => person full name)
   *
   * @param array $personIds - return an array of person's full name for the specified person only.
   * @deprecated Replaced by agPersonNameHelper->getPrimaryNameAsString()
   */
  static public function getPersonFullName($personIds = NULL)
  {
    $personPrimaryNames = self::getPersonPrimaryNames($personIds);

    $personFullName = array();
    foreach($personPrimaryNames as $personId => $personNameInfo)
    {
      $givenName = array_key_exists(1, $personNameInfo) ? $personNameInfo[1]['name'] : '';
      $middleName = array_key_exists(2, $personNameInfo) ? ' ' . $personNameInfo[2]['name'] : '';
      $familyName = array_key_exists(3, $personNameInfo) ? ' ' . $personNameInfo[3]['name'] : '';
      $fullName = $givenName . $middleName . $familyName;
      $fullName = ucwords($fullName);
      $personFullName[$personId] = $fullName;
    }

    return $personFullName;
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

  /**
   * adds new values for the Lucene Index for an agPerson/staff-member.
   * The terms added to the index will allow the agPerson they are associated with to be
   * returned after running a search.
   * @return an updated Lucene Index
   *
   * See further comments inside the code for more information.
   */
  public function updateLuceneIndex()
  {
    $index = agPersonTable::getLuceneIndex();

    foreach ($index->find('pk:' . $this->getId()) as $hit) {
      $index->delete($hit->id);
    }

// This creates the new document for the index and sets a field with a pk value of the created/edited person's ID.
    $doc = new Zend_Search_Lucene_Document();
    $doc->addField(Zend_Search_Lucene_Field::Keyword('pk', $this->getId()));
// Generate the variables to concat field values into so we don't get an exception.
    $natBuild = "";
    $relBuild = "";
    $nameBuild = "";

// The loops below index the agPerson's associated values and assign them to the PK.
    foreach ($this->getAgEthnicity() as $ethnicity) {
      $doc->addField(Zend_Search_Lucene_Field::UnStored('ethnicity', $ethnicity->ethnicity, 'utf-8'));
      $index->addDocument($doc);
    }

    foreach ($this->getAgNationality() as $nationality) {
      $natBuild = $natBuild . $nationality->nationality . ' ';
    }

    if (isset($natBuild)) {
      $doc->addField(Zend_Search_Lucene_Field::UnStored('nationality', $natBuild, 'utf-8'));
    }

    foreach ($this->getAgReligion() as $religion) {
      $relBuild = $relBuild . $religion->religion . ' ';
    }

    if (isset($relBuild)) {
      $doc->addField(Zend_Search_Lucene_Field::UnStored('religion', $relBuild, 'utf-8'));
    }

    foreach ($this->getAgPersonName() as $name) {
      $nameBuild = $nameBuild . $name->person_name . ' ';
    }

    if (isset($nameBuild)) {
      $doc->addField(Zend_Search_Lucene_Field::UnStored('name', $nameBuild, 'utf-8'));
    }

    $index->addDocument($doc);
    $index->commit();
  }

}
