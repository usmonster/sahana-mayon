<?php

/**
 * agAddressTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class agAddressTable extends agDoctrineTable
{
    /**
     * Returns an instance of this class.
     *
     * @return object agAddressTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('agAddress');
    }
}