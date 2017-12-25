<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function get_text2php_handle($text) {

    return '';

}

function get_table_fields($table) {
    $query = db_query('SELECT * FROM '.$table);
    $i = 0;
    $field_str = '';
    $is_int = array();
    while ($field = mysql_fetch_field($query->result_id)) {
        $is_int[$i] = in_array(strtolower(mysql_field_type($query->result_id, $i)),
            array('tinyint', 'smallint', 'mediumint', 'int', 'bigint'),
            TRUE);
        $field_str .= db_escape_identifiers($field->name).', ';
        $i++;
    }
    $field_str = preg_replace('/, $/' , '', $field_str);
    return array('is_int'=>$is_int, 'field_str'=>$field_str);
}

function get_optimize_list($only = false)
{
    $row_arr = array();
    if ($only) {
        $result = db_query("SHOW TABLE STATUS");
    }else{
        $result = db_query("SHOW TABLE STATUS FROM `".BASE_DB_NAME."` WHERE Data_free>0");
    }
    foreach($result->result_array() AS $row) {
        if ($row['Data_free']=="0")
        {
            $row['Data_free']="-";
        }
        if ($row['Data_free']>1 && $row['Data_free']<1024)
        {
            $row['Data_free']=$row['Data_free']." byte";
        }
        elseif($row['Data_free']>1024 && $row['Data_free']<1048576)
        {
            $row['Data_free']=number_format(($row['Data_free']/1024),1)." KB";
        }
        elseif($row['Data_free']>1048576)
        {
            $row['Data_free']=number_format(($row['Data_free']/1024/1024),1)." MB";
        }
        $row['Data_length']=$row['Data_length']+$row['Index_length'];
        //--
        if ($row['Data_length']=="0")
        {
            $row['Data_length']="-";
        }
        elseif($row['Data_length']<1048576)
        {
            $row['Data_length']=number_format(($row['Data_length']/1024),1)." KB";
        }
        elseif($row['Data_length']>1048576)
        {
            $row['Data_length']=number_format(($row['Data_length']/1024/1024),1)." MB";
        }
        $row_arr[] = $row;
    }
    return $row_arr;
}

function get_dir_list() {
    $dir = BASE_PATH.'caches/bakup/';
    $dirArray[]=NULL;
    if (false != ($handle = opendir ( $dir ))) {
        $i=0;
        while ( false !== ($file = readdir ( $handle )) ) {
            if ($file != "." && $file != ".."&&!strpos($file,".")) {
                $dirArray[$i]=$file;
                $i++;
            }
        }
        closedir ( $handle );
    }
    $inorder = array();
    foreach ($dirArray AS $k=>$v) {
        if (!preg_match("/^[0-9]{8}_[0-9]{4}/i", $v)) {
            unset($dirArray[$k]);
        }else{
            if (file_exists($dir.$v.'/info.php')) {
                $infos = @include $dir.$v.'/info.php';
                $dirArray[$k] = array();
                $dirArray[$k]['pre'] = $v;
                $dirArray[$k]['release'] = $infos['ES_RELEASE'];
                $dirArray[$k]['notrel'] = ($infos['ES_RELEASE']!=ES_RELEASE)?1:0;
                $dirArray[$k]['maketime'] = date("Y-m-d H:i", filectime($dir.$v));
                list($dirArray[$k]['number'], $dirArray[$k]['filesize']) = get_dir_numsize($dir.$v);
                $inorder[$k] = $dirArray[$k]['maketime'];
            }else{
                unset($dirArray[$k]);
            }
        }
    }
    array_multisort($inorder, SORT_DESC, $dirArray);
    return $dirArray;
}

function get_dir_numsize($dir) {
    @$dh = opendir($dir);
    $size = 0;
    $num = 0;
    while ($file = @readdir($dh)) {
        if ($file != "." and $file != "..") {
            $path = $dir."/".$file;
            if (is_file($path)) {
                $size += filesize($path);
                $num++;
            }
        }
    }
    @closedir($dh);
    return array($num, get_format_bytes($size));
}

function get_format_bytes($size) {
    $units = array(' B', ' KB', ' MB', ' GB', ' TB');
    for ($i = 0; $size >= 1024 && $i < 4; $i++) $size /= 1024;
    return round($size, 2).$units[$i];
}