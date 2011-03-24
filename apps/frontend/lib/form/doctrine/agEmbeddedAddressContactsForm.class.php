<?php
/**
* Agasti Embedded Address Contacts Form is the container form for multiple phone contact entry
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


class agEmbeddedAddressContactsForm extends sfForm
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
* This sets up the agEmbeddedAddressContactForms nedded to create an agPerson. Depending on whether the person is being created or edited,
* forms are either empty (if creating) or populated (if editing, and the person has, through the entity table an agEntityAddressContact value
* for the agAddressContactType associated with the embedded form.
**/
  public function configure()
  {
    $this->address_contact_types = Doctrine::getTable('agAddressContactType')->createQuery('a')->execute();
    $this->address_formats = Doctrine::getTable('agAddressFormat')->createQuery('a')->execute();
    /**
    *  The loops below make a 2-d array of line sequence values, inline sequence values, and address element ids. Use it to output fields for address entry.
    **/


    foreach($this->address_formats as $af)
    {
      $addressElements[$af->line_sequence][$af->inline_sequence] = $af->getAgAddressElement()->address_element;
    }

    foreach($addressElements as $ae)
    {
      foreach($ae as $addressElement)
      {
        $floo = new agEmbeddedAddressContactForm();
        // Hardcoded for now, this sets the input type for state to a dropdown list, populated only by address values that 
        // have address element 4/state as their address_element value. Refactor to use agAddressFormat's field_type_id in
        // conjunction with agFieldType.
        if($addressElement == 'state')
        {
          $floo->setWidget('address_to_type', new sfWidgetFormDoctrineChoice(array('multiple' => false, 'model' => 'agAddressValue')));
          $floo->widgetSchema->setLabel('address_to_type', false);
          $floo->widgetSchema['address_to_type']->addOption(
            'query',
             agDoctrineQuery::create()
               ->select('a.value')
               ->from('agAddressValue a')
               ->where('a.address_element_id = 4')
          );
        }
        $this->embedForm($addressElement, $floo);
      }
    }

    $q = agDoctrineQuery::create()
                              ->select('f.line_sequence AS line_sequence, f.inline_sequence AS inline_sequence, f.pre_delimiter AS pre_delimiter, f.post_delimiter AS post_delimiter, f.is_required AS is_required, e.address_element AS address_element')
                              ->from('agAddressFormat f')
                              ->leftJoin('f.agAddressStandard s')
                              ->leftJoin('s.agCountry c')
                              ->leftJoin('f.agAddressElement e')
                              ->where('s.address_standard = ?', 'us standard')
                              ->andWhere('c.country = ?', 'United States');
    $this->gorp = $q->execute(array(), Doctrine::HYDRATE_NONE);

   
    foreach ($this->address_contact_types as $address_contact_type)
    {
      $type = new agEmbeddedAddressContactForm();
      $type->setDefault('address_contact_type_id', $address_contact_type->getId());
      $type->setDefault('priority', 1);

      foreach ($this->person->getAgEntity()->getAgEntityAddressContact() as $current)
      {
        $address = $current->getAgAddress();
        $formats = $current->getAgAddress()->getAgAddressStandard()->getAgAddressFormat();
        $addressValues = $address->getAgAddressMjAgAddressValue();

        if($current->address_contact_type_id == $address_contact_type->id)
        {
          foreach($formats as $format)
          {
            foreach($addressValues as $addressValue)
            {
              if ($addressValue->getAgAddressValue()->getAddressElementId() == $format->getAddressElementId())
              {
                $sploo = $addressValue->getAgAddressValue()->getValue();
               // echo $sploo . '<br />';
              }
            }
          }
          //echo '<br />';
        }
        if ($current->getAddressContactTypeId() == $address_contact_type->getId())
        {
          $type->getObject()->setAgAddressContactType($address_contact_type);
          $type = new agEmbeddedAddressContactForm();
          //$type->setDefault('address_to_type', $current->getAgAddress()->getAgAddressMjAgAddressValue()->getAgAddressValue());
        }
      }
      $type->getObject()->setAddressContactTypeId($address_contact_type->id);
      //$this->embedForm($address_contact_type->getAddressContactType(), $type);
    }
  }
}