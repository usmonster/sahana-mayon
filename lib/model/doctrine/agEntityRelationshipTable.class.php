<?php

/**
 * agEntityRelationshipTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class agEntityRelationshipTable extends agDoctrineTable
{
    /**
     * Returns an instance of this class.
     *
     * @return object agEntityRelationshipTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('agEntityRelationship');
    }
}