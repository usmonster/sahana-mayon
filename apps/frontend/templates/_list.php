<?php
$thisUrl = url_for($targetModule . '/' . $targetAction);

$listParams = $sf_data->getRaw('listParams');
$sortOrders = array('ASC' => '&#x25B2;', 'DESC' => '&#x25BC;');

$firstPage = $pager->getFirstPage();
$lastPage = $pager->getLastPage();

$pageVars = array(
  '&lt;&lt;' => array($pager->getFirstPage(), $firstPage, 'First Page'),
  '&lt;' => array($pager->getPreviousPage(), $firstPage, 'Previous Page'),
  '&gt;' => array($pager->getNextPage(), $lastPage, 'Next Page'),
  '&gt;&gt;' => array($pager->getLastPage(), $lastPage, 'Last Page'),
  );

if (!(isset($caption)))
  $caption = 'List';
//the above two lines are in place to supress warnings until sort is corrected
?>
<table class="staffTable">
  <caption><?php echo $caption ?>

    <?php
    if (count($widgets) > 0) {
      foreach ($widgets as $widget) {
    ?>
        <div class="floatRight" style="font-size: 12px;">
          <form name="statusForm" action="<?php echo $thisUrl ?>" method="get">
        <?php echo $widget ?>
        <?php if (isset($listParams['sort'])): ?>
          <input type="hidden" name="sort" value="<?php echo $listParams['sort'] ?>">
        <?php endif ?>
        <?php if (isset($listParams['order'])): ?>
            <input type="hidden" name="order" value="<?php echo $listParams['order'] ?>">
        <?php endif ?>

          </form></div>
    <?php
          }
        }
    ?>
      </caption>
      <thead>
        <tr class="head">
      <?php
        $listhead = "";
        foreach ($displayColumns as $column => $columnCaption) {
          $listhead .= '  <th>' . $columnCaption['title'];
          if ($columnCaption['sortable']) {

            // get the sort orders
            foreach ($sortOrders as $sortOrder => $character) {
              
              // match if this is the one currently selected to find highlighted css class
              if ((strtolower($listParams['sort']) == strtolower($column)) &&
                  (strtolower($listParams['order']) == strtolower($sortOrder))) {
                $class = 'buttonSortSelected';
              } else {
                $class = 'buttonSort';
              }

              // mock up params as-though sort and order were introduced
              $sortUrlParams = array_merge($listParams, array('sort' => $column, 'order' => $sortOrder));
              
              // echo the link
              $listhead .= link_to($character, $thisUrl . '?' . http_build_query($sortUrlParams),
                'class=' . $class);
            }
          }
          if ($columnCaption->offsetExists('tooltip')) {
            $listhead .= '<a href="' . $columnCaption['tooltip'] . '" class="tooltipTrigger">?</a>';
          }
          $listhead .= '</th>';
        }
        echo $listhead;
      ?>
      </tr>
    </thead>
    <tbody>
<?php
        echo get_partial('global/row', array(
            'data' => $data,
            //'widget' => $widget,
            'displayColumns' => $displayColumns,
            'targetModule' => $targetModule
        ));
?>

      </tbody>
      <tfoot class="tFootInfo">
        <tr>
          <td colspan=<?php echo count($displayColumns) + 1 ?>>
<?php
        // Output the current staff members being shown, as well total number in the list.
        // The if prevents getting a 1-0 display indicator if there is an empty list.
        $totalRecords = $pager->getNumResults();
        $firstIndice = ($totalRecords === 0) ? 0 : $pager->getFirstIndice();
        $lastIndice = $pager->getLastIndice();
        echo $firstIndice . " - " . $lastIndice . " of " . $totalRecords;
?>
      </td>
    </tr>
    <tr>
      <td colspan=<?php echo count($displayColumns) + 1 ?>>
<?php
        $listFoot = '<div class="floatRight">';
        $currentPage = $pager->getPage();

        foreach ($pageVars as $character => $values) {
          if (!isset($values[1]) || $currentPage == $values[1]) {
            $listFoot .= '<a class="buttonTextOff">' . $character . '</a>';
          } else {
            $listFoot .= link_to($character, $thisUrl . '?' .
              http_build_query(array_merge($listParams, array('page' => $values[0]))),
              'class="buttonText" title="' . $values[2] . '"');
          }
        }
        $listFoot .= '</div>';

        echo $listFoot;
?>
      </td>
    </tr>
  </tfoot>
</table>
