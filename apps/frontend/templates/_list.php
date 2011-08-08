<?php
  $thisUrl = url_for($targetModule . '/' . $targetAction);
  if (isset($params))
  {
    $thisUrl .= $params;
  }
  $ascArrow = '&#x25B2;';
  $descArrow = '&#x25BC;';
  ($status != 'active') ? $statusAppend = '?status=' . $status : $statusAppend = '?status=active';
  ($sort != null) ? $sortAppend = '&sort=' . $sort : $sortAppend = '';
  ($order != null) ? $orderAppend = '&order=' . $order : $orderAppend = '';

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
            <?php if(isset($sort)): ?>
              <input type="hidden" name="sort" value="<?php echo $sort ?>">
           <?php endif ?>
            <?php if(isset($order)): ?>
              <input type="hidden" name="order" value="<?php echo $order ?>">
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
        foreach ($displayColumns as $column => $columnCaption)
        {
          $listhead .= '  <th>' . $columnCaption['title'];
          if ($columnCaption['sortable'])
          {
            $listhead .= $sort == $column && $order == 'ASC' ? '<a href="' . $thisUrl . '?status=' . $status . '&sort=' . $column . '&order=ASC" class="buttonSortSelected" title="ascending">' . $ascArrow . '</a>' : '<a href="' . $thisUrl . '?status=' . $status . '&sort=' . $column . '&order=ASC" class="buttonSort" title="ascending">' . $ascArrow . '</a>';
            $listhead .= $sort == $column && $order == 'DESC' ? '<a href="' . $thisUrl . '?status=' . $status . '&sort=' . $column . '&order=DESC" class="buttonSortSelected" title="descending">' . $descArrow . '</a>' : '<a href="' . $thisUrl . '?status=' . $status . '&sort=' . $column . '&order=DESC" class="buttonSort" title="descending">' . $descArrow . '</a>';
          }
          if ($columnCaption->offsetExists('tooltip'))
          {
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
          $listfoot = '<div class="floatRight">';
          $currentPage = $pager->getPage();
          $firstPage = $pager->getFirstPage();
          $lastPage = $pager->getLastPage();
          $seperator = ($targetAction == 'list') ? '?' : '&';

          //First Page link (or inactive if we're at the first page).
          $listfoot .= ( ($currentPage == $firstPage) ? '<a class="buttonTextOff">&lt;&lt;</a>' : '<a href="' . $thisUrl . $seperator . 'page=' . $firstPage . '&status=' . $status . $sortAppend . $orderAppend . '" class="buttonText" title="First Page">&lt;&lt;</a>');
          //Previous Page link (or inactive if we're at the first page).
          $listfoot .= ( ($currentPage == $firstPage) ? '<a class="buttonTextOff">&lt;</a>' : '<a href="' . $thisUrl . $seperator . 'page=' . $pager->getPreviousPage() . '&status=' . $status . $sortAppend . $orderAppend . '" class="buttonText" title="Previous Page">&lt;</a>');
          //Next Page link (or inactive if we're at the last page).
          $listfoot .= ( ($currentPage == $lastPage) ? '<a class="buttonTextOff">&gt;</a>' : '<a href="' . $thisUrl . $seperator . 'page=' . $pager->getNextPage() . '&status=' . $status . $sortAppend . $orderAppend . '" class="buttonText" title="Next Page">&gt;</a>');
          //Last Page link (or inactive if we're at the last page).
          $listfoot .= ( ($currentPage == $lastPage) ? '<a class="buttonTextOff">&gt;&gt;</a>' : '<a href="' . $thisUrl . $seperator . 'page=' . $pager->getLastPage() . '&status=' . $status . $sortAppend . $orderAppend . '" class="buttonText" title="Last Page">&gt;&gt;</a>');
          $listfoot .= '</div>';

          echo $listfoot;
        ?>
      </td>
    </tr>
  </tfoot>
</table>
