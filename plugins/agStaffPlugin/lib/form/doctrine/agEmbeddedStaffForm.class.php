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
class agEmbeddedStaffForm extends BaseagStaffForm
{
  public function setup()
  {
    parent::setup();
    $current = $this->getObject()->getAgStaffResource();
    //get all 'staff reources' saved for this person.
    if (!$current){
      $currentoptions = array();
      foreach($current as $curopt)
      {
        $currentoptions[$curopt->getAgStaffResourceOrganization()->getAgOrganizationId()] = $curopt->getAgStaffResourceOrganization()->getAgOrganization();//could be optimized, set the current record correctly
      }
    }
    else{
      $current = Doctrine::getTable('agOrganization')->findAll();
                 // ->execute();
      foreach($current as $curopt)
      {
        $currentoptions[$curopt->getId()] = $curopt->getOrganization();
      }
     }

    $this->setWidgets(array(
      'id'                          => new sfWidgetFormInputHidden(),
      'person_id'                   => new sfWidgetFormInputHidden(),//new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agPerson'), 'add_empty' => false)),
      'staff_status_id'             => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agStaffStatus'), 'add_empty' => false,'method' => 'getStaffStatus')),
      //'created_at'                  => new sfWidgetFormDateTime(),
      //'updated_at'                  => new sfWidgetFormDateTime(),
      'ag_staff_resource_type_list' => new sfWidgetFormDoctrineChoice(array('multiple' => false, 'model' => 'agStaffResourceType')),
      'ag_organization_list'        => new sfWidgetFormChoice(array('choices' => $currentoptions,'multiple' => false))//,array('style' => 'height:300px;width:300px;'))
    ));

    $this->setValidators(array(
      'id'                           => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'person_id'                    => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agPerson'),'required' => false)),
      'staff_status_id'              => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agStaffStatus'),'required' => false)),
      //'created_at'                  => new sfValidatorDateTime(),
      //'updated_at'                  => new sfValidatorDateTime(),
      'ag_organization_list'         => new sfValidatorChoice(array('choices' => array_keys($currentoptions),'required' => false)),
      'ag_staff_resource_type_list'  => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agStaffResourceType', 'required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'agStaff', 'column' => array('person_id')))
    );
    $this->validatorSchema->setOption('allow_extra_fields', true);
    $this->widgetSchema->setLabel('ag_staff_resource_type_list', 'staff type');
    $this->widgetSchema->setLabel('ag_organization_list', 'organization');
    $this->widgetSchema->setLabel('staff_status_id', 'status');
    $this->widgetSchema->setNameFormat('staff[%s]');
    //$this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

  }



}