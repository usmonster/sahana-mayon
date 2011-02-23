<?php use_javascript('jquery.ui.custom.js'); ?>
<?php use_javascript('json.serialize.js'); ?>
<script type="text/javascript">
  function queryConstruct() {
    var out = Array();
    $('.filter option:selected').each(function(index) {
      out[index] = $(this).parent().attr('id') + ":" + $(this).text();
    })
    $("#query_condition").val(out.join(' AND '));
  }

</script>

<form action="<?php echo url_for('event/staffshift?id=' . $event_id) . '/' . $shift_id?>" method="post"<?php echo ($XmlHttpRequest != false ? ' class="modalForm"' : ''); ?>>

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
<input type="submit" name="Search" id="Search" value="Search" onclick="queryConstruct()" class="linkButton<?php echo ($XmlHttpRequest != false ? ' modalSubmit' : '');?>">

</form>

<?php echo javascript_include_tag('agModal.js'); ?>