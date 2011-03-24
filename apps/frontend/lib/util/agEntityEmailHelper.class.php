<?php
/**
 * Provides entity email helper functions and inherits several methods and properties from the
 * bulk record helper.
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 *
 * @author Shirley Chan, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
class agEntityEmailHelper extends agBulkRecordHelper
{
  public    $defaultIsPrimary = FALSE,
            $defaultIsStrType = FALSE;

  protected $_batchSizeModifier = 2;


  /**
   * Method to return an agDocrineQuery object, preconfigured to collect entity emails.
   *
   * @param array $entityIds A single dimension array of entityId's that will be queried.
   * @param boolean $strType Boolean that determines whether or not the email contact type will
   * be returned as an ID value or its string equivalent.
   * @return Doctrine_Query An agDoctrineQuery object.
   */
  private function _getEntityEmailQuery($entityIds = NULL, $strType = NULL)
  {
    // if no (null) ID's are passed, get the entityIds from the class property
    $entityIds = $this->getRecordIds($entityIds);

    // if strType is not passed, get the default
    if (is_null($strType)) { $strType = $this->defaultIsStrType; }

    // the most basic version of this query
    $q = agDoctrineQuery::create()
       ->select('eec.entity_id')
         ->addSelect('ec.email_contact')
       ->from('agEntityEmailContact eec')
            ->innerJoin('eec.agEmailContact ec')
       ->whereIn('eec.entity_id', $entityIds)
       ->orderBy('eec.priority');

    // here we determine whether to return the email_contact_type_id or its string value
    if ($strType)
    {
      $q->addSelect('ect.email_contact_type')
        ->innerJoin('eec.agEmailContactType ect');
    } else {
      $q->addSelect('eec.email_contact_type_id');
    }

    return $q;
  }

    /**
   * Method to return entity emails for a group of entity ids, sorted from highest priority to
   * lowest priority, and grouped by the email contact type.
   *
   * @param array $entityIds A single dimension array of entityId's that will be queried.
   * @param boolean $strType Boolean that determines whether or not the email contact type will
   * be returned as an ID value or its string equivalent.
   * @param boolean $primary Boolean that determines whether or not only the primary email will
   * be returned (for that type).
   * @return array A two or three dimensional array (depending on the setting of the $primary
   * parameter), by entityId, by emailContactType.
   */
  public function getEntityEmailByType ($entityIds = NULL,
                                        $strType = NULL,
                                        $primary = NULL)
  {
    // initial results declarations
    $entityEmails = array();

    // if primary is not passed, get the default
    if (is_null($primary)) { $primary = $this->defaultIsPrimary; }

    // build our query object
    $q = $this->_getEntityEmailQuery($entityIds, $strType);

    // if this is a primary query we add the restrictor
    if ($primary)
    {
      $q->addWhere(' EXISTS (
        SELECT s.id
          FROM agEntityEmailContact s
          WHERE s.entity_id = eec.entity_id
            AND s.email_contact_type_id = eec.email_contact_type_id
          HAVING MIN(s.priority) = eec.priority )') ;
    }

    // build this as custom hydration to 'double tap' the data
    $rows = $q->execute(array(), Doctrine_Core::HYDRATE_NONE);
    $index = 0;
    $priorEntityId = '';
    $priorContactType = '';
    foreach ($rows as $row)
    {
      // if we're only returning the primary, change the third dimension from an array to a value
      // NOTE: because of the restricted query, we can trust there is only one component per type
      // in our output and safely make this assumption
      if ($primary)
      {
        $entityEmails[$row[0]][$row[2]][] = $row[1];
      }
      // if not primary, we have one more loop in our return for another array nesting
      else {
        if ($row[0] != $priorEntityId || $row[2] != $priorContactType) { $index = 0; }
        $entityEmails[$row[0]][$row[2]][$index++] = $row[1];
        $priorEntityId = $row[0];
        $priorContactType = $row[2];
      }
    }
    return $entityEmails ;
  }

    /**
   * Method to return entity emails for a group of entity ids, sorted from highest priority to
   * lowest priority.
   *
   * @param array $entityIds A single dimension array of entityId's that will be queried.
   * @param boolean $strType Boolean that determines whether or not the email contact type will
   * be returned as an ID value or its string equivalent.
   * @param boolean $primary Boolean that determines whether or not only the primary email will
   * be returned (for that type).
   * @return array A three dimensional array, by entityId, then indexed from highest priority
   * email to lowest, with a third dimension containing the email type as index[0], and the
   * email value as index[1].
   */
  public function getEntityEmail ($entityIds = NULL,
                                  $strType = NULL,
                                  $primary = NULL)
  {
    // initial results declarations
    $entityEmails = array();

    // if primary is not passed, get the default
    if (is_null($primary)) { $primary = $this->defaultIsPrimary; }

    // build our query object
    $q = $this->_getEntityEmailQuery($entityIds, $strType);

    // if this is a primary query we add the restrictor
    if ($primary)
    {
      $q->addWhere(' EXISTS (
        SELECT s.id
          FROM agEntityEmailContact s
          WHERE s.entity_id = eec.entity_id
          HAVING MIN(s.priority) = eec.priority )') ;
    }
    // build this as custom hydration to 'double tap' the data
    $rows = $q->execute(array(), Doctrine_Core::HYDRATE_NONE);

    $index = 0;
    $priorEntityId = '';
    $priorContactType = '';
    foreach ($rows as $row)
    {
      // if we're only returning the primary, change the second dimension from an array to a value
      // NOTE: because of the restricted query, we can trust there is only one component per type
      // in our output and safely make this assumption
      if ($primary) {
        $entityEmails[$row[0]][]= array($row[2],$row[1]);
      }
      // if not primary, we have one more loop in our return for another array nesting
      else {
        if ($row[0] != $priorEntityId || $row[2] != $priorContactType) { $index = 0; }
        $entityEmails[$row[0]][$index++] = array($row[2], $row[1]);
      }

    }

    return $entityEmails;
  }

}