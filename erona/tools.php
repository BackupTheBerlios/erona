<?php
session_start();

/*
include("connect.php");
include("session.php");

if (!isset($_SESSION['user_id']))
{
header("Location: http://" . ERONA_URL . "login.php");
}

$sql = "SELECT title, descr FROM user WHERE id = " . $_SESSION['user_id'];
$res = mysql_query($sql) or die(mysql_error() . $sql);
$row = mysql_fetch_array($res);
*/

$query = "";

if ( (!empty($_GET['t'])) && (!empty($_GET['s'])) )
{
	$reload = stripslashes(urldecode($_GET['t'])) . " aktualisieren";
	$query = "?s=" . $_GET['s'];
} else
{
	$reload = "alle meine Feeds aktualisieren";
}

echo "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?".">";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>eRONA: Meine Feeds</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="css.css" rel="stylesheet" type="text/css" />
</head>

<body style="margin-top: 0px; margin-bottom: 0px;">
<span class="kopf"><acronym title="easy RSS Online News Aggregator">eRONA</acronym></span>
&nbsp;&nbsp;&nbsp;
<span class="nav">
<?php
if ($_SESSION['public'] != 1)
{
	# <img src="./images/update.gif" alt="" /> <a href="update.php' . $query . '" target="dataFrame">' . $reload . '</a>&nbsp; &nbsp;
	echo ' <img src="./images/profil.gif" alt="" /> <a href="profil.php" target="dataFrame">Profil</a>&nbsp; &nbsp;
<img src="./images/logout.gif" alt="" /> <a href="logout.php" target="_top">Logout</a>';
} else
{
	echo '<img src="./images/login.gif" alt="" /> <a href="./" target="_top">Login</a>';
}

#echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b><acronym title="' . $row['descr'] . '">' . $row['title'] . '</b >';
?>
</span>
</body>
</html>
