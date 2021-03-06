<?php

/**
 * BaseagLanguageFormat
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $language_format
 * @property boolean $app_display
 * @property Doctrine_Collection $agPersonMjAgLanguage
 * @property Doctrine_Collection $agLanguageCompetency
 * @property Doctrine_Collection $agPersonLanguageCompetency
 * 
 * @method integer             getId()                         Returns the current record's "id" value
 * @method string              getLanguageFormat()             Returns the current record's "language_format" value
 * @method boolean             getAppDisplay()                 Returns the current record's "app_display" value
 * @method Doctrine_Collection getAgPersonMjAgLanguage()       Returns the current record's "agPersonMjAgLanguage" collection
 * @method Doctrine_Collection getAgLanguageCompetency()       Returns the current record's "agLanguageCompetency" collection
 * @method Doctrine_Collection getAgPersonLanguageCompetency() Returns the current record's "agPersonLanguageCompetency" collection
 * @method agLanguageFormat    setId()                         Sets the current record's "id" value
 * @method agLanguageFormat    setLanguageFormat()             Sets the current record's "language_format" value
 * @method agLanguageFormat    setAppDisplay()                 Sets the current record's "app_display" value
 * @method agLanguageFormat    setAgPersonMjAgLanguage()       Sets the current record's "agPersonMjAgLanguage" collection
 * @method agLanguageFormat    setAgLanguageCompetency()       Sets the current record's "agLanguageCompetency" collection
 * @method agLanguageFormat    setAgPersonLanguageCompetency() Sets the current record's "agPersonLanguageCompetency" collection
 * 
 * @package    AGASTI_CORE
 * @subpackage model
 * @author     CUNY SPS
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseagLanguageFormat extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('ag_language_format');
        $this->hasColumn('id', 'integer', 2, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'length' => 2,
             ));
        $this->hasColumn('language_format', 'string', 64, array(
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
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('agPersonMjAgLanguage', array(
             'refClass' => 'agPersonLanguageCompetency',
             'local' => 'language_format_id',
             'foreign' => 'person_language_id'));

        $this->hasMany('agLanguageCompetency', array(
             'refClass' => 'agPersonLanguageCompetency',
             'local' => 'language_format_id',
             'foreign' => 'language_competency_id'));

        $this->hasMany('agPersonLanguageCompetency', array(
             'local' => 'id',
             'foreign' => 'language_format_id'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}