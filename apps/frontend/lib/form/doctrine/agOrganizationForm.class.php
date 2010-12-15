<?php

/**
 * An extension of an organization base form to process the edit and show forms of organization and its related records.
 *
 * PHP Version 5
 *
 * LICENSE: This source file is subject to LGPLv3.0 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 *
 * @author Shirley Chan, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
class agOrganizationForm extends BaseagOrganizationForm
{
  public function setup()
  {
    //parent::setup();
//    parent::configure();

    unset(
      $this['updated_at'],
      $this['created_at'],
//      $this['entity_id'],
      $this['ag_branch_list']
    );
  }

  public function configure()
  {
    /*
     * configure() extends the base method to remove unused fields
     */
    //parent::configure();

    unset(
      $this['updated_at'],
      $this['created_at'],
//      $this['entity_id'],
      $this['ag_branch_list']
    );

    // Get the staffs and their staff resource type associated to the organization.
    $staffResourceTypes = agStaffResourceType::staffResourceTypeInArray();
    $organizationStaffResources = agOrganization::organizationStaffByResource(array($this->getObject()->getId()));
    $personFullName = agPerson::getPersonFullName();

    // Creating an array of staff resource organzation and it's correlating staff name, staff id, and staff resource type for the associated staff list box.
    //   array(staff resource organization id => staffInfoString) where staffInfoStringn = staff primary full name (staff id) : staff resource type.
    //   ie array(1 => Joe Smith (32) : Operator
    $currentOrgStaffResourceOption = array();
    foreach ($organizationStaffResources as $orgStfRes)
    {
      if ( $orgStfRes['staff_resource_organization_id'] == NULL )
      {
        $currentOrgStaffResourceOption['none'] = 'None';
      } else {
        $optString = $personFullName[ $orgStfRes['person_id'] ] . ' (' . $orgStfRes['staff_id'] . ') : ' . ucwords($staffResourceTypes[ $orgStfRes['staff_resource_type_id'] ]);
        $currentOrgStaffResourceOption[$orgStfRes['staff_resource_organization_id']] = $optString;
      }
    }

    $this->setWidgets(array(
      'id'                                   => new sfWidgetFormInputHidden(),
      'entity_id'                            => new sfWidgetFormInputHidden(),
      'organization'                         => new sfWidgetFormInputText(),
      'description'                          => new sfWidgetFormInputText(),
      'ag_staff_resource_organization_list'  => new sfWidgetFormChoice(array('choices' => $currentOrgStaffResourceOption, 'multiple' => true), array('style' => 'height:300px;width:300px;')),
      'ag_staff_resource_non_org_list'       => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agStaffResource', 'expanded' => false), array('style' => 'height:300px;width:300px;')),
    ));

    $query = Doctrine_Query::create()
      ->select ('s.id AS staff_id, s.person_id, sr.id AS staff_resource_id, sr.staff_resource_type_id, count(*) as count')
      ->from('agStaffResource AS sr, sr.agStaff AS s')
      ->where('sr.staff_id NOT IN (SELECT ssr.staff_id FROM agStaffResourceOrganization AS ssro INNER JOIN ssro.agStaffResource AS ssr WHERE ssro.organization_id=? GROUP BY ssr.staff_id)', $this->getObject()->get('id'))
      ->groupBy('sr.staff_id')
      ->having('count(*) = 1');

    $sql = $query->getSqlQuery();
//    echo "<BR>" . $sql . "<BR>";
//    $resultSet = $query->execute();
//    print_r($resultSet->toArray());
//    $resultSet = $query->execute(array(), Doctrine::HYDRATE_ARRAY);
//    print_r($resultSet);

    $this->widgetSchema['ag_staff_resource_non_org_list']->addOption(
        'query',
        Doctrine_Query::create()
//          ->select('o.id, sro.id, sr.id')
//          ->from('agOrganization o, o.agStaffResourceOrganization sro, sro.agStaffResource sr')
      ->select ('s.id AS staff_id, s.person_id, sr.id AS staff_resource_id, sr.staff_resource_type_id, count(*) as count')
      ->from('agStaffResource AS sr, sr.agStaff AS s')
      ->where('sr.staff_id NOT IN (SELECT ssr.staff_id FROM agStaffResourceOrganization AS ssro INNER JOIN ssro.agStaffResource AS ssr WHERE ssro.organization_id=? GROUP BY ssr.staff_id)', $this->getObject()->get('id'))
      ->groupBy('sr.staff_id')
      ->having('count(*) = 1')
    );

    $this->widgetSchema->setLabel('ag_staff_resource_organization_list','Associated Staff Resource List');

    $this->setValidators(array(
      'id'           => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'entity_id'      => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agEntity'))),
      'organization' => new sfValidatorString(array('required' => true)),//, array('min_length' => 1), array('max_length' => 128)),
      'description'  => new sfValidatorString(array('required' => false)),//, array('min_length' => 1), array('max_length' => 255)),
      'ag_staff_resource_organization_list'  => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agStaffResourceOrganization', 'required' => false)),
      'ag_staff_resource_non_org_list'  => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agStaff', 'required' => false)),
    ));
    $this->validatorSchema->setOption('allow_extra_fields', true);

    $custDeco = new agWidgetFormSchemaFormatterInline($this->getWidgetSchema());
    $this->getWidgetSchema()->addFormFormatter('custDeco', $custDeco);
    $this->getWidgetSchema()->setFormFormatterName('custDeco');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

  }
  protected function doSave($con = null)
  {
    $existing = $this->getObject()->getAgScenarioFacilityResource();
    foreach($existing as $rec){$current[] = $rec;}
    //$existing = $this->object->agFacilityResource->getPrimaryKeys();
    $values = $this->getTaintedValues();
    //all we need to save, is the allocated list: it's order included(this is proving to be clumsy while working with a listbox, jquery is prefered)
    if($values) $values = $values['ag_facility_resource_order'];
    unset($this['ag_facility_resource_order']); //we unset the two form items that are being forced in, the validator won't like it
    unset($this['ag_facility_resource_list']);
    parent::doSave($con);
    if($values)
    {
      /** this should be $current->getAgScenarioFacilityResource(), will it return an array? will it be cached? */
      if($current)  $toDelete = array_diff($current,$values);

      if(count($toDelete) >0)
      {
        /** @todo clean this up, a subquery to delete on would be optimal */
        $deleteRecs = Doctrine_Query::create()
        ->select('a.facility_resource_id')
        ->from('agScenarioFacilityResource a')
        ->whereIn('a.facility_resource_id', $toDelete)->execute();
        foreach($deleteRecs as $deletor)
        {
          $deletor->delete();
        }
      }
      foreach($values as $key => $value)
      {
        if( in_array($value,$current)) $agScenarioFacilityResource = $currentCheck;
        else{
          //if there isn't an entry in agScenarioFacilityResource for this group/facility_resource...
          $agScenarioFacilityResource = new agScenarioFacilityResource();
          $agScenarioFacilityResource->scenario_facility_group_id = $this->getObject()->getId();
          $agScenarioFacilityResource->activation_sequence = $key +1;
          $agScenarioFacilityResource->facility_resource_id = $value;
          $agScenarioFacilityResource->facility_resource_allocation_status_id = 4;
          }
        $agScenarioFacilityResource->save();
      }
    }
    else
    {
        /**
         * @todo there are no values, so we need to delete
         *   if there aren't values in $values that exist in $checkArray, or if
         *  those items have changed.. (activation order, is the only thing)
         *  we need to update said item
         */
    }
  }
}
