<?php use_stylesheets_for_form($scenarioshiftform) ?>
<?php use_javascripts_for_form($scenarioshiftform) ?>

<?php $append = '?shiftid=' . ($scenarioshiftform->getObject()->isNew() ? 'new' : $scenarioshiftform->getObject()->getId()) ?>


<form name="scenario_shift_form" id="scenario_shift_form" action="
<?php echo url_for('scenario/shifts?id=' . $scenario_id) . $append ?>" method="post">
          <br />

  <?php echo $scenarioshiftform; ?>

          <br /><br />

          <table>
            <tfoot>
              <tr>
                <td colspan="2">
          <?php echo $scenarioshiftform->renderHiddenFields(false) ?>
          &nbsp;<a href="<?php echo url_for('scenario/scenarioshiftlist?id=' . $scenario_id) ?>" class="linkButton">Back to list</a>
          <?php if (!$scenarioshiftform->getObject()->isNew()): ?>
            &nbsp;<?php
            echo link_to('Delete', 'scenario/deletescenarioshift?id=' .
                $scenarioshiftform->getObject()->getId(),
                array('method' => 'delete', 'confirm' => 'Are you sure?', 'class' => 'linkButton')) ?>
<?php endif; ?>
          <input type="submit" value="Save" class="saveLinkButton" />
        </td>
      </tr>
    </tfoot>
  </table>
</form>