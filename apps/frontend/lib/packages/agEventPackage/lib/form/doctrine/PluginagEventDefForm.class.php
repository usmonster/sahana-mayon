<?php

/**
 * PluginagEvent form.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: sfDoctrineFormPluginTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class PluginagEventDefForm extends PluginagEventForm
{

  public function configure()
  {
    unset($this['created_at'],
        $this['updated_at'],
        $this['ag_affected_area_list'],
        $this['ag_scenario_list']
    );
  }

}