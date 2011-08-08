<?php

/**
 * agReport form base class.
 *
 * @method agReport getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagReportForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                    => new sfWidgetFormInputHidden(),
      'report_name'           => new sfWidgetFormInputText(),
      'report_description'    => new sfWidgetFormInputText(),
      'created_at'            => new sfWidgetFormDateTime(),
      'updated_at'            => new sfWidgetFormDateTime(),
      'ag_lucene_search_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agSearch')),
    ));

    $this->setValidators(array(
      'id'                    => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'report_name'           => new sfValidatorString(array('max_length' => 64)),
      'report_description'    => new sfValidatorPass(array('required' => false)),
      'created_at'            => new sfValidatorDateTime(),
      'updated_at'            => new sfValidatorDateTime(),
      'ag_lucene_search_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agSearch', 'required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agReport', 'column' => array('report_name')))
    );

    $this->widgetSchema->setNameFormat('ag_report[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agReport';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['ag_lucene_search_list']))
    {
      $this->setDefault('ag_lucene_search_list', $this->object->agLuceneSearch->getPrimaryKeys());
    }

  }

  protected function doSave($con = null)
  {
    $this->saveagLuceneSearchList($con);

    parent::doSave($con);
  }

  public function saveagLuceneSearchList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['ag_lucene_search_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->agLuceneSearch->getPrimaryKeys();
    $values = $this->getValue('ag_lucene_search_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('agLuceneSearch', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('agLuceneSearch', array_values($link));
    }
  }

}
