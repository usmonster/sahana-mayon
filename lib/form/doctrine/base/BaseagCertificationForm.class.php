<?php

/**
 * agCertification form base class.
 *
 * @method agCertification getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagCertificationForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                         => new sfWidgetFormInputHidden(),
      'certification'              => new sfWidgetFormInputText(),
      'description'                => new sfWidgetFormInputText(),
      'certifying_organization_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agOrganization'), 'add_empty' => false)),
      'app_display'                => new sfWidgetFormInputCheckbox(),
      'created_at'                 => new sfWidgetFormDateTime(),
      'updated_at'                 => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'                         => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'certification'              => new sfValidatorInteger(),
      'description'                => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'certifying_organization_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agOrganization'))),
      'app_display'                => new sfValidatorBoolean(array('required' => false)),
      'created_at'                 => new sfValidatorDateTime(),
      'updated_at'                 => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agCertification', 'column' => array('certification', 'certifying_organization_id')))
    );

    $this->widgetSchema->setNameFormat('ag_certification[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agCertification';
  }

}