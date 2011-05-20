<h2>Event Shifts for <span class="highlightedText"><?php echo $event_name ?></span></h2>

<?php
  //Defines the columns of the event shift display list page.

$columns = array(
    'id' => array('title' => 'Id', 'sortable' => false),
    'eventFacilityGroup' => array('title' => 'Facility Group', 'sortable' => false),
    'eventFacilityResource' => array('title' => 'Staff Resource Type /<br/>Facility Resource', 'sortable' => false),
    'start_time' =>  array('title' => 'Start', 'sortable' => false),
    'end_time' =>  array('title' => 'End', 'sortable' => false),
    'committed_staff' =>  array('title' => 'Committed Staff', 'sortable' => false)
  );

  $thisUrl = url_for('event/shifts?event=' . $event_name);

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
      <td><a class=continueButton href="<?php echo url_for('event/shifts?event=' . urlencode($event_name)) . '/' . $ag_event_shift->getId() ?>" title="View event Shift <?php echo $ag_event_shift->getId() ?>"><?php echo $recordRowNumber++; ?></a></td>
      <td><?php echo $ag_event_shift->getAgEventFacilityResource()->getAgEventFacilityGroup()->getEventFacilityGroup() ?></td>
      <td><?php echo $ag_event_shift->getAgStaffResourceType()->getStaffResourceType() . ' / <br />' . 
            $ag_event_shift->getAgEventFacilityResource()->getAgFacilityResource();
            ?>
      </td>
      
      <?php
      if(!$ag_event_shift->getAgEventFacilityResource()->getAgEventFacilityResourceActivationTime()){
        $startTime = $ag_event_shift->getAgEvent()->getZeroHour() +
            
            $ag_event_shift['minutes_start_to_facility_activation'];
        
        $endTime = $ag_event_shift->getAgEvent()->getZeroHour() - 
            $ag_event_shift['minutes_start_to_facility_activation'];

      }
      else{
        $startTime = $ag_event_shift->getAgEvent()->getZeroHour() +
        $startTime = $ag_event_shift->getAgEventFacilityResource()->getAgEventFacilityResourceActivationTime()
            - $ag_event_shift['minutes_start_to_facility_activation'];

        
        $endTime = $ag_event_shift->getAgEventFacilityResource()->getAgEventFacilityResourceActivationTime()
            - $ag_event_shift['minutes_start_to_facility_activation'];
        
      }
      
      
        ?>
      <td><?php echo $startTime; //$ag_event_shift->getStartTime(); ?></td>
      <td><?php echo $endTime; //$ag_event_shift->getEndTime(); ?></td>
      <td><?php echo count($ag_event_shift->getAgStaffEvent()); ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<br>
<div>
  <a href="<?php echo url_for('event/shifts?event=' . urlencode($event_name)) ?>/new" class="continueButton" title="Create New Scenario Shift">Create New Event Shift</a>

</div>

<div class="rightFloat" >
  <?php
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
