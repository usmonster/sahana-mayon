<?php

/**
 * agGeoTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class agGeoTable extends PluginagGeoTable
{
    /**
     * Returns an instance of this class.
     *
     * @return object agGeoTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('agGeo');
    }
}