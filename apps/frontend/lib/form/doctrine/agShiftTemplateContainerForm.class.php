<?php

/**
 * agShiftContainerForm extends sfForm to include an array of singleshifttemplateform
 *
 * @method agStaff getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     Nils Stolpe, CUNY SPS
 * @author     Charles Wisniewski, CUNY SPS
 * @author     Shirley Chan, CUNY SPS
 */
class agShiftTemplateContainerForm extends sfForm
{
  public $scenario_id;

  public function __construct($scenario_id = null, $requiredResourceCombo = null)
  {
    if ($scenario_id == null)
    {
      throw new LogicException('you must provide a scenario_id to construct a shift template form');
    } else {
      $this->scenario_id = $scenario_id;
    }

    $this->uniqRequiredResourceCombo = $requiredResourceCombo;
    
    parent::__construct(array(), array(), array());
  }

  public function addShiftTemplateForm($num)
  {
    $defaultShiftTaskLength = agGlobal::getParam('default_shift_task_length');
    $defaultShiftBreakLength = agGlobal::getParam('default_shift_break_length');
    $defaultShiftMinutesStartToFacilityActivation = agGlobal::getParam('default_shift_minutes_start_to_facility_activation');
    $defaultDaysInOperation = agGlobal::getParam('default_days_in_operation');
    $defaultShiftMaxStaffRepeatShifts = agGlobal::getParam('default_shift_max_staff_repeat_shifts');
    $defaultDeploymentAlgorithmId = agDoctrineQuery::create()
      ->select('da.id')
      ->from('agDeploymentAlgorithm da')
      ->where('da.deployment_algorithm = ?', agGlobal::getParam('default_deployment_algorithm'))
      ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);

    $embed_form = new agSingleShiftTemplateForm($this->scenario_id);
    //$embed_form->getWidgetSchema()->setNameFormat('[' . $num . '][%s]');
    $embed_form->setDefault('task_length_minutes', $defaultShiftTaskLength);
    $embed_form->setDefault('break_length_minutes', $defaultShiftBreakLength);
    $embed_form->setDefault('minutes_start_to_facility_activation', $defaultShiftMinutesStartToFacilityActivation);
    $embed_form->setDefault('days_in_operation', $defaultDaysInOperation);
    $embed_form->setDefault('max_staff_repeat_shifts', $defaultShiftMaxStaffRepeatShifts);
    $embed_form->setDefault('deployment_algorithm_id', $defaultDeploymentAlgorithmId);
    $this->embedForm($num, $embed_form);
    //Re-embedding the container
    
    // $this->embedForm('staff', $this->getEmbeddedForm('staff'));
  }

  public function bind(array $taintedValues = null, array $taintedFiles = null)
  {
    foreach ($taintedValues as $key => $newShiftTemplate)
    {
      if (!isset($this[$key]))
      {
        $this->addShiftTemplateForm($key);
      }
    }
    parent::bind($taintedValues, $taintedFiles);
  }

  public function configure()
  {
    $this->getWidgetSchema()->setNameFormat('shift_template[%s]');
    unset($this['_csrf_token']);

    $formCounter = 0;
    $requiredResourceCombo = $this->uniqRequiredResourceCombo;

    $shiftTemplates = agDoctrineQuery::create()
            ->from('agShiftTemplate a')
            ->where('a.scenario_id = ?', $this->scenario_id)
            ->execute(); //->getFirst();

    $defaultShiftTaskLength = agGlobal::getParam('default_shift_task_length');
    $defaultShiftBreakLength = agGlobal::getParam('default_shift_break_length');
    $defaultShiftMinutesStartToFacilityActivation = agGlobal::getParam('default_shift_minutes_start_to_facility_activation');
    $defaultDaysInOperation = agGlobal::getParam('default_days_in_operation');
    $defaultShiftMaxStaffRepeatShifts = agGlobal::getParam('default_shift_max_staff_repeat_shifts');
    $defaultDeploymentAlgorithmId = agDoctrineQuery::create()
      ->select('da.id')
      ->from('agDeploymentAlgorithm da')
      ->where('da.deployment_algorithm = ?', agGlobal::getParam('default_deployment_algorithm'))
      ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);

    if (count($shiftTemplates) > 0)
    {
      foreach ($shiftTemplates as $shiftTemplate)
      {
        // Remove from $requiredResourceCombo if a shift template is already defined for that combo.
        $existingTemplate = array($shiftTemplate->getStaffResourceTypeId(),
                                  $shiftTemplate->getFacilityResourceTypeId(),
                                  $shiftTemplate->getShiftStatusId());
        $pos = array_search($existingTemplate, $requiredResourceCombo);
        if ($pos !== FALSE)
        {
          unset($requiredResourceCombo[$pos]);
        }

        //$shiftTemplateForm = new agEmbeddedShiftTemplateForm($shiftTemplate);
        $shiftTemplateForm = new agSingleShiftTemplateForm($this->scenario_id, $shiftTemplate);
        $shiftTemplateForm->getWidgetSchema()->setNameFormat('shift_template[' . $formCounter . '][%s]');

        $this->embedForm($formCounter, $shiftTemplateForm);
        $this->getWidgetSchema()->setLabel($formCounter++, false);
      }
    }
    
    // Create new shift template for all predefined staff and facility resource types.
    if (count($requiredResourceCombo) > 0)
    {
      foreach ($requiredResourceCombo AS $combo)        
      {
        list($staffResourceTypeId, $facilityResourceTypeId, $shiftStatusId) = $combo;
        $shiftTemplateForm = new agSingleShiftTemplateForm($this->scenario_id);
        $shiftTemplateForm->getWidgetSchema()->setNameFormat('shift_template[' . $formCounter . '][%s]');
        $shiftTemplateForm->setDefault('facility_resource_type_id', $facilityResourceTypeId);
        $shiftTemplateForm->setDefault('staff_resource_type_id', $staffResourceTypeId);
        $shiftTemplateForm->setDefault('shift_status_id', $shiftStatusId);
        $shiftTemplateForm->setDefault('task_length_minutes', $defaultShiftTaskLength);
        $shiftTemplateForm->setDefault('break_length_minutes', $defaultShiftBreakLength);
        $shiftTemplateForm->setDefault('minutes_start_to_facility_activation', $defaultShiftMinutesStartToFacilityActivation);
        $shiftTemplateForm->setDefault('days_in_operation', $defaultDaysInOperation);
        $shiftTemplateForm->setDefault('max_staff_repeat_shifts', $defaultShiftMaxStaffRepeatShifts);
        $shiftTemplateForm->setDefault('deployment_algorithm_id', $defaultDeploymentAlgorithmId);

        $this->embedForm($formCounter, $shiftTemplateForm);
        $this->getWidgetSchema()->setLabel($formCounter++, false);
      }
    }
    
    // Create a blank new shift template if no shift template or predefined requirements defined.
    if ($formCounter == 0)
    {
      $shiftTemplateForm = new agSingleShiftTemplateForm($this->scenario_id);
      $shiftTemplateForm->getWidgetSchema()->setNameFormat('shift_template[0][%s]');
      $shiftTemplateForm->setDefault('task_length_minutes', $defaultShiftTaskLength);
      $shiftTemplateForm->setDefault('break_length_minutes', $defaultShiftBreakLength);
      $shiftTemplateForm->setDefault('minutes_start_to_facility_activation', $defaultShiftMinutesStartToFacilityActivation);
      $shiftTemplateForm->setDefault('days_in_operation',$defaultDaysInOperation);
      $shiftTemplateForm->setDefault('max_staff_repeat_shifts', $defaultShiftMaxStaffRepeatShifts);
      $shiftTemplateForm->setDefault('deployment_algorithm_id', $defaultDeploymentAlgorithmId);

      $this->embedForm('0', $shiftTemplateForm);
      $this->getWidgetSchema()->setLabel('0', false);

//handle for creation of more than just the one form.. or have it come in through jquery
    }
    $this->getValidatorSchema()->setOption('allow_extra_fields', true);
    $this->getValidatorSchema()->setOption('filter_extra_fields', false);
  }

//  public function deleteEmbeddedForms($num = null, $forms = null)
//  {
//    if (null === $forms)
//    {
//      $forms = $this->embeddedForms;
//    }
//
//    if (!empty($num))
//    {
//      $forms[$num]->updateObject($this->values[$num]);
//      unset($forms[$num]);
//    }
//
//    return;
//  }

  public function deleteShiftTemplates($shiftTemplateId)
  {
    $deleteCount = array();
    $deleteCount['scenarioShift'] = agDoctrineQuery::create()
      ->delete('agScenarioShift')
      ->where('originator_id = ?', $shiftTemplateId)
      ->execute();
    $deleteCount['shiftTempalte'] = agDoctrineQuery::create()
      ->delete('agShiftTemplate')
      ->where('id = ?', $shiftTemplateId)
      ->execute();
    return $deleteCount;
  }

  public function saveEmbeddedForms($deleteEmbeddedFormId = null, $con = null, $forms = null)
  {
    if (null === $forms)
    {
      $forms = $this->embeddedForms;
    }
    if (is_array($forms))
    {
      foreach ($forms as $key => $form)
      {

        if ($form instanceof agSingleShiftTemplateForm)
        {
          // Save shift template if no $deleteEmbeddedFormId is passed in.
          $form->updateObject($this->values[$key]);
          $form->getObject()->scenario_id = $this->scenario_id;
          $shiftTemplate = $form->getObject();

          if (!empty($deleteEmbeddedFormId) && $key == $deleteEmbeddedFormId)
          {
            // Do not save embedded form.  Delete embedded form and related records in db for
            // existing shift templates.
            if (!$form->isNew())
            {
              $this->deleteShiftTemplates($shiftTemplate->id);
            }
          } else {
            if ($shiftTemplate->staff_resource_type_id && $shiftTemplate->task_id
                && $shiftTemplate->facility_resource_type_id)
            {
              $shiftTemplate->save();
            }
          }
          unset($forms[$key]);
        }
      }
    }
    return; // parent::saveEmbeddedForms($con, $forms); <-correct,
    //this should never have been here, sfForm will save nothing
  }
//  public function saveEmbeddedForms($con = null, $forms = null)
//  {
//    if (null === $forms)
//    {
//      $forms = $this->embeddedForms;
//    }
//    if (is_array($forms))
//    {
//      foreach ($forms as $key => $form) {
//
//        if ($form instanceof agSingleShiftTemplateForm) {
//          if ($form->isNew()) {
//            $form->updateObject($this->values[$key]);
//            $form->getObject()->scenario_id = $this->scenario_id;
//            $newShiftTemplate = $form->getObject();
//            if ($newShiftTemplate->staff_resource_type_id && $newShiftTemplate->task_id
//                && $newShiftTemplate->facility_resource_type_id) {
//              $newShiftTemplate->save();
//              //$this->getObject()->getAgShiftTemplate()->add($newShiftTemplate);
//              unset($forms[$key]);
//            } else {
//              unset($forms[$key]);
//            }
//          } else {
//            $form->updateObject($this->values[$key]);
//            $form->getObject()->scenario_id = $this->scenario_id;
//            $oldShiftTemplate = $form->getObject();
//            if ($oldShiftTemplate->staff_resource_type_id && $oldShiftTemplate->task_id
//                && $oldShiftTemplate->facility_resource_type_id) {
//
//              $oldShiftTemplate->save();
//            } else {
//              //$form->getObject()->delete(); don't delete this way :\
//            }
//            unset($forms[$key]);
//          }
//        }
//      }
//    }
//    return; // parent::saveEmbeddedForms($con, $forms); <-correct,
//    //this should never have been here, sfForm will save nothing
//  }

}