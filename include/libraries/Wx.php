<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
error_reporting(0);

class Wx {
    private $wxcpt;
    public $data = array();

    /**
     * 接收信息
     */
    public function receive()
    {
        global $_A,$_GPC;
        if ($this->iscorp()) {
            include_once "weixin/WXBizMsgCrypt.php";
            $this->wxcpt = new WXBizMsgCrypt($_A['al']['wx_token'], $_A['al']['wx_aeskey'], $_A['al']['wx_appid']);
        }
        if (isset($_GPC['echostr'])) {
            //验证事件
            if ($this->iscorp()) {
                $errCode = $this->wxcpt->VerifyURL($_GPC["msg_signature"], $_GPC["timestamp"], $_GPC["nonce"], $_GPC["echostr"], $sEchoStr);
                if ($errCode == 0) {
                    echo $sEchoStr;
                }
            }else{
                $tmpArr = array($_A['al']['wx_token'], $_GPC["timestamp"], $_GPC["nonce"]);
                sort($tmpArr, SORT_STRING);
                $tmpStr = implode( $tmpArr );
                $tmpStr = sha1( $tmpStr );
                if( $tmpStr == $_GPC["signature"] ){
                    $this->lastin($_A['al']['id']);
                    echo $_GPC["echostr"];
                }
            }
            exit();
        } elseif (isset($_GPC['signature']) || isset($_GPC['msg_signature'])) {
            $this->Message();
        }
    }

    /**
     * 接收信息处理
     */
    public function Message()
    {
        global $_A,$_GPC;
        //
        $post_str = isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : file_get_contents("php://input");
        if (empty($post_str)) return false;
        //模拟器
        if (isset($_GPC['sid']) && $_GPC['sid']) {
            if ($_GPC['sid'] != md52($_A['al']['id'],$_A['al']['wx_appid'])) {
                echo "some parameter is empty.";
                exit ();
            }else{
                define('_ISEMULATOR', true);
                $post_obj = array();
                $post_obj['FromUserName'] = $this->getNode ( $_GPC['content'], "FromUserName" );
                $post_obj['ToUserName'] = $this->getNode ( $_GPC['content'], "ToUserName" );
                $post_obj['CreateTime'] = $this->getNode ( $_GPC['content'], "CreateTime" );
                $post_obj['MsgType'] = $this->getNode ( $_GPC['content'], "MsgType" );
                $post_obj['Event'] = $this->getNode ( $_GPC['content'], "Event" );
                $post_obj['Content'] = $this->getNode ( $_GPC['content'], "Content" );
                $post_obj['EventKey'] = $this->getNode ( $_GPC['content'], "EventKey" );
            }
        }else{
            if ($this->iscorp()) {
                $errCode = $this->wxcpt->DecryptMsg($_GPC['msg_signature'], $_GPC["timestamp"], $_GPC["nonce"], $post_str, $post_str);
                if ($errCode != 0) { exit (); }
                $post_obj = json_decode(xml2json($post_str), true);
                $_A['corp_agentid'] = value($post_obj, 'AgentID');
            }else{
                $post_obj = json_decode(xml2json($post_str), true);
                if (isset($post_obj['Encrypt']) && !isset($post_obj['FromUserName'])) {
                    include_once "weixin/WXBizMsgCrypt.php";
                    $this->wxcpt = new WXBizMsgCrypt($_A['al']['wx_token'], $_A['al']['wx_aeskey'], $_A['al']['wx_appid']);
                    $errCode = $this->wxcpt->DecryptMsg($_GPC['msg_signature'], $_GPC["timestamp"], $_GPC["nonce"], $post_str, $post_str);
                    if ($errCode != 0) { exit (); }
                    $post_obj = json_decode(xml2json($post_str), true);
                }
            }
        }
        //
        $M = array();
        $M['userinfo'] = '';
        $M['logon_id'] = '';
        $M['user_name'] = '';
        $M['openid'] = $M['fromuserid'] = value($post_obj, 'FromUserName');     //OPENID 发送消息方ID
        $M['openid_type'] = 'weixin';
        $M['appid'] = value($post_obj, 'ToUserName');                           //接收消息方ID
        $M['createtime'] = value($post_obj, 'CreateTime');            	        //消息创建时间
        $M['msgtype'] = value($post_obj, 'MsgType');                  	        //消息类型
        $M['eventtype'] = strtolower(value($post_obj, 'Event'));    	        //获取事件类型
        $M['rawdata'] = $post_obj;
        $M['alid'] = $_A['al']['id'];
        $this->exist_group($M);
        $this->lastin($_A['al']['id']);

        // 收到用户发送的对话消息
        if ($M['msgtype'] == "text") {
            $M['text'] = trim(value($post_obj, 'Content'));
            $this->savemessage($M);
            $this->sendkey($M, $M['text']);
        }
        // 图片消息、语音消息、视频消息、小视频消息
        elseif (in_array($M['msgtype'], array('image','voice','video','shortvideo'))) {
            $media_id = value($post_obj, 'MediaId');
            $mediaurl = '';
            if ($M['msgtype'] == "image") {
                $mediaurl = value($post_obj, "PicUrl");
            }
            if ($media_id) {
                $CI =& get_instance();
                $CI->load->helper('communication');
                if ($this->iscorp()) {
                    $url = "https://qyapi.weixin.qq.com/cgi-bin/media/get?access_token=".$this->token()."&media_id=".$media_id;
                }else{
                    $url = "http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=".$this->token()."&media_id=".$media_id;
                }
                $_html = ihttp_request($url);
                if(!is_error($_html)) {
                    $rethtml = @json_decode($_html['content'], true);
                    if (isset($rethtml['errmsg']) && in_array($rethtml['errmsg'], array('40001','42001'))) {
                        $this->error_code($rethtml['errcode']);
                        $_html = ihttp_request($url);
                        if(!is_error($_html)) {
                            $rethtml = @json_decode($_html['content'], true);
                        }
                    }
                    if (!isset($rethtml['errmsg'])) {
                        $_file = 'uploadfiles/users/'.value($_A,'userid','int').'/'.str_replace('shortvideo','video',$M['msgtype']).'s/_weixin/'.date('Ym/', SYS_TIME);
                        $nan = $media_id.str_replace(array('image','voice','video','shortvideo'), array('.jpg','.'.value($post_obj,"Format"),'.mp4','.mp4'), $M['msgtype']);
                        if ($M['msgtype'] == 'shortvideo') { $nan = "short_".$nan; }
                        $_dir = BASE_PATH.$_file;
                        make_dir($_dir);
                        $fp2 = @fopen($_dir.$nan,'a');
                        fwrite($fp2, $_html['content']);
                        fclose($fp2);
                        $mediaurl = $_file.$nan;
                        if (value($post_obj, "Recognition")) {
                            $mediaurl = array('text'=>$_file.$nan, 'recognition'=>value($post_obj, "Recognition"));
                        }
                    }
                }
            }
            $M['text'] = $mediaurl;
            $this->savemessage($M);
            $this->sendkey($M, $M['text']);
        }
        //地理位置消息 (*2)
        elseif ($M['msgtype'] == "location") {
            $M['text'] = array(
                'text'=>value($post_obj, "Label"),
                'latitude'=>value($post_obj, "Location_X"),
                'longitude'=>value($post_obj, "Location_Y"),
                'scale'=>value($post_obj, "Scale")
            );
            $this->savemessage($M);
            $this->sendkey($M, $M['text']);
        }
        //链接消息
        elseif ($M['msgtype'] == "link") {
            $M['text'] = array(
                'text'=>value($post_obj, "Title"),
                'url'=>value($post_obj, "Url"),
                'description'=>value($post_obj, "Description")
            );
            $this->savemessage($M);
            $this->sendkey($M, $M['text']);
        }
        //事件推送
        elseif ($M['msgtype'] == "event") {
            switch ($M['eventtype'])
            {
                case "subscribe":                   //①关注的事件；②扫描带参数二维码事件（用户未关注时，进行关注后的事件推送）
                    $M['msgtype'] = 'follow';
                    $M['text'] = '::关注';
                    $eventkey = value($post_obj, "EventKey");
                    if (substr($eventkey,0,8)=='qrscene_') {
                        $M['text'] = array(
                            'text'=>'::扫描二维码关注',
                            'eventkey'=>substr($eventkey,8),
                            'ticket'=>value($post_obj, "Ticket")
                        );
                    }
                    $this->savemessage($M);
                    $this->sendkey($M, $M['text']);
                    break;
                case "scan":                        //扫描带参数二维码事件（用户已关注时的事件推送）
                    $M['msgtype'] = 'scan';
                    $M['text'] = array(
                        'text'=>'::扫描二维码进入',
                        'eventkey'=>value($post_obj, "EventKey"),
                        'ticket'=>value($post_obj, "Ticket")
                    );
                    $this->savemessage($M);
                    $this->sendkey($M, $M['text']);
                    break;
                case "unsubscribe":                 //取消关注消息
                    $M['msgtype'] = 'unfollow';
                    $M['text'] = '::取消关注';
                    $this->savemessage($M);
                    $this->sendkey($M, $M['text']);
                    break;
                case "view":                        //跳转URL
                    $M['msgtype'] = 'view';
                    $M['text'] = trim(value($post_obj, "EventKey"));
                    $this->savemessage($M);
                    $this->sendkey($M, $M['text']);
                    break;
                case "click":                       //点击菜单拉取消息时的事件
                    $M['msgtype'] = 'click';
                    $M['text'] = trim(value($post_obj, "EventKey"));
                    $this->savemessage($M);
                    $this->sendkey($M, $M['text']);
                    break;
                case "location":                    //上报地理位置事件（用户同意上报地理位置后，每次进入公众号会话时）
                    $M['msgtype'] = 'location_event';
                    $M['text'] = array(
                        'text'=>'::上报地理位置',
                        'latitude'=>value($post_obj, "Latitude"),
                        'longitude'=>value($post_obj, "Longitude"),
                        'precision'=>value($post_obj, "Precision")
                    );
                    $mloca = db_getone(tableal('message'), array('type'=>'weixin','openid'=>$M['openid'],'msgtype'=>'location_event'));
                    if ($mloca) {
                        $mesarr = array('text'=>array2string($M['text']), 'indate'=>$M['createtime']);
                        if ($this->iscorp()) {
                            $wx_corp = $_A['al']['wx_corp'][$_A['corp_agentid']];
                            $iarr['setting'] = array();
                            $iarr['setting']['corp_agentid'] = $wx_corp['agentid'];
                            $iarr['setting']['corp_name'] = $wx_corp['name'];
                            $iarr['setting'] = array2string($iarr['setting']);
                        }
                        db_update(tableal('message'), $mesarr, array('id'=>$mloca['id']));
                    }else{
                        $this->savemessage($M);
                    }
                    $this->sendkey($M, $M['text']);
                    break;
                case "enter_agent":                 //进入窗口事件
                    $M['msgtype'] = 'enter_agent';
                    $M['text'] = array(
                        'text'=>'::进入窗口',
                        'time'=>$M['createtime'],
                        'time_cn'=>date("Y-m-d H:i:s", $M['createtime'])
                    );
                    $mloca = db_getone(tableal('message'), array('type'=>'weixin','openid'=>$M['openid'],'msgtype'=>'enter_agent'));
                    if ($mloca) {
                        $mesarr = array('text'=>array2string($M['text']), 'indate'=>$M['createtime']);
                        if ($this->iscorp()) {
                            $wx_corp = $_A['al']['wx_corp'][$_A['corp_agentid']];
                            $iarr['setting'] = array();
                            $iarr['setting']['corp_agentid'] = $wx_corp['agentid'];
                            $iarr['setting']['corp_name'] = $wx_corp['name'];
                            $iarr['setting'] = array2string($iarr['setting']);
                        }
                        db_update(tableal('message'), $mesarr, array('id'=>$mloca['id']));
                    }else{
                        $this->savemessage($M);
                    }
                    $this->sendkey($M, $M['text']);
                    break;
                case "scancode_push":               //扫码推事件的事件
                    $M['msgtype'] = 'scancode_push';
                    $M['text'] = array(
                        'text'=>value($post_obj, "EventKey"),
                        'scantype'=>value($post_obj, "ScanCodeInfo|ScanType"),
                        'scanresult'=>value($post_obj, "ScanCodeInfo|ScanResult")
                    );
                    $this->savemessage($M);
                    $this->sendkey($M, $M['text']);
                    break;
                case "scancode_waitmsg":            //扫码推事件且弹出“消息接收中”提示框的事件
                    $M['msgtype'] = 'scancode_waitmsg';
                    $M['text'] = array(
                        'text'=>value($post_obj, "EventKey"),
                        'scantype'=>value($post_obj, "ScanCodeInfo|ScanType"),
                        'scanresult'=>value($post_obj, "ScanCodeInfo|ScanResult")
                    );
                    $this->savemessage($M);
                    $this->sendkey($M, $M['text']);
                    break;
                case "pic_sysphoto":                //(*1)弹出系统拍照发图的事件
                case "pic_photo_or_album":          //(*1)弹出拍照或者相册发图的事件
                case "pic_weixin":                  //(*1)弹出微信相册发图器的事件
                case "location_select":             //(*2)弹出地理位置选择器的事件
            }
        }
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
        $wherearr = array('alid'=>$M['alid'], 'type'=>'weixin', 'openid'=>$M['openid']);
        if ($notype) unset($wherearr['type']);
        $row = db_getone(table('fans'), $wherearr);
        if ($only) return $row;
        //
        if (empty($row) || $M['eventtype'] == 'subscribe') {
            $CI =& get_instance();
            $CI->load->library('communication');
            if ($this->iscorp()) {
                $url  = 'https://qyapi.weixin.qq.com/cgi-bin/user/get?access_token='.$this->token().'&userid='.$M['openid'];
                $content = $CI->communication->ihttp_request($url);
                $_content = isset($content['content'])?json_decode($content['content'], true):'';
                if (value($_content, 'errcode') == '0') {
                    $M['user_name'] = value($_content, 'name');
                    $M['sex'] = str_replace(array("1","2"), array("男","女"),value($_content, 'gender'));
                    $M['avatar'] = value($_content, 'avatar');
                    $M['userphone'] = value($_content, 'mobile');
                    $M['useremail'] = value($_content, 'email');
                }
            }else{
                $url  = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$this->token().'&openid='.$M['openid'].'&lang=zh_CN';
                $content = $CI->communication->ihttp_request($url);
                $_content = isset($content['content'])?json_decode($content['content'], true):'';
                if (value($_content, 'subscribe') == '1') {
                    $M['user_name'] = value($_content, 'nickname');
                    $M['sex'] = str_replace(array("1","2"), array("男","女"),value($_content, 'sex'));
                    $M['city'] = value($_content, 'city');
                    $M['province'] = value($_content, 'province');
                    $M['avatar'] = value($_content, 'headimgurl');
                }
            }
            $this->error_code($_content);
            if (empty($M['user_name'])) { $M['user_name'] = substr($M['openid'],0,8).'***'.substr($M['openid'],-8); }
        }
        if ($M['user_name']) {
            $CI =& get_instance();
            $CI->load->helper('emoji_other');
            $M['user_name'] = emoji_unified_to_null($M['user_name']);
        }
        //基本信息
        $iarr = array(
            'type'=>'weixin',
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
        if ($M['eventtype'] == 'unsubscribe') {
            $iarr['follow'] = 0;
        }elseif ($M['eventtype'] == 'subscribe' || isset($M['msgtype'])) {
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
        $iarr['type'] = 'weixin';
        $iarr['msgtype'] = $M['msgtype'];
        $iarr['text'] = is_array($M['text'])?array2string($M['text']):$M['text'];
        $iarr['tobe'] = $tobe; //0接收、1发送
        $iarr['indate'] = ($M['createtime'] && $M['createtime']>0)?$M['createtime']:SYS_TIME;
        $iarr['emulator'] = defined('_ISEMULATOR')?1:0;
        if (isset($_A['corp_agentid'])) {
            $wx_corp = $_A['al']['wx_corp'][$_A['corp_agentid']];
            $iarr['setting'] = array();
            $iarr['setting']['corp_agentid'] = $wx_corp['agentid'];
            $iarr['setting']['corp_name'] = $wx_corp['name'];
            $iarr['setting'] = array2string($iarr['setting']);
        }
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
        $_data = array();
        $_data['touser'] = $M['openid'];
        $_data['msgtype'] = 'text';
        $_data['text']['content'] = new_addslashes($M['text']);
        //发给这个关注的用户
        $CI =& get_instance();
        $CI->load->library('communication');
        if ($this->iscorp()) {
            $url = "https://qyapi.weixin.qq.com/cgi-bin/message/send?access_token=".$this->token();
            if (is_array($_data) && $_A['corp_agentid']) {
                $_data['agentid'] = $_A['corp_agentid'];
            }
        }else{
            $url  = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$this->token();
        }
        $content = $CI->communication->ihttp_date($url, $_data);
        $content = isset($content['content'])?json_decode($content['content'], true):'';
        $this->error_code($content, true);
        $errcode = value($content, 'errcode');
        if ($errcode != 0) {
            $rcode = value($content, 'errmsg');
            db_update(tableal('message'), array('err'=>'错误:'.$rcode), array('id'=>$mid));
        }
        return $content;
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
        $imagetext = new_addslashes(string2array($M['text']));
        $_data = array();
        $_data['touser'] = $M['openid'];
        $_data['msgtype'] = 'news';
        $_data['news']['articles'] = array();
        $_data['news']['articles'][] = array(
            'title'=>$imagetext['title'],
            'description'=>$imagetext['desc'],
            'url'=>$imagetext['url'],
            'picurl'=>fillurl($imagetext['img'])
        );
        //发给这个关注的用户
        $CI =& get_instance();
        $CI->load->library('communication');
        if ($this->iscorp()) {
            $url = "https://qyapi.weixin.qq.com/cgi-bin/message/send?access_token=".$this->token();
            if (is_array($_data) && $_A['corp_agentid']) {
                $_data['agentid'] = $_A['corp_agentid'];
            }
        }else{
            $url  = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$this->token();
        }
        $content = $CI->communication->ihttp_date($url, $_data);
        $content = isset($content['content'])?json_decode($content['content'], true):'';
        $this->error_code($content, true);
        $errcode = value($content, 'errcode');
        if ($errcode != 0) {
            $rcode = value($content, 'errmsg');
            db_update(tableal('message'), array('err'=>'错误:'.$rcode), array('id'=>$mid));
        }
        return $content;
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
        if ($library['type'] == 'onlyimg') {
            $library = new_addslashes($library);
            $_data = array();
            $_data['touser'] = $M['openid'];
            $_data['msgtype'] = 'news';
            $_data['news']['articles'] = array();
            $_data['news']['articles'][] = array(
                'title'=>$library['title'],
                'description'=>$library['descriptions'],
                'url'=>appurl('library/'.$library['id']),
                'picurl'=>fillurl($library['img'])
            );
        }elseif ($library['type'] == 'manyimg') {
            $libset = string2array($library['setting']);
            $_data = array();
            $_data['touser'] = $M['openid'];
            $_data['msgtype'] = 'news';
            $_data['news']['articles'] = array();
            foreach($libset AS $k=>$item) {
                $item = new_addslashes($item);
                $_data['news']['articles'][] = array(
                    'title'=>$item['title'],
                    'description'=>$item['descriptions'],
                    'url'=>appurl('library/'.$library['id'].'/'.$k),
                    'picurl'=>fillurl($item['img'])
                );
            }
        }else{
            return false;
        }
        //发给这个关注的用户
        $CI =& get_instance();
        $CI->load->library('communication');
        if ($this->iscorp()) {
            $url = "https://qyapi.weixin.qq.com/cgi-bin/message/send?access_token=".$this->token();
            if (is_array($_data) && $_A['corp_agentid']) {
                $_data['agentid'] = $_A['corp_agentid'];
            }
        }else{
            $url  = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$this->token();
        }
        $content = $CI->communication->ihttp_date($url, $_data);
        $content = isset($content['content'])?json_decode($content['content'], true):'';
        $this->error_code($content, true);
        $errcode = value($content, 'errcode');
        if ($errcode != 0) {
            $rcode = value($content, 'errmsg');
            db_update(tableal('message'), array('err'=>'错误:'.$rcode), array('id'=>$mid));
        }
        return $content;
    }

    /**
     * 发送 图片、语音、视频
     * @param array $M
     * @return bool|mixed|string
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
        $extension = pathinfo($M['text'], PATHINFO_EXTENSION);
        $CI =& get_instance();
        $CI->load->helper('communication');
        $cmd5 = md5($M['text'].$row['wx_appid']);
        $cmd5t = "media_upload_".$M['alid']."_".$cmd5;
        $tmp = db_getone(table('tmp'), array('title'=>$cmd5t, '`indate`>'=>(SYS_TIME - 259200)),"`indate` DESC");
        if (empty($tmp)) {
            if ($M['msgtype'] == 'video' && $extension != "mp4") {
                $tmp = array();
            }else{
                $data = array('media' => '@'.BASE_PATH.$M['text']);
                if ($this->iscorp()) {
                    $sendapi = "https://qyapi.weixin.qq.com/cgi-bin/media/upload?access_token=".$this->token()."&type=".$M['msgtype'];
                }else{
                    $sendapi = "https://api.weixin.qq.com/cgi-bin/media/upload?access_token=".$this->token()."&type=".$M['msgtype'];
                }
                $resp = ihttp_request($sendapi, $data);
                $respcon = @json_decode($resp['content'], true);
                $this->error_code($respcon);
                $media_id = $respcon['media_id'];
                if ($media_id) {
                    $tmp = array();
                    $tmp['title'] = $cmd5t;
                    $tmp['value'] = $media_id;
                    $tmp['indate'] = $respcon['created_at'];
                    $tmp['content'] = $M['text'];
                    db_delete(table('tmp'), "`title` LIKE 'media_upload_".$M['alid']."_%' AND `indate`<".(SYS_TIME - 259200)."");
                    db_insert(table('tmp'), $tmp, true);
                }
            }
        }
        $_data = array();
        $_data['touser'] = $M['openid'];
        if ($tmp['value']) {
            $_data['msgtype'] = $M['msgtype'];
            //
            if ($M['msgtype'] == 'video') {
                $_data[$M['msgtype']] = array(
                    'media_id'=>$tmp['value'],
                    'thumb_media_id'=>'',
                    'title'=>($M['video_title']?$M['video_title']:'视频'),
                    'description'=>''
                );
            }else{
                $_data[$M['msgtype']] = array(
                    'media_id'=>$tmp['value']
                );
            }
        }else{
            $_data['msgtype'] = 'news';
            $_data['news']['articles'] = array();
            $_data['news']['articles'][] = array(
                'title'=>'点击查看'.str_replace(array('image','voice','video'), array('图片','语音','视频'), $M['msgtype']),
                'description'=>'',
                'url'=>appurl("system/showmedia/")."&type=".$M['msgtype']."&value=".urlencode($M['text']),
                'picurl'=>''
            );
        }
        //发给这个关注的用户
        $CI =& get_instance();
        $CI->load->library('communication');
        if ($this->iscorp()) {
            $url = "https://qyapi.weixin.qq.com/cgi-bin/message/send?access_token=".$this->token();
            if (is_array($_data) && $_A['corp_agentid']) {
                $_data['agentid'] = $_A['corp_agentid'];
            }
        }else{
            $url  = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$this->token();
        }
        $content = $CI->communication->ihttp_date($url, $_data);
        $content = isset($content['content'])?json_decode($content['content'], true):'';
        $this->error_code($content, true);
        $errcode = value($content, 'errcode');
        if ($errcode != 0) {
            $rcode = value($content, 'errmsg');
            db_update(tableal('message'), array('err'=>'错误:'.$rcode), array('id'=>$mid));
        }
        return $content;
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
        if ($M['msgtype'] == "follow"){
            //关注、带参数二维码关注
            $content['type'] = $row['setting']['attention']['content']['type'];
            $content['text'] = $row['setting']['attention']['content']['text'];
            $content['material'] = $row['setting']['attention']['content']['material'];
            $content['imagetext'] = $row['setting']['attention']['content']['imagetext'];
        }elseif (substr($key,0,1) == "#" && $M['msgtype'] == "click"){
            //菜单特殊关键词
            $content['type'] = 'text';
            $content['text'] = substr($key, 1);
            if (strpos($content['text'], "多客服") === 0) $content['_is_service'] = true;
        }elseif ($M['msgtype'] == "text" || $M['msgtype'] == "click"){
            //发送关键词、菜单关键词
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
                $setting = string2array($reply['setting'], true);
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
        $M['msgtype'] = $content['type'];
        //自定义接口
        if (isset($setting) && $setting['apitype']) {
            if (strexists($setting['api_url'], 'http://') || strexists($setting['api_url'], 'https://')) {
                list($resxml, $result) = $this->procRemote($setting, $M);
                $M['msgtype'] = 'text';
                $M['text'] = '{自定义接口回复}';
                if ($result['MsgType'] == "text") {
                    $M['text'].= ' - 文本：'.$result['Content'];
                }else{
                    $M['text'].= ' - '.str_replace(
                        array('image', 'voice', 'video', 'music', 'news'),
                        array('图片', '语音', '视频', '音乐', '图文'),
                        $result['MsgType']);
                }
                $this->savemessage($M, 1);
                if ($result) {
                    echo $this->resecho($resxml);
                    return true;
                }else{
                    return false;
                }
            }
        }
        //文本
        if ($content['type'] == 'text') {
            $M['text'] = $content['text'];
            //
            if (isset($content['_is_service'])) {
                $this->respText(trim(substr($content['text'], strlen("多客服"))), true);
                echo $this->resecho("<xml>
                     <ToUserName><![CDATA[".$M['openid']."]]></ToUserName>
                     <FromUserName><![CDATA[".$M['appid']."]]></FromUserName>
                     <CreateTime>".SYS_TIME."</CreateTime>
                     <MsgType><![CDATA[transfer_customer_service]]></MsgType>
                </xml>");
            }else{
                $this->savemessage($M, 1);
                echo $this->resecho("<xml>
                    <ToUserName><![CDATA[".$M['openid']."]]></ToUserName>
                    <FromUserName><![CDATA[".$M['appid']."]]></FromUserName>
                    <CreateTime>".SYS_TIME."</CreateTime>
                    <MsgType><![CDATA[text]]></MsgType>
                    <Content><![CDATA[".$this->send2text($content['text'])."]]></Content>
                </xml>");
            }
            return true;
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
            $imagetext = $content['imagetext'];
            $itemtext = "<xml>
                <ToUserName><![CDATA[".$M['openid']."]]></ToUserName>
                <FromUserName><![CDATA[".$M['appid']."]]></FromUserName>
                <CreateTime>".SYS_TIME."</CreateTime>
                <MsgType><![CDATA[news]]></MsgType>
                <ArticleCount>1</ArticleCount>
                <Articles>";
            $itemtext.= "<item>
				<Title><![CDATA[".$imagetext['title']."]]></Title>
				<Description><![CDATA[".$imagetext['desc']."]]></Description>
				<PicUrl><![CDATA[".fillurl($imagetext['img'])."]]></PicUrl>
				<Url><![CDATA[".$imagetext['url']."]]></Url>
				</item>";
            $itemtext.= "</Articles></xml>";
            echo $this->resecho($itemtext);
            return true;
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
            $itemtext = "";
            if ($library['type'] == 'onlyimg') {
                $itemtext.= "<xml>
					<ToUserName><![CDATA[".$M['openid']."]]></ToUserName>
					<FromUserName><![CDATA[".$M['appid']."]]></FromUserName>
					<CreateTime>".SYS_TIME."</CreateTime>
					<MsgType><![CDATA[news]]></MsgType>
					<ArticleCount>1</ArticleCount>
					<Articles>";
                $itemtext.= "<item>
                    <Title><![CDATA[".$library['title']."]]></Title>
                    <Description><![CDATA[".$library['descriptions']."]]></Description>
                    <PicUrl><![CDATA[".fillurl($library['img'])."]]></PicUrl>
                    <Url><![CDATA[".appurl('library/'.$library['id'])."]]></Url>
                    </item>";
                $itemtext.= "</Articles></xml>";
            }elseif ($library['type'] == 'manyimg') {
                $libset = string2array($library['setting']);
                $itemtext.= "<xml>
					<ToUserName><![CDATA[".$M['openid']."]]></ToUserName>
					<FromUserName><![CDATA[".$M['appid']."]]></FromUserName>
					<CreateTime>".SYS_TIME."</CreateTime>
					<MsgType><![CDATA[news]]></MsgType>
					<ArticleCount>".count($libset)."</ArticleCount>
					<Articles>";
                foreach($libset AS $k=>$item) {
                    $itemtext.= "<item>
						<Title><![CDATA[".$item['title']."]]></Title>
						<Description><![CDATA[".$item['descriptions']."]]></Description>
						<PicUrl><![CDATA[".fillurl($item['img'])."]]></PicUrl>
						<Url><![CDATA[".appurl('library/'.$library['id'].'/'.$k)."]]></Url>
						</item>";
                }
                $itemtext.= "</Articles></xml>";
            }
            //发给这个关注的用户
            echo $this->resecho($itemtext);
            return true;
        }
        //图片、语音、视频
        if (in_array($content['type'], array('image','voice','video')) && $content[$content['type']]) {
            $M['text'] = $content[$content['type']];
            $this->savemessage($M, 1);
            //
            $extension = pathinfo($content[$content['type']], PATHINFO_EXTENSION);
            $CI =& get_instance();
            $CI->load->helper('communication');
            $cmd5 = md5($content[$content['type']].$M['appid']);
            $cmd5t = "media_upload_".$M['alid']."_".$cmd5;
            $tmp = db_getone(table('tmp'), array('title'=>$cmd5t, '`indate`>'=>(SYS_TIME - 259200)),"`indate` DESC");
            if (empty($tmp)) {
                if ($content['type'] == 'video' && $extension != "mp4") {
                    $tmp = array();
                }else{
                    if (!defined('_ISEMULATOR')) {
                        $this->sendCustomNotice(array(
                            'touser'=>$M['openid'],
                            'msgtype'=>'text',
                            'text'=>array('content'=>$this->encode($this->send2text("正在查询中，请等待......")))
                        ));
                    }
                    $data = array('media' => '@'.BASE_PATH.$content[$content['type']]);
                    if ($this->iscorp()) {
                        $sendapi = "https://qyapi.weixin.qq.com/cgi-bin/media/upload?access_token=".$this->token()."&type=".$content['type'];
                    }else{
                        $sendapi = "https://api.weixin.qq.com/cgi-bin/media/upload?access_token=".$this->token()."&type=".$content['type'];
                    }
                    $resp = ihttp_request($sendapi, $data);
                    $respcon = @json_decode($resp['content'], true);
                    $this->error_code($respcon);
                    $media_id = $respcon['media_id'];
                    if ($media_id) {
                        $tmp = array();
                        $tmp['title'] = $cmd5t;
                        $tmp['value'] = $media_id;
                        $tmp['indate'] = $respcon['created_at'];
                        $tmp['content'] = $content[$content['type']];
                        db_delete(table('tmp'), "`title` LIKE 'media_upload_".$M['alid']."_%' AND `indate`<".(SYS_TIME - 259200)."");
                        db_insert(table('tmp'), $tmp, true);
                    }
                }
            }
            if ($tmp['value']) {
                $itemtext = "<xml>
                    <ToUserName><![CDATA[".$M['openid']."]]></ToUserName>
                    <FromUserName><![CDATA[".$M['appid']."]]></FromUserName>
                    <CreateTime>".SYS_TIME."</CreateTime>
                    <MsgType><![CDATA[".$content['type']."]]></MsgType>";
                if ($content['type'] == 'image') {
                    $itemtext.= "<Image>
                        <MediaId><![CDATA[".$tmp['value']."]]></MediaId>
                    </Image>";
                }elseif ($content['type'] == 'voice') {
                    $itemtext.= "<Voice>
                        <MediaId><![CDATA[".$tmp['value']."]]></MediaId>
                    </Voice>";
                }elseif ($content['type'] == 'video') {
                    $itemtext.= "<Video>
                        <MediaId><![CDATA[".$tmp['value']."]]></MediaId>
                        <Title><![CDATA[".($content['video_title']?$content['video_title']:'视频')."]]></Title>
                        <Description><![CDATA[".$content['video_desc']."]]></Description>
                    </Video>";
                }
                $itemtext.= "</xml>";
                echo $this->resecho($itemtext);
            }else{
                $itemtext = "<xml>
                    <ToUserName><![CDATA[".$M['openid']."]]></ToUserName>
                    <FromUserName><![CDATA[".$M['appid']."]]></FromUserName>
                    <CreateTime>".SYS_TIME."</CreateTime>
                    <MsgType><![CDATA[news]]></MsgType>
                    <ArticleCount>1</ArticleCount>
                    <Articles>";
                $itemtext.= "<item>
                    <Title><![CDATA[点击查看".str_replace(array('image','voice','video'), array('图片','语音','视频'), $content['type'])."]]></Title>
                    <Description><![CDATA[]]></Description>
                    <PicUrl><![CDATA[]]></PicUrl>
                    <Url><![CDATA[".appurl("system/showmedia/")."&type=".$content['type']."&value=".urlencode($content[$content['type']])."]]></Url>
                    </item>";
                $itemtext.= "</Articles></xml>";
                echo $this->resecho($itemtext);
            }
            return true;
        }
        return false;
    }

    /**
     * 判断加密 EncryptMsg
     * @param $responce
     * @return string
     */
    public function resecho($responce) {
        global $_GPC;
        if ($this->iscorp() && !defined('_ISEMULATOR')) {
            $sEncryptMsg = "";
            $errCode = $this->wxcpt->EncryptMsg($responce, $_GPC["timestamp"], $_GPC["nonce"], $sEncryptMsg);
            if ($errCode == 0) {
                $responce = $sEncryptMsg;
            }
        }
        return $responce;
    }

    /**
     * 自定义接口
     * @param $item
     * @param $M
     * @return array
     */
    public function procRemote($item, $M)
    {
        $CI =& get_instance();
        $CI->load->helper('communication');
        if (!strexists($item['api_url'], '?')) {
            $item['api_url'] .= '?';
        } else {
            $item['api_url'] .= '&';
        }
        $sign = array(
            'timestamp' => SYS_TIME,
            'nonce' => generate_password(10,1),
        );
        $signkey = array($item['api_token'], $sign['timestamp'], $sign['nonce']);
        sort($signkey, SORT_STRING);
        $sign['signature'] = sha1(implode($signkey));
        $item['api_url'] .= http_build_query($sign, '', '&');

        $body = "<xml>" . PHP_EOL .
            "<ToUserName><![CDATA[".$M['openid']."]]></ToUserName>" . PHP_EOL .
            "<FromUserName><![CDATA[".$M['appid']."]]></FromUserName>" . PHP_EOL .
            "<CreateTime>".SYS_TIME."</CreateTime>" . PHP_EOL .
            "<MsgType><![CDATA[text]]></MsgType>" . PHP_EOL .
            "<Content><![CDATA[".$M['text']."]]></Content>" . PHP_EOL .
            "<MsgId>".SYS_TIME.generate_password(3,1)."</MsgId>" . PHP_EOL .
            "</xml>";
        $response = ihttp_request($item['api_url'], $body, array('CURLOPT_HTTPHEADER' => array('Content-Type: text/xml; charset=utf-8')));
        $result = array();
        require_once "weixin/response.php";
        $wxapi = new WXAPI_RESPONSE($M['openid'], $M['appid']);
        if (!is_error($response)) {
            $temp = @json_decode($response['content'], true);
            if (is_array($temp)) {
                $result = $wxapi->buildResponse($temp);
            } else {
                if (!empty($response['content'])){
                    $obj = @simplexml_load_string(trim($response['content']), 'SimpleXMLElement', LIBXML_NOCDATA);
                    if($obj instanceof SimpleXMLElement) {
                        $type = strtolower(strval($obj->MsgType));
                        if($type == 'text') {
                            $result = $wxapi->respText(strval($obj->Content));
                        }
                        if($type == 'image') {
                            $imid = strval($obj->Image->MediaId);
                            $result = $wxapi->respImage($imid);
                        }
                        if($type == 'voice') {
                            $imid = strval($obj->Voice->MediaId);
                            $result = $wxapi->respVoice($imid);
                        }
                        if($type == 'video') {
                            $video = array();
                            $video['video'] = strval($obj->Video->MediaId);
                            $video['thumb'] = strval($obj->Video->ThumbMediaId);
                            $result = $wxapi->respVideo($video);
                        }
                        if($type == 'music') {
                            $music = array();
                            $music['title'] = strval($obj->Music->Title);
                            $music['description'] = strval($obj->Music->Description);
                            $music['musicurl'] = strval($obj->Music->MusicUrl);
                            $music['hqmusicurl'] = strval($obj->Music->HQMusicUrl);
                            $result = $wxapi->respMusic($music);
                        }
                        if($type == 'news') {
                            $news = array();
                            foreach($obj->Articles->item as $item) {
                                $news[] = array(
                                    'title' => strval($item->Title),
                                    'description' => strval($item->Description),
                                    'picurl' => strval($item->PicUrl),
                                    'url' => strval($item->Url)
                                );
                            }
                            $result = $wxapi->respNews($news);
                        }
                    }
                }
            }
        }
        $reswml = $wxapi->buildRespond($result);
        if (defined('_ISEMULATOR')) {
            $result = $wxapi->array2xml($result);
        }
        return array($reswml, $result);
    }

    /**
     * 从模块发送文本信息
     * @param string $content
     * @param bool $isret
     * @return string
     */
    public function respText($content = '', $isret = false)
    {
        global $_A;
        if (empty($content)) return 'Invaild value';
        if (!defined('_ISPROCESSOR')) define('_ISPROCESSOR', true);
        $_A['M']['text'] = $content;
        $_A['M']['msgtype'] = 'text';
        $mid = $this->savemessage($_A['M'], 1);
        //
        $content = str_replace("\r\n", "\n", $content);
        $send = array();
        $send['touser'] = trim($_A['M']['openid']);
        $send['msgtype'] = 'text';
        $send['text'] = array('content' => $this->encode($this->send2text($content)));
        $Request = $this->sendCustomNotice($send);
        if (is_error($Request) && $Request['message'] && $mid) {
            db_update(tableal('message'), array('err'=>$Request['message']), array('id'=>$mid));
        }
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
     * @param string $tis       上传素材提示语
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
        $mid = $this->savemessage($_A['M'], 1);
        //
        $media_id = $this->media_upload($content, $type, trim($_A['M']['openid']), $tis);
        if (empty($media_id)) {
            $send = array();
            $send['touser'] = trim($_A['M']['openid']);
            $send['msgtype'] = 'news';
            $send['news']['articles'][] = $this->encode(array(
                'title'=>'点击查看'.str_replace(array('image','voice','video'), array('图片','语音','视频'), $type),
                'description'=>'',
                'url'=>appurl("system/showmedia/")."&type=".$type."&value=".urlencode($content)
            ));
        }else{
            $send = array();
            $send['touser'] = trim($_A['M']['openid']);
            $send['msgtype'] = $type;
            $send[$type] = array('media_id' => $media_id);
        }
        $Request = $this->sendCustomNotice($send);
        if (is_error($Request) && $Request['message'] && $mid) {
            db_update(tableal('message'), array('err'=>$Request['message']), array('id'=>$mid));
        }
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
        if (!defined('_ISPROCESSOR')) define('_ISPROCESSOR', true);
        $_A['M']['text'] = array2string($arr);
        $_A['M']['msgtype'] = 'imagetext';
        $mid = $this->savemessage($_A['M'], 1);
        //
        $send = array();
        $send['touser'] = trim($_A['M']['openid']);
        $send['msgtype'] = 'news';
        $send['news']['articles'] = $this->newshandle($arr);
        $Request = $this->sendCustomNotice($send);
        if (is_error($Request) && $Request['message'] && $mid) {
            db_update(tableal('message'), array('err'=>$Request['message']), array('id'=>$mid));
        }
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
        $image_text_msg = array();
        foreach($array AS $item) {
            $image_text_msg[] = $this->encode(array(
                'title'=>$item['title'],
                'description'=>$item['desc'],
                'url'=>$item['url'],
                'picurl'=>$item['img']
            ));
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
                            $es_site->respond('weixin');
                        }
                        if (method_exists($es_site, 'weixinrespond')) {
                            $es_site->weixinrespond();
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
     * 上传素材
     * @param string $url               素材原地址
     * @param string $type              素材类型 分别有图片（image）、语音（voice）、视频（video）和缩略图（thumb）
     * @param string $waittisopenid     上传素材等候提示对象openid
     * @param string $waittis           上传素材等候提示
     * @return mixed                    返回media_id素材ID
     */
    public function media_upload($url, $type, $waittisopenid = '', $waittis = '正在查询中，请等待......')
    {
        global $_A;
        $CI =& get_instance();
        $CI->load->helper('communication');
        $cmd5 = md5($url.$_A['al']['wx_appid']);
        $cmd5t = "media_upload_".$_A['al']['id']."_".$cmd5;
        $tmp = db_getone(table('tmp'), array('title'=>$cmd5t, '`indate`>'=>(SYS_TIME - 259200)),"`indate` DESC");
        if (empty($tmp)) {
            if ($type == 'video' && $type != "mp4") {
                $tmp = array();
            }else{
                $tmp = array();
                $urlpath = $url;
                if (!file_exists($urlpath)) {
                    $urlpath = BASE_PATH.$url;
                }
                if (file_exists($urlpath)) {
                    if ($waittisopenid && !defined('_ISEMULATOR')) {
                        $this->sendCustomNotice(array(
                            'touser'=>$waittisopenid,
                            'msgtype'=>'text',
                            'text'=>array('content'=>$this->encode($this->send2text($waittis)))
                        ));
                    }
                    $data = array('media' => '@'.$urlpath);
                    if ($this->iscorp()) {
                        $sendapi = "https://qyapi.weixin.qq.com/cgi-bin/media/upload?access_token=".$this->token()."&type=".$type;
                    }else{
                        $sendapi = "https://api.weixin.qq.com/cgi-bin/media/upload?access_token=".$this->token()."&type=".$type;
                    }
                    $resp = ihttp_request($sendapi, $data);
                    $respcon = @json_decode($resp['content'], true);
                    $this->error_code($respcon);
                    $media_id = $respcon['media_id'];
                    if ($media_id) {
                        $tmp = array();
                        $tmp['title'] = $cmd5t;
                        $tmp['value'] = $media_id;
                        $tmp['indate'] = $respcon['created_at'];
                        $tmp['content'] = $type;
                        db_delete(table('tmp'), "`title` LIKE 'media_upload_".$_A['al']['id']."_%' AND `indate`<".(SYS_TIME - 259200)."");
                        db_insert(table('tmp'), $tmp, true);
                    }
                }
            }
        }
        return $tmp['value'];
    }

    /**
     * 添加菜单
     */
    /*public function addmenu($data)
    {

    }*/

    /**
     * 更新菜单
     * @param $data
     * @param int $agentid 企业应用的id
     * @return mixed|string
     */
    public function upmenu($data, $agentid = 0)
    {
        $_arr = array();
        foreach($data as $k=>$v){
            if (!isset($v['status'])) continue;
            $_arr[$k]['type'] = $v['keytype'];
            $_arr[$k]['name'] = $v['title'];
            if ($v['keytype'] == 'view'){
                $_arr[$k]['url'] = $v['keytext'];
            }else{
                $_arr[$k]['key'] = $v['keytext'];
            }
            $_arr[$k]['sub_button'] = array();
            $_arr[$k] = @array_map('urlencode', $_arr[$k]);
            //
            $_but = array();
            if (isset($v['child']) && !empty($v['child'])){
                unset($_arr[$k]['type']);
                unset($_arr[$k]['key']);
                unset($_arr[$k]['url']);
                $j = 0;
                foreach($v['child'] as $vc) {
                    if (!isset($vc['status'])) continue;
                    $_but[$j]['type'] = $vc['keytype'];
                    $_but[$j]['name'] = $vc['title'];
                    if ($vc['keytype'] == 'view'){
                        $_but[$j]['url'] = $vc['keytext'];
                    }else{
                        $_but[$j]['key'] = $vc['keytext'];
                    }
                    $_but[$j]['sub_button'] = array();
                    $_but[$j] = @array_map('urlencode', $_but[$j]);
                    $j++;
                }
                $_arr[$k]['sub_button'] = $_but;
            }
        }
        //
        if ($this->iscorp($agentid)) {
            $url  = "https://qyapi.weixin.qq.com/cgi-bin/menu/create?access_token=".$this->token()."&agentid=".$agentid;
        }else{
            $url  = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$this->token();
        }
        $_arr = array("button"=>$_arr);
        $data = urldecode(json_encode($_arr));
        //
        $CI =& get_instance();
        $CI->load->library('communication');
        $content = $CI->communication->ihttp_date($url, $data);
        $_content = isset($content['content'])?json_decode($content['content'], true):'';
        $this->error_code($_content, true);
        return $_content;
    }

    /**
     * 获取菜单
     * @param int $agentid 企业应用的id
     * @return mixed|string
     */
    public function getmenu($agentid = 0)
    {
        $CI =& get_instance();
        $CI->load->library('communication');
        if ($this->iscorp($agentid)) {
            $url = "https://qyapi.weixin.qq.com/cgi-bin/menu/get?access_token=".$this->token()."&agentid=".$agentid;
        }else{
            $url = "https://api.weixin.qq.com/cgi-bin/menu/get?access_token=".$this->token();
        }
        $content = $CI->communication->ihttp_request($url);
        $_content = isset($content['content'])?json_decode($content['content'], true):'';
        $this->error_code($_content, true);
        return $_content;
    }

    /**
     * 删除菜单
     * @param int $agentid 企业应用的id
     * @return mixed|string
     */
    public function delmenu($agentid = 0)
    {
        $CI =& get_instance();
        $CI->load->library('communication');
        if ($this->iscorp($agentid)) {
            $url = "https://qyapi.weixin.qq.com/cgi-bin/menu/delete?access_token=".$this->token()."&agentid=".$agentid;
        }else{
            $url = "https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=".$this->token();
        }
        $content = $CI->communication->ihttp_request($url);
        $_content = isset($content['content'])?json_decode($content['content'], true):'';
        $this->error_code($_content, true);
        return $_content;
    }

    /**
     * 是否企业号
     * @param int $agentid
     * @return bool
     */
    public function iscorp($agentid = -1) {
        global $_A;
        if ($agentid > -1) {
            $_A['corp_agentid'] = $agentid;
        }
        if ($_A['al']['wx_level'] == 7) {
            return true;
        }else{
            return false;
        }
    }

    /**
     * 发送模板消息
     * @param string $tmid  模板ID
     * @param array $conarr 模板数据
     * @param string $url 链接地址
     * @param string $openid 用户OPENID
     * @param string $topcolor 头部颜色
     * @return bool|mixed
     */
    public function respTmplmsg($tmid, $conarr = array(), $url = '', $openid = '', $topcolor = '#44B549') {
        global $_A;
        if (empty($conarr)) return 'Invaild value';
        if (!defined('_ISPROCESSOR'))define('_ISPROCESSOR', true);
        $token = $this->token();
        if(is_error($token)){
            return $token;
        }
        $CI =& get_instance();
        $CI->load->helper('communication');
        $arr = array();
        $arr['touser'] = $openid?$openid:$_A['M']['openid'];
        $arr['template_id'] = $tmid;
        $arr['url'] = $url;
        $arr['topcolor'] = $topcolor?$topcolor:"#44B549";
        $arr['data'] = $conarr;
        if (defined('_ISEMULATOR')) {
            $arr['msgtype'] = 'tmplmsg';
            echo json_encode($arr); exit();
        }
        //
        $url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$token;
        $response = ihttp_post($url, json_encode($arr));

        if(is_error($response)) {
            return error(-1, "访问公众平台接口失败, 错误: {$response['message']}");
        }
        $result = @json_decode($response['content'], true);
        if(empty($result)) {
            return error(-1, "接口调用失败, 元数据: {$response['meta']}");
        } elseif(!empty($result['errcode'])) {
            return error(-1, "访问微信接口错误, 错误代码: {$result['errcode']}, 错误信息: {$result['errmsg']},错误详情：{$this->error_code($result['errcode'])}");
        }
        if (!defined('_ISSENDCUSTOMNOTICE')) define('_ISSENDCUSTOMNOTICE', true);
        return $result;
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
        if (defined('_ISEMULATOR')) {
            echo json_encode($data); exit();
        }
        $token = $this->token();
        if(is_error($token)){
            return $token;
        }
        get_instance()->load->helper('communication');
        if ($this->iscorp()) {
            $url = "https://qyapi.weixin.qq.com/cgi-bin/message/send?access_token={$token}";
            if (is_array($data) && $_A['corp_agentid']) {
                $data['agentid'] = $_A['corp_agentid'];
            }
        }else{
            $url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token={$token}";
        }
        $response = ihttp_request($url, urldecode(json_encode($data)));
        if(is_error($response)) {
            return error(-1, "访问公众平台接口失败, 错误: {$response['message']}");
        }
        $result = @json_decode($response['content'], true);
        if(empty($result)) {
            return error(-1, "接口调用失败, 元数据: {$response['meta']}");
        } elseif(!empty($result['errcode'])) {
            return error(-1, "访问微信接口错误：{$this->error_code($result['errcode'])}");
        }
        if (!defined('_ISSENDCUSTOMNOTICE')) define('_ISSENDCUSTOMNOTICE', true);
        return $result;
    }

    /** **********************************************************************************************/
    /** **********************************************************************************************/
    /** **********************************************************************************************/

    public function error_code(&$content, $replace = false) {
        global $_A;
        $code = $content;
        if (is_array($code) && isset($code['errcode'])) {
            $code = $code['errcode'];
        }
        if ($this->iscorp()) {
            $errors = @include "weixin/errmsg_corp.php";
        }else{
            $errors = @include "weixin/errmsg.php";
        }
        $code = strval($code);
        if($code == '40001' || $code == '40014' || $code == '42001') {
            $setting = string2array($_A['al']['setting']);
            $record = array();
            $record['token'] = '';
            $record['expire'] = 0;
            $setting['wx_token'] = json_encode($record);
            $_A['al']['setting'] = $setting;
            db_update(table('users_al'), array("setting"=>array2string($_A['al']['setting'])), array('id' => $_A['al']['id']));
            $returntxt = $errors[$code]?$errors[$code]:'微信公众平台授权异常, 系统已修复这个错误';
            $returntxt.= '【请刷新页面后重试】';
        }else{
            if($errors[$code]) {
                $returntxt = $errors[$code];
            } else {
                $returntxt = '未知错误'.$code;
            }
        }
        if ($replace && is_array($content) && isset($content['errmsg'])) {
            $content['errmsg'] = $returntxt;
        }
        return $returntxt;
    }

    public function fansAll() {
        global $_GPC;
        $token = $this->token();
        if(is_error($token)){
            return $token;
        }
        $url = 'https://api.weixin.qq.com/cgi-bin/user/get?access_token=' . $token;
        if(!empty($_GPC['next_openid'])) {
            $url .= '&next_openid=' . $_GPC['next_openid'];
        }
        $CI =& get_instance();
        $response = $CI->communication->ihttp_request($url);
        if(is_error($response)) {
            return error(-1, "访问公众平台接口失败, 错误: {$response['message']}");
        }
        $result = @json_decode($response['content'], true);
        if(empty($result)) {
            return error(-1, "接口调用失败, 元数据: {$response['meta']}");
        } elseif(!empty($result['errcode'])) {
            return error(-1, "访问公众平台接口失败, 错误: {$result['errmsg']},错误详情：{$this->error_code($result['errcode'])}");
        }
        $return = array();
        $return['total'] = $result['total'];
        $return['fans'] = $result['data']['openid'];
        $return['next'] = $result['next_openid'];
        return $return;
    }

    public function token()
    {
        global $_A;
        if (empty($_A['al']['wx_appid'])) return "";
        $setting = string2array($_A['al']['setting']);
        $wx_token = json_decode(value($setting, 'wx_token'), true);
        if ($wx_token['expire'] > SYS_TIME) {
            return $wx_token['token'];
        }
        //从网页中获取
        $CI =& get_instance();
        if ($this->iscorp()) {
            $url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid={$_A['al']['wx_appid']}&corpsecret={$_A['al']['wx_secret']}";
        }else{
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$_A['al']['wx_appid']}&secret={$_A['al']['wx_secret']}";
        }
        $CI->load->library('communication');
        $content = $CI->communication->ihttp_request($url);
        if($CI->communication->is_error($content)) {
            $error = '获取微信公众号授权失败, 请稍后重试！错误详情: ' . $content['message'];
            return $CI->communication->error(-1, $error);
        }
        $token = @json_decode($content['content'], true);
        if(empty($token) || !is_array($token) || empty($token['access_token']) || empty($token['expires_in'])) {
            $errorinfo = substr($content['meta'], strpos($content['meta'], '{'));
            $errorinfo = @json_decode($errorinfo, true);
            $error = '获取微信公众号授权失败, 请稍后重试！ 公众平台返回原始数据为: 错误代码-' . $errorinfo['errcode'] . '，错误信息-' . $errorinfo['errmsg'];
            return $CI->communication->error(-1, $error);
        }
        $record = array();
        $record['token'] = $token['access_token'];
        $record['expire'] = SYS_TIME + $token['expires_in'] - 200;
        $setting['wx_token'] = json_encode($record);
        $_A['al']['setting'] = $setting;
        db_update(table('users_al'), array("setting"=>array2string($_A['al']['setting'])), array('id' => $_A['al']['id']));
        return $token['access_token'];
    }

    public function jssdk()
    {
        global $_A;
        if (empty($_A['al']['wx_appid'])) return "";
        $setting = string2array($_A['al']['setting']);
        $wx_token = json_decode(value($setting, 'wx_jssdk'), true);
        if ($wx_token['expire'] > SYS_TIME) {
            return $wx_token['ticket'];
        }
        //从网页中获取
        $CI =& get_instance();
        if ($this->iscorp()) {
            $url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=".$this->token();
        }else{
            $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=".$this->token()."&type=jsapi";
        }
        $CI->load->library('communication');
        $content = $CI->communication->ihttp_request($url);
        if($CI->communication->is_error($content)) {
            $error = '调用接口获取微信公众号 jsapi_ticket 失败, 错误信息: ' . $content['message'];
            return $CI->communication->error(-1, $error);
        }
        $ticket = @json_decode($content['content'], true);
        $this->error_code($ticket, true);
        if(empty($ticket) || intval(($ticket['errcode'])) != 0) {
            return $CI->communication->error(-1, '获取微信公众号 jsapi_ticket 结果错误, 错误信息: ' . $ticket['errmsg']);
        }
        $record = array();
        $record['ticket'] = $ticket['ticket'];
        $record['expire'] = SYS_TIME + $ticket['expires_in'] - 200;
        $setting['wx_jssdk'] = json_encode($record);
        $_A['al']['setting'] = $setting;
        db_update(table('users_al'), array("setting"=>array2string($_A['al']['setting'])), array('id' => $_A['al']['id']));
        return $ticket['ticket'];
    }

    public function jssdkConfig($url = '', $module = ''){
        global $_A;
        if (empty($_A['al']['wx_appid'])) return "";
        if (empty($module)) $module = $_A['module'];
        $getjs_appid = value($_A, 'al|setting|other|getjs_appid');
        if ($getjs_appid) {
            $getjs_appoint = value($_A, 'al|setting|other|getjs_appoint', true);
            if (empty($getjs_appoint) || in_array($module, $getjs_appoint)) {
                return $this->jssdkConfig_get($url);
            }
        }
        $jsapiTicket = $this->jssdk();
        $CI =& get_instance();
        $CI->load->library('communication');
        if($CI->communication->is_error($jsapiTicket)) {
            $jsapiTicket = $jsapiTicket['message'];
        }
        $nonceStr = generate_password(16);
        $timestamp = SYS_TIME;
        $url = $url?$url:get_url();

        $string1 = "jsapi_ticket={$jsapiTicket}&noncestr={$nonceStr}&timestamp={$timestamp}&url={$url}";
        $signature = sha1($string1);

        $config = array(
            "appId"		=> $_A['al']['wx_appid'],
            "nonceStr"	=> $nonceStr,
            "timestamp" => "$timestamp",
            "signature" => $signature,
        );

        if(ENVIRONMENT == "development") {
            $config['url'] = $url;
            $config['string1'] = $string1;
            $config['name'] = $_A['al']['wx_name'];
        }

        return $config;
    }

    private function jssdkConfig_get($url = '') {
        global $_A;
        if (empty($_A['al']['wx_appid'])) return "";
        if (empty($_A['al']['setting']['other']['getjs_appid']) || empty($_A['al']['setting']['other']['getjs_secret'])) return "";
        require_once "weixin/jssdk.php";
        $jssdk = new WXAPI_JSSDK($_A['al']['setting']['other']['getjs_appid'], $_A['al']['setting']['other']['getjs_secret'], $url, $_A['al']['wx_level']);
        $signPackage = $jssdk->GetSignPackage();

        $config = array(
            "appId"		=> $signPackage['appId'],
            "nonceStr"	=> $signPackage['nonceStr'],
            "timestamp" => $signPackage['timestamp'],
            "signature" => $signPackage['signature']
        );

        if(ENVIRONMENT == "development") {
            $config['url'] = $signPackage['url'];
            $config['string1'] = $signPackage['rawString'];
            $config['name'] = '借用';
        }
        return $config;
    }

    public function encode($string) {
        if(!is_array($string)) return str_replace(array("%3A", "%2F", "%3a", "%2f", "%40"), array(":", "/", ":", "/", "@"), urlencode(str_replace('"','\"',$string)));
        foreach($string as $key => $val) $string[$key] = $this->encode($val);
        return $string;
    }

    public function decode($string) {
        if(!is_array($string)) return urldecode($string);
        foreach($string as $key => $val) $string[$key] = $this->decode($val);
        return $string;
    }

    public function send2text($content) {
        if ($content) {
            $content = preg_replace("/\[url=(.+?)\](.+?)\[\/url\]/is","<a href=\"\\1\">\\2</a>",$content);
            $content = preg_replace("/\[\/(.+?)\]/is", "/$1", $content);
        }
        return$content;
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

    public function corp_upapp()
    {
        global $_A;
        $CI =& get_instance();
        $CI->load->library('communication');
        $url = "https://qyapi.weixin.qq.com/cgi-bin/agent/list?access_token=".$_A['corp_token'];
        $content = $CI->communication->ihttp_request($url);
        if($CI->communication->is_error($content)) {
            return $CI->communication->error(-1, '网络错误！错误详情: ' . $content['message']);
        }
        $content = @json_decode($content['content'], true);
        if (empty($content['agentlist'])) {
            message(null, '获取完成，列表为空！');
            return $CI->communication->error(0, '获取完成，列表为空！');
        }
        $alist = array();
        foreach($content['agentlist'] AS $item) {
            $url = "https://qyapi.weixin.qq.com/cgi-bin/agent/get?access_token=".$_A['corp_token']."&agentid=".$item['agentid'];
            $agent = $CI->communication->ihttp_request($url);
            if(!$CI->communication->is_error($agent)) {
                $agent = @json_decode($agent['content'], true);
                if ($agent['agentid'] == $item['agentid']) {
                    $alist[$agent['agentid']] = $agent;
                }
            }
        }
        db_update(table('users_al'), array('wx_corp'=>array2string($alist)), array('id'=>$_A['al']['id']));
        return $alist;
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
                $this->data['alifunction'] = string2array($row['function']);
                unset($row['function']);
            }
        }else{
            $row = db_getone(table('users_al'), array('id'=>intval($this->data['id'])));
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
            if ($row['wx_level'] == 7) {
                $row['wx_corp'] = string2array($row['wx_corp']);
            }else{
                unset($row['wx_corp']);
            }
            //
            if (isset($_A['u']['userid']) && $_A['u']['userid'] == $row['userid']) {
                $_user = $_A['u'];
            }else{
                $_user = db_getone(table('users'), array('userid'=>$row['userid']));
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
        if (isset($_GET['sid']) && $_GET['sid'] == md52($_A['al']['id'], $_A['al']['wx_appid'])) {
            $_A['wx_token'] = '';
            $_A['wx_jssdkConfig'] = array();
        }else{
            $_A['wx_token'] = $this->token();
            $_A['wx_jssdkConfig'] = $this->jssdkConfig();
        }
        if ($this->iscorp()) {
            $_A['corp_token'] = $_A['wx_token'];
            $_A['corp_jssdkConfig'] = $_A['wx_jssdkConfig'];
        }
    }

    private function lastin($id) {
        if (!defined('_ISEMULATOR')) {
            db_update(table('users_al'), array('wx_lastin'=>SYS_TIME), array('id'=>$id));
        }
    }

    private function writeLog($text) {
        //return false;
        // $text=iconv("GBK", "UTF-8//IGNORE", $text);
        file_put_contents ( dirname ( __FILE__ ) . "/wx-log.txt", date ( "Y-m-d H:i:s" ) . "  " . $text . "\r\n", FILE_APPEND );
    }
}
