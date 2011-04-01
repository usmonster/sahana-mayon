<?php

/**
 * Global parameter form
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author     Nils Stolpe, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 *
 */
class agGlobalParamForm extends BaseagGlobalParamForm
{

  public function setup()
  {
    parent::setup();
    $this->setWidgets(array(
      'id' => new sfWidgetFormInputHidden(),
      'host_id' => new sfWidgetFormDoctrineChoice(array
        ('model' => $this->getRelatedModelName('agHost'), 'add_empty' => false)),
      'datapoint' => new sfWidgetFormInputText(),
      'value' => new sfWidgetFormTextarea(),
        //'created_at' => new sfWidgetFormDateTime(),
        //'updated_at' => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id' => new sfValidatorChoice(array
        (
        'choices' => array($this->getObject()->get('id')),
        'empty_value' => $this->getObject()->get('id'),
        'required' => false)
      ),
      'host_id' => new sfValidatorDoctrineChoice(array
        ('model' => $this->getRelatedModelName('agHost'))),
      'datapoint' => new sfValidatorString(array('max_length' => 128)),
      'value' => new sfValidatorString(array('max_length' => 128)),
        //'created_at' => new sfValidatorDateTime(),
        //'updated_at' => new sfValidatorDateTime(),
    ));
    $this->validatorSchema->setPostValidator(
        new sfValidatorDoctrineUnique(array
          (
          'model' => 'agGlobalParam', 'column' => array
            ('host_id', 'datapoint')
            )
        )
    );

    $this->widgetSchema->setNameFormat('ag_global_param[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();
  }

  public function configure()
  {
    parent::configure();
    unset($this['created_at'],
        $this['updated_at']
    );
  }

  public function getModelName()
  {
    return 'agGlobalParam';
  }

}