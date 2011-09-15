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
    $this->setWidget('event_name', new sfWidgetFormInputText(array(),array('class' => 'set250 inputGray')));
    $this->setWidget('zero_hour',new sfWidgetFormDateTime());
    $this->setValidator('zero_hour', new agValidatorDateTime());
    $this->setValidator('event_name', new sfValidatorRegex(
      array('required' => TRUE, 'pattern' => '/^[a-zA-Z0-9 _-]{4,64}$/', 'trim' => TRUE),
      array('invalid' => 'You must enter a valid name between 4 and 64 characters in length. Allowed Characters include: A-z, 0-9, ' .
        'space, underscore, and dash.')
    ));
    $this->setDefault('zero_hour', (time() + (72*60*60)));
  }
}