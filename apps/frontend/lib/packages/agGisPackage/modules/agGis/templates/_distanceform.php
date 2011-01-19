<?php use_stylesheets_for_form($distanceform) ?>
<?php use_javascript('jquery.ui.custom.js'); ?>

<form action="<?php echo url_for('gis/'.($distanceform->getObject()->isNew() ? 'create' : 'update').(!$distanceform->getObject()->isNew() ? '?id='.$distanceform->getObject()->getId() : '')) ?>" method="post" <?php $distanceform->isMultipart() and print 'enctype="multipart/form-data" ' ?>>
<?php if (!$distanceform->getObject()->isNew()): ?>
<input type="hidden" name="sf_method" value="put" />
<?php endif; ?>
  <table>
    <tfoot>
      <tr>
        <td colspan="2">
          <?php echo $distanceform->renderHiddenFields(false) ?>
          <input type="submit" value="crush machine" />
        </td>
      </tr>
    </tfoot>
    <tbody>
      <?php echo $distanceform->renderGlobalErrors() ?>
      <tr>
        <td>
          <?php echo $distanceform ?>

        </td>
      </tr>
    </tbody>
  </table>
</form>
