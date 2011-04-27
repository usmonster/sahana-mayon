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
    $staffContainerForm->getWidgetSchema()->setLabel('status', false);
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
    $staffContainerContainer = new sfForm();

//      $staffConDeco = new agWidgetFormSchemaFormatterSubContainer($staffContainerContainer->getWidgetSchema());
//      $staffContainerContainer->getWidgetSchema()->addFormFormatter('staffConDeco', $staffConDeco);
//      $staffContainerContainer->getWidgetSchema()->setFormFormatterName('staffConDeco');


    $restrictedOptions = array();
    if (isset($staffResourceObjects)) {
      $i = 0;
      foreach ($staffResourceObjects as $staffResourceObject) {

        $resourceTypeQuery = agDoctrineQuery::create()
              ->select('a.id, a.staff_resource_type')
              ->from('agStaffResourceType a');
        if(count($restrictedOptions) > 0)$resourceTypeQuery->whereNotIn('a.id', $restrictedOptions);
        $resourceTypeOptions = $resourceTypeQuery->execute(array(), 'key_value_pair');

        $staffResourceForm = new PluginagEmbeddedAgStaffResourceForm($staffResourceObject);
        $staffResourceForm->setWidget('staff_resource_type_id', new sfWidgetFormChoice(array('choices' => $resourceTypeOptions )));


//unset($staffResourceForm['created_at'], $staffResourceForm['updated_at']);
        if (isset($this->staff_id)) {
          $staffResourceForm->setDefault('staff_id', $this->staff_id);
        }
          $custDeco = new agWidgetFormSchemaFormatterInlineLeftLabel($staffResourceForm->getWidgetSchema());
          $staffResourceForm->getWidgetSchema()->addFormFormatter('custDeco', $custDeco);
          $staffResourceForm->getWidgetSchema()->setFormFormatterName('custDeco');

        $staffContainerContainer->embedForm($i, $staffResourceForm);
        $staffContainerContainer->getWidgetSchema()->setLabel($i, false);
        $i++;
        $restrictedOptions[] = $staffResourceObject->getStaffResourceTypeId();
      }
      $staffContainerForm->embedForm('type', $staffContainerContainer);
      $staffContainerForm->getWidgetSchema()->setLabel('type', false);
    } else {
      $staffResourceForm = new PluginagEmbeddedAgStaffResourceForm();
      $custDeco = new agWidgetFormSchemaFormatterInlineLeftLabel($staffResourceForm->getWidgetSchema());
      $staffResourceForm->getWidgetSchema()->addFormFormatter('custDeco', $custDeco);
      $staffResourceForm->getWidgetSchema()->setFormFormatterName('custDeco');
      //unset($staffResourceForm['created_at'], $staffResourceForm['updated_at']);
      if (isset($this->staff_id)) {
        $staffResourceForm->setDefault('staff_id', $this->staff_id);
      }
      $staffContainerContainer->embedForm('0', $staffResourceForm);
      //$staffResourceForm->getWidgetSchema()->setLabel('', $value) ContainerForm->getWidgetSchema()->setLabel(
      $staffContainerContainer->getWidgetSchema()->setLabel('0', false);


      $staffContainerForm->embedForm('type', $staffContainerContainer);

      $staffContainerForm->getWidgetSchema()->setLabel('type', false);
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
      //sort by active to the top

      $typeForms = $this->embeddedForms['staff']->embeddedForms['type'];
      foreach($typeForms->getEmbeddedForms() as  $formKey => $formForm){
      //$values = $this->values['staff']['type'];
        $values = $formForm->getObject()->getData();
        //bind ?
      //we can inject $values['staffresource'] from the above.
        $this->saveStaffResourceTypeForm($formForm, $values);
      unset($typeForms[$formKey]);
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
      //if($form->getObject()->created_at !=null ..
      $form->getObject()->created_at = $mysqldate = date('Y-m-d H:i:s', time());
      $form->getObject()->updated_at = $mysqldate = date('Y-m-d H:i:s', time());
    }
    $form->getObject()->save();
    //$this->staff_resource_id = $form->getObject()->id;
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