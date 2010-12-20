<?php use_stylesheets_for_form($staffresourceform) ?>
<?php use_javascripts_for_form($staffresourceform) ?>

<form action="<?php echo url_for('scenario/staffresources'.($staffresourceform->getObject()->isNew() ? 'create' : 'update').(!$staffresourceform->getObject()->isNew() ? '?id='.$staffresourceform->getObject()->getId() : '')) ?>" method="post" <?php $staffresourceform->isMultipart() and print 'enctype="multipart/form-data" ' ?>>
<?php if (!$staffresourceform->getObject()->isNew()): ?>
<input type="hidden" name="sf_method" value="put" />
<?php endif; ?>
  <table>
    <tfoot>
      <tr>
        <td colspan="2">
          <?php echo $staffresourceform->renderHiddenFields(false) ?>
          <?php if (!$staffresourceform->getObject()->isNew()): ?>
            &nbsp;<?php echo link_to('Delete', 'scenario/deletegrouptype?id='.$staffresourceform->getObject()->getId(), array('method' => 'delete', 'confirm' => 'Are you sure?')) ?>
          <?php endif; ?>
          <input type="submit" value="Save" />
          <input type="submit" value="Save and Continue" name="Continue" onclick="serialTran()"/>
        </td>
      </tr>
    </tfoot>
    <tbody>
      <?php echo $staffresourceform->renderGlobalErrors() ?>
      <tr>
        <th>Facility Resource Information</th>
        <td>

          <?php echo $staffresourceform ?>
        </td>
      </tr>
    </tbody>
  </table>
</form>
