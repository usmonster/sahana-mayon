<?php

/**
 * provides event shift management functions
 *
 * PHP Version 5
 *
 * LICENSE: This source file is subject to LGPLv3.0 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 *
 * @author Charles Wisniewski, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */


class agEventFacilityResourceStatusListener extends Doctrine_Record_Listener
{
  protected $_options ;

  public function __construct($options = array('disabled' => false))
  {
    $this->_options = $options ;
  }

  public function postSave(Doctrine_Event $event)
  {
    $invoker = $event->getInvoker() ;
    $eventFacilityResourceId = $invoker->event_facility_resource_id ;
    //$activationTime = $invoker->activation_time ;

//    $id = $event->getInvoker()->id ;
//    $currentRecord = $this->getTable()->find($id);
//    $eventFacilityResourceId = $currentRecord->event_facility_resource_id ;
//    $activationTime = $currentRecord->activation_time ;

    $message = "Caught postSave() event for agEventFacilityResourceStatusListener ( invoker->event_facility_resource_id = {$eventFacilityResourceId}, invoker->activation_time = {$activationTime} )" ;

    if (sfConfig::get('sf_logging_enabled'))
    {
      sfContext::getInstance()->getLogger()->info($message);
    }
  }
}