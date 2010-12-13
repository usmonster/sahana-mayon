<?php

/**
 * agFacility form base class.
 *
 * @method agFacility getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagFacilityForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                             => new sfWidgetFormInputHidden(),
      'site_id'                        => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agSite'), 'add_empty' => false)),
      'facility_name'                  => new sfWidgetFormInputText(),
      'facility_code'                  => new sfWidgetFormInputText(),
      'created_at'                     => new sfWidgetFormDateTime(),
      'updated_at'                     => new sfWidgetFormDateTime(),
      'ag_facility_resource_type_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agFacilityResourceType')),
    ));

    $this->setValidators(array(
      'id'                             => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'site_id'                        => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agSite'))),
      'facility_name'                  => new sfValidatorString(array('max_length' => 64)),
      'facility_code'                  => new sfValidatorString(array('max_length' => 10)),
      'created_at'                     => new sfValidatorDateTime(),
      'updated_at'                     => new sfValidatorDateTime(),
      'ag_facility_resource_type_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agFacilityResourceType', 'required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorAnd(array(
        new sfValidatorDoctrineUnique(array('model' => 'agFacility', 'column' => array('site_id'))),
        new sfValidatorDoctrineUnique(array('model' => 'agFacility', 'column' => array('facility_name'))),
        new sfValidatorDoctrineUnique(array('model' => 'agFacility', 'column' => array('facility_code'))),
      ))
    );

    $this->widgetSchema->setNameFormat('ag_facility[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agFacility';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['ag_facility_resource_type_list']))
    {
      $this->setDefault('ag_facility_resource_type_list', $this->object->agFacilityResourceType->getPrimaryKeys());
    }

  }

  protected function doSave($con = null)
  {
    $this->saveagFacilityResourceTypeList($con);

    parent::doSave($con);
  }

  public function saveagFacilityResourceTypeList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['ag_facility_resource_type_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->agFacilityResourceType->getPrimaryKeys();
    $values = $this->getValue('ag_facility_resource_type_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('agFacilityResourceType', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('agFacilityResourceType', array_values($link));
    }
  }

}