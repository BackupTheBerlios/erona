<?php

include("connect.php");
include("session.php");

if ( (!isset($_SESSION['user_id'])) || ($_SESSION['public'] == 1) )
{
	header("Location: http://" . ERONA_URL . "index.php");
}

echo "<?xml version=\"1.0\" encoding=\"utf-8\"?".">"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>eRONA: Feeds importieren</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="popcss.css" rel="stylesheet" type="text/css" />
</head>

<body>
<h3>Feeds importieren</h3>

<?php

include('./libs/URL.php');

ob_end_flush();

$_ok = TRUE;

if ( isset($_FILES['probe']) || !empty($_POST['url']) )
{

require_once('magpie/rss_fetch.inc');
require_once('magpie/rss_parse.inc');
#require_once('magpie/rss_utils.inc');

	if (!empty($_POST['url']))
	{
		if (validateUrlSyntax($_POST['url'], 's+a+'))
		{
			if (!$snoopy = _fetch_remote_file($_POST['url']))
			{
				echo "Ich kann " . $_POST['url'] . " nicht ?en...<br />";
				$_ok = FALSE;
			}

			$simple = $snoopy->results;
			unset($snoopy);

		} else
		{
			echo "Also, " . $_POST['url'] . " ist aber keine richtige URL...<br />";
			$_ok = FALSE;
		}

	} elseif (isset($_FILES['probe']))
	{

		$name = "temp/" . time() . ".opml";
		$_ok = move_uploaded_file($_FILES['probe']['tmp_name'], $name);
		$simple = implode("", file($name));
	}
	
	if ($_ok)
	{
		$p = xml_parser_create();
		xml_parse_into_struct($p,$simple,$outlines,$index);
		xml_parser_free($p);

		for ($i = 0; $i < (count($outlines) - 1); $i++)
		{
			if (@$rss = $outlines[$i]['attributes']['XMLURL'])
			{
				$sql_test = "SELECT id FROM feeds WHERE rss='$rss'";
				$result = mysql_query($sql_test);
				$row = mysql_fetch_array($result);
				#print_r($row);
				if ($feed_id = isKnownFeed($rss))
				{
					if (subscribedFeedUser($feed_id, $_SESSION['user_id']))
					{
						echo '<b>' . $rss . '</b> erfolgreich abonniert.<br /><br />';
						flush();
						continue;
					} else
					{

						$sql_userfeeds = "INSERT INTO user_feeds (user_id, feed_id) VALUES(" . $_SESSION['user_id'] . ", " . $feed_id . ")";
						$result = mysql_query($sql_userfeeds) or die(mysql_error() . $sql_userfeeds);

						$sql = "UPDATE feeds SET reader = reader + 1 WHERE id = " . $feed_id;
						$result = mysql_query($sql) or die (mysql_error() . $sql);
					}

				} else
				{

					$feed = @fetch_rss($rss);
					$chd = $feed->channel;
					#print_r($feed);
		
					@$url = addslashes(htmlentities($chd['link']));
					@$tit = addslashes(htmlentities($chd['title']));
					@$des = addslashes(htmlentities($chd['description']));
		
					if ( ($url == "") || ($tit == "") )
					{
						#echo "Dieser Feed hat keinen Titel/keine URL!<br />";
						#flush();
						continue;
					}
	
					if (!@$lan = $chd['language'])
					{
						if (!@$lan = $chd['dc']['language'])
						{
							$lan = "de";
						} else
						{
							$lan = substr($lan, 0, 2);
						}
					} else
					{
						$lan = substr($lan, 0, 2);
					}				
					
					$sql_feed = "INSERT INTO feeds (rss, url, title, description, lang, eingetragen, updated) VALUES('" . $rss . "', '" . $url . "', '" . $tit . "', '" . $des . "', '" . $lan . "', '" . time() . "', '0')";
					$result = mysql_query($sql_feed) or die(mysql_error() . $sql_feed);
					$feed_id = mysql_insert_id();

					$sql_userfeeds = "INSERT INTO user_feeds (user_id, feed_id) VALUES(" . $_SESSION['user_id'] . ", " . $feed_id  .")";
					$result = mysql_query($sql_userfeeds) or die(mysql_error() . $sql_userfeeds);

					$sql = "UPDATE feeds SET reader = reader + 1 WHERE id = " . $feed_id;
					$result = mysql_query($sql) or die (mysql_error() . $sql);

					#echo '<meta http-equiv="refresh" content="2; URL=http://" . ERONA_URL . "update.php?s=' . $id . '" />';
					echo '*<b>' . $rss . '</b> erfolgreich abonniert.<br /><br />';
					flush();
				}
			}
		}
	}
	
	if ($_ok)
	{
		echo '<a onclick="parent.opener.location.reload()" href="myfeeds_index.php?s=meine" target="mainFrame" onclick="self.close()">Zur&uuml;ck zu eRONA</a>.<br />
		Du kannst dieses Fenster jetzt schlie&szlig;en, mal so nebenbei ;)';
	} else
	{
		echo '<a href="import.php">Zur&uuml;ck und nochmal versuchen</a> ;)';
	}

} else
{
?>
<form action="import.php" method="post" enctype="multipart/form-data">
<fieldset>
<legend>Feed aus Datei importieren</legend>

<div class="row">
<span class="label"><label for="probe">Datei:</label></span>
<span class="formw"><input type="file" name="probe" /></span>
</div>

<div class="row">
<span class="label"><label for="probe">URL:</label></span>
<span class="formw"><input type="text" name="url" /></span>
</div>

<div class="row">
<span class="label">&nbsp;</span>
<span class="formw"><input type="submit" value="Feeds importieren" /></span>
</div>

</fieldset>
</form>

<?php
}
?>
</body>
</html>
