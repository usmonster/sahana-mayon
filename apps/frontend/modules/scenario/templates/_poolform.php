<?php use_stylesheets_for_form($poolform) ?>
<?php //use_javascript('tooltip');    ?>
<?php use_javascript('jquery.ui.custom.js'); ?>
<?php use_javascript('json.serialize.js'); ?>
<script type="text/javascript">
  function queryConstruct() {
    var out = new Array();
    $('.filter option:selected').each(function(index) {
      conditionObject = new Object();
      if($(this).text() != ''){
        conditionObject.condition = $(this).text();
        conditionObject.field = $(this).parent().attr('id');
        conditionObject.operator = '=';
        out.push(conditionObject);
      }
      //ONLY IF text is NOT empty
    })
    if(out.length == 1){
      $("#staff_pool_search_search_condition").val(JSON.stringify(out));
    }
    else if(out.length ==0){
      $("#staff_pool_search_search_condition").val('[ ]');
    }  
    else{
      var query_c = Array(out.pop());
      if(query_c != undefined){
        $("#staff_pool_search_search_condition").val(JSON.stringify(query_c));
      }
    }
  }

</script>

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
            <input type="submit" value="Save" class="linkButton" name="Save" onclick="queryConstruct()"/>
            <input type="submit" value="Preview" class="linkButton" name="Preview" onclick="queryConstruct()"/>

            <?php if (isset($search_id)) { ?>
              <a href="<?php echo url_for('scenario/staffpool?id=' . $scenario_id) ?>" class="linkButton" title="New Staff Pool">New Staff Pool</a>
              <input type="submit" value="Delete" name="Delete" class="linkButton"/> <!--this should be used if you are 'editing' a search condition but then want to create a new one, without 'refreshing' the page -->
            <?php } ?>
            <input type="submit" value="Save and Continue" class="linkButton" name="Continue" onclick="queryConstruct()"/>
            <a href="<?php echo url_for('scenario/shifttemplates?id=' . $scenario_id) ?>" class="linkButton" title="Skip and Continue">Skip and Continue</a>
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



