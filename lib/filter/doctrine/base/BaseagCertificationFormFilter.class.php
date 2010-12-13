<?php

/**
 * agCertification filter form base class.
 *
 * @package    AGASTI_CORE
 * @subpackage filter
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseagCertificationFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'certification'              => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'description'                => new sfWidgetFormFilterInput(),
      'certifying_organization_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agOrganization'), 'add_empty' => true)),
      'app_display'                => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'created_at'                 => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'                 => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
    ));

    $this->setValidators(array(
      'certification'              => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'description'                => new sfValidatorPass(array('required' => false)),
      'certifying_organization_id' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('agOrganization'), 'column' => 'id')),
      'app_display'                => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'created_at'                 => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'                 => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
    ));

    $this->widgetSchema->setNameFormat('ag_certification_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agCertification';
  }

  public function getFields()
  {
    return array(
      'id'                         => 'Number',
      'certification'              => 'Number',
      'description'                => 'Text',
      'certifying_organization_id' => 'ForeignKey',
      'app_display'                => 'Boolean',
      'created_at'                 => 'Date',
      'updated_at'                 => 'Date',
    );
  }
}
