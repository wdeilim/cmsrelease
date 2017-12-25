<?php


function smarty_modifier_date_format_cn($string, $format = '%b %e, %Y', $default_date = '')
{
    if (substr(PHP_OS,0,3) == 'WIN') {
        $_win_from = array ('%e',   '%T',        '%D');
        $_win_to    = array ('%#d', '%H:%M:%S', '%m/%d/%y');
        $format = str_replace($_win_from, $_win_to, $format);
    }
    $arrTemp = array('年','月','日','时','分','秒','時');
    foreach($arrTemp as $v){
        if(strpos($format,$v)){
            $strFormat = str_replace('%','',$format);
        }
    }
    if($string != '') {
        if(!empty($strFormat)) return date($strFormat, date_format_cn_smarty_make_timestamp($string));
        else return strftime($format, date_format_cn_smarty_make_timestamp($string));
    } elseif (isset($default_date) && $default_date != '') {
        if(!empty($strFormat)) return date($strFormat, date_format_cn_smarty_make_timestamp($default_date));
        else return strftime($format, date_format_cn_smarty_make_timestamp($default_date));
    } else {
        return;
    }

}

function date_format_cn_smarty_make_timestamp($string)
{
    if (empty($string)) {
        // use "now":
        return time();
    } elseif ($string instanceof DateTime) {
        return $string->getTimestamp();
    } elseif (strlen($string) == 14 && ctype_digit($string)) {
        // it is mysql timestamp format of YYYYMMDDHHMMSS?
        return mktime(substr($string, 8, 2),substr($string, 10, 2),substr($string, 12, 2),
            substr($string, 4, 2),substr($string, 6, 2),substr($string, 0, 4));
    } elseif (is_numeric($string)) {
        // it is a numeric string, we handle it as timestamp
        return (int) $string;
    } else {
        // strtotime should handle it
        $time = strtotime($string);
        if ($time == -1 || $time === false) {
            // strtotime() was not able to parse $string, use "now":
            return time();
        }

        return $time;
    }
}
