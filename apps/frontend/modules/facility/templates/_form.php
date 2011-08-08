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
  <?php echo $form ?>
  <?php
//    foreach($form->getEmbeddedForms() as $eForm) {
//      echo $eForm;
//          $b = new sfForm($defaults, $options, $CSRFSecret);
//      $c = $b->g
//    }

  ?>
  <a href="<?php echo url_for('facility/list') ?>" class="generalButton">Back to List</a>
  <?php if (!$form->getObject()->isNew()): ?>
    <?php echo link_to('Delete', 'facility/delete?id=' . $form->getObject()->getId(), array('method' => 'delete', 'confirm' => $confirm, 'class' => 'deleteButton')) ?>
    <?php echo link_to('Disable', 'facility/disable?id=' . $form->getObject()->getId(), array('method' => 'delete', 'confirm' => $confirm, 'class' => 'continueButton')) ?>
  <?php endif; ?>
  <input type="submit" value="Save" class="continueButton" style="margin-left: 0px" <?php //
  //TODO we should have the confirmation script above actually work and pipe to the save method here.
  //echo $confirmScript ?>/>
</form>
