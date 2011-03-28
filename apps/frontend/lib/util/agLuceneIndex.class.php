<?php

/**
 *
 * Normalizing import data.
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
 *
 */
class agLuceneIndex
{

  private static $indexModels;

  function __construct($indexModels)
  {
    self::$indexModels = $indexModels;
  }

  public function indexAll($models = null)
  {
    chdir(sfConfig::get('sf_root_dir')); // Trick plugin into thinking you are in a project directory
    $dispatcher = sfContext::getInstance()->getEventDispatcher();
    $task = new luceneReindexTask($dispatcher, new sfFormatter()); //this->dispatcher

    foreach (self::$indexModels as $indexModel) {
      $returnArray[$indexModel] = $task->run(array('model' => $indexModel), array('env' => 'all', 'connection' => 'doctrine', 'application' => 'frontend'));
    }
    return $returnArray;
  }

}

?>
