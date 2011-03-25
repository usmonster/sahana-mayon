<?php

/**
 * Extends agGlobalParam
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author Usman Akeju, http://sps.cuny.edu
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
class agGlobalParam extends BaseagGlobalParam
{

  public function postDelete($event)
  {
    parent::postDelete($event);
    sfContext::getInstance()->getEventDispatcher()->notify(new sfEvent($this, 'global_param.param_updated'));
  }

  public function postSave($event)
  {
    parent::postSave($event);
    sfContext::getInstance()->getEventDispatcher()->notify(new sfEvent($this, 'global_param.param_updated'));
  }

}
