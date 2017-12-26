<?php
error_reporting(0);
header("Content-Type:text/javascript;charset=utf-8");

$requirecontent = "";
if (dirname(__FILE__).DIRECTORY_SEPARATOR.'require.js') {
 $requirecontent = file_get_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'require.js')."\r\n";
}
if (dirname(__FILE__).DIRECTORY_SEPARATOR.'require.config.js') {
 $requirecontent.= file_get_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'require.config.js')."\r\n";
}
echo $requirecontent; exit();
?>