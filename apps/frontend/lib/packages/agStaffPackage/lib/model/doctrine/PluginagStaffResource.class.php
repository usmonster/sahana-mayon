<?php

/**
 * PluginagStaffResource
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class PluginagStaffResource extends BaseagStaffResource
{
  /**
   * Method to return a system-wide staff resource query with top priority digital contacts and
   * statuses. This method is expected to return a query object that can be extended / added to
   * for additional functionality such as limits and offsets and/or sorting.
   *
   * To sort, add a doctrine orderBy clause using the header name you wish to sort by.
   *
   * <code>
   * $query = agStaffResource::getStaffResourceQuery();
   * $headers = $query[0];
   * $query = $query[1];
   *
   * $query->orderBy($headers[$column][0] . ' ' $sortDirection);
   * $query->execute(array(), Doctrine_Core::HYDRATE_SCALAR);
   * </code>
   *
   * To limit, apply the doctrine limit and offset clauses, respectively.
   *
   * <code>
   * $query = agStaffResource::getStaffResourceQuery();
   * $headers = $query[0];
   * $query = $query[1];
   *
   * $query->limit($recordLimit);
   * $query->offset($recordOffset);
   * $query->execute(array(), Doctrine_Core::HYDRATE_SCALAR);
   * </code>
   *
   *    * @return array An array of headers and the query object.
   * <code>
   *   array( $headers = array( 'column_array_key' => array('columnName', 'Label')),
   *          $agDoctrineQueryObject
   *   )
   * </code>
   */
  public static function getStaffResourceQuery()
  {
    $headers = array();

    // build our basic query (that doesn't need fancy stuff)
    $q = agDoctrineQuery::create()
        ->select('agStaffResource.id')
        ->addSelect('s.id')
        ->addSelect('p.id')
        ->addSelect('e.id')
        ->addSelect('agStaffResourceType.staff_resource_type')
        ->addSelect('agStaffResourceType.staff_resource_type_abbr')
        ->addSelect('agStaffResourceStatus.staff_resource_status')
        ->addSelect('agStaffResourceStatus.is_available')
        ->addSelect('eec.id')
        ->addSelect('ect.email_contact_type')
        ->addSelect('agOrganization.organization')
        ->addSelect('ec.email_contact')
        ->addSelect('epc.id')
        ->addSelect('pct.phone_contact_type')
        ->addSelect('pc.phone_contact')
        ->from('agStaffResource agStaffResource')
        ->innerJoin('agStaffResource.agStaff AS s')
        ->innerJoin('s.agPerson AS p')
        ->innerJoin('p.agEntity AS e')
        ->innerJoin('agStaffResource.agStaffResourceType agStaffResourceType')
        ->innerJoin('agStaffResource.agOrganization agOrganization')
        ->innerJoin('agStaffResource.agStaffResourceStatus agStaffResourceStatus')
        ->leftJoin('e.agEntityEmailContact AS eec')
        ->leftJoin('eec.agEmailContactType AS ect')
        ->leftJoin('eec.agEmailContact AS ec')
        ->leftJoin('e.agEntityPhoneContact AS epc')
        ->leftJoin('epc.agPhoneContactType AS pct')
        ->leftJoin('epc.agPhoneContact AS pc');

    $emailWhere = '(' .
        '(EXISTS (' .
        'SELECT subE.id ' .
        'FROM agEntityEmailContact AS subE ' .
        'WHERE subE.entity_id = eec.entity_id ' .
        'HAVING MIN(subE.priority) = eec.priority' .
        ')) ' .
        'OR (eec.id IS NULL)' .
        ')';
    $q->where($emailWhere);

    $phoneWhere = '(' .
        '(EXISTS (' .
        'SELECT subP.id ' .
        'FROM agEntityPhoneContact AS subP ' .
        'WHERE subP.entity_id = epc.entity_id ' .
        'HAVING MIN(subP.priority) = epc.priority' .
        ')) ' .
        'OR (epc.id IS NULL)' .
        ')';
    $q->andWhere($phoneWhere);

    // map the headers
    $headers['agStaffResource_id'] = array('agStaffResource.id', 'Staff Resource ID');
    $headers['s_id'] = array('s.id', 'Staff ID');
    $headers['p_id'] = array('p.id', 'Person ID');
    $headers['e_id'] = array('e.id', 'Entity ID');
    $headers['agStaffResourceType_staff_resource_type'] = array('agStaffResourceType.staff_resource_type', 'Staff Resource Type');
    $headers['agStaffResourceType_staff_resource_type_abbr'] = array('agStaffResourceType.staff_resource_type_abbr',
      'Type');
    $headers['agOrganization_organization'] = array('agOrganization.organization', 'Organization');
    $headers['agStaffResourceStatus_staff_resource_status'] = array('agStaffResourceStatus.staff_resource_status',
      'Staff Resource Status');
    $headers['agStaffResourceStatus_is_available'] = array('agStaffResourceStatus.is_available', 'Available?');
    $headers['ect_email_contact_type'] = array('ect.email_contact_type', 'E-Mail Type');
    $headers['ec_email_contact'] = array('ec.email_contact', 'E-Mail');
    $headers['pct_phone_contact_type'] = array('pct.phone_contact_type', 'Phone Type');
    $headers['pc_phone_contact'] = array('pc.phone_contact', 'Phone');


    // do this to get the ID types ordered property
    $nameHelper = new agPersonNameHelper();
    $nameComponents = $nameHelper->defaultNameComponents;
    unset($nameHelper);

    // do this to get the string types, again ordered properly
    $nameTypes = json_decode(agGlobal::getParam('default_name_components'));

    // loop through each of the name types
    foreach ($nameComponents as $ncIdx => $nc) {
      // grab our type id
      $ncId = $nc[0];

      // build the clause strings
      $selectId = 'pmpn' . $ncId . '.id';
      $column = 'pn' . $ncId . '.person_name';
      $select = $column . ' AS name' . $ncId;
      $pmpnJoin = 'p.agPersonMjAgPersonName AS pmpn' . $ncId . ' WITH pmpn' . $ncId .
          '.person_name_type_id = ?';
      $pnJoin = 'pmpn' . $ncId . '.agPersonName AS pn' . $ncId;

      $where = '(' .
          '(EXISTS (' .
          'SELECT sub' . $ncId . '.id ' .
          'FROM agPersonMjAgPersonName AS sub' . $ncId . ' ' .
          'WHERE sub' . $ncId . '.person_name_type_id = ? ' .
          'AND sub' . $ncId . '.person_id = pmpn' . $ncId . '.person_id ' .
          'HAVING MIN(sub' . $ncId . '.priority) = pmpn' . $ncId . '.priority' .
          ')) ' .
          'OR (pmpn' . $ncId . '.id IS NULL)' .
          ')';

      // add the clauses to the query
      $q->addSelect($selectId)
          ->addSelect($select)
          ->leftJoin($pmpnJoin, $ncId)
          ->leftJoin($pnJoin)
          ->andWhere($where, $ncId);

      // add header information
      $header = 'pn' . $ncId . '_name' . $ncId;
      $headers[$nameTypes[$ncIdx][0]] = array($column, $header);
      //05.23.2011 changed the order of the name array holder to use for sorting
    }
    return array($headers, $q);
  }

  /**
   * Method to set all active staff in the system to a disabled status
   * @param Doctrine_Connection $conn An optional doctrine connection object.
   * @return integer Returns the number of updates performed.
   */
  public static function disableAllStaff(Doctrine_Connection $conn = NULL)
  {
    $results = 0;

    // get our disabled status
    $disabledStatus = agGlobal::getParam('staff_disabled_status');
    $disabledStatusId = agDoctrineQuery::create()
        ->select('srs.id')
        ->from('agStaffResourceStatus srs')
        ->where('srs.staff_resource_status = ?', $disabledStatus)
        ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);

    // build our update query
    $q = agDoctrineQuery::create()
        ->update('agStaffResource')
        ->set('staff_resource_status_id', '?', $disabledStatusId)
        ->where('staff_resource_status_id <> ?', $disabledStatusId);

    // here we check our current transaction scope and create a transaction or savepoint
    if (is_null($conn)) {
      $conn = Doctrine_Manager::connection();
    }
    $useSavepoint = ($conn->getTransactionLevel() > 0) ? TRUE : FALSE;
    if ($useSavepoint) {
      $conn->beginTransaction(__FUNCTION__);
    } else {
      $conn->beginTransaction();
    }

    try {
      // attempt to execute our query and commit
      $results = $q->execute();
      if ($useSavepoint) {
        $conn->commit(__FUNCTION__);
      } else {
        $conn->commit();
      }
    } catch (Exception $e) {
      // log our error message
      $errMsg = 'Failed to reset staff statuses to ' . $disabledStatus . '. Rolling back.';
      sfContext::getInstance()->getLogger()->err($errMsg);

      // roll back and rethrow
      if ($useSavepoint) {
        $conn->rollback(__FUNCTION__);
      } else {
        $conn->rollback();
      }
      throw $e;
    }
    return $results;
  }

  /**
   * Returns an integer of the total staff count.
   * @return integer An integer with the total number of staff
   * @deprecated How is this a useful number without some context (eg, active, etc)?
   */
  static public function returnStaffResourceCount()
  {
    $staffCount = agDoctrineQuery::create()
        ->select('count(*) as count')
        ->from('agStaffResource as s')
        ->execute(array(), Doctrine::HYDRATE_SINGLE_SCALAR);
    return $staffCount;
  }
}