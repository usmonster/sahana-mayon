<?php

/**
 * BaseagPersonMjAgLanguage
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $person_id
 * @property integer $language_id
 * @property integer $priority
 * @property agPerson $agPerson
 * @property agLanguage $agLanguage
 * @property Doctrine_Collection $agLanguageFormat
 * @property Doctrine_Collection $agLanguageCompetency
 * @property Doctrine_Collection $agPersonLanguageCompetency
 * 
 * @method integer              getId()                         Returns the current record's "id" value
 * @method integer              getPersonId()                   Returns the current record's "person_id" value
 * @method integer              getLanguageId()                 Returns the current record's "language_id" value
 * @method integer              getPriority()                   Returns the current record's "priority" value
 * @method agPerson             getAgPerson()                   Returns the current record's "agPerson" value
 * @method agLanguage           getAgLanguage()                 Returns the current record's "agLanguage" value
 * @method Doctrine_Collection  getAgLanguageFormat()           Returns the current record's "agLanguageFormat" collection
 * @method Doctrine_Collection  getAgLanguageCompetency()       Returns the current record's "agLanguageCompetency" collection
 * @method Doctrine_Collection  getAgPersonLanguageCompetency() Returns the current record's "agPersonLanguageCompetency" collection
 * @method agPersonMjAgLanguage setId()                         Sets the current record's "id" value
 * @method agPersonMjAgLanguage setPersonId()                   Sets the current record's "person_id" value
 * @method agPersonMjAgLanguage setLanguageId()                 Sets the current record's "language_id" value
 * @method agPersonMjAgLanguage setPriority()                   Sets the current record's "priority" value
 * @method agPersonMjAgLanguage setAgPerson()                   Sets the current record's "agPerson" value
 * @method agPersonMjAgLanguage setAgLanguage()                 Sets the current record's "agLanguage" value
 * @method agPersonMjAgLanguage setAgLanguageFormat()           Sets the current record's "agLanguageFormat" collection
 * @method agPersonMjAgLanguage setAgLanguageCompetency()       Sets the current record's "agLanguageCompetency" collection
 * @method agPersonMjAgLanguage setAgPersonLanguageCompetency() Sets the current record's "agPersonLanguageCompetency" collection
 * 
 * @package    AGASTI_CORE
 * @subpackage model
 * @author     CUNY SPS
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseagPersonMjAgLanguage extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('ag_person_mj_ag_language');
        $this->hasColumn('id', 'integer', 5, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'length' => 5,
             ));
        $this->hasColumn('person_id', 'integer', 5, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 5,
             ));
        $this->hasColumn('language_id', 'integer', 4, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 4,
             ));
        $this->hasColumn('priority', 'integer', 1, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 1,
             ));


        $this->index('UX_ag_person_mj_ag_language', array(
             'fields' => 
             array(
              0 => 'person_id',
              1 => 'language_id',
             ),
             'type' => 'unique',
             ));
        $this->index('agPersonMjAgLanguage_priority_unq', array(
             'fields' => 
             array(
              0 => 'person_id',
              1 => 'priority',
             ),
             'type' => 'unique',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('agPerson', array(
             'local' => 'person_id',
             'foreign' => 'id'));

        $this->hasOne('agLanguage', array(
             'local' => 'language_id',
             'foreign' => 'id'));

        $this->hasMany('agLanguageFormat', array(
             'refClass' => 'agPersonLanguageCompetency',
             'local' => 'person_language_id',
             'foreign' => 'language_format_id'));

        $this->hasMany('agLanguageCompetency', array(
             'refClass' => 'agPersonLanguageCompetency',
             'local' => 'person_language_id',
             'foreign' => 'language_competency_id'));

        $this->hasMany('agPersonLanguageCompetency', array(
             'local' => 'id',
             'foreign' => 'person_language_id'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}