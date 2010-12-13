<?php
/**
 * @file
 *
 * File containing ##MODULE_NAME##Actions class.
 */

/**
 * ##MODULE_NAME## actions.
 *
 * @package    ##PROJECT_NAME##
 * @class      ##MODULE_NAME##Actions
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: actions.class.php
 */
class ##MODULE_NAME##Actions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    $this->forward('default', 'module');
  }
}
