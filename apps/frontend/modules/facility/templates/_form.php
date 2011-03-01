<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>
<?php

if($events != ""){
  //we also need to bind the submit button to a confirmation alert
  $confirmScript = ' onclick="return confirm(\'Are you sure?  This facility is currently used in some scenarios/events\');"';
}
else{
  $confirmScript = ' onclick="return confirm(\'Are you sure?\');"';
}
?>

<form action="<?php echo url_for('facility/'.($form->getObject()->isNew() ? 'create' : 'update').(!$form->getObject()->isNew() ? '?id='.$form->getObject()->getId() : '')) ?>" method="post" <?php $form->isMultipart() and print 'enctype="multipart/form-data" ' ?>>
<?php if (!$form->getObject()->isNew()): ?>
<input type="hidden" name="sf_method" value="put" />
<?php endif; ?>
  <table>
    <tfoot>
      <tr>
        <td colspan="2">
          &nbsp;<a href="<?php echo url_for('facility/list') ?>" class="linkButton">Back to list</a>
          <?php if (!$form->getObject()->isNew()): ?>
            &nbsp;<a href="<?php echo url_for('facility/delete?id=' . $form->getObject()->getId()) ?>" class="linkButton"<?php echo $confirmScript ?>>Delete</a>

          <?php endif; ?>
          <input type="submit" value="Save" class="linkButton"<?php echo $confirmScript ?>/>
        </td>
      </tr>
    </tfoot>
    <tbody><tr><td>
      <?php echo $form ?>
    </td></tr></tbody>
  </table>
</form>
