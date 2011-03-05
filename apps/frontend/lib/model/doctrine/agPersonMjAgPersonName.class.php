<?php

/** 
 * Extends the BaseagPersonMjAgPersonName class and provides additional functionality
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
 */


class agPersonMjAgPersonName extends BaseagPersonMjAgPersonName
{
  /**
   * Method to return the primary names of all or specific persons
   * @param array $personIds An array of personId's to query
   * @return array A two dimensional associative array, keyed by person_id,  with a secondary key
   * of person_name_type and value of person_name.
   */
  public function getPrimary($personIds = NULL)
  {
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
          HAVING MIN(p.priority) = pmpn.priority)') ;

    // if searching for a particular person or persons, restrict here
    if (! is_null($personIds)) {
      $query->andWhereIn('pmpn.person_id', $personIds) ;
    }

    // execute and return our results
    $results = $query->execute(array(), 'assoc_two_dim') ;
    return $results ;
  }
}
