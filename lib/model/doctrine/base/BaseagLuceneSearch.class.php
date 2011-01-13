<?php

/**
 * BaseagLuceneSearch
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $lucene_search_type_id
 * @property string $query_name
 * @property gzip $query_condition
 * @property agLuceneSearchType $agLuceneSearchType
 * @property Doctrine_Collection $agReport
 * @property Doctrine_Collection $agReportGenerator
 * @property Doctrine_Collection $agScenarioStaffGenerator
 * 
 * @method integer             getId()                       Returns the current record's "id" value
 * @method integer             getLuceneSearchTypeId()       Returns the current record's "lucene_search_type_id" value
 * @method string              getQueryName()                Returns the current record's "query_name" value
 * @method gzip                getQueryCondition()           Returns the current record's "query_condition" value
 * @method agLuceneSearchType  getAgLuceneSearchType()       Returns the current record's "agLuceneSearchType" value
 * @method Doctrine_Collection getAgReport()                 Returns the current record's "agReport" collection
 * @method Doctrine_Collection getAgReportGenerator()        Returns the current record's "agReportGenerator" collection
 * @method Doctrine_Collection getAgScenarioStaffGenerator() Returns the current record's "agScenarioStaffGenerator" collection
 * @method agLuceneSearch      setId()                       Sets the current record's "id" value
 * @method agLuceneSearch      setLuceneSearchTypeId()       Sets the current record's "lucene_search_type_id" value
 * @method agLuceneSearch      setQueryName()                Sets the current record's "query_name" value
 * @method agLuceneSearch      setQueryCondition()           Sets the current record's "query_condition" value
 * @method agLuceneSearch      setAgLuceneSearchType()       Sets the current record's "agLuceneSearchType" value
 * @method agLuceneSearch      setAgReport()                 Sets the current record's "agReport" collection
 * @method agLuceneSearch      setAgReportGenerator()        Sets the current record's "agReportGenerator" collection
 * @method agLuceneSearch      setAgScenarioStaffGenerator() Sets the current record's "agScenarioStaffGenerator" collection
 * 
 * @package    AGASTI_CORE
 * @subpackage model
 * @author     CUNY SPS
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseagLuceneSearch extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('ag_lucene_search');
        $this->hasColumn('id', 'integer', 2, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'length' => 2,
             ));
        $this->hasColumn('lucene_search_type_id', 'integer', 2, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 2,
             ));
        $this->hasColumn('query_name', 'string', 64, array(
             'type' => 'string',
             'notnull' => true,
             'length' => 64,
             ));
        $this->hasColumn('query_condition', 'gzip', null, array(
             'type' => 'gzip',
             'notnull' => true,
             ));


        $this->index('agLuceneSearch_unq', array(
             'fields' => 
             array(
              0 => 'query_name',
             ),
             'type' => 'unique',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('agLuceneSearchType', array(
             'local' => 'lucene_search_type_id',
             'foreign' => 'id'));

        $this->hasMany('agReport', array(
             'refClass' => 'agReportGenerator',
             'local' => 'lucene_search_id',
             'foreign' => 'report_id'));

        $this->hasMany('agReportGenerator', array(
             'local' => 'id',
             'foreign' => 'lucene_search_id'));

        $this->hasMany('agScenarioStaffGenerator', array(
             'local' => 'id',
             'foreign' => 'lucene_search_id'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}