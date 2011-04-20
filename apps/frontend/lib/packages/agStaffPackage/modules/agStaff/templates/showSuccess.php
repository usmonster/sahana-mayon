<?php
use_helper('agTemplate');
($sf_request->getGetParameter('filter')) ?
        $filterAppend = '&filter=' . $sf_request->getGetParameter('filter') : $filterAppend = '';
($sf_request->getGetParameter('sort')) ?
        $sortAppend = '&sort=' . $sf_request->getGetParameter('sort') : $sortAppend = '';
($sf_request->getGetParameter('order')) ?
        $orderAppend = '&order=' . $sf_request->getGetParameter('order') : $orderAppend = '';
?>
<table class="singleTable">
  <thead>
  <caption>Primary</caption>
</thead>
<tbody>
  <tr>
    <th class="headLeft" rowspan="2">Name:</th>
  <?php
  foreach ($ag_person_name_types as $agPersonNameType) {
    echo '<th class="subHead">' . ucwords($agPersonNameType) . '</th>';
  }
  ?>
</tr>
<tr>
  <?php
  foreach ($ag_person_name_types as $agPersonNameType) {
    echo "<td>";
    $names = $agStaff->getAgPerson()->getAgPersonMjAgPersonName();
    foreach ($names as $name) {
      if ($name->getPersonNameTypeId() == $agPersonNameType->getId()) {
        echo $name->getAgPersonName();
      }
    }

    echo "</td>";
  }
  ?>
</tr>
<tr>
  <th class="headLeft">Sex:</th>
  <td colspan="<?php echo count($ag_person_name_types); ?>">
    <?php
    $sexes = $agStaff->getAgPerson()->getAgSex();
    foreach ($sexes as $sex) {
      echo ucwords($sex);
    }
    ?>
  </td>
</tr>
<tr>
  <th class="headLeft">Date of Birth:</th>
  <td colspan="<?php echo count($ag_person_name_types) - 2; ?>">
    <?php
    echo $agStaff->getAgPerson()->getAgPersonDateOfBirth()->getDateOfBirth();
    ?>
  </td>
  <th class="headMid">Age:</th>
  <td>
    <?php
    #This will need to be adjusted, don't think it's accurate for all dates, but wanted to get something in here.
    if ($agStaff->getAgPerson()->getAgPersonDateOfBirth()->getDateOfBirth() == !null) {
      echo floor(
          (time() - strtotime($agStaff->getAgPerson()->getAgPersonDateOfBirth()->getDateOfBirth()))
          / 31556926
      );
    }
    ?>
  </td>
</tr>
<tr>
  <th class="headLeft">Profession:</th>
  <td colspan="<?php echo count($ag_person_name_types); ?>">
    <?php
    $professions = $agStaff->getAgPerson()->getAgProfession();
    foreach ($professions as $profession) {
      echo $profession . "<br /> ";
    }
    ?>
  </td>
</tr>
<tr>
  <th class="headLeft">Nationality:</th>
  <td colspan="<?php echo count($ag_person_name_types); ?>">
    <?php
    $nationalities = $agStaff->getAgPerson()->getAgNationality();
    foreach ($nationalities as $nationality) {
      if ($nationality->getAppDisplay() == 1) {
        echo $nationality . "<br /> ";
      }
    }
    ?>
  </td>
</tr>
<tr>
  <th class="headLeft">Ethnicity:</th>
  <td colspan="<?php echo count($ag_person_name_types); ?>">
    <?php
    $ethnicities = $agStaff->getAgPerson()->getAgEthnicity();
    foreach ($ethnicities as $ethnicity) {
      echo $ethnicity . "<br /> ";
    }
    ?>
  </td>
</tr>
<tr>
  <th class="headLeft">Religion:</th>
  <td colspan="<?php echo count($ag_person_name_types); ?>">
    <?php
    $religions = $agStaff->getAgPerson()->getAgReligion();
    foreach ($religions as $religion) {
      echo $religion . "<br /> ";
    }
    ?>
  </td>
</tr>
<tr>
  <th class="headLeft">Marital Status:</th>
  <td colspan="<?php echo count($ag_person_name_types); ?>">
    <?php
    $maritalStatuses = $agStaff->getAgPerson()->getAgMaritalStatus();
    foreach ($maritalStatuses as $maritalStatus) {
      echo ucwords($maritalStatus->getMaritalStatus());
    }
    ?>
  </td>
</tr>
</tbody>
</table>

<br />

<table class="singleTable">
<thead>
<caption>Contact</caption>
</thead>
<tbody>
<tr>
  <th class="headLeft" rowspan="2">Phone:</th>
  <?php
    foreach ($ag_phone_contact_types as $agPhoneContactType) {
      echo '<th class="subHead">' . ucwords($agPhoneContactType->getPhoneContactType()) . '</th>';
    }
  ?>
  </tr>
  <tr>
  <?php
    foreach ($ag_phone_contact_types as $agPhoneContactType) {
      echo "<td>";
      $check = 0;
      //$phoneContacts = $agStaff->getAgPerson()->getAgPersonMjAgPhoneContact();
      $phoneContacts = $agStaff->getAgPerson()->getAgEntity()->getAgEntityPhoneContact();
      foreach ($phoneContacts as $phoneContact) {
        if ($phoneContact->getPhoneContactTypeId() == $agPhoneContactType->getId()) {
          echo preg_replace(
                  $phoneContact
                  ->getAgPhoneContact()
                  ->getAgPhoneFormat()
                  ->getAgPhoneFormatType()->match_pattern,
                  $phoneContact
                  ->getAgPhoneContact()
                  ->getAgPhoneFormat()
                  ->getAgPhoneFormatType()->replacement_pattern,
                  $phoneContact
                  ->getAgPhoneContact()->phone_contact
          );
          echo '<br />';
          $check = 1;
        }
      }
      if ($check == 0) {
        echo "----";
      }
      echo "</td>";
    }
  ?>
  </tr>
</tbody>
</table>

<br />

<table class="singleTable">
  <tbody>
    <tr>
      <th class="headLeft" rowspan="2">Email:</th>
    <?php
    foreach ($ag_email_contact_types as $agEmailContactType) {
      echo '<th class="subHead">' . ucwords($agEmailContactType->getEmailContactType()) . '</th>';
    }
    ?>
  </tr>
  <tr>
    <?php
    foreach ($ag_email_contact_types as $agEmailContactType) {
      echo "<td>";
      $check = 0;
      $emailContacts = $agStaff->getAgPerson()->getAgEntity()->getAgEntityEmailContact();
      foreach ($emailContacts as $emailContact) {
        if ($emailContact->getEmailContactTypeId() == $agEmailContactType->getId()) {
          echo '<a href="mailto:' . $emailContact->getAgEmailContact() . '" class="linkText">'
          . $emailContact->getAgEmailContact() . '</a>' . '<br />';
          $check = 1;
        }
      }
      if ($check == 0) {
        echo "----";
      }
      echo "</td>";
    }
    ?>
  </tr>
<tbody>
</table>

<br />

<table class="singleTable">
<thead>
<caption>Language</caption>
</thead>
<tbody>
<tr>
  <th class="headLeft">Language:</th>
  <?php
    foreach ($ag_language_formats as $agLanguageFormat) {
      echo '<th class="subHead">' . ucwords($agLanguageFormat->getLanguageFormat()) . '</th>';
    }
  ?>
  </tr>
<?php
    $personLanguages = $agStaff->getAgPerson()->getAgPersonMjAgLanguage();
    foreach ($personLanguages as $personLanguage) {
      echo '<tr>';
      echo '<th class="subHeadLeft">' . $personLanguage->agLanguage . '</th>';

      foreach ($ag_language_formats as $agLanguageFormat) {
        echo '<td>';
        $check = 0;
        $competencies = $personLanguage->getAgPersonLanguageCompetency();
        foreach ($competencies as $competency) {
          if ($competency->getLanguageFormatId() == $agLanguageFormat->id) {
            echo ucwords($competency->getAgLanguageCompetency()->language_competency);
            $check = 1;
          }
        }
        if ($check == 0) {
          echo "----";
        }
        echo '</td>';
      }

      echo '</tr>';
    }
?>
  </tbody>
  </table>

  <br />

  <table class="singleTable">
    <thead>
    <caption>Address</caption>
  </thead>
  <tbody>
    <?php
      if(isset($addressArray)) {
        $addressTable = buildAddressTable($addressArray->getRawValue());
        echo $addressTable;
      } else {
        echo '<tr><td class="subHead">This staff person does not have any associated address information</td></tr>';
      }
    ?>
  </tbody>
  </table>

  <br />

  <table class="singleTable">
    <thead>
      <caption>Administrative</caption>
    </thead>
    <tbody>
            <?php 
            foreach ($agStaff->getAgStaffResource() as $staffRec) {
            ?>
      <tr>
        <th class="headLeft">Staff ID:</th>
        <td>
          <?php
            foreach($agStaff->getAgPerson()->getAgStaff() as $staff) {
              echo $staff->id;
            }
          ?>
        </td>
      </tr>
      <tr>
        <th class="headLeft">Staff Affiliation:</th>
        <td colspan="4">
          <?php
           echo $staffRec->getAgOrganization()->organization;
          ?>
        </td>
      </tr>
      <tr>
        <th class="headLeft">Staff Type:</th>
        <td colspan="4">
          <?php
                echo $staffRec->getAgStaffResourceType()->staff_resource_type;
          ?>
        </td>
      </tr>
      <tr>
<th class="headMid">Staff Status:</th>
        <td colspan="4">
          <?php
                echo $staffRec->getAgStaffResourceStatus()->staff_resource_status;
          //echo ucwords($staff->getAgStaffResource->getAgStaffResourcesStatus());
          ?>
        </td>
      </tr>
         <?php
             }
           ?>
      <tr>
        <th class="headLeft">Created:</th>
        <td colspan="4"><?php echo $agStaff->getCreatedAt() ?></td>
      </tr>
      <tr>
        <th class="headLeft">Updated:</th>
        <td colspan="4"><?php echo $agStaff->getUpdatedAt() ?></td>
      </tr>


    </tbody>
  </table>
  <br/>
  <?php
    $customCheck =
      Doctrine::getTable('agPersonCustomFieldValue')
      ->findByDql(
          'person_id = ?', $agStaff->getAgPerson()->id
      )
      ->getFirst();
    if ($customCheck instanceof agPersonCustomFieldValue) {
      echo '<table class="singleTable">' . PHP_EOL;
      echo '<thead>' . PHP_EOL;
      echo '<caption>Import Data</caption>' . PHP_EOL;
      echo '</thead>' . PHP_EOL;
      echo '<tbody>' . PHP_EOL;
      foreach ($agStaff->getAgPerson()->getAgPersonCustomFieldValue() as $cFieldVal) {
        if ($cFieldVal->getAgPersonCustomField()->person_custom_field == $cFieldVal->getAgPersonCustomField()->getAgCustomFieldType()->custom_field_type) {
          echo '<tr>' . PHP_EOL;;
          echo '<th class="headLeft">' . $cFieldVal->getAgPersonCustomField()->person_custom_field . ':</th>' . PHP_EOL;;
          echo '<td>' . $cFieldVal->value . '</td>' . PHP_EOL;;
          echo '</tr>' . PHP_EOL;;
        } else {
          echo '<tr>' . PHP_EOL;;
          echo '<th class="headLeft">' . $cFieldVal->getAgPersonCustomField()->getAgCustomFieldType()->custom_field_type . "&ndash;" . $cFieldVal->getAgPersonCustomField()->person_custom_field . ':</th>' . PHP_EOL;;
          echo '<td>' . $cFieldVal->value . '</td>' . PHP_EOL;;
          echo '</tr>' . PHP_EOL;;
        }
      }
      echo '</tbody>' . PHP_EOL;
      echo '</table>' . PHP_EOL;
      echo '<br />' . PHP_EOL;
    }
    $awesome = $agStaff->getAgPerson()->getAgStaff();

    foreach($agStaff->getAgPerson()->getAgStaff() as $staff) {
      $staff_id = $staff->getId();
    }
    ?>

  <div class="floatLeft">
    <a href="<?php echo url_for('staff/edit?id=' . $staff_id) ?>"
       class="linkButton">Edit</a>
   <?php

   echo link_to(
        'Delete',
        'agStaff/delete?id=' . $staff_id,
        array('method' => 'delete', 'confirm' => 'Are you sure?', 'class' => 'linkButton')
    ); ?>
   <?php
    echo (isset($query)) ?
        '<a href="' . url_for('staff/search') . '/page/' . $pager->getFirstPage() .
        '?query=' . $query . '" class="linkButton">List</a>' :
        '<a href="' . url_for('staff/list') . '" class="linkButton">List</a>';
   ?>
</div>
<div class="floatRight">
<?php
    if (isset($query)) { //check to see if $query exists. True if page has been requested from search results. Links will then have $query appended.
      //If this isn't the FIRST page, echo a link to the FIRST page. Else, create an <a> with no href.
      echo(
      !$pager->isFirstPage() ? '<a href="' . url_for('staff/show') .
          '/page/' . $pager->getFirstPage() . '?query=' . $query .
          '" class="buttonText" title="First Staff Member">&lt;&lt;</a>' :
          '<a class="buttonTextOff">&lt;&lt;</a>'
      );
      //If this isn't the FIRST page, echo a link to the PREVIOUS page. Else, create an <a> with no href.
      echo(
      !$pager->isFirstPage() ? '<a href="' . url_for('staff/show') .
          '/page/' . $pager->getPreviousPage() . '?query=' . $query .
          '" class="buttonText" title="Previous Staff Member">&lt;</a>' :
          '<a class="buttonTextOff">&lt;</a>'
      );
      //if this isn't the LAST page, echo a link to the NEXT page. Else, create an <a> with no href.
      echo(
      !$pager->isLastPage() ? '<a href="' . url_for('staff/show') .
          '/page/' . $pager->getNextPage() . '?query=' . $query .
          '" class="buttonText" title="Next Staff Member">&gt;</a>' :
          '<a class="buttonTextOff">&gt;</a>'
      );
      //if this isn't the LAST page, echo a link to the LAST page. Else, create an <a> with no href.
      echo(
      !$pager->isLastPage() ? '<a href="' . url_for('staff/show') .
          '/page/' . $pager->getLastPage() . '?query=' . $query .
          '" class="buttonText" title="Last Staff Member">&gt;&gt;</a>' :
          '<a class="buttonTextOff">&gt;&gt;</a>'
      );
    } else { //Normal display, accesses all staff members if page has been requested from indexSuccess
      //if this isn't the FIRST page, echo a link to the FIRST page. Else, create an <a> with no href.
      echo(
      !$pager->isFirstPage() ? '<a href="' . url_for('staff/show') .
          '?page=' . $pager->getFirstPage() . $sortAppend . $orderAppend .
          '" class="buttonText" title="First Staff Member">&lt;&lt;</a>' :
          '<a class="buttonTextOff">&lt;&lt;</a>');
      //if this isn't the FIRST page, echo a link to the PREVIOUS page. Else, create an <a> with no href.
      echo(
      !$pager->isFirstPage() ? '<a href="' . url_for('staff/show') .
          '?page=' . $pager->getPreviousPage() . $sortAppend . $orderAppend .
          '" class="buttonText" title="Previous Staff Member">&lt;</a>' :
          '<a class="buttonTextOff">&lt;</a>'
      );
      //if this isn't the LAST page, echo a link to the NEXT page. Else, create an <a> with no href.
      echo(
      !$pager->isLastPage() ? '<a href="' . url_for('staff/show') .
          '?page=' . $pager->getNextPage() . $sortAppend . $orderAppend .
          '" class="buttonText" title="Next Staff Member">&gt;</a>' :
          '<a class="buttonTextOff">&gt;</a>'
      );
      //if this isn't the LAST page, echo a link to the LAST page. Else, create an <a> with no href.
      echo(
      !$pager->isLastPage() ? '<a href="' . url_for('staff/show') .
          '?page=' . $pager->getLastPage() . $sortAppend . $orderAppend .
          '" class="buttonText" title="Last Staff Member">&gt;&gt;</a>' :
          '<a class="buttonTextOff">&gt;&gt;</a>'
      );
    }
?>
</div>