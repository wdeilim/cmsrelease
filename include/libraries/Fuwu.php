<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once 'fuwu/function.inc.php';
require_once 'fuwu/HttpRequst.php';
require_once 'fuwu/config.php';
require_once 'fuwu/AlipaySign.php';
header ( "Content-type: text/html; charset=gbk" );


class Fuwu {
	public $data = array();


	/**
	 * 通过 auth_code 获取用户信息
	 * @param $auth_code
	 * @return array|string
	 */
	public function getUserInfo($auth_code)
    {
		require_once 'fuwu/UserInfo.php';
		$user = new UserInfo ();
		$retuser = object_array($user->getUserInfo($auth_code));
        return characet($retuser);
    }

	/**
	 * 接收信息
	 */
	public function receive()
	{
        global $_A,$_GPC;
        $post_str = isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : file_get_contents("php://input");
        if (empty($post_str)) return false;
        parse_str(mb_convert_encoding(urldecode($post_str), "UTF-8","GBK"), $post_obj);

		$sign = HttpRequest::getRequest ( "sign" , $post_obj );
		$sign_type = HttpRequest::getRequest ( "sign_type" , $post_obj );
		$biz_content = HttpRequest::getRequest ( "biz_content" , $post_obj );
		$service = HttpRequest::getRequest ( "service" , $post_obj );
		$charset = HttpRequest::getRequest ( "charset" , $post_obj );

		if (empty ( $sign ) || empty ( $sign_type ) || empty ( $biz_content ) || empty ( $service ) || empty ( $charset )) {
            if ($_GPC['sid']) {
                if ($_GPC['sid'] != md52($_A['al']['id'],$_A['al']['al_appid'])) {
                    echo "some parameter is empty.";
                    writeLog ( "some parameter is empty.");
                    exit ();
                }
                $service = "alipay.mobile.public.message.notify";
                $biz_content = $_GPC['content'];
                define('_ISEMULATOR', true);
            }else{
                echo "some parameter is empty.";
                writeLog ( "some parameter is empty.");
                exit ();
            }
		}

		// 验证网关请求
		if ($service == "alipay.service.check") {
			$as = new AlipaySign ();
			$xml = simplexml_load_string ( $biz_content );
			$EventType = ( string ) $xml->EventType;
			if ($EventType == "verifygw") {
				$response_xml = "<success>true</success><biz_content>".$as->getPublicKeyStr($this->config('merchant_public_key_file'))."</biz_content>";
				$return_xml = $as->sign_response ( $response_xml, $this->config('charset'), $this->config('merchant_private_key_file'));
				writeLog ( "response_xml: " . $return_xml );
				$this->lastin($_A['al']['id']);
				echo $return_xml;
				exit ();
			}
		} else if ($service == "alipay.mobile.public.message.notify") {
			// 处理收到的消息
			require_once 'fuwu/Message.php';
			require_once 'fuwu/PushMsg.php';
			$this->Message($biz_content);
		}
	}


	/**
	 * 接收信息处理
	 * @param $biz_content
	 */
	public function Message($biz_content)
	{
		global $_A;
		writeLog ( $biz_content );
		$M = array();
		$M['userinfo'] = $this->getNode ( $biz_content, "UserInfo" );
		$userinfo = json_decode($M['userinfo'], true);
		$M['logon_id'] = value($userinfo, 'logon_id');
		$M['user_name'] = value($userinfo, 'user_name');
		$M['openid'] = $M['fromuserid'] = $this->getNode ( $biz_content, "FromUserId" );
		$M['openid_type'] = 'alipay';
		$M['appid'] = $this->getNode ( $biz_content, "AppId" );
		$M['createtime'] = $this->getNode ( $biz_content, "CreateTime" );
		$M['msgtype'] = $this->getNode ( $biz_content, "MsgType" );
		$M['eventtype'] = $this->getNode ( $biz_content, "EventType" );
		$M['agreementid'] = $this->getNode ( $biz_content, "AgreementId" );
		$M['actionparam'] = $this->getNode ( $biz_content, "ActionParam" );
		$M['accountno'] = $this->getNode ( $biz_content, "AccountNo" );
		$M['rawdata'] = xml2array($biz_content);
		$M['alid'] = $_A['al']['id'];
		$this->exist_group($M);
		$this->lastin($_A['al']['id']);

		$push = new PushMsg ();

		// 收到用户发送的对话消息
		if ($M['msgtype'] == "text") {
			$M['text'] = $this->getNode($biz_content, "Text");
			$this->savemessage($M);
			$return_msg = $this->sendkey($M, $M['text']);
			writeLog("发送对话消息返回：" . $return_msg);
		}

		// 接收用户发送的 图片消息
		if ($M['msgtype'] == "image") {
			$mediaId = $this->getNode ( $biz_content, "MediaId" );
			$format = $this->getNode ( $biz_content, "Format" );

			$biz_content = "{\"mediaId\":\"" . $mediaId . "\"}";

			make_dir(BASE_PATH.'uploadfiles/users/'.value($_A,'userid','int')."/images/_alipay/".date("Ym/", SYS_TIME));
			$fileName = 'uploadfiles/users/'.value($_A,'userid','int').'/images/_alipay/'.date("Ym/", SYS_TIME).$mediaId.$format;
			// 下载保存图片
			$push->downMediaRequest($biz_content, BASE_PATH.$fileName);

			$M['text'] = $fileName;
			$this->savemessage($M);
			$return_msg = $this->sendkey($M, $M['text']);
			writeLog("收到的图片返回：" . $return_msg);
		}

		if ($M['eventtype'] == "follow") {
            // 收到用户发送的关注消息
			$M['msgtype'] = 'follow';
			$M['text'] = '::关注';
			$this->savemessage($M);
			$return_msg = $this->sendkey($M, $M['text']);
			writeLog ( "关注返回：" . $return_msg );

		} elseif ($M['eventtype'] == "unfollow") {
			// 处理取消关注消息
			$M['msgtype'] = 'unfollow';
			$M['text'] = '::取消关注';
			$this->savemessage($M);
			writeLog ( "取消关注");

		} elseif ($M['eventtype'] == "enter") {
			// 处理进入消息，扫描二维码进入,获取二维码扫描传过来的参数
			$arr = json_decode($M['actionparam']);
			if ($arr != null) {
                $sceneId = $arr->scene->sceneId;
                $M['msgtype'] = 'enter';
                $M['text'] = $arr;
                $this->savemessage($M);
                writeLog("二维码传来的参数：".var_export ($arr, true));
                writeLog("二维码传来的参数,场景ID：" . $sceneId);
				// 这里可以根据定义场景ID时指定的规则，来处理对应事件。
				// 如：跳转到某个页面，或记录从什么来源(哪种宣传方式)来关注的本服务窗
			}

		} elseif ($M['eventtype'] == "click") {
			// 处理菜单点击的消息
			$M['msgtype'] = 'click';
			$M['text'] = $M['actionparam'];
			$this->savemessage($M);
			$return_msg = $this->sendkey($M, $M['text']);
			writeLog ( "点击菜单返回：" . $return_msg );

		}

		// 给支付宝返回ACK回应消息，不然支付宝会再次重试发送消息,再调用此方法之前，不要打印输出任何内容
		echo self::mkAckMsg($M['fromuserid']);
		exit ();
	}


    /**
     * 保存/查看 用户信息
     * @param array $M
     * @param bool $only
     * @param bool $notype
     * @return array|bool
     */
    public function exist_group($M = array(), $only = false, $notype = false)
	{
		global $_A;
		if (empty($M['openid']) || empty($M['alid'])) {
			return false;
		}
        $wherearr = array('alid'=>$M['alid'], 'type'=>'alipay', 'openid'=>$M['openid']);
        if ($notype) unset($wherearr['type']);
		$row = db_getone(table('fans'), $wherearr);
		if ($only) return $row;
		if ($M['user_name']) {
			$CI =& get_instance();
			$CI->load->helper('emoji_other');
			$M['user_name'] = emoji_unified_to_null($M['user_name']);
		}
        //基本信息
		$iarr = array(
			'type'=>'alipay',
			'logon_id'=>$M['logon_id'],
			'user_name'=>$M['user_name'],
			'update'=>SYS_TIME
		);
		if ($M['avatar']) $iarr['avatar'] = $M['avatar'];
		if ($M['cert_type_value']) $iarr['cert_type_value'] = $M['cert_type_value'];
		if ($M['cert_no']) $iarr['cert_no'] = $M['cert_no'];
		if ($M['sex']) $iarr['sex'] = $M['sex'];
		if ($M['phone']) $iarr['phone'] = $M['phone'];
		if ($M['mobile']) $iarr['mobile'] = $M['mobile'];
		if ($M['province']) $iarr['province'] = $M['province'];
		if ($M['city']) $iarr['city'] = $M['city'];
		if ($M['area']) $iarr['area'] = $M['area'];
		if ($M['address']) $iarr['address'] = $M['address'];
		if ($M['zip']) $iarr['zip'] = $M['zip'];
		if ($M['setting']) $iarr['setting'] = $M['setting'];
		//
		if ($M['userphone']) $iarr['userphone'] = $M['userphone'];
		if ($M['useremail']) $iarr['useremail'] = $M['useremail'];
		if ($M['userpass']) $iarr['userpass'] = $M['userpass'];
        //是否关注
		if ($M['eventtype'] == 'unfollow') {
			$iarr['follow'] = 0;
		}elseif ($M['eventtype'] == 'follow' || isset($M['msgtype'])) {
			$iarr['follow'] = 1;
		}
		if ($row) {
			$_A['_updatefans'] = 1;
            //更新用户信息
            if (!$iarr['logon_id']) unset($iarr['logon_id']);
            if (!$iarr['user_name']) unset($iarr['user_name']);
			$ret = db_update(table('fans'), $iarr, array('id'=>$row['id']));
		}else{
			$_A['_insertfans'] = 1;
			//新增用户信息
			if ($M['follow']) $iarr['follow'] = $M['follow'];
			$iarr['alid'] = $M['alid'];
			$iarr['appid'] = $M['appid'];
			$iarr['openid'] = $M['openid'];
			$iarr['indate'] = SYS_TIME;
			$ret = db_insert(table('fans'), $iarr, true);
			$iarr['id'] = $ret;
		}
		$row = _array_merge($row, $iarr);
		unset($row['setting']);
		$_A['fans'] = $row;
		return $ret;
	}

	/**
	 * 保存事件记录
	 * @param array $M
	 * @param int $tobe
	 * @return bool
	 */
	public function savemessage($M = array(), $tobe = 0)
	{
        global $_A;
        $_A['M'] = $M;
		if (empty($M['openid']) || empty($M['alid'])) return false;
		$iarr = array();
		$iarr['alid'] = $M['alid'];
		$iarr['openid'] = $M['openid'];
		$iarr['type'] = 'alipay';
		$iarr['msgtype'] = $M['msgtype'];
		$iarr['text'] = $M['text'];
		$iarr['tobe'] = $tobe; //0接收、1发送
		$iarr['indate'] = SYS_TIME;
        $iarr['emulator'] = defined('_ISEMULATOR')?1:0;
		$ret = !defined('_ISEMULATOR')?db_insert(tableal('message'), $iarr ,true):0;
		if (!defined('_ISPROCESSOR')) {
			define('_ISPROCESSOR', true);
			$this->processor();
		}
		return $ret;
	}

    /**
     * 发送文本信息
     * @param array $M
     * @return bool
     */
    public function sendtext($M = array())
    {
        global $_A;
        if (empty($M['openid']) || empty($M['alid'])) {
            return false;
        }
        if ($_A['al']['id'] == $M['alid']) {
            $row = $_A['al'];
        }else{
            $row = db_getone("SELECT * FROM ".table('users_al'), array('id'=>$M['alid']));
        }
        if (empty($row)) {
            return false;
        }
        //
        $mid = $this->savemessage($M, 1);
        //
        require_once 'fuwu/PushMsg.php';
        $push = new PushMsg ();
        $text_msg = $push->mkTextMsg(new_addslashes($M['text']));
        //发给这个关注的用户
        $biz_content = $push->mkTextBizContent($M['openid'], $text_msg);
        $biz_content = iconv("UTF-8", "GBK//IGNORE", $biz_content);
        $biz_content = object_array($push->sendRequest($biz_content));
        $rcode = value($biz_content, 'alipay_mobile_public_message_custom_send_response|code');
        if ($rcode != '200'){
            db_update(tableal('message'), array('err'=>'错误:'.$this->error_code($rcode)), array('id'=>$mid));
        }
        return $biz_content;
    }

    /**
     * 发送图像文本
     * @param array $M
     * @return bool
     */
    public function sendimagetext($M = array())
    {
        global $_A;
        if (empty($M['openid']) || empty($M['alid'])) {
            return false;
        }
        if ($_A['al']['id'] == $M['alid']) {
            $row = $_A['al'];
        }else{
            $row = db_getone("SELECT * FROM ".table('users_al'), array('id'=>$M['alid']));
        }
        if (empty($row)) {
            return false;
        }
        //
        $mid = $this->savemessage($M, 1);
        //
        require_once 'fuwu/PushMsg.php';
        $push = new PushMsg ();
        $image_text_msg = array();
        $imagetext = new_addslashes(string2array($M['text']));
        $image_text_msg[] = $push->mkImageTextMsg(
            $imagetext['title'],
            $imagetext['desc'],
            $imagetext['url'],
            fillurl($imagetext['img']), "");
        //发给这个关注的用户
        $biz_content = $push->mkImageTextBizContent($M['openid'], $image_text_msg);
        $biz_content = object_array($push->sendRequest($biz_content));
        $rcode = value($biz_content, 'alipay_mobile_public_message_custom_send_response|code');
        if ($rcode != '200'){
            db_update(tableal('message'), array('err'=>'错误:'.$this->error_code($rcode)), array('id'=>$mid));
        }
        return $biz_content;
    }

    /**
     * 发送广播素材
     * @param array $M
     * @return bool
     */
    public function sendmaterial($M = array())
    {
        global $_A;
        if (empty($M['openid']) || empty($M['alid']) || empty($M['material'])) {
            return false;
        }
        if ($_A['al']['id'] == $M['alid']) {
            $row = $_A['al'];
        }else{
            $row = db_getone("SELECT * FROM ".table('users_al'), array('id'=>$M['alid']));
        }
        if (empty($row)) {
            return false;
        }
        $library = db_getone("SELECT * FROM ".table('library'), array('alid'=>$row['id'], 'id'=>intval($M['material'])));
        if (empty($library)) {
            return false;
        }
        $M['msgtype'] = 'material';
        $M['text'] = $library['id'];
        $mid = $this->savemessage($M, 1);
        //
        require_once 'fuwu/PushMsg.php';
        $push = new PushMsg ();
        $image_text_msg = array();
        if ($library['type'] == 'onlyimg') {
            $library = new_addslashes($library);
            $image_text_msg[] = $push->mkImageTextMsg(
                $library['title'],
                $library['descriptions'],
				appurl('library/'.$library['id']),
                fillurl($library['img']), "");
        }elseif ($library['type'] == 'manyimg') {
            $libset = string2array($library['setting']);
            foreach($libset AS $k=>$item) {
                $item = new_addslashes($item);
                $image_text_msg[] = $push->mkImageTextMsg(
                    $item['title'],
                    $item['descriptions'],
					appurl('library/'.$library['id'].'/'.$k),
                    fillurl($item['img']), "");
            }
        }
        //发给这个关注的用户
        $biz_content = $push->mkImageTextBizContent($M['openid'], $image_text_msg);
        $biz_content = object_array($push->sendRequest($biz_content));
        $rcode = value($biz_content, 'alipay_mobile_public_message_custom_send_response|code');
        if ($rcode != '200'){
            db_update(tableal('message'), array('err'=>'错误:'.$this->error_code($rcode)), array('id'=>$mid));
        }
        return $biz_content;
    }

	/**
	 * 发送 图片、语音、视频
	 * @param array $M
	 * @return array|bool|string
	 */
	public function sendmedia($M = array())
	{
		global $_A;
		if (empty($M['openid']) || empty($M['alid'])) {
			return false;
		}
		if ($_A['al']['id'] == $M['alid']) {
			$row = $_A['al'];
		}else{
			$row = db_getone("SELECT * FROM ".table('users_al'), array('id'=>$M['alid']));
		}
		if (empty($row)) {
			return false;
		}
		//
		$mid = $this->savemessage($M, 1);
		//
		require_once 'fuwu/PushMsg.php';
		$push = new PushMsg ();
		$image_text_msg = array();
		$imagetext = new_addslashes(array(
			'title'=>'点击查看'.str_replace(array('image','voice','video'), array('图片','语音','视频'), $M['msgtype']),
			'desc'=>'',
			'url'=>appurl("system/showmedia/")."&type=".$M['msgtype']."&value=".urlencode($M['text'])
		));
		$image_text_msg[] = $push->mkImageTextMsg(
			$imagetext['title'],
			$imagetext['desc'],
			$imagetext['url'],
			"", "");
		//发给这个关注的用户
		$biz_content = $push->mkImageTextBizContent($M['openid'], $image_text_msg);
		$biz_content = object_array($push->sendRequest($biz_content));
		$rcode = value($biz_content, 'alipay_mobile_public_message_custom_send_response|code');
		if ($rcode != '200'){
			db_update(tableal('message'), array('err'=>'错误:'.$this->error_code($rcode)), array('id'=>$mid));
		}
		return $biz_content;
	}

	/**
	 * 通过关键词发送信息
	 * @param array $M
	 * @param string $key
	 * @return bool
	 */
	public function sendkey($M = array(), $key = '')
	{
        global $_A;
		if (empty($M['openid']) || empty($M['alid']) || empty($key)) {
			return false;
		}
        if ($_A['al']['id'] == $M['alid']) {
            $row = $_A['al'];
        }else{
            $row = db_getone("SELECT * FROM ".table('users_al'), array('id'=>$M['alid']));
        }
		if (empty($row)) {
			return false;
		}
		$row['setting'] = string2array($row['setting'], true);
		//
        $content = array();
		if ($key == "::关注"){
			$content['type'] = $row['setting']['attention']['content']['type'];
			$content['text'] = $row['setting']['attention']['content']['text'];
			$content['material'] = $row['setting']['attention']['content']['material'];
            $content['imagetext'] = $row['setting']['attention']['content']['imagetext'];
		}elseif ($M['msgtype'] == "image"){
			$content['type'] = $row['setting']['imagekey']['content']['type'];
			$content['text'] = $row['setting']['imagekey']['content']['text'];
			$content['material'] = $row['setting']['imagekey']['content']['material'];
        }elseif (substr($key,0,1) == "#" && $M['msgtype'] == "click"){
            $content['type'] = 'text';
            $content['text'] = substr($key, 1);
		}elseif ($M['msgtype'] == "text" || $M['msgtype'] == "click"){
			preg_match_all("/\{素材库ID\:(\d+)\}/s", $key, $matchmate);
			if (isset($matchmate[1][0]) && $matchmate[1][0] > 0) {
				$reply = db_getone("SELECT * FROM ".table('library'), array('alid'=>$row['id'], 'id'=>intval($matchmate[1][0])));
				if (!empty($reply)) {
					$_library = $reply;
					$content['type'] = 'material';
					$content['material'] = $reply['id'];
				}
			}else{
				$wheresql = " WHERE `alid`=".$row['id']." AND `match`=0 AND `key` LIKE '%,".$key.",%' ";
				$reply = db_getone("SELECT * FROM ".table('reply').$wheresql." ORDER BY `inorder` DESC");
				if (empty($reply)) {
					$wheresql = " WHERE `alid`=".$row['id']." AND `match`=1 AND `key` LIKE '%".$key."%' ";
					$reply = db_getone("SELECT * FROM ".table('reply').$wheresql." ORDER BY `inorder` DESC");
				}
				$content = string2array($reply['content'], true);
				$content['type'] = $reply['type'];
			}
			//无合适内容时回复
			if (empty($reply)) {
				if ($row['setting']['nonekey']['status'] != '启用' || defined('_ISSENDCUSTOMNOTICE')) {
					return false;
				}
				$content['type'] = $row['setting']['nonekey']['content']['type'];
				$content['text'] = $row['setting']['nonekey']['content']['text'];
				$content['material'] = $row['setting']['nonekey']['content']['material'];
                $content['imagetext'] = $row['setting']['nonekey']['content']['imagetext'];
			}
		}else{
			return false;
		}
		//
		$push = new PushMsg ();
        $M['msgtype'] = $content['type'];
		//文本
		if ($content['type'] == 'text') {
			$M['text'] = $content['text'];
			$this->savemessage($M, 1);
			//
			$text_msg = $push->mkTextMsg(new_addslashes($content['text']));
			$biz_content = $push->mkTextBizContent($M['openid'], $text_msg);
			$biz_content = iconv("UTF-8", "GBK//IGNORE", $biz_content);
			return $push->sendRequest ($biz_content);
		}
        //图像文本
        if ($content['type'] == 'imagetext') {
            if (!isset($content['imagetext'])) {
                return false;
            }
			if (empty($content['imagetext']['url']) && isset($reply['module'])) {
				if ($reply['do']) {
					$content['imagetext']['url'] = appurl($reply['module'].'/'.$reply['do'].'/',
						array('from_user' => base64_encode(authcode($M['openid'], 'ENCODE'))));
				}else{
					$content['imagetext']['url'] = appurl($reply['module'].'/welcome/',
						array('rid'=>$reply['id'], 'from_user' => base64_encode(authcode($M['openid'], 'ENCODE'))));
				}
			}
            $M['text'] = array2string($content['imagetext']);
            $this->savemessage($M, 1);
            //
            $image_text_msg = array();
			$imagetext = new_addslashes($content['imagetext']);
            $image_text_msg[] = $push->mkImageTextMsg(
                $imagetext['title'],
                $imagetext['desc'],
				$imagetext['url'],
                fillurl($imagetext['img']), "");
            //发给这个关注的用户
            $biz_content = $push->mkImageTextBizContent($M['openid'], $image_text_msg);
            return $push->sendRequest($biz_content);
        }
		//广播素材
		if ($content['type'] == 'material') {
			$library = isset($_library)?$_library:db_getone("SELECT * FROM ".table('library'), array('alid'=>$row['id'], 'id'=>intval($content['material'])));
			if (empty($library)) {
				return false;
			}
			$M['text'] = $content['material'];
			$this->savemessage($M, 1);
			//
			$image_text_msg = array();
			if ($library['type'] == 'onlyimg') {
                $library = new_addslashes($library);
				$image_text_msg[] = $push->mkImageTextMsg(
					$library['title'],
					$library['descriptions'],
					appurl('library/'.$library['id']),
					fillurl($library['img']), "");
			}elseif ($library['type'] == 'manyimg') {
				$libset = string2array($library['setting']);
				foreach($libset AS $k=>$item) {
                    $item = new_addslashes($item);
					$image_text_msg[] = $push->mkImageTextMsg(
						$item['title'],
						$item['descriptions'],
						appurl('library/'.$library['id'].'/'.$k),
						fillurl($item['img']), "");
				}
			}
			//发给这个关注的用户
			$biz_content = $push->mkImageTextBizContent($M['openid'], $image_text_msg);
			return $push->sendRequest($biz_content);
		}
		//图片、语音、视频
		if (in_array($content['type'], array('image','voice','video')) && $content[$content['type']]) {
			$M['text'] = $content[$content['type']];
			$this->savemessage($M, 1);
			//
			$image_text_msg = array();
			$imagetext = new_addslashes(array(
				'title'=>'点击查看'.str_replace(array('image','voice','video'), array('图片','语音','视频'), $content['type']),
				'desc'=>'',
				'url'=>appurl("system/showmedia/")."&type=".$content['type']."&value=".urlencode($content[$content['type']])
			));
			$image_text_msg[] = $push->mkImageTextMsg(
				$imagetext['title'],
				$imagetext['desc'],
				$imagetext['url'],
				"", "");
			//发给这个关注的用户
			$biz_content = $push->mkImageTextBizContent($M['openid'], $image_text_msg);
			return $push->sendRequest($biz_content);
		}
	}

	/**
	 * 从模块发送文本信息
	 * @param string $content
	 * @param bool $isret
	 * @return Ambigous|string
	 */
	public function respText($content = '', $isret = false)
	{
		global $_A;
		if (empty($content)) return 'Invaild value';
		if (!defined('_ISPROCESSOR')) define('_ISPROCESSOR', true);
		$_A['M']['text'] = $content;
		$_A['M']['msgtype'] = 'text';
		$this->savemessage($_A['M'], 1);
		//
		$content = str_replace("\r\n", "\n", $content);
		$push = new PushMsg ();
		$text_msg = $push->mkTextMsg(new_addslashes($content));
		$biz_content = $push->mkTextBizContent($_A['M']['openid'], $text_msg);
		$biz_content = iconv("UTF-8", "GBK//IGNORE", $biz_content);
		$Request = $push->sendRequest ($biz_content);
		if ($isret) {
			return $Request;
		}else{
			exit();
		}
	}

	/**
	 * 从模块发送(图片、语音、视频)信息
	 * @param string $content   资源地址
	 * @param string $type      类型 （image）、语音（voice）、视频（video）
	 * @param string $tis       上传素材提示语(服务窗无用)
	 * @param bool $isret
	 * @return array|mixed|string
	 */
	public function respMedia($content = '', $type = '',  $tis = '', $isret = false)
	{
		global $_A;
		if (empty($content)) return 'Invaild value';
		if (!defined('_ISPROCESSOR')) define('_ISPROCESSOR', true);
		if (!in_array($type, array('image', 'voice', 'video'))) return 'Err value';
		$_A['M']['text'] = $content;
		$_A['M']['msgtype'] = $type;
		$this->savemessage($_A['M'], 1);
		//
		$imagetext = new_addslashes(array(
			'title'=>'点击查看'.str_replace(array('image','voice','video'), array('图片','语音','视频'), $type),
			'desc'=>'',
			'url'=>appurl("system/showmedia/")."&type=".$type."&value=".urlencode($content)
		));
		$push = new PushMsg ();
		$image_text_msg = array();
		$image_text_msg[] = $push->mkImageTextMsg(
			$imagetext['title'],
			$imagetext['desc'],
			$imagetext['url'],
			"", "");
		$biz_content = $push->mkImageTextBizContent($_A['M']['openid'], $image_text_msg);
		$Request = $push->sendRequest($biz_content);
		if ($isret) {
			return $Request;
		}else{
			exit();
		}
	}

    /**
     * 从模块发送图文信息
     * @param array $arr
     * @param bool $isret
     * @return string
     */
    public function respNews($arr = array(), $isret = false)
	{
		global $_A;
		if (empty($arr)) return 'Invaild value';
		$arr = new_addslashes($arr);
		//
		if (!defined('_ISPROCESSOR')) define('_ISPROCESSOR', true);
		$_A['M']['text'] = array2string($arr);
		$_A['M']['msgtype'] = 'imagetext';
		$this->savemessage($_A['M'], 1);
		//
		$push = new PushMsg ();
		$biz_content = $push->mkImageTextBizContent($_A['M']['openid'], $this->newshandle($arr));
        $Request = $push->sendRequest($biz_content);
        if ($isret) {
            return $Request;
        }else{
            exit();
        }
	}

	/**
	 * 格式化图文信息
	 * @param $data
	 * @return array
	 */
	public function newshandle($data)
	{
		$array = $data;
		foreach($array AS $key=>$val) {
			if (!is_array($val)) {
				unset($array[$key]);
			}
		}
		if (empty($array)) {
			$array = array(0=>$data);
		}else{
			$array = $data;
		}
		$push = new PushMsg ();
		$image_text_msg = array();
		foreach($array AS $item) {
			$imagetext = new_addslashes(array(
				'title'=>$item['title'],
				'desc'=>$item['desc'],
				'url'=>$item['url'],
				'img'=>$item['img']
			));
			$image_text_msg[] = $push->mkImageTextMsg(
				$imagetext['title'],
				$imagetext['desc'],
				$imagetext['url'],
				$imagetext['img'],
				"");
		}
		return $image_text_msg;
	}

    /**
     * 处理到每个模块
     */
    public function processor()
    {
        global $_A;
        static $processorcs = array();
        if (isset($this->data['alifunction'])) {
            $function = $this->data['alifunction'];
        }else{
            $row = db_getone("SELECT function FROM ".table('users_al'), array('id'=>intval($_A['al']['id'])));
			$function = string2array($row['function']);
			$this->data['alifunction'] = $function;
        }
        if ($function) {
			$tempmodule = $_A['module'];
			$inorder = array();
			foreach ($function as $key => $val) {
				$inorder[$key] = array($val['default'], ($tempmodule==$val['title_en'])?1:0);
			}
			array_multisort($inorder, SORT_DESC, $function);
            foreach($function AS $item) {
                $a = $item['title_en'];
				$_A['module'] = $a;
                $processor = FCPATH.'addons/'.$a.'/processor.php';
                if (!isset($processorcs[$a])) {
                    if (file_exists($processor)) {
                        get_instance()->base->inc($processor);
						$classname = "ESP_".ucfirst($a);
						if (!class_exists($classname)) {
							$classname = "ES_Processor_".ucfirst($a);
							if (!class_exists($classname)) {
								continue;
							}
						}
                        $es_site = new $classname();
						if (method_exists($es_site, 'respond')) {
							$es_site->respond('alipay');
						}
                        if (method_exists($es_site, 'alipayrespond')) {
                            $es_site->alipayrespond();
                        }
                        $processorcs[$a] = true;
                    }else{
                        $processorcs[$a] = false;
                    }
                }
            }
			$_A['module'] = $tempmodule;
        }
    }

	/**
	 * 添加菜单
	 */
	public function addmenu($data)
	{
		$paramsArray = array (
			'method' => "alipay.mobile.public.menu.add",
			'biz_content' => json_encode($data),
			'charset' => $this->config('charset'),
			'sign_type' => 'RSA',
			'app_id' => $this->config('app_id'),
			'timestamp' => date ( 'Y-m-d H:i:s', SYS_TIME)
		);
		$as = new AlipaySign ();
		$sign = $as->sign_request ($paramsArray, $this->config('merchant_private_key_file'));
		$paramsArray['sign'] = $sign;
		$aaa = HttpRequest::sendPostRequst ($this->config('gatewayUrl'), $paramsArray );
		$text = iconv("GBK", "UTF-8//IGNORE", $aaa);
		$text = json_decode(characet($text), true);
		//
		$response = value($text, 'alipay_mobile_public_menu_add_response', true);
		if (empty($response)) {
			$response = value($text, 'error_response', true);
		}
		if (value($response,'code') == '11013') {
			$response = $this->upmenu($data);
		}
		return $response;
	}

	/**
	 * 更新菜单
	 */
	public function upmenu($data)
	{
		$paramsArray = array (
			'method' => "alipay.mobile.public.menu.update",
			'biz_content' => json_encode($data),
			'charset' => $this->config('charset'),
			'sign_type' => 'RSA',
			'app_id' => $this->config('app_id'),
			'timestamp' => date ( 'Y-m-d H:i:s', SYS_TIME)
		);
		$as = new AlipaySign ();
		$sign = $as->sign_request ($paramsArray, $this->config('merchant_private_key_file'));
		$paramsArray['sign'] = $sign;
		$aaa = HttpRequest::sendPostRequst ($this->config('gatewayUrl'), $paramsArray );
		$text = iconv("GBK", "UTF-8//IGNORE", $aaa);
		$text = json_decode(characet($text), true);
		//
		$response = value($text, 'alipay_mobile_public_menu_update_response', true);
		if (empty($response)) {
			$response = value($text, 'error_response', true);
		}
		return $response;
	}

	/**
	 * 获取菜单
	 */
	public function getmenu()
	{
		$paramsArray = array (
			'method' => "alipay.mobile.public.menu.get",
			'charset' => $this->config('charset'),
			'sign_type' => 'RSA',
			'app_id' => $this->config('app_id'),
			'timestamp' => date ( 'Y-m-d H:i:s', SYS_TIME)
		);
		$as = new AlipaySign ();
		$sign = $as->sign_request ($paramsArray, $this->config('merchant_private_key_file'));
		$paramsArray['sign'] = $sign;
		$aaa = HttpRequest::sendPostRequst ($this->config('gatewayUrl'), $paramsArray );
		$text = iconv("GBK", "UTF-8//IGNORE", $aaa);
		$text = json_decode(characet($text), true);
		//
		$response = value($text, 'alipay_mobile_public_menu_get_response', true);
		if (empty($response)) {
			$response = value($text, 'error_response', true);
		}
		return $response;
	}

	/**
	 * 发送客服信息
	 * @param $data
	 * @return array|mixed|string
	 */
	function sendCustomNotice($data) {
		global $_A;
		if(empty($data)) {
			return error(-1, '参数错误');
		}
		$this->setting($_A['al']['id']);
		require_once 'fuwu/PushMsg.php';
		$push = new PushMsg ();
		$return_msg = $push->sendMsgRequest(json_encode($data));
		$result = iconv("GBK", "UTF-8//IGNORE", $return_msg);
		$result = json_decode(characet($result), true);
		$response = value($result, 'alipay_mobile_public_message_custom_send_response', true);
		if (empty($response)) {
			$error_response = value($result, 'error_response', true);
			return error(-1, "访问服务窗接口失败, 错误: ".$error_response['msg'].$error_response['sub_msg']);
		} elseif($response['code'] != "200") {
			return error(-1, "访问之腐败接口错误, 错误代码: {$result['code']}, 错误信息: {$result['msg']},错误详情：{$this->error_code($result['code'])}");
		}
		if (!defined('_ISSENDCUSTOMNOTICE')) define('_ISSENDCUSTOMNOTICE', true);
		return $result;
	}


	/** **********************************************************************************************/
	/** **********************************************************************************************/
	/** **********************************************************************************************/

	public function error_code($code) {
		$errors = @include "fuwu/errmsg.php";
		$code = strval($code);
		if($errors[$code]) {
			return $errors[$code];
		} else {
			return '未知错误'.$code;
		}
	}

	public function mkAckMsg($toUserId) {
		$as = new AlipaySign ();
		$response_xml = "<XML><ToUserId><![CDATA[" . $toUserId . "]]></ToUserId><AppId><![CDATA[".$this->config('app_id')."]]></AppId><CreateTime>".SYS_TIME. "</CreateTime><MsgType><![CDATA[ack]]></MsgType></XML>";

		$return_xml = $as->sign_response ($response_xml, $this->config('charset'), $this->config('merchant_private_key_file'));
		writeLog ("response_xml: " . $return_xml );
		return $return_xml;
	}

	/**
	 * 直接获取xml中某个结点的内容
	 * @param $xml
	 * @param $node
	 * @return string
	 */
    public function getNode($xml, $node) {
        $xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>" . $xml;
        $dom = new DOMDocument ( "1.0", "utf-8" );
        $dom->loadXML ( $xml );
        $event_type = $dom->getElementsByTagName ( $node );
		return ($event_type->item ( 0 ))?trim ( $event_type->item ( 0 )->nodeValue ):'';
    }

	public function db()
	{
		return $this->data['ddb'];
	}

	public function config($n = '')
	{
		if (empty($n)) {
			return $GLOBALS['Fconfig'];
		}
		return isset($GLOBALS['Fconfig'][$n])?$GLOBALS['Fconfig'][$n]:'';
	}

	public function setting($alid = 0, $b = array(), $ddb = null)
	{
		global $_A;
		if (!isset($_A['u'])) $_A['u'] = array();
		if (!isset($_A['f'])) $_A['f'] = array();
		if (!isset($_A['uf'])) $_A['uf'] = array();
		if (!isset($_A['al'])) $_A['al'] = array();
		$this->data['id'] = $alid;
		$this->data['arr'] = $b;
		$this->data['ddb'] = $ddb;
		if (empty($ddb)) {
			$CI =& get_instance();
			$this->data['ddb'] = $CI->ddb;
		}
		if (isset($_A['al']['id']) && $_A['al']['id'] == $this->data['id']) {
			$row = $_A['al'];
            if (isset($GLOBALS['al_function']) && $GLOBALS['al_function']) {
                $row['function'] = $GLOBALS['al_function'];
                $this->data['alifunction'] = $row['function'];
                unset($row['function']);
            }
		}else{
			$row = db_getone("SELECT * FROM ".table('users_al'), array('id'=>intval($this->data['id'])));
			if ($row) {
				$this->data['alifunction'] = string2array($row['function']);
				unset($row['function']);
			}
		}
		if ($row) {
			$GLOBALS['Fconfig']['gatewayUrl'] = $row['al_gateway'];
			$GLOBALS['Fconfig']['app_id'] = $row['al_appid'];
			$GLOBALS['Fconfig']['al_rsa'] = $row['al_rsa'];
			$GLOBALS['Fconfig']['al_key'] = $row['al_key'];
			//
			$row['setting'] = string2array($row['setting']);
			//
			if (isset($_A['u']['userid']) && $_A['u']['userid'] == $row['userid']) {
				$_user = $_A['u'];
			}else{
				$_user = db_getone("SELECT * FROM ".table('users'), array('userid'=>$row['userid']));
			}
			if ($_user) {
				$_A['u'] = $_user;
				$_A['userid'] = $_user['userid'];
				$_A['username'] = $_user['username'];
                unset($_A['u']['regsetting']);
			}
		}
		$this->data['ali'] = $row;
        //
        unset($row['payment']);
        $_A['al'] = $row;
    }

	private function lastin($id) {
		if (!defined('_ISEMULATOR')) {
			db_update(table('users_al'), array('al_lastin'=>SYS_TIME), array('id'=>$id));
		}
	}
}
