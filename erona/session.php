<?php
include ("functions.php");

session_start();

#$_SESSION['user_id'] = 1;
#$_SESSION['public'] = 0;

#print_r($_SESSION);

if ( (!empty($_SESSION['death'])) && ($_SESSION['death'] < time() - 3600) && $_SESSION['public'] == 0)
{
	session_destroy();
	#die("zeit");
	header("Location: http://" . ERONA_URL . "index.php");
}

if (!empty($_GET['p']))
{
	$_SESSION['loggedin'] = false;
	if ($_SESSION['loggedin'])
	{
		session_destroy();
		session_start();
	}

	$sql = "SELECT id, title FROM user WHERE id = " . intval($_GET['p']) . " AND public = 1";
	$res = mysql_query($sql);
	$row = mysql_fetch_assoc($res);
	if ($row['id'] != intval($_GET['p'])) { die("Tut mir leid, diese Feed-Sammlung ist nicht öffentlich." . ' <a href="index.php" target="_top">Login</a>'); }

	$_SESSION['user_id'] = $row['id'];
	$_SESSION['title']   = $row['title'];
	$_SESSION['public']  = 1;
	#var_dump($_SESSION);
	$user_session        = TRUE;

}

if ( (isset($_SESSION['public'])) && ($_SESSION['public'] == 1) )
{
	$_SESSION['public']  = 1;

} else
{
	$_SESSION['public']  = 0;

}

if (!isset($_SESSION['user_id']))
{
	session_destroy();
	header("Location: http://" . ERONA_URL . "index.php");
	#die("keine user_id");
} else
{
	$user_session = TRUE;
}

$_SESSION['death'] = time() + 3600;
$_SESSION['item_where'] = $_SESSION['item_where'];

?>
