<?php

include("connect.php");
include("session.php");

if ( (!isset($_SESSION['user_id'])) || ($_SESSION['public'] == 1) )
{
	header("Location: http://" . ERONA_URL . "login.php");
}

include("connect.php");

echo "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?".">";

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>eRONA: Feed abonnieren</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="css.css" rel="stylesheet" type="text/css" />
<?php readfile(".metas"); ?>
</head>
<body>
<?php

if ( (!isset($_GET['fid'])) || (!is_numeric($_GET['fid'])) )
{
	$meldung = "Kein Feed ausgewählt... Schade ;-)";

} else
{
	$id = $_GET['fid'];
	$user_id = $_SESSION['user_id'];

	$sql = "SELECT COUNT(feed_id) as cid FROM user_feeds WHERE feed_id = $id AND user_id = $user_id";
	$result = mysql_query($sql) or die (mysql_error() . $sql);
	$row = mysql_fetch_array($result);
	if ($row['cid'] > 0)
	{
		$meldung = "Sie haben diesen Feed bereits abonniert.";

	} else
	{
		$sql = "INSERT INTO user_feeds (user_id, feed_id) VALUES ($user_id, $id)";
		$result = mysql_query($sql) or die (mysql_error() . $sql);

		$sql = "UPDATE feeds SET reader = reader + 1 WHERE id = $id";
		$result = mysql_query($sql) or die (mysql_error() . $sql);

		getFeeds("meine");

		$meldung = "Abonnement erfolgreich. Viel Spaß beim Lesen ;-)";
	}
}

echo $meldung;
echo '<br /><a onclick="parent.opener.location.reload();" href="' . urldecode($_GET['r']) . '">zurück</a>';
?>
</body>
</html>
