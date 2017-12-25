<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once('DataReturn.class.php');

class ES_Emulator extends CI_Model {

	public function __construct()
	{
		parent::__construct();
	}

    /**
     * web前执行
     */
    public function doWeb() {
        $this->user->islogin(1);
    }

    /**
     *
     */
    public function doWebIndex()
    {
        global $_A;
		$user = $this->user->getuser();
		$lwhere = array('userid'=>$_A['userid']);
		if ($user['admin']) $lwhere = array();
        $allist = $this->ddb->getall(table("users_al"), $lwhere, 'id DESC');
        $this->cs->show(get_defined_vars());
    }

    /**
     * 读取菜单
     */
    public function doWebMenu()
    {
        global $_A,$_GPC;
        $user = $this->user->getuser();
        $lwhere = array('userid'=>$_A['userid'], 'id'=>intval($_GPC['id']));
        if ($user['admin']) unset($lwhere['userid']);
        $row = $this->ddb->getone(table("users_al"), $lwhere);
        if (empty($row)) {
            echo '服务窗不存在！'; exit();
        }
        $f = $this->ddb->getone(table("functions"), array('title_en'=>'menu'));
        if (empty($f)) {
            echo '模块不存在！'; exit();
        }
        $lwhere = array('userid'=>$_A['userid'], 'fid'=>$f['id'], 'alid'=>$row['id']);
        if ($user['admin']) unset($lwhere['userid']);
        $uf = $this->ddb->getone(table("users_functions"), $lwhere);
        if (empty($uf)) {
            echo '功能不存在！'; exit();
        }
        $setting = string2array($uf['setting']);
		if ($_GPC['type'] == "weixin") {
			if ($row['wx_level'] == 7) {
				$menu = value($setting, 'wxmenu-'.$_GPC['agentid'], true);
			}else{
				$menu = value($setting, 'wxmenu', true);
			}
		}else{
			$menu = value($setting, 'menu', true);
		}
        $_html = '<select class="form-control" id="menukey" onchange="_selectmenu(this)">';
        if ($menu && is_array($menu)) {
            foreach($menu AS $item) {
                if (isset($item['child']) && $item['child'] && count($item['child']) > 0) {
                    $_html.= '<optgroup label="'.$item['title'].'">';
                    foreach($item['child'] AS $child) {
                        $_html.= '<option value="'.trim($child['keytext']).'" data-type="'.trim($child['keytype']).'">'.trim($child['title']).'</option>';
                    }
                    $_html.= '</optgroup>';
                }else{
                    $_html.= '<option value="'.trim($item['keytext']).'" data-type="'.trim($item['keytype']).'">'.trim($item['title']).'</option>';
                }
            }
        }else{
            $_html.= '<option value="">==没有菜单==</option>';
        }
        $_html.= '</select>';
        echo $_html;
    }

    /**
     *
     */
    public function doWebSend()
    {
        global $_A,$_GPC;
        $arr = array();
        $arr['success'] = 1;
        $arr['rethtml'] = 0;
        $arr['message'] = '';
        $arr['preview'] = ''; //预览
        //

        $user = $this->user->getuser();
        $lwhere = array('userid'=>$_A['userid'], 'id'=>intval($_GPC['id']));
        if ($user['admin']) unset($lwhere['userid']);
        $row = $this->ddb->getone(table("users_al"), $lwhere);
        if (empty($row)) {
            $arr['message'] = '服务项目不存在';
            echo json_encode($arr); exit();
        }
		$logon_id = substr(md5($_GPC['user']),0,3).'****'.substr(md5($_GPC['user']),-4);
		$user_name = $_GPC['user'];
		$avatar = IMG_PATH.'avatar.jpg';
		if (is_numeric($_GPC['user'])) {
			if (strlen($_GPC['user']) < 15) {
				$vip = $this->ddb->getone("SELECT openid FROM ".table("vip_users"),
					array('card'=>$_GPC['user'], 'type'=>'alipay', 'alid'=>$row['id']));
				if ($vip) {
					$_GPC['user'] = $vip['openid'];
				}
			}
			$fans = $this->ddb->getone("SELECT avatar,logon_id,user_name FROM ".table("fans"),
				array('openid'=>$_GPC['user'], 'type'=>'alipay', 'alid'=>$row['id']));
			if ($fans) {
				$avatar = $fans['avatar']?fillurl($fans['avatar']):$avatar;
				$logon_id = $fans['logon_id'];
				$user_name = $fans['user_name'];
			}
		}
		//服务窗模拟
		if ($_GPC['ftype'] == "alipay") {
			$url = $_A['url']['index']."alipay/".$row['id']."/?sid=".md52($row['id'],$row['al_appid']);
			switch($_GPC['type'])
			{
				case "text":        //文本
					$xml = '
						<XML>
							<Text>
								<Content><![CDATA['.$_GPC['content'].']]></Content>
							</Text>
							<AppId><![CDATA['.$row['al_appid'].']]></AppId>
							<MsgType><![CDATA[text]]></MsgType>
							<CreateTime><![CDATA['.SYS_TIME.']]></CreateTime>
							<FromUserId><![CDATA['.$_GPC['user'].']]></FromUserId>
							<MsgId><![CDATA['.SYS_TIME.rand(10000000,99999999).']]></MsgId>
							<UserInfo><![CDATA[{"logon_id":"'.$logon_id.'","user_name":"'.$user_name.'"}]]></UserInfo>
						</XML>';
					//
					$arr['preview'].= '<div class="li">';
					$arr['preview'].= '<img class="avatar-right" src="'.$avatar.'">';
					$arr['preview'].= '<div class="text text-right">'.$_GPC['content'].'</div>';
					$arr['preview'].= '<div style="clear:both;"></div>';
					$arr['preview'].= '</div>';
					break;

				case "menu":        //菜单
					$xml = '
						<XML>
							<AppId><![CDATA['.$row['al_appid'].']]></AppId>
							<FromUserId><![CDATA['.$_GPC['user'].']]></FromUserId>
							<CreateTime><![CDATA['.SYS_TIME.']]></CreateTime>
							<MsgType><![CDATA[event]]></MsgType>
							<EventType><![CDATA[click]]></EventType>
							<ActionParam><![CDATA['.$_GPC['menukey'].']]></ActionParam>
							<AgreementId><![CDATA[]]></AgreementId>
							<AccountNo><![CDATA[]]></AccountNo>
							<MsgId><![CDATA['.SYS_TIME.rand(10000000,99999999).']]></MsgId>
							<UserInfo><![CDATA[{"logon_id":"'.$logon_id.'","user_name":"'.$user_name.'"}]]></UserInfo>
						</XML>';
					//
					$arr['preview'].= '<div class="li">';
					$arr['preview'].= '<img class="avatar-right" src="'.$avatar.'">';
					$arr['preview'].= '<div class="text text-right">菜单::'.$_GPC['menuname'].'</div>';
					$arr['preview'].= '<div style="clear:both;"></div>';
					$arr['preview'].= '</div>';
					$avatarleft = "";
					if ($_GPC['menutype'] == "link") {
						$avatarleft = "打开链接: <a href='".$_GPC['menukey']."' target='_blank'>".$_GPC['menukey']."</a>";
					}elseif ($_GPC['menutype'] == "tel") {
						$avatarleft = "拨打电话: ".$_GPC['menukey'];
					}elseif ($_GPC['menutype'] == "in") {
						$avatarleft = "发送广播: 接收到一条指定的图文广播";
					}elseif ($_GPC['menutype'] == "in2") {
						$avatarleft = "查看广播正文: 进入到指定的单图文广播，直接查看正文内容";
					}elseif ($_GPC['menutype'] == "alipay") {
						$avatarleft = "查看地图: 查看指定关键字的地理位置";
					}elseif ($_GPC['menutype'] == "alipay2") {
						$avatarleft = "消费记录: 查看与服务窗之间发生的交易明细";
					}elseif ($_GPC['menutype'] != "out") {
						$arr['message'] = '此菜单不支持模拟！';
						echo json_encode($arr); exit();
					}
					if ($avatarleft) {
						$arr['rethtml'] = 1;
						$arr['message'] = $avatarleft;
						echo json_encode($arr); exit();
					}
					break;

				case "subscribe":   //关注
					$xml = '
						<XML>
							<AppId><![CDATA['.$row['al_appid'].']]></AppId>
							<FromUserId><![CDATA['.$_GPC['user'].']]></FromUserId>
							<CreateTime><![CDATA['.SYS_TIME.']]></CreateTime>
							<MsgType><![CDATA[event]]></MsgType>
							<EventType><![CDATA[follow]]></EventType>
							<ActionParam><![CDATA[{"scene":{"sceneId": "0"}}]]></ActionParam>
							<AgreementId><![CDATA[]]></AgreementId>
							<AccountNo><![CDATA[]]></AccountNo>
							<MsgId><![CDATA['.SYS_TIME.rand(10000000,99999999).']]></MsgId>
							<UserInfo><![CDATA[{"logon_id":"'.$logon_id.'","user_name":"'.$user_name.'"}]]></UserInfo>
						</XML>';
					//
					$arr['preview'].= '<div class="li">';
					$arr['preview'].= '<img class="avatar-right" src="'.$avatar.'">';
					$arr['preview'].= '<div class="text text-right">::关注</div>';
					$arr['preview'].= '<div style="clear:both;"></div>';
					$arr['preview'].= '</div>';
					break;

				case "unsubscribe":   //取消关注
					$xml = '
						<XML>
							<AppId><![CDATA['.$row['al_appid'].']]></AppId>
							<FromUserId><![CDATA['.$_GPC['user'].']]></FromUserId>
							<CreateTime><![CDATA['.SYS_TIME.']]></CreateTime>
							<MsgType><![CDATA[event]]></MsgType>
							<EventType><![CDATA[unfollow]]></EventType>
							<ActionParam><![CDATA[{"scene":{"sceneId": "0"}}]]></ActionParam>
							<AgreementId><![CDATA[]]></AgreementId>
							<AccountNo><![CDATA[]]></AccountNo>
							<MsgId><![CDATA['.SYS_TIME.rand(10000000,99999999).']]></MsgId>
							<UserInfo><![CDATA[{"logon_id":"'.$logon_id.'","user_name":"'.$user_name.'"}]]></UserInfo>
						</XML>';
					//
					$arr['preview'].= '<div class="li">';
					$arr['preview'].= '<img class="avatar-right" src="'.$avatar.'">';
					$arr['preview'].= '<div class="text text-right">::取消关注</div>';
					$arr['preview'].= '<div style="clear:both;"></div>';
					$arr['preview'].= '</div>';
					break;

				default:            //未知
					$arr['message'] = '错误的消息类型';
					echo json_encode($arr); exit();

			}
			error_reporting(0);
			$this->load->helper('communication');
			$_html = ihttp_post($url, array('content'=>$xml));
			if(is_error($_html)) {
				$arr['message'] = "错误: {$_html['message']}";
				echo json_encode($arr); exit();
			}
			$rethtml = @json_decode($_html['content'], true);
			if(empty($rethtml)) {
				$arr['message'] = "接口调用失败, 元数据: {$_html['meta']}";
				echo json_encode($arr); exit();
			} elseif(!empty($rethtml['errcode'])) {
				$arr['message'] = "错误, 错误代码: {$rethtml['errcode']}";
				echo json_encode($arr); exit();
			}
			foreach($rethtml AS $k=>$v) { $rethtml[$k] = mb_convert_encoding(urldecode($v), "UTF-8","GBK"); }
            if (isset($rethtml['biz_content'])) {
                $rethtml['biz_content'] = preg_replace('/[\r\n]+/','<br/>', $rethtml['biz_content']);
                $rethtml['biz_content'] = json_decode($rethtml['biz_content'], true);
            }
			//message
			$param = array('data'  => $rethtml, 'format' => 'hide');
			$obj = new DataReturn($param);
			$arr['message'] = $obj->data_return();
			//preview
			$biz_content = value($rethtml, 'biz_content');
			$msgType = value($biz_content, 'msgType');
			$avatar = IMG_PATH.'avatarsys.jpg';
			if ($msgType == 'text') {
				$arr['preview'].= '<div class="li">';
				$arr['preview'].= '<img class="avatar-left" src="'.$avatar.'">';
				$arr['preview'].= '<div class="text text-left">'.value($biz_content, 'text|content').'</div>';
				$arr['preview'].= '<div style="clear:both;"></div>';
				$arr['preview'].= '</div>';
			}elseif ($msgType == 'image-text') {
				$articles = value($biz_content, 'articles');
				if ($articles && is_array($articles)) {
					$arr['preview'].= '<div class="li">';
					$arr['preview'].= '<img class="avatar-left" src="'.$avatar.'">';
					$arr['preview'].= '<div class="image-text">'.$this->articles($articles).'</div>';
					$arr['preview'].= '<div style="clear:both;"></div>';
					$arr['preview'].= '</div>';
				}
			}
		}
		//微信模拟
		elseif ($_GPC['ftype'] == "weixin") {
			if ($row['wx_level'] == 7) {
				$url = $_A['url']['index']."weixin/".$row['id']."-".$_GPC['agentid']."/?sid=".md52($row['id'],$row['wx_appid'])."&signature=1";
			}else{
				$url = $_A['url']['index']."weixin/".$row['id']."/?sid=".md52($row['id'],$row['wx_appid'])."&signature=1";
			}
			switch($_GPC['type'])
			{
				case "text":        //文本
					$xml = '
						<xml>
							 <ToUserName><![CDATA['.$row['wx_appid'].']]></ToUserName>
							 <FromUserName><![CDATA['.$_GPC['user'].']]></FromUserName>
							 <CreateTime>'.SYS_TIME.'</CreateTime>
							 <MsgType><![CDATA[text]]></MsgType>
							 <Content><![CDATA['.$_GPC['content'].']]></Content>
							 <MsgId>'.SYS_TIME.rand(10000000,99999999).'</MsgId>
						 </xml>';
					//
					$arr['preview'].= '<div class="li">';
					$arr['preview'].= '<img class="avatar-right" src="'.$avatar.'">';
					$arr['preview'].= '<div class="text text-right">'.$_GPC['content'].'</div>';
					$arr['preview'].= '<div style="clear:both;"></div>';
					$arr['preview'].= '</div>';
					break;

				case "menu":        //菜单
					$xml = '
						<xml>
							<ToUserName><![CDATA['.$row['wx_appid'].']]></ToUserName>
							<FromUserName><![CDATA['.$_GPC['user'].']]></FromUserName>
							<CreateTime>'.SYS_TIME.'</CreateTime>
							<MsgType><![CDATA[event]]></MsgType>
							<Event><![CDATA[CLICK]]></Event>
							<EventKey><![CDATA['.$_GPC['menukey'].']]></EventKey>
						</xml>';
					//
					$arr['preview'].= '<div class="li">';
					$arr['preview'].= '<img class="avatar-right" src="'.$avatar.'">';
					$arr['preview'].= '<div class="text text-right">菜单::'.$_GPC['menuname'].'</div>';
					$arr['preview'].= '<div style="clear:both;"></div>';
					$arr['preview'].= '</div>';
					$avatarleft = "";
					if ($_GPC['menutype'] == "link") {
						$avatarleft = "打开链接: <a href='".$_GPC['menukey']."' target='_blank'>".$_GPC['menukey']."</a>";
					}elseif ($_GPC['menutype'] == "tel") {
						$avatarleft = "拨打电话: ".$_GPC['menukey'];
					}elseif ($_GPC['menutype'] == "in") {
						$avatarleft = "发送广播: 接收到一条指定的图文广播";
					}elseif ($_GPC['menutype'] == "in2") {
						$avatarleft = "查看广播正文: 进入到指定的单图文广播，直接查看正文内容";
					}elseif ($_GPC['menutype'] == "alipay") {
						$avatarleft = "查看地图: 查看指定关键字的地理位置";
					}elseif ($_GPC['menutype'] == "alipay2") {
						$avatarleft = "消费记录: 查看与服务窗之间发生的交易明细";
					}elseif ($_GPC['menutype'] != "click") {
						$arr['message'] = '此菜单不支持模拟！';
						echo json_encode($arr); exit();
					}
					if ($avatarleft) {
						$arr['rethtml'] = 1;
						$arr['message'] = $avatarleft;
						echo json_encode($arr); exit();
					}
					break;

				case "subscribe":   //关注
					$xml = '
						<xml>
							<ToUserName><![CDATA['.$row['wx_appid'].']]></ToUserName>
							<FromUserName><![CDATA['.$_GPC['user'].']]></FromUserName>
							<CreateTime>'.SYS_TIME.'</CreateTime>
							<MsgType><![CDATA[event]]></MsgType>
							<Event><![CDATA[subscribe]]></Event>
						</xml>';
					//
					$arr['preview'].= '<div class="li">';
					$arr['preview'].= '<img class="avatar-right" src="'.$avatar.'">';
					$arr['preview'].= '<div class="text text-right">::关注</div>';
					$arr['preview'].= '<div style="clear:both;"></div>';
					$arr['preview'].= '</div>';
					break;

				case "unsubscribe":   //取消关注
					$xml = '
						<xml>
							<ToUserName><![CDATA['.$row['wx_appid'].']]></ToUserName>
							<FromUserName><![CDATA['.$_GPC['user'].']]></FromUserName>
							<CreateTime>'.SYS_TIME.'</CreateTime>
							<MsgType><![CDATA[event]]></MsgType>
							<Event><![CDATA[unsubscribe]]></Event>
						</xml>';
					//
					$arr['preview'].= '<div class="li">';
					$arr['preview'].= '<img class="avatar-right" src="'.$avatar.'">';
					$arr['preview'].= '<div class="text text-right">::取消关注</div>';
					$arr['preview'].= '<div style="clear:both;"></div>';
					$arr['preview'].= '</div>';
					break;

				default:            //未知
					$arr['message'] = '错误的消息类型';
					echo json_encode($arr); exit();
			}
			$_html = $this->curl($url, array('content'=>$xml));
			if (is_error($_html)) {
				$arr['message'] = $_html['message'];
				echo json_encode($arr); exit();
			}
			$rethtml = str_replace(array(" ","　","\t"), "", $_html);
			if (empty($rethtml)) {
				$arr['message'] = $_html;
				echo json_encode($arr); exit();
			}
			$arr['message'] = $rethtml;
			$retcon = json_decode(xml2json($rethtml), true);
			if ($retcon && is_array($retcon)) {
				//preview
				$msgType = value($retcon, 'MsgType');
				$avatar = IMG_PATH.'avatarsys.jpg';
				if ($msgType == 'text') {
					$arr['preview'].= '<div class="li">';
					$arr['preview'].= '<img class="avatar-left" src="'.$avatar.'">';
					$arr['preview'].= '<div class="text text-left">'.value($retcon, 'Content').'</div>';
					$arr['preview'].= '<div style="clear:both;"></div>';
					$arr['preview'].= '</div>';
				}elseif ($msgType == 'news') {
					$articles = value($retcon, 'Articles|item');
					if ($articles && is_array($articles)) {
						$arr['preview'].= '<div class="li">';
						$arr['preview'].= '<img class="avatar-left" src="'.$avatar.'">';
						$arr['preview'].= '<div class="image-text">'.$this->articles_wx($articles).'</div>';
						$arr['preview'].= '<div style="clear:both;"></div>';
						$arr['preview'].= '</div>';
					}
				}elseif ($msgType == 'image' && $MediaId = value($retcon, 'Image|MediaId')) {
					$tmp = db_getone(table("tmp"), array("value"=>$MediaId));
					if ($tmp) {
						$arr['preview'].= '<div class="li">';
						$arr['preview'].= '<img class="avatar-left" src="'.$avatar.'">';
						$arr['preview'].= '<div class="text text-left text-mediaid"><a href="'.fillurl($tmp['content']).'" target="_blank"><img src="'.fillurl($tmp['content']).'"></a></div>';
						$arr['preview'].= '<div style="clear:both;"></div>';
						$arr['preview'].= '</div>';
					}
				}elseif ($msgType == 'voice' && $MediaId = value($retcon, 'Voice|MediaId')) {
					$tmp = db_getone(table("tmp"), array("value"=>$MediaId));
					if ($tmp) {
						$arr['preview'].= '<div class="li">';
						$arr['preview'].= '<img class="avatar-left" src="'.$avatar.'">';
						$arr['preview'].= '<div class="text text-left text-mediaid"><audio src="'.fillurl($tmp['content']).'" controls="controls">您的浏览器不支持 audio 标签。</audio></div>';
						$arr['preview'].= '<div style="clear:both;"></div>';
						$arr['preview'].= '</div>';
					}
				}elseif ($msgType == 'video' && $MediaId = value($retcon, 'Video|MediaId')) {
					$tmp = db_getone(table("tmp"), array("value"=>$MediaId));
					if ($tmp) {
						$arr['preview'].= '<div class="li">';
						$arr['preview'].= '<img class="avatar-left" src="'.$avatar.'">';
						$arr['preview'].= '<div class="text text-left text-mediaid"><video src="'.fillurl($tmp['content']).'" controls="controls">您的浏览器不支持 video 标签。</video></div>';
						$arr['preview'].= '<div style="clear:both;"></div>';
						$arr['preview'].= '</div>';
					}
				}
			}else{
				$retcon = json_decode($rethtml, true);
				$this->load->library('wx');
				$retcon = $this->wx->decode($retcon);
				//preview
				$msgType = value($retcon, 'msgtype');
				$avatar = IMG_PATH.'avatarsys.jpg';
				if ($msgType == 'text') {
					$arr['preview'].= '<div class="li">';
					$arr['preview'].= '<img class="avatar-left" src="'.$avatar.'">';
					$arr['preview'].= '<div class="text text-left">'.value($retcon, 'text|content').'</div>';
					$arr['preview'].= '<div style="clear:both;"></div>';
					$arr['preview'].= '</div>';
				}elseif ($msgType == 'news') {
					$articles = value($retcon, 'news|articles');
					if ($articles && is_array($articles)) {
						$arr['preview'].= '<div class="li">';
						$arr['preview'].= '<img class="avatar-left" src="'.$avatar.'">';
						$arr['preview'].= '<div class="image-text">'.$this->articles_wx2($articles).'</div>';
						$arr['preview'].= '<div style="clear:both;"></div>';
						$arr['preview'].= '</div>';
					}
				}elseif ($msgType == 'tmplmsg') {
					$arr['preview'].= '<div class="li">';
					$arr['preview'].= '<img class="avatar-left" src="'.$avatar.'">';
					$arr['preview'].= '<div class="text text-left">::模板信息</div>';
					$arr['preview'].= '<div style="clear:both;"></div>';
					$arr['preview'].= '</div>';
				}
			}
		}
		else {
			$arr['message'] = '参数错误-ftype！';
			echo json_encode($arr); exit();
		}
        echo json_encode($arr); exit();
    }


    /** ************************************************************************************************************** */
    /** ************************************************************************************************************** */
    /** ************************************************************************************************************** */

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

    /**
     * 文本图像 格式化返回
     * @param array $articles
     * @return array|string
     */
    private function articles($articles = array()) {
        $preview = "";
        if (!is_array($articles)) return $articles;
        if (count($articles) > 1) {
            foreach($articles AS $key=>$item) {
                $preview.= '<div class="appmsg-content">';
                if ($key == 0) {
                    $preview.= '
                        <div class="cover-appmsg-item">
                            <h4 class="appmsg-title"><a href="'.$item['url'].'" target="_blank">'.$item['title'].'</a></h4>
                            <div class="appmsg-thumb-wrap">
                                <img src="'.$item['imageUrl'].'" class="appmsg-thumb">
                            </div>
                        </div>';
                }else{
                    $preview.= '
                        <div class="appmsg-item cf">
                            <img src="'.$item['imageUrl'].'" class="appmsg-thumb">
                            <h4 class="appmsg-title"><a href="'.$item['url'].'" target="_blank">'.$item['title'].'</a></h4>
                        </div>';
                }
                $preview.= '</div>';
            }
        }else{
            $item = $articles[0];
            $preview.= '
                <div class="appmsg-content appmsg-content-one">
                    <h4 class="appmsg-title"><a href="'.$item['url'].'" target="_blank">'.$item['title'].'</a></h4>
                    <div class="appmsg-info">
                        <a href="'.$item['url'].'" target="_blank"><em class="appmsg-date">'.date("Y年m月d日").'</em></a>
                    </div>
                    <div class="appmsg-thumb-wrap">
                        <a href="'.$item['url'].'" target="_blank"><img class="appmsg-thumb" src="'.$item['imageUrl'].'"></a>
                    </div>
                    <p class="appmsg-desc">'.$item['desc'].'</p>
                </div>';
        }
        return $preview;
    }

	/**
	 * 文本图像 格式化返回
	 * @param array $articles
	 * @return array|string
	 */
	private function articles_wx($articles = array()) {
		$preview = "";
		if (!is_array($articles)) return $articles;
		if (count($articles) > 1 && !isset($articles['Title'])) {
			foreach($articles AS $key=>$item) {
				$preview.= '<div class="appmsg-content">';
				if ($key == 0) {
					$preview.= '
                        <div class="cover-appmsg-item">
                            <h4 class="appmsg-title"><a href="'.$item['Url'].'" target="_blank">'.$item['Title'].'</a></h4>
                            <div class="appmsg-thumb-wrap">
                                <img src="'.$item['PicUrl'].'" class="appmsg-thumb">
                            </div>
                        </div>';
				}else{
					$preview.= '
                        <div class="appmsg-item cf">
                            <img src="'.$item['PicUrl'].'" class="appmsg-thumb">
                            <h4 class="appmsg-title"><a href="'.$item['Url'].'" target="_blank">'.$item['Title'].'</a></h4>
                        </div>';
				}
				$preview.= '</div>';
			}
		}else{
			$item = $articles;
			$preview.= '
                <div class="appmsg-content appmsg-content-one">
                    <h4 class="appmsg-title"><a href="'.$item['Url'].'" target="_blank">'.$item['Title'].'</a></h4>
                    <div class="appmsg-info">
                        <a href="'.$item['Url'].'" target="_blank"><em class="appmsg-date">'.date("Y年m月d日").'</em></a>
                    </div>
                    <div class="appmsg-thumb-wrap">
                        <a href="'.$item['Url'].'" target="_blank"><img class="appmsg-thumb" src="'.$item['PicUrl'].'"></a>
                    </div>
                    <p class="appmsg-desc">'.$item['Description'].'</p>
                </div>';
		}
		return $preview;
	}

	private function articles_wx2($articles = array()) {
		$preview = "";
		if (!is_array($articles)) return $articles;
		if (count($articles) > 1) {
			foreach($articles AS $key=>$item) {
				$preview.= '<div class="appmsg-content">';
				if ($key == 0) {
					$preview.= '
                        <div class="cover-appmsg-item">
                            <h4 class="appmsg-title"><a href="'.$item['url'].'" target="_blank">'.$item['title'].'</a></h4>
                            <div class="appmsg-thumb-wrap">
                                <img src="'.$item['picurl'].'" class="appmsg-thumb">
                            </div>
                        </div>';
				}else{
					$preview.= '
                        <div class="appmsg-item cf">
                            <img src="'.$item['picurl'].'" class="appmsg-thumb">
                            <h4 class="appmsg-title"><a href="'.$item['url'].'" target="_blank">'.$item['title'].'</a></h4>
                        </div>';
				}
				$preview.= '</div>';
			}
		}else{
			$item = $articles[0];
			$preview.= '
                <div class="appmsg-content appmsg-content-one">
                    <h4 class="appmsg-title"><a href="'.$item['url'].'" target="_blank">'.$item['title'].'</a></h4>
                    <div class="appmsg-info">
                        <a href="'.$item['Url'].'" target="_blank"><em class="appmsg-date">'.date("Y年m月d日").'</em></a>
                    </div>
                    <div class="appmsg-thumb-wrap">
                        <a href="'.$item['url'].'" target="_blank"><img class="appmsg-thumb" src="'.$item['picurl'].'"></a>
                    </div>
                    <p class="appmsg-desc">'.$item['description'].'</p>
                </div>';
		}
		return $preview;
	}

    private function curl($url, $postFields = null) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);

        $postBodyString = "";
        if (is_array($postFields) && 0 < count($postFields)) {

            $postMultipart = false;
            foreach ($postFields as $k => $v) {
                if ("@" != substr($v, 0, 1)) //判断是不是文件上传
                {
                    $postBodyString .= "$k=" . urlencode($v) . "&";
                } else //文件上传用multipart/form-data，否则用www-form-urlencoded
                {
                    $postMultipart = true;
                }
            }
            unset ($k, $v);
            curl_setopt($ch, CURLOPT_POST, true);
            if ($postMultipart) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
            } else {
                curl_setopt($ch, CURLOPT_POSTFIELDS, substr($postBodyString, 0, -1));
            }
        }
        $headers = array('content-type: application/x-www-form-urlencoded;charset=GBK');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $reponse = curl_exec($ch);

        if (curl_errno($ch)) {
			return error(-1, "错误:".curl_error($ch));
        } else {
            $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if (200 !== $httpStatusCode) {
				return error(-1, "错误:".$httpStatusCode.$reponse);
            }
        }
        curl_close($ch);
        return $reponse;
    }
}
