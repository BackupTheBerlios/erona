<?php
error_reporting(E_ERROR | E_PARSE | E_WARNING);

include("connect.php");
include("session.php");

if (!@isset($_SESSION['user_id']))
{
	header("Location: http://wwworker.com/erona/login.php");
}

$row = getFeeds("meine");

/*
$sql = sprintf("SELECT *
FROM feeds
LEFT JOIN user_feeds ON feeds.id = user_feeds.feed_id
WHERE user_feeds.user_id = %s ORDER BY title ASC", $_SESSION['user_id']);
$result = mysql_query($sql) or die(mysql_error() . $sql . "myfeeds_list.php");
*/

$sql = "SELECT title, descr FROM user WHERE id = " . $_SESSION['user_id'];
$res = mysql_query($sql) or die(mysql_error() . $sql);
$meinname = mysql_fetch_array($res);

echo "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?".">";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>eRONA: Meine Feeds</title>
<?php readfile(".metas"); ?>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="css.css" rel="stylesheet" type="text/css" />
</head>
<body style="border: 1px green;">
<script type="text/javascript">
<!--
function reloadPage(t, s)
{
    parent.toolFrame.location.href = 'http://wwworker.com/erona/tools.php?t=' + t + '&s=' + s;
}
-->
</script>
<?php

echo '<h3>' . $meinname['title'] . '</h3><em>' . $meinname['descr'] . '</em><br /><br />';

if ($_SESSION['public'] != 1)
{
    echo 'Feeds
<span class="nav">
<a href="feeds.php"target="mainFrame">abonnieren</a>/ <a href="#neu">hinzufügen</a>/ <a href="#imp">importieren</a>/ <a href="#exp">exportieren</a><br /><br />
</span>';
}

echo '<span class="liste">
<a onclick="reloadPage(' . "''" . ', ' . "'" . 'meine' . "'" . ');" target="mainFrame" href="myfeeds_index.php?s=meine"><b>Meine Feeds</b></a><br />';

/*
while ($row = mysql_fetch_array($result))
{
      echo '&bull; <a onclick="reloadPage(' . "'" . urlencode($row['title']) . "'" . ');" target="mainFrame" href="myfeeds_index.php?s=' . $row['id'] . '">' . $row['title'] . '</a> (<a title="Abo von ' . $row['title'] . ' kündigen" href="deabo.php?fid=' . $row['id'] . '&r=myfeeds_index.php?s=0" target="mainFrame">x</a>)<br />';
}
*/

for ($i = 0; $i < (count($row) - 1); $i++)
{
      echo '&bull; <a onclick="reloadPage(' . "'" . urlencode($row[$i]['title']) . "'" . ', ' . $row[$i]['id'] . ');" target="mainFrame" href="myfeeds_index.php?s=' . $row[$i]['id'] . '">' . $row[$i]['title'] . '</a>';
      
      if ($_SESSION['public'] != 1)
      {
          echo ' (<a title="Abo von ' . $row[$i]['title'] . ' kündigen" href="deabo.php?fid=' . $row[$i]['id'] . '&r=' . urlencode('myfeeds_index.php?s=meine') . '" target="mainFrame">x</a>)';
          echo ' (<a title="Ähnliche Feeds anzeigen" href="similar.php?s=' . $row[$i]['id'] . '&t=' . urlencode($row[$i]['title']) . '" target="mainFrame">ä</a>)';
      }
      
      echo "<br />\n";
}

echo '</span>
<br />';

if ($_SESSION['public'] != 1)
{
    echo '<form action="new_feed.php" method="post" target="dataFrame">
<fieldset>
<legend><a name="neu">Feed hinzufügen</a></legend>

<label for="rss">URL des Feeds: </lablel><br />
<input type="text" name="rss" size="30" /><br />

<input type="hidden" name="stage" value="1" />
<input type="submit" value="Feed hinzufügen" />
</fieldset>
</form>

<form action="import.php" method="post" target="dataFrame" enctype="multipart/form-data">
<fieldset>
<legend><a name="imp">Feeds importieren</a></legend>

<label for="probe">Datei:</label><br />
<input type="file" name="probe" />

<input type="submit" value="Feeds importieren" />
</fieldset>
</form>

<form action="export.php" method="post">
<fieldset>
<legend><a name="exp">Feeds exportieren</a></legend>
<input type="submit" value="Feeds exportieren" />
</fieldset>
</form>';
}

?>

</body>
</html>
