<?php

/**
 * agReportTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class agReportTable extends agDoctrineTable
{
    /**
     * Returns an instance of this class.
     *
     * @return object agReportTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('agReport');
    }
}