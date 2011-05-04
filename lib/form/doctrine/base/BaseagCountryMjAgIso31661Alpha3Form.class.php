<?php

/**
 * agCountryMjAgIso31661Alpha3 form base class.
 *
 * @method agCountryMjAgIso31661Alpha3 getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagCountryMjAgIso31661Alpha3Form extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'country_id'          => new sfWidgetFormInputHidden(),
      'iso_31661_alpha3_id' => new sfWidgetFormInputHidden(),
      'created_at'          => new sfWidgetFormDateTime(),
      'updated_at'          => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'country_id'          => new sfValidatorChoice(array('choices' => array($this->getObject()->get('country_id')), 'empty_value' => $this->getObject()->get('country_id'), 'required' => false)),
      'iso_31661_alpha3_id' => new sfValidatorChoice(array('choices' => array($this->getObject()->get('iso_31661_alpha3_id')), 'empty_value' => $this->getObject()->get('iso_31661_alpha3_id'), 'required' => false)),
      'created_at'          => new sfValidatorDateTime(),
      'updated_at'          => new sfValidatorDateTime(),
    ));

    $this->widgetSchema->setNameFormat('ag_country_mj_ag_iso31661_alpha3[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agCountryMjAgIso31661Alpha3';
  }

}
