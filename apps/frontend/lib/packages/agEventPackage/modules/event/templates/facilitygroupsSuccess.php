<div id="fgroup">
  <h2><span class="highlightedText"><?php echo $event_name ?></span> Event Facility Group and Facility Management</h2>
  <?php use_javascript('agMain.js'); ?>
  <?php if($fgroupForm != null) : ?>
  <?php include_partial('fgroupForm', array('fgroupForm' => $fgroupForm, 'event_name' => $event_name, 'facilitygroupsForm' => $facilitygroupsForm)); ?>
  <?php else: ?>
  <span class="highlightedText bold">There are no facility groups that were in the standby state that have been changed to active.</span>
  <br />
  <span>Please click below to change the status of a facility group.</span>
  <div class="rightFloat" >
    <a href="<?php echo url_for('event/listgroups?event=' . urlencode($event_name)); ?>" class="continueButton" title="Facilities and Resources">Manage Facility Groups</a><br/>
  </div>
  <?php endif; ?>
</div>