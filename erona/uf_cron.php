<?php

include("connect.php");

$sql = "SELECT id FROM user";
$res = mysql_query($sql);

while ($row = mysql_fetch_array($res))
{
    $user = $row['id'];
    
    $sql = "SELECT feed_id FROM user_feeds WHERE user_id = ". $user;
    $res2 = mysql_query($sql);
    
    $fp = fopen("temp/uf_" . $user . ".tmp", "w");

    while ($feeds_row = mysql_fetch_array($res2))
    {
        fwrite($fp, $feeds_row['feed_id'] . "\n");
    }

    fclose($fp);

}

?>
