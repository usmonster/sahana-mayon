<p> Here you can tweak the staff resource pools you have defined in the planning phase. </p>
<h2><span class="highlightedText"><?php echo $event_name ?></span> Staff Pool Management</h2><br/>
<br /> <em>maybe show existing saved searches here?</em>


<form action="<?php echo url_for($form_action) ?>" method="get" name="filter">
<input type="submit" value="Filter By:" class="linkButton">
    <?php echo $filterForm ?> <!-- onChange: filter. -->
</form>
<br />
<h3>Current Event Staff Members, <?php echo $pager->getFirstIndice() . "-" . $pager->getLastIndice() . " of " . $pager->count() . ((isset($event)) ? ' for the <span class="highlightedText">' . $event_name . '</span> Event' : ' for all Events'); ?></h3>



<?php
$columns = array(
  'es_id' => array('title' => 'First Name', 'sortable' => false),
  'es_id' => array('title' => 'Last Name', 'sortable' => false),
  'organization_name' => array('title' => 'Organization', 'sortable' => true),
  'staff_status' => array('title' => 'Staff Status', 'sortable' => true),
  'type' => array('title' => 'Staff Type', 'sortable' => true),
  'widget' => array('title' => 'Event Status', 'sortable' => false),
);

//pager comes in from the action

include_partial('global/listform', array('sf_request' => $sf_request, 'form_action' => $form_action, 'event_id' => $event_id, 'columns' => $columns, 'pager' => $pager, 'widget' => $widget));

//echo agListForm::eventstafflist($sf_request,'Event Staff Listing', $columns, $pager, $widget);
?>
