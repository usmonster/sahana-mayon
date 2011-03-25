<?php

/**
 * agStaff filter form base class.
 *
 * @package    AGASTI_CORE
 * @subpackage filter
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseagStaffFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'person_id'                   => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agPerson'), 'add_empty' => true)),
      'staff_status_id'             => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agStaffStatus'), 'add_empty' => true)),
      'ag_staff_resource_type_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agStaffResourceType')),
    ));

    $this->setValidators(array(
      'person_id'                   => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('agPerson'), 'column' => 'id')),
      'staff_status_id'             => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('agStaffStatus'), 'column' => 'id')),
      'ag_staff_resource_type_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agStaffResourceType', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('ag_staff_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function addAgStaffResourceTypeListColumnQuery(Doctrine_Query $query, $field, $values)
  {
    if (!is_array($values))
    {
      $values = array($values);
    }

    if (!count($values))
    {
      return;
    }

    $query
      ->leftJoin($query->getRootAlias().'.agStaffResource agStaffResource')
      ->andWhereIn('agStaffResource.staff_resource_type_id', $values)
    ;
  }

  public function getModelName()
  {
    return 'agStaff';
  }

  public function getFields()
  {
    return array(
      'id'                          => 'Number',
      'person_id'                   => 'ForeignKey',
      'staff_status_id'             => 'ForeignKey',
      'ag_staff_resource_type_list' => 'ManyKey',
    );
  }
}
