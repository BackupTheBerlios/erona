<?php
error_reporting(E_ERROR | E_PARSE | E_WARNING);

include("connect.php");
include("session.php");

if (!@isset($_SESSION['user_id']))
{
	header("Location: http://wwworker.com/erona/login.php");
}

$_SESSION['list'] = "";

$items = getItems();

$s = $_GET['s'];

switch ($s)
{
    case "all":
         $feeds = "allen Feeds ";
         break;
    case "meine":
         $feeds = "meinen Feeds ";
         break;
    default:
         if (is_numeric($s))
	 {
             $sql = "SELECT * FROM feeds WHERE id = $s";
             $res = mysql_query($sql);
	     $feed = mysql_fetch_array($res);
	     $feeds = $feed['title'];
	 }
         break;
}

echo "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?".">"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>eRONA: Meine Feeds</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<?php readfile(".metas"); ?>
<link href="css.css" rel="stylesheet" type="text/css" />
</head>

<body>
<script type="text/javascript">
<!--
function reloadPage(i)
{
    location.href = 'http://wwworker.com/erona/myfeeds_index.php?<?php echo $_SERVER['QUERY_STRING']; ?>#' + i;
}
-->
</script>
<span class="liste">

<?php

if ( (count($items[0]) == 0) && ($s == "meine") )
{
    die('Sie haben noch keine Feeds abonniert. Fügen sie mit dem Formular auf der linken Seite einen Feed hinzu, oder werfen Sie einen Blick auf die <a href="feeds.php">Liste</a> aller eRONA bekannten Feeds und abonnieren Sie von dort aus.');
}

if (is_numeric($s))
{
    echo '<a href="' . $feed['url'] . '" target="_blank" title="' . $feed['title'] . ': ' . $feed['description '] . '">' . $feed['title'] . '</a> (<a href="' . $feed['rss'] . '" target="_blank">XML</a>)<br />' . $items[0]['fdes'] . '<br />';
}


echo "\n" . '<form action="search.php' . '?s=' . $_GET['s'] . '" method="post">
<label for="string"><b>In ' . $feeds . ' nach</b></label>:
<input type="text" name="string" />
<input type="hidden" name="where" value="' . $_SESSION['item_where'] . '" />
<input type="hidden" name="feed" value="' . $feeds . '" />
<input type="submit" value="suchen." />
</form><br />';

if ($items === 0)
{
    echo "Dieser Feed hat seit mehr als 14 Tagen keine neuen Einträge.";
}

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

    echo '<a name="' . $i . '">' . $ftime . '</a>: ' . $start_tag . '<a title="' . $item['ftitle'] . ': ' . $item['ititle'] . '" onclick="reloadPage(' . $i . ');" href="data.php?in=' . $i . '&amp;s=' . $_GET['s'] . '#' . $i . '" target="dataFrame">' . $item['ititle'] . '</a>' . $end_tag . '<br />' . "\n";

}

$_SESSION['list'] = serialize($list);

?>
</span>
</body>
</html>
