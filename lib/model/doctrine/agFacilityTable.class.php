<?php

/**
 * agFacilityTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class agFacilityTable extends agDoctrineTable
{
    /**
     * Returns an instance of this class.
     *
     * @return object agFacilityTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('agFacility');
    }
}