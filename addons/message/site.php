<?php
defined('BASEPATH') OR exit('No direct script access allowed');
include_once 'function.php';

class ES_Message extends CI_Model {

	public function __construct()
	{
		parent::__construct();
        $this->load->helper('tpl');
        $this->load->helper('cloud');
	}

    /**
     *
     */
    public function doWebIndex($parent = null)
    {
        $user = $this->user->getuser();
        $func = $this->user->functions();

        $page = value($parent, 1, 'int');
        $keyval = $this->input->get('keyval');
        $msgtype = $this->input->get('msgtype');
        $pageurl = urlencode($this->base->url[2]);
        $wheresql = " `alid`='".$func['al']['id']."'";
        $wheresqlstar = $wheresql." AND `star`=1";
        //搜索
        if ($msgtype && in_array($msgtype, array('text','click','view','image','voice','video'))){
            $wheresql.= " AND `msgtype`='".$msgtype."' ";
        }
        if ($keyval){
			if ($this->input->get('keytype')) {
				$wheresql.= " AND `openid` IN (SELECT openid FROM ".table("fans")." WHERE `user_name` LIKE '%".$keyval."%') ";
			}else{
				$wheresql.= " AND `text` LIKE '%".$keyval."%' ";
			}
        }
        $tthis = $this;

        $this->cs->show(get_defined_vars());
    }

    /**
     * 删除数据
     */
    public function doWebDel() {
        $this->user->getuser();
        $func = $this->user->functions();
        $reconfirm = $this->input->get('reconfirm');
        $msgtype = $this->input->get('msgtype');
        $mtitle = $this->input->get('mtitle');
        $wheresql = " `alid`='".$func['al']['id']."'";
        if ($msgtype && in_array($msgtype, array('text','click','view','image','voice','video'))){
            $wheresql.= " AND `msgtype`='".$msgtype."' ";
        }
        if (empty($reconfirm)){
            $larr = array();
            $larr[] = array('title'=>'确定删除', 'url'=>weburl('message/del')."&reconfirm=1&msgtype=".$msgtype);
            $larr[] = array('title'=>'我点错了，返回', 'url'=>weburl('message/index')."&msgtype=".$msgtype);
            message('再次确认', '确定要【'.$mtitle.'】吗？！', $larr);
        }
        db_delete(tableal("message"), $wheresql);
        message('删除数据', '删除成功！', weburl('message/index')."&msgtype=".$msgtype);
    }

    /**
     * 回复
     * @param null $parent
     */
    public function doWebReply($parent = null) {
        global $_A;
        $this->user->getuser();
        $func = $this->user->functions();

        $id = value($parent, 1, 'int');
        $arr = array();
        $arr['success'] = 0;
        //
        $row = $this->ddb->getone(tableal("message"), array('id'=>intval($id)));
        if (empty($row)) {
            $arr['message'] = "要回复的信息不存在";
            echo json_encode($arr); exit();
        }
        //
        $msgtype = $row['type'];
        if ($msgtype == "weixin") {
            $this->load->library('wx');
            $this->wx->setting($func['al']['id'], $parent, $this->ddb);
            if ($_A['al']['wx_level'] == 7) {
                $row['setting'] = string2array($row['setting']);
                $_A['corp_agentid'] = $row['setting']['corp_agentid'];
            }
        }else{
            $this->load->library('fuwu');
            $this->fuwu->setting($func['al']['id'], $parent, $this->ddb);
        }
        //
        $replutext = $this->input->post('replutext');
        if ($replutext['type'] == 'text') {
            $text = cut_str($replutext['text'], 300, 0, "...");
            if (empty($text)) {
                $arr['message'] = "请输入要回复的内容";
                echo json_encode($arr); exit();
            }
            $M = $row;
            $M['msgtype'] = 'text';
            $M['text'] = $text;
            if ($msgtype == "weixin") {
                $return_msg = $this->wx->sendtext($M);
                if (value($return_msg, 'errcode') != '0'){
                    $arr['message'] = "【失败】".value($return_msg, 'errmsg');
                    echo json_encode($arr); exit();
                }
            }else{
                $return_msg = object_array($this->fuwu->sendtext($M));
                if (value($return_msg, 'alipay_mobile_public_message_custom_send_response|code') != '200'){
                    $arr['message'] = "【失败】".value($return_msg, 'alipay_mobile_public_message_custom_send_response|code');
                    echo json_encode($arr); exit();
                }
            }
            $uarr = array();
            $uarr['reply'] = $text;
            $uarr['replydate'] = SYS_TIME;
            $this->ddb->update(tableal("message"), $uarr, array('id'=>$row['id']));
            $arr['text'] = $uarr['reply'];

        }elseif ($replutext['type'] == 'imagetext') {
            if (!isset($replutext['imagetext'])) {
                $arr['message'] = "请输入要回复的内容";
                echo json_encode($arr); exit();
            }
            $M = $row;
            $M['msgtype'] = 'imagetext';
            $M['text'] = array2string($replutext['imagetext']);
            if ($msgtype == "weixin") {
                $return_msg = $this->wx->sendimagetext($M);
                if (value($return_msg, 'errcode') != '0'){
                    $arr['message'] = "【失败】".value($return_msg, 'errmsg');
                    echo json_encode($arr); exit();
                }
            }else{
                $return_msg = object_array($this->fuwu->sendimagetext($M));
                if (value($return_msg, 'alipay_mobile_public_message_custom_send_response|code') != '200'){
                    $arr['message'] = "【失败】".value($return_msg, 'alipay_mobile_public_message_custom_send_response|code');
                    echo json_encode($arr); exit();
                }
            }
            $uarr = array();
            $uarr['reply'] = array2string($replutext['imagetext']);
            $uarr['replydate'] = SYS_TIME;
            $this->ddb->update(tableal("message"), $uarr, array('id'=>$row['id']));
            $arr['text'] = $uarr['reply'];

        }elseif ($replutext['type'] == 'material') {
            $M = $row;
            $M['msgtype'] = 'material';
            $M['material'] = $replutext['material'];
            if ($msgtype == "weixin") {
                $return_msg = $this->wx->sendmaterial($M);
                if (value($return_msg, 'errcode') != '0'){
                    $arr['message'] = "【失败】".value($return_msg, 'errmsg');
                    echo json_encode($arr); exit();
                }
            }else{
                $return_msg = object_array($this->fuwu->sendmaterial($M));
                if (value($return_msg, 'alipay_mobile_public_message_custom_send_response|code') != '200'){
                    $arr['message'] = "【失败】".value($return_msg, 'alipay_mobile_public_message_custom_send_response|code');
                    echo json_encode($arr); exit();
                }
            }
            $uarr = array();
            $uarr['reply'] = "广播素材：".$replutext['material'];
            $uarr['replydate'] = SYS_TIME;
            $this->ddb->update(tableal("message"), $uarr, array('id'=>$row['id']));
            $arr['text'] = $uarr['reply'];

        }elseif (in_array($replutext['type'], array('image','voice','video'))) {
            if (empty($replutext[$replutext['type']])) {
                $arr['message'] = "请选择要发送的文件！";
                echo json_encode($arr); exit();
            }
            $M = $row;
            $M['msgtype'] = $replutext['type'];
            $M['text'] = $replutext[$replutext['type']];
            if ($msgtype == "weixin") {
                $return_msg = $this->wx->sendmedia($M);
                if (value($return_msg, 'errcode') != '0'){
                    $arr['message'] = "【失败】".value($return_msg, 'errmsg');
                    echo json_encode($arr); exit();
                }
            }else{
                $return_msg = object_array($this->fuwu->sendmedia($M));
                if (value($return_msg, 'alipay_mobile_public_message_custom_send_response|code') != '200'){
                    $arr['message'] = "【失败】".value($return_msg, 'alipay_mobile_public_message_custom_send_response|code');
                    echo json_encode($arr); exit();
                }
            }
            $uarr = array();
            $uarr['reply'] = $replutext[$replutext['type']];
            $uarr['replydate'] = SYS_TIME;
            $this->ddb->update(tableal("message"), $uarr, array('id'=>$row['id']));
            $arr['text'] = $uarr['reply'];

        }else{
            $arr['message'] = "参数错误！";
            echo json_encode($arr); exit();
        }
        $arr['success'] = 1;
        $arr['message'] = "回复成功！";
        echo json_encode($arr); exit();
    }

    /**
     * 记录
     */
    public function doWebCount($parent = null)
    {
        global $_GPC;
        $user = $this->user->getuser();
        $func = $this->user->functions();

        $start = $_GPC['time']['start']?strtotime($_GPC['time']['start']):strtotime(date("Y-m-d", SYS_TIME - 7*86400));
        $end = $_GPC['time']['end']?strtotime($_GPC['time']['end']):strtotime(date("Y-m-d", SYS_TIME));
        $day = floor(($end-$start)/86400);
        $time = array('start'=>date('Y-m-d', $start),'end'=>date('Y-m-d', $end));
        //获取数据
        if (in_array($_GPC['m_name'], array('follow', 'unfollow', 'text', 'click', 'view', 'imagetext', 'music', 'image', 'voice', 'video', 'video', 'other'))) {
            $wheresql = " WHERE `alid`=".$func['al']['id'];
            if ($_GPC['m_name'] == 'other') {
                $wheresql.= " AND `msgtype` IN ('location','link','scan','location_event','enter_agent','scancode_push','scancode_waitmsg')";
            }else{
                $wheresql.= " AND `msgtype`='".$_GPC['m_name']."'";
            }
            $wheresql.= " AND `indate`>".strtotime(date("Y-m-d 00:00:00", $start))." AND `indate`<".strtotime(date("Y-m-d 23:59:59", $end));
            $wheresql.= ' AND `tobe`='.intval($_GPC['m_tobe']);
            $messlist = db_getall("SELECT type,indate FROM ".tableal("message").$wheresql);
            $datalist = array();
            foreach($messlist AS $data) { $datalist[$data['type']][date("m-d",$data['indate'])]++; }
            $arr = array();
            for($i=$day; $i>=0; $i--) {
                $t0 = date("m-d", $end - $i*86400);
                $t1 = strtotime(date("Y-m-d 00:00:00", $end - $i*86400));
                $t2 = strtotime(date("Y-m-d 23:59:59", $end - $i*86400));
                $weixin = intval($datalist['weixin'][$t0]);
                $alipay = intval($datalist['alipay'][$t0]);
                $arr['ymd'][] = date("Ymd", $end - $i*86400);
                $arr['time'][] = array($t0, $t1, $t2);
                $arr['weixin'][] = $weixin;
                $arr['alipay'][] = $alipay;
                $arr['day'][] = $t0;
                $arr['countweixin']+= $weixin;
                $arr['countalipay']+= $alipay;
            }
            $arr['daynum'] = $day;
            $arr['rule'] = $arr['countweixin'] + $arr['countalipay'];
            echo json_encode($arr); exit();
        }

        $tthis = $this;

        $this->cs->show(get_defined_vars());
    }

    /**
     * 记录
     */
    public function doWebNotes($parent = null)
    {
        $user = $this->user->getuser();
        $func = $this->user->functions();
        //
        $page = value($parent, 1, 'int');
        $pageurl = urlencode($this->base->url[3]);
        //
        $row = db_getone(tableal("message"), array('id'=>intval($this->input->get('id')),'alid'=>$func['al']['id']));
        if (empty($row)) {
            $this->cs->showmsg(null, '信息不存在');
        }
        //
        $wheresql = " `openid`='".$row['openid']."'";
        //
        $tthis = $this;
        $this->cs->show(get_defined_vars());
    }

    /**
     * 标星
     * @param null $parent
     */
    public function doWebStar($parent = null)
    {
        $this->user->getuser();
        $id = value($parent, 1, 'int');
        $str = value($parent, 2, 'int');
        $str = ($str==1)?1:0;
        //
        $arr = array();
        $arr['success'] = 0;
        $arr['message'] = "标星失败";
        if (db_update(tableal("message"),array("star"=>$str), array("id"=>intval($id)))) {
            $arr['success'] = 1;
            $arr['message'] = "标星成功";
        }
        echo json_encode($arr); exit();
    }

    /**
     * 获取用户信息
     * @param $openid
     * @param string $type
     * @return array|int|string
     */
    public function getgroup($openid, $type = 'user_name')
    {
        static $getgroup_classes = array();
        if (isset($getgroup_classes[$openid.$type])) {
            if (!empty($getgroup_classes[$openid.$type])) {
                return $getgroup_classes[$openid.$type];
            }
        }
        $type = $type?$type:'*';
        $row = db_getone("SELECT ".$type." FROM ".table('fans'), array('openid'=>$openid));
        $_row = ($type == '*')?$row:value($row, 'user_name');
        $getgroup_classes[$openid.$type] = $_row;
        return $_row;
    }


    /**
     * 删除功能执行
     * @param int $alid
     * @return bool
     */
    public function useDeleted($alid = 0) {
        db_delete(table("message"), array('alid' => $alid));
        db_run('DROP TABLE IF EXISTS `'.table('_al'.$alid.'_message').'`');
        return true;
    }
}
