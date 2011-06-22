<?php use_stylesheets_for_form($scenarioForm) ?>
<?php use_javascripts_for_form($scenarioForm) ?>

<h2>Respond</h2>
<p>From this page you can quickly navigate to Sahana Agasti's emergency response features.
<br/>Active events may be accessed directly from this page. For access to all events, click the List
  All Events button below.</p>
<h3>Please select a scenario to base your event on: </h3>
<br/>

<form action="<?php echo url_for('event/meta') ?> " method="post" name="scenario">
  <table>
    <tfoot>
      <tr>
        <td colspan="2">
          <input type="submit" value="Go to Pre-Deployment" class="continueButton" name="create" />
        </td>
      </tr>
    </tfoot>
    <tbody>
      <?php echo $scenarioForm ?>
    </tbody>
  </table>
</form>
<br/> <br/> 

<h3>Active Events:</h3>
<?php use_javascript('agVerticalTabs.js');
    use_stylesheet('agVerticalTabs.css'); ?>

<!--    <script type="text/javascript">
        $(document).ready(function(){
            $("#textExample").verticaltabs();
        });
    </script>-->
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
                        <a href="<?php
                        echo url_for('event/meta?event=' . urlencode($ag_event->getEventName()))
                            ?>" class="generalButton">Event Name and Description</a><br><br>
                        <a href="<?php
                        echo url_for('event/staff?event=' . urlencode($ag_event->getEventName()))
                            ?>" class="generalButton">Staff Management</a><br><br>
                        <a href="<?php
                        echo url_for('event/listgroups?event=' . urlencode($ag_event->getEventName()))
                            ?>" class="generalButton">Manage Facility Groups</a><br><br>
                        <a href="<?php
                        echo url_for('event/messaging?event=' . urlencode($ag_event->getEventName()))
                            ?>" class="generalButton">Message Management</a><br><br>
                        <a href="<?php
                        echo url_for('event/resolution?event=' . urlencode($ag_event->getEventName()))
                            ?>" class="generalButton">Resolve
                         <?php echo $ag_event->getEventName()
                             //TODO replace these with link_tos
                             ?></a><br><br>
                    </p>
                </li>
        <?php endforeach; ?>
            </ul>
        </div>
<br><br>
 <?php echo link_to('List All Events', 'event/index',
            array('class' => 'generalButton')) ?>

<br><br><br><br>

