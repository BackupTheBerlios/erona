<?php

error_reporting(E_ERROR | E_PARSE | E_WARNING);

if ( (!empty($_GET['mode'])) && (is_numeric($_GET['p'])) )
{
    include("connect.php");
    include("functions.php");
    include("rss10.inc");
    
    $only_rss = FALSE;

    if (!isset($_SESSION['user_id']))
    {
    	session_start();
        $_SESSION['user_id'] = $_GET['p'];
        $_SESSION['public']  = 1;
        $user_session = TRUE;
        $only_rss = TRUE;
    }

    #include("reflog/page.php");
    getRSS($_GET['p']);

    if ($only_rss)
    {
        $_SESSION['public']  = 0;
        session_destroy();
    }

    exit;
}

include("connect.php");
include("session.php");

$query = "";

if (!isset($_SESSION['user_id']))
{
	header("Location: http://wwworker.com/erona/login.php");
} else
{
	echo "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?".">";
	#include("reflog/page.php");
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html>
<head>
<title>eRONA: Meine Feeds</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<?php readfile(".metas"); ?>
</head>

<frameset rows="30,*" cols="*" frameborder="2" border="2" framespacing="1">

  <frame frameborder="2" border="2" src="tools.php" name="toolFrame" title="Logout, Mein Profil, Feeds aktualisieren" longdesc="http://wwworker.com/erona/frames.html#toolFrame">

  <frameset cols="300,*" frameborder="2" border="2" framespacing="1">

    <frame frameborder="2" border="2" src="myfeeds_list.php" name="feedsFrame" title="Abonnierte Feeds, einen Feed hinzufügen" longdesc="http://wwworker.com/erona/frames.html#feedsFrame">

    <frameset rows="200,*" frameborder="2" border="2" framespacing="1">

      <frame frameborder="2" border="2" src="myfeeds_index.php?s=meine" name="mainFrame" title="Liste von Items meiner Feeds" longdesc="http://wwworker.com/erona/frames.html#mainFrame">
      <frame frameborder="2" border="2" src="hallo.html" name="dataFrame" title="Text eines Items" longdesc="http://wwworker.com/erona/frames.html#dataFrame">

  </frameset>
</frameset>
<noframes><body>

</body></noframes>
</html>
<?php
}
?>
