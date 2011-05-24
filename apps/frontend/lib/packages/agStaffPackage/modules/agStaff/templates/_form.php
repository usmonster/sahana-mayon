<?php use_javascript('agasti.js') ?>
<?php use_stylesheets_for_form($form);
use_javascripts_for_form($form);
?>
<form action="<?php echo url_for('agStaff/' . ($form->getObject()->isNew() ? 'create' : 'update') . (!$form->getObject()->isNew() ? '?id=' . $form->getObject()->getAgStaff()->getFirst()->getId() : '')) ?>" method="post" <?php $form->isMultipart() and print 'enctype="multipart/form-data" ' ?>>
<?php if (!$form->getObject()->isNew()): ?>
  <input type="hidden" name="sf_method" value="put" />
<?php endif; ?>
  <div class="displayInlineBlock marginBot10px">
    <div class="infoHolder" id="staffResourceTypeHolder">
      <h3>Staff Information</h3>
      <hr class="ruleGray" />
      <?php echo $form['staff']['status'] ?>
      <?php foreach($form['staff']['type'] as $staff_type_form): ?>
        <div class="displayInline staffCounter">
        <?php $formId = $staff_type_form->offsetGet('id')->getValue(); ?>
          <a href="<?php echo url_for('staff/deletestaffresource'); ?>" class="ui-icon ui-icon-circle-minus removeStaffResource floatRight" <?php echo ($formId != null ? 'id="staff_resource_' . $staff_type_form->offsetGet('id')->getValue() . '"' : '' ); ?>></a>
        <?php echo $staff_type_form; ?>
        </div>
      <?php endforeach; ?>
      <br />
      <a href="<?php echo url_for('staff/addstaffresource'); ?>" name="groupStatus" class="generalButton addStaffResource" id="staff_id_1">Add Staff Resource</a>
      </div>
    <div class="clearBoth"> </div>
    <div class="infoHolder">
      <h3>Primary Information</h3>
      <hr class="ruleGray" />
      <?php echo $form['id'] ?>
      <div class="clearBoth"> </div>
      <?php echo $form['Primary'] ?>
      <div class="clearBoth"> </div>
      <div class="displayInlineBlock">
        <h4>
          <?php echo $form['ag_sex_list']->renderLabel(); ?>
          <a href="<?php echo url_for('@wiki') . '/doku.php?id=tooltip:sex&do=export_xhtmlbody' ?>" class="tooltipTrigger" title="Sex">?</a>
        </h4>
        <?php echo $form['ag_sex_list'] ?>
      </div>
      <div class="displayInlineBlock">
        <?php echo $form['date of birth']; ?>
      </div>
      <br />
      <br />
      <div class="displayBlock">
      </div>
      <div class="clearBoth"> </div>
      <br />
    </div>
    <div class="infoHolder">
      <h3>Contact</h3>
      <hr class="ruleGray" />
      <?php echo $form['Contact'] ?>
    </div>
    <?php echo $form['_csrf_token'] ?>
    <div class="infoHolder">
      <h3><?php echo $form['address']->renderLabel(); ?> <a href="<?php echo url_for('@wiki') . '/doku.php?id=tooltip:contact_address&do=export_xhtmlbody' ?>" class="tooltipTrigger" title="Address">?</a></h3>
      <hr class="ruleGray" />
      <div class="clearBoth"> </div>
      <?php echo $form['address']; ?>
    </div>
  </div>
  <br />
  <input type="submit" value="Save" class="continueButton" />
  <input type="submit" value="Save and Create Another" name="CreateAnother" class="continueButton" />
  <a href="<?php echo url_for('staff/list') ?>" class="generalButton">Back to Staff List</a>
  <?php if (!$form->getObject()->isNew()): ?>
    <?php echo link_to('Delete', 'agStaff/delete?id=' . $form->getObject()->getAgStaff()->getFirst()->getId(), array('method' => 'delete', 'confirm' => 'Are you sure?', 'class' => 'deleteButton', 'title' => 'Delete this Staff Person')) ?>
  <?php endif; ?>
</form>
