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
    $this->toplinks = sfConfig::get('app_menu_top');
    $this->secondlinks = sfConfig::get('app_menu_children');
    $this->thirdlinks = sfConfig::get('app_menu_grandchildren');
    //

    foreach ($this->toplinks as $item) {
      $menu->addChild($item['label'], $item['route'], array('class' => 'menu1'));
      //echo link_to($item['label'], $item['route'], array('class' => 'navButton'));
//      foreach ($secondlinks as $secondlink) {
//      $a = 4;
//    }
    }

    foreach ($this->secondlinks as $item) {
      $toplink = $this->toplinks[$item['parent']];
      if (isset($toplink)) {
        $parent = $menu[$toplink['label']];
        $parent->addChild($item['label'], $item['route'], array('class' => 'menu2'));
      }
      //echo link_to($item['label'], $item['route'], array('class' => 'navButton'));
    }

    foreach ($this->thirdlinks as $item) {
      if(preg_match('/^[a-z0-9\/\:]*\/\:[a-z0-9]+/i', $item['route'])) {
        $goop = explode('/', $item['route']);
        foreach($goop as $g){
          if(preg_match('/^\:\w+/', $g)) {
            $matches[] = $g;
          }
        }
      }
      $secondlink = $this->secondlinks[$item['parent']];
      if (isset($secondlink)) {
        $topname = $this->toplinks[$secondlink['parent']]['label'];
        $parent = $menu[$topname][$secondlink['label']];
        $parent->addChild($item['label'], $item['route'], array('class' => 'menu3'));
      }
    }
      chdir(sfConfig::get('sf_root_dir')); // Trick plugin into thinking you are in a project directory
      $task = new sfAppRoutesTask($this->dispatcher, new sfFormatter());
      $task->run(array('application' => 'frontend'), array());
      $routing = new sfPatternRouting($this->dispatcher);
      $routes = $routing->getRoutes();
//      $x = sfRouting::getRoutes();
    $this->menu = $menu;
  }
}