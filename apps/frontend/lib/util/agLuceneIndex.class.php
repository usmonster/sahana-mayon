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

  /**
   * @param boolean $reset by default is 0, meaning the entire index is rewritten.
   */
  public static function indexAll(sfEvent $event = null, $reset = 0)
  {
    if (isset($event)) {
      $action = $event->getSubject();
    }
    if (isset($action)) {
      $context = $action->getContext();
      $models = $action->getSearchedModels();
    } elseif (sfContext::hasInstance()) {
      $context = sfContext::getInstance();
    } else {
      return;
    }

    $dispatcher = $context->getEventDispatcher();
    $task = new luceneReindexTask($dispatcher, new sfFormatter());

    // "Tricks" plugin into thinking we're in a project directory
    $oldDir = getcwd();
    chdir(sfConfig::get('sf_root_dir'));

    if (isset($models)) {
      foreach ($models as $indexModel) {
        $task->run(
            array('model' => $indexModel),
            array(
              'reset' => $reset,
              'env' => 'all',
              'connection' => 'doctrine',
              'application' => 'frontend'
            )
        );
      }
    } else {
      $task->run(
          array(),
          array(
            'reset' => $reset,
            'env' => 'all',
            'connection' => 'doctrine',
            'application' => 'frontend'
          )
      );
    }

    // restores context
    chdir($oldDir);
    sfContext::getInstance()->setResponse($context->getResponse());
  }

}

?>
