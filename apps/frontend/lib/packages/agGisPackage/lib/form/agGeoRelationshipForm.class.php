<?php

/**
 * agGeoRelationship form.
 *
 * @package    AGASTI_CORE
 * @subpackage form
 * @author     CUNY SPS
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class agGeoRelationshipForm extends BaseagGeoRelationshipForm
{
  public function configure()
  {
  }

    public function doSave($con = null)
  {
    //before we SAVE data, we have to put some data into geo_relationship_km_value
    //so...

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
