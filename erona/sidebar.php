<?php
echo "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?".">";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>eRONA Sidebar</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="css.css" rel="stylesheet" type="text/css" />
</head>

<body>
<div id="body">
<span class="sidebar">
<?php

include("connect.php");
include("functions.php");

if (!is_numeric($_GET['p']))
{
    die ('<h3>Warum habe ich einen Referer von dieser Seite in meinen Logs?</h3>Diese Seite kann von <a href="http://" . ERONA_URL . "">eRONA</a>-Benutzern als Sidebar in ihren Browser integriert werden, um die aktuellsten Nachrichten aus ihren Nachrichten-Abonnements anzuzeigen.');
}

$target = "_main";
if (strstr($_SERVER['HTTP_USER_AGENT'], "Opera")) $target = "_blank";
elseif (strstr($_SERVER['HTTP_USER_AGENT'], "Gecko")) $target = "_content";

$sql = "SELECT id FROM user WHERE id=" . $_GET['p'] . " AND public=1";
$res = mysql_query($sql);
$row = mysql_fetch_array($res);

if ($row['id'] != $_GET['p'])
{
    die ("Diese Feed-Sammlung ist nicht öffentlich.");
}

$sql = "SELECT i.url AS iurl, i.title AS ititle, i.date AS idate, f.title AS ftitle, f.url AS furl FROM items AS i LEFT JOIN user_feeds ON i.feed_id = user_feeds.feed_id LEFT JOIN feeds AS f ON i.feed_id = f.id WHERE user_feeds.user_id = " . $_GET['p'] . " ORDER BY i.date DESC LIMIT 10";
$res = mysql_query($sql) or die(mysql_error() . " $sql");

while ($row = mysql_fetch_array($res))
{
    $stamp = parse_w3cdtf($row['idate']);
    $stamp = dst_test($stamp);
    $ftime = date("H:i", $stamp);

    $ftitle = $row['ftitle'];
    if (strlen($ftitle) > 20) { $ftitle = substr($ftitle, 0, 16) . "..."; }
    
    $ititle = $row['ititle'];
    if (strlen($ititle) > 25) { $ititle = substr($ititle, 0, 22) . "..."; }

    echo '<p>&bull; [' . $ftime . '] ' . $ftitle . '<br />';
    echo '<a target="' . $target . '" href="' . $row['iurl'] . '" title="' . $row['ititle'] . '">' . $ititle . '</a></p>';
}

?>
<br />Powered by <a target="_content" href="http://" . ERONA_URL . ""><strong>eRONA</strong></a>
</span>
</div>
</body>
</html>
