<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ES_Menu extends CI_Model {

	public function __construct()
	{
        global $_A;
		parent::__construct();
        if (!$this->uri->segment(3) && $_A['al']['wx_appid']) {
            gourl(weburl('menu/weixin'));
        }
	}

    /**
     * 服务窗自定义菜单
     */
    public function doWebIndex()
    {
        $user = $this->user->getuser();
        $func = $this->user->functions();

        $setting = string2array($func['uf']['setting']);
        $_menu = value($setting, 'menu', true);
        foreach($_menu as $key=>$val){
            if (!isset($val['child'])){
                $_menu[$key]['child'] = array();
            }
        }
        $menus = json_encode($_menu);
        $this->cs->show(get_defined_vars());
    }

	/**
	 * 公众号自定义菜单
	 */
	public function doWebWeixin()
	{
		global $_A,$_GPC;
		$user = $this->user->getuser();
		$func = $this->user->functions();

		$setting = string2array($func['uf']['setting']);
		if ($_A['al']['wx_level'] == 7) {
			$_menu = value($setting, 'wxmenu-'.intval($_GPC['agentid']), true);
		}else{
			$_menu = value($setting, 'wxmenu', true);
		}
		foreach($_menu as $key=>$val){
			if (!isset($val['child'])){
				$_menu[$key]['child'] = array();
			}
		}
		$menus = json_encode($_menu);
		//
		$this->cs->show(get_defined_vars());
	}

    /**
     * 保存菜单
     */
    public function doWebSave()
    {
        $this->user->getuser();
        $func = $this->user->functions();
        if ($this->input->post("dosubmit")){
            $fost = $this->input->post();
            $arr = array();
            $arr['success'] = 0;
            //
            $_arr = array();
            foreach($fost as $key=>$val){
                $_n = get_subto($key,'_','_');
                $_n2 = get_subto($key,$_n.'_','_');
                if (substr($key,0,5)=='menu_'){
                    $_arr[$_n][$_n2] = $val;
                }elseif (substr($key,0,10)=='menuchild_'){
                    $_n3 = get_subto($key, $_n2.'_');
                    $_arr[$_n]['child'][$_n2][$_n3] = $val;
                }
            }
            $inorder = array();
            foreach ($_arr as $key => $val) {
                $inorder[$key] = $val['inorder'];
				//
				$_inorder = array();
				foreach ($_arr[$key]['child'] as $_key => $_val) {
					$_arr[$key]['child'][$_key]['inorder'] = intval($_val['inorder']);
					$_inorder[$_key] = intval($_val['inorder']);
				}
				array_multisort($_inorder, SORT_ASC, $_arr[$key]['child']);
            }
            array_multisort($inorder, SORT_ASC, $_arr);
            $setting = string2array($func['uf']['setting']);
            $setting['menu'] = $_arr;
            if ($this->ddb->update(table('users_functions'), array('setting'=>array2string($setting)), array('id'=>$func['uf']['id']))){
                $arr['success'] = 1;
                $arr['message'] = '保存成功';
            }else{
                $arr['message'] = '保存失败';
            }
            echo json_encode($arr); exit();
        }
    }


	/**
	 * 保存到服务器
	 */
	public function doWebSavemenu()
	{
		$this->user->getuser();
		$func = $this->user->functions();
		$setting = string2array($func['uf']['setting']);
		//
		$arr = array();
		$arr['success'] = 0;
		$arr['message'] = '';
		//
		$this->load->library('fuwu');
		$this->fuwu->setting($func['al']['id']);
		//
		$_arr = array();
		foreach(value($setting, 'menu') as $k=>$v){
			if (!isset($v['status'])) continue;
			$_arr[$k]['name'] = $v['title'];
			//
			$_but = array();
			if (isset($v['child']) && !empty($v['child'])){
				foreach($v['child'] as $kc=>$vc) {
					if (!isset($vc['status'])) continue;
					$temp = array();
					$temp['actionParam'] = $vc['keytext'];
					$temp['actionType'] = str_replace(array('in2','alipay2','alipay3'), array('in','alipay','alipay'),$vc['keytype']);
					$temp['name'] = $vc['title'];
					if ($vc['keytype'] == 'link') {
						$temp['authType'] = '';
					}elseif ($vc['keytype'] == 'in') {
						$temp['msgShowType'] = '';
					}elseif ($vc['keytype'] == 'in2') {
						$temp['msgShowType'] = 'open_direct';
					}elseif ($vc['keytype'] == 'alipay') {
						$temp['minVersion'] = '7.6.0.1028';
						$temp['actionParam'] = 'alipays://platformapi/startapp?appId=20000050&title=%e9%99%84%e8%bf%91%e7%bd%91%e7%82%b9&keywords='.urlencode($temp['actionParam']).'&publicId=#publicId#&sourceId=publicPlatform';
					}elseif ($vc['keytype'] == 'alipay2') {
						$temp['minVersion'] = '7.6.0.1028';
					}elseif ($vc['keytype'] == 'alipay3') {
						$temp['minVersion'] = null;
						$temp['actionParam'] = 'alipays://platformapi/startapp?appId=09999988&actionType=toAccount&hideExpression=true&account='.$temp['actionParam'].'&publicId=#publicId#&sourceId=publicPlatform';
					}
					$_but[] = $temp;
				}
				$_arr[$k]['subButton'] = $_but;
			}else{
				$_arr[$k]['actionParam'] = $v['keytext'];
				$_arr[$k]['actionType'] = str_replace(array('in2','alipay2','alipay3'), array('in','alipay','alipay'), $v['keytype']);;
				if ($v['keytype'] == 'link') {
					$_arr[$k]['authType'] = '';
				}elseif ($v['keytype'] == 'in') {
					$_arr[$k]['msgShowType'] = '';
				}elseif ($v['keytype'] == 'in2') {
					$_arr[$k]['msgShowType'] = 'open_direct';
				}elseif ($v['keytype'] == 'alipay') {
					$_arr[$k]['minVersion'] = '7.6.0.1028';
					$_arr[$k]['actionParam'] = 'alipays://platformapi/startapp?appId=20000050&title=%e9%99%84%e8%bf%91%e7%bd%91%e7%82%b9&keywords='.urlencode($_arr[$k]['actionParam']).'&publicId=#publicId#&sourceId=publicPlatform';
				}elseif ($v['keytype'] == 'alipay2') {
					$_arr[$k]['minVersion'] = '7.6.0.1028';
				}elseif ($v['keytype'] == 'alipay3') {
					$_arr[$k]['minVersion'] = null;
					$_arr[$k]['actionParam'] = 'alipays://platformapi/startapp?appId=09999988&actionType=toAccount&hideExpression=true&account='.$_arr[$k]['actionParam'].'&publicId=#publicId#&sourceId=publicPlatform';
				}
			}
		}
		$_arr = array("button"=>$_arr);
		if (value($setting, 'menuisup') == '1') {
			$ret = $this->fuwu->upmenu($_arr);
		}else{
			$ret = $this->fuwu->addmenu($_arr);
			$setting['menuisup'] = 1;
			$this->ddb->update(table('users_functions'), array('setting'=>array2string($setting)), array('id'=>$func['uf']['id']));
		}
		$arr['message'] = value($ret, 'sub_msg').value($ret, 'msg');
		if (value($ret, 'code') == 200) {
			$arr['success'] = 1;
		}
		echo json_encode($arr); exit();
	}


	/**
	 * 同步菜单结构
	 */
	public function doWebSynchronous()
	{
		$this->user->getuser();
		$func = $this->user->functions();
		//
		$this->load->library('fuwu');
		$this->fuwu->setting($func['al']['id']);
		$menu = $this->fuwu->getmenu();
		//
		$arr = array();
		$arr['success'] = 0;
		$arr['message'] = value($menu, 'sub_msg').value($menu, 'msg');
		if (value($menu, 'code') == 200) {
			$menu_content = json_decode($menu['menu_content'], true);
			if ($menu_content['button']) {
				$sync = array();
				foreach($menu_content['button'] AS $key=>$item) {
					$sync[$key] = array(
						'inorder'=>$key,
						'title'=>$item['name'],
						'keytype'=>value($item, 'actionType'),
						'keytext'=>value($item, 'actionParam'),
						'status'=>'on'
					);
					if ($sync[$key]['keytype'] == 'in' && substr($sync[$key]['keytext'],-1) == '2') {
						$sync[$key]['keytype'] = 'in2';
					}
					if ($sync[$key]['keytype'] == 'alipay') {
						if (strpos($sync[$key]['keytext'], 'ShowRecords') !== false) {
							$sync[$key]['keytype'] = 'alipay2';
						}elseif (strpos($sync[$key]['keytext'], 'toAccount') !== false) {
							$sync[$key]['keytype'] = 'alipay3';
							$sync[$key]['keytext'] = get_subto($sync[$key]['keytext'],'&account=','&');
						}else{
							$sync[$key]['keytext'] = urldecode(get_subto($sync[$key]['keytext'],'&keywords=','&'));
						}
					}
					$subButton = value($item, 'subButton');
					if ($subButton) {
						foreach($subButton AS $k2=>$i2) {
							$sync[$key]['child'][$k2] = array(
								'inorder'=>$k2,
								'title'=>$i2['name'],
								'keytype'=>value($i2, 'actionType'),
								'keytext'=>value($i2, 'actionParam'),
								'status'=>'on'
							);
							if ($sync[$key]['child'][$k2]['keytype'] == 'in' && substr($sync[$key]['child'][$k2]['keytext'],-1) == '2') {
								$sync[$key]['child'][$k2]['keytype'] = 'in2';
							}
							if ($sync[$key]['child'][$k2]['keytype'] == 'alipay') {
								if (strpos($sync[$key]['child'][$k2]['keytext'], 'ShowRecords') !== false) {
									$sync[$key]['child'][$k2]['keytype'] = 'alipay2';
								}elseif (strpos($sync[$key]['child'][$k2]['keytext'], 'toAccount') !== false) {
									$sync[$key]['child'][$k2]['keytype'] = 'alipay3';
									$sync[$key]['child'][$k2]['keytext'] = get_subto($sync[$key]['child'][$k2]['keytext'],'&account=','&');
								}else{
									$sync[$key]['child'][$k2]['keytext'] = urldecode(get_subto($sync[$key]['child'][$k2]['keytext'],'&keywords=','&'));
								}
							}
						}
					}
				}
				//
				$setting = string2array($func['uf']['setting']);
				//$setting['menu_old'][] = $setting['menu'];
				$setting['menu'] = $sync;
				$setting['menuisup'] = 1;
				if ($this->ddb->update(table('users_functions'), array('setting'=>array2string($setting)), array('id'=>$func['uf']['id']))){
					$arr['success'] = 1;
					$arr['message'] = '同步成功';
				}else{
					$arr['message'] = '同步失败';
				}
			}else{
				$arr['message'] = '服务器上的数据为空';
			}
		}
		echo json_encode($arr); exit();
	}


	/**
	 * 保存菜单 （微信）
	 */
	public function doWebWxsave()
	{
		global $_A,$_GPC;
		$this->user->getuser();
		$func = $this->user->functions();
		if ($this->input->post("dosubmit")){
			$fost = $this->input->post();
			$arr = array();
			$arr['success'] = 0;
			//
			$_arr = array();
			foreach($fost as $key=>$val){
				$_n = get_subto($key,'_','_');
				$_n2 = get_subto($key,$_n.'_','_');
				if (substr($key,0,5)=='menu_'){
					$_arr[$_n][$_n2] = $val;
				}elseif (substr($key,0,10)=='menuchild_'){
					$_n3 = get_subto($key, $_n2.'_');
					$_arr[$_n]['child'][$_n2][$_n3] = $val;
				}
			}
			$inorder = array();
			foreach ($_arr as $key => $val) {
				$inorder[$key] = $val['inorder'];
				//
				$_inorder = array();
				foreach ($_arr[$key]['child'] as $_key => $_val) {
					$_arr[$key]['child'][$_key]['inorder'] = intval($_val['inorder']);
					$_inorder[$_key] = intval($_val['inorder']);
				}
				array_multisort($_inorder, SORT_ASC, $_arr[$key]['child']);
			}
			array_multisort($inorder, SORT_ASC, $_arr);
			$setting = string2array($func['uf']['setting']);
			if ($_A['al']['wx_level'] == 7) {
				$setting['wxmenu-'.intval($_GPC['agentid'])] = $_arr;
			}else{
				$setting['wxmenu'] = $_arr;
			}
			if ($this->ddb->update(table('users_functions'), array('setting'=>array2string($setting)), array('id'=>$func['uf']['id']))){
				$arr['success'] = 1;
				$arr['message'] = '保存成功';
			}else{
				$arr['message'] = '保存失败';
			}
			echo json_encode($arr); exit();
		}
	}

	/**
	 * 保存到服务器 (微信)
	 */
	public function doWebWxsavemenu()
	{
		global $_A,$_GPC;
		$this->user->getuser();
		$func = $this->user->functions();
		$setting = string2array($func['uf']['setting']);
		//
		$arr = array();
		$arr['success'] = 0;
		$arr['message'] = '';
		//
		$this->load->library('wx');
		$this->wx->setting($func['al']['id']);
		//
		if ($_A['al']['wx_level'] == 7) {
			$arr = $this->wx->upmenu(value($setting, 'wxmenu-'.intval($_GPC['agentid']), true), intval($_GPC['agentid']));
		}else{
			$arr = $this->wx->upmenu(value($setting, 'wxmenu', true));
		}
		if ($arr['errcode']){
			$arr['success'] = 0;
		}else{
			$arr['success'] = 1;
		}
		echo json_encode($arr); exit();
	}

	/**
	 * 同步菜单结构 (微信)
	 */
	public function doWebWxsynchronous()
	{
		global $_A,$_GPC;
		$this->user->getuser();
		$func = $this->user->functions();
		//
		$this->load->library('wx');
		$this->wx->setting($func['al']['id']);
		$menu = $this->wx->getmenu(intval($_GPC['agentid']));
		//
		$arr = array();
		$arr['success'] = 0;
		$arr['message'] = "获取失败".$menu['errmsg'];
		if (value($menu, 'menu|button')) {
			$menu_content = $menu['menu']['button'];
			$sync = array();
			if ($menu_content) {
				foreach($menu_content AS $key=>$item) {
					$sync[$key] = array(
						'inorder'=>$key,
						'title'=>$item['name'],
						'keytype'=>value($item, 'type'),
						'keytext'=>value($item, 'key').value($item, 'url'),
						'status'=>'on'
					);
					$subButton = value($item, 'sub_button');
					if ($subButton && is_array($subButton)) {
						foreach($subButton AS $k2=>$i2) {
							$sync[$key]['child'][$k2] = array(
								'inorder'=>$k2,
								'title'=>$i2['name'],
								'keytype'=>value($i2, 'type'),
								'keytext'=>value($i2, 'key').value($i2, 'url'),
								'status'=>'on'
							);
						}
					}
				}
				$setting = string2array($func['uf']['setting']);
				//$setting['wxmenu_old'][] = $setting['wxmenu'];
				if ($_A['al']['wx_level'] == 7) {
					$setting['wxmenu-'.intval($_GPC['agentid'])] = $sync;
				}else{
					$setting['wxmenu'] = $sync;
				}
				if ($this->ddb->update(table('users_functions'), array('setting'=>array2string($setting)), array('id'=>$func['uf']['id']))){
					$arr['success'] = 1;
					$arr['message'] = '同步成功';
				}else{
					$arr['message'] = '同步失败';
				}
			}else{
				$arr['message'] = '服务器上的数据为空';
			}
		}
		echo json_encode($arr); exit();
	}

	/**
	 * 删除菜单 （微信）
	 */
	public function doWebWxdel()
	{
		global $_GPC;
		$this->user->getuser();
		$func = $this->user->functions();
		//
		//
		$this->load->library('wx');
		$this->wx->setting($func['al']['id']);
		$arr = $this->wx->delmenu(intval($_GPC['agentid']));
		if ($arr['errcode']){
			$arr['success'] = 0;
		}else{
			$arr['success'] = 1;
		}
		echo json_encode($arr); exit();
	}


	/**
	 * 编辑器选择
	 */
	public function doWebReply($a = null)
	{
		$user = $this->user->getuser();
		$func = $this->user->functions();
		//
		$page = $this->input->get('page');
		$keyval = $this->input->get('keyval');
		$wheresql = " `module`!='reply' AND `status`='启用' AND `alid`=".$func['al']['id'];
		//搜索
		if ($keyval){
			$wheresql.= " AND `key` LIKE '%".$keyval."%' ";
		}
		$_this = $this;
		$this->cs->show($a[1]?'reply_'.$a[1]:'reply', get_defined_vars());
	}

	/**
	 * 获取模块详情
	 * @param $title_en
	 * @return mixed
	 */
	public function moduleinfo($title_en) {
		static $modules = array();
		if (isset($modules[$title_en])) {
			$item = $modules[$title_en];
		}else{
			$item = db_getone(table('functions'), array('title_en'=>$title_en));
			if ($item) {
				$item['setting'] = string2array($item['setting']);
			}else{
				$item = array();
			}
			$modules[$title_en] = $item;
		}
		return $item;
	}

}
