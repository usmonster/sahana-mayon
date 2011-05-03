<?php
//Defines the columns of the scenario shift display list page.
$columns = array(
  'id' => array('title' => 'Id', 'sortable' => false),
  'scenario' => array('title' => 'scenario', 'sortable' => false),
  'ScenarioFacilityResource' => array('title' => 'facility resource', 'sortable' => false),
  'staffResourceId' => array('title' => 'staff resource id', 'sortable' => false),
  'taskId' => array('title' => 'taskId', 'sortable' => false),
  'taskLengthMinutes' => array('title' => 'task length minutes', 'sortable' => false),
  'breakLengthMinutes' => array('title' => 'breakLengthMinutes', 'sortable' => false),
  'minutesStartToFacilityActivation' => array('title' => 'facility activation start minutes', 'sortable' => false),
  'minimumStaff' => array('title' => 'minimum staff', 'sortable' => false),
  'maximumStaff' => array('title' => 'maximum staff', 'sortable' => false),
  'staffWave' => array('title' => 'staff wave', 'sortable' => false),
  'shiftStatusId' => array('title' => 'shiftStatusId', 'sortable' => false),
  'deploymentAlgorithmId' => array('title' => 'deployment algorithm id', 'sortable' => false)
);

$thisUrl = url_for('scenario/showscenarioshiftgroup');

($sf_request->getGetParameter('sort')) ? $sortAppend = '&sort=' . $sf_request->getGetParameter('sort') : $sortAppend = '';
($sf_request->getGetParameter('order')) ? $orderAppend = '&order=' . $sf_request->getGetParameter('order') : $orderAppend = '';

$ascArrow = '&#x25B2;';
$descArrow = '&#x25BC;';
?>

<h3>Delete a Group of Scenario Shifts</h3>

<br />
Delete all scenario shifts relating to <?php echo $scenarioName; ?> scenario.
<br /><br />

<table class="staffTable">
  <thead>
    <tr class="head">
<?php foreach ($columns as $column => $columnCaption): ?>
        <th>
<?php echo $columnCaption['title'] ?>
        <?php if ($columnCaption['sortable']) {
          echo($sortColumn == $column && $sortOrder == 'ASC' ? '<a href="' . $thisUrl . '?sort=' . $column . '&order=ASC" class="buttonSortSelected" title="ascending">' . $ascArrow . '</a>' : '<a href="' . $thisUrl . '?sort=' . $column . '&order=ASC" class="buttonSort" title="ascending">' . $ascArrow . '</a>');
        } ?>
<?php if ($columnCaption['sortable']) {
          echo($sortColumn == $column && $sortOrder == 'DESC' ? '<a href="' . $thisUrl . '?sort=' . $column . '&order=DESC" class="buttonSortSelected" title="descending">' . $descArrow . '</a>' : '<a href="' . $thisUrl . '?sort=' . $column . '&order=DESC" class="buttonSort" title="descending">' . $descArrow . '</a>');
        } ?>
        </th>
<?php endforeach; ?>
      </tr>
    </thead>
    <tbody>
<?php $recordRowNumber = $pager->getFirstIndice(); ?>
<?php foreach ($pager->getResults() as $ag_scenario_shift): ?>
          <tr>
            <td><?php echo $recordRowNumber++; ?></td>
            <td><?php echo $ag_scenario_shift->getAgScenarioFacilityResource()->getAgScenarioFacilityGroup()->getAgScenario()->getScenario(); ?></td>
            <td><?php echo $ag_scenario_shift->getAgScenarioFacilityResource(); ?></td>
            <td><?php echo $ag_scenario_shift->getAgStaffResourceType()->getStaffResourceType(); ?></td>
            <td><?php echo $ag_scenario_shift->getTaskId(); ?></td>
            <td><?php echo $ag_scenario_shift->getTaskLengthMinutes(); ?></td>
            <td><?php echo $ag_scenario_shift->getBreakLengthMinutes(); ?></td>
            <td><?php echo $ag_scenario_shift->getMinutesStartToFacilityActivation(); ?></td>
            <td><?php echo $ag_scenario_shift->getMinimumStaff(); ?></td>
            <td><?php echo $ag_scenario_shift->getMaximumStaff(); ?></td>
            <td><?php echo $ag_scenario_shift->getStaffWave(); ?></td>
            <td><?php echo $ag_scenario_shift->getAgShiftStatus()->getShiftStatus(); ?></td>
            <td><?php echo $ag_scenario_shift->getAgDeploymentAlgorithm()->getDeploymentAlgorithm(); ?></td>
          </tr>
<?php endforeach; ?>
        </tbody>
      </table>

      <br />

      <table>
        <tfoot>
          <tr>
            <td colspan="2">
              &nbsp;<a href="<?php echo url_for('scenario/scenarioshiftgroup') ?>" class="linkButton">Back to List</a>
              &nbsp;<?php echo link_to('Delete All', 'scenario/deletescenarioshiftgroup?scenId=' . $scenarioId, array('method' => 'delete', 'confirm' => 'Are you sure?', 'class' => 'linkButton')) ?>
            </td>
          </tr>
        </tfoot>
      </table>


      <div class="rightFloat">
  <?php
//
//First Page link (or inactive if we're at the first page).
          echo(!$pager->isFirstPage() ? '<a href="' . $thisUrl . '?scenId=' . $scenarioId . '&page=' . $pager->getFirstPage() . $sortAppend . $orderAppend . '" class="buttonText" title="First Page">&lt;&lt;</a>' : '<a class="buttonTextOff">&lt;&lt;</a>');
//Previous Page link (or inactive if we're at the first page).
          echo(!$pager->isFirstPage() ? '<a href="' . $thisUrl . '?scenId=' . $scenarioId . '&page=' . $pager->getPreviousPage() . $sortAppend . $orderAppend . '" class="buttonText" title="Previous Page">&lt;</a>' : '<a class="buttonTextOff">&lt;</a>');
//Next Page link (or inactive if we're at the last page).
          echo(!$pager->isLastPage() ? '<a href="' . $thisUrl . '?scenId=' . $scenarioId . '&page=' . $pager->getNextPage() . $sortAppend . $orderAppend . '" class="buttonText" title="Next Page">&gt;</a>' : '<a class="buttonTextOff">&gt;</a>');
//Last Page link (or inactive if we're at the last page).
          echo(!$pager->isLastPage() ? '<a href="' . $thisUrl . '?scenId=' . $scenarioId . '&page=' . $pager->getLastPage() . $sortAppend . $orderAppend . '" class="buttonText" title="Last Page">&gt;&gt;</a>' : '<a class="buttonTextOff">&gt;&gt;</a>');
  ?>
</div>
