<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<?php $path = sfConfig::get('sf_relative_url_root', preg_replace('#/[^/]+\.php5?$#', '', isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : (isset($_SERVER['ORIG_SCRIPT_NAME']) ? $_SERVER['ORIG_SCRIPT_NAME'] : ''))) ?>

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="title" content="symfony project" />
    <meta name="robots" content="index, follow" />
    <meta name="description" content="symfony project" />
    <meta name="keywords" content="symfony, project" />
    <meta name="language" content="en" />
    <title>Sahana Agasti 2.0</title>

    <link rel="shortcut icon" href="<?php echo $path ?>/images/favicon.ico" />
    <link rel="stylesheet" type="text/css" media="screen" href="<?php echo $path ?>/sf/sf_default/css/screen.css" />
    <!--[if lt IE 7.]>
    <link rel="stylesheet" type="text/css" media="screen" href="<?php echo $path ?>/sf/sf_default/css/ie.css" />
    <![endif]-->

  </head>
  <body>
    <div class="sfTContainer">
      <div class="sfTMessageContainer sfTAlert">
        <img alt="page not found" class="sfTMessageIcon" src="<?php echo $path ?>/sf/sf_default/images/icons/tools48.png" height="48" width="48" />
        <div class="sfTMessageWrap">
          <h1>Oops! An Error Occurred</h1>
          <h5>The server returned a "<?php echo $code ?> <?php echo $text ?>".</h5>
        </div>
      </div>

      <dl class="sfTMessageInfo">
        <dt>Something is broken!</dt>
        <dd>Please e-mail the System Administrator Team
          Sorry for any inconvenience caused.</dd>
        <?php //we can override the error handlers to send information of the error to the view ?>
        <dt>What's next</dt>
        <dd>
          <ul class="sfTIconList">
            <li class="sfTLinkMessage"><a href="javascript:history.go(-1)">Back to previous page</a></li>
            <li class="sfTLinkMessage"><a href="/">Go to Homepage</a></li>
          </ul>
        </dd>
      </dl>
    </div>
  </body>
</html>

