<?php
$dbu = "sascha_worker";
$dbp = "a210fHJslGa";
$dbn = "sascha_wwworker_com";
$dbs = "localhost";

mysql_connect($dbs, $dbu, $dbp) or die("dbms error");
mysql_select_db($dbn) or die("dn error");

ini_set("track_errors", "1");

?>
