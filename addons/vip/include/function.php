<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 过期计算
 * @param $startdate 开始时间
 * @param $enddate 结束时间
 * @return string
 */
if (!function_exists('vip_content_status'))
{
	function vip_content_status($startdate, $enddate){
		if ($enddate < time()){
			//过期
			return "<font color='red'>已过期</font>";
		}elseif ($startdate > time()){
			//未开始
			return "未开始";
		}else{
			$_val = $enddate - time();
			if ($_val < 86400*30){
				//N天后过期
				$_d = intval($_val / 86400);
				if ($_d == '0') {
					return "<font color='#cd0000'>明天过期</font>";
				}else{
					return "<font color='#cd0000'>".$_d."天后过期</font>";
				}
			}else{
				return "正常";
			}
		}
	}
}


/**
 * 生成SN码
 * @param $userid
 * @param $contentid
 * @return int|string
 */
if (!function_exists('getSN'))
{
	function getSN($userid, $contentid){
		$text = 6;
		$text.= substr(date("Ym"),2);
		$text.= rand(0,9);
		$text.= strlen($contentid)<3?substr("000".$contentid,-3):$contentid;
		$text.= strlen($userid)<3?substr("000".$userid,-3):$userid;
		return $text;
	}
}

/**
 * 格式化数据
 * @param $str 要格式化的字符串
 * @param $type 类型  0卡号、1手机号、2金币
 */
if (!function_exists('format_user'))
{
	function format_user($str, $type = 0){
		if (preg_match("/([\x81-\xfe][\x40-\xfe])/", $str, $match)) {
			return $str;
		}
		$str = trim($str);
		$text = "";
		if  ($type == 0){
			for ($i=0; $i<ceil(strlen($str)/4); $i++){
				$text.= substr($str, $i*4, 4) ." ";
			}
		}
		if  ($type == 1 && strlen($str) == 11){
			$text = substr($str, 0, 3)." ".substr($str, 3, 4)." ".substr($str, 7);
		}
		if  ($type == 2){
			$text = __doFormatMoney($str);
		}
		return trim($text);
	}
}

if (!function_exists('__doFormatMoney'))
{
	function __doFormatMoney($money){
		$money1 = "";
		if (strpos($money,".") !== false){
			$money1 = substr($money, strpos($money,"."));
			$money = substr($money, 0, strpos($money,"."));
		}
		$tmp_money = strrev($money);
		$format_money = "";
		for($i = 3;$i<strlen($money);$i+=3){
			$format_money .= substr($tmp_money,0,3).",";
			$tmp_money = substr($tmp_money,3);
		}
		$format_money .=$tmp_money;
		$format_money = strrev($format_money);
		return $format_money.$money1;
	}
}


/**
 * 通过积分获取会员等级
 * @param $integral
 * @return string
 */
if (!function_exists('get_user_rank'))
{
	function get_user_rank($integral){
		global $_A;
		static $rank_classes = array();
		if (!isset($rank_classes["jfdj"])) {
			$ufrow = get_instance()->ddb->getone("SELECT userdata FROM ".table("users_functions"), array('id'=>$_A['uf']['id']));
			$userdata = string2array($ufrow['userdata']);
			$arr = $rank_classes['jfdj'] = $userdata['jfdj'];
		}else{
			$arr = $rank_classes['jfdj'];
		}
		$text = "等级积分:".$integral;
		if (is_array($arr)){
			foreach ($arr as $item){
				$_temp = "";
				if ($item['lev_a'] <= 0 && $integral <= $item['lev_b']){
					$_temp = $item['name'];
				}elseif ($item['lev_b'] <= 0 && $integral >= $item['lev_a']){
					$_temp = $item['name'];
				}else{
					if ($integral >= $item['lev_a'] && $integral <= $item['lev_b']) $_temp = $item['name'];
				}
				if (!empty($_temp)){
					if (isset($item['color'])){
						if ($item['color']) {
							$_temp = "<font color='{$item['color']}'>{$_temp}</font>";
						}
					}
					$text = $_temp;
					break;
				}
			}
		}
		return $text;
	}
}


/**
 * 通过分店ID获取分店名称
 * @param int $shopid
 * @param array $arr
 * @param string $field
 * @return string
 */
if (!function_exists('get_shop'))
{
	function get_shop($shopid = 0, $arr = array(), $field = 'name'){
		static $shop_classes = array();
		if (!isset($shop_classes["shop"])) {
			$_arr = array();
			$_row = get_instance()->ddb->getall("SELECT * FROM ".table("vip_shop"), $arr);
			foreach($_row as $item){ $_arr[$item['id']] = $item; }
			$shop_classes['shop'] = $_arr;
		}else{
			$_arr = $shop_classes['shop'];
		}
		$text = "分店:".$shopid;
		if (is_array($_arr)){
			if (isset($_arr[$shopid][$field])){
				$text = $_arr[$shopid][$field];
			}
		}
		return $text;
	}
}

/**
 * 获取vip商店表
 * @return mixed
 */
if (!function_exists('getvip_show'))
{
	function getvip_show(){
		global $_A;
		static $getvip_classes = array();
		$title = 'showlist';
		if (!isset($getvip_classes[$title])) {
			$_row = get_instance()->ddb->getall("SELECT * FROM ".table("vip_shop"),
				array('alid'=>$_A['al']['id']));
			if ($_row){
				$_arr = array();
				foreach($_row as $item){ $_arr[$item['id']] = $item; }
				$getvip_classes[$title] = $_arr;
			}else{
				$getvip_classes[$title] = array();
			}
		}
		return $getvip_classes[$title];
	}
}
/**
 * 获取vip配置表
 * @param $title
 * @param $arr
 * @return mixed
 */
if (!function_exists('getvip_setting'))
{
	function getvip_setting($title, $arr){
		static $getvip_classes = array();
		if (!isset($getvip_classes[$title])) {
			$_row = get_instance()->ddb->getone("SELECT * FROM ".table("vip_setting"),
				array('alid'=>$arr['alid'], 'title'=>$title));
			$getvip_classes[$title] = ($_row)?string2array($_row['content']):array();
		}
		return $getvip_classes[$title];
	}
}

/**
 *
 * 获取banner
 */
if (!function_exists('vippagebanner'))
{
	function vippagebanner($title, $arr){
		if ($title){
			$bnameimg = isset($arr[$title])?$arr[$title]:'';
			if (empty($bnameimg)){
				$bnameimg = IMG_PATH.'vipbanner/'.$title.'.jpg';
			}else{
				$bnameimg = fillurl($bnameimg);
			}
			return $bnameimg;
		}
	}
}

/**
 * 增加积分返回字段
 * @param $num
 * @param $fiele
 * @return string
 */
if (!function_exists('point_field'))
{
	function point_field($num, $fiele = ''){
		get_instance()->session->unset_userdata('_VIP_GET_CON');
		$resql = "`point`=`point`+{$num},`pluspoint`=`pluspoint`+{$num}";
		if ($fiele){
			$resql.= ",`{$fiele}`=`{$fiele}`+".$num."";
		}
		return $resql;
	}
}

/**
 * @param $str
 * @param $r
 * @return string
 */
if (!function_exists('avatar_fillurl'))
{
	function avatar_fillurl($str, $r = "/0") {
		if (substr($str, -2) == "/0") {
			$str = substr($str,0 , -2);
			$str.= $r;
		}
		return fillurl($str);
	}
}
