<?php
class navComponents extends sfComponents
{
  /**
   * Executes the building of the navigation menu.
   */
  public function executeMenu()
  {
    $route = sfContext::getInstance()->getRouting()->getCurrentRouteName();
    $this->pages = sfConfig::get('app_navpages');
    //unset($this->pages[$route]);
  }
}
