<?php

/**
 * ZABBIX
 * Copyright (C) 2000-2010 SIA Zabbix
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.
 */
function check_php_version()
{
  $required = '5.2.12';
  $recommended = '5.3.2';

  if (version_compare(phpversion(), $recommended, '>=')) {
    $req = 2;
  } else if (version_compare(phpversion(), $required, '>=')) {
    $req = 1;
  } else {
    $req = 0;
  }

  $result = array(
    'name' => S_PHP_VERSION,
    'current' => phpversion(),
    'required' => $required,
    'recommended' => $recommended,
    'result' => $req,
    'error' => S_MINIMAL_VERSION_OF_PHP_IS . SPACE . $required
  );

  return $result;
}

function check_php_memory_limit()
{
  $required = 256 * 1024 * 1024;
  $recommended = 512 * 1024 * 1024;

  $current = ini_get('memory_limit');

  if (str2mem($current) >= $recommended) {
    $req = 2;
  } else if (str2mem($current) >= $required) {
    $req = 1;
  } else {
    $req = 0;
  }

  $result = array(
    'name' => S_PHP_MEMORY_LIMIT,
    'current' => $current,
    'required' => mem2str($required),
    'recommended' => mem2str($recommended),
    'result' => $req,
    'error' => mem2str($required) . SPACE . S_IS_A_MINIMAL_PHP_MEMORY_LIMITATION_SMALL
  );

  return $result;
}

function check_php_post_max_size()
{
  $required = 16 * 1024 * 1024;
  $recommended = 32 * 1024 * 1024;

  $current = ini_get('post_max_size');

  if (str2mem($current) >= $recommended) {
    $req = 2;
  } else if (str2mem($current) >= $required) {
    $req = 1;
  } else {
    $req = 0;
  }

  $result = array(
    'name' => S_PHP_POST_MAX_SIZE,
    'current' => $current,
    'required' => mem2str($required),
    'recommended' => mem2str($recommended),
    'result' => $req,
    'error' => mem2str($required) . SPACE . S_IS_A_MINIMUM_SIZE_OF_PHP_POST_SMALL
  );

  return $result;
}

function check_php_upload_max_filesize()
{
  $required = 2 * 1024 * 1024;
  $recommended = 16 * 1024 * 1024;

  $current = ini_get('upload_max_filesize');

  if (str2mem($current) >= $recommended) {
    $req = 2;
  } else if (str2mem($current) >= $required) {
    $req = 1;
  } else {
    $req = 0;
  }

  $result = array(
    'name' => S_PHP_UPLOAD_MAX_FILESIZE,
    'current' => $current,
    'required' => mem2str($required),
    'recommended' => mem2str($recommended),
    'result' => $req,
    'error' => mem2str($required) . SPACE . S_IS_MINIMAL_FOR_PHP_ULOAD_FILESIZE_SMALL
  );

  return $result;
}

function check_php_max_execution_time()
{
  $required = 60;
  $recommended = 600;

  $current = ini_get('max_execution_time');

  if ($current >= $recommended) {
    $req = 2;
  } else if ($current >= $required) {
    $req = 1;
  } else {
    $req = 0;
  }

  $result = array(
    'name' => S_PHP_MAX_EXECUTION_TIME,
    'current' => $current,
    'required' => $required,
    'recommended' => $recommended,
    'result' => $req,
    'error' => $required . SPACE . S_SEC_SMALL . SPACE . S_IS_A_MINIMAL_LIMITATION_EXECTUTION_TIME_SMALL
  );

  return $result;
}

function check_php_max_input_time()
{
  $required = 60;
  $recommended = 600;

  $current = ini_get('max_input_time');

  if ($current >= $recommended) {
    $req = 2;
  } else if ($current >= $required) {
    $req = 1;
  } else {
    $req = 0;
  }

  $result = array(
    'name' => S_PHP_MAX_INPUT_TIME,
    'current' => $current,
    'required' => $required,
    'recommended' => $recommended,
    'result' => $req,
    'error' => $required . SPACE . S_SEC_SMALL . SPACE . S_IS_A_MINIMAL_LIMITATION_INPUT_PARSE_TIME_SMALL
  );

  return $result;
}

function check_php_timezone()
{
  $current = ini_get('date.timezone');
  $current = !empty($current);

  $req = $current ? 1 : 0;

  $result = array(
    'name' => S_PHP_TIMEZONE,
    'current' => $req ? ini_get('date.timezone') : S_NO_SMALL,
    'required' => null,
    'recommended' => null,
    'result' => $req,
    'error' => S_TIMEZONE_FOR_PHP_IS_NOT_SET . '.' . SPACE . S_PLEASE_SET . SPACE . '"date.timezone"' . SPACE . S_OPTION_IN_SMALL . SPACE . 'php.ini.'
  );

  return $result;
}

function check_php_databases()
{
  $current = array();

  if (function_exists('mysql_pconnect') &&
      function_exists('mysql_select_db') &&
      function_exists('mysql_error') &&
      function_exists('mysql_query') &&
      function_exists('mysql_fetch_array') &&
      function_exists('mysql_fetch_row') &&
      function_exists('mysql_data_seek') &&
      function_exists('mysql_insert_id')) {

    $current[] = 'MySQL';
    $current[] = '<br \>';
  }

  if (function_exists('pg_pconnect') &&
      function_exists('pg_fetch_array') &&
      function_exists('pg_fetch_row') &&
      function_exists('pg_exec') &&
      function_exists('pg_getlastoid')) {

    $current[] = 'PostgreSQL';
    $current[] = '<br \>';
  }

  if (function_exists('ocilogon') &&
      function_exists('ocierror') &&
      function_exists('ociparse') &&
      function_exists('ociexecute') &&
      function_exists('ocifetchinto')) {

    $current[] = 'Oracle';
    $current[] = '<br \>';
  }

  if (function_exists('sqlite3_open') &&
      function_exists('sqlite3_close') &&
      function_exists('sqlite3_query') &&
      function_exists('sqlite3_error') &&
      function_exists('sqlite3_fetch_array') &&
      function_exists('sqlite3_query_close') &&
      function_exists('sqlite3_exec')) {

    $current[] = 'SQLite3';
    $current[] = '<br \>';
  }

  $req = !empty($current) ? 1 : 0;

  $result = array(
    'name' => S_PHP_DATABASES_SUPPORT,
    'current' => empty($current) ? S_NO_SMALL : '<span>' . $current . '</span>',
    'required' => null,
    'recommended' => null,
    'result' => $req,
    'error' => S_REQUIRES_ANY_DATABASE_SUPPORT
  );

  return $result;
}

function check_php_bc()
{

  $current = function_exists('bcadd') &&
      function_exists('bccomp') &&
      function_exists('bcdiv') &&
      function_exists('bcmod') &&
      function_exists('bcmul') &&
      function_exists('bcpow') &&
      function_exists('bcpowmod') &&
      function_exists('bcscale') &&
      function_exists('bcsqrt') &&
      function_exists('bcsub');

  $req = $current ? 1 : 0;

  $result = array(
    'name' => 'PHP BC math',
    'current' => $req ? S_YES_SMALL : S_NO_SMALL,
    'required' => null,
    'recommended' => null,
    'result' => $req,
    'error' => S_REQUIRES_BCMATH_MODULE . SPACE . '[' . S_CONFIGURE_PHP_WITH_SMALL . SPACE . '--enable-bcmath]'
  );

  return $result;
}

function check_php_mbstring()
{

  $current = mbstrings_available();

  $req = $current ? 1 : 0;

  $result = array(
    'name' => 'PHP MB string',
    'current' => $req ? S_YES_SMALL : S_NO_SMALL,
    'required' => null,
    'recommended' => null,
    'result' => $req,
    'error' => S_REQUIRES_MB_STRING_MODULE . SPACE . '[' . S_CONFIGURE_PHP_WITH_SMALL . SPACE . '--enable-mbstring]'
  );

  return $result;
}

function check_php_sockets()
{

  $current = function_exists('socket_create');

  $req = $current ? 1 : 0;

  $result = array(
    'name' => S_PHP_SOCKETS,
    'current' => $req ? S_YES_SMALL : S_NO_SMALL,
    'required' => null,
    'recommended' => null,
    'result' => $req,
    'error' => S_REQUIRED_SOCKETS_MODULE . SPACE . '[' . S_CONFIGURE_PHP_WITH_SMALL . SPACE . '--enable-sockets]'
  );

  return $result;
}

function check_php_gd()
{

  $required = '2.0';
  $recommended = '2.0.34';

  if (is_callable('gd_info')) {
    $gd_info = gd_info();
    preg_match('/(\d\.?)+/', $gd_info['GD Version'], $current);
    $current = $current[0];
  } else {
    $current = 'unknown';
  }

  if (version_compare($current, $recommended, '>=')) {
    $req = 2;
  } else if (version_compare($current, $required, '>=')) {
    $req = 1;
  } else {
    $req = 0;
  }

  $result = array(
    'name' => 'PHP GD',
    'current' => $current,
    'required' => $required,
    'recommended' => $recommended,
    'result' => $req,
    'error' => S_THE_GD_EXTENSION_IS_NOT_LOADED
  );

  return $result;
}

function check_php_gd_png()
{

  if (is_callable('gd_info')) {
    $gd_info = gd_info();
    $current = isset($gd_info['PNG Support']);
  } else {
    $current = false;
  }

  $req = $current ? 1 : 0;

  $result = array(
    'name' => S_GD_PNG_SUPPORT,
    'current' => $req ? S_YES_SMALL : S_NO_SMALL,
    'required' => null,
    'recommended' => null,
    'result' => $req,
    'error' => S_REQUIRES_IMAGES_GENERATION_SUPPORT . SPACE . '[PNG]'
  );

  return $result;
}

function check_php_xml()
{
  $required = '2.6.15';
  $recommended = '2.7.6';

  $current = constant('LIBXML_DOTTED_VERSION');
  if (version_compare($current, $recommended, '>=')) {
    $req = 2;
  } else if (version_compare($current, $required, '>=')) {
    $req = 1;
  } else {
    $req = 0;
  }

  $result = array(
    'name' => S_LIBXML_MODULE,
    'current' => $current,
    'required' => $required,
    'recommended' => $recommended,
    'result' => $req,
    'error' => S_PHPXML_MODULE_IS_NOT_INSTALLED
  );

  return $result;
}

function check_php_ctype()
{

  $current = function_exists('ctype_alnum') &&
      function_exists('ctype_alpha') &&
      function_exists('ctype_cntrl') &&
      function_exists('ctype_digit') &&
      function_exists('ctype_graph') &&
      function_exists('ctype_lower') &&
      function_exists('ctype_print') &&
      function_exists('ctype_punct') &&
      function_exists('ctype_space') &&
      function_exists('ctype_xdigit') &&
      function_exists('ctype_upper');

  $req = $current ? 1 : 0;

  $result = array(
    'name' => S_CTYPE_MODULE,
    'current' => $req ? S_YES_SMALL : S_NO_SMALL,
    'required' => null,
    'recommended' => null,
    'result' => $req,
    'error' => S_REQUIRES_CTYPE_MODULE . SPACE . '[' . S_CONFIGURE_PHP_WITH_SMALL . SPACE . '--enable-ctype]'
  );

  return $result;
}

function check_php_session()
{
  $current = function_exists('session_start') &&
      function_exists('session_write_close');

  $req = $current ? 1 : 0;

  return array(
    'name' => S_SESSION_MODULE,
    'current' => $req ? S_YES_SMALL : S_NO_SMALL,
    'required' => null,
    'recommended' => null,
    'result' => $req,
    'error' => S_REQUIRED_SESSION_MODULE . SPACE . '[' . S_CONFIGURE_PHP_WITH_SMALL . SPACE . '--enable-session]'
  );
}

function mbstrings_available()
{
  $mbstrings_fnc_exist =
      function_exists('mb_strlen') &&
      function_exists('mb_strtoupper') &&
      function_exists('mb_strpos') &&
      function_exists('mb_substr'); //&&
  // function_exists('mb_stristr') &&
  // function_exists('mb_strstr');

  return $mbstrings_fnc_exist;
}

function check_php_requirements()
{
  $result = array();

  $result[] = check_php_version();
  $result[] = check_php_memory_limit();
  # TODO: uncomment this when staff/facility import is working
  #$result[] = check_php_post_max_size(); 
//  $result[] = check_file_permissions(sfConfig::get('sf_cache_dir'),'0775');
//  $result[] = check_file_permissions(sfConfig::get('sf_log_dir'),'0775');
  $result[] = check_file_permissions(sfConfig::get('sf_config_dir'), '0775');
  $result[] = check_file_permissions(sfConfig::get('sf_app_config_dir'), '0775');
  $result[] = check_file_permissions(sfConfig::get('sf_data_dir') . '/indexes/', '0775');
  $result[] = check_file_permissions(sfConfig::get('sf_data_dir') . '/search/', '0775');
  $result[] = check_file_permissions(sfConfig::get('sf_data_dir') . '/sql/', '0775');

  // removing these for now, since they're not explicit requirements
//      $result[] = check_php_post_max_size();
//		$result[] = check_php_upload_max_filesize();
//		$result[] = check_php_max_execution_time();
//		$result[] = check_php_max_input_time();
//		$result[] = check_php_timezone();
//		$result[] = check_php_databases();
//		$result[] = check_php_bc();
//		$result[] = check_php_mbstring();
//		$result[] = check_php_sockets();
//		$result[] = check_php_session();
//		$result[] = check_php_gd();
//		$result[] = check_php_gd_png();
//		$result[] = check_php_xml();
//		$result[] = check_php_ctype();

  return $result;
}

function check_file_permissions($path, $requiredPerms)
{
  // TODO: just use is_readable/is_writeable instead? -UA.
  clearstatcache();
  $current = substr(sprintf('%o', fileperms($path)), -4);
  $ret = (($current >= $perm) ? 2 : 0);

  $result = array(
    'name' => $path,
    'current' => $current,
    'required' => $requiredPerms,
    'recommended' => $requiredPerms,
    'result' => $ret,
    'error' => 'fail'
  );
  return $result;
}

?>
