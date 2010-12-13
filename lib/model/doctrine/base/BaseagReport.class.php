<?php

/**
 * BaseagReport
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $report_name
 * @property gzip $report_description
 * @property Doctrine_Collection $agLuceneSearch
 * @property Doctrine_Collection $agQuerySelectField
 * @property Doctrine_Collection $agReportGenerator
 * 
 * @method integer             getId()                 Returns the current record's "id" value
 * @method string              getReportName()         Returns the current record's "report_name" value
 * @method gzip                getReportDescription()  Returns the current record's "report_description" value
 * @method Doctrine_Collection getAgLuceneSearch()     Returns the current record's "agLuceneSearch" collection
 * @method Doctrine_Collection getAgQuerySelectField() Returns the current record's "agQuerySelectField" collection
 * @method Doctrine_Collection getAgReportGenerator()  Returns the current record's "agReportGenerator" collection
 * @method agReport            setId()                 Sets the current record's "id" value
 * @method agReport            setReportName()         Sets the current record's "report_name" value
 * @method agReport            setReportDescription()  Sets the current record's "report_description" value
 * @method agReport            setAgLuceneSearch()     Sets the current record's "agLuceneSearch" collection
 * @method agReport            setAgQuerySelectField() Sets the current record's "agQuerySelectField" collection
 * @method agReport            setAgReportGenerator()  Sets the current record's "agReportGenerator" collection
 * 
 * @package    AGASTI_CORE
 * @subpackage model
 * @author     CUNY SPS
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseagReport extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('ag_report');
        $this->hasColumn('id', 'integer', 2, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'length' => 2,
             ));
        $this->hasColumn('report_name', 'string', 64, array(
             'type' => 'string',
             'notnull' => true,
             'length' => 64,
             ));
        $this->hasColumn('report_description', 'gzip', null, array(
             'type' => 'gzip',
             ));


        $this->index('UX_agReport', array(
             'fields' => 
             array(
              0 => 'report_name',
             ),
             'type' => 'unique',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('agLuceneSearch', array(
             'refClass' => 'agReportGenerator',
             'local' => 'report_id',
             'foreign' => 'lucene_search_id'));

        $this->hasMany('agQuerySelectField', array(
             'local' => 'id',
             'foreign' => 'report_id'));

        $this->hasMany('agReportGenerator', array(
             'local' => 'id',
             'foreign' => 'report_id'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}