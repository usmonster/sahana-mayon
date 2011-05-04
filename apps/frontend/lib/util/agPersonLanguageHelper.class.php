<?php

/**
 * Provides person language helper functions and inherits several methods and properties from the
 * bulk record helper.
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
  public function __construct($personIds = NULL)
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
  protected function _getLanguageComponents($personIds = NULL, $primary = TRUE)
  {
    $personIds = $this->getRecordIds($personIds) ;

    $q = agDoctrineQuery::create()
      ->select('pml.person_id')
          ->addSelect('l.language')
          ->addSelect('plc.language_format_id')
          ->addSelect('plc.language_competency_id')
        ->from('agPersonMjAglanguage pml')
          ->innerJoin('pml.agLanguage l')
          ->leftJoin('pml.agPersonLanguageCompetency plc')
        ->whereIn('pml.person_id', $personIds)
        ->orderBy('pml.priority ASC, plc.language_format_id') ;

    if ($primary)
    {
      $q->andwhere(' EXISTS (
        SELECT p.id
        FROM agPersonMjAgLanguage p
        WHERE p.person_id = pml.person_id
          AND p.language_id = pml.language_id
        HAVING MIN(p.priority) = pml.priority)') ;
    }

    return $q ;
  }
  
  /**
   * Method to retrieve person's language, format, and competency information.
   * 
   * @param array $personIds A single-dimension array of person id values. Default is NULL.
   * @param boolean $primary A boolean that controls whether or not the query constructor will
   * build a query that only returns primary names or all names.
   * @return array A multi-dimensional associative array keyed by person id, 
   * ordering index, and format id.
   * <code>
   * array( personID => 
   *            array( index => array( [0] => langauge,
   *                                   [1] => array( formatId => competencyId 
   *                                                 ... )
   *                                 )
   *                   ... )
   *      ...)
   * </code>      
   */
  public function getPersonLanguageById($personIds = NULL, $primary = TRUE)
  {
    $results = array();
    $q = $this->_getLanguageComponents($personIds, $primary);

    $rows = $q->execute(array(), Doctrine_Core::HYDRATE_NONE) ;

    foreach ($rows AS $row)
    {
      // Check whether or not the person already has other associated language
      // stored in $results.
      if (isset($results[$row[0]]))
      { // Person is multilingual.
        $rowLang = $row[1];
        $personLanguages = $results[$row[0]];
        $idx = NULL;

        // Search for index to insert into $result.  Appending to sub array if
        // person already has a format defined for the language.  Otherwise,
        // add new language and format to person.
        foreach ($personLanguages AS $index => $pLangs)
        {
          if ($pLangs[0] == $rowLang)
          {
            $idx = $index;
            break;
          }
        }

        if (isset($idx))
        {
          $results[$row[0]][$idx][1][$row[2]] = $row[3];
        }
        else
        {
          $results[$row[0]][] = array($row[1], array($row[2] => $row[3]));
        }
      }
      else
      {
        // First language encounter for person.  Save initial language to
        // results.
        //
        // Check whether or not formats and competency level is defined for the
        // person's language.
        $formatComponent = is_null($row[2]) ? array() : array($row[2] => $row[3]);
        $results[$row[0]][] = array($row[1], $formatComponent);
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
  private function diffArrays(&$compareFrom, &$keyArray)
  {
    // Sort array to speed up comparisons.
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
   * <code>
   * array( personID =>
   *            array( index => array( [0] => langauge,
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
  public function setPersonLanguages( $personLanguages,
                                  $keepHistory = NULL,
                                  $throwOnError = NULL,
                                  $createNewEdgeValues = FALSE,
                                  Doctrine_Connection $conn = NULL)
  {
    // explicit declarations are nice
    $results = array();
    $invalidLanguages = array();
    $uniqLanguages = array();
    $uniqFormats = array();
    $uniqCompetencies = array();
    $err = NULL;

    // pick up some defaults if not passed anything
    if (is_null($conn)) { $conn = Doctrine_Manager::connection(); }

    // reduce our person language to just the uniques
    foreach ($personLanguages AS $personId => &$languageComponents)
    {
      foreach ($languageComponents AS $priority => &$langComps)
      {
        foreach ($langComps AS &$lang)
        {
          // we do this at this early stage so we're always dealing with a post-trimmed value
          $lang[0] = trim($lang[0]);

          // add it to our unique array
          $uniqLanguages[] = $lang[0];

          foreach($lang[1] AS $format => &$competency)
          {
            // Always trim value
            $format = trim($format);
            $competency = trim($competency);

            // Add to unique arrays
            $uniqFormats[] = $format;
            $uniqCompetencies[] = $competency;
          }
        }
      }
    }

    // actually make these unqiue arrays unique
    $uniqLanguages = array_unique($uniqLanguages);
    $uniqFormats = array_unique($uniqFormats);
    $uniqCompetencies = array_unique($uniqCompetencies);

    // get existing values
    $languageIds = $this->getLanguageIds($uniqLanguages);
    $formatIds = $this->getLanguageFormatIds($uniqFormats);
    $competencyIds =$this->getLanguageCompetencyIds($uniqCompetencies);

    // Find new languages by diffing the arrays.
    $newLanguages = $this->diffArray($uniqLanguages, $languageIds);
    unset($uniqLanguages);

    // Find new formats by diffing the arrays.
    $newFormats = array_diff($uniqFormats, $formatIds);
    unset($uniqFormats);

    // Find new formats by diffing the arrays.
    $newCompetencies = array_diff($uniqCompetencies, $competencyIds);
    unset($uniqCompetencies);

    // Check whether or not new edge values are allowed.
    if (!$createNewEdgeValues)
    {
      $errMsg = NULL;
      if (count($newLanguages) > 0)
      {
        $errMsg = sprintf('Invalid languauges (%s)', implode(', ', $newLanguages));

        // log whichever error we received
        sfContext::getInstance()->getLogger()->err($errMsg);

        // throw the exception we promised in our boolean
        if ($throwOnError) { throw new Exception($errMsg); }
      }

      if (count($newFormats) > 0)
      {
        $errMsg = sprintf('Invalid language formats (%s)', implode(', ', $newFormats));

        // log whichever error we received
        sfContext::getInstance()->getLogger()->err($errMsg);

        // throw the exception we promised in our boolean
        if ($throwOnError) { throw new Exception($errMsg); }
      }

      if (count($newCompetencies) > 0)
      {
        $errMsg = sprintf('Invalid language competencies (%s)', implode(', ', $newCompetencies));

        // log whichever error we received
        sfContext::getInstance()->getLogger()->err($errMsg);

        // throw the exception we promised in our boolean
        if ($throwOnError) { throw new Exception($errMsg); }
      }

      // Create new edge values if $createNewEdgeValues is TRUE
      if ($createNewEdgeValues)
      {
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
          // set new languagess / return their ids
          $languageIds = ($languageIds + ($this->setNewLanguages($newLanguages, $throwOnError, $conn)));
          unset($newLanguages);

          // set new language formats / return their ids
          $formatIds = ($formatIds + ($this->setNewLanguageFormats($newFormats, $throwOnError, $conn)));
          unset($newFormats);

          // set new language competency / return their ids
          $competencyIds = ($competencyIds + ($this->setNewLanguageCompetencies($newCompetencies, $throwOnError, $conn)));
          unset($newCompetencies);
        }
        catch(Exception $e)
        {
          // log our error
          $errMsg = sprintf('%s failed to execute. (%s).', __FUNCTION__, $e->getMessage());

          // capture the error for comparison later
          $err = $e;
        }
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
          if (array_key_exists($languageIds[$langComps[0]]))
          {
            foreach ($langComps[1] AS $lang)
            {
              foreach ($lang AS $format => $competency)
              {
                if (array_key_exists($format, $formatIds) && array_key_exists($competency, $competencyIds))
                {
                  $personLanguages[$personId][$priority][1][$formatIds[$format]] = $competencyIds[$competency];
                }
                else
                {
                  // Capture invalid format/competency records.
                  if (isset($invalidLanguages[$personId]))
                  {
                    $invalidLanguage[$personId][$priority][1][$formatIds[$format]] = $competencyIds[$competency];
                  }
                  else 
                  {
                    $invalidLanguages[$personId][$priority] = array($langComp[1], array($format => $competency));
                  }
                  unset($personLanguages[$personId][$priority][1][$format]);
                }
              } // end of language format/competency loop
            } // end of language and its component loop
          }
          else
          {
            // Invalid language.
            $invalidLanguages[$personId][$priority] = $langComps;
            unset($personLanguages[$personId][$priority]);
          }
        }
      }

      // At this point $personLanguages only consist of valid languages components.

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
   * <code>
   * array( personID =>
   *            array( index => array( [0] => langauge,
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
  protected function _setPersonLanguage ($personLanguages,
                                         $keepHistory = NULL,
                                         $throwOnError = NULL,
                                         Doctrine_Connection $conn = NULL)
  {
    // explicit results declaration
    $results = array('upserted'=>0, 'removed'=>0, 'failures'=>array()) ;
    $currLanguages = array() ;

    // get some defaults if not explicitly passed
    if (is_null($keepHistory)) { $keepHistory = $this->keepHistory ; }
    if (is_null($throwOnError)) { $throwOnError = $this->throwOnError ; }
    if (is_null($conn)) { $conn = Doctrine_Manager::connection() ; }

    if ($keepHistory)
    {
      // if we're going to process existing languages and keep them, then hold on
      $currLanguages = $this->getPersonLanguageById(array_keys($personLanguages), FALSE) ;
    }

    // execute the reprioritization helper and pass it our current addresses as found in the db
    $personLanguages = $this->reprioritizePersonLanguages($personLanguages, $currLanguages ) ;

    // define our blank collection
    $coll = new Doctrine_Collection('agPersonMjAgLanguage') ;


    /**
     * @TODO Reprioritizing person language and upserting records in db using collection to keep
     * person_language_id for existing records if possible.  Store new records' person_language_ids
     * for later upserts on ag_person_language_competency table.
     */

    return $results ;
  }

  /**
   * Method to prioritize person languages.
   *
   * @param array $newLanguages A multi-dimesional array of person languages similar to the
   * output of getLanguageById
   * <code>
   * <code>
   * array( personID =>
   *            array( index => array( [0] => langauge,
   *                                   [1] => array( formatId => competencyId
   *                                                 ... )
   *                                 )
   *                   ... )
   *      ...)
   * </code>
   * @param array $currLanguages A multi-dimesional array of person languages similar to the
   * output of getLanguageById
   * <code>
   * <code>
   * array( personID =>
   *            array( index => array( [0] => langauge,
   *                                   [1] => array( formatId => competencyId
   *                                                 ... )
   *                                 )
   *                   ... )
   *      ...)
   * </code>
   * @return array $newLanguages A multi-dimesional array of person languages similar to the
   * output of getLanguageById
   * <code>
   * <code>
   * array( personID =>
   *            array( index => array( [0] => langauge,
   *                                   [1] => array( formatId => competencyId
   *                                                 ... )
   *                                 )
   *                   ... )
   *      ...)
   * </code>
   */
  protected function reprioritizePersonLanguages( $newLanguages, $currLanguages )
  {
    // loop through and do an inner array diff
    foreach($newLanguages as $personId => $pLangs)
    {
      // @TODO
      // Compare personId and languageId between $newLanguages and $currLanguages.
      // Append $currLanguage to $newLanguages if not found in $newLanguages.
    }

    return $newLanguages ;
  }

}
