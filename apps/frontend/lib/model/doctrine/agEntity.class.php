<?php
/**
*
* Extends BaseagEntity and returns entity information
*
* PHP Version 5.3
*
* LICENSE: This source file is subject to LGPLv2.1 license
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

