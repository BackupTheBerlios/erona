<?php
include ("functions.php");

session_start();

#print_r($_SESSION);

if ( (!empty($_SESSION['death'])) && ($_SESSION['death']< time()) )
{
    session_destroy();
    #die("zeit");
    header("Location: http://wwworker.com/erona/login.php");
}

if (!empty($_GET['p']))
{
    $sql = "SELECT COUNT(id) as cid FROM user WHERE id = " . $_GET['p'] . " AND public = 1";
    $res = mysql_query($sql);
    $row = mysql_fetch_array($res);
    if ($row['cid'] != 1) { die("Tut mir leid, diese Feed-Sammlung ist nicht öffentlich." . ' <a href="login.php" target="_top">Login</a>'); }

    $_SESSION['user_id'] = $_GET['p'];
    $_SESSION['public']  = 1;
    $user_session = TRUE;

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
    header("Location: http://wwworker.com/erona/login.php");
    #die("keine user_id");
} else
{
    $user_session = TRUE;
}

if ($user_session)
{
    if (@is_numeric($_GET['s']))
    {
        getFeeds($_GET['s']);
    } else
    {
        getFeeds("meine");
    }
}

$_SESSION['death'] = time() + 900;

?>
