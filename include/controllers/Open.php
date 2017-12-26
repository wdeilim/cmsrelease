<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Open extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->library('communication');
		$this->load->library('wxopen');
	}

	public function weixin($a)
	{
		global $_A;
		$syspage = 'weixin_'.$a;
		if (method_exists($this, $syspage)) {
			if (empty($_A['openweixin']['appid'])) {
				die($a.':no');
			}
			$this->$syspage();
		}else{
			die($a.':err');
		}
	}

	/**
	 * 发起授权、授权回调
	 */
	public function weixin_auth()
	{
		global $_A,$_GPC;
        $this->load->model('user');
        $this->user->islogin();
        //
		$auth_code = @$_GPC['auth_code'] ?: '';
		$auth_code || $this->weixin_302();
		//
		$atoken = $this->weixin_get_authorizer_token($auth_code);
		$atoken || $this->weixin_302();
		$this->wxopen->save_refresh($atoken['authorizer_appid'], $atoken['authorizer_refresh_token']);
		
		//获取授权方的账户信息
		$info = $this->weixin_get_authorizer_info($atoken['authorizer_appid']);
        $authinfo = $info['authorizer_info'];
        $authinfo || $this->weixin_302();
		//
		if ($authinfo && !empty($authinfo['user_name'])) {
            $service_type_info = $authinfo['service_type_info']['id'];
            $verify_type_info = $authinfo['verify_type_info']['id'];
            if ($verify_type_info == 0) {
                $wx_level = ($service_type_info == 2)?4:3;
            }else{
                $wx_level = ($service_type_info == 2)?2:1;
            }
            $usersal = db_getone(table('users_al'), array('wx_appid'=>$atoken['authorizer_appid']));
            if (empty($usersal)) {
                $user = $this->user->getuser();
                $qrcode_url = $authinfo['qrcode_url'];
                if ($qrcode_url) {
                    $qrcode_content = $this->communication->ihttp_request($qrcode_url);
                    if (!is_error($qrcode_content) && !empty($qrcode_content['content'])) {
                        $_path = "uploadfiles/users/".$user['userid']."/images/".date("Y/m/");
                        make_dir(FCPATH.$_path);
                        $_path.= $authinfo['user_name']?md5($authinfo['user_name']):SYS_TIME;
                        $_path.= '.jpg';
                        file_put_contents(FCPATH.$_path, $qrcode_content['content']);
                        $qrcode_img = $_path;
                    }
                }
                $_arr = array(
                    'al_name' => '',
                    'al_appid' => '',
                    'al_gateway' => 'https://openapi.alipay.com/gateway.do',
                    'al_rsa' => '',
                    'al_key' => '',
                    'al_qrcode' => '',
                    'wx_name' => $authinfo['nick_name'],
                    'wx_appid' => $atoken['authorizer_appid'],
                    'wx_secret' => '',
                    'wx_token' => generate_password(10),
                    'wx_aeskey' => generate_password(43),
                    'wx_level' => $wx_level,
                    'wx_suburl' => '',
                    'wx_qrcode' => isset($qrcode_img)?$qrcode_img:'',
                    'linkaddr' => '',
                    'payment' => '',
                    'setting' => array2string(array(
						'openweixin'=>$_A['openweixin']['appid'],
						'openweixin_expires'=>SYS_TIME + 2592000
					)),
                    'userid' => $user['userid'],
                    'username' => $user['username'],
                    'companyname' => $user['companyname'],
                    'function' => '',
                    'indate' => SYS_TIME,
                    'inip' => ONLINE_IP
                );
                $alid = db_insert(table('users_al'), $_arr, true);
                if ($alid){
                    $frow = db_getall("SELECT id FROM ".table('functions')." WHERE `default`=1 ORDER BY `inorder`");
                    foreach ($frow AS $fitem) {
                        $farr = array();
                        $farr['userid'] = $user['userid'];
                        $farr['fid'] = $fitem['id'];
                        $farr['alid'] = $alid;
                        $farr['al_name'] = $_arr['al_name'];
                        $farr['wx_name'] = $_arr['wx_name'];
                        $farr['indate'] = SYS_TIME;
                        $farr['enddate'] = 0;
                        $this->ddb->insert(table('users_functions'), $farr);
                    }
                    $this->user->handle_function();
                }else{
                    message('授权失败', '授权失败：系统繁忙请稍后再试！', -1);
                }
            }else{
				$alsetting = string2array($usersal['setting']);
				$alsetting['openweixin'] = $_A['openweixin']['appid'];
				$alsetting['openweixin_expires'] = SYS_TIME + 2592000;
				db_update(table('users_al'), array('setting'=>array2string($alsetting)), array('id'=>$usersal['id']));
			}
            //
            message('授权成功', '公众号: '.$authinfo['nick_name'].'授权成功！', -1);
		}else{
            message('授权失败', '授权失败！', -1);
		}
		exit();
	}

	/**
	 * 公众号消息与事件接收URL
	 */
	public function weixin_exp()
	{
		global $_A;
        $this->load->model('user');
        $this->user->islogin();
        //
		$ticket = $this->wxopen->get_ticket();
		if (empty($ticket)) {
			echo '<div style="text-align:center;margin:100px auto;font-size:14px;">需要全网发布后才可以添加，如果已经发布请10分钟后再试！</div>'; exit();
		}
		$precode = $this->weixin_get_pre_authcode();
		$redirect = urlencode($_A['url'][2].'auth/');
		$url = 'https://mp.weixin.qq.com/cgi-bin/componentloginpage?component_appid=%s&pre_auth_code=%s&redirect_uri=%s';
		$url = sprintf($url, $_A['openweixin']['appid'], $precode, $redirect);
		gourl($url);
	}

	/**
	 * 接收授权事件
	 */
	public function weixin_receive()
	{
		global $_A,$_GPC;
		$post_xml = isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : file_get_contents("php://input");
		//
		$post_obj = json_decode(xml2json($post_xml), true);
		$appid = $post_obj['AppId'];
		$msg_sign = $_GPC['msg_signature'];
		$times = $_GPC['timestamp'];
		$nonce = $_GPC['nonce'];
		//
		require_once dirname(dirname(__FILE__))."/libraries/weixin/WXBizMsgCrypt.php";
		$pc = new WXBizMsgCrypt($_A['openweixin']['token'], $_A['openweixin']['key'], $_A['openweixin']['appid']);
		$code = $pc->decryptMsg($msg_sign, $times, $nonce, $post_xml, $post_xml);
		if ($code) { echo $code; exit(); }
		$decrypt_obj = json_decode(xml2json($post_xml), true);
		//
		if ($decrypt_obj['InfoType'] == 'authorized') {
			//授权
			$appname = $decrypt_obj['AuthorizerAppid'];
			$usersal = db_getone(table('users_al'), array('wx_appid'=>$decrypt_obj['AuthorizerAppid']));
			if ($usersal) {
				$appname = $usersal['wx_name'];
				$alsetting = string2array($usersal['setting']);
				$alsetting['openweixin'] = $appid;
				$alsetting['openweixin_expires'] = SYS_TIME + 2592000;
				db_update(table('users_al'), array('setting'=>array2string($alsetting)), array('id'=>$usersal['id']));
			}
			//
			$openweixin = $this->wxopen->get_openweixin();
			$openweixin['applist'][$decrypt_obj['AuthorizerAppid']] = $appname;
            $this->wxopen->save_openweixin($openweixin);
		}elseif ($decrypt_obj['InfoType'] == 'unauthorized') {
			//取消授权
			$usersal = db_getone(table('users_al'), array('wx_appid'=>$decrypt_obj['AuthorizerAppid']));
			if ($usersal) {
				$alsetting = string2array($usersal['setting']);
				$alsetting['openweixin'] = 0;
				$alsetting['openweixin_expires'] = SYS_TIME;
				db_update(table('users_al'), array('setting'=>array2string($alsetting)), array('id'=>$usersal['id']));
			}
			//
			$openweixin = $this->wxopen->get_openweixin();
			unset($openweixin['applist'][$decrypt_obj['AuthorizerAppid']]);
            $this->wxopen->save_openweixin($openweixin);
		}
		$ticket = $decrypt_obj['ComponentVerifyTicket'];
		$ret = $ticket && $this->wxopen->save_ticket($ticket);
		if ($ret) {
			die('success');
		}else{
			var_export($ticket);
			var_export($ret);
		}
	}

	/**
	 * 接收消息与事件
	 */
	public function weixin_callback()
	{
		global $_A,$_GPC;
		require_once dirname(dirname(__FILE__))."/libraries/weixin/WXBizMsgCrypt.php";
		$post_xml = isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : file_get_contents("php://input");

		$msg_sign = $_GPC['msg_signature'];
		$times = $_GPC['timestamp'];
		$nonce = $_GPC['nonce'];
		$pc = new WXBizMsgCrypt($_A['openweixin']['token'], $_A['openweixin']['key'], $_A['openweixin']['appid']);
		$code = $pc->decryptMsg($msg_sign, $times, $nonce, $post_xml, $post_xml);
		if ($code) { echo $code; exit(); }
		$post_obj = json_decode(xml2json($post_xml), true);
		if ($_A['segments'][4] == 'wx570bc396a51b8ff8') {
			$response = array(
				'ToUserName' => $post_obj['FromUserName'],
				'FromUserName' => $post_obj['ToUserName'],
				'CreateTime' => SYS_TIME,
				'MsgId' => SYS_TIME,
				'MsgType' => 'text',
			);
			switch ($post_obj['MsgType']) {
				case 'text':
					if ($post_obj['Content'] == 'TESTCOMPONENT_MSG_TYPE_TEXT') {
						$response['Content'] = 'TESTCOMPONENT_MSG_TYPE_TEXT_callback';
					}else if (strexists($post_obj['Content'], 'QUERY_AUTH_CODE')) {
						$authcode = get_subto($post_obj['Content'], ':');
						$auth_info = $this->weixin_get_authorizer_token($authcode);
						$url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=".$auth_info['authorizer_access_token'];
						$data = array(
							'touser' => $post_obj['FromUserName'],
							'msgtype' => 'text',
							'text' => array('content' => $authcode.'_from_api'),
						);
						$this->communication->ihttp_date($url, $data);
						exit();
					}
					break;
				case 'event':
					$response['Content'] = $post_obj['Event'].'from_callback';
					break;
			}
			if ($response) {
				$xml = array(
					'Nonce' => $nonce,
					'TimeStamp' => $times,
					'Encrypt' => $this->wxopen->aes_encode(
                        $this->wxopen->array2xml($response),
                        $_A['openweixin']['key'], $_A['openweixin']['appid']
                    ),
				);
				$signature = array($xml['Encrypt'], $_A['openweixin']['token'], $times, $nonce);
				sort($signature, SORT_STRING);
				$signature = implode($signature);
				$xml['MsgSignature'] = sha1($signature);
				echo $this->wxopen->array2xml($xml);
			}
		}elseif ($_A['segments'][4]) {
			$usersal = db_getone(table('users_al'), array('wx_appid'=>$_A['segments'][4]));
			if ($usersal) {
				$alid = intval($usersal['id']);
				$this->load->library('wx');
				$this->wx->setting($alid, array(), $this->ddb);
				$this->wx->Message($post_obj); exit();
			}
		}
		exit();
	}

	/**
	 * 使用授权码换取公众号的授权信息
	 * @param $authcode
	 * @return bool|mixed
	 */
	private function weixin_get_authorizer_token($authcode)
	{
		global $_A;
		static $info = array();
		$key = md5(__FUNCTION__.$authcode);
		if (isset($info[$key])) {
			return $info[$key];
		}
		$token = $this->wxopen->get_token();
		if (!$token) {
			return false;
		}
		$url = 'https://api.weixin.qq.com/cgi-bin/component/api_query_auth?component_access_token='.$token;
		$arg = array(
			'component_appid' => $_A['openweixin']['appid'],
			'authorization_code' => $authcode,
		);
		$req = $this->communication->ihttp_date($url, $arg);
		$ret = @json_decode($req['content'], true);
		if ($ret && !empty($ret['authorization_info'])) {
			$info[$key] = $ret['authorization_info'];
			return $info[$key];
		}
		return false;
	}

	/**
	 * 获取授权方的账户信息
	 * @param $appid
	 * @return bool
	 */
	private function weixin_get_authorizer_info($appid)
	{
		global $_A;
		$token = $this->wxopen->get_token();
		if (!$token) {
			return false;
		}
		$url = 'https://api.weixin.qq.com/cgi-bin/component/api_get_authorizer_info?component_access_token='.$token;
		$arg = array(
			'component_appid' => $_A['openweixin']['appid'],
			'authorizer_appid' => $appid,
		);
		$req = $this->communication->ihttp_date($url, $arg);
		$ret = @json_decode($req['content'], true);
		if ($ret) {
			return $ret;
		}
		return false;
	}

	/**
	 * 获取授权AUTHCODE
	 * @return bool|null
	 */
	private function weixin_get_pre_authcode()
	{
		global $_A;
		static $code = NULL;
		if (!is_null($code)) {
			return $code;
		}
		$token = $this->wxopen->get_token();
		if (!$token) {
			return false;
		}
		$url = 'https://api.weixin.qq.com/cgi-bin/component/api_create_preauthcode?component_access_token='.$token;
		$arg = array(
			'component_appid' => $_A['openweixin']['appid'],
		);
		$req = $this->communication->ihttp_date($url, $arg);
		$ret = @json_decode($req['content'], true);
		if ($ret && !empty($ret['pre_auth_code'])) {
			$code = $ret['pre_auth_code'];
			return $code;
		}
		return false;
	}

	/**
	 * 发起授权
	 */
	private function weixin_302()
	{
		global $_A;
		gourl($_A['url'][2].'exp/');
	}
}
