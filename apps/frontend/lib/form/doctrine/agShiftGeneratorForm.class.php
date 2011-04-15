<?php

/**
 * Creates Scenario Shift Template form
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author Charles Wisniewski CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 *
 */
class agShiftGeneratorForm extends sfForm
{

  public $facility_staff_resources;
  public $scenario_id;

  public function __construct($facility_staff_resources, $scenario_id)
  {
    $this->facility_staff_resources = $facility_staff_resources;
    $this->scenario_id = $scenario_id;
    parent::__construct(array(), array(), array());
  }

  public function setup()
  {
    //parent::setup();
    $this->validatorSchema->setOption('allow_extra_fields', true);
    $this->widgetSchema->setNameFormat('shift_template[%s]');
  }

  public function configure()
  {
    //we need to get all unique facility resources for every facility group in the scenario
    //this will be useful in the 'final stage', showing the user that
    //they do not have shift templates defined for those scenario/facility/staff types

    /* create new shifttemplatecontainerform */


    /* if the scenario already has existing shift templates, get them */
    $this->agShiftTemplates = Doctrine::getTable('agShiftTemplate')
            ->createQuery('agST')
            ->select('agST.*')
            ->from('agShiftTemplate agST')
            ->where('scenario_id = ?', $this->scenario_id)  //and where the facility_group_types / staff_resource_type combo is not in the scenario shifts
            //->andWhereNotIn('facility_resource_type_id', $this->facility_staff_resources)
            ->execute();

    if ($this->agShiftTemplates->count() > 0) {

      foreach ($this->agShiftTemplates as $shiftTemplate) {
        $shiftTemplateForm = new agEmbeddedShiftTemplateForm($shiftTemplate);
        $shiftTemplateId = $shiftTemplate->getId();
        $this->embedForm('existing' . $shiftTemplateId, $shiftTemplateForm);
        $this->widgetSchema->setLabel('existing' . $shiftTemplateId, false);



        foreach ($this->facility_staff_resources as $fsrKey => $fsrVal) {
          if ($shiftTemplate->staff_resource_type_id == $fsrVal['fsr_staff_resource_type_id'] && $shiftTemplate->facility_resource_type_id == $fsrVal['fr_facility_resource_type_id']) {
            unset($this->facility_staff_resources[$fsrKey]);
          }
        }
        //$offset should be $this->facility_staff_resrouces' keys
//            //where the SRT and the FRT != array_values... but 0 and 1 have to match...
        //remove that facility_staff_resource entry from $this->facility_staff_resources
      }
    }


    /* create a blank shiftTemplateForm for each of the types to add */
    /**
     * @todo put the top foreach and the bottom foreach together
     */
    foreach ($this->facility_staff_resources as $fsr) {
      $shiftGenForm = new agEmbeddedShiftTemplateForm();
      $shiftGenForm->setDefault('facility_resource_type_id', $fsr['fr_facility_resource_type_id']);
      $shiftGenForm->setDefault('staff_resource_type_id', $fsr['fsr_staff_resource_type_id']);
      $shiftGenForm->setDefault('scenario_id', $this->scenario_id);

      $this->embedForm('shift_gen' . $fsr['fr_facility_resource_type_id'] . $fsr['fsr_staff_resource_type_id'], $shiftGenForm);
      $this->widgetSchema->setLabel('shift_gen' . $fsr['fr_facility_resource_type_id'] . $fsr['fsr_staff_resource_type_id'], false);
      //now saving: the fun part
      //and set defaults.
      //also get facility resource types that don't have a min/max assigned, and let user know
    }
//    }

    /* embed a new shift_template form into the shifttemplate container */

    /* embed the shift template container form into the scenario form */
//    $shiftTemplateForm = new agEmbeddedShiftTemplateForm();
//    $shiftTemplateForm->setDefault('scenario_id', $this->scenario_id);
//
//    $this->embedForm('new', $shiftTemplateForm);
//    $this->widgetSchema->setLabel(array('shift_template' => 'Shift Templates'));
    /* for each of the shift templates, provide 5 preview scenario shifts */
  }

  public function saveEmbeddedForms($con = null, $forms = null)
  {
    if (null === $forms) {
      $forms = $this->embeddedForms;
    }
    if (is_array($forms)) {

      foreach ($forms as $key => $form) {

        if ($form instanceof agEmbeddedShiftTemplateForm) {
          if ($form->isNew()) {
            $form->updateObject($this->values[$key]);
            $newShiftTemplate = $form->getObject();
            if ($newShiftTemplate->staff_resource_type_id && $newShiftTemplate->task_id
                && $newShiftTemplate->facility_resource_type_id && $newShiftTemplate->shift_template) {
              $newShiftTemplate->save();
              //$this->getObject()->getAgShiftTemplate()->add($newShiftTemplate);
              unset($forms[$key]);
            } else {
              unset($forms[$key]);
            }
          } else {
            $form->updateObject($this->values[$key]);
            $oldShiftTemplate = $form->getObject();
            if ($oldShiftTemplate->staff_resource_type_id && $oldShiftTemplate->task_id
                && $oldShiftTemplate->facility_resource_type_id) {
              $form->getObject()->save();
            } else {
              $form->getObject()->delete();
            }
            unset($forms[$key]);
          }
        }
      }
    }
    return; // parent::saveEmbeddedForms($con, $forms); <-correct,
    //this should never have been here, sfForm will save nothing
  }

}
