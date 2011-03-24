<?php

/**
 * provides event shift management functions
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author Charles Wisniewski, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
class agEventFacilityResourceStatusListener extends Doctrine_Record_Listener
{

  protected $_options;

  public function __construct($options = array('disabled' => false))
  {
    $this->_options = $options;
  }

  public function postSave(Doctrine_Event $event)
  {
    $invoker = $event->getInvoker();
    $eventFacilityResourceId = $invoker->event_facility_resource_id;
    $new_status = $invoker->facility_resource_allocation_status_id;

// $newstatus is the new status id being applied
// $eventFacilityResourceId is the new eventFacilityResourceId being applied

    if (in_array($new_status, agEventFacilityHelper::getFacilityResourceAllocationStatus('staffed', FALSE))) {
      // fire off your confirmation modal here --> would be awesome if it'd appear regardless of what action / page triggered it
//      $dispatcher = sfContext::getInstance()->getEventDispatcher();
//      $response = new sfResponse($dispatcher, $options);
//      sfContext::getInstance()->setResponse($response);
//      $confirmed = FALSE;

      //$modalWindow = new agModalWindow('are you sure?');  //this should pop off a
      //$modalWindow->dispatch();
      //$confirmed = $modalWindow->return;
      //if ($confirmed == TRUE)
      if (TRUE == TRUE) {
        agEventFacilityHelper::releaseEventFacilityResource($eventFacilityResourceId);

        //sfContext::getInstance()->getLogger()->notice($message);
        //$dispatcher = sfContext::getInstance() -> getEventDispatcher();

        
      }
    }
  }

}