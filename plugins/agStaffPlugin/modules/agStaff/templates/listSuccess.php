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
      <!-- Sex -->
      <th class="head" rowspan="2">
        <div style="margin: 0; padding: 0">Sex</div>
        <?php
          echo($sortColumn =='sex' && $sortOrder == 'ASC' ? '<a href="' . url_for('staff/list') . '?sort=sex&order=ASC" class="buttonSortSelected" title="ascending">&#x25B2;</a>' : '<a href="' . url_for('staff/list') . '?sort=sex&order=ASC" class="buttonSort" title="ascending">&#x25B2;</a>');
          echo($sortColumn =='sex' && $sortOrder == 'DESC' ? '<a href="' . url_for('staff/list') . '?sort=sex&order=DESC" class="buttonSortSelected" title="descending">&#x25BC;</a>' : '<a href="' . url_for('staff/list') . '?sort=sex&order=DESC" class="buttonSort" title="descending">&#x25BC;</a>');
        ?>
      </th>
      <!-- Nationality -->
      <th class="head" rowspan="2">
        <div style="margin: 0; padding: 0">Nationality</div>
        <?php
          echo($sortColumn =='nationality' && $sortOrder == 'ASC' ? '<a href="' . url_for('staff/list') . '?sort=nationality&order=ASC" class="buttonSortSelected" title="ascending">&#x25B2;</a>' : '<a href="' . url_for('staff/list') . '?sort=nationality&order=ASC" class="buttonSort" title="ascending">&#x25B2;</a>');
          echo($sortColumn =='nationality' && $sortOrder == 'DESC' ? '<a href="' . url_for('staff/list') . '?sort=nationality&order=DESC" class="buttonSortSelected" title="descending">&#x25BC;</a>' : '<a href="' . url_for('staff/list') . '?sort=nationality&order=DESC" class="buttonSort" title="descending">&#x25BC;</a>');
        ?>
      </th>
      <!-- Ethnicity -->
      <th class="head" rowspan="2">
        <div style="margin: 0; padding: 0">Ethnicity</div>
        <?php
          echo($sortColumn =='ethnicity' && $sortOrder == 'ASC' ? '<a href="' . url_for('staff/list') . '?sort=ethnicity&order=ASC" class="buttonSortSelected" title="ascending">&#x25B2;</a>' : '<a href="' . url_for('staff/list') . '?sort=ethnicity&order=ASC" class="buttonSort" title="ascending">&#x25B2;</a>');
          echo($sortColumn =='ethnicity' && $sortOrder == 'DESC' ? '<a href="' . url_for('staff/list') . '?sort=ethnicity&order=DESC" class="buttonSortSelected" title="descending">&#x25BC;</a>' : '<a href="' . url_for('staff/list') . '?sort=ethnicity&order=DESC" class="buttonSort" title="descending">&#x25BC;</a>');
        ?>
      </th>
      <!-- Language -->
      <th class="head" rowspan="2">
        <div style="margin: 0; padding: 0">Language</div>
       <?php
          echo($sortColumn =='language' && $sortOrder == 'ASC' ? '<a href="' . url_for('staff/list') . '?sort=language&order=ASC" class="buttonSortSelected" title="ascending">&#x25B2;</a>' : '<a href="' . url_for('staff/list') . '?sort=language&order=ASC" class="buttonSort" title="ascending">&#x25B2;</a>');
          echo($sortColumn =='language' && $sortOrder == 'DESC' ? '<a href="' . url_for('staff/list') . '?sort=language&order=DESC" class="buttonSortSelected" title="descending">&#x25BC;</a>' : '<a href="' . url_for('staff/list') . '?sort=language&order=DESC" class="buttonSort" title="descending">&#x25BC;</a>');
        ?>
      </th>
      <!-- Religion -->
      <th class="head" rowspan="2">
        <div style="margin: 0; padding: 0;">Religion</div>
        <?php
          echo($sortColumn =='religion' && $sortOrder == 'ASC' ? '<a href="' . url_for('staff/list') . '?sort=religion&order=ASC" class="buttonSortSelected" title="ascending">&#x25B2;</a>' : '<a href="' . url_for('staff/list') . '?sort=religion&order=ASC" class="buttonSort" title="ascending">&#x25B2;</a>');
          echo($sortColumn =='religion' && $sortOrder == 'DESC' ? '<a href="' . url_for('staff/list') . '?sort=religion&order=DESC" class="buttonSortSelected" title="descending">&#x25BC;</a>' : '<a href="' . url_for('staff/list') . '?sort=religion&order=DESC" class="buttonSort" title="descending">&#x25BC;</a>');
        ?>
      </th>
      <!-- Resource Type and Organization  -->
      <th class="head" rowspan="2">
        <div style="margin: 0; padding: 0;">Organization</div>
        <?php
#          echo($sortColumn =='religion' && $sortOrder == 'ASC' ? '<a href="' . url_for('staff/list') . '?sort=religion&order=ASC" class="buttonSortSelected" title="ascending">&#x25B2;</a>' : '<a href="' . url_for('staff/list') . '?sort=religion&order=ASC" class="buttonSort" title="ascending">&#x25B2;</a>');
#          echo($sortColumn =='religion' && $sortOrder == 'DESC' ? '<a href="' . url_for('staff/list') . '?sort=religion&order=DESC" class="buttonSortSelected" title="descending">&#x25BC;</a>' : '<a href="' . url_for('staff/list') . '?sort=religion&order=DESC" class="buttonSort" title="descending">&#x25BC;</a>');
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
        <a href="<?php echo url_for('staff/show') . '?page=' . $i . $sortAppend . $orderAppend; ?>" title="View Staff Member <?php echo $staffMember->getId(); ?>" class="linkButton"><?php echo $i++; ?></a>
      </td>
      <?php
        $names = $staffMember->getAgPersonMjAgPersonName();
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
        foreach ($staffMember->getAgPersonSex() as $personSex)
        {
          echo($personSex->getAgSex());
        }
  ?>
      </td>
      <td>
        <?php
    $nationalities = $staffMember->getAgPersonMjAgNationality();
          foreach ($nationalities as $nationality)
          {
      if($nationality->getAgNationality()->getAppDisplay() == 1)
            {
        echo $nationality->getAgNationality() . "<br /> ";
      }
    }
  ?>
      </td>
      <td>
        <?php $ethnicities = $staffMember->getAgEthnicity(); ?>
        <?php foreach ($ethnicities as $ethnicity): ?>
        <?php echo $ethnicity . "<br /> "; ?>
      <?php endforeach; ?>
      </td>
      <td>
        <?php $languages = $staffMember->getAgLanguage(); ?>
        <?php foreach ($languages as $language): ?>
          <?php echo $language . "<br /> "; ?>
      <?php endforeach; ?>
      </td>
      <td>
        <?php $religions = $staffMember->getAgReligion(); ?>
      <?php foreach ($religions as $religion): ?>
        <?php echo $religion . "<br /> "; ?>
      <?php endforeach; ?>
      </td>
      <td>
        <a href="<?php echo url_for('staff/assign') . "?id=" . $staffMember->getId(); ?>" class="linkButton">Assign</a>
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

