
<h2><span class="highlightedText"><?php echo $event_name ?></span>Event Facility Group and Facility Management</h2>
<?php use_javascript('agMain.js'); ?>
<?php
if ($fgroupForm != null){
  include_partial('fgroupForm', array('fgroupForm' => $fgroupForm, 'event_name' => $event_name, 'facilitygroupsForm' => $facilitygroupsForm));
}
else{
?>
<span class="highlightedText"><strong>There are no facility groups that were in the standby state that have been changed to active</span><br />
please click below to change the status of a facility group</strong>
<div class="rightFloat" >
  <a href="<?php echo url_for('event/listgroups?event=' . urlencode($event_name)); ?>" class="linkButton" title="Facilities and Resources">Manage Facility Groups</a><br/>
</div>
<?php
}
    ?>

