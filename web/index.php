<?php

//if db not installed, redirect to install.php
if (file_exists(dirname(__FILE__) . '/../config/databases.yml') == FALSE) {
  header("Location: " . $_SERVER['REQUEST_URI'] . "install/");
  exit;
}

require_once(dirname(__FILE__) . '/../config/ProjectConfiguration.class.php');

$configuration = ProjectConfiguration::getApplicationConfiguration('frontend', 'prod', false);


sfContext::createInstance($configuration)->dispatch();

// no changes
//$application = new sfSymfonyCommandApplication($dispatcher, null,
//  array('symfony_lib_dir' => realpath(dirname(__FILE__).'/..')));
//$statusCode = $application->run();
