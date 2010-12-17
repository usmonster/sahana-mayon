<?php use_stylesheets_for_form($form) ?>
<?php use_helper('jQuery') ?>
<?php jq_add_plugins_by_name(array('ui')) ?>
<?php use_javascript('dimensions');?>
<?php use_javascript('tooltip');?>

<form action="<?php echo url_for('scenario/'.($form->getObject()->isNew() ? 'create' : 'update').(!$form->getObject()->isNew() ? '?id='.$form->getObject()->getId() : '')) ?>" method="post" <?php $form->isMultipart() and print 'enctype="multipart/form-data" ' ?>>
<?php if (!$form->getObject()->isNew()): ?>
<input type="hidden" name="sf_method" value="put" />
<?php endif; ?>
  <table>
    <tfoot>
      <tr>
        <td colspan="2">
          <?php echo $form->renderHiddenFields(false) ?>
          &nbsp;<a href="<?php echo url_for('scenario/list') ?>">Back to list</a>
          <?php if (!$form->getObject()->isNew()): ?>
            &nbsp;<?php echo link_to('Delete', 'scenario/delete?id='.$form->getObject()->getId(), array('method' => 'delete', 'confirm' => 'Are you sure?')) ?>
          <?php endif; ?>
          <input type="submit" value="Save" />
          <input type="submit" value="Save and Continue" name="Continue"/>
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