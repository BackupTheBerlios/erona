<?php
include("connect.php");
echo "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?".">";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>easy RSS</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="css.css" rel="stylesheet" type="text/css" />
</head>

<body>
<div id="body">
<p><span class="kopf">eRONA: easy RSS Online News Aggregator</span></p>
<p><span class="nav">
<h3>Öffentliche Feed-Sammlungen</h3>
<?php

$sql = "SELECT id, title, descr FROM user WHERE public = 1";
$res = mysql_query($sql);
while ($row = mysql_fetch_array($res))
{
    if (strlen(trim($row['title'])) < 2)
    {
        $title = "Öffentliche Feed-Sammlung n. n.";
    } else
    {
        $title = trim($row['title']);
    }

    if (strlen(trim($row['descr'])) < 2)
    {
        $descr = "n/a";
    } else
    {
        $descr = trim($row['descr']);
    }

    echo '<p>&bull; <a href="myfeeds.php?p=' . $row['id'] . '">' . $title . '</a><br />' . $descr . '</p>' . "\n";
}
?>
</span></p>
</body>
</html>
