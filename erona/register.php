<?php
error_reporting(E_ERROR | E_PARSE | E_WARNING);

include("connect.php");

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
<?php
$mail = $pass = "";

@$mail = $_POST['mail'];
@$pass = $_POST['pass'];

if ( (!isset($_POST['mail'])) || (!isset($_POST['pass'])) )
{
    $meldung = "Bitte geben Sie eine gültige E-Mail-Adresse und ein Passwort ein.";

} else
{
    $sql = "INSERT INTO user (name, password) VALUES ('$mail', '$pass')";
    $result = mysql_query($sql) or die (mysql_error() . $sql);
    echo 'Vielen Dank für Ihre Registrierung. Sie können sich jetzt <a href="login.php">einloggen</a>';
    exit;

}
?>
<span class="kopf">Willkommen zu eRONA: easy RSS Online News Aggregator</span><br /><br />
Um eRONA nutzen zu können, müssen Sie sich registrieren. Ohne Registrierung kann eRONA keine Personalisierung vornehmen; schließlich hat eRONA keine Glaskugel ;-)<br /><br />
<?php echo "<b>$meldung</b><br />"; ?>
<form action="register.php" method="post">
<fieldset>
<legend>Registrierung</legend>
<label for="mail">E-Mail-Adresse: </label><br />
<input type="text" name="mail" value="<?php echo $mail; ?>"><br />
<br />
<label for="pass">Passwort: </label><br />
<input type="password" name="pass" value="<?php echo $pass; ?>"><br />
</fieldset>
<input type="submit" value="Ich möchte mich diesen bei eRONA registrieren">
</form>
</body>
</html>
