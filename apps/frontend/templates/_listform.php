<?php


//the below has been moved to an abstracted function in agActions actions.class.php and should be replaced
$sortColumn = $sf_request->getGetParameter('sort');
    $sortOrder = $sf_request->getGetParameter('order');
    ($sf_request->getGetParameter('filter')) ? $filterAppend = '&filter=' . $sf_request->getGetParameter('filter') : $filterAppend = '';
    ($sf_request->getGetParameter('sort')) ? $sortAppend = '&sort=' . $sf_request->getGetParameter('sort') : $sortAppend = '';
    ($sf_request->getGetParameter('order')) ? $orderAppend = '&order=' . $sf_request->getGetParameter('order') : $orderAppend = '';

    $thisUrl = $form_action;

    ($sf_request->getGetParameter('sort')) ? $sortAppend = '&sort=' . $sf_request->getGetParameter('sort') : $sortAppend = '';
    ($sf_request->getGetParameter('order')) ? $orderAppend = '&order=' . $sf_request->getGetParameter('order') : $orderAppend = '';

    $ascArrow = '&#x25B2;';
    $descArrow = '&#x25BC;';
?>
<form action="<?php echo url_for($form_action) ?>" method="post" name="listform">
<table class="staffTable">

  <thead>
    <tr class="head">
     <?php
     $listhead = "";
    foreach ($displayColumns as $column => $columnCaption) {

      $listhead .= '  <th>' . $columnCaption['title'];
      if ($columnCaption['sortable']) {
        $listhead .= $sortColumn == $column && $sortOrder == 'ASC' ? '<a href="' . $thisUrl . '?sort=' . $column . '&order=ASC" class="buttonSortSelected" title="ascending">' . $ascArrow . '</a>' : '<a href="' . $thisUrl . '?sort=' . $column . '&order=ASC" class="buttonSort" title="ascending">' . $ascArrow . '</a>';
        $listhead .= $sortColumn == $column && $sortOrder == 'DESC' ? '<a href="' . $thisUrl . '?sort=' . $column . '&order=DESC" class="buttonSortSelected" title="descending">' . $descArrow . '</a>' : '<a href="' . $thisUrl . '?sort=' . $column . '&order=DESC" class="buttonSort" title="descending">' . $descArrow . '</a>';
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

          echo get_partial('global/formresult', array(
        'obj' => $result,
        'widget' => $widget,
         'displayColumns' => $displayColumns));
    }
  ?>
    </tbody>
</table>

  <br />
  
  <input type="submit" name="Save" value="Save" id="save">
</form>