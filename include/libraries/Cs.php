<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once ('smarty/Smarty.class.php');

class Cs extends Smarty {
	public $_Ci_data = array();

	public function __construct()
	{
		parent::__construct();
		//路径配置
		$this->template_dir = $this->_Ci_data['template'] = APPPATH.'views'.DIRECTORY_SEPARATOR.BASE_TEMP.DIRECTORY_SEPARATOR;
		$this->compile_dir = $this->_Ci_data['compile'] = FCPATH.'caches/tpl_cache';
		$this->config_dir = $this->_Ci_data['config'] = FCPATH.'caches/tpl_config';
		$this->cache_dir = $this->_Ci_data['cache'] = FCPATH.'caches/tpl_cache';
		//边界符号
		$this->left_delimiter ="{#";
		$this->right_delimiter ="#}";
		//使用缓存
		$this->caching = false;
	}

	public function setting($a = '', $b = '')
	{
		$this->$a = $b;
	}

	public function data($a = '', $b = '')
	{
		$this->_Ci_data[$a] = $b;
	}

	/**
	 * @param string $file
	 * @param null $_param
	 * @param null $_smd5
	 */
	public function view($file, $_param = null, $_smd5 = null)
	{
		if (isset($_param['__browserpage']) && $_param['__browserpage']){
			$_tempfile = str_replace('.tpl', '.'.$_param['__browserpage'].'.tpl', $file);
			if (file_exists($this->_Ci_data['template'].$_tempfile)){
				$file = $_tempfile;
			}
		}
		if (file_exists($file) || file_exists($this->_Ci_data['template'].$file)){
			//模板路径
			if (!defined('DIY_BASE_NAME')) {
				$_temp_path = BASE_URI.'templates/'.BASE_TEMP.'/'.$file;
				$this->assign('NOW_PATH', substr($_temp_path, 0, strrpos($_temp_path, '/') + 1));
			}
			if (file_exists($this->_Ci_data['template'].$file)) {
				$_temp_path = BASE_URI.str_replace(realpath(FCPATH), '', $this->_Ci_data['template']);
				$_temp_path = str_replace(array("/\\", "\\"), array("/","/"), $_temp_path);
				$this->assign('NOW_PATH', $_temp_path);
			}
			//
			$this->_Ci_param($_param);
            header("Content-type: text/html; charset=".BASE_CHARSET);
			$_output = $this->fetch($file, $this->_Ci_smarty_display($_smd5));
			echo $this->_Ci_smarty_jscss_suffix($_output);
		}else{
			$this->showmsg(null, "文件不存在：".$file);
		}
	}

	/**
	 * @param $file
	 * @param null $_param
	 * @param null $_smd5
	 */
	public function show($file = '', $_param = null, $_smd5 = null)
	{
		global $_A;
		$segments = $this->_Ci_data['segments'];
		$segment1 = isset($segments[1])?$segments[1]:'';
		$segment2 = isset($segments[2])?$segments[2]:'';
		$segment3 = isset($segments[3])?$segments[3]:'index';
		if (is_array($file) && empty($_param)) {
			$_param = $file;
			$file = $segment3?$segment3:0;
		}
		$newtemp = BASE_PATH.'addons'.DIRECTORY_SEPARATOR.$segment2.DIRECTORY_SEPARATOR.'template'.DIRECTORY_SEPARATOR;
		$_temp_path = BASE_URI.'addons/'.$segment2.'/template/';
		if ($segment1 == 'app') {
            $newtemp.= 'mobile'.DIRECTORY_SEPARATOR;
			$_temp_path.= 'mobile/';
		}
		$_file = (empty($file))?$segment3:$file;
		//默认模板
		$newfile = $newtemp.$_file.'.tpl';
		//默认模板 - 微信
		$browser = isset($_A['browser'])?$_A['browser']:'';
		if ($browser == "weixin" && file_exists($newtemp.$_file.'.weixin.tpl')) {
			$newfile = $newtemp.$_file.'.weixin.tpl';
		}
		//用户自定义模板
		if (isset($_A['u']['id'])) {
			if ($_A['browser'] == "weixin" && file_exists($newtemp.$_A['u']['id'].DIRECTORY_SEPARATOR.$_file.'.weixin.tpl')) {
				$newfile = $newtemp.$_A['u']['id'].DIRECTORY_SEPARATOR.$_file.'.weixin.tpl';
			}elseif (file_exists($newtemp.$_A['u']['id'].DIRECTORY_SEPARATOR.$_file.'.tpl')) {
				$newfile = $newtemp.$_A['u']['id'].DIRECTORY_SEPARATOR.$_file.'.tpl';
			}
		}
		//
		if (file_exists($newfile)){
			$this->assign('NOW_PATH', $_temp_path);
            $this->template_dir = $this->_Ci_data['template'] = $newtemp;
            $this->view($newfile, $_param, $_smd5);
		}else{
            $this->view($file, $_param, $_smd5);
        }
	}

	/**
	 * @param $filearr
	 * @param null $_param
	 * @param null $_smd5
	 */
	public function showsel($filearr, $_param = null, $_smd5 = null)
	{
		$file = '';
		if (is_array($filearr)){
			foreach($filearr as $val){
				$file = $val;
				if (file_exists($this->_Ci_data['template'].$file)){
					break;
				}
			}
		}else{
			$file = $filearr;
		}
		if ($file) {
			$this->view($file, $_param, $_smd5);
		}
	}


	/**
	 * 页面提示
	 * @param string $title 提示标题
	 * @param string $body 提示内容
	 * @param array $links 连接组
	 * @param string $gotolinks 自动跳转链接
	 * @param string $gototime 自动跳转时间 默认3秒
	 */
	public function showmsg($title = '', $body = '', $links = array(), $gotolinks = '', $gototime = '3')
	{
		if (empty($title)) $title = '系统提醒';
		//关闭缓存
		$this->caching = false;
		//
		$_datalink = '';
		if (!empty($links)){
			if (is_array($links)) {
				$is_tithre = true;
				foreach($links as $_link) {
					if (is_array($_link)) {
						$is_tithre = false;
						if (!isset($_link['href']) && isset($_link['url'])) {
							$_link['href'] = $_link['url'];
						}
						if (!isset($_link['title']) && isset($_link['name'])) {
							$_link['title'] = $_link['name'];
						}
						if ($_link['href'] == '-1'){
							$_link['href'] = 'javascript:onclick=history.go(-1)';
						}
						$_datalink.= '<a href="'.$_link['href'].'">'.$_link['title'].'</a>';
						if ($_link['cut']){
							$_datalink.= $_link['cut'];
						}else{
							$_datalink.= '<br/>'.chr(13).chr(10);
						}
					}
				}
				if ($is_tithre && count($links) == 2) {
					if (!isset($links['href']) && isset($links['url'])) {
						$links['href'] = $links['url'];
					}
					if (!isset($links['title']) && isset($links['name'])) {
						$links['title'] = $links['name'];
					}
					if ($links['href'] && $links['title']) {
						if ($links['href'] == '-1'){
							$links['href'] = 'javascript:onclick=history.go(-1)';
						}
						$_datalink.= '<a href="'.$links['href'].'">'.$links['title'].'</a>';
						$_datalink.= '<br/>'.chr(13).chr(10);
					}
				}
			}else{
				$gotolinks = $links;
				$_datalink.= '<a href="'.$links.'">如果你的浏览器没有自动跳转，请点击此链接</a>';
			}
		}else{
			if (isset($_POST['go_url'])){
				$_POST['go_url'] = str_replace('&amp;', '&', urldecode($_POST['go_url']));
				$_POST['go_url'] = str_replace('&', '&amp;', $_POST['go_url']);
				$_datalink.= '<a href="'.$_POST['go_url'].'">返回来源地址</a>';
			}else{
				$_datalink.= '<a href="javascript:onclick=history.go(-1)">返回来源地址</a>';
			}
			$_datalink.= '<br/>'.chr(13).chr(10);
		}
		//
		$this->assign('showmsg',1);
		$this->assign('title',$title); //标题
		$this->assign('body',$body); //说明
		$this->assign('links',$links); //链接组
		$this->assign('gotourl',$gotolinks); //自动跳转地址
		$this->assign('gototime',$gototime); //自动跳转时间
		$this->assign('datalink', $_datalink);
		//
		$_output = $this->fetch('showmsg.tpl', $this->_Ci_smarty_display());
		echo $this->_Ci_smarty_jscss_suffix($_output);
		exit();

	}

	/**
	 * @param $_output
	 * @return mixed
	 */
	public function _Ci_smarty_jscss_suffix($_output) {
		if (defined('ES_RELEASE')) {
			//css
			preg_match_all('/<\s*link\s+[^>]*?href\s*=\s*(\'|\")?(.+\.css)\\1[^>]*?\/?\s*>/i', $_output, $matchcss);
			if ($matchcss[0]) {
				foreach($matchcss[0] AS $key=>$val) {
					$val_path = $matchcss[2][$key];
					$_output = str_replace($val, str_replace($val_path, $val_path."?_vwins_ver=".ES_RELEASE, $val), $_output);
				}
			}
			//js
			preg_match_all('/<\s*script\s+[^>]*?src\s*=\s*(\'|\")?(.+\.js)\\1[^>]*?\/?\s*>/i', $_output, $matchjs);
			if ($matchjs[0]) {
				foreach($matchjs[0] AS $key=>$val) {
					$val_path = $matchjs[2][$key];
					$_output = str_replace($val, str_replace($val_path, $val_path."?_vwins_ver=".ES_RELEASE, $val), $_output);
				}
			}
		}
		return $_output;
	}

	private function _Ci_smarty_display($_smd5 = null)
	{
		if (!empty($_smd5)){
			return $_smd5;
		}else{
			$_arr = array_merge($_GET,$_POST);
			if (empty($_arr)){
				return md5($this->_Ci_request_url());
			}else{
				ksort($_arr);
				return md5(@implode('', $_arr));
			}
		}
	}

	private function _Ci_request_url()
	{
		if (isset($_SERVER['REQUEST_URI'])){
			$_url = $_SERVER['REQUEST_URI'];
		}else{
			if (isset($_SERVER['argv'])){
				$_url = $_SERVER['PHP_SELF'] .'?'. $_SERVER['argv'][0];
			}else{
				$_url = $_SERVER['PHP_SELF'] .'?'.$_SERVER['QUERY_STRING'];
			}
		}
		return $_url;
	}

	private function _Ci_param($_param = null)
	{
		global $_A,$_GPC;
		if (defined('NOW_PATH')) $_A['NOW_PATH'] = NOW_PATH;
		unset($_param['_A']);
		unset($_param['_GPC']);
		$this->assign('_A', $_A);
		$this->assign('_GPC', $_GPC);
		//
		if (!empty($_param)){
			if (is_array($_param)){
				foreach ($_param as $_ck=>$_cv){
					$this->assign((is_string($_ck))?$_ck:"param_".$_ck, $_cv);
				}
			}else{
				$this->assign('param', $_param);
			}
		}
	}
}
