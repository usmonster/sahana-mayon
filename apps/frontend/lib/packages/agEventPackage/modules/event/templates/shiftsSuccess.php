<h3>Event Shifts for <?php echo $eventName ?></h3>

<?php #include_partial('scenarioshiftform', array('scenarioshiftform' => $scenarioshiftform, 'myRandomParam' => $myRandomParam, 'outputResults' => $outputResults)) ?>
<?php
  //Defines the columns of the scenario shift display list page.
  $columns = array(
    'id' => array('title' => 'Id', 'sortable' => false),
    'eventFacilityGroup' => array('title' => 'facility group', 'sortable' => false),
    'eventFacilityResource' => array('title' => 'facility resource', 'sortable' => false),
    'staffResourceType' =>  array('title' => 'staff resource type', 'sortable' => false),
    'start_time' =>  array('title' => 'start', 'sortable' => false),
    'end_time' =>  array('title' => 'end', 'sortable' => false),
    'committed_staff' =>  array('title' => 'committed staff', 'sortable' => false)
  );

  $thisUrl = url_for('event/shifts?id=' . $event_id);

  ($sf_request->getGetParameter('sort')) ? $sortAppend = '&sort=' . $sf_request->getGetParameter('sort') : $sortAppend = '';
  ($sf_request->getGetParameter('order')) ? $orderAppend = '&order=' . $sf_request->getGetParameter('order') : $orderAppend = '';

  $ascArrow = '&#x25B2;';
  $descArrow = '&#x25BC;';

?>

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
    <?php foreach ($pager->getResults() as $ag_event_shift): ?>
    <tr>
      <td><a class=linkButton href="<?php echo url_for('event/shifts?id='.$ag_event_shift->getId()) . '?shiftid=' . $ag_event_shift->getId() ?>" title="View event Shift <?php echo $ag_event_shift->getId() ?>"><?php echo $recordRowNumber++; ?></a></td>
      <td><?php echo $eventShifts[$ag_event_shift->getId()]['event']; ?></td>
      <td><?php echo $ag_event_shift->getAgEventFacilityResource();//->getAgEventFacilityResource(); ?></td>
      <td><?php echo $ag_event_shift->getAgStaffResourceType()->getStaffResourceType(); ?></td>
      <td><?php echo $ag_event_shift->getMinutesStartToFacilityActivation(); ?></td>
      <td><?php echo $ag_event_shift->getStaffWave(); ?></td>
      <td><?php echo $ag_event_shift->getAgShiftStatus()->getShiftStatus(); ?></td>
      <td><?php echo $ag_event_shift->getAgDeploymentAlgorithm()->getDeploymentAlgorithm(); ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<br>
<div>
  <a href="<?php echo url_for('event/shifts?id=' .$event_id) ?>/new" class="linkButton" title="Create New Scenario Shift">Create New Event Shift</a>
  <a href="<?php echo url_for('event/staffpool?id=' .$event_id ) ?>" class="linkButton" title="Define Staff Pools">Save and Define Staff Pools</a>

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
