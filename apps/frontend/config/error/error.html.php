
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<?php $path = sfConfig::get('sf_relative_url_root', preg_replace('#/[^/]+\.php5?$#', '', isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : (isset($_SERVER['ORIG_SCRIPT_NAME']) ? $_SERVER['ORIG_SCRIPT_NAME'] : ''))) ?>
<?php require_once(sfConfig::get('sf_lib_dir') . '/vendor/symfony/lib/helper/AssetHelper.php'); ?>

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <!--This page allows only static content-->
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="title" content="Sahana Agasti, Mayon 2.0" />
        <meta name="description" content="Emergency Management" />
        <meta name="language" content="en" />
        <title>Sahana Agasti, Mayon 2.0</title>
        <link rel="shortcut icon" href="/agasti/images/favicon.ico" />
        <link rel="stylesheet" type="text/css" media="screen" href="/agasti/css/main.css" />
        <script type="text/javascript" src="../../../../web/js/jquery.js"></script>
        <script type="text/javascript" src="../../../../web/js/Menu_Shape.js"></script>
        <!--[if lt IE 9]>
        <script type="text/javascript" src="/agasti/js/IE9.js"></script>
        <![endif]-->
    </head>
    <body>
        <div id="header">
            <div class="floatLeft">
                <img src="../../../../web/images/Sahana_logo.png" alt="Sahana Agasti: Emergency Management" class="logo"/>
                <!--<h1>Sahana Agasti:</h1><h2> Emergency Management </h2>-->
            </div>
        </div>
        <div id="wrapper">
            <div id="navigation">
            </div>
            <div id="columns">
                <h2> New York City Sahana Agasti</h2>
                <br/>
                <h5>The server returned a "<?php echo $code ?> <?php echo $text ?>".</h5>
                <br/>
                <h4>
                <?php echo $exception->getmessage(); ?>
                </h4>
                <br/>
                <br/>
                <?php $url = htmlspecialchars($_SERVER['HTTP_REFERER']); echo "<a href='$url' class='linkButton'>Back</a>"; ?>
                <a href="home" class="linkButton">Return to homepage.</a>
            </div>
        </div>
        <div id="footer">
            <img alt="[Agasti Logo]" src="../../../../web/images/agasti_logo.png" />      <img alt="[The Seal of the City of New York]" src="../../../../web/images/logo.png" />
        </div>
    </body>
</html>