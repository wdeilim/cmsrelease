<?php
class Reply extends CI_Model {

	public function __construct()
    {
        parent::__construct();
	}

	/**
	 * 文本回复
	 * @param string $content 文本内容
	 * @return array
	 */
	public function text($content)
	{
		global $_A;
		if ($_A['M']['openid_type'] == 'alipay') {
			$this->load->library('fuwu');
			$Request = object_array($this->fuwu->respText($content, true));
			$RequestCode = value($Request, 'alipay_mobile_public_message_custom_send_response|code');
			$RequestMsg = value($Request, 'alipay_mobile_public_message_custom_send_response|msg');
			if ($RequestCode != '200') {
				return error(-1, $RequestMsg.$RequestCode);
			}
			return error(0, '成功');
		}elseif ($_A['M']['openid_type'] == 'weixin') {
			$this->load->library('wx');
			$Request = $this->wx->respText($content, true);
			if (is_error($Request)) {
				return error(-1, $Request['message']);
			}else{
				return error(0, '成功');
			}
		}
		return error(-1, '参数错误');
	}

	/**
	 * 图片回复
	 * @param string $content 图片地址
	 * @return array
	 */
	public function img($content, $waittis = '正在查询中，请等待......')
	{
		global $_A;
		if ($_A['M']['openid_type'] == 'alipay') {
			$this->load->library('fuwu');
			$Request = object_array($this->fuwu->respMedia($content, 'image', $waittis, true));
			$RequestCode = value($Request, 'alipay_mobile_public_message_custom_send_response|code');
			$RequestMsg = value($Request, 'alipay_mobile_public_message_custom_send_response|msg');
			if ($RequestCode != '200') {
				return error(-1, $RequestMsg.$RequestCode);
			}
			return error(0, '成功');
		}elseif ($_A['M']['openid_type'] == 'weixin') {
			$this->load->library('wx');
			$Request = $this->wx->respMedia($content, 'image', $waittis, true);
			if (is_error($Request)) {
				return error(-1, $Request['message']);
			}else{
				return error(0, '成功');
			}
		}
		return error(-1, '参数错误');
	}

	/**
	 * 语音回复
	 * @param string $content 语音地址
	 * @return array
	 */
	public function voice($content, $waittis = '正在查询中，请等待......')
	{
		global $_A;
		if ($_A['M']['openid_type'] == 'alipay') {
			$this->load->library('fuwu');
			$Request = object_array($this->fuwu->respMedia($content, 'voice', $waittis, true));
			$RequestCode = value($Request, 'alipay_mobile_public_message_custom_send_response|code');
			$RequestMsg = value($Request, 'alipay_mobile_public_message_custom_send_response|msg');
			if ($RequestCode != '200') {
				return error(-1, $RequestMsg.$RequestCode);
			}
			return error(0, '成功');
		}elseif ($_A['M']['openid_type'] == 'weixin') {
			$this->load->library('wx');
			$Request = $this->wx->respMedia($content, 'voice', $waittis, true);
			if (is_error($Request)) {
				return error(-1, $Request['message']);
			}else{
				return error(0, '成功');
			}
		}
		return error(-1, '参数错误');
	}

	/**
	 * 视频回复
	 * @param string $content 视频地址
	 * @return array
	 */
	public function video($content, $waittis = '正在查询中，请等待......')
	{
		global $_A;
		if ($_A['M']['openid_type'] == 'alipay') {
			$this->load->library('fuwu');
			$Request = object_array($this->fuwu->respMedia($content, 'video', $waittis, true));
			$RequestCode = value($Request, 'alipay_mobile_public_message_custom_send_response|code');
			$RequestMsg = value($Request, 'alipay_mobile_public_message_custom_send_response|msg');
			if ($RequestCode != '200') {
				return error(-1, $RequestMsg.$RequestCode);
			}
			return error(0, '成功');
		}elseif ($_A['M']['openid_type'] == 'weixin') {
			$this->load->library('wx');
			$Request = $this->wx->respMedia($content, 'video', $waittis, true);
			if (is_error($Request)) {
				return error(-1, $Request['message']);
			}else{
				return error(0, '成功');
			}
		}
		return error(-1, '参数错误');
	}

	/**
	 * 图文回复
	 * @param array $data 图文信息组 array('title'=>,'desc'=>,'img'=>,'url'=>,) 支持多数组
	 * @return string
	 */
	public function news($data = array())
    {
        global $_A;
		if ($_A['M']['openid_type'] == 'alipay') {
			$this->load->library('fuwu');
			$Request = object_array($this->fuwu->respNews($data, true));
			$RequestCode = value($Request, 'alipay_mobile_public_message_custom_send_response|code');
			$RequestMsg = value($Request, 'alipay_mobile_public_message_custom_send_response|msg');
			if ($RequestCode != '200') {
				return error(-1, $RequestMsg.$RequestCode);
			}
			return error(0, '成功');
		}elseif ($_A['M']['openid_type'] == 'weixin') {
			$this->load->library('wx');
			$Request = $this->wx->respNews($data, true);
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