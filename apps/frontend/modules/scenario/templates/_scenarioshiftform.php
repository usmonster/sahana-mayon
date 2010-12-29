<?php use_stylesheets_for_form($scenarioshiftform) ?>
<?php use_javascripts_for_form($scenarioshiftform) ?>

<form name="scenario_shift_form" id="scenario_shift_form" action="<?php echo url_for('scenario/'.($scenarioshiftform->getObject()->isNew() ? 'createscenarioshift' : 'updatescenarioshift').(!$scenarioshiftform->getObject()->isNew() ? '?id='.$scenarioshiftform->getObject()->getId() : '')) ?>" method="post" <?php $scenarioshiftform->isMultipart() and print 'enctype="multipart/form-data" ' ?>>
  <?php if (!$scenarioshiftform->getObject()->isNew()): ?>
    <input type="hidden" name="sf_method" value="put" />
  <?php endif; ?>

<br />

<?php
  echo $scenarioshiftform->renderGlobalErrors();
  echo $scenarioshiftform['id']->renderError();
  echo $scenarioshiftform['id'];
  echo $scenarioshiftform;
?>

<br /><br />

  <table>
    <tfoot>
      <tr>
        <td colspan="2">
          <?php echo $scenarioshiftform->renderHiddenFields(false) ?>
          &nbsp;<a href="<?php echo url_for('scenario/scenarioshiftlist') ?>" class="linkButton">Back to list</a>
          <?php if (!$scenarioshiftform->getObject()->isNew()): ?>
            &nbsp;<?php echo link_to('Delete', 'scenario/deletescenarioshift?id='.$scenarioshiftform->getObject()->getId(), array('method' => 'delete', 'confirm' => 'Are you sure?', 'class' => 'linkButton')) ?>
          <?php endif; ?>
          <input type="submit" value="Save" class="linkButton" />
        </td>
      </tr>
    </tfoot>
  </table>
</form>
