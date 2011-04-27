<?php

/**
 * Agasti Staff Pool Filter Form Class - A class to filter staff pool results
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
 * */
class agStaffPoolFilterForm extends sfForm
{

  public $scenario_id;

  public function __construct($scenario_id = null)
  {
    if ($scenario_id == null) {
      throw new LogicException('you must provide a scenario_id to construct a filter form');
    } else {
      $this->scenario_id = $scenario_id;
    }
    parent::__construct(array(), array(), array());
  }

  public function setup()
  {
      $dsrt = agScenarioResourceHelper::returnDefaultStaffResourceTypes($this->scenario_id);
      if (count($dsrt) > 1) {
        $defaultStaffResourceTypes = $dsrt;
      } else {
        $defaultStaffResourceTypes =
                agDoctrineQuery::create()
                ->select('srt.id, srt.staff_resource_type')
                ->from('agStaffResourceType srt')
                ->execute(array(), Doctrine_Core::HYDRATE_SCALAR);
      }
      $defaultStaffTypes = array('' => '');
      foreach($defaultStaffResourceTypes as $dsrt){
        $defaultStaffTypes[$dsrt['srt_id']] = $dsrt['srt_staff_resource_type'];
      }

    $this->setWidgets(
        array(
            'agStaffResourceType.staff_resource_type' =>
          new sfWidgetFormChoice(array('choices' => $defaultStaffTypes),
              array('label' => 'Staff Type','class' => 'filter')
              )
      , 'agOrganization.organization' => new sfWidgetFormDoctrineChoice(
          array(
            'model' => 'agOrganization',
            'method' => 'getOrganization',
            'label' => 'Staff Organization',
            'add_empty' => true
      ),array('class' => 'filter'))));

//          )));

//    $this->setWidgets(array('agStaffResourceType.staff_resource_type' => new sfWidgetFormDoctrineChoice(
//          array(
//            'model' => 'agStaffResourceType',
//            'method' => 'getStaffResourceType',
//            'label' => 'Staff Type',
//
//            'add_empty' => true,
//            //'query', agDoctrineQuery::create()->select('a.id')->from('agDefaultScenarioStaffResourceType a')->where('a.scenario_id = ?', $this->scenario_id)
//          ),array('class' => 'filter'))

//set up inputs for form

  }

}