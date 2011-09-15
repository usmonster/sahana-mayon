<?php

/**
 * PluginsfGuardUserProfileTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class PluginsfGuardUserProfileTable extends agDoctrineTable
{

    /**
     * Returns an instance of this class.
     *
     * @return object PluginsfGuardUserProfileTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('PluginsfGuardUserProfile');
    }
    
    public function getByToken(array $parameters)
    {
        $user = Doctrine_Core::getTable('sfGuardUserProfile')->findOneByToken($parameters['token']);
        if (!$user || !$user->getIsWebserviceClient() || !$user->getIsActive()) {
            throw new sfError404Exception(sprintf('Client with token "%s" does not exist or is not activated.', $parameters['token']));
        }
        return $user;
    }    

}