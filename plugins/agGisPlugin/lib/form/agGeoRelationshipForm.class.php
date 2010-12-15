<?php

/**
 * agGeoRelationship form, this is to be used on occasion to determine distances between staff and shelters
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class agGeoRelationshipForm extends BaseagGeoRelationshipForm
{
   public function setup()
  {
    unset($this['created_at'],
          $this['updated_at']
          );
     $this->setWidgets(array(
      'id'                        => new sfWidgetFormInputHidden(),
      'geo_id1'                   => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('geo1'), 'add_empty' => false)),
      'geo_id2'                   => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('geo2'), 'add_empty' => false)),
      'geo_relationship_type_id'  => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('agGeoRelationshipType'), 'add_empty' => false)),
      'geo_relationship_km_value' => new sfWidgetFormInputText(),
      //'created_at'                => new sfWidgetFormDateTime(),
      //'updated_at'                => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'                        => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'geo_id1'                   => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('geo1'))),
      'geo_id2'                   => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('geo2'))),
      'geo_relationship_type_id'  => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agGeoRelationshipType'))),
      'geo_relationship_km_value' => new sfValidatorNumber(),
      //'created_at'                => new sfValidatorDateTime(),
      //'updated_at'                => new sfValidatorDateTime(),
    ));

//
//      'ag_facility_resource_list'           => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'agFacilityResource', 'expanded' => false), array('style' => 'height:300px;width:300px;')),
//      'ag_facility_resource_order'          => new sfWidgetFormChoice(array('choices' => $currentoptions,'multiple' => true),array('style' => 'height:300px;width:300px;'))
//      ));
//        $this->widgetSchema['ag_facility_resource_list']->addOption(
//      'query',
//      Doctrine_Query::create()
//        ->select('a.facility_id, af.*, afrt.*')
//        ->from('agFacilityResource a, a.agFacility af, a.agFacilityResourceType afrt')
//        ->whereNotIn('a.id', array_keys($currentoptions))
//    );
//
//    $this->setValidators(array(
//      'id'                                  => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
//      'scenario_id'                         => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agScenario'))),
//      'scenario_facility_group'             => new sfValidatorString(array('max_length' => 64)),
//      'facility_group_type_id'              => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agFacilityGroupType'))),
//      'facility_group_allocation_status_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('agFacilityGroupAllocationStatus'))),
//      'activation_sequence'                 => new sfValidatorInteger(),
//      'ag_facility_resource_list'           => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'agFacilityResource', 'required' => false)),
//      //'ag_facility_resource_order'          => new sfValidatorChoice(array('required' => false))




    $this->widgetSchema->setNameFormat('ag_geo_relationship[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);
    //going to have to do some funny stuff here.
  }
  public function configure()
  {
    unset($this['created_at'],
          $this['updated_at']
          );
  }

    public function doSave($con = null)
  {
    //before we SAVE data, we have to put some data into geo_relationship_km_value
    //so...
    $forms;
    $geoOp = new agGis();
    //get latitude and longitude from each
    //dql to get a
    $geo1 =  Doctrine_Core::getTable('agGeo')
                            ->select('agG.*, agGC.*, agGF.*')
                            ->from('agGeo agG, agG.agGeoCoordinate agGC, agG.agGeoFeature agGF ')
                            ->where('agG.geo_id = aGF.geo_id AND agG.geo_id = ?', $this->getObject()->getGeoId1())
                            ->execute();
    $geo2 =  Doctrine_Core::getTable('agGeo')
                            ->select('agG.*, agGC.*, agGF.*')
                            ->from('agGeo agG, agG.agGeoCoordinate agGC, agG.agGeoFeature agGF ')
                            ->where('agG.geo_id = aGF.geo_id AND agG.geo_id = ?', $this->getObject()->getGeoId2())
                            ->execute();

    $geo1 = $geo1->getAgGeoFeature()->getAgGeoCoordinate();
    $geo2 = $geo1->getAgGeoFeature()->getAgGeoCoordinate();
//    $boo = new agGeoFeature();
//    $shoo = $boo->getAgGeoCoordinate();
//
//    $geo1 = $this->getObject()->getAgGeo;
//    $geo2 = $this->getObject()->geo_id2;
    $this->getObject()->geo_relationship_type_id = 1;
    $this->getObject()->geo_relationship_km_value = $geoOp->getDistance($geo1[0], $geo2[0], $geo1[1], $geo2[1]);
    $this->getObject()->save();
  }

}
