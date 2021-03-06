<h2>Prepare</h2>
<p>In the <?php echo sfConfig::get('sf_application_name'); ?>, preparing means creating Scenarios.  Scenarios are plans within the application
that complement your organizations existing emergency plans.</p>
<p><strong>Important:</strong> Resources, like staff or facilities, should be loaded into the application
  before creating a scenario.  Facilities can be entered manually before scenario creation or 
  uploaded during the creation of each scenario.</p>
<p>For more information about Scenario Creation click "Scenario Creator Tutorial" below.</p>

<h3>Please select one of the following actions: </h3><br>
<h4>Manage Resources</h4>
<table cellspacing="20">
    <tr>
      <td><?php echo link_to('Manage<br>Staff', 'staff/index', array('class' => 'generalButton width140', 'title' => 'Add, Edit, Import, and Manage Staff')) ?></td>
      <td><?php echo link_to('Manage<br>Facilities', 'facility/index', array('class' => 'generalButton width140', 'title' => 'Add, Edit, Import, and Manage Facility')) ?></td>
      <td><?php echo link_to('Manage<br>Organizations', 'organization/index', array('class' => 'generalButton width140', 'title' => 'Add, Edit, and Manage Organizations')) ?></td>
    </tr>
</table>
<h4>Manage Scenarios</h4>
<table cellspacing="20">
    <tr>
      <td><?php echo link_to('Create<br>New Scenario', 'scenario/pre', array('class' => 'generalButton width140')) ?></td>
      <td><?php echo link_to('Manage<br>Scenarios', 'scenario/index', array('class' => 'generalButton width140')) ?></td>
      <td><?php echo link_to('Deploy a<br>Scenario', 'event/index', array('class' => 'generalButton width140')) ?></td>
      <td><?php echo link_to('Scenario Creator<br>Tutorial', public_path('wiki/doku.php?id=manual:user:scenario:Tutorial'), array('class' => 'generalButton width140', 'title' => 'Help', 'target' => '_blank')) ?></td>
      <td></td>
    </tr>
</table>

<br><br><br><br>

