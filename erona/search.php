<?php
error_reporting(E_ERROR | E_PARSE | E_WARNING);

include("connect.php");
include("session.php");

if (!@isset($_SESSION['user_id']))
{
	header("Location: http://wwworker.com/erona/login.php");
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

echo "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?".">";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>eRONA: Meine Feeds</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
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

$sql = "SELECT i.num as inum, i.title as ititle, i.url as iurl, i.descr as idescr, i.date as idate, ui.iread as iread, f.title as ftitle, f.description as fdes, f.url as furl, f.rss as frss FROM items as i LEFT JOIN feeds as f ON i.feed_id = f.id LEFT JOIN user_items as ui ON ui.item_num = i.num AND ui.user_id = " . $_SESSION['user_id'] . " WHERE MATCH(i.descr, i.title) AGAINST ('$string') AND i.feed_id IN (" . $_SESSION['feed_liste'] . ") ORDER BY i.date DESC";
$res = mysql_query($sql) or die(mysql_error() . "  " . $sql);

while ($row = mysql_fetch_array($res)) { $items[] = $row; }

for ($i = 0; $i < (count($items) - 1); $i++)
{
    $item = $items[$i];
    $stamp = parse_w3cdtf($item['idate']);
    $stamp = dst_test($stamp);
    $ftime = date("d.m.Y, H:i", $stamp);
    
    $next = $prev = FALSE;

    $prev = @$items[($i - 1)]['inum'];
    $next = @$items[($i + 1)]['inum'];
    $read = $item['iread'];

    $list[$i]['id'] = $item['inum'];
    $list[$i]['prev'] = $prev;
    $list[$i]['next'] = $next;
    $list[$i]['iread'] = $read;

    $start_tag = $end_tag   = "";

    if ( ($read < 1) && ($_SESSION['public'] != 1) )
    {
    	$start_tag = "<b>";
    	$end_tag   = "</b>";

    }

    echo '<a name="' . $i . '">' . $ftime . '</a>: ' . $start_tag . '<a title="' . $item['ftitle'] . ': ' . $item['ititle'] . '" onclick="reloadPage(' . $i . ');" href="data.php?in=' . $i . '&amp;s=suche#' . $i . '" target="dataFrame">' . $item['ititle'] . '</a>' . $end_tag . '<br />' . "\n";
    $gefunden = TRUE;
}

#print_r($list);

if (!$gefunden)
{
    echo "Deine Suche nach <i>$string</i> in " . $_POST['feed'] . " war leider erfolglos: keine Treffer ;-(";
}

$_SESSION['suche'] = $string;

$_SESSION['list'] = serialize($list);

?>
</span>
</body>
</html>
