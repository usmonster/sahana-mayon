<?php
  use_javascript('agMain.js');
  $wizardOp['step'] = 7;
  $encodedWizard = json_encode($wizardOp);
  $sf_response->setCookie('wizardOp', $encodedWizard);

  ?>
<h2>Scenario Shifts for <span class="highlightedText"><?php echo $scenarioName ?></span></h2>
  <?php include_partial('wizard', array('wizardDiv' => $wizardDiv)); ?>

<?php #include_partial('scenarioshiftform', array('scenarioshiftform' => $scenarioshiftform, 'myRandomParam' => $myRandomParam, 'outputResults' => $outputResults)) ?>
<?php
  //Defines the columns of the scenario shift display list page.
  $columns = array(
    'id' => array('title' => 'ID', 'sortable' => false),
    'ScenarioFacilityResource' => array('title' => 'Staff Resource Type /<br/>Facility Resource ', 'sortable' => false),
    'taskId' =>  array('title' => 'Status / Task', 'sortable' => false),
    'minimumStaff' =>  array('title' => 'Min / Max<br/>Staff', 'sortable' => false),
    'taskLengthMinutes' =>  array('title' => 'Task / Break<br/>Length', 'sortable' => false),
    'minutesStartToFacilityActivation' =>  array('title' => 'Shifts<br/>Start', 'sortable' => false),
    'staffWave' =>  array('title' => 'Staff<br/>Wave', 'sortable' => false),
  );

  $thisUrl = url_for('scenario/shifts?id=' . $scenario_id);

  ($sf_request->getGetParameter('sort')) ? $sortAppend = '&sort=' . $sf_request->getGetParameter('sort') : $sortAppend = '';
  ($sf_request->getGetParameter('order')) ? $orderAppend = '&order=' . $sf_request->getGetParameter('order') : $orderAppend = '';

  $ascArrow = '&#x25B2;';
  $descArrow = '&#x25BC;';

?>

<p>A total of <span class="highlightedText">
          <?php echo $shifts ?></span> shifts have been generated covering
          <span class="highlightedText"><?php echo $operationTime ?></span> of operation.  Click the id below to customize individual shifts.</p>

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
    <?php $recordRowNumber = $pager->getFirstIndice(); ?>
    <?php foreach ($pager->getResults() as $ag_scenario_shift): ?>
    <tr>
      <td><a class=continueButton href="<?php echo url_for('scenario/shifts?id=' . $scenario_id) . '/' . $ag_scenario_shift->getId() ?>" title="View Scenario Shift <?php echo $ag_scenario_shift->getId() ?>"><?php echo $recordRowNumber++; ?></a></td>
      <td class="left"><?php
//            $facilityResourceId = $scenarioShifts[$ag_scenario_shift->getId()]['facility_resource_id'];
//#            $facilityResourceDisplay = $facilityResourceInfo[$facilityResourceId]['facility_name'] . ' (' . $facilityResourceInfo[$facilityResourceId]['facility_code'] . ') : ' . $facilityResourceInfo[$facilityResourceId]['facility_resource_type'];
//            $facilityResourceDisplay = $facilityResourceInfo[$facilityResourceId]['facility_name'] .  ' : ' . $facilityResourceInfo[$facilityResourceId]['facility_resource_type'];
//            echo $facilityResourceDisplay;
        echo $ag_scenario_shift->agStaffResourceType['staff_resource_type'] . ' / '. '<br/>'
        . $ag_scenario_shift->getAgScenarioFacilityResource(); ?></td>
      <td><?php echo $ag_scenario_shift->agShiftStatus['shift_status'] . ' / '. '<br/>'
        . $ag_scenario_shift->agTask['task']; ?></td>
      <td><?php echo $ag_scenario_shift['minimum_staff']; ?> / <?php
        echo $ag_scenario_shift['maximum_staff']; ?></td>
      <td><?php
        echo agDateTimeHelper::minsToComponentsStr($ag_scenario_shift['task_length_minutes']); ?> / <?php
        echo agDateTimeHelper::minsToComponentsStr($ag_scenario_shift['break_length_minutes']); ?></td>
      <td><?php echo agDateTimeHelper::minsToComponentsStr($ag_scenario_shift['minutes_start_to_facility_activation']); ?></td>
      <td><?php echo $ag_scenario_shift['staff_wave']; ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<br>
<div>
  <a href="<?php echo url_for('scenario/shifts?id=' .$scenario_id) .'/new'?>" class="continueButton" title="Create New Scenario Shift">Create New Scenario Shift</a>
  <a href="<?php echo url_for('scenario/review?id=' .$scenario_id ) ?>" class="continueButton" title="Finish Wizard">Finish Wizard and Review Scenario</a>

</div>

<div class="rightFloat" >
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
