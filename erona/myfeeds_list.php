<?php

#apd_set_pprof_trace('./temp/apd/');

include("connect.php");
include("session.php");

if (!@isset($_SESSION['user_id']))
{
	header("Location: http://" . ERONA_URL . "index.php");
}

$row = getFeeds("meine");

echo "<?xml version=\"1.0\" encoding=\"utf-8\"?".">";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>eRONA: Meine Feeds</title>
<?php readfile(".metas"); ?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="css.css" rel="stylesheet" type="text/css" />

<script type="text/javascript">
<!--
var NextUpdate = <?php echo ( filemtime('./temp/startlastupdate') + UPDATE_CYCLE - time() ); ?>;
var UpdateRunning = <?php echo (file_exists('./temp/updaterunning')) ? 1 : 0; ?>;

function ZeitAnzeigen()
{
	var absSekunden = Math.round(ZeitBerechnen());
	var anzMinuten = Math.round(absSekunden / 60);

	if (UpdateRunning == 1)
	{
		if ((Math.abs(absSekunden) % 60) == 0)
		{
			window.location.reload();
		}
		window.document.Anzeige.Zeit.value = "Update running.";
	} else
	{
		/* if (anzMinuten <= 0)
		{
			window.location.reload();
		} */

		if (anzMinuten == 1)
		{
			window.document.Anzeige.Zeit.value = "einer Minute";
		}

		if (anzMinuten > 1)
		{
			window.document.Anzeige.Zeit.value = anzMinuten + " Minuten";
		}

		window.setTimeout('ZeitAnzeigen()',1000);
	}
}

function ZeitBerechnen()
{
	NextUpdate = NextUpdate - 1;
	return ( NextUpdate );
}
// -->
</script>

</head>
<body style="border: 1px green;" onLoad="window.setTimeout('ZeitAnzeigen()',0)">
<script type="text/javascript">
<!--
function popup (url, target) {
	self.name = 'erona';
	fenster=window.open(url, target, "width=600,height=400,status=no,scrollbars=yes,resizable=no");
	fenster.focus();
}
//-->
</script>
<?php


echo '<span class="kopf"><acronym title="easy RSS Online News Aggregator">eRONA</acronym></span>
&nbsp;&nbsp;&nbsp;
<span class="nav">';

if ($_SESSION['public'] != 1)
{
	# <img src="./images/update.gif" alt="" /> <a href="update.php' . $q&uuml;ry . '" target="dataFrame">' . $reload . '</a>&nbsp; &nbsp;
	echo ' <img src="./images/profil.gif" alt="" /> <a href="profil.php" target="dataFrame" onclick="popup(\'profil.php\', \'profil\'); return false;">Profil</a>&nbsp; &nbsp;
<img src="./images/logout.gif" alt="" /> <a href="logout.php" target="_top">Logout</a>';
} else
{
	echo '<img src="./images/login.gif" alt="" /> <a href="./" target="_top">Login</a>';
}

echo '<br /><form name="Anzeige" action="" style="font-size: 0.8em; margin-top: 5px; margin-bottom: 5px;">
N&auml;chstes Update in <input readonly size="8" style="font-weight: normal; font-size: 0.8em; border: 0px solid white;" name="Zeit" value="30 Minuten">
</form></span>
<div class="dataheadline">' . $_SESSION['title'] . '</div>';

if ($_SESSION['public'] != 1)
{
	echo '<div class="navr" style="line-height: 120%;">
<a href="feeds.php" target="mainFrame" onclick="popup(\'feeds.php\', \'feeds\'); return false;">Alle Feeds</a> |
<a href="#">Meine Feeds</a> |
<a href="new.php" target="mainFrame" onclick="popup(\'new.php\', \'new\'); return false;">Feed hinzuf&uuml;gen</a><br />
<a href="import.php" target="mainFrame" onclick="popup(\'import.php\', \'import\'); return false;">OPML-Import</a> |
<a href="export.php" target="mainFrame" onclick="popup(\'export.php\', \'export\'); return false;">OPML-Export</a></div>
';
}

echo '<div class="dataheadline" style="margin-bottom: 0px; margin-top: 10px;"><a target="mainFrame" href="myfeeds_index.php?s=meine"><img src="./images/gohome.gif" /></a> <a target="mainFrame" href="myfeeds_index.php?s=meine">Inbox</a></div>
<ul class="feedliste" style="margin-top: 5px;">
';

$_feeds = count($row) - 1;
for ($i = 0; $i < $_feeds; $i++)
{
	echo '<li><a target="mainFrame" href="myfeeds_index.php?s=' . $row[$i]['id'] . '">' . $row[$i]['title'] . '</a></li>
';
}

echo '</ul>
';

?>

</body>
</html>
