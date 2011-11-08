<?php

/**
 * BasesfGuardUserProfile
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $user_id
 * @property string $token
 * @property boolean $is_webservice_client
 * @property boolean $is_active
 * @property  $created_at
 * @property  $updated_at
 * @property sfGuardUser $User
 * 
 * @method integer            getUserId()               Returns the current record's "user_id" value
 * @method string             getToken()                Returns the current record's "token" value
 * @method boolean            getIsWebserviceClient()   Returns the current record's "is_webservice_client" value
 * @method boolean            getIsActive()             Returns the current record's "is_active" value
 * @method sfGuardUser        getUser()                 Returns the current record's "User" value
 * @method sfGuardUserProfile setUserId()               Sets the current record's "user_id" value
 * @method sfGuardUserProfile setToken()                Sets the current record's "token" value
 * @method sfGuardUserProfile setIsWebserviceClient()   Sets the current record's "is_webservice_client" value
 * @method sfGuardUserProfile setIsActive()             Sets the current record's "is_active" value
 * @method sfGuardUserProfile setUser()                 Sets the current record's "User" value
 * 
 * @package    AGASTI_CORE
 * @subpackage model
 * @author     CUNY SPS
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BasesfGuardUserProfile extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('sf_guard_user_profile');
        $this->hasColumn('user_id', 'integer', null, array(
             'type' => 'integer',
             'notnull' => true,
             ));
        $this->hasColumn('token', 'string', 40, array(
             'type' => 'string',
             'length' => 40,
             ));
        $this->hasColumn('is_webservice_client', 'boolean', null, array(
             'type' => 'boolean',
             'default' => 0,
             ));
        $this->hasColumn('is_active', 'boolean', null, array(
             'type' => 'boolean',
             'default' => 0,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('sfGuardUser as User', array(
             'local' => 'user_id',
             'foreign' => 'id',
             'onDelete' => 'CASCADE'));

        $timestampable0 = new Doctrine_Template_Timestampable(array(
             ));
        $this->actAs($timestampable0);
    }
}