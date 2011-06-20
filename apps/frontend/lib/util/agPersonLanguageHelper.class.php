<?php

/**
 * Provides person language helper functions and inherits several methods and properties from the
 * language helper.
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
 *
 */
class agPersonLanguageHelper extends agLanguageHelper
{
  /**
   * This is the classes' constructor and is used to set up class properties, where appropriate.
   *
   * @param array $personIds A single-dimension array of person id values.
   */
  public function __construct(array $personIds = NULL)
  {
    // set our person ids if passed any at construction
    parent::__construct($personIds) ;
  }

  /**
   * Method to construct and return a basic language query object.
   *
   * @param array $personIds A single-dimension array of person id values. Default is NULL.
   * @param boolean $primary A boolean that controls whether or not the query constructor will
   * build a query that only returns primary language or all languages.
   * @return Doctrine_Query An instantiated doctrine query object.
   */
  protected function _getLanguageComponents(array $personIds = NULL, $primary = TRUE)
  {
    $personIds = $this->getRecordIds($personIds) ;

    $q = agDoctrineQuery::create()
      ->select('pml.person_id')
          ->addSelect('pml.priority')
          ->addSelect('pml.language_id')
          ->addSelect('plc.language_format_id')
          ->addSelect('plc.language_competency_id')
        ->from('agPersonMjAglanguage pml')
          ->leftJoin('pml.agPersonLanguageCompetency plc')
        ->whereIn('pml.person_id', $personIds)
        ->orderBy('pml.priority ASC') ;

    if ($primary)
    {
      $q->andwhere(' EXISTS (
        SELECT p.id
        FROM agPersonMjAgLanguage p
        WHERE p.person_id = pml.person_id
        HAVING MIN(p.priority) = pml.priority)') ;
    }

    return $q ;
  }
  
  /**
   * Method to retrieve person's language, format, and competency information by ids.
   * 
   * @param array $personIds A single-dimension array of person id values. Default is NULL.
   * @param boolean $primary A boolean that controls whether or not the query constructor will
   * build a query that only returns primary names or all names.
   * @return array A multi-dimensional associative array keyed by person id, 
   * ordering index, and format id and valued by competency Id.
   * <code>
   * array( personID => 
   *            array( priority => array( [0] => langaugeId,
   *                                      [1] => array( formatId => competencyId
   *                                                    ... )
   *                                    )
   *                   ... )
   *        ...)
   * </code>      
   */
  public function getPersonLanguageById(array $personIds = NULL, $primary = TRUE)
  {
    $results = array();
    $q = $this->_getLanguageComponents($personIds, $primary);

    $rows = $q->execute(array(), Doctrine_Core::HYDRATE_NONE) ;

    foreach ($rows AS $row)
    {
      $personId = $row[0];
      $idx = $row[1];
      $languageId = $row[2];
      $formatId = $row[3];
      $competencyId = $row[4];
      if (is_null($formatId))
      {
        $results[$personId][$idx] = array($languageId, array());
      }
      else
      {
        if (isset($results[$personId][$idx]))
        {
          $results[$personId][$idx][1][$formatId] = $competencyId;
        }
        else
        {
          $results[$personId][$idx] = array($languageId, array($formatId => $competencyId));
        }
      }
    }

    return $results;
  }

  /**
   * Method to retrieve person's language, format, and competency information.
   *
   * @param array $personIds A single-dimension array of person id values. Default is NULL.
   * @param boolean $primary A boolean that controls whether or not the query constructor will
   * build a query that only returns primary names or all names.
   * @return array A multi-dimensional associative array keyed by person id,
   * ordering index, and format and valued by competency.
   * <code>
   * array( personID =>
   *            array( index => array( [0] => langauge,
   *                                   [1] => array( format => competency
   *                                                 ... )
   *                                 )
   *                   ... )
   *      ...)
   * </code>
   */
  public function getPersonLanguage(array $personIds = NULL, $primary = TRUE)
  {
    $results = array();
    $q = $this->_getLanguageComponents($personIds, $primary);
    $q->addSelect('l.language')
      ->addSelect('lf.language_format')
      ->addSelect('lc.language_competency')
      ->innerJoin('pml.agLanguage l')
      ->leftJoin('plc.agLanguageFormat lf')
      ->leftJoin('plc.agLanguageCompetency lc');

    $rows = $q->execute(array(), Doctrine_Core::HYDRATE_NONE) ;

    foreach ($rows AS $row)
    {
      $personId = $row[0];
      $idx = $row[1];
      $language = $row[5];
      $format = $row[6];
      $competency = $row[7];
      if (is_null($format))
      {
        $results[$personId][$idx] = array($language, array());
      }
      else
      {
        if (isset($results[$personId][$idx]))
        {
          $results[$personId][$idx][1][$format] = $competency;
        }
        else
        {
          $results[$personId][$idx] = array($language, array($format => $competency));
        }
      }
    }

    return $results;
  }
  /**
   * Method to return the diff of the two arrays.
   *
   * @param array $compareFrom A simple array to compare from.
   * @param array $compareTo A simple array to compare against.
   * @return array $diffArray Returns a simple array of the diff.
   */
  private function diffArrays(array $compareFrom, array $keyArray)
  {
    // Sort array to speed up comparisons.
    array_walk($compareFrom, function(&$language) { $language = strtolower($language); });
    sort($compareFrom);
    $compareTo = array_keys($keyArray);
    sort($compareTo);

    // diff, then release the two component arrays -- all we care about is the diff
    $diffArray = array_diff($compareFrom, $compareTo);
    unset($compareFrom);
    unset($compareTo);
    return $diffArray;
  }

  /**
   * Method to set person languages and automatically prioritize them accordingly.
   *
   * @param array $personLanguages A multi-dimesional array of person languages similar to the
   * output of getLanguageById
   * <code>
   * array( personID =>
   *            array( index => array( [0] => langauge,
   *                                   [1] => array( format => competency
   *                                                 ... )
   *                                 )
   *                   ... )
   *      ...)
   * </code>
   * @param boolean $keepHistory Boolean to control whether or not current languages will be retained
   * or whether all languages will be replaced. Defaults to class property.
   * @param boolean $createEdgeTableValues Boolean to control whether or not new languages are allowed
   * and should be inserted to agLanguage table.
   * @param boolean $throwOnError Boolean to control whether or not failures will throw
   * an exception. Defaults to the class property.
   * @param Doctrine_Connection $conn An optional Doctrine Connection object.
   * @return array An associative array with counts of the operations performed and/or personId's
   * for which no operations could be performed.
   */
  public function setPersonLanguages(array $personLanguages,
                                     $keepHistory = NULL,
                                     $createEdgeTableValues = NULL,
                                     $throwOnError = NULL,
                                     Doctrine_Connection $conn = NULL)
  {
    // explicit declarations are nice
    $results = array();
    $invalidLanguages = array();
    $uniqLanguages = array();
    $uniqFormats = array();
    $uniqCompetencies = array();
    $err = NULL;
    $deletePersonlanguages = array();

    // pick up some defaults if not passed anything
    if (is_null($conn)) { $conn = Doctrine_Manager::connection(); }
    if (is_null($createEdgeTableValues)) { $createEdgeTableValues = $this->createEdgeTableValues; }

    // reduce our person language to just the uniques
    foreach ($personLanguages AS $personId => &$languageComponents)
    {
      foreach ($languageComponents AS $priority => &$langComps)
      {
        // we do this at this early stage so we're always dealing with a post-trimmed value
        $langComps[0] = trim($langComps[0]);

        // add it to our unique array
        $uniqLanguages[] = $langComps[0];

        if (isset($langComps[1]))
        {
          foreach($langComps[1] AS $format => &$competency)
          {
            // Always trim value
            $competency = trim($competency);

            // Add to unique arrays
            $uniqFormats[] = $format;
            $uniqCompetencies[] = $competency;
          }
        }
        else
        {
          // Add empty array so we can eliminate the step of checking later on when processing
          // person languages.
          $langComps[1] = array();
        }
      }
    }
    // Unset all variables used in foreach loops that are no longer needed to release memory and
    // prevent any potential hidden issues.
    unset($personId, $languageComponents, $priority, $langComps, $format, $competency);

    // actually make these unqiue arrays unique
    $uniqLanguages = array_unique($uniqLanguages);
    $uniqFormats = array_unique($uniqFormats);
    $uniqCompetencies = array_unique($uniqCompetencies);

    // get key mapping
    $languageIds = array_change_key_case($this->getLanguageIds($uniqLanguages), CASE_LOWER);
    $formatIds = array_change_key_case($this->getLanguageFormatIds($uniqFormats));
    $competencyIds = array_change_key_case($this->getLanguageCompetencyIds($uniqCompetencies));

    // Find new languages by diffing the arrays.
    $newLanguages = $this->diffArrays($uniqLanguages, $languageIds);

    // Unset the unqiue arrays after final usage.
    unset($uniqLanguages, $uniqFormats, $uniqCompetencies);
    
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

    // Create new edge values if $createNewEdgeValues is TRUE
    if ($createEdgeTableValues)
    {
      try
      {
        // set new languagess / return their ids
        $languageIds = ($languageIds + ($this->setNewLanguages($newLanguages, $throwOnError, $conn)));
        unset($newLanguages);
      }
      catch(Exception $e)
      {
        // log our error
        $errMsg = sprintf('%s failed to execute. (%s).', __FUNCTION__, $e->getMessage());

        // capture the error for comparison later
        $err = $e;
      }
    }
    else
    {
      // if $createNewEdgeValues not true and a new language is passed in, capture the new language
      // as error.
      $errMsg = NULL;
      if (count($newLanguages) > 0)
      {
        $errMsg = sprintf('Invalid languages (%s)', implode(', ', $newLanguages));

        // log whichever error we received
        sfContext::getInstance()->getLogger()->err($errMsg);

        // throw the exception we promised in our boolean
        if ($throwOnError) { throw new Exception($errMsg); }
      }
    }

    // set languages, formats, and competencies.
    if (is_null($err))
    {
      // recombine all language components and their person owners
      foreach ($personLanguages AS $personId => $languageComponents)
      {
        foreach ($languageComponents AS $priority => $langComps)
        {
          $lowerLanguage = strtolower($langComps[0]);
          if (array_key_exists($lowerLanguage, $languageIds))
          {
            $personLanguages[$personId][$priority][0] = $languageIds[$lowerLanguage];
            foreach ($langComps[1] AS $format => $competency)
            {
              $lowerFormat = strtolower($format);
              $lowerCompetency = strtolower($competency);
              if (array_key_exists($lowerFormat, $formatIds)
                  && array_key_exists($lowerCompetency, $competencyIds))
              {
                $personLanguages[$personId][$priority][1][$formatIds[$lowerFormat]] = $competencyIds[$lowerCompetency];
              }
              else
              {
                // Capture invalid format/competency records.
                if (isset($invalidLanguages[$personId][$priority]))
                {
                  $invalidLanguages[$personId][$priority][1][$format] = $competency;
                }
                else
                {
                  $invalidLanguages[$personId][$priority] = array($langComps[0], array($format => $competency));
                }
              }
              unset($personLanguages[$personId][$priority][1][$format]);
            } // end of language format/competency loop
          }
          else
          {
            // Invalid language.
            $invalidLanguages[$personId][$priority] = $langComps;
            unset($personLanguages[$personId][$priority]);

            // @TODO May need to add $enforceStrict logic here for invalid languages.
          }
        }
      }
      // Unset all variables used in foreach loops that are no longer needed to release memory and
      // prevent any potential hidden issues.
      unset($personId, $languageComponents, $priority, $langComps, $format, $competency);
      unset($lowerLanguage, $lowerFormat, $lowerCompetency);

      // At this point $personLanguages only consist of valid languages and language components.

      try
      {
        // connect languages to their owners
        $results = $this->_setPersonLanguage($personLanguages, $keepHistory, $throwOnError, $conn);
      }
      catch(Exception $e)
      {
        // log our error
        $errMsg = sprintf('%s failed to execute. (%s).', '_setPersonNames', $e->getMessage());

        // capture the exception for later
        $err = $e;
      }
    }

    if (is_null($err))
    {
      // yay, no problems, now we commit
      if ($useSavepoint) { $conn->commit(__FUNCTION__) ; } else { $conn->commit(); }
    }
    else
    {
      // log whichever error we received
      sfContext::getInstance()->getLogger()->err($errMsg);

      // rollback
      if ($useSavepoint) { $conn->rollback(__FUNCTION__); } else { $conn->rollback(); }

      // ALWAYS throw an error, it's like stepping on a crack if you don't
      if ($throwOnError) { throw $e; }
    }

    return $results;
  }

  /**
   * Protected method that creates new person language entries in the many-to-many person language table.
   *
   * @param array $personLanguages A multi-dimesional array of person languages similar to the
   * output of getLanguageById
   * <code>
   * array( personID =>
   *            array( index => array( [0] => langaugeId,
   *                                   [1] => array( formatId => competencyId
   *                                                 ... )
   *                                 )
   *                   ... )
   *      ...)
   * </code>
   * @param boolean $keepHistory Boolean to control whether or not current languages will be retained
   * or whether all languages will be replaced. Defaults to class property.
   * @param boolean $throwOnError Boolean to control whether or not failures will throw
   * an exception. Defaults to the class property.
   * @param Doctrine_Connection $conn An optional Doctrine Connection object.
   * @return array An associative array with counts of the operations performed and/or personId's
   * for which no operations could be performed.
   */
  protected function _setPersonLanguage (array $personLanguages,
                                         $keepHistory = NULL,
                                         $throwOnError = NULL,
                                         Doctrine_Connection $conn = NULL)
  {
    // explicit results declaration
    $results = array('upserted'=>0, 'removed'=>0, 'failures'=>array());
    $currLanguages = array();

    // get some defaults if not explicitly passed
    if (is_null($keepHistory)) { $keepHistory = $this->keepHistory; }
    if (is_null($throwOnError)) { $throwOnError = $this->throwOnError; }
    if (is_null($conn)) { $conn = Doctrine_Manager::connection(); }

    if ($keepHistory)
    {
      // if we're going to process existing languages and keep them, then hold on
      $currLanguages = $this->getPersonLanguageById(array_keys($personLanguages), FALSE);
    }

    // execute the reprioritization helper and pass it our current languages as found in the db
    $personLanguages = $this->reprioritizePersonLanguages($personLanguages, $currLanguages);

    // Unset $currLanguages
    unset($currLanguages);

    // Build a simpler $personLanguages array for a more efficient use of searches later on.
    $simplifiedNewLanguages = array();

    foreach ($personLanguages AS $personId => $pLangs)
    {
      foreach ($pLangs as $priority => $lang)
      {
        $simplifiedNewLanguages[$personId][$lang[0]] = $priority;
      }
    }
    // Release memories.
    unset($personId, $pLangs, $priority, $lang);

    // here we set up our collection of ag_person_mj_ag_language records, selecting only those with
    // the personIds we're affecting. Note: INDEXBY is very important if we intend to access this
    // collection via array access as we do later.

    $deletePersonLanguageIds = array();
    $updateLanguageComponents = array();

    $q = agDoctrineQuery::create($conn)
      ->select('mpl.*')
        ->from('agPersonMjAgLanguage mpl INDEXBY mpl.id')
        ->whereIn('mpl.person_id', array_keys($personLanguages));
    $personLanguageCollection = $q->execute() ;

    // Determine records whether to be updated and/or deleted.
    foreach($personLanguageCollection AS $collId => $pLang)
    {
      $collPId = $pLang['person_id'];
      $collLId = $pLang['language_id'];

      if (isset($simplifiedNewLanguages[$collPId])
          && array_key_exists($collLId, $simplifiedNewLanguages[$collPId]))
      {
        $personLanguagePriority = $simplifiedNewLanguages[$collPId][$collLId];

        if ($personLanguageCollection[$collId]['priority'] == $personLanguagePriority)
        {
          // No update to person language record necessary.  Should still add to 
          // $updateLanguageComponent to determine if child needs update later on.  Unset it from
          // $simplifiedNewLanguage.
          $updateLanguageComponents[$collId] = $personLanguages[$collPId][$personLanguagePriority][1];
          unset($simplifiedNewLanguages[$collPId][$collLId]);
        }
        else
        {
          // Easier to completely remove record than to perform update and possibly run into
          // priority constraint violation issues.
          $deletePersonLanguageIds[] = $collId;
        }
      }
      else
      {
        // Remove from collection.
        $deletePersonLanguageIds[] = $collId;
        $results['removed']++;
      }
    }
    // Unset variables to release memories.
    unset($collId, $pLang, $collPId, $collLId, $personLanguagePriority, $personLanguageCollection);

    // Remove all delete records, leaving only records for new inserts and updates.
    $this->deletePersonLanguages($deletePersonLanguageIds, $throwOnError, $conn);
    unset($deletePersonLanguageIds);

    // Now all deletes are done on the manay-to-many person language table and it's cooresponding 
    // person language competency child table at this point.  Only thing left to do on the person
    // language joining table is to add new person language records.

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

    try
    {
      // Add new records and records with updated priority values in the person language
      // many-to-many table.
      foreach ($simplifiedNewLanguages AS $personId => $component)
      {
        foreach ($component AS $languageId => $priority)
        {
          // Create new person language records.
          $personLanguage = new agPersonMjAgLanguage();
          $personLanguage['person_id'] = $personId;
          $personLanguage['language_id'] = $languageId;
          $personLanguage['priority'] = $priority;
          $personLanguage->save($conn);
          $personLanguageId = $personLanguage->id;

          // Since we're creating new person language records, might as well create their
          // cooresponding format and competency records.

          $languageComponents = $personLanguages[$personId][$priority][1];

          if (count($languageComponents) > 0)
          {
            foreach ($languageComponents AS $formatId => $competencyId)
            {
              // Create new person's language component records.
              $personLanguageCompetency = new agPersonLanguageCompetency();
              $personLanguageCompetency['person_language_id'] = $personLanguageId;
              $personLanguageCompetency['language_format_id'] = $formatId;
              $personLanguageCompetency['language_competency_id'] = $competencyId;
              $personLanguageCompetency->save($conn);
            }
          }

          // Unset all new person language records.
          if (count($personLanguages[$personId]) == 1)
          {
            unset($personLanguages[$personId]);
          }
          else
          {
            unset($personLanguages[$personId][$priority]);
          }
        }
        $results['upserted']++;
      }

      // Unset array to release memory.
      unset($personLanguage, $personLanguageCompetency);
      unset($personId, $component, $languageId, $priority, $languageComponents, $formatId, $competencyId);
      unset($simplifiedNewLanguages);

      // commit, being sensitive to our nesting
      if ($useSavepoint) { $conn->commit(__FUNCTION__); } else { $conn->commit(); }
    }
    catch(Exception $e)
    {
      // log our error
      $errMsg = sprintf('%s failed at: %s', __FUNCTION__, $e->getMessage());
      sfContext::getInstance()->getLogger()->err($errMsg);

      // rollback
      if ($useSavepoint) { $conn->rollback(__FUNCTION__); } else { $conn->rollback(); }

      // ALWAYS throw an error, it's like stepping on a crack if you don't
      if ($throwOnError) { throw $e; }

      $results['failures'] = array_keys($personLanguages);
    }

    // At this point, we still have to update, delete, and add new person language records.

    // Query for all existing person language competency records.
    $q = agDoctrineQuery::create($conn)
      ->select('plc.*')
        ->from('agPersonLanguageCompetency plc INDEXBY plc.id')
        ->whereIn('plc.person_language_id', array_keys($updateLanguageComponents));
    $personLanguageComponentColl = $q->execute() ;

    $isUpdated = 0;

    // Update/delete person language competency records.
    foreach($personLanguageComponentColl AS $collId => $components)
    {
      $collPersonLanguageId = $components['person_language_id'];
      $collFormatId = $components['language_format_id'];
      if (isset($updateLanguageComponents[$collPersonLanguageId])
          && array_key_exists($collFormatId, $updateLanguageComponents[$collPersonLanguageId]))
      {
        $personCompetencyId = $updateLanguageComponents[$collPersonLanguageId][$collFormatId];
        if ($personLanguageComponentColl[$collId]['language_competency_id'] != $personCompetencyId)
        {
          $personLanguageComponentColl[$collId]['language_competency_id'] = $personCompetencyId;
        }
      }
      else
      {
        $personLanguageComponentColl->remove($collId);
      }

      if (isset($updateLanguageComponents[$collPersonLanguageId][$collFormatId]))
      {
        if (count($updateLanguageComponents[$collPersonLanguageId]) == 1)
        {
          unset($updateLanguageComponents[$collPersonLanguageId]);
        }
        else
        {
          unset($updateLanguageComponents[$collPersonLanguageId][$collFormatId]);
        }
      }
      $isUpdated = 1;
    }
    if ($isUpdated)
    {
      $results['upserted']++;
    }
    unset($collId, $components, $collPersonLanguageId, $collFormatId, $personCompetencyId);

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

    try
    {
      $personLanguageComponentColl->save($conn);

      // commit, being sensitive to our nesting
      if ($useSavepoint) { $conn->commit(__FUNCTION__); } else { $conn->commit(); }
    }
    catch(Exception $e)
    {
      // log our error
      $errMsg = sprintf('%s failed at: %s', __FUNCTION__, $e->getMessage());
      sfContext::getInstance()->getLogger()->err($errMsg);

      // rollback
      if ($useSavepoint) { $conn->rollback(__FUNCTION__); } else { $conn->rollback(); }

      // ALWAYS throw an error, it's like stepping on a crack if you don't
      if ($throwOnError) { throw $e; }

      $results['failures'] = array_keys($personLanguages);
    }

    // Last final step is to add new person language competency records.

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

    try
    {
      foreach($updateLanguageComponents AS $personLanguageId => $pLangs)
      {
        foreach($pLangs as $formatId => $competencyId)
        {
          // Create new person's language component records.
          $personLanguageCompetency = new agPersonLanguageCompetency();
          $personLanguageCompetency['person_language_id'] = $personLanguageId;
          $personLanguageCompetency['language_format_id'] = $formatId;
          $personLanguageCompetency['language_competency_id'] = $competencyId;
          $personLanguageCompetency->save($conn);
        }
      }
      // commit, being sensitive to our nesting
      if ($useSavepoint) { $conn->commit(__FUNCTION__); } else { $conn->commit(); }
    }
    catch(Exception $e)
    {
      // log our error
      $errMsg = sprintf('%s failed at: %s', __FUNCTION__, $e->getMessage());
      sfContext::getInstance()->getLogger()->err($errMsg);

      // rollback
      if ($useSavepoint) { $conn->rollback(__FUNCTION__); } else { $conn->rollback(); }

      // ALWAYS throw an error, it's like stepping on a crack if you don't
      if ($throwOnError) { throw $e; }

      $results['failures'] = array_keys($personLanguages);
    }

    unset($updateLanguageComponents, $personLanguageId, $pLangs, $formatId, $competencyId);

    return $results ;
  }

  /**
   * Method to prioritize person languages.
   *
   * @param array $newLanguages A multi-dimesional array of person languages similar to the
   * output of getLanguageById
   * <code>
   * array( personID =>
   *            array( index => array( [0] => langaugeId,
   *                                   [1] => array( formatId => competencyId
   *                                                 ... )
   *                                 )
   *                   ... )
   *      ...)
   * </code>
   * @param array $currLanguages A multi-dimesional array of person languages similar to the
   * output of getLanguageById
   * <code>
   * array( personID =>
   *            array( index => array( [0] => langaugeId,
   *                                   [1] => array( formatId => competencyId
   *                                                 ... )
   *                                 )
   *                   ... )
   *      ...)
   * </code>
   * @return array $newLanguages A multi-dimesional array of person languages similar to the
   * output of getLanguageById
   * <code>
   * array( personID =>
   *            array( index => array( [0] => langaugeId,
   *                                   [1] => array( formatId => competencyId
   *                                                 ... )
   *                                 )
   *                   ... )
   *      ...)
   * </code>
   */
  protected function reprioritizePersonLanguages(array $newLanguages, array $currLanguages)
  {
    $simplifiedNewLanguages = array();

    foreach ($newLanguages AS $personId => $pLangs)
    {
      if (count($pLangs) == 0) {
        $simplifiedNewLanguages[$personId] = array();
      }
      else
      {
        foreach ($pLangs as $priority => $lang)
        {
          $simplifiedNewLanguages[$personId][$lang[0]] = $priority;
        }
      }
    }
    // Unset foreach loop variables that are no longer in needed.
    unset($personId, $pLangs, $priority, $lang);

    // loop through and do an inner array diff
    foreach($currLanguages AS $personId => $pLangs)
    {
      // Compare personId and languageId between $newLanguages and $currLanguages.
      foreach ($pLangs AS $index => $lang)
      {
        // Append person's current language that's not found in the new language array to the 
        // new language array for further processing.
        if (!array_key_exists($lang[0], $simplifiedNewLanguages[$personId]))
        {
          $priority = count($newLanguages[$personId]) + 1;
          $newLanguages[$personId][$priority] = $lang;
        }
        // Unset to release memory
        unset($currLanguages[$personId][$index]);
      }
      // Unset to release memory
      unset($currLanguages[$personId]);
    }
    // Release memories.
    unset($personId, $pLangs, $index, $lang);
    unset($currLanguages, $simplifiedNewLanguages);

    return $newLanguages;
  }

  /**
   * Method to remove person language competency entries from a person.
   *
   * @param array $personIds A single-dimension array of personIds
   * @param boolean $throwOnError Boolean to control whether or not any errors will trigger an
   * exception throw. Defaults to the class property value.
   * @param Doctrine_Connection $conn An optional doctrine connection object.
   */
  protected function deletePersonLanguageCompetencies(array $personLanguageIds,
                                                      $throwOnError = NULL,
                                                      Doctrine_Connection $conn = NULL)
  {
    // explicit declarations are good!
    $results = 0;
    $err = NULL;

    // pick up some defaults if not passed anything
    if (is_null($throwOnError)) { $throwOnError = $this->throwOnError; }
    if (is_null($conn)) { $conn = Doctrine_Manager::connection(); }

    // build our query object
    $q = agDoctrineQuery::create($conn)
                 ->delete('agPersonLanguageCompetency')
                 ->whereIn('person_language_id', $personLanguageIds);

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

    try
    {
      // execute our person language competency mapping join purge
      $results = $q->execute();

      // most excellent! no errors at all, so we commit... finally!
      if ($useSavepoint) { $conn->commit(__FUNCTION__) ; } else { $conn->commit(); }
    }
    catch(Exception $e)
    {
      // log our error
      $errMsg = sprintf('Failed to purge person language competency for persons %s. Rolled back changes!',
        json_encode($personLanguageIds)) ;
      sfContext::getInstance()->getLogger()->err($errMsg);

      // rollback
      if ($useSavepoint) { $conn->rollback(__FUNCTION__); } else { $conn->rollback(); }

      // ALWAYS throw an error, it's like stepping on a crack if you don't
      if ($throwOnError) { throw $e; }

      $results['failures'] = array_keys($personLanguages);
    }

    return $results;
  }

  /**
   * Method to remove person language entries from a person.
   *
   * @param array $personIds A single-dimension array of personIds
   * @param boolean $throwOnError Boolean to control whether or not any errors will trigger an
   * exception throw. Defaults to the class property value.
   * @param Doctrine_Connection $conn An optional doctrine connection object.
   */
  protected function deletePersonLanguages(array $personLanguageIds,
                                                 $throwOnError = NULL,
                                                 Doctrine_Connection $conn = NULL)
  {
    // explicit declarations are good!
    $results = 0;
    $err = NULL;

    // pick up some defaults if not passed anything
    if (is_null($throwOnError)) { $throwOnError = $this->throwOnError; }
    if (is_null($conn)) { $conn = Doctrine_Manager::connection(); }

    // Delete from child table.
    $this->deletePersonLanguageCompetencies($personLanguageIds, $throwOnError, $conn);


    // build our query object
    $q = agDoctrineQuery::create($conn)
                 ->delete('agPersonMjAgLanguage')
                 ->whereIn('id', $personLanguageIds);

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

    try
    {
      // execute our person language competency mapping join purge
      $results = $q->execute();

      // most excellent! no errors at all, so we commit... finally!
      if ($useSavepoint) { $conn->commit(__FUNCTION__) ; } else { $conn->commit(); }
    }
    catch(Exception $e)
    {
      // log our error
      $errMsg = sprintf('Failed to purge person\'s languages for persons %s. Rolled back changes!',
        json_encode($personLanguageIds)) ;
      sfContext::getInstance()->getLogger()->err($errMsg);

      // rollback
      if ($useSavepoint) { $conn->rollback(__FUNCTION__); } else { $conn->rollback(); }

      // ALWAYS throw an error, it's like stepping on a crack if you don't
      if ($throwOnError) { throw $e; }

      $results['failures'] = array_keys($personLanguages);
    }

    return $results;
  }

}
