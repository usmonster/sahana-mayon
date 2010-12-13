<?php

/**
 * BaseagReportGenerator
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $report_id
 * @property integer $lucene_search_id
 * @property agReport $agReport
 * @property agLuceneSearch $agLuceneSearch
 * 
 * @method integer           getId()               Returns the current record's "id" value
 * @method integer           getReportId()         Returns the current record's "report_id" value
 * @method integer           getLuceneSearchId()   Returns the current record's "lucene_search_id" value
 * @method agReport          getAgReport()         Returns the current record's "agReport" value
 * @method agLuceneSearch    getAgLuceneSearch()   Returns the current record's "agLuceneSearch" value
 * @method agReportGenerator setId()               Sets the current record's "id" value
 * @method agReportGenerator setReportId()         Sets the current record's "report_id" value
 * @method agReportGenerator setLuceneSearchId()   Sets the current record's "lucene_search_id" value
 * @method agReportGenerator setAgReport()         Sets the current record's "agReport" value
 * @method agReportGenerator setAgLuceneSearch()   Sets the current record's "agLuceneSearch" value
 * 
 * @package    AGASTI_CORE
 * @subpackage model
 * @author     CUNY SPS
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseagReportGenerator extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('ag_report_generator');
        $this->hasColumn('id', 'integer', 2, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'length' => 2,
             ));
        $this->hasColumn('report_id', 'integer', 2, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 2,
             ));
        $this->hasColumn('lucene_search_id', 'integer', 2, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 2,
             ));


        $this->index('agReportGenerator_unq', array(
             'fields' => 
             array(
              0 => 'report_id',
             ),
             'type' => 'unique',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('agReport', array(
             'local' => 'report_id',
             'foreign' => 'id'));

        $this->hasOne('agLuceneSearch', array(
             'local' => 'lucene_search_id',
             'foreign' => 'id'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}