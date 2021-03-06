<?php

/**
 * BaseagGeoSource
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $geo_source
 * @property timestamp $data_compiled
 * @property integer $geo_source_score_id
 * @property agGeoSourceScore $agGeoSourceScore
 * @property Doctrine_Collection $agGeo
 * 
 * @method integer             getId()                  Returns the current record's "id" value
 * @method string              getGeoSource()           Returns the current record's "geo_source" value
 * @method timestamp           getDataCompiled()        Returns the current record's "data_compiled" value
 * @method integer             getGeoSourceScoreId()    Returns the current record's "geo_source_score_id" value
 * @method agGeoSourceScore    getAgGeoSourceScore()    Returns the current record's "agGeoSourceScore" value
 * @method Doctrine_Collection getAgGeo()               Returns the current record's "agGeo" collection
 * @method agGeoSource         setId()                  Sets the current record's "id" value
 * @method agGeoSource         setGeoSource()           Sets the current record's "geo_source" value
 * @method agGeoSource         setDataCompiled()        Sets the current record's "data_compiled" value
 * @method agGeoSource         setGeoSourceScoreId()    Sets the current record's "geo_source_score_id" value
 * @method agGeoSource         setAgGeoSourceScore()    Sets the current record's "agGeoSourceScore" value
 * @method agGeoSource         setAgGeo()               Sets the current record's "agGeo" collection
 * 
 * @package    AGASTI_CORE
 * @subpackage model
 * @author     CUNY SPS
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseagGeoSource extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('ag_geo_source');
        $this->hasColumn('id', 'integer', 4, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'length' => 4,
             ));
        $this->hasColumn('geo_source', 'string', 64, array(
             'type' => 'string',
             'notnull' => true,
             'length' => 64,
             ));
        $this->hasColumn('data_compiled', 'timestamp', null, array(
             'type' => 'timestamp',
             'notnull' => true,
             ));
        $this->hasColumn('geo_source_score_id', 'integer', 2, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 2,
             ));


        $this->index('UX_agGeoSource', array(
             'fields' => 
             array(
              0 => 'geo_source',
              1 => 'data_compiled',
             ),
             'type' => 'unique',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('agGeoSourceScore', array(
             'local' => 'geo_source_score_id',
             'foreign' => 'id'));

        $this->hasMany('agGeo', array(
             'local' => 'id',
             'foreign' => 'geo_source_id'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}