<h2>Respond</h2>
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. In orci turpis, tristique et ornare ac, consequat quis mi. Curabitur nisl dui, lacinia ut aliquam ut, dictum a dolor. Pellentesque non consectetur justo. Nulla hendrerit mollis dui in adipiscing. Suspendisse potenti. In sem mi, condimentum eget elementum ut, sollicitudin in quam. Nullam tortor lorem, viverra id sagittis vel, pellentesque sit amet orci. Suspendisse ultricies pulvinar ligula, id elementum sem molestie sollicitudin. Sed aliquet molestie eros, eget pretium nunc tempus vel. Phasellus metus turpis, vestibulum eu mattis sit amet, auctor in felis. Morbi tempus sapien eget ipsum dapibus vitae convallis magna ultrices. Vivamus luctus, tortor in consequat laoreet, urna libero iaculis enim, nec pulvinar augue mi eget purus. Vestibulum leo justo, auctor eget dictum sed, dictum id elit. Nunc mattis consectetur lectus quis accumsan.<br><br>

    Aliquam molestie tortor vel eros pretium vel mollis nisi laoreet. Nam semper leo nec lorem commodo ultrices. Praesent dapibus sollicitudin elit pulvinar sagittis. Praesent id velit sed augue sagittis varius ut sed massa. Cras gravida euismod quam, molestie feugiat enim semper ut. Mauris condimentum blandit mattis. Suspendisse potenti. Nullam quis eleifend mauris. Vivamus bibendum erat a lectus egestas mattis. Nunc nec metus pretium tortor venenatis interdum condimentum non nibh. Maecenas non mollis orci. Donec eu feugiat libero.<br><br> </p>
<h3>Please select one of the following actions: </h3>
<table cellspacing="20">
    <tr>
        <td><?php echo link_to('Activate a<br>Scenario', 'scenario/index', array('class' => 'linkButton width140')) ?></td>
        <td><?php echo link_to('Event<br>Management', 'event/index', array('class' => 'linkButton width140')) ?></td>
    </tr>
</table>

<h3>Active Events:</h3>
<?php use_javascript('verticaltabs.pack.js');
    use_stylesheet('verticaltabs.css'); ?>

    <script type="text/javascript">
        $(document).ready(function(){
            $("#textExample").verticaltabs({speed: 500,slideShow: false,activeIndex: 0});
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
                        <a href="<?php echo url_for('event/meta?event=' . urlencode($ag_event->getEventName())) ?>" class="linkButton">Change Event Metadata</a><br><br>
                        <a href="<?php echo url_for('event/staff?event=' . urlencode($ag_event->getEventName())) ?>" class="linkButton">Staff Management</a><br><br>
                        <a href="<?php echo url_for('event/listgroups?event=' . urlencode($ag_event->getEventName())) ?>" class="linkButton">Manage Facility Groups</a><br><br>
                        <a href="<?php echo url_for('event/resolution?event=' . urlencode($ag_event->getEventName())) ?>" class="linkButton">Resolve <?php echo $ag_event->getEventName() ?></a><br><br>
                    </p>
                </li>
        <?php endforeach; ?>
            </ul>
        </div>
<br><br><br><br>

