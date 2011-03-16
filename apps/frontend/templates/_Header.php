<?php include_http_metas() ?>
    <?php include_metas() ?>
    <?php include_title() ?>
    <link rel="shortcut icon" href="<?php echo url_for('images/favicon.ico') ?>" />
    <?php include_stylesheets() ?>
    <!--[if lte IE 6]><!-->
    <link rel="stylesheet" type="text/css" href="<?php echo url_for('css/lte_ie6_css.css') ?>" media="screen" />
    <!--<![endif]-->

    <?php include_javascripts() ?>

    <!--[if !lte IE 6]><!-->
     <link rel="stylesheet" type="text/css" href="<?php echo url_for('css/menu.css') ?>" media="screen" />
    <!--<![endif]-->
     <!--[if lt IE 9]>
    <?php echo javascript_include_tag('IE9'); ?>
    <![endif]-->