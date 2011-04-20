<?php

/**
 * agEventAffectedArea form base class.
 *
 * @method agEventAffectedArea getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagEventAffectedAreaForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'               => new sfWidgetFormInputHidden(),
      'event_id'         => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agEvent'), 'add_empty' => false)),
      'affected_area_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agAffectedArea'), 'add_empty' => false)),
      'created_at'       => new sfWidgetFormDateTime(),
      'updated_at'       => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'               => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'event_id'         => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agEvent'))),
      'affected_area_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agAffectedArea'))),
      'created_at'       => new sfValidatorDateTime(),
      'updated_at'       => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agEventAffectedArea', 'column' => array('event_id', 'affected_area_id')))
    );

    $this->widgetSchema->setNameFormat('ag_event_affected_area[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agEventAffectedArea';
  }

}
