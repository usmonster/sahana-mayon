<?php

/**
 *
 * extends the base Facility class for added functionality
 *
 * PHP Version 5
 *
 * LICENSE: This source file is subject to LGPLv3.0 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 *
 * @author Ilya Gulko, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
class agFacility extends BaseagFacility
{
  /**
   * Builds an index for facility.
   *
   * @return Zend_Search_Lucene_Document $doc
   */
  public function updateLucene()
  {
    $doc = new Zend_Search_Lucene_Document();
    $doc->addField(Zend_Search_Lucene_Field::Keyword('Id', $this->id, 'utf-8'));
    $doc->addField(Zend_Search_Lucene_Field::unStored('facility', $this->facility_name, 'utf-8'));
    $doc->addField(Zend_Search_Lucene_Field::unStored('facility_code', $this->facility_code, 'utf-8'));

    $facilityInfo = Doctrine_Query::create()
      ->select('f.id, fr.id, frt.id, frt.facility_resource_type, frt.facility_resource_type_abbr')
      ->from('agFacility f')
      ->innerJoin('f.agFacilityResource fr')
      ->innerJoin('fr.agFacilityResourceType frt')
      ->where('f.id=?', $this->id)
      ->execute(array(), Doctrine_Core::HYDRATE_SCALAR);

    $resourceType = null;
    // Cannot save facility's resource type info until after the facility is saved.
    foreach ($facilityInfo as $fac)
    {
      $resourceType = $resourceType . ' ' . $fac['frt_facility_resource_type'] . ' ' . $fac['frt_facility_resource_type_abbr'];
    }
    if (isset($resourceType))
    {
      $doc->addField(Zend_Search_Lucene_Field::unStored('facility_resource' , $resourceType, 'utf-8'));
    }

    return $doc;
  }

  /**
   * delete()
   *
   * extends the base class's delete() function to delete related
   * agFacilityResource records before deleting the agFacility
   * record
   *
   * @param $conn Doctrine_Connection object that gets passed
   *              through into the base class's function
   *
   * @return passthrough from base class
   *
   */
  public function delete(Doctrine_Connection $conn = null)
  {
    foreach ($this->getAgFacilityResource() as $agFR) {
      $agFR->delete();
    }

    return parent::delete($conn);
  }

  /**
   * getBorough()
   *
   * Get this agFacility record's Borough, based on its first work
   * address
   */
  public function getBorough() {
    $addresses = $this->getAgSite()->getAgEntity()->getAgEntityAddressContact();

    foreach ($addresses as $address) {
      if ($address->getAgAddressContactType() == 'work') {
        foreach ($address->getAgAddress()->getAgAddressValue() as $addressValue) {
          /**
           * @todo check for correct agAddressValue type and then return
           *       its value
           */
        }
      }
    }

  }

}

