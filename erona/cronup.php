#!/usr/bin/php
<?php

#apd_set_pprof_trace('/webs/sascha/root/www/aggregator.de/temp/apd');

include("connect.php");
include("functions.php");

require_once('magpie/rss_fetch.inc');
require_once('magpie/rss_parse.inc');

$feeds = 0;

$sql = "SELECT id, rss FROM feeds";
$result = mysql_query($sql) or die(mysql_error() . ":$sql:");

while ($row[] = mysql_fetch_array($result)) { $feeds++; }

$gc = $ge = 0;
$upd = $ins = $ski = 0;

$start_time = time();

touch('/webs/sascha/root/www/aggregator.de/temp/startlastupdate');
touch('/webs/sascha/root/www/aggregator.de/temp/updaterunning');

for ($i = 0; $i < (count($row) - 1); $i++)
{
	$_echo_source = str_pad($row[$i]['id'], 5, '0', STR_PAD_LEFT);

	$url = $row[$i]['rss'];

	if ($feed = fetch_rss($url))
	{

		$_echo_LT5 = $_echo = "";

		if (count($feed->items) < 5)
		{
			$_echo_LT5 = " (LT5!)";
		}

		foreach ($feed->items as $item)
		{

			$check_sql = $sql = $ftime = $flink = $ftitle = $fdesc = '';
			$we_have_a_date = TRUE;

			if (@!$flink = $item['link'])
			{
				$flink = $item['guid'];
			}

			$time = dst_test(time());

			if (isset($item['pubdate']))
			{
				$stamp = bug_free_strtotime($item['pubdate']);
				$stamp = dst_test($stamp);
				$ftime = date("Y-m-d", $stamp) . "T" . date("H:i:s+01:00", $stamp);
				$_date_from = "pubdate";

			} elseif (isset($item['dc']['date']))
			{
				$stamp = parse_w3cdtf($item['dc']['date']);
				$stamp = dst_test($stamp);
				$ftime = date("Y-m-d", $stamp) . "T" . date("H:i:s+01:00", $stamp);
				$_date_from = "dc:date";

			} else
			{
				$stamp = $time;
				$ftime = date("Y-m-d", $stamp) . "T" . date("H:i:s+01:00", $stamp);
				$we_have_a_date = FALSE;
				$_date_from = "time()";
			}

			if (@!$ftitle = $item['title'])
			{
				$ftitle = substr(strip_tags($fdesc), 0, 25) . "...";

			}
			$ftitle = str_replace("'", "\\'", $ftitle);

			if (@!$fdesc = $item['content']['encoded'])
			{
				if (@!$fdesc = $item['description'])
				{
					$fdesc = '';
				}
			}
			$fdesc  = str_replace("'", "\\'", $fdesc);

			$check_sql = "SELECT num, stamp_date FROM items WHERE url = '$flink'";
			$check_res = mysql_query($check_sql);
			$num = mysql_fetch_array($check_res);

			#$ftitle = htmlentities($ftitle, ENT_QUOTES, 'UTF-8');
			#$fdesc  = htmlentities($fdesc , ENT_QUOTES, 'UTF-8');

			$ftitle = mysql_real_escape_string(stripslashes($ftitle));
			$fdesc  = mysql_real_escape_string(stripslashes($fdesc));

			if ( ($num['num'] > 0) &&
			     ($we_have_a_date) &&
			     ($stamp > $num['stamp_date'])
			   )
			{
				$sql = "UPDATE items_contents SET title = '" . $ftitle . "', descr = '" . $fdesc . "' WHERE num = " . $num['num'];
				$res = mysql_query($sql);
				$sql = "UPDATE items SET stamp_date = '" . $stamp ."', indexed = '" . $time ."' WHERE num = " . $num['num'];
				$_echo .= "updateing " . substr($flink, 0, 25) . " - OLD-Date: " . $num['stamp_date'] . " NEW-Date: " . $stamp . " (" . $_date_from . ") - ";
				$upd++;

			} elseif ($num['num'] < 1)
			{
				$sql  = "INSERT INTO items (feed_id, url, title, stamp_date, indexed) VALUES(" . $row[$i]['id'] . ", '" . $flink . "', '" . $ftitle . "', '" . $stamp . "', '" . $time . "')";
				$res = mysql_query($sql);
				$id = mysql_insert_id();

				$sql = "INSERT INTO items_contents (id, title, descr) VALUES(" . $id . ", '" . $ftitle . "', '" . $fdesc . "')";
                        	$_echo .= "inserting " . substr($flink, 0, 25) . " - Date: " . $stamp . " (" . $_date_from . ") - ";
				$ins++;

			} else
			{
				// echo "SKIPPING!";
				$ski++;
			}

			if (!empty($sql))
			{
				if ( $result = mysql_query($sql) )
				{
					$_echo .= " DONE\n";
					$gc++;
				} else
				{
					$_echo .= " ERROR: " . mysql_error() . "\n";
					$ge++;
				}
			}

		}

		if (!empty($_echo_LT5) || !empty($_echo))
		{
			#echo $_echo_source . $_echo_LT5 . $_echo . "\n";
		}


	} else
	{
		#fputs(STDERR, $_echo_source . "\n" . magpie_error());
		$sql = "INSERT INTO updatelog (feed_id, timestamp, message) VALUES (" . $row[$i]['id'] . ", '" . $start_time . "', '" . mysql_real_escape_string(magpie_error()) . "')";
		mysql_query($sql);
	}



	$sql = "UPDATE feeds SET updated='" . time() . "' WHERE id = " . $row[$i]['id'];
	$result = mysql_query($sql) or die (mysql_error() . $sql);
}

touch('/webs/sascha/root/www/aggregator.de/temp/stoplastupdate');
unlink('/webs/sascha/root/www/aggregator.de/temp/updaterunning');

/*
$sek = time() - $start_time;

echo "\n\nUpdate beendet. $ins neue Items, $upd Updates, $ski Doubletten uebergangen. $ge MySql-Fehler. $sek Sekunden";
*/
?>
