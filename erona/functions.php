<?php

// from magpie 0.51
function parse_w3cdtf ( $date_str ) {
	
/*	# regex to match wc3dtf
	if (strlen( $date_str) < 11)
	{
		$date_str .= 'T00:00:00+01:00';
	}
*/
	$pat = "/(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2})(:(\d{2}))?(?:([-+])(\d{2}):?(\d{2})|(Z))?/";
	
	if ( preg_match( $pat, $date_str, $match ) ) {
		list( $year, $month, $day, $hours, $minutes, $seconds) = 
			array( $match[1], $match[2], $match[3], $match[4], $match[5], $match[6]);
		
		# calc epoch for current date assuming GMT
		$epoch = gmmktime( $hours, $minutes, $seconds, $month, $day, $year);
		
		$offset = 0;
		if ( $match[10] == 'Z' ) {
			# zulu time, aka GMT
		}
		else {
			list( $tz_mod, $tz_hour, $tz_min ) =
				array( $match[8], $match[9], $match[10]);
			
			# zero out the variables
			if ( ! $tz_hour ) { $tz_hour = 0; }
			if ( ! $tz_min ) { $tz_min = 0; }
		
			$offset_secs = (($tz_hour*60)+$tz_min)*60;
			
			# is timezone ahead of GMT?  then subtract offset
			#
			if ( $tz_mod == '+' ) {
				$offset_secs = $offset_secs * -1;
			}
			
			$offset = $offset_secs;	
		}
		$epoch = $epoch + $offset;
		return $epoch;
	}
	else {
		return FALSE;
	}
}

function bug_free_strtotime($str)
{
	$stamp = strtotime($str);

	if ($stamp === -1)
	{
		return FALSE;
	}

	// $str2 = substr(date('D, d m Y H:i:s T',$stamp),0,29);
	$str2 = date('r',$stamp);
	// return ($str == $str2) ? $stamp : $stamp-3600;
	return $stamp;
}

function dst_test($stamp)
{
	$dst = date("I", $stamp);
	$stamp -= ($dst * 3600);
	return $stamp;
}

function getFeeds($s)
{

	$item_where = $feed_where = $feed_liste = "";
	
	$_fields = 'id, rss, url, title, description, lang, eingetragen, updated, reader';

	switch($s)
	{
		case "all":
		$sql = "SELECT " . $_fields . " FROM feeds ORDER BY title ASC";
		$result = mysql_query($sql) or die(mysql_error() . $sql . "getFeeds");
		while ($row[] = mysql_fetch_assoc($result)) {}
		return $row;
		break;
		case "meine":
		$sql = sprintf("SELECT " . $_fields . "
            FROM feeds
            LEFT JOIN user_feeds ON feeds.id = user_feeds.feed_id
            WHERE user_feeds.user_id = %s ORDER BY feeds.title", $_SESSION['user_id']);
		break;
		case "top25":
		$sql = "SELECT " . $_fields . " FROM feeds WHERE id NOT IN (" . $_SESSION['item_where'] . ") ORDER BY reader DESC LIMIT 25";
		$result = mysql_query($sql) or die(mysql_error() . $sql . "getFeeds");
		while ($row[] = mysql_fetch_assoc($result)) {}
		return $row;
		break;
		default:
		$sql = sprintf("SELECT " . $_fields . "
            FROM feeds
            WHERE id = %s", $s);
		break;
	}

	$result = mysql_query($sql) or die(mysql_error() . $sql . "getFeeds, " . $_SERVER['PHP_SELF']);

	while ($row[] = mysql_fetch_assoc($result)) {}
	$where = "";
	$_rows = count($row);
	for ($i = 0; $i < $_rows - 1; $i++)
	{
		$item_where .= $row[$i]['id'];
		$feed_where .= "id = " . $row[$i]['id'];
		#$feed_liste .= $row[$i]['id'];
		if ( $i < ($_rows - 2) ) { $item_where .= ", "; $feed_where .= " OR "; /*$feed_liste .= ", "; */ }
	}
	$_SESSION['item_where'] = $item_where;
	$_SESSION['feed_where'] = $feed_where;
	$_SESSION['feed_liste'] = /* $feed_liste; */ $item_where;

	return($row);
}

function getSimilar($feed_id)
{
	$sql = "SELECT f.reader AS frc, uf1.feed_id AS fid
FROM user_feeds AS uf1
LEFT JOIN user_feeds uf2 ON uf1.user_id = uf2.user_id
LEFT JOIN feeds f ON f.id = $feed_id
WHERE uf2.feed_id = $feed_id AND uf1.feed_id <> $feed_id
ORDER BY frc DESC LIMIT 0 , 50";

	$res = mysql_query($sql);

	while ($row = mysql_fetch_assoc($res))
	{
		$sql = "SELECT * FROM feeds WHERE id = " . $row['fid'];
		$res2 = mysql_query($sql);
		$feed_data[] = mysql_fetch_assoc($res2);
	}

	#print_r($feed_data);

	return $feed_data;
}

function getItems($timespan = TIMESPAN_DEFAULT, $getdesc = FALSE, $geturl = FALSE, $c = 0)
{
	$ret_row = array();
	$time = (time() - ($timespan));
	
	$sql = "SELECT MAX(num) AS startnum FROM items WHERE stamp_date <= '" . $time . "'";
	$res = mysql_query($sql);
	$startnum = mysql_fetch_assoc($res);
	$startnum = $startnum['startnum'];

	#$time = date("Y-m-d", $stamp) . "T" . '00:00:00+01:00'; // date("H:i:s+01:00", $time);

	if ($getdesc)
	{
		$getdesc  = ' ic.descr as idescr, ';
		$descjoin = ' LEFT JOIN items_contents AS ic ON ic.id = i.num ';
	} else
	{
		$getdesc  = $descjoin = '';
	}

	$geturl  = ($geturl ) ? ' i.url as iurl, ' : ' ';

#	$sql = "SELECT i.num as inum, i.title as ititle, i.url as iurl, $getdesc i.stamp_date as idate, i.indexed as iindexed, ui.iread as iread, f.title as ftitle, f.description as fdes, f.url as furl, f.rss as frss FROM items as i LEFT JOIN feeds as f ON i.feed_id = f.id LEFT JOIN user_items as ui ON ui.item_num = i.num AND ui.user_id = " . $_SESSION['user_id'] . " WHERE i.feed_id IN(" . $_SESSION['item_where'] . ") AND i.stamp_date > '" . $time . "' ORDER BY i.stamp_date DESC"; // LIMIT $n, $m";

	$sql = "SELECT i.num as inum, i.title as ititle, " . $getdesc . $geturl . " i.stamp_date as idate, i.indexed as iindexed, ui.iread as iread, f.title as ftitle
	FROM items as i
	LEFT JOIN feeds as f ON i.feed_id = f.id
	LEFT JOIN user_items as ui ON ui.item_num = i.num AND ui.user_id = " . $_SESSION['user_id'] .
	$descjoin . "
 	WHERE i.feed_id IN(" . $_SESSION['item_where'] . ") AND i.stamp_date > '" . $time . "'
	ORDER BY i.stamp_date DESC";
//	WHERE i.feed_id IN(" . $_SESSION['item_where'] . ") AND i.num >= " . $startnum . "

	if ($c > 0)
	{
		$sql .= " LIMIT $c";
	}
	echo '<!-- ' . $sql . ' -->';
	$result = mysql_query($sql);
	#echo mysql_error() . $sql . "<br />";
	while ($row = mysql_fetch_assoc($result))
	{
		$ret_row[] = $row;
	}

	return $ret_row;
}

function getItem($id)
{
	//$sql = "SELECT * FROM items WHERE id='$id'";
	$sql = "SELECT i.num as inum, i.title as ititle, i.url as iurl, ic.descr as idescr, i.stamp_date as idate, f.title as ftitle, f.id as fid, f.url as furl FROM items as i LEFT JOIN items_contents AS ic ON i.num = ic.id LEFT JOIN feeds as f ON i.feed_id = f.id WHERE i.num = '$id'"; // LIMIT $n, $m";
	#echo '<!-- ' . $sql . ' -->';
	$result = mysql_query($sql) or die(mysql_error() . $sql . "<br />");
	while (@$row[] = mysql_fetch_assoc($result)) {}

	$row = $row[0];

	return $row;
}

/* originally written by Keith Devens, keithdevens.com */
function getRSSLocation($html, $location){
	if(!$html or !$location){
		return false;
	}else{
		#search through the HTML, save all <link> tags
		# and store each link's attributes in an associative array
		preg_match_all('/<link\s+(.*?)\s*\/?>/si', $html, $matches);
		$links = $matches[1];
		$final_links = array();
		$link_count = count($links);
		for($n=0; $n<$link_count; $n++){
			$attributes = preg_split('/\s+/s', $links[$n]);
			foreach($attributes as $attribute){
				$att = preg_split('/\s*=\s*/s', $attribute, 2);
				if(isset($att[1])){
					$att[1] = preg_replace('/([\'"]?)(.*)\1/', '$2', $att[1]);
					$final_link[strtolower($att[0])] = $att[1];
				}
			}
			$final_links[$n] = $final_link;
		}
		#now figure out which one points to the RSS file
		for($n=0; $n<$link_count; $n++){
			if(strtolower($final_links[$n]['rel']) == 'alternate'){
				if(strtolower($final_links[$n]['type']) == 'application/rss+xml'){
					$href = $final_links[$n]['href'];
				}
				if(!$href and strtolower($final_links[$n]['type']) == 'text/xml'){
					#kludge to make the first version of this still work
					$href = $final_links[$n]['href'];
				}
				if($href){
					if(strstr($href, "http://") !== false){ #if it's absolute
					$full_url = $href;
					}else{ #otherwise, 'absolutize' it
					$url_parts = parse_url($location);
					#only made it work for http:// links. Any problem with this?
					$full_url = "http://$url_parts[host]";
					if(isset($url_parts['port'])){
						$full_url .= ":$url_parts[port]";
					}
					if($href{0} != '/'){ #it's a relative link on the domain
					$full_url .= dirname($url_parts['path']);
					if(substr($full_url, -1) != '/'){
						#if the last character isn't a '/', add it
						$full_url .= '/';
					}
					}
					$full_url .= $href;
					}
					return $full_url;
				}
			}
		}
		return false;
	}
}

function getDesc($html){
	#search through the HTML, save all <link> tags
	# and store each link's attributes in an associative array
	preg_match_all('/<meta\s+(.*?)\s*\/?>/si', $html, $matches);
	$links = $matches[1];
	#print_r($links);
	$link_count = count($links);
	for($n=0; $n<$link_count; $n++)
	{
		if (substr($links[$n], 0, 18) == 'name="description"')
		{
			$final_link = substr($links[$n], 19);
			#echo $final_link;
			$desc = substr($final_link, 9, strlen($final_link) - 2);
			return $desc;
		}
	}
	return false;
}

function urlCheck($base, $file)
{
	#echo "checking $base/$file";
	$data = FALSE;

	$fp = fsockopen ($base, 80, $errno, $errstr, 30);
	if (!$fp)
	{
		return $data;
	} else
	{
		fputs ($fp, "GET " . $file . " HTTP/1.0\r\n\r\n");
		$return = fgets($fp,256);
		fclose($fp);
		$data['errorcode']  = substr($return, 9, 3);
		$data['errorclass'] = substr($return, 9, 1);
	}

	return $data;
}

function iso8601_date($time) {
   $tzd = date('O',$time);
   $tzd = substr(chunk_split($tzd, 3, ':'),0,6);
   $date = date('Y-m-d\TH:i:s', $time) . $tzd;
   return $date;
}

function makeRSS($p, $user)
{
	getFeeds("meine");
	$items = getItems(TIMESPAN_QUARTAL, TRUE, TRUE, 15);

	$rss=new RSSWriter("http://" . ERONA_URL . "myfeeds.php?p=$p",
		$user['title'],
		$user['descr']
	);

	$rss->useModule("dc", "http://purl.org/dc/elements/1.1/");

	for ($i = 0; $i < count($items) - 1; $i++)
	{
		#echo $i;
		$rss->addItem($items[$i]['iurl'],
		$items[$i]['ititle'],
		array("description" => $items[$i]['idescr'],
		"dc:date" =>iso8601_date($items[$i]['idate'])
		)
		);
	}

	$rss->serialize();
}

function getRSS($p)
{
	$sql = "SELECT * FROM user WHERE id=$p AND public=1";
	$res = mysql_query($sql);
	$row = mysql_fetch_assoc($res);

	if ($row['id'] == $p)
	{
		makeRSS($p, $row);

	} else
	{
		die("Diese Feed-Sammlung ist nicht öffentlich. This Feed-Collection is not public.");
	}
}

function isKnownFeed($rss)
{
	$sql = "SELECT id FROM feeds WHERE rss = '$rss'";
	$res = mysql_query($sql);
	$row = mysql_fetch_assoc($res);
	
	return ($row['id'] > 0) ? $row['id'] : false;
}

function subscribedFeedUser($feed_id, $user_id)
{
	$sql = "SELECT feed_id FROM user_feeds WHERE user_id = $user_id AND feed_id = $feed_id";
	$res = mysql_query($sql);
	$row = mysql_fetch_assoc($res);
	
	return ($row['feed_id'] == $feed_id) ? true : false;
}	

function shouldBeUpdated($feed_id)
{
	$sql = "SELECT updated FROM feeds WHERE id = $feed_id";
	$res = mysql_query($sql);
	$row = mysql_fetch_assoc($res);
	
	return ($row['updated'] < (time() - UPDATE_CYCLE)) ? true : false;	
}
?>
