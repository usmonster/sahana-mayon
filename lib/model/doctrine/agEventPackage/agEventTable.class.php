<?php

/**
 * agEventTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class agEventTable extends PluginagEventTable
{
    /**
     * Returns an instance of this class.
     *
     * @return object agEventTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('agEvent');
    }
}