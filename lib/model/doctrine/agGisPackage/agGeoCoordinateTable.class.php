<?php

/**
 * agGeoCoordinateTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class agGeoCoordinateTable extends PluginagGeoCoordinateTable
{
    /**
     * Returns an instance of this class.
     *
     * @return object agGeoCoordinateTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('agGeoCoordinate');
    }
}