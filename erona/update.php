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

<body style="margin: 10px;">

<table width="90%">
<tr>
	<th>Blog</th>
	<th>Aktualisierungsmeldung</th>
</tr>
<?php

#echo $_SESSION['feed_where'];

if (isset($_GET['s']))
{
     $s = $_GET['s'];
} else
{
    $s = "meine";
}

getFeeds($s);

#echo $_SESSION['feed_where'];

$sql = "SELECT * FROM feeds WHERE " . $_SESSION['feed_where'];
$result = mysql_query($sql) or die(mysql_error() . ":$sql:");
#echo mysql_error().$sql;
while ($row[] = mysql_fetch_array($result)) {}

$gc = $ge = 0;

require_once('magpierss-0.5.2/rss_fetch.inc');
require_once('magpierss-0.5.2/rss_parse.inc');
require_once('magpierss-0.5.2/rss_utils.inc');

$start = time();

for ($i = 0; $i < (count($row) - 1); $i++)
{
	$c = $e = 0;
	
	echo "<tr><td><b>" . $row[$i]['title'] . "</b>:</td>";
        
        if ($row[$i]['updated'] > (time()-3600))
	{
		$delta = date("H:i", $row[$i]['updated']+3600);
		echo "<td>Update erst um $delta wieder möglich. <a href=\"hilfe.html#update\" target=\"dataFrame\">?</a></td></tr>";
		flush();
	} else
	{
	
		$url = $row[$i]['rss'];
		
		# exec("php update_ext.php -url $url -id " . $row[$i]['title'] . " -t " . $row[$i]['title']);

		$feed = fetch_rss($url);
		
		foreach ($feed->items as $item)
		{

			#echo "Aktualisiere <b>" . $row[$i]['title'] . "</b><br />";
			$flink = $ftitle = $fdesc = 0;
	
        	        if (@!$flink = $item['link'])
			{
				$flink = $item['guid'];
			}
  			
			if (@!$ftime = $item['pubdate'])
			{
				if (@!$ftime = $item['dc']['date'])
				{
					$time = time();
                                	$ftime = date("Y-m-d", $time) . "T" . date("H:i:s+01:00", $time);
	                        }
	                        $stamp = parse_w3cdtf($ftime);
				$stamp = dst_test($stamp);
				$ftime = date("Y-m-d", $stamp) . "T" . date("H:i:s+01:00", $stamp);
			} else
			{	$stamp = bug_free_strtotime($ftime);
				$stamp = dst_test($stamp);
				$ftime = date("Y-m-d", $stamp) . "T" . date("H:i:s+01:00", $stamp);
			}
	
			if (@!$fdesc = $item['content']['encoded'])
                	{
	                        if (@!$fdesc = $item['description'])
				{
					$fdesc = '<a href="' . $flink . '" title="' . $ftitle . '">' . $ftitle . '</a>';
				}
        	        }
        	        
        	        #print_r($fdesc);

			if (@!$ftitle = $item['title'])
			{
				$ftitle = substr(strip_tags($fdesc), 0, 25) . "...";
			}

	                $fdesc  = str_replace("'", "\\'", $fdesc);
                	$ftitle = str_replace("'", "\\'", $ftitle);
			
	                $sql = "INSERT INTO items (id, feed_id, url, title, descr, date) VALUES('" . $flink . "', " . $row[$i]['id'] . ", '" . $flink . "', '" . $ftitle . "', '" . $fdesc . "', '" . $ftime . "')";
			#echo "<p>$sql</p>";
	                if ( $result = mysql_query($sql) )
        	        {
	                	$c++;
	                } else
        	        {
                	        $e++;
	                }
        	        #echo '<li>Zeit: ' . $ftime . ': <a href="' . $flink . '">Titel: ' . $ftitle . '</a><br />' . $fdesc . '</li>' . "\n";
			#break;
		}
		#echo "</ul>\n";
		
		$sql = "UPDATE feeds SET updated='" . time() . "' WHERE id = " . $row[$i]['id'];
		$result = mysql_query($sql) or die (mysql_error() . $sql);

		echo "<td>$c neue Einträge ($e Doubletten)</td></tr>";
		$ge += $e;
		$gc += $c;
	}
}
echo '</table><br />Update erfolgreich. ' . $gc  . ' neue Items in die Datenbank aufgenommen, ' . $ge . ' Doubletten übergangen. <a href="myfeeds_index.php?s=' . $s . '" target="mainFrame">Weiter gehts</a><br /><br />';

echo 'Dauer: ' . ((time() - $start) / 60) . 'sek.';
?>
</body>
</html>
