<?php
error_reporting(E_ERROR | E_PARSE | E_WARNING);

include("connect.php");
include("session.php");

if (@!is_numeric($_SESSION['user_id']))
{
    session_destroy();
    die("Keine Session");
} else
{
    $user_id = $_SESSION['user_id'];
}
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
<?php

#print_r($_SESSION);

if ($_POST['stage'] == 1)
{
    $sql = "UPDATE user SET name='" . $_POST['name'] . "', title='" . $_POST['title'] . "', descr='" . $_POST['descr'] . "', public='" . $_POST['public'] . "' WHERE id=" . $_SESSION['user_id'];
    $res = mysql_query($sql) or die (mysql_error() . $sql);

    echo "<h3>Profil gespeichert</h3>";

    if ($_POST['public'] == 1)
    {
        $sb_attr = "onclick=\"if (window.sidebar) { window.sidebar.addPanel('" . $title . "', this.href, ''); return false; }\" rel=\"sidebar\" target=\"_search\"";
    	echo "Sie haben die Feed-Sammlung &quot;" . $title . "&quot; als öffentlich zugänglich markiert.<br /><br />\n";
    	echo "Um die Sammlung zu lesen: <b><a href=\"http://wwworker.com/erona/myfeeds.php?p=" . $_SESSION['user_id'] . "\">http://wwworker.com/erona/myfeeds.php?p=" . $_SESSION['user_id'] . "</a></b><br /><br />\n";
    	echo "Um die Sammlung als RSS zu nutzen: <b><a href=\"http://wwworker.com/erona/myfeeds.php?p=" . $_SESSION['user_id'] . "&amp;mode=rss\">http://wwworker.com/erona/myfeeds.php?p=" . $_SESSION['user_id'] . "&amp;mode=rss</a></b><br /><br />\n";
    	echo "Ihre Sammlung als <a title=\"" . $title . "\" href=\"http://wwworker.com/erona/sidebar.php?p=" . $_SESSION['user_id'] . "\" $sb_attr>Sidebar in Ihrem Browser</a>.<br /><br />";
        echo "PS: Wenn Sie jetzt eine der URL's öffnen, müssen Sie sich danach erneut in eRONA einloggen.";
    }
} else
{

    $sql = "SELECT * FROM user WHERE id = $user_id";
    $res = mysql_query($sql);
    $row = mysql_fetch_array($res);
    
    #extract($row);
    
    $email = $row['e-mail'];
    
    $checked = "";
    
    if ($row['public == 1'])
    {
        $checked = " checked ";
    }
    
    echo '<form action="profil.php" method="post">
    <fieldset>
    <legend>Mein Profil</legend>
    
    <label for="name">E-Mail-Adresse:</label><br />
    <input name="name" type="text" value="' . $row['name'] . '" /><br />
    
    <label for="title">Titel der Feed-Sammlung:</label><br />
    <input name="title" type="text" value="' . $row['title'] . '" /><br />
    
    <label for="descr">Beschreibung der Feed-Sammlung:</label><br />
    <input name="descr" type="text" value="' . $row['descr'] . '" /><br />
    
    <label for="public">Feed-Sammlung öffentlich machen?:</label><br />
    <input name="public" type="checkbox" value="1"' . $checked . '/> ja<br /><br />
    
    <input type="submit" value="Profil speichern" />
    <input type="hidden" name="stage" value="1" />
    </fieldset>
    </form>' . "\n";
    
    if ($row['public'] == 1)
    {

        $sb_attr = "onclick=\"if (window.sidebar) { window.sidebar.addPanel('" . $title . "', this.href, ''); return false; }\" rel=\"sidebar\" target=\"_search\"";
    	echo "Sie haben die Feed-Sammlung &quot;" . $title . "&quot; als öffentlich zugänglich markiert.<br /><br />\n";
    	echo "Um die Sammlung zu lesen: <b><a href=\"http://wwworker.com/erona/myfeeds.php?p=" . $_SESSION['user_id'] . "\">http://wwworker.com/erona/myfeeds.php?p=" . $_SESSION['user_id'] . "</a></b><br /><br />\n";
    	echo "Um die Sammlung als RSS zu nutzen: <b><a href=\"http://wwworker.com/erona/myfeeds.php?p=" . $_SESSION['user_id'] . "&amp;mode=rss\">http://wwworker.com/erona/myfeeds.php?p=" . $_SESSION['user_id'] . "&amp;mode=rss</a></b><br /><br />\n";
    	echo "Ihre Sammlung als <a title=\"" . $title . "\" href=\"http://wwworker.com/erona/sidebar.php?p=" . $_SESSION['user_id'] . "\" $sb_attr>Sidebar in Ihrem Browser</a>.<br /><br />";
        echo "PS: Wenn Sie jetzt eine der URL's öffnen, müssen Sie sich danach erneut in eRONA einloggen.";

    }
}

#print_r($_SESSION);

?>
</body>
</html>
