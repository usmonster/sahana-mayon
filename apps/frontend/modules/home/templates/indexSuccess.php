
<h2>New York City Sahana Agasti</h2>

<?php
if ($sf_user->isAuthenticated()) {
  echo "<h3> Welcome to Sahana Agasti Emergency Preparedness and Response Software</h3>";
}
?>

<p>Sahana Agasti is the NYC Office of Emergency Management Coastal emergency planning and 
  response application with tools to manage staff and facility resources, response plans, and deploy
  emergency event response via an easy to use web interface. </p>

<?php
if (!$sf_user->isAuthenticated()) {
  echo "<h2> To begin, please login in the upper right.</h2>";
}
?>

<?php
if ($sf_user->isAuthenticated()) {
?>
  <h3>To begin, select an option from the menus above or the icons below.</h3>  
  
  
  <table cellspacing='20'>
    <tr>
      <td><?php echo link_to('Prepare', 'home/prepare', array('class' => 'generalButton width140', 'title' => 'Prepare a Response Scenario')); ?></td>
      <td><?php echo link_to('Respond', 'home/respond', array('class' => 'generalButton width140', 'title' => 'Deploy and Manage Events')); ?></td>
    </tr>
    <tr>
      <td><?php echo link_to('Wiki Home', public_path('wiki/doku.php'), array('class' => 'generalButton width140', 'title' => 'Help', 'target' => '_blank')); ?></td>
      <td><?php echo link_to('Administration', 'admin/index', array('class' => 'generalButton width140', 'title' => 'Administration')); ?></td>
    </tr>
  </table>
<?php
}
?>