<?php

error_reporting(E_ERROR | E_PARSE | E_WARNING);

include("connect.php");
include("session.php");

if ( (!isset($_SESSION['user_id'])) || ($_SESSION['public'] == 1) )
{
	header("Location: http://wwworker.com/erona/login.php");
}

echo "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?".">"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>eRONA: Meine Feeds</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="css.css" rel="stylesheet" type="text/css" />
</head>

<body style="margin: 0px;">
<h3>Feed hinzufügen</h3>
<?php

$url = $rss = $tit = $des = $lan = "";

if ($_POST['stage'] == "1")
{
    #$url = $_POST['url'];
    $rss = $_POST['rss'];
    #$tit = $_POST['tit'];
    
    $rss_file = parse_url($rss);
    $rss_base = $rss_file['host'];
    $rss_file = $rss_file['path'];

    /* if (empty($rss))
    {
    	$parts = parse_url($url);
    	$base = "http://" . $parts['host'] . "/";
    	
    	#echo "öffne $base";
        $fp = fopen($base, "r");
    	while (!feof($fp))
    	{
    	        #echo "lesen";
                $f .= fgets($fp, 8192);
    	}
        #echo "schliessen";
    	fclose($fp);

        #echo "get rss";
        $rss = getRSSLocation($f, $base);
        $des = getDesc($f);
        
    }

    $data = urlCheck($rss_base, $rss_file);
    if (!$data)
    {
    	die("Verarbeitungfehler!");
    } else
    {
        if ($data['errorclass'] > 3)
        {
            echo "<b>Bitte überprüfen Sie die URL zum Feed!</b><br />";
        }
    } */
    
    require_once('magpierss-0.5.2/rss_fetch.inc');
    require_once('magpierss-0.5.2/rss_parse.inc');
    require_once('magpierss-0.5.2/rss_utils.inc');
    
    $feed = @fetch_rss($rss);
    $chd = $feed->channel;
    #print_r($feed);

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
    <form action="new_feed.php" method="post">
    <fieldset>
    <legend><a name="neu">Angaben überprüfen</a></legend>
    <label for="tit">Titel: </lablel><br />
    <input type="text" name="tit" value="<?php echo $tit; ?>"><br />

    <label for="url">URL: </lablel><br />
    <input type="text" size="50" name="url" value="<?php echo $url; ?>"><br />

    <label for="rss">RSS: </lablel><br />
    <input type="text" size="50" name="rss" value="<?php echo $rss; ?>"><br />

    <label for="des">Beschreibung: </lablel><br />
    <input type="text" size="50" name="des" value="<?php echo $des; ?>"><br />
    
    <label for="lan">Sprache des Feeds (de, en, fr, ...): </lablel><br />
    <input type="text" maxlength="2" name="lan" value="<?php echo $lan; ?>"><br />

    <input type="hidden" name="stage" value="2">
    <input type="submit" value="Angaben bestätigen">

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

    $sql_test = "SELECT id FROM feeds WHERE rss='$rss'";
    $result = mysql_query($sql_test);
    $row = mysql_fetch_array($result);
    #print_r($row);
    if ($row['id'] > 0)
    {
        $id = $row['id'];
        $sql = "SELECT COUNT(feed_id) as cid FROM user_feeds WHERE feed_id = " . $row['id'] . " AND user_id = " . $_SESSION['user_id'];
        $result = mysql_query($sql) or die (mysql_error() . $sql);
        $row = mysql_fetch_array($result);
        if ($row['cid'] > 0)
        {
            echo "Sie haben diesen Feed bereits abonniert.";
            exit;
        }
    } else
    {
        $sql_feed = "INSERT INTO feeds (rss, url, title, description, lang, eingetragen, updated) VALUES('" . $rss . "', '" . $url . "', '" . $tit . "', '" . $des . "', '" . $lan . "', '" . time() . "', '0')";
        $result = mysql_query($sql_feed) or die(mysql_error() . $sql_feed);
        $id = mysql_insert_id();
    }

    $sql_userfeeds = "INSERT INTO user_feeds (user_id, feed_id) VALUES(" . $_SESSION['user_id'] . ", $id)";
    $result = mysql_query($sql_userfeeds) or die(mysql_error() . $sql_userfeeds);

    $sql = "UPDATE feeds SET reader = reader + 1 WHERE id = $id";
    $result = mysql_query($sql) or die (mysql_error() . $sql);

    #echo '<meta http-equiv="refresh" content="2; URL=http://wwworker.com/erona/update.php?s=' . $id . '" />';
    echo 'Feed erfolgreich eingetragen. <a onclick="parent.feedsFrame.location.reload()" href="update.php?s=' . $id . '" target="dataFrame">Weiter gehts zur Aktualisierung</a>.';

}

?>
</body>
</html>
