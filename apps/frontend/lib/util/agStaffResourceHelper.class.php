<?php

/**
 * agStaffResourceHelper this class extends the bulk record helper
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author     Shirley Chan, CUNY SPS
 * @author     Chad Heuschober, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 *
 */
class agStaffResourceHelper extends agBulkRecordHelper
{
  public    $defaultIsStrType = FALSE,
            $agPersonNameHelper ;

  /**
   * Method to construct the staff resource components query.
   *
   * @param array $staffResourceIds A single-dimension array of staff resource IDs. If left NULL
   * reverts to the $recordIds class property.
   * @param boolean $strType Boolean value to determine whether or not edge table values will be
   * returned as string values or their integer ID equivalent.
   * @return Doctrine_Query An agDoctrineQuery object.
   */
  protected function _getStaffResourceComponents($staffResourceIds = NULL, $strType = NULL)
  {
    // pick up the default Id's and safe single values as arrays
    $staffResourceIds = $this->getRecordIds($staffResourceIds) ;

    // if strType is not passed, get the default
    if (is_null($strType)) { $strType = $this->defaultIsStrType ; }

    // create the basic query object
    $q = agDoctrineQuery::create()
      ->select('sr.id')
          ->addSelect('s.id')
          ->addSelect('s.person_id')
        ->from('agStaffResource sr')
          ->innerJoin('sr.agStaff s')
        ->whereIn('sr.id', $staffResourceIds) ;

    // add components based on whether or not values will be returned as strings or ids
    if ($strType)
    {
      $q->addSelect('srt.staff_resource_type_abbr')
        ->addSelect('ss.staff_status')
        ->innerJoin('sr.agStaffResourceType srt')
        ->innerJoin('s.agStaffStatus ss') ;
    }
    else
    {
      $q->addSelect('sr.staff_resource_type_id')
        ->addSelect('s.staff_status_id') ;
    }

    return $q ;
  }

  /**
   * Method to lazily load the $agPersonNameHelper class property (an instance of
   * agPersonNameHelper).
   * @return object The instantiated agPersonNameHelper object
   */
  public function getAgPersonNameHelper()
  {
    // if our object does not yet exist, instantiate it
    if (! isset($this->agPersonNameHelper))
    {
      $this->agPersonNameHelper = agPersonNameHelper::init() ;
    }

    // return the object itself to the call
    return $this->agPersonNameHelper ;
  }

  /**
   * Method to return information about a group of staff resources.
   *
   * @param array $staffResourceIds A single-dimension array of staff resource IDs. If left NULL
   * reverts to the $recordIds class property.
   * @param boolean $strType Boolean value to determine whether or not edge table values will be
   * returned as string values or their integer ID equivalent.
   * @param string $nameMethod A string representing an agPersonNameHelper method name. Should be
   * sourced from the agPersonNameHelper constants.
   * @param array $nameArguments An array of arguments to pass to the $nameMethod.
   * @return array An associative array, keyed by staff_resource_id, of staff resource components.
   */
  public function getStaffResourceComponents( $staffResourceIds = NULL,
                                              $strType = NULL,
                                              $nameMethod = NULL,
                                              $nameArguments = array())
  {
    // always set our default results so we have something to return
    $results = array() ;
    $helperArgs = array() ;

    // construct our query
    $q = $this->_getStaffResourceComponents($staffResourceIds, $strType) ;

    // loop through using custom hydration so we can double-tap the data
    $rows = $q->execute(array(), Doctrine_Core::HYDRATE_NONE) ;
    foreach ($rows as $row)
    {
      // here we use static key values for our indexed returns so that type & status are neutral
      $results[$row[0]]['staff_id'] = $row[1] ;
      $results[$row[0]]['person_id'] = $row[2] ;
      $results[$row[0]]['type'] = $row[3] ;
      $results[$row[0]]['status'] = $row[4] ;

      // if this is not null, it'd be nice to have a stack of personId's in a single dimension array
      if (! is_null($nameMethod))
      {
        // the first arg [0] is always the array of personId's follow?
        $helperArgs[0][] = $row[2] ;
      }
    }

    // assume we're done if no name method is passed
    if (is_null($nameMethod))
    {
      return $results ;
    }

    // otherwise we continue and start by loading our name helper
    $nameHelper = $this->getAgPersonNameHelper() ;

     // finish appending the rest of our name helper args
    foreach ($nameArguments as $arg)
    {
      $helperArgs[] = $arg ;
    }

    // use the name helper to return formatted names
    $userFunc = array($nameHelper,$nameMethod) ;
    $formattedNames = call_user_func_array($userFunc,$helperArgs) ;

    // loop through our original results
    foreach ($results as $staffResourceId => $components)
    {
      // if the names return shows that the person has a name, add it to our results
      if (array_key_exists($components['person_id'],$formattedNames))
      {
        $results[$staffResourceId]['name'] = $formattedNames[$components['person_id']] ;
      }
    }

    return $results ;
  }
}