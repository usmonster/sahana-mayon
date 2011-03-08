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
  <?php
    foreach ($hits as $hit) {


      echo get_partial('search/result', array(
        'obj' => $results[$hit->model][$hit->pk],
        'pk' => $hit,
        'target_module' => $target_module)
      );

      //  echo get_partial($hit->model, array(
      //    'obj' => $results[$hit->model][$hit->pk],
      //    'pk' => $hit)
      //  );
    }
  ?>
</table>