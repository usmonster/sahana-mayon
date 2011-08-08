<?php

/**
* apps/frontend/lib/packages/agEventPackage/lib/model/doctrine/agEventFacilityResource.class.php
* 
* @package    agEventPackage
* @subpackage model
* @author     Nils Stolpe CUNY SPS
**/
class agEventFacilityResource extends PluginagEventFacilityResource
{
  public function __toString()
  {
    return $this->getAgFacilityResource()
                ->getAgFacility()
                ->getFacilityName()
                . ': ' .
           $this->getAgFacilityResource()
                ->getAgFacilityResourceType()
                ->getFacilityResourceType();
  }
}