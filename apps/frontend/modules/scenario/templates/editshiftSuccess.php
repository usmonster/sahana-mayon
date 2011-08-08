<?php (!$scenarioshiftform->isNew()) ? $action = 'Edit' : $action = 'Create New'; ?>
<h3><?php echo $action; ?> Scenario Shift</h3>

<?php $append = '?shiftid=' . ($scenarioshiftform->getObject()->isNew() ? 'new' : $scenarioshiftform->getObject()->getId()) ?>

<form name="shift_template" id="shift_template" action="


<?php echo url_for('scenario/shifts?id=' . $scenario_id) . $append ?>" method="post">

      <?php
//include_partial('scenarioshiftform', array('scenarioshiftform' => $scenarioshiftform, 'scenario_id' => $scenario_id))
      include_partial('shiftform',
                      array('scenarioshiftform' => $scenarioshiftform, 'scenario_id' => $scenario_id))
      ?>
</form>

