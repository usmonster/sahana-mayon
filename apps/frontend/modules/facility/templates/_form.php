<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>
<?php
if ($events != "") {
  //we also need to bind the submit button to a confirmation alert
  $confirm = 'Are you sure?  This facility is currently used in some scenarios or events and the change may affect deployment and response.';
} else {
  $confirm = 'Are you sure?';
}
?>

<form action="<?php echo url_for('facility/' . ($form->getObject()->isNew() ? 'create' : 'update') . (!$form->getObject()->isNew() ? '?id=' . $form->getObject()->getId() : '')) ?>" method="post" <?php $form->isMultipart() and print 'enctype="multipart/form-data" ' ?>>
<?php if (!$form->getObject()->isNew()): ?>
    <input type="hidden" name="sf_method" value="put" />
  <?php endif; ?>
  <table>
    <tfoot>
      <tr>
        <td colspan="2">
          &nbsp;<a href="<?php echo url_for('facility/list') ?>" class="linkButton">Back to List</a>
<?php if (!$form->getObject()->isNew()): ?>
<?php echo link_to('Delete', 'facility/delete?id=' . $form->getObject()->getId(), array('method' => 'delete', 'confirm' => $confirm, 'class' => 'linkButton')) ?> 
<?php echo link_to('Disable', 'facility/disable?id=' . $form->getObject()->getId(), array('method' => 'delete', 'confirm' => $confirm, 'class' => 'linkButton')) ?>
<?php endif; ?>
          <input type="submit" value="Save" class="linkButton" style="margin-left: 0px" <?php //
          //TODO we should have the confirmation script above actually work and pipe to the save method here.
          //echo $confirmScript ?>/>
        </td>
      </tr>
    </tfoot>
    <tbody><tr><td>
<?php echo $form ?>
        </td></tr></tbody>
  </table>
</form>
