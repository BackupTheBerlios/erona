<?php

include("connect.php");
include("session.php");

if (!@isset($_SESSION['user_id']))
{
	header("Location: http://" . ERONA_URL . "login.php");
}

$_SESSION['suche'] = "";
$_SESSION['list']  = "";
$list              = array();

if ( (!empty($_GET['t'])) && (!empty($_GET['s'])) )
{
	$reload = urldecode($_GET['t']) . " durchsuchen";
	$query = "?s=" . $_GET['s'];
} else
{
	$reload = "alle meine Feeds durchsuchen";
}

echo "<?xml version=\"1.0\" encoding=\"iso-8859-15\"?".">";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>eRONA: Meine Feeds</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-15" />
<link href="css.css" rel="stylesheet" type="text/css" />
</head>

<body style="margin-top: 0px; margin-bottom: 0px;">
<span class="liste">
<?php
$string = trim(urldecode($_POST['string']));
$gefunden = FALSE;

// Böse Menschen davonabhalten, Unfug zu machen
if (stristr($string, "&") || stristr($string, ";") || stristr($string, "'") || stristr($string, "\""))
{
	echo "Fehler: Der Suchterm darf nur alphanumerische Zeichen beinhalten: $string";
	exit;
}

echo "<h3>Suche in " . $_POST['feed'] . " nach <i>$string</i></h3>";

$sql = "SELECT i.num as inum, i.title as ititle, i.stamp_date as idate, i.indexed as iindexed, ui.iread as iread, f.title as ftitle
FROM items as i
LEFT JOIN feeds AS f ON i.feed_id = f.id
LEFT JOIN user_items AS ui ON ui.item_num = i.num AND ui.user_id = " . $_SESSION['user_id'] . "
LEFT JOIN items_contents AS ic ON ic.id = i.num
WHERE MATCH(ic.descr, ic.title) AGAINST ('$string' IN BOOLEAN MODE) AND i.feed_id IN (" . $_SESSION['feed_liste'] . ")
ORDER BY i.stamp_date DESC";

#echo $sql;

$res = mysql_query($sql) or die(mysql_error() . "  " . $sql);

while ($row = mysql_fetch_array($res)) { $items[] = $row; }
$_items = count($items);
if ($_items > 0)
{
	$lday = "";

	for ($i = 0; $i < $_items; $i++)
	{
    $item = $items[$i];

    $indexed = date("d.m.Y, H:i", $item['iindexed']);
    $ftime = date("d.m.Y, H:i", $item['idate']);

    $day  = strftime("%e. %B %Y", $item['idate']);
    $hour = date("G:i", $item['idate']);
    
    $read = $item['iread'];

    $search = array('%C3%84', '%C3%96', '%C3%9C', '%C3%A4', '%C3%B6', '%C3%BC');
    $replace = array('&Auml;', '&Ouml;', '&Uuml;', '&auml;', '&ouml;', '&uuml;');

    $item['ititle'] = urlencode($item['ititle']);
    $item['ititle'] = str_replace($search, $replace, $item['ititle']);
    $item['ititle'] = urldecode($item['ititle']);
    $item['ititle'] = stripslashes($item['ititle']);

    $item['ftitle'] = urlencode($item['ftitle']);
    $item['ftitle'] = str_replace($search, $replace, $item['ftitle']);
    $item['ftitle'] = urldecode($item['ftitle']);
    $item['ftitle'] = stripslashes($item['ftitle']);    

    $date_indexed = ($ftime == $indexed) ? '*' : '';

    $start_tag = $end_tag = "";

    if ( ($read != 1) && ($stamp > $_SESSION['lasttime']) && ($_SESSION['public'] != 1) )
    {
    	$start_tag = "<strong>";
    	$end_tag   = "</strong>";

    }

    if ($day <> $lday)
    {
    	echo '<div class="headline"><span class="dateline">' . $day . ' <a style="text-decoration: none;" href="#top">top</a></span></div>';
    }

    echo '<div class="date"><a name="' . $i . '">' . $hour . 'h</a></div><div class="date_indexed">' . $date_indexed . '</div><div class="headline">' . $start_tag . '<a title="' . $item['ftitle'] . ': ' . htmlspecialchars($item['ititle']) . ' - idate: ' . $ftime . ', indexed: ' . $indexed . '" onclick="reloadPage(' . $i . ');" href="data.php?in=' . $item['inum'] . '&amp;s=' . $_GET['s'] . '#' . $i . '" target="dataFrame">' . $item['ititle'] . '</a>' . $end_tag . '</div>' . "\n";

    $lday = $day;
	}

} else
{
	echo "Deine Suche nach <i>$string</i> in " . $_POST['feed'] . " war leider erfolglos: keine Treffer ;-(";
}

$_SESSION['suche'] = $string;

#$_SESSION['list'] = serialize($list);

?>
</span>
</body>
</html>
