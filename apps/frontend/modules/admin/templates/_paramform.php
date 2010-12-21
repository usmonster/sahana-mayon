<form action="<?php echo url_for('admin/config') ?> " method="post" />
  <table>
    <tfoot>
      <tr>
        <td colspan="2">
          <?php echo $paramform->renderHiddenFields(false) ?>
          <input type="submit" value="Save" name="update" class="buttonSmall"/>
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
