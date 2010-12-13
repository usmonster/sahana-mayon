<?php

/**
 * agBranch form base class.
 *
 * @method agBranch getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagBranchForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                   => new sfWidgetFormInputHidden(),
      'branch'               => new sfWidgetFormInputText(),
      'description'          => new sfWidgetFormInputText(),
      'created_at'           => new sfWidgetFormDateTime(),
      'updated_at'           => new sfWidgetFormDateTime(),
      'ag_organization_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agOrganization')),
    ));

    $this->setValidators(array(
      'id'                   => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'branch'               => new sfValidatorString(array('max_length' => 64)),
      'description'          => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'created_at'           => new sfValidatorDateTime(),
      'updated_at'           => new sfValidatorDateTime(),
      'ag_organization_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agOrganization', 'required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agBranch', 'column' => array('branch')))
    );

    $this->widgetSchema->setNameFormat('ag_branch[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agBranch';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['ag_organization_list']))
    {
      $this->setDefault('ag_organization_list', $this->object->agOrganization->getPrimaryKeys());
    }

  }

  protected function doSave($con = null)
  {
    $this->saveagOrganizationList($con);

    parent::doSave($con);
  }

  public function saveagOrganizationList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['ag_organization_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->agOrganization->getPrimaryKeys();
    $values = $this->getValue('ag_organization_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('agOrganization', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('agOrganization', array_values($link));
    }
  }

}