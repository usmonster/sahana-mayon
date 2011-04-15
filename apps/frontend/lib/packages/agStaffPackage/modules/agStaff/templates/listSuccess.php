<?php
$agPersonNameTypesResultSet = Doctrine::getTable('agPersonNameType')
        ->createQuery('b')
        ->execute();
foreach ($agPersonNameTypesResultSet as $nameType) {
  $agPersonNameTypes[$nameType->getId()] = $nameType->getPersonNameType();
}
$sortColumn = $sf_request->getGetParameter('sort');
$sortOrder = $sf_request->getGetParameter('order');
($sf_request->getGetParameter('status')) ? $filterAppend = '?status=' . $sf_request->getGetParameter('status') : $filterAppend = '?status=active';
($sf_request->getGetParameter('sort')) ? $sortAppend = '&sort=' . $sf_request->getGetParameter('sort') : $sortAppend = '';
($sf_request->getGetParameter('order')) ? $orderAppend = '&order=' . $sf_request->getGetParameter('order') : $orderAppend = '';

$ag_person_name_types = $agPersonNameTypesResultSet;
?>
<div class="floatRight" style="font-size: 12px;"><form name="statusForm" action="<?php echo url_for('staff/list') ?>" method="get"><?php echo $statusFilterForm ?></form></div>
<table class="staffTable">
  <caption>Staff List
  </caption>
  <thead>
    <tr class="tableRow noBorderBottom">
      <th class="head" rowspan="2"></th>
      <th class="head" colspan="<?php echo count($ag_person_name_types); ?>" class="centerText">
        Name
      </th>
      <!-- Agency -->
      <th class="head" rowspan="2">
        <div class="tableHeaderContent">Agency</div>
        <?php
        echo($sortColumn == 'agency' && $sortOrder == 'ASC' ? '<a href="' . url_for('staff/list') . '?sort=agency&order=ASC" class="buttonSortSelected" title="ascending">&#x25B2;</a>' : '<a href="' . url_for('staff/list') . '?sort=agency&order=ASC" class="buttonSort" title="ascending">&#x25B2;</a>');
        echo($sortColumn == 'agency' && $sortOrder == 'DESC' ? '<a href="' . url_for('staff/list') . '?sort=agency&order=DESC" class="buttonSortSelected" title="descending">&#x25BC;</a>' : '<a href="' . url_for('staff/list') . '?sort=agency&order=DESC" class="buttonSort" title="descending">&#x25BC;</a>');
        ?>
      </th>
      <!-- classification -->
      <th class="head" rowspan="2">
        <div class="tableHeaderContent">Classification</div>
        <?php
        echo($sortColumn == 'staff_type' && $sortOrder == 'ASC' ? '<a href="' . url_for('staff/list') . '?sort=staff_type&order=ASC" class="buttonSortSelected" title="ascending">&#x25B2;</a>' : '<a href="' . url_for('staff/list') . '?sort=staff_type&order=ASC" class="buttonSort" title="ascending">&#x25B2;</a>');
        echo($sortColumn == 'staff_type' && $sortOrder == 'DESC' ? '<a href="' . url_for('staff/list') . '?sort=staff_type&order=DESC" class="buttonSortSelected" title="descending">&#x25BC;</a>' : '<a href="' . url_for('staff/list') . '?sort=staff_type&order=DESC" class="buttonSort" title="descending">&#x25BC;</a>');
        ?>
      </th>
      <!-- work_contact -->
      <th class="head" rowspan="2">
        <div class="tableHeaderContent">Phone Contact(s)</div>
        <?php
        echo($sortColumn == 'work_email' && $sortOrder == 'ASC' ? '<a href="' . url_for('staff/list') . '?sort=work_email&order=ASC" class="buttonSortSelected" title="ascending">&#x25B2;</a>' : '<a href="' . url_for('staff/list') . '?sort=work_email&order=ASC" class="buttonSort" title="ascending">&#x25B2;</a>');
        echo($sortColumn == 'work_email' && $sortOrder == 'DESC' ? '<a href="' . url_for('staff/list') . '?sort=work_email&order=DESC" class="buttonSortSelected" title="descending">&#x25BC;</a>' : '<a href="' . url_for('staff/list') . '?sort=work_email&order=DESC" class="buttonSort" title="descending">&#x25BC;</a>');
        ?>
      </th>
      <!-- home_contact -->
      <th class="head" rowspan="2">
        <div class="tableHeaderContent">Email Contact(s)</div>
        <?php
        echo($sortColumn == 'home_email' && $sortOrder == 'ASC' ? '<a href="' . url_for('staff/list') . '?sort=home_email&order=ASC" class="buttonSortSelected" title="ascending">&#x25B2;</a>' : '<a href="' . url_for('staff/list') . '?sort=home_email&order=ASC" class="buttonSort" title="ascending">&#x25B2;</a>');
        echo($sortColumn == 'home_email' && $sortOrder == 'DESC' ? '<a href="' . url_for('staff/list') . '?sort=home_email&order=DESC" class="buttonSortSelected" title="descending">&#x25BC;</a>' : '<a href="' . url_for('staff/list') . '?sort=home_email&order=DESC" class="buttonSort" title="descending">&#x25BC;</a>');
        ?>
      </th>
      <!-- staff resource status -->
      <th class="head" rowspan="2">
        <div class="tableHeaderContent">Staff Resource Status</div>
        <?php
        echo($sortColumn == 'staff_resource_status' && $sortOrder == 'ASC' ? '<a href="' . url_for('staff/list') . '?sort=staff_resource_status&order=ASC" class="buttonSortSelected" title="ascending">&#x25B2;</a>' : '<a href="' . url_for('staff/list') . '?sort=staff_resource_status&order=ASC" class="buttonSort" title="ascending">&#x25B2;</a>');
        echo($sortColumn == 'staff_resource_status' && $sortOrder == 'DESC' ? '<a href="' . url_for('staff/list') . '?sort=staff_resource_status&order=DESC" class="buttonSortSelected" title="descending">&#x25BC;</a>' : '<a href="' . url_for('staff/list') . '?sort=staff_resource_status&order=DESC" class="buttonSort" title="descending">&#x25BC;</a>');
        ?>
      </th>
    </tr>
    <tr class="tableRow">
      <?php foreach ($ag_person_name_types as $ag_person_name_type_id => $ag_person_name_type): ?>
          <th class="subHead">
            <div class="noMargin noPadding"><?php echo ucwords($ag_person_name_type); ?></div>
        <?php
          echo($sortColumn == 'person_name' . '_' . $ag_person_name_type_id && $sortOrder == 'ASC' ? '<a href="' . url_for('staff/list') . '?sort=person_name' . '_' . $ag_person_name_type_id . '&order=ASC" class="buttonSortSelected" title="ascending">&#x25B2;</a>' : '<a href="' . url_for('staff/list') . '?sort=person_name' . '_' . $ag_person_name_type_id . '&order=ASC" class="buttonSort" title="ascending">&#x25B2;</a>');
          echo($sortColumn == 'person_name' . '_' . $ag_person_name_type_id && $sortOrder == 'DESC' ? '<a href="' . url_for('staff/list') . '?sort=person_name' . '_' . $ag_person_name_type_id . '&order=DESC" class="buttonSortSelected" title="descending">&#x25BC;</a>' : '<a href="' . url_for('staff/list') . '?sort=person_name' . '_' . $ag_person_name_type_id . '&order=DESC" class="buttonSort" title="descending">&#x25BC;</a>');
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
                <a href="<?php echo url_for('staff/show?page=' . $i) . $sortAppend . $orderAppend; ?>" title="View Staff Member <?php echo $staffMember->getId(); ?>" class="linkButton"><?php echo $i++; ?></a>
              </td>
      <?php
            $names = $staffMember->getAgPerson()->getAgPersonMjAgPersonName();
            foreach ($agPersonNameTypes as $agPersonNameTypeId => $agPersonNameType) {
              echo "<td>";

              foreach ($names as $name) {
                if ($agPersonNameTypeId == $name->getPersonNameTypeId()) {
                  echo $name->getAgPersonName();
                }
              }
              echo "</td>";
            }
      ?>
            <td>
        <?php
            $displayStaffResources = array();
            foreach ($staffMember->getAgStaffResource() as $staffRec) {
              if ($staffResourceStatus == 'all' ||
                  $staffRec->getAgStaffResourceStatus()->staff_resource_status == $staffResourceStatus)
              {
                $stfRes = $staffRec->getAgStaffResourceType()->staff_resource_type;
                $stfOrg = $staffRec->getAgOrganization()->organization;
                $stfResStat = $staffRec->getAgStaffResourceStatus()->staff_resource_status;
                $displayStaffResources[$stfRes] = array($stfOrg, $stfResStat);
              }
            }
            $lineBreak = FALSE;
            foreach($displayStaffResources as $sRes => $sr)
            {
              if ($lineBreak) { echo '<br />'; }
              else { $lineBreak = TRUE; }
              echo $sr[0];
            }
        ?>
          </td>
          <td>
        <?php
            $lineBreak = FALSE;
            foreach($displayStaffResources as $sRes =>$sr)
            {
              if ($lineBreak) { echo '<br />'; }
              else { $lineBreak = TRUE; }
              echo $sRes;
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
                  echo '<a href="mailto:' . $emailContact->getAgEmailContact() . '" class="linkText">'
                  . $emailContact->getAgEmailContact() . '</a>' . '<br />';
                  $check = 1;
                }
              }
            }
        ?>
          </td>
          <td>
            <?php
              $lineBreak = FALSE;
              foreach($displayStaffResources as $sRes =>$sr)
              {
                if ($lineBreak) { echo '<br />'; }
                else { $lineBreak = TRUE; }
                echo $sr[1];
              }
            ?>
          </td>
        </tr>
    <?php endforeach; ?>
          </tbody>
          <tfoot class="tFootInfo">
          <tr>
            <td colspan="11">
               <?php
    // Output the current staff members being shown, as well total number in the list.
    echo $pager->getFirstIndice() . "-" . $pager->getLastIndice() . " of " . $pager->count();
    ?>
            </td>
          </tr>
            </tfoot>
        </table>
        <br />
        <div class="floatLeft">
          <a href="<?php echo url_for('staff/new') ?>" class="linkButton" title="Create New Staff Member">Create New</a>
        </div>
        <div class="floatRight">
  <?php
//This block creates the navigation links for paginated staff members.
//
//First Page link (or inactive if we're at the first page).
            echo(!$pager->isFirstPage() ? '<a href="' . url_for('staff/list') . $filterAppend . '&page=' . $pager->getFirstPage() . $sortAppend . $orderAppend . '" class="buttonText" title="First Page">&lt;&lt;</a>' : '<a class="buttonTextOff">&lt;&lt;</a>');
//Previous Page link (or inactive if we're at the first page).
            echo(!$pager->isFirstPage() ? '<a href="' . url_for('staff/list') . $filterAppend .'&page=' . $pager->getPreviousPage() . $sortAppend . $orderAppend . '" class="buttonText" title="Previous Page">&lt;</a>' : '<a class="buttonTextOff">&lt;</a>');
//Next Page link (or inactive if we're at the last page).
            echo(!$pager->isLastPage() ? '<a href="' . url_for('staff/list') . $filterAppend . '&page=' . $pager->getNextPage() . $sortAppend . $orderAppend . '" class="buttonText" title="Next Page">&gt;</a>' : '<a class="buttonTextOff">&gt;</a>');
//Last Page link (or inactive if we're at the last page).
            echo(!$pager->isLastPage() ? '<a href="' . url_for('staff/list') . $filterAppend . '&page=' . $pager->getLastPage() . $sortAppend . $orderAppend . '" class="buttonText" title="Last Page">&gt;&gt;</a>' : '<a class="buttonTextOff">&gt;&gt;</a>');
  ?>
</div>

