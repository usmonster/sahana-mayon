<?php

/**
 *
 * extends the base Facility class for added functionality
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author Ilya Gulko, CUNY SPS
 * @author Antonio Estrada, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
class agFacility extends BaseagFacility
{

  protected $isAutoIndexed;

  public function __construct($table = null, $isNewEntry = false, $isAutoIndexed = true)
  {
    parent::__construct($table, $isNewEntry);
    $this->isAutoIndexed = $isAutoIndexed;
  }

  /**
   * Builds an index for facility.
   *
   * The Lucene Facility Index allows for a facility to be searched by:
   * id, Facility Code, Facility Name, Facility Resource Type, Facility
   * e-mail, and Facility Phone.
   *
   * @return Zend_Search_Lucene_Document $doc
   *
   */
  public function updateLucene()
  {
    if (!$this->isAutoIndexed) {
      return null;
    }
    $doc = new Zend_Search_Lucene_Document();
    $doc->addField(Zend_Search_Lucene_Field::Keyword('Id', $this->id, 'utf-8'));
    $doc->addField(Zend_Search_Lucene_Field::unStored('facility', $this->facility_name, 'utf-8'));
    $doc->addField(Zend_Search_Lucene_Field::unStored('facility_code', $this->facility_code, 'utf-8'));

    $facilityInfo = agDoctrineQuery::create()
            ->select('f.id, fr.id, frt.id, frt.facility_resource_type, frt.facility_resource_type_abbr')
            ->from('agFacility f')
            ->innerJoin('f.agFacilityResource fr')
            ->innerJoin('fr.agFacilityResourceType frt')
            ->where('f.id=?', $this->id)
            ->execute(array(), Doctrine_Core::HYDRATE_SCALAR);

    $resourceType = null;
    // Cannot save facility's resource type info until after the facility is saved.
    foreach ($facilityInfo as $fac) {
      $resourceType = $resourceType . ' ' . $fac['frt_facility_resource_type'] . ' ' . $fac['frt_facility_resource_type_abbr'];
    }
    if (isset($resourceType)) {
      $doc->addField(Zend_Search_Lucene_Field::unStored('facility_resource', $resourceType, 'utf-8'));
    }

    // Make facilities searchable by e-mail
    $query = agDoctrineQuery::create()
            ->select('f.id, s.id, e.id, eec.id, ec.id, ec.email_contact')
            ->from('agFacility f')
            ->innerJoin('f.agSite s')
            ->innerJoin('s.agEntity e')
            ->innerJoin('e.agEntityEmailContact eec')
            ->innerJoin('eec.agEmailContact ec')
            ->where('f.id=?', $this->id);
    $queryString = $query->getSQLQuery();
    $facilityEmails = $query->execute(array(), Doctrine_Core::HYDRATE_SCALAR);

    $facility_email = null;
    foreach ($facilityEmails as $fac) {
      $facility_email .= ' ' . $fac['ec_email_contact'];
    }
    if (isset($facility_email)) {
      $doc->addField(Zend_Search_Lucene_Field::unStored('facility_email', $facility_email, 'utf-8'));
    }

    // Make facility searchable by address
    $query = agDoctrineQuery::create()
            ->select('f.id, s.id, e.id, eac.id, a.id, ama.id, av.id, aa.id, aa.alias, av.value')
            ->from('agFacility f')
            ->innerJoin('f.agSite s')
            ->innerJoin('s.agEntity e')
            ->innerJoin('e.agEntityAddressContact eac')
            ->innerJoin('eac.agAddress a')
            ->innerJoin('a.agAddressMjAgAddressValue ama')
            ->innerJoin('ama.agAddressValue av')
            ->leftJoin('av.agAddressAlias aa')
            ->where('f.id=?', $this->id);
    $queryString = $query->getSQLQuery();
    $facilityAddresses = $query->execute(array(), Doctrine_Core::HYDRATE_SCALAR);

    $facility_address = null;
    foreach ($facilityAddresses as $fac) {
      $facility_address .= ' ' . $fac['av_value'];
      $aliasAddress = $fac['aa_alias'];
      if ($aliasAddress != null) {
        $facility_address .= ' ' . $aliasAddress;
      }
    }
    if (isset($facility_address)) {
      $doc->addField(Zend_Search_Lucene_Field::unStored('facility_address', $facility_address, 'utf-8'));
    }

    // Make facility searchable by phone.
    $query = agDoctrineQuery::create()
            ->select('f.id, s.id, e.id, epc.id, pc.id, pc.phone_contact')
            ->from('agFacility f')
            ->innerJoin('f.agSite s')
            ->innerJoin('s.agEntity e')
            ->innerJoin('e.agEntityPhoneContact epc')
            ->innerJoin('epc.agPhoneContact pc')
            ->where('f.id=?', $this->id);
    $facilityPhones = $query->execute(array(), Doctrine_Core::HYDRATE_SCALAR);

    $facility_phone = null;
    foreach ($facilityPhones as $fac) {
      $facility_phone .= ' ' . $fac['pc_phone_contact'];
    }
    if (isset($facility_phone)) {
      $doc->addField(Zend_Search_Lucene_Field::unStored('facility_phone', $facility_phone, 'utf-8'));
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
  public function getBorough()
  {
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

