<?php


$base     = "heise.de";
$rss_file = "easyrs/";

$data = FALSE;

$fp = fsockopen ($base, 80, $errno, $errstr, 30);
if (!$fp)
{
    return $data;
} else
{
    fputs ($fp, "GET /" . $rss_file . " HTTP/1.0\r\n\r\n");
    $return = fgets($fp,13);
    $data['errorcode']  = substr($return, 9, 3);
    $data['errorclass'] = substr($return, 9, 1);
    fclose($fp);
}

return $data;
?>
