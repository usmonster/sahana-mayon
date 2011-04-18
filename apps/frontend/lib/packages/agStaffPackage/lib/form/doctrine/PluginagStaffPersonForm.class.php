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
class PluginagStaffPersonForm extends agPersonForm
{

  public $staff_id;

  /**
   * Sets up the form.
   * */
  public function setup()
  {
    parent::setup();
  }

  /**
   * Configures the form and starts off the embedding chain.
   * */
  public function configure()
  {
    parent::configure();
    $this->embedAgStaffPersonForms();
  }

  /**
   * Calls all of the embed...Form() methods.
   * */
  public function embedAgStaffPersonForms()
  {
    $staffContainerForm = new sfForm();
    $this->embedStaffForm($staffContainerForm);
    $this->embedStaffResourceForm($staffContainerForm);
    //$this->embedStaffResourceOrganizationForm($staffContainerForm);
    $this->embedForm('staff', $staffContainerForm);
  }

  /**
   * Embeds an instance of PluginEmbedddedAgStaffForm()
   * */
  public function embedStaffForm($staffContainerForm)
  {
    if ($id = $this->getObject()->id) {
      $staffObject = agDoctrineQuery::create()
              ->from('agStaff a')
              ->where('a.person_id =?', $id)
              ->execute()->getFirst();
    }
    $staffForm = new PluginagEmbeddedAgStaffForm(isset($staffObject) ? $staffObject : null);
    $staffContainerForm->embedForm('status', $staffForm);
  }

  /**
   * Embeds an instance of PluginEmbedddedAgStaffResourceForm()
   * */
  public function embedStaffResourceForm($staffContainerForm)
  {
    if ($staff = $this->getObject()->getAgStaff()->getFirst()) {
      $staffResourceObjects = agDoctrineQuery::create()
              ->from('agStaffResource a')
              ->where('a.staff_id = ?', $staff->id)
              ->execute(); //->getFirst();
    }
    if ($staffResourceObjects) {
      foreach ($staffResourceObjects as $staffResourceObject) {
        $staffResourceForm = new PluginagEmbeddedAgStaffResourceForm($staffResourceObject);
        //unset($staffResourceForm['created_at'], $staffResourceForm['updated_at']);
        if (isset($this->staff_id)) {
          $staffResourceForm->setDefault('staff_id', $this->staff_id);
        }
        $staffContainerForm->embedForm('type', $staffResourceForm);
      }
    } else {
      $staffResourceForm = new PluginagEmbeddedAgStaffResourceForm();
      //unset($staffResourceForm['created_at'], $staffResourceForm['updated_at']);
      if (isset($this->staff_id)) {
        $staffResourceForm->setDefault('staff_id', $this->staff_id);
      }
      $staffContainerForm->embedForm('type', $staffResourceForm);
//handle for creation of more than just the one form.. or have it come in through jquery
    }
  }

  /**
   * Embeds an instance of PluginEmbedddedAgStaffResourceOrganizationForm()
   * */
  public function embedStaffResourceOrganizationForm($staffContainerForm)
  {
    if (!$this->isNew()) {
      $staffResOrgObject = agDoctrineQuery::create()
              ->from('agStaffResourceOrganization a')
              ->where('a.staff_resource_id = ?', $this->getObject()->getAgStaff()->getFirst()->getAgStaffResource()->getFirst()->id)
              ->execute()->getFirst();
    }

    $staffResOrgForm = new PluginagEmbeddedAgStaffResourceOrganizationForm(isset($staffResOrgObject) ? $staffResOrgObject : null);
    $staffContainerForm->embedForm('organization', $staffResOrgForm);
  }

  /**
   * Calls the various save...Form() methods.
   * */
  public function saveEmbeddedForms($con = null, $forms = null)
  {
    if (isset($this->embeddedForms['staff'])) {
      $form = $this->embeddedForms['staff']->embeddedForms['status'];
      $values = $this->values['staff']['status'];
      $this->saveStaffForm($form, $values);
      unset($this->embeddedForms['staff']->embeddedForms['status']);
    }
    if (isset($this->embeddedForms['staff'])) {
      foreach($this->embeddedForms['staff']->embeddedForms['type'] as  $form){

      $values = $this->values['staff']['type'];
      //we can inject $values['staffresource'] from the above.
      $this->saveStaffResourceTypeForm($form, $values);
      unset($this->embeddedForms['staff']->embeddedForms['type']);
      }
    }
    return parent::saveEmbeddedForms($con, $forms);
  }

  /**
   * Saves the PluginEmbedddedAgStaffForm() that is embeddedin embedStaffForm()
   *
   * @param PluginEmbedddedAgStaffForm()                          $form
   * @param agStaff values submitted by PluginagStaffPersonForm   $values
   * */
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
   * */
  public function saveStaffResourceTypeForm($form, $values)
  {


    //this needs to handle multiple forms 
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
   * */
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