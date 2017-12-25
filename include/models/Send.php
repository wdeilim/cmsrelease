<?php
class Send extends CI_Model {
	public $_data =	array();

	public function __construct()
    {
        parent::__construct();
	}

	private function initfans($opneid)
	{
		global $_A;
		$warr = array('openid'=>$opneid);
		if ($_A['al']['id']) {
			$warr['alid'] = $_A['al']['id'];
		}
		$fans = db_getone(table('fans'), $warr);
		if (empty($fans)) {
			return error(-1, '粉丝不存在');
		}
		if ($fans['follow'] != 1) {
			return error(-1, '粉丝未关注:'.$fans['follow']);
		}
		$this->_data['fans'] = $fans;
		if ($fans['type'] == 'alipay') {
			$this->load->library('fuwu');
			$this->fuwu->setting($fans['alid']);
		}elseif ($fans['type'] == 'weixin') {
			$this->load->library('wx');
			$this->wx->setting($fans['alid']);
		}
		return error(0, $fans);
	}

	private function media($content, $type, $tis)
	{
		$fans = $this->_data['fans'];
		if ($fans['type'] == 'alipay') {
			$imagetext = new_addslashes(array(
				'title'=>'点击查看'.str_replace(array('image','voice','video'), array('图片','语音','视频'), $type),
				'desc'=>'',
				'url'=>appurl("system/showmedia/")."&type=".$type."&value=".urlencode($content)
			));
			require_once (dirname(__FILE__).'/../libraries/fuwu/PushMsg.php');
			$push = new PushMsg ();
			$image_text_msg = array();
			$image_text_msg[] = $push->mkImageTextMsg(
				$imagetext['title'],
				$imagetext['desc'],
				$imagetext['url'],
				"", "");
			$biz_content = $push->mkImageTextBizContent($fans['openid'], $image_text_msg);
			return object_array($push->sendRequest($biz_content));
		}elseif ($fans['type'] == 'weixin') {
			if ($tis) {
				$media_id = $this->wx->media_upload($content, $type, $fans['openid'], $tis);
			}else{
				$media_id = $this->wx->media_upload($content, $type);
			}
			if (empty($media_id)) {
				$send = array();
				$send['touser'] = $fans['openid'];
				$send['msgtype'] = 'news';
				$send['news']['articles'][] = $this->wx->encode(array(
					'title'=>'点击查看'.str_replace(array('image','voice','video'), array('图片','语音','视频'), $type),
					'description'=>'',
					'url'=>appurl("system/showmedia/")."&type=".$type."&value=".urlencode($content)
				));
			}else{
				$send = array();
				$send['touser'] = $fans['openid'];
				$send['msgtype'] = $type;
				$send[$type] = array('media_id' => $media_id);
			}
			return $this->wx->sendCustomNotice($send);
		}
		return error(-1, '粉丝参数错误');
	}

	private function newshandle($data)
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
		$fans = $this->_data['fans'];
		if ($fans['type'] == 'alipay') {
			require_once (dirname(__FILE__).'/../libraries/fuwu/PushMsg.php');
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
		}elseif ($fans['type'] == 'weixin') {
			$image_text_msg = array();
			foreach($array AS $item) {
				$image_text_msg[] = $this->wx->encode(array(
					'title'=>$item['title'],
					'description'=>$item['desc'],
					'url'=>$item['url'],
					'picurl'=>$item['img']
				));
			}
			return $image_text_msg;
		}
		return error(-1, '粉丝参数错误');
	}

	/**
	 * 发送文本
	 * @param string $opneid 	粉丝openid
	 * @param string $content 	发送的文本内容
	 * @return array
	 */
	public function text($opneid, $content)
	{
		$ifans = $this->initfans($opneid);
		if (is_error($ifans)) { return $ifans; }
		//
		$fans = $this->_data['fans'];
		if ($fans['type'] == 'alipay') {
			require_once (dirname(__FILE__).'/../libraries/fuwu/PushMsg.php');
			$push = new PushMsg ();
			$text_msg = $push->mkTextMsg(new_addslashes(str_replace("\r\n", "\n", $content)));
			$biz_content = $push->mkTextBizContent($fans['openid'], $text_msg);
			$biz_content = iconv("UTF-8", "GBK//IGNORE", $biz_content);
			$Request = object_array($push->sendRequest($biz_content));
			$RequestCode = value($Request, 'alipay_mobile_public_message_custom_send_response|code');
			$RequestMsg = value($Request, 'alipay_mobile_public_message_custom_send_response|msg');
			if ($RequestCode != '200') {
				return error(-1, $RequestMsg.$RequestCode);
			}
			return error(0, '成功');
		}elseif ($fans['type'] == 'weixin') {
			$send = array();
			$send['touser'] = $fans['openid'];
			$send['msgtype'] = 'text';
			$send['text'] = array('content' => $this->wx->encode($this->wx->send2text(str_replace("\r\n", "\n", $content))));
			$Request = $this->wx->sendCustomNotice($send);
			if (is_error($Request)) {
				return error(-1, $Request['message']);
			}else{
				return error(0, '成功');
			}
		}
		return error(-1, '参数错误');
	}

	/**
	 * 发送图片文件
	 * @param string $opneid 	粉丝openid
	 * @param string $content 	发送的图片地址
	 * @param string $waittis
	 * @return array
	 */
	public function img($opneid, $content, $waittis = '')
	{
		$ifans = $this->initfans($opneid);
		if (is_error($ifans)) { return $ifans; }
		//
		$fans = $this->_data['fans'];
		if ($fans['type'] == 'alipay') {
			$Request = $this->media($content, 'image', $waittis);
			$RequestCode = value($Request, 'alipay_mobile_public_message_custom_send_response|code');
			$RequestMsg = value($Request, 'alipay_mobile_public_message_custom_send_response|msg');
			if (is_error($Request)) {
				return error(-1, $Request['message']);
			}elseif ($RequestCode != '200') {
				return error(-1, $RequestMsg.$RequestCode);
			}
			return error(0, '成功');
		}elseif ($fans['type'] == 'weixin') {
			$Request = $this->media($content, 'image', $waittis);
			if (is_error($Request)) {
				return error(-1, $Request['message']);
			}else{
				return error(0, '成功');
			}
		}
		return error(-1, '参数错误');
	}

	/**
	 * 发送语音文件
	 * @param string $opneid 	粉丝openid
	 * @param string $content 	语音文件地址
	 * @param string $waittis
	 * @return array
	 */
	public function voice($opneid, $content, $waittis = '')
	{
		$ifans = $this->initfans($opneid);
		if (is_error($ifans)) { return $ifans; }
		//
		$fans = $this->_data['fans'];
		if ($fans['type'] == 'alipay') {
			$Request = $this->media($content, 'voice', $waittis);
			$RequestCode = value($Request, 'alipay_mobile_public_message_custom_send_response|code');
			$RequestMsg = value($Request, 'alipay_mobile_public_message_custom_send_response|msg');
			if (is_error($Request)) {
				return error(-1, $Request['message']);
			}elseif ($RequestCode != '200') {
				return error(-1, $RequestMsg.$RequestCode);
			}
			return error(0, '成功');
		}elseif ($fans['type'] == 'weixin') {
			$Request = $this->media($content, 'voice', $waittis);
			if (is_error($Request)) {
				return error(-1, $Request['message']);
			}else{
				return error(0, '成功');
			}
		}
		return error(-1, '参数错误');
	}

	/**
	 * 发送视频文件
	 * @param string $opneid 	粉丝openid
	 * @param string $content 	视频文件地址
	 * @param string $waittis
	 * @return array
	 */
	public function video($opneid, $content, $waittis = '')
	{
		$ifans = $this->initfans($opneid);
		if (is_error($ifans)) { return $ifans; }
		//
		$fans = $this->_data['fans'];
		if ($fans['type'] == 'alipay') {
			$Request = $this->media($content, 'video', $waittis);
			$RequestCode = value($Request, 'alipay_mobile_public_message_custom_send_response|code');
			$RequestMsg = value($Request, 'alipay_mobile_public_message_custom_send_response|msg');
			if (is_error($Request)) {
				return error(-1, $Request['message']);
			}elseif ($RequestCode != '200') {
				return error(-1, $RequestMsg.$RequestCode);
			}
			return error(0, '成功');
		}elseif ($fans['type'] == 'weixin') {
			$Request = $this->media($content, 'video', $waittis);
			if (is_error($Request)) {
				return error(-1, $Request['message']);
			}else{
				return error(0, '成功');
			}
		}
		return error(-1, '参数错误');
	}

	/**
	 * 发送图文信息
	 * @param string $opneid 	粉丝openid
	 * @param string $data 		图文信息组  array('title'=>,'desc'=>,'img'=>,'url'=>,) 支持多数组
	 * @return array
	 */
	public function news($opneid, $data)
	{
		$ifans = $this->initfans($opneid);
		if (is_error($ifans)) { return $ifans; }
		//
		$fans = $this->_data['fans'];
		$image_text_msg = $this->newshandle($data);
		if ($fans['type'] == 'alipay') {
			require_once (dirname(__FILE__).'/../libraries/fuwu/PushMsg.php');
			$push = new PushMsg ();
			$biz_content = $push->mkImageTextBizContent($fans['openid'], $image_text_msg);
			$Request = object_array($push->sendRequest($biz_content));
			$RequestCode = value($Request, 'alipay_mobile_public_message_custom_send_response|code');
			$RequestMsg = value($Request, 'alipay_mobile_public_message_custom_send_response|msg');
			if ($RequestCode != '200') {
				return error(-1, $RequestMsg.$RequestCode);
			}
			return error(0, '成功');
		}elseif ($fans['type'] == 'weixin') {
			$send = array();
			$send['touser'] = $fans['openid'];
			$send['msgtype'] = 'news';
			$send['news']['articles'] = $image_text_msg;
			$Request = $this->wx->sendCustomNotice($send);
			if (is_error($Request)) {
				return error(-1, $Request['message']);
			}else{
				return error(0, '成功');
			}
		}
		return error(-1, '参数错误');
	}
}
?>