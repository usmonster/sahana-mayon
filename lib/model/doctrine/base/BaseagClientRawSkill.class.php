<?php

/**
 * BaseagClientRawSkill
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $raw_skill_id
 * @property integer $client_id
 * @property agRawSkill $agRawSkill
 * @property agClient $agClient
 * 
 * @method integer          getId()           Returns the current record's "id" value
 * @method integer          getRawSkillId()   Returns the current record's "raw_skill_id" value
 * @method integer          getClientId()     Returns the current record's "client_id" value
 * @method agRawSkill       getAgRawSkill()   Returns the current record's "agRawSkill" value
 * @method agClient         getAgClient()     Returns the current record's "agClient" value
 * @method agClientRawSkill setId()           Sets the current record's "id" value
 * @method agClientRawSkill setRawSkillId()   Sets the current record's "raw_skill_id" value
 * @method agClientRawSkill setClientId()     Sets the current record's "client_id" value
 * @method agClientRawSkill setAgRawSkill()   Sets the current record's "agRawSkill" value
 * @method agClientRawSkill setAgClient()     Sets the current record's "agClient" value
 * 
 * @package    AGASTI_CORE
 * @subpackage model
 * @author     CUNY SPS
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseagClientRawSkill extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('ag_client_raw_skill');
        $this->hasColumn('id', 'integer', 5, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'length' => 5,
             ));
        $this->hasColumn('raw_skill_id', 'integer', 4, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 4,
             ));
        $this->hasColumn('client_id', 'integer', 5, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 5,
             ));


        $this->index('UX_agPersonEventRawSkill', array(
             'fields' => 
             array(
              0 => 'raw_skill_id',
              1 => 'client_id',
             ),
             'type' => 'unique',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('agRawSkill', array(
             'local' => 'raw_skill_id',
             'foreign' => 'id'));

        $this->hasOne('agClient', array(
             'local' => 'client_id',
             'foreign' => 'id'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}