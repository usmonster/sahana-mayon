<?php

/**
 * agEventStaffStatusTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class agEventStaffStatusTable extends PluginagEventStaffStatusTable
{
    /**
     * Returns an instance of this class.
     *
     * @return object agEventStaffStatusTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('agEventStaffStatus');
    }
}