<?php

error_reporting(0);

$dbu = "database user";
$dbp = "database password";
$dbn = "database name";
$dbs = "database server";

// for local development
if ($_SERVER['HTTP_HOST'] == 'localhost')
{
	$dbu = "local database user";
	$dbp = "local database password";
	$dbn = "local database name";
	$dbs = "local database server";
}

mysql_connect($dbs, $dbu, $dbp) or die("dbms error");
mysql_select_db($dbn) or die("dn error");

ini_set("track_errors", "1");

setlocale(LC_ALL, "de_DE");

$dirname = (substr(dirname($_SERVER['REQUEST_URI']), -1) == '/' || substr(dirname($_SERVER['REQUEST_URI']), -1) == '\\') ? dirname($_SERVER['REQUEST_URI']) : dirname($_SERVER['REQUEST_URI']) . '/';

define('ERONA_URL', $_SERVER['HTTP_HOST'] . $dirname);
define('UPDATE_CYCLE', 3600 / 2);
define('UNREAD_SAVE', 7 * 24 * 3600);

define('TIMESPAN_DEFAULT' , 3 * 24 * 3600);
define('TIMESPAN_WEEK' , 7 * 24 * 3600);
define('TIMESPAN_MONTH' , 31 * 24 * 3600);
define('TIMESPAN_QUARTAL' , 90 * 24 * 3600);

define('MAGPIE_USER_AGENT', 'eRONA Beta - http://wwworker.com/erona/');
define('MAGPIE_USE_GZIP', true);
define('MAGPIE_OUTPUT_ENCODING', 'UTF-8');

?>
