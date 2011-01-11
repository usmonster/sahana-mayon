<?php

/**
 * agFoo filter form base class.
 *
 * @package    AGASTI_CORE
 * @subpackage filter
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseagFooFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'foo' => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'bar' => new sfWidgetFormFilterInput(array('with_empty' => false)),
    ));

    $this->setValidators(array(
      'foo' => new sfValidatorPass(array('required' => false)),
      'bar' => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('ag_foo_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agFoo';
  }

  public function getFields()
  {
    return array(
      'id'  => 'Number',
      'foo' => 'Text',
      'bar' => 'Text',
    );
  }
}
