<?php

/**
 * BaseagSearch
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $search_type_id
 * @property string $search_hash
 * @property string $search_name
 * @property clob $search_condition
 * @property agSearchType $agSearchType
 * @property Doctrine_Collection $agReport
 * @property Doctrine_Collection $agReportGenerator
 * @property Doctrine_Collection $agScenarioStaffGenerator
 * 
 * @method integer             getId()                       Returns the current record's "id" value
 * @method integer             getSearchTypeId()             Returns the current record's "search_type_id" value
 * @method string              getSearchHash()               Returns the current record's "search_hash" value
 * @method string              getSearchName()               Returns the current record's "search_name" value
 * @method clob                getSearchCondition()          Returns the current record's "search_condition" value
 * @method agSearchType        getAgSearchType()             Returns the current record's "agSearchType" value
 * @method Doctrine_Collection getAgReport()                 Returns the current record's "agReport" collection
 * @method Doctrine_Collection getAgReportGenerator()        Returns the current record's "agReportGenerator" collection
 * @method Doctrine_Collection getAgScenarioStaffGenerator() Returns the current record's "agScenarioStaffGenerator" collection
 * @method agSearch            setId()                       Sets the current record's "id" value
 * @method agSearch            setSearchTypeId()             Sets the current record's "search_type_id" value
 * @method agSearch            setSearchHash()               Sets the current record's "search_hash" value
 * @method agSearch            setSearchName()               Sets the current record's "search_name" value
 * @method agSearch            setSearchCondition()          Sets the current record's "search_condition" value
 * @method agSearch            setAgSearchType()             Sets the current record's "agSearchType" value
 * @method agSearch            setAgReport()                 Sets the current record's "agReport" collection
 * @method agSearch            setAgReportGenerator()        Sets the current record's "agReportGenerator" collection
 * @method agSearch            setAgScenarioStaffGenerator() Sets the current record's "agScenarioStaffGenerator" collection
 * 
 * @package    AGASTI_CORE
 * @subpackage model
 * @author     CUNY SPS
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseagSearch extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('ag_search');
        $this->hasColumn('id', 'integer', 2, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'length' => 2,
             ));
        $this->hasColumn('search_type_id', 'integer', 2, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 2,
             ));
        $this->hasColumn('search_hash', 'string', 128, array(
             'type' => 'string',
             'notnull' => true,
             'length' => 128,
             ));
        $this->hasColumn('search_name', 'string', 64, array(
             'type' => 'string',
             'notnull' => true,
             'length' => 64,
             ));
        $this->hasColumn('search_condition', 'clob', 65532, array(
             'type' => 'clob',
             'notnull' => true,
             'length' => 65532,
             ));


        $this->index('IX_agSearch_searchType', array(
             'fields' => 
             array(
              0 => 'search_type_id',
             ),
             ));
        $this->index('agSearch_unq_searchHash', array(
             'fields' => 
             array(
              0 => 'search_hash',
             ),
             'type' => 'unique',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('agSearchType', array(
             'local' => 'search_type_id',
             'foreign' => 'id'));

        $this->hasMany('agReport', array(
             'refClass' => 'agReportGenerator',
             'local' => 'search_id',
             'foreign' => 'report_id'));

        $this->hasMany('agReportGenerator', array(
             'local' => 'id',
             'foreign' => 'search_id'));

        $this->hasMany('agScenarioStaffGenerator', array(
             'local' => 'id',
             'foreign' => 'search_id'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}