<?php if(isset($missingEvent)) : ?>
<h3>You need a selected and active event to use this page. Please return to the Event Dashboard using the link below, or browse to the appropriate page through the navigation menu.</h3>
<a href="<?php echo url_for('event/index'); ?>" class="linkButton">Event Dashboard</a>
<?php return; ?>
<?php endif; ?>

<?php
            $a = $facilityResourceArray;
            $c = 4;
  use_javascript('jquery.ui.custom.js');
  use_javascript('agMain.js');
  use_stylesheet('jquery/jquery.ui.custom.css');
  //  use_javascript('agModal.js');
  $sortColumn = $sf_request->getGetParameter('sort');
  $sortOrder = $sf_request->getGetParameter('order');
  //  ($sf_request->getParameter('filter')) ? $filterAppend = '&filter=' . $sf_request->getGetParameter('filter') : $filterAppend = '';
  ($sf_request->getParameter('sort')) ? $sortAppend = '&sort=' . $sf_request->getParameter('sort') : $sortAppend = '';
  ($sf_request->getParameter('order')) ? $orderAppend = '&order=' . $sf_request->getParameter('order') : $orderAppend = '';
?>
<h2><?php if (isset($event)) : ?><span class="highlightedText"><?php echo $event->event_name; ?></span><?php endif; ?> Facility Group Management</h2>
<br />
<h3>Facility Resources<?php echo $pager->getFirstIndice() . "-" . $pager->getLastIndice() . " of " . $pager->count() . ((isset($event)) ? ' for the <span class="highlightedText">' . $event->event_name . '</span> Event' : ' for all Events'); ?></h3>
<div id="tableContainer">
  <table class="singleTable" style="width: 700px;">
    <thead>
      <tr>
        <th class="head"></th>
        <th class="head">
          <div class="tableHeaderContent">Group Name</div>
          <?php
          echo($sortColumn == 'group' && $sortOrder == 'ASC' ? '<a href="' . url_for('event/listgroups?sort=group&order=ASC' . (isset($event) ? '&event=' . urlencode($event->event_name) : '')) . '" class="buttonSortSelected" title="ascending">&#x25B2;</a>' : '<a href="' . url_for('event/listgroups?sort=group&order=ASC' . (isset($event) ? '&event=' . urlencode($event->event_name) : '')) . '" class="buttonSort" title="ascending">&#x25B2;</a>');
          echo($sortColumn == 'group' && $sortOrder == 'DESC' ? '<a href="' . url_for('event/listgroups?sort=group&order=DESC' . (isset($event) ? '&event=' . urlencode($event->event_name) : '')) . '" class="buttonSortSelected" title="descending">&#x25BC;</a>' : '<a href="' . url_for('event/listgroups?sort=group&order=DESC' . (isset($event) ? '&event=' . urlencode($event->event_name) : '')) . '" class="buttonSort" title="descending">&#x25BC;</a>');
          ?>
        </th>
        <th class="head">
          <div class="tableHeaderContent">Type</div>
          <?php
          echo($sortColumn == 'type' && $sortOrder == 'ASC' ? '<a href="' . url_for('event/listgroups?sort=type&order=ASC' . (isset($event) ? '&event=' . urlencode($event->event_name) : '')) . '" class="buttonSortSelected" title="ascending">&#x25B2;</a>' : '<a href="' . url_for('event/listgroups?sort=type&order=ASC' . (isset($event) ? '&event=' . urlencode($event->event_name) : '')) . '" class="buttonSort" title="ascending">&#x25B2;</a>');
          echo($sortColumn == 'type' && $sortOrder == 'DESC' ? '<a href="' . url_for('event/listgroups?sort=type&order=DESC' . (isset($event) ? '&event=' . urlencode($event->event_name) : '')) . '" class="buttonSortSelected" title="descending">&#x25BC;</a>' : '<a href="' . url_for('event/listgroups?sort=type&order=DESC' . (isset($event) ? '&event=' . urlencode($event->event_name) : '')) . '" class="buttonSort" title="descending">&#x25BC;</a>');
          ?>
        </th>
        <th class="head">
          <div class="tableHeaderContent">Status</div>
          <?php
          echo($sortColumn == 'status' && $sortOrder == 'ASC' ? '<a href="' . url_for('event/listgroups?sort=status&order=ASC' . (isset($event) ? '&event=' . urlencode($event->event_name) : '')) . '" class="buttonSortSelected" title="ascending">&#x25B2;</a>' : '<a href="' . url_for('event/listgroups?sort=status&order=ASC' . (isset($event) ? '&event=' . urlencode($event->event_name) : '')) . '" class="buttonSort" title="ascending">&#x25B2;</a>');
          echo($sortColumn == 'status' && $sortOrder == 'DESC' ? '<a href="' . url_for('event/listgroups?sort=status&order=DESC' . (isset($event) ? '&event=' . urlencode($event->event_name) : '')) . '" class="buttonSortSelected" title="descending">&#x25BC;</a>' : '<a href="' . url_for('event/listgroups?sort=status&order=DESC' . (isset($event) ? '&event=' . urlencode($event->event_name) : '')) . '" class="buttonSort" title="descending">&#x25BC;</a>');
          ?>
        </th>
        <th class="head">
          <div class="tableHeaderContent">Facility Resource Count</div>
          <?php
          echo($sortColumn == 'count' && $sortOrder == 'ASC' ? '<a href="' . url_for('event/listgroups?sort=count&order=ASC' . (isset($event) ? '&event=' . urlencode($event->event_name) : '')) . '" class="buttonSortSelected" title="ascending">&#x25B2;</a>' : '<a href="' . url_for('event/listgroups?sort=count&order=ASC' . (isset($event) ? '&event=' . urlencode($event->event_name) : '')) . '" class="buttonSort" title="ascending">&#x25B2;</a>');
          echo($sortColumn == 'count' && $sortOrder == 'DESC' ? '<a href="' . url_for('event/listgroups?sort=count&order=DESC' . (isset($event) ? '&event=' . urlencode($event->event_name) : '')) . '" class="buttonSortSelected" title="descending">&#x25BC;</a>' : '<a href="' . url_for('event/listgroups?sort=count&order=DESC' . (isset($event) ? '&event=' . urlencode($event->event_name) : '')) . '" class="buttonSort" title="descending">&#x25BC;</a>');
          ?>
        </th>
        <?php if (!(isset($event))): ?>
        <th class="head">
          <div class="tableHeaderContent">Event</div>
          <?php
            echo($sortColumn == 'event' && $sortOrder == 'ASC' ? '<a href="' . url_for('event/listgroups?sort=event&order=ASC') . '" class="buttonSortSelected" title="ascending">&#x25B2;</a>' : '<a href="' . url_for('event/listgroups?sort=event&order=ASC') . '" class="buttonSort" title="ascending">&#x25B2;</a>');
            echo($sortColumn == 'event' && $sortOrder == 'DESC' ? '<a href="' . url_for('event/listgroups?sort=event&order=DESC') . '" class="buttonSortSelected" title="descending">&#x25BC;</a>' : '<a href="' . url_for('event/listgroups?sort=event&order=DESC') . '" class="buttonSort" title="descending">&#x25BC;</a>');
          ?>
        </th>
        <?php endif; ?>
          </tr>
        </thead>
        <tbody>
<?php foreach ($pager->getResults() as $facilityGroup): ?>
          <tr>
            <td><a href="<?php echo url_for('event/eventfacilityresource/?eventFacResId=' . $facilityGroup['efg_id']); ?>" id="<?php echo $facilityGroup['efg_id']; ?>" class="expander">&#9654;</a></td>
            <td><?php echo $facilityGroup['efg_event_facility_group'] ?></td>
            <td><?php echo $facilityGroup['fgt_facility_group_type'] ?></td>
            <td><a href="<?php echo url_for('event/eventfacilitygroup')?>" class="facilityGroupStatus linkText" id="group_id_<?php echo $facilityGroup['efg_id']; ?>"><?php echo $facilityGroup['fgas_facility_group_allocation_status'] ?></a></td>
            <td><?php echo $facilityGroup['efr_count'] ?></td>
            <?php
              if (!(isset($event))) {
                echo '<td>' . $facilityGroup['e_event_name'] . '</td>';
              }
            ?>
          </tr>
          <tr>
            <td colspan="5">
              <div class="expandable" id="expandable_<?php echo $facilityGroup['efg_id']; ?>">
              </div>
            </td>
          </tr>
<?php endforeach; ?>

              </tbody>
<?php echo javascript_include_tag('agModal.js'); ?>
            </table>
          </div>
<?php if(isset($event_id)): ?>
  <br />
  <a href="<?php echo url_for('event/facilitygroups?event=' . urlencode($event_name)); ?>" class="linkButton" title="Facilities and Resources">Manage Standby Facility Groups</a><br/>
<?php endif; ?>
          <div class="floatRight">
  <?php
//First Page link (or inactive if we're at the first page).
              echo(!$pager->isFirstPage() ? '<a href="' . url_for('event/listgroups?page=' . $pager->getFirstPage() . (isset($event) ? '&event=' . urlencode($event->event_name) : '') . $sortAppend . $orderAppend) . '" class="buttonText" title="First Page">&lt;&lt;</a>' : '<a class="buttonTextOff">&lt;&lt;</a>');
//Previous Page link (or inactive if we're at the first page).
              echo(!$pager->isFirstPage() ? '<a href="' . url_for('event/listgroups?page=' . $pager->getPreviousPage() . (isset($event) ? '&event=' . urlencode($event->event_name) : '') . $sortAppend . $orderAppend) . '" class="buttonText" title="Previous Page">&lt;</a>' : '<a class="buttonTextOff">&lt;</a>');
//Next Page link (or inactive if we're at the last page).
              echo(!$pager->isLastPage() ? '<a href="' . url_for('event/listgroups?page=' . $pager->getNextPage() . (isset($event) ? '&event=' . urlencode($event->event_name) : '') . $sortAppend . $orderAppend) . '" class="buttonText" title="Next Page">&gt;</a>' : '<a class="buttonTextOff">&gt;</a>');
//Last Page link (or inactive if we're at the last page).
              echo(!$pager->isLastPage() ? '<a href="' . url_for('event/listgroups?page=' . $pager->getLastPage() . (isset($event) ? '&event=' . urlencode($event->event_name) : '') . $sortAppend . $orderAppend) . '" class="buttonText" title="Last Page">&gt;&gt;</a>' : '<a class="buttonTextOff">&gt;&gt;</a>');
  ?>
            </div>