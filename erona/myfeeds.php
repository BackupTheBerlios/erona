<?php

if ( (!empty($_GET['mode'])) && (is_numeric($_GET['p'])) )
{
    include("connect.php");
    include("functions.php");
    include("./libs/rss10.inc");

    header("Content-Type: application/rss+xml");

    if (!isset($_SESSION['user_id']))
    {
    	session_start();
        $_SESSION['user_id'] = $_GET['p'];
        $_SESSION['public']  = 1;
        $user_session = TRUE;
        $only_rss = TRUE;
    }

    getRSS($_GET['p']);
    
    session_destroy();
    exit;
}

include("connect.php");
include("session.php");

$query = "";

if (!isset($_SESSION['user_id']))
{
	header("Location: http://" . ERONA_URL . "index.php");
} else
{
	echo "<?xml version=\"1.0\" encoding=\"utf-8\"?".">";
	#include("reflog/page.php");
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html>
<head>
<title>eRONA: Meine Feeds</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php readfile(".metas"); ?>
</head>


  <frameset cols="290,*" bordercolor="#006600">
    <frame src="myfeeds_list.php" name="feedsFrame" title="Abonnierte Feeds, einen Feed hinzufügen" longdesc="http://" . ERONA_URL . "frames.html#feedsFrame">
    <frameset rows="218,*" bordercolor="#006600">
      <frame src="myfeeds_index.php?s=meine" name="mainFrame" title="Liste von Items meiner Feeds" longdesc="http://" . ERONA_URL . "frames.html#mainFrame">
      <frame src="hallo.html" name="dataFrame" title="Text eines Items" longdesc="http://" . ERONA_URL . "frames.html#dataFrame">
    </frameset>
  </frameset>
  <noframes></noframes>
</html>
<?php
}
?>
