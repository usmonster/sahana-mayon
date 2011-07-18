<?php
/**
 *
 * Implements longitude and latitude fields for forms.
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author Nils Stolpe, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
class agEmbeddedGeoAddressForm extends agGeoCoordinateForm
{
  public function configure()
  {
    parent::configure();

    unset($this['created_at'], $this['updated_at']);

    $this->setValidator('longitude', new sfValidatorNumber(array('required' => false)));
    $this->setValidator('latitude', new sfValidatorNumber(array('required' => false)));
//    $this->setValidators(array(
//      'id'         => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
//      'longitude'  => new sfValidatorNumber(),
//      'latitude'   => new sfValidatorNumber(),
//      'created_at' => new sfValidatorDateTime(),
//      'updated_at' => new sfValidatorDateTime(),
//    ));

    $this->getWidget('latitude')->setLabel('Lat');
    $this->getWidget('latitude')->setAttribute('class', 'inputGray');

    $this->getWidget('longitude')->setLabel('Long');
    $this->getWidget('longitude')->setAttribute('class', 'inputGray');

    $custDeco = new agFormatterTopLabel($this->getWidgetSchema());
    $this->getWidgetSchema()->addFormFormatter('custDeco', $custDeco);
    $this->getWidgetSchema()->setFormFormatterName('custDeco');
  }
}
