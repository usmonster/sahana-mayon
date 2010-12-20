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

   /** @method setup()
    * Unset widgets that are auto-filled, unnecessary, or whose relations are not properly defined without using embedded forms.
    **/
   public function setup()
  {
    unset(
      $this['updated_at'],
      $this['created_at'],
      $this['ag_branch_list']
    );
  }

  /** @method configure()
   *  In the method it is creating and setting both widgets and validtion for the form.
   **/
  public function configure()
  {
    /*
     * configure() extends the base method to remove unused fields
     */

    unset(
      $this['id'],
//      $this['entity_id'],
      $this['updated_at'],
      $this['created_at'],
      $this['ag_branch_list']
    );

    // Get the staffs and their staff resource type associated to the organization.
    $staffResourceTypes = agStaffResourceType::staffResourceTypeInArray();
    $organizationStaffResources = agOrganization::organizationStaffByResource(array($this->getObject()->getId()));
    $personFullName = agPerson::getPersonFullName();

    // Creating an array of staff resource organzation and it's correlating staff name, staff id, and staff resource type for the associated staff resource list box.
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
        $currentOrgStaffResourceOption[ $orgStfRes['staff_resource_id'] ] = $optString;
      }
    }

    // Query for available staff who only has one resource type assigned.
    $availOrgStaffResource = Doctrine_Query::create()
      ->select ('s.id AS staff_id, s.person_id, sr.id AS staff_resource_id, sr.staff_resource_type_id, count(*) as count')
      ->from('agStaffResource AS sr, sr.agStaff AS s')
      ->where('sr.staff_id NOT IN (SELECT ssr.staff_id FROM agStaffResourceOrganization AS ssro INNER JOIN ssro.agStaffResource AS ssr WHERE ssro.organization_id=? GROUP BY ssr.staff_id)', $this->getObject()->get('id'))
      ->groupBy('sr.staff_id')
      ->having('count(*) = 1')
      ->execute();

    // Creating an array of staff resource organzation and it's correlating staff name, staff id, and staff resource type for the available staff resource list box.
    //   array(staff resource organization id => staffInfoString) where staffInfoStringn = staff primary full name (staff id) : staff resource type.
    //   ie array(1 => Joe Smith (32) : Operator
    $availOrgStaffResourceOption = array();
    foreach ($availOrgStaffResource as $availStfRes)
    {
      $optString = $personFullName[ $availStfRes['agStaff']['person_id'] ] . ' (' . $availStfRes['staff_id'] . ') : ' . ucwords($staffResourceTypes[ $availStfRes['staff_resource_type_id'] ]);
      $availOrgStaffResourceOption[ $availStfRes['staff_resource_id'] ] = $optString;
    }
    if ( count($availOrgStaffResourceOption) == 0 )
    {
      $availOrgStaffResourceOption['none'] = 'None';
    }

  $this->setWidgets(array(
      'id'                                   => new sfWidgetFormInputHidden(),
      'entity_id'                            => new sfWidgetFormInputHidden(),
      'organization'                         => new sfWidgetFormInputText(),
      'description'                          => new sfWidgetFormInputText(),
      'ag_staff_resource_organization_list'  => new sfWidgetFormChoice(array('choices' => $currentOrgStaffResourceOption, 'multiple' => true), array('style' => 'height:300px;width:300px;')),
      'ag_staff_resource_non_org_list'       => new sfWidgetFormChoice(array('choices' => $availOrgStaffResourceOption, 'multiple' => true), array('style' => 'height:300px;width:300px;')),
    ));

    $this->widgetSchema->setLabel('ag_staff_resource_organization_list','Associated Staff Resource List');
    $this->widgetSchema->setLabel('ag_staff_resource_non_org_list','Available (non-associated) Staff Resource List');

    $this->setValidators(array(
      'id'           => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'organization' => new sfValidatorString(array('required' => true, 'max_length' => 128)), 
      'description'  => new sfValidatorString(array('required' => false, 'min_length' => 1, 'max_length' => 255)),
      'ag_staff_resource_organization_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agStaffResourceOrganization', 'required' => false)),
      'ag_staff_resource_non_org_list'      => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agStaff', 'required' => false)),
    ));

    $this->validatorSchema->setOption('allow_extra_fields', true);
    $this->validatorSchema->setPostValidator(
      new sfValidatorAnd(array(
        new sfValidatorDoctrineUnique(array('model' => 'agStaffResourceOrganization', 'column' => array('id'))),
        new sfValidatorDoctrineUnique(array('model' => 'agStaffResourceOrganization', 'column' => array('staff_resource_id', 'organization_id'))),
      ))
    );
    $this->widgetSchema->setNameFormat('ag_staff_resource_organization[%s]');

    $custDeco = new agWidgetFormSchemaFormatterInline($this->getWidgetSchema());
    $this->getWidgetSchema()->addFormFormatter('custDeco', $custDeco);
    $this->getWidgetSchema()->setFormFormatterName('custDeco');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

  }

}
