<?php
/**
* apps/frontend/lib/packages/agEventPackage/lib/form/doctrine/agEventShiftForm.class.php
*
* @package    agEventPackage
* @subpackage model
* @author     Nils Stolpe CUNY SPS
**/
class agEventShiftForm extends PluginagEventShiftForm
{
  public function configure()
  {
    unset($this['ag_staff_event_list'],
          $this['created_at'],
          $this['updated_at']
    );
    $custDeco = new agWidgetFormSchemaFormatterShift($this->getWidgetSchema());
    $this->getWidgetSchema()->addFormFormatter('custDeco', $custDeco);
    $this->getWidgetSchema()->setFormFormatterName('custDeco');

    foreach($this->getWidgetSchema()->getFields() as $widget) {
      $widget->setAttribute('class', 'inputGray');
    }
  }
}
