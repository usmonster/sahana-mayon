<?php use_stylesheets_for_form($poolform) ?>
<?php //use_javascript('tooltip');?>

<form action="<?php echo url_for('scenario/staffpool?id=' . $scenario_id) ?> " method="post">

      <table>
    <tfoot>
      <tr>
        <td colspan="2">
          <?php echo $poolform->renderHiddenFields(false) ?>
          <input type="submit" value="Save" class="linkButton"/>
          <input type="submit" value="Save and Continue" name="Continue" class="linkButton"/>
        </td>
      </tr>
    </tfoot>
    <tbody>
      <tr>
        <td>
      <?php echo $poolform ?>
        </td>
      </tr>
    </tbody>
  </table>
</form>



