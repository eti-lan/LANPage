<?php
/**
 * jQuery Bracket server - release 1
 *
 * Copyright (c) 2012, Teijo Laine,
 * http://aropupu.fi/bracket-server/
 *
 * Licenced under the MIT licence
 * 
 * Copyright (c) 2020, fly
 */
 
 // get $competition_edit_password
if (file_exists(stream_resolve_include_path('../config.php'))) {
	include_once('../config.php');
} else if (file_exists(stream_resolve_include_path('../config.sample.php'))) {
	include_once('../config.sample.php');
} else {
	die;
}

/** 
 * incompatible with PHP 7.x
if (get_magic_quotes_gpc()) {
  $process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
  while (list($key, $val) = each($process)) {
    foreach ($val as $k => $v) {
      unset($process[$key][$k]);
      if (is_array($v)) {
        $process[$key][stripslashes($k)] = $v;
        $process[] = &$process[$key][stripslashes($k)];
      } else {
        $process[$key][stripslashes($k)] = stripslashes($v);
      }
    }
  }
  unset($process);
}
 */

define('VALID_PATTERN', '[0-9A-Za-z_]+');
define('PATH', './data/');

function get($id) { return isset($_GET[$id])?$_GET[$id]:false; }
function pathwrap($id) { return PATH.'jqb_'.$id.'.json'; }

$args = array('op' => get('op'),
              'id' => get('id'),
              'data' => get('data'),
			  'token' => get('token'));

$error = false;

$restApi = array(
  'get' => 'apiGet',
  'set' => 'apiSet',
  'list' => 'apiList',
  'delete' => 'apiDelete'
);

if (!in_array($args['op'], array_keys($restApi))) {
  doError("invalid op");
}
if ($args['id'] !== false && !preg_match('/^'.VALID_PATTERN.'$/', $args['id'])) {
  doError("invalid id");
}
if ($args['op']=='delete' && $args['token'] != md5($competition_edit_password)) {
  doError("invalid token");
}


function doError($reason="") {
  header('content-type: json; charset=utf-8');
  header("HTTP/1.0 404 Not Found");
  echo 'Error '.$reason.'- Syntax is: {"api": ["?op=get&id=<name>", "?op=set&id=<name>&data=<json data>", "?op=list", "?op=delete&id=<name>&token=<token>"]}';
  exit(0);
}

function apiDelete($args) {
  return unlink(pathwrap($args['id']));
}

function apiGet($args) {
  return file_get_contents(pathwrap($args['id']));
}

function apiSet($args) {
  return file_put_contents(pathwrap($args['id']), $args['data']);
}

function apiList($args) {
  $d = dir(PATH);
  $res = '[';
  while (false !== ($entry = $d->read())) {
    if (preg_match('/^jqb_('.VALID_PATTERN.')\.json$/', $entry, $match)) {
      $res .= '"'.$match[1].'",';
    }
  }
  if (strlen($res) > 2)
    $res = substr($res, 0, strlen($res)-1);
  $res .= ']';
  $d->close();
  return $res;
}

$res = $restApi[$args['op']]($args);

if ($res === false)
  $error = true;
else if (is_string($res)) {
  header('content-type: json; charset=utf-8');
  echo $res;
}

if ($error === true) {
  doError();
}
?>
