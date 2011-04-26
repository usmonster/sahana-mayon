<?php
$thisUrl = url_for($target_module .'/list');
$ascArrow = '&#x25B2;';
$descArrow = '&#x25BC;';

$orderAppend = '';
$sortAppend = '';
//the above two lines are in place to supress warnings until sort is corrected
?>
<table class="staffTable">

  <thead>
    <tr class="head">
<?php
$listhead = "";
foreach ($displayColumns as $column => $columnCaption) {

  $listhead .= '  <th>' . $columnCaption['title'];
  if ($columnCaption['sortable']) {
    $listhead .= $sort == $column && $order == 'ASC' ? '<a href="' . $thisUrl . '?sort=' . $column . '&order=ASC" class="buttonSortSelected" title="ascending">' . $ascArrow . '</a>' : '<a href="' . $thisUrl . '?sort=' . $column . '&order=ASC" class="buttonSort" title="ascending">' . $ascArrow . '</a>';
    $listhead .= $sort == $column && $order == 'DESC' ? '<a href="' . $thisUrl . '?sort=' . $column . '&order=DESC" class="buttonSortSelected" title="descending">' . $descArrow . '</a>' : '<a href="' . $thisUrl . '?sort=' . $column . '&order=DESC" class="buttonSort" title="descending">' . $descArrow . '</a>';
  }
  $listhead .= '</th>';
}
echo $listhead;
?>
    </tr>
  </thead>
  <tbody>
<?php
foreach ($pager->getResults() as $result) {
  echo get_partial('global/row', array(
    'obj' => $result,
    //'widget' => $widget,
    'displayColumns' => $displayColumns,
    'target_module' => $target_module
  ));
}

?>

  </tbody>
</table>
<?php
    $listfoot = '<div class="floatRight">';
//
//First Page link (or inactive if we're at the first page).
    $listfoot .= ( !$pager->isFirstPage() ? '<a href="' . $thisUrl . '?page=' . $pager->getFirstPage() . $sortAppend . $orderAppend . '" class="buttonText" title="First Page">&lt;&lt;</a>' : '<a class="buttonTextOff">&lt;&lt;</a>');
//Previous Page link (or inactive if we're at the first page).
    $listfoot .= ( !$pager->isFirstPage() ? '<a href="' . $thisUrl . '?page=' . $pager->getPreviousPage() . $sortAppend . $orderAppend . '" class="buttonText" title="Previous Page">&lt;</a>' : '<a class="buttonTextOff">&lt;</a>');
//Next Page link (or inactive if we're at the last page).
    $listfoot .= ( !$pager->isLastPage() ? '<a href="' . $thisUrl . '?page=' . $pager->getNextPage() . $sortAppend . $orderAppend . '" class="buttonText" title="Next Page">&gt;</a>' : '<a class="buttonTextOff">&gt;</a>');
//Last Page link (or inactive if we're at the last page).
    $listfoot .= ( !$pager->isLastPage() ? '<a href="' . $thisUrl . '?page=' . $pager->getLastPage() . $sortAppend . $orderAppend . '" class="buttonText" title="Last Page">&gt;&gt;</a>' : '<a class="buttonTextOff">&gt;&gt;</a>');
    $listfoot .= '</div>';

    // Commented out $listheader here. It's declaration is commented above. Ask Charles.

    echo $listfoot;
    ?>