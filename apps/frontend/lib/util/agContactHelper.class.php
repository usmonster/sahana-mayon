<?php

/**
 *
 * Provides bulk-contact manipulation methods
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author Shirley Chan, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 *
 */
class agContactHelper
{

  /**
   *
   * @param string $type Type of contact:  email, phone, or email
   * @return array $contactType An associative array, array(contact_type_id => contact_type).
   */
  public static function getContactTypes($type)
  {
    $contactTypes = array();

    switch (strtolower($type)) {
      case 'email':
        $table = 'agEmailContactType';
        $attribute = 'email_contact_type';
        break;
      case 'phone':
        $table = 'agPhoneContactType';
        $attribute = 'phone_contact_type';
        break;
      case 'address':
        $table = 'agAddressContactType';
        $attribute = 'address_contact_type';
        break;
      default;
        return $contactTypes;
    }

    $contactTypes = agDoctrineQuery::create()
                    ->select('id, ' . $attribute)
                    ->from($table)
                    ->execute(array(), 'key_value_pair');
    return $contactTypes;
  }

  /**
   *
   * @param array $phoneFormatTypes An array of phone format types
   * @return array An associative array, array(phone_format_id => phone_format_type).
   */
  public static function getPhoneFormatTypes($phoneFormatTypes)
  {
    $phoneFormatTypes = agDoctrineQuery::create()
                    ->select('pf.id, pft.phone_format_type')
                    ->from('agPhoneFormat pf')
                    ->innerJoin('pf.agPhoneFormatType pft')
                    ->whereIn('phone_format_type', $phoneFormatTypes)
                    ->execute(array(), 'key_value_pair');
    return $phoneFormatTypes;
  }

}
