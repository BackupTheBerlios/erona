<?php
$dbu = "dbuser";
$dbp = "dbpassword";
$dbn = "dbname";
$dbs = "dbserver";

mysql_connect($dbs, $dbu, $dbp) or die("dbms error");
mysql_select_db($dbn) or die("dn error");

ini_set("track_errors", "1");

?>
