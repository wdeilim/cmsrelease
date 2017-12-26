<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Safecheck {

    protected static $checkcmd = array('SEL'=>1, 'UPD'=>1, 'INS'=>1, 'REP'=>1, 'DEL'=>1);
    protected static $config;

    public static function checkquery($sql, $skipcheck = array()) {
        if (self::$config === null) {
            self::$config = array(
                'dfunction'     => array ('load_file','hex','substring','if','ord','char'),
                'daction'       => array ('@','intooutfile','intodumpfile','unionselect','(select','unionall','uniondistinct'),
                'dnote'         => array ('/*','*/','#','--','"'),
                'dlikehex'      => 1,
                'afullnote'     => 0
            );
        }
        if ($skipcheck) {
            if (!is_array($skipcheck)) {
                $skipcheck = array($skipcheck);
            }
            foreach ($skipcheck AS $skipitem) {
                $skipconf = strtolower(trim($skipitem));
                if (in_array($skipconf, self::$config['dfunction']) || in_array($skipconf, self::$config['daction']) || in_array($skipconf, self::$config['dnote'])) {
                    continue;
                }else{
                    $sql = str_replace($skipitem, '', $sql);
                }
            }
        }
        $check = 1;
        $cmd = strtoupper(substr(trim($sql), 0, 3));
        if (isset(self::$checkcmd[$cmd])) {
            $check = self::_do_query_safe($sql);
        } elseif(substr($cmd, 0, 2) === '/*') {
            $check = -1;
        }

        if ($check < 1) {
            //throw new DbException('It is not safe to do this query', 0, $sql);
            $log_file = BASE_PATH.'caches/log/sql_'.date('Ymd').'.php';
            $userIP = ONLINE_IP;
            $getUrl = get_url();
            $time = date('Y-m-d H:i:s');
            fputs(fopen($log_file,'a+'),"<?PHP exit();?>$userIP||$time<br>$getUrl<br>$sql<br>$check<br>===========<br>\r\n");
            echo _Sys_Safe_Stop::scanpape(); exit();
        }
        return true;
    }

    public static function discard_checkquery($db_string) {
        $db_string_bak = $db_string;
        if (defined('OFF_SQL_TEMP') && OFF_SQL_TEMP < time()) {
            $clean = '';
            $error = '';
            $old_pos = 0;
            $pos = -1;
            $log_file = BASE_PATH.'caches/log/sql_'.date('Ymd').'.php';
            $userIP = ONLINE_IP;
            $getUrl = get_url();
            $time = date('Y-m-d H:i:s');
            if (leftexists(trim($db_string), 'select', true)){
                $notallow1 = "[^0-9a-z@\._-]{1,}(sleep|benchmark|load_file|outfile)[^0-9a-z@\.-]{1,}";
                if(preg_match("/".$notallow1."/i", $db_string)){
                    fputs(fopen($log_file,'a+'),"<?PHP exit();?>$userIP||$time<br>$getUrl<br>$db_string<br>SelectBreak<br>===========<br>\r\n");
                    echo _Sys_Safe_Stop::scanpape(); exit();
                }
            }
            while (TRUE){
                $pos = strpos($db_string, '\'', $pos + 1);
                if ($pos === FALSE){
                    break;
                }
                $clean .= substr($db_string, $old_pos, $pos - $old_pos);
                while (TRUE){
                    $pos1 = strpos($db_string, '\'', $pos + 1);
                    $pos2 = strpos($db_string, '\\', $pos + 1);
                    if ($pos1 === FALSE){
                        break;
                    }elseif ($pos2 == FALSE || $pos2 > $pos1){
                        $pos = $pos1;
                        break;
                    }
                    $pos = $pos2 + 1;
                }
                $clean .= '$s$';
                $old_pos = $pos + 1;
            }
            $clean .= substr($db_string, $old_pos);
            $clean = trim(strtolower(preg_replace(array('~\s+~s' ), array(' '), $clean)));
            if (strpos($clean, '@') !== FALSE  OR strpos($clean,'char(')!== FALSE OR strpos($clean,'"')!== FALSE
                OR strpos($clean,'$s$$s$')!== FALSE){
                $fail = TRUE;
                if (preg_match("#^create table#i",$clean)) $fail = FALSE;
                $error = "unusual character";
            }elseif (strpos($clean, '/*') > 2 || strpos($clean, '--') !== FALSE || strpos($clean, '#') !== FALSE){
                $fail = TRUE;
                $error = "comment detect";
            }elseif (strpos($clean, 'sleep') !== FALSE && preg_match('~(^|[^a-z])sleep($|[^[a-z])~is', $clean) != 0){
                $fail = TRUE;
                $error = "slown down detect";
            }elseif (strpos($clean, 'benchmark') !== FALSE && preg_match('~(^|[^a-z])benchmark($|[^[a-z])~is', $clean) != 0){
                $fail = TRUE;
                $error = "slown down detect";
            }elseif (strpos($clean, 'load_file') !== FALSE && preg_match('~(^|[^a-z])load_file($|[^[a-z])~is', $clean) != 0){
                $fail = TRUE;
                $error = "file fun detect";
            }elseif (strpos($clean, 'into outfile') !== FALSE && preg_match('~(^|[^a-z])into\s+outfile($|[^[a-z])~is', $clean) != 0){
                $fail = TRUE;
                $error = "file fun detect";
            }
            if (!empty($fail)){
                fputs(fopen($log_file,'a+'),"<?PHP exit();?>$userIP||$time<br>$getUrl<br>$db_string<br>$error<br>===========<br>\r\n");
                echo _Sys_Safe_Stop::scanpape(); exit();
            }else{
                return $db_string_bak;
            }
        }
        return $db_string_bak;
    }

    private static function _do_query_safe($sql) {
        $sql = str_replace(array('\\\\', '\\\'', '\\"', '\'\''), '', $sql);
        if (strpos($sql, '/') === false && strpos($sql, '#') === false && strpos($sql, '-- ') === false && strpos($sql, '@') === false && strpos($sql, '`') === false) {
            $clean = preg_replace("/'(.+?)'/s", '', $sql);
        } else {
            $len = strlen($sql);
            $mark = $clean = '';
            for ($i = 0; $i < $len; $i++) {
                $str = $sql[$i];
                switch ($str) {
                    case '`':
                        if(!$mark) {
                            $mark = '`';
                            $clean .= $str;
                        } elseif ($mark == '`') {
                            $mark = '';
                        }
                        break;
                    case '\'':
                        if (!$mark) {
                            $mark = '\'';
                            $clean .= $str;
                        } elseif ($mark == '\'') {
                            $mark = '';
                        }
                        break;
                    case '/':
                        if (empty($mark) && $sql[$i + 1] == '*') {
                            $mark = '/*';
                            $clean .= $mark;
                            $i++;
                        } elseif ($mark == '/*' && $sql[$i - 1] == '*') {
                            $mark = '';
                            $clean .= '*';
                        }
                        break;
                    case '#':
                        if (empty($mark)) {
                            $mark = $str;
                            $clean .= $str;
                        }
                        break;
                    case "\n":
                        if ($mark == '#' || $mark == '--') {
                            $mark = '';
                        }
                        break;
                    case '-':
                        if (empty($mark) && substr($sql, $i, 3) == '-- ') {
                            $mark = '-- ';
                            $clean .= $mark;
                        }
                        break;

                    default:

                        break;
                }
                $clean .= $mark ? '' : $str;
            }
        }

        if(strpos($clean, '@') !== false) {
            return '-3';
        }

        $clean = preg_replace("/[^a-z0-9_\-\(\)#\*\/\"]+/is", "", strtolower($clean));

        if (self::$config['afullnote']) {
            $clean = str_replace('/**/', '', $clean);
        }

        if (is_array(self::$config['dfunction'])) {
            foreach (self::$config['dfunction'] as $fun) {
                if (strpos($clean, $fun . '(') !== false)
                    return '-1';
            }
        }

        if (is_array(self::$config['daction'])) {
            foreach (self::$config['daction'] as $action) {
                if (strpos($clean, $action) !== false)
                    return '-3';
            }
        }

        if (self::$config['dlikehex'] && strpos($clean, 'like0x')) {
            return '-2';
        }

        if (is_array(self::$config['dnote'])) {
            foreach (self::$config['dnote'] as $note) {
                if (strpos($clean, $note) !== false)
                    return '-4';
            }
        }
        return 1;
    }
}
