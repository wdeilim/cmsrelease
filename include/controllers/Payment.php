<?php
defined('BASEPATH') OR exit('No direct script access allowed');
error_reporting(0);

define('ALIPAY_GATEWAY', 'http://wappaygw.alipay.com/service/rest.htm');
/**
 * Class Payment 支付接口
 */
class Payment extends CI_Controller {
    public $params = '';

    public function __construct()
    {
        parent::__construct();
    }


    public function _remap($_a = null, $_arr = array())
    {
        global $_GPC,$_A;
        if ($_a == 'alipay') {
            //支付宝支付返回
            $_mod = $_arr[0];
            if (in_array($_mod, array('return','notify','merchant'))) {
                $_mod = 'alipay_'.$_mod;
                $this->$_mod(); return true;
            }
        }
		if ($_a == 'weixin') {
			//微信返回
			$_mod = $_arr[0];
			if (in_array($_mod, array('notify'))) {
				$_mod = 'weixin_'.$_mod;
				$this->$_mod(); return true;
			}
		}
        //
        $params = @json_decode(base64_decode($_GPC['params']), true);
        $moduels = array();
        $functions = db_getall(table('functions'));
        foreach($functions AS $item) {
            $moduels[$item['title_en']] = $item;
        }
        if(empty($params) || !array_key_exists($params['module'], $moduels)) {
            message('访问错误.');
        }
        $this->params = $params;
        $_A['module'] = $params['module'];
        //
        $this->load->model('user');
        $this->user->funs('app');
        $this->load->library('communication');
        //
        if (empty($_A['vip']['id'])) message('访问错误-pay-vip');
        $params = $this->params;
        //
        $do = $this->uri->segment(2);
        $dos = array();
        $dos[] = 'credit';
        $dos[] = 'alipay';
        $dos[] = 'weixin';
        $type = in_array($do, $dos) ? $do : '';
        if(empty($type)) {
            message(null,'支付方式错误,请联系商家！');
        }
        //
        $pars  = array();
        $pars['alid'] = $_A['al']['id'];
        $pars['module'] = $params['module'];
        $pars['tid'] = $params['tid'];
        $log = db_getone(table('core_paylog'), $pars);
        if(!empty($log) && $log['status'] != '0') {
            if ($type != 'weixin' || $_GPC['done'] != '1') {
				message(null, '这个订单已经支付成功, 不需要重复支付.');
			}
        }
        if($log && $log['fee'] != $params['fee']) {
            db_delete(table('core_paylog'), array('plid' => $log['plid']));
            $log = null;
        }
        if(empty($log)) {
            $fee = $params['fee'];
            $record = array();
            $record['alid'] = $_A['al']['id'];
            $record['openid'] = $_A['fans']['openid'];
            $record['module'] = $params['module'];
            $record['type'] = $type;
            $record['tid'] = $params['tid'];
            $record['tag'] = $params['tag'];
            $record['fee'] = $fee;
            $record['status'] = '0';
            $plid = db_insert(table('core_paylog'), $record, true);
            if($plid) {
                $record['plid'] = $plid;
                $log = $record;
            } else {
                message('系统错误, 请稍后重试.');
            }
        }else{
            $plid = $log['plid'];
            if($log['type'] != $type) {
                $record = array();
                $record['type'] = $type;
                db_update(table('core_paylog'), $record, array('plid' => $log['plid']));
            }
        }
        $ps = array();
        $ps['tid'] = $log['plid'];
        $ps['user'] = $_A['fans']['user_name'];
        $ps['fee'] = $log['fee'];
        $ps['title'] = $params['title'];

        //支付宝付款
        if($type == 'alipay') {
            if(!empty($plid)) {
                db_update(table('core_paylog'), array('openid' => $_A['fans']['openid']), array('plid' => $plid));
            }
            $alrow = db_getone(table('users_al'), array('id'=>$_A['al']['id']));
            $alrow['payment'] = string2array($alrow['payment']);
            $ret = $this->alipay_build($ps, $alrow['payment']['alipay']);
            if($ret['url']) {
                gourl($ret['url']);
            }
        }

		//微信支付付款
		if($type == 'weixin') {
			if($_GPC['done'] == '1') {
				//微信支付 成功返回
				$log = db_getone(table('core_paylog'), array('tid'=>$params['tid'], 'module'=>$params['module']));
				if(!empty($log)) {
					if ($log['status'] == '0') {
						db_update(table('core_paylog'), array('status'=>1), array('plid' => $log['plid']));
						if ($log['module'] != "vip") {
							$this->base->apprun('vip');
							ES_Apprun_Vip::money_point(array("openid"=>$log['openid']), $log['fee']);
						}
					}
					//
					$site = $this->createModuleSite($log['module']);
					if(!$this->is_error($site)) {
						$site->alid = $_A['al']['alid'];
						$method = 'payResult';
						if (method_exists($site, $method)) {
							$ret = array();
							$ret['result'] = $log['status'] == '1' ? 'success' : 'failed';
							$ret['type'] = $log['type'];
							$ret['from'] = 'return';
							$ret['tid'] = $log['tid'];
							$ret['user'] = $log['openid'];
							$ret['fee'] = $log['fee'];
							$ret['alid'] = $log['alid'];
							exit($site->$method($ret));
						}
					}
				}
			}
			//
			$alrow = db_getone(table('users_al'), array('id'=>$_A['al']['id']));
			$alrow['payment'] = string2array($alrow['payment']);
			$wechat = $alrow['payment']['weixin'];
			if(empty($wechat)) message(null,"没有设定微信支付参数。");
			$wechat['appid'] = $wechat['appid']?$wechat['appid']:$alrow['wx_appid'];
			$wechat['secret'] = $wechat['secret']?$wechat['secret']:$alrow['wx_secret'];
			$wOpt = $this->wechat_build($ps, $wechat);
			if ($this->is_error($wOpt)) {
				if ($wOpt['message'] == 'invalid out_trade_no') {
					$id = date('YmdH');
					db_update(table('core_paylog'), array('plid' => $id), array('plid' => $log['plid']));
					$this->ddb->query("ALTER TABLE ".table('core_paylog')." auto_increment = ".($id+1).";");
					message(null,"抱歉，发起支付失败，系统已经修复此问题，请重新尝试支付。");
				}
				message(null,"抱歉，发起支付失败，具体原因为：“{$wOpt['errno']}:{$wOpt['message']}”。请及时联系站点管理员。");
				exit();
			}
			tpl('weixinjspay.tpl', get_defined_vars()); exit();
		}

        //余额支付
        if($type == 'credit') {
            if($_A['vip']['money'] < $ps['fee']) {
                message(null, "余额不足以支付, 需要 {$ps['fee']}, 当前 {$_A['vip']['money']}");
            }
            $fee = floatval($ps['fee']);
            $mret = ES_Apprun_Vip::money(array("id"=>$_A['vip']['id']), $fee * -1, $params['tag']);
            if (!$mret['success']) {
                message(null, "付款失败！");
            }
            //
            $pars = array();
            $pars['plid'] = $ps['tid'];
            $log = db_getone(table('core_paylog'), $pars);
            if(!empty($log) && $log['status'] == '0') {
                $record = array();
                $record['status'] = '1';
                db_update(table('core_paylog'), $record, array('plid' => $log['plid']));
                $site = $this->createModuleSite($log['module']);
                if(!$this->is_error($site)) {
                    $site->alid = $_A['al']['id'];
                    $site->inMobile = true;
                    $method = 'payResult';
                    if (method_exists($site, $method)) {
                        $ret = array();
                        $ret['result'] = 'success';
                        $ret['type'] = $log['type'];
                        $ret['from'] = 'return';
                        $ret['tid'] = $log['tid'];
                        $ret['user'] = $log['openid'];
                        $ret['fee'] = $log['fee'];
                        $ret['alid'] = $log['alid'];
                        exit($site->$method($ret));
                    }
                }
            }

        }
    }

	/**
	 * @param $params
	 * @param $wechat
	 * @return array
	 */
	function wechat_build($params, $wechat) {
		global $_A,$_GPC;
		$wechat_openid = $_A['fans']['openid'];
		$get_appid = $_A['al']['setting']['other']['get_appid'];
		$wx_appid = $get_appid?$get_appid:$_A['al']['wx_appid'];
		if ($wechat['appid'] != $wx_appid) {
			//支付微信号与授权不同时将重新授权openid
			$sessionname = 'openid_'.md5($wx_appid.$_A['fans']['openid']);
			$wechat_openid = $this->session->userdata($sessionname);
			if (empty($wechat_openid) || strlen($wechat_openid) < 15) {
				if (isset($_GPC['weixin_oauth2'])) {
					if (!isset($_GPC['code'])) {
						message(null, "WXpay OAuth 2.0授权失败！");
					}
					$this->load->library('communication');
					$url  = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$wechat['appid'];
					$url.= '&secret='.$wechat['secret'].'&code='.$_GPC['code'].'&grant_type=authorization_code';
					$content = $this->communication->ihttp_request($url);
					$_content = isset($content['content'])?json_decode($content['content'], true):'';
					$wechat_openid = value($_content,'openid');
					if (empty($wechat_openid)) {
						message(null, "WXpay OAuth 2.0授权失败 - wx - get - openid！");
					}
					$this->session->set_userdata($sessionname, $wechat_openid);
				}else{
					$_url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$wechat['appid'];
					$_url.= '&redirect_uri='.urlencode(get_link('weixin_oauth2')."&weixin_oauth2=1");
					$_url.= '&response_type=code&scope=snsapi_base&state=STATE#wechat_redirect';
					gourl($_url);
				}
			}
		}
		if (empty($wechat['version']) && !empty($wechat['signkey'])) {
			$wechat['version'] = 1;
		}
		$wOpt = array();
		if ($wechat['version'] == 1) {
			$wOpt['appId'] = $wechat['appid'];
			$wOpt['timeStamp'] = SYS_TIME;
			$wOpt['nonceStr'] = generate_password(8);
			$package = array();
			$package['bank_type'] = 'WX';
			$package['body'] = $params['title'];
			$package['attach'] = $_A['al']['id'];
			$package['partner'] = $wechat['partner'];
			$package['out_trade_no'] = $params['tid'];
			$package['total_fee'] = $params['fee'] * 100;
			$package['fee_type'] = '1';
			$package['notify_url'] = $this->config->site_url('payment/weixin/notify');
			$package['spbill_create_ip'] = ONLINE_IP;
			$package['time_start'] = date('YmdHis', SYS_TIME);
			$package['time_expire'] = date('YmdHis', SYS_TIME + 600);
			$package['input_charset'] = 'UTF-8';
			ksort($package);
			$string1 = '';
			foreach($package as $key => $v) {
				$string1 .= "{$key}={$v}&";
			}
			$string1 .= "key={$wechat['key']}";
			$sign = strtoupper(md5($string1));

			$string2 = '';
			foreach($package as $key => $v) {
				$v = urlencode($v);
				$string2 .= "{$key}={$v}&";
			}
			$string2 .= "sign={$sign}";
			$wOpt['package'] = $string2;

			$string = '';
			$keys = array('appId', 'timeStamp', 'nonceStr', 'package', 'appKey');
			sort($keys);
			foreach($keys as $key) {
				$v = $wOpt[$key];
				if($key == 'appKey') {
					$v = $wechat['signkey'];
				}
				$key = strtolower($key);
				$string .= "{$key}={$v}&";
			}
			$string = rtrim($string, '&');

			$wOpt['signType'] = 'SHA1';
			$wOpt['paySign'] = sha1($string);
			return $wOpt;
		} else {
			$package = array();
			$package['appid'] = $wechat['appid'];
			$package['mch_id'] = $wechat['mchid'];
			$package['nonce_str'] = generate_password(8);
			$package['body'] = $params['title'];
			$package['attach'] = $_A['al']['id'];
			$package['out_trade_no'] = $params['tid'];
			$package['total_fee'] = $params['fee'] * 100;
			$package['spbill_create_ip'] = ONLINE_IP;
			$package['time_start'] = date('YmdHis', SYS_TIME);
			$package['time_expire'] = date('YmdHis', SYS_TIME + 600);
			$package['notify_url'] = $this->config->site_url('payment/weixin/notify');
			$package['trade_type'] = 'JSAPI';
			$package['openid'] = $wechat_openid;
			ksort($package, SORT_STRING);
			$string1 = '';
			foreach($package as $key => $v) {
				$string1 .= "{$key}={$v}&";
			}
			$string1 .= "key={$wechat['apikey']}";
			$package['sign'] = strtoupper(md5($string1));
			$dat = self::array2xml($package);
			$response = $this->communication->ihttp_request('https://api.mch.weixin.qq.com/pay/unifiedorder', $dat);
			if ($this->is_error($response)) {
				return $response;
			}
			$xml = @simplexml_load_string($response['content'], 'SimpleXMLElement', LIBXML_NOCDATA);
			if (strval($xml->return_code) == 'FAIL') {
				return array(
					'errno' => -1,
					'message' => $xml->return_msg,
				);
			}
			if (strval($xml->result_code) == 'FAIL') {
				return array(
					'errno' => -1,
					'message' => strval($xml->err_code).': '.strval($xml->err_code_des),
				);
			}
			$prepayid = $xml->prepay_id;
			$wOpt['appId'] = $wechat['appid'];
			$wOpt['timeStamp'] = SYS_TIME;
			$wOpt['nonceStr'] = generate_password(8);
			$wOpt['package'] = 'prepay_id='.$prepayid;
			$wOpt['signType'] = 'MD5';
			ksort($wOpt, SORT_STRING);
			$string = '';
			foreach($wOpt as $key => $v) {
				$string .= "{$key}={$v}&";
			}
			$string .= "key={$wechat['apikey']}";
			$wOpt['paySign'] = strtoupper(md5($string));
			return $wOpt;
		}
	}

    /**
     * @param $params
     * @param array $alipay
     * @return array
     */
    private function alipay_build($params, $alipay = array())
    {
        $tid = $params['tid'];
        $set = array();
        $set['service'] = 'alipay.wap.trade.create.direct';
        $set['format'] = 'xml';
        $set['v'] = '2.0';
        $set['partner'] = $alipay['partner'];
        $set['req_id'] = $tid;
        $set['sec_id'] = 'MD5';
        $callback = $this->config->site_url('payment/alipay/return');
        $notify = $this->config->site_url('payment/alipay/notify');
        $merchant = $this->config->site_url('payment/alipay/merchant');
        $expire = 10;
        $set['req_data'] = "<direct_trade_create_req><subject>{$params['title']}</subject><out_trade_no>{$tid}</out_trade_no><total_fee>{$params['fee']}</total_fee><seller_account_name>{$alipay['account']}</seller_account_name><call_back_url>{$callback}</call_back_url><notify_url>{$notify}</notify_url><out_user>{$params['user']}</out_user><merchant_url>{$merchant}</merchant_url><pay_expire>{$expire}</pay_expire></direct_trade_create_req>";
        $prepares = array();
        foreach($set as $key => $value) {
            if($key != 'sign') {
                $prepares[] = "{$key}={$value}";
            }
        }
        sort($prepares);
        $string = implode($prepares, '&');
        $string .= $alipay['secret'];
        $set['sign'] = md5($string);
        $response = $this->communication->ihttp_request(ALIPAY_GATEWAY . '?' . http_build_query($set, '', '&'));
        $ret = array();
        @parse_str($response['content'], $ret);
        foreach($ret as &$v) {
            $v = str_replace('\"', '"', $v);
        }
        if(is_array($ret)) {
            if($ret['res_error']) {
                $error = simplexml_load_string($ret['res_error'], 'SimpleXMLElement', LIBXML_NOCDATA);
                if($error instanceof SimpleXMLElement && $error->detail) {
                    message(null, "发生错误, 无法继续支付. 详细错误为: " . strval($error->detail));
                }
            }

            if($ret['partner'] == $set['partner'] && $ret['req_id'] == $set['req_id'] && $ret['sec_id'] == $set['sec_id'] && $ret['service'] == $set['service'] && $ret['v'] == $set['v']) {
                $prepares = array();
                foreach($ret as $key => $value) {
                    if($key != 'sign') {
                        $prepares[] = "{$key}={$value}";
                    }
                }
                sort($prepares);
                $string = implode($prepares, '&');
                $string .= $alipay['secret'];
                if(md5($string) == $ret['sign']) {
                    $obj = simplexml_load_string($ret['res_data'], 'SimpleXMLElement', LIBXML_NOCDATA);
                    if($obj instanceof SimpleXMLElement && $obj->request_token) {
                        $token = strval($obj->request_token);
                        $set = array();
                        $set['service'] = 'alipay.wap.auth.authAndExecute';
                        $set['format'] = 'xml';
                        $set['v'] = '2.0';
                        $set['partner'] = $alipay['partner'];
                        $set['sec_id'] = 'MD5';
                        $set['req_data'] = "<auth_and_execute_req><request_token>{$token}</request_token></auth_and_execute_req>";
                        $prepares = array();
                        foreach($set as $key => $value) {
                            if($key != 'sign') {
                                $prepares[] = "{$key}={$value}";
                            }
                        }
                        sort($prepares);
                        $string = implode($prepares, '&');
                        $string .= $alipay['secret'];
                        $set['sign'] = md5($string);
                        $url = ALIPAY_GATEWAY . '?' . http_build_query($set, '', '&');
                        return array('url' => $url);
                    }
                }
            }
        }
        message(null, '非法访问.');
    }

    /**
     * 支付宝付款 成功返回
     */
    public function alipay_return()
    {
		global $_A;
        if(empty($_GET['out_trade_no'])) {
            exit('request failed.');
        }
        $log = db_getone(table('core_paylog'), array('plid'=>trim($_GET['out_trade_no'])));
		//
		$_A['module'] = $log['module'];
		$_A['al'] = db_getone(table('users_al'),
			array('id'=>intval($log['alid'])));
		$_A['u'] = db_getone(table('users'),
			array('userid'=>intval($_A['al']['userid'])));
		$_A['f'] = db_getone(table('functions'),
			array('title_en'=>$_A['module']));
		$_A['uf'] = db_getone(table('users_functions'),
			array('userid'=>intval($_A['al']['userid']),'fid'=>intval($_A['f']['id']),'alid'=>intval($_A['al']['id'])));
		//
		$_A['al']['payment'] = string2array($_A['al']['payment']);
        $alipay = $_A['al']['payment']['alipay'];
        if(empty($alipay)) {
            exit('request failed...');
        }
        $prepares = array();
        foreach($_GET as $key => $value) {
            if($key != 'sign' && $key != 'sign_type') {
                $prepares[] = "{$key}={$value}";
            }
        }
        sort($prepares);
        $string = implode($prepares, '&');
        $string .= $alipay['secret'];
        $sign = md5($string);
        if($sign == $_GET['sign'] && $_GET['result'] == 'success') {
            if(!empty($log)) {
				if ($log['status'] == '0') {
					db_update(table('core_paylog'), array('status'=>1), array('plid' => $log['plid']));
					if ($log['module'] != "vip") {
						$this->base->apprun('vip');
						ES_Apprun_Vip::money_point(array("openid"=>$log['openid']), $log['fee']);
					}
				}
				//
                $site = $this->createModuleSite($log['module']);
                if(!$this->is_error($site)) {
                    $site->alid = $_A['al']['alid'];
                    $method = 'payResult';
                    if (method_exists($site, $method)) {
                        $ret = array();
                        $ret['result'] = $log['status'] == '1' ? 'success' : 'failed';
                        $ret['type'] = $log['type'];
                        $ret['from'] = 'return';
                        $ret['tid'] = $log['tid'];
                        $ret['user'] = $log['openid'];
                        $ret['fee'] = $log['fee'];
                        $ret['alid'] = $log['alid'];
                        exit($site->$method($ret));
                    }
                }
            }
        }
    }

    /**
     * 支付宝付款 异步通知
     */
    public function alipay_notify()
    {
		global $_A;
        $obj = simplexml_load_string($_POST['notify_data'], 'SimpleXMLElement', LIBXML_NOCDATA);
        if($obj instanceof SimpleXMLElement && $obj->out_trade_no) {
            $out_trade_no = strval($obj->out_trade_no);
            $log = db_getone(table('core_paylog'), array('plid'=>trim($out_trade_no)));
			//
			$_A['module'] = $log['module'];
			$_A['al'] = db_getone(table('users_al'),
				array('id'=>intval($log['alid'])));
			$_A['u'] = db_getone(table('users'),
				array('userid'=>intval($_A['al']['userid'])));
			$_A['f'] = db_getone(table('functions'),
				array('title_en'=>$_A['module']));
			$_A['uf'] = db_getone(table('users_functions'),
				array('userid'=>intval($_A['al']['userid']),'fid'=>intval($_A['f']['id']),'alid'=>intval($_A['al']['id'])));
			//
            $setting = $_A['al']['payment'] = string2array($_A['al']['payment']);
            $alipay = $setting['alipay'];
            if(!empty($alipay)) {
                $string = "service={$_POST['service']}&v={$_POST['v']}&sec_id={$_POST['sec_id']}&notify_data={$_POST['notify_data']}";
                $string .= $alipay['secret'];
                $sign = md5($string);
                if($sign == $_POST['sign']) {
                    if(!empty($log) && $log['status'] == '0') {
						db_update(table('core_paylog'), array('status'=>1), array('plid' => $log['plid']));
						if ($log['module'] != "vip") {
							$this->base->apprun('vip');
							ES_Apprun_Vip::money_point(array("openid"=>$log['openid']), $log['fee']);
						}
						//
                        $site = $this->createModuleSite($log['module']);
                        if(!$this->is_error($site)) {
                            $site->alid = $_A['al']['alid'];
                            $method = 'payResult';
                            if (method_exists($site, $method)) {
                                $ret = array();
                                $ret['result'] = $log['status'] == '1' ? 'success' : 'failed';
                                $ret['type'] = $log['type'];
                                $ret['from'] = 'return';
                                $ret['tid'] = $log['tid'];
                                $ret['user'] = $log['openid'];
                                $ret['fee'] = $log['fee'];
                                $ret['alid'] = $log['alid'];
                                exit($site->$method($ret));
                            }
                        }
                    }
                }
            }
        }
        exit('fail');
    }

    /**
     * 支付宝付款 支付失败
     */
    public function alipay_merchant()
    {
        message(null, '支付失败, 请稍后重试.');
    }

	/**
	 * 微信支付 通知返回
	 */
	public function weixin_notify()
	{
		global $_A;
		$input = file_get_contents('php://input');
		if (!empty($input) && empty($_GET['out_trade_no'])) {
			$obj = simplexml_load_string($input, 'SimpleXMLElement', LIBXML_NOCDATA);
			$data = json_decode(json_encode($obj), true);
			if (empty($data)) {
				exit('fail');
			}
			if ($data['result_code'] != 'SUCCESS' || $data['return_code'] != 'SUCCESS') {
				exit('fail');
			}
			$get = $data;
		} else {
			$get = $_GET;
		}
		$out_trade_no = $get['out_trade_no'];
		$log = db_getone(table('core_paylog'), array('plid'=>trim($out_trade_no)));
		//
		$_A['module'] = $log['module'];
		$_A['al'] = db_getone(table('users_al'),
			array('id'=>intval($log['alid'])));
		$_A['u'] = db_getone(table('users'),
			array('userid'=>intval($_A['al']['userid'])));
		$_A['f'] = db_getone(table('functions'),
			array('title_en'=>$_A['module']));
		$_A['uf'] = db_getone(table('users_functions'),
			array('userid'=>intval($_A['al']['userid']),'fid'=>intval($_A['f']['id']),'alid'=>intval($_A['al']['id'])));
		//
		$setting = $_A['al']['payment'] = string2array($_A['al']['payment']);
		$weixin = $setting['weixin'];
		if(!empty($weixin)) {
			ksort($get);
			$string1 = '';
			foreach($get as $k => $v) {
				if($v != '' && $k != 'sign') {
					$string1 .= "{$k}={$v}&";
				}
			}
			$weixin['signkey'] = ($weixin['version'] == 1) ? $weixin['key'] : $weixin['apikey'];
			$sign = strtoupper(md5($string1 . "key={$weixin['signkey']}"));
			if($sign == $get['sign']) {
				if(!empty($log) && $log['status'] == '0') {
					db_update(table('core_paylog'), array('status'=>1), array('plid' => $log['plid']));
					if ($log['module'] != "vip") {
						$this->base->apprun('vip');
						ES_Apprun_Vip::money_point(array("openid"=>$log['openid']), $log['fee']);
					}
					//
					$site = $this->createModuleSite($log['module']);
					if(!$this->is_error($site)) {
						$site->alid = $_A['al']['alid'];
						$method = 'payResult';
						if (method_exists($site, $method)) {
							$ret = array();
							$ret['result'] = $log['status'] == '1' ? 'success' : 'failed';
							$ret['type'] = $log['type'];
							$ret['from'] = 'return';
							$ret['tid'] = $log['tid'];
							$ret['user'] = $log['openid'];
							$ret['fee'] = $log['fee'];
							$ret['alid'] = $log['alid'];
							$site->$method($ret);
							exit('success');
						}
					}
				}
			}
		}
		exit('fail');
	}

    /**
     * @param $name
     * @return null
     */
    private function createModuleSite($name) {
        global $_A;
        static $file;
        $classname = "Es_{$name}";
        if(!class_exists($classname)) {
            $file = BASE_PATH . "addons/{$name}/site.php";
            if(!is_file($file)) {
                return null;
            }
            require_once $file;
        }
        if(!class_exists($classname)) {
            trigger_error('ModuleSite Definition Class Not Found', E_USER_WARNING);
            return null;
        }
        $o = new $classname();
        $o->alid = $_A['al']['id'];
        $o->modulename = $name;
        $o->__define = $file;
        if($o instanceof CI_Model) {
            return $o;
        } else {
            trigger_error('ModuleReceiver Class Definition Error', E_USER_WARNING);
            return null;
        }
    }

    /**
     * @param $data
     * @return bool
     */
    private function is_error($data) {
        if (empty($data) || !is_array($data) || !array_key_exists('errno', $data) || (array_key_exists('errno', $data) && $data['errno'] == 0)) {
            return false;
        } else {
            return true;
        }
    }

	/**
	 * @param $arr
	 * @param int $level
	 * @return mixed|string
	 */
	private function array2xml($arr, $level = 1) {
		$s = $level == 1 ? "<xml>" : '';
		foreach($arr as $tagname => $value) {
			if (is_numeric($tagname)) {
				$tagname = $value['TagName'];
				unset($value['TagName']);
			}
			if(!is_array($value)) {
				$s .= "<{$tagname}>".(!is_numeric($value) ? '<![CDATA[' : '').$value.(!is_numeric($value) ? ']]>' : '')."</{$tagname}>";
			} else {
				$s .= "<{$tagname}>" . self::array2xml($value, $level + 1)."</{$tagname}>";
			}
		}
		$s = preg_replace("/([\x01-\x08\x0b-\x0c\x0e-\x1f])+/", ' ', $s);
		return $level == 1 ? $s."</xml>" : $s;
	}
}
