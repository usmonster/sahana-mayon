<?php

/**
 * agTinyEventFacilityResourceStatusForm
 *
 * @method agEventFacilityResourceStatus getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     nils Stolpe, CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
class agTinyEventFacilityResourceStatusForm extends PluginagEventFacilityResourceStatusForm
{
  public function configure()
  {
    $this->setWidgets(array(
      'id'                                     => new sfWidgetFormInputHidden(),
      'event_facility_resource_id'             => new sfWidgetFormInputHidden(),
      'time_stamp'                             => new sfWidgetFormInputHidden(),
      'facility_resource_allocation_status_id' => new sfWidgetFormDoctrineChoice(array(
                                                                                   'model'     => $this->getRelatedModelName('agFacilityResourceAllocationStatus'),
                                                                                   'add_empty' => false,
                                                                                   'method'    => 'getFacilityResourceAllocationStatus'
                                                                                )),
    ));

    $this->setValidators(array(
      'id'                                     => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'event_facility_resource_id'             => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agEventFacilityResource'))),
      'time_stamp'                             => new sfValidatorDateTime(),
      'facility_resource_allocation_status_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agFacilityResourceAllocationStatus'), 'required' => false)),
    ));

    $this->getWidgetSchema()->setLabel('facility_resource_allocation_status_id', false);

    $custDeco = new agWidgetFormSchemaFormatterTiny($this->getWidgetSchema());
    $this->getWidgetSchema()->addFormFormatter('custDeco', $custDeco);
    $this->getWidgetSchema()->setFormFormatterName('custDeco');
  }

}