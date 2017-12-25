<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 服务窗 开发者网关
 * Class Alipay
 */
class Alipay extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
	}

	public function _remap($alid = null, $arr = array())
	{
		$this->load->library('fuwu');
		$this->fuwu->setting(intval($alid), $arr, $this->ddb);
		$this->fuwu->receive();
		exit();
	}
}
