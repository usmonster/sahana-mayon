<?php

/**
 * agPersonLanguageCompetency form base class.
 *
 * @method agPersonLanguageCompetency getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagPersonLanguageCompetencyForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                     => new sfWidgetFormInputHidden(),
      'person_language_id'     => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agPersonMjAgLanguage'), 'add_empty' => false)),
      'language_format_id'     => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agLanguageFormat'), 'add_empty' => false)),
      'language_competency_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agLanguageCompetency'), 'add_empty' => false)),
      'created_at'             => new sfWidgetFormDateTime(),
      'updated_at'             => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'                     => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'person_language_id'     => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agPersonMjAgLanguage'))),
      'language_format_id'     => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agLanguageFormat'))),
      'language_competency_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agLanguageCompetency'))),
      'created_at'             => new sfValidatorDateTime(),
      'updated_at'             => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agPersonLanguageCompetency', 'column' => array('person_language_id', 'language_format_id')))
    );

    $this->widgetSchema->setNameFormat('ag_person_language_competency[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agPersonLanguageCompetency';
  }

}
