<?php

/**
 * agGlobalParam form base class.
 *
 * @method agGlobalParam getObject() Returns the current form's model object
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
class agGlobalParamForm extends sfForm
{
  public function setup()
  {
    /**
     *  if the facility already has resources assigned, get all the data for them
     *   */

    $globalparamContainer = new sfForm(array(), array());
    if (//$this->agGlobalParams = Doctrine::getTable('agGlobalParam')->findAll()){

            $this->ag_global_params = Doctrine_Query::create()
            ->select('agSFR.*')
            ->from('agGlobalParam agSFR')
            ->execute()) {

      /**
       *  for every existing facility resource, create an agEmbeddedFacilityResourceForm and embed it into $facilityResourceContainer
       *   */
      foreach ($this->ag_global_params as $globalParam) {
        $globalParamForm = new embeddedGlobalParamForm($globalParam);

        $globalParamForm->setDefault('host_id', 1);
        $globalParamId = $globalParam->getId();
        $globalparamContainer->embedForm($globalParamId, $globalParamForm);
        $globalparamContainer->widgetSchema->setLabel($globalParamId, false);
      }
      $globalParamForm = new embeddedGlobalParamForm;
      $globalparamContainer->embedForm('global', $globalParamForm);
    }
    $this->embedForm('ag_global_param', $globalparamContainer);
  }
  public function configure(){
    parent::configure();
        unset($this['created_at'],
          $this['updated_at'],
          $this['host_id']
          );
  }
  public function getModelName()
  {
    return 'agGlobalParam';
  }
  public function saveEmbeddedForms($con = null, $forms = null)
  {
    /**
     *
     **/
    if (null === $forms) {
      $forms = $this->embeddedForms;
    }
    if (is_array($forms)) {
      foreach ($forms as $key => $form) {
        if ($form instanceof embeddedGlobalParamForm) {
          if ($form->isNew()) {
            $newFacilityResource = $form->getObject();
            if ($newFacilityResource->capacity && $newFacilityResource->facility_resource_type_id
                && $newFacilityResource->facility_resource_status_id) {
              $newFacilityResource->setFacilityId($this->getObject()->getId());
              $newFacilityResource->save();
              $this->getObject()->getAgFacilityResource()->add($newFacilityResource);
              unset($forms[$key]);
            } else {
              unset($forms[$key]);
            }
          } else {
            $objGlobalParam = $form->getObject();
            if ($objGlobalParam->value && $objGlobalParam->datapoint) {
              $form->getObject()->setHostId(1);
              $form->getObject()->save();
            } else {
              $form->getObject()->delete();
            }
            unset($forms[$key]);
            //$form->getObject()->setFacilityId($this->getObject()->getId());
          }
        }
      }
    }

    return parent::saveEmbeddedForms($con, $forms);
  }

}