<h2>Respond</h2>
<p>From this page you can quickly navigate to Sahana Agasti's emergency response features.
<br/>Active events may be accessed directly from this page. For access to all events, click the List
  All Events button below.</p>
<h3>Please select one of the following actions: </h3>
<table cellspacing="20">
    <tr>
        <td><?php echo link_to('Deploy a<br>Scenario', 'scenario/list',
            array('class' => 'generalButton width140')) ?></td>
        <td><?php echo link_to('List All<br>Events', 'event/index',
            array('class' => 'generalButton width140')) ?></td>
    </tr>
</table>

<h3>Active Events:</h3>
<?php use_javascript('agVerticalTabs.js');
    use_stylesheet('agVerticalTabs.css'); ?>

    <script type="text/javascript">
        $(document).ready(function(){
            $("#textExample").verticaltabs();
        });
    </script>
    <div class="verticalslider" id="textExample">
        <ul class="verticalslider_tabs">
        <?php foreach ($ag_events as $ag_event): ?>
            <li><a href="#"><?php echo $ag_event->getEventName() ?></a></li>
        <?php endforeach; ?>
        </ul>
        <ul class="verticalslider_contents">
        <?php foreach ($ag_events as $ag_event): ?>
                <li>
                    <p>
                        <a href="<?php echo url_for('event/meta?event=' . urlencode($ag_event->getEventName())) ?>" class="continueButton">Change Event Metadata</a><br><br>
                        <a href="<?php echo url_for('event/staff?event=' . urlencode($ag_event->getEventName())) ?>" class="continueButton">Staff Management</a><br><br>
                        <a href="<?php echo url_for('event/listgroups?event=' . urlencode($ag_event->getEventName())) ?>" class="continueButton">Manage Facility Groups</a><br><br>
                        <a href="<?php echo url_for('event/resolution?event=' . urlencode($ag_event->getEventName())) ?>" class="continueButton">Resolve <?php echo $ag_event->getEventName() ?></a><br><br>
                    </p>
                </li>
        <?php endforeach; ?>
            </ul>
        </div>
<br><br><br><br>

