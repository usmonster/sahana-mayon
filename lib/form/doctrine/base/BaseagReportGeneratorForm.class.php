<?php

/**
 * agReportGenerator form base class.
 *
 * @method agReportGenerator getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagReportGeneratorForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'               => new sfWidgetFormInputHidden(),
      'report_id'        => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agReport'), 'add_empty' => false)),
      'lucene_search_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agLuceneSearch'), 'add_empty' => false)),
      'created_at'       => new sfWidgetFormDateTime(),
      'updated_at'       => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'               => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'report_id'        => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agReport'))),
      'lucene_search_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agLuceneSearch'))),
      'created_at'       => new sfValidatorDateTime(),
      'updated_at'       => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agReportGenerator', 'column' => array('report_id')))
    );

    $this->widgetSchema->setNameFormat('ag_report_generator[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agReportGenerator';
  }

}
