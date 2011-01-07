<?php
class navComponents extends sfComponents
{
  /**
   * Executes the building of the navigation menu.
   */
  public function executeMenu()
  {
    $route = sfContext::getInstance()->getRouting()->getCurrentRouteName();
    $this->toplinks = sfConfig::get('app_toplinks');
    $this->secondlinks = sfConfig::get('app_second_navpages');
    $b = 3;
//    foreach ( as ) {
//
//    }
    //unset($this->pages[$route]);
  }
}
