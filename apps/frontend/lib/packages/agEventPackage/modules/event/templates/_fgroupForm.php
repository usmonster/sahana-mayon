<?php use_stylesheets_for_form($fgroupForm) ?>
<?php use_javascripts_for_form($fgroupForm) ?>





<form action="<?php echo url_for('event/fgroup?id=' . $event_id) ?> " method="post">
  <?php echo $facilitygroupsForm['facility_group_list']->renderRow(array('onchange' => 'submit();'), 'Facility Group') ?>
  <input type="hidden" value="facility_group_filter" name="facility_group_filter"><input type="button" name="checkall" id="checkall" value="check all">
</form>

<form action="<?php echo url_for('event/fgroup?id=' . $event_id) ?> " method="post">

  <table>
    <tfoot>
      <tr>
        <td colspan="2">
          <input type="submit" value="Apply Activation Time" class="linkButton"/>
        </td>
      </tr>
    </tfoot>
    <tbody>
      <?php echo $fgroupForm ?>

    </tbody>
  </table>
</form>
<script type="text/javascript">
$(document).ready(function(){

 $("#checkall").click(function()
  {
   var checked_status = 'checked';
   $(".inputGray").each(function()
   {
    this.checked = checked_status;
   });
  });

});
</script>