<?php

include("connect.php");
include("session.php");

if (($_GET['go'] == "go" || $_GET['go'] == "gogo")&& $_SESSION['user_id'] > 0)
{

	header("Content-Type: text/xml");

	if ($_GET['go'] == "go")
	{
		header("Content-Disposition: attachment; filename=erona.opml;");
	}

	$row = getFeeds("meine");

	$date = date("D, d M Y H:i:s T", time());
	$opml = '<opml>
    <head>
        <title>eRONA Feedsammlung</title>
        <dateCreated>' . $date . '</dateCreated>
    </head>
    <body>
';

	for ($i = 0; $i < (count($row) - 1); $i++)
	{

		$tit = $des = $url = $rss = "";

		$tit = stripslashes(strip_tags($row[$i]['title']));
		$des = stripslashes(strip_tags($row[$i]['description']));
		$url = $row[$i]['url'];
		$rss = $row[$i]['rss'];

		$opml .= '        <outline type="rss" title="' . $tit . '" description="' . $des . '" xmlUrl="' . $rss . '" htmlUrl="' . $url . '" />
';

	}

	$opml .= '    </body>
</opml>';

	echo '<?xml version="1.0" encoding="utf-8"?>';
	echo $opml;

} else
{

	if ( (!isset($_SESSION['user_id'])) || ($_SESSION['public'] == 1) )
	{
		header("Location: http://" . ERONA_URL . "index.php");
	}

	echo "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?".">";
	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>eRONA: Meine Feeds exportieren</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="popcss.css" rel="stylesheet" type="text/css" />
</head>
<body style="margin: 0px;">
<h3>Feeds exportieren</h3>
<p>Mit dieser Funktion kannst Du eine OPML-Datei Deiner Feedsammlung erzeugen. OPML-Dateien werden u. a. dazu benutzt, Feedsammlungen von einem Feedreader zum nächsten zu kopieren - enthalten sind jedoch nur die Feeds selbst, also der Name, die Beschreibung und andere technische Daten, nicht aber Einträge aus den Feeds!</p>
<p>OPML-Dateien kann man auch dazu benutzen, Blogrolls zu erzeugen, wenn Dein Blogsystem das unterstützt. Für die meisten Systeme gibt es Plugins, die aus einer OPML-Datei automatisch eine chique formatierte Blogroll erzeugen.</p>
<p>Sobald Du nun auf den Button hier unten klickst, öffnet sich ein Downloaddialog und Du kannst Deine Feedsammlungauf Deinem Computer speichern.</p>
<form action="export.php" method="get">
	<input type="submit" value="Export starten." />
	<input type="hidden" name="go" value="go" />
</form>';

	if ($_SESSION['public_profile'] == 1)
	{
		echo '<p>Da Dein Profil öffentlich ist, kannst Du Dir den manuellen Export hier sogar sparen - sofern Dein Blogsystem die OMPL-Datei für die Blogroll auch übers Web, also per http://... einlesen kann. Um jederzeit auf eine aktuelle Blogroll zuzugreifen, benutze die URL <a href="http://' . ERONA_URL . 'export.php?p=' . $_SESSION['user_id'] . '&amp;go=gogo">http://' . ERONA_URL . 'export.php?p=' . $_SESSION['user_id'] . '&amp;go=gogo</a>.</p>';
	}
	echo '</body></html>';
}
?>
