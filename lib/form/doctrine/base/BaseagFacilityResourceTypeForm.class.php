<?php

/**
 * agFacilityResourceType form base class.
 *
 * @method agFacilityResourceType getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagFacilityResourceTypeForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                          => new sfWidgetFormInputHidden(),
      'facility_resource_type'      => new sfWidgetFormInputText(),
      'facility_resource_type_abbr' => new sfWidgetFormInputText(),
      'description'                 => new sfWidgetFormInputText(),
      'app_display'                 => new sfWidgetFormInputCheckbox(),
      'created_at'                  => new sfWidgetFormDateTime(),
      'updated_at'                  => new sfWidgetFormDateTime(),
      'ag_facility_list'            => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agFacility')),
    ));

    $this->setValidators(array(
      'id'                          => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'facility_resource_type'      => new sfValidatorString(array('max_length' => 30)),
      'facility_resource_type_abbr' => new sfValidatorString(array('max_length' => 10)),
      'description'                 => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'app_display'                 => new sfValidatorBoolean(array('required' => false)),
      'created_at'                  => new sfValidatorDateTime(),
      'updated_at'                  => new sfValidatorDateTime(),
      'ag_facility_list'            => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agFacility', 'required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorAnd(array(
        new sfValidatorDoctrineUnique(array('model' => 'agFacilityResourceType', 'column' => array('facility_resource_type'))),
        new sfValidatorDoctrineUnique(array('model' => 'agFacilityResourceType', 'column' => array('facility_resource_type_abbr'))),
      ))
    );

    $this->widgetSchema->setNameFormat('ag_facility_resource_type[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agFacilityResourceType';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['ag_facility_list']))
    {
      $this->setDefault('ag_facility_list', $this->object->agFacility->getPrimaryKeys());
    }

  }

  protected function doSave($con = null)
  {
    $this->saveagFacilityList($con);

    parent::doSave($con);
  }

  public function saveagFacilityList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['ag_facility_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->agFacility->getPrimaryKeys();
    $values = $this->getValue('ag_facility_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('agFacility', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('agFacility', array_values($link));
    }
  }

}