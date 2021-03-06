<?php use_stylesheets_for_form($grouptypeform) ?>
<?php use_javascripts_for_form($grouptypeform) ?>
<?php use_javascript('jquery.ui.custom.js'); ?>
<form action="<?php
echo url_for('scenario/grouptype' .
    ($grouptypeform->getObject()->isNew() ? 'create' : 'update') .
    (!$grouptypeform->getObject()->isNew() ? '?id=' . $grouptypeform->getObject()->getId() : '')) ?>"
      method="post" <?php $grouptypeform->isMultipart() and print 'enctype="multipart/form-data" ' ?>>
        <?php if (!$grouptypeform->getObject()->isNew()): ?>
    <input type="hidden" name="sf_method" value="put" />
  <?php endif; ?>

          <table>
            <tfoot>
              <tr>
                <td colspan="2">
          <?php echo $grouptypeform->renderHiddenFields(false) ?>
          <?php if (!$grouptypeform->getObject()->isNew()): ?>
            &nbsp;<?php
            echo link_to('Delete', 'scenario/deletegrouptype?id=' .
                $grouptypeform->getObject()->getId(),
                array('method' => 'delete', 'confirm' => 'Are you sure?', 'class' => 'deleteButton')) ?>
<?php endif; ?>
            <input type="submit" value="Save" class="continueButton"/>
          </td>
        </tr>
      </tfoot>
      <tbody>
<?php echo $grouptypeform->renderGlobalErrors() ?>
            <tr>
             <!--<th>Facility Group Type Information</th>-->
              <td>
<?php echo $grouptypeform ?>
        </td>
      </tr>
    </tbody>
  </table>
</form>
