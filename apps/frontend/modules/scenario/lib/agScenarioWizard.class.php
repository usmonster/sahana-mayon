<?php

/**
 * Agasti Scenario Wizard Class - A class to generate and control a workflow
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

class agScenarioWizard
{

  function __construct(&$WIZARD_OP)
  {
    $this->steps = array(
      1 => array('title' => 'Name and Describe Scenario', 'url' => 'scenario/meta'),
      2 => array('title' => 'Select Staff and Facility Types', 'url' => 'scenario/resourcetypes'),
      3 => array('title' => 'Create Facility Groups', 'url' => 'scenario/listgroup'),
      4 => array('title' => 'Set Staff Resource Requirements', 'url' => 'scenario/staffresources'),
      5 => array('title' => 'Define Staff Pool', 'url' => 'scenario/staffpool' ),
      6 => array('title' => 'Create Shift Templates', 'url' => 'scenario/shifttemplates'),
      7 => array('title' => 'Review Shifts ', 'url' => 'scenario/shifts'),
      8 => array('title' => 'Scenario Review ', 'url' => 'scenario/review')
    );

    $this->WIZARD_OP = &$WIZARD_OP;
  }

  function getConfig($name, $default = null)
  {
//if entry method to this function is admin/config instead of install, set the global
    return isset($this->WIZARD_OP[$name]) ? $this->WIZARD_OP[$name] : $default;
  }

  function setConfig($name, $value)
  {
    return ($this->WIZARD_OP[$name] = $value);
  }

  function getStep()
  {
    return $this->getConfig('step', 1);
  }


  function getList()
  {
    /**
     * this is a simple html constructor, takes our array and
     * generates a series of list items
     */
    sfProjectConfiguration::getActive()->loadHelpers(array ('Helper','Url', 'Asset', 'Tag'));
    $list = '<div id="stepper"><div class="stepHead">Scenario Creation Wizard </div><div>' . $this->steps[$this->getStep()]['title'] . '</div>';
    $list .= '<ul class="stepperList">';
    foreach ($this->steps as $id => $data) {
      if ($id < $this->getStep()){
        $style = 'completed';
        $link = url_for($data['url'] . '?id=' . $this->getConfig('scenario_id'));
      }
      elseif($id == $this->getStep())
      {
        $link = null;
        $style = 'current';
      }
      else{
        $style = null;
        $link = null;
      }
      if($link == null)
        $list = $list . '<li class="' . $style . '" title="' . $data['title'] . '">' . $id . '</li>';
      else
        $list = $list . '<li class="' . $style . '" title="' . $data['title'] . '">' . '<a href="' . $link . '">' . $id . '</a></li>';
    }
    $list = $list . "<li class=\"altLItext\"></li></ul></div><div style=\"clear:both;\"></div><br />";

    return $list;

  }

  function getState()
  {
    $fun = $this->steps[$this->getStep()]['fun'];
    return $this->$fun();
  }



}
