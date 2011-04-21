<?php use_javascript('jquery.ui.custom.js'); ?>

<form action="<?php echo url_for('scenario/resourcetypes' . '?id=' .$scenario_id) ?>" method="post">
  <table>
    <tfoot>
      <tr>
        <td colspan="2">
          <?php echo $resourceForm->renderHiddenFields(false) ?>
          &nbsp;<a href="<?php echo url_for('scenario/list') ?>" class="linkButton">Back to list</a>
          <input type="submit" value="Save and Continue" name="Continue" class="linkButton"/>
        </td>
      </tr>
    </tfoot>
    <tbody>
      <tr>
        <td>
      <?php echo $resourceForm ?>
        </td>
      </tr>
    </tbody>
  </table>
</form>