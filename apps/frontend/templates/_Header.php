<?php include_http_metas() ?>
    <?php include_metas(); ?>
    <?php include_title(); ?>
    <link rel="shortcut icon" href="<?php echo url_for('images/favicon.ico'); ?>" />
    <?php include_stylesheets(); ?>
    <!--[if lte IE 6]>
    <?php echo stylesheet_tag('lte_ie6_css'); ?>
    <![endif]-->
    <?php include_javascripts(); ?>
    <!--[if lt IE 9]>
    <?php echo javascript_include_tag('IE9'); ?>
    <![endif]-->