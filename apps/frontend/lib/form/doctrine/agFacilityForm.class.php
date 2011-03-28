<?php

/**
 *
 * Implements top-level form for creating and editing facility
 * records and associated facility resource records
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author Ilya Gulko, CUNY SPS
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
class agFacilityForm extends BaseagFacilityForm
{
  /*
   * configure() extends the base method to remove unused fields
   */

  public function configure()
  {
    /**
     *  unset fields that we will not be displaying in the for
     *
     * Method below of explicitly stating the widgets we want might be better...Might not too,
     * depends on validator maybe?
     */
    parent::configure();
    unset(
        $this['updated_at'],
        $this['created_at'],
        $this['site_id'],
        $this['ag_facility_resource_type_list']
//      $this['facility_name'],
//      $this['facility_code']


    );


    $this->setWidget('id', new sfWidgetFormInputHidden());

      $this->setWidget('facility_name', new sfWidgetFormInputText(array(), array('class' => 'inputGray')));
      //'facility_code' => new sfWidgetFormInputText(array(), array('class' => 'inputGray')),
    //));

//    $this->agEntity = $this->getObject()->getAgEntity();
    $this->agEntity = $this->getObject()->getAgSite()->getAgEntity();
    
    $this->embedAgFacilityForms();


    /**
     * Sort the widgets by using getPositions() and useFields().
     * Because useFields() also specifies all of the fields/widgets
     * that will be displayed, we have to take care to not get rid
     * of any fields.
     */
    
    //add csrf protection to the form that has been modified
    $this->addCSRFProtection();

    $formFields = $this->getWidgetSchema()->getPositions();
    $useFields = array('facility_name');
    $useFields = array_merge($useFields, array_diff($formFields, $useFields));
    $this->useFields($useFields);


    /**
     * Set labels on a few fields
     */
    $this->widgetSchema->setLabels(array(
      'resource' => 'Resources',
      'facility_name' => 'Name',
      //'facility_code' => 'Facility Code'
    ));

    /**
     * Set the formatter of this form to
     * agWidgetFormSchemaFormatterSection
     */
    $sectionsDeco = new agWidgetFormSchemaFormatterSection($this->getWidgetSchema());
    $this->getWidgetSchema()->addFormFormatter('section', $sectionsDeco);
    $this->getWidgetSchema()->setFormFormatterName('section');

  }

  public function embedPhoneForm($contactContainer)
  {
    $this->ag_phone_contact_types = Doctrine::getTable('agPhoneContactType')->createQuery('a')->execute();

    $phoneContainer = new sfForm(array(), array());
    $phoneDeco = new agWidgetFormSchemaFormatterRow($phoneContainer->getWidgetSchema());
    $phoneContainer->getWidgetSchema()->addFormFormatter('row', $phoneDeco);
    $phoneContainer->getWidgetSchema()->setFormFormatterName('row');
    foreach ($this->ag_phone_contact_types as $phoneContactType) {
      if ($id = $this->agEntity->id) {
        $phoneObject = Doctrine_query::create()
                ->from('agPhoneContact pc')
                ->where('pc.id IN (SELECT jn.phone_contact_id FROM agEntityPhoneContact jn WHERE jn.entity_id = ? AND phone_contact_type_id = ?)', array($id, $phoneContactType->id))
                ->execute()->getFirst();
      }
      $phoneContactForm = new agEmbeddedAgPhoneContactForm(isset($phoneObject) ? $phoneObject : null);
      $phoneContactForm->widgetSchema->setLabel('phone_contact', false);
      $phoneContainer->embedForm($phoneContactType->getPhoneContactType(), $phoneContactForm);
    }

    $contactContainer->embedForm('phone', $phoneContainer);
  }

  public function embedEmailForm($contactContainer)
  {
    $this->ag_email_contact_types = Doctrine::getTable('agEmailContactType')->createQuery('a')->execute();

    $emailContainer = new sfForm();
    $emailDeco = new agWidgetFormSchemaFormatterRow($emailContainer->getWidgetSchema());
    $emailContainer->getWidgetSchema()->addFormFormatter('row', $emailDeco);
    $emailContainer->getWidgetSchema()->setFormFormatterName('row');

    foreach ($this->ag_email_contact_types as $emailContactType) {
      if ($id = $this->agEntity->id) {
        $emailObject = Doctrine_query::create()
                ->from('agEmailContact ec')
                ->where('ec.id IN (SELECT jn.email_contact_id FROM agEntityEmailContact jn WHERE jn.entity_id = ? AND email_contact_type_id = ?)', array($id, $emailContactType->id))
                ->execute()->getFirst();
      }
      $emailContactForm = new agEmbeddedAgEmailContactForm(isset($emailObject) ? $emailObject : null);
      $emailContactForm->widgetSchema->setLabel('email_contact', false);
      $emailContainer->embedForm($emailContactType->getEmailContactType(), $emailContactForm);
    }
    $contactContainer->embedForm('email', $emailContainer);
  }

  public function embedResourcesForm($resourceContainer)
  {
    $facilityResourceContainer = new sfForm(array(), array());

    /**
     *  if the facility already has resources assigned, get all the data for them
     *   */
    if ($this->agFacilityResources = Doctrine::getTable('agFacilityResource')
            ->createQuery('agFR')
            ->select('agFR.*, agFRT.*, agFRS.*')
            ->from('agFacilityResource agFR, agFR.agFacilityResourceType agFRT, agFR.agFacilityResourceStatus')
            ->where('facility_id = ?', $this->getObject()->getId())
            ->execute()) {

      /**
       *  for every existing facility resource, create an agEmbeddedFacilityResourceForm and embed it into $facilityResourceContainer
       *   */
      foreach ($this->agFacilityResources as $facilityResource) {
        $facilityResourceForm = new agEmbeddedFacilityResourceForm($facilityResource);


        $facilityResourceId = $facilityResource->getId();
        $facilityResourceContainer->embedForm($facilityResourceId, $facilityResourceForm);
        $facilityResourceContainer->widgetSchema->setLabel($facilityResourceId, false);
      }
    }

    /**
     *  create a up to 3 new blank agEmbeddedFacilityResourceForm
     *  instances in case the user wants to add another facility
     *  resource
     *   */
    for ($iNewForm = 0; $iNewForm < max(3 - count($this->agFacilityResources), 1); $iNewForm++) {
      $facilityResourceForm = new agEmbeddedFacilityResourceForm();

      $facilityResourceContainer->embedForm('new' . $iNewForm, $facilityResourceForm);
      $facilityResourceContainer->widgetSchema->setLabel('new' . $iNewForm, false);
    }

    /**
     *  embed the facility resource container form into the facility form
     *   */
    
    $resourceContainer->embedForm('resource', $facilityResourceContainer);

  }

  public function embedAddressForm($contactContainer)
  {
    /**
     * Address Embedding Section
     *
     * This block sets up the embedded agEmbeddedAgAddressContactForms.
     * */
    $this->address_contact_types = Doctrine::getTable('agAddressContactType')
            ->createQuery('a')
            ->select('a.*')
            ->from('agAddressContactType a')
            ->where('address_contact_type = ?', 'work')
            ->execute();
    $this->address_formats = Doctrine::getTable('agAddressFormat')
            ->createQuery('addressFormat')
            ->select('af.*, ae.*')
            ->from('agAddressFormat af, af.agAddressElement ae')
            ->execute();

    // This loop makes a 3d array of line sequence values (as the first level key), inline sequence values (as
    // the second level key), address element values(as the third level string key), and address element ids (as
    // the third level value).

    /**
     * @todo this works fine for now, since we only have one address format, but should be
     * refactored to create a new array from values in the agAddressFormat table for each
     * address_standard_id in that table.
     * */
    foreach ($this->address_formats as $af) {
      $addressElements[$af->line_sequence][$af->inline_sequence][$af->getAgAddressElement()->address_element] = $af->getAgAddressElement()->id;
    }

    $addressContainer = new sfForm(array(), array()); // Container form.
    #$addressContainer->widgetSchema->setFormFormatterName('list');

    $addressDeco = new agWidgetFormSchemaFormatterRow($addressContainer->getWidgetSchema());
    $addressContainer->getWidgetSchema()->addFormFormatter('row', $addressDeco);
    $addressContainer->getWidgetSchema()->setFormFormatterName('row');

    $stateList = agDoctrineQuery::create()
            ->select('a.value')
            ->from('agAddressValue a')
            ->where('a.address_element_id = 4')
            ->execute();  //select * from ag_address_element .. and get the element_id by the text ('state')

    $this->entityAddress = Doctrine::getTable('agEntity')
            ->createQuery('entityAddresses')
            ->select('e.*, eac.*, act.*, a.*, as.*, aav.*, av.*, p.*, ae.*')
            ->from('agEntity e, e.agEntityAddressContact eac, eac.agAddressContactType act,
                eac.agAddress a, a.agAddressStandard as, a.agAddressMjAgAddressValue aav,
                aav.agAddressValue av, av.agAddressElement ae, e.agPerson p')
            ->where('e.id = ?', $this->getObject()->getAgSite()->getAgEntity()->getId())
            ->execute()
            ->getFirst();

//  foreach($this->entityAddresses as $entityAddress) {
//    $entityAddress = $entityAddress;
//    break;
//  }
//  $addressValueElement = agDoctrineQuery::create()
//    ->select('address.*, mj.*, value.*')
//    ->from('agAddress address, address.agAddressMjAgAddressValue mj, mj.agAddressValue value')
//    ->where('a.id = ?', $current->getId())
//    ->execute();

    $addressFirstPass = true;

    foreach ($this->address_contact_types as $address_contact_type) {
      $addressSubContainer = new sfForm(array(), array()); // Sublevel container forms beneath address to hold a complete address for each address type.
//    $addressDeco = new agWidgetFormSchemaFormatterInline($addressSubContainer->getWidgetSchema());
//    $addressSubContainer->getWidgetSchema()->addFormFormatter('row', $addressDeco);
//    $addressSubContainer->getWidgetSchema()->setFormFormatterName('row');

      foreach ($addressElements as $ae) {
        foreach ($ae as $addressElement) {
          $valueForm = new agEmbeddedAgAddressValueForm(); // Lowest level address form, actually holds the data.
          $valueForm->setDefault('address_element_id', $addressElement[key($addressElement)]); //set the default address_element_id.
          $valueForm->widgetSchema->setLabel('value', false); //hide the 'value' field label.
          // Hardcoded for now, this sets the input type for state to a dropdown list, populated only by address values that
          // have address element 4/state as their address_element value. Refactor to use agAddressFormat's field_type_id in
          // conjunction with agFieldType.
          if (key($addressElement) == 'state') {
            $valueForm->setWidget(
                'value',
                new sfWidgetFormDoctrineChoice(array(
                  'multiple' => false,
                  'model' => 'agAddressValue',
                  'add_empty' => true,
                  'key_method' => 'getValue'
                ))); //key_method sets the option value of the constructed select list to the value rather than id.
            $valueForm->widgetSchema->setLabel('value', false);
            $valueForm->widgetSchema['value']->addOption(
                'query',
                $stateList
            );
          }

          if (isset($this->entityAddress) && $this->entityAddress) {
            // Each of the agPerson's existing address records.
            foreach ($this->entityAddress->getAgEntityAddressContact() as $current) {
              if ($current->address_contact_type_id == $address_contact_type->id) {
                $addressValueElement = $current->getId();

                foreach ($current->getAgAddress()->getAgAddressMjAgAddressValue() as $av) {//Get the joins from agAddress to agAddressValue
                  if ($av->getAgAddressValue()->getAgAddressElement()->address_element == key($addressElement)) {
                    $valueForm->setDefault('value', $av->getAgAddressValue()->value);
                    $valueForm->id_holder = $av->getAgAddressValue()->id;
                  }
                }
              }
            }
          }
          $valueForm->addressType = $address_contact_type->address_contact_type; //set an addressType property for the form so the type can be used when saving.
          $addressSubContainer->embedForm(key($addressElement), $valueForm); //Embed the address elements.

          if (!$addressFirstPass) {
            //$addressSubContainer->widgetSchema->setLabel(key($addressElement), false);
          }
        }
      }
      $addressContainer->embedForm($address_contact_type, $addressSubContainer); //Embed the addresses-by-type

      $addressFirstPass = false;
    }
    $contactContainer->embedForm('address', $addressContainer); //Embed all the addresses into agPersonForm.
  }

  public function embedAgFacilityForms()
  {
    $this->widgetSchema->setNameFormat('ag_facility[%s]');
    $contactContainer = new sfForm();

    $resourceContainer = new sfForm();
    $this->embedResourcesForm($resourceContainer);
    $resourceContainer->getWidgetSchema()->setLabel('resource',false);
    $this->embedForm('resources', $resourceContainer);
//
    $this->embedAddressForm($contactContainer);
    $this->embedEmailForm($contactContainer);
    $this->embedPhoneForm($contactContainer);
    $this->embedForm('contact', $contactContainer);
  }

  /*
   * setup() extends the base method to remove unused fields and embed subforms
   */



  /**
   * saveEmbeddedForms() is a recursive function that saves all
   * forms supplied in the $forms parameter or in the form it is
   * called from
   *
   * @param $con database connection (optional)
   * @param $forms array of sfForm objects to save
   *
   * @return parent class's saveEmbeddedForms()
   */
  public function saveEmbeddedForms($con = null, $forms = null)
  {
    if (null === $forms) {
      $forms = $this->embeddedForms;
    }

    if (is_array($forms)) {
      foreach ($forms as $key => $form) {

//      /**
//       * Primary information section
//       */
//      if ($form instanceof sfForm && $key == 'primary') {
//        if ($this instanceof agFacilityForm) {
//          $this->getObject()->setFacilityName($this->values['primary']['facility_name']);
//          $this->getObject()->setFacilityCode($this->values['primary']['facility_code']);
//          $this->getObject()->save();
//        }
//      }

        /**
         *  Facility Resource section
         * */
        if ($form instanceof agEmbeddedFacilityResourceForm) {
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
            $objFacilityResource = $form->getObject();
            if ($objFacilityResource->capacity && $objFacilityResource->facility_resource_type_id
                && $objFacilityResource->facility_resource_status_id) {
              $form->getObject()->save();
            } else {
              $form->getObject()->delete();
            }
            unset($forms[$key]);
            //$form->getObject()->setFacilityId($this->getObject()->getId());
          }
        }

        /**
         *  Email Section
         * */
        if ($form instanceof agEmbeddedAgEmailContactForm) {
          //This query finds the email_contact_type ID we need for the next query.
          $typeQuery = Doctrine::getTable('agEmailContactType')->createQuery('b')
                  ->select('b.id')
                  ->from('agEmailContactType b')
                  ->where('b.email_contact_type = ?', $key);

          $typeId = $typeQuery->fetchOne()->id;

          //This query gets the person's agEntityEmailContact object, based on person_id and email_contact_type_id (as $typeId).
          $joinQuery = Doctrine::getTable('agEntityEmailContact')->createQuery('c')
                  ->select('c.id')
                  ->from('agEntityEmailContact c')
                  ->where('c.email_contact_type_id = ?', $typeId)
                  ->andWhere('c.entity_id =?', $this->getObject()->getAgSite()->getAgEntity()->id);
          //Check if the agEmbeddedAgEmailContactForm has an email_contact value.
          if ($form->getObject()->email_contact <> null) {
            // Get an agEntityEmailContact object from $joinQuery. Then create a new agEntityEmailContactForm
            // and put the retrieved object inside it. Set its priority to $typeId
            if ($join = $joinQuery->fetchOne()) {
              $joinForm = new agEntityEmailContactForm($join);
              $joinForm->getObject()->priority = $typeId;
            }
            // Or create a new agEntityEmailContactForm to be populated later and set its priority to $typeId.
            else {
              $joinForm = new agEntityEmailContactForm();
              $joinForm->getObject()->priority = $typeId;
            }

            // Check if the email_contact value has changed since the page was rendered.
            if ($form->getObject()->email_contact <> $form->getDefault('email_contact')) {
              // If it has changed, save the entered value as $emailLookUp. Then revert the object
              // to its default values from the page render. This prevents a duplicate entry error.
              $emailLookUp = $form->getObject()->email_contact;
              $form->updateObject($form->getDefaults());

              // Create a query to see if the submitted email value, as $emailLookUp, already exists
              // in the database.
              $q = Doctrine::getTable('agEmailContact')->createQuery('a')
                      ->select('a.id')
                      ->from('agEmailContact a')
                      ->where('a.email_contact = ?', $emailLookUp);

              // If it does...
              if ($queried = $q->fetchOne()) {
                // Get the id of the email in the db...
                $email_id = $queried->get('id');
                // ...then see if the agEntityEmailContact has an email_contact_id corresponding to the
                // email queried for. If it is, unset the form, no update is needed...
                if (isset($joinForm->email_contact_id) && $joinForm->email_contact_id == $email_id) {
                  unset($forms[$key]);
                }
                // ...If it wasn't, populate the agEntityEmailContact object's values and save it.
                // Then unset the form.
                else {
                  $joinForm->getObject()->entity_id = $this->getObject()->getAgSite()->getAgEntity()->id;
                  $joinForm->getObject()->email_contact_id = $email_id;
                  $joinForm->getObject()->email_contact_type_id = $typeId;
                  $joinForm->getObject()->save();
                  unset($forms[$key]);
                }
              }
              // If the entered email isn't in the database already, make a new agEmailContact object,
              // populate it with the new email, and save it.
              // Then populate the agEntityEmailContact object's values, associating it with the
              // id of the new email, and save it. Unset the form.
              elseif (!$queried = $q->fetchOne()) {
                $newEmail = new agEmailContact();
                $newEmail->email_contact = $emailLookUp;
                $newEmail->save();
                $joinForm->getObject()->entity_id = $this->getObject()->getAgSite()->getAgEntity()->id;
                $joinForm->getObject()->email_contact_id = $newEmail->id;
                $joinForm->getObject()->email_contact_type_id = $typeId;
                $joinForm->getObject()->save();
                unset($forms[$key]); //This unsets the form, prevents a multiple save.
              }
            }
            // If the name hasn't been changed, unset the form.
            else {
              unset($forms[$key]);
            }
          }
          // If the name field is blank, unset the field...
          else {
            unset($forms[$key]);
            // ...if it was populated, delete the existing agPersonMjAgPersonName object since it is
            // no longer needed.
            if ($join = $joinQuery->fetchOne()) {
              $join->delete();
            }
          }
        }

        /**
         *  Phone Section
         * */
        if ($form instanceof agEmbeddedAgPhoneContactForm) {
          //This query finds the phone_contact_type ID we need for the next query.
          $typeQuery = Doctrine::getTable('agPhoneContactType')->createQuery('b')
                  ->select('b.id')
                  ->from('agPhoneContactType b')
                  ->where('b.phone_contact_type = ?', $key);

          $typeId = $typeQuery->fetchOne()->id;

          //This query gets the person's agEntityPhoneContact object, based on person_id and name_type_id (as $typeId).
          $joinQuery = Doctrine::getTable('agEntityPhoneContact')->createQuery('c')
                  ->select('c.id')
                  ->from('agEntityPhoneContact c')
                  ->where('c.phone_contact_type_id = ?', $typeId)
                  ->andWhere('c.entity_id =?', $this->getObject()->getAgSite()->getAgEntity()->id);
          //Check if the agEmbeddedAgPhoneContactForm has an phone_contact value.
          if ($form->getObject()->phone_contact <> null) {
            // Get an agEntityPhoneContact object from $joinQuery. Then create a new agEntityPhoneContactForm
            // and put the retrieved object inside it. Set its priority to $typeId
            if ($join = $joinQuery->fetchOne()) {
              $joinForm = new agEntityPhoneContactForm($join);
              $joinForm->getObject()->priority = $typeId;
            }
            // Or create a new agEntityPhoneContactForm to be populated later and set its priority to $typeId.
            else {
              $joinForm = new agEntityPhoneContactForm();
              $joinForm->getObject()->priority = $typeId;
            }
            #$joinForm->getObject()->phone_format_id = 1;
            // Check if the phone_contact value has changed since the page was rendered.
            if ($form->getObject()->phone_contact <> $form->getDefault('phone_contact')) {
              // If it has changed, save the entered value as $phoneLookUp. Then revert the object
              // to its default values from the page render. This prevents a duplicate entry error.
              $dirtyPhone = $form->getObject()->phone_contact;
              $phoneLookUp = preg_replace('/[^0-9x]+/', '', $dirtyPhone);

              $form->updateObject($form->getDefaults());

              // Create a query to see if the submitted phone value, as $phoneLookUp, already exists
              // in the database.
              $q = Doctrine::getTable('agPhoneContact')->createQuery('a')
                      ->select('a.id')
                      ->from('agPhoneContact a')
                      ->where('a.phone_contact = ?', $phoneLookUp);

              // If it does...
              if ($queried = $q->fetchOne()) {
                // Get the id of the phone in the db...
                $phone_id = $queried->get('id');
                // ...then see if the agEntityPhoneContact has an phone_contact_id corresponding to the
                // phone queried for. If it is, unset the form, no update is needed...
                if (isset($joinForm->phone_contact_id) && $joinForm->phone_contact_id == $phone_id) {
                  unset($forms[$key]);
                }
                // ...If it wasn't, populate the agEntityPhoneContact object's values and save it.
                // Then unset the form.
                else {
                  $joinForm->getObject()->entity_id = $this->getObject()->getAgSite()->getAgEntity()->id;
                  $joinForm->getObject()->phone_contact_id = $phone_id;
                  $joinForm->getObject()->phone_contact_type_id = $typeId;
                  $joinForm->getObject()->save();
                  unset($forms[$key]);
                }
              }
              // If the entered phone isn't in the database already, make a new agPhoneContact object,
              // populate it with the new phone, and save it.
              // Then populate the agEntityPhoneContact object's values, associating it with the
              // id of the new phone, and save it. Unset the form.
              elseif (!$queried = $q->fetchOne()) {
                $newPhone = new agPhoneContact();
                $newPhone->phone_contact = $phoneLookUp;
                $newPhone->phone_format_id = 1;
                /** @todo phone_format_id shouldn't be hardcoded -Ilya */
                $newPhone->save();
                $joinForm->getObject()->entity_id = $this->getObject()->getAgSite()->getAgEntity()->id;
                $joinForm->getObject()->phone_contact_id = $newPhone->id;
                $joinForm->getObject()->phone_contact_type_id = $typeId;
                $joinForm->getObject()->save();
                unset($forms[$key]); //This unsets the form, prevents a multiple save.
              }
            }
            // If the name hasn't been changed, unset the form.
            else {
              unset($forms[$key]);
            }
          }
          // If the phone contact field is blank, unset the field...
          else {
            unset($forms[$key]);
            // ...if it was populated, delete the existing agEntityPhoneContact object since it is
            // no longer needed.
            if ($join = $joinQuery->fetchOne()) {
              $join->delete();
            }
          }
        }


        /**
         *  Address Saving Section
         */
        if (isset($form->addressType)) {//This value is only set for agEmbeddedAgAddressValueForms. Used due to multi-level complexity of address.
          //This query finds the address_contact_type ID we need for the next query.
          $typeQuery = Doctrine::getTable('agAddressContactType')->createQuery('b')
                  ->select('b.id')
                  ->from('agAddressContactType b')
                  ->where('b.address_contact_type = ?', $form->addressType);

          $typeId = $typeQuery->fetchOne()->id;

          //This query gets the person's agEntityAddressContact object, based on person_id and address_contact_type_id (as $typeId).
          $joinEntityAddressQuery = Doctrine::getTable('agEntityAddressContact')->createQuery('c')
                  ->select('c.id')
                  ->from('agEntityAddressContact c')
                  ->where('c.address_contact_type_id = ?', $typeId)
                  ->andWhere('c.entity_id = ?', $this->getObject()->getAgSite()->getAgEntity()->id);
          //Check if the agEmbeddedAgAddressValueForm has a value.

          if ($form->getObject()->value <> null) {
            // Get an agEntityAddressContact object from $joinEntityAddressQuery. Then create a new agEntityAddressContactForm
            // and put the retrieved object inside it. Set its priority to $typeId
            if ($join = $joinEntityAddressQuery->fetchOne()) {
              $joinEntityAddressForm = new agEntityAddressContactForm($join);
              $joinEntityAddressForm->getObject()->priority = $typeId;
            }
            // Or create a new agAddress, set its address_standard_id, and save it. Then create
            // agEntityPhoneContactForm to be populated later and set its priority and address_id.
            else {
              $newAddress = new agAddress();
              $newAddress->address_standard_id = 1;
              $newAddress->save();
              $joinEntityAddressForm = new agEntityAddressContactForm();
              $joinEntityAddressForm->getObject()->priority = $typeId;
              $joinEntityAddressForm->getObject()->address_id = $newAddress->id;
              $joinEntityAddressForm->getObject()->address_contact_type_id = $typeId;
              $joinEntityAddressForm->getObject()->entity_id = $this->getObject()->getAgSite()->getAgEntity()->id;
              $joinEntityAddressForm->getObject()->save();
            }

            // Check if the agAddressValue has changed since the page was rendered.
            if ($form->getObject()->value <> $form->getDefault('value')) {
              // Store the newly entered value as $addressValueLookUp. Then revert the object
              // to its default values from the page render. This prevents a duplicate entry error.
              $addressValueLookUp = $form->getObject()->value;
              $form->updateObject($form->getDefaults());

              // Create a query to see if the submitted address value, as $addressValueLookUp, already exists
              // in the database.
              $addressValueQuery = Doctrine::getTable('agAddressValue')->createQuery('a')
                      ->select('a.id')
                      ->from('agAddressValue a')
                      ->where('a.value = ?', $addressValueLookUp)
                      ->andWhere('a.address_element_id = ?', $form->getObject()->address_element_id);

              // If it does...
              if ($queried = $addressValueQuery->fetchOne()) {
                // If it exists, get an agAddressMjAgAddressValue object that joins the id of the agAddress being
                // worked with and the id of the original agAddressValue being worked with. Used to change an
                // address_value_id on the agAddressMjAgAddressValue object. id_holder is only set for already joined
                // address values.
                if (isset($form->id_holder)) {
                  $joinAddressValueQuery = Doctrine::getTable('agAddressMjAgAddressValue')->createQuery('a')
                          ->select('a.id')
                          ->from('agAddressMjAgAddressValue a')
                          ->where('a.address_value_id = ?', $form->id_holder)
                          ->andWhere('a.address_id = ?', $joinEntityAddressForm->getObject()->address_id);

                  $joinAddressValue = $joinAddressValueQuery->fetchOne();
                  // reassign the agAddressValue of the join to the newly selected value.
                  $joinAddressValue->address_value_id = $queried->id;
                  $joinAddressValue->save();
                  unset($forms[$key]);
                } else {
                  $joinAddressValue = new agAddressMjAgAddressValue();
                  $joinAddressValue->address_id = $joinEntityAddressForm->getObject()->address_id;
                  $joinAddressValue->address_value_id = $queried->id;
                  $joinAddressValue->save();
                  unset($forms[$key]);
                }
              }
              // If the entered address_value isn't in the database already, make a new agAddressValue object,
              // populate it with the new address value, and save it.
              elseif (!$queried = $addressValueQuery->fetchOne()) {
                $newAddressValue = new agAddressValue();
                $newAddressValue->value = $addressValueLookUp;
                $newAddressValue->address_element_id = $form->getObject()->address_element_id;
                $newAddressValue->save();

                if (isset($form->id_holder)) {
                  $joinAddressValueQuery = Doctrine::getTable('agAddressMjAgAddressValue')->createQuery('a')
                          ->select('a.id')
                          ->from('agAddressMjAgAddressValue a')
                          ->where('a.address_value_id = ?', $form->id_holder)
                          ->andWhere('a.address_id = ?', $joinEntityAddressForm->getObject()->address_id);

                  $joinAddressValue = $joinAddressValueQuery->fetchOne();
                  // reassign the agAddressValue of the join to the newly selected value.
                  $joinAddressValue->address_value_id = $newAddressValue->id;
                  $joinAddressValue->save();
                  unset($forms[$key]);
                } else {
                  $joinAddressValue = new agAddressMjAgAddressValue();
                  $joinAddressValue->address_id = $joinEntityAddressForm->getObject()->address_id;
                  $joinAddressValue->address_value_id = $newAddressValue->id;
                  $joinAddressValue->save();
                  unset($forms[$key]);
                }
              }
            }
            // If the address_value hasn't been changed, unset the form.
            else {
              unset($forms[$key]);
            }
          }
          // If the address_value field is blank, unset the form...
          else {
            unset($forms[$key]);
            // ...if it was populated, delete the existing agAddressMjAgAddressValue object since it is
            // no longer needed.
            if ($form->getObject()->value <> $form->getDefault('value')) {
              $joinAddressValueQuery = Doctrine::getTable('agAddressMjAgAddressValue')->createQuery('a')->select('a.id')
                      ->from('agAddressMjAgAddressValue a')
                      ->where('a.address_value_id = ?', $form->id_holder)
                      ->andWhere('a.address_id = ?', $joinEntityAddressQuery->fetchOne()->address_id);

              if ($join = $joinAddressValueQuery->fetchOne()) {
                $join->delete();
              }
            }
          }
          if ($entJoin = $joinEntityAddressQuery->fetchOne()) {
            $q = Doctrine::getTable('agAddressMjAgAddressValue')->createQuery('a')
                    ->select('a.id')->from('agAddressMjAgAddressValue a')
                    ->where('a.address_id = ?', $entJoin->address_id);
            if (!($r = $q->fetchOne())) {
              $entAdd = $entJoin->getAgAddress();
              $entJoin->delete();
              $entAdd->delete();
            }
          }
        }
      }
    }
    return parent::saveEmbeddedForms($con, $forms);
  }

}
