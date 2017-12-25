<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ES_Processor_Vip extends CI_Model {

	/**
	 * 事件处理 (通用)
	 * @param string $apitype			//消息类型：weixin 为微信信息、alipay 为服务窗信息
	 * @return bool
	 */
	public function respond($apitype = '')
    {
        global $_A;
        $fans = $_A['fans'];
        if (empty($fans['openid']) || empty($fans['alid'])) return false;

        //检测用户
        $vuser = $this->ddb->getone("SELECT * FROM ".table('vip_users'),
			array('alid'=>$fans['alid'], 'type'=>$fans['type'], 'openid'=>$fans['openid']));
        if (empty($vuser)) {
            $fans['setting'] = string2array($fans['setting']);
            //
            $vuser = array(
                'money' => 0,           //消费金额
                'moneyone' => 0,        //单次消费金额(最高数)
                'ofdate' => 0,          //最后消费时间
                'pluspoint' => 0,       //加起来的积分(每次增加的积分总和,有增无减) -- 也就是最高积分
                'point' => 0,           //剩余积分
                'inpoint' => 0,         //签到积分
                'outpoint' => 0,        //消费积分 -- 也就是使用过的积分
                'editnum' => 0,         //修改资料次数
                'indate' => SYS_TIME,   //注册时间
                'enddate' => 0          //到期时间
            );
            $vuser['id'] = $this->ddb->insert(table('vip_users'), array('openid'=>$fans['openid']), true);
            //基本信息
			$vuser['type'] = $fans['type'];
			$vuser['alid'] = $fans['alid'];
            $vuser['openid'] = $fans['openid'];
            $vuser['fullname'] = $fans['user_name'];
            $vuser['follow'] = $fans['follow'];
            //资料信息
			$vuser['phone'] = isMobile($fans['phone'])?$fans['phone']:$fans['userphone'];
			$vuser['avatar'] = $fans['avatar'];
            $vuser['sex'] = $fans['setting']['sex'];
            $vuser['address'] = $fans['setting']['address'];
            //
            $idlen = strlen($vuser['id']);
            if ($idlen > 4) {
                $vuser['card'] = 8000 + intval(substr($vuser['id'], 0, $idlen - 4));
                $vuser['card'].= zerofill($_A['al']['id'], 4).zerofill(substr($vuser['id'],-4), 4);
            }else{
                $vuser['card'] = '8000'.zerofill($_A['al']['id'], 4).zerofill($vuser['id'], 4);
            }
            $this->ddb->update(table('vip_users'), $vuser, array('id'=>$vuser['id']));
        }else{
			$_uparr = array();
			if (isset($fans['follow']) && $vuser['follow'] != $fans['follow']) {
				$_uparr['follow'] = $fans['follow'];
			}
			if (isset($_A['_updatefans'])) {
				if ($fans['user_name']) $_uparr['fullname'] = $vuser['fullname'] = $fans['user_name'];
				if ($fans['phone']) $_uparr['phone'] = $vuser['phone'] = $fans['phone'];
				if ($fans['avatar']) $_uparr['avatar'] = $vuser['avatar'] = $fans['avatar'];
				if ($fans['sex']) $_uparr['sex'] = $vuser['sex'] = $fans['sex'];
				if ($fans['address']) $_uparr['address'] = $vuser['address'] = $fans['address'];
			}
			if ($_uparr) {
				$_uparr['update'] = SYS_TIME;
				$this->ddb->update(table('vip_users'), $_uparr, array('id'=>$vuser['id']));
			}
        } $_A['vip'] = $vuser;

		// oauth 服务窗授权
		if (isset($_A['_oauthalipay'])) {

		}

        // oauth 微信授权
        if (isset($_A['_oauthalipay'])) {

        }

		// 新增用户信息
		if (isset($_A['_insertfans'])) {

		}

		// 更新用户信息
		if (isset($_A['_updatefans'])) {

		}

		/**
		 * 服务窗 事件
		 */
		$M = $_A['M'];
        // 收到用户发送的对话消息
        if ($M['msgtype'] == "text") {

        }
		// 收到用户点击菜单
		elseif ($M['msgtype'] == "click") {

		}
        // 接收用户发送的 图片消息
        elseif ($M['msgtype'] == "image") {

        }
        // 接收用户事件
        elseif ($M['msgtype'] == "event") {

            // 收到用户发送的关注消息
            if ($M['eventtype'] == "follow") {


            }
            // 处理取消关注消息
            elseif ($M['eventtype'] == "unfollow") {


            }
            // 处理进入消息，扫描二维码进入,获取二维码扫描传过来的参数
            elseif ($M['eventtype'] == "enter") {


            }
            // 处理菜单点击的消息
            elseif ($M['eventtype'] == "click") {


            }

        }

		//签到
		if ($M['msgtype'] == "text" || $M['msgtype'] == "click") {
			$signreply = db_getone(table('vip_setting'), array('title'=>'signkey_'.$fans['alid']));
			if (!empty($signreply)) {
				$signsetting = string2array($signreply['content']);
				if (strexists(",".$signsetting['keys'].",", ",".$M['text'].",")) {
					$get_appid = $_A['al']['setting']['other']['get_appid'];
					$get_appoint = value($_A, 'al|setting|other|get_appoint', true);
					if ($get_appid && (empty($get_appoint) || in_array('vip', $get_appoint))) {
						$this->base->respText('<a href="'.appurl('vip/sign').'">点击这里签到！</a>', $apitype);
					}else{
						$oktis = $signsetting['oktis']?$signsetting['oktis']:'签到成功啦！你已获得#签到积分#积分。';
						$notis = $signsetting['notis']?$signsetting['notis']:'您今天已经签到过了！明天再来吧！';
						$signs = $this->sign();
						if ($signs === false) {
							$this->base->respText($notis, $apitype);
						}else{
							$oktis = str_replace("#签到积分#", $signs, $oktis);
							$this->base->respText($oktis, $apitype);
						}
					}
				}
			}
		}
    }

	/**
	 * 事件处理 (服务窗)
	 */
	public function alipayrespond()
	{

	}

	/**
	 * 事件处理 (公众号)
	 */
	public function weixinrespond()
	{

	}

	/** ***********************************************************************************************************/
	/** ***********************************************************************************************************/
	/** ***********************************************************************************************************/

	/**
	 * 签到
	 */
	private function sign()
	{
		global $_A;
		$sign = db_getone(table('vip_point_notes'), array('alid'=>$_A['vip']['alid'], 'type'=>'sign', 'indate_cn'=>date("Y-m-d",SYS_TIME), 'card'=>$_A['vip']['card']));
		if ($sign){
			return false;
		}

		$F = db_getone(table('functions'), array('title_en'=>'vip'));
		if (empty($F)) return false;
		$UF = db_getone(table('users_functions'), array('fid'=>$F['id'], 'alid'=>$_A['vip']['alid']));
		$vip_data = string2array($UF['userdata']);
		$jfcl = $vip_data['jfcl'];

		$_arr = array();
		$_arr['type'] = 'sign'; //签到
		$_arr['card'] = $_A['vip']['card'];
		$_arr['openid'] = $_A['vip']['openid'];
		$_arr['point'] = $_A['vip']['point'];
		$_arr['outpoint'] = intval($jfcl['meiri']);
		$_arr['content'] = "每日签到奖励";
		$_arr['indate'] = SYS_TIME;
		$_arr['indate_cn'] = date("Y-m-d",SYS_TIME);
		$_arr['alid'] = $_A['vip']['alid'];
		$signnum = $_arr['outpoint'];
		if (db_insert(table('vip_point_notes'), $_arr)){
			//更新到会员信息
			db_query("UPDATE ".table('vip_users')." SET ".$this->point_field($_arr['outpoint'],'inpoint')." WHERE `alid`='".$_A['vip']['alid']."' AND `card`='".$_A['vip']['card']."'");
			//判断连续签到
			$lianxu = intval($jfcl['lianxu']);
			if ($lianxu > 0){
				$ttime = strtotime(date("Y-m-d", strtotime("-".intval($lianxu-1)." days")));
				$lxrow = db_getone("SELECT COUNT(*) AS num FROM ".table('vip_point_notes')." WHERE `alid`='".$_A['vip']['alid']."' AND `type`='sign' AND `indate`>=".$ttime." AND `card`='".$_A['vip']['card']."'");
				$llrow = db_getone("SELECT COUNT(*) AS num FROM ".table('vip_point_notes')." WHERE `alid`='".$_A['vip']['alid']."' AND `type`='signs' AND `indate`>=".$ttime." AND `card`='".$_A['vip']['card']."'");
				if ($lxrow['num'] >= $lianxu && $llrow['num'] <= 0){ // 条件：X天内签到数大于等于X条 AND X天内没有活得过连续签到奖励
					$_arr = array();
					$_arr['type'] = 'signs'; //连续签到
					$_arr['card'] = $_A['vip']['card'];
					$_arr['openid'] = $_A['vip']['openid'];
					$_arr['point'] = $_A['vip']['point'] + $_arr['outpoint'];
					$_arr['outpoint'] = intval($jfcl['lianxuval']);
					$_arr['content'] = "连续签到{$jfcl['lianxu']}天奖励";
					$_arr['indate'] = SYS_TIME;
					$_arr['indate_cn'] = date("Y-m-d",SYS_TIME);
					$_arr['alid'] = $_A['vip']['alid'];
					$this->ddb->insert(table('vip_point_notes'), $_arr);
					$signnum+= $_arr['outpoint'];
					//更新到会员信息
					$this->ddb->query("UPDATE ".table('vip_users')." SET ".$this->point_field($_arr['outpoint'],'inpoint')." WHERE `alid`='".$_A['vip']['alid']."' AND `card`='".$_A['vip']['card']."'");
				}
			}
			return $signnum;
		}else{
			return false;
		}
	}

	/**
	 * 签到 - 辅助函数
	 */
	private function point_field($num, $fiele = ''){
		get_instance()->session->unset_userdata('_VIP_GET_CON');
		$resql = "`point`=`point`+{$num},`pluspoint`=`pluspoint`+{$num}";
		if ($fiele){
			$resql.= ",`{$fiele}`=`{$fiele}`+".$num."";
		}
		return $resql;
	}
}
