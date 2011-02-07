<?php

/**
 * agShiftTemplate form.
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
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
    //this will be useful in the 'final stage', showing the user that they do not have shift templates
    //defined for those scenario/facility/staff types

    /* create new shifttemplatecontainerform */


    /* if the scenario already has existing shift templates, get them */

//    if ($this->agShiftTemplates = Doctrine::getTable('agShiftTemplate')
//            ->createQuery('agST')
//            ->select('agST.*')
//            ->from('agShiftTemplate agST')
//            ->where('scenario_id = ?', $this->getOption('scenario_id'))  //and where the facility_group_types / staff_resource_type combo is not in the scenario shifts
//            //->andWhereNotIn('facility_resource_type_id', $this->facility_staff_resources)
//            ->execute()) {
//            }
//            //where the SRT and the FRT != array_values... but 0 and 1 have to match...
//            //$staffresourcetypes = array_
//      /* for every existing shift template, create an agEmbeddedShiftTemplateForm and embed it into $facilitygroupResourceContainer */
//
//      //if a shift template exists with this facility_resource_type and this staff_resource_type,
//      //get the object and make the form with that object, else, make a blank object
//      //the if/else statement here will fail currently
//
//      //if in array match? what's the best way to join these up? create forms with the objects first
//      //and then a set of empties? with those carrying objects being unset from the array to make?
//      foreach ($this->agShiftTemplates as $shiftTemplate) {
//        $shiftTemplateForm = new agEmbeddedShiftTemplateForm($shiftTemplate);
//        $shiftTemplateId = $shiftTemplate->getId();
//        $this->embedForm('existing' . $shiftTemplateId, $shiftTemplateForm);
//        $this->widgetSchema->setLabel('existing' . $shiftTemplateId, false);
//      }
//    } else {

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
                && $oldShiftTemplate->facility_resource_type_id && $oldShiftTemplate->shift_template) {
              $form->getObject()->save();
            } else {
              $form->getObject()->delete();
            }
            unset($forms[$key]);
          }
        }
      }
    }
    return; // parent::saveEmbeddedForms($con, $forms); <-correct, this should never have been here, sfForm will save nothing
  }

}
