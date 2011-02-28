<?php
//note for devs: anytime you see *Name Of Event* it's my placeholder for where you should make
//the app display the name of the event the user is working in.
//
// This page is currently a stub!  The following random string is a marker for the stub.
// PnaODfcm3Kiz4MV4vzbtr4
// PLEASE REMOVE THIS COMMENT BLOCK WHEN YOU DEVELOP THIS PAGE!
// 
// ***This is all still the staff code.  Needs to be cleaned & refactored.***
//
?>

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
    <tr class="tableRow">
      <th class="head" rowspan="2"></th>
      <th class="head" colspan="<?php echo count($ag_person_name_types); ?>" class="centerText">
      Name
      </th>
      <!-- Sex -->
      <th class="head" rowspan="2">
        <div class="tableHeaderContent">Sex</div>
        <?php
          echo($sortColumn =='sex' && $sortOrder == 'ASC' ? '<a href="' . url_for('staff/list') . '?sort=sex&order=ASC" class="buttonSortSelected" title="ascending">&#x25B2;</a>' : '<a href="' . url_for('staff/list') . '?sort=sex&order=ASC" class="buttonSort" title="ascending">&#x25B2;</a>');
          echo($sortColumn =='sex' && $sortOrder == 'DESC' ? '<a href="' . url_for('staff/list') . '?sort=sex&order=DESC" class="buttonSortSelected" title="descending">&#x25BC;</a>' : '<a href="' . url_for('staff/list') . '?sort=sex&order=DESC" class="buttonSort" title="descending">&#x25BC;</a>');
        ?>
      </th>
      <!-- Nationality -->
      <th class="head" rowspan="2">
        <div class="tableHeaderContent">Nationality</div>
        <?php
          echo($sortColumn =='nationality' && $sortOrder == 'ASC' ? '<a href="' . url_for('staff/list') . '?sort=nationality&order=ASC" class="buttonSortSelected" title="ascending">&#x25B2;</a>' : '<a href="' . url_for('staff/list') . '?sort=nationality&order=ASC" class="buttonSort" title="ascending">&#x25B2;</a>');
          echo($sortColumn =='nationality' && $sortOrder == 'DESC' ? '<a href="' . url_for('staff/list') . '?sort=nationality&order=DESC" class="buttonSortSelected" title="descending">&#x25BC;</a>' : '<a href="' . url_for('staff/list') . '?sort=nationality&order=DESC" class="buttonSort" title="descending">&#x25BC;</a>');
        ?>
      </th>
      <!-- Ethnicity -->
      <th class="head" rowspan="2">
        <div class="tableHeaderContent">Ethnicity</div>
        <?php
          echo($sortColumn =='ethnicity' && $sortOrder == 'ASC' ? '<a href="' . url_for('staff/list') . '?sort=ethnicity&order=ASC" class="buttonSortSelected" title="ascending">&#x25B2;</a>' : '<a href="' . url_for('staff/list') . '?sort=ethnicity&order=ASC" class="buttonSort" title="ascending">&#x25B2;</a>');
          echo($sortColumn =='ethnicity' && $sortOrder == 'DESC' ? '<a href="' . url_for('staff/list') . '?sort=ethnicity&order=DESC" class="buttonSortSelected" title="descending">&#x25BC;</a>' : '<a href="' . url_for('staff/list') . '?sort=ethnicity&order=DESC" class="buttonSort" title="descending">&#x25BC;</a>');
        ?>
      </th>
      <!-- Language -->
      <th class="head" rowspan="2">
        <div class="tableHeaderContent">Language</div>
       <?php
          echo($sortColumn =='language' && $sortOrder == 'ASC' ? '<a href="' . url_for('staff/list') . '?sort=language&order=ASC" class="buttonSortSelected" title="ascending">&#x25B2;</a>' : '<a href="' . url_for('staff/list') . '?sort=language&order=ASC" class="buttonSort" title="ascending">&#x25B2;</a>');
          echo($sortColumn =='language' && $sortOrder == 'DESC' ? '<a href="' . url_for('staff/list') . '?sort=language&order=DESC" class="buttonSortSelected" title="descending">&#x25BC;</a>' : '<a href="' . url_for('staff/list') . '?sort=language&order=DESC" class="buttonSort" title="descending">&#x25BC;</a>');
        ?>
      </th>
      <!-- Religion -->
      <th class="head" rowspan="2">
        <div class="tableHeaderContent">Religion</div>
        <?php
          echo($sortColumn =='religion' && $sortOrder == 'ASC' ? '<a href="' . url_for('staff/list') . '?sort=religion&order=ASC" class="buttonSortSelected" title="ascending">&#x25B2;</a>' : '<a href="' . url_for('staff/list') . '?sort=religion&order=ASC" class="buttonSort" title="ascending">&#x25B2;</a>');
          echo($sortColumn =='religion' && $sortOrder == 'DESC' ? '<a href="' . url_for('staff/list') . '?sort=religion&order=DESC" class="buttonSortSelected" title="descending">&#x25BC;</a>' : '<a href="' . url_for('staff/list') . '?sort=religion&order=DESC" class="buttonSort" title="descending">&#x25BC;</a>');
        ?>
      </th>
    </tr>
   <tr class="tableRow">
     <?php foreach ($ag_person_name_types as $ag_person_name_type_id => $ag_person_name_type): ?>
       <th class="subHead" rowspan="2">
         <div class="tableHeaderContent"><?php echo ucwords($ag_person_name_type); ?></div>
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
    </tr>
    <?php endforeach; ?>
  </tbody>
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
    echo(!$pager->isFirstPage() ? '<a href="' . url_for('staff/list') . '?page=' . $pager->getFirstPage() . $sortAppend . $orderAppend . '" class="buttonText" title="First Page">&lt;&lt;</a>' : '<a class="buttonTextOff">&lt;&lt;</a>');
//Previous Page link (or inactive if we're at the first page).
    echo(!$pager->isFirstPage() ? '<a href="' . url_for('staff/list') . '?page=' . $pager->getPreviousPage() . $sortAppend . $orderAppend .'" class="buttonText" title="Previous Page">&lt;</a>' : '<a class="buttonTextOff">&lt;</a>');
//Next Page link (or inactive if we're at the last page).
    echo(!$pager->isLastPage() ? '<a href="' . url_for('staff/list') . '?page=' . $pager->getNextPage() . $sortAppend . $orderAppend .'" class="buttonText" title="Next Page">&gt;</a>' : '<a class="buttonTextOff">&gt;</a>');
//Last Page link (or inactive if we're at the last page).
    echo(!$pager->isLastPage() ? '<a href="' . url_for('staff/list') . '?page=' . $pager->getLastPage() . $sortAppend . $orderAppend .'" class="buttonText" title="Last Page">&gt;&gt;</a>' : '<a class="buttonTextOff">&gt;&gt;</a>');
  ?>
</div>

