<table class="staffTable">
    <caption>Organizations
    <?php
    // Output the current organizations being shown, as well total number in the list.
    echo $pager->getFirstIndice() . "-" . $pager->getLastIndice() . " of " . $pager->count();
    ?>
  </caption>

  <thead>
    <tr class="head">
<?php foreach ($columnHeaders as $column => $columnCaption): ?>
  <th>
    <?php
      // echo the column header
      $header = ucwords($columnCaption['title']);
      echo $header;

      // echo the tooltip if appropriate
      if (isset($columnCaption['tooltip']))
      {
        echo '<a href="' . url_for('@wiki') . '/doku.php?id=tooltip:' . $columnCaption['tooltip'] . '&do=export_xhtmlbody" class="tooltipTrigger" title="' . $header . ' Help">?</a>';
      }

      // echo sortable columns (if relevant)
      if (!empty($columnCaption['sortable']))
      {
        foreach (array('ASC' => '&#x25B2;', 'DESC' => '&#x25BC;') as $order => $orderChar) {
          $class = ($sortColumn == $column && $sortOrder == $order) ? 'buttonSortSelected' : 'buttonSort';
          $colParams = $sf_data->getRaw('listParams');
          $colParams['sort'] = $column;
          $colParams['order'] = $order;
          $url = $this->moduleName . '/' . $this->actionName . '?' . http_build_query($colParams);

          echo link_to($orderChar, $url, array('class' => $class, 'title' => 'Sort by ' . $header . ' (' . $order . ')'));
        }
      }
    ?>
  </th>
<?php endforeach; ?>
<?php $recordRowNumber = $pager->getFirstIndice(); ?>
<?php foreach ($pager->getResults() as $ag_organization): ?>
          <tr>
            <td><a class=continueButton href="<?php echo url_for('organization/show?id=' . $ag_organization->getId()) ?>" title="View Organization <?php echo $ag_organization->getId() ?>"><?php echo $recordRowNumber++; ?></a></td>
            <td><?php echo $ag_organization->getOrganization() ?></td>
            <td><?php echo $ag_organization->getDescription(); ?></td>
            <td><?php echo $ag_organization->get('staffCount'); ?></td>
          </tr>
<?php endforeach; ?>
  <tfoot class="tFootInfo">
      <tr>
        <td colspan="4">
        <?php
          // Output the current staff members being shown, as well total number in the list.
          // The if prevents getting a 1-0 display indicator if there is an empty list.
          $totalRecords = $pager->count();
          $firstIndice = ($totalRecords === 0) ? 0 : $pager->getFirstIndice();
          $lastIndice = $pager->getLastIndice();
          echo $firstIndice . " - " . $lastIndice . " of " . $totalRecords;
        ?>
      </td>
    </tr>
     <tr>
        <td colspan="4">
        <?php
        $pagerParams = $sf_data->getRaw('listParams');
        $thisUrl = $url = $this->moduleName . '/' . $this->actionName . '?';

        //First Page link (or inactive if we're at the first page).
        if (!$pager->isFirstPage()) {
         // first page
         $pagerParams['page'] = $pager->getFirstPage();
         echo link_to('<<', $thisUrl . http_build_query($pagerParams), array('class' => 'buttonText', 'title' => 'First Page'));

         // previous page
         $pagerParams['page'] = $pager->getPreviousPage();
         echo link_to('<', $thisUrl . http_build_query($pagerParams), array('class' => 'buttonText', 'title' => 'Previous Page'));
         } else {
          echo '<a class="buttonTextOff">&lt;&lt;</a>';
          echo '<a class="buttonTextOff">&lt;</a>';
        }


        //First Page link (or inactive if we're at the first page).
        if (!$pager->isLastPage()) {
         // first page
         $pagerParams['page'] = $pager->getNextPage();
         echo link_to('>', $thisUrl . http_build_query($pagerParams), array('class' => 'buttonText', 'title' => 'Next Page'));

         // previous page
         $pagerParams['page'] = $pager->getLastPage();
         echo link_to('>>', $thisUrl . http_build_query($pagerParams), array('class' => 'buttonText', 'title' => 'Last Page'));
         } else {
          echo '<a class="buttonTextOff">&gt;&gt;</a>';
          echo '<a class="buttonTextOff">&gt;</a>';
        }

  ?>    
      </td>
    </tr>
        </tbody>
      </table>
      <br>
     
      
      
      <div>
        <a href="<?php echo url_for('organization/new') ?>" class="continueButton" title="Create New Organization">Create New</a>
      </div>
 