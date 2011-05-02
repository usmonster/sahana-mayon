<?php
use_javascript('jquery.ui.custom.js');
//use_javascript('agMain.js');
use_stylesheet('jquery/jquery.ui.custom.css');
?>


<form action="<?php echo url_for('scenario/shifttemplates?id=' . $scenario_id); ?>" method="post">
<?php foreach ($shifttemplateforms->getEmbeddedForms() as $key => $shifttemplateform): ?>
    <div class="infoHolder shiftTemplateCounter">
<?php include_partial('newshifttemplateform', array('shifttemplateform' => $shifttemplateform, 'scenario_id' => $scenario_id, 'number' => $key)) ?>

  </div>
<?php endforeach; ?>
    <input type="submit" class="linkButton" value="Save, Generate Shifts and Continue" name="Continue" />
</form>