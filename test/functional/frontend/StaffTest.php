<?php

require_once 'AgSeleniumTestCase.php';

/**
 * StaffTest
 *
 * PHP Version 5
 *
 * LICENSE: This source file is subject to LGPLv3.0 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 *
 * @author Usman Akeju, CUNY SPS
 *
 * @todo complete this test
 *
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */
class StaffTest extends AgSeleniumTestCase
{

  /**
   * Fills out specified address with the provided data.
   */
  public function fillStaffName($given, $middle, $family, $maiden, $alias)
  {
    if (!is_null($given)) {
      $this->type('ag_person_name_given_person_name', $given);
    }
    if (!is_null($middle)) {
      $this->type('ag_person_name_middle_person_name', $middle);
    }
    if (!is_null($family)) {
      $this->type('ag_person_name_family_person_name', $family);
    }
    if (!is_null($maiden)) {
      $this->type('ag_person_name_maiden_person_name', $maiden);
    }
    if (!is_null($alias)) {
      $this->type('ag_person_name_alias_person_name', $alias);
    }

    return $this;
  }

  /**
   * Fills out specified address with the provided data.
   */
  public function fillStaffAddress($type, $line1, $line2, $city, $state, $zip, $zipPlus4, $borough,
                                   $country)
  {
    if (!is_null($line1)) {
      $this->type('ag_person_address_' . $type . '_line_1_value', $line1);
    }
    if (!is_null($line2)) {
      $this->type('ag_person_address_' . $type . '_line_2_value', $line2);
    }
    if (!is_null($city)) {
      $this->type('ag_person_address_' . $type . '_city_value', $city);
    }
    if (!is_null($state)) {
      $this->select('ag_person_address_' . $type . '_state_value', 'label=' . $state);
    }
    if (!is_null($zip)) {
      $this->type('ag_person_address_' . $type . '_zip5_value', $zip);
    }
    if (!is_null($zipPlus4)) {
      $this->type('ag_person_address_' . $type . '_zip_4_value', $zipPlus4);
    }
    if (!is_null($borough)) {
      $this->type('ag_person_address_' . $type . '_borough_value', $borough);
    }
    if (!is_null($country)) {
      $this->type('ag_person_address_' . $type . '_country_value', $country);
    }

    return $this;
  }

  /**
   * Fills out specified language with the provided data.
   */
  public function fillStaffLanguage($ordinal, $language, $read, $write, $speak)
  {
    if (!is_null($language)) {
      $this->select(
          'ag_person_languages_language_' . $ordinal . '_language_language_id',
          'label=' . $language
          );
    }
    if (!is_null($read)) {
      $this->select(
          'ag_person_languages_language_' . $ordinal . '_read_language_competency_id',
          'label=' . $read
          );
    }
    if (!is_null($write)) {
      $this->select(
          'ag_person_languages_language_' . $ordinal . '_write_language_competency_id',
          'label=' . $write
          );
    }
    if (!is_null($speak)) {
      $this->select(
          'ag_person_languages_language_' . $ordinal . '_speak_language_competency_id',
          'label=' . $speak
          );
    }

    return $this;
  }

  /**
   * Fills out and submits the staff creation/edit form with the provided data.
   *
   * @todo make each form-filling action conditional based on input (i.e., null? -> do nothing)
   */
  public function fillStaffForm(
  $givenName, $middleName, $familyName, $maidenName, $aliasName, $sex, $maritalStatus, $ethnicity,
  $birthMonth, $birthMonth, $birthYear, $language1, $language1Read, $language1Write,
  $language1Speak, $language2, $language2Read, $language2Write, $language2Speak, $language3,
  $language3Read, $language3Write, $language3Speak, $nationality, $religion, $workEmail,
  $personalEmail, $otherEmail, $workPhone, $homePhone, $mobilePhone, $otherPhone, $homeAddressLine1,
  $homeAddressLine2, $homeCity, $homeState, $homeZip, $homeZipPlus4, $homeBorough, $homeCountry,
  $workAddressLine1, $workAddressLine2, $workCity, $workState, $workZip, $workZipPlus4,
  $workBorough, $workCountry, $vacationAddressLine1, $vacationAddressLine2, $vacationCity,
  $vacationState, $vacationZip, $vacationZipPlus4, $vacationBorough, $vacationCountry,
  $otherAddressLine1, $otherAddressLine2, $otherCity, $otherState, $otherZip, $otherZipPlus4,
  $otherBorough, $otherCountry)
  {
    $this
        ->fillStaffName($givenName, $middleName, $familyName, $maidenName, $aliasName)
        ->select('ag_person_ag_sex_list', 'label=' . $sex)
        ->select('ag_person_ag_marital_status_list', 'label=' . $maritalStatus)
        ->select('ag_person_ag_ethnicity_list', 'label=' . $ethnicity)
        ->click('ag_person_date_of_birth_date_of_birth')
        ->type(
            'ag_person_date_of_birth_date_of_birth',
            $birthMonth . '/' . $birthDay . '/' . $birthYear
            )
//        ->select('ag_person_date_of_birth_date_of_birth_month', 'label=' . $birthMonth)
//        ->select('ag_person_date_of_birth_date_of_birth_day', 'label=' . $birthDay)
//        ->select('ag_person_date_of_birth_date_of_birth_year', 'label=' . $birthYear)
        ->fillStaffLanguage(1, $language1, $language1Read, $language1Write, $language1Speak)
        ->fillStaffLanguage(2, $language2, $language2Read, $language2Write, $language2Speak)
        ->fillStaffLanguage(3, $language3, $language3Read, $language3Write, $language3Speak)
        ->addSelection('ag_person_ag_nationality_list', 'label=' . $nationality)
        ->addSelection('ag_person_ag_religion_list', 'label=' . $religion)
        ->type('ag_person_email_work_email_contact', $workEmail)
        ->type('ag_person_email_personal_email_contact', $personalEmail)
        ->type('ag_person_email_other_email_contact', $otherEmail)
        ->type('ag_person_phone_work_phone_contact', $workPhone)
        ->type('ag_person_phone_home_phone_contact', $homePhone)
        ->type('ag_person_phone_mobile_phone_contact', $mobilePhone)
        ->type('ag_person_phone_other_phone_contact', $otherPhone)
        ->fillStaffAddress(
            'home', $homeAddressLine1, $homeAddressLine2, $homeCity, $homeState,
            $homeZip, $homeZipPlus4, $homeBorough, $homeCountry
            )
        ->fillStaffAddress(
            'work', $workAddressLine1, $workAddressLine2, $workCity, $workState,
            $workZip, $workZipPlus4, $workBorough, $workCountry
            )
        ->fillStaffAddress(
            'vacation', $vacationAddressLine1, $vacationAddressLine2, $vacationCity,
            $vacationState, $vacationZip, $vacationZipPlus4, $vacationBorough, $vacationCountry
            )
        ->fillStaffAddress(
            'other', $otherAddressLine1, $otherAddressLine2, $otherCity, $otherState,
            $otherZip, $otherZipPlus4, $otherBorough, $otherCountry
            )
        ->click("//input[@value='Save']");

    return $this;
  }

  /**
   * @todo refactor this to use fillStaffForm?
   */
  public function testStaffBasicCrud($given = 'John', $middle = 'Jacob',
                                     $family = 'Jingleheimer-Schmidt', $alias = 'Jack',
                                     $sex = 'other')
  {
    $family .= $this->testId;

    $workAddressLine1 = '123 Abc';
    $workAddressLine2 = 'Bcd 4';
    $workCity = 'New Jack';
    $workState = 'AK';
    $workZip = '02220';
    $workZipPlus4 = '0001';
    $workBorough = 'Jokers';
    $workCountry = 'ABC';

    $this
        // opens the main URI
        ->open()
        // tries to login with correct credentials
        ->doLogin()
        ->navigateToStaff()
        // creates
        ->clickAndWait('link=Create Staff')
        /** @todo replace this block with fillStaffForm */
        ->fillStaffName($given, $middle, $family, null, $alias)
        ->select('ag_person_ag_sex_list', 'label=' . $sex)
        ->select('ag_person_ag_marital_status_list', 'label=unmarried partner')
        ->select('ag_person_ag_ethnicity_list', 'label=Caucasian (European)')
        ->type('ag_person_date_of_birth_date_of_birth', '02/25/2005')
//        ->select('ag_person_date_of_birth_date_of_birth_month', 'label=02')
//        ->select('ag_person_date_of_birth_date_of_birth_day', 'label=25')
//        ->select('ag_person_date_of_birth_date_of_birth_year', 'label=2005')
        ->fillStaffLanguage(1, 'Bulgarian', 'intermediate', 'basic', 'fluent')
        ->addSelection('ag_person_ag_nationality_list', 'label=American Samoan')
        ->fillStaffAddress(
            'work', $workAddressLine1, $workAddressLine2, $workCity, $workState,
            $workZip, $workZipPlus4, $workBorough, $workCountry
            )
        ->clickAndWait("//input[@value='Save']")
        ->assertElementPresent('link=Delete')
        // reads
        ->clickAndWait('link=List')
        ->assertTextPresent($family)
        ->clickAndWait('xpath=//td[text()="' . $family . '"]/../td[1]/a')
        ->assertTextPresent($family)
        // updates
        ->clickAndWait('link=Edit')
        ->fillStaffName($middle, '', 'Schmidt-J' . $this->testId, null, 'Jackie')
        ->select('ag_person_ag_sex_list', 'label=post-op transwoman')
        ->select('ag_person_ag_marital_status_list', 'label=separated')
        ->select('ag_person_ag_ethnicity_list', 'label=Arctic (Siberian, Eskimo)')
        ->type('ag_person_date_of_birth_date_of_birth', '02/25/2006')
//        ->select('ag_person_date_of_birth_date_of_birth_year', 'label=2006')
        ->addSelection('ag_person_ag_nationality_list', 'label=Algerian')
        ->removeSelection('ag_person_ag_nationality_list', 'label=American Samoan')
        ->type('ag_person_email_work_email_contact', 'b@m.an')
        ->type('ag_person_phone_work_phone_contact', '1231231234')
        ->clickAndWait("//input[@value='Save']")
        ->clickAndWait('link=List')
        ->assertTextPresent('Schmidt-J' . $this->testId)
        ->clickAndWait('xpath=//td[text()="' . 'Schmidt-J' . $this->testId . '"]/../td[1]/a')
        ->assertTextPresent('Schmidt-J' . $this->testId)
        ->assertTextPresent('(123) 123-1234')
        ->assertTextPresent('b@m.an')
        ->assertTextPresent($workCity . ', ' . $workState . ' ' . $workZip . '-' . $workZipPlus4)
        ->clickAndWait('link=Edit')
        ->type('ag_person_email_work_email_contact', 'b@man')
        ->clickAndWait("//input[@value='Save']")
        ->assertTextPresent('Invalid.')
        // deletes
        ->clickAndWait('link=Delete')
        ->assertTrue((bool) preg_match("/^Are you sure[\s\S]$/", $this->getConfirmation()));
    $this
        // TODO: fill in the rest
        ->doLogout();
  }

  public function testStaffFormValidation()
  {
    $this->markTestIncomplete();

//    $this
//        // opens the main URI
//        ->open()
//        // tries to login with correct credentials
//        ->doLogin()
//        ->navigateToStaff()
//        // TODO: fill in the rest
//        ->doLogout();
  }

}
