<?php

/**
 * agSearch form base class.
 *
 * @method agSearch getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagSearchForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'               => new sfWidgetFormInputHidden(),
      'search_type_id'   => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agSearchType'), 'add_empty' => false)),
      'search_hash'      => new sfWidgetFormInputText(),
      'search_name'      => new sfWidgetFormInputText(),
      'search_condition' => new sfWidgetFormTextarea(),
      'created_at'       => new sfWidgetFormDateTime(),
      'updated_at'       => new sfWidgetFormDateTime(),
      'ag_report_list'   => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agReport')),
    ));

    $this->setValidators(array(
      'id'               => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'search_type_id'   => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agSearchType'))),
      'search_hash'      => new sfValidatorString(array('max_length' => 128)),
      'search_name'      => new sfValidatorString(array('max_length' => 64)),
      'search_condition' => new sfValidatorString(),
      'created_at'       => new sfValidatorDateTime(),
      'updated_at'       => new sfValidatorDateTime(),
      'ag_report_list'   => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agReport', 'required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agSearch', 'column' => array('search_name')))
    );

    $this->widgetSchema->setNameFormat('ag_search[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agSearch';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['ag_report_list']))
    {
      $this->setDefault('ag_report_list', $this->object->agReport->getPrimaryKeys());
    }

  }

  protected function doSave($con = null)
  {
    $this->saveagReportList($con);

    parent::doSave($con);
  }

  public function saveagReportList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['ag_report_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->agReport->getPrimaryKeys();
    $values = $this->getValue('ag_report_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('agReport', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('agReport', array_values($link));
    }
  }

}
