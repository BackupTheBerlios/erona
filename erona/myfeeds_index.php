<?php

#apd_set_pprof_trace('./temp/apd');

include("connect.php");
include("session.php");

if (!@isset($_SESSION['user_id']))
{
	header("Location: http://" . ERONA_URL . "index.php");
}

$s = (is_numeric($_GET['s'])) ? $_GET['s'] : 'meine';
getFeeds($s);

switch ($_POST['timespan'])
{
 	case 'week':
 		$timespan = TIMESPAN_WEEK;
 		break;
 	case 'month':
 		$timespan = TIMESPAN_MONTH;
 		break;
 	case 'quartal':
 		$timespan = TIMESPAN_QUARTAL;
 		break;
 	default:
 		$timespan = TIMESPAN_DEFAULT;
 		break;
}

switch ($s)
{
    case "all":
         $feeds = "allen Feeds ";
         break;
    case "meine":
         $feeds = "meinen Feeds ";
         $feed['title'] = "Inbox";
         if ($_GET['mark'] == "read")
         {
             $_SESSION['lasttime'] = $time = time();
	     
	     $sql = "UPDATE user SET lasttime = " . $time . " WHERE id = " . $_SESSION["user_id"];
             $res = mysql_query($sql);

	     $sql = "DELETE FROM user_items WHERE user_id = " . $_SESSION["user_id"];
	     $res = mysql_query($sql);
         }
	 break;
    default:
         if (is_numeric($s))
	 {
             $sql = "SELECT id, title, url FROM feeds WHERE id = " . $s;
             $res = mysql_query($sql);
	     $feed = mysql_fetch_array($res);
	     $feeds = 'diesem Feed ';

	     $sql = "SELECT message FROM updatelog WHERE feed_id = " . $s;
	     $res = mysql_query($sql);
	     $message = mysql_fetch_assoc($res);
	     $message = (!empty($message["message"])) ? $message["message"] : '';
	 }
         break;
}

$items = getItems($timespan);

echo '<?xml version="1.0" encoding="utf-8"?'.'>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>eRONA: Meine Feeds</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php readfile(".metas"); ?>
<link href="css.css" rel="stylesheet" type="text/css" />
</head>

<body>
<script type="text/javascript">
<!--
function reloadPage(i)
{
    location.href = 'http://<?php echo ERONA_URL; ?>myfeeds_index.php?<?php echo $_SERVER['QUERY_STRING']; ?>#' + i;
}
-->
</script>
<?php

if ( (count($items[0]) == 0) && ($s == "meine") )
{
    die('Du hast noch keine Feeds abonniert. F&uuml;g mit dem Formular auf der linken Seite einen Feed hinzu, oder wirf einen Blick auf die <a href="feeds.php">Liste</a> aller Feeds und abonniere von dort aus.');
}

echo '<div class="dataheadline"><a name="top"></a>';
if (is_numeric($s))
{
	echo '<a href="' . $feed['url'] . '" target="_blank" title="Öffne ' . $feed['url'] . ' in einem neuen Fenster"><img src="./images/window_list.gif" alt="" /></a> ';
}
echo $feed['title'] . '</div>';
echo '<div class="navsmall" style="text-align: left; vertical-align: bottom; float: left; border: 0; width: 50%;">' . "\n";

if ($_SESSION['public'] != 1)
{
    if ($s == "meine")
    {
        echo '<a style="vertical-align: bottom;" href="myfeeds_index.php?s=' . $s . '&mark=read">Alles als gelesen markieren</a>';
    } elseif (is_numeric($s))
    {
        echo '<a href="similar.php?s=' . $feed['id'] . '&t=' . urlencode($feed['title']) . '">&auml;hnliche Feeds</a> |
	<a href="deabo.php?fid=' . $feed['id'] . '&r=' . urlencode('myfeeds_index.php?s=meine') . '" target="mainFrame">Abo k&uuml;ndigen</a>';
    }
}
?>
</div>
<div class="navsmall" style="text-align: right;">
	<form style="display: inline; margin: 0;" action="myfeeds_index.php?s=<?php echo $_GET['s']; ?>" method="post">
		<select style="border: 1px inset purple; margin: 0; padding: 0; font-size: 0.8em; font-weight: normal;" name="timespan" onchange="this.form.submit()">
			<option value="default">Eintr&auml;ge der letzten</option>
			<option value="default">3 Tage anzeigen</option>
			<option value="week">7 Tage anzeigen</option>
	                <option value="month">31 Tage anzeigen</option>
	                <option value="quartal">90 Tage anzeigen</option>
	            </select>
	        </form>
	&nbsp;&nbsp;&nbsp;
	<form style="display: inline; margin: 0;" action="search.php?s=<?php echo $_GET['s']; ?>" method="post">
		Finde <input style="font-size: 0.8em; font-weight: normal;" name="string" type="text" size="10" maxlength="20" value="Suchbegriff" />
		<input type="hidden" name="where" value="<?php echo $_SESSION['item_where']; ?>" />
		<input type="hidden" name="feed" value="<?php echo $feeds; ?>" />
	</form>
</div>

<?php

if (!empty($message))
{
	echo '<p>Beim letzten Update ist ein Fehler aufgetreten:
	<pre>' . $message . '</pre></p>';
}

if (count($items) == 0)
{
    echo '<p>Dieser Feed hat seit ' . ($timespan / 3600 / 24) . ' Tagen keine neuen Eintr&auml;ge.</p>';
}

$lday = "";

$_items = count($items);
for ($i = 0; $i < $_items; $i++)
{
    $item = $items[$i];

    $indexed = date("d.m.Y, H:i", $item['iindexed']);
    $ftime = date("d.m.Y, H:i", $item['idate']);

    $day  = strftime("%e. %B %Y", $item['idate']);
    $hour = date("G:i", $item['idate']);
    
    $read = $item['iread'];

/*
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
*/

    $date_indexed = ($ftime == $indexed) ? '*' : '';

    $start_tag = $end_tag = "";

    if ( ($read != 1) && ($item['iindexed'] > $_SESSION['lasttime']) && ($_SESSION['public'] != 1) )
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

?>
</body>
</html>
