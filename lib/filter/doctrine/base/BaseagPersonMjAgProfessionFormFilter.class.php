<?php

/**
 * agPersonMjAgProfession filter form base class.
 *
 * @package    AGASTI_CORE
 * @subpackage filter
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseagPersonMjAgProfessionFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'person_id'     => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agPerson'), 'add_empty' => true)),
      'profession_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agProfession'), 'add_empty' => true)),
      'title'         => new sfWidgetFormFilterInput(),
      'created_at'    => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'    => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
    ));

    $this->setValidators(array(
      'person_id'     => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('agPerson'), 'column' => 'id')),
      'profession_id' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('agProfession'), 'column' => 'id')),
      'title'         => new sfValidatorPass(array('required' => false)),
      'created_at'    => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'    => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
    ));

    $this->widgetSchema->setNameFormat('ag_person_mj_ag_profession_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agPersonMjAgProfession';
  }

  public function getFields()
  {
    return array(
      'id'            => 'Number',
      'person_id'     => 'ForeignKey',
      'profession_id' => 'ForeignKey',
      'title'         => 'Text',
      'created_at'    => 'Date',
      'updated_at'    => 'Date',
    );
  }
}
