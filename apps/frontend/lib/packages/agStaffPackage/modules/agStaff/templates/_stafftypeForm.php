<?php use_stylesheets_for_form($staffTypeForm) ?>
<?php use_javascripts_for_form($staffTypeForm) ?>

<form action="<?php
echo url_for('staff/stafftypes') ?>" method="post">
   
          <table>
            <tfoot>
              <tr>
                <td colspan="2">
          <?php echo $staffTypeForm->renderHiddenFields(false) ?>
          <?php if (!$staffTypeForm->getObject()->isNew()): ?>
            &nbsp;<?php
            echo link_to('Delete', 'staff/stafftype?id=' .
                $staffTypeForm->getObject()->getId(),
                array('method' => 'delete', 'confirm' => 'Are you sure?', 'class' => 'deleteButton')) ?>
<?php endif; ?>
            <input type="submit" value="Save" />
          </td>
        </tr>
      </tfoot>
      <tbody>
<?php echo $staffTypeForm->renderGlobalErrors() ?>
            <tr>
              <th>Staff Type Information</th>
              <td>

<?php echo $staffTypeForm ?>
        </td>
      </tr>
    </tbody>
  </table>
</form>
