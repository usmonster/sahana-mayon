<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <?php include_partial('global/Header'); ?>
  </head>
  <body>
    <div id="header">
      <div class="floatLeft">
        <?php //echo link_to(image_tag('Sahana_logo.png', array('class' => 'logo', 'alt' => 'Sahana Agasti: Emergency Management')),'home/index') ?>
        <a href="<?php echo url_for('home/index'); ?>" style="text-decoration: none"><h1><?php echo sfConfig::get('sf_application_name'); ?></h1></a>
        <?php
        $configFilePath = sfConfig::get('sf_config_dir') . '/config.yml';
        if (file_exists($configFilePath)) {
          $cfgArray = sfYaml::load($configFilePath);
          $configArray['authMethod'] = $cfgArray['admin']['auth_method']['value'];
          if ($configArray['authMethod'] == 'bypass' && $sf_user->isAuthenticated()) {
            echo link_to(
                'Super Administrator Mode is currently enabled. Click here to disable.',
                'admin/config',
                array('class' => 'alertButton', 'title' => 'System Settings'));
          }
        }
        ?>
      </div>
      <?php include_component('sfGuardAuth', 'login'); ?>
      </div>
      <div id="wrapper">
        <div id="navigation">
        <?php $loggedIn = $sf_user->isAuthenticated(); ?>
        <?php if ($loggedIn): ?>
          <!-- Some of these nav links don't all actually work; some are just placeholders for now so it looks nice. -->
        <?php include_component('nav', 'Menu'); ?>
          <span class="floatRight MarginPointOneEm">
          <?php include_partial('search/searchForm'); ?>
        </span>
        <?php endif ?>
        </div>
        <div id="columns">

        <?php echo $sf_content ?>

        </div>
      </div>
    <?php include_partial('global/Footer'); ?>
  </body>
</html>
