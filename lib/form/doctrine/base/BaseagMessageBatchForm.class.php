<?php

/**
 * agMessageBatch form base class.
 *
 * @method agMessageBatch getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagMessageBatchForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                => new sfWidgetFormInputHidden(),
      'event_id'          => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agEvent'), 'add_empty' => false)),
      'batch_template_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agBatchTemplate'), 'add_empty' => false)),
      'execution_time'    => new sfWidgetFormDateTime(),
      'created_at'        => new sfWidgetFormDateTime(),
      'updated_at'        => new sfWidgetFormDateTime(),
      'ag_entity_list'    => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agEntity')),
    ));

    $this->setValidators(array(
      'id'                => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'event_id'          => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agEvent'))),
      'batch_template_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agBatchTemplate'))),
      'execution_time'    => new sfValidatorDateTime(),
      'created_at'        => new sfValidatorDateTime(),
      'updated_at'        => new sfValidatorDateTime(),
      'ag_entity_list'    => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agEntity', 'required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agMessageBatch', 'column' => array('event_id', 'batch_template_id', 'execution_time')))
    );

    $this->widgetSchema->setNameFormat('ag_message_batch[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agMessageBatch';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['ag_entity_list']))
    {
      $this->setDefault('ag_entity_list', $this->object->agEntity->getPrimaryKeys());
    }

  }

  protected function doSave($con = null)
  {
    $this->saveagEntityList($con);

    parent::doSave($con);
  }

  public function saveagEntityList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['ag_entity_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->agEntity->getPrimaryKeys();
    $values = $this->getValue('ag_entity_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('agEntity', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('agEntity', array_values($link));
    }
  }

}