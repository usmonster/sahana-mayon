<?php
  use_javascript('agMain.js');
  use_javascript('jquery.ui.custom.js');
  use_stylesheet('jquery/jquery.ui.custom.css');
  use_stylesheet('jquery/mayon.jquery.ui.css');
  ?>
<h2>Staff Resource Pool: <span class="highlightedText"><?php echo $scenarioName ?> </span></h2>
<?php
  include_partial('wizard', array('wizardDiv' => $wizardDiv));
?>
<h4>Create searches to build the staff pool for the <span class="highlightedText"><?php echo $scenarioName;
?> </span> scenario.</h4>
<p>The staff pool is built from a set of searches.  Creating from searches allows you to create custom
deployment of staff based on the scale of the plan and response.</p>

<?php if (count($saved_searches) > 0): ?>

<div class="infoHolder" style="width:750px;">
  <h3>Saved Search Summary</h3>
  <br />
  <table class="blueTable">
    <thead>
      <tr class="head">
        <th>Staff Resource Type</th>
        <th>Count</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($scenarioStaffByResourceCount AS $type => $count): ?>
      <tr>
        <td><?php echo ucwords(strtolower($type)); ?></td>
        <td><?php echo $count; ?></td>
      </tr>
      <?php endforeach; ?>
      <tr class="noborder">
        <td colspan="2"></td>
      </tr>
      <?php foreach ($summaryCount AS $type => $count): ?>
      <tr>
        <td><?php echo ucwords(strtolower($type)); ?></td>
        <td><?php echo $count; ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<div class="infoHolder" style="width:750px;">
  <h3>Saved Searches<a href="<?php echo url_for('@wiki') . '/doku.php?id=tooltip:staff_pool_searches&do=export_xhtmlbody' ?>" class="tooltipTrigger" title="Staff Pool Searches">?</a></h3>
      <p class="highlightedText">Searches displayed in deployment order.</p>
  <table class="blueTable">
    <thead>
      <tr class="head">
        <th>Search Name</th>
        <th>Search Conditions</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($saved_searches as $saved_search): ?>
      <tr>
        <td><a href="<?php echo url_for('scenario/staffpool?id=' . $scenario_id) . '?search_id=' .
          $saved_search['search_id'] ?> " class="continueButton"><?php echo $saved_search->agSearch['search_name'] ?></a></td>
        <td><?php echo agSearchHelper::searchConditionsToString($saved_search->agSearch['id']); ?></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php endif; ?>
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

<?php if (isset($previewStaffCountResults)): ?>
  <div id="searchresults" class="infoHolder">
  <h3>Search Preview:</h3>
  <br />
  <?php $previewStaffCountResults = $sf_data->getRaw('previewStaffCountResults'); ?>
  <?php if (empty($previewStaffCountResults)): ?>
    No match found.
  <?php else: ?>
    <table class="blueTable">
      <thead>
        <tr class="head">
          <th>Staff Resource Type</th>
          <th>Match Found</th>
        </tr>
      </thead>
      <tbody>
    <?php foreach($previewStaffCountResults AS $staffResourceCount): ?>
        <tr>
          <td><?php echo ucwords(strtolower($staffResourceCount[0])); ?></td>
          <td><?php echo $staffResourceCount[1]; ?></td>
        </tr>
    <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
  </div>
<?php endif; ?>