<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class App 客户端
 */
class App extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('user');
	}

	public function _remap($a = null, $arr = array())
	{
		global $_GPC;
		if (OFF_SITE_IS) {
			message(null, OFF_SITE_WHY?OFF_SITE_WHY:'请稍后...');
		}
		$a = $a?$a:'index';
		$b = isset($arr[0])?$arr[0]:'index';
        if (!$this->base->inc(FCPATH.'addons/'.$a.'/site.php')){
            show_error('The module does not exist!', 404);
        }
		$classname = "ES_".ucfirst($a);
		if (!class_exists($classname)) {
			show_error('Does not exist Class method"'.$classname.'"!', 404);
		}else{
			header("Content-type: text/html; charset=".BASE_CHARSET);
			$_GPC['param'] = $arr;
			$es_site = new $classname();
			$es_method = 'doMobile'.ucfirst($b);
			$this->base->inc(FCPATH.'addons/'.$a.'/inc/mobile/'.strtolower($b).'.php');
            if (method_exists($es_site, 'doMobile')) {
                $es_site->doMobile();
            }
			if (method_exists($es_site, $es_method)) {
				$es_site->$es_method($arr?$arr:null);
			}
		}
		exit();
	}
}
