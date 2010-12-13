<?php

/**
 * agOrganization form base class.
 *
 * @method agOrganization getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagOrganizationForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'             => new sfWidgetFormInputHidden(),
      'organization'   => new sfWidgetFormInputText(),
      'description'    => new sfWidgetFormInputText(),
      'entity_id'      => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agEntity'), 'add_empty' => false)),
      'created_at'     => new sfWidgetFormDateTime(),
      'updated_at'     => new sfWidgetFormDateTime(),
      'ag_branch_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agBranch')),
    ));

    $this->setValidators(array(
      'id'             => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'organization'   => new sfValidatorString(array('max_length' => 128)),
      'description'    => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'entity_id'      => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agEntity'))),
      'created_at'     => new sfValidatorDateTime(),
      'updated_at'     => new sfValidatorDateTime(),
      'ag_branch_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agBranch', 'required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorAnd(array(
        new sfValidatorDoctrineUnique(array('model' => 'agOrganization', 'column' => array('organization'))),
        new sfValidatorDoctrineUnique(array('model' => 'agOrganization', 'column' => array('entity_id'))),
      ))
    );

    $this->widgetSchema->setNameFormat('ag_organization[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agOrganization';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['ag_branch_list']))
    {
      $this->setDefault('ag_branch_list', $this->object->agBranch->getPrimaryKeys());
    }

  }

  protected function doSave($con = null)
  {
    $this->saveagBranchList($con);

    parent::doSave($con);
  }

  public function saveagBranchList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['ag_branch_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->agBranch->getPrimaryKeys();
    $values = $this->getValue('ag_branch_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('agBranch', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('agBranch', array_values($link));
    }
  }

}