<?php
error_reporting(E_ERROR | E_PARSE | E_WARNING);

include("connect.php");
include("session.php");

if ( (!isset($_SESSION['user_id'])) ) // || ($_SESSION['public'] == 1) )
{
	header("Location: http://wwworker.com/erona/login.php");
}

echo "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?".">";

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>eRONA: Meine Feeds</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="css.css" rel="stylesheet" type="text/css" />
<?php readfile(".metas"); ?>
</head>
<body>
<table>
<?php

getFeeds("meine");

$feeds = getSimilar($_GET['s']);
$feeds_gefunden = FALSE;

echo "<h3><i>" . urldecode($_GET['t']) . "</i> ähnliche Feeds</h3>\n";
echo '<span class="liste">';

echo '<table>';

for ($i = 0; $i < (count($feeds) - 1); $i++)
{
    $abo = 1;
    $sql = "SELECT " . $feeds[$i]['id'] . " IN (" . $_SESSION['feed_liste'] . ")";
    $res = mysql_query($sql);
    if (!$row = @mysql_fetch_row($res))
    {
        $abo = 1;
    } else
    {
        $abo = $row[0];
    }
    
    if ($abo == 0)
    {
        $feeds_gefunden = TRUE;

        echo '<tr><td>&bull; <a href="' . $feeds[$i]['url'] . '">' . $feeds[$i]['title'] . '</a>&nbsp;(' . $feeds[$i]['reader'] . ' Leser)</td>';

        echo '<td>&nbsp;[<a title="' . $feeds[$i]['title'] . ' abonnieren" href="abo.php?fid=' . $feeds[$i]['id'] . '&r=' . urlencode('similar.php?s=' . $_GET['s'] . '&t=' . $_GET['t']) . '">abonnieren</a>]&nbsp;</td>';

        #echo '<td>&nbsp;[<a target="_blank" title="Ist dieser Eintrag eine Doublette oder gibt es ein Problem beim Updaten?" href="ffehler.php?fid=' . $feeds[$i]['id'] . '&r=feeds.php">Probleme?</a>]</td>';
        
        echo '</tr>';
    }

}

echo '</table>';

if (!$feeds_gefunden)
{
    echo "Keine ähnlichen Feeds gefunden... Dieser Feed wird wohl nur von Dir gelesen ;-)";
}
?>
</span>
</body>
</html>
