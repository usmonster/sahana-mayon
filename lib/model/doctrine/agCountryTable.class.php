<?php

/**
 * agCountryTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class agCountryTable extends agDoctrineTable
{
    /**
     * Returns an instance of this class.
     *
     * @return object agCountryTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('agCountry');
    }
}