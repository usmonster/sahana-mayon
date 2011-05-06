<?php

/**
 *
 * Provides a way for the application to reindex data records after import or install/data-load
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

  function __construct(array $indexModels)
  {
    self::$indexModels = $indexModels;
  }

/**
 * @todo remove $models param since it's unused? of make the function+class static? ask Charles. -UA
 * @param array $models the model(s) to be reindexed
 * @param boolean $reset by default is 0, meaning the entire index is rewritten.
 * @return results of the reindexing command, keyed by the model name
 */
  public function indexAll($models = null, $reset=0)
  {
    chdir(sfConfig::get('sf_root_dir')); // Trick plugin into thinking you are in a project directory
    $dispatcher = sfContext::getInstance()->getEventDispatcher();
    $task = new luceneReindexTask($dispatcher, new sfFormatter()); //this->dispatcher

    foreach (self::$indexModels as $indexModel) {
      $returnArray[$indexModel] = $task->run(array('model' => $indexModel), array('reset' => $reset, 'env' => 'all', 'connection' => 'doctrine', 'application' => 'frontend'));
    }
    return $returnArray;
  }

}

?>
