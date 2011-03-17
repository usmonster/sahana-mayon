<?php use_stylesheets_for_form($fgroupForm) ?>
<?php use_javascripts_for_form($fgroupForm) ?>

<form action="<?php echo url_for('event/fgroup?event=' . urlencode($event_name)) ?> " method="post">
  <?php echo $facilitygroupsForm['facility_group_list']->renderRow(array('onchange' => 'submit();'), 'Facility Group') ?>
  <input type="hidden" value="facility_group_filter" name="facility_group_filter">
  <br />
  <span class="borderBottom">
    <input type="checkbox" name="checkall" id="checkall" value="check all">
    <label for="checkall" class="labelSmall">Select All Facility Resources</label>
  </span>
</form>
<br />
<form action="<?php echo url_for('event/fgroup?event=' . urlencode($event_name)) ?> " method="post">

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
//$(document).ready(function(){
//  $("#checkall").click(function () {
//    $('.checkToggle').attr('checked', this.checked);
//  });
//  $(".checkToggle").click(function(){
//    if($(".checkToggle").length == $(".checkToggle:checked").length) {
//      $("#checkall").attr("checked", "checked");
//    } else {
//      $("#checkall").removeAttr("checked");
//    }
//  });

// $("#checkall").click(function()
//  {
//   var checked_status = 'checked';
//   $(".inputGray").each(function()
//   {
//    this.checked = checked_status;
//   });
//  });

});
</script>