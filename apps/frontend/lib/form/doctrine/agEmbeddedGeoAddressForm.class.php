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

    $this->setValidator('longitude', new sfValidatorNumber(array('required' => false, 'min' => -180, 'max' => 180)));
    $this->setValidator('latitude', new sfValidatorNumber(array('required' => false, 'min' => -90, 'max' => 90)));
//    $this->setValidators(array(
//      'id'         => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
//      'longitude'  => new sfValidatorNumber(),
//      'latitude'   => new sfValidatorNumber(),
//      'created_at' => new sfValidatorDateTime(),
//      'updated_at' => new sfValidatorDateTime(),
//    ));


    $this->getWidget('latitude')->setAttribute('class', 'inputGray address-geo');
    $this->getWidget('longitude')->setAttribute('class', 'inputGray address-geo');

    $this->getWidgetSchema()->moveField('longitude', sfWidgetFormSchema::AFTER, 'latitude');

    $custDeco = new agFormatterTopLabel($this->getWidgetSchema());
    $this->getWidgetSchema()->addFormFormatter('custDeco', $custDeco);
    $this->getWidgetSchema()->setFormFormatterName('custDeco');

    $this->validatorSchema->setPostValidator(
      new sfValidatorCallback(array('callback' => array($this, 'checkStateBoth')))
    );
  }

  /*
   * A function to validate whether longitude and latitude have both been passed
   * in with the form. Errors when one is NULL and the other not NULL.
   */
  public function checkStateBoth($validator, $values)
  {
    if(($values['latitude'] == NULL && $values['longitude'] <> NULL) ||
       ($values['latitude'] <> NULL && $values['longitude'] == NULL)
    ) {
      throw new sfValidatorError($validator, 'Please set both latitude and longitude');
    }
    return $values;
  }
}
