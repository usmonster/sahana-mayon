<?php

/**
 * agAccount form base class.
 *
 * @method agAccount getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseagAccountForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                => new sfWidgetFormInputHidden(),
      'account_name'      => new sfWidgetFormInputText(),
      'account_status_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agAccountStatus'), 'add_empty' => false)),
      'description'       => new sfWidgetFormInputText(),
      'created_at'        => new sfWidgetFormDateTime(),
      'updated_at'        => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'                => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'account_name'      => new sfValidatorString(array('max_length' => 64)),
      'account_status_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agAccountStatus'))),
      'description'       => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'created_at'        => new sfValidatorDateTime(),
      'updated_at'        => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agAccount', 'column' => array('account_name')))
    );

    $this->widgetSchema->setNameFormat('ag_account[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'agAccount';
  }

}