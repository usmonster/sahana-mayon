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
class agLanguageHelper extends agBulkRecordHelper
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
   * A quick helper method to take in an array language and return an array of language ids.
   * @param array $languages A monodimensional array of languages.
   * @return array An associative array, keyed by language, with a value of language_id.
   */
  public function getLanguageIds($languages)
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
  public function getLanguageFormatIds($languageFormats)
  {
    return agDoctrineQuery::create()
      ->select('lf.language_format')
          ->addSelect('lf.id')
        ->from('agLanguageFormat lf')
        ->whereIn('lf.language_format', $languageFormats)
      ->useResultCache(TRUE, 3600, __FUNCTION__)
      ->execute(array(), agDoctrineQuery::HYDRATE_KEY_VALUE_PAIR);
  }

  /**
   * Method to return language competency ids from language competency values.
   * @param array $language_competency An array of language_competency
   * @return array An associative array of language competency ids keyed by language competency.
   */
  public function getLanguageCompetencyIds($languageCompetencies)
  {
    return agDoctrineQuery::create()
      ->select('lc.language_competency')
          ->addSelect('lc.id')
        ->from('agLanguageCompetency lc')
        ->whereIn('lc.language_competency', $languageCompetencies)
      ->useResultCache(TRUE, 3600, __FUNCTION__)
      ->execute(array(), agDoctrineQuery::HYDRATE_KEY_VALUE_PAIR);
  }

  /**
   * Method to create new person language.
   *
   * @param array $newLanguages A single dimension array of languages.
   * @param boolean $throwOnError Boolean to control whether or not failures trigger exceptions.
   * Defaults to class property.
   * @param Doctrine_Connection $conn An optional doctrine connection object.
   * @return array An associative array keyed by the language string with the languageId as a value.
   */
  protected function setNewLanguages($newLanguages,
                                     $throwOnError = NULL,
                                     Doctrine_Connection $conn = NULL)
  {
    // explicit delcarations are nice
    $results = array();
    $err = NULL;

    // pick up our default connection / transaction objects if not passed anything
    if (is_null($throwOnError)) { $throwOnError = $this->throwOnError; }
    if (is_null($conn)) { $conn = Doctrine_Manager::connection(); }

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

    foreach ($newLanguages as $language)
    {
      $newRec = new agLanguage();
      $newRec['language'] = $language;

      try
      {
        $newRec->save($conn);
        $results[$language] = $newRec->getId();
      }
      catch(Exception $e)
      {
        // log our error
        $errMsg = sprintf('Couldn\'t insert language value %s. Rolled back changes!', $language);

        // capture our exception for a later throw and break out of this loop
        $err = $e;
        break;
      }
    }

    if (is_null($err))
    {
      // yay, it all went well so let's commit!
      if ($useSavepoint) { $conn->commit(__FUNCTION__); } else { $conn->commit(); }
    }
    else
    {
      // log our error
      sfContext::getInstance()->getLogger()->err($errMsg);

      // rollback
      if ($useSavepoint) { $conn->rollback(__FUNCTION__); } else { $conn->rollback(); }

      // throw an error if directed to do so
      if ($throwOnError) { throw $err; }
    }

    return $results;
  }

  /**
   * Method to create new person language format.
   *
   * @param array $newFormats A single dimension array of language formats.
   * @param boolean $throwOnError Boolean to control whether or not failures trigger exceptions.
   * Defaults to class property.
   * @param Doctrine_Connection $conn An optional doctrine connection object.
   * @return array An associative array keyed by the language format string with the languageFormatId as a value.
   */
  protected function setNewLanguageFormats($newFormats,
                                           $throwOnError = NULL,
                                           Doctrine_Connection $conn = NULL)
  {
    // explicit delcarations are nice
    $results = array();
    $err = NULL;

    // pick up our default connection / transaction objects if not passed anything
    if (is_null($throwOnError)) { $throwOnError = $this->throwOnError; }
    if (is_null($conn)) { $conn = Doctrine_Manager::connection(); }

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

    foreach ($newFormats as $format)
    {
      $newRec = new agLanguageFormat();
      $newRec['language_format'] = $format;

      try
      {
        $newRec->save($conn);
        $results[$format] = $newRec->getId();
      }
      catch(Exception $e)
      {
        // log our error
        $errMsg = sprintf('Couldn\'t insert language format value %s. Rolled back changes!', $format);

        // capture our exception for a later throw and break out of this loop
        $err = $e;
        break;
      }
    }

    if (is_null($err))
    {
      // yay, it all went well so let's commit!
      if ($useSavepoint) { $conn->commit(__FUNCTION__); } else { $conn->commit(); }
    }
    else
    {
      // log our error
      sfContext::getInstance()->getLogger()->err($errMsg);

      // rollback
      if ($useSavepoint) { $conn->rollback(__FUNCTION__); } else { $conn->rollback(); }

      // throw an error if directed to do so
      if ($throwOnError) { throw $err; }
    }

    return $results;
  }

  /**
   * Method to create new person language competency.
   *
   * @param array $newCompetencies A single dimension array of language competencies.
   * @param boolean $throwOnError Boolean to control whether or not failures trigger exceptions.
   * Defaults to class property.
   * @param Doctrine_Connection $conn An optional doctrine connection object.
   * @return array An associative array keyed by the language competency string with the
   * languageCompetencyId as a value.
   */
  protected function setNewLanguageCompetencies($newCompetencies,
                                                $throwOnError = NULL,
                                                Doctrine_Connection $conn = NULL)
  {
    // explicit delcarations are nice
    $results = array();
    $err = NULL;

    // pick up our default connection / transaction objects if not passed anything
    if (is_null($throwOnError)) { $throwOnError = $this->throwOnError; }
    if (is_null($conn)) { $conn = Doctrine_Manager::connection(); }

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

    foreach ($newCompetencies as $competency)
    {
      $newRec = new agLanguageCompetency();
      $newRec['language_competency'] = $competency;

      try
      {
        $newRec->save($conn) ;
        $results[$competency] = $newRec->getId();
      }
      catch(Exception $e)
      {
        // log our error
        $errMsg = sprintf('Couldn\'t insert language competency value %s. Rolled back changes!', $competency);

        // capture our exception for a later throw and break out of this loop
        $err = $e;
        break;
      }
    }

    if (is_null($err))
    {
      // yay, it all went well so let's commit!
      if ($useSavepoint) { $conn->commit(__FUNCTION__); } else { $conn->commit(); }
    }
    else
    {
      // log our error
      sfContext::getInstance()->getLogger()->err($errMsg);

      // rollback
      if ($useSavepoint) { $conn->rollback(__FUNCTION__); } else { $conn->rollback(); }

      // throw an error if directed to do so
      if ($throwOnError) { throw $err; }
    }

    return $results;
  }

}
