<?php

/**
 * agGlobalParam this class extends the agGlobalParam object for various purposes
 *
 * LICENSE: This source file is subject to LGPLv3.0 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 *
 * @author     Charles Wisniewski, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
class agGlobalParam extends BaseagGlobalParam
{
  static public function loadglobalparams(){
    $globalparams = Doctrine::getTable('agGlobalParam')->findAll(Doctrine_CORE::HYDRATE_ARRAY);
    foreach ($globalparams as $globalparam) { //for each entry
      foreach($globalparam as $datapoint) {
        if (!defined($datapoint['datapoint'])) //for each datapoint/value pair
          define($datapoint['datapoint'], $datapoint['value']);
      }
    }
  }
  static public function getParam($desired_param){
    return $desired_param;
  }
}
