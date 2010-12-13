<?php

/**
 * agPersonCertification form base class.
 *
 * @method agPersonCertification getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagPersonCertificationForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'               => new sfWidgetFormInputHidden(),
      'person_id'        => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agPerson'), 'add_empty' => false)),
      'certification_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agCertification'), 'add_empty' => false)),
      'date_received'    => new sfWidgetFormDate(),
      'date_expires'     => new sfWidgetFormDate(),
      'created_at'       => new sfWidgetFormDateTime(),
      'updated_at'       => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'               => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'person_id'        => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agPerson'))),
      'certification_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agCertification'))),
      'date_received'    => new sfValidatorDate(),
      'date_expires'     => new sfValidatorDate(),
      'created_at'       => new sfValidatorDateTime(),
      'updated_at'       => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agPersonCertification', 'column' => array('person_id', 'certification_id', 'date_received')))
    );

    $this->widgetSchema->setNameFormat('ag_person_certification[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agPersonCertification';
  }

}