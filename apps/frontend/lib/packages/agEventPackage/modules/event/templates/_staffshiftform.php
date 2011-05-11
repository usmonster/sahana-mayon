<?php use_javascript('jquery.ui.custom.js'); ?>
<?php use_javascript('json.serialize.js'); ?>
<script type="text/javascript">
  function queryConstruct() {
    var out = Array();
    $('.filter option:selected').each(function(index) {
      out[index] = $(this).parent().attr('id') + ":" + $(this).text();
    })
    $("#search_condition").val(out.join(' AND '));
  }

</script>

<form name="staffshiftform" id="staffshiftform" action="<?php echo url_for('event/staffshift?event=' . urlencode($event_name)) . '/' . $shift_id?>" method="post"<?php echo ($xmlHttpRequest != false ? ' class="modalForm"' : ''); ?>>

<h3>Construct Search Conditions:</h3>
<?php
    $labels = $filterForm->getWidgetSchema()->getLabels();
    $fields = $filterForm->getWidgetSchema()->getFields();
    $wSchema = $filterForm->getWidgetSchema();
foreach($fields as $key => $field)
{
  echo '<label class ="filterButton">' . $labels[$key] . '</label>';
  echo $filterForm[$key];
}
?>
<input type="submit" name="Search" id="Search" value="Search" onclick="queryConstruct()" class="continueButton<?php echo ($xmlHttpRequest != false ? ' modalSubmit' : '');?>">

</form>

<?php //echo javascript_include_tag('agModal.js'); ?>