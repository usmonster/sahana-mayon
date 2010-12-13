<?php

/**
 * BaseagImportType
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $import_type
 * @property string $description
 * @property boolean $app_display
 * @property Doctrine_Collection $agPerson
 * @property Doctrine_Collection $agImport
 * 
 * @method integer             getId()          Returns the current record's "id" value
 * @method string              getImportType()  Returns the current record's "import_type" value
 * @method string              getDescription() Returns the current record's "description" value
 * @method boolean             getAppDisplay()  Returns the current record's "app_display" value
 * @method Doctrine_Collection getAgPerson()    Returns the current record's "agPerson" collection
 * @method Doctrine_Collection getAgImport()    Returns the current record's "agImport" collection
 * @method agImportType        setId()          Sets the current record's "id" value
 * @method agImportType        setImportType()  Sets the current record's "import_type" value
 * @method agImportType        setDescription() Sets the current record's "description" value
 * @method agImportType        setAppDisplay()  Sets the current record's "app_display" value
 * @method agImportType        setAgPerson()    Sets the current record's "agPerson" collection
 * @method agImportType        setAgImport()    Sets the current record's "agImport" collection
 * 
 * @package    AGASTI_CORE
 * @subpackage model
 * @author     CUNY SPS
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseagImportType extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('ag_import_type');
        $this->hasColumn('id', 'integer', 2, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'length' => 2,
             ));
        $this->hasColumn('import_type', 'string', 128, array(
             'unique' => true,
             'type' => 'string',
             'notnull' => true,
             'length' => 128,
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
        $this->hasMany('agPerson', array(
             'refClass' => 'agImport',
             'local' => 'import_type_id',
             'foreign' => 'person_id'));

        $this->hasMany('agImport', array(
             'local' => 'id',
             'foreign' => 'import_type_id'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}