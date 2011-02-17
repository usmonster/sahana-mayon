<?php
  use_javascript('jquery.ui.custom.js');
  use_stylesheet('jquery/jquery.ui.custom.css');
//  use_javascript('agModal.js');
  $sortColumn = $sf_request->getGetParameter('sort');
  $sortOrder = $sf_request->getGetParameter('order');
//  ($sf_request->getParameter('filter')) ? $filterAppend = '&filter=' . $sf_request->getGetParameter('filter') : $filterAppend = '';
  ($sf_request->getParameter('sort')) ? $sortAppend = '&sort=' . $sf_request->getParameter('sort') : $sortAppend = '';
  ($sf_request->getParameter('order')) ? $orderAppend = '&order=' . $sf_request->getParameter('order') : $orderAppend = '';
?>
<h2><?php if(isset($event)) {echo '<span style="color: #ff8f00">' . $event->event_name . ' </span>';} ?> Facilities Management</h2>
<br />
<h3>Facilities <?php echo $pager->getFirstIndice() . "-" . $pager->getLastIndice() . " of " . $pager->count() . ((isset($event)) ? ' for the <span style="color: #ff8f00">' . $event->event_name . '</span> Event' : ' for all Events'); ?></h3>
<div id="tableContainer">
<table class="singleTable">
  <thead>
    <tr>
      <th class="head">
        <div class="tableHeaderContent">Facility Group</div>
        <?php
          echo($sortColumn =='group' && $sortOrder == 'ASC' ? '<a href="' . url_for('event/listgroups?sort=group&order=ASC' . (isset($event) ? '&event=' . urlencode($event->event_name) : '')) . '" class="buttonSortSelected" title="ascending">&#x25B2;</a>' : '<a href="' . url_for('event/listgroups?sort=group&order=ASC' . (isset($event) ? '&event=' . urlencode($event->event_name) : '')) . '" class="buttonSort" title="ascending">&#x25B2;</a>');
          echo($sortColumn =='group' && $sortOrder == 'DESC' ? '<a href="' . url_for('event/listgroups?sort=group&order=DESC' . (isset($event) ? '&event=' . urlencode($event->event_name) : '')) . '" class="buttonSortSelected" title="descending">&#x25BC;</a>' : '<a href="' . url_for('event/listgroups?sort=group&order=DESC' . (isset($event) ? '&event=' . urlencode($event->event_name) : '')) . '" class="buttonSort" title="descending">&#x25BC;</a>');
        ?>
      </th>
      <th class="head">
        <div class="tableHeaderContent">Facility Name & Resource Type</div>
        <?php
          echo($sortColumn =='name' && $sortOrder == 'ASC' ? '<a href="' . url_for('event/listgroups?sort=name&order=ASC' . (isset($event) ? '&event=' . urlencode($event->event_name) : '')) . '" class="buttonSortSelected" title="ascending">&#x25B2;</a>' : '<a href="' . url_for('event/listgroups?sort=name&order=ASC' . (isset($event) ? '&event=' . urlencode($event->event_name) : '')) . '" class="buttonSort" title="ascending">&#x25B2;</a>');
          echo($sortColumn =='name' && $sortOrder == 'DESC' ? '<a href="' . url_for('event/listgroups?sort=name&order=DESC' . (isset($event) ? '&event=' . urlencode($event->event_name) : '')) . '" class="buttonSortSelected" title="descending">&#x25BC;</a>' : '<a href="' . url_for('event/listgroups?sort=name&order=DESC' . (isset($event) ? '&event=' . urlencode($event->event_name) : '')) . '" class="buttonSort" title="descending">&#x25BC;</a>');
        ?>
      </th>
      <th class="head">
        <div class="tableHeaderContent">Facility Code</div>
        <?php
          echo($sortColumn =='code' && $sortOrder == 'ASC' ? '<a href="' . url_for('event/listgroups?sort=code&order=ASC' . (isset($event) ? '&event=' . urlencode($event->event_name) : '')) . '" class="buttonSortSelected" title="ascending">&#x25B2;</a>' : '<a href="' . url_for('event/listgroups?sort=code&order=ASC' . (isset($event) ? '&event=' . urlencode($event->event_name) : '')) . '" class="buttonSort" title="ascending">&#x25B2;</a>');
          echo($sortColumn =='code' && $sortOrder == 'DESC' ? '<a href="' . url_for('event/listgroups?sort=code&order=DESC' . (isset($event) ? '&event=' . urlencode($event->event_name) : '')) . '" class="buttonSortSelected" title="descending">&#x25BC;</a>' : '<a href="' . url_for('event/listgroups?sort=code&order=DESC' . (isset($event) ? '&event=' . urlencode($event->event_name) : '')) . '" class="buttonSort" title="descending">&#x25BC;</a>');
        ?>
      </th>
      <th class="head">
        <div class="tableHeaderContent">Facility Status</div>
        <?php
          echo($sortColumn =='status' && $sortOrder == 'ASC' ? '<a href="' . url_for('event/listgroups?sort=status&order=ASC' . (isset($event) ? '&event=' . urlencode($event->event_name) : '')) . '" class="buttonSortSelected" title="ascending">&#x25B2;</a>' : '<a href="' . url_for('event/listgroups?sort=status&order=ASC' . (isset($event) ? '&event=' . urlencode($event->event_name) : '')) . '" class="buttonSort" title="ascending">&#x25B2;</a>');
          echo($sortColumn =='status' && $sortOrder == 'DESC' ? '<a href="' . url_for('event/listgroups?sort=status&order=DESC' . (isset($event) ? '&event=' . urlencode($event->event_name) : '')) . '" class="buttonSortSelected" title="descending">&#x25BC;</a>' : '<a href="' . url_for('event/listgroups?sort=status&order=DESC' . (isset($event) ? '&event=' . urlencode($event->event_name) : '')) . '" class="buttonSort" title="descending">&#x25BC;</a>');
        ?>
      </th>
      <th class="head">
        <div class="tableHeaderContent">Facility Activation Time</div>
        <?php
          echo($sortColumn =='time' && $sortOrder == 'ASC' ? '<a href="' . url_for('event/listgroups?sort=time&order=ASC' . (isset($event) ? '&event=' . urlencode($event->event_name) : '')) . '" class="buttonSortSelected" title="ascending">&#x25B2;</a>' : '<a href="' . url_for('event/listgroups?sort=time&order=ASC' . (isset($event) ? '&event=' . urlencode($event->event_name) : '')) . '" class="buttonSort" title="ascending">&#x25B2;</a>');
          echo($sortColumn =='time' && $sortOrder == 'DESC' ? '<a href="' . url_for('event/listgroups?sort=time&order=DESC' . (isset($event) ? '&event=' . urlencode($event->event_name) : '')) . '" class="buttonSortSelected" title="descending">&#x25BC;</a>' : '<a href="' . url_for('event/listgroups?sort=time&order=DESC' . (isset($event) ? '&event=' . urlencode($event->event_name) : '')) . '" class="buttonSort" title="descending">&#x25BC;</a>');
        ?>
      </th>
      <th class="head">
        <div class="tableHeaderContent">Facility Group Type</div>
        <?php
          echo($sortColumn =='type' && $sortOrder == 'ASC' ? '<a href="' . url_for('event/listgroups?sort=type&order=ASC' . (isset($event) ? '&event=' . urlencode($event->event_name) : '')) . '" class="buttonSortSelected" title="ascending">&#x25B2;</a>' : '<a href="' . url_for('event/listgroups?sort=type&order=ASC' . (isset($event) ? '&event=' . urlencode($event->event_name) : '')) . '" class="buttonSort" title="ascending">&#x25B2;</a>');
          echo($sortColumn =='type' && $sortOrder == 'DESC' ? '<a href="' . url_for('event/listgroups?sort=type&order=DESC' . (isset($event) ? '&event=' . urlencode($event->event_name) : '')) . '" class="buttonSortSelected" title="descending">&#x25BC;</a>' : '<a href="' . url_for('event/listgroups?sort=type&order=DESC' . (isset($event) ? '&event=' . urlencode($event->event_name) : '')) . '" class="buttonSort" title="descending">&#x25BC;</a>');
        ?>
      </th>
      <?php if(!(isset($event))): ?> 
        <th class="head">
        <div class="tableHeaderContent">Event</div>
        <?php
          echo($sortColumn =='event' && $sortOrder == 'ASC' ? '<a href="' . url_for('event/listgroups?sort=event&order=ASC') . '" class="buttonSortSelected" title="ascending">&#x25B2;</a>' : '<a href="' . url_for('event/listgroups?sort=event&order=ASC') . '" class="buttonSort" title="ascending">&#x25B2;</a>');
          echo($sortColumn =='event' && $sortOrder == 'DESC' ? '<a href="' . url_for('event/listgroups?sort=event&order=DESC') . '" class="buttonSortSelected" title="descending">&#x25BC;</a>' : '<a href="' . url_for('event/listgroups?sort=event&order=DESC') . '" class="buttonSort" title="descending">&#x25BC;</a>');
        ?>
        </th>
      <?php endif;?>
    </tr>
  </thead>
  <tbody>
    <?php foreach($pager->getResults() as $facility): ?>
      <?php// foreach ($facilityGroup as $facility): ?>
      <tr>
        <td><a href="<?php echo url_for('event/groupdetail?event=' . urlencode($facility['e_event_name']) . '&group=' . urlencode($facility['efg_event_facility_group'])) ?>" class="linkText modalTrigger" title="Facility Group <?php echo $facility['efg_event_facility_group']; ?> for the <?php echo $facility['e_event_name']; ?> Scenario"><?php echo $facility['efg_event_facility_group'] ?></a></td>
        <td><?php echo $facility['f_facility_name'] . ": " . $facility['frt_facility_resource_type']; ?></td>
        <td><?php echo $facility['f_facility_code']; ?></td>
        <td class="modalReloadable"><?php echo $facility['ras_facility_resource_allocation_status']; ?></td>
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
<?php echo javascript_include_tag('agModal.js'); ?>
</table>
</div>
<div style="float: right;">
  <?php

//First Page link (or inactive if we're at the first page).
    echo(!$pager->isFirstPage() ? '<a href="' . url_for('event/listgroups?page=' . $pager->getFirstPage() . (isset($event) ? '&event=' . urlencode($event->event_name) : '') . $sortAppend . $orderAppend ) . '" class="buttonText" title="First Page">&lt;&lt;</a>' : '<a class="buttonTextOff">&lt;&lt;</a>');
//Previous Page link (or inactive if we're at the first page).
    echo(!$pager->isFirstPage() ? '<a href="' . url_for('event/listgroups?page=' . $pager->getPreviousPage() . (isset($event) ? '&event=' . urlencode($event->event_name) : '') . $sortAppend . $orderAppend) . '" class="buttonText" title="Previous Page">&lt;</a>' : '<a class="buttonTextOff">&lt;</a>');
//Next Page link (or inactive if we're at the last page).
    echo(!$pager->isLastPage() ? '<a href="' . url_for('event/listgroups?page=' . $pager->getNextPage() . (isset($event) ? '&event=' . urlencode($event->event_name) : ''). $sortAppend . $orderAppend) . '" class="buttonText" title="Next Page">&gt;</a>' : '<a class="buttonTextOff">&gt;</a>');
//Last Page link (or inactive if we're at the last page).
    echo(!$pager->isLastPage() ? '<a href="' . url_for('event/listgroups?page=' . $pager->getLastPage() . (isset($event) ? '&event=' . urlencode($event->event_name) : '') . $sortAppend . $orderAppend) . '" class="buttonText" title="Last Page">&gt;&gt;</a>' : '<a class="buttonTextOff">&gt;&gt;</a>');
  ?>
</div>
<a href="<?php echo url_for('event/newgroup') ?>" class="buttonText" title="New Facility Group">New</a>