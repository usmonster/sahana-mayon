<?php use_stylesheets_for_form($form) ?>
<?php use_javascripts_for_form($form) ?>

<form name="organization_form" id="organization_form" action="<?php echo url_for('organization/'.($form->getObject()->isNew() ? 'create' : 'update').(!$form->getObject()->isNew() ? '?id='.$form->getObject()->getId() : '')) ?>" method="post" <?php $form->isMultipart() and print 'enctype="multipart/form-data" ' ?>>
<?php if (!$form->getObject()->isNew()): ?>
<input type="hidden" name="sf_method" value="put" />
<?php endif; ?>
  <table>
    <tfoot>
      <tr>
        <td colspan="2">
          &nbsp;<a href="<?php echo url_for('organization/list') ?>" class="linkButton">Back to list</a>
          <?php if (!$form->getObject()->isNew()): ?>
            &nbsp;<?php echo link_to('Delete', 'organization/delete?id='.$form->getObject()->getId(), array('method' => 'delete', 'confirm' => 'Are you sure?', 'class' => 'linkButton')) ?>
          <?php endif; ?>
          <input type="submit" value="Save" class="linkButton" />
        </td>
      </tr>
    </tfoot>
    <tbody>
      <?php echo $form ?>
    </tbody>
  </table>
</form>
