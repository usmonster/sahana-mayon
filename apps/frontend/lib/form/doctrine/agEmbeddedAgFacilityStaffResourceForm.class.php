<?php

/**
 * agEmbeddedAgFacilityStaffResourceForm extends agFacilityStaffResourceForm.
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author  Nils Stolpe, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
class agEmbeddedAgFacilityStaffResourceForm extends agFacilityStaffResourceForm
{

  public function configure(){

        $this->setWidgets(
        array(
          //'id' => new sfWidgetFormInputHidden(),
          'scenario_facility_resource_id' => new sfWidgetFormInputHidden(),
          'staff_resource_type_id' => new sfWidgetFormInputHidden(),
          'minimum_staff' => new sfWidgetFormInputText(
              array('label' => 'Min'),
              array('class' => 'inputGraySmall')
          ),
          'maximum_staff' => new sfWidgetFormInputText(
              array('label' => 'Max'),
              array('class' => 'inputGraySmall')
          ),
        //'created_at'=> new sfWidgetFormDateTime(),
        //'updated_at'=> new sfWidgetFormDateTime(),
        )
    );

    $this->setValidators(
        array(
          //'id' => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')),
          //  'empty_value' => $this->getObject()->get('id'), 'required' => false)),
          'scenario_facility_resource_id' => new sfValidatorDoctrineChoice(
              array('model' => $this->getRelatedModelName('agScenarioFacilityResource'))
          ),
          'staff_resource_type_id' => new sfValidatorDoctrineChoice(
              array('model' => $this->getRelatedModelName('agStaffResourceType'))
          ),
          'minimum_staff' => new sfValidatorInteger(),
          'maximum_staff' => new sfValidatorInteger(),
        //'created_at'                    => new sfValidatorDateTime(),
        //'updated_at'                    => new sfValidatorDateTime(),
        )
    );
        //form field group
    $staffResourceFormDeco = new agFormFormatterInlineLabels2($this->getWidgetSchema());
              $this->getWidgetSchema()->addFormFormatter('staffResourceFormDeco', $staffResourceFormDeco);
              $this->getWidgetSchema()->setFormFormatterName('staffResourceFormDeco');


  }

}