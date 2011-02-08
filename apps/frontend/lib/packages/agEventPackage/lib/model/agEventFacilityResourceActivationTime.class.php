<?php

/**
 * agEventFacilityResourceActivationTime
 * 
 * Extends BaseagEventFacilityResourceActivationTime to add listeners
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class agEventFacilityResourceActivationTime extends BaseagEventFacilityResourceActivationTime
{
  public function setTableDefinition()
  {
    parent::setTableDefinition() ;

    $this->addListener(new agEventFacilityResourceActivationTimeListener()) ;
  }
}