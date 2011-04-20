<?php

/**
 * agLuceneSearchType form base class.
 *
 * @method agLuceneSearchType getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagLuceneSearchTypeForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                 => new sfWidgetFormInputHidden(),
      'lucene_search_type' => new sfWidgetFormInputText(),
      'description'        => new sfWidgetFormInputText(),
      'app_display'        => new sfWidgetFormInputCheckbox(),
      'created_at'         => new sfWidgetFormDateTime(),
      'updated_at'         => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'                 => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'lucene_search_type' => new sfValidatorString(array('max_length' => 64)),
      'description'        => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'app_display'        => new sfValidatorBoolean(array('required' => false)),
      'created_at'         => new sfValidatorDateTime(),
      'updated_at'         => new sfValidatorDateTime(),
    ));

    $this->widgetSchema->setNameFormat('ag_lucene_search_type[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agLuceneSearchType';
  }

}
