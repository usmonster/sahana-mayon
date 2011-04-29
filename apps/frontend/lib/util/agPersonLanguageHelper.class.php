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
class agPersonLanguageHelper extends agBulkRecordHelper
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

  public function getPersonLanguageById($personIds = NULL, $primary = TRUE)
  {
    $result = array();
    $q = $this->_getLanguageComponents($personIds, $primary);

    $rows = $q->execute(array(), Doctrine_Core::HYDRATE_NONE) ;

    foreach ($rows AS $row)
    {
      // Check whether or not formats and competency level is defined for the person's language.
      if (is_null($row[2]))
      {
        // No formats or competency level defined for language.  Thus, safe to save as empty array.
        $results[$row[0]][] = array($row[1], array());
      }
      else
      {
        if (isset($results[$row[0]]))
        {
          // Person is multilingual.
          $language = $rows[1];
          $pLangs = $results[$row[0]];
          $idx = array_walk($pLangs,
                            function($langs, $index, $lang) { if ($langs[0] == $lang) { echo $index; }},
                            $language );

          // Check whether or not the person already has other language(s) saved in $results.  If
          // so, append to the existing person's language array.  If not, add as new array language.
          if (isset($idx))
          {
            $comp =& $results[$row][$idx][1];
            $comp[$row[2]] = $row[3];
          }
          else
          {
            $results[$row[0]][] = array($row[1], array($row[2] => $row[3]));
          }
        }
        else
        {
          // First language encounter for person.  Save initial language to results.
          $results[$rows[0]][] = array($row[1], array($row[2] => $row[3]));
        }
      }
    }

    return $results;
  }

  /**
   * A quick helper method to take in an array language and return an array of language ids.
   * @param array $languages A monodimensional array of languages.
   * @return array An associative array, keyed by language, with a value of language_id.
   */
  static public function getLanguageIds($languages)
  {
    return agDoctrineQuery::create()
      ->select('l.language')
          ->addSelect('l.id')
        ->from('agLanguage l')
        ->whereIn('l.language', $languages)
      ->useResultCache(TRUE, 3600, __FUNCTION__)
      ->execute(array(), agDoctrineQuery::HYDRATE_KEY_VALUE_PAIR);
  }

  /**
   * Method to return language format ids from language format values.
   * @param array $languageFormats An array of language_format
   * @return array An associative array of language format ids keyed by language format.
   */
  static public function getLanguageFormatIds($languageFormats)
  {
    return agDoctrineQuery::create()
      ->select('lf.language_format')
          ->addSelect('lf.id')
        ->from('agLanguageFormat lf')
        ->whereIn('ect.email_contact_type', $languageFormats)
      ->useResultCache(TRUE, 3600, __FUNCTION__)
      ->execute(array(), agDoctrineQuery::HYDRATE_KEY_VALUE_PAIR);
  }

  /**
   * Method to return language competency ids from language competency values.
   * @param array $language_competency An array of language_competency
   * @return array An associative array of language competency ids keyed by language competency.
   */
  static public function getLanguageCompetencyIds($languageCompetencys)
  {
    return agDoctrineQuery::create()
      ->select('lc.languagen_competency')
          ->addSelect('lc.id')
        ->from('agLanguageCompetency lc')
        ->whereIn('lc.langauge_competency', $languageCompetencys)
      ->useResultCache(TRUE, 3600, __FUNCTION__)
      ->execute(array(), agDoctrineQuery::HYDRATE_KEY_VALUE_PAIR);
  }
}
