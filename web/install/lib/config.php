<?php

/**
 * extends agActions for the administration module
 *
 * PHP Version 5.3
 *
 * LICENSE: This source file is subject to LGPLv2.1 license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/lgpl-2.1.html
 *
 * @author Clayton Kramer, CUNY SPS
 * @author Charles Wisniewski, CUNY SPS
 * Copyright of the Sahana Software Foundation, sahanafoundation.org
 */

// Steps array for install menu
$steps[] = array('title' => 'Introduction', 'route' => '/intro');
$steps[] = array('title' => 'License Agreement', 'route' => '/license');
$steps[] = array('title' => 'PHP Requirements', 'route' => '/syscheck');
$steps[] = array('title' => 'File Permissions', 'route' => '/filecheck');
$steps[] = array('title' => 'Configure Database', 'route' => '/dbconfig');
$steps[] = array('title' => 'Super User Settings', 'route' => '/superuser');
$steps[] = array('title' => 'Pre-Install Confirmation', 'route' => '/confirm');
$steps[] = array('title' => 'Installation Summary', 'route' => '/summary');

global $trans;
$trans = array(
  // Translation text for requirements.php
  'S_PHP_VERSION' => 'PHP Version',
  'S_MINIMAL_VERSION_OF_PHP_IS' => 'Minimal version of is',
  'S_PHP_MEMORY_LIMIT' => 'Memory limit',
  'S_IS_A_MINIMAL_PHP_MEMORY_LIMITATION_SMALL' => 'is a minimal memory limitation',
  'S_PHP_POST_MAX_SIZE' => 'Post max size',
  'S_IS_A_MINIMUM_SIZE_OF_PHP_POST_SMALL' => 'is minimum size of post',
  'S_PHP_MAX_EXECUTION_TIME' => 'Max execution time (seconds)',
  'S_PHP_MAX_INPUT_TIME' => 'Max input time (seconds)',
  'S_IS_A_MINIMAL_LIMITATION_EXECTUTION_TIME_SMALL' => 'is a minimal limitation on execution time of scripts',
  'S_IS_A_MINIMAL_LIMITATION_INPUT_PARSE_TIME_SMALL' => 'is a minimal limitation on input parse time for scripts',
  'S_PHP_TIMEZONE' => 'Default timezone set',
  'S_NO_SMALL' => 'no',
  'S_YES_SMALL' => 'yes',
  'S_TIMEZONE_FOR_PHP_IS_NOT_SET' => 'Timezone is not set',
  'S_PLEASE_SET' => 'Please set',
  'S_OPTION_IN_SMALL' => 'option in',
  'S_PHP_DATABASES_SUPPORT' => 'MySQL database support',
  'S_REQUIRES_ANY_DATABASE_SUPPORT' => 'Requires database support for MySQL',
  'S_REQUIRES_BCMATH_MODULE' => 'Requires bcmath module',
  'S_CONFIGURE_PHP_WITH_SMALL' => 'configure with',
  'S_REQUIRES_MB_STRING_MODULE' => 'Requires mb string module',
  'S_PHP_SOCKETS' => 'Sockets',
  'S_REQUIRED_SOCKETS_MODULE' => 'Required sockets module',
  'S_THE_GD_EXTENSION_IS_NOT_LOADED' => 'The GD extension is not loaded.',
  'S_GD_PNG_SUPPORT' => 'GD PNG Support',
  'S_REQUIRES_IMAGES_GENERATION_SUPPORT' => 'Requires images generation support',
  'S_LIBXML_MODULE' => 'libxml module',
  'S_PHPXML_MODULE_IS_NOT_INSTALLED' => 'php-xml module is not installed',
  'S_CTYPE_MODULE' => 'ctype module',
  'S_REQUIRES_CTYPE_MODULE' => 'Requires ctype module',
  'S_PHP_UPLOAD_MAX_FILESIZE' => 'Upload max filesize',
  'S_IS_MINIMAL_FOR_PHP_ULOAD_FILESIZE_SMALL' => 'is minimum for upload filesize',
  'S_SESSION_MODULE' => 'Session',
  'S_REQUIRED_SESSION_MODULE' => 'Required Session module',
  'S_SEC_SMALL' => "seconds",
  'S_M' => "M",
  'S_K' => "K",
  'S_B' => "B",
  'SPACE' => " ",
  'S_APC_VERSION' => "Alternative PHP Cache (APC)",
  'S_MINIMAL_VERSION_OF_APC_IS' => "APC is required and must be at least version "
);
foreach ($trans as $const => $label) {
    if (!defined($const))
        define($const, $label);
}
unset($GLOBALS['trans']);
?>
