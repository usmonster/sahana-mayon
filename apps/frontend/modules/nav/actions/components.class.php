<?php
class navComponents extends sfComponents
{
  /**
   * Executes the building of the navigation menu.
   */
  public function executeMenu()
  {
    // Nils's & Charles's modifications:
//    $route = sfContext::getInstance()->getRouting()->getCurrentRouteName();
//    $this->toplinks = sfConfig::get('app_toplinks');
//    $this->secondlinks = sfConfig::get('app_second_navpages');
//    $b = 3;

    $menu = new ioMenu();
    $menu->addChild('overview', '@homepage');

    $menu->addChild('Plan', '');

    $menu['Plan']->addChild('Scenario', '@scenario');
    $menu['Plan']['Scenario']->addChild('Scenario Staff Resources', '@scenario_show_facility_staff_resource?id=1');

    $menu['Plan']->addChild('Facility', '@facility');

    $menu->addChild('Prep', '');

    $menu['Prep']->addChild('Scenario', '@scenario');
    $menu['Prep']['Scenario']->addChild('Scenario Staff Resources', '@scenario_show_facility_staff_resource?id=1');

    //echo $menu->render();
    $this->menu = $menu;
  }
}
