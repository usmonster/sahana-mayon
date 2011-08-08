<?php use_javascript('agasti.js') ?>
<?php use_javascript('jQuery.fileinput.js') ?>
<?php   use_javascript('jquery.ui.custom.js');
  use_stylesheet('jquery/jquery.ui.custom.css');
  use_stylesheet('jquery/mayon.jquery.ui.css');?>
<h3>Create New Organization <a href="<?php echo url_for('@wiki') . '/doku.php?id=tooltip:organization_new&do=export_xhtmlbody' ?>" class="tooltipTrigger" title="Create New Organization">?</a></h3>
<p>Enter the name and description of the new Organization.</p>

<?php include_partial('form', array('form' => $form)) ?>
