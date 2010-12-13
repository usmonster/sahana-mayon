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
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
class agPerson extends BaseagPerson
{
  /**
   * getPersonPrimaryNames() is a static method to return a multi layer array of person's primary name.
   * @return array( person id => name type id => array( mj person name id, person name type, person name id, person name, priority) )
   *
   * @param array $personIds - Queries the primary names for the specified person only.
   */
  static public function getPersonPrimaryNames($personIds = NULL)
  {
    try
    {
//      $query = Doctrine_Query::create()
//        ->select('mn.person_id, mn.person_name_type_id, pn.person_name, mn.priority')
//        ->from('agPersonMjAgPersonName as mn')
//        ->innerJoin('mn.agPersonName as pn')
//        ->where('1=1');
//
//      /*
//       * Append a where clause to query for specific persons if an
//       * array of person ids is passed in as argument.
//       */
//      if (is_array($personIds) and count($personIds) > 0)
//      {
//        $query->whereIn('mn.person_id', $personIds);
//      }
//      $query->groupBy('mn.person_id, mn.person_name_type_id')
//        ->orderBy('mn.person_id, mn.person_name_type_id, priority desc');
//
//      print_r($query->getSqlQuery());
//
////      $resultSet = $query->execute(array(), Doctrine::HYDRATE_SCALAR);
//      $resultSet = $query->execute(array(), Doctrine::HYDRATE_SCALAR);
//      print_r($resultSet);

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

//      print_r($personNameArray);
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
   */
  static public function getPersonFullName($personIds = NULL)
  {
    $personPrimaryNames = self::getPersonPrimaryNames($personIds);
//    print_r($personPrimaryNames);

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


  /* function getAgPersonNameByTypeId($type) {
    //$type = intval($type);
    $type = $type->getAgNameType();

    if (!$type) return null;

    foreach($this->getAgPersonName() as $agPersonName) {
    if ($agPersonName->getAgPersonNameType() == $type) {
    return $agPersonName;
    }
    }

    return null;
    } */

  /**
   * agPersonNameGet() returns an array of the names for the current agPerson object
   * @return an array of names for the current agPerson object
   */
  function agPersonNameGet()
  {
    // TODO pass in array for person info to be paginated in the showSuccess page:
    $name_array = array();
    $ag_person_name_types = Doctrine::getTable('agPersonNameType')
            ->createQuery('b')
            ->execute();

    foreach ($ag_person_name_types as $ag_person_name_type) {
      $names = $this->getAgPersonMjAgPersonName();
      foreach ($names as $name) {
        if ($name->getPersonNameTypeId() == $ag_person_name_type->getId()) {
          $name_array[ucwords($ag_person_name_type)] = $name->getAgPersonName();
        }
      }
    }

    return $name_array;
  }

  /**
   * getAgPersonNameByType() retrieves the person's name for the supplied name type
   *
   * @param $agPersonNameTypeId ID of name type that we want returned.
   *
   * @return String value of person's name for the specified name type.
   *
   */
  function getPersonNameByType($agPersonNameTypeId)
  {
    $agPersonNameTypeId = intval($agPersonNameTypeId);
    if (!$agPersonNameTypeId) {
      return null;
    }

    $personNames = $this->getAgPersonMjAgPersonName();

    foreach ($personNames as $personName) {
      $thisNameType = $personName->getAgPersonNameType()->getId();
      if ($personName->getAgPersonNameType()->getId() == $agPersonNameTypeId) {
        return $personName->getAgPersonName()->getPersonName();
      }
    }

    return null;
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

  /** Extends the save() function by adding an updateIndex hook to the Lucene search engine
   * @param conn the connection to Doctrine
   * @return current save state of the object being saved
   */
  public function save(Doctrine_Connection $conn = null)
  {
    $conn = $conn ? $conn : $this->getTable()->getConnection();
    $conn->beginTransaction();

    try {
      $ret = parent::save($conn);

      //$this->updateLuceneIndex(); This has been moved to apps/frontend/lib/BaseFormDoctrine to ensure names are indexed properly.

      $conn->commit();

      return $ret;
    } catch (Exception $e) {
      $conn->rollBack();
      throw $e;
    }
  }

  /**
   * Deletes an agPerson. The function has been extended to update the Lucene index
   * as well, since a deleted agPerson should no longer show up in search results.
   *
   * @param $conn the connection to Doctrine
   * @return parent::delete($conn) Removes an agPerson object from the database.
   *
   */
  public function delete(Doctrine_Connection $conn = null)
  {
    $index = agPersonTable::getLuceneIndex();

    foreach ($index->find('pk:' . $this->getId()) as $hit) {
      $index->delete($hit->id);
    }

    return parent::delete($conn);
  }

}
