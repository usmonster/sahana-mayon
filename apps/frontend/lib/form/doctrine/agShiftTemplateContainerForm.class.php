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
 */
class agShiftTemplateContainerForm extends sfForm
{

  public $scenario_id;

  public function __construct($scenario_id = null)
  {
    if ($scenario_id == null) {
      throw new LogicException('you must provide a scenario_id to construct a shift template form');
    } else {
      $this->scenario_id = $scenario_id;
    }
    parent::__construct(array(), array(), array());
  }

  public function addShiftTemplateForm($num)
  {
    
    $embed_form = new agSingleShiftTemplateForm($this->scenario_id);
    //$embed_form->getWidgetSchema()->setNameFormat('[' . $num . '][%s]');
    $this->embedForm($num, $embed_form);
    //Re-embedding the container
    
    // $this->embedForm('staff', $this->getEmbeddedForm('staff'));
  }

  public function bind(array $taintedValues = null, array $taintedFiles = null)
  {
    foreach ($taintedValues as $key => $newShiftTemplate) {
      if (!isset($this[$key])) {
        $this->addShiftTemplateForm($key);
      }
    }
    parent::bind($taintedValues, $taintedFiles);
  }
    public function configure(){
      $this->getWidgetSchema()->setNameFormat('shift_template[%s]');
      unset($this['_csrf_token']);
      $shiftTemplates = agDoctrineQuery::create()
              ->from('agShiftTemplate a')
              ->where('a.scenario_id = ?', $this->scenario_id)
              ->execute(); //->getFirst();

    if (count($shiftTemplates) > 0) {
      $i = 0;
      foreach ($shiftTemplates as $shiftTemplate) {

        //$shiftTemplateForm = new agEmbeddedShiftTemplateForm($shiftTemplate);
        $shiftTemplateForm = new agSingleShiftTemplateForm($this->scenario_id, $shiftTemplate);
        $shiftTemplateForm->setDefault('facility_resource_type_id', $shiftTemplate->getFacilityResourceTypeId());// ->facility_resource_type_id);
        $shiftTemplateForm->setDefault('staff_resource_type_id', $shiftTemplate->getStaffResourceTypeId());
        $shiftTemplateForm->getWidgetSchema()->setNameFormat('shift_template[' . $i . '][%s]');

        $this->embedForm($i, $shiftTemplateForm);
        $this->getWidgetSchema()->setLabel($i, false);
        $i++;
      }

    } else {
      $shiftTemplateForm = new agSingleShiftTemplateForm($this->scenario_id);
      $shiftTemplateForm->getWidgetSchema()->setNameFormat('shift_template[0][%s]');
      $foo = agGlobal::getParam('default_shift_length');
      $shiftTemplateForm->setDefault('task_length_minutes', agGlobal::getParam('default_shift_length'));
      $shiftTemplateForm->setDefault('break_length_minutes', agGlobal::getParam('default_shift_break_length'));
      $shiftTemplateForm->setDefault('minutes_start_to_facility_activation', agGlobal::getParam('default_shift_minutes_start_to_facility_activation'));
      $shiftTemplateForm->setDefault('shift_repeats',agGlobal::getParam('default_shift_repeats'));
      $shiftTemplateForm->setDefault('max_staff_repeat_shifts', agGlobal::getParam('default_shift_max_staff_repeat_shifts'));

      $this->embedForm('0', $shiftTemplateForm);
      $this->getWidgetSchema()->setLabel('0', false);

//handle for creation of more than just the one form.. or have it come in through jquery
    }
    $this->getValidatorSchema()->setOption('allow_extra_fields', true);
    $this->getValidatorSchema()->setOption('filter_extra_fields', false);

    }
  public function saveEmbeddedForms($con = null, $forms = null)
  {
    if (null === $forms) {
      $forms = $this->embeddedForms;
    }
    if (is_array($forms)) {

      foreach ($forms as $key => $form) {

        if ($form instanceof agSingleShiftTemplateForm) {
          if ($form->isNew()) {
            $form->updateObject($this->values[$key]);
            $form->getObject()->scenario_id = $this->scenario_id;
            $newShiftTemplate = $form->getObject();
            if ($newShiftTemplate->staff_resource_type_id && $newShiftTemplate->task_id
                && $newShiftTemplate->facility_resource_type_id) {
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
              $form->getObject()->scenario_id = $this->scenario_id;
              $form->getObject()->save();
            } else {
              //$form->getObject()->delete(); don't delete this way :\
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