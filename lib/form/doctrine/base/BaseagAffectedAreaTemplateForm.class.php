<?php

/**
 * agAffectedAreaTemplate form base class.
 *
 * @method agAffectedAreaTemplate getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagAffectedAreaTemplateForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'            => new sfWidgetFormInputHidden(),
      'affected_area' => new sfWidgetFormInputText(),
      'geo_id'        => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agGeo'), 'add_empty' => false)),
      'created_at'    => new sfWidgetFormDateTime(),
      'updated_at'    => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'            => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'affected_area' => new sfValidatorString(array('max_length' => 64)),
      'geo_id'        => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agGeo'))),
      'created_at'    => new sfValidatorDateTime(),
      'updated_at'    => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agAffectedAreaTemplate', 'column' => array('affected_area')))
    );

    $this->widgetSchema->setNameFormat('ag_affected_area_template[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agAffectedAreaTemplate';
  }

}
