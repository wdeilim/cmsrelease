<?php
$_A = $_GPC = array();
class Base extends CI_Model {
    public $url = array();

	public function __construct()
    {
		global $_A,$_GPC;
        parent::__construct();
        date_default_timezone_set("PRC");

		$this->load->library('session');
        $this->load->library('cs');

        $this->load->model('ddb');
        $this->load->helper('global');
        $this->load->helper('cookie');

        //判断加载函数
        $BAST_HELPER = FCPATH.APPPATH.'helpers/'.$this->uri->segment(1).'_helper.php';
        if (file_exists($BAST_HELPER)) {
            include_once($BAST_HELPER);
        }
        $BAST_HELPER = FCPATH.APPPATH.'helpers/'.$this->uri->segment(1).'_'.$this->uri->segment(2).'_helper.php';
        if (file_exists($BAST_HELPER)) {
            include_once($BAST_HELPER);
        }

        //基本参数赋值
        $this->url[0] = $this->__url();
        $this->url[1] = $this->__url(1);
        $this->url[2] = $this->__url(2);
        $this->url[3] = $this->__url(3);
		$this->url[4] = $this->__url(4);
        $this->url['now'] = $this->__url('now');
        $this->url['index'] = $this->__url('index');
		$_A['url'] = $this->url;
        //
        $this->url['get'] = $this->input->get();
        $this->url['post'] = $this->input->post();
        $this->url['cookie'] = $this->input->cookie();
		$_GPC = array_merge($this->url['get'], $this->url['post'], $this->url['cookie']);

        define('SYS_TIME', time()); //时间戳
        define('ONLINE_IP', get_ip()); //客户IP

		$_A['SYS_TIME'] = SYS_TIME;
		$_A['ONLINE_IP'] = ONLINE_IP;

        $this->cs->data('segments', $this->uri->segments);
        $this->cs->assign('urlarr',$this->url);
        $this->cs->assign('TIME', SYS_TIME);
        $this->cs->assign('BASE_NAME', defined('JS_PATH')?BASE_NAME:'网站名称');
        $this->cs->assign('JS_PATH', defined('JS_PATH')?JS_PATH:'/');
        $this->cs->assign('CSS_PATH', defined('CSS_PATH')?CSS_PATH:'/');
        $this->cs->assign('IMG_PATH', defined('IMG_PATH')?IMG_PATH:'/');

		$_A['segments'] = $this->uri->segments;
		$_A['module'] = $this->uri->segment(2);
		$_A['userid'] = $this->session->userdata('userid');
		$_A['username'] = $this->session->userdata('username');
		@setcookie("alipay_userid", $_A['userid'], 30*24*3600+SYS_TIME, BASE_DIR);

		$_A['BASE_NAME'] = BASE_NAME;
		$_A['BASE_URI'] = BASE_URI;
		$_A['BASE_PATH'] = BASE_PATH;
		$_A['JS_PATH'] = JS_PATH;
		$_A['CSS_PATH'] = CSS_PATH;
		$_A['IMG_PATH'] = IMG_PATH;

	}


    private function __url($param = null)
    {
        switch ($param){
            case "1":
                $text = $this->uri->segment(1);
                break;
            case "2":
                $text = $this->uri->slash_segment(1).$this->uri->segment(2);
                break;
			case "3":
				$text = $this->uri->slash_segment(1).$this->uri->slash_segment(2).$this->uri->segment(3);
				break;
			case "4":
				$text = $this->uri->slash_segment(1).$this->uri->slash_segment(2).$this->uri->slash_segment(3).$this->uri->segment(4);
				break;
            case "now":
                $text = $this->uri->uri_string();
                break;
            case "index":
                return rtrim($this->config->site_url(), '/').'/';
                break;
            default:
                $text = '';
                break;
        }
        $text = $this->config->site_url($text);
        $text = rtrim($text, '/').'/';
        return $text;
    }

    /**
     * 加载类文件函数
     * @param $path
     * @return bool
     */
    public static function inc($path)
    {
        static $incclasses = array();
        $key = md5($path);
        if (isset($incclasses[$key])) {
            return true;
        }
        if (file_exists($path)) {
            include $path;
            $incclasses[$key] = true;
            return true;
        }
        return false;
    }

    /**
     * 加载类文件函数
     * @param $path
     * @return bool
     */
    public static function apprun($path)
    {
        static $incclasses = array();
        $_path = FCPATH.'addons/'.$path.'/apprun.php';
        $key = md5($_path);
        if (isset($incclasses[$key])) {
            return true;
        }
        if (file_exists($_path)) {
            include $_path;
            $incclasses[$key] = true;
            return true;
        }
        return false;
    }

	/**
	 * 图文回复
	 * @param array $data array('title'=>,'desc'=>,'img'=>,'url'=>,)
	 * @param string $type
	 */
	public function respNews($data = array(), $type = '')
    {
        global $_A;
		if ($type == "alipay" || $_A['M']['openid_type'] == 'alipay') {
			$this->load->library('fuwu');
			$this->fuwu->respNews($data);
		}elseif ($type == "weixin" || $_A['M']['openid_type'] == 'weixin') {
			$this->load->library('wx');
			$this->wx->respNews($data);
        }
	}

	/**
	 * 文本回复
	 * @param $content
	 * @param string $type
	 */
	public function respText($content, $type = '')
    {
        global $_A;
		if ($type == "alipay" || $_A['M']['openid_type'] == 'alipay') {
			$this->load->library('fuwu');
			$this->fuwu->respText($content);
		}elseif ($type == "weixin" || $_A['M']['openid_type'] == 'weixin') {
			$this->load->library('wx');
			$this->wx->respText($content);
		}
	}
}
?>