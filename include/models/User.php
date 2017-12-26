<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Model {
	var $_type = "";
	var $_user = array();
	var $_data = array();

	function __construct()
	{
		parent::__construct();
		$this->getuser(1);
		$this->functions(1);
		$this->funs();
	}

	public function set_udata($name, $val) {
		$this->session->set_userdata($name, $val);
		set_cookie($name, $val, 86400);
	}

	public function get_udata($name) {
		$val = $this->session->userdata($name);
		if (empty($val)) { $val = get_cookie($name); }
		return $val;
	}

	/**
	 * 是否登录 （客户端） 身份验证
	 */
	public function oauth()
	{
		global $_A;
		if (empty($_A['fans']['openid'])) {
			$this->session->set_userdata('auth:fromurl', get_url());
			$this->auth_login();
		}
	}

	/**
	 * 通过公众号获取粉丝openid等信息 （客户端）
	 * @param string $appid 	公众号的 appid
	 * @param string $secret	公众号的 secret
	 * @param string $scope		是否获取详细信息：snsapi_userinfo是、其他不是
	 * @param bool $iscache		是否允许缓存 默认允许【$scope=snsapi_userinfo 时始终不允许缓存】
	 * @return array|mixed|string
	 */
	public function oauth2($appid, $secret, $scope = 'snsapi_base', $iscache = true)
	{
		global $_A,$_GPC;
		$_scope = (in_array($scope, array('snsapi_userinfo', 'userinfo')) || $scope === true)?'snsapi_userinfo':'snsapi_base';
		$sessionname = 'oauth2_'.md5($appid.$secret.$_scope.$_A['fans']['openid']);
		$wechat_openid = $this->session->userdata($sessionname);
		if (empty($wechat_openid) || strlen($wechat_openid) < 15 || $iscache == false || $_scope == 'snsapi_userinfo') {
			if (isset($_GPC['models_user_oauth2'])) {
				if (!isset($_GPC['code'])) {
					return error(-1, 'User OAuth 2.0授权失败！');
				}
				$this->load->library('communication');
				$url  = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appid;
				$url.= '&secret='.$secret.'&code='.$_GPC['code'].'&grant_type=authorization_code';
				$content = $this->communication->ihttp_request($url);
				$_content = isset($content['content'])?json_decode($content['content'], true):'';
				$wechat_openid = value($_content,'openid');
				if (empty($wechat_openid)) {
					return error(-1, 'User OAuth 2.0授权失败 - get - openid！');
				}
				//
				if ($_scope == 'snsapi_userinfo') {
					$access_token = value($_content, 'access_token');
					$url  = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$wechat_openid.'&lang=zh_CN';
					$content = $this->communication->ihttp_request($url);
					$_conten2 = isset($content['content'])?json_decode($content['content'], true):'';
					if (value($_conten2,'openid')) {
						$_content = $_conten2;
					}
				}
				$this->session->set_userdata($sessionname, $wechat_openid);
				return error(0, $_content);
			}else{
				if (isset($_GET['code']) || isset($_GET['weixin_oauth2'])) {
					$_url = get_link('weixin_oauth2|code|state');
				}else{
					$_url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$appid;
					$_url.= '&redirect_uri='.urlencode(get_link('models_user_oauth2')."&models_user_oauth2=1");
					$_url.= '&response_type=code&scope='.$_scope.'&state=STATE#wechat_redirect';
				}
				gourl($_url); exit();
			}
		}else{
			return error(0, array('openid'=>$wechat_openid));
		}
	}

    /**
     * 获取用户信息 （客户端）
     * @param string $segments
     * @return bool
     */
    public function funs($segments = '')
    {
        global $_A,$_GPC;
		if ($_A['segments'][1] != "app" && $segments != "app") { return false; } //判断是否客户端， 否则返回跳过
        //识别浏览器
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        if (strpos($user_agent, 'AlipayClient') !== false) {
            $_A['browser'] = 'alipay';
        }elseif (strpos($user_agent, 'MicroMessenger') !== false) {
            $_A['browser'] = 'weixin';
        }else{
            $_A['browser'] = 'none';
        }
		$openidname = '__SYS:USERID:'.intval(isset($_A['al']['id'])?$_A['al']['id']:0);
        //链接带有身份信息
        if (isset($_GPC['from_user'])) {
            $_A['openid'] = authcode(base64_decode($_GPC['from_user']));
			$this->set_udata($openidname, $_A['openid']);
			gourl(get_link('from_user'));
        }
		$_A['openid'] = $this->get_udata($openidname);
        //服务窗授权
        if ($_A['browser'] == 'alipay' && $_A['al']['al_appid']) {
			$this->load->library('fuwu');
			$this->fuwu->setting(intval($_A['al']['id']));
			if ($_A['openid']) {
				$_A['fans'] = $this->fuwu->exist_group(array('openid'=>$_A['openid'],'alid'=>$_A['al']['id']), true);
			}
			if (empty($_A['fans'])) {
				if (isset($_GPC['alipay_oauth2'])) {
					if (!isset($_GPC['auth_code'])) {
						message(null, "OAuth 2.0授权失败！");
					}
					$user_info = $this->fuwu->getUserInfo($_GPC['auth_code']);
					$_A['openid'] = $user_info['oauth']['alipay_user_id'];
					if (isset($user_info['error']) && empty($_A['openid'])) {
						message(null, "温馨提示", "错误: ".$user_info['error']['msg'].$user_info['error']['sub_msg']);
					}
					$this->set_udata($openidname, $_A['openid']);
					$M = array(
						'openid'=>$_A['openid'],
						'alid'=>$_A['al']['id'],
						'appid'=>$_A['al']['al_appid']
					);
					if (isset($user_info['info'])) {
						$M['logon_id'] = $user_info['info']['user_id'];
						$M['user_name'] = $user_info['info']['real_name'];
						$M['avatar'] = $user_info['info']['avatar'];
						$M['cert_type_value'] = $user_info['info']['cert_type_value'];
						$M['cert_no'] = $user_info['info']['cert_no'];
						$M['sex'] = $user_info['info']['gender']=='F'?'女':'男';
						$M['phone'] = $user_info['info']['phone'];
						$M['mobile'] = $user_info['info']['mobile'];
						$M['province'] = $user_info['info']['province'];
						$M['city'] = $user_info['info']['city'];
						$M['area'] = $user_info['info']['area'];
						$M['address'] = $user_info['info']['address'];
						$M['zip'] = $user_info['info']['zip'];
						$M['setting'] = array2string($user_info);
					}
					$_A['_oauthalipay'] = 1; //服务窗oaut授权
                    //
					$this->fuwu->exist_group($M);
					$this->fuwu->processor();
					//
					gourl(get_link('alipay_oauth2|auth_code'));
				} else {
					$_url = 'https://openauth.alipay.com/oauth2/publicAppAuthorize.htm?app_id='.$_A['al']['al_appid'];
					$_url.= '&auth_skip=true&scope=auth_userinfo&redirect_uri='.urlencode(get_link('alipay_oauth2')."&alipay_oauth2=1");
					gourl($_url);
				}
			}
        }
        //微信授权
        elseif ($_A['browser'] == 'weixin' && $_A['al']['wx_appid']) {
			$this->load->library('wx');
			$this->wx->setting(intval($_A['al']['id']));
			//
			$_temp_isget = false;
			$get_appid = value($_A, 'al|setting|other|get_appid');
			if ($get_appid) {
				$get_appoint = value($_A, 'al|setting|other|get_appoint', true);
				if (empty($get_appoint) || in_array($_A['module'], $get_appoint)) {
					//微信授权 - 借用
					$_temp_isget = true;
					//
					$_A['wxget']['appid'] = trim($get_appid);
					$_A['wxget']['secret'] = trim($_A['al']['setting']['other']['get_secret']);
					$_A['wxget']['scope'] = trim($_A['al']['setting']['other']['get_scope'])=='snsapi_base'?'snsapi_base':'snsapi_userinfo';
					$_A['wxget']['md5'] = substr(md5($_A['wxget']['appid'].$_A['wxget']['secret']), 8, 16);
					$_A['openid'] = $this->get_udata('g_openid_'.$_A['wxget']['md5']);
					if ($_A['openid']) {
						$_A['fans'] = $this->wx->exist_group(array('openid'=>$_A['openid'],'alid'=>$_A['al']['id']), true);
					}
					if (empty($_A['fans'])) {
						if (isset($_GPC['weixin_oauth2'])) {
							if (!isset($_GPC['code'])) {
								message(null, "OAuth 2.0授权失败！");
							}
							$this->load->library('communication');
							$url  = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$_A['wxget']['appid'];
							$url.= '&secret='.$_A['wxget']['secret'].'&code='.$_GPC['code'].'&grant_type=authorization_code';
							$content = $this->communication->ihttp_request($url);
							$_content = isset($content['content'])?json_decode($content['content'], true):'';
							$_A['openid'] = value($_content,'openid');
							if (empty($_A['openid'])) {
								message(null, "OAuth 2.0授权失败 - wx - get - openid！");
							}
							if ($_A['wxget']['scope'] == 'snsapi_userinfo') {
								$access_token = value($_content,'access_token');
								$url  = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$_A['openid'].'&lang=zh_CN';
								$content = $this->communication->ihttp_request($url);
								$_content = isset($content['content'])?json_decode($content['content'], true):'';
								$_A['openid'] = value($_content,'openid');
								if (empty($_A['openid'])) {
									message(null, "OAuth 2.0授权失败 - wx - get - userinfo！");
								}
							}
							$this->set_udata('g_openid_'.$_A['wxget']['md5'], $_A['openid']);
							$M = array(
								'openid'=>$_A['openid'],
								'alid'=>$_A['al']['id'],
								'appid'=>$_A['al']['wx_appid'],
								'follow'=>3 //借用的会员
							);
							$M['user_name'] = value($_content, 'nickname');
							$M['sex'] = str_replace(array("1","2"), array("男","女"),value($_content, 'sex'));
							$M['city'] = value($_content, 'city');
							$M['province'] = value($_content, 'province');
							$M['avatar'] = value($_content, 'headimgurl');
							$_A['_oauthweixin'] = 1; //微信oaut授权
							//
							$this->wx->exist_group($M);
							$this->wx->processor();
							//
							gourl(get_link('weixin_oauth2|code'));
						} else {
							$_url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$_A['wxget']['appid'];
							$_url.= '&redirect_uri='.urlencode(get_link('weixin_oauth2')."&weixin_oauth2=1");
							$_url.= '&response_type=code&scope='.$_A['wxget']['scope'].'&state=STATE#wechat_redirect';
							gourl($_url);
						}
					}
				}
			}
            if ($_temp_isget == false) {
                $this->load->library('wxopen');
                $openweixin = $this->wxopen->isopen();
                if ($openweixin) {
                    //微信授权 - 第三方
                    $_temp_isget = true;
                    $_A['openid'] = $this->get_udata('o_openid_'.$openweixin);
                    if ($_A['openid']) {
                        $_A['fans'] = $this->wx->exist_group(array('openid'=>$_A['openid'],'alid'=>$_A['al']['id']), true);
                    }
                    if (empty($_A['fans'])) {
                        if (isset($_GPC['weixin_oauth2'])) {
                            if (!isset($_GPC['code'])) {
                                message(null, "OAuth 2.0授权失败！");
                            }
                            $this->load->library('communication');
                            $this->load->library('wxopen');
                            $url  = 'https://api.weixin.qq.com/sns/oauth2/component/access_token?appid='.$_A['al']['wx_appid'];
                            $url.= '&code='.$_GPC['code'].'&grant_type=authorization_code&component_appid='.$openweixin;
                            $url.= '&component_access_token='.$this->wxopen->get_token();
                            $content = $this->communication->ihttp_request($url);
                            $_content = isset($content['content'])?json_decode($content['content'], true):'';
                            $_A['openid'] = value($_content,'openid');
                            if (empty($_A['openid'])) {
                                message(null, "OAuth 2.0授权失败 - wx - open - openid！");
                            }
                            $access_token = value($_content,'access_token');
                            $url  = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$_A['openid'].'&lang=zh_CN';
                            $content = $this->communication->ihttp_request($url);
                            $_content = isset($content['content'])?json_decode($content['content'], true):'';
                            $_A['openid'] = value($_content,'openid');
                            if (empty($_A['openid'])) {
                                message(null, "OAuth 2.0授权失败 - wx - open - userinfo！");
                            }
                            $this->set_udata('o_openid_'.$openweixin, $_A['openid']);
                            $M = array(
                                'openid'=>$_A['openid'],
                                'alid'=>$_A['al']['id'],
                                'appid'=>$_A['al']['wx_appid'],
                                'follow'=>1
                            );
                            $M['user_name'] = value($_content, 'nickname');
                            $M['sex'] = str_replace(array("1","2"), array("男","女"),value($_content, 'sex'));
                            $M['city'] = value($_content, 'city');
                            $M['province'] = value($_content, 'province');
                            $M['avatar'] = value($_content, 'headimgurl');
                            $_A['_oauthopenweixin'] = 1; //微信第三方授权
                            //
                            $this->wx->exist_group($M);
                            $this->wx->processor();
                            //
                            gourl(get_link('weixin_oauth2|code'));
                        }else{
                            $_url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$_A['al']['wx_appid'];
                            $_url.= '&redirect_uri='.urlencode(get_link('weixin_oauth2')."&weixin_oauth2=1");
                            $_url.= '&response_type=code&scope=snsapi_userinfo&state=STATE';
                            $_url.= '&component_appid='.$openweixin.'#wechat_redirect';
                            gourl($_url);
                        }
                    }
                }
            }
			if ($_temp_isget == false) {
				//微信授权 - 原号
				if ($_A['openid']) {
					$_A['fans'] = $this->wx->exist_group(array('openid'=>$_A['openid'],'alid'=>$_A['al']['id']), true);
				}
				if (empty($_A['fans']) && in_array($_A['al']['wx_level'], array("4","7"))) {
					if (isset($_GPC['weixin_oauth2'])) {
						if (!isset($_GPC['code'])) {
							message(null, "OAuth 2.0授权失败！");
						}
						$this->load->library('communication');
						if ($_A['al']['wx_level'] == 7) {
							$url  = 'https://qyapi.weixin.qq.com/cgi-bin/user/getuserinfo?access_token='.$this->wx->token().'&code='.$_GPC['code'];
							$content = $this->communication->ihttp_request($url);
							$_content = isset($content['content'])?json_decode($content['content'], true):'';
							if (isset($_content['OpenId'])) {
								message(null, "抱歉，只允许企业成员访问！");
							}
							$_A['openid'] = value($_content,'UserId');
							if (empty($_A['openid'])) {
								message(null, "OAuth 2.0授权失败 - wx - openid！");
							}
							$url  = 'https://qyapi.weixin.qq.com/cgi-bin/user/get?access_token='.$this->wx->token().'&userid='.$_A['openid'];
							$content = $this->communication->ihttp_request($url);
							$_content = isset($content['content'])?json_decode($content['content'], true):'';
							$_A['openid'] = value($_content,'userid');
							$_content['nickname'] = value($_content,'name');
							$_content['sex'] = value($_content,'gender');
							$_content['headimgurl'] = value($_content,'avatar');
						}else{
							$url  = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$_A['al']['wx_appid'];
							$url.= '&secret='.$_A['al']['wx_secret'].'&code='.$_GPC['code'].'&grant_type=authorization_code';
							$content = $this->communication->ihttp_request($url);
							$_content = isset($content['content'])?json_decode($content['content'], true):'';
							$_A['openid'] = value($_content,'openid');
							if (empty($_A['openid'])) {
								message(null, "OAuth 2.0授权失败 - wx - openid！");
							}
							$access_token = value($_content,'access_token');
							$url  = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$_A['openid'].'&lang=zh_CN';
							$content = $this->communication->ihttp_request($url);
							$_content = isset($content['content'])?json_decode($content['content'], true):'';
							$_A['openid'] = value($_content,'openid');
						}
						if (empty($_A['openid'])) {
							message(null, "OAuth 2.0授权失败 - wx - userinfo！");
						}
						$this->set_udata($openidname, $_A['openid']);
						$M = array(
							'openid'=>$_A['openid'],
							'alid'=>$_A['al']['id'],
							'appid'=>$_A['al']['wx_appid']
						);
						$M['user_name'] = value($_content, 'nickname');
						$M['sex'] = str_replace(array("1","2"), array("男","女"),value($_content, 'sex'));
						$M['city'] = value($_content, 'city');
						$M['province'] = value($_content, 'province');
						$M['avatar'] = value($_content, 'headimgurl');
						$_A['_oauthweixin'] = 1; //微信oaut授权
						//
						$this->wx->exist_group($M);
						$this->wx->processor();
						//
						gourl(get_link('weixin_oauth2|code'));
					} else {
						$_url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$_A['al']['wx_appid'];
						$_url.= '&redirect_uri='.urlencode(get_link('weixin_oauth2')."&weixin_oauth2=1");
						if ($_A['al']['wx_level'] == 7) {
							$_url.= '&response_type=code&scope=snsapi_base&state=STATE#wechat_redirect';
						}else{
							$_url.= '&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect';
						}
						gourl($_url);
					}
				}
			}
		}
		//其他授权方式
		else{
			if ($_A['browser'] == 'weixin') {
				$this->load->library('wx');
				$this->wx->setting(intval($_A['al']['id']));
				if ($_A['openid']) {
					$_A['fans'] = $this->wx->exist_group(array('openid'=>$_A['openid'],'alid'=>$_A['al']['id']), true, true);
				}
			}else{
				$this->load->library('fuwu');
				$this->fuwu->setting(intval($_A['al']['id']));
				if ($_A['openid']) {
					$_A['fans'] = $this->fuwu->exist_group(array('openid'=>$_A['openid'],'alid'=>$_A['al']['id']), true, true);
				}
			}
        }
		if (empty($_A['fans']['openid']) && value($_A, 'f|oauth')) {
			$this->session->set_userdata('auth:fromurl', get_url());
			$this->auth_login();
		}

        //APP端(客户端) 执行前
        if (isset($GLOBALS['al_function']) && $GLOBALS['al_function']) {
            foreach($GLOBALS['al_function'] AS $item) {
                $title_en = $item['title_en'];
                if ($this->base->apprun($title_en)) {
                    $classname = "ES_Apprun_".ucfirst($title_en);
                    $GLOBALS['app'.$title_en] = new $classname();
                    if (method_exists($GLOBALS['app'.$title_en], 'before')) {
                        $GLOBALS['app'.$title_en]->before();
                    }
                }
            }
        }
    }

	/**
	 * 登录\注册\找回密码 （客户端）
	 */
	public function auth_login()
	{
		global $_GPC,$_A;
		$arr = array();
		$arr['success'] = 0;
		$arr['message'] = '';
		$openidname = '__SYS:USERID:'.intval($_A['al']['id']);
		//
		$_temp_isget = false;
		$get_appid = value($_A, 'al|setting|other|get_appid');
		if ($get_appid) {
			$get_appoint = value($_A, 'al|setting|other|get_appoint', true);
			if (empty($get_appoint) || in_array($_A['module'], $get_appoint)) {
				$_temp_isget = true; //微信授权 - 借用
			}
		}
		$this->cs->assign('_temp_isget', $_temp_isget);
		//
		if (isset($_GPC['authsend'])) {
			if ($_GPC['authtype'] == "alipay") {
				//支付宝登录
				$this->load->library('fuwu');
				$this->fuwu->setting(intval($_A['al']['id']));
				if (!isset($_GPC['auth_code'])) {
					message(null, "OAuth 2.0授权登录失败！");
				}
				$user_info = $this->fuwu->getUserInfo($_GPC['auth_code']);
				$_A['openid'] = $user_info['oauth']['alipay_user_id'];
				if (isset($user_info['error']) && empty($_A['openid'])) {
					message(null, "温馨提示", "错误: ".$user_info['error']['msg'].$user_info['error']['sub_msg']);
				}
				$this->set_udata($openidname, $_A['openid']);
				$M = array(
					'openid'=>$_A['openid'],
					'alid'=>$_A['al']['id'],
					'appid'=>$_A['al']['al_appid']
				);
				if (isset($user_info['info'])) {
					$M['logon_id'] = $user_info['info']['user_id'];
					$M['user_name'] = $user_info['info']['real_name'];
					$M['avatar'] = $user_info['info']['avatar'];
					$M['cert_type_value'] = $user_info['info']['cert_type_value'];
					$M['cert_no'] = $user_info['info']['cert_no'];
					$M['sex'] = $user_info['info']['gender']=='F'?'女':'男';
					$M['phone'] = $user_info['info']['phone'];
					$M['mobile'] = $user_info['info']['mobile'];
					$M['province'] = $user_info['info']['province'];
					$M['city'] = $user_info['info']['city'];
					$M['area'] = $user_info['info']['area'];
					$M['address'] = $user_info['info']['address'];
					$M['zip'] = $user_info['info']['zip'];
					$M['setting'] = array2string($user_info);
				}
				$_A['_oauthalipay'] = 1; //服务窗oaut授权
				//
				$this->fuwu->exist_group($M);
				$this->fuwu->processor();
				gourl(get_link('authsend|authtype|app_id|source|scope|auth_code'));
			}elseif ($_GPC['authtype'] == "weixincode") {
				//微信登录(二维码)
				$wxcodename = '__SYS:WXCODE:'.intval($_A['al']['id']);
				$wxcodedata = $this->get_udata($wxcodename);
				if (empty($wxcodedata)) {
					$wxcodedata = generate_password(8);
					$this->set_udata($wxcodename, $wxcodedata);
				}
				$wxvalue = generate_password(6);
				db_delete(table('tmp'), array("`indate`<"=>(SYS_TIME-600),"`title` LIKE 'system_wxac_%'"=>''));
				$notewhere = array('title'=>'system_wxac_'.$wxcodedata);
				$notes = db_getone(table('tmp'), $notewhere);
				if (empty($notes)) {
					$notewhere['indate'] = SYS_TIME;
					$notewhere['value'] = $wxvalue;
					$notewhere['content'] = get_link('authsend|authtype|tv');
					db_insert(table('tmp'), $notewhere);
				}else{
					db_update(table('tmp'), array('indate'=>SYS_TIME, 'value'=>$wxvalue, 'content'=>get_link('authsend|authtype|tv')), $notewhere);
				}
				//
				include_once dirname(dirname(__FILE__)).'/libraries/other/phpqrcode.php';
				QRcode::png(appurl('system/weixinauth/s'.$wxcodedata.'/v'.$wxvalue.'/'), false, 'L', 10, 0);
				exit();
			}elseif ($_GPC['authtype'] == "alipaycode") {
				//支付宝登录(二维码)
				$alcodename = '__SYS:ALCODE:'.intval($_A['al']['id']);
				$alcodedata = $this->get_udata($alcodename);
				if (empty($alcodedata)) {
					$alcodedata = generate_password(8);
					$this->set_udata($alcodename, $alcodedata);
				}
				$alvalue = generate_password(6);
				db_delete(table('tmp'), array("`indate`<"=>(SYS_TIME-600),"`title` LIKE 'system_alac_%'"=>''));
				$notewhere = array('title'=>'system_alac_'.$alcodedata);
				$notes = db_getone(table('tmp'), $notewhere);
				if (empty($notes)) {
					$notewhere['indate'] = SYS_TIME;
					$notewhere['value'] = $alvalue;
					$notewhere['content'] = get_link('authsend|authtype|tv');
					db_insert(table('tmp'), $notewhere);
				}else{
					db_update(table('tmp'), array('indate'=>SYS_TIME, 'value'=>$alvalue, 'content'=>get_link('authsend|authtype|tv')), $notewhere);
				}
				//
				include_once dirname(dirname(__FILE__)).'/libraries/other/phpqrcode.php';
				QRcode::png(appurl('system/alipayauth/s'.$alcodedata.'/v'.$alvalue.'/'), false, 'L', 10, 0);
				exit();
			}
			if (empty($_GPC['username'])) {
				$arr['message'] = '请输入手机号/邮箱！';
				echo json_encode($arr); exit();
			}
			if ($_GPC['authtype'] == "login") {
				//登录
				$wheresql = "(`userphone`='".$_GPC['username']."' OR `useremail`='".$_GPC['username']."')";
				$wheresql.= " AND `alid`='".$_A['al']['id']."'";
				$fans = db_getone("SELECT * FROM ".table('fans')." WHERE ".$wheresql);
				if (empty($fans)) {
					$arr['message'] = '账号或密码不正确！';
					echo json_encode($arr); exit();
				}elseif($fans['userpass'] != md52($_A['al']['id'].$_GPC['userpass'])) {
					$arr['message'] = '账号或密码不正确！';
					echo json_encode($arr); exit();
				}
				$this->set_udata($openidname, $fans['openid']);
				$arr['success'] = 1;
				$arr['message'] = '登录成功！';
				echo json_encode($arr); exit();
			}elseif ($_GPC['authtype'] == "login2") {
				//登录（验证码）
				$wheresql = "(`userphone`='".$_GPC['username']."' OR `useremail`='".$_GPC['username']."')";
				$wheresql.= " AND `alid`='".$_A['al']['id']."'";
				$fans = db_getone("SELECT * FROM ".table('fans')." WHERE ".$wheresql);
				if (empty($fans)) {
					$arr['message'] = '手机号/邮箱不正确或尚未注册！';
					echo json_encode($arr); exit();
				}
				if (empty($fans['userphone']) || !isMobile($fans['userphone'])) {
					$arr['message'] = '此账号尚未绑定手机号码！';
					echo json_encode($arr); exit();
				}
				$this->load->model('sms');
				if ($_GPC['method'] == "code") {
					$content = $this->sms->send($fans['userphone'], 0, 'login');
					if ($content['success'] == '1') {
						$arr['success'] = 1;
						if (strexists($_GPC['username'],"@") && strexists($_GPC['username'],".")) {
							$arr['message'] = '验证码已发送至'.$fans['userphone'].$content['text'].'！';
						}else{
							$arr['message'] = '验证码发送成功'.$content['text'].'！';
						}
					}else{
						$arr['message'] = $content['message'];
					}
				}else{
					$content = $this->sms->verify($fans['userphone'], $_GPC['usercode'], 0, 'login');
					if ($content['success'] == '1') {
						$this->set_udata($openidname, $fans['openid']);
						$arr['success'] = 1;
						$arr['message'] = '登录成功！';
					}else{
						$arr['message'] = $content['message'];
					}
				}
				echo json_encode($arr); exit();
			}elseif ($_GPC['authtype'] == "reg") {
				//注册
				$this->load->library('fuwu');
				$this->fuwu->setting(intval($_A['al']['id']));
				$M = array(
					'openid'=>generate_password(32),
					'alid'=>$_A['al']['id'],
					'appid'=>$_A['al']['al_appid'],
					'follow'=>'4' //注册
				);
				$isin = db_getone(table('fans'), array('openid'=>$M['openid']));
				if ($isin) {
					$arr['message'] = '网络繁忙，请重试！';
					echo json_encode($arr); exit();
				}
				if (isMobile($_GPC['username'])) {
					$M['userphone'] = $_GPC['username'];
					//
					$isin = db_getone(table('fans'), array('alid'=>$_A['al']['id'], 'userphone'=>$M['userphone']));
					if ($isin) {
						$arr['message'] = '手机号码已存在！';
						echo json_encode($arr); exit();
					}
				}elseif (isMail($_GPC['username'])) {
					$M['useremail'] = $_GPC['username'];
					//
					$isin = db_getone(table('fans'), array('alid'=>$_A['al']['id'], 'useremail'=>$M['useremail']));
					if ($isin) {
						$arr['message'] = '邮箱地址已存在！';
						echo json_encode($arr); exit();
					}
				}else{
					$arr['message'] = '手机号或邮箱格式不正确！';
					echo json_encode($arr); exit();
				}
				$M['userpass'] = md52($_A['al']['id'].$_GPC['userpass']);
				$this->fuwu->exist_group($M);
				$this->fuwu->processor();
				//
				$this->set_udata($openidname, $M['openid']);
				$arr['success'] = 1;
				$arr['message'] = '注册成功！';
				echo json_encode($arr); exit();
			}elseif ($_GPC['authtype'] == "pass") {
				//找回密码（发送验证码）
				$wheresql = "(`userphone`='".$_GPC['username']."' OR `useremail`='".$_GPC['username']."')";
				$wheresql.= " AND `alid`='".$_A['al']['id']."'";
				$fans = db_getone("SELECT * FROM ".table('fans')." WHERE ".$wheresql);
				if (empty($fans)) {
					$arr['message'] = '手机号/邮箱不正确或尚未注册！';
					echo json_encode($arr); exit();
				}
				if (empty($fans['userphone']) || !isMobile($fans['userphone'])) {
					$arr['message'] = '此账号尚未绑定手机号码！';
					echo json_encode($arr); exit();
				}
				$this->load->model('sms');
				$content = $this->sms->send($fans['userphone'], 0, 'pass');
				if ($content['success'] == '1') {
					$arr['success'] = 1;
					if (strexists($_GPC['username'],"@") && strexists($_GPC['username'],".")) {
						$arr['message'] = '验证码已发送至'.$fans['userphone'].$content['text'].'！';
					}else{
						$arr['message'] = '验证码发送成功'.$content['text'].'！';
					}
				}else{
					$arr['message'] = $content['message'];
				}
				echo json_encode($arr); exit();
			}elseif ($_GPC['authtype'] == "pass2") {
				//找回密码（重置密码）
				$wheresql = "(`userphone`='".$_GPC['username']."' OR `useremail`='".$_GPC['username']."')";
				$wheresql.= " AND `alid`='".$_A['al']['id']."'";
				$fans = db_getone("SELECT * FROM ".table('fans')." WHERE ".$wheresql);
				if (empty($fans)) {
					$arr['message'] = '手机号/邮箱不正确或尚未注册！';
					echo json_encode($arr); exit();
				}
				if (empty($fans['userphone']) || !isMobile($fans['userphone'])) {
					$arr['message'] = '此账号尚未绑定手机号码！';
					echo json_encode($arr); exit();
				}
				$this->load->model('sms');
				$content = $this->sms->verify($fans['userphone'], $_GPC['usercode'], 0, 'pass');
				if ($content['success'] == '1') {
					db_update(table('fans'), array('userpass'=>md52($_A['al']['id'].$_GPC['userpass'])), array('id'=>$fans['id']));
					$this->set_udata($openidname, $fans['openid']);
					$arr['success'] = 1;
					$arr['message'] = '密码重置并登陆成功！';
				}else{
					$arr['message'] = $content['message'];
				}
				echo json_encode($arr); exit();
			}
		}
		tpl('auth.tpl'); exit();
	}

    /**
     * 是否登录 (管理端)
     */
    public function islogin($t = null)
	{
        if ($t) {
            $this->getuser();
            $this->functions();
        }else{
            $username = $this->session->userdata('username');
            //未登录(session空)
            if (empty($username) || strlen($username) < 3){
                gourl(weburl("system/login"));
            }
        }
	}

	/**
	 * 获取用户资料 (管理端)
	 * @param null $r
	 * @return array
	 */
	public function getuser($r = null)
	{
		global $_A;
		$username = $this->session->userdata('username');
        if (empty($this->_user)) {
			$u = intval($this->input->get('ui'));
			if ($u) {
				$this->_user = db_getone("SELECT * FROM ".table('users'), array('userid'=>$u));
			}else{
				$this->_user = db_getone("SELECT * FROM ".table('users'), array('username'=>$username));
			}
            if (empty($this->_user)){
                //未登录
                $this->session->unset_userdata('username');
				if (!$r) {
					gourl(weburl("system/login"));
				}
            }else{
				$_A['userid'] = $this->_user['userid'];
				$_A['username'] = $this->_user['username'];
			}
        }elseif (!$r && empty($username)) {
			$this->session->unset_userdata('username');
			gourl(weburl("system/login"));
		}
        $_A['u'] = $this->_user;
        unset($_A['u']['regsetting']);
        return $this->_user;
    }

	/**
	 * 模块信息 (管理端)
	 * @param null $r
	 * @return array
	 */
	public function functions($r = null)
	{
		global $_A;
        //模块  用户模块  服务窗
		$_A['f'] = $_A['uf'] = $_A['al'] = array();

		// 模块功能
		if (isset($this->_data['f'])) {
			$row = $this->_data['f'];
		}else{
			$row = db_getone("SELECT * FROM ".table('functions'), array('title_en'=>$_A['module']));
			$this->_data['f'] = $row;
		}
		if (empty($row)){
			if ($r) {
				return '此功能不存在！';
			}else{
				$this->cs->showmsg('温馨提醒', '此功能不存在！');
			}
		}
        $_A['f'] = $row;

        // 用户模块信息
		$uf = $this->input->get('uf'); //users_functions id
		$warr = array(
			'id'=>intval($uf),
			'fid'=>$row['id']
		);
		$al = $this->input->get('al'); //alid
		if ($al) {
			$warr['alid'] = intval($al);
			$this->session->set_userdata('user:alid', $warr['alid']);
		}
		if (empty($i) || empty($l)) {
			$user = $this->getuser(1);
			$warr = array(
				'fid'=>$row['id'],
				'userid'=>value($user,'userid','int')
			);
			if ($user['admin']) unset($warr['userid']);
			if ($this->session->userdata('user:alid')) {
				$warr['alid'] = $this->session->userdata('user:alid');
                if (empty($warr['userid'])) unset($warr['userid']);
			}
		}
		if (isset($this->_data['uf'])) {
			$row2 = $this->_data['uf'];
		}else{
			$row2 = db_getone("SELECT * FROM ".table('users_functions'), $warr);
			$this->_data['uf'] = $row2;
		}
		if (empty($row2)){
			if ($r) {
				return '模块不存在，可能访问的链接超时，请返回平台首页重新进入。';
			}else{
				$this->cs->showmsg('温馨提醒', '模块不存在，可能访问的链接超时，请返回平台首页重新进入。');
			}
		}
		$row2['setting'] = string2array($row2['setting']);
        $GLOBALS['uf_userdata'] = $row2['userdata'];
        unset($row2['userdata']);
		$_A['uf'] = $row2;

        // 服务窗信息
		if (isset($this->_data['al'])) {
			$row3 = $this->_data['al'];
		}else{
			$row3 = db_getone("SELECT * FROM ".table('users_al'), array('id'=>$row2['alid']));
            $row3['function'] = string2array($row3['function']);
			$this->_data['al'] = $row3;
            $GLOBALS['al_function'] = $row3['function'];
            unset($row3['function']);
		}
		if (empty($row3)){
			if ($r) {
				return '服务窗不存在。';
			}else{
				$this->cs->showmsg('温馨提醒', '服务窗不存在。');
			}
		}
		$row3['setting'] = string2array($row3['setting']);
		if ($row3['wx_level'] == 7) {
			$row3['wx_corp'] = string2array($row3['wx_corp']);
		}else{
			unset($row3['wx_corp']);
		}
        //
        unset($row3['payment']);
		$_A['al'] = $row3;
        //
        return array('f'=>$row, 'uf'=>$row2, 'al'=>$row3);
    }

	/**
	 * 更新公众号拥有功能 (管理端)
	 * @param int $userid
	 */
	public function handle_function($userid = 0)
	{
		if (empty($userid)) {
			$this->getuser();
			$userid = $this->_user['userid'];
		}
		$allist = $this->ddb->getall("SELECT * FROM ".table('users_al'), array('userid'=>intval($userid)));
		foreach($allist as $v){
			$flist = $this->ddb->getall("SELECT a.*,a.id as ida,b.* FROM ".table('users_functions')." a INNER JOIN ".table('functions')." b ON a.fid=b.id WHERE a.alid=".$v['id']." AND a.userid=".$userid." ORDER BY b.inorder");
			$_arr = array();
			foreach($flist as $fv){
				$_arr[$fv['id']] = $fv;
			}
			$inorder = array();
			foreach ($_arr as $key => $val) {
				$inorder[$key] = $val['ida'];
			}
			array_multisort($inorder, SORT_ASC, $_arr);
			$this->ddb->update(table('users_al'), array('function'=>array2string($_arr)), array('id'=>$v['id']));
		}
	}
}
?>