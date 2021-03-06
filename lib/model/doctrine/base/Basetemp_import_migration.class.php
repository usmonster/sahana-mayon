<?php

/**
 * Basetemp_import_migration
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * 
 * @method integer               getId() Returns the current record's "id" value
 * @method temp_import_migration setId() Sets the current record's "id" value
 * 
 * @package    AGASTI_CORE
 * @subpackage model
 * @author     CUNY SPS
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class Basetemp_import_migration extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('temp_import_migration');
        $this->hasColumn('id', 'integer', 5, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'length' => 5,
             ));

        $this->option('type', 'MyISAM');
        $this->option('collate', 'utf8_unicode_ci');
        $this->option('charset', 'utf8');
    }

    public function setUp()
    {
        parent::setUp();
        
    }
}