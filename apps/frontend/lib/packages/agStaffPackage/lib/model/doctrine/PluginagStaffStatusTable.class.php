<?php

/**
 * agStaffStatusTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
abstract class PluginagStaffStatusTable extends Doctrine_Table
{
    /**
     * Returns an instance of this class.
     *
     * @return object agStaffStatusTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('agStaffStatus');
    }
}
