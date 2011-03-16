<?php

/**
 * PluginagEventShift form.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: sfDoctrineFormPluginTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
abstract class PluginagEventShiftForm extends BaseagEventShiftForm
{

  public function setup()
  {
    parent::setup();
        unset($this['ag_staff_event_list'],
          $this['created_at'],
          $this['updated_at']
         );
    $custDeco = new agWidgetFormSchemaFormatterShift($this->getWidgetSchema());
    $this->getWidgetSchema()->addFormFormatter('custDeco', $custDeco);
    $this->getWidgetSchema()->setFormFormatterName('custDeco');
  }

}
