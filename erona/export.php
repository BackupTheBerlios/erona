<?php

header("Content-Type: text/xml");
header("Content-Disposition: attachment; filename=erona.opml;");

error_reporting(0);

include("connect.php");
include("session.php");

$sql = sprintf("SELECT *
FROM feeds
LEFT JOIN user_feeds ON feeds.id = user_feeds.feed_id
WHERE user_feeds.user_id = %s ORDER BY title ASC", $_SESSION['user_id']);
$result = mysql_query($sql) or die(mysql_error() . $sql . "myfeeds_list.php");

while ($row[] = mysql_fetch_array($result)) {}

$date = date("D, d M Y H:i:s T", time());
$opml = '<opml>
    <head>
        <title>eRONA Abonnements</title>
        <dateCreated>' . $date . '</dateCreated>
    </head>
    <body>
';

for ($i = 0; $i < (count($row) - 1); $i++)
{

    $tit = $des = $url = $rss = "";
    
    $tit = $row[$i]['title'];
    $des = $row[$i]['description'];
    $url = $row[$i]['url'];
    $rss = $row[$i]['rss'];

    $opml .= '        <outline type="rss" title="' . $tit . '" description="' . $des . '" xmlUrl="' . $rss . '" htmlUrl="' . $url . '" />
';

}

$opml .= '    </body>
</opml>';

echo "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?".">";
echo $opml;
?>
