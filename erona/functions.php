<?php

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

function getFeeds($s)
{

$item_where = $feed_where = $feed_liste = "";

    switch($s)
    {
        case "all":
            $sql = "SELECT * FROM feeds ORDER BY title ASC";
            $result = mysql_query($sql) or die(mysql_error() . $sql . "getFeeds");
            while ($row[] = mysql_fetch_array($result)) {}
            return $row;
            break;
        case "meine":
            $sql = sprintf("SELECT *
            FROM feeds
            LEFT JOIN user_feeds ON feeds.id = user_feeds.feed_id
            WHERE user_feeds.user_id = %s ORDER BY feeds.title", $_SESSION['user_id']);
            break;
        case "top25":
            $sql = "SELECT * FROM feeds WHERE id NOT IN (" . $_SESSION['item_where'] . ") ORDER BY reader DESC LIMIT 25";
            $result = mysql_query($sql) or die(mysql_error() . $sql . "getFeeds");
            while ($row[] = mysql_fetch_array($result)) {}
            return $row;
            break;
        default:
            $sql = sprintf("SELECT *
            FROM feeds
            WHERE id = %s", $s);
            break;
    }

    $result = mysql_query($sql) or die(mysql_error() . $sql . "getFeeds, " . $_SERVER['PHP_SELF']);

    while ($row[] = mysql_fetch_array($result)) {}
    $where = "";
    for ($i = 0; $i < (count($row) - 1); $i++)
    {
        $item_where .= $row[$i]['id'];
        $feed_where .= "id = " . $row[$i]['id'];
        #$feed_liste .= $row[$i]['id'];
        if ( $i < (count($row) - 2) ) { $item_where .= ", "; $feed_where .= " OR "; /*$feed_liste .= ", "; */ }
    }
    $_SESSION['item_where'] = $item_where;
    $_SESSION['feed_where'] = $feed_where;
    $_SESSION['feed_liste'] = /* $feed_liste; */ $item_where;

    return($row);
}

function getSimilar($feed_id)
{
    #$feed_data[] = array();

/*
    $sql = "SELECT f.reader AS frc, uf1.feed_id AS fid
FROM user_feeds AS uf1, feeds AS f
LEFT JOIN user_feeds uf2 ON uf1.user_id = uf2.user_id
WHERE uf2.feed_id = $feed_id AND uf1.feed_id <> $feed_id
ORDER BY frc DESC
LIMIT 0 , 50";
*/

    $sql = "SELECT f.reader AS frc, uf1.feed_id AS fid
FROM user_feeds AS uf1
LEFT JOIN user_feeds uf2 ON uf1.user_id = uf2.user_id
LEFT JOIN feeds f ON f.id = $feed_id
WHERE uf2.feed_id = $feed_id AND uf1.feed_id <> $feed_id
ORDER BY frc DESC LIMIT 0 , 50";

    $res = mysql_query($sql);
    
    while ($row = mysql_fetch_array($res))
    {
    	$sql = "SELECT * FROM feeds WHERE id = " . $row['fid'];
    	$res2 = mysql_query($sql);
    	$feed_data[] = mysql_fetch_array($res2);
    }

    #print_r($feed_data);

    return $feed_data;
}

function getItems($c = FALSE)
{
    $i = 0;
    $stamp = (time() - (14 * 24 * 3600));
    $time = date("Y-m-d", $stamp) . "T" . date("H:i:s+01:00", $stamp);
    $sql = "SELECT i.num as inum, i.title as ititle, i.url as iurl, i.descr as idescr, i.date as idate, ui.iread as iread, f.title as ftitle, f.description as fdes, f.url as furl, f.rss as frss FROM items as i LEFT JOIN feeds as f ON i.feed_id = f.id LEFT JOIN user_items as ui ON ui.item_num = i.num AND ui.user_id = " . $_SESSION['user_id'] . " WHERE i.feed_id IN(" . $_SESSION['item_where'] . ") AND i.date > '" . $time . "' ORDER BY i.date DESC"; // LIMIT $n, $m";
    if (is_numeric($c))
    {
    	$sql .= " LIMIT $c";
    }
    #echo $sql;
    $result = mysql_query($sql);
    #echo mysql_error() . $sql . "<br />";
    while (@$row[] = mysql_fetch_array($result)) { $i++; }

    if ($i > 0)
    {
        return $row;
    } else
    {
        return 0;
    }
}

function getItem($id)
{
    //$sql = "SELECT * FROM items WHERE id='$id'";
    $sql = "SELECT i.num as inum, i.title as ititle, i.url as iurl, i.descr as idescr, i.date as idate, f.title as ftitle, f.description as fdes, f.url as furl, f.rss as frss FROM items as i LEFT JOIN feeds as f ON i.feed_id = f.id WHERE i.num = '$id'"; // LIMIT $n, $m";
    $result = mysql_query($sql) or die(mysql_error() . $sql . "<br />");
    while (@$row[] = mysql_fetch_array($result)) {}

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

function makeRSS($p, $user)
{
    getFeeds("meine");
    $items = getItems(15);
    
    $rss=new RSSWriter("http://wwworker.com/erona/myfeeds.php?p=$p",
                       $user['title'],
                       $user['descr'] // ,
#                       array("admin:generatorAgent" => "rdf:resource=\"http://wwworker.com/erona/\"",
#                             "admin:errorReportsTo" => "rdf:resource=\"mailto:sc@itst.org\""
#                       )
    );

    $rss->useModule("dc", "http://purl.org/dc/elements/1.1/");
#    $rss->useModule("admin", "http://webns.net/mvcb/");

    #print_r($items);

    for ($i = 0; $i < count($items) - 1; $i++)
    {
        #echo $i;
        $rss->addItem($items[$i]['iurl'],
                      $items[$i]['ititle'],
                      array("description" => $items[$i]['idescr'],
                            "dc:date" => $items[$i]['idate']
                       )
        );
    }

    $rss->serialize();
}

function getRSS($p)
{
    $sql = "SELECT * FROM user WHERE id=$p AND public=1";
    $res = mysql_query($sql);
    $row = mysql_fetch_array($res);
    
    if ($row['id'] == $p)
    {
         makeRSS($p, $row);
/*         $time = time();
         $last = $row['rssbuilddate'];
         if ( ($time - $last) > 3600)
         {
             makeRSS($p, $row);
         } else
         {
             readfile("temp/rss_$p.xml");
         }
*/
    } else
    {
    	die("Diese Feed-Sammlung ist nicht öffentlich. This Feed-Collection is not public.");
    }
}
?>
