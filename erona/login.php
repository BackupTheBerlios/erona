<?php
error_reporting(E_ERROR | E_PARSE | E_WARNING);
include("connect.php");
@session_destroy();
$user_session = FALSE;

if ( (empty($_POST['user'])) )
{
	$meldung = "Kein Username angegeben... So kann das doch nicht gehen ;-) Versuchs nochmal.";

} elseif ( (!isset($_SESSION['user_id'])) )
{
	$sql = "SELECT * FROM user WHERE name='" . trim($_POST['user']) . "' AND password='" . trim($_POST['passwort']) . "'";
	$row = mysql_fetch_array(mysql_query($sql));
	$user_id = $row['id'];

	if ($user_id < 1)
	{
		$meldung = "Den Benutzer <i>" .  $_POST['user'] . "/" . $_POST['passwort'] . "</i> kennt eRONA nicht... So kann das doch nicht gehen ;-) Versuchs nochmal.";
	} else
	{
		session_start();
		$_SESSION["user_id"] = $user_id;
		$_SESSION["user_name"] = $_POST['user'];
		$_SESSION["death"] = time() + 900;
		$_SESSION['public'] = 0;		
		//$_SESSION["public"] = $row['public'];
		$meldung = "Willkommen, " . $_POST['user'];
		$user_session = TRUE;
        	echo '<a href="myfeeds.php" target="_top">Login ok, weiter gehts</a>.';
                //header("Location: http://wwworker.com/erona/myfeeds.php");
        	die();
	}

} elseif ( (isset($_SESSION['user_id'])) )
{
	$user_session = TRUE;
}

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
if (!$user_session)
{
?>
<div id="login">
<form action="login.php" method="post">
<fieldset>
<legend>Anmeldung</legend>
<span class="left"><label for="user">E-Mail-Adresse:</label></span> <span class="right"><input name="user" type="text" /></span><br />
<span class="left"><label for="passwort">Passwort:</label></span> <span class="right"><input name="passwort" type="password" /></span><br />
</fieldset>
<input value="Einloggen" name="" type="submit" />
</form>
<a href="logout.php">Logout</a>
<?php
} else {
?>
<a href="index.php">Login ok, weiter gehts.</a>
<?php
}
?>
</body>
</html>
