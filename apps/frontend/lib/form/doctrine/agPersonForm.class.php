<?php

/**
 * Agasti Person Form Class - A class to generate either a 'new person' or
 * 'edit person' form
 *
 * PHP Version 5
 *
 * LICENSE: This source file is subject to LGPLv3.0 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 *
 * @author Charles Wisniewski, CUNY SPS
 * @author Nils Stolpe, CUNY SPS
 * @author Ilya Gulko, CUNY SPS
 *
 * @todo Major clean-up required.
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
class agPersonForm extends BaseagPersonForm
{
  public function embedDateOfBirthForm() {
    $dateOfBirthForm = new agEmbeddedPersonDateOfBirthForm($this->getObject()->getAgPersonDateOfBirth());
    $dateOfBirthForm->widgetSchema->setLabel('date_of_birth', 'Date of Birth');
    $dateOfBirthForm->setDefault('person_id', $this->getObject()->id);
    $this->embedForm('date of birth', $dateOfBirthForm);
  }
  
  public function embedLanguageForm() {
    $this->ag_person_language_formats = Doctrine::getTable('agLanguageFormat')->createQuery('a')->execute();
    //create the container form and set it's formatter.
    $languageContainer = new sfForm();
    $langConDeco = new agWidgetFormSchemaFormatterSubContainer($languageContainer->getWidgetSchema());
    $languageContainer->getWidgetSchema()->addFormFormatter('langConDeco', $langConDeco);
    $languageContainer->getWidgetSchema()->setFormFormatterName('langConDeco');

    //set the max limit for the iterator for the $langSubContainer forms to 3. Then check the languages, if they're greater than 3, reset it to that greater number.
    $m = 3;
    if (count($this->getObject()->getAgPersonMjAgLanguage()) > 2) {
      $m = count($this->getObject()->getAgPersonMjAgLanguage()) + 1;
    }

    //Create the $languageSubContainer forms and set their formatters.
    for ($i = 1; $i <= $m; $i++) {
      $languageSubContainer = new sfForm(); //subcontainer form to hold language and join forms.
      $langSubConDeco = new agWidgetFormSchemaFormatterSubContainer($languageSubContainer->getWidgetSchema());
      $languageSubContainer->getWidgetSchema()->addFormFormatter('langSubConDeco', $langSubConDeco);
      $languageSubContainer->getWidgetSchema()->setFormFormatterName('langSubConDeco');

      $personLanguages = $this->getObject()->getAgPersonMjAgLanguage();
      // If the current person already has some languages, set the default id and language values of the form.
      $languageForm = new agEmbeddedAgPersonMjAgLanguageForm();
      $languageForm->setDefault('priority', $i);
      if (isset($personLanguages[$i - 1])) {
        //$languageForm = new agEmbeddedAgPersonMjAgLanguageForm($personLanguages[$i - 1]);
        $languageForm->setDefaults(array(
          'language_id' => $personLanguages[$i - 1]->language_id,
          'id' => $personLanguages[$i - 1]->id,
          //Right now, priority is hardcoded to the order forms show up in. Somewhat sensible, maybe indicate this on frontend?
          'priority' => $i
        ));
      }
      // If not, just create the empty forms.
      // Only create the labels if this is the first language, so everything shows up in a matrix.
      if ($i <> 1) {
        $languageForm->widgetSchema->setLabel('language_id', false);
      } else {
        $languageForm->widgetSchema->setLabel('language_id', 'Language Name');
      }

      $languageSubContainer->embedForm('language', $languageForm);
      $languageSubContainer->widgetSchema->setLabel('language', false);

      // Create the forms for language competency, one for each format. If the person has them already, stick in the objects.
      foreach ($this->ag_person_language_formats as $langFormat) {
        $formatForm = new agEmbeddedAgPersonLanguageCompetencyForm();
        $formatForm->setDefault('language_format_id', $langFormat->id);
        // If the $languageForm has a default for language, check for related competencies.
        if ($languageForm->getDefault('language_id') <> null) {
          $q = Doctrine_Query::create()
                  ->select('a.id')
                  ->from('agPersonMjAgLanguage a')
                  ->where('a.person_id = ?', $this->getObject()->id)
                  ->andWhere('a.language_id =?', $languageForm->getDefault('id'));

          $competencyQuery = Doctrine_query::create()
                  ->select('a.*')
                  ->from('agPersonLanguageCompetency a')
                  ->where('person_language_id = ?', $languageForm->getDefault('id'))
                  ->andWhere('language_format_id = ?', $langFormat->id);
          // Check if a competency has been assigned to the current language/language-format combo. If it has, fill the form w/ the object. If not, make an empty form.
          if ($competencyObject = $competencyQuery->fetchOne()) {
            $formatForm = new agEmbeddedAgPersonLanguageCompetencyForm($competencyObject);
          }
        } else {
          $formatForm->setDefault('language_format_id', $langFormat->id);
        }
        // If the $languagForm doesn't have an object, make an empty $formatForm.
        // Same as with $languageForm. We only want labels for the first row of objects. The others will fall directly under, so the labels should be understandable.
        if ($i <> 1) {
          $formatForm->widgetSchema->setLabel('language_competency_id', false);
        } else {
          $formatForm->widgetSchema->setLabel('language_competency_id', ucwords($langFormat->language_format));
        }
        $languageSubContainer->embedForm($langFormat->language_format, $formatForm);
        $languageSubContainer->widgetSchema->setLabel($langFormat->language_format, false);
      }
      $languageContainer->embedForm('language ' . $i, $languageSubContainer);
      $languageContainer->widgetSchema->setLabel('language ' . $i, false);
    }

    $this->embedForm('languages', $languageContainer);
  }

  public function embedNameForm(){
    $this->ag_person_name_types = Doctrine::getTable('agPersonNameType')->createQuery('a')->execute();
    $nameContainer = new sfForm(array(), array());
    //$nameContainer->widgetSchema->setFormFormatterName('list');
    $nameConDeco = new agWidgetFormSchemaFormatterSubContainer($nameContainer->getWidgetSchema());
    $nameContainer->getWidgetSchema()->addFormFormatter('nameConDeco', $nameConDeco);
    $nameContainer->getWidgetSchema()->setFormFormatterName('nameConDeco');
    foreach ($this->ag_person_name_types as $nameType) {
      $nameForm = new agEmbeddedAgPersonNameForm();
      foreach ($this->getObject()->getAgPersonMjAgPersonName() as $current) {
        if ($current->getPersonNameTypeId() == $nameType->getId()) {
          $nameForm->setDefault('person_name', $current->getAgPersonName()->person_name);
        }
      }
      $nameForm->widgetSchema->setLabel('person_name', ucwords($nameType->person_name_type));
      $nameContainer->embedForm($nameType->getPersonNameType(), $nameForm);
    }

    $this->embedForm('name', $nameContainer);
  }

  public function setup()
  {
    parent::setup();

    //$this->getWidget('ag_scenario_list')->setOption('expanded', true);
    //$this->getWidget('ag_nationality_list')->setOption('expanded', true);
    //$this->getWidget('ag_religion_list')->setOption('expanded', true)->setOption('column_count', 5);
  }

  /**
   * Initializes the array of datapoints for the person form.
   * */
  public function configure()
  {
    /**
     * Unset widgets that are auto-filled, unnecessary, or whose relations
     * are not properly defined without using embedded forms.
     * */
    unset($this['created_at'],
        $this['updated_at'],
        $this['ag_language_list'],
        $this['ag_country_list'],
        $this['ag_aid_provider_list'],
        $this['ag_import_list'],
        $this['ag_residential_status_list'],
        $this['ag_import_type_list'],
        $this['ag_account_list'],
        $this['ag_phone_contact_type_list'],
        $this['ag_email_contact_type_list'],
        $this['ag_address_contact_type_list'],
        $this['ag_phone_contact_list'],
        $this['ag_email_contact_list'],
        $this['ag_email_contact'],
        $this['ag_person_name_list'],
        $this['ag_person_name_type_list'],
        $this['entity_id'],
        $this['ag_person_custom_field_list']);

    /**
     * Remove multiple selection from widgets that have been autogenerated with multiple selection.
     **/
    $this->setWidget('ag_sex_list', new sfWidgetFormDoctrineChoice(array('multiple' => false, 'model' => 'agSex')));
    $this->setWidget('ag_ethnicity_list', new sfWidgetFormDoctrineChoice(array('multiple' => false, 'model' => 'agEthnicity')));
    $this->setWidget('ag_marital_status_list', new sfWidgetFormDoctrineChoice(array('multiple' => false, 'model' => 'agMaritalStatus')));

    /**
     * Give the list widgets more aesthetically appealing and descriptive labels.
     * */
    //$this->widgetSchema->setLabel('ag_scenario_list', 'Scenario');
    $this->widgetSchema->setLabel('ag_sex_list', 'Sex');
    $this->widgetSchema->setLabel('ag_religion_list', 'Religion');
    $this->widgetSchema->setLabel('ag_profession_list', 'Profession');
    $this->widgetSchema->setLabel('ag_nationality_list', 'Nationality');
    $this->widgetSchema->setLabel('ag_ethnicity_list', 'Ethnicity');
    $this->widgetSchema->setLabel('ag_marital_status_list', 'Marital Status');

    /**
     * Narrow down the options for select lists by using app_display. Lots of repetetive code,
     * could be moved to function.
     * */
    $this->widgetSchema['ag_nationality_list']->addOption(
        'query',
            Doctrine_Query::create()
            ->select('a.nationality')
            ->from('agNationality a')
            ->where('a.app_display = 1')
    );

    $this->widgetSchema['ag_religion_list']->addOption(
        'query',
            Doctrine_Query::create()
            ->select('a.religion')
            ->from('agReligion a')
            ->where('a.app_display = 1')
    );

    $this->widgetSchema['ag_profession_list']->addOption(
        'query',
            Doctrine_Query::create()
            ->select('a.profession')
            ->from('agProfession a')
            ->where('a.app_display = 1')
    );

    $this->widgetSchema['ag_ethnicity_list']->addOption(
        'query',
            Doctrine_Query::create()
            ->select('a.ethnicity')
            ->from('agEthnicity a')
            ->where('a.app_display = 1')
    );

    $this->widgetSchema['ag_sex_list']->addOption(
        'query',
            Doctrine_Query::create()
            ->select('a.sex')
            ->from('agSex a')
            ->where('a.app_display = 1')
    );

    $this->widgetSchema['ag_marital_status_list']->addOption(
        'query',
            Doctrine_Query::create()
            ->select('a.marital_status')
            ->from('agMaritalStatus a')
            ->where('a.app_display = 1')
    );

    $this->agEntity = $this->getObject()->getAgEntity();

    /**
     * Embed Forms
     * */
    $this->embedDateOfBirthForm();
    $this->embedLanguageForm();
    $this->embedNameForm();



    /**
     * Embedded Forms
     *
     * The blocks below set up the embedded forms for an agPerson.
     */
    /**
     * Language Embedding Section
     *
     * This block sets up the embedded forms using agEmbeddedAgPersonMjAgLanguageForm
     * and agEmbeddedAgPersonLanguageCompetencyForm.
     */


    /**
     * Name Embedding Section
     *
     * This block sets up the embedded agEmbeddedAgPersonNameForms, one for each agPersonNameType,
     * populated with the agPersonName that corresponds to the current agPerson and
     * agPersonNameType (if it exists).
     */


    /**
     * Email Embedding Section
     *
     * This block sets up the embedded agEmbeddedAgEmailContactForms, one for each
     * agEmailContactType, populated with the agPersonName that corresponds to the current
     * agPerson and agEmailContactType (if it exists).
     */
    $this->ag_email_contact_types = Doctrine::getTable('agEmailContactType')->createQuery('a')->execute();

    $emailContainer = new sfForm(array(), array());
    $emailContainer->widgetSchema->setFormFormatterName('list');
    foreach ($this->ag_email_contact_types as $emailContactType) {
      $emailContactForm = new agEmbeddedAgEmailContactForm();
      foreach ($this->getObject()->getAgEntity()->getAgEntityEmailContact() as $current) {
        if ($current->getEmailContactTypeId() == $emailContactType->getId()) {
          $emailContactForm->setDefault('email_contact', $current->getAgEmailContact()->email_contact);
        }
      }
      $emailContactForm->widgetSchema->setLabel('email_contact', false);
      $emailContainer->embedForm($emailContactType->getEmailContactType(), $emailContactForm);
    }

    $this->embedForm('email', $emailContainer);

    /**
     * Phone Embedding Section
     *
     * This block sets up the embedded agEmbeddedAgPhoneContactForms, one for each
     * agPhoneContactType, populated with the agPersonName that corresponds to the
     * current agPerson and agPhoneContactType (if it exists).
     * */
    $this->ag_phone_contact_types = Doctrine::getTable('agPhoneContactType')->createQuery('a')->execute();

    $phoneContainer = new sfForm(array(), array());
    $phoneContainer->widgetSchema->setFormFormatterName('list');
    foreach ($this->ag_phone_contact_types as $phoneContactType) {
      $phoneContactForm = new agEmbeddedAgPhoneContactForm();
      foreach ($this->getObject()->getAgEntity()->getAgEntityPhoneContact() as $current) {
        if ($current->getPhoneContactTypeId() == $phoneContactType->getId()) {
          $phoneContactForm->setDefault('phone_contact', preg_replace(
                  $current->getAgPhoneContact()->getAgPhoneFormat()->getAgPhoneFormatType()->match_pattern,
                  $current->getAgPhoneContact()->getAgPhoneFormat()->getAgPhoneFormatType()->replacement_pattern,
                  $current->getAgPhoneContact()->phone_contact));
        }
      }
      $phoneContactForm->widgetSchema->setLabel('phone_contact', false);
      $phoneContainer->embedForm($phoneContactType->getPhoneContactType(), $phoneContactForm);
    }

    $this->embedForm('phone', $phoneContainer);

    /**
     * Address Embedding Section
     *
     * This block sets up the embedded agEmbeddedAgAddressContactForms.
     * */
    $this->address_contact_types = Doctrine::getTable('agAddressContactType')->createQuery('a')->execute();
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
     */
    foreach ($this->address_formats as $af) {
      $addressElements[$af->line_sequence][$af->inline_sequence][$af->getAgAddressElement()->address_element] = $af->getAgAddressElement()->id;
    }

    $addressContainer = new sfForm(array(), array()); // Container form.
    $addressContainer->widgetSchema->setFormFormatterName('list');

    $stateList = Doctrine_Query::create()
            ->select('a.value')
            ->from('agAddressValue a')
            ->where('a.address_element_id = 4')
            ->execute();

    $this->entityAddress = Doctrine::getTable('agEntity')
            ->createQuery('entityAddresses')
            ->select('e.*, eac.*, act.*, a.*, as.*, aav.*, av.*, p.*, ae.*')
            ->from('agEntity e, e.agEntityAddressContact eac, eac.agAddressContactType act,
              eac.agAddress a, a.agAddressStandard as, a.agAddressMjAgAddressValue aav,
              aav.agAddressValue av, av.agAddressElement ae, e.agPerson p')
            ->where('e.id = ?', $this->agEntity->getId())
            ->execute()
            ->getFirst();


    foreach ($this->address_contact_types as $address_contact_type) {
      $addressSubContainer = new sfForm(array(), array()); // Sublevel container forms beneath address to hold a complete address for each address type.
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
                    ),
                    array('class' => 'inputGray'
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
        }
      }
      $addressContainer->embedForm($address_contact_type, $addressSubContainer); //Embed the addresses-by-type
    }
    $this->embedForm('address', $addressContainer); //Embed all the addresses into agPersonForm.
  }

  /**
   * Saves the forms embedded on the Person page.
   * @param $forms array of forms to save
   * @param $con the current doctrine connection instance
   * @return a call to the parent form's saveEmbeddedForms method
   * @todo break this method into smaller pieces
   * */
  public function saveEmbeddedForms($con = null, $forms = null)
  {
    if (null === $forms) {
      $forms = $this->embeddedForms;
    }
    if (is_array($forms)) {
      foreach ($forms as $key => $form) {
        /**
         * Date of Birth daving section
         *
         * @todo: The '0000-00-00' below is pretty hackish and should be fixed. It prevents a DB error for a field that should not be null
         * formValuesAreBlank() or something similar should really be used, in updateObject, to catch this earlier and unset.
         * */
        if ($form instanceof agEmbeddedPersonDateOfBirthForm) {
          if ($form->getObject()->person_id == null && $form->getObject()->date_of_birth <> '0000-00-00') {
            $form->getObject()->person_id = $this->getObject()->id;
            $form->getObject()->save();
          } elseif ($form->getObject()->date_of_birth == '0000-00-00' && $form->getObject()->person_id <> null) {
            $form->getObject()->delete();
          } elseif ($form->getObject()->date_of_birth <> '0000-00-00' && $form->getObject()->person_id <> null) {
            $form->getObject()->save();
          }
          unset($forms[$key]);
        }
        /**
         *  Name Saving Section
         * */
        if ($form instanceof agEmbeddedAgPersonNameForm) {
          //This query finds the name_type ID we need for the next query.
          $typeQuery = Doctrine::getTable('agPersonNameType')->createQuery('b')
                  ->select('b.id')
                  ->from('agPersonNameType b')
                  ->where('b.person_name_type = ?', $key);

          $typeId = $typeQuery->fetchOne()->id;

          //This query gets the person's agPersonMjAgPersonName object, based on person_id and name_type_id (as $typeId).
          $joinQuery = Doctrine::getTable('agPersonMjAgPersonName')->createQuery('c')
                  ->select('c.id')
                  ->from('agPersonMjAgPersonName c')
                  ->where('c.person_name_type_id = ?', $typeId)
                  ->andWhere('c.person_id =?', $this->getObject()->id);

          //Check if the agEmbeddedAgPersonNameForm has a name value.
          if ($form->getObject()->person_name <> null) {
            // Get an agPersonMjAgPersonName object from $joinQuery. Then create a new agPersonMjAgPersonNameForm
            // and put the retrieved object inside it.
            if ($join = $joinQuery->fetchOne()) {
              $joinForm = new agPersonMjAgPersonNameForm($join);
            }
            // Or create a new agPersonMjAgPersonNameForm to be populated later and set its priority to 1.
            else {
              $joinForm = new agPersonMjAgPersonNameForm();
              $joinForm->getObject()->priority = 1;
            }

            // Check if the person_name value has changed since the page was rendered.
            if ($form->getObject()->person_name <> $form->getDefault('person_name')) {
              // If it has changed, save the entered value as $nameLookup. Then revert the object
              // to its default values from the page render. This prevents a duplicate entry error.
              $nameLookUp = $form->getObject()->person_name;
              $form->updateObject($form->getDefaults());

              // Create a query to see if the submitted name value, as $nameLookup, already exists
              // in the database.
              $q = Doctrine::getTable('agPersonName')->createQuery('a')
                      ->select('a.id')
                      ->from('agPersonName a')
                      ->where('a.person_name = ?', $nameLookUp);

              // If it does...
              if ($queried = $q->fetchOne()) {
                // Get the id of the name in the db...
                $name_id = $queried->get('id');
                // ...then see if the agPersonMjAgPersonName has a person_name_id corresponding to the
                // name queried for. If it is, unset the form, no update is needed...
                if (isset($joinForm->person_name_id) && $joinForm->person_name_id == $name_id) {
                  unset($forms[$key]);
                }
                // ...If it wasn't, populate the agPersonMjAgPersonName object's values and save it.
                // Then unset the form.
                else {
                  $joinForm->getObject()->person_id = $this->getObject()->id;
                  $joinForm->getObject()->person_name_id = $name_id;
                  $joinForm->getObject()->person_name_type_id = $typeId;
                  $joinForm->getObject()->save();
                  unset($forms[$key]);
                }
              }
              // If the entered name isn't in the database already, make a new agPersonName object,
              // populate it with the new name, and save it.
              // Then populate the agPersonMjAgPersonName object's values, associating it with the
              // id of the new name, and save it. Unset the form.
              elseif (!$queried = $q->fetchOne()) {
                $newName = new agPersonName();
                $newName->person_name = $nameLookUp;
                $newName->save();
                $joinForm->getObject()->person_id = $this->getObject()->id;
                $joinForm->getObject()->person_name_id = $newName->id;
                $joinForm->getObject()->person_name_type_id = $typeId;
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
         *  Email Saving Section
         * */
        if ($form instanceof agEmbeddedAgEmailContactForm) {
          //This query finds the email_contact_type ID we need for the next query.
          $typeQuery = Doctrine::getTable('agEmailContactType')->createQuery('b')
                  ->select('b.id')
                  ->from('agEmailContactType b')
                  ->where('b.email_contact_type = ?', $key);

          $typeId = $typeQuery->fetchOne()->id;

          //This query gets the person's agEntityEmailContact object, based on person_id and name_type_id (as $typeId).
          $joinQuery = Doctrine::getTable('agEntityEmailContact')->createQuery('c')
                  ->select('c.id')
                  ->from('agEntityEmailContact c')
                  ->where('c.email_contact_type_id = ?', $typeId)
                  ->andWhere('c.entity_id =?', $this->getObject()->getAgEntity()->id);
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
                  $joinForm->getObject()->entity_id = $this->getObject()->getAgEntity()->id;
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
                $joinForm->getObject()->entity_id = $this->getObject()->getAgEntity()->id;
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
         *  Phone Saving Section
         * */
        if ($form instanceof agEmbeddedAgPhoneContactForm) {
          //This query finds the phone_contact_type ID we need for the next query.
          $typeQuery = Doctrine::getTable('agPhoneContactType')->createQuery('b')
                  ->select('b.id')
                  ->from('agPhoneContactType b')
                  ->where('b.phone_contact_type = ?', $key);

          $typeId = $typeQuery->fetchOne()->id;

          //This query gets the person's agEntityPhoneContact object, based on person_id and phone_contact_type_id (as $typeId).
          $joinQuery = Doctrine::getTable('agEntityPhoneContact')->createQuery('c')
                  ->select('c.id')
                  ->from('agEntityPhoneContact c')
                  ->where('c.phone_contact_type_id = ?', $typeId)
                  ->andWhere('c.entity_id = ?', $this->getObject()->getAgEntity()->id);
          //Check if the agEmbeddedAgPhoneContactForm has a phone_contact value.
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
                  $joinForm->getObject()->entity_id = $this->getObject()->getAgEntity()->id;
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
                $phoneFormats = Doctrine::getTable('agPhoneFormatType')->createQuery('a')->execute();
                //Match $phoneLookup against each match_pattern in agPhoneFormatType.
                foreach ($phoneFormats as $phoneFormat) {
                  if (preg_match($phoneFormat->match_pattern, $phoneLookUp)) {
                    $newPhone->phone_format_id = $phoneFormat->id;
                  }
                }

                /* @todo phone_format_id shouldn't be hardcoded -Ilya */
                $newPhone->save();
                $joinForm->getObject()->entity_id = $this->getObject()->getAgEntity()->id;
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
         *  Address Saving Section
         * */
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
                  ->andWhere('c.entity_id = ?', $this->getObject()->entity_id);
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
              $joinEntityAddressForm->getObject()->entity_id = $this->getObject()->getAgEntity()->id;
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
        /**
         * Language Saving Section
         * */
        if ($form instanceof agEmbeddedAgPersonMjAgLanguageForm) {
          $joinQuery = Doctrine::getTable('agPersonMjAgLanguage')->createQuery('d')
                  ->select('d.id')
                  ->from('agPersonMjAgLanguage d')
                  ->where('d.id =?', $form->getObject()->id);

          if ($form->getObject()->language_id <> null) {
            //Create a new agPersonMjAgLanguageForm. Populate it with an existing object, if it exists.
            //check if the langauge value has changed between page render and form submission. if it has, set the join object's lanquage to the new language.
            if ($form->getObject()->language_id <> $form->getDefault('language_id')) {
              // Have to get the object from the DB. Symfony errors out if we try to save the new one, won't try to update, just does an insert.
              if ($join = $joinQuery->fetchOne()) {
                $join->language_id = $form->getObject()->language_id;
                $join->save();
              } else {
                $form->getObject()->person_id = $this->getObject()->id;
                $form->getObject()->save();
              }
              $joinId = $form->getObject()->id;
              $form->updateObject($form->getDefaults());
              unset($forms[$key]);
            } else {
              //If it didn't change, just unset it and be done w/ it.
              unset($forms[$key]);
              $joinId = (isset($form->getObject()->id)) ? $form->getObject()->id : null; //Need this for the agEmbeddedAgPersonLanguageCompetencyForm section.
            }
          } else {
            //If the language form is blank, unset it.
            unset($forms[$key]);
            //Then see if it was made blank between render and sumbission.
            if ($form->getObject()->language_id <> $form->getDefault('language_id')) {
              //if it was, delete the person-language join and all the associated data.
              //$join = $joinQuery->fetchOne();
              if ($join = $joinQuery->fetchOne()) {
                $competencies = $join->getAgPersonLanguageCompetency();
                $competencies->delete();
                $join->delete();
              }
              $joinId = null;
            }
          }
        }
        if ($form instanceof agEmbeddedAgPersonLanguageCompetencyForm) {
          // Check if a competency is selected, and also if there is still an existing person-language join.
          if ($form->getObject()->language_competency_id <> null && $joinId <> null) {
            // Check if it's changed between render and submission.
            if ($form->getObject()->language_competency_id <> $form->getDefault('language_competency_id')) {
              if ($form->getObject()->person_language_id == null) {
                $form->getObject()->person_language_id = $joinId;
              }
              $form->getObject()->save();
            }
            unset($forms[$key]);
          } else //If there's no competency ID selected, or if the agPersonMjAgLanguage that uses the competency has been deleted.
          if ($form->getObject()->language_competency_id <> $form->getDefault('language_competency_id')) {//If it became blank between render and submit
            //  $form->getObject()->id = $form->getDefault('id');//Set the id and ...
            $form->updateObject($form->getDefaults());
            // ...delete the object.
            $form->getObject()->delete();
            unset($forms[$key]);
          }
          unset($forms[$key]);
        }
        if ($form instanceof agEmbeddedStaffForm) {
          unset($forms[$key]);
        }
        
      }
    }
    return parent::saveEmbeddedForms($con, $forms);
  }

  protected function doSave($con = null)
  {
//  if ($this->object->getAgPersonDateOfBirth()->date_of_birth == null) {
//    unset($this['date_of_birth']);
//  } else {
//
//  }
//
//    if(get_class($this) == 'agStaffPersonForm')
//    {
//        $embeddeds=$this->getEmbeddedForms();
//        $embeddedstaffform;
//        $staffValues = $this->getValue('staff');
//        $srt = $staffValues['ag_staff_resource_type_list'];
//        //save the staff person's organization
//        //save the staff person's status for that organization
//        $this->saveagStaffResourceTypeList($con);
//    }
    return parent::doSave($con);
  }

}
