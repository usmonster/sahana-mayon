<h2>Staff Resource Pool</h2> <br>
Your staff resource pool is essentially a set of searches that let you refine who is available to deploy.
<p> On this page you decide what staff to pull from and give them weight.  This is all done through search.</p>
<?php if(count($saved_searches) > 0){ ?>
<h3>Existing Saved Searches</h3>
<table>
  <thead>
    <tr>
      <th>Search Name</th>
      <th>Search Type</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($saved_searches as $saved_search): ?>
      <tr>
        <td><a href="<?php echo url_for('scenario/staffpool?id=' . $scenario_id) . '?search_id=' . $saved_search->getId() ?> "><?php echo $saved_search->getAgLuceneSearch()->query_name ?></a></td>
        <td><?php echo $saved_search->getAgLuceneSearch()->getAgLuceneSearchType() ?></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
<?php } ?>
<?php
      if (!isset($search_id)) {
        $search_id = NULL;
      } else {
        $search_id = $search_id;
        //this could be more elegant.
      }
?>
<hr />
<h3>Staff Pool Definition</h3>
<?php include_partial('poolform', array('poolform' => $poolform, 'filterForm' => $filterForm, 'scenario_id' => $scenario_id, 'search_id' => $search_id)) ?>

      <div id="searchresults">

        <!--sometimes this will fail -->
  <?php if (isset($searchquery)) {
 ?>
        <hr />
  <?php include_partial('global/search', array('hits' => $hits, 'searchquery' => $searchquery, 'results' => $results)) ?>
<?php } ?>
</div>