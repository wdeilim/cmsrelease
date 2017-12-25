<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Index extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		gourl($this->base->url['index'].'web/system');
	}

	public function test()
	{
		exit('test');
	}

}
