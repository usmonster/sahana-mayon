<h2><span class="highlightedText"><?php echo $event_name ?></span> Staff Management</h2>

<div class="infoHolder">
<a href="<?php echo url_for('event/staffpool?event=' . $event_name); ?>" class="continueButton" title="View Staff Resource Pools for Event">Modify Staff Resource Pools for Event</a><br/>
<br/>
<a href="<?php echo url_for('event/shifts?event=' . $event_name); ?>" class="continueButton" title="View Shifts for Event">View Shifts for Event</a><br/>
<br/>

</div>

<hr class="ruleGray" />

<?php   $importUrl = url_for('event/active?event=' . urlencode($sf_data->getRaw('event_name')));
                echo link_to($event_name.' Event Management', $importUrl,
                array('class' => 'generalButton', 'title' => $event_name.' Event Management'));
                ?>
