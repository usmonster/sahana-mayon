<?php
  $agPersonNameTypesResultSet = Doctrine::getTable('agPersonNameType')
    ->createQuery('b')
    ->execute();
  foreach ($agPersonNameTypesResultSet as $nameType) {
    $agPersonNameTypes[$nameType->getId()] = $nameType->getPersonNameType();
  }

  $sortColumn = $sf_request->getGetParameter('sort');
  $sortOrder = $sf_request->getGetParameter('order');
  ($sf_request->getGetParameter('filter')) ? $filterAppend = '&filter=' . $sf_request->getGetParameter('filter') : $filterAppend = '';
  ($sf_request->getGetParameter('sort')) ? $sortAppend = '&sort=' . $sf_request->getGetParameter('sort') : $sortAppend = '';
  ($sf_request->getGetParameter('order')) ? $orderAppend = '&order=' . $sf_request->getGetParameter('order') : $orderAppend = '';

  $ag_person_name_types = $agPersonNameTypesResultSet;
?>
<table class="staffTable">
  <caption>Staff Members
    <?php
      // Output the current staff members being shown, as well total number in the list.
      echo $pager->getFirstIndice() . "-" . $pager->getLastIndice() . " of " . $pager->count();
    ?>
  </caption>
<thead>
    <tr style="border: none">
      <th class="head" rowspan="2"></th>
      <th class="head" colspan="<?php echo count($ag_person_name_types); ?>" style="text-align: center">
      Name
      </th>
      <!-- Agency -->
      <th class="head" rowspan="2">
        <div class="tableHeaderContent">Agency</div>
        <?php
          echo($sortColumn =='agency' && $sortOrder == 'ASC' ? '<a href="' . url_for('staff/list') . '?sort=agency&order=ASC" class="buttonSortSelected" title="ascending">&#x25B2;</a>' : '<a href="' . url_for('staff/list') . '?sort=agency&order=ASC" class="buttonSort" title="ascending">&#x25B2;</a>');
          echo($sortColumn =='agency' && $sortOrder == 'DESC' ? '<a href="' . url_for('staff/list') . '?sort=agency&order=DESC" class="buttonSortSelected" title="descending">&#x25BC;</a>' : '<a href="' . url_for('staff/list') . '?sort=agency&order=DESC" class="buttonSort" title="descending">&#x25BC;</a>');
        ?>
      </th>
      <!-- classification -->
      <th class="head" rowspan="2">
        <div class="tableHeaderContent">Classification</div>
        <?php
          echo($sortColumn =='staff_type' && $sortOrder == 'ASC' ? '<a href="' . url_for('staff/list') . '?sort=staff_type&order=ASC" class="buttonSortSelected" title="ascending">&#x25B2;</a>' : '<a href="' . url_for('staff/list') . '?sort=staff_type&order=ASC" class="buttonSort" title="ascending">&#x25B2;</a>');
          echo($sortColumn =='staff_type' && $sortOrder == 'DESC' ? '<a href="' . url_for('staff/list') . '?sort=staff_type&order=DESC" class="buttonSortSelected" title="descending">&#x25BC;</a>' : '<a href="' . url_for('staff/list') . '?sort=staff_type&order=DESC" class="buttonSort" title="descending">&#x25BC;</a>');
        ?>
      </th>
      <!-- work_contact -->
      <th class="head" rowspan="2">
        <div class="tableHeaderContent">Work Contact</div>
        <?php
          echo($sortColumn =='work_email' && $sortOrder == 'ASC' ? '<a href="' . url_for('staff/list') . '?sort=work_email&order=ASC" class="buttonSortSelected" title="ascending">&#x25B2;</a>' : '<a href="' . url_for('staff/list') . '?sort=work_email&order=ASC" class="buttonSort" title="ascending">&#x25B2;</a>');
          echo($sortColumn =='work_email' && $sortOrder == 'DESC' ? '<a href="' . url_for('staff/list') . '?sort=work_email&order=DESC" class="buttonSortSelected" title="descending">&#x25BC;</a>' : '<a href="' . url_for('staff/list') . '?sort=work_email&order=DESC" class="buttonSort" title="descending">&#x25BC;</a>');
        ?>
      </th>
      <!-- home_contact -->
      <th class="head" rowspan="2">
        <div class="tableHeaderContent">Home Contact</div>
        <?php
          echo($sortColumn =='home_email' && $sortOrder == 'ASC' ? '<a href="' . url_for('staff/list') . '?sort=home_email&order=ASC" class="buttonSortSelected" title="ascending">&#x25B2;</a>' : '<a href="' . url_for('staff/list') . '?sort=home_email&order=ASC" class="buttonSort" title="ascending">&#x25B2;</a>');
          echo($sortColumn =='home_email' && $sortOrder == 'DESC' ? '<a href="' . url_for('staff/list') . '?sort=home_email&order=DESC" class="buttonSortSelected" title="descending">&#x25BC;</a>' : '<a href="' . url_for('staff/list') . '?sort=home_email&order=DESC" class="buttonSort" title="descending">&#x25BC;</a>');
        ?>
      </th>
      <!-- staff status -->
      <th class="head" rowspan="2">
        <div class="tableHeaderContent">Staff Status</div>
        <?php
          echo($sortColumn =='staff_status' && $sortOrder == 'ASC' ? '<a href="' . url_for('staff/list') . '?sort=staff_status&order=ASC" class="buttonSortSelected" title="ascending">&#x25B2;</a>' : '<a href="' . url_for('staff/list') . '?sort=staff_status&order=ASC" class="buttonSort" title="ascending">&#x25B2;</a>');
          echo($sortColumn =='staff_status' && $sortOrder == 'DESC' ? '<a href="' . url_for('staff/list') . '?sort=staff_status&order=DESC" class="buttonSortSelected" title="descending">&#x25BC;</a>' : '<a href="' . url_for('staff/list') . '?sort=staff_status&order=DESC" class="buttonSort" title="descending">&#x25BC;</a>');
        ?>
      </th>
    </tr>
   <tr style="border: none;">
     <?php foreach ($ag_person_name_types as $ag_person_name_type_id => $ag_person_name_type): ?>
       <th class="subHead" rowspan="2">
         <div style="margin: 0; padding: 0"><?php echo ucwords($ag_person_name_type); ?></div>
         <?php
           echo($sortColumn =='person_name'. '_' . $ag_person_name_type_id && $sortOrder == 'ASC' ? '<a href="' . url_for('staff/list') . '?sort=person_name'. '_' . $ag_person_name_type_id . '&order=ASC" class="buttonSortSelected" title="ascending">&#x25B2;</a>' : '<a href="' . url_for('staff/list') . '?sort=person_name'. '_' . $ag_person_name_type_id . '&order=ASC" class="buttonSort" title="ascending">&#x25B2;</a>');
           echo($sortColumn =='person_name'. '_' . $ag_person_name_type_id && $sortOrder == 'DESC' ? '<a href="' . url_for('staff/list') . '?sort=person_name'. '_' . $ag_person_name_type_id . '&order=DESC" class="buttonSortSelected" title="descending">&#x25BC;</a>' : '<a href="' . url_for('staff/list') . '?sort=person_name'. '_' . $ag_person_name_type_id . '&order=DESC" class="buttonSort" title="descending">&#x25BC;</a>');
         ?>
       </th>
    <?php endforeach; ?>
    </tr>
  </thead>
  <tbody>
    <?php $i = $pager->getFirstIndice(); ?>
    <?php foreach ($pager->getResults() as $staffMember): ?>
    <tr>
      <td>
        <a href="<?php echo url_for('staff/show') . '?page=' . $staffMember->getId() . $sortAppend . $orderAppend; ?>" title="View Staff Member <?php echo $staffMember->getId(); ?>" class="linkButton"><?php echo $i++; ?></a>
      </td>
      <?php
        $names = $staffMember->getAgPerson()->getAgPersonMjAgPersonName();
        foreach ($agPersonNameTypes as $agPersonNameTypeId => $agPersonNameType)
        {
          echo "<td>";

          foreach ($names as $name)
          {
            if ($agPersonNameTypeId == $name->getPersonNameTypeId())
            {
              echo $name->getAgPersonName();
            }
          }
          echo "</td>";
        }
      ?>
      <td>
        <?php
          foreach ($staffMember->getAgStaffResource() as $staffRec) {
            foreach ($staffRec->getAgStaffResourceOrganization() as $staffRecOrg){
              echo $staffRecOrg->getAgOrganization()->organization;
            }
          }
        ?>
      </td>
      <td>
        <?php
          foreach ($staffMember->getAgStaffResourceType() as $rType) {
            echo $rType->staff_resource_type;
          }
        ?>
      </td>
      <td>
    <?php
      foreach ($ag_phone_contact_types as $agPhoneContactType) {
        $check = 0;
        //$phoneContacts = $agStaff->getAgPerson()->getAgPersonMjAgPhoneContact();
        $phoneContacts = $staffMember->getAgPerson()->getAgEntity()->getAgEntityPhoneContact();
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
      }
    ?>
      </td>
      <td>
      <?php
      foreach ($ag_email_contact_types as $agEmailContactType) {
        $check = 0;
        $emailContacts = $staffMember->getAgPerson()->getAgEntity()->getAgEntityEmailContact();
        foreach ($emailContacts as $emailContact) {
          if ($emailContact->getEmailContactTypeId() == $agEmailContactType->getId()) {
            echo '<a href="mailto:' . $emailContact->getAgEmailContact() . '" class="linkMail">'
            . $emailContact->getAgEmailContact() . '</a>' . '<br />';
            $check = 1;
          }
        }
      }
      ?>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<br />
<div style="float: left;">
  <a href="<?php echo url_for('staff/new') ?>" class="linkButton" title="Create New Staff Member">Create New</a>
</div>
<div style="float: right;">
  <?php

//This block creates the navigation links for paginated staff members.
//
//First Page link (or inactive if we're at the first page).
    echo(!$pager->isFirstPage() ? '<a href="' . url_for('staff/list') . '?page=' . $pager->getFirstPage() . $sortAppend . $orderAppend . '" class="buttonText" title="First Page">&lt;&lt;</a>' : '<a class="buttonTextOff">&lt;&lt;</a>');
//Previous Page link (or inactive if we're at the first page).
    echo(!$pager->isFirstPage() ? '<a href="' . url_for('staff/list') . '?page=' . $pager->getPreviousPage() . $sortAppend . $orderAppend .'" class="buttonText" title="Previous Page">&lt;</a>' : '<a class="buttonTextOff">&lt;</a>');
//Next Page link (or inactive if we're at the last page).
    echo(!$pager->isLastPage() ? '<a href="' . url_for('staff/list') . '?page=' . $pager->getNextPage() . $sortAppend . $orderAppend .'" class="buttonText" title="Next Page">&gt;</a>' : '<a class="buttonTextOff">&gt;</a>');
//Last Page link (or inactive if we're at the last page).
    echo(!$pager->isLastPage() ? '<a href="' . url_for('staff/list') . '?page=' . $pager->getLastPage() . $sortAppend . $orderAppend .'" class="buttonText" title="Last Page">&gt;&gt;</a>' : '<a class="buttonTextOff">&gt;&gt;</a>');
  ?>
</div>

