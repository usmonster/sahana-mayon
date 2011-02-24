<?php

/**
 * agEventFacilityResourceStatus
 *
 * Extends BaseagEventFacilityResourceStatus to add listeners
 *
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class agEventFacilityResourceStatus extends PluginagEventFacilityResourceStatus
{
  public function setTableDefinition()
  {
    parent::setTableDefinition() ;

    $this->addListener(new agEventFacilityResourceStatusListener());
  }

}