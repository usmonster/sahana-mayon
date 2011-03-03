<table class="staffTable">
  <h3>Search Results:
    <?php
    $count = count($hits);
    if ($count == 0) {
      echo "No results found for '<strong>" . $searchquery . "</strong>'";
    } elseif ($count == 1) {
      echo "1 result found for '<strong>" . $searchquery . "</strong>':";
    } else {
      echo sprintf("%d results found", $count) . " for '<strong>" . $searchquery . "</strong>':";
    } ?></h3>

<form action="<?php echo url_for('event/staffshift?id=' . $event_id) . '/' . $shift_id ?>" method="post" name="resultform">
  <?php
  
    foreach ($hits as $hit) {
      //  print_r($hit->model);
      //  print_r($results);

        echo get_partial('search/formresult', array(
        'obj' => $results[$hit->model][$hit->pk],
        'pk' => $hit,
        'widget' => $widget)
      );
/**<tr>
<?php foreach(array_keys($data) as $header){
  echo $header;
}
  can we do something like this, or headers are going to be unreliable?
 *
 */
      //  echo get_partial($hit->model, array(
      //    'obj' => $results[$hit->model][$hit->pk],
      //    'pk' => $hit)
      //  );
    }
  ?>
  <input type="submit" name="Add" value="Add" id="add">
</form>

</table>
