<?php
error_reporting(E_ERROR | E_PARSE | E_WARNING);

include("connect.php");
include("session.php");

if (!@isset($_SESSION['user_id']))
{
	header("Location: http://wwworker.com/erona/login.php");
}


echo "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?".">"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>eRONA: Meine Feeds</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="css.css" rel="stylesheet" type="text/css" />
</head>

<body style="margin: 10px;">
<script type="text/javascript">
<!--
function reloadPage(i,s)
{
    parent.mainFrame.location.href = 'http://wwworker.com/erona/myfeeds_index.php?in=' + i + '&s=' + s + '#' + i;
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
    $s = $_GET['s'];
}

$list = @unserialize($_SESSION['list']);

#print_r($list);

$next = $prev = FALSE;

$prev = @$list[($_GET['in'])]['prev'];
$next = @$list[($_GET['in'])]['next'];
$n = $list[($_GET['in'])]['id'];

$item = getItem($n);

$stamp = @parse_w3cdtf($item['idate']);
$stamp = @dst_test($stamp);
$ftime = @date("d.m.Y, H:i", $stamp);

echo '<table width="90%">
    <tr>
        <td align="left">';
#if ($prev && ($s != "suche")) { echo '<a onclick="reloadPage(' . ($_GET['in'] - 1) . ', ' . "'" . $s . "'" . ');" href="data.php?n=' . ($prev) . '&amp;in=' . ($in - 1) . '&amp;s=' . $s . '">&lt;&nbsp;vorheriger Artikel</a>'; }
echo '</td>
        <td width="90%" align="center"><a href="' . $item['furl'] . '" target="_blank" title="' .$item['ftitle'] . '">' . $item['ftitle'] . '</a>, ' . $ftime . '<br /><br />
            <a href="' . $item['iurl'] . '" target="_blank"><b>' . $item['ititle'] . '</b></a></td>
        <td align="right">';
#if ($next && ($s != "suche")) { echo '<a onclick="reloadPage(' . ($_GET['in'] + 1) . ', ' . "'" . $s . "'" . ');" href="data.php?n=' . ($next) . '&amp;in=' . ($in + 1) . '&amp;s=' . $s . '">n&auml;chster Artikel&nbsp;&gt;</a>'; }
echo '</td>
    </tr>
</table>
<br /><hr size="0" noshade="noshade" />';

/*
echo '<span class="liste"><div align="center">';
echo '<a href="data.php?n=' . ($_GET['prev']) . '">&lt; vorheriger Artikel</a> ';
echo '<a href="' . $item['furl'] . '" target="dataFrame" title="' .$item['ftitle'] . '">' . $item['ftitle'] . '</a>, ';
echo "$ftime: ";
echo '<a href="' . $item['iurl'] . '" target="dataFrame">' . $item['ititle'] . '</a> ';
echo '<a href="data.php?n=' . ($_GET['next']) . '">nächster Artikel &gt;</a><br /><br />';
echo '</div></span><br /><hr size="1" noshade />';
*/

echo $item['idescr'];

if ( ($_SESSION['public'] != 1) && ($list[($_GET['in'])]['iread'] == 0) )
{
    $sql = "INSERT INTO user_items (user_id, item_num, iread) VALUES ('" . $_SESSION['user_id'] . "', $n, 1)";
    $res = mysql_query($sql) or die(mysql_error() . $sql);
    $list[($_GET['in'])]['iread'] = 1;
}

$_SESSION['list'] = serialize($list);

?>
</body>
</html>
