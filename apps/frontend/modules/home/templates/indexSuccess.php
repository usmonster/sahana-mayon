
<h2> New York City Sahana Agasti</h2>

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

<p> Agasti 2.0 is the NYC Office of Emergency Management Coastal Storm Plan web application.
  Agasti is an emergency management application with tools to manage staff, resources, client
  information and facilities through an easy to use web interface. </p>

<p>To begin, select an option from the menus above.</p>

<?php
if ($sf_user->isAuthenticated()) {
  echo "<table cellspacing='20'>
    <tr>
      <td><a href='./prepare' class='linkButton width140'>Prepare</a></td>
      <td><a href='./scenario' class='linkButton width140'>Respond</a></td>
    </tr>
    <tr>
      <td><a href='./wiki' class='linkButton width140'>Wiki Home</a></td>
      <td><a href='./admin' class='linkButton width140'>Administration</a></td>
    </tr>
</table>";
}
?>