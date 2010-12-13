<?php

/**
 * BaseagPersonSkill
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $person_id
 * @property integer $skill_id
 * @property integer $competency_id
 * @property date $date_expires
 * @property boolean $confirmed
 * @property agCompetency $agCompetency
 * @property agPerson $agPerson
 * @property agSkill $agSkill
 * 
 * @method integer       getId()            Returns the current record's "id" value
 * @method integer       getPersonId()      Returns the current record's "person_id" value
 * @method integer       getSkillId()       Returns the current record's "skill_id" value
 * @method integer       getCompetencyId()  Returns the current record's "competency_id" value
 * @method date          getDateExpires()   Returns the current record's "date_expires" value
 * @method boolean       getConfirmed()     Returns the current record's "confirmed" value
 * @method agCompetency  getAgCompetency()  Returns the current record's "agCompetency" value
 * @method agPerson      getAgPerson()      Returns the current record's "agPerson" value
 * @method agSkill       getAgSkill()       Returns the current record's "agSkill" value
 * @method agPersonSkill setId()            Sets the current record's "id" value
 * @method agPersonSkill setPersonId()      Sets the current record's "person_id" value
 * @method agPersonSkill setSkillId()       Sets the current record's "skill_id" value
 * @method agPersonSkill setCompetencyId()  Sets the current record's "competency_id" value
 * @method agPersonSkill setDateExpires()   Sets the current record's "date_expires" value
 * @method agPersonSkill setConfirmed()     Sets the current record's "confirmed" value
 * @method agPersonSkill setAgCompetency()  Sets the current record's "agCompetency" value
 * @method agPersonSkill setAgPerson()      Sets the current record's "agPerson" value
 * @method agPersonSkill setAgSkill()       Sets the current record's "agSkill" value
 * 
 * @package    AGASTI_CORE
 * @subpackage model
 * @author     CUNY SPS
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseagPersonSkill extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('ag_person_skill');
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
        $this->hasColumn('skill_id', 'integer', 2, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 2,
             ));
        $this->hasColumn('competency_id', 'integer', 2, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 2,
             ));
        $this->hasColumn('date_expires', 'date', null, array(
             'type' => 'date',
             ));
        $this->hasColumn('confirmed', 'boolean', null, array(
             'default' => 0,
             'type' => 'boolean',
             'notnull' => true,
             ));


        $this->index('agStaffSkill_unq', array(
             'fields' => 
             array(
              0 => 'person_id',
              1 => 'skill_id',
             ),
             'type' => 'unique',
             ));
        $this->index('agStaffSkill_staffid', array(
             'fields' => 
             array(
              0 => 'person_id',
             ),
             ));
        $this->index('agStaffSkill_skillId', array(
             'fields' => 
             array(
              0 => 'skill_id',
             ),
             ));
        $this->index('agStaffSkill_competencyId', array(
             'fields' => 
             array(
              0 => 'competency_id',
             ),
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('agCompetency', array(
             'local' => 'competency_id',
             'foreign' => 'id'));

        $this->hasOne('agPerson', array(
             'local' => 'person_id',
             'foreign' => 'id'));

        $this->hasOne('agSkill', array(
             'local' => 'skill_id',
             'foreign' => 'id'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}