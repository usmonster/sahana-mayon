<?php

/**
 * agFacilityGroupTypeForm.class.php extended base class.
 *
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author Charles Wisniewski, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
class agFacilityGroupTypeForm extends BaseagFacilityGroupTypeForm
{

  /**
   * setup widgets, remove unneeded widgets no return
   */
  public function setup()
  {
    $this->setWidgets(
        array(
          'id' => new sfWidgetFormInputHidden(),
          'facility_group_type' => new sfWidgetFormInputText(),
          'description' => new sfWidgetFormInputText(),
        //'app_display'         => new sfWidgetFormInputCheckbox(),
//      'created_at'          => new sfWidgetFormDateTime(),
//      'updated_at'          => new sfWidgetFormDateTime(),
        )
    );

    $this->setValidators(
        array(
          'id' => new sfValidatorChoice(
              array(
                'choices' => array
                  ($this->getObject()->get('id')),
                'empty_value' => $this->getObject()->get('id'),
                'required' => false
              )
          ),
          'facility_group_type' => new sfValidatorString(
              array('max_length' => 30)
          ),
          'description' => new sfValidatorString(
              array('max_length' => 255, 'required' => false)
          ),
        //'app_display'         => new sfValidatorBoolean(array('required' => false)),
//      'created_at'          => new sfValidatorDateTime(),
//      'updated_at'          => new sfValidatorDateTime(),
        )
    );

    $this->validatorSchema->setPostValidator(
        new sfValidatorDoctrineUnique(
            array('model' => 'agFacilityGroupType', 'column' => array('facility_group_type'))
        )
    );

    $this->widgetSchema->setNameFormat('ag_facility_group_type[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);
  }

  /**
   *
   * @return string of the current models name
   */
  public function getModelName()
  {
    return 'agFacilityGroupType';
  }

}