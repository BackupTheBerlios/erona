<?php
include("connect.php");
error_reporting(E_ERROR | E_PARSE | E_WARNING);

#include("reflog/page.php");

session_start();

if (@$_SESSION['death'] < time())
{
    session_destroy();
}

$user_session = FALSE;

if (!@isset($_SESSION['user_id']))
{
	$user_session = FALSE;
} else
{
	#print_r($_SESSION);
	$user_session = TRUE;
	$user_id      = @$_SESSION['user_id'];
	$user_name    = @$_SESSION['user_name'];
}

echo "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?".">";
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>easy RSS</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="css.css" rel="stylesheet" type="text/css" />
</head>

<body>
<div id="body">
<p><span class="kopf">eRONA: easy RSS Online News Aggregator</span> </p>
<p> <span class="nav">
<?php
if ($user_session)
{
    echo '<a href="logout.php">Logout</a> - <a href="profil.php">Persönliche Daten ändern</a> - <a href="myfeeds.php">eRONA starten</a>';
} else
{
    echo '<a href="login.php">Login</a> - <a href="register.php">Anmelden</a>';
}
?>
</span></p>
<p>Infos zu eRONA gibt es <a href="http://c-x.itst.org?eRONA">hier</a></p>
<p><b>Statistik</b><br />
<?php
$feeds_sql = "SELECT COUNT(id) AS cid FROM feeds";
$items_sql = "SELECT COUNT(id) AS cid FROM items";
$user_sql  = "SELECT COUNT(id) AS cid FROM user";
$puser_sql = "SELECT COUNT(id) AS cid FROM user WHERE public = 1";

$res_feeds = mysql_query($feeds_sql);
$res_items = mysql_query($items_sql);
$res_user = mysql_query($user_sql);
$res_puser = mysql_query($puser_sql);

$feeds = mysql_fetch_array($res_feeds);
$items = mysql_fetch_array($res_items);
$user = mysql_fetch_array($res_user);
$puser = mysql_fetch_array($res_puser);

echo $feeds['cid'] . " Feeds<br />";
echo $items['cid'] . " Items<br />";
echo $user['cid'] . ' Benutzer/Feed-Sammlungen, davon <a href="publics.php">' . $puser['cid'] . ' öffentlich</a>';

?>
</p>
</div>
</body>
</html>
