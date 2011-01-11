<?php

/**
 * agStaff form base class.
 *
 * @method agStaff getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
class agStaffPersonForm extends agPersonForm
{
  public function setup()
  {
    parent::setup();
  }

  public function configure()
  {
    parent::configure();
   $staffResourceContainer = new sfForm(array(), array());
   //also       'staff_status_id'             => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agStaffStatus'), 'add_empty' => false)),
   //because currently it is coming in with every embeddedstafform
    /**
     *  if the staff already has resources and organizations assigned, get all the data for them
     *   */
   $gloogle = new agStaffResourceOrganizationForm();
   // Create the new agStaffForm() and configure its widgets.
   $floogle = new agStaffForm();
   $floogle->getWidgetSchema()->offsetGet('staff_status_id')->addOption('method', 'getStaffStatus');
   $floogle->getWidgetSchema()->offsetGet('person_id')->addOption('method', 'getNameString');

   $floogle->getWidgetSchema()->offsetSet('ag_staff_resource_type_list', new sfWidgetFormDoctrineChoice(array('multiple' => false, 'model' => 'agStaffResourceType')));

   $floogle->getWidgetSchema()->offsetUnset('created_at');
   $floogle->getWidgetSchema()->offsetUnset('updated_at');
   $floogle->getWidgetSchema()->offsetUnset('person_id');

   $floogle->getWidgetSchema()->setLabel('staff_status_id', 'Status');
   $floogle->getWidgetSchema()->setLabel('ag_staff_resource_type_list', 'Staff Type');

   // Check to see if there is already an agStaff object that can be used to fill the form.
   if ($staff = $this->getObject()->getAgStaff()->getFirst()) {
     // Set the form's object to $staff
     $floogle->getObject()->merge($staff);
     // Set the form defaults to $staff. updateDefaultsFromObject won't do this.
     $floogle->setDefaults($staff->toArray());
   }
   $this->embedForm('floogle', $floogle);
   $this->embedForm('gloogle', $gloogle);
   $staff_resources = array();
   if ($this->getObject()->getAgStaff()->getFirst()) {
     foreach($this->getObject()->getAgStaff()->getFirst()->getAgStaffResource() as $staffrec) {
       $staff_resources[] = $staffrec->getId();
     }
   }
   if($staff_resources){
     if ($this->agStaffResources = Doctrine::getTable('agStaffResourceOrganization')
              ->createQuery('agSRO')
              ->select('agSRO.*')
              ->from('agStaffResourceOrganization agSRO')
              ->where('staff_resource_id = ?', $staff_resources)
              ->execute()) {

        /**
         *  for every existing staff organization resource, create an agEmbeddedStaffForm and embed it into $staffResourceContainer
         *   */
        foreach ($this->agStaffResources as $staffResource) {
          $staffResourceForm = new agEmbeddedStaffForm($staffResource->getAgStaffResource()->getAgStaff());
          // Set $organization variable to the agOrganization() value associated w/
          // the staff member. The id value of this object is used to set the default for ag_organization_list.
          // Maybe the conditional here is unnecessary? Doesn't seem to come in here if the form is new.
          $organization = $this->getObject()->getAgStaff()->getFirst()->getAgStaffResource()->getFirst()->getAgStaffResourceOrganization()->getFirst()->getAgOrganization();
          if ($organization <> null) {
            $staffResourceForm->setDefault('ag_organization_list', $organization->id);
          }
          $staffResourceId = $staffResource->getId();
          $staffResourceContainer->embedForm($staffResourceId, $staffResourceForm);
          $staffResourceContainer->widgetSchema->setLabel($staffResourceId, false);
        }
      }
    }
    else{
    /**
     *  embed a form for new staff information entry
     *   */
      $staffResourceForm = new agEmbeddedStaffForm();

      $staffResourceContainer->embedForm('new', $staffResourceForm);
      $staffResourceContainer->widgetSchema->setLabel('new', false);
    
    }
    /**
     *  embed the facility resource container form into the facility form
     *   */
    $this->embedForm('staff', $staffResourceContainer);

  }
  protected function doSave($con = null)
  {
    //if($this->isNew()){
//    parent::doSave();
    //}
  }
// Going in here for all the embedded forms. Which makes sense, since this extends person. Maybe a check, return parent if it's not what we want?
  
  public function saveEmbeddedForms($con = null, $forms = null)
  {
    if (null === $forms) {
      $forms = $this->embeddedForms;
    }

    if (is_array($forms)) {
      foreach ($forms as $key => $form) {
        /**
         *  Staff Resource section
         * */
      if ($form instanceof sfForm) {
        $emforms = $form->embeddedForms;
      }
      $staffValues = $this->values;
      $staffValues = $staffValues['staff'];

      $staffExists = $this->getObject()->getAgStaff();
//            if(!$staffExists){
        $staff = new agStaff();

        //$newStaffResource->staff_status_id;// getValue('staff_status_id');
        //staff status id should be set only once (per staff member)

      //get all staff values from the form
      foreach ($emforms as $emkey => $emform) {
          if ($emform instanceof agEmbeddedStaffForm) {
            $newStaffResource = $emform->getObject();
            $staff->person_id = $this->getObject()->getIncremented(); //get the person_id that will exist.
            $staff->person_id = $this->getObject()->getId();
            $foo = $this->getObject()->id;
            $staff->staff_status_id = $staffValues[$emkey]['staff_status_id'][0];
            $staff->save();
          //$taintedValues  = $emform->getTaintedValues();

              //$this->getObject()->setAgStaff($staff);
              //we should have a staff id now bound through the person object
              //we have no agstaffresource yet!
              //$staff_id = $this->getObject()->staff_id;
//            }
//            else {
//              $staff = $staffExists->getFirst();
//            }
              //$this->getObject()->
              //we only need to get the first record of a person to staff
            if ($emform->isNew()) {
              //check to see if there is an existing staff member with this person_id
              if ($staffValues[$emkey]['ag_organization_list'][0] && $staffValues[$emkey]['ag_staff_resource_type_list'][0]) {
                //$this->getObject()->getAgStaffResource();
                //have to create a staff resource AND a staff resource organization
                $staffResource = new agStaffResource();
                $staffResource->staff_id = $staff->id;
                $staffResource->staff_resource_type_id = $staffValues[$emkey]['ag_staff_resource_type_list'][0];
                $staffResource->save();
                $newStaffResourceOrganization = new agStaffResourceOrganization();
                $newStaffResourceOrganization->staff_resource_id = $staffResource->getId();
                $newStaffResourceOrganization->id = $staffResource->getId();
                $newStaffResourceOrganization->organization_id = $staffValues[$emkey]['ag_organization_list'][0];
                $newStaffResourceOrganization->save();
                //$newFacilityResource->save();
                //$this->getObject()->getAgStaffResource()->add($staffResource);
                unset($emforms[$emkey]);
              } else {
                unset($emforms[$emkey]);
              }
            } else {
              //if we are updating this form
              $objStaffResource = $emform->getObject();
              if ($staffValues[$emkey]['ag_organization_list'][0] && $staffValues[$emkey]['ag_staff_resource_type_list'][0]) {
                $emform->getObject()->save();
              } else {
                $emform->getObject()->delete(); //if we've blanked out all information on a form
              }
              unset($emforms[$emkey]);
              //$form->getObject()->setFacilityId($this->getObject()->getId());
            }
          }
        }
      }
    }
  return parent::saveEmbeddedForms($con, $forms);

  }
}