<?php echo "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?".">"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>eRONA: Feed-Test</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="css.css" rel="stylesheet" type="text/css" />
</head>

<body>
<?php
require_once('magpierss-0.5.2/rss_fetch.inc');
require_once('magpierss-0.5.2/rss_parse.inc');
require_once('magpierss-0.5.2/rss_utils.inc');

function bug_free_strtotime($str)
{
	$stamp = strtotime($str);
	$str2 = substr(date('D, d m Y H:i:s T',$stamp),0,29);
	return ($str == $str2) ? $stamp : $stamp-3600;
}

function dst_test($stamp)
{
	$dst = date("I", $stamp);
	$stamp -= ($dst * 3600);
	return $stamp;
}

$url = "0";
if (isset($_GET['feedurl']))
{
	$url = $_GET['feedurl'];
}

if ($url != "0")
{
	$feed = @fetch_rss($url);
	$ctitle = $feed->channel['title'];
	$clink = $feed->channel['link'];
	$clang = $feed->channel['dc']['language'];
	$cdesc = $feed->channel['description'];
	
	$ctitle_class = $clink_class = $clang_class = $cdesc_class = "nok";
	$ftitle_class = $flink_class = $fdesc_class = $ftime_class = "nok";
	
	if ($ctitle != "")
	{
		$ctitle_class = "ok";
	} else {
		$error = TRUE;
	}
	
	
	if ($clink != "")
	{
		$clink_class = "ok";
	} else {
		$error = TRUE;
	}
	
	if ($clang != "")
	{
		$clang_class = "ok";
	} else {
		$error = TRUE;
	}

	if ($cdesc != "")
	{
		$cdesc_class = "ok";
	} else {
		$error = TRUE;
	}

	#echo "<ul>\n";
	foreach ($feed->items as $item)
	{
		$flink = $item['link'];
		$ftitle = $item['title']; 
  		$ftime = parse_w3cdtf($item['dc']['date']);
		$fdesc = $item['description'];
		#echo '<li>' . $ftime . ': <a href="$flink">' . $ftitle . '</a></li>' . "\n";
	}
	#echo "</ul>\n";
	
	if ($ftime != -1)
	{
		$ftime_class = "ok";
	} else {
		$error = TRUE;
	}
	
	if ($flink != "")
	{
		$flink_class = "ok";
	} else {
		$error = TRUE;
	}
	
	if ($ftitle != "")
	{
		$ftitle_class = "ok";
	} else {
		$error = TRUE;
	} 
	
	if ($fdesc != "")
	{
		$fdesc_class = "ok";
	} else {
		$error = TRUE;
	}
}

($error) ? $result = '<span class="nok"><b>Der Feed hat Fehler oder unterstützt nicht alle Daten.</b></span>' : $result = '<span class="ok"><b>Der Feed ist in Ordnung.</b></span>';

?> 
<h2>Feed-Test: <?php echo $url; ?></h2>
<?php echo $result; ?>
<hr size="1" noshade />

<span class="ok">Grün markierte</span> Abschnitte wurden erkannt und sind in Ordnung.<br />
<span class="nok">Rot markierte</span> Abschnitte wurden nicht erkannt oder sind nicht in Ordnung.<br />

<h3>Allgemeine Channel-Daten</h3>
<span class="<?php echo $ctitle_class; ?>">Titel: <?php echo $ctitle; ?></span><br />
<span class="<?php echo $clink_class; ?>">Link: <?php echo $clink; ?></span><br />
<span class="<?php echo $clang_class; ?>">Sprache: <?php echo $clang; ?></span><br />
<span class="<?php echo $cdesc_class; ?>">Beschreibung: <?php echo $cdesc; ?></span><br />

<h3>Feed-Inhalte</h3>
<span class="<?php echo $ftitle_class; ?>">Titel</span>: Der Titel ist die Überschrift eines Artikels des Feeds.<br />
<span class="<?php echo $flink_class; ?>">Link</span>: Der Link ist die URL (Permalink) eines Artikels.<br />
<span class="<?php echo $ftime_class; ?>">Zeit</span>: Die Zeit markiert die Veröffentlichungszeit eines Artikels.<br />
<span class="<?php echo $fdesc_class; ?>">Text</span>: Der Text kann ein Auszug (die ersten x Zeichen) oder der gesamte Artikel sein.<br />

<hr size="1" noshade />

<h4>Einen anderen Feed testen</h4>
<form action="test.php" method="get" name="test" dir="ltr" lang="de">
  URL des Feeds: 
  <input name="feedurl" type="text" size="50" maxlength="100" <?php if ($url != "0" ) { echo 'value="' . $url . '"'; } ?> />
  <input name="submit" type="submit" value="Feed testen" />
</form>
</body>
</html>
