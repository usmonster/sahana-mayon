<?php

/**
 * agPersonMjAgNationality form base class.
 *
 * @method agPersonMjAgNationality getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagPersonMjAgNationalityForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'             => new sfWidgetFormInputHidden(),
      'person_id'      => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agPerson'), 'add_empty' => false)),
      'nationality_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agNationality'), 'add_empty' => false)),
      'created_at'     => new sfWidgetFormDateTime(),
      'updated_at'     => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'             => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'person_id'      => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agPerson'))),
      'nationality_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agNationality'))),
      'created_at'     => new sfValidatorDateTime(),
      'updated_at'     => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agPersonMjAgNationality', 'column' => array('person_id', 'nationality_id')))
    );

    $this->widgetSchema->setNameFormat('ag_person_mj_ag_nationality[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agPersonMjAgNationality';
  }

}
