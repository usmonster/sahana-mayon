<?php
  //Defines the columns of the organization display list page.
  $columns = array(
    'id' => array('title' => 'Id', 'sortable' => true),
    'scenario' => array('title' => 'scenario', 'sortable' => true),
    'count' => array('title' => 'count', 'sortable' => false),
    'delete' => array('title' => 'delete', 'sortable' => false)
  );

  $thisUrl = url_for('scenario/listshifttemplate');

  ($sf_request->getGetParameter('sort')) ? $sortAppend = '&sort=' . $sf_request->getGetParameter('sort') : $sortAppend = '';
  ($sf_request->getGetParameter('order')) ? $orderAppend = '&order=' . $sf_request->getGetParameter('order') : $orderAppend = '';

  $ascArrow = '&#x25B2;';
  $descArrow = '&#x25BC;';

?>
<h3>Listing of Scenario with Shift Templates Defined</h3>

<br />

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
<!--      <th>Staff Count</th> -->
    </tr>
  </thead>
  <tbody>
    <?php $recordRowNumber = $pager->getFirstIndice(); ?>
    <?php foreach ($pager->getResults() as $ag_shift_template): ?>
    <tr>
      <td><a class=linkButton href="<?php echo url_for('scenario/newshifttemplate?scenId='.$ag_shift_template->getScenarioId()) ?>" title="View Shift Templates in Scenario <?php echo $ag_shift_template->getScenarioId() ?>"><?php echo $recordRowNumber++; ?></a></td>
      <td><?php echo $ag_shift_template->getAgScenario()->getScenario() ?></td>
      <td><?php echo $ag_shift_template->getCount(); ?></td>
      <td><?php echo link_to('delete', 'scenario/deleteshifttemplategroup?scenId='.$ag_shift_template->getScenarioId(), array('method' => 'delete', 'confirm' => 'Are you sure?', 'class' => 'linkButton')) ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<br>
<div>
  <a href="<?php echo url_for('scenario/generatescenarioshift') ?>" class="linkButton" title="Generate Scenario Shifts">Generate Scenario Shifts</a>
</div>

<div style="float: right;">


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
