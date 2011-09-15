<?php use_javascript('json.serialize.js'); ?>

<?php
$action = url_for('scenario/staffpool?id=' . $scenario_id);
if (isset($search_id)) {
  $action .= '?search_id=' . $search_id;
}
?>
<div class="infoHolder" style="width:750px;">
  <h3>Staff Pool Definition<a href="<?php echo url_for('@wiki') . '/doku.php?id=tooltip:staff_pool&do=export_xhtmlbody' ?>" class="tooltipTrigger" title="Staff Pool Definition">?</a></h3>
  <form action="<?php echo $action ?>" method="post">

    <table>
      <tfoot>
        <tr>
          <td colspan="2">
            <?php echo $poolform->renderHiddenFields(false) ?>
            <input type="submit" value="Save" class="continueButton" name="Save" onclick="queryConstruct()"/>
            <input type="submit" value="Preview" class="generalButton" name="Preview" onclick="queryConstruct()"/>

            <?php if (isset($search_id)) { ?>
              <a href="<?php echo url_for('scenario/staffpool?id=' . $scenario_id) ?>" class="continueButton" title="New Staff Pool">New Staff Pool</a>
              <input type="submit" value="Delete" name="Delete" class="deleteButton"/> <!--this should be used if you are 'editing' a search condition but then want to create a new one, without 'refreshing' the page -->
            <?php } ?>
            <input type="submit" value="Save and Continue" class="continueButton" name="Continue" onclick="queryConstruct()"/>
          </td>
        </tr>
      </tfoot>
      <tbody>
        <tr>
          <td>  
            <?php echo $poolform ?>
          </td>
        </tr>
        <tr> 
          <td>
            <div class="infoHolder">
              <h4 class="head">Search Conditions:</h4>

              <?php
              $labels = $filterForm->getWidgetSchema()->getLabels();
              $fields = $filterForm->getWidgetSchema()->getFields();
              foreach ($fields as $key => $field) {
                echo '<label class ="filterButton">' . $labels[$key] . '</label>';
                echo $filterForm[$key];
              }
              ?>
            </div>
          </td>
        </tr>
      </tbody>
    </table>
  </form>
</div>



