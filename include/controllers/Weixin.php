<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 公众号 开发者网关
 * Class Alipay
 */
class Weixin extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
	}

	public function _remap($alid = null, $arr = array())
	{
		$this->load->library('wx');
		if (strexists($alid, "-")) {
			global $_A;
			list($alid, $_A['corp_agentid']) = explode("-", $alid);
		}
		$this->wx->setting(intval($alid), $arr, $this->ddb);
		$this->wx->receive();
		exit();
	}
}
