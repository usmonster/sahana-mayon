<form action="<?php echo url_for('admin/config') ?> " method="post">
<?php if (!$paramform->getObject()->isNew()): ?>
<input type="hidden" name="sf_method" value="put" />
<?php endif; ?>
  <table>
    <tfoot>
      <tr>
        <td colspan="2">
          <?php echo $paramform->renderHiddenFields(false) ?>
          <?php if (!$paramform->getObject()->isNew()): ?>
            &nbsp;
              <input type="submit" value="Delete" name="delete" class="deleteButton"/>
              <input type="hidden" value="<?php echo $paramform->getObject()->getId() ?>" name="deleteparam">
          <?php endif; ?>
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