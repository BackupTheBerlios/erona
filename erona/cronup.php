<?php

$start_time = time();

error_reporting(0);
#set_error_handler("eh");

function eh ($type, $msg, $file, $line, $context)
{
global $row;

    $message  = "Fehler in $file in Zeile $line:\n$type\n$msg\n\n";
    
    while (list ($key, $val) = each ($row))
    {
        $message .= "$key => $val\n";
    }

    echo $message;
}

include("connect.php");
include("functions.php");

if (isset($_GET['s']))
{
     $s = $_GET['s'];
} else
{
    $s = "meine";
}

$feeds = 0;

$sql = "SELECT * FROM feeds WHERE updated < " . (time()-5000);
$result = mysql_query($sql) or die(mysql_error() . ":$sql:");

while ($row[] = mysql_fetch_array($result)) { $feeds++; }

$gc = $ge = 0;

#echo "$feeds Feeds.\n";

require_once('magpierss-0.5.2/rss_fetch.inc');
require_once('magpierss-0.5.2/rss_parse.inc');
require_once('magpierss-0.5.2/rss_utils.inc');

for ($i = 0; $i < (count($row) - 1); $i++)
{
	$c = $e = 0;
	
	#echo str_pad($row[$i]['id'], 5, STR_PAD_RIGHT) . ": " . $row[$i]['title'] . ":\n";
        
		$url = $row[$i]['rss'];
		
		if ($feed = fetch_rss($url))
		{

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

			        if (@!$ftitle = $item['title'])
			        {
				        $ftitle = substr(strip_tags($fdesc), 0, 25) . "...";

			        }
        
        	                $fdesc  = str_replace("'", "\\'", $fdesc);
        	                $ftitle = str_replace("'", "\\'", $ftitle);
        			
        	                $sql = "INSERT INTO items (id, feed_id, url, title, descr, date) VALUES('" . $flink . "', " . $row[$i]['id'] . ", '" . $flink . "', '" . $ftitle . "', '" . $fdesc . "', '" . $ftime . "')";

        	                if ( $result = mysql_query($sql) )
        	                {
				        $c++;
        	                } else
        	                {
				        $e++;
        	                }

        		}
        	}

		$sql = "UPDATE feeds SET updated='" . time() . "' WHERE id = " . $row[$i]['id'];
		$result = mysql_query($sql) or die (mysql_error() . $sql);

		#echo "$c neue Einträge ($e Doubletten)\n\n";
		$ge += $e;
		$gc += $c;
}
#echo "\n\nUpdate erfolgreich. " . $gc  . ' neue Items in die Datenbank aufgenommen, ' . $ge . ' Doubletten übergangen.\n';

$dur = time() - $start_time;

#echo $dur . "sek";
?>
