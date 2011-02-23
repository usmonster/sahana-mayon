<h2>Staff Resource Pool</h2> <br>
<h3>for the <span class="highlightedText"><?php echo $eventName ?> </span> Event.</h3>
<p> Here you can tweak the staff resource pools you have defined in the planning phase. </p>


<?php if (count($saved_searches) > 0) {
 ?>
  <h3>Existing Saved Searches</h3>
  <table>
    <thead>
      <tr>
        <th>Scenario Base</th>
        <th>Search Name</th>
        <th>Search Type</th>
      </tr>
    </thead>
    <tbody>
<?php foreach ($saved_searches as $saved_search): ?>
    <tr>
      <td><?php echo $saved_search->getAgScenario()->getScenario() ?></a></td>
      <td><a href="<?php echo url_for('event/staffpool?id=' . $event_id) . '/' . $saved_search->getId() ?> "><?php echo $saved_search->getAgLuceneSearch()->query_name ?></a></td>
      <td><?php echo $saved_search->getAgLuceneSearch()->getAgLuceneSearchType() ?></td>
    </tr>
<?php endforeach; ?>
  </tbody>
</table>
<?php } ?>