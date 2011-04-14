
<h2>New York City Sahana Agasti</h2>

<?php
if (!$sf_user->isAuthenticated()) {
  echo "<h2> To begin, please login in the upper right.</h2>";
}
?>

<?php
if ($sf_user->isAuthenticated()) {
  echo "<h3> Welcome to Sahana Agasti Emergency Preparedness and Response Software</h3>";
}
?>

<p>Agasti 2.0 is the NYC Office of Emergency Management Coastal Storm Plan web application.
  Agasti is an emergency management application with tools to manage staff, resources, client
  information and facilities through an easy to use web interface. </p>

<p>To begin, select an option from the menus above.</p>

<?php
if ($sf_user->isAuthenticated()) {
?><table cellspacing='20'>
    <tr>
      <td><?php echo link_to('Prepare', 'home/prepare', array('class' => 'linkButton width140', 'title' => 'Prepare')); ?></td>
      <td><?php echo link_to('Respond', 'home/respond', array('class' => 'linkButton width140', 'title' => 'Respond')); ?></td>
    </tr>
    <tr>
      <td><?php echo link_to('Wiki Home', public_path('wiki/doku.php'), array('class' => 'linkButton width140', 'title' => 'Wiki Home')); ?></td>
      <td><?php echo link_to('Administration', 'admin/index', array('class' => 'linkButton width140', 'title' => 'Administration')); ?></td>
    </tr>
  </table>
<?php
}
?>