<?php

include("connect.php");
include("session.php");

if ( (!isset($_SESSION['user_id'])) ) // || ($_SESSION['public'] == 1) )
{
	header("Location: http://" . ERONA_URL . "login.php");
	#print_r($_SESSION);
	#die ("user_public");
}

echo "<?xml version=\"1.0\" encoding=\"utf-8\"?".">";

if (!isset($_GET['mode']))
{
	$feeds = getFeeds('all');
	$titel = "Alle Feeds";
} else
{
	$feeds = getFeeds('top25');
	$titel = "Top 25 Feeds";
}?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>eRONA: Meine Feeds</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link href="css.css" rel="stylesheet" type="text/css" />
<?php readfile(".metas"); ?>
</head>
<body>
<?php
echo "<b>$titel</b><br />\n";
echo '<a href="feeds.php" title="Alle Feeds anzeigen">Alle Feeds</a> / <a href="feeds.php?mode=top" title="Die 25 meistgelesenen Feeds anzeigen">Top 25 Feeds</a><br /><br />
<small>Du siehst hier immer nur Feeds, die Du <b>noch nicht abonniert</b> hast.</small><table>';

$ufc = $abo = 0;

for ($i = 0; $i < (count($feeds) - 1); $i++)
{

	#if ($i == 24) { break; }

	$_feeds = explode(',', $_SESSION['feed_liste']);

	/* $sql = "SELECT " . $feeds[$i]['id'] . " IN (" . $_SESSION['feed_liste'] . ")";
	$res = mysql_query($sql);
	$row = @mysql_fetch_row($res);
	$abo = $row[0]; */

	/* $sql = "SELECT COUNT(uf.feed_id) AS c FROM user_feeds AS uf WHERE uf.feed_id = " . $feeds[$i]['id'];
	$res = mysql_query($sql);
	$row = @mysql_fetch_array($res);
	$ufc = $row['c'];

	$sql = "UPDATE feeds SET reader = " . $ufc . " WHERE id = " . $feeds[$i]['id'];
	$res = mysql_query($sql); */

	if (!in_array($feeds[$i]['id'], $_feeds))
	{

		echo '<tr><td>&bull; <a href="' . $feeds[$i]['url'] . '">' . $feeds[$i]['title'] . '</a>&nbsp;(' . $feeds[$i]['reader'] . ' Leser)</td>';

		echo '<td>&nbsp;[<a title="' . $feeds[$i]['title'] . ' abonnieren" href="abo.php?fid=' . $feeds[$i]['id'] . '&r=feeds.php">abonnieren</a>]&nbsp;</td>';

		#echo '<td>&nbsp;[<a target="_blank" title="Ist dieser Eintrag eine Doublette oder gibt es ein Problem beim Updaten?" href="ffehler.php?fid=' . $feeds[$i]['id'] . '&r=feeds.php">Probleme?</a>]</td>

		echo '</tr>';
	}

	#$i++;
}
?>
</table>
</span>
</body>
</html>