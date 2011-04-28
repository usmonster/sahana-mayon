<h2>Prepare</h2>
<p>In Sahana Agasti, preparing means creating Scenarios.  Scenarios are plans within the application
that complement your organizations Emergency plans.</p>
<p><strong>Important:</strong> Resources, like staff or facilities, must be loaded into the application
  before creating a scenario.  Staff must be loaded before scenarios are created.
  Facilities can be entered manually before scenario creation or uploaded during the creation
  of each scenario.</p>
<p>For more information about Scenario Creation click "Scenario Creator Walkthrough" below.</p>

<h3>Please select one of the following actions: </h3><br>
<h4>Manage Resources</h4>
<table cellspacing="20">
    <tr>
      <td><?php echo link_to('Manage<br>Staff', 'staff/index', array('class' => 'linkButton width140')) ?></td>
      <td><?php echo link_to('Manage<br>Facilities', 'facility/index', array('class' => 'linkButton width140')) ?></td>
      <td><?php echo link_to('Manage<br>Organizations', 'organization/index', array('class' => 'linkButton width140')) ?></td>
    </tr>
</table>
<h4>Manage Scenarios</h4>
<table cellspacing="20">
    <tr>
      <td><?php echo link_to('Create<br>New Scenario', 'scenario/pre', array('class' => 'linkButton width140')) ?></td>
      <td><?php echo link_to('Manage<br>Existing Scenarios', 'scenario/index', array('class' => 'linkButton width140')) ?></td>
      <td><?php echo link_to('Deploy a<br>Scenario', 'event/index', array('class' => 'linkButton width140')) ?></td>
      <td><?php echo link_to('Scenario Creator<br>Walkthrough', 'wiki/doku.php?id=manual:user:scenario:walkthrough', array('class' => 'linkButton width140')) ?></td>
      <td></td>
    </tr>
</table>

<br><br><br><br>

