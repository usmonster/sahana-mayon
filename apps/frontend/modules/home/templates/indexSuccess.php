
<h1> New York City Sahana Agasti</h1>

<?php
if (!$sf_user->isAuthenticated()) {
  echo "<h2> To begin, please login in the upper right.</h2>";
}
?>

<?php
if ($sf_user->isAuthenticated()) {
  echo "<h2> Welcome to Sahana Agasti Emergency Preparedness and Response Software</h2>";
}
?>

<p> Agasti 2.0 is the NYC Office of Emergency Management Coastal Storm Plan web application.
  Agasti is an emergency management application with tools to manage staff, resources, client
  information and facilities through an easy to use web interface. </p>
<<<<<<< TREE

<p>Built with PHP, MySQL, Doctrine ORM and Symfony.</p>
=======
<p>To begin, select an option from the menus above.</p>>>>>>>> MERGE-SOURCE
