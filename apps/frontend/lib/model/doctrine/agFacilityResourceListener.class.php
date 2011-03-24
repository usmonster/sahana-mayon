<?php

/**
 * Listener class for facility resource status changes
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 *
 * @author Charles Wisniewski, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
class agFacilityResourceListener extends Doctrine_Record_Listener
{

  protected $_options;

  public function __construct($options = array('disabled' => false))
  {
    $this->_options = $options;
  }

  /**
   * This listener implements a chained status-update feature that updates the statuses of
   * scenarioFacilityResources and eventFacilityResources. It should be noted that this action only
   * fires when the new status being applied causes a change in the boolean state [is_active] (part
   * of agFacilityResourceStatus).
   * @param Doctrine_Event $event
   */
  public function preSave(Doctrine_Event $event)
  {
    $invoker = $event->getInvoker();
    $facilityResourceId = array($invoker->id);
    $new_status = $invoker->facility_resource_status_id;

    // $newstatus is the new status id being applied
    // $facilityResourceId is the new facilityResourceId being applied

    //if ($confirmed == TRUE) chain updates to the child facilities in scenario and event
    if (TRUE == TRUE) {
      $result = agFacilityHelper::setFacilityResourceStatusOnUpdate($facilityResourceId, $new_status) ;
    }
  }

}