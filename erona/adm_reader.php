<?php
error_reporting(E_ALL);
include("connect.php");

    $sql = "SELECT id FROM feeds";
    $res = mysql_query($sql);
    
    while ($row = mysql_fetch_array($res))
    {

        $sql = "SELECT COUNT(uf.feed_id) AS c FROM user_feeds AS uf WHERE uf.feed_id = " . $row['id'];
        $res2 = mysql_query($sql);
        $row2 = mysql_fetch_array($res2);
        $ufc = $row2['c'];

        $sql = "UPDATE feeds SET reader = " . $ufc . " WHERE id = " . $row['id'];
        $res3 = mysql_query($sql);
    }
?>
