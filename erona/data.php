<?php

#apd_set_pprof_trace('./temp/apd');

include("connect.php");
include("session.php");
#include("I18N_UnicodeString.php");

if (!@isset($_SESSION['user_id']))
{
	header("Location: http://" . ERONA_URL . "login.php");
}

if (!is_numeric($_GET['in']))
{
 	echo "Ohne Eintragnummer zeige ich nichts an...";
 	exit;
}

echo "<?xml version=\"1.0\" encoding=\"utf-8\"?".">"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>eRONA: Meine Feeds</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="css.css" rel="stylesheet" type="text/css" />

</head>

<body style="margin: 10px;">
<script type="text/javascript">
<!--
function reloadPage(i,s)
{
	parent.mainFrame.location.href = 'http://" . ERONA_URL . "myfeeds_index.php?in=' + i + '&s=' + s + '#' + i;
	//parent.mainFrame.scrollTo(parent.mainFrame.document.anchors[i].x, parent.mainFrame.document.anchors[i].y);
	//alert(parent.mainFrame.document.anchors[1].x);
}
-->
</script>
<?php

if (!isset($_GET['s']))
{
	$s = "meine";
} else
{
	$s = intval($_GET['s']);
}

$item = getItem($_GET['in']);

$day  = strftime("%e. %B %Y", $item['idate']);
$hour = date("G:i", $item['idate']);

/*
$item['idescr'] = urlencode($item['idescr']);
$search = array('%C3%84', '%C3%96', '%C3%9C', '%C3%A4', '%C3%B6', '%C3%BC');
$replace = array('&Auml;', '&Ouml;', '&Uuml;', '&auml;', '&ouml;', '&uuml;');
$item['idescr'] = str_replace($search, $replace, $item['idescr']);
$item['idescr'] = urldecode($item['idescr']);

$item['ititle'] = urlencode($item['ititle']);
$item['ititle'] = str_replace($search, $replace, $item['ititle']);
$item['ititle'] = urldecode($item['ititle']);
$item['ititle'] = strip_tags($item['ititle']);
$item['ititle'] = stripslashes($item['ititle']);

$item['ftitle'] = urlencode($item['ftitle']);
$item['ftitle'] = str_replace($search, $replace, $item['ftitle']);
$item['ftitle'] = urldecode($item['ftitle']);
$item['ftitle'] = stripslashes($item['ftitle']);
*/

if (trim($item['idescr']) == '')
{
    $item['idescr'] = '<em>[Zu diesem Eintrag wurde kein Inhalt &uuml;bermittelt.<br /><a href="' . $item['iurl'] . '" target="_blank">&Ouml;ffne ihn im Original</a>, um ihn zu lesen.]</em>';
}
else
{
    $item['idescr'] = stripslashes($item['idescr']);
}

/*
$u = new I18N_UnicodeString(nl2br($item['idescr']), 'HTML');
$item['idescr'] = $u->toUtf8String();
*/

echo '
<div class="dataheadline"><a href="' . $item['iurl'] . '" target="_blank" title="Öffne ' .$item['ititle'] . ' in einem neuen Fenster"><img src="./images/window_list.gif" alt="" /></a> ' . $item['ititle'] . '</div>
<div class="datablog"><a href="myfeeds_index.php?s=' . $item['fid'] . '" target="mainFrame" title="Einträge aus ' .$item['ftitle'] . ' anzeigen">' . $item['ftitle'] . '</a>, ' . $day . ', ' . $hour . '</div>';

echo '<hr size="0" noshade="noshade" style="border: 1px dotted purple;" />';

echo '<div class="data">' . $item['idescr'] . '</div>';

if ( ($_SESSION['public'] != 1) && ($list[($_GET['in'])]['iread'] == 0) )
{
$sql = "INSERT INTO user_items (user_id, item_num, iread, date_read) VALUES ('" . $_SESSION['user_id'] . "', " . $_GET['in'] . ", 1, " . time() . ")";
$res = mysql_query($sql) or die(mysql_error() . $sql);

}

?>
</body>
</html>
