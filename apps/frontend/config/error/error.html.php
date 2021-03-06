<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
$path = sfConfig::get('sf_relative_url_root', preg_replace
            ('#/[^/]+\.php5?$#', '', isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] :
                (isset($_SERVER['ORIG_SCRIPT_NAME']) ? $_SERVER['ORIG_SCRIPT_NAME'] : ''))) ?>
<?php sfProjectConfiguration::getActive()->loadHelpers(array
  ('Helper', 'Tag', 'Url', 'Asset', 'Partial')); ?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <!--This page allows only static content-->
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="title" content="Sahana Agasti, Mayon 2.0" />
    <meta name="description" content="Emergency Management" />
    <meta name="language" content="en" />
    <title>Sahana Agasti, Mayon 2.0</title>
    <link rel="shortcut icon" href="<?php echo public_path('images/favicon.ico'); ?>" />
    <?php echo stylesheet_tag('agMain'); ?>
    <?php echo stylesheet_tag('agMenu'); ?>
    <!--[if lte IE 6]>
    <?php echo stylesheet_tag('lte_ie6_css'); ?>
    <![endif]-->
    <!--[if lt IE 9]>
    <?php echo javascript_include_tag('IE9'); ?>
    <![endif]-->
  </head>
  <body>
    <div id="header">
      <div class="floatLeft">
        <?php //echo link_to(image_tag('Sahana_logo.png', array('class' => 'logo', 'alt' => 'Sahana Agasti: Emergency Management')),'home/index') ?>
        <a href="<?php echo url_for('home/index'); ?>" style="text-decoration: none"><h1>Sahana Agasti:</h1><h2> Emergency Management </h2></a>
      </div>
    </div>
    <div id="wrapper">
      <div id="navigation">
      </div>
      <div id="columns">
        <!--Error occured on page -->
        <!--<br/> <?php echo $_SERVER['REQUEST_URI'] ?> -->
        <h2> New York City Sahana Agasti</h2>
        <br/>
        <h5>The server returned a "<?php echo $code ?> <?php echo $text ?>".</h5>
        <br/>
        <h4>
          <?php preg_match('SQLSTATE',$exception->getmessage()) ?>
          <?php $exception_message = preg_match("/SQLSTATE/i",$exception)? "Error related to MYSQL Database. Please click 'Back' button to continue." : $exception->getmessage(); ?>
          <?php echo $exception_message; ?>
        <!--An error occured on server. Please click 'Back' button to reload the page.-->
        </h4>
        <br/>
        <br/>
        <?php echo link_to('Back', htmlspecialchars($_SERVER['HTTP_REFERER']), array('class' => 'generalButton')); ?>
        <?php echo link_to('Return to homepage.', 'home/index', array('class' => 'generalButton')); ?>
        </div>
      </div>
    <?php include_partial('global/Footer'); ?>
  </body>
</html>