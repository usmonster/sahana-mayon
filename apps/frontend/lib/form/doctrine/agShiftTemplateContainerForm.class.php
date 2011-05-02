<?php

/**
 * agStaffPerson form extends agPersonForm to include staff information
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
    //Embedding the new picture in the container
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

      unset($this['_csrf_token']);
      $shiftTemplates = agDoctrineQuery::create()
              ->from('agShiftTemplate a')
              ->where('a.scenario_id = ?', $this->scenario_id)
              ->execute(); //->getFirst();

    if (isset($shiftTemplates)) {
      $i = 0;
      foreach ($shiftTemplates as $shiftTemplate) {

        //$shiftTemplateForm = new agEmbeddedShiftTemplateForm($shiftTemplate);
        $shiftTemplateForm = new agSingleShiftTemplateForm($this->scenario_id, $shiftTemplate);
        $shiftTemplateForm->setDefault('facility_resource_type_id', $shiftTemplate->getFacilityResourceTypeId());// ->facility_resource_type_id);
        $shiftTemplateForm->setDefault('staff_resource_type_id', $shiftTemplate->getStaffResourceTypeId());
        //since we overload these widgets on construct with scenario default
//        $shiftTemplateForm->getWidgetSchema()->setIdFormat('shift_template_' . $i . '_%s');
        $shiftTemplateForm->getWidgetSchema()->setNameFormat('shift_template[' . $i . '][%s]');

//unset($staffResourceForm['created_at'], $staffResourceForm['updated_at']);
        $this->embedForm($i, $shiftTemplateForm);
        $this->getWidgetSchema()->setLabel($i, false);
        $i++;
      }

    } else {
      $shiftTemplateForm = new agSingleShiftTemplateForm($this->scenario_id);
      //unset($staffResourceForm['created_at'], $staffResourceForm['updated_at']);
      $shiftTemplateForm->getWidgetSchema()->setNameFormat('[0][%s]');
      $shiftTemplateForm->getWidgetSchema()->setIdFormat('0%s');
      $this->embedForm('0', $shiftTemplateForm);
      //$staffResourceForm->getWidgetSchema()->setLabel('', $value) ContainerForm->getWidgetSchema()->setLabel(
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