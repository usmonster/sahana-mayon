<?php
  use_javascript('agMain.js');
  use_javascript('jquery.ui.custom.js');
  use_stylesheet('jquery/jquery.ui.custom.css');
  use_stylesheet('jquery/mayon.jquery.ui.css');
  use_javascript('agTooltip.js');
?>
<h2>Staff Resource Pool: <span class="highlightedText"><?php echo $scenarioName ?> </span></h2>
<?php
include_partial('wizard', array('wizardDiv' => $wizardDiv));
?>
<p>The staff is built from a set of searches.  Creating from searches allows you to create custom
deployment of staff based on the scale of the plan and response.</p>

<?php if (count($saved_searches) > 0) {
?><div class="infoHolder" style="width:750px;">
  <h3>Saved Searches<a href="<?php echo url_for('@wiki') . '/doku.php?id=tooltip:staff_pool_searches&do=export_xhtmlbody' ?>" class="tooltipTrigger" title="Staff Pool Searches">?</a></h3>
  <table class="blueTable">
    <thead>
      <tr class="head">
        <th>Search Name</th>
        <th>Search Conditions</th>
      </tr>
      <tr>
        <th style="float:right;">
          <span class="highlightedText"><?php echo $total_staff ?></span> total staff in system, <span class="highlightedText"><?php echo $scenario_staff_count ?></span> Staff Members in pool
        <th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($saved_searches as $saved_search): ?>
      <tr>
        <td><a href="<?php echo url_for('scenario/staffpool?id=' . $scenario_id) . '?search_id=' .
          $saved_search['search_id'] ?> " class="linkButton"><?php echo $saved_search->agSearch['search_name'] ?></a></td>
        <td><?php echo agSearchHelper::searchConditionsToString($saved_search->agSearch['id']); ?></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php } ?>
<?php
    if (!isset($search_id)) {
      $search_id = NULL;
    } else {
      $search_id = $search_id;
      //this could be more elegant.
    }
?>
    <br />

<?php include_partial('poolform', array('poolform' => $poolform, 'filterForm' => $filterForm, 'scenario_id' => $scenario_id, 'search_id' => $search_id)) ?>

    <div id="searchresults" class="infoHolder">

      <!--sometimes this will fail -->
  <?php if (isset($pager)) {
 ?>

  <?php
      //include_partial('search/search', array('hits' => $hits, 'searchquery' => $searchquery, 'results' => $results, 'target_module' => $target_module))
      $displayColumns = array(
        'id' => array('title' => '', 'sortable' => false),
        'fn' => array('title' => 'First Name', 'sortable' => false),
        'ln' => array('title' => 'Last Name', 'sortable' => false),
        'agency' => array('title' => 'Agency', 'sortable' => true),
        'classification' => array('title' => 'Classification', 'sortable' => true),
        'phones' => array('title' => 'Phone Contact(s)', 'sortable' => true),
        'emails' => array('title' => 'Email Contact(s)', 'sortable' => true),
        'staff_status' => array('title' => 'Status', 'sortable' => false),
      );

//pager comes in from the action

      $order = null;
      $sort = null;
      $filter = null;
      //the above three lines are in place to supress warnings until SOF is functional
      include_partial('global/list', array('sf_request' => $sf_request,
        'displayColumns' => $displayColumns,
        'pager' => $pager,
        'order' => $order,
        'sort' => $sort,
        'status' => $status,
        'target_module' => 'staff',
        'caption' => 'Search Results',
        'widgets' => array()
          )
      );
    } ?>
</div>