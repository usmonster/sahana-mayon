<h2><span class="highlightedText"><?php echo $event_name ?></span> Resolution</h2>

<p>In order to resolve an event all associated facilities must be closed.</p>
<p>There are <strong><?php echo count($active_facility_groups) //foreach echo link  ?></strong> active facility groups.
<?php if(count($active_facility_groups) >0){
?>
  <p>Please close these facilities before ending the event.</p>
<?php 
}?>
<a href="<?php echo url_for('event/active?event=' . urlencode($event_name)); ?>" class="linkButton">Cancel</a>

<?php include_partial('resForm', array('resForm' => $resForm, 'event_name' => $event_name)) ?>
