<?php

include("connect.php");
include("session.php");

if ($_SESSION['public'] == 1)
{
	session_destroy();
	header("Location: http://" . ERONA_URL . "index.php");
} else
{
	$user_id = $_SESSION['user_id'];
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>eRONA: Mein Profil<?php if ($_POST['stage'] == 1) echo " - gespeichert"; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="popcss.css" rel="stylesheet" type="text/css" />
<?php readfile(".metas"); ?>
</head>
<body>
<?php

#print_r($_SESSION);

if ($_POST['stage'] == 1)
{
	$sql = "UPDATE user SET name='" . mysql_real_escape_string($_POST['name']) . "', title='" . mysql_real_escape_string($_POST['title']) . "', descr='" . mysql_real_escape_string($_POST['descr']) . "', public='" . mysql_real_escape_string($_POST['public']) . "' WHERE id=" . $_SESSION['user_id'];
	$res = mysql_query($sql) or die (mysql_error() . $sql);

	#echo "<h3>Profil gespeichert</h3>";

	/* if ($_POST['public'] == 1)
	{
	$sb_attr = "onclick=\"if (window.sidebar) { window.sidebar.addPanel('" . $title . "', this.href, ''); return false; }\" rel=\"sidebar\" target=\"_search\"";
	echo "Sie haben die Feed-Sammlung &quot;" . $title . "&quot; als öffentlich zugänglich markiert.<br /><br />\n";
	echo "Um die Sammlung zu lesen: <b><a href=\"http://" . ERONA_URL . "myfeeds.php?p=" . $_SESSION['user_id'] . "\">http://" . ERONA_URL . "myfeeds.php?p=" . $_SESSION['user_id'] . "</a></b><br /><br />\n";
	echo "Um die Sammlung als RSS zu nutzen: <b><a href=\"http://" . ERONA_URL . "myfeeds.php?p=" . $_SESSION['user_id'] . "&amp;mode=rss\">http://" . ERONA_URL . "myfeeds.php?p=" . $_SESSION['user_id'] . "&amp;mode=rss</a></b><br /><br />\n";
	echo "Ihre Sammlung als <a title=\"" . $title . "\" href=\"http://" . ERONA_URL . "sidebar.php?p=" . $_SESSION['user_id'] . "\" $sb_attr>Sidebar in Ihrem Browser</a>.<br /><br />";
	echo "PS: Wenn Sie jetzt eine der URL's öffnen, müssen Sie sich danach erneut in eRONA einloggen.";
	} */
}


$sql = "SELECT * FROM user WHERE id = $user_id";
$res = mysql_query($sql);
$row = mysql_fetch_array($res);

#extract($row);

$email = $row['e-mail'];

$checked = "";

if ($row['public'] == 1)
{
	$checked = " checked ";
}

echo '<form action="profil.php" method="post">
    <fieldset>
    <legend>Mein Profil</legend>

    <div class="row">
    <span class="label"><label for="name">E-Mail-Adresse:</label></span>
    <span class="formw"><input name="name" type="text" value="' . $row['name'] . '" /></span>
    </div>

    <div class="row">
    <span class="label"><label for="title">Titel der Feed-Sammlung:</label></span>
    <span class="formw"><input name="title" type="text" value="' . $row['title'] . '" /></span>
    </div>

    <div class="row">
    <span class="label"><label for="descr">Beschreibung der Feed-Sammlung:</label></span>
    <span class="formw"><input name="descr" type="text" value="' . $row['descr'] . '" /></span>
    </div>

    <div class="row">
    <span class="label"><label for="public">Feed-Sammlung öffentlich machen?:</label></span>
    <span class="formw"><input name="public" type="checkbox" value="1"' . $checked . '/></span>
    </div>

    <div class="row">
    <span class="label">&nbsp;</span>
    <span class="formw"><input type="submit" value="Profil speichern" /></span>
    </div>

    <input type="hidden" name="stage" value="1" />
    </fieldset>
    </form>' . "\n";

if ($row['public'] == 1)
{
	$sb_attr = "onclick=\"if (window.sidebar) { window.sidebar.addPanel('" . $title . "', this.href, ''); return false; }\" rel=\"sidebar\" target=\"_search\"";
	echo "<p>Du hast Deine Feed-Sammlung als öffentlich zugänglich markiert. Das bedeutet, das Du sie lesen kannst, ohne Dich einloggen zu müssen. Mit den folgenden URLs kannst Du auf Deine Feedsammlung zugreifen.";
	echo "<ul><li>Feedsammlung im Browser lesen: <a href=\"http://" . ERONA_URL . "myfeeds.php?p=" . $_SESSION['user_id'] . "\">http://" . ERONA_URL . "myfeeds.php?p=" . $_SESSION['user_id'] . "</a></li>";
	echo "<li>Die letzten 15 Einträge aus Deiner Feedsammlung als RSS: <a href=\"http://" . ERONA_URL . "myfeeds.php?p=" . $_SESSION['user_id'] . "&amp;mode=rss\">http://" . ERONA_URL . "myfeeds.php?p=" . $_SESSION['user_id'] . "&amp;mode=rss</a></li>";
	echo '<li>Deine Feedsammlung als OPML abrufen, zum Beispiel für eine Blogroll: <a href="http://' . ERONA_URL . 'export.php?p=' . $_SESSION['user_id'] . '&amp;go=gogo">http://' . ERONA_URL . 'export.php?p=' . $_SESSION['user_id'] . '&amp;go=gogo</a></li>';
	echo "<li>Außerdem kannst Du eine Feedsammlung in der <a title=\"" . $row['title'] . "\" href=\"http://" . ERONA_URL . "sidebar.php?p=" . $_SESSION['user_id'] . "\" $sb_attr>Sidebar Deines Browsers lesen</a>.</li></ul></p>";
}

#print_r($_SESSION);

?>
</body>
</html>
