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
class PluginagStaffPersonForm extends agPersonForm
{
  public $staff_id;
  /**
  * Sets up the form.
  **/
  public function setup()
  {
    parent::setup();
  }

  /**
  * Configures the form and starts off the embedding chain.
  **/
  public function configure()
  {
    parent::configure();
    $this->embedAgStaffPersonForms();
  }

  /**
  * Calls all of the embed...Form() methods.
  **/
  public function embedAgStaffPersonForms()
  {
    $staffContainerForm = new sfForm();
    $this->embedStaffForm($staffContainerForm);
    $this->embedStaffResourceForm($staffContainerForm);
    $this->embedStaffResourceOrganizationForm($staffContainerForm);
    $this->embedForm('staff', $staffContainerForm);
  }

  /**
  * Embeds an instance of PluginEmbedddedAgStaffForm()
  **/
  public function embedStaffForm($staffContainerForm)
  {
    if ($id = $this->getObject()->id) {
      $staffObject = Doctrine_Query::create()
              ->from('agStaff a')
              ->where('a.person_id =?', $id)
              ->execute()->getFirst();
    }
    $staffForm = new PluginagEmbeddedAgStaffForm(isset($staffObject) ? $staffObject : null);
    $staffContainerForm->embedForm('status', $staffForm);
  }

  /**
  * Embeds an instance of PluginEmbedddedAgStaffResourceForm()
  **/
  public function embedStaffResourceForm($staffContainerForm)
  {
    if ($staff = $this->getObject()->getAgStaff()->getFirst()) {
      $staffResourceObject = Doctrine_Query::create()
              ->from('agStaffResource a')
              ->where('a.staff_id = ?', $staff->id)
              ->execute()->getFirst();
    }
    $staffResourceForm = new PluginagEmbeddedAgStaffResourceForm(isset($staffResourceObject) ? $staffResourceObject : null);
    unset($staffResourceForm['created_at'], $staffResourceForm['updated_at']);
    if(isset($this->staff_id)){
        $staffResourceForm->setDefault('staff_id', $this->staff_id);
    }
    $staffContainerForm->embedForm('type', $staffResourceForm);
  }

  /**
  * Embeds an instance of PluginEmbedddedAgStaffResourceOrganizationForm()
  **/
  public function embedStaffResourceOrganizationForm($staffContainerForm)
  {
    if (!$this->isNew()) {
      $staffResOrgObject = Doctrine_Query::create()
              ->from('agStaffResourceOrganization a')
              ->where('a.staff_resource_id = ?', $this->getObject()->getAgStaff()->getFirst()->getAgStaffResource()->getFirst()->id)
              ->execute()->getFirst();
    }

    $staffResOrgForm = new PluginagEmbeddedAgStaffResourceOrganizationForm(isset($staffResOrgObject) ? $staffResOrgObject : null);
    $staffContainerForm->embedForm('organization', $staffResOrgForm);
  }

  /**
  * Calls the various save...Form() methods.
  **/
  public function saveEmbeddedForms($con = null, $forms = null)
  {
    if (isset($this->embeddedForms['staff'])) {
      $form = $this->embeddedForms['staff']->embeddedForms['status'];
      $values = $this->values['staff']['status'];
      $this->saveStaffForm($form, $values);
      unset($this->embeddedForms['staff']->embeddedForms['status']);
    }
    if (isset($this->embeddedForms['staff'])) {
      $form = $this->embeddedForms['staff']->embeddedForms['type'];
      $values = $this->values['staff']['type'];
      //we can inject $values['staffresource'] from the above.
      $this->saveStaffResourceTypeForm($form, $values);
      unset($this->embeddedForms['staff']->embeddedForms['type']);
    }
    if (isset($this->embeddedForms['staff'])) {
      $form = $this->embeddedForms['staff']->embeddedForms['organization'];
      $values = $this->values['staff']['organization'];
      $this->saveStaffResourceOrganizationForm($form, $values);
      unset($this->embeddedForms['staff']->embeddedForms['organization']);
    }
    return parent::saveEmbeddedForms($con, $forms);
  }

  /**
  * Saves the PluginEmbedddedAgStaffForm() that is embeddedin embedStaffForm()
  *
  * @param PluginEmbedddedAgStaffForm()                          $form
  * @param agStaff values submitted by PluginagStaffPersonForm   $values
  **/
  public function saveStaffForm($form, $values)
  {
    $form->updateObject($values);
    if ($form->getObject()->person_id == null) {
      $form->getObject()->person_id = $this->getObject()->id;
    }
    $form->getObject()->save();
    $this->staff_id = $form->getObject()->id;
  }

  /**
  * Saves the PluginEmbedddedAgStaffResourceForm() that is embeddedin embedStaffResourceForm()
  *
  * @param PluginEmbedddedAgStaffResourceForm()                          $form
  * @param agStaffResource values submitted by PluginagStaffResourceForm   $values
  **/
  public function saveStaffResourceTypeForm($form, $values)
  {
    $form->updateObject($values);
    if ($form->getObject()->staff_id == null) {
      $form->getObject()->staff_id = $this->staff_id;
    }
    $form->getObject()->save();
    $this->staff_resource_id = $form->getObject()->id;
  }

  /**
  * Saves the PluginEmbedddedAgStaffResourceOrganizationForm() that is embedded
  * in embedStaffResourceOrganizationForm()
  *
  * @param PluginEmbedddedAgStaffResourceOrganizationForm()                                              $form
  * @param agStaffResourceOrganization values submitted by PluginagStaffPersonResourceOrganizationForm   $values
  **/
  public function saveStaffResourceOrganizationForm($form, $values)
  {
    $form->updateObject($values);
    if ($form->getObject()->staff_resource_id == null) {
      $form->getObject()->staff_resource_id = $this->staff_resource_id;
      //since ag_staff_resource_organization.id does not auto increment, and we don't want collisions,
      //we should set the id = to the staff_resource_id..
      $form->getObject()->id = $this->staff_resource_id;
    }
    $form->getObject()->save();
  }
}