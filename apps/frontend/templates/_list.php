<?php
$thisUrl = url_for('staff/list');
$ascArrow = '&#x25B2;';
$descArrow = '&#x25BC;';
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