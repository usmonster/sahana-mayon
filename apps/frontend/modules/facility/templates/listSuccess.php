<?php
  $columns = array(
    'id' => array('title' => 'Id', 'sortable' => false),
    'facility_code' => array('title' => 'Facility Code', 'sortable' => true),
    'facility_name' => array('title' => 'Facility Name', 'sortable' => true),
    'services' => array('title' => 'Services', 'sortable' => false)
  );
  $thisUrl = url_for('facility/list');

  ($sf_request->getGetParameter('sort')) ? $sortAppend = '&sort=' . $sf_request->getGetParameter('sort') : $sortAppend = '';
  ($sf_request->getGetParameter('order')) ? $orderAppend = '&order=' . $sf_request->getGetParameter('order') : $orderAppend = '';

  $ascArrow = '&#x25B2;';
  $descArrow = '&#x25BC;';

?>
<h3>Facility Listing</h3>

<table class="staffTable">
  <thead>
    <tr class="head">
<?php foreach($columns as $column => $columnCaption): ?>
      <th>
        <?php echo $columnCaption['title'] ?>
        <?php if ($columnCaption['sortable']) { echo($sortColumn == $column && $sortOrder == 'ASC' ? '<a href="' . $thisUrl . '?sort=' . $column . '&order=ASC" class="buttonSortSelected" title="ascending">' . $ascArrow . '</a>' : '<a href="' . $thisUrl . '?sort=' . $column . '&order=ASC" class="buttonSort" title="ascending">' . $ascArrow . '</a>'); } ?>
        <?php if ($columnCaption['sortable']) { echo($sortColumn == $column && $sortOrder == 'DESC' ? '<a href="' . $thisUrl . '?sort=' . $column . '&order=DESC" class="buttonSortSelected" title="descending">' . $descArrow . '</a>' : '<a href="' . $thisUrl . '?sort=' . $column . '&order=DESC" class="buttonSort" title="descending">' . $descArrow . '</a>'); } ?>
      </th>
<?php endforeach; ?>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($pager->getResults() as $ag_facility): ?>
    <tr>
      <td><a class=linkButton href="<?php echo url_for('facility/show?id='.$ag_facility->getId()) ?>"><?php echo $ag_facility->getId() ?></a></td>
      <td><?php echo $ag_facility->getFacilityCode() ?></td>
      <td><?php echo $ag_facility->getFacilityName() ?></td>
      <td><?php $comma=0; foreach ($ag_facility->getAgFacilityResource() as $n) echo ($comma++ > 0 ? ', ' : '') . ucwords($n->getAgFacilityResourceType()->getFacilityResourceType()); echo (count($ag_facility->getAgFacilityResource()) ? ' (' . count($ag_facility->getAgFacilityResource()) . ')' : '(None)');  ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<br>
<div>
  <a href="<?php echo url_for('facility/new') ?>" class="linkButton" title="Create New Facility">Create New</a>
</div>

<div style="float: right;">
  <?php

//
//First Page link (or inactive if we're at the first page).
    echo(!$pager->isFirstPage() ? '<a href="' . $thisUrl . '?page=' . $pager->getFirstPage() . $sortAppend . $orderAppend . '" class="buttonText" title="First Page">&lt;&lt;</a>' : '<a class="buttonTextOff">&lt;&lt;</a>');
//Previous Page link (or inactive if we're at the first page).
    echo(!$pager->isFirstPage() ? '<a href="' . $thisUrl . '?page=' . $pager->getPreviousPage() . $sortAppend . $orderAppend .'" class="buttonText" title="Previous Page">&lt;</a>' : '<a class="buttonTextOff">&lt;</a>');
//Next Page link (or inactive if we're at the last page).
    echo(!$pager->isLastPage() ? '<a href="' . $thisUrl . '?page=' . $pager->getNextPage() . $sortAppend . $orderAppend .'" class="buttonText" title="Next Page">&gt;</a>' : '<a class="buttonTextOff">&gt;</a>');
//Last Page link (or inactive if we're at the last page).
    echo(!$pager->isLastPage() ? '<a href="' . $thisUrl . '?page=' . $pager->getLastPage() . $sortAppend . $orderAppend .'" class="buttonText" title="Last Page">&gt;&gt;</a>' : '<a class="buttonTextOff">&gt;&gt;</a>');
  ?>
</div>
