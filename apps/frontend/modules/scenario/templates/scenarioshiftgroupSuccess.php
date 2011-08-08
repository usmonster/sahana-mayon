<h3>List Scenario With Defined Scenario Shifts</h3>

<?php
//Defines the columns of the organization display list page.
$columns = array(
  'id' => array('title' => 'Id', 'sortable' => false),
  'scenario' => array('title' => 'scenario', 'sortable' => false),
  'shiftCount' => array('title' => 'shift count', 'sortable' => false),
  'Delete Group Shifts' => array('title' => 'delete shifts', 'sortable' => false)
);

$thisUrl = url_for('scenario/scenarioshiftgroup');

($sf_request->getGetParameter('sort')) ? $sortAppend = '&sort=' . $sf_request->getGetParameter('sort') : $sortAppend = '';
($sf_request->getGetParameter('order')) ? $orderAppend = '&order=' . $sf_request->getGetParameter('order') : $orderAppend = '';

$ascArrow = '&#x25B2;';
$descArrow = '&#x25BC;';
?>
<br />
Click on Id to view all scenario shifts defined for that scenario.
<br /><br />

<table class="staffTable">
  <thead>
    <tr class="head">
      <?php foreach ($columns as $column => $columnCaption): ?>
        <th>
        <?php echo $columnCaption['title'] ?>
        <?php
        if ($columnCaption['sortable']) {
          echo($sortColumn == $column && $sortOrder == 'ASC' ? '<a href="' . $thisUrl . '?sort=' . $column . '&order=ASC" class="buttonSortSelected" title="ascending">' . $ascArrow . '</a>' : '<a href="' . $thisUrl . '?sort=' . $column . '&order=ASC" class="buttonSort" title="ascending">' . $ascArrow . '</a>');
        } ?>
        <?php
        if ($columnCaption['sortable']) {
          echo($sortColumn == $column && $sortOrder == 'DESC' ? '<a href="' . $thisUrl . '?sort=' . $column . '&order=DESC" class="buttonSortSelected" title="descending">' . $descArrow . '</a>' : '<a href="' . $thisUrl . '?sort=' . $column . '&order=DESC" class="buttonSort" title="descending">' . $descArrow . '</a>');
        } ?>
        </th>
<?php endforeach; ?>
      </tr>
    </thead>
    <tbody>
<?php $recordRowNumber = $pager->getFirstIndice(); ?>
<?php foreach ($pager->getResults() as $scenarioShiftGroup): ?>
          <tr>
            <td><a class=continueButton href="<?php echo url_for('scenario/showscenarioshiftgroup?scenId=' . $scenarioShiftGroup->getId()) ?>" title="View Scenario Shifts in Scenario <?php echo $scenarioShiftGroup->getId() ?>"><?php echo $recordRowNumber++; ?></a></td>
            <td><?php echo $scenarioShiftGroup->getScenario(); ?></td>
            <td><?php echo $scenarioShiftGroup->getCount(); ?></td>
            <td><?php echo link_to('delete', 'scenario/deletescenarioshiftgroup?scenId=' . $scenarioShiftGroup->getId(), array('method' => 'delete', 'confirm' => 'Are you sure?', 'class' => 'continueButton')) ?></td>
          </tr>
<?php endforeach; ?>
        </tbody>
      </table>

      <div class="rightFloat">
  <?php
//
//First Page link (or inactive if we're at the first page).
          echo(!$pager->isFirstPage() ? '<a href="' . $thisUrl . '?page=' . $pager->getFirstPage() . $sortAppend . $orderAppend . '" class="buttonText" title="First Page">&lt;&lt;</a>' : '<a class="buttonTextOff">&lt;&lt;</a>');
//Previous Page link (or inactive if we're at the first page).
          echo(!$pager->isFirstPage() ? '<a href="' . $thisUrl . '?page=' . $pager->getPreviousPage() . $sortAppend . $orderAppend . '" class="buttonText" title="Previous Page">&lt;</a>' : '<a class="buttonTextOff">&lt;</a>');
//Next Page link (or inactive if we're at the last page).
          echo(!$pager->isLastPage() ? '<a href="' . $thisUrl . '?page=' . $pager->getNextPage() . $sortAppend . $orderAppend . '" class="buttonText" title="Next Page">&gt;</a>' : '<a class="buttonTextOff">&gt;</a>');
//Last Page link (or inactive if we're at the last page).
          echo(!$pager->isLastPage() ? '<a href="' . $thisUrl . '?page=' . $pager->getLastPage() . $sortAppend . $orderAppend . '" class="buttonText" title="Last Page">&gt;&gt;</a>' : '<a class="buttonTextOff">&gt;&gt;</a>');
  ?>
</div>
