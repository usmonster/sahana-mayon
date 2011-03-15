<?php

/**
 * agEventFacilityResourceActivationTime form.
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrinePluginFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class agEventFacilityResourceActivationTimeForm extends PluginagEventFacilityResourceActivationTimeForm
{
  public function configure()
  {
    $this->setWidget('operate_on', new agWidgetFormSelectCheckbox(array('choices' => array(null), 'label' => false), array('class' => 'checkToggle')));
  }
}
