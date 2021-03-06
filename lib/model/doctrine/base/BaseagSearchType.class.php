<?php

/**
 * BaseagSearchType
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $search_type
 * @property string $description
 * @property boolean $app_display
 * @property Doctrine_Collection $agSearch
 * 
 * @method integer             getId()          Returns the current record's "id" value
 * @method string              getSearchType()  Returns the current record's "search_type" value
 * @method string              getDescription() Returns the current record's "description" value
 * @method boolean             getAppDisplay()  Returns the current record's "app_display" value
 * @method Doctrine_Collection getAgSearch()    Returns the current record's "agSearch" collection
 * @method agSearchType        setId()          Sets the current record's "id" value
 * @method agSearchType        setSearchType()  Sets the current record's "search_type" value
 * @method agSearchType        setDescription() Sets the current record's "description" value
 * @method agSearchType        setAppDisplay()  Sets the current record's "app_display" value
 * @method agSearchType        setAgSearch()    Sets the current record's "agSearch" collection
 * 
 * @package    AGASTI_CORE
 * @subpackage model
 * @author     CUNY SPS
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseagSearchType extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('ag_search_type');
        $this->hasColumn('id', 'integer', 2, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'length' => 2,
             ));
        $this->hasColumn('search_type', 'string', 64, array(
             'type' => 'string',
             'notnull' => true,
             'length' => 64,
             ));
        $this->hasColumn('description', 'string', 255, array(
             'type' => 'string',
             'length' => 255,
             ));
        $this->hasColumn('app_display', 'boolean', null, array(
             'default' => 1,
             'type' => 'boolean',
             'notnull' => true,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('agSearch', array(
             'local' => 'id',
             'foreign' => 'search_type_id'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}