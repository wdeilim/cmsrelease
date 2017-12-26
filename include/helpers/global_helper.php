<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 获取表
 */
function table($table){
    return defined('BASE_DB_FORE')?BASE_DB_FORE.$table:$table;
}

/**
 * 获取表 (每个客户自己的)
 */
function tableal($table, $alid = 0){
    if (empty($alid)) {
        global $_A;
        $alid = $_A['al']['id'];
    }
    $alid = intval($alid);
    $ttable = table($table);
    if (in_array($table, array('message')) && $alid > 0) {
        $utable = table('_al'.$alid.'_'.$table);
        if (db_tableexists($utable)) {
            $ttable = $utable;
        }else{
            $CI = & get_instance();
            $CI->load->dbutil();
            $prefs = array(
                'tables'    => array(table($table)),
                'ignore'    => array(),
                'format'    => 'txt',
                'filename'  => $table.'.sql',
                'add_drop'  => TRUE,
                'add_insert'    => FALSE,
                'newline'   => "\r\n"
            );
            $tabledump = $CI->dbutil->backup($prefs);
            $tabledump = str_replace(' '.$ttable.' ', ' '.$utable.' ', $tabledump);
            $tabledump = str_replace('`'.$ttable.'`', '`'.$utable.'`', $tabledump);
            $tabledump = preg_replace('/ AUTO_INCREMENT=([\d\.]+) /i', ' ', $tabledump);
            $tabledump = preg_replace('/ COMMENT=\'(.*?)\'/i', ' COMMENT=\'$1_会员ALID:'.$alid.'\'', $tabledump);
            db_run($tabledump);
            if (db_tableexists($utable)) {
                $ttable = $utable;
                //把原数据迁移到新表
                if ($table == 'message') {
                    $items = db_getall(table($table), array('alid'=>$alid), '`id`');
                    $runsql = "";
                    foreach($items AS $item) {
                        $value = '';
                        foreach($item AS $val) {
                            $value.= ",".db_escape($val);
                        }
                        $value = substr($value,1);
                        $runsql.= "INSERT INTO `".$ttable."` VALUES (".$value.");\r\n";
                    }
                    if ($runsql) { db_run($runsql); }
                }
            }
        }
    }
    return $ttable;
}

/**
 * 获取值
 */
function value($obj, $key = '', $null_is_arr = false, $default = ''){
    if (is_int($key)) {
        if (isset($obj[$key])){
            $obj = $obj[$key];
        }else{
            $obj = "";
        }
    }elseif (!empty($key)){
        $arr = explode(".", str_replace("|", ".", $key));
        foreach ($arr as $val){
            if (isset($obj[$val])){
                $obj = $obj[$val];
            }else{
                $obj = "";break;
            }
        }
    }
    if ($default && empty($obj)) $obj = $default;
    if ($null_is_arr) {
        if ($null_is_arr === 'int') {
            $obj = intval($obj);
        }elseif (empty($obj)) {
            $obj = array();
        }
    }
    return $obj;
}

/**
 * $a=$b 则返回$c
 */
function isto($a, $b, $c){
    if ($a == $b){
        return $c;
    }else{
        return "";
    }
}

/**
 * 跳转
 */
function gourl($url = null){
    if (empty($url)){
        $url = get_url();
    }
    header("Location: ".$url);
    exit();
}

/**
 * $n=$v 则返回selected="selected"
 */
function sel($n, $v, $d = false){
    if ($d && empty($v)) return 'selected="selected"';
    return ($n == $v)?' selected="selected"':'';
}

/**
 * 给文字加颜色标签font
 */
function col($n, $c=''){
    if (!empty($c)){
        return "<font color='".$c."'>".$n."</font>";
    }else{
        return $n;
    }
}

/**
 * 给文字加颜色标签style
 */
function cot($color){
    if ($color){
        return " style='color:".$color.";'";
    }
}

/**
 * $n=$v 则返回checked="true"
 */
function che($n, $v, $d = false){
    if ($d && empty($v)) return 'checked="true"';
    $val = " value=\"".$v."\"";
    if (is_array($n)){
        $val.= (in_array($v, $n))?' checked="true"':'';
    }else{
        $val.= ($n == $v)?' checked="true"':'';
    }
    return $val;
}

/**
 * $n 包含,$v, 则返回checked="true"
 */
function ches($n, $v, $d = false){
    if ($d && empty($v)) return 'checked="true"';
    $val = " value=\"".$v."\"";
    $val.= (strpos($n, ",".$v.",") !== false)?' checked="true"':'';
    return $val;
}

/**
 * $n[$v] 存在则返回checked="true"
 */
function chi($n, $v){
    $val = '';
    if (!empty($v)){
        if (isset($n[$v])){
            $val = ' checked="true"';
        }
    }
    return $val;
}

/**
 * 补零
 * @param $str
 * @param int $length
 * @param int $after
 * @return string
 */
function zerofill($str, $length = 0, $after = 1) {
    if (strlen($str) >= $length) {
        return $str;
    }
    $_str = '';
    for ( $i = 0; $i < $length; $i++ ){
        $_str .= '0';
    }
    if ($after) {
        $_ret = substr($_str.$str, $length*-1);
    }else{
        $_ret = substr($str.$_str, 0, $length);
    }
    return $_ret;
}
/**
 * 新建目录
 */
function make_dir($path){
    if(!file_exists($path)){
        make_dir(dirname($path));
        @mkdir($path,0777);
        @chmod($path,0777);
    }
}

/**
 *
 * 写入缓存文本
 * @param string $file_path 保存路径
 * @param array $config_arr 保存的数组
 */
function writecache($file_path, $config_arr){
    if (!isset($config_arr['auto_time'])) $config_arr['auto_time'] = time();
    if (!isset($config_arr['auto_time_text'])) $config_arr['auto_time_text'] = date('Y-m-d', time());
    $content = "<?php\r\n";
    $content .= "\$data = " . var_export($config_arr, true) . ";\r\n";
    $content .= "?>";
    $cache_file_path = FCPATH."caches".DIRECTORY_SEPARATOR."cache.".$file_path.".php";
    make_dir(dirname($cache_file_path));
    if (!file_put_contents($cache_file_path, $content, LOCK_EX))
    {
        $fp = @fopen($cache_file_path, 'wb+');
        if (!$fp)
        {
            exit('生成缓存文件失败');
        }
        if (!@fwrite($fp, trim($content)))
        {
            exit('生成缓存文件失败');
        }
        @fclose($fp);
    }
}

/**
 * 读取缓存文本
 * @param string $file_path    保存路径
 * @return array
 */
function getcache($file_path){
    $cache_file_path = FCPATH."caches".DIRECTORY_SEPARATOR."cache.".$file_path.".php";
    if(file_exists($cache_file_path)) {
        @include($cache_file_path);
        return isset($data)?$data:array();
    }else{
        return array();
    }
}

/**
 * 去除html
 * @param $text
 * @param int $length
 * @return mixed|string
 */
function get_html($text, $length = 255){
    $text = cut_str(strip_tags($text), $length, 0, "...");
    return $text;
}
/**
 *
 * 截取字符串
 * @param string $string 	字符串
 * @param int $length 	    截取长度
 * @param int $start 	    何处开始
 * @param string $dot 		超出尾部添加
 * @param string $charset 	默认编码
 * @return mixed|string
 */
function cut_str($string, $length, $start=0, $dot='', $charset = BASE_CHARSET)
{
    if (strtolower($charset) == 'utf-8'){
        if(get_strlen($string) <= $length) return $string;
        $strcut = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array('&', '"', '<', '>'), $string);
        $strcut = utf8_substr($strcut, $length, $start);
        $strcut = str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $strcut);
        return $strcut.$dot;
    }else{
        $length=$length*2;
        if(strlen($string) <= $length) return $string;
        $string = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array('&', '"', '<', '>'), $string);
        $strcut = '';
        for($i = 0; $i < $length; $i++) {
            $strcut .= ord($string[$i]) > 127 ? $string[$i].$string[++$i] : $string[$i];
        }
        $strcut = str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $strcut);
    }
    return $strcut.$dot;
}

/**
 * PHP获取字符串中英文混合长度
 * @param string $str 		字符串
 * @param string $charset	编码
 * @return float            返回长度，1中文=1位，2英文=1位
 */
function get_strlen($str,$charset = BASE_CHARSET){
    if(strtolower($charset)=='utf-8') $str = iconv('utf-8','GBK//IGNORE',$str);
    $num = strlen($str);
    $cnNum = 0;
    for($i=0;$i<$num;$i++){
        if(ord(substr($str,$i+1,1))>127){
            $cnNum++;
            $i++;
        }
    }
    $enNum = $num-($cnNum*2);
    $number = ($enNum/2)+$cnNum;
    return ceil($number);
}

/**
 * PHP截取UTF-8字符串，解决半字符问题。
 * @param string $str       源字符串
 * @param int $len          左边的子串的长度
 * @param int $start        何处开始
 * @return string           取出的字符串, 当$len小于等于0时, 会返回整个字符串
 */
function utf8_substr($str, $len, $start=0)
{
    $len=$len*2;
    for($i=0;$i<$len;$i++)
    {
        $temp_str=substr($str,0,1);
        if(ord($temp_str) > 127){
            $i++;
            if($i<$len){
                $new_str[]=substr($str,0,3);
                $str=substr($str,3);
            }
        }else{
            $new_str[]=substr($str,0,1);
            $str=substr($str,1);
        }
    }
    return join(array_slice($new_str,$start));
}

/**
 * 将字符串转换为数组
 * @param	string	$data	字符串
 * @return	array	返回数组格式，如果，data为空，则返回空数组
 */
function string2array($data) {
    if(is_array($data)) return $data;
    $data = trim($data);
    if($data == '') return array();
    if (strpos(strtolower($data), 'array') === 0) {
        @ini_set('display_errors', 'on');
        @eval("\$array = $data;");
        @ini_set('display_errors', 'off');
    }else{
        if (strpos($data, '{\\') === 0) {
            $data = stripslashes($data);
        }
        $array = json_decode($data, true);
    }
    return (isset($array)&&is_array($array))?$array:array();
}

/**
 * 将数组转换为字符串
 * @param	array	$data		数组
 * @param	int 	$isformdata	如果为0，则不使用new_stripslashes处理，可选参数，默认为1
 * @return	string	返回字符串，如果，data为空，则返回空
 */
function array2string($data, $isformdata = 1) {
    if ($data == '' || empty($data)) return '';
    if ($isformdata) $data = new_stripslashes($data);
    if (version_compare(PHP_VERSION,'5.3.0','<')){
        return addslashes(json_encode($data));
    }else{
        return addslashes(json_encode($data, JSON_FORCE_OBJECT));
    }
}

/**
 * 将数组转换为字符串 (已废弃)
 * @param	array	$data		数组
 * @param	int 	$isformdata	如果为0，则不使用new_stripslashes处理，可选参数，默认为1
 * @return	string	返回字符串，如果，data为空，则返回空
 */
function array2string_discard($data, $isformdata = 1) {
    if ($data == '' || empty($data)) return '';
    if ($isformdata) $data = new_stripslashes($data);
    return var_export($data, TRUE);
}

/**
 * @param $array
 * @return array
 */
function object_array($array){
    if(is_object($array)){
        $array = (array)$array;
    }
    if(is_array($array)){
        foreach($array as $key=>$value){
            $array[$key] = object_array($value);
        }
    }
    return $array;
}

/**
 * @param string $source  传的是文件，还是xml的string的判断
 * @param bool|false $simplexml
 * @return string
 */
function xml2json($source, $simplexml = false) {
    if ($simplexml) {
        if(is_file($source)){
            $xml_array = @simplexml_load_file($source);
        }else{
            $xml_array = @simplexml_load_string($source, NULL, LIBXML_NOCDATA);
        }
    }else{
        $xml_array = xml2array($source);
    }
    $json = json_encode($xml_array);
    return $json;
}

/**
 * @param $XML
 * @return array
 */
function xml2array($XML) {
    $CI = & get_instance();
    $CI->load->library('x2a');
    return $CI->x2a->xmltoarray($XML);
}

/**
 * 返回经stripslashes处理过的字符串或数组
 * @param array|string $string 需要处理的字符串或数组
 * @return mixed
 */
function new_stripslashes($string) {
    if(!is_array($string)) return stripslashes($string);
    foreach($string as $key => $val) $string[$key] = new_stripslashes($val);
    return $string;
}

/**
 * 返回经addslashes处理过的字符串或数组
 * @param array|string $string 需要处理的字符串或数组
 * @return mixed
 */
function new_addslashes($string){
    if(!is_array($string)) return addslashes($string);
    foreach($string as $key => $val) $string[$key] = new_addslashes($val);
    return $string;
}

/**
 * 合拼数组
 * @return array
 */
function _array_merge()
{
    $arg_list = func_get_args();
    $arr = array();
    $j = count($arg_list);
    if ($j > 0) {
        for ($i = 0; $i < $j; $i++) {
            if (is_array($arg_list[$i])) {
                $arr = array_merge($arr, $arg_list[$i]);
            }
        }
    }
    return $arr;
}

/**
 * @return string
 *
 * 用法
 * ① weburl('a/welcome/')                       返回 http://xxx/web/a/welcome/?.....
 * ② weburl('a/welcome/', array('rid'=>1))      返回 http://xxx/web/a/welcome/?rid=1&.....
 * ③ weburl(0, 'a', 'b', 'c')                   返回 http://xxx/web/a/b/c/?.....
 * ③ weburl(array('rid'=>1), 'a', 'b', 'c')     返回 http://xxx/web/a/b/c/?rid=1&.....
 */
function weburl()
{
    $arg_list = func_get_args();
    $arg1 = isset($arg_list[0])?$arg_list[0]:'';
    if (!isset($arg_list[0])) {
        $CI = & get_instance();
        $arg1 = ltrim(ltrim($CI->uri->uri_string(), 'web'), '/');
        $arg2 = array();
    }elseif (empty($arg1) || is_array($arg1)) {
        $arg2 = $arg1;
        $arg1 = "";
        for ($i = 1; $i < count($arg_list); $i++) {
            $arg1.= $arg_list[$i]."/";
        }
        $arg1 = rtrim($arg1, "/");
    }else{
        $arg2 = isset($arg_list[1])?$arg_list[1]:'';
    }
    return systemurl($arg1, $arg2, 'web');
}

/**
 * @return string
 */
function appurl()
{
    $arg_list = func_get_args();
    $arg1 = isset($arg_list[0])?$arg_list[0]:'';
    if (!isset($arg_list[0])) {
        $CI = & get_instance();
        $arg1 = ltrim(ltrim($CI->uri->uri_string(), 'app'), '/');
        $arg2 = array();
    }elseif (empty($arg1) || is_array($arg1)) {
        $arg2 = $arg1;
        $arg1 = "";
        for ($i = 1; $i < count($arg_list); $i++) {
            $arg1.= $arg_list[$i]."/";
        }
        $arg1 = rtrim($arg1, "/");
    }else{
        $arg2 = isset($arg_list[1])?$arg_list[1]:'';
    }
    return systemurl($arg1, $arg2, 'app');
}

/**
 * @return string
 */
function mobileurl()
{
    $arg_list = func_get_args();
    $arg1 = isset($arg_list[0])?$arg_list[0]:'';
    if ($arg1 === 0) {
        $CI = & get_instance();
        $arg1 = ltrim(ltrim($CI->uri->uri_string(), 'app'), '/');
        $arg2 = array();
    }elseif (empty($arg1) || is_array($arg1)) {
        $arg2 = $arg1;
        $arg1 = "";
        for ($i = 1; $i < count($arg_list); $i++) {
            $arg1.= $arg_list[$i]."/";
        }
        $arg1 = rtrim($arg1, "/");
    }else{
        $arg2 = isset($arg_list[1])?$arg_list[1]:'';
    }
    return systemurl($arg1, $arg2, 'app');
}

/**
 * @param null $p
 * @param array $a
 * @param string $page
 * @return string
 */
function systemurl($p = null, $a = array(), $page = '')
{
    global $_A;
    static $ES_URL = array();
    $m5 = is_array($a)?implode('',$a):$a;
    $m5 = md5($p.$m5.$page);
    if (isset($ES_URL[$m5])) {
        return $ES_URL[$m5];
    }
    if (isset($ES_URL['CI'])) {
        $CI = $ES_URL['CI'];
    }else{
        $CI = $ES_URL['CI'] = & get_instance();
    }
    if (is_numeric($p)) {
        $p = $CI->base->url[intval($p)];
    }elseif (in_array($p, array('now','index'))) {
        $p = $CI->base->url[$p];
    }else{
        $p = $page."/".$p;
        $p = $CI->config->site_url($p)."/";
    }
    $_a = '';
    if (is_array($a)) {
        $_a = '';
        foreach($a AS $k=>$v) {
            $_a.= $k."=".$v."&";
        }
        $_a = rtrim($_a,'&');
    }elseif ($a) {
        $_a = $a;
    }
    if (isset($_A['u']['userid'])) $_a.= "&ui=".$_A['u']['userid'];
    if (isset($_A['al']['id'])) $_a.= "&al=".$_A['al']['id'];
    if (isset($_A['uf']['id'])) $_a.= "&uf=".$_A['uf']['id'];
    if ($_a) {
        $_a = "?".ltrim(ltrim($_a,'&'),'?');
    }else{
        $_a = "?index=".generate_password(5);
    }
    return $ES_URL[$m5] = $p.$_a;
}

/**
 * 加密 openid
 * @param string $string      明文 或 密文
 * @param string $operation   DECODE表示解密,其它表示加密
 * @param string $key         密匙
 * @param int $expiry         密文有效期
 * @return string
 */
function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
    $ckey_length = 4;
    $key = md5($key != '' ? $key : BASE_ENCRYPTION);
    $keya = md5(substr($key, 0, 16));
    $keyb = md5(substr($key, 16, 16));
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

    $cryptkey = $keya.md5($keya.$keyc);
    $key_length = strlen($cryptkey);

    $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
    $string_length = strlen($string);

    $result = '';
    $box = range(0, 255);

    $rndkey = array();
    for($i = 0; $i <= 255; $i++) {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
    }

    for($j = $i = 0; $i < 256; $i++) {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }

    for($a = $j = $i = 0; $i < $string_length; $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }

    if($operation == 'DECODE') {
        if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
            return substr($result, 26);
        } else {
            return '';
        }
    } else {
        return $keyc.str_replace('=', '', base64_encode($result));
    }

}

/**
 * 获取当前页面地址
 * @return string
 */
function get_url() {
    global $_A;
    return $_A['url']['now'].get_get();
    /*
    $sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
    $php_self = $_SERVER['PHP_SELF'] ? safe_replace($_SERVER['PHP_SELF']) : safe_replace($_SERVER['SCRIPT_NAME']);
    $path_info = isset($_SERVER['PATH_INFO']) ? safe_replace($_SERVER['PATH_INFO']) : '';
    $relate_url = isset($_SERVER['REQUEST_URI']) ? safe_replace($_SERVER['REQUEST_URI']) : $php_self.(isset($_SERVER['QUERY_STRING']) ? '?'.safe_replace($_SERVER['QUERY_STRING']) : $path_info);
    $_get_url = $sys_protocal.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '').$relate_url;
    $ipageurl = BASE_URI;
    $ipageurl.= BASE_IPAGE?ltrim(BASE_IPAGE,'/').'/':BASE_IPAGE;
    return str_replace(BASE_URI.'index.php/', $ipageurl, $_get_url);
    */
}

/**
 * 安全过滤函数
 *
 * @param $string
 * @return string
 */
function safe_replace($string) {
    $string = str_replace('%20','',$string);
    $string = str_replace('%27','',$string);
    $string = str_replace('%2527','',$string);
    $string = str_replace('*','',$string);
    $string = str_replace('"','&quot;',$string);
    $string = str_replace("'",'',$string);
    $string = str_replace('"','',$string);
    $string = str_replace(';','',$string);
    $string = str_replace('<','&lt;',$string);
    $string = str_replace('>','&gt;',$string);
    $string = str_replace("{",'',$string);
    $string = str_replace('}','',$string);
    $string = str_replace('\\','',$string);
    return $string;
}

/**
 * 去除或保留链接中的参数
 * @param string|null $param   要去除或保留的参数名称
 * @param string|null $baoliu  留空为去除，其他为保留
 * @param array $array         自定义参数组，留空为$_GET参数组
 * @return string
 */
function get_get($param = null, $baoliu = null, $array = array()) {
    if (!empty($array) && is_array($array)) {
        $get = $array;
    }else{
        $CI =& get_instance();
        $get = $CI->input->get();
    }
    $text = "";
    if ($param){
        $str = str_replace("|", ",", $param);
        $arr = explode(',', $str);
        if ($baoliu){
            $get1 = array();
            foreach($arr as $value){
                $get1[$value] = $get[$value];
            }
            $get = $get1;
        }else{
            foreach($arr as $value){
                unset($get[$value]);
            }
        }
    }
    if ($get){
        foreach($get as $k=>$v){
            $text .="{$k}={$v}&";
        }
    }
    $text = !empty($text)?"?".substr($text,0,-1):'';
    return $text;
}

/**
 * 获取地址(去除链接参数)
 * @param string $str     变量,用半角逗号隔开
 * @param string $baoliu  采用保留方式
 * @param array $array    链接自定变量
 * @param int $allurl     1保留全路径,0不保留
 * @return string
 */
function get_link($str = '', $baoliu = '', $array = array(), $allurl = 1) {
    global $_A;
    $get = get_get($str, $baoliu, $array);
    if (empty($get)) {
        $get = '?index='.generate_password(5);
    }
    $_url = $_A['url']['now'];
    $index = $_A['url']['index'];
    if (!isset($_A['url']['now'])) {
        $CI =& get_instance();
        $_url = $CI->config->site_url($CI->uri->uri_string()).'/';
        $index = $CI->config->site_url().'/';
    }
    if (!$allurl){
        $_url = substr($_url, strlen($index) - 1);
    }
    return $_url.$get;
}

/**
 * @param $str
 * @return string
 */
function get_smarty_request($str){
    $CI =& get_instance();
    $str=rawurldecode($str);
    $strtrim=rtrim($str,']');
    if (substr($strtrim,0,4)=='GET['){
        $getkey=substr($strtrim,4);
        return $CI->input->get($getkey);
    }elseif (substr($strtrim,0,5)=='POST['){
        $getkey=substr($strtrim,5);
        return $CI->input->post($getkey);
    }elseif (substr($strtrim,0,6)=='PGEST['){
        $getkey=substr($strtrim,6);
        return $CI->input->post($getkey)?$CI->input->post($getkey):$CI->input->get($getkey);
    }else{
        return $str;
    }
}

/**
 * @param string $title       提示标题
 * @param string $body        提示正文内容
 * @param array $links        显示链接组或链接
 * @param string $gotolinks   自动跳转链接
 * @param string $gototime    自动跳转链接时间
 */
function message($title = '', $body = '', $links = array(), $gotolinks = '', $gototime = '3') {
    $CI =& get_instance();
    if ($title && empty($body)) {
        $body = $title;
    }
    header ( "Content-type: text/html; charset=".BASE_CHARSET );
    $CI->cs->showmsg($title, $body, $links, $gotolinks, $gototime);
}

/**
 * @return string
 */
function get_ip(){
    if (getenv('HTTP_CLIENT_IP') and strcasecmp(getenv('HTTP_CLIENT_IP'),'unknown')) {
        $onlineip = getenv('HTTP_CLIENT_IP');
    }elseif (getenv('HTTP_X_FORWARDED_FOR') and strcasecmp(getenv('HTTP_X_FORWARDED_FOR'),'unknown')) {
        $onlineip = getenv('HTTP_X_FORWARDED_FOR');
    }elseif (getenv('REMOTE_ADDR') and strcasecmp(getenv('REMOTE_ADDR'),'unknown')) {
        $onlineip = getenv('REMOTE_ADDR');
    }elseif (isset($_SERVER['REMOTE_ADDR']) and $_SERVER['REMOTE_ADDR'] and strcasecmp($_SERVER['REMOTE_ADDR'],'unknown')) {
        $onlineip = $_SERVER['REMOTE_ADDR'];
    }else{
        $onlineip = '0,0,0,0';
    }
    preg_match("/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/", $onlineip, $match);
    return $onlineip = $match[0] ? $match[0] : 'unknown';
}

/**
 * @param $text
 * @param string $pass
 * @return string
 */
function md52($text, $pass = ''){
    $_text = md5($text) . $pass;
    return md5($_text);
}


/**
 * 随机字符串
 * @param int $length 随机字符长度
 * @param string $type
 * @return string 1数字、2大小写字母、21小写字母、22大写字母、默认全部;
 */
function generate_password( $length = 8 ,$type = '') {
    // 密码字符集，可任意添加你需要的字符
    switch ($type){
        case '1':
            $chars = '0123456789';
            break;
        case '2':
            $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            break;
        case '21':
            $chars = 'abcdefghijklmnopqrstuvwxyz';
            break;
        case '22':
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            break;
        default:
            $chars = $type?$type:'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            break;
    }
    $passwordstr = '';
    $max = strlen($chars) - 1;
    for ( $i = 0; $i < $length; $i++ ){
        $passwordstr .= $chars[ mt_rand(0, $max) ];
    }
    return $passwordstr;
}
function str_random($length, $chars = '0123456789') {
    return generate_password($length, $chars);
}

/**
 * 会员过期计算
 * @param $enddate
 * @param string $format
 * @return bool|string
 */
function user_status($enddate, $format = 'Y-m-d H:i:s'){
    if ($enddate <= 0) {
        return "永不过期";
    }
    $_val = $enddate - SYS_TIME;
    if ($_val <= 0){
        return "<font color='#999999' style='text-decoration:line-through;'> 已过期 </font>";
    }elseif($_val >= 0 && $_val < 59){
        return ($_val+1)."秒后过期";
    }elseif($_val >= 60 && $_val < 3600){
        $min = intval($_val / 60);
        return "<font color='#ff0000'><b>".$min."分钟后过期</b></font>";
    }elseif($_val >=3600 && $_val < 86400){
        $h = intval($_val / 3600);
        return "<font color='#C22A2A'>".$h."小时后过期</font>";
    }elseif ($_val >=86400 && $_val < 86400*30){
        return "<font color='#C58600'>".intval($_val / 86400)."天后过期</font>";
    }else{
        return date($format, $enddate);
    }
}

/**
 * 截取指定字符串
 * @param $str
 * @param string $ta
 * @param string $tb
 * @return string
 */
function get_subto($str, $ta = '', $tb = ''){
    if ($ta && strpos($str, $ta) !== false){
        $str = substr($str, strpos($str, $ta) + strlen($ta));
    }
    if ($tb && strpos($str, $tb) !== false){
        $str = substr($str, 0, strpos($str, $tb));
    }
    return $str;
}

/**
 * 相对路径补全
 * @param string $str
 * @return string
 */
function fillurl($str = ''){
    if (empty($str)) return $str;
    if (substr($str,0,7) == "http://" || substr($str,0,8) == "https://" || substr($str,0,6) == "ftp://" || substr($str,0,1) == "/" || substr(str_replace(' ','',$str),0,11) == "data:image/"){
        return $str;
    }else{
        return BASE_URI.$str;
    }
}
if (!function_exists('toimage')) {
    function toimage($src = '') {
        return fillurl($src);
    }
}
if (!function_exists('tomedia')) {
    function tomedia($src = '') {
        return fillurl($src);
    }
}

/**
 * 关键词分割整理
 * @param string $str
 * @return string
 */
function replykey($str = ''){
    if (empty($str)) return $str;
    $strarr = explode(",", $str);
    $strtext = "";
    foreach($strarr as $val){
        if ($val) $strtext.= '<em class="replykey" title="'.$val.'">'.cut_str($val,5,0,'...').'</em>';
    }
    return $strtext;
}

/**
 * 中国正常GCJ02坐标---->百度地图BD09坐标
 * 腾讯地图用的也是GCJ02坐标
 * @param float $lat 纬度
 * @param float $lng 经度
 * @return array
 */
function map_gcj02_to_bd09($lat, $lng){
    $x_pi = 3.14159265358979324 * 3000.0 / 180.0;
    $x = $lng;
    $y = $lat;
    $z =sqrt($x * $x + $y * $y) + 0.00002 * sin($y * $x_pi);
    $theta = atan2($y, $x) + 0.000003 * cos($x * $x_pi);
    $lng = $z * cos($theta) + 0.0065;
    $lat = $z * sin($theta) + 0.006;
    return array('lng'=>$lng,'lat'=>$lat);
}

/**
 * 百度地图BD09坐标---->中国正常GCJ02坐标
 * 腾讯地图用的也是GCJ02坐标
 * @param double $lat 纬度
 * @param double $lng 经度
 * @return array();
 */
function map_bd09_to_gcj02($lat,$lng){
    $x_pi = 3.14159265358979324 * 3000.0 / 180.0;
    $x = $lng - 0.0065;
    $y = $lat - 0.006;
    $z = sqrt($x * $x + $y * $y) - 0.00002 * sin($y * $x_pi);
    $theta = atan2($y, $x) - 0.000003 * cos($x * $x_pi);
    $lng = $z * cos($theta);
    $lat = $z * sin($theta);
    return array('lng'=>$lng,'lat'=>$lat);
}

/**
 * 格式化导出csv
 * @param $text
 * @return mixed|string
 */
function _csv($text){
    $text = str_replace('"','""', $text);
    $text = '"'.$text.'"';
    $text = strip_tags($text);
    return $text;
}

/**
 * 检测日期格式
 * @param string $str  需要检测的字符串
 * @return bool
 */
function is_date($str){
    $strArr = explode('-',$str);
    if(empty($strArr) || count($strArr) != 3){
        return false;
    } else {
        list($year, $month, $day) = $strArr;
        if (checkdate($month,$day,$year)) {
            return true;
        } else {
            return false;
        }
    }
}

/**
 * 检测时间格式
 * @param string $str  需要检测的字符串
 * @return bool
 */
function is_time($str){
    $strArr = explode(':',$str);
    if(empty($strArr) || count($strArr) != 2){
        return false;
    } else {
        list($hour, $minute) = $strArr;
        if (intval($hour) > 23 || intval($minute) > 59) {
            return false;
        } else {
            return true;
        }
    }
}

/**
 * 检测手机号码格式
 * @param string $str  需要检测的字符串
 * @return int
 */
function isMobile($str) {
    return preg_match("/^1(3|4|5|8)\d{9}$/",$str);
}

/**
 * 检测邮箱格式
 * @param string $str 需要检测的字符串
 * @return int
 */
function isMail($str){
    $RegExp='/^[a-z0-9][a-z\.0-9-_]+@[a-z0-9_-]+(?:\.[a-z]{0,3}\.[a-z]{0,2}|\.[a-z]{0,3}|\.[a-z]{0,2})$/i';
    return preg_match($RegExp,$str);
}

/**
 * 阵列数组
 * @param $keys
 * @param $src
 * @param bool $default
 * @return array
 */
function array_elements($keys, $src, $default = FALSE) {
    $return = array();
    if(!is_array($keys)) {
        $keys = array($keys);
    }
    foreach($keys as $key) {
        if(isset($src[$key])) {
            $return[$key] = $src[$key];
        } else {
            $return[$key] = $default;
        }
    }
    return $return;
}

/**
 * 模板输出
 * @param string $file
 * @param null $_param
 * @param null $_smd5
 */
function tpl($file = '', $_param = null, $_smd5 = null) {
    get_instance()->cs->show($file, $_param, $_smd5);
}

/**
 * @param string $file
 * @return string
 */
function tpl_fetch($file = '') {
    define('___TPL_FETCH_FILE',  $file);
    return BASE_PATH.'include/views/tpl_fetch.php';
}

/**
 * 模板输出 (指定系统文件)
 * @param string $file
 * @return string
 */
function template($file = '') {
    $filesite = "";
    switch($file) {
        case 'header':
            $filesite = BASE_PATH."addons/system/template/header_fun.tpl";
            break;
        case 'footer':
            $filesite = BASE_PATH."addons/system/template/footer.tpl";
            break;
        case 'reply_right':
            $filesite = BASE_PATH."addons/system/template/reply/_right.tpl";
            break;
    }
    return $filesite?get_instance()->cs->fetch($filesite):'';
}


/**
 * 判断字符串存在(包含)
 * @param string $string
 * @param string $find
 * @return bool
 */
function strexists($string, $find) {
    return !(strpos($string, $find) === FALSE);
}

/**
 * 判断字符串开头包含
 * @param string $string        //原字符串
 * @param string $find          //判断字符串
 * @param bool|false $lower     //是否不区分大小写
 * @return int
 */
function leftexists($string, $find, $lower = false) {
    if ($lower) {
        $string = strtolower($string);
        $find = strtolower($find);
    }
    return (substr($string, 0, strlen($find)) == $find);
}

/**
 * 判断字符串结尾包含
 * @param string $string        //原字符串
 * @param string $find          //判断字符串
 * @param bool|false $lower     //是否不区分大小写
 * @return int
 */
function rightexists($string, $find, $lower = false) {
    if ($lower) {
        $string = strtolower($string);
        $find = strtolower($find);
    }
    return (substr($string, strlen($find)*-1) == $find);
}

/**
 * @param $errno
 * @param string $message
 * @return array
 */
function error($errno, $message = '') {
    return array(
        'errno' => $errno,
        'message' => $message,
    );
}

/**
 * @param $data
 * @return bool
 */
function is_error($data) {
    if (empty($data) || !is_array($data) || !array_key_exists('errno', $data) || (array_key_exists('errno', $data) && $data['errno'] == 0)) {
        return false;
    } else {
        return true;
    }
}

/**
 * 版本比较
 * @param $version1
 * @param $version2
 * @return mixed
 */
function ver_compare($version1, $version2) {
    if(strlen($version1) <> strlen($version2)) {
        $version1_tmp = explode('.', $version1);
        $version2_tmp = explode('.', $version2);
        if(strlen($version1_tmp[1]) == 1) {
            $version1 .= '0';
        }
        if(strlen($version2_tmp[1]) == 1) {
            $version2 .= '0';
        }
    }
    return version_compare($version1, $version2);
}

/**
 * 获取模块参数组
 * @param $setname
 * @param string $modulename
 * @return array
 */
function module_setting($setname, $modulename = '') {
    global $_A;
    if (empty($modulename)) {
        $modulename = $_A['module'];
    }
    $setting = array();
    if ($setname && $modulename) {
        $setrow = db_getone(table('bind_setting'), array('alid'=>$_A['al']['id'], 'module'=>$modulename, 'do'=>$setname));
        if (!empty($setrow)) {
            $setting = string2array($setrow['setting']);
        }
    }
    return $setting;
}


/**
 * 查询分页列表
 * @param string $table     表名称
 * @param string $where     查询条件，默认空
 * @param string $order     排序方式，默认空
 * @param int $row          每页显示，默认10
 * @param int $page         当前页，默认1
 * @param string $field     读取字段名称
 * @return array (total=>总数量,perpage=>每页显示,nowpage=>当前页,totalpage=>总页数,list=>数据列表)
 */
function db_getlist($table, $where='', $order='', $row=10, $page=1, $field='*') {
    return get_instance()->ddb->getlist($table, $where, $order, $row, $page, $field);
}

/**
 * 查询统计
 * @param string  $sql
 * @param array   $wherearr
 * @return mixed
 */
function db_total($sql, $wherearr = array()) {
    return get_instance()->ddb->get_total($sql, $wherearr);
}

/**
 * 查询行数统计
 * @param string  $sql
 * @param array   $wherearr
 * @return mixed
 */
function db_count($sql, $wherearr = array()) {
    return get_instance()->ddb->get_count($sql, $wherearr);
}

/**
 * 查询第一条数据
 * @param string  $sql         数据表名称 或 sql语句
 * @param array   $wherearr    查询条件 支持数组 或 条件字符串
 * @param string  $ordersql    排序（不包含ORDER BY）
 * @return mixed
 */
function db_getone($sql, $wherearr = array(), $ordersql = '') {
    return get_instance()->ddb->getone($sql, $wherearr, $ordersql);
}

/**
 * 查询全部数据
 * @param string  $sql         数据表名称 或 sql语句
 * @param array   $wherearr    查询条件 支持数组 或 条件字符串
 * @param string  $ordersql    排序（不包含ORDER BY）
 * @return mixed
 */
function db_getall($sql, $wherearr = array(), $ordersql = '') {
    return get_instance()->ddb->getall($sql, $wherearr, $ordersql);
}

/**
 * 更新数据
 * @param string  $table       数据表名称
 * @param array   $data        要更新的数据组
 * @param array   $where       更新条件 支持数组 或 条件字符串
 * @return mixed
 */
function db_update($table, $data = array(), $where = array()) {
    return get_instance()->ddb->update($table, $data, $where);
}

/**
 * 插入数据
 * @param string  $table       数据表名称
 * @param array   $data        要插入的数据组
 * @param bool    $retid       是否返回主键值
 * @return mixed
 */
function db_insert($table, $data = array(), $retid = false){
    return get_instance()->ddb->insert($table, $data, $retid);
}

/**
 * 删除数据
 * @param string  $table       数据表名称
 * @param array   $where       删除条件 支持数组 或 条件字符串
 * @param string  $glue        删除数组型条件连接符 AND 或 OR
 * @return mixed
 */
function db_delete($table, $where = array(), $glue = 'AND'){
    return get_instance()->ddb->delete($table, $where, $glue);
}


/**
 * 执行一条sql
 * @param string  $sql       完整的SQL
 * @param array   $wherearr
 * @return mixed
 */
function db_query($sql, $wherearr = array()) {
    return get_instance()->ddb->query($sql, $wherearr);
}

/**
 * 简化执行一条sql
 * @param string  $sql        完整的SQL
 * @param array   $wherearr
 * @return mixed
 */
function db_query_simple($sql, $wherearr = array()) {
    return get_instance()->ddb->query_simple($sql, $wherearr);
}

/**
 * 运行多条sql
 * @param string  $sql        完整的SQL，多条SQL用 ;+换行 隔开
 * @return mixed
 */
function db_run($sql) {
    return get_instance()->ddb->run($sql);
}

/**
 * 简化运行多条sql
 * @param string  $sql        完整的SQL，多条SQL用 ;+换行 隔开
 * @return mixed
 */
function db_run_simple($sql) {
    return get_instance()->ddb->run_simple($sql);
}

/**
 * 事务开始
 */
function db_trans_start() {
    return get_instance()->ddb->trans_start();
}

/**
 * 事务完成
 */
function db_trans_complete() {
    return get_instance()->ddb->trans_complete();
}

/**
 * 查询表字段是否存在
 * @param string $table       数据表名称
 * @param string $fieldname   字段名称
 * @return mixed
 */
function db_fieldexists($table, $fieldname = '') {
    return get_instance()->ddb->fieldexists($table, $fieldname);
}

/**
 * 查询索引是否存在
 * @param string $table       数据表名称
 * @param string $indexname   索引名称
 * @return mixed
 */
function db_indexexists($table, $indexname = '') {
    return get_instance()->ddb->indexexists($table, $indexname);
}

/**
 * 查询表是否存在
 * @param string $table        数据表名称
 * @return mixed
 */
function db_tableexists($table){
    return get_instance()->ddb->tableexists($table);
}

/**
 * @param $str
 * @return array|int|string
 */
function db_escape($str) {
    return get_instance()->ddb->escape($str);
}
function db_escape_str($str) {
    return get_instance()->ddb->escape_str($str);
}
function db_escape_like_str($str) {
    return get_instance()->ddb->escape_like_str($str);
}
function db_escape_identifiers($str) {
    return get_instance()->ddb->escape_identifiers($str);
}
function db_addcheck($str) {
    return get_instance()->ddb->addcheck($str);
}
function db_checksql($str) {
    return get_instance()->ddb->checksql($str);
}

/**
 * 分页
 * @param $total
 * @param $pageIndex
 * @param int $pageSize
 * @param string $url
 * @param array $context
 * @return string
 */
function pagination($total, $pageIndex, $pageSize = 15, $url = '', $context = array('before' => 5, 'after' => 4)) {
    global $_A;
    $pdata = array(
        'tcount' => 0,
        'tpage' => 0,
        'cindex' => 0,
        'findex' => 0,
        'pindex' => 0,
        'nindex' => 0,
        'lindex' => 0,
        'options' => ''
    );

    $pdata['tcount'] = $total;
    $pdata['tpage'] = ceil($total / $pageSize);
    if($pdata['tpage'] <= 1) {
        return '';
    }
    $cindex = $pageIndex;
    $cindex = min($cindex, $pdata['tpage']);
    $cindex = max($cindex, 1);
    $pdata['cindex'] = $cindex;
    $pdata['findex'] = 1;
    $pdata['pindex'] = $cindex > 1 ? $cindex - 1 : 1;
    $pdata['nindex'] = $cindex < $pdata['tpage'] ? $cindex + 1 : $pdata['tpage'];
    $pdata['lindex'] = $pdata['tpage'];


    if($url) {
        $pdata['faa'] = 'href="?' . str_replace('*', $pdata['findex'], $url) . '"';
        $pdata['paa'] = 'href="?' . str_replace('*', $pdata['pindex'], $url) . '"';
        $pdata['naa'] = 'href="?' . str_replace('*', $pdata['nindex'], $url) . '"';
        $pdata['laa'] = 'href="?' . str_replace('*', $pdata['lindex'], $url) . '"';
    } else {
        $_GET['page'] = $pdata['findex'];
        $pdata['faa'] = 'href="' . $_A['url']['now'] . '?' . http_build_query($_GET) . '"';
        $_GET['page'] = $pdata['pindex'];
        $pdata['paa'] = 'href="' . $_A['url']['now'] . '?' . http_build_query($_GET) . '"';
        $_GET['page'] = $pdata['nindex'];
        $pdata['naa'] = 'href="' . $_A['url']['now'] . '?' . http_build_query($_GET) . '"';
        $_GET['page'] = $pdata['lindex'];
        $pdata['laa'] = 'href="' . $_A['url']['now'] . '?' . http_build_query($_GET) . '"';
    }

    $html = '<div><ul class="pagination pagination-centered">';
    if($pdata['cindex'] > 1) {
        $html .= "<li><a {$pdata['faa']} class=\"pager-nav\">首页</a></li>";
        $html .= "<li><a {$pdata['paa']} class=\"pager-nav\">&laquo;上一页</a></li>";
    }
    if(!$context['before'] && $context['before'] != 0) {
        $context['before'] = 5;
    }
    if(!$context['after'] && $context['after'] != 0) {
        $context['after'] = 4;
    }

    if($context['after'] != 0 && $context['before'] != 0) {
        $range = array();
        $range['start'] = max(1, $pdata['cindex'] - $context['before']);
        $range['end'] = min($pdata['tpage'], $pdata['cindex'] + $context['after']);
        if ($range['end'] - $range['start'] < $context['before'] + $context['after']) {
            $range['end'] = min($pdata['tpage'], $range['start'] + $context['before'] + $context['after']);
            $range['start'] = max(1, $range['end'] - $context['before'] - $context['after']);
        }
        for ($i = $range['start']; $i <= $range['end']; $i++) {

            if($url) {
                $aa = 'href="?' . str_replace('*', $i, $url) . '"';
            } else {
                $_GET['page'] = $i;
                $aa = 'href="?' . http_build_query($_GET) . '"';
            }
            $html .= ($i == $pdata['cindex'] ? '<li class="active"><a href="javascript:;">' . $i . '</a></li>' : "<li><a {$aa}>" . $i . '</a></li>');
        }
    }

    if($pdata['cindex'] < $pdata['tpage']) {
        $html .= "<li><a {$pdata['naa']} class=\"pager-nav\">下一页&raquo;</a></li>";
        $html .= "<li><a {$pdata['laa']} class=\"pager-nav\">尾页</a></li>";
    }
    $html .= '</ul></div>';
    return $html;
}