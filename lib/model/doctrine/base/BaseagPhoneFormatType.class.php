<?php

/**
 * BaseagPhoneFormatType
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $phone_format_type
 * @property boolean $app_display
 * @property string $validation
 * @property string $match_pattern
 * @property string $replacement_pattern
 * @property Doctrine_Collection $agPhoneFormat
 * 
 * @method integer             getId()                  Returns the current record's "id" value
 * @method string              getPhoneFormatType()     Returns the current record's "phone_format_type" value
 * @method boolean             getAppDisplay()          Returns the current record's "app_display" value
 * @method string              getValidation()          Returns the current record's "validation" value
 * @method string              getMatchPattern()        Returns the current record's "match_pattern" value
 * @method string              getReplacementPattern()  Returns the current record's "replacement_pattern" value
 * @method Doctrine_Collection getAgPhoneFormat()       Returns the current record's "agPhoneFormat" collection
 * @method agPhoneFormatType   setId()                  Sets the current record's "id" value
 * @method agPhoneFormatType   setPhoneFormatType()     Sets the current record's "phone_format_type" value
 * @method agPhoneFormatType   setAppDisplay()          Sets the current record's "app_display" value
 * @method agPhoneFormatType   setValidation()          Sets the current record's "validation" value
 * @method agPhoneFormatType   setMatchPattern()        Sets the current record's "match_pattern" value
 * @method agPhoneFormatType   setReplacementPattern()  Sets the current record's "replacement_pattern" value
 * @method agPhoneFormatType   setAgPhoneFormat()       Sets the current record's "agPhoneFormat" collection
 * 
 * @package    AGASTI_CORE
 * @subpackage model
 * @author     CUNY SPS
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseagPhoneFormatType extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('ag_phone_format_type');
        $this->hasColumn('id', 'integer', 2, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'length' => 2,
             ));
        $this->hasColumn('phone_format_type', 'string', 64, array(
             'unique' => true,
             'type' => 'string',
             'notnull' => true,
             'length' => 64,
             ));
        $this->hasColumn('app_display', 'boolean', null, array(
             'default' => 1,
             'type' => 'boolean',
             'notnull' => true,
             ));
        $this->hasColumn('validation', 'string', 128, array(
             'default' => '.*',
             'type' => 'string',
             'notnull' => true,
             'length' => 128,
             ));
        $this->hasColumn('match_pattern', 'string', 128, array(
             'default' => '(.*)',
             'type' => 'string',
             'notnull' => true,
             'length' => 128,
             ));
        $this->hasColumn('replacement_pattern', 'string', 128, array(
             'default' => '$1',
             'type' => 'string',
             'notnull' => true,
             'length' => 128,
             ));


        $this->index('agPhoneFormatType_unq', array(
             'fields' => 
             array(
              0 => 'phone_format_type',
             ),
             'type' => 'unique',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('agPhoneFormat', array(
             'local' => 'id',
             'foreign' => 'phone_format_type_id'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}