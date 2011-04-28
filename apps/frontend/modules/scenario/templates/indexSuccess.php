<h2>Scenario Management</h2>
<p>In Agasti 2.0 a 'Scenario' is, in the simplest definition, a plan.  Specifically, a response plan.
  Through the Scenario Manager in Agasti 2.0 Emergency Managers are able to plan the deployment of
  staff and facility resources for the duration of the emergency response.  Once created, the scenario
  can be customized and modified as the emergency response plan evolves.</p>
<p>In the event of an emergency response the scenario is deployed and becomes a specific 'Event' and  allows the
  emergency responder to customize the pre-created scenario without starting from scratch.  Any changes during
  an event will not affect the scenario.</p>
<strong>Please select one of the following scenario actions:</strong><br />
<?php
echo '<a href="' . url_for('scenario/pre') . '" class="buttonText" title="Create New Scenario">Create Scenario<a/><br/>';
echo '<a href="' . url_for('scenario/list') . '" class="buttonText" title="List Existing Scenarios">List Scenarios</a><br/>';
echo '<a href="' . public_path('wiki/doku.php?id=manual:user:scenario') . '" target="new" class="buttonText" title="Help">Help</a><br/>';
?><br>
Some users may find it helpful to use the wiki walkthrough the first time they create a scenario:<br/>
<?php
echo '<a href="' . public_path('wiki/doku.php?id=manual:user:scenario:walkthrough') . '" target="new" class="buttonText" title="Help">Scenario Creator Walkthrough</a><br/>';
?><br>