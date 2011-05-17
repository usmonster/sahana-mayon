<?php include_http_metas(); ?>
<?php include_metas(); ?>
<?php agTemplateHelper::include_customTitle(); ?>
<link rel="shortcut icon" href="<?php echo public_path('images/favicon.ico'); ?>" />
<?php include_stylesheets(); ?>
<!--[if lte IE 6]>
<?php echo stylesheet_tag('lte_ie6_css'); ?>
<![endif]-->