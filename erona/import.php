<?php

error_reporting(E_ERROR | E_PARSE | E_WARNING);

include("connect.php");
include("session.php");

error_reporting(E_ERROR | E_PARSE | E_WARNING);

if ( (!isset($_SESSION['user_id'])) || ($_SESSION['public'] == 1) )
{
	header("Location: http://wwworker.com/erona/login.php");
}

echo "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?".">"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>eRONA: Feeds importieren</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="css.css" rel="stylesheet" type="text/css" />
</head>

<body style="margin: 0px;">
<h3>Feeds importieren</h3>

<?php

if (isset($_FILES['probe']))
{
    $name = "temp/" . time() . ".opml";
    move_uploaded_file($_FILES['probe']['tmp_name'], $name);

    $simple = implode("", file($name));
    
    #$simple = "<para><note type=\"test\">simple note</note></para>";
    
    $p = xml_parser_create();
    xml_parse_into_struct($p,$simple,$outlines,$index);
    xml_parser_free($p);
    
    for ($i = 0; $i < (count($outlines) - 1); $i++)
    {
        if (@$rss = $outlines[$i]['attributes']['XMLURL'])
        {
            require_once('magpierss-0.5.2/rss_fetch.inc');
            require_once('magpierss-0.5.2/rss_parse.inc');
            require_once('magpierss-0.5.2/rss_utils.inc');
    
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
    
            echo "<b>$tit</b>: ";
    
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
                    echo "Sie haben diesen Feed bereits abonniert.<br /><br />";
                    flush();
                    continue;
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
            echo 'Feed erfolgreich eingetragen.<br /><br />';
            flush();
        }
    }
}
?>
<a onclick="parent.feedsFrame.location.reload()" href="update.php" target="dataFrame">Weiter gehts zur Aktualisierung</a>.
</body>
</html>
