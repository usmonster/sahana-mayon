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
      1 => array('title' => '1. Name and Describe Scenario', 'url' => 'scenario/meta'),
      2 => array('title' => '2. Define needed Staff and Facility Types', 'url' => 'scenario/resourcetypes'),
      3 => array('title' => '3. Create Facility Groups', 'url' => 'scenario/fgroup'),
      4 => array('title' => '4. Staff Resource Requirements', 'url' => 'scenario/staffresources'),
      5 => array('title' => '5. Shift Templates & Shift Definition', 'url' => 'scenario/shifttemplates'),
      6 => array('title' => '6. Shift Review ', 'url' => 'scenario/shifts'),
      7 => array('title' => '7. Create Staff Pools', 'url' => 'scenario/staffpool' )
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
    $list = '<div id="stepper"><div>' . $this->steps[$this->getStep()]['title'] . '</div>';
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
    $list = $list . "</ul></div><div style=\"clear:both;\"></div><br />";

    return $list;

  }

  function getState()
  {
    $fun = $this->steps[$this->getStep()]['fun'];
    return $this->$fun();
  }



}
