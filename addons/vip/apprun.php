<?php
defined('BASEPATH') OR exit('No direct script access allowed');
error_reporting(0);
include_once 'include/function.php';

class ES_Apprun_Vip extends CI_Model {

    /**
     * APP端 执行前
     */
    public function before()
    {
        global $_A;
        //获取会员信息
        $fans = $_A['fans'];
        $_A['vip'] = db_getone("SELECT * FROM ".table('vip_users'),
			array('alid'=>$fans['alid'], 'type'=>$fans['type'], 'openid'=>$fans['openid']));
		$this->get_content($_A['vip']['card']);
    }

	/**
	 * @param int $card
	 * @param string $phone
	 * @param bool|false $covering
	 * @return array
	 */
	public function edit_phone($card = 0, $phone = '', $covering = false) {
		return $this->edit_mobile($card, $phone, $covering);
	}

	/**
	 * 更新手机号码
	 * @param int $card
	 * @param string $phone
	 * @param bool|false $covering
	 * @return array
	 */
	public function edit_mobile($card = 0, $phone = '', $covering = false) {
		global $_A;
		if (!isMobile($phone)) {
			return array('success'=>0, 'message'=>'手机号码格式错误');
		}
		$warr = array();
		if ($card) {
			if (is_array($card)){
				$warr = array_merge($card, $warr);
			}else{
				$warr['card'] = $card;
			}
			if (isset($_A['vip']['card']) && $_A['vip']['card'] == $warr['card']) {
				$row = $_A['vip'];
			}else{
				$row = db_getone(table("vip_users"), $warr);
			}
		}else{
			if (empty($_A['vip']['id'])) {
				return array('success'=>0, 'message'=>'拉取会员信息失败');
			}else{
				$warr['id'] = $_A['vip']['id'];
				$row = $_A['vip'];
			}
		}
		if (empty($row)) {
			return array('success'=>0, 'message'=>'会员不存在');
		}
		if ($row['openid'] == $_A['fans']['openid'] && $row['alid'] == $_A['fans']['alid']) {
			$fans = $_A['fans'];
		}else{
			$fans = db_getone(table('fans'), merge(array('openid'=>$row['openid'], 'alid'=>$row['alid'])));
		}
		if (empty($fans)) {
			return array('success'=>0, 'message'=>'会员信息不存在');
		}
		if ($fans['userphone'] == $phone) {
			return array('success'=>1, 'message'=>'与原手机号码相同');
		}
		$haverow = db_getone(table('fans'), merge(array('userphone'=>$phone, '`id`!='=>$fans['id'])));
		if (empty($covering)) {
			if (!empty($haverow)) {
				return array('success'=>0, 'message'=>'手机号码已存在');
			}
		}
		db_update(table('fans'), array('userphone'=>$phone), array('id'=>$fans['id']));
		db_update(table('vip_users'), array('phone'=>$phone), array('id'=>$row['id']));
		if (!empty($haverow)) {
			db_update(table('fans'), array('userphone'=>''), array('userphone'=>$phone, "`id`!="=>$fans['id']));
			db_update(table('vip_users'), array('phone'=>''), array("phone"=>$phone, "`id`!="=>$row['id']));
		}
		return array('success'=>1, 'message'=>'修改成功');
	}

	/**
	 * 更新邮箱地址
	 * @param int $card
	 * @param string $email
	 * @param bool|false $covering
	 * @return array
	 */
	public function edit_mail($card = 0, $email = '', $covering = false) {
		global $_A;
		if (!isMail($email)) {
			return array('success'=>0, 'message'=>'邮箱地址格式错误');
		}
		$warr = array();
		if ($card) {
			if (is_array($card)){
				$warr = array_merge($card, $warr);
			}else{
				$warr['card'] = $card;
			}
			if (isset($_A['vip']['card']) && $_A['vip']['card'] == $warr['card']) {
				$row = $_A['vip'];
			}else{
				$row = db_getone(table("vip_users"), $warr);
			}
		}else{
			if (empty($_A['vip']['id'])) {
				return array('success'=>0, 'message'=>'拉取会员信息失败');
			}else{
				$warr['id'] = $_A['vip']['id'];
				$row = $_A['vip'];
			}
		}
		if (empty($row)) {
			return array('success'=>0, 'message'=>'会员不存在');
		}
		if ($row['openid'] == $_A['fans']['openid'] && $row['alid'] == $_A['fans']['alid']) {
			$fans = $_A['fans'];
		}else{
			$fans = db_getone(table('fans'), merge(array('openid'=>$row['openid'], 'alid'=>$row['alid'])));
		}
		if (empty($fans)) {
			return array('success'=>0, 'message'=>'会员信息不存在');
		}
		if ($fans['useremail'] == $email) {
			return array('success'=>1, 'message'=>'与原邮箱地址相同');
		}
		$haverow = db_getone(table('fans'), merge(array('useremail'=>$email, '`id`!='=>$fans['id'])));
		if (empty($covering)) {
			if (!empty($haverow)) {
				return array('success'=>0, 'message'=>'邮箱地址已存在');
			}
		}
		db_update(table('fans'), array('useremail'=>$email), array('id'=>$fans['id']));
		db_update(table('vip_users'), array('email'=>$email), array('id'=>$row['id']));
		if (!empty($haverow)) {
			db_update(table('fans'), array('useremail'=>''), array('useremail'=>$email, "`id`!="=>$fans['id']));
			db_update(table('vip_users'), array('email'=>''), array("email"=>$email, "`id`!="=>$row['id']));
		}
		return array('success'=>1, 'message'=>'修改成功');
	}

    /**
     * 变化积分
     * @param $card
     * @param $num
     * @param $content
     * @return array
     */
    public function point($card, $num, $content)
    {
        global $_A;
        //
        $num = intval($num);
        if (empty($num)) {
            return array('success'=>0, 'message'=>'积分数量错误');
        }
		$warr = array();
		if ($card) {
			if (is_array($card)){
				$warr = array_merge($card, $warr);
			}else{
				$warr['card'] = $card;
			}
		}else{
			return array('success'=>0, 'message'=>'会员卡号错误');
		}
		if (isset($_A['vip']['card']) && $_A['vip']['card'] == $warr['card']) {
			$row = $_A['vip'];
		}else{
			$row = db_getone("SELECT * FROM ".table("vip_users"), $warr);
		}
        if (empty($row)) {
            return array('success'=>0, 'message'=>'用户不存在');
        }
        if ($num > 0) {
            $resql = "`point`=`point`+{$num},`pluspoint`=`pluspoint`+{$num}";
			$this->session->unset_userdata('_VIP_GET_CON');
        }else{
            if ($row['point'] < $num) {
                return array('success'=>0, 'message'=>'用户积分不足');
            }
            $resql = "`point`=`point`-{$num}";
        }
        if (db_query("UPDATE ".table('vip_users')." SET ".$resql." WHERE `id`=".$row['id'])){
            //添加记录
            $poiarr = array();
            $poiarr['type'] = 'system';
            $poiarr['card'] = $row['card'];
            $poiarr['openid'] = $row['openid'];
            $poiarr['point'] = $row['point'];
            $poiarr['outpoint'] = $num;
            $poiarr['content'] = $content;
            $poiarr['indate'] = SYS_TIME;
            $poiarr['indate_cn'] = date("Y-m-d", SYS_TIME);
            $poiarr['alid'] = $row['alid'];
            db_insert(table('vip_point_notes'), $poiarr);
            //
            return array('success'=>1, 'users'=>$row);
        }else{
            return array('success'=>0, 'message'=>'更新数据失败', 'users'=>$row);
        }
    }

	/**
	 * 变化余额
	 * @param $card
	 * @param $num
	 * @param $content
	 * @param bool $_point 是否计算增加积分，默认：是
	 * @return array
	 */
	public function money($card, $num, $content, $_point = true)
    {
        global $_A;
        //
        $num = floatval($num);
        if (empty($num)) {
            return array('success'=>0, 'message'=>'金额数量错误');
        }
		$warr = array();
		if ($card) {
			if (is_array($card)){
				$warr = array_merge($card, $warr);
			}else{
				$warr['card'] = $card;
			}
		}else{
			return array('success'=>0, 'message'=>'会员卡号错误');
		}
		if (isset($_A['vip']['card']) && $_A['vip']['card'] == $warr['card']) {
			$row = $_A['vip'];
		}else{
			$row = db_getone("SELECT * FROM ".table("vip_users"), $warr);
		}
        if (empty($row)) {
            return array('success'=>0, 'message'=>'用户不存在');
        }
        if ($num < 0 && $row['money'] < abs($num)) {
            return array('success'=>0, 'message'=>'用户余额不足');
        }
        if (db_query("UPDATE ".table('vip_users')." SET `money`=`money`+".$num." WHERE `id`=".$row['id'])){
            //添加记录
            $poiarr = array();
            $poiarr['type'] = 'system';
            $poiarr['card'] = $row['card'];
            $poiarr['openid'] = $row['openid'];
            $poiarr['money'] = $row['money'];
            $poiarr['outmoney'] = $num;
            $poiarr['content'] = $content;
            $poiarr['indate'] = SYS_TIME;
            $poiarr['indate_cn'] = date("Y-m-d", SYS_TIME);
            $poiarr['alid'] = $row['alid'];
            db_insert(table('vip_point_notes'), $poiarr);
			if ($num < 0 && $_point) {
				//消费增加到积分
				$this->money_point($card, abs($num));
			}
            //
            return array('success'=>1, 'users'=>$row);
        }else{
            return array('success'=>0, 'message'=>'更新数据失败', 'users'=>$row);
        }
    }

	/**
	 * 消费X元，奖励Y积分
	 * @param $card
	 * @param int $num 消费X元（正数）
	 * @param string $content
	 * @return array
	 */
	public function money_point($card, $num, $content = '')
	{
		global $_A;
		//
		$num = floatval($num);
		if ($num <= 0 ) {
			return array('success'=>0, 'message'=>'金额数量错误');
		}
		$warr = array();
		if ($card) {
			if (is_array($card)){
				$warr = array_merge($card, $warr);
			}else{
				$warr['card'] = $card;
			}
		}else{
			return array('success'=>0, 'message'=>'会员卡号错误');
		}
		if (isset($_A['vip']['card']) && $_A['vip']['card'] == $warr['card']) {
			$row = $_A['vip'];
		}else{
			$row = db_getone("SELECT * FROM ".table("vip_users"), $warr);
		}
		if (empty($row)) {
			return array('success'=>0, 'message'=>'用户不存在');
		}
		//
		$f = db_getone("SELECT id FROM ".table("functions"), array('title_en'=>'vip'));
		if ($row['alid'] == $_A['al']['id']) {
			$al = $_A['al'];
		}else{
			$al = db_getone("SELECT id,userid FROM ".table("users_al"), array('id'=>$row['alid']));
		}
		$uf = db_getone("SELECT userdata FROM ".table("users_functions"),
			array('fid'=>intval($f['id']), 'alid'=>intval($al['id']), 'userid'=>intval($al['userid'])));
		$vip_data = string2array($uf['userdata']);
		$jfcl = $vip_data['jfcl'];
		$xiaofei = $jfcl['xiaofei'];
		$jiangli = $jfcl['jiangli'];
		$tpoint = 0;
		if ($xiaofei > 0 && $jiangli > 0){
			$tpoint = intval(($num/$xiaofei)*$jiangli);
		}
		if ($tpoint > 0) {
			$content = $content?$content:'消费奖励(每消费'.$xiaofei.'元,获'.$jiangli.'分)';
			return $this->point(array("id"=>$row['id']), $tpoint, $content);
		}else{
			return array('success'=>0, 'message'=>'消费未达指定数没有奖励');
		}
	}

	/**
	 * 领取符合条件的 通知、特权、优惠券、礼品券
	 * @param $card
	 * @return bool
	 */
	public function get_content($card) {
		global $_A;
		//
		$_VIP_GET_CON = $this->session->userdata('_VIP_GET_CON');
		if ($_VIP_GET_CON == "IN") return false;
		$this->session->set_userdata('_VIP_GET_CON', 'IN');
		//会员
		$warr = array('alid'=>$_A['al']['id']);
		if ($card && is_array($card)) {
			$warr = array_merge($card, $warr);
		}else{
			$warr['card'] = $card;
		}
		if (isset($_A['vip']['card']) && $_A['vip']['card'] == $warr['card']) {
			$row = $_A['vip'];
		}else{
			$row = db_getone("SELECT * FROM ".table("vip_users"), $warr);
		}
		if (empty($row)) return false;
		//会员类型
		$_vipname = strip_tags(get_user_rank($row['pluspoint']));
		if (empty($_vipname)) return false;
		//查询条件
		$_wheres = "`alid`=".$_A['al']['id'];
		$_wheres.= " AND `id` NOT IN (SELECT contentid FROM ".table("vip_record")." WHERE `userid`=".$row['id']." AND".$_wheres.")";
		$_wheres.= " AND `onevips` LIKE '%,".$_vipname.",%' ";
		$_wheres.= " AND (`type`='msg' OR (`startdate`<".SYS_TIME." AND `enddate`>".SYS_TIME.")) ";
		$conlist = db_getall("SELECT * FROM ".table("vip_content")." WHERE ".$_wheres);
		$inarr = array();
		$inarr['alid'] = $_A['al']['id'];
		$inarr['userid'] = $row['id'];
		$inarr['card'] = $row['card'];
		$inarr['openid'] = $row['openid'];
		$inarr['view'] = 0;
		foreach($conlist AS $item){
			$inarr['type'] = $item['type'];
			$inarr['contentid'] = $item['id'];
			$inarr['indate'] = $item['indate'];
			$inarr['num'] = 0;
			if ($item['type']=='cut') {
				$inarr['num'] = $item['int_c']; //优惠券数量(例外)
			}elseif ($item['type']=='gift'){
				$inarr['num'] = $item['int_b']; //礼品券数量(例外)
			}
			$inarr['sn'] = getSN($row['id'], $item['id']);
			db_insert(table("vip_record"), $inarr);
		}
		return true;
	}

	/**
	 * 更新会员数据，不存在的则添加
	 * @param $ret  array() 获取用户基本信息(UnionID机制)
	 * @param $alid
	 * @param $wx_appid
	 * @return bool
	 */
	public function upto_wxuser($ret, $alid, $wx_appid) {
		if ($ret['openid'] && strlen($ret['openid'])>20 && $alid && $wx_appid) {
			$this->load->helper('emoji_other');
			$ret['nickname'] = emoji_unified_to_null($ret['nickname']);
			//粉丝
			$uparr = array();
			$uparr['type'] = 'weixin';
			$uparr['appid'] = $wx_appid;
			$uparr['openid'] = $ret['openid'];
			$uparr['user_name'] = $ret['nickname'];
			$uparr['follow'] = intval($ret['subscribe']);
			$uparr['avatar'] = $ret['headimgurl'];
			$uparr['province'] = $ret['province'];
			$uparr['city'] = $ret['city'];
			$uparr['address'] = '';
			if(!empty($ret['province'])) $uparr['address'].= $ret['province'].'省';
			if(!empty($ret['city'])) $uparr['address'].= $ret['city'].'市';
			$uparr['sex'] = '';
			if ($ret['sex'] == '1') {
				$uparr['sex'] = '男';
			}elseif ($ret['sex'] == '2') {
				$uparr['sex'] = '女';
			}
			$row = db_getone(table('fans'), array('alid'=>$alid, 'openid'=>$ret['openid']));
			if (empty($row)) {
				$uparr['alid'] = $alid;
				$uparr['indate'] = SYS_TIME;
				$uparr['id'] = db_insert(table('fans'), $uparr, true);
			}else{
				db_update(table('fans'), $uparr, array('id'=>$row['id']));
				$uparr['id'] = $row['id'];
			}
			if (empty($uparr['id'])) {
				return false;
			}
			//会员
			unset($uparr['id']);
			unset($uparr['appid']);
			unset($uparr['province']);
			unset($uparr['city']);
			unset($uparr['user_name']);
			$uparr['fullname'] = $ret['nickname'];
			$rou = db_getone(table('vip_users'), array('alid'=>$alid, 'openid'=>$ret['openid']));
			if (empty($rou)) {
				$uparr['alid'] = $alid;
				$uparr['indate'] = SYS_TIME;
				$uparr['id'] = db_insert(table('vip_users'), $uparr, true);
				//
				$idlen = strlen($uparr['id']);
				if ($idlen > 4) {
					$uparr['card'] = 8000 + intval(substr($uparr['id'], 0, $idlen - 4));
					$uparr['card'].= zerofill($alid, 4).zerofill(substr($uparr['id'],-4), 4);
				}else{
					$uparr['card'] = '8000'.zerofill($alid, 4).zerofill($uparr['id'], 4);
				}
				db_update(table('vip_users'), array('card'=>$uparr['card']), array('id'=>$uparr['id']));
			}else{
				db_update(table('vip_users'), $uparr, array('id'=>$rou['id']));
				$uparr['id'] = $rou['id'];
			}
			if (empty($uparr['id'])) {
				return false;
			}else{
				return true;
			}
		}
		return false;
	}

}
