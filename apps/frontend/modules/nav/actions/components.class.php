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

    foreach ($this->toplinks as $toplink) {
      $menu->addChild($toplink['name'], $toplink['route'], array('class' => 'menu1'));
      //echo link_to($toplink['name'], $toplink['route'], array('class' => 'navButton'));
//      foreach ($secondlinks as $secondlink) {
//      $a = 4;
//    }
      foreach ($this->secondlinks as $secondlink) {
        if($secondlink['parent'] == $toplink['name']) {
          $menu[$toplink['name']]->addChild($secondlink['name'], $secondlink['route'], array('class' => 'menu2'));
          //echo link_to($secondlink['name'], $secondlink['route'], array('class' => 'navButton'));
        }
      }
    }
    $this->menu = $menu;
  }
}