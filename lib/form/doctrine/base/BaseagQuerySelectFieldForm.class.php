<?php

/**
 * agQuerySelectField form base class.
 *
 * @method agQuerySelectField getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagQuerySelectFieldForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'             => new sfWidgetFormInputHidden(),
      'report_id'      => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agReport'), 'add_empty' => false)),
      'field_table'    => new sfWidgetFormInputText(),
      'select_field'   => new sfWidgetFormInputText(),
      'field_sequence' => new sfWidgetFormInputText(),
      'created_at'     => new sfWidgetFormDateTime(),
      'updated_at'     => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'             => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'report_id'      => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agReport'))),
      'field_table'    => new sfValidatorString(array('max_length' => 64)),
      'select_field'   => new sfValidatorString(array('max_length' => 64)),
      'field_sequence' => new sfValidatorInteger(),
      'created_at'     => new sfValidatorDateTime(),
      'updated_at'     => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agQuerySelectField', 'column' => array('report_id', 'field_table', 'select_field', 'field_sequence')))
    );

    $this->widgetSchema->setNameFormat('ag_query_select_field[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agQuerySelectField';
  }

}
