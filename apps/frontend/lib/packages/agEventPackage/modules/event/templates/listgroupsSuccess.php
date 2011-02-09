<h2><?php if(isset($event)) {echo '<span style="color: #ff8f00">' . $event->event_name . ' </span>';} ?> Facilities Management</h2>
<?php
//and a return to dashboard button.
$b = $pager;
$a = $facilityGroupArray;
?>
<br />
<h3>Facilities <?php echo $pager->getFirstIndice() . "-" . $pager->getLastIndice() . " of " . $pager->count() . ((isset($event)) ? ' for the <span style="color: #ff8f00">' . $event->event_name . '</span> Event' : ' for all Events'); ?></h3>
<table class="singleTable">
  <thead>
    <tr>
      <th class="head">
        <div class="tableHeaderContent">Facility Group</div>
        <?php
//          echo($sortColumn =='agency' && $sortOrder == 'ASC' ? '<a href="' . url_for('staff/list') . '?sort=agency&order=ASC" class="buttonSortSelected" title="ascending">&#x25B2;</a>' : '<a href="' . url_for('staff/list') . '?sort=agency&order=ASC" class="buttonSort" title="ascending">&#x25B2;</a>');
//          echo($sortColumn =='agency' && $sortOrder == 'DESC' ? '<a href="' . url_for('staff/list') . '?sort=agency&order=DESC" class="buttonSortSelected" title="descending">&#x25BC;</a>' : '<a href="' . url_for('staff/list') . '?sort=agency&order=DESC" class="buttonSort" title="descending">&#x25BC;</a>');
        ?>
      </th>
      <th class="head">Facility Name & Resource Type</th>
      <th class="head">Facility Code</th>
      <th class="head">Facility Status</th>
      <th class="head">Facility Activation Time</th>
      <th class="head">Facility Group Type</th>
      <?php
        if(!(isset($event))) {
          echo '<th class="head">Event</th>';
        }
      ?>
    </tr>
  </thead>
  <tbody>
    <?php foreach($pager->getResults() as $facility): ?>
      <?php// foreach ($facilityGroup as $facility): ?>
      <tr>
        <td><a href="<?php echo url_for('event/groupdetail?event=' . urlencode($facility['e_event_name']) . '&group=' . urlencode($facility['efg_event_facility_group'])) ?>" class="linkText" name="modal" title="Facility Group <?php echo $facility['efg_event_facility_group']; ?> for the <?php echo $facility['e_event_name']; ?> Scenario"><?php echo $facility['efg_event_facility_group'] ?></a></td>
        <td><?php echo $facility['f_facility_name'] . ": " . $facility['frt_facility_resource_type']; ?></td>
        <td><?php echo $facility['f_facility_code']; ?></td>
        <td><?php echo $facility['ras_facility_resource_allocation_status']; ?></td>
        <td><?php
            if(isset($facility['efrat_activation_time'])) {
              $timeSplit = explode(' ', $facility['efrat_activation_time']);
              echo $timeSplit[0];
            } else {
              echo '----';
            }
        ?></td>
        <td><?php echo $facility['fgt_facility_group_type'] ?></td>
        <?php
          if(!(isset($event))) { echo '<td>' . $facility['e_event_name'] . '</td>'; } ?>
      </tr>
      <?php //endforeach; ?>
    <?php endforeach; ?>
  </tbody>
</table>
<div style="float: right;">
  <?php

//First Page link (or inactive if we're at the first page).
    echo(!$pager->isFirstPage() ? '<a href="' . url_for('event/listgroups' . '?page=' . $pager->getFirstPage() . (isset($event) ? '&event=' . urlencode($event->event_name) : '')) . /*$sortAppend . $orderAppend .*/ '" class="buttonText" title="First Page">&lt;&lt;</a>' : '<a class="buttonTextOff">&lt;&lt;</a>');
//Previous Page link (or inactive if we're at the first page).
    echo(!$pager->isFirstPage() ? '<a href="' . url_for('event/listgroups' . '?page=' . $pager->getPreviousPage() . (isset($event) ? '&event=' . urlencode($event->event_name) : '')) . /*$sortAppend . $orderAppend .*/'" class="buttonText" title="Previous Page">&lt;</a>' : '<a class="buttonTextOff">&lt;</a>');
//Next Page link (or inactive if we're at the last page).
    echo(!$pager->isLastPage() ? '<a href="' . url_for('event/listgroups' . '?page=' . $pager->getNextPage() . (isset($event) ? '&event=' . urlencode($event->event_name) : '')) . /*$sortAppend . $orderAppend .*/'" class="buttonText" title="Next Page">&gt;</a>' : '<a class="buttonTextOff">&gt;</a>');
//Last Page link (or inactive if we're at the last page).
    echo(!$pager->isLastPage() ? '<a href="' . url_for('event/listgroups' . '?page=' . $pager->getLastPage() . (isset($event) ? '&event=' . urlencode($event->event_name) : '')) . /*$sortAppend . $orderAppend .*/'" class="buttonText" title="Last Page">&gt;&gt;</a>' : '<a class="buttonTextOff">&gt;&gt;</a>');
  ?>
</div>
<a href="<?php echo url_for('event/newgroup') ?>" class="buttonText" title="New Facility Group">New</a>
