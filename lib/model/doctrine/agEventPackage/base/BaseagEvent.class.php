<?php

/**
 * BaseagEvent
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $event_name
 * @property integer $zero_hour
 * @property Doctrine_Collection $agAffectedArea
 * @property Doctrine_Collection $agEventScenario
 * @property Doctrine_Collection $agEventDescription
 * @property Doctrine_Collection $agEventAudit
 * @property Doctrine_Collection $agEventAffectedArea
 * @property Doctrine_Collection $agEventFacilityGroup
 * @property Doctrine_Collection $agEventStaff
 * @property Doctrine_Collection $agEventMessageTrigger
 * @property Doctrine_Collection $agEventStatus
 * @property Doctrine_Collection $agClient
 * @property Doctrine_Collection $agMessageBatch
 * @property Doctrine_Collection $agPet
 * @property Doctrine_Collection $agScenario
 * 
 * @method integer             getId()                    Returns the current record's "id" value
 * @method string              getEventName()             Returns the current record's "event_name" value
 * @method integer             getZeroHour()              Returns the current record's "zero_hour" value
 * @method Doctrine_Collection getAgAffectedArea()        Returns the current record's "agAffectedArea" collection
 * @method Doctrine_Collection getAgEventScenario()       Returns the current record's "agEventScenario" collection
 * @method Doctrine_Collection getAgEventDescription()    Returns the current record's "agEventDescription" collection
 * @method Doctrine_Collection getAgEventAudit()          Returns the current record's "agEventAudit" collection
 * @method Doctrine_Collection getAgEventAffectedArea()   Returns the current record's "agEventAffectedArea" collection
 * @method Doctrine_Collection getAgEventFacilityGroup()  Returns the current record's "agEventFacilityGroup" collection
 * @method Doctrine_Collection getAgEventStaff()          Returns the current record's "agEventStaff" collection
 * @method Doctrine_Collection getAgEventMessageTrigger() Returns the current record's "agEventMessageTrigger" collection
 * @method Doctrine_Collection getAgEventStatus()         Returns the current record's "agEventStatus" collection
 * @method Doctrine_Collection getAgClient()              Returns the current record's "agClient" collection
 * @method Doctrine_Collection getAgMessageBatch()        Returns the current record's "agMessageBatch" collection
 * @method Doctrine_Collection getAgPet()                 Returns the current record's "agPet" collection
 * @method Doctrine_Collection getAgScenario()            Returns the current record's "agScenario" collection
 * @method agEvent             setId()                    Sets the current record's "id" value
 * @method agEvent             setEventName()             Sets the current record's "event_name" value
 * @method agEvent             setZeroHour()              Sets the current record's "zero_hour" value
 * @method agEvent             setAgAffectedArea()        Sets the current record's "agAffectedArea" collection
 * @method agEvent             setAgEventScenario()       Sets the current record's "agEventScenario" collection
 * @method agEvent             setAgEventDescription()    Sets the current record's "agEventDescription" collection
 * @method agEvent             setAgEventAudit()          Sets the current record's "agEventAudit" collection
 * @method agEvent             setAgEventAffectedArea()   Sets the current record's "agEventAffectedArea" collection
 * @method agEvent             setAgEventFacilityGroup()  Sets the current record's "agEventFacilityGroup" collection
 * @method agEvent             setAgEventStaff()          Sets the current record's "agEventStaff" collection
 * @method agEvent             setAgEventMessageTrigger() Sets the current record's "agEventMessageTrigger" collection
 * @method agEvent             setAgEventStatus()         Sets the current record's "agEventStatus" collection
 * @method agEvent             setAgClient()              Sets the current record's "agClient" collection
 * @method agEvent             setAgMessageBatch()        Sets the current record's "agMessageBatch" collection
 * @method agEvent             setAgPet()                 Sets the current record's "agPet" collection
 * @method agEvent             setAgScenario()            Sets the current record's "agScenario" collection
 * 
 * @package    AGASTI_CORE
 * @subpackage model
 * @author     CUNY SPS
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseagEvent extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('ag_event');
        $this->hasColumn('id', 'integer', 4, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'length' => 4,
             ));
        $this->hasColumn('event_name', 'string', 64, array(
             'type' => 'string',
             'notnull' => true,
             'length' => 64,
             ));
        $this->hasColumn('zero_hour', 'integer', 5, array(
             'type' => 'integer',
             'length' => 5,
             ));


        $this->index('agEvent_unq', array(
             'fields' => 
             array(
              0 => 'event_name',
             ),
             'type' => 'unique',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('agAffectedArea', array(
             'refClass' => 'agEventAffectedArea',
             'local' => 'event_id',
             'foreign' => 'affected_area_id'));

        $this->hasMany('agEventScenario', array(
             'local' => 'id',
             'foreign' => 'event_id'));

        $this->hasMany('agEventDescription', array(
             'local' => 'id',
             'foreign' => 'event_id'));

        $this->hasMany('agEventAudit', array(
             'local' => 'id',
             'foreign' => 'event_id'));

        $this->hasMany('agEventAffectedArea', array(
             'local' => 'id',
             'foreign' => 'event_id'));

        $this->hasMany('agEventFacilityGroup', array(
             'local' => 'id',
             'foreign' => 'event_id'));

        $this->hasMany('agEventStaff', array(
             'local' => 'id',
             'foreign' => 'event_id'));

        $this->hasMany('agEventMessageTrigger', array(
             'local' => 'id',
             'foreign' => 'event_id'));

        $this->hasMany('agEventStatus', array(
             'local' => 'id',
             'foreign' => 'event_id'));

        $this->hasMany('agClient', array(
             'local' => 'id',
             'foreign' => 'event_id'));

        $this->hasMany('agMessageBatch', array(
             'local' => 'id',
             'foreign' => 'event_id'));

        $this->hasMany('agPet', array(
             'local' => 'id',
             'foreign' => 'event_id'));

        $this->hasMany('agScenario', array(
             'refClass' => 'agEventScenario',
             'local' => 'event_id',
             'foreign' => 'scenario_id'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}