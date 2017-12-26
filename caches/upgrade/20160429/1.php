<?php

function writesetting_20160429($text, $filen = 'config', $istxt = false)
{
    $cache_file_path = BASE_PATH."caches".DIRECTORY_SEPARATOR."cache.".$filen;
    $cache_file_path .= $istxt?".txt":".php";
    $content = $istxt?"":"<?php\r\n";
    $content .= $text;
    $content .= $istxt?"":"?>";
    make_dir(dirname($cache_file_path));
    if (!file_put_contents($cache_file_path, $content, LOCK_EX)) {
        $fp = @fopen($cache_file_path, 'wb+');
        if (!$fp) {
            //exit('生成缓存文件失败');
        }
        if (!@fwrite($fp, trim($content))) {
            //exit('生成缓存文件失败');
        }
        @fclose($fp);
    }
}


$_temfile = BASE_PATH."caches".DIRECTORY_SEPARATOR."cache.templet.php";
if (file_exists($_temfile)) {
    $_temcon = file_get_contents($_temfile);
    if (strexists($_temcon, 'return array (')) {
        $_temarr = @include $_temfile;
        if (is_array($_temarr)){
            writesetting_20160429(array2string(_Sys_Safe_Stop::addslashes_deep($_temarr)), 'templet', true);
        }
    }
    @unlink($_temcon);
}

?>

