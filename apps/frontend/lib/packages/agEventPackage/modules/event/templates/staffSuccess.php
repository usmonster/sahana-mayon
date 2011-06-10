<?php
use_javascript('agasti.js');
use_javascript('agastiMessaging.js');
use_javascript('jQuery.fileinput.js');
use_javascript('jquery.ui.custom.js');
use_stylesheet('jquery/jquery.ui.custom.css');
use_stylesheet('jquery/mayon.jquery.ui.css');
?>


<h2>Staff Management: <span class="highlightedText"><?php echo $event_name ?></span></h2>
<br/>
<h4>Manage staff pools and shifts for the  <span class="highlightedText"><?php echo $event_name; ?></span> event.</h4>

<table class="blueTable">
  <tr class="head">
    <th class="row1">Steps</th>
    <th>Description</th>
  </tr>
  <tr>
    <td>
        <a href="<?php echo url_for('event/staffpool?event=' . $event_name); ?>" class="generalButton" title="View Staff Resource Pools for Event">Modify Staff Resource Pools for Event</a>
        <br/></td>
    <td>texty</td>

  </tr>
  <tr>
    <td>
        <a href="<?php echo url_for('event/shifts?event=' . $event_name); ?>" class="generalButton" title="View Shifts for Event">View Shifts for Event</a><br/>
    </td>
    <td>texty</td>


  </tr></table>
<br/>

<?php
$importUrl = url_for('event/active?event=' . urlencode($sf_data->getRaw('event_name')));
echo link_to($event_name . ' Event Management', $importUrl,
    array('class' => 'generalButton', 'title' => $event_name . ' Event Management'));
?>
