<?php

/**
 * BaseagEventAuditSql
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $event_audit_id
 * @property gzip $sql_cmd
 * @property agEventAudit $agEventAudit
 * @property Doctrine_Collection $agEventAuditValue
 * 
 * @method integer             getId()                Returns the current record's "id" value
 * @method integer             getEventAuditId()      Returns the current record's "event_audit_id" value
 * @method gzip                getSqlCmd()            Returns the current record's "sql_cmd" value
 * @method agEventAudit        getAgEventAudit()      Returns the current record's "agEventAudit" value
 * @method Doctrine_Collection getAgEventAuditValue() Returns the current record's "agEventAuditValue" collection
 * @method agEventAuditSql     setId()                Sets the current record's "id" value
 * @method agEventAuditSql     setEventAuditId()      Sets the current record's "event_audit_id" value
 * @method agEventAuditSql     setSqlCmd()            Sets the current record's "sql_cmd" value
 * @method agEventAuditSql     setAgEventAudit()      Sets the current record's "agEventAudit" value
 * @method agEventAuditSql     setAgEventAuditValue() Sets the current record's "agEventAuditValue" collection
 * 
 * @package    AGASTI_CORE
 * @subpackage model
 * @author     CUNY SPS
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseagEventAuditSql extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('ag_event_audit_sql');
        $this->hasColumn('id', 'integer', 5, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'length' => 5,
             ));
        $this->hasColumn('event_audit_id', 'integer', 5, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 5,
             ));
        $this->hasColumn('sql_cmd', 'gzip', null, array(
             'type' => 'gzip',
             'notnull' => true,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('agEventAudit', array(
             'local' => 'event_audit_id',
             'foreign' => 'id'));

        $this->hasMany('agEventAuditValue', array(
             'local' => 'id',
             'foreign' => 'event_audit_id'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}