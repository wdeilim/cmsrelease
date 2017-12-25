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
		include('cache.sqlset.php');
		if (defined('OFF_SQL_TEMP') && !OFF_SQL_TEMP)
		{
			$this->init();
		}
	}

	private function scanpape()
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
			body, h1, h2, p,dl,dd,dt{margin: 0;padding: 0;font: 15px/1.5 微软雅黑,tahoma,arial;}
			body{background:#efefef;}
			h1, h2, h3, h4, h5, h6 {font-size: 100%;cursor:default;}
			ul, ol {list-style: none outside none;}
			a {text-decoration: none;color:#447BC4}
			a:hover {text-decoration: underline;}
			.ip-attack{max-width:560px; margin:200px auto 0;}
			.ip-attack dl{ background:#fff; border-radius:10px;border: 1px solid #CDCDCD;-webkit-box-shadow: 0 0 8px #CDCDCD;-moz-box-shadow: 0 0 8px #cdcdcd;box-shadow: 0 0 8px #CDCDCD; margin: 30px; padding: 30px;}
			.ip-attack dt{text-align:center;}
			.ip-attack dt.tit{font-size:18px;margin:20px 0}
			.ip-attack dt a.sys{padding-left:80px;}
			.ip-attack dd{font-size:16px; color:#333; text-align:center;}
			.tips{text-align:center; font-size:14px; line-height:50px; color:#999;}
			</style>
			</head>
			<body>
			<div class="ip-attack">
				<dl>
				 <dt class="tit">输入内容存在危险字符，安全起见，已被本站拦截</dt>
				 <dt>
					<a href="javascript:history.go(-1)">返回上一页</a>
					<a href="'.$ipageurl.'web/system/settings/?index=safe" target="_blank" class="sys">我是管理员</a>
				 </dt>
				</dl>
				</div>
			   </body>
			</html>
		';
		return $webscanpape;
	}

	private function StopAttack($StrFiltKey,$StrFiltValue,$ArrFiltReq)
	{
		$StrFiltValue = $this->arr_foreach($StrFiltValue);
		if (preg_match("/".$ArrFiltReq."/is",$StrFiltValue) == 1){
			echo $this->scanpape(); exit();
		}
		if (preg_match("/".$ArrFiltReq."/is",$StrFiltKey) == 1){
			echo $this->scanpape(); exit();
		}
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

	public function init()
	{
		foreach($_GET as $key=>$value){
			$this->StopAttack($key, $value, $this->getfilter);
		}
		foreach($_POST as $key=>$value){
			$this->StopAttack($key, $value, $this->postfilter);
		}
		foreach($_COOKIE as $key=>$value){
			$this->StopAttack($key, $value, $this->cookiefilter);
		}
		$referer = empty($_SERVER['HTTP_REFERER']) ? array() : array($_SERVER['HTTP_REFERER']);
		foreach($referer as $key=>$value){
			$this->StopAttack($key, $value, $this->getfilter);
		}
	}
}
new _Sys_Safe_Stop();
?>