<?php

include("connect.php");
include("session.php");

#error_reporting(E_ALL);

if ( (!isset($_SESSION['user_id'])) || ($_SESSION['public'] == 1) )
{
	header("Location: http://" . ERONA_URL . "login.php");
}

echo "<?xml version=\"1.0\" encoding=\"utf-8\"?".">"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>eRONA: Meine Feeds</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="popcss.css" rel="stylesheet" type="text/css" />
</head>
<body style="margin: 0px;">
<h3>Feed hinzufuegen</h3>
<?php

$url = $rss = $tit = $des = $lan = "";

if ( isset($_POST['stage']) && ($_POST['stage'] == "1") )
{
	#$url = $_POST['url'];
	$rss = $_POST['rss'];
	#$tit = $_POST['tit'];

	// Böse Menschen davonabhalten, Unfug zu machen
	if (stristr($rss, ";") || stristr($rss, "'") || stristr($rss, "\""))
	{
		echo '<p>Das ist doch keine URL... <a href="new.php">Bitte nochmal</a> ;)</p>';
		exit;
	}

	$feed_id = isKnownFeed($rss);

	if ($feed_id > 0)
	{
		if (subscribedFeedUser($feed_id, $_SESSION['user_id']))
		{
			echo '<p>Du hast diesen Feed doch schon abonniert. <a href="myfeeds_index.php?s='  .$id . '" target="mainFrame" onclick="self.close();">Zurueck zu eRONA</a>.</p>';
			exit;
		} else
		{
			$sql_userfeeds = "INSERT INTO user_feeds (user_id, feed_id) VALUES(" . $_SESSION['user_id'] . ", $feed_id)";
			$result = mysql_query($sql_userfeeds) or die(mysql_error() . $sql_userfeeds);

			$sql = "UPDATE feeds SET reader = reader + 1 WHERE id = " . $feed_id;
			$result = mysql_query($sql) or die (mysql_error() . $sql);
		}

		echo '<p>Feed abonniert.</p>';

/*		if (shouldBeUpdated($feed_id))
		{
			echo '<a href="update.php?s=' . $feed_id . '">Weiter gehts zur Aktualisierung</a>.';
			exit;
		} else
		{
			echo '<a onclick="parent.opener.location.reload()" href="myfeeds_index.php?s=' . $feed_id . '" target="mainFrame" onclick="self.close();">Zurueck zu eRONA</a>.';
			exit;
		} */

		echo '<a onclick="parent.opener.location.reload()" href="myfeeds_index.php?s=' . $feed_id . '" target="mainFrame" onclick="self.close();">Zurueck zu eRONA</a>.';

	}

	$rss_file = parse_url($rss);
	$rss_base = $rss_file['host'];
	$rss_file = $rss_file['path'];

	require_once('magpie/rss_fetch.inc');
	require_once('magpie/rss_parse.inc');
	#require_once('magpie/rss_utils.inc');

	$feed = @fetch_rss($rss);
	$chd = $feed->channel;

	$url = $chd['link'];
	$tit = $chd['title'];
	$des = $chd['description'];
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


    ?>
    <form action="new.php" method="post">
    <fieldset>
    <legend><a name="neu">Angaben ueberpruefen</a></legend>

    <div class="row">
    <span class="label"><label for="tit">Titel: </label></span>
    <span class="formw"><input type="text" name="tit" value="<?php echo $tit; ?>"></span>
    </div>

    <div class="row">
    <span class="label"><label for="url">URL: </label></span>
    <span class="formw"><input type="text" size="50" name="url" value="<?php echo $url; ?>"></span>
    </div>

    <div class="row">
    <span class="label"><label for="rss">RSS: </label></span>
    <span class="formw"><input type="text" size="50" name="rss" value="<?php echo $rss; ?>"></span>
    </div>

    <div class="row">
    <span class="label"><label for="des">Beschreibung: </label></span>
    <span class="formw"><input type="text" size="50" name="des" value="<?php echo $des; ?>"></span>
    </div>

    <div class="row">
    <span class="label"><label for="lan">Sprache des Feeds (de, en, fr, ...): </label></span>
    <span class="formw"><input type="text" maxlength="2" name="lan" value="<?php echo $lan; ?>"></span>
    </div>

    <div class="row">
    <span class="label">&nbsp;</span>
    <span class="formw"><input type="submit" value="Angaben bestaetigen"></span>
    </div>

    <input type="hidden" name="stage" value="2">

    </fieldset>
    </form>
    <?php
} elseif ($_POST['stage'] == "2")
{
	$url = $_POST['url'];
	$rss = $_POST['rss'];
	$tit = $_POST['tit'];
	$des = $_POST['des'];
	$lan = $_POST['lan'];

	$feed_id = isKnownFeed($rss);

	if ($feed_id > 0)
	{
		if (subscribedFeedUser($feed_id, $_SESSION['user_id']))
		{
			echo '<p>Du hast diesen Feed doch schon abonniert. <a href="myfeeds_index.php?s='  .$id . '" target="mainFrame" onclick="self.close();">Zurueck zu eRONA</a>.</p>';
			exit;
		} else
		{
			$sql_userfeeds = "INSERT INTO user_feeds (user_id, feed_id) VALUES(" . $_SESSION['user_id'] . ", $feed_id)";
			$result = mysql_query($sql_userfeeds) or die(mysql_error() . $sql_userfeeds);

			$sql = "UPDATE feeds SET reader = reader + 1 WHERE id = " . $feed_id;
			$result = mysql_query($sql) or die (mysql_error() . $sql);
		}

		echo '<p>Feed abonniert.</p>';

/*		if (shouldBeUpdated($feed_id))
		{
			echo '<a href="update.php?s=' . $feed_id . '">Weiter gehts zur Aktualisierung</a>.';
			exit;
		} else
		{
			echo '<a onclick="parent.opener.location.reload()" href="myfeeds_index.php?s=' . $feed_id . '" target="mainFrame" onclick="self.close();">Zurueck zu eRONA</a>.';
			exit;
		} */
		
		echo '<p><a onclick="parent.opener.location.reload()" href="myfeeds_index.php?s=' . $feed_id . '" target="mainFrame" onclick="self.close();">Zurueck zu eRONA</a>.</p>';

	} else
	{
		$sql_feed = "INSERT INTO feeds (rss, url, title, description, lang, eingetragen, updated) VALUES('" . $rss . "', '" . $url . "', '" . $tit . "', '" . $des . "', '" . $lan . "', '" . time() . "', '0')";
		$result = mysql_query($sql_feed) or die(mysql_error() . $sql_feed);
		$feed_id = mysql_insert_id();
		
		$sql_userfeeds = "INSERT INTO user_feeds (user_id, feed_id) VALUES(" . $_SESSION['user_id'] . ", $feed_id)";
		$result = mysql_query($sql_userfeeds) or die(mysql_error() . $sql_userfeeds);

		$sql = "UPDATE feeds SET reader = reader + 1 WHERE id = " . $feed_id;
		$result = mysql_query($sql) or die (mysql_error() . $sql);
		
		$nextstart = (int) ( (filemtime('./temp/startlastupdate') + UPDATE_CYCLE - time() ) / 60);

		echo '<p>Prima, Du hast ' . $tit . ' abonniert. Da ich diesen Feed noch nicht kannte, ist er leer, d. h. Du wirst keine Eintraege sehen. Macht aber nix. Beim naechsten Update (in ' . $nextstart . ' Minuten) wird er gefuellt, versprochen ;)</p>';

	}

	#echo '<meta http-equiv="refresh" content="2; URL=http://" . ERONA_URL . "update.php?s=' . $id . '" />';
	echo '<p>Feed abonniert.</p>';

	$sql = "SELECT updated FROM feeds WHERE id = " . $feed_id;
	$result = mysql_query($sql);
	$row = mysql_fetch_array($result);

	echo '<p><a href="myfeeds_index.php?s=' . $feed_id . '" target="mainFrame" onclick="parent.opener.location.reload()">Zurueck zu eRONA</a>.</p>';

/*	if ($row['updated'] > (time() - 3 * 3600))
	{
		echo '<a href="update.php?s=' . $feed_id . '">Weiter gehts zur Aktualisierung</a>.';
		exit;
	} else
	{
		echo '<a href="myfeeds_index.php?s=' . $feed_id . '" target="mainFrame" onclick="parent.opener.location.reload()">Zurueck zu eRONA</a>.';
		exit;

	} */

} elseif (empty($_POST['stage']))
{
	echo '<form action="new.php" method="post">

<fieldset>

<legend><a name="neu">Feed hinzufuegen</a></legend>

    <div class="row">
    <span class="label"><label for="rss">URL des Feeds:</label></span>
    <span class="formw"><input type="text" name="rss" size="30" /></span>
    </div>

    <div class="row">
    <span class="label">&nbsp;</span>
    <span class="formw"><input type="submit" value="Feed hinzufuegen" /></span>
    </div>

    <input type="hidden" name="stage" value="1" />

    </fieldset>
</form>';
}

?>
</body>
</html>