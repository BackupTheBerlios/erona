<?php
include("connect.php");
session_start();
session_destroy();
$user_session = FALSE;

#error_reporting(E_ALL);

if ($_SERVER['HTTP_HOST'] == 'localhost')
{
	$_POST['submit']   = '.';
	$_POST['mail']     = 'itst';
	$_POST['passwort'] = 'butterfly';
}

if ( !empty($_POST['submit']) )
{
	$user = mysql_real_escape_string(htmlspecialchars(trim($_POST['mail'])));
	$pass = mysql_real_escape_string(htmlspecialchars(trim($_POST['passwort'])));

	$sql = "SELECT * FROM user WHERE name='" . $user . "' AND password='" . $pass . "'";
	$res = mysql_query($sql) or die(mysql_error() . ": " . $sql);
	$row = mysql_fetch_array($res);
	$user_id = $row['id'];
	$lasttime = $row['lasttime'];

	if ($user_id < 1)
	{
		$meldung = "Der Benutzername oder das Passwort ist falsch, oder beides ;) Bitte nochmal.";
	} else
	{
		session_start();
		$_SESSION["user_id"] = $user_id;
		$_SESSION["user_name"] = $_POST['mail'];

		$_SESSION["title"] = $row['title'];
		$_SESSION["descr"] = $row['descr'];

		$_SESSION["death"] = time() + 3600;
		$_SESSION["lasttime"] = $lasttime;
		$_SESSION["public_profile"] = $row['public'];
		
		$_SESSION['loggedin'] = TRUE;

		//$_SESSION["public"] = $row['public'];
		$meldung = "Willkommen, " . $_POST['mail'];
		$user_session = TRUE;
		#echo '<a href="myfeeds.php" target="_top">Login ok, weiter gehts</a>.';
		header("Location: http://" . ERONA_URL . "myfeeds.php");
		die();
	}

} else
{
	echo "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?".">";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>eRONA Login</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="css.css" rel="stylesheet" type="text/css" />
</head>

<body>
<?php
echo "<h3>$meldung</h3>";
?>
<div id="login">
<form action="login.php" method="post" target="_top">
<fieldset>
<legend>Login</legend>
<span class="left"><label for="mail">E-Mail-Adresse:</label></span> <span class="right"><input name="mail" type="text" /></span><br />
<span class="left"><label for="passwort">Passwort:</label></span> <span class="right"><input name="passwort" type="password" /></span><br />
</fieldset>
<input value="Einloggen" name="submit" type="submit" />
</form>
</body>
</html>
<?php
}
?>
