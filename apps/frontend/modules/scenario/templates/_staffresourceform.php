<?php
$thisUrl = url_for('scenario/staffresources?id=' . $scenario->id);
?>
<!--
<table class="staffTable">
  <tbody>
    <tr>
      <td>-->

<form action="<?php echo url_for('scenario/staffresources?id=' . $scenario->id) ?>" method="post" name="staffResourceRequirements">
  <?php
    echo $facilityStaffResourceContainer;

    echo "<br />";
    
    $listfoot = '<div class="floatRight">';
    $currentPage = $pager->getPage();
    $firstPage = $pager->getFirstPage();
    $lastPage = $pager->getLastPage();

    //First Page link (or inactive if we're at the first page).
    $listfoot .= '<input type="hidden" name="firstPage" value="' . $firstPage . '"/>';
    $listfoot .= ( ($currentPage == $firstPage) ? '<a class="buttonTextOff">&lt;&lt;</a>' : '<input class="buttonText" type="submit" name="first" value="<<" style="background:none;border:0;color:#000000";font-size:30%;/>');
    //Previous Page link (or inactive if we're at the first page).
    $listfoot .= '<input type="hidden" name="previousPage" value="' . $pager->getPreviousPage() . '"/>';
    $listfoot .= ( ($currentPage == $firstPage) ? '<a class="buttonTextOff">&lt;</a>' : '<input class="buttonText" type="submit" name="previous" value="<" style="background:none;border:0;color:#000000";font-size:30%;/>');
    //Next Page link (or inactive if we're at the last page).
    $listfoot .= '<input type="hidden" name="nextPage" value="' . $pager->getNextPage() . '"/>';
    $listfoot .= ( ($currentPage == $lastPage) ? '<a class="buttonTextOff">&gt;</a>' : '<input class="buttonText" type="submit" name="next" value=">" style="background:none;border:0;color:#000000";font-size:30%;/>');
    //Last Page link (or inactive if we're at the last page).
    $listfoot .= '<input type="hidden" name="lastPage" value="' . $lastPage . '"/>';
    $listfoot .= ( ($currentPage == $lastPage) ? '<a class="buttonTextOff">&gt;&gt;</a>' : '<input class="buttonText" type="submit" name="last" value=">>" style="background:none;border:0;color:#000000";font-size:30%;/>');
    $listfoot .= '</div>';

    echo $listfoot;

  ?>
  <br />
  <br />
  <input class="continueButton" type="submit" value="Save" name="Save"/>
  <input class="continueButton" type="submit" value="Save and Continue" name="Continue"/>
</form>
<?php
?>
