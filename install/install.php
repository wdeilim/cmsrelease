<?php
@session_start();
@set_time_limit(1000);
error_reporting(0);
header("Content-Type:text/html;charset=utf-8");
if(phpversion() < '5.3.0') set_magic_quotes_runtime(0);
if(phpversion() < '5.3.0') exit('您的php版本过低，不能安装本软件，请升级到5.3.0或更高版本再安装，谢谢！');
define('IN_KUAIFAN', true);
define('INSTALL', true);
$_GET['vs'] = 2;
require_once(dirname(dirname(__FILE__)).'/caches/config.php');
if(file_exists(BASE_PATH.'caches/install.lock')) exit('您已经安装过微窗,如果需要重新安装，请删除 ./caches/install.lock 文件！');

$step = trim($_REQUEST['step']) ? trim($_REQUEST['step']) : 1;
if(strrpos(strtolower(PHP_OS),"win") === FALSE) {
	define('ISUNIX', TRUE);
} else {
	define('ISUNIX', FALSE);
}
$steps = include BASE_PATH.'install/step.inc.php';

$mode = 0777;

switch($step)
{
	case '1': //安装许可协议
		$license = file_get_contents(BASE_PATH."install/license.txt");
		include BASE_PATH."install/step/step".$step.".tpl.php";
		break;

	case '2':  //环境检测 (FTP帐号设置）
		$PHP_GD  = '';
		if(extension_loaded('gd')) {
			if(function_exists('imagepng')) $PHP_GD .= 'png';
			if(function_exists('imagejpeg')) $PHP_GD .= ' jpg';
			if(function_exists('imagegif')) $PHP_GD .= ' gif';
		}
		$PHP_JSON = '0';
		if(extension_loaded('json')) {
			if(function_exists('json_decode') && function_exists('json_encode')) $PHP_JSON = '1';
		}
		//新加fsockopen 函数判断,此函数影响安装后会员注册及登录操作。
		if(function_exists('fsockopen')) {
			$PHP_FSOCKOPEN = '1';
		}
		$PHP_DNS = preg_match("/^[0-9.]{7,15}$/", @gethostbyname('www.baidu.com')) ? 1 : 0;
		//是否满足微窗安装需求
		$is_right = (phpversion() >= '5.2.0' && extension_loaded('mysql') && $PHP_JSON && $PHP_GD && $PHP_FSOCKOPEN) ? 1 : 0;
		include BASE_PATH."install/step/step".$step.".tpl.php";
		break;
	case '3': //检测目录属性
		make_dir('../caches/tpl_cache/_cache/');
		$chmod_file = 'chmod.txt';
		$selectmod = $needmod.$selectmod;
		$selectmods = explode(',',$selectmod);
		$files = file(BASE_PATH."install/".$chmod_file);
		foreach($files as $_k => $file) {
			$file = str_replace('*','',$file);
			$file = trim($file);
			if(is_dir(BASE_PATH.$file)) {
				$is_dir = '1';
				$cname = '目录';
				//继续检查子目录权限，新加函数
				$write_able = writable_check(BASE_PATH.$file);
			} else {
				$is_dir = '0';
				$cname = '文件';
			}
			//新的判断
			if($is_dir =='0' && is_writable(BASE_PATH.$file)) {
				$is_writable = 1;
			} elseif($is_dir =='1' && dir_writeable(BASE_PATH.$file)){
				$is_writable = $write_able;
				if($is_writable=='0'){
					$no_writablefile = 1;
				}
			}else{
				$is_writable = 0;
				$no_writablefile = 1;
			}

			$filesmod[$_k]['file'] = $file;
			$filesmod[$_k]['is_dir'] = $is_dir;
			$filesmod[$_k]['cname'] = $cname;
			$filesmod[$_k]['is_writable'] = $is_writable;
		}
		/*
		if(dir_writeable(BASE_PATH)) {
			$is_writable = 1;
		} else {
			$is_writable = 0;
		}
		$filesmod[$_k+1]['file'] = '网站根目录';
		$filesmod[$_k+1]['is_dir'] = '1';
		$filesmod[$_k+1]['cname'] = '目录';
		$filesmod[$_k+1]['is_writable'] = $is_writable;
		*/
		include BASE_PATH."install/step/step".$step.".tpl.php";
		break;
	case '4': //配置帐号 （MYSQL帐号、管理员帐号、首页文件）
		$url = get_url();
		$url = substr($url, 0, strrpos($url, '/install.php?'));
		$url = substr($url, 0, strrpos($url, '/') + 1);
		$html = ihttp_request($url.'index.php/web/system/login/');
		if (strpos($html['responseline'], "404") !== false) {
			$nomoren = 1;
		}
		$html = ihttp_request($url.'web/system/login/');
		if (strpos($html['responseline'], "404") !== false) {
			$noweijingtai = 1;
		}
		if (!isset($_SERVER['REQUEST_URI'], $_SERVER['SCRIPT_NAME'])) {
			$nomoren = 1;
			$noweijingtai = 1;
		}
		include BASE_PATH."install/step/step".$step.".tpl.php";
		break;
	case '5': //安装详细过程
		extract($_POST);
		include BASE_PATH."install/step/step".$step.".tpl.php";
		break;
	case '6'://完成安装
		file_put_contents(BASE_PATH.'caches/install.lock','');
		$installurl = "http://cloud.vwins.cn/gateway.php?method=pro_install&release=".ES_RELEASE."&url=".urlencode(get_url());
		include BASE_PATH."install/step/step".$step.".tpl.php";
		//删除安装目录
		__removeDir(BASE_PATH.'install/');
		break;

	case 'dbtest'://数据库测试
		extract($_GET);
		//引索文件部分
		if ($ipage == 'hide' || $ipage == 'pathinfo') {
			$_ipage = $ipage;
			$_ipage = str_replace('hide', '', $_ipage);
			$_ipage = str_replace('pathinfo', 'index.php?', $_ipage);
			$_protocol = (!isset($_SERVER['REQUEST_URI'], $_SERVER['SCRIPT_NAME']))?'QUERY_STRING':'REQUEST_URI';
			$db_config = "<?php
				define('DIY_BASE_NAME', '');
				define('DIY_BRAND_NAME', '');
				define('DIY_BRAND_URL', '');
				define('DIY_LINKQQ_PATH', '342210020');
				define('DIY_BOTTOM_INFO', '');
				define('DIY_BASE_IPAGE', '".$_ipage."');
				define('DIY_BASE_PROTOCOL', '".$_protocol."');
			?>";
			$cache_file_path =BASE_PATH. "caches/cache.config.php";
			write_static_cache_install($cache_file_path, $db_config, 1);
		}
		//数据库部分
		if(!@mysql_connect($dbhost, $dbuser, $dbpass)) {
			exit('2');
		}
		$server_info = mysql_get_server_info();
		if($server_info < '4.0') exit('6');
		if(!mysql_select_db($dbname)) {
			if(!@mysql_query("CREATE DATABASE `$dbname`")) exit('3');
			mysql_select_db($dbname);
		}
		$tables = array();
		$query = mysql_query("SHOW TABLES FROM `$dbname`");
		while($r = mysql_fetch_row($query)) {
			$tables[] = $r[0];
		}
		if($tables && in_array($pre.'users', $tables)) {
			exit('0');
		} else {
			exit('1');
		}
		break;
	case 'installmodule': //执行SQL
		extract($_POST);
		$GLOBALS['dbcharset'] = $dbcharset;
		$PHP_SELF = isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : (isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : $_SERVER['ORIG_PATH_INFO']);
		$rootpath = str_replace('\\','/',dirname($PHP_SELF));
		$rootpath = substr($rootpath,0,-7);
		$rootpath = strlen($rootpath)>1 ? $rootpath : "/";
		if($module == '1') {
			if ($pre != 'es_' && (strpos($pre, "es_") !== false)){
				echo "表前缀不能使用“{$pre}”。（禁止使用包含“es_”但又不是“es_”的字符，例如: 把“{$pre}”改成“".str_replace('es_', 'ab_', $pre)."”试试）";
				exit;
			}
			if(!preg_match("/^[a-zA-Z]{1}[a-zA-Z0-9_]{2,9}+$/",$pre)){
				echo "表前缀不能使用“{$pre}”。（必须是字母加数字或下划线组成的3到10位字符串）";
				exit;
			}
			$_PHP_SELF = strtolower($_SERVER['PHP_SELF']);
			$_PHP_SELF = substr($PHP_SELF, 0, strrpos($PHP_SELF,'install/install.php'));
			$db_config = '<?php
				$__dbhost   = "'.$dbhost.'";

				$__dbname   = "'.$dbname.'";

				$__dbuser   = "'.$dbuser.'";

				$__dbpass   = "'.$dbpass.'";

				$__dbcharset = "'.str_replace("-","",$dbcharset).'";

				$__dbpre    = "'.$pre.'";

				$__proself    = "'.$_PHP_SELF.'";

			?>';
			$cache_file_path =BASE_PATH. "caches/config_db.php";
			write_static_cache_install($cache_file_path, $db_config, 1);

			$lnk = mysql_connect($dbhost, $dbuser, $dbpass) or die ('Not connected : ' . mysql_error());
			$version = mysql_get_server_info();

			if($version > '4.1' && $dbcharset) {
				mysql_query("SET NAMES '".str_replace("-","",$dbcharset)."'");
			}

			if($version > '5.0') {
				mysql_query("SET sql_mode=''");
			}

			if(!@mysql_select_db($dbname)){
				@mysql_query("CREATE DATABASE $dbname");
				if(@mysql_error()) {
					echo "a";exit;
				} else {
					mysql_select_db($dbname);
				}
			}
			$_SESSION['insqlnum'] = 0;
			$_SESSION['insqlarr'] = array();
			$dbfile =  'vwins_db.sql';
			if(file_exists(BASE_PATH."install/main/".$dbfile)) {
				$sql = file_get_contents(BASE_PATH."install/main/".$dbfile);
				_sql_execute($sql);
				//创建网站创始人
				if($dbcharset=='gbk') $username = iconv('UTF-8','GBK',$username);
				$encrypt = random(6, '1294567890abcdefghigklmnopqrstuvwxyzABCDEFGHIGKLMNOPQRSTUVWXYZ');
				$password_md5 = md52($password, $encrypt);
				$email = trim($email);
				_sql_execute("INSERT INTO `".$pre."users` (`admin`,`username`,`encrypt`,`userpass`,`fullname`,`email`,`companyname`) VALUES('1','{$username}','{$encrypt}','{$password_md5}','{$username}','{$email}','{$username}')");
			} else {
				echo 'b';//数据库文件不存在
				exit;
			}
		} elseif ($module == 'sqlnum') {
			$insqlval = "";
			if (count($_SESSION['insqlarr']) > 0){
				foreach ($_SESSION['insqlarr'] as $_val) {
					$_val = str_replace("'","",$_val);
					if ($_val) $insqlval.= "{$_val};\\r\\n";
				}
			}
			if (!empty($insqlval)) {
				echo "程序安装失败，执行SQL失败：<a href=\"#sqlerra\" onClick=\"document.getElementById('sqlerrdiv').style.display='block';document.getElementById('sqlerrtext').innerHTML='{$insqlval}';\" style=\"color:#FF0000;text-decoration:underline;\">".count($_SESSION['insqlarr'])."行(点击查看详情)</a>";
			}else{
				echo "执行SQL：".$_SESSION['insqlnum']."行";
			}
			exit;
		}
		//........
		echo $module;
		break;
	case 'dbtest'://数据库测试
		extract($_GET);
		if(!@mysql_connect($dbhost, $dbuser, $dbpass)) {
			exit('2');
		}
		$server_info = mysql_get_server_info();
		if($server_info < '4.0') exit('6');
		if(!mysql_select_db($dbname)) {
			if(!@mysql_query("CREATE DATABASE `$dbname`")) exit('3');
			mysql_select_db($dbname);
		}
		$tables = array();
		$query = mysql_query("SHOW TABLES FROM `$dbname`");
		while($r = mysql_fetch_row($query)) {
			$tables[] = $r[0];
		}
		if($tables && in_array($pre.'users', $tables)) {
			exit('0');
		}
		else {
			exit('1');
		}
		break;
}


//写入文本
function write_static_cache_install($cache_file_path, $config_arr ,$arr = '')
{
	if ($arr){
		$content = $config_arr;
	}else{
		$content = "<?php\r\n";
		$content .= "\$data = " . var_export($config_arr, true) . ";\r\n";
		$content .= "?>";
	}
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


function format_textarea($string) {
	return nl2br(str_replace(' ', '&nbsp;', htmlspecialchars($string)));
}

function dir_writeable($dir) {
	$writeable = 0;
	if(is_dir($dir)) {
		if($fp = @fopen("$dir/chkdir.test", 'w')) {
			@fclose($fp);
			@unlink("$dir/chkdir.test");
			$writeable = 1;
		} else {
			$writeable = 0;
		}
	}
	return $writeable;
}

function writable_check($path){
	$dir = '';
	$is_writable = '1';
	if(!is_dir($path)){return '0';}
	$dir = opendir($path);
	while (($file = readdir($dir)) !== false){
		if($file!='.' && $file!='..'){
			if(is_file($path.'/'.$file)){
				//是文件判断是否可写，不可写直接返回0，不向下继续
				if(!is_writable($path.'/'.$file)){
					return '0';
				}
			}else{
				//目录，循环此函数,先判断此目录是否可写，不可写直接返回0 ，可写再判断子目录是否可写 
				$dir_wrt = dir_writeable($path.'/'.$file);
				if($dir_wrt=='0'){
					return '0';
				}
				$is_writable = writable_check($path.'/'.$file);
			}
		}
	}
	return $is_writable;
}

function _sql_execute($sql,$r_tablepre = '',$s_tablepre = 'es_') {
	$sqls = _sql_split($sql,$r_tablepre,$s_tablepre);
	if(is_array($sqls))
	{
		foreach($sqls as $sql)
		{
			if(trim($sql) != '')
			{
				$con = mysql_query($sql);
				$_SESSION['insqlnum']++;
				if (!$con) $_SESSION['insqlarr'][] = $sql;
			}
		}
	}
	else
	{
		$con = mysql_query($sqls);
		$_SESSION['insqlnum']++;
		if (!$con) $_SESSION['insqlarr'][] = $sqls;
	}
	return true;
}
function _sql_split($sql,$r_tablepre = '',$s_tablepre='es_') {
	global $dbcharset,$pre;
	$r_tablepre = $r_tablepre ? $r_tablepre : $pre;
	if(mysql_get_server_info > '4.1' && $dbcharset)
	{
		$sql = preg_replace("/TYPE=(InnoDB|MyISAM|MEMORY)( DEFAULT CHARSET=[^; ]+)?/", "ENGINE=\\1 DEFAULT CHARSET=".str_replace("-","",$dbcharset),$sql);
	}
	$sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
	$sys_protocal.= (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '');
	$sql = str_replace("{:domain}", $sys_protocal, $sql);
	if($r_tablepre != $s_tablepre) {
		$sql = str_replace(' ' . $s_tablepre, ' ' . $r_tablepre, $sql);
		$sql = str_replace(' `' . $s_tablepre, ' `' . $r_tablepre, $sql);
	}
	$sql = str_replace("\r", "\n", $sql);
	$ret = array();
	$num = 0;
	$queriesarray = explode(";\n", trim($sql));
	unset($sql);
	foreach($queriesarray as $query)
	{
		$ret[$num] = '';
		$queries = explode("\n", trim($query));
		$queries = array_filter($queries);
		foreach($queries as $query)
		{
			$str1 = substr($query, 0, 1);
			if($str1 != '#' && $str1 != '-') $ret[$num] .= $query;
		}
		$num++;
	}
	return $ret;
}

function random($length, $chars = '0123456789') {
	$hash = '';
	$max = strlen($chars) - 1;
	for($i = 0; $i < $length; $i++) {
		$hash .= $chars[mt_rand(0, $max)];
	}
	return $hash;
}
//删除文件
function __fujian_del($str_file_path) {
	if (!unlink($str_file_path)){
		return false;
	}else{
		return true;
	}
}
//删除文件与目录
function __removeDir($dirName){
	if(!is_dir($dirName)){ //如果传入的参数不是目录，则为文件，应将其删除
		@unlink($dirName);//删除文件
		return false;
	}
	$handle = @opendir($dirName); //如果传入的参数是目录，则使用opendir将该目录打开，将返回的句柄赋值给$handle
	while(($file = @readdir($handle)) !== false) //这里明确地测试返回值是否全等于（值和类型都相同）FALSE，否则任何目录项的名称求值为 FALSE 的都会导致循环停止（例如一个目录名为“0”）。
	{
		if($file!='.'&&$file!='..') //在文件结构中，都会包含形如“.”和“..”的向上结构，但是它们不是文件或者文件夹
		{
			$dir = $dirName . '/' . $file; //当前文件$dir为文件目录+文件
			is_dir($dir)?__removeDir($dir):@unlink($dir); //判断$dir是否为目录，如果是目录则递归调用__removeDir($dirName)函数，将其中的文件和目录都删除；如果不是目录，则删除该文件
		}
	}
	closedir($handle);
	return rmdir($dirName) ;
}

function make_dir($path){
	if(!file_exists($path)){
		make_dir(dirname($path));
		@mkdir($path,0777);
		@chmod($path,0777);
	}
}

function md52($text, $pass = ''){
	$_text = md5($text) . $pass;
	return md5($_text);
}

function get_url() {
	$sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
	$php_self = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
	$path_info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
	$relate_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $php_self.(isset($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'] : $path_info);
	return $sys_protocal.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '').$relate_url;
}

function ihttp_request($url, $post = '', $extra = array(), $timeout = 60) {
	$urlset = parse_url($url);
	if(empty($urlset['path'])) {
		$urlset['path'] = '/';
	}
	if(!empty($urlset['query'])) {
		$urlset['query'] = "?{$urlset['query']}";
	}
	if(empty($urlset['port'])) {
		$urlset['port'] = $urlset['scheme'] == 'https' ? '443' : '80';
	}
	if (strpos($url, 'https://')!==false && !extension_loaded('openssl')) {
		if (!extension_loaded("openssl")) {
			return '请开启您PHP环境的openssl';
			//die('请开启您PHP环境的openssl');
		}
	}
	if(function_exists('curl_init') && function_exists('curl_exec')) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $urlset['scheme']. '://' .$urlset['host'].($urlset['port'] == '80' ? '' : ':'.$urlset['port']).$urlset['path'].$urlset['query']);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		if($post) {
			if (is_array($post)) {
				$filepost = false;
				foreach ($post as $name => $value) {
					if (substr($value, 0, 1) == '@') {
						$filepost = true;
						break;
					}
				}
				if (!$filepost) {
					$post = http_build_query($post);
				}
			}
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		}
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSLVERSION, 1);
		if (defined('CURL_SSLVERSION_TLSv1')) {
			curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
		}
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:9.0.1) Gecko/20100101 Firefox/9.0.1');
		if (!empty($extra) && is_array($extra)) {
			$headers = array();
			foreach ($extra as $opt => $value) {
				if (strpos($opt, 'CURLOPT_')!==false) {
					curl_setopt($ch, constant($opt), $value);
				} elseif (is_numeric($opt)) {
					curl_setopt($ch, $opt, $value);
				} else {
					$headers[] = "{$opt}: {$value}";
				}
			}
			if(!empty($headers)) {
				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			}
		}
		$data = curl_exec($ch);
		$status = curl_getinfo($ch);
		$errno = curl_errno($ch);
		$error = curl_error($ch);
		curl_close($ch);
		if($errno || empty($data)) {
			return $error;
		} else {
			return ihttp_response_parse($data);
		}
	}
	$method = empty($post) ? 'GET' : 'POST';
	$fdata = "{$method} {$urlset['path']}{$urlset['query']} HTTP/1.1\r\n";
	$fdata .= "Host: {$urlset['host']}\r\n";
	if(function_exists('gzdecode')) {
		$fdata .= "Accept-Encoding: gzip, deflate\r\n";
	}
	$fdata .= "Connection: close\r\n";
	if (!empty($extra) && is_array($extra)) {
		foreach ($extra as $opt => $value) {
			if (strpos($opt, 'CURLOPT_') === false) {
				$fdata .= "{$opt}: {$value}\r\n";
			}
		}
	}
	$body = '';
	if ($post) {
		if (is_array($post)) {
			$body = http_build_query($post);
		} else {
			$body = urlencode($post);
		}
		$fdata .= 'Content-Length: ' . strlen($body) . "\r\n\r\n{$body}";
	} else {
		$fdata .= "\r\n";
	}
	if($urlset['scheme'] == 'https') {
		$fp = fsockopen('ssl://' . $urlset['host'], $urlset['port'], $errno, $error);
	} else {
		$fp = fsockopen($urlset['host'], $urlset['port'], $errno, $error);
	}
	stream_set_blocking($fp, true);
	stream_set_timeout($fp, $timeout);
	if (!$fp) {
		return error(1, $error);
	} else {
		fwrite($fp, $fdata);
		$content = '';
		while (!feof($fp))
			$content .= fgets($fp, 512);
		fclose($fp);
		return ihttp_response_parse($content, true);
	}
}


function ihttp_response_parse($data, $chunked = false) {
	$rlt = array();
	$pos = strpos($data, "\r\n\r\n");
	$split1[0] = substr($data, 0, $pos);
	$split1[1] = substr($data, $pos + 4, strlen($data));

	$split2 = explode("\r\n", $split1[0], 2);
	preg_match('/^(\S+) (\S+) (\S+)$/', $split2[0], $matches);
	$rlt['code'] = $matches[2];
	$rlt['status'] = $matches[3];
	$rlt['responseline'] = $split2[0];
	$header = explode("\r\n", $split2[1]);
	$isgzip = false;
	$ischunk = false;
	foreach ($header as $v) {
		$row = explode(':', $v);
		$key = trim($row[0]);
		$value = trim($row[1]);
		if (is_array($rlt['headers'][$key])) {
			$rlt['headers'][$key][] = $value;
		} elseif (!empty($rlt['headers'][$key])) {
			$temp = $rlt['headers'][$key];
			unset($rlt['headers'][$key]);
			$rlt['headers'][$key][] = $temp;
			$rlt['headers'][$key][] = $value;
		} else {
			$rlt['headers'][$key] = $value;
		}
		if(!$isgzip && strtolower($key) == 'content-encoding' && strtolower($value) == 'gzip') {
			$isgzip = true;
		}
		if(!$ischunk && strtolower($key) == 'transfer-encoding' && strtolower($value) == 'chunked') {
			$ischunk = true;
		}
	}
	if($chunked && $ischunk) {
		$rlt['content'] = ihttp_response_parse_unchunk($split1[1]);
	} else {
		$rlt['content'] = $split1[1];
	}
	if($isgzip && function_exists('gzdecode')) {
		$rlt['content'] = gzdecode($rlt['content']);
	}

	$rlt['meta'] = $data;
	if($rlt['code'] == '100') {
		return ihttp_response_parse($rlt['content']);
	}
	return $rlt;
}

function ihttp_response_parse_unchunk($str = null) {
	if(!is_string($str) or strlen($str) < 1) {
		return false;
	}
	$eol = "\r\n";
	$add = strlen($eol);
	$tmp = $str;
	$str = '';
	do {
		$tmp = ltrim($tmp);
		$pos = strpos($tmp, $eol);
		if($pos === false) {
			return false;
		}
		$len = hexdec(substr($tmp, 0, $pos));
		if(!is_numeric($len) or $len < 0) {
			return false;
		}
		$str .= substr($tmp, ($pos + $add), $len);
		$tmp  = substr($tmp, ($len + $pos + $add));
		$check = trim($tmp);
	} while(!empty($check));
	unset($tmp);
	return $str;
}
?>