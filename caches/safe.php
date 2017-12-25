<?php
if (!function_exists('_Sys_Safe_CustomError'))
{
	function _Sys_Safe_CustomError($errno, $errstr, $errfile, $errline)
	{
		echo "<b>Error number:</b> [" . $errno . "_" . $errstr . "],error on line " . $errline . " in " . $errfile . "<br />";
		die();
	}
	set_error_handler("_Sys_Safe_CustomError", E_ERROR);
}


class _Sys_Safe_Stop {

	private $getfilter = "\\<.+javascript:window\\[.{1}\\\\x|<.*=(&#\\d+?;?)+?>|<.*(data|src)=data:text\\/html.*>|\\b(alert\\(|confirm\\(|expression\\(|prompt\\(|benchmark\s*?\(.*\)|sleep\s*?\(.*\)|\\b(group_)?concat[\\s\\/\\*]*?\\([^\\)]+?\\)|\bcase[\s\/\*]*?when[\s\/\*]*?\([^\)]+?\)|load_file\s*?\\()|<[a-z]+?\\b[^>]*?\\bon([a-z]{4,})\s*?=|^\\+\\/v(8|9)|\\b(and|or)\\b\\s*?([\\(\\)'\"\\d]+?=[\\(\\)'\"\\d]+?|[\\(\\)'\"a-zA-Z]+?=[\\(\\)'\"a-zA-Z]+?|>|<|\s+?[\\w]+?\\s+?\\bin\\b\\s*?\(|\\blike\\b\\s+?[\"'])|\\/\\*.*\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT\s*(\(.+\)\s*|@{1,2}.+?\s*|\s+?.+?|(`|'|\").*?(`|'|\")\s*)|UPDATE\s*(\(.+\)\s*|@{1,2}.+?\s*|\s+?.+?|(`|'|\").*?(`|'|\")\s*)SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE)@{0,2}(\\(.+\\)|\\s+?.+?\\s+?|(`|'|\").*?(`|'|\"))FROM(\\(.+\\)|\\s+?.+?|(`|'|\").*?(`|'|\"))|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)";

	private $postfilter = "<.*=(&#\\d+?;?)+?>|<.*data=data:text\\/html.*>|\\b(alert\\(|confirm\\(|expression\\(|prompt\\(|benchmark\s*?\(.*\)|sleep\s*?\(.*\)|\\b(group_)?concat[\\s\\/\\*]*?\\([^\\)]+?\\)|\bcase[\s\/\*]*?when[\s\/\*]*?\([^\)]+?\)|load_file\s*?\\()|<[^>]*?\\b(onerror|onmousemove|onload|onclick|onmouseover)\\b|\\b(and|or)\\b\\s*?([\\(\\)'\"\\d]+?=[\\(\\)'\"\\d]+?|[\\(\\)'\"a-zA-Z]+?=[\\(\\)'\"a-zA-Z]+?|>|<|\s+?[\\w]+?\\s+?\\bin\\b\\s*?\(|\\blike\\b\\s+?[\"'])|\\/\\*.*\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT\s*(\(.+\)\s*|@{1,2}.+?\s*|\s+?.+?|(`|'|\").*?(`|'|\")\s*)|UPDATE\s*(\(.+\)\s*|@{1,2}.+?\s*|\s+?.+?|(`|'|\").*?(`|'|\")\s*)SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE)(\\(.+\\)|\\s+?.+?\\s+?|(`|'|\").*?(`|'|\"))FROM(\\(.+\\)|\\s+?.+?|(`|'|\").*?(`|'|\"))|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)";

	private $cookiefilter = "benchmark\s*?\(.*\)|sleep\s*?\(.*\)|load_file\s*?\\(|\\b(and|or)\\b\\s*?([\\(\\)'\"\\d]+?=[\\(\\)'\"\\d]+?|[\\(\\)'\"a-zA-Z]+?=[\\(\\)'\"a-zA-Z]+?|>|<|\s+?[\\w]+?\\s+?\\bin\\b\\s*?\(|\\blike\\b\\s+?[\"'])|\\/\\*.*\\*\\/|<\\s*script\\b|\\bEXEC\\b|UNION.+?SELECT\s*(\(.+\)\s*|@{1,2}.+?\s*|\s+?.+?|(`|'|\").*?(`|'|\")\s*)|UPDATE\s*(\(.+\)\s*|@{1,2}.+?\s*|\s+?.+?|(`|'|\").*?(`|'|\")\s*)SET|INSERT\\s+INTO.+?VALUES|(SELECT|DELETE)@{0,2}(\\(.+\\)|\\s+?.+?\\s+?|(`|'|\").*?(`|'|\"))FROM(\\(.+\\)|\\s+?.+?|(`|'|\").*?(`|'|\"))|(CREATE|ALTER|DROP|TRUNCATE)\\s+(TABLE|DATABASE)";

	public function __construct()
	{
		static $safeinitrun;
		//
		if ($safeinitrun !== true) {
			$safeinitrun = true;
			include('cache.sqlset.php');
			if (defined('OFF_SQL_TEMP') && !OFF_SQL_TEMP) {
				$this->init();
			}
		}
	}

	static function stripslashes_(&$array = array())
	{
		if (!empty($array)) {
			$array = _Sys_Safe_Stop::stripslashes_deep($array);
		}
	}

	static function stripslashes_deep($value)
	{
		if (empty($value)) {
			return $value;
		} else {
			if (!get_magic_quotes_gpc()) {
				$value=is_array($value) ? array_map("_Sys_Safe_Stop::stripslashes_deep", $value) : stripslashes($value);
			}
			return $value;
		}
	}

	static function addslashes_(&$array = array())
	{
		if (!empty($array)) {
			$array = _Sys_Safe_Stop::addslashes_deep($array);
		}
	}

	static function addslashes_deep($value)
	{
		if (empty($value)) {
			return $value;
		} else {
			if (!get_magic_quotes_gpc()) {
				$value=is_array($value) ? array_map("_Sys_Safe_Stop::addslashes_deep", $value) : _Sys_Safe_Stop::mystrip_tags(addslashes($value));
			} else {
				$value=is_array($value) ? array_map("_Sys_Safe_Stop::addslashes_deep", $value) : _Sys_Safe_Stop::mystrip_tags($value);
			}
			return $value;
		}
	}

	static function remove_xss($string) {
		$string = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S', '', $string);

		$parm1 = Array('javascript', 'union','vbscript', 'expression', 'applet', 'xml', 'blink', 'link', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'base');

		$parm2 = Array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload','href','action','location','background','src','poster');

		$parm3 = Array('alert','sleep','load_file','confirm','prompt','benchmark','select','update','insert','delete','alter','drop','truncate','script','eval','outfile','dumpfile');

		$parm = array_merge($parm1, $parm2, $parm3);

		for ($i = 0; $i < sizeof($parm); $i++) {
			$pattern = '/';
			for ($j = 0; $j < strlen($parm[$i]); $j++) {
				if ($j > 0) {
					$pattern .= '(';
					$pattern .= '(&#[x|X]0([9][a][b]);?)?';
					$pattern .= '|(&#0([9][10][13]);?)?';
					$pattern .= ')?';
				}
				$pattern .= $parm[$i][$j];
			}
			$pattern .= '/i';
			$string = preg_replace($pattern, '****', $string);
		}
		return $string;
	}

	static function mystrip_tags($string)
	{
		$string =  _Sys_Safe_Stop::new_html_special_chars($string);
		$string =  _Sys_Safe_Stop::remove_xss($string);
		return $string;
	}

	static function new_html_special_chars($string)
	{
		$string = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array('&', '"', '<', '>'), $string);
		return $string;
	}

	static function scanpape()
	{
		global $__proself;
		$base_url = isset($_SERVER['SERVER_PORT'])&&$_SERVER['SERVER_PORT']=='443'?'https://':'http://'.$_SERVER['HTTP_HOST'];
		$base_dir = (isset($__proself)&&$__proself)?$__proself:'/';
		$base_ipage = (defined('DIY_BASE_IPAGE'))?DIY_BASE_IPAGE:'index.php';
		$ipageurl = $base_url.$base_dir;
		$ipageurl.= $base_ipage?ltrim($base_ipage,'/').'/':$base_ipage;

		$webscanpape = '
			<!DOCTYPE html>
			<html>
			<head>
			<title>网站安全</title>
			<meta charset="utf-8" />
			<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
			<style>
			body,dd,dl,dt,h1,h2,p{margin:0;padding:0;font:15px/1.5 微软雅黑,tahoma,arial}
			body{background:#efefef}
			h1,h2,h3,h4,h5,h6{font-size:100%;cursor:default}
			ol,ul{list-style:none outside none}
			a{text-decoration:none !important;;color:#447BC4 !important;}
			a:hover{text-decoration:underline !important;}
			.safe_stop_warn{position:fixed;width:100%;height:100%;top:0;left:0;background:#efefef;z-index:99999;}
			.safe_stop_warn .ip-attack{max-width:560px;margin:200px auto 0}
			.safe_stop_warn .ip-attack dl{background:#fff;border-radius:10px;border:1px solid #CDCDCD;-webkit-box-shadow:0 0 8px #CDCDCD;-moz-box-shadow:0 0 8px #cdcdcd;box-shadow:0 0 8px #CDCDCD;margin:30px;padding:30px}
			.safe_stop_warn .ip-attack dt{text-align:center}
			.safe_stop_warn .ip-attack dt.tit{font-size:18px;margin:20px 0}
			.safe_stop_warn .ip-attack dt a.sys{padding-left:80px}
			.safe_stop_warn .ip-attack dd{font-size:16px;color:#333;text-align:center}
			</style>
			</head>
				<body>
					<div class="safe_stop_warn">
						<div class="ip-attack">
							<dl>
							 <dt class="tit">输入内容存在危险字符，安全起见，已被本站拦截</dt>
							 <dt>
								<a href="javascript:history.go(-1)">返回上一页</a>
								<a href="'.$ipageurl.'web/system/settings/?index=safe" target="_blank" class="sys">我是管理员</a>
							 </dt>
							</dl>
						</div>
					</div>
			    </body>
			</html>
		';
		return $webscanpape;
	}

	public function init()
	{
		foreach($_GET as $key=>$value){
			$this->StopAttack($key, $value, $this->getfilter, "GET");
		}
		foreach($_POST as $key=>$value){
			$this->StopAttack($key, $value, $this->postfilter, "POST");
		}
		foreach($_COOKIE as $key=>$value){
			$this->StopAttack($key, $value, $this->cookiefilter, "COOKIE");
		}
		$referer = empty($_SERVER['HTTP_REFERER']) ? array() : array($_SERVER['HTTP_REFERER']);
		foreach($referer as $key=>$value){
			$this->StopAttack($key, $value, $this->getfilter, "REFERRER");
		}
	}

	public function safe($StrValue)
	{
		if (defined('OFF_SQL_TEMP') && !OFF_SQL_TEMP)
		{
			if (is_array($StrValue)) {
				foreach($StrValue AS $key=>$val) {
					$this->safe($key);
					$this->safe($val);
				}
			}else{
				if (preg_match("/".$this->getfilter."/is", $StrValue) == 1){
					echo _Sys_Safe_Stop::scanpape(); exit();
				}
				if (preg_match("/".$this->postfilter."/is", $StrValue) == 1){
					echo _Sys_Safe_Stop::scanpape(); exit();
				}
			}
		}
	}

	private function StopAttack($StrFiltKey,$StrFiltValue,$ArrFiltReq,$method)
	{
		$StrFiltValue = $this->arr_foreach($StrFiltValue);
		if (preg_match("/".$ArrFiltReq."/is",$StrFiltValue) == 1){
			$this->webscan_slog($method, $StrFiltValue);
			echo _Sys_Safe_Stop::scanpape(); exit();
		}
		if (preg_match("/".$ArrFiltReq."/is",$StrFiltKey) == 1){
			$this->webscan_slog($method, $StrFiltKey);
			echo _Sys_Safe_Stop::scanpape(); exit();
		}
	}

	private function webscan_slog($rdata, $method)
	{
		$log_file = dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'caches/log/safe_'.date('Ymd').'.php';
		fputs(fopen($log_file,'a+'),"<?PHP exit();?>".$_SERVER["REMOTE_ADDR"]."||".date('Y-m-d H:i:s')."<br>".$this->webscan_url()."<br>".$rdata."<br>".$method."<br>===========<br>\r\n");
	}

	private function webscan_url() {
		$sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
		$php_self = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
		$path_info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
		$relate_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $php_self.(isset($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'] : $path_info);
		return $sys_protocal.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '').$relate_url;
	}

	private function arr_foreach($arr)
	{
		static $str;
		static $keystr;
		if (!is_array($arr)) {
			return $arr;
		}
		foreach ($arr as $key => $val ) {
			$keystr=$keystr.$key;
			if (is_array($val)) {
				$this->arr_foreach($val);
			} else {
				$str[] = $val.$keystr;
			}
		}
		return implode($str);
	}
}
new _Sys_Safe_Stop();
?>