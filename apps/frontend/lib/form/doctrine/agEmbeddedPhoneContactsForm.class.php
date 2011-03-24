<?php
/**
* Agasti Embedded Phone Contacts Form is the container form for multiple phone contact entry
*
* PHP Version 5.3
*
* LICENSE: This source file is subject to LGPLv2.1 license
* that is available through the world-wide-web at the following URI:
* http://www.gnu.org/copyleft/lesser.html
*
* @author Nils Stolpe, CUNY SPS
*
* Copyright of the Sahana Software Foundation, sahanafoundation.org
*/


class agEmbeddedPhoneContactsForm extends sfForm
{
  protected $person;

/**
* @param $person is an agPerson object to be acted on
**/
  public function __construct(agPerson $person)
  {
    $this->person = $person;

    parent::__construct();
  }

/**
* This sets up the agEmbeddedPhoneContactForms nedded to create an agPerson. Depending on whether the person is being created or edited,
* forms are either empty (if creating) or populated (if editing, and the person has, through the entity table an agEntityPhoneContact value
* for the agPhoneContactType associated with the embedded form.
**/
  public function configure()
  {
    $this->phone_contact_types = Doctrine::getTable('agPhoneContactType')->createQuery('a')->execute();

    foreach ($this->phone_contact_types as $phone_contact_type)
    {
      $type = new agEmbeddedPhoneContactForm();
      $type->setDefault('phone_contact_type_id', $phone_contact_type->getId());
      $type->setDefault('priority', 1);
      foreach ($this->person->getAgEntity()->getAgEntityPhoneContact() as $current)
      {
        if ($current->getPhoneContactTypeId() == $phone_contact_type->getId())
        {
          $type->getObject()->setAgPhoneContactType($phone_contact_type);
          $type = new agEmbeddedPhoneContactForm();
          $type->setDefault('phone_to_type', preg_replace(
                  $current->getAgPhoneContact()->getAgPhoneFormat()->getAgPhoneFormatType()->match_pattern,
                  $current->getAgPhoneContact()->getAgPhoneFormat()->getAgPhoneFormatType()->replacement_pattern,
                  $current->getAgPhoneContact()->phone_contact));
        }
      }
      $type->getObject()->setPhoneContactTypeId($phone_contact_type->id);
      $this->embedForm($phone_contact_type->getPhoneContactType(), $type);
    }
  }
}