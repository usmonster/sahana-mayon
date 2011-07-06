<?php

/**
 * agGeoCoordinate form base class.
 *
 * @method agGeoCoordinate getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
class agEmbeddedGeoAddressForm extends agGeoCoordinateForm
{
  public function configure()
  {
    parent::configure();

    unset($this['created_at'], $this['updated_at']);

    $this->getWidget('latitude')->setLabel('Lat');
    $this->getWidget('latitude')->setAttribute('class', 'inputGray');

    $this->getWidget('longitude')->setLabel('Long');
    $this->getWidget('longitude')->setAttribute('class', 'inputGray');

    $custDeco = new agFormatterTopLabel($this->getWidgetSchema());
    $this->getWidgetSchema()->addFormFormatter('custDeco', $custDeco);
    $this->getWidgetSchema()->setFormFormatterName('custDeco');
  }

}
