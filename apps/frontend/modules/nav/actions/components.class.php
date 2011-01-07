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

    $menu->addChild('Facility', '@facility');
    $menu['Facility']->addChild('Scenario', '@scenario');
    $menu['Facility']->addChild('Staff', '@staff');

    echo $menu->render();
  }
}
