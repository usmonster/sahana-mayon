<h3>Add Staff to shift id: <span class="highlightedText"><?php echo $shift_id ?></span></h3>
<?php include_partial('staffshiftform', array('filterForm' => $filterForm, 'shift_id' => $shift_id, 'event_id' => $event_id, 'xmlHttpRequest' => $xmlHttpRequest)) ?>
  <div id="searchresults">
    <div class="modalReloadable">
    <!--sometimes this will fail -->
  <?php if (isset($searchquery)) { ?>
    <hr />
  <?php include_partial('search/resultform', array('hits' => $hits, 'searchquery' => $searchquery, 'results' => $results, 'widget' => $widget, 'shift_id' => $shift_id, 'event_id' => $event_id));

      } ?>
    </div>
</div>