<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Web 管理端
 */
class Web extends CI_Controller {
    private $a = '';
    private $b = '';
    private $dosetting = array();

	public function __construct()
	{
		parent::__construct();
		$this->load->model('user');
	}

	public function _remap($a = null, $arr = array())
	{
        global $_A,$_GPC;
        if (OFF_SITE_IS) {
            if ($arr[0] == 'login' || $_A['u']['admin'] || (empty($arr) && empty($_A['u']))) {
                //登录页面和管理员访问正常
            }else{
                message(null, OFF_SITE_WHY?OFF_SITE_WHY:'请稍后...');
            }
        }
        $this->a = $a?$a:'index';
        $this->b = isset($arr[0])?$arr[0]:'index';
        $_A['f']['setting'] = string2array(value($_A['f'], 'setting'));
        if ($this->b == 'index') {
            $bindings = value($_A['f']['setting'], 'bindings');
            $this->cs->assign('bindings', $bindings);
            //
            $bindscount = 0;
            $bindscount+= isset($bindings['cover'])&&is_array($bindings['cover'])?count($bindings['cover']):0;
            $bindscount+= isset($bindings['menu'])&&is_array($bindings['menu'])?count($bindings['menu']):0;
            $bindscount+= isset($bindings['setting'])&&is_array($bindings['setting'])?count($bindings['setting']):0;
            $this->cs->assign('bindscount', $bindscount);
            if ($bindscount > 0 && !in_array(value($_GPC, 'entry'), array('reply', 'cover', 'menu', 'setting'))) {
                //项目首页
                $this->_index($arr);
            }elseif (value($_GPC, 'entry') == 'cover' && $this->_binds($_GPC['do'], $bindings['cover'])) {
                //入口页面
                $this->_cover($arr);
            }elseif (value($_GPC, 'entry') == 'menu' && $this->_binds($_GPC['do'], $bindings['menu'])) {
                //菜单页面
                $this->_menu($arr);
            }elseif (value($_GPC, 'entry') == 'setting' && $this->_binds($_GPC['do'], $bindings['setting'])) {
                //参数设置
                $this->_setting($arr);
            }elseif (isset($_A['f']['reply']) && $_A['f']['reply']) {
                //规则列表
                $this->_reply($arr);
            }elseif (isset($_GPC['do']) && $_GPC['do'] == 'keychart') {
                //关键词走势
                $this->_keychart($arr);
            }else{
                //接入节点
                $this->_site($arr);
            }
        }else{
            //接入节点
            $this->_site($arr);
        }
        exit();
	}

    public function _index($arr = array())
    {
        global $_A,$_GPC;
        header("Content-type: text/html; charset=".BASE_CHARSET);
        $this->user->islogin();
        $_GPC['param'] = $arr;
        $this->cs->assign('_A', $_A);
        $this->cs->assign('_GPC', $_GPC);
        $this->cs->assign('this', $this);
        $this->cs->assign('NOW_PATH', BASE_URI."addons/system/template/reply/");
        //
        $this->cs->show(BASE_PATH."addons/system/template/reply/index.tpl");
    }

    public function _cover($arr = array())
    {
        global $_A,$_GPC;
        $this->_thing_head($arr);
        //
        $this->load->helper('tpl');
        $reply = db_getone(table('reply'), array('module'=>$this->a, 'alid'=>$_A['al']['id'], 'do'=>$_GPC['do']));
        if (empty($reply)) {
            $reply = array();
        }else{
            $reply['content'] = string2array($reply['content']);
            $reply['setting'] = string2array($reply['setting']);
        }
        if ($_GPC['dosubmit']) {
            $_GPC['reply']['key'] = str_replace('，', ',', $_GPC['reply']['key']);
            if (!empty($reply)) {
                $rok = db_update(table('reply'),
                    array(
                        'type'=>$_GPC['reply']['content']['type'],
                        'status'=>'启用',
                        'match'=>intval($_GPC['reply']['match']),
                        'key'=>','.$_GPC['reply']['key'].',',
                        'content'=>array2string($_GPC['reply']['content']),
                        'update'=>SYS_TIME,
                    ),
                    array('id'=>$reply['id']));
                if (!$rok) message(null, '提交失败！');
            }else{
                $reply['id'] = db_insert(table('reply'),
                    array(
                        'module'=>$this->a,
                        'alid'=>$_A['al']['id'],
                        'type'=>$_GPC['reply']['content']['type'],
                        'status'=>'启用',
                        'match'=>intval($_GPC['reply']['match']),
                        'key'=>','.$_GPC['reply']['key'].',',
                        'title'=>$this->dosetting['title'],
                        'do'=>$_GPC['do'],
                        'content'=>array2string($_GPC['reply']['content']),
                        'indate'=>SYS_TIME,
                    ), true);
                if (!$reply['id']) message(null, '提交失败！');
            }
            $larr = array(
                0 => array(
                    'title'=>'重新修改',
                    'href'=>get_link("dosubmit")
                ),
                1 => array(
                    'title'=>'返回 '.$_A['f']['title'],
                    'href'=>get_link("do")
                )
            );
            message(null, '提交成功！', $larr, $larr[1]['href']);
        }
        $this->cs->assign('reply', $reply);
        $this->cs->show(BASE_PATH."addons/system/template/reply/cover.tpl");
    }

    public function _menu($arr = array())
    {
        global $_A,$_GPC;
        $this->_thing_head($arr);
        //
        gourl(weburl(0, $_A['f']['title_en'], $_GPC['do']));
    }

    public function _setting($arr = array())
    {
        global $_A,$_GPC;
        $this->_thing_head($arr);
        //
        $setrow = db_getone(table('bind_setting'), array('alid'=>$_A['al']['id'], 'module'=>$this->a, 'do'=>$_GPC['do']));
        if (empty($setrow)) {
            db_insert(table('bind_setting'), array('alid'=>$_A['al']['id'], 'module'=>$this->a, 'do'=>$_GPC['do']));
            $setting = array();
        }else{
            $setting = string2array($setrow['setting']);
        }
        //
        if ($this->base->inc(FCPATH.'addons/'.$this->a.'/module.php')){
            $classname = "ESM_".ucfirst($this->a);
            if (class_exists($classname)) {
                $es_site = new $classname();
                $es_method = 'doSettingDisplay';
                if ($_GPC['dosubmit']) {
                    $post = $this->input->post();
                    if (is_array($post['setting']) && $post['setting']) {
                        $post = $post['setting'];
                    }
                    $setting = _array_merge($setting, $post);
                    $es_method = 'doSettingSubmit';
                }
                if (method_exists($es_site, $es_method)) {
                    $es_site->$es_method($_GPC['do'], $setting?$setting:null);
                }
            }
        }
        if ($_GPC['dosubmit']) {
            $post = $this->input->post();
            if (is_array($post['setting']) && $post['setting']) {
                $post = $post['setting'];
            }
            unset($post['submit']);
            unset($post['dosubmit']);
            db_update(table('bind_setting'), array('setting'=>array2string($post)), array('id'=>$setrow['id']));
            $larr = array(
                0 => array(
                    'title'=>'重新修改',
                    'href'=>get_link("dosubmit")
                ),
                1 => array(
                    'title'=>'返回 '.$_A['f']['title'],
                    'href'=>get_link("do|entry")
                )
            );
            message(null, '提交成功！', $larr, $larr[1]['href']);
        }
        //
        $this->cs->assign('setting', $setting);
        $this->cs->show(BASE_PATH."addons/system/template/reply/setting.tpl");
    }


    public function _reply($arr = array())
    {
        global $_A,$_GPC;
        header("Content-type: text/html; charset=".BASE_CHARSET);
        $this->user->islogin();
        $_GPC['param'] = $arr;
        $this->cs->assign('_A', $_A);
        $this->cs->assign('_GPC', $_GPC);
        $this->cs->assign('this', $this);
        $this->cs->assign('NOW_PATH', BASE_URI."addons/system/template/reply/");
        //
        if ($_GPC['do'] == 'add') {
            //添加修改
            $this->load->helper('tpl');
            $id = intval($_GPC['id']);
            $reply = array();
            $submit = '添加';
            if ($id > 0) {
                $reply = db_getone(table('reply'), array('module'=>$this->a, 'alid'=>$_A['al']['id'], 'id'=>$id));
                if (empty($reply)) {
                    $id = 0;
                }else{
                    $submit = '修改';
                    $reply['content'] = string2array($reply['content']);
                    $reply['setting'] = string2array($reply['setting']);
					$this->cs->assign('replymenu', value(string2array($_A['f']['setting']), 'bindings|reply'));
                }
            }
            if ($_GPC['dosubmit']) {
                if (empty($_GPC['reply']['title'])) {
                    message(null, '规则名称不能留空！');
                }
                $_GPC['reply']['key'] = str_replace('，', ',', $_GPC['reply']['key']);
                if ($id > 0) {
                    $rok = db_update(table('reply'),
                        array(
                            'type'=>$_GPC['reply']['content']['type'],
                            'status'=>'启用',
                            'match'=>intval($_GPC['reply']['match']),
                            'key'=>','.$_GPC['reply']['key'].',',
                            'title'=>$_GPC['reply']['title'],
                            'vip_link'=>intval($_GPC['reply']['vip_link']),
                            'vip_title'=>$_GPC['reply']['vip_title'],
                            'content'=>array2string($_GPC['reply']['content']),
                            'update'=>SYS_TIME,
                        ),
                        array('id'=>$id));
					if (!$rok) message(null, $submit.'失败！');
                }else{
                    $id = db_insert(table('reply'),
                        array(
                            'module'=>$this->a,
                            'alid'=>$_A['al']['id'],
                            'type'=>$_GPC['reply']['content']['type'],
                            'status'=>'启用',
                            'match'=>intval($_GPC['reply']['match']),
                            'key'=>','.$_GPC['reply']['key'].',',
                            'title'=>$_GPC['reply']['title'],
                            'vip_link'=>intval($_GPC['reply']['vip_link']),
                            'vip_title'=>$_GPC['reply']['vip_title'],
                            'content'=>array2string($_GPC['reply']['content']),
                            'indate'=>SYS_TIME,
                        ), true);
					if (!$id) message(null, $submit.'失败！');
					$reply['id'] = $id;
                }
                $reply = db_getone(table('reply'), array('id'=>$id));
                $reply['content'] = string2array($reply['content']);
                $reply['setting'] = string2array($reply['setting']);
				$this->_reply_help('FormSubmit', $id, $reply);
				$larr = array(
					0 => array(
						'title'=>'重新修改规则',
						'href'=>get_link("dosubmit|id").'&id='.$id
					),
					1 => array(
						'title'=>'返回 '.$_A['f']['title'].' 规则列表',
						'href'=>get_link("do")
					)
				);
				message(null, $submit.'成功！', $larr, $larr[1]['href']);
            }
            $this->cs->assign('id', $id);
            $this->cs->assign('reply', $reply);
            $this->cs->assign('submit', $submit);
            $this->cs->show(BASE_PATH."addons/system/template/reply/add.tpl");
        }elseif ($_GPC['do'] == 'del') {
            //删除
			$id = intval($_GPC['id']);
			$reply = db_getone(table('reply'), array('module'=>$this->a, 'alid'=>$_A['al']['id'], 'id'=>$id));
			$this->_reply_help('Delete', $id, $reply);
			if ($reply) {
				db_delete(table('reply'), array('id'=>$id));
				message(null, '删除成功！');
			}else{
				message(null, '删除失败，规则不存在！');
			}
        }elseif ($_GPC['do'] == 'keychart') {
            //统计关键词
            $this->_keychart();
        }else{
            //列表
            $lists = db_getall(table('reply'),
                array('module'=>$this->a, 'alid'=>$_A['al']['id'], '(`do` IS NULL OR `do`=\'\')'=>null),
                '`id` DESC');
            $this->cs->assign('lists', $lists);
            $this->cs->assign('replymenu', value($_A['f']['setting'], 'bindings|reply'));
            $this->cs->show(BASE_PATH."addons/system/template/reply/lists.tpl");
        }
    }

    public function _keychart($arr = array())
    {
        global $_A,$_GPC;
        header("Content-type: text/html; charset=".BASE_CHARSET);
        $this->user->islogin();
        $_GPC['param'] = $arr;
        $this->cs->assign('_A', $_A);
        $this->cs->assign('_GPC', $_GPC);
        $this->cs->assign('this', $this);
        $this->cs->assign('NOW_PATH', BASE_URI."addons/system/template/reply/");
        //
        $this->load->helper('tpl');
        $_A['f']['title'] = '关键词走势';
        $key = $_GPC['key'];
        $arr = explode(",", $key);
        $start = $_GPC['time']['start']?strtotime($_GPC['time']['start']):strtotime(date("Y-m-d", SYS_TIME - 7*86400));
        $end = $_GPC['time']['end']?strtotime($_GPC['time']['end']):strtotime(date("Y-m-d", SYS_TIME));
        $day = floor(($end-$start)/86400);
        $lists = array();
        foreach($arr AS $item) {
            $k = 'kchart_'.md5($item);
            $lists[$k]['k'] = $k;
            $lists[$k]['key'] = $item;
            $lists[$k]['day'] = array();
            $wheresql = " WHERE `alid`=".$_A['al']['id']." AND `tobe`=0 AND `text`='".$item."' ";
            $wheresql.= " AND `indate`>".strtotime(date("Y-m-d 00:00:00", $start))." AND `indate`<".strtotime(date("Y-m-d 23:59:59", $end));
            $messlist = db_getall("SELECT type,indate FROM ".tableal("message").$wheresql);
            $datalist = array();
            foreach($messlist AS $data) { $datalist[$data['type']][date("m-d",$data['indate'])]++; }
            $lists[$k]['countweixin'] = 0;
            $lists[$k]['countalipay'] = 0;
            for($i=$day; $i>=0; $i--) {
                $t0 = date("m-d", $end - $i*86400);
                $t1 = strtotime(date("Y-m-d 00:00:00", $end - $i*86400));
                $t2 = strtotime(date("Y-m-d 23:59:59", $end - $i*86400));
                $weixin = intval($datalist['weixin'][$t0]);
                $alipay = intval($datalist['alipay'][$t0]);
                $lists[$k]['ymd'][] = date("Ymd", $end - $i*86400);
                $lists[$k]['time'][] = array($t0, $t1, $t2);
                $lists[$k]['weixin'][] = $weixin;
                $lists[$k]['alipay'][] = $alipay;
                $lists[$k]['day'][] = $t0."[".($weixin+$alipay)."]";
                $lists[$k]['countweixin']+= $weixin;
                $lists[$k]['countalipay']+= $alipay;
            }
            $lists[$k]['count'] = count($messlist);
        }
        $referer = urldecode($_GPC['referer']);
        $this->cs->assign('key', $key);
        $this->cs->assign('lists', $lists);
        $this->cs->assign('referer', $referer);
        $this->cs->assign('furl', weburl(0, $_A['f']['title_en'])."&do=keychart&id=".$_GPC['id']."&key=".urlencode($_GPC['key'])."&referer=".urlencode($_GPC['referer']));
        $this->cs->assign('time', array('start'=>date('Y-m-d', $start),'end'=>date('Y-m-d', $end)));
        $this->cs->show(BASE_PATH."addons/system/template/reply/chart.tpl");
    }

    public function _site($arr = array())
    {
        global $_A,$_GPC;
        $a = $this->a;
        $b = $this->b;
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
            $es_method = 'doWeb'.ucfirst($b);
            $this->base->inc(FCPATH.'addons/'.$a.'/inc/'.strtolower($b).'.php');
            if (method_exists($es_site, 'doWeb')) {
                $es_site->doWeb();
            }
            //功能菜单 头部
            $bindmenu = $_A['f']['setting']['bindings'][empty($_GPC['id'])?'menu':'reply'];
            $this->dosetting = $this->_binds($b, $bindmenu);
            if ($this->dosetting['embed']) {
                $this->cs->assign('bindmenu', is_array($bindmenu)?$bindmenu:array());
                $this->cs->assign('dosetting', $this->dosetting);
                $this->cs->view(BASE_PATH."addons/system/template/reply/_top.tpl");
            }
            //接入功能节点
            if (method_exists($es_site, $es_method)) {
                $es_site->$es_method($arr?$arr:null);
            }
            //功能菜单 底部
            if ($this->dosetting['embed']) {
                $this->cs->view(BASE_PATH."addons/system/template/reply/_bottom.tpl");
            }
        }
    }

    public function _binds($do, $arr = array())
    {
        $isbind = false;
        if ($do && is_array($arr) && $arr) {
            foreach ($arr AS $item) {
                if ($item['do'] == $do) {
                    $isbind = $item;
                    break;
                }
            }
        }
        return $isbind;
    }

    public function _thing_head($arr)
    {
        global $_A,$_GPC;
        $bindings = $_A['f']['setting']['bindings'];
        $this->dosetting = $this->_binds($_GPC['do'], $bindings[value($_GPC, 'entry')]);
        if (empty($this->dosetting)) {
            gourl(weburl(0, $_A['f']['title_en']));
        }
        header("Content-type: text/html; charset=".BASE_CHARSET);
        $this->user->islogin();
        $_GPC['param'] = $arr;
        $this->cs->assign('_A', $_A);
        $this->cs->assign('_GPC', $_GPC);
        $this->cs->assign('this', $this);
        $this->cs->assign('dosetting', $this->dosetting);
        $this->cs->assign('NOW_PATH', BASE_URI."addons/system/template/reply/");
    }

	public function _reply_help($b, $id, $reply = array())
	{
		if ($this->base->inc(FCPATH.'addons/'.$this->a.'/module.php')){
			$classname = "ESM_".ucfirst($this->a);
			if (class_exists($classname)) {
				header("Content-type: text/html; charset=".BASE_CHARSET);
				$es_site = new $classname();
				$es_method = 'do'.ucfirst($b);
				if (method_exists($es_site, $es_method)) {
                    $this->cs->assign('NOW_PATH', BASE_URI."addons/".$this->a."/template/");
					$es_site->$es_method($id, $reply);
				}
			}
		}
	}

    public function _setting_help($b)
    {
        $this->cs->assign('NOW_PATH', BASE_URI."addons/".$this->a."/template/");
        $this->cs->show(BASE_PATH."addons/".$this->a."/template/".$b.".tpl");
    }
}

