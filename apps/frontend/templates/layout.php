<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <?php include_http_metas() ?>
    <?php include_metas() ?>
    <?php include_title() ?>
    <link rel="shortcut icon" href="<?php echo url_for('images/favicon.ico') ?>" />
    <?php include_stylesheets() ?>
    <?php include_javascripts() ?> 
    <!--[if lt IE 9]>
    <script src="<?php echo url_for('js/IE9.js') ?>"></script>
    <![endif]-->
  </head>
  <body>
    <div id="header">
      <div style="float: left;">
        <h1>Sahana Agasti:</h1><h2> Emergency Management </h2>
        <?php
        $configFilePath = sfConfig::get('sf_config_dir') . '/config.yml';
        if (file_exists($configFilePath)) {
          $cfgArray = sfYaml::load($configFilePath);
          $configArray['authMethod'] = $cfgArray['admin']['auth_method']['value'];
          if ($configArray['authMethod'] == 'bypass' && $sf_user->isAuthenticated()) {
            echo '<a href="' . url_for('admin/config') . '" class="alertButton" title="System Settings">Super Administrator Mode is currently enabled. Click here to disable.</a>' . PHP_EOL;
          }
        }
        ?>
      </div>
      <?php include_component('sfGuardAuth', 'login'); ?>
      </div>
      <div id="wrapper">
        <div id="navigation">
          <!-- Some of these nav links don't all actually work; some are just placeholders for now so it looks nice. -->
        <?php include_component('nav', 'Menu'); ?>
        <?php $loggedIn = $sf_user->isAuthenticated(); ?>
        <?php if ($loggedIn): ?>
          <span style="margin: .1em; float: right;">
          <?php include_partial('agStaff/searchForm'); ?>
        </span>
        <?php endif ?>
        </div>
        <div id="columns">
        <?php echo $sf_content ?>
          <!-- Maybe we should somehow disable the other login form from displaying in
          sf_content
          the event that you're not in the home module. -->
        </div>
      </div>
      <div id="footer">
      <?php echo image_tag('logo.png', array('alt' => '[The Seal of the City of New York]')) ?>
    </div>
  </body>
</html>
