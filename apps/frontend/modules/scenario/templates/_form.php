<?php use_stylesheets_for_form($form) ?>
<?php use_javascript('jquery.ui.custom.js'); ?>

<form action="<?php echo url_for('scenario/meta' . (!$form->getObject()->isNew() ? '?id='.$form->getObject()->getId() : '')) ?>" method="post">
  <table>
    <tfoot>
      <tr>
        <td colspan="2">
          <?php echo $form->renderHiddenFields(false) ?>
          &nbsp;<a href="<?php echo url_for('scenario/list') ?>" class="continueButton">Back to List</a>
          <?php if (!$form->getObject()->isNew()): ?>
            &nbsp;<?php echo link_to ('Delete', 'scenario/delete?id='.$form->getObject()->getId(), array('class' => 'deleteButton', 'method' => 'delete', 'confirm' => 'Are you sure?')) ?>
          <?php endif; ?>
          <input type="submit" value="Save and Continue" name="Continue" class="continueButton"/>
        </td>
      </tr>
    </tfoot>
    <tbody>
      <tr>
        <td>
      <?php echo $form ?>
        </td>
      </tr>
    </tbody>
  </table>
</form>