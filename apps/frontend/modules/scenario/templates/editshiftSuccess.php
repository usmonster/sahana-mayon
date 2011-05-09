<?php
use_javascript('agMain.js');
use_javascript('jquery.ui.custom.js');
//use_javascript('agMain.js');
use_stylesheet('jquery/jquery.ui.custom.css');
use_stylesheet('jquery/mayon.jquery.ui.css');
?>


<?php (!$scenarioshiftform->isNew()) ? $action = 'Edit' : $action = 'Create New'; ?>
<h3><?php echo $action; ?> Scenario Shift</h3>

<?php $append = '?shiftid=' . ($scenarioshiftform->getObject()->isNew() ? 'new' : $scenarioshiftform->getObject()->getId()) ?>

<form name="scenario_shift_form" id="scenario_shift_form" action="


<?php echo url_for('scenario/shifts?id=' . $scenario_id) . $append ?>" method="post">

      <?php
//include_partial('scenarioshiftform', array('scenarioshiftform' => $scenarioshiftform, 'scenario_id' => $scenario_id))
      include_partial('shiftform',
                      array('scenarioshiftform' => $scenarioshiftform, 'scenario_id' => $scenario_id))
      ?>
</form>

