<?php use_stylesheets_for_form($poolform) ?>
<?php //use_javascript('tooltip');   ?>
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
    if(out.length > 1){
      $("#staff_pool_search_search_condition").val(JSON.stringify(out));
    }
    else{
      var query_c = Array(out.pop());
      if(query_c != undefined){
        $("#staff_pool_search_search_condition").val(JSON.stringify(query_c));
      }
      else{
        $("#staff_pool_search_search_condition").val('[]');
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
          <a href="<?php echo url_for('scenario/review?id=' . $scenario_id) ?>" class="linkButton" title="Review Scenario">Finish Scenario Wizard</a>
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

          <h3>Construct Search Conditions:</h3>

<?php
    $labels = $filterForm->getWidgetSchema()->getLabels();
    $fields = $filterForm->getWidgetSchema()->getFields();
foreach($fields as $key => $field)
{
  echo '<label class ="filterButton">' . $labels[$key] . '</label>';
  echo $filterForm[$key];
}
?>
        </td>
      </tr>
    </tbody>
  </table>
</form>




