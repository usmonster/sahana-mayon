<?php

/**
 * agTaskTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class agTaskTable extends agDoctrineTable
{
    /**
     * Returns an instance of this class.
     *
     * @return object agTaskTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('agTask');
    }
}