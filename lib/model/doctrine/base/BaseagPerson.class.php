<?php

/**
 * BaseagPerson
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $entity_id
 * @property agEntity $agEntity
 * @property agPersonDateOfBirth $agPersonDateOfBirth
 * @property Doctrine_Collection $agNationality
 * @property Doctrine_Collection $agReligion
 * @property Doctrine_Collection $agProfession
 * @property Doctrine_Collection $agLanguage
 * @property Doctrine_Collection $agCountry
 * @property Doctrine_Collection $agEthnicity
 * @property Doctrine_Collection $agSex
 * @property Doctrine_Collection $agMaritalStatus
 * @property Doctrine_Collection $agImport
 * @property Doctrine_Collection $agResidentialStatus
 * @property Doctrine_Collection $agPersonName
 * @property Doctrine_Collection $agPersonNameType
 * @property Doctrine_Collection $agPersonCustomField
 * @property Doctrine_Collection $agStaff
 * @property Doctrine_Collection $agStaffStatus
 * @property Doctrine_Collection $agPersonLastImport
 * @property Doctrine_Collection $agImportType
 * @property Doctrine_Collection $agClient
 * @property Doctrine_Collection $agPersonMjAgNationality
 * @property Doctrine_Collection $agPersonEthnicity
 * @property Doctrine_Collection $agPersonMjAgReligion
 * @property Doctrine_Collection $agPersonMjAgProfession
 * @property Doctrine_Collection $agPersonMaritalStatus
 * @property Doctrine_Collection $agPersonMjAgLanguage
 * @property Doctrine_Collection $agPersonSex
 * @property Doctrine_Collection $agPersonMjAgPersonName
 * @property Doctrine_Collection $agPersonResidentialStatus
 * @property Doctrine_Collection $agPersonCustomFieldValue
 * @property Doctrine_Collection $agPersonSkill
 * @property Doctrine_Collection $agPersonCertification
 * 
 * @method integer             getId()                        Returns the current record's "id" value
 * @method integer             getEntityId()                  Returns the current record's "entity_id" value
 * @method agEntity            getAgEntity()                  Returns the current record's "agEntity" value
 * @method agPersonDateOfBirth getAgPersonDateOfBirth()       Returns the current record's "agPersonDateOfBirth" value
 * @method Doctrine_Collection getAgNationality()             Returns the current record's "agNationality" collection
 * @method Doctrine_Collection getAgReligion()                Returns the current record's "agReligion" collection
 * @method Doctrine_Collection getAgProfession()              Returns the current record's "agProfession" collection
 * @method Doctrine_Collection getAgLanguage()                Returns the current record's "agLanguage" collection
 * @method Doctrine_Collection getAgCountry()                 Returns the current record's "agCountry" collection
 * @method Doctrine_Collection getAgEthnicity()               Returns the current record's "agEthnicity" collection
 * @method Doctrine_Collection getAgSex()                     Returns the current record's "agSex" collection
 * @method Doctrine_Collection getAgMaritalStatus()           Returns the current record's "agMaritalStatus" collection
 * @method Doctrine_Collection getAgImport()                  Returns the current record's "agImport" collection
 * @method Doctrine_Collection getAgResidentialStatus()       Returns the current record's "agResidentialStatus" collection
 * @method Doctrine_Collection getAgPersonName()              Returns the current record's "agPersonName" collection
 * @method Doctrine_Collection getAgPersonNameType()          Returns the current record's "agPersonNameType" collection
 * @method Doctrine_Collection getAgPersonCustomField()       Returns the current record's "agPersonCustomField" collection
 * @method Doctrine_Collection getAgStaff()                   Returns the current record's "agStaff" collection
 * @method Doctrine_Collection getAgStaffStatus()             Returns the current record's "agStaffStatus" collection
 * @method Doctrine_Collection getAgPersonLastImport()        Returns the current record's "agPersonLastImport" collection
 * @method Doctrine_Collection getAgImportType()              Returns the current record's "agImportType" collection
 * @method Doctrine_Collection getAgClient()                  Returns the current record's "agClient" collection
 * @method Doctrine_Collection getAgPersonMjAgNationality()   Returns the current record's "agPersonMjAgNationality" collection
 * @method Doctrine_Collection getAgPersonEthnicity()         Returns the current record's "agPersonEthnicity" collection
 * @method Doctrine_Collection getAgPersonMjAgReligion()      Returns the current record's "agPersonMjAgReligion" collection
 * @method Doctrine_Collection getAgPersonMjAgProfession()    Returns the current record's "agPersonMjAgProfession" collection
 * @method Doctrine_Collection getAgPersonMaritalStatus()     Returns the current record's "agPersonMaritalStatus" collection
 * @method Doctrine_Collection getAgPersonMjAgLanguage()      Returns the current record's "agPersonMjAgLanguage" collection
 * @method Doctrine_Collection getAgPersonSex()               Returns the current record's "agPersonSex" collection
 * @method Doctrine_Collection getAgPersonMjAgPersonName()    Returns the current record's "agPersonMjAgPersonName" collection
 * @method Doctrine_Collection getAgPersonResidentialStatus() Returns the current record's "agPersonResidentialStatus" collection
 * @method Doctrine_Collection getAgPersonCustomFieldValue()  Returns the current record's "agPersonCustomFieldValue" collection
 * @method Doctrine_Collection getAgPersonSkill()             Returns the current record's "agPersonSkill" collection
 * @method Doctrine_Collection getAgPersonCertification()     Returns the current record's "agPersonCertification" collection
 * @method agPerson            setId()                        Sets the current record's "id" value
 * @method agPerson            setEntityId()                  Sets the current record's "entity_id" value
 * @method agPerson            setAgEntity()                  Sets the current record's "agEntity" value
 * @method agPerson            setAgPersonDateOfBirth()       Sets the current record's "agPersonDateOfBirth" value
 * @method agPerson            setAgNationality()             Sets the current record's "agNationality" collection
 * @method agPerson            setAgReligion()                Sets the current record's "agReligion" collection
 * @method agPerson            setAgProfession()              Sets the current record's "agProfession" collection
 * @method agPerson            setAgLanguage()                Sets the current record's "agLanguage" collection
 * @method agPerson            setAgCountry()                 Sets the current record's "agCountry" collection
 * @method agPerson            setAgEthnicity()               Sets the current record's "agEthnicity" collection
 * @method agPerson            setAgSex()                     Sets the current record's "agSex" collection
 * @method agPerson            setAgMaritalStatus()           Sets the current record's "agMaritalStatus" collection
 * @method agPerson            setAgImport()                  Sets the current record's "agImport" collection
 * @method agPerson            setAgResidentialStatus()       Sets the current record's "agResidentialStatus" collection
 * @method agPerson            setAgPersonName()              Sets the current record's "agPersonName" collection
 * @method agPerson            setAgPersonNameType()          Sets the current record's "agPersonNameType" collection
 * @method agPerson            setAgPersonCustomField()       Sets the current record's "agPersonCustomField" collection
 * @method agPerson            setAgStaff()                   Sets the current record's "agStaff" collection
 * @method agPerson            setAgStaffStatus()             Sets the current record's "agStaffStatus" collection
 * @method agPerson            setAgPersonLastImport()        Sets the current record's "agPersonLastImport" collection
 * @method agPerson            setAgImportType()              Sets the current record's "agImportType" collection
 * @method agPerson            setAgClient()                  Sets the current record's "agClient" collection
 * @method agPerson            setAgPersonMjAgNationality()   Sets the current record's "agPersonMjAgNationality" collection
 * @method agPerson            setAgPersonEthnicity()         Sets the current record's "agPersonEthnicity" collection
 * @method agPerson            setAgPersonMjAgReligion()      Sets the current record's "agPersonMjAgReligion" collection
 * @method agPerson            setAgPersonMjAgProfession()    Sets the current record's "agPersonMjAgProfession" collection
 * @method agPerson            setAgPersonMaritalStatus()     Sets the current record's "agPersonMaritalStatus" collection
 * @method agPerson            setAgPersonMjAgLanguage()      Sets the current record's "agPersonMjAgLanguage" collection
 * @method agPerson            setAgPersonSex()               Sets the current record's "agPersonSex" collection
 * @method agPerson            setAgPersonMjAgPersonName()    Sets the current record's "agPersonMjAgPersonName" collection
 * @method agPerson            setAgPersonResidentialStatus() Sets the current record's "agPersonResidentialStatus" collection
 * @method agPerson            setAgPersonCustomFieldValue()  Sets the current record's "agPersonCustomFieldValue" collection
 * @method agPerson            setAgPersonSkill()             Sets the current record's "agPersonSkill" collection
 * @method agPerson            setAgPersonCertification()     Sets the current record's "agPersonCertification" collection
 * 
 * @package    AGASTI_CORE
 * @subpackage model
 * @author     CUNY SPS
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseagPerson extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('ag_person');
        $this->hasColumn('id', 'integer', 5, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'length' => 5,
             ));
        $this->hasColumn('entity_id', 'integer', 5, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => 5,
             ));


        $this->index('agPerson_unq', array(
             'fields' => 
             array(
              0 => 'entity_id',
             ),
             'type' => 'unique',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('agEntity', array(
             'local' => 'entity_id',
             'foreign' => 'id'));

        $this->hasOne('agPersonDateOfBirth', array(
             'local' => 'id',
             'foreign' => 'person_id'));

        $this->hasMany('agNationality', array(
             'refClass' => 'agPersonMjAgNationality',
             'local' => 'person_id',
             'foreign' => 'nationality_id'));

        $this->hasMany('agReligion', array(
             'refClass' => 'agPersonMjAgReligion',
             'local' => 'person_id',
             'foreign' => 'religion_id'));

        $this->hasMany('agProfession', array(
             'refClass' => 'agPersonMjAgProfession',
             'local' => 'person_id',
             'foreign' => 'profession_id'));

        $this->hasMany('agLanguage', array(
             'refClass' => 'agPersonMjAgLanguage',
             'local' => 'person_id',
             'foreign' => 'language_id'));

        $this->hasMany('agCountry', array(
             'refClass' => 'agPersonResidentialStatus',
             'local' => 'person_id',
             'foreign' => 'country_id'));

        $this->hasMany('agEthnicity', array(
             'refClass' => 'agPersonEthnicity',
             'local' => 'person_id',
             'foreign' => 'ethnicity_id'));

        $this->hasMany('agSex', array(
             'refClass' => 'agPersonSex',
             'local' => 'person_id',
             'foreign' => 'sex_id'));

        $this->hasMany('agMaritalStatus', array(
             'refClass' => 'agPersonMaritalStatus',
             'local' => 'person_id',
             'foreign' => 'marital_status_id'));

        $this->hasMany('agImport', array(
             'refClass' => 'agPersonLastImport',
             'local' => 'person_id',
             'foreign' => 'last_import_id'));

        $this->hasMany('agResidentialStatus', array(
             'refClass' => 'agPersonResidentialStatus',
             'local' => 'person_id',
             'foreign' => 'residential_status_id'));

        $this->hasMany('agPersonName', array(
             'refClass' => 'agPersonMjAgPersonName',
             'local' => 'person_id',
             'foreign' => 'person_name_id'));

        $this->hasMany('agPersonNameType', array(
             'refClass' => 'agPersonMjAgPersonName',
             'local' => 'person_id',
             'foreign' => 'person_name_type_id'));

        $this->hasMany('agPersonCustomField', array(
             'refClass' => 'agPersonCustomFieldValue',
             'local' => 'person_id',
             'foreign' => 'person_custom_field_id'));

        $this->hasMany('agStaff', array(
             'local' => 'id',
             'foreign' => 'person_id'));

        $this->hasMany('agStaffStatus', array(
             'refClass' => 'agStaff',
             'local' => 'person_id',
             'foreign' => 'staff_status_id'));

        $this->hasMany('agPersonLastImport', array(
             'local' => 'id',
             'foreign' => 'person_id'));

        $this->hasMany('agImportType', array(
             'refClass' => 'agImport',
             'local' => 'person_id',
             'foreign' => 'import_type_id'));

        $this->hasMany('agClient', array(
             'local' => 'id',
             'foreign' => 'person_id'));

        $this->hasMany('agPersonMjAgNationality', array(
             'local' => 'id',
             'foreign' => 'person_id'));

        $this->hasMany('agPersonEthnicity', array(
             'local' => 'id',
             'foreign' => 'person_id'));

        $this->hasMany('agPersonMjAgReligion', array(
             'local' => 'id',
             'foreign' => 'person_id'));

        $this->hasMany('agPersonMjAgProfession', array(
             'local' => 'id',
             'foreign' => 'person_id'));

        $this->hasMany('agPersonMaritalStatus', array(
             'local' => 'id',
             'foreign' => 'person_id'));

        $this->hasMany('agPersonMjAgLanguage', array(
             'local' => 'id',
             'foreign' => 'person_id'));

        $this->hasMany('agPersonSex', array(
             'local' => 'id',
             'foreign' => 'person_id'));

        $this->hasMany('agPersonMjAgPersonName', array(
             'local' => 'id',
             'foreign' => 'person_id'));

        $this->hasMany('agPersonResidentialStatus', array(
             'local' => 'id',
             'foreign' => 'person_id'));

        $this->hasMany('agPersonCustomFieldValue', array(
             'local' => 'id',
             'foreign' => 'person_id'));

        $this->hasMany('agPersonSkill', array(
             'local' => 'id',
             'foreign' => 'person_id'));

        $this->hasMany('agPersonCertification', array(
             'local' => 'id',
             'foreign' => 'person_id'));

        $luceneable0 = new Luceneable();
        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($luceneable0);
        $this->actAs($timestampable0);
    }
}