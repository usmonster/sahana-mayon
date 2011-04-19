<?php
//Defines the columns of the organization display list page.
$columns = array(
  'id' => array('title' => 'Id', 'sortable' => false),
  'organization' => array('title' => 'Organization', 'sortable' => true),
  'description' => array('title' => 'Description', 'sortable' => true)
);

$thisUrl = url_for('organization/list');

($sf_request->getGetParameter('sort')) ? $sortAppend = '&sort=' . $sf_request->getGetParameter('sort') : $sortAppend = '';
($sf_request->getGetParameter('order')) ? $orderAppend = '&order=' . $sf_request->getGetParameter('order') : $orderAppend = '';

$ascArrow = '&#x25B2;';
$descArrow = '&#x25BC;';
?>
<p>Below are the current organizations in Agasti.  To search for a specific organization use the
  search box above.</p>
<table class="staffTable">
    <caption>Organizations
    <?php
    // Output the current staff members being shown, as well total number in the list.
    echo $pager->getFirstIndice() . "-" . $pager->getLastIndice() . " of " . $pager->count();
    ?>
  </caption>

  <thead>
    <tr class="head">
<?php foreach ($columns as $column => $columnCaption): ?>
        <th>
<?php echo $columnCaption['title'] ?>
        <?php if ($columnCaption['sortable']) {
          echo($sortColumn == $column && $sortOrder == 'ASC' ? '<a href="' . $thisUrl . '?sort=' . $column . '&order=ASC" class="buttonSortSelected" title="ascending">' . $ascArrow . '</a>' : '<a href="' . $thisUrl . '?sort=' . $column . '&order=ASC" class="buttonSort" title="ascending">' . $ascArrow . '</a>');
        } ?>
<?php if ($columnCaption['sortable']) {
          echo($sortColumn == $column && $sortOrder == 'DESC' ? '<a href="' . $thisUrl . '?sort=' . $column . '&order=DESC" class="buttonSortSelected" title="descending">' . $descArrow . '</a>' : '<a href="' . $thisUrl . '?sort=' . $column . '&order=DESC" class="buttonSort" title="descending">' . $descArrow . '</a>');
        } ?>
        </th>
<?php endforeach; ?>
        <th>Staff Count</th>
      </tr>
    </thead>
    <tbody>
<?php $recordRowNumber = $pager->getFirstIndice(); ?>
<?php foreach ($pager->getResults() as $ag_organization): ?>
          <tr>
            <td><a class=linkButton href="<?php echo url_for('organization/show?id=' . $ag_organization->getId()) ?>" title="View Organization <?php echo $ag_organization->getId() ?>"><?php echo $recordRowNumber++; ?></a></td>
            <td><?php echo $ag_organization->getOrganization() ?></td>
            <td><?php echo $ag_organization->getDescription(); ?></td>
            <td><?php echo $ag_organization->get('staffCount'); ?></td>
          </tr>
<?php endforeach; ?>
        </tbody>
      </table>
      <br>
      <div>
        <a href="<?php echo url_for('organization/new') ?>" class="linkButton" title="Create New Organization">Create New</a>
      </div>

      <div class="rightFloat">
  <?php
//
//First Page link (or inactive if we're at the first page).
          echo(!$pager->isFirstPage() ? '<a href="' . $thisUrl . '?page=' . $pager->getFirstPage() . $sortAppend . $orderAppend . '" class="buttonText" title="First Page">&lt;&lt;</a>' : '<a class="buttonTextOff">&lt;&lt;</a>');
//Previous Page link (or inactive if we're at the first page).
          echo(!$pager->isFirstPage() ? '<a href="' . $thisUrl . '?page=' . $pager->getPreviousPage() . $sortAppend . $orderAppend . '" class="buttonText" title="Previous Page">&lt;</a>' : '<a class="buttonTextOff">&lt;</a>');
//Next Page link (or inactive if we're at the last page).
          echo(!$pager->isLastPage() ? '<a href="' . $thisUrl . '?page=' . $pager->getNextPage() . $sortAppend . $orderAppend . '" class="buttonText" title="Next Page">&gt;</a>' : '<a class="buttonTextOff">&gt;</a>');
//Last Page link (or inactive if we're at the last page).
          echo(!$pager->isLastPage() ? '<a href="' . $thisUrl . '?page=' . $pager->getLastPage() . $sortAppend . $orderAppend . '" class="buttonText" title="Last Page">&gt;&gt;</a>' : '<a class="buttonTextOff">&gt;&gt;</a>');
  ?>
</div>
