<?php

session_start();
session_destroy();

include("connect.php");

header("Location: http://" . ERONA_URL . "index.php");

?>
