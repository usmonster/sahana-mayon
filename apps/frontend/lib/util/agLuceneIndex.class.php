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
   * An event-driven way to index models related to a specific event
   * @param sfEvent $event A symfony event
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

    self::indexModels($models, $context, $reset);
  }

  /**
   * Reindexes  models passed in the $models array
   * @param array $models An array of models to reindex
   * @param sfContext $context The sfContext instance to use
   * @param boolean $reset by default is 0, meaning the entire index is rewritten.
   * @todo maybe refactor to do all this stuff by reference? -UA
   */
  public static function indexModels(array $models, sfContext $context = NULL, $reset = 0)
  {
    ignore_user_abort(true);
    set_time_limit(0);

    if (is_null($context)) {
      $context = sfContext::getInstance();
    }

    $dispatcher = $context->getEventDispatcher();
    $task = new luceneReindexTask($dispatcher, new sfFormatter());

    $oldDir = getcwd();
    try {
      // "Tricks" plugin into thinking we're in a project directory
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
    } catch(Exception $e) {
      $this->sfContext->getLogger()->warning("Failed execute reindex task: \n" . $e->getMessage());
    }

    // restores context
    chdir($oldDir);
    sfContext::getInstance()->setResponse($context->getResponse());
  }

}

?>
