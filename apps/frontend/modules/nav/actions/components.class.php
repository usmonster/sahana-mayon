<?php

class navComponents extends sfComponents
{

  /**
   * Executes the building of the navigation menu.
   */
  public function executeMenu()
  {
    $menu = new ioMenu();
    // Nils's & Charles's modifications:
    $route = sfContext::getInstance()->getRouting()->getCurrentRouteName();
    $this->toplinks = sfConfig::get('app_toplinks');
    $this->secondlinks = sfConfig::get('app_secondlinks');
    $this->thirdlinks = sfConfig::get('app_third_navpages');
    $b = 3;
    //

    foreach ($this->toplinks as $item) {
      $menu->addChild($item['name'], $item['route'], array('class' => 'menu1'));
      //echo link_to($item['name'], $item['route'], array('class' => 'navButton'));
//      foreach ($secondlinks as $secondlink) {
//      $a = 4;
//    }
    }

    foreach ($this->secondlinks as $item) {
      $toplink = $this->toplinks[$item['parent']];
      if (isset($toplink)) {
        $parent = $menu[$toplink['name']];
        $parent->addChild($item['name'], $item['route'], array('class' => 'menu2'));
      }
      //echo link_to($item['name'], $item['route'], array('class' => 'navButton'));
    }

    foreach ($this->thirdlinks as $item) {
      $secondlink = $this->secondlinks[$item['parent']];
      if (isset($secondlink)) {
        $topname = $this->toplinks[$secondlink['parent']]['name'];
        $parent = $menu[$topname][$secondlink['name']];
        $parent->addChild($item['name'], $item['route'], array('class' => 'menu3'));
      }
    }


    $this->menu = $menu;
  }

}