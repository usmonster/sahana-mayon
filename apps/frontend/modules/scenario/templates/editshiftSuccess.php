<?php (!$scenarioshiftform->isNew()) ? $action = 'Edit' :$action = 'Create New'; ?>
<h3><?php echo $action; ?> Scenario Shift</h3>

<?php include_partial('scenarioshiftform', array('scenarioshiftform' => $scenarioshiftform, 'scenario_id' => $scenario_id)) ?>


