<?php
/**
*
* Extends BaseagEntity and returns entity information
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

class agEntity extends BaseagEntity
{
  /**
   * getEntityEmailContact()
   *
   * @return collection of this entity's emails and email contact
   *         types, indexed by email contact type
   *
   */
  public function getEntityEmailContact() {
    if ($this->getId()) {
      return Doctrine_Core::getTable('agEmailContact')
        ->createQuery('a')
        ->select('eec.*, ec.*, ect.*')
        ->from('agEntityEmailContact eec INDEXBY eec.email_contact_type_id, eec.agEmailContact ec, eec.agEmailContactType ect')
        ->where('eec.entity_id = ?', $this->getId())
        ->execute();
      ;
    }
  }

  /**
   * getEntityPhoneContact()
   *
   * @return collection of this entity's emails and email contact
   *         types, indexed by email contact type
   *
   */
  public function getEntityPhoneContact() {
    if ($this->getId()) {
      return Doctrine_Core::getTable('agPhoneContact')
        ->createQuery('a')
        ->select('epc.*, pc.*, pct.*, pf.*')
        ->from('agEntityPhoneContact epc INDEXBY epc.phone_contact_type_id, epc.agPhoneContact pc, epc.agPhoneContactType pct, pc.agPhoneFormat pf')
        ->where('epc.entity_id = ?', $this->getId())
        ->execute();
      ;
    }
  }

  /**
   * delete()
   *
   * extends the base class's delete() function to delete related
   * objects before deleting agEntity
   */
  public function delete(Doctrine_Connection $conn = null)
  {
    $entityData = array();

    /**
     * Check for any related agSite, agPerson, or agOrganization
     * records and delete them first
     */
    $entityTypes = array('agSite', 'agPerson', 'agOrganization');

    /**
     * Check for each type of record
     */
    foreach ($entityTypes as $eType) {
      $method = 'get' . ucfirst($eType);
      if ($entityDataItem = $this->$method()->getFirst()) {
        $entityData[] = $entityDataItem;
      }
    }

    /**
     * Make sure that there is only one type of record. If there is
     * more than one, throw an exception, because this should never
     * happen.
     */
    if (count($entityData) > 1) {
      throw new LogicException(
          'agEntity is linked to more than one record! This should never happen!');
    }

    /**
     * If there is a related record, delete it
     */
    if (count($entityData)) {
      $entityData[0]->delete();
    }

    /**
     * Delete related phone, email, and address records
     */
    foreach ($this->getAgEntityPhoneContact() as $agC) {
      $agC->delete();
    }

    foreach ($this->getAgEntityEmailContact() as $agC) {
      $agC->delete();
    }

    foreach ($this->getAgEntityAddressContact() as $agC) {
      $agC->delete();
    }

    /**
     * Pass through to the base class's delete() method
     */
    return parent::delete($conn);
  }

}

