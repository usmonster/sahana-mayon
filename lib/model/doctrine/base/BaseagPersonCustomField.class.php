<?php

/**
 * BaseagPersonCustomField
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $person_custom_field
 * @property integer $custom_field_type_id
 * @property boolean $app_display
 * @property agCustomFieldType $agCustomFieldType
 * @property Doctrine_Collection $agPerson
 * @property Doctrine_Collection $agPersonCustomFieldValue
 * 
 * @method integer             getId()                       Returns the current record's "id" value
 * @method string              getPersonCustomField()        Returns the current record's "person_custom_field" value
 * @method integer             getCustomFieldTypeId()        Returns the current record's "custom_field_type_id" value
 * @method boolean             getAppDisplay()               Returns the current record's "app_display" value
 * @method agCustomFieldType   getAgCustomFieldType()        Returns the current record's "agCustomFieldType" value
 * @method Doctrine_Collection getAgPerson()                 Returns the current record's "agPerson" collection
 * @method Doctrine_Collection getAgPersonCustomFieldValue() Returns the current record's "agPersonCustomFieldValue" collection
 * @method agPersonCustomField setId()                       Sets the current record's "id" value
 * @method agPersonCustomField setPersonCustomField()        Sets the current record's "person_custom_field" value
 * @method agPersonCustomField setCustomFieldTypeId()        Sets the current record's "custom_field_type_id" value
 * @method agPersonCustomField setAppDisplay()               Sets the current record's "app_display" value
 * @method agPersonCustomField setAgCustomFieldType()        Sets the current record's "agCustomFieldType" value
 * @method agPersonCustomField setAgPerson()                 Sets the current record's "agPerson" collection
 * @method agPersonCustomField setAgPersonCustomFieldValue() Sets the current record's "agPersonCustomFieldValue" collection
 * 
 * @package    AGASTI_CORE
 * @subpackage model
 * @author     CUNY SPS
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseagPersonCustomField extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('ag_person_custom_field');
        $this->hasColumn('id', 'integer', 2, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'length' => 2,
             ));
        $this->hasColumn('person_custom_field', 'string', 64, array(
             'type' => 'string',
             'notnull' => true,
             'length' => 64,
             ));
        $this->hasColumn('custom_field_type_id', 'integer', 2, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 2,
             ));
        $this->hasColumn('app_display', 'boolean', null, array(
             'default' => 1,
             'type' => 'boolean',
             'notnull' => true,
             ));


        $this->index('agPersonCustomField_unq', array(
             'fields' => 
             array(
              0 => 'person_custom_field',
             ),
             'type' => 'unique',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('agCustomFieldType', array(
             'local' => 'custom_field_type_id',
             'foreign' => 'id'));

        $this->hasMany('agPerson', array(
             'refClass' => 'agPersonCustomFieldValue',
             'local' => 'person_custom_field_id',
             'foreign' => 'person_id'));

        $this->hasMany('agPersonCustomFieldValue', array(
             'local' => 'id',
             'foreign' => 'person_custom_field_id'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}