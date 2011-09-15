<form action="<?php echo url_for('admin/globals') ?> " method="post">
<?php if (!$paramform->getObject()->isNew()): ?>
<input type="hidden" name="sf_method" value="put" />
<?php endif; ?>
  <table class="adminGlobal2">
    <tfoot>
      <tr>
        <td colspan="2">
          <?php echo $paramform->renderHiddenFields(false) ?>
          <input type="submit" value="Save" name="update" class="continueButton"/>
        </td>
      </tr>
    </tfoot>
    <tbody>
      <tr>
        <?php echo $paramform;?>
      </tr>
    </tbody>
  </table>
</form>