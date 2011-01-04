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

    /*create new shifttemplatecontainerform*/
    $shiftTemplateContainer = new sfForm(array(), array());

    /* if the scenario already has existing shift templates, get them */
    if ($this->agShiftTemplates = Doctrine::getTable('agShiftTemplate')
            ->createQuery('agST')
            ->select('agST.*')
            ->from('agShiftTemplate agST')
            ->where('scenario_id = ?', $this->getOption('scenario_id'))
            ->execute())
    {
      /* for every existing shift template, create an agEmbeddedShiftTemplateForm and embed it into $facilitygroupResourceContainer */
      foreach ($this->agShiftTemplates as $shiftTemplate)
      {
        $shiftTemplateForm = new agEmbeddedShiftTemplateForm($shiftTemplate);
        $shiftTemplateId = $shiftTemplate->getId();
        $this->embedForm('existing' . $shiftTemplateId, $shiftTemplateForm);
        $this->widgetSchema->setLabel('existing' . $shiftTemplateId, false);
      }
    }

    /* create a blank shiftTemplateForm for each of the types to add */

    /* embed a new shift_template form into the shifttemplate container */

    /* embed the shift template container form into the scenario form */
    $this->scenario_id = $this->getOption('scenario_id');
    $shiftTemplateForm = new agEmbeddedShiftTemplateForm();
    $shiftTemplateForm->setDefault('scenario_id', $this->scenario_id);

    $this->embedForm('new',$shiftTemplateForm);
    $this->widgetSchema->setLabel(array('shift_template' => 'Shift Templates'));
    /* for each of the shift templates, provide 5 preview scenario shifts */
  }

  public function saveEmbeddedForms($con = null, $forms = null)
  {
    if (null === $forms) {
      $forms = $this->embeddedForms;
      }
    if (is_array($forms))
    {

      foreach ($forms as $key => $form)
      {

        if ($form instanceof agEmbeddedShiftTemplateForm)
        {
          if ($form->isNew())
          {
            $form->updateObject($this->values[$key]);
            $newShiftTemplate = $form->getObject();
            if ($newShiftTemplate->staff_resource_type_id && $newShiftTemplate->task_id
            && $newShiftTemplate->facility_resource_type_id && $newShiftTemplate->shift_template)
            {
              $newShiftTemplate->setScenarioId(1);//$this->getObject()->getId()
              $newShiftTemplate->save();
              //$this->getObject()->getAgShiftTemplate()->add($newShiftTemplate);
              unset($forms[$key]);
            }
            else
            {
              unset($forms[$key]);
            }
          }
          else {
            $form->updateObject($this->values[$key]);
            $oldShiftTemplate = $form->getObject();
            if ($oldShiftTemplate->staff_resource_type_id && $oldShiftTemplate->task_id
            && $oldShiftTemplate->facility_resource_type_id && $oldShiftTemplate->shift_template)
            {
              $form->getObject()->save();
            }
            else
            {
              $form->getObject()->delete();
            }
            unset($forms[$key]);
          }
        }

      }
    }
    return;// parent::saveEmbeddedForms($con, $forms);
  }
}
