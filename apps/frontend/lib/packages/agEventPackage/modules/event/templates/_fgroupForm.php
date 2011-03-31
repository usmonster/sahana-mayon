<?php use_stylesheets_for_form($fgroupForm) ?>
<?php use_javascripts_for_form($fgroupForm) ?>

<form action="<?php echo url_for('event/facilitygroups?event=' . urlencode($event_name)) ?> " method="post">
  <?php echo $facilitygroupsForm['facility_group_list']->renderRow(array('onchange' => 'submit();'), 'Facility Group') ?>
  <input type="hidden" value="facility_group_filter" name="facility_group_filter">
  <br />
  <span class="borderBottom">
    <input type="checkbox" name="checkall" id="checkall" value="check all">
    <label for="checkAll" class="labelSmall">Select All Facility Resources</label>
  </span>
</form>
<br />
<form action="<?php echo url_for('event/facilitygroups?event=' . urlencode($event_name)) ?> " method="post">

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