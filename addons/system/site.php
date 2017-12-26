<?php
defined('BASEPATH') OR exit('No direct script access allowed');
include_once 'function.php';
define('WEIXIN_ROOT', 'https://mp.weixin.qq.com');

class ES_System extends CI_Model {

	public function __construct()
	{
		parent::__construct();
        $this->load->helper("communication");
	}

	/**
	 * 恢复数据库
	 * @param null $parent
	 */
	public function doWebImport($parent = null)
	{
		global $_GPC;
		$fileid = intval($_GPC['fileid'])?intval($_GPC['fileid']):1;
		$import_name = $this->session->userdata('import_name');
		$import_random = $this->session->userdata('import_random');
		//
		$arr = array();
		$arr['success'] = 0;
		$arr['message'] = "";
		$arr['nexturl'] = "";
		if ($parent[1] != $import_random) {
			$arr['message'] = "参数错误，请重试！";
			echo json_encode($arr); exit();
		}
		if (empty($import_name) || $_GPC['name'] != $import_name) {
			$arr['message'] = "备份不存在！";
			echo json_encode($arr); exit();
		}
		$dir = BASE_PATH.'caches/bakup/'.$_GPC['name'];
		if (!is_dir($dir)) {
			$arr['message'] = '备份目录不存在，请刷新后再试！';
			echo json_encode($arr); exit();
		}
        $preg = "/^vwins_".$_GPC['name']."_([0-9]{1,9}+)\.sql/i";
		$sqlfiles = glob($dir.'/*.sql');
        $inorder = array();
        foreach ($sqlfiles as $key => $val) {
            if (preg_match($preg, basename($val))) {
                $inorder[$key] = preg_replace($preg, "$1", basename($val));
            }else{
                unset($sqlfiles[$key]);
            }
        }
        array_multisort($inorder, SORT_ASC, $sqlfiles);
        $j = 0;
		foreach($sqlfiles as $id=>$sqlfile) {
            $j++;
            if ($j >= $fileid) {
                if (file_exists($sqlfile) && $sqltxt = file_get_contents($sqlfile)) {
                    db_run_simple($sqltxt);
                }
                $arr['success'] = 1;
                $arr['message'] = "正在还原【".$import_name."】(第".$j."/".count($sqlfiles)."卷)！<br/>正在继续还原，请不要关闭此网页！";
                $arr['nexturl'] = get_link('fileid').'&fileid='.($j+1);
                echo json_encode($arr); exit();
            }
		}
        //
        sleep(1);
        $arr['success'] = 1;
		$arr['message'] = "还原成功！";
		$arr['nexturl'] = "";
        $this->session->set_userdata('import_name', "");
        $this->session->set_userdata('import_random', generate_password(6, 2));
        //保存版本信息
        if (file_exists($dir.'/info.php')) {
            $infos = @include $dir.'/info.php';
            if (isset($infos['ES_VERSION']) && isset($infos['ES_RELEASE'])) {
                file_put_contents(BASE_PATH.'caches/version.php',
                    "<?php define('ES_VERSION', '".$infos['ES_VERSION']."'); define('ES_RELEASE', '".$infos['ES_RELEASE']."'); ?>");
            }
        }
		echo json_encode($arr); exit();
	}

    /**
     * 联动地址
     * @param null $parent
     */
    public function doWebLinkage($parent = null)
    {
        $parentid = value($parent, 1, 'int');
        $wheresql = " WHERE `parentid`=0 AND `style`=0";
        if ($parentid > 0 ){
            $wheresql = " WHERE `parentid`=".$parentid." AND `style`=0";
        }
        $row = db_getall("SELECT * FROM ".table('linkage')." {$wheresql} ORDER BY `listorder`");
        echo json_encode($row);
        exit();
    }

    /**
     * 登录
     */
    public function doWebLogin()
	{
        $settinglists = $this->ddb->getone("SELECT * FROM ".table('setting')." WHERE `title`='setting'");
        $settinglists = string2array($settinglists['content']);
        $topmenulists = $settinglists['topmenu'];
        $regitem = $settinglists['regitem'];
        $settingother = $settinglists['other'];
        //
		if ($this->input->post("dosubmit")){
			$fost = $this->input->post();
			$arr = array();
			$arr['success'] = 0;
			if (empty($fost['username'])){
				$arr['message'] = '请输入用户名/邮箱/手机号码';
				echo json_encode($arr); exit();
			}
			if (empty($fost['userpass'])){
				$arr['message'] = '请输入密码';
				echo json_encode($arr); exit();
			}
			$wheresql = "`username`='".$fost['username']."'";
			$row = $this->ddb->getone("SELECT * FROM ".table('users')." WHERE ".$wheresql);
			if (empty($row)){
				$arr['message'] = '用户名或密码错误';
				echo json_encode($arr); exit();
			}
			if (md52($fost['userpass'], $row['encrypt']) != $row['userpass']){
				$arr['message'] = '用户名或密码错误';
				echo json_encode($arr); exit();
			}
			$arr['message'] = '登录成功！<br/>上次登录IP：';
			$arr['message'].= ($row['loginip'])?$row['loginip'].'<br>上次登录时间：'.date('Y-m-d H:i:s', $row['logindate']).'':'无';
			//
			$this->user->handle_function($row['userid']);
			//
			$_arr = array();
			$_arr['loginip'] = ONLINE_IP;
			$_arr['logindate'] = SYS_TIME;
			$this->ddb->update(table('users'), $_arr, array('userid'=>$row['userid']));
			//
			$this->session->set_userdata('userid', $row['userid']);
			$this->session->set_userdata('username', $row['username']);
			@setcookie("alipay_userid_admin", $row['admin'], 30*24*3600+SYS_TIME, BASE_DIR);
			$arr['success'] = 1;
			echo json_encode($arr); exit();
		}
		//
		$tem = array();
		if (file_exists(FCPATH."caches".DIRECTORY_SEPARATOR."cache.templet.txt")) {
			$tem = string2array(file_get_contents(FCPATH."caches".DIRECTORY_SEPARATOR."cache.templet.txt"));
		}
		if (empty($tem['t1']['loginbg'])) {
			$tem['t1']['loginbg'] = array('addons/system/template/css/login_1/1.jpg');
		}
		if (empty($tem['t2']['loginbg'])) {
			$tem['t2']['loginbg'] = array(
				'addons/system/template/css/login_2/1.jpg',
				'addons/system/template/css/login_2/2.jpg',
				'addons/system/template/css/login_2/3.jpg'
			);
		}
		if (empty($tem['t2']['logo'])) {
			$tem['t2']['logo'] = 'addons/system/template/css/login_2/logo.png';
		}
		if (empty($tem['t2']['map']['title']) || empty($tem['t2']['map']['lat']) || empty($tem['t2']['map']['lng'])) {
			$tem['t2']['map']['title'] = '广西壮族自治区南宁市青秀区长湖路24号';
			$tem['t2']['map']['lat'] = '108.370866';
			$tem['t2']['map']['lng'] = '22.832879';
		}
		if (empty($tem['t2']['comp'])) {
			$tem['t2']['comp'] = '广西三顾网络科技有限公司';
		}
		if (empty($tem['t2']['tel'])) {
			$tem['t2']['tel'] = '0771-5671712';
		}
		if (empty($tem['t3']['loginbg'])) {
			$tem['t3']['loginbg'] = array('caches/statics/images/sign-in-bg.jpg');
		}
		$templet = in_array($tem['templet'], array(1,2,3))?$tem['templet']:1;
		//
		$_SESSION['_RELEASE'.ES_RELEASE] = "";
		tpl('login_'.$templet,get_defined_vars());
	}

    /**
     * 注册
     */
    public function doWebReg()
    {
        $settinglists = $this->ddb->getone("SELECT * FROM ".table('setting')." WHERE `title`='setting'");
        $settinglists = string2array($settinglists['content']);
        $topmenulists = $settinglists['topmenu'];
        $regitem = $settinglists['regitem'];
        $settingother = $settinglists['other'];
        //
        if ($this->input->post("dosubmit")){
            $fost = $this->input->post();
            $arr = $_arr = array();
            $arr['success'] = 0;
            //
			if (OFF_REG_IS) {
				$arr['message'] = '尚未开放注册功能！';
				echo json_encode($arr); exit();
			}
			//
            $_arr['username'] = $fost['username'];
            $_arr['encrypt'] = generate_password(6);
            $_arr['userpass'] = md52($fost['userpass'], $_arr['encrypt']);
            //
            if ($regitem['fullname']=='fullname') $_arr['fullname'] = $fost['fullname'];
            if ($regitem['phone']=='phone') $_arr['phone'] = $fost['phone'];
            if ($regitem['email']=='email') $_arr['email'] = $fost['email'];
            if ($regitem['qqnum']=='qqnum') $_arr['qqnum'] = $fost['qqnum'];
            if ($regitem['companyname']=='companyname') $_arr['companyname'] = $fost['companyname'];
            if ($regitem['tel']=='tel') $_arr['tel'] = $fost['tel'];
            if ($regitem['linkaddr']=='linkaddr') $_arr['linkaddr'] = $fost['linkaddr'];
            if ($regitem['address']=='address') $_arr['address'] = $fost['address'];
            //
            $_arr['indate'] = SYS_TIME;
            $_arr['inip'] = ONLINE_IP;
            $_arr['regsetting'] = array2string($fost);
            //判断用户名
            $_count = $this->ddb->get_total("SELECT COUNT(*) AS num FROM ".table('users'), array('username'=>$_arr['username']));
            if ($_count){
                $arr['message'] = '用户名已存在';
                echo json_encode($arr); exit();
            }
            //判断邮箱
            if ($regitem['email']=='email'){
                $_count = $this->ddb->get_total("SELECT COUNT(*) AS num FROM ".table('users'), array('email'=>$_arr['email']));
                if ($_count){
                    $arr['message'] = '邮箱已存在';
                    echo json_encode($arr); exit();
                }
            }
            //判断手机号码
            if ($regitem['phone']=='phone'){
                $_count = $this->ddb->get_total("SELECT COUNT(*) AS num FROM ".table('users'), array('phone'=>$_arr['phone']));
                if ($_count){
                    $arr['message'] = '手机号码已存在';
                    echo json_encode($arr); exit();
                }
            }
            //开始注册
			$_arr['loginip'] = ONLINE_IP;
			$_arr['logindate'] = SYS_TIME;
            $userid = $this->ddb->insert(table('users'), $_arr, true);
            if ($userid){
				$this->user->handle_function($userid);
				$this->session->set_userdata('userid', $userid);
				$this->session->set_userdata('username', $_arr['username']);
				@setcookie("alipay_userid_admin", 0, 30*24*3600+SYS_TIME, BASE_DIR);
				//
                $arr['success'] = 1;
                $arr['message'] = '注册成功';
            }else{
                $arr['message'] = '注册失败';
            }
            echo json_encode($arr); exit();
        }
        tpl(get_defined_vars());
    }

    /**
     * 登出
     */
    public function doWebOut()
    {
        $this->session->unset_userdata('userid');
        $this->session->unset_userdata('username');
		setcookie('_RELEASEVER:'.ES_RELEASE, '', SYS_TIME - 1, BASE_DIR);
        gourl(weburl("system/login"));
    }

    /**
     * 会员中心
     */
    public function doWebIndex()
    {
		global $_GPC;
        $user = $this->user->getuser();

		$page = value($_GPC,'page','int');
		$pageurl = urlencode($this->base->url[2]);
		//
		$wheresql = ($user['admin'])?"1":"`userid`=".$user['userid'];
		$key = db_escape_str(value($_GPC, 'key'));
		if (!isset($_GPC['key'])) {
			$key = db_escape_str(value($_GPC, '_index_key'));
		}
		if ($key){
			$wheresql.= " AND (`al_name` LIKE '%".$key."%' OR `wx_name` LIKE '%".$key."%')";
		}
        if ($user['admin']) {
			$userlist = db_getall("select * from ".table('users_al')." GROUP BY username");
			$filter = $_GPC['filter'];
            if ($filter != "") {
				setcookie('user_filter', $filter, SYS_TIME + 94608000, BASE_DIR);
            }else{
                $filter = $_COOKIE['user_filter'];
            }
            if ($filter > 0) {
                $wheresql.= " AND (`userid`=".intval($filter).")";
            }
        }
		//
		$ordersql = '`indate` DESC';
		$clastalid = intval($_COOKIE['user_lastalid']);
		$slastalid = intval($this->session->userdata('user:alid'));
		if (empty($slastalid)) { $slastalid = $clastalid; }
		if ($slastalid > 0) {
			$ordersql = 'CASE WHEN `id`='.$slastalid.' THEN 0 ELSE 1 END>`indate` DESC';
			if (empty($clastalid)) {
				setcookie('user_lastalid', $slastalid, SYS_TIME + 94608000, BASE_DIR);
			}
		}
        $_release = value($_COOKIE, '_RELEASEVER:'.ES_RELEASE);
        $_apprele = value($_COOKIE, '_RELEASEAPP:'.ES_RELEASE);
        tpl(get_defined_vars());
    }

    /**
     * 功能列表
     */
    public function doWebFunctions()
    {
        $user = $this->user->getuser();
        tpl(get_defined_vars());
    }

    /**
     * 常见问题
     */
    public function doWebQuestion()
    {
        $user = $this->user->getuser();
        tpl(get_defined_vars());
    }

	/**
	 * 公告查看
	 */
	public function doWebInformation()
	{
		global $_GPC;
		$user = $this->user->getuser();
		$pages = value($_GPC['param'], 1);
		if ($pages == 'show') {
			$info = db_getone(table('information'), array('id'=>value($_GPC['param'], 2, 'int')));
		}else{
			$pages = 'list';
			$page = value($_GPC['param'], 1, 'int');
			$pageurl = urlencode($this->base->url[3]);
		}
		tpl('information_'.$pages, get_defined_vars());
	}

	/**
	 * 云安装部分
	 */
	public function doWebCloud()
	{
		global $_GPC;
		$this->load->helper("cloud");
		if ($_GPC['step'] == "step1" || $_GPC['step'] == "step2" || $_GPC['step'] == "step3") {
			//
			$arr = array();
			$arr['success'] = 0;
            $arr['message'] = '';
			$cloudrow = $this->ddb->getone("SELECT * FROM ".table("cloud"), array('md5'=>md5($_GPC['random'])));
			if (empty($cloudrow)) {
				$arr['message'] = "非法操作！";
				echo json_encode($arr); exit();
			}
			$modulename = $_GPC['module'];
			if($this->ddb->getone(table('functions'), array("title_en"=>$modulename))) {
				$arr['message'] = "功能模块已经安装或是唯一标识已存在！";
				echo json_encode($arr); exit();
			}
			$modulepath = BASE_PATH . 'addons/' . $modulename . '/';
			if (file_exists($modulepath.'manifest.xml')) {
				@unlink($modulepath.'manifest.xml');
			}
			$r = cloud_prepare();
			if(is_error($r)) {
				$arr['message'] = $r['message'];
				echo json_encode($arr); exit();
			}
			//分解步骤
			if ($_GPC['step'] == "step1") {
				$manifest = cloud_ext_module_manifest($modulename);
				if(is_error($manifest)) {
					$arr['message'] = $manifest['message'];
					echo json_encode($arr); exit();
				}
				$_install = cloud_module_install_file($modulename, $manifest['install']);
				if(is_error($_install)) {
					$arr['message'] = $_install['message'];
					echo json_encode($arr); exit();
				}
				if(empty($manifest)) {
					$arr['message'] = "模块安装配置文件不存在或是格式不正确！";
					echo json_encode($arr); exit();
				}
				$manifest_check = $this->manifest_check($modulename, $manifest, true);
				if ($manifest_check) {
					$arr['message'] = $manifest_check;
					echo json_encode($arr); exit();
				}
				$cloudfiles = cloud_ext_module_files($modulename);
				if(is_error($cloudfiles)) {
					$arr['message'] = $cloudfiles['message'];
					echo json_encode($arr); exit();
				}
				make_dir(BASE_PATH.'addons/'.$modulename);
				$arr['isinurl'] = 1;
                $arr['success'] = 1;
                $arr['manifest'] = 1;
                $arr['cloudfiles'] = $cloudfiles;
				echo json_encode($arr); exit();
			}elseif ($_GPC['step'] == "step2") {
                if ($_GPC['local']) $_GPC['paths'] = array(array('type'=>$_GPC['path_type'],'path'=>$_GPC['path']));
				$cloudfiles = json_encode($_GPC['paths']);
				$clouddowns = cloud_ext_module_downs($cloudfiles, $modulename);
				if(is_error($clouddowns)) {
					$arr['message'] = $clouddowns['message'];
				}else{
					$arr['success'] = 1;
				}
				echo json_encode($arr); exit();
			}elseif ($_GPC['step'] == "step3") {
				$manifest = cloud_ext_module_manifest($modulename);
				if(is_error($manifest)) {
					$arr['message'] = $manifest['message'];
					echo json_encode($arr); exit();
				}
				$manifest_check = $this->manifest_check($modulename, $manifest, true);
				if ($manifest_check) {
					$arr['message'] = $manifest_check;
					echo json_encode($arr); exit();
				}
				$modulepath = BASE_PATH . 'addons/' . $modulename . '/';
				$module = ext_module_convert($manifest);
				$module['cloud'] = 1;
				if(!file_exists($modulepath . 'site.php')) {
					$arr['message'] = '模块缺少处理文件！';
					echo json_encode($arr); exit();
				}
				if(db_insert(table('functions'), $module)) {
					if(strexists($manifest['install'], '.php')) {
						if(file_exists($modulepath . $manifest['install'])) {
							include_once $modulepath . $manifest['install'];
							@unlink($modulepath . $manifest['install']);
						}
					}elseif (strexists($manifest['install'], '.sql') && $data = ext_file_get_contents($modulepath . $manifest['install'])) {
						db_run($data);
						@unlink($modulepath . $manifest['install']);
					} else {
						db_run($manifest['install']);
					}
					$arr['success'] = 1;
					$arr['message'] = '功能模块安装成功！';
				} else {
					$arr['message'] = '功能模块安装失败, 请联系模块开发者！';
				}
				echo json_encode($arr); exit();
			}else{
				$arr['message'] = '参数错误！';
				echo json_encode($arr); exit();
			}
        } elseif ($_GPC['step'] == "install") {
            tpl('functions_cloud_install', get_defined_vars());
        } elseif ($_GPC['step'] == "unbind") {
            $row = db_getone("SELECT * FROM ".table('setting'), array('title'=>'cloud'));
            if (!empty($row)) {
                $contentset = string2array($row['content']);
                $contentset['cloudok'] = 0;
                $this->ddb->update(table('setting'), array('content'=>array2string($contentset)), array("title"=>$row['title']));
            }
		} else {
			$pars = array();
			$pars['method'] = 'install_store';
			$pars['module'] = $_GPC['module'];
			$pars['random'] = $_GPC['random'];
			$pars['title'] = $_GPC['title'];
			$dat = ihttp_post(CLOUD_GATEWAY, $pars);
			if (is_error($dat)) {
				echo $dat['message'];
			}else{
				echo $dat['content'];
				if ($dat['content'] == "readypass") {
					$this->ddb->query("DELETE FROM ".table("cloud"));
					db_insert(table("cloud"), array('md5'=>md5(md5($pars['random'])), 'indate'=>SYS_TIME, 'inip'=>ONLINE_IP));
				}
			}
		}
		exit();
	}


    /**
     * 检测新版本
     */
    public function doWebVersion()
    {
		$arr = array();
		$arr['success'] = 0;
		$arr['message'] = '';
		//
        $_release = value($_COOKIE, '_RELEASEVER:'.ES_RELEASE);
        if (!$_release) {
            $this->load->helper("cloud");
            $pro = cloud_pro_upgrade(ES_RELEASE, ES_VERSION, true);
            if (is_error($pro) && $pro['message'] != "当前版本已经是最新版本") { exit(); }
            $pro = json_decode($pro, true);
			$_release = $pro['to_release']?$pro['to_release']:'99';
			setcookie('_RELEASEVER:'.ES_RELEASE, $_release, SYS_TIME + 21600, BASE_DIR);
        }
		if ($_release != '99') {
			$arr['success'] = 1;
			$arr['message'] = $_release;
		}
		echo json_encode($arr); exit();
    }

    /**
     * 检测应用新版本
     */
    public function doWebVersionapp()
    {
		$arr = array();
		$arr['success'] = 0;
		//
		$_release = value($_COOKIE, '_RELEASEAPP:'.ES_RELEASE);
		if (!$_release) {
			$row = db_getall("SELECT title_en,version FROM ".table('functions'), array('cloud'=>1));
			$applist = array(); $appversion = array();
			foreach($row AS $item) {
				$applist[] = $item['title_en'];
				$appversion[$item['title_en']] = $item['version'];
			}
			$this->load->helper("cloud");
			$release = ihttp_post(CLOUD_GATEWAY, array(
				'method'=>'upgrade_lists',
				'funs'=>implode(',', $applist)
			) , 10);
			$content = json_decode($release['content'], true);
			if(!is_error($release)) {
				foreach($content AS $key=>$item) {
					if ($item['version'] > $appversion[$key]) {
						setcookie('_RELEASEAPP:'.ES_RELEASE, 1, SYS_TIME + 21600, BASE_DIR);
						$_release = 1; break;
					}
				}
			}
            $_release = $_release?$_release:'99';
		}
		if ($_release != '99') {
			$arr['success'] = 1;
		}else{
			setcookie('_RELEASEAPP:'.ES_RELEASE, 99, SYS_TIME + 21600, BASE_DIR);
		}
		echo json_encode($arr); exit();
    }

    /**
	 * 系统部分
	 */
	public function doWebSettings()
	{
		global $_GPC;
		$user = $this->user->getuser();
		if ($user['admin'] != 1) {
			message("没有权限");
		}
		$_A['syspage'] = isset($_GPC['param'][1])?$_GPC['param'][1]:'settings';
		$_A['syspage'] = "S_".$_A['syspage'];
		if (method_exists($this, $_A['syspage'])) {
			$this->$_A['syspage']();
		}else{
			message("参数错误！");
		}

	}

	/**
	 * 系统设置
	 */
	public function S_settings()
	{
		global $_A;
        $this->load->helper('tpl');
        $setting = db_getone("SELECT * FROM ".table('setting'), array('title'=>'setting'));
        if (empty($setting)) {
            db_insert(table('setting'), array('title'=>'setting'), true);
            $setting['title'] = 'setting'; $setting['content'] = '';
        }
        $setting['content'] = string2array($setting['content']);
		//
		if (file_exists(FCPATH."caches".DIRECTORY_SEPARATOR."cache.offsite.php")) {
			require_once FCPATH."caches".DIRECTORY_SEPARATOR."cache.offsite.php";
		}
		if (file_exists(FCPATH."caches".DIRECTORY_SEPARATOR."cache.sqlset.php")) {
			require_once FCPATH."caches".DIRECTORY_SEPARATOR."cache.sqlset.php";
		}
		//
		$tem = array();
		if (file_exists(FCPATH."caches".DIRECTORY_SEPARATOR."cache.templet.txt")) {
			$tem = string2array(file_get_contents(FCPATH."caches".DIRECTORY_SEPARATOR."cache.templet.txt"));
		}
		if (empty($tem['t1']['loginbg'])) {
			$tem['t1']['loginbg'] = array('addons/system/template/css/login_1/1.jpg');
		}
		if (empty($tem['t2']['loginbg'])) {
			$tem['t2']['loginbg'] = array(
				'addons/system/template/css/login_2/1.jpg',
				'addons/system/template/css/login_2/2.jpg',
				'addons/system/template/css/login_2/3.jpg'
			);
		}
		if (empty($tem['t2']['logo'])) {
			$tem['t2']['logo'] = 'addons/system/template/css/login_2/logo.png';
		}
		if (empty($tem['t3']['loginbg'])) {
			$tem['t3']['loginbg'] = array('caches/statics/images/sign-in-bg.jpg');
		}
        //
		if ($this->input->post("dosubmit")){
			$fost = $this->input->post();
			$arr = array();
			$arr['success'] = 0;
            if ($fost['_type'] == 'topmenu') {
                $marr = array();
                foreach($fost['title'] AS $k=>$v){
                    if ($v) {
                        $marr[] = array(
                            'title'=>$v,
                            'link'=>$fost['link'][$k],
                            'target'=>$fost['target'][$k]
                        );
                    }
                }
                $setting['content']['topmenu'] = $marr;
                db_update(table('setting'), array('content'=>array2string($setting['content'])), array('title'=>'setting'));
                $arr['success'] = 1;
                echo json_encode($arr); exit();
            }elseif ($fost['_type'] == '_regitem') {
                $setting['content']['regitem'] = $fost['regitem'];
                db_update(table('setting'), array('content'=>array2string($setting['content'])), array('title'=>'setting'));
                $arr['success'] = 1;
                echo json_encode($arr); exit();
            }elseif ($fost['_type'] == '_other') {
                $setting['content']['other'] = $fost['other'];
                db_update(table('setting'), array('content'=>array2string($setting['content'])), array('title'=>'setting'));
                $arr['success'] = 1;
                echo json_encode($arr); exit();
			}elseif ($fost['_type'] == '_offsite') {
				$text = "";
				$text.= "define('OFF_SITE_IS', '".intval($fost['OFF_SITE_IS'])."');\r\n";
				$text.= "define('OFF_SITE_WHY', '"._Sys_Safe_Stop::addslashes_deep($fost['OFF_SITE_WHY'])."');\r\n";
				$text.= "define('OFF_REG_IS', '".intval($fost['OFF_REG_IS'])."');\r\n";
				$this->writesetting($text, 'offsite');
				$arr['success'] = 1;
				echo json_encode($arr); exit();
			}elseif ($fost['_type'] == '_sqlset') {
				if (empty($fost['OFF_SQL_TEMP'])) {
					$arr['success'] = 0;
					$arr['message'] = "您没有选择任何操作！";
					echo json_encode($arr); exit();
				}
				if (empty($fost['OFF_SQL_PASS'])) {
					$arr['success'] = 0;
					$arr['message'] = "请输入管理员密码！";
					echo json_encode($arr); exit();
				}
				if (md52($fost['OFF_SQL_PASS'], $_A['u']['encrypt']) != $_A['u']['userpass']){
					$arr['success'] = 0;
					$arr['message'] = "管理员密码错误！";
					echo json_encode($arr); exit();
				}
				$texttis = "暂停保护".intval($fost['OFF_SQL_TEMP'])."分钟";
				switch (intval($fost['OFF_SQL_TEMP'])) {
					case 60:
						$texttis = "暂停保护1小时";
						break;
					case 180:
						$texttis = "暂停保护3小时";
						break;
					case 360:
						$texttis = "暂停保护6小时";
						break;
					case 720:
						$texttis = "暂停保护12小时";
						break;
					case 1440:
						$texttis = "暂停保护24小时";
						break;
				}
				$text = "";
				$text.= "define('OFF_SQL_TEMP', ".(intval($fost['OFF_SQL_TEMP'])*60+SYS_TIME).");\r\n";
				$text.= "define('OFF_SQL_TEMP_TIS', '"._Sys_Safe_Stop::addslashes_deep($texttis)."');\r\n";
				$this->writesetting($text, 'sqlset');
				$arr['success'] = 1;
				echo json_encode($arr); exit();
			}elseif ($fost['_type'] == '_templet') {
				$this->writesetting(array2string(_Sys_Safe_Stop::addslashes_deep($fost['tem'])), 'templet', true);
				$arr['success'] = 1;
				echo json_encode($arr); exit();
            }else{
                $text = "";
                foreach($fost as $k=>$v){
                    if (substr($k,0,4) == 'SET_'){
                        $text.= "define('DIY_".substr($k,4)."', '"._Sys_Safe_Stop::addslashes_deep($v)."');\r\n";
                    }
                }
				$text.= "define('DIY_BASE_PROTOCOL', '".((defined('DIY_BASE_PROTOCOL'))?DIY_BASE_PROTOCOL:'REQUEST_URI')."');\r\n";
                $this->writesetting($text, 'config');
                $arr['success'] = 1;
                echo json_encode($arr); exit();
            }
		}
		$temphtml = ihttp_request(BASE_URI.'index.php/index/test/');
		if (strpos($temphtml['responseline'], "403") !== false || strpos($temphtml['responseline'], "404") !== false) {
			$nomoren = 1;
		}
		$temphtml = ihttp_request(BASE_URI.'index/test/');
		if (strpos($temphtml['responseline'], "403") !== false || strpos($temphtml['responseline'], "404") !== false) {
			$noweijingtai = 1;
		}
		if (!isset($_SERVER['REQUEST_URI'], $_SERVER['SCRIPT_NAME'])) {
			$nomoren = 1;
			$noweijingtai = 1;
		}
		//
		$sqltemp = OFF_SQL_TEMP - SYS_TIME;
		$sqlcountdown = $this->dgmstrftime($sqltemp);
        //
		tpl('settings', get_defined_vars());
	}

	/**
	 * 支付方式
	 */
	public function S_pay()
	{
		global $_GPC;
		if ($_GPC['do'] == 'install') {
			db_update(table('pay'), array('view'=>1), array('value'=>$_GPC['value']));
		}elseif ($_GPC['do'] == 'uninstall') {
			db_update(table('pay'), array('view'=>0), array('value'=>$_GPC['value']));
		}elseif ($_GPC['do'] == 'edit') {
			if ($_GPC['dosubmit']) {
				db_update(table('pay'), array('content'=>array2string($_GPC['data']['content'])), array('value'=>$_GPC['value']));
			}
			$pay = db_getone(table('pay'), array('value'=>$_GPC['value']));
			$pay['content'] = string2array($pay['content']);
			tpl('setting_pay_edit', get_defined_vars());
			exit();
		}
		tpl('setting_pay', get_defined_vars());
	}

	/**
	 * 数据库管理
	 */
	public function S_database()
	{
		global $_A,$_GPC;
		$this->load->helper("database");
		$do = $_GPC['do'];
		if ($do == 'datalist') {
			//获取列表
			$arr = array();
			$arr['success'] = 1;
			//
			$arr['list'] = get_optimize_list(true);
			$arr['import'] = get_dir_list();
			$arr['optimize'] = get_optimize_list();
			//
			echo json_encode($arr); exit();

		}elseif ($do == 'import') {
			//还原
            @set_time_limit(0);
			$random = generate_password(6, 2);
			$this->session->set_userdata('import_name', "");
			$this->session->set_userdata('import_random', $random);
			$arr = array();
			$arr['success'] = 0;
			$arr['nexturl'] = $_A['url'][2].'import/'.$random.'/';
			//
			$import = array();
			foreach(get_dir_list() AS $v) {
				$import[] = $v['pre'];
			}
			if (!in_array($_GPC['name'], $import)) {
				$arr['message'] = '备份不存在，请刷新后再试！';
				echo json_encode($arr); exit();
			}
			$dir = BASE_PATH.'caches/bakup/'.$_GPC['name'];
			if (!is_dir($dir)) {
				$arr['message'] = '目录不存在，请刷新后再试！';
				echo json_encode($arr); exit();
			}
			//
			$this->session->set_userdata('import_name', $_GPC['name']);
			$arr['success'] = 1;
			$arr['message'] = "正在恢复，请稍后...";
			echo json_encode($arr); exit();

		}elseif ($do == 'importdel') {
			//删除还原包
			$arr = array();
			$arr['success'] = 0;
			$arr['message'] = '';
			//
			$importdel = array();
			foreach(get_dir_list() AS $v) {
				$importdel[] = $v['pre'];
			}
			if (!in_array($_GPC['name'], $importdel)) {
				$arr['message'] = '备份不存在，请刷新后再试！';
				echo json_encode($arr); exit();
			}
			$dir = BASE_PATH.'caches/bakup/'.$_GPC['name'];
			if (!is_dir($dir)) {
				$arr['message'] = '目录不存在，请刷新后再试！';
				echo json_encode($arr); exit();
			}
			$this->load->helper('file');
			if (delete_files($dir, TRUE)) {
				@rmdir($dir);
				$arr['success'] = 1;
				$arr['message'] = '删除成功！';
			}else{
				$arr['message'] = '删除失败！';
			}
			echo json_encode($arr); exit();

		}elseif ($do == 'optimize') {
			//优化
            @set_time_limit(0);
			$arr = array();
			$arr['success'] = 0;
			$arr['message'] = '';
			//
			$optimize = array();
			foreach(get_optimize_list() AS $v) {
				$optimize[] = $v['Name'];
			}
			foreach($_GPC['optimize'] AS $k=>$v) {
				if (!in_array($v, $optimize)) {
					unset($_GPC['optimize'][$k]);
				}
			}
			if (empty($_GPC['optimize'])) {
				$arr['message'] = '请选择数据表！';
				echo json_encode($arr); exit();
			}
			$_timeval = implode(',', $_GPC['optimize']);
			if (db_query("OPTIMIZE TABLE {$_timeval}")){
				$arr['success'] = 1;
				$arr['message'] = '执行优化成功！';
			}else{
				$arr['message'] = '执行优化失败！';
			}
			//
			echo json_encode($arr); exit();

		}elseif ($do == 'repair') {
			//修复
            @set_time_limit(0);
			$arr = array();
			$arr['success'] = 0;
			$arr['message'] = '';
			//
			$repair = array();
			foreach(get_optimize_list(true) AS $v) {
				$repair[] = $v['Name'];
			}
			foreach($_GPC['repair'] AS $k=>$v) {
				if (!in_array($v, $repair)) {
					unset($_GPC['repair'][$k]);
				}
			}
			if (empty($_GPC['repair'])) {
				$arr['message'] = '请选择数据表！';
				echo json_encode($arr); exit();
			}
			$_timeval = implode(',', $_GPC['repair']);
			if (db_query("REPAIR TABLE {$_timeval}")){
				$arr['success'] = 1;
				$arr['message'] = '执行修复成功！';
			}else{
				$arr['message'] = '执行修复失败！';
			}
			//
			echo json_encode($arr); exit();

		}elseif ($do == 'export') {
			//备份
            @set_time_limit(0);
			$arr = array();
			$arr['success'] = 1;
			$arr['message'] = '';
			$arr['nexturl'] = '';
			//
			$sizelimit = intval($_GPC['sizelimit'])?intval($_GPC['sizelimit']):1;
			$random = intval($_GPC['random'])?intval($_GPC['random']):mt_rand(1000, 9999);
			$fileid = intval($_GPC['fileid'])?intval($_GPC['fileid']):1;
			if ($fileid == 1) { $_SESSION['database_insert'] = ""; }
			//获取数据表
			$tablespath = BASE_PATH.'caches/bakup/';
            $tablestemp = $tablespath.'_tables.php';
            $tablefield = $tablespath.'_fields.php';
			if ($fileid > 1 && file_exists($tablestemp) && file_exists($tablefield)) {
				$tables = @include $tablestemp;
				$fields = @include $tablefield;
			}else{
				$tables = array();
                $fields = array();
				$export = $_GPC['export'];
				foreach ($export as $_val){
                    $tables[] = $_val;
                    $fields[$_val] = get_table_fields($_val);
                }
				make_dir($tablespath);
				file_put_contents($tablestemp, '<?php
				if (!defined("BASEPATH")) exit("No direct script access allowed");
				return '.array2string($tables,0).'; ?>');
				file_put_contents($tablefield, '<?php
				if (!defined("BASEPATH")) exit("No direct script access allowed");
				return '.array2string($fields,0).'; ?>');
			}
            if (empty($tables)) {
                $arr['success'] = 0;
                $arr['message'] = '请选择数据表！';
                echo json_encode($arr); exit();
            }
            if (empty($fields)) {
                $arr['success'] = 0;
                $arr['message'] = '请求出错，请重试！';
                echo json_encode($arr); exit();
            }
			//
			$this->load->dbutil();
			$tablespath.= date('Ymd').'_'.$random.'/';
			make_dir($tablespath);
			$filename = 'vwins_'.date('Ymd').'_'.$random.'_'.$fileid.'.sql';
			$_timeurl = get_link("do|sizelimit|random|fileid|tabi|startfrom");
			$_timeurl.= "&do=export&sizelimit=".$sizelimit."&random=".$random."&fileid=".intval($fileid + 1);
			$tabi = intval($_GPC['tabi']);
			$tabledump = "";
			if ($_SESSION['database_insert'] != "true") {
				$j = 0;
				foreach ($tables as $_val){
					if ($tabi <= 0 || $j > $tabi) {
						$prefs = array(
							'tables'    => array($_val),
							'ignore'    => array(),
							'format'    => 'txt',
							'filename'  => NULL,
							'add_drop'  => TRUE,
							'add_insert'    => FALSE,
							'newline'   => "\n"
						);
						$tabledump.= $this->dbutil->backup($prefs);
						if (strlen($tabledump) >= $sizelimit * 1000) {
							$_timeurl.= "&tabi=".$j;
							file_put_contents($tablespath.$filename, $tabledump);
							$arr['message'] = "备份文件 {$filename} 写入成功<br/>正在继续备份，请不要关闭此网页！";
							$arr['nexturl'] = $_timeurl;
							echo json_encode($arr); exit();
						}
					}
					$j++;
				}
				$tabi = 0;
			}
			$_SESSION['database_insert'] = "true";
			$startfrom = max(0, intval($_GPC['startfrom']));
			$limitstart = 0;
			$j = 0;
			foreach ($tables as $_val){
				if ($tabi <= 0 || $j > $tabi) {
                    $is_int = $fields[$_val]['is_int'];
                    $field_str = $fields[$_val]['field_str'];
					$w = 0;
					while(strlen($tabledump) < $sizelimit * 1000) {
						$limitstart = $w * 10;
						$rows = db_getall("SELECT * FROM `".$_val."` LIMIT ".($startfrom + $limitstart).", 10");
						if (empty($rows)) {
							break;
						}else{
							foreach($rows AS $row) {
								$val_str = '';
                                $l = 0;
								foreach ($row as $v) {
									if ($v === NULL) {
										$val_str .= 'NULL';
									}else{
                                        $val_str .= (empty($is_int[$l])) ? db_escape($v) : $v;
									}
									$val_str .= ', ';
                                    $l++;
								}
								$val_str = preg_replace('/, $/' , '', $val_str);
                                $tabledump .= "INSERT INTO `".$_val."` (".$field_str.") VALUES (".$val_str.");\n";
							}
						}
						$w++;
					}
					if (strlen($tabledump) >= $sizelimit * 1000) {
						$_timeurl.= "&tabi=".(!empty($w)?($j-1):$j);
						$_timeurl.= "&startfrom=".($startfrom + $limitstart + 10);
						file_put_contents($tablespath.$filename, $tabledump);
						$arr['message'] = "备份文件 {$filename} 写入成功<br/>当前备份表 {$_val}<br/>正在继续备份，请不要关闭此网页！";
						$arr['nexturl'] = $_timeurl;
						echo json_encode($arr); exit();
					}
					$startfrom = 0;
				}
				$j++;
			}
			if (!empty($tabledump)) {
				$_timeurl.= "&tabi=".$j;
				file_put_contents($tablespath.$filename, $tabledump);
				$arr['message'] = "备份文件 {$filename} 写入成功<br/>正在继续备份，请不要关闭此网页！";
				$arr['nexturl'] = $_timeurl;
				echo json_encode($arr); exit();
			}
            file_put_contents($tablespath.'info.php', '<?php
            if (!defined("BASEPATH")) exit("No direct script access allowed");
            return '.array2string(get_defined_constants(), 0).'; ?>');
			if (file_exists($tablestemp)) { @unlink($tablestemp); }
			if (file_exists($tablefield)) { @unlink($tablefield); }
			$arr['message'] = "备份成功！<br/>文件保存在：".BASE_DIR.str_replace(BASE_PATH, '', $tablespath);
			echo json_encode($arr); exit();
		}
		//
		tpl('setting_database', get_defined_vars());
	}

	/**
	 * 客户管理
	 */
	public function S_users()
	{
		global $_GPC;
		$page = value($_GPC['param'], 2, 'int');
		$pageurl = urlencode($this->base->url[4]);
		//
		$wheresql = "";
		if ($_GPC['t'] == "month"){
			$wheresql.= "`indate`>=".mktime(0,0,0,date('m'),1,date('Y'))." AND ";
		}else{
            if ($_GPC['companyname']){
                $wheresql.= "`companyname` LIKE '%".db_escape_str($_GPC['companyname'])."%' AND ";
            }
            if ($_GPC['username']){
                $wheresql.= "`username` LIKE '%".db_escape_str($_GPC['username'])."%' AND ";
            }
			if ($_GPC['phone']){
				$wheresql.= "`phone` LIKE '%".db_escape_str($_GPC['phone'])."%' AND ";
			}
			if ($_GPC['tel']){
				$wheresql.= "`tel` LIKE '%".db_escape_str($_GPC['tel'])."%' AND ";
			}
			if ($_GPC['fullname']){
				$wheresql.= "`fullname` LIKE '%".db_escape_str($_GPC['fullname'])."%' AND ";
			}
		}
		$orderby = " `indate` DESC ";
		if ($_GPC['openfunc']){
			$orderby = " (CASE WHEN `userid`=".intval($_GPC['openfunc'])." THEN 0 ELSE 1 END),`indate` DESC ";
		}
		tpl('users', get_defined_vars());
	}

	/**
	 * 修改所属管理账号
	 */
	public function S_eduser()
	{
		global $_GPC;
		$id = value($_GPC['param'], 2, 'int');
		$al = db_getone(table('users_al'), array('id'=>$id));
		$ul = db_getall(table('users'), '', '`indate` DESC');
		if (isset($_GPC['eduseruserid'])) {
			$arr = array();
			$arr['success'] = 0;
			$row = db_getone(table('users'), array('userid'=>intval($_GPC['eduseruserid'])));
			if (empty($row) || $row['userid'] == $al['userid']) {
				$arr['message'] = '所属管理账号选择错误！';
				echo json_encode($arr); exit();
			}
			db_update(table('users_al'),
				array('userid'=>$row['userid'], 'username'=>$row['username'], 'companyname'=>$row['companyname']),
				array('id'=>$al['id']));
			db_update(table('users_functions'),
				array('userid'=>$row['userid']),
				array('alid'=>$al['id']));
			$this->user->handle_function($al['userid']);
			$this->user->handle_function($row['userid']);
			//
			$arr['success'] = 1;
			$arr['message'] = '修改成功！';
			echo json_encode($arr); exit();
		}
		tpl('users_eduser', get_defined_vars());
	}

	/**
	 * 会员 新增
	 */
	public function S_infoadd()
	{
		if ($this->input->post("dosubmit")){
			$fost = $this->input->post();
			$arr = $_arr = array();
			$arr['success'] = 0;
			//
			$_arr['admin'] = intval($fost['admin']);
			$_arr['username'] = $fost['username'];
			if (empty($fost['username']) || strlen($fost['username']) < 3) {
				$arr['message'] = '用户名不能小于三个字符';
				echo json_encode($arr); exit();
			}
			if (empty($fost['userpass']) || strlen($fost['userpass']) < 6) {
				$arr['message'] = '密码不能小于6个字符';
				echo json_encode($arr); exit();
			}
			if ($fost['userpass']) {
				$_arr['encrypt'] = generate_password(6);
				$_arr['userpass'] = md52($fost['userpass'], $_arr['encrypt']);
			}
			$_arr['point'] = intval($fost['point']);
			$_arr['fullname'] = $fost['fullname'];
			$_arr['phone'] = $fost['phone'];
			$_arr['email'] = $fost['email'];
			$_arr['qqnum'] = $fost['qqnum'];
			$_arr['companyname'] = $fost['companyname'];
			$_arr['tel'] = $fost['tel'];
			$_arr['linkaddr'] = $fost['linkaddr'];
			$_arr['address'] = $fost['address'];
			$_arr['indate'] = SYS_TIME;
			$_arr['inip'] = ONLINE_IP;
			$_arr['regsetting'] = array2string($fost);
			//判断用户名
			$wheresql = " WHERE `username`='".$_arr['username']."'";
			$_count = $this->ddb->get_total("SELECT COUNT(*) AS num FROM ".table('users').$wheresql);
			if ($_count){
				$arr['message'] = '用户名已存在';
				echo json_encode($arr); exit();
			}
			//判断邮箱
			if ($_arr['email']) {
				$wheresql = " WHERE `email`='".$_arr['email']."'";
				$_count = $this->ddb->get_total("SELECT COUNT(*) AS num FROM ".table('users').$wheresql);
				if ($_count){
					$arr['message'] = '邮箱已存在';
					echo json_encode($arr); exit();
				}
			}
			//判断手机号码
			if ($_arr['email']) {
				$wheresql = " WHERE `phone`='".$_arr['phone']."'";
				$_count = $this->ddb->get_total("SELECT COUNT(*) AS num FROM ".table('users').$wheresql);
				if ($_count){
					$arr['message'] = '手机号码已存在';
					echo json_encode($arr); exit();
				}
			}
			//开始添加
			$_arr['userid'] = $this->ddb->insert(table('users'), $_arr, true);
			if ($_arr['userid']){
				if ($_arr['point'] != 0) {
					db_insert(table('users_point'), array(
							'userid'=>$_arr['userid'],
							'change'=>$_arr['point'],
							'point'=>$_arr['point'],
							'pointtxt'=>'系统注册',
							'indate'=>SYS_TIME
						));
				}
				$arr['success'] = 1;
				$arr['message'] = '注册成功';
			}else{
				$arr['message'] = '注册失败';
			}
			echo json_encode($arr); exit();
		}
		tpl('users_info_add', get_defined_vars());
	}

	/**
	 * 会员资料修改
	 */
	public function S_info()
	{
		global $_GPC;
		$users = $this->ddb->getone("SELECT * FROM ".table('users'), array('userid'=>value($_GPC['param'], 2, 'int')));
		if (empty($users)){
			message(null, "会员不存在");
		}
		if ($this->input->post("dosubmit")){
			$fost = $this->input->post();
			$arr = $_arr = array();
			$arr['success'] = 0;
			//
			$_arr['admin'] = intval($fost['admin']);
			if ($fost['userpass']) {
				$_arr['encrypt'] = generate_password(6);
				$_arr['userpass'] = md52($fost['userpass'], $_arr['encrypt']);
			}
			$_arr['point'] = intval($fost['point']);
			$_arr['fullname'] = $fost['fullname'];
			$_arr['phone'] = $fost['phone'];
			$_arr['email'] = $fost['email'];
			$_arr['qqnum'] = $fost['qqnum'];
			$_arr['companyname'] = $fost['companyname'];
			$_arr['tel'] = $fost['tel'];
			$_arr['linkaddr'] = $fost['linkaddr'];
			$_arr['address'] = $fost['address'];
			//判断邮箱
			if ($_arr['email']) {
				$wheresql = " WHERE `email`='".$_arr['email']."' AND `userid`!=".$users['userid'];
				$_count = $this->ddb->get_total("SELECT COUNT(*) AS num FROM ".table('users').$wheresql);
				if ($_count){
					$arr['message'] = '邮箱已存在';
					echo json_encode($arr); exit();
				}
			}
			//判断手机号码
			if ($_arr['phone']) {
				$wheresql = " WHERE `phone`='".$_arr['phone']."' AND `userid`!=".$users['userid'];
				$_count = $this->ddb->get_total("SELECT COUNT(*) AS num FROM ".table('users').$wheresql);
				if ($_count){
					$arr['message'] = '手机号码已存在';
					echo json_encode($arr); exit();
				}
			}
			//开始修改
			if ($this->ddb->update(table('users'), $_arr, array('userid'=>$users['userid']))){
				$this->ddb->update(table('users_al'), array('companyname'=>$_arr['companyname']), array('userid'=>$users['userid']));
				if ($_arr['point'] != $users['point']) {
					db_insert(table('users_point'), array(
						'userid'=>$users['userid'],
						'change'=>$_arr['point'] - $users['point'],
						'point'=>$_arr['point'],
						'pointtxt'=>'系统修改',
						'indate'=>SYS_TIME
					));
				}
				$arr['success'] = 1;
				$arr['message'] = '修改成功';
			}else{
				$arr['message'] = '修改失败';
			}
			echo json_encode($arr); exit();
		}
		tpl('users_info', get_defined_vars());
	}

	/**
	 * 获取公众号的功能列表
	 */
	public function S_fun()
	{
		global $_GPC;
		$alrow = $this->ddb->getone("SELECT * FROM ".table('users_al'), array('id'=>value($_GPC['param'], 2, 'int')));
		$lists = isset($alrow['function'])?string2array($alrow['function']):array();
		$inorder = array();
		foreach ($lists as $key => $val) {
			$inorder[$key] = array($val['default'], $val['ida']);
		}
		array_multisort($inorder, SORT_ASC, $lists);
		tpl('fun_info', get_defined_vars());
	}

	/**
	 * 开通功能
	 */
	public function S_openfun()
	{
		global $_GPC;
		$userid = value($_GPC['param'], 2, 'int');
		if ($this->input->post("dosubmit")){
			$fost = $this->input->post();
			$arr = $_arr = array();
			$arr['success'] = 0;
			//
			$row = $this->ddb->getone("SELECT * FROM ".table('users_al'), array('userid'=>$userid, 'id'=>$fost['alid']));
			if (empty($row)){
				$arr['message'] = '请选择正确的账号！';
				echo json_encode($arr); exit();
			}
            $functionsid = value($_GPC, 'functionsid', true);
            if (empty($functionsid)) {
                $arr['message'] = '请正确选择需要开通的功能！';
                echo json_encode($arr); exit();
            }
            $okn = $ern = 0;
			$this->load->helper("cloud");
            foreach($functionsid AS $fid) {
                $frow = $this->ddb->getone("SELECT * FROM ".table('functions'), array('id'=>intval($fid)));
                if (!empty($frow)){
                    $ufrow = $this->ddb->getone("SELECT * FROM ".table('users_functions'), array('alid'=>$row['id'], 'fid'=>$fid));
                    if (empty($ufrow)){
						if ($row['wx_level'] == 7) {
							$setting = string2array($frow['setting']);
							$setting['category'] = cloud_category($frow['title_en'], $frow['version']);
							db_update(table('functions'), array('setting'=>array2string($setting)), array('id'=>$frow['id']));
							if (!in_array('wxcorp', $setting['category'])) {
								$arr['message'] = "【".$frow['title']."】暂不支持企业号！";
								echo json_encode($arr); exit();
							}
						}
                        //
                        $_arr['userid'] = $userid;
                        $_arr['fid'] = $fid;
                        $_arr['alid'] = $row['id'];
                        $_arr['al_name'] = $row['al_name'];
                        $_arr['wx_name'] = $row['wx_name'];
                        $_arr['indate'] = SYS_TIME;
                        $_arr['enddate'] = 0;
                        if ($this->ddb->insert(table('users_functions'), $_arr)){
                            $okn++;
                        }else{
                            $ern++;
                        }
                    }
                }
            }
            $arr['message'] = '操作完成';
            if ($okn) {
                $arr['message'].= '(添加成功'.$okn.'个)';
            }
            if ($ern) {
                $arr['message'].= '(添加失败'.$ern.'个)';
            }
            $arr['message'] = str_replace(')(','、',$arr['message']);
            $arr['success'] = 1;
            $this->user->handle_function($userid);
			echo json_encode($arr); exit();
		}
		$allist = db_getall("SELECT * FROM ".table('users_al'), array('userid'=>intval($userid)), '`indate` desc');
		$funlist = db_getall("SELECT * FROM ".table('functions'), array('status'=>'使用中'));
		tpl('fun_open', get_defined_vars());
	}

	/**
	 * 删除公众号的功能
	 */
	public function S_delfun()
	{
		global $_GPC;
        $this->load->model('del');
		$id = value($_GPC['param'], 2, 'int');
		$row = $this->ddb->getone("SELECT * FROM ".table('users_functions'), array('id'=>intval($id)));
		$fun = $this->ddb->getone("SELECT `default` FROM ".table('functions'), array('id'=>intval($row['fid'])));

		$arr = array();
		$arr['success'] = 0;
        if ($fun['default']) {
            $arr['message'] = '系统基础模块不可删除';
            echo json_encode($arr); exit();
        }
		if ($this->del->deluse($row['id'])){
			$this->user->handle_function($row['userid']);
			$arr['success'] = 1;
            $arr['message'] = '删除成功';
		}else{
            $arr['message'] = '删除失败';
        }
		echo json_encode($arr); exit();
	}

	/**
	 * 删除用户(公司)
	 */
	public function S_delcompany()
	{
		global $_GPC;
        $this->load->model('del');
		$userid = value($_GPC['param'], 2, 'int');
		$arr = array();
		$arr['success'] = 0;
		$row = $this->ddb->getone("SELECT * FROM ".table('users'), array('userid'=>$userid));
		if (empty($row)){
			$arr['message'] = '数据参数错误！';
			echo json_encode($arr); exit();
		}
		//
        if ($this->del->deluser($row['userid'])){
            $arr['success'] = 1;
            $arr['message'] = '删除成功';
        }else{
			$arr['message'] = '删除失败';
		}
		echo json_encode($arr); exit();
	}

	/**
	 * 系统升级
	 */
	public function S_upgrade()
	{
		global $_GPC;
		$this->load->helper("cloud");
		if ($_GPC['method'] == "upgrade_step1") {
			$_SESSION['_RELEASE'.ES_RELEASE] = '';
			$arr = array();
			$arr['success'] = 0;
			$arr['message'] = '';
			$r = cloud_prepare();
			if(is_error($r)) {
				$arr['message'] = $r['message'];
				echo json_encode($arr); exit();
			}
			$pro = cloud_pro_upgrade(ES_RELEASE, ES_VERSION);
			if(is_error($pro)) {
				$arr['message'] = $pro['message'];
				echo json_encode($arr); exit();
			}
			$pro = json_decode($pro, true);
			$filelist = "";
			foreach($pro['filelist'] AS $item) {
				$filelist.= "<p>".$item['path']."</p>";
			}

			$arr['success'] = 1;
			$arr['message'] = "<div class='profrom'>当前版本：<br/>".ES_VERSION."_".ES_RELEASE."</div>";
			$arr['message'].= "<div class='proto'>新版本：<br/>".$pro['to_version']."_".$pro['to_release']."</div>";
			$arr['message'].= "<div class='profile'>相关文件：<div>".$filelist."</div></div>";
			$arr['message'].= "<div class='proinfo'>版本说明：<div>".$pro['version_description']."</div></div>";
			$arr['filelist'] = $pro['filelist'];
			echo json_encode($arr); exit();
		}elseif ($_GPC['method'] == "upgrade_step2") {
			$arr = array();
			$arr['success'] = 0;
			$arr['message'] = '';
			$r = cloud_prepare();
			if(is_error($r)) {
				$arr['message'] = $r['message'];
				echo json_encode($arr); exit();
			}

			$paths = array();
			$paths[] = array('type'=>value($_GPC,'path_type'), 'path'=>str_replace("\\\\","/",value($_GPC,'path')));
			$cloudfiles = json_encode($paths);
			$clouddowns = cloud_pro_downs(ES_RELEASE, ES_VERSION, $cloudfiles);
			if(is_error($clouddowns)) {
				$arr['message'] = $clouddowns['message'];
			}else{
				$arr['success'] = 1;
			}
			echo json_encode($arr); exit();
		}elseif ($_GPC['method'] == "upgrade_step3") {
			$arr = array();
			$arr['success'] = 0;
			$arr['message'] = '';
			$r = cloud_prepare();
			if(is_error($r)) {
				$arr['message'] = $r['message'];
				echo json_encode($arr); exit();
			}
			cloud_release_copys(ES_RELEASE);
			$pro = cloud_pro_sql(ES_RELEASE, ES_VERSION);
			if(is_error($pro)) {
				$arr['message'] = $pro['message'];
				echo json_encode($arr); exit();
			}
			$pro = json_decode($pro, true);
			if ($pro['sql']) {
				db_run($pro['sql']);
			}
			//升级的相关文件
			$updir = BASE_PATH.'caches'.DIRECTORY_SEPARATOR.'upgrade'.DIRECTORY_SEPARATOR.$pro['to_release'].DIRECTORY_SEPARATOR;
			if (is_dir($updir)) {
				$file_list = glob($updir.'*');
				if(!empty($file_list)) {
					foreach ($file_list as $filev) {
						$ending = strtolower(substr($filev, -4, 4));
						if (in_array($ending, array('.sql', '.php')) && file_exists($filev)) {
							if ($ending == '.sql' && $data = file_get_contents($filev)) {
								db_run($data); @unlink($filev);
							} elseif ($ending == '.php') {
								include_once $filev; @unlink($filev);
							}
						}
					}
				}
				@unlink($updir);
			}
			//保存版本信息
			file_put_contents(BASE_PATH.'caches/version.php',
				"<?php define('ES_VERSION', '".$pro['to_version']."'); define('ES_RELEASE', '".$pro['to_release']."'); ?>");
			$arr['success'] = 1;
			$arr['message'] = '升级完成！';
			echo json_encode($arr); exit();
		}
		$tem = array();
		if (file_exists(FCPATH."caches".DIRECTORY_SEPARATOR."cache.templet.txt")) {
			$tem = string2array(file_get_contents(FCPATH."caches".DIRECTORY_SEPARATOR."cache.templet.txt"));
		}
		tpl('setting_upgrade', get_defined_vars());
	}

    /**
     * 绑定云中心
     */
    public function S_cloud()
    {
        global $_GPC;
        $this->load->helper("cloud");
		$row = db_getone("SELECT * FROM ".table('setting'), array('title'=>'cloud'));
		if (empty($row)) {
			db_insert(table('setting'), array('title'=>'cloud'), true);
			$row['title'] = 'cloud';
			$row['content'] = '';
		}
        $contentset = string2array($row['content']);
		if ($_GPC['method'] == 'money_over') {
			$pars = array();
			$pars['method'] = 'money_over';
			$pars['name'] = $contentset['cloudname'];
			$dat = ihttp_post(CLOUD_GATEWAY, $pars);
			if (is_error($dat)) {
				echo $dat['message'];
			}else{
				$content = json_decode($dat['content'], true);
				echo floatval($content['money'])."<a href='".$content['buyurl']."' target='_blank' style='color:#0198cd;padding-left:5px;'>(充值交易币)</a>";
			}
			exit();
		}
        if (isset($_GPC['dosubmit'])) {
            $contentset['cloudname'] = $_GPC['cloudname'];
            $contentset['cloudpass'] = ($_GPC['cloudpass']==md52($contentset['cloudpass']))?$contentset['cloudpass']:$_GPC['cloudpass'];
            $r = cloud_namepass($contentset['cloudname'], $contentset['cloudpass']);
            if(is_error($r)) {
                $contentset['cloudok'] = 0;
                $this->ddb->update(table('setting'), array('content'=>array2string($contentset)), array("title"=>$row['title']));
                message(null, $r['message']);
            }else{
                $contentset['cloudok'] = 1;
                $this->ddb->update(table('setting'), array('content'=>array2string($contentset)), array("title"=>$row['title']));
                message(null, "绑定成功", get_url());
            }
        }
        tpl('setting_cloud', get_defined_vars());
    }

	/**
	 * 功能模块管理
	 */
	public function S_functions()
	{
        global $_GPC;
        $this->load->helper("cloud");
        if ($_GPC['method'] == 'upgrade_lists') {
            setcookie('_RELEASEAPP:'.ES_RELEASE, '', SYS_TIME - 1, BASE_DIR);
            $r = ihttp_post(CLOUD_GATEWAY, $_POST, 10);
			$content = json_decode($r['content'], true);
			$funs = explode(",", $_POST['funs']);
			$local = array();
			foreach($funs AS $item) {
				if ($item && !isset($content[$item])) {
					$xmlpath = BASE_PATH . 'addons/' . $item . '/manifest.xml';
					if (file_exists($xmlpath)) {
						$manifest = ext_module_manifest($item);
						$local[$item] = $manifest['application'];
					}
				}
			}
			$content['__LOCAL'] = $local;
            if(!is_error($r)) echo json_encode($content);
            exit();
        }
        if ($_GPC['method'] == 'upgrade_lists_have') {
            setcookie('_RELEASEAPP:'.ES_RELEASE, '1', SYS_TIME + 21600, BASE_DIR);
            exit();
        }
		if ($_GPC['method'] == 'upgrade_upcontent') {
			$r = ihttp_post(CLOUD_GATEWAY, $_POST, 10);
			if(!is_error($r)) echo $r['content']?$r['content']:'无';
			exit();
		}
		tpl('functions_admin', get_defined_vars());
	}

    /**
     * 安装新功能 列表
     */
    public function S_functionsadd()
    {
        $this->load->helper("cloud");
        $moduleids = array();
        $moduleids[] = 'system';
        $modules = db_getall(table('functions'), '', '`id` DESC');
        if(!empty($modules)) {
            foreach($modules as $m) {
                $moduleids[] = $m['title_en'];
            }
        }
        $path = BASE_PATH . 'addons/';
        $localUninstallModules = array();
        $localUninstallModules_noso = array();
        $localUninstallModules_title = array();
        if (is_dir($path)) {
            if ($handle = opendir($path)) {
                while (false !== ($modulepath = readdir($handle))) {
                    if ($modulepath != "." && $modulepath != "..") {
                        $manifest = ext_module_manifest($modulepath);
                        if (is_array($manifest) && !empty($manifest['application']['title_en']) && !in_array($manifest['application']['title_en'], $moduleids)) {
                            $m = ext_module_convert($manifest);
                            if(!in_array(ES_VERSION, $manifest['versions'])) {
                                $m['version_error'] = true;
                            }
                            $localUninstallModules[$m['title']] = $m;
                            if($m['issolution'] <> 1) {
                                $localUninstallModules_noso[$m['title']] = $m;
                                $localUninstallModules_title[$m['title']] = $m['title'];
                            }
                            $moduleids[] = $manifest['application']['title_en'];
                        }
                    }
                }
            }
        }
        $prepare_module = json_encode(array_keys($localUninstallModules_noso));
        $prepare_module_title = json_encode($localUninstallModules_title);
        tpl('functions_add', get_defined_vars());
    }

	/**
	 * 安装新功能 云商城
	 */
	public function S_functionstore()
	{
		global $_GPC;
		$this->load->helper("cloud");
		//
		$row = db_getone("SELECT * FROM ".table('setting'), array('title'=>'cloud'));
		$contentset = array();
		if (!empty($row)) {
			$contentset = string2array($row['content']);
		}
		$key = md52($contentset['cloudname'].$contentset['cloudpass']);
		if ($_GPC['cloudkey']) {
			$arr = array();
			$arr['success'] = 0;
			$arr['message'] = '';
			//
			if ($contentset['cloudok']) {
				$r = cloud_cloudkey($contentset['cloudname'], $contentset['cloudpass']);
				if(!is_error($r) && $r['message'] == $key) {
					$arr['success'] = 1;
					$arr['message'] = $r['message'];
					$this->session->set_userdata('cloud:key:'.$key, $r['message']);
				}
			}
			echo json_encode($arr); exit();
		}
		$cloudkey = $this->session->userdata('cloud:key:'.$key);
		tpl('functions_store', get_defined_vars());
	}

    /**
    * 安装新功能 列表
    */
    public function S_functionsinstall()
    {
        global $_GPC;
        $this->load->helper("cloud");
        $modulename = $_GPC['en'];
        if($this->ddb->getone(table('functions'), array("title_en"=>$modulename))) {
            message(null, '功能模块已经安装或是唯一标识已存在！');
        }
		$modulepath = BASE_PATH . 'addons/' . $modulename . '/';
		$manifest = ext_module_manifest($modulename);
		if (!empty($manifest)) {
			$r = cloud_m_prepare($modulename);
			if(is_error($r)) {
				message(null, $r['message']);
			}
		}
        if(empty($manifest)) {
            message(null, '模块安装配置文件不存在或是格式不正确！');
        }
        $this->manifest_check($modulename, $manifest);
        $module = ext_module_convert($manifest);
		if(!file_exists($modulepath . 'site.php')) {
			message(null,'模块缺少处理文件！');
		}
        if(db_insert(table('functions'), $module)) {
            $this->set_bindings($modulename, $module);
            $this->set_category($modulename, $module);
            if(strexists($manifest['install'], '.php')) {
                if(file_exists($modulepath . $manifest['install'])) {
                    include_once $modulepath . $manifest['install'];
                    @unlink($modulepath . $manifest['install']);
                }
            }elseif (strexists($manifest['install'], '.sql') && $data = ext_file_get_contents($modulepath . $manifest['install'])) {
                db_run($data);
                @unlink($modulepath . $manifest['install']);
            } else {
                db_run($manifest['install']);
            }
            message(null, '功能模块安装成功！', weburl('system/settings/functions'));
        } else {
            message(null, '功能模块安装失败, 请联系模块开发者！');
        }
    }

    /**
     * 更新模块
     */
    public function S_functionsupgrade()
    {
        global $_GPC;
        $this->load->helper("cloud");
        $modulename = $_GPC['en'];
        $module = $this->ddb->getone(table('functions'), array("title_en"=>$modulename));
        if (empty($module)) {
            message(null, '功能模块已经被卸载或是不存在！');
        }
		$oldversion = $module['version'];
		if ($_GPC['act'] == "updatefile"){
			$arr = array();
			$arr['success'] = 0;
			$paths = array();
			$paths[] = array('type'=>value($_GPC,'path_type'), 'path'=>str_replace("\\\\","/",value($_GPC,'path')));
			$cloudfiles = json_encode($paths);
			$clouddowns = cloud_ext_module_downs($cloudfiles, $modulename, $oldversion);
			if(is_error($clouddowns)) {
				$arr['message'] = $clouddowns['message'];
			}else{
				$arr['success'] = 1;
			}
			echo json_encode($arr); exit();
		}
        $modulepath = BASE_PATH . 'addons/' . $modulename . '/';
        $manifest = ext_module_manifest($module['title_en']);
        if (empty($manifest) || $module['cloud']) {
            $r = cloud_prepare();
            if(is_error($r)) {
                message(null, $r['message'], weburl('system/settings/cloud'));
            }
            $manifest = cloud_ext_module_manifest($modulename, $oldversion);
            if(is_error($manifest)) {
                message(null, $manifest['message']);
            }
            $_upgrade = cloud_module_upgrade_file($modulename, $manifest['upgrade'], $oldversion);
            if(is_error($_upgrade)) {
                message(null, $_upgrade['message']);
            }
            $is_cloud = true;
        }
        if (empty($manifest)) {
            message(null, '模块安装配置文件不存在或是格式不正确！');
        }
		$this->manifest_check($modulename, $manifest, false, true);
        if(ver_compare($module['version'], $manifest['application']['version']) != -1) {
            message(null, '已安装的模块版本不低于要更新的版本, 操作无效.');
        }
        if(!file_exists($modulepath . 'site.php')) {
            message(null, '模块缺少处理文件！');
        }
        $module = ext_module_convert($manifest);
        unset($module['id']);
        if (isset($is_cloud)) {
			$module['cloud'] = 1;
			if ($_GPC['act'] != "success") {
				$cloudfiles = cloud_ext_module_files($modulename, 1, $oldversion);
				if(is_error($cloudfiles)) {
					message(null, $cloudfiles['message']);
				}
				if ($cloudfiles) {
					tpl('functions_upgrade', get_defined_vars()); exit();
				}
			}
        }
        if(!empty($manifest['upgrade'])) {
            if(strexists($manifest['upgrade'], '.php')) {
                if(file_exists($modulepath . $manifest['upgrade'])) {
                    include_once $modulepath . $manifest['upgrade'];
                    if (isset($is_cloud)) @unlink($modulepath . $manifest['upgrade']);
                }
            }elseif (strexists($manifest['upgrade'], '.sql') && $data = ext_file_get_contents($modulepath . $manifest['upgrade'])) {
                db_run($data);
                if (isset($is_cloud)) @unlink($modulepath . $manifest['upgrade']);
            } else {
                db_run($manifest['upgrade']);
            }
        }
        $this->ddb->update(table('functions'), $module, array('title_en' => $modulename));
        $this->set_bindings($modulename, $module);
        $this->set_category($modulename, $module);
		cloud_upgrade($module['title_en'], $module['version']);
        message(null, '功能模块更新成功！', weburl('system/settings/functions/?winopen=1'));
    }

    /**
     * 卸载功能模块
     */
    public function S_functionsuninstall()
    {
        global $_GPC;
        $this->load->helper("cloud");
        $id = intval($_GPC['id']);
        $module = $this->ddb->getone(table('functions'), array('id'=>$id));
        if(empty($module)) {
            message(null, '功能模块已经被卸载或是不存在！');
        }
        if(!empty($module['default'])) {
            message(null, '系统模块不能卸载！');
        }
        $modulepath = BASE_PATH . 'addons/' . $module['title_en'] . '/';
        $manifest = ext_module_manifest($module['title_en']);
        if (empty($manifest) || $module['cloud']) {
            $r = cloud_prepare();
            if(is_error($r)) {
                message(null, $r['message'], weburl('system/settings/cloud'));
            }
            $manifest = cloud_ext_module_manifest($module['title_en'], '', $module['version']);
            if(is_error($manifest)) {
                message(null, $manifest['message']);
            }
            $_uninstall = cloud_module_uninstall_file($module['title_en'], $manifest['uninstall']);
            if(is_error($_uninstall)) {
                message(null, $_uninstall['message']);
            }
            $is_cloud = true;
        }
		$this->manifest_check($module['title_en'], $manifest, false, true);
        if(db_delete(table('functions'), array('id'=>$id))) {
            $this->del_bindings($module['title_en']);
            if(!empty($manifest['uninstall'])) {
                if(strexists($manifest['uninstall'], '.php')) {
                    if(file_exists($modulepath . $manifest['uninstall'])) {
                        include_once $modulepath . $manifest['uninstall'];
                        if (isset($is_cloud)) @unlink($modulepath . $manifest['uninstall']);
                    }
                }elseif (strexists($manifest['uninstall'], '.sql') && $data = ext_file_get_contents($modulepath . $manifest['uninstall'])) {
                    db_run($data);
                    if (isset($is_cloud)) @unlink($modulepath . $manifest['uninstall']);
                } else {
                    db_run($manifest['uninstall']);
                }
            }
			if (isset($is_cloud)) {
				cloud_uninstall($module['title_en'], $module['version']);
			}
			//刷新、删除数据
			$funlist = db_getall(table('users_functions'), array('fid'=>$id));
			$uuarr = array();
			foreach($funlist as $val){
				db_delete(table('users_functions'), array('id'=>$val['id']));
				if (!in_array($uuarr, $val['userid'])) {
					$uuarr[] = $val['userid'];
					$this->user->handle_function($val['userid']);
				}
			}
            message(null, '功能模块卸载成功！', weburl('system/settings/functions'));
        }else{
            message(null, '功能模块卸载失败, 请联系模块开发者！');
        }
    }

	/**
	 *
	 */
	public function S_setcloud()
	{
		global $_GPC;
		$setcloud = explode(",", $_GPC['setcloud']);
		$wheresql = ""; $att = "";
		foreach($setcloud AS $item) {
			if ($item) {
				$wheresql.= $att."'".$item."'"; $att = ",";
			}
		}
		if ($wheresql) {
			db_query_simple("UPDATE ".table('functions')." SET `cloud`='1' WHERE (`title_en` IN (".$wheresql."))");
		} exit();
	}

    /**
	 * 修改功能状态
	 */
	public function S_status()
	{
		global $_GPC;
		$id = value($_GPC['param'], 2, 'int');
		$row = $this->ddb->getone("SELECT * FROM ".table('functions'), array('id'=>intval($id)));
		$arr = array();
		$arr['success'] = 0;
		$arr['status'] = ($row['status'] == '使用中')?'暂停中':'使用中';
		if ($this->ddb->update(table('functions'), array('status'=>$arr['status']), array('id'=>$row['id']))){
			$arr['success'] = 1;
			$arr['message'] = '修改成功';
		}
		echo json_encode($arr); exit();
	}

	/**
	 * 编辑功能
	 */
	public function S_editfunc()
	{
		global $_GPC;
		$id = value($_GPC['param'], 2, 'int');
		$row = $this->ddb->getone(table('functions'), array('id'=>intval($id)));
		if ($_GPC["dosubmit"]){
			$arr = array();
			$arr['success'] = 0;
			$arr['message'] = '';
			//
			$_GPC['edit_title'] = trim($_GPC['edit_title']);
			if (empty($_GPC['edit_title'])) {
				$arr['message'] = "功能名称不能为空！";
				echo json_encode($arr); exit();
			}
			if (strlen($_GPC['edit_title']) < 3) {
				$arr['message'] = "功能名称不能小于3个字符！";
				echo json_encode($arr); exit();
			}
			$uarr = array();
			$uarr['title'] = $_GPC['edit_title'];
			$uarr['ability'] = $_GPC['edit_ability'];
			$uarr['content'] = $_GPC['edit_content'];
			$uarr['point'] = max(-1, intval($_GPC['edit_point']));
			if (!db_update(table('functions'), $uarr, array('id'=>intval($id)))) {
				$arr['message'] = "保存失败！";
				echo json_encode($arr); exit();
			}
			if ($_GPC['edit_icon'] && file_exists(BASE_PATH.$_GPC['edit_icon'])) {
				@copy(BASE_PATH.$_GPC['edit_icon'], BASE_PATH.'addons/'.$row['title_en'].'/icon.png');
			}
			db_update(table('bindings'), array('module_name'=>$uarr['title']), array('module'=>$row['title_en']));
			$urow = db_getall("SELECT DISTINCT userid FROM ".table('users_functions'), array('fid'=>$row['id']));
			foreach($urow AS $item) {
				$this->user->handle_function($item['userid']);
			}
			$arr['success'] = 1;
			echo json_encode($arr); exit();
		}
		$this->load->helper('tpl');
		tpl('functions_edit', get_defined_vars());
	}

	/**
	 * 删除功能
	 */
	public function S_delfunc()
	{
		global $_GPC;
		$id = value($_GPC['param'], 2, 'int');
		$row = $this->ddb->getone("SELECT * FROM ".table('functions'), array('id'=>intval($id)));

		$arr = array();
		$arr['success'] = 0;
		//
		$rown = $this->ddb->getone("SELECT * FROM ".table('users_functions'), array('fid'=>intval($id)));
		if ($rown){
			$arr['message'] = "此功能有已开通的公众号无法删除！";
			echo json_encode($arr); exit();
		}
		//
		if ($this->ddb->query("DELETE FROM ".table('functions')." WHERE `id`=".$row['id']."")){
			$arr['success'] = 1;
			//刷新、删除数据
			$funlist = db_getall("SELECT * FROM ".table('users_functions'), array('fid'=>$row['id']));
			$uuarr = array();
			foreach($funlist as $val){
				$this->ddb->query("DELETE FROM ".table('users_functions')." WHERE `id`=".$val['id']."");
				if (!in_array($uuarr, $val['userid'])) {
					$uuarr[] = $val['userid'];
					$this->user->handle_function($val['userid']);
				}
			}
		}
		echo json_encode($arr); exit();
	}

	/**
	 * 公告管理
	 */
	public function S_information()
	{
		global $_GPC;
		$page = value($_GPC['param'], 2, 'int');
		$pageurl = urlencode($this->base->url[4]);
		tpl('setting_information', get_defined_vars());
	}

	/**
	 * 添加公告(修改公告)
	 */
	public function S_informationadd()
	{
		global $_GPC;
		$id = value($_GPC['param'], 2, 'int');
		$info = array();
		if ($id > 0){
			$info = $this->ddb->getone("SELECT * FROM ".table('information'), array('id'=>intval($id)));
			if (empty($info)) $id = 0;
		}else{
			$info['indate'] = SYS_TIME;
		}
		if ($this->input->post("dosubmit")){
			$fost = $this->input->post();
			$arr = $_arr = array();
			$arr['success'] = 0;
			//
			$_arr['title'] = $fost['title'];
			$_arr['indate'] = strtotime($fost['indate']);
			$_arr['inorder'] = intval($fost['inorder']);
			$_arr['view'] = intval($fost['view']);
			$_arr['content'] = $fost['content'];
			$_arr['inip'] = ONLINE_IP;
			//
			if (empty($_arr['title'])){
				$arr['message'] = '请填写标题';
				echo json_encode($arr); exit();
			}
			if ($id > 0){
				$res = $this->ddb->update(table('information'), $_arr, array('id'=>$id));
				if ($res){
					$arr['success'] = 1;
					$arr['message'] = '修改成功';
				}else{
					$arr['message'] = '修改失败';
				}

			}else{
				$res = $this->ddb->insert(table('information'), $_arr, true);
				if ($res){
					$arr['success'] = 1;
					$arr['message'] = '添加成功';
				}else{
					$arr['message'] = '添加失败';
				}
			}
			echo json_encode($arr); exit();
		}
		tpl('setting_informationadd', get_defined_vars());
	}

	/**
	 * 删除公告
	 */
	public function S_informationdel()
	{
		global $_GPC;
		$id = value($_GPC['param'], 2, 'int');
		$arr = array();
		$arr['success'] = 0;
		$info = $this->ddb->getone("SELECT * FROM ".table('information'), array('id'=>intval($id)));
		if ($info){
			$this->ddb->query("DELETE FROM ".table('information')." WHERE `id`=".$info['id']);
			$arr['success'] = 1;
			$arr['message'] = '删除成功！';
		}else{
			$arr['message'] = '要删除的对象已不存在！';
		}
		echo json_encode($arr); exit();
	}

	/**
	 * 常见问题
	 */
	public function S_question()
	{
		global $_GPC;
		$page = value($_GPC['param'], 2, 'int');
		$pageurl = urlencode($this->base->url[4]);
		tpl('setting_question', get_defined_vars());
	}

	/**
	 * 添加常见问题(修改常见问题)
	 */
	public function S_questionadd()
	{
		global $_GPC;
		$id = value($_GPC['param'], 2, 'int');
		$info = array();
		if ($id > 0){
			$info = $this->ddb->getone("SELECT * FROM ".table('question'), array('id'=>intval($id)));
			if (empty($info)) $id = 0;
		}else{
			$info['indate'] = SYS_TIME;
		}
		if ($this->input->post("dosubmit")){
			$fost = $this->input->post();
			$arr = $_arr = array();
			$arr['success'] = 0;
			//
			$_arr['title'] = $fost['title'];
			$_arr['indate'] = strtotime($fost['indate']);
			$_arr['inorder'] = intval($fost['inorder']);
			$_arr['content'] = $fost['content'];
			$_arr['inip'] = ONLINE_IP;
			//
			if (empty($_arr['title'])){
				$arr['message'] = '请填写标题';
				echo json_encode($arr); exit();
			}
			if ($id > 0){
				$res = $this->ddb->update(table('question'), $_arr, array('id'=>$id));
				if ($res){
					$arr['success'] = 1;
					$arr['message'] = '修改成功';
				}else{
					$arr['message'] = '修改失败';
				}

			}else{
				$res = $this->ddb->insert(table('question'), $_arr, true);
				if ($res){
					$arr['success'] = 1;
					$arr['message'] = '添加成功';
				}else{
					$arr['message'] = '添加失败';
				}
			}
			echo json_encode($arr); exit();
		}
		tpl('setting_questionadd', get_defined_vars());
	}

	/**
	 * 删除常见问题
	 */
	public function S_questiondel()
	{
		global $_GPC;
		$id = value($_GPC['param'], 2, 'int');
		$arr = array();
		$arr['success'] = 0;
		$info = $this->ddb->getone("SELECT * FROM ".table('question'), array('id'=>intval($id)));
		if ($info){
			$this->ddb->query("DELETE FROM ".table('question')." WHERE `id`=".$info['id']);
			$arr['success'] = 1;
			$arr['message'] = '删除成功！';
		}else{
			$arr['message'] = '要删除的对象已不存在！';
		}
		echo json_encode($arr); exit();
	}

	/**
	 * 添加功能
	 */
	public function doWebOpenfunc()
	{
		global $_GPC;
		$user = $this->user->getuser();
		$pageurl = urlencode($this->base->url[3]);
		//
        $wherearr = array('id'=>intval($_GPC['openalid']));
        if (!$user['admin']) {
            $wherearr['userid'] = $user['userid'];
        }
		$info = db_getone(table('users_al'), $wherearr);
		$infouser = ($user['userid']==$info['userid'])?$user:db_getone(table('users'), array('userid'=>$info['userid']));
		if ($this->input->post("dosubmit")){
			$arr = array();
			$arr['success'] = 0;
			//
			if (empty($info)) {
				$arr['message'] = '选择的接入不存在！';
				echo json_encode($arr); exit();
			}
			$func = db_getone(table('functions'), array('title_en'=>$_GPC['title_en']));
			if (empty($func)) {
				$arr['message'] = '选择的功能不存在！';
				echo json_encode($arr); exit();
			}
			if ($func['point'] < 0) {
				$arr['message'] = '选择的功能未开放！';
				echo json_encode($arr); exit();
			}
			$ufun = db_getone(table('users_functions'), array('userid'=>$infouser['userid'], 'fid'=>$func['id'], 'alid'=>$info['id']));
			if (!empty($ufun)) {
				$arr['message'] = '已经添加过此功能，无须重复添加！';
				echo json_encode($arr); exit();
			}
			if ($info['wx_level'] == 7) {
				$this->load->helper("cloud");
				$setting = string2array($func['setting']);
				$setting['category'] = cloud_category($func['title_en'], $func['version']);
				db_update(table('functions'), array('setting'=>array2string($setting)), array('id'=>$func['id']));
				if (!in_array('wxcorp', $setting['category'])) {
					$arr['message'] = "【".$func['title']."】暂不支持企业号！";
					echo json_encode($arr); exit();
				}
			}
			if ($infouser['point'] < $func['point']) {
				$arr['message'] = '当前剩余'.POINT_NAME.'不足，无法开通此功能！';
				echo json_encode($arr); exit();
			}
			if (!db_update(table('users'), array('point[-]'=>$func['point']), array('userid'=>$infouser['userid']))) {
				$arr['message'] = '系统繁忙请稍后再试！';
				echo json_encode($arr); exit();
			}
			db_insert(table('users_point'), array(
				'userid'=>$infouser['userid'],
				'change'=>$func['point']*-1,
				'point'=>$infouser['point'] - $func['point'],
				'pointtxt'=>'购买功能: '.$func['title'].' (接入ID'.$info['id'].')',
				'indate'=>SYS_TIME
			));
			$_arr = array();
			$_arr['userid'] = $infouser['userid'];
			$_arr['fid'] = $func['id'];
			$_arr['alid'] = $info['id'];
			$_arr['al_name'] = $info['al_name'];
			$_arr['wx_name'] = $info['wx_name'];
			$_arr['indate'] = SYS_TIME;
			$_arr['enddate'] = 0;
			if (db_insert(table('users_functions'), $_arr)){
				$arr['success'] = 1;
				$this->user->handle_function($infouser['userid']);
			}else{
				$arr['message'] = POINT_NAME.'已经扣除，但是系统繁忙没有添加功能成功，请联系网站客服！';
			}
			echo json_encode($arr); exit();
		}
		//
		if (empty($info)) {
			message(null, '选择的接入不存在！');
		}
		$function = string2array($info['function']);
		$functions = array();
		foreach($function AS $tmp) {
			$functions[] = $tmp['title_en'];
		}
		$wheresql = '`default`=0';
		tpl(get_defined_vars());
	}

    /**
     * 修改资料
     */
    public function doWebMe_edit()
    {
        $user = $this->user->getuser();
        if ($this->input->post("dosubmit")){
            $fost = $this->input->post();
            $arr = $_arr = array();
            $arr['success'] = 0;
            //
            $_arr['fullname'] = $fost['fullname'];
            $_arr['phone'] = $fost['phone'];
            $_arr['email'] = $fost['email'];
            $_arr['qqnum'] = $fost['qqnum'];
            $_arr['companyname'] = $fost['companyname'];
            $_arr['tel'] = $fost['tel'];
            $_arr['linkaddr'] = $fost['linkaddr'];
            $_arr['address'] = $fost['address'];
            //判断邮箱
            $wheresql = " WHERE `email`='".$_arr['email']."' AND `userid`!=".$user['userid'];
            $_count = $this->ddb->get_total("SELECT COUNT(*) AS num FROM ".table('users').$wheresql);
            if ($_count){
                $arr['message'] = '邮箱已存在';
                echo json_encode($arr); exit();
            }
            //判断手机号码
            $wheresql = " WHERE `phone`='".$_arr['phone']."' AND `userid`!=".$user['userid'];
            $_count = $this->ddb->get_total("SELECT COUNT(*) AS num FROM ".table('users').$wheresql);
            if ($_count){
                $arr['message'] = '手机号码已存在';
                echo json_encode($arr); exit();
            }
            //开始修改
            if ($this->ddb->update(table('users'), $_arr, array('userid'=>$user['userid']))){
                $this->ddb->update(table('users_al'), array('companyname'=>$_arr['companyname']), array('userid'=>$user['userid']));
                $arr['success'] = 1;
                $arr['message'] = '修改成功';
            }else{
                $arr['message'] = '修改失败';
            }
            echo json_encode($arr); exit();
        }
        tpl(get_defined_vars());
    }

	/**
	 * 我的积分
	 */
	public function doWebMe_point()
	{
		global $_A,$_GPC;
		$user = $this->user->getuser();
		if ($_GPC['param'][1] == 'recharge') {
			if ($_GPC['isok']) {
				$order = db_getone(table('pay_order'), array('status'=>1,'id'=>$_GPC['orderid']));
				$order['setting'] = string2array($order['setting']);
				tpl('me_point_recharge_ok', get_defined_vars());
			}elseif ($_GPC['dosubmit']) {
				if ($_GPC['amount'] <= 0 || $_GPC['amount']!=intval($_GPC['amount'])) {
					message("请输入正确的充值金额！");
				}
				$pay = db_getone(table('pay'), array('view'=>1, 'value'=>$_GPC['payment']));
				if (empty($pay)) {
					message("请选择正确的支付方式！");
				}
				//
				db_query("DELETE FROM ".table('pay_order')." WHERE `status`=0 AND `indate`<".(SYS_TIME-604800));
				$oarr = array('userid'=>$user['userid'],
					'amount'=>intval($_GPC['amount']),
					'int'=>intval($_GPC['amount'])*POINT_CONVERT,
					'status'=>0,
					'setting'=>array2string(array('type'=>$pay['title'])),
					'indate'=>SYS_TIME);
				$order['id'] = db_insert(table('pay_order'), $oarr, true);
				if (empty($order['id'])) {
					message("系统繁忙请稍后再试！");
				}
				$payid = date('Ymds',SYS_TIME).rand(10,99).$order['id'].$_A['u']['userid'];
				if (!db_update(table('pay_order'), array('payid'=>$payid), array("id"=>$order['id']))) {
					message("系统繁忙请稍后再试！-2");
				}
				//
				$payconfig = string2array($pay['content']);
				require_once("include/".$pay['value'].".php");
				$payment_form = get_code(array('id'=>$order['id'],
					'payid'=>$payid,
					'title'=>'积分充值',
					'remark'=>BASE_NAME.' - 积分充值',
					'return_url'=>$_A['url'][2].'me_point_respond_alipay', //同步通知
					'notify_url'=>$_A['url'][2].'me_point_respond_alipay/notify', //异步通知
					'amount'=>intval($_GPC['amount'])),
					$payconfig);
				if (empty($payment_form)) message("在线支付参数错误！");
				tpl('me_point_recharge_next', get_defined_vars());
			}else{
				$paylist = db_getall(table('pay'), array('view'=>1));
				if (count($paylist) < 1) {
					message("网站未开通".POINT_NAME."充值功能！");
				}
				tpl('me_point_recharge', get_defined_vars());
			}
		}else{
			$pageurl = urlencode($this->base->url[3]);
			$wheresql = '`userid`='.intval($user['userid']);
            $userid = $_GPC['userid'];
            $titname = "我的";
            if ($userid > 0 && $userid != $user['userid'] && $user['admin'] == 1) {
                $use = db_getone(table('users'), array('userid'=>intval($userid)));
                if ($use) {
                    $wheresql = '`userid`='.intval($use['userid']);
                    $titname = $use['username']."的";
                }
            }
            tpl(get_defined_vars());
		}
	}

	/**
	 * 支付宝返回
	 */
	public function doWebMe_point_respond_alipay()
	{
		global $_GPC;
		$pay = db_getone(table('pay'), array('view'=>1, 'value'=>'alipay'));
		$payment = string2array($pay['content']);

		require_once("include/alipay.php");
		$verify_result = get_respond($payment, $_GPC['param'][1]);
		if($verify_result) { //验证成功
			$dingdan           = $_GPC['out_trade_no'];		//获取订单号
			$total_fee         = $_GPC['total_fee'];		//获取总价格
			//$trade_status 	   = $_GPC['trade_status'];		//交易状态
			$order = db_getone(table('pay_order'), array('payid'=>$dingdan));
			if ($order) {
				if (empty($order['status'])) {
					$ouser = db_getone(table('users'), array('userid'=>$order['userid']));
					db_insert(table('users_point'), array(
						'userid'=>$order['userid'],
						'change'=>$order['int'],
						'point'=>$ouser['point']+$order['int'],
						'pointtxt'=>'充值：￥'.$total_fee,
						'indate'=>SYS_TIME
					));
					db_update(table('users'), array('point[+]'=>$order['int']), array('userid'=>$order['userid']));
					db_update(table('pay_order'), array('status'=>1), array('id'=>$order['id']));
				}
				if ($_GPC['param'][1]) {
					echo "success"; exit();
				}
				gourl($this->base->url[2].'me_point/recharge?isok=1&orderid='.$order['id']);
			}
			message(null, "订单不存在！", array('title'=>'点击返回我的积分','href'=>$this->base->url[2].'me_point'));
		} else {
			message(null, "充值完成！", array('title'=>'点击返回我的积分','href'=>$this->base->url[2].'me_point'));
		}
	}

    /**
     * 修改密码
     */
    public function doWebMe_pass()
    {
        $user = $this->user->getuser();
        if ($this->input->post("dosubmit")){
            $fost = $this->input->post();
            $arr = $_arr = array();
            $arr['success'] = 0;
            //
			if (md52($fost['olduserpass'], $user['encrypt']) != $user['userpass']) {
				$arr['message'] = '旧密码不正确';
				echo json_encode($arr); exit();
			}
			if (md52($fost['userpass'], $user['encrypt']) == $user['userpass']) {
				$arr['message'] = '新密码不能与旧密码相同';
				echo json_encode($arr); exit();
			}
            if (strlen($fost['userpass']) < 6){
                $arr['message'] = '密码不能小于6位数';
                echo json_encode($arr); exit();
            }
            //
            $_arr['encrypt'] = generate_password(6);
            $_arr['userpass'] = md52($fost['userpass'], $_arr['encrypt']);
            //开始修改
            if ($this->ddb->update(table('users'), $_arr, array('userid'=>$user['userid']))){
                $arr['success'] = 1;
                $arr['message'] = '修改成功';
				$this->session->unset_userdata('username');
            }else{
                $arr['message'] = '修改失败';
            }
            echo json_encode($arr); exit();
        }
        tpl(get_defined_vars());
    }

    /**
     * 添加、修改 服务窗
     */
    public function doWebAdd($parent = null)
    {
		global $_GPC,$_A;
        $this->load->helper('tpl');
        $_id = value($parent, 1, 'int');
        $user = $this->user->getuser();
        //
        $host = $_SERVER['SERVER_NAME'];
        $submit = '添加';
        $row = array();
		$_func = array();
        if ($_id > 0){
			$warr = array('userid'=>$user['userid'], 'id'=>$_id);
			if ($user['admin']) unset($warr['userid']);
            $row = $this->ddb->getone("SELECT * FROM ".table('users_al'), $warr);
            if ($row){
				if (in_array($row['wx_level'], array(7))) {
					gourl(weburl('system/addcorp/'.$_id));
				}
                $submit = '修改';
                $row['setting'] = string2array($row['setting']);
                $row['payment'] = string2array($row['payment']);
				$_func = string2array($row['function']);
				//
				$this->session->set_userdata('user:alid', $_id);
            }else{
                $_id = 0;
            }
        }
		if (empty($_func)) {
			$_func = db_getall("SELECT * FROM ".table('functions'), array('default'=>1));
		}
        $edit = $row;
        if ($this->input->post("dosubmit")){
            $fost = new_trim($this->input->post());
            $arr = $_arr = array();
            $arr['success'] = 0;
            $arr['id'] = $_id;
            //
            $_arr['al_name'] = $fost['al_name'];
            $_arr['al_appid'] = $fost['al_appid'];
            $_arr['al_gateway'] = $fost['al_gateway'];
            $_arr['al_rsa'] = $fost['al_rsa'];
            $_arr['al_key'] = $fost['al_key']?$fost['al_key']:'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDdJAQqGm0tHaMs0cgHl29N3gFv9aSsCcKFcK+edI4OQFl0iLt6U4In/st9XXJMQjN2Ltun6JsD3cHEx1iNmE26H2Z+C/AU6usaqnLQwmQnAhvik7XE/wkHAhcNRq55qCm6Xt48yrmE6hkO5NH2y6DQIIdiaYC5XhKNqWb7tezLJQIDAQAB';
            $_arr['al_qrcode'] = $fost['al_qrcode'];
            //
            $_arr['wx_name'] = $fost['wx_name'];
            $_arr['wx_appid'] = $fost['wx_appid'];
            $_arr['wx_secret'] = $fost['wx_secret'];
            $_arr['wx_token'] = $fost['wx_token'];
            $_arr['wx_aeskey'] = $fost['wx_aeskey'];
            $_arr['wx_level'] = $fost['wx_level'];
            $_arr['wx_suburl'] = $fost['wx_suburl'];
            $_arr['wx_qrcode'] = $fost['wx_qrcode'];
            //
            $_arr['linkaddr'] = $fost['linkaddr'];
            $_arr['payment'] = array2string($fost['payment']);
			$_arr['setting'] = $row['setting'];
			$_arr['setting']['other'] = $fost['other'];
            if ($_id > 0){
				$_arr['setting']['wx_token'] = '{}';
				$_arr['setting']['wx_jssdk'] = '{}';
				$_arr['setting'] = array2string($_arr['setting']);
                if ($this->ddb->update(table('users_al'), $_arr, array('id'=>$row['id']))){
					// 同步到微信
					if ($fost['wx_username']) {
						$this->account_weixin_interface($fost['wx_username'], array(
							'url' => $_A['url']['index'].'weixin/'.$row['id'].(strexists($_A['url']['index'],'?')?'?index':''),
							'token' => $_arr['wx_token'],
							'encodingaeskey' => $_arr['wx_aeskey']
						));
					}
                    $arr['success'] = 1;
                    $arr['message'] = '修改成功';
                    $this->ddb->update(table('users_functions'),
                        array('al_name'=>$_arr['al_name'],'wx_name'=>$_arr['wx_name']),array('alid'=>$row['id']));
                    $this->user->handle_function();
                }else{
                    $arr['message'] = '修改失败';
                }
                echo json_encode($arr); exit();
            }
            $_arr['userid'] = $user['userid'];
            $_arr['username'] = $user['username'];
            $_arr['companyname'] = $user['companyname'];
            $_arr['function'] = array2string(array());
            $_arr['indate'] = SYS_TIME;
            $_arr['inip'] = ONLINE_IP;
            //判断服务号appid
            if ($_arr['al_appid']) {
                $_count = $this->ddb->get_total("SELECT COUNT(*) AS num FROM ".table('users_al'), array('al_appid'=>$_arr['al_appid']));
                if ($_count){
                    $arr['message'] = '支付宝服务窗APPID已存在';
                    echo json_encode($arr); exit();
                }
            }
            //判断公众号appid
            if ($_arr['wx_appid']) {
                $_count = $this->ddb->get_total("SELECT COUNT(*) AS num FROM ".table('users_al'), array('wx_appid'=>$_arr['wx_appid']));
                if ($_count){
                    $arr['message'] = '微信公众号APPID已存在';
                    echo json_encode($arr); exit();
                }
            }
            //开始添加
			$_arr['setting'] = array2string($_arr['setting']);
            $alid = $this->ddb->insert(table('users_al'), $_arr, true);
            if ($alid){
                $arr['success'] = 1;
                $arr['message'] = '添加成功';
                $arr['id'] = $alid;
				// 同步到微信
				if ($fost['wx_username']) {
					$this->account_weixin_interface($fost['wx_username'], array(
							'url' => $_A['url']['index'].'weixin/'.$arr['id'].(strexists($_A['url']['index'],'?')?'?index':''),
							'token' => $_arr['wx_token'],
							'encodingaeskey' => $_arr['wx_aeskey']
						));
				}
                //
                $frow = db_getall("SELECT id FROM ".table('functions')." WHERE `default`=1 ORDER BY `inorder`");
                foreach ($frow AS $fitem) {
                    $farr = array();
                    $farr['userid'] = $user['userid'];
                    $farr['fid'] = $fitem['id'];
                    $farr['alid'] = $alid;
                    $farr['al_name'] = $_arr['al_name'];
                    $farr['wx_name'] = $_arr['wx_name'];
                    $farr['indate'] = SYS_TIME;
                    $farr['enddate'] = 0;
                    $this->ddb->insert(table('users_functions'), $farr);
                }
                $this->user->handle_function();
            }else{
                $arr['message'] = '添加失败';
            }
            echo json_encode($arr); exit();
        }
		//
		$lwhere = array('`wx_level`!='=>7);
		if (!$user['admin']) $lwhere['`wx_level`!='] = 7;
		$wxlist = db_getall(table("users_al"), $lwhere, 'id DESC');
		foreach($wxlist AS $k=>$v) {
			if ($_id == $v['id'] || empty($v['wx_name']) || empty($v['wx_appid'])) {
				unset($wxlist[$k]);
			}
		}
        tpl(get_defined_vars());
    }

	/**
	 * 添加、修改 企业号
	 */
	public function doWebAddcorp($parent = null)
	{
		global $_GPC,$_A;
		$this->load->helper('tpl');
		$_id = value($parent, 1, 'int');
		$user = $this->user->getuser();
		//
		$host = $_SERVER['SERVER_NAME'];
		$submit = '添加';
		$row = array();
		$_func = array();
		if ($_id > 0){
			$warr = array('userid'=>$user['userid'], 'id'=>$_id);
			if ($user['admin']) unset($warr['userid']);
			$row = $this->ddb->getone("SELECT * FROM ".table('users_al'), $warr);
			if ($row){
				if (!in_array($row['wx_level'], array(7))) {
					gourl(weburl('system/add/'.$_id));
				}
				$submit = '修改';
				$row['wx_corp'] = string2array($row['wx_corp']);
				$row['setting'] = string2array($row['setting']);
				$row['payment'] = string2array($row['payment']);
				$_func = string2array($row['function']);
			}else{
				$_id = 0;
			}
		}
		if ($_GPC['act'] == 'upapp') {
			if ($_id > 0){
				$this->load->library('wx');
				$this->wx->setting($row['id']);
				$this->wx->corp_upapp();
				echo "更新成功！";
			}else{
				echo "参数错误！";
			}
			exit();
		}
		if (empty($_func)) {
			$_func = db_getall("SELECT * FROM ".table('functions'), array('default'=>1));
		}
		$edit = $row;
		if ($this->input->post("dosubmit")) {
			$fost = new_trim($this->input->post());
			$arr = $_arr = array();
			$arr['success'] = 0;
			$arr['message'] = '';
			$arr['id'] = $_id;
			//
			$_arr['wx_name'] = $fost['wx_name'];
			$_arr['wx_appid'] = $fost['wx_appid'];
			$_arr['wx_secret'] = $fost['wx_secret'];
			$_arr['wx_qrcode'] = $fost['wx_qrcode'];
			$_arr['wx_token'] = $fost['wx_token']?$fost['wx_token']:generate_password(8);
			$_arr['wx_aeskey'] = $fost['wx_aeskey']?$fost['wx_aeskey']:generate_password(43);
			//
			$_arr['linkaddr'] = $fost['linkaddr'];
			$_arr['setting'] = $row['setting'];
			$_arr['setting']['other'] = $fost['other'];
			if ($_id > 0){
				$_arr['setting']['wx_token'] = '{}';
				$_arr['setting']['wx_jssdk'] = '{}';
				$_arr['setting'] = array2string($_arr['setting']);
				if ($this->ddb->update(table('users_al'), $_arr, array('id'=>$row['id']))){
					//
					$arr['success'] = 1;
					$arr['message'] = '修改成功';
				}else{
					$arr['message'] = '修改失败';
				}
				echo json_encode($arr); exit();
			}
			$_arr['userid'] = $user['userid'];
			$_arr['username'] = $user['username'];
			$_arr['companyname'] = $user['companyname'];
			$_arr['function'] = array2string(array());
			$_arr['indate'] = SYS_TIME;
			$_arr['inip'] = ONLINE_IP;
			//判断公众号appid
			$_count = $this->ddb->get_total("SELECT COUNT(*) AS num FROM ".table('users_al'), array('wx_appid'=>$_arr['wx_appid']));
			if ($_count){
				$arr['message'] = 'CorpID已存在';
				echo json_encode($arr); exit();
			}
			//判断 CorpID 、 CorpSecret
			$this->load->library('communication');
			$url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=".$_arr['wx_appid']."&corpsecret=".$_arr['wx_secret'];
			$content = $this->communication->ihttp_request($url);
			if($this->communication->is_error($content)) {
				$arr['message'] = '获取AccessToken失败, 请稍后重试！错误详情: ' . $content['message'];
				echo json_encode($arr); exit();
			}
			$token = @json_decode($content['content'], true);
			if(empty($token) || !is_array($token) || empty($token['access_token']) || empty($token['expires_in'])) {
				$errorinfo = substr($content['meta'], strpos($content['meta'], '{'));
				$errorinfo = @json_decode($errorinfo, true);
				$arr['message'] = '获取微信公众号授权失败, 请稍后重试！ 公众平台返回原始数据为: 错误代码-' . $errorinfo['errcode'] . '，错误信息-' . $errorinfo['errmsg'];
				echo json_encode($arr); exit();
			}
			$record = array();
			$record['token'] = $token['access_token'];
			$record['expire'] = SYS_TIME + $token['expires_in'] - 200;
			$_arr['setting']['wx_token'] = json_encode($record);
			//开始添加
			$_arr['wx_level'] = 7; 	// 7 企业号专用
			$_arr['setting'] = array2string($_arr['setting']);
			$alid = $this->ddb->insert(table('users_al'), $_arr, true);
			if ($alid){
				$arr['success'] = 1;
				$arr['message'] = '添加成功';
				$arr['id'] = $alid;
				//
				$frow = db_getall("SELECT id FROM ".table('functions')." WHERE `default`=1 ORDER BY `inorder`");
				foreach ($frow AS $fitem) {
					$farr = array();
					$farr['userid'] = $user['userid'];
					$farr['fid'] = $fitem['id'];
					$farr['alid'] = $alid;
					$farr['al_name'] = $_arr['al_name'];
					$farr['wx_name'] = $_arr['wx_name'];
					$farr['indate'] = SYS_TIME;
					$farr['enddate'] = 0;
					$this->ddb->insert(table('users_functions'), $farr);
				}
				$this->user->handle_function();
				$this->load->library('wx');
				$this->wx->setting($alid);
				$this->wx->corp_upapp();
			}else{
				$arr['message'] = '添加失败';
			}
			echo json_encode($arr); exit();
		}
		tpl(get_defined_vars());
	}

    /**
     * 删除 接入
     * @param null $parent
     */
    public function doWebDel($parent = null)
    {
		$this->user->getuser();
        $_id = value($parent, 1, 'int');
        $this->load->model('del');
        $this->del->delal($_id);
        exit();
    }

    /**
     * 判断服务窗名称是否存在
     */
    public function doWebAlname()
    {
        $success = 0;
        $_name = $this->input->get('alname');
        if (!empty($_name)) {
            $row = $this->ddb->getone("SELECT * FROM ".table('users_al'), array('al_name'=>$_name));
            if (empty($row)){
                $success = 1;
            }
        }
        echo $success; exit();
    }

	/**
	 * 上传 资源
	 * @param null $parent
	 */
	public function doWebUpfile($parent = null)
    {
        $user = $this->user->getuser();
        $iname = value($parent, 1, false, 'file_img'); 	//表单名
        $tname = value($parent, 2, false, 'images'); 	//类型：images/videos/voices
        $fname = value($parent, 3);						//文件命名
        $allowed = value($_GET, 'allowed'); 			//格式限制
        $size = intval(value($_GET, 'size')); 			//大小限制KB
        $userid = intval(value($_GET, 'userid')); 		//用户ID
		if (empty($userid)) $userid = $user['userid'];
        $arr = array();
        $tname = in_array($tname, array('images','audio','voices','videos'))?$tname:'images';
        $arr['upload_path'] = FCPATH."uploadfiles/users/".$userid."/".$tname."/".date("Y/m/");
		if ($tname == 'audio' || $tname == 'voices') {
			$arr['allowed_types'] = 'mp3|wma|wav|amr';
		}elseif ($tname == 'videos'){
			$arr['allowed_types'] = 'rm|rmvb|wmv|avi|mpg|mpeg|mp4';
		}else{
			$arr['allowed_types'] = 'gif|jpg|jpeg|png';
		}
		if ($allowed && $allowed != "undefined") {
			$arr['allowed_types'] = $allowed;
		}
        $arr['file_name'] = ($fname)?$fname:SYS_TIME.rand(10,99);
		if ($size > 0) {
			$arr['max_size'] = $size;
		}
        $this->load->model('vupload');
        $data = $this->vupload->upfile($arr, $iname);
        echo json_encode($data);
        exit();
    }

    /**
     * 删除文件
     */
    public function doWebDelup()
    {
        $user = $this->user->getuser();
        $_path = "uploadfiles/users/".$user['userid'];
		$_gath = str_replace("..", "", $this->input->get('path'));
        if (strpos($_gath, $_path) !== false) {
            if (file_exists(FCPATH.$_gath)) {
                @unlink(FCPATH.$_gath);
            }
			if (file_exists(FCPATH.$_gath.'_thumb.jpg')) {
				@unlink(FCPATH.$_gath.'_thumb.jpg');
			}
        }
    }

    /**
     * 获取文件列表
     * @param null $parent
     */
    public function doWebListup($parent = null)
    {
        $user = $this->user->getuser();
        $getp = value($parent, 1, false, 'images');
		if ($user['admin']) {
			$_path = "uploadfiles/users/";
		}else{
			$_path = "uploadfiles/users/".$user['userid']."/".$getp."/";
		}
        $_gath = $this->input->get('path');
        $_nowval = $this->input->post('nowval');
        if ($_gath) {
			$this->session->set_userdata('sys:'.$getp.':listup_path', $_gath);
			$_path.= str_replace('|','/',trim($_gath,'|'))."/";
        }elseif ($_nowval && preg_match('/^'.str_replace('/','\/',$_path).'/i', $_nowval)){
			//首次默认路径
			$nowpath = dirname($_nowval);
			$nownane = substr($_nowval, strlen($nowpath) + 1);
			$_gath = str_replace("/", "|", substr($nowpath, strlen($_path)-1));
			$_path.= substr($nowpath, strlen($_path)-1)."/";
		}elseif (!isset($_GET['path'])) {
			$_gath = $this->session->userdata('sys:'.$getp.':listup_path');
			$_path.= str_replace('|','/',trim($_gath,'|'))."/";
		}
		$_path = str_replace("//", "/", $_path);
        $folder = '';
        $path = array();
        foreach(explode('|', $_gath) as $v){
            $folder.= ($v)?'|'.$v:'';
            if ($v) $path[$v] = $folder;
        }
		$_path = str_replace("..", "", $_path);
        $list = glob(FCPATH.$_path . '*', GLOB_BRACE);
        $dir = $file= array();
        if ($path) {
            $dir['uponeleveldir']['title'] = '...';
            $dir['uponeleveldir']['url'] = '';
            $dir['uponeleveldir']['img'] = '';
            $dir['uponeleveldir']['class'] = 'folder';
            $dir['uponeleveldir']['type'] = '0';
            $dir['uponeleveldir']['f'] = substr($_gath,0,strripos(rtrim($_gath,'|'),'|')).'|';;
        }
		$this->load->model('vupload');
        foreach ($list as $v){
            $filename = basename($v);
            if (is_dir($v)) {
                $dir[$filename]['title'] = $filename;
                $dir[$filename]['url'] = $_path.$filename;
                $dir[$filename]['img'] = '';
                $dir[$filename]['class'] = 'folder';
                $dir[$filename]['type'] = '0';
                $dir[$filename]['f'] = $_gath.'|'.$filename;
            } elseif (substr($filename,-10) != "_thumb.jpg" && substr($filename,-9) != "_cart.jpg") {
                $file[$filename]['title'] = $filename;
                $file[$filename]['url'] = $_path.$filename;
                $file[$filename]['img'] = fillurl($_path.$filename);
				$file[$filename]['thumb'] = $file[$filename]['img'];
                $file[$filename]['class'] = '';
                $file[$filename]['type'] = '1';
                $file[$filename]['f'] = '';
				$extension = pathinfo(BASE_PATH.$_path.$filename, PATHINFO_EXTENSION);
				if (in_array($extension, array('mp3','wma','wav','amr'))) {
					$file[$filename]['thumb'] = fillurl('addons/system/template/images/file_music.png');
				}elseif (in_array($extension, array('rm','rmvb','wmv','avi','mpg','mpeg','mp4'))) {
					$file[$filename]['thumb'] = fillurl('addons/system/template/images/file_video.png');
				}elseif (in_array($extension, array('gif','jpg','jpeg','png','bmp'))) {
					if (!file_exists(BASE_PATH.$_path.$filename.'_thumb.jpg')) {
						$this->vupload->img2thumb(BASE_PATH.$_path.$filename, BASE_PATH.$_path.$filename.'_thumb.jpg', 100, 0);
					}
					if (file_exists(BASE_PATH.$_path.$filename.'_thumb.jpg')) {
						$file[$filename]['thumb'].= '_thumb.jpg';
					}
				}else{
					$file[$filename]['thumb'] = fillurl('addons/system/template/images/file_other.png');
				}
            }
        }
		if ($user['admin'] && preg_match('/^uploadfiles\/users\/(\d+)\/*$/i', $_path)) {
			foreach($dir AS $k=>$v) {
				if (!in_array($v['title'], array('...', $getp))) {
					unset($dir[$k]);
				}
			}
		}
		if (count($dir) > 1) {
			$inorder = array();
			foreach ($dir as $key => $val) {
				$inorder[$key] = ($val['title']=='...')?-999999:$val['title'];
			}
			array_multisort($inorder, SORT_ASC, $dir);
		}
        $listarr = array_merge($dir, $file);
        tpl(get_defined_vars());
    }


	/**
	 * 登录微信
	 */
	public function doWebWeixinlogin()
	{
		global $_GPC;
		$user = $this->user->getuser();
		$username = trim($_GPC['wxusername']);
		$password = md5(trim($_GPC['wxpassword']));
		$imgcode = trim($_GPC['verify']);
		$loginurl = WEIXIN_ROOT . '/cgi-bin/login?lang=zh_CN';
		$post = array(
			'username' => $username,
			'pwd' => $password,
			'imgcode' => $imgcode,
			'f' => 'json',
		);
		$arr = array();
		$arr['success'] = 0;
		$arr['message'] = '';
		$arr['username'] = $username;
		$arr['password'] = $password;
		$arr['errcode'] = 0;
		$arr['bindalias'] = '';
		$arr['ret'] = 0;
		$arr['islogin'] = false;
		//
		$data = array();
		if ($_GPC['validate_wx_tmpl'] == '1') {
			//二维码验证
			$response = ihttp_request(WEIXIN_ROOT.'/safe/safeuuid?timespam='.ceil(microtime(true)*1000).'&token=&lang=zh_CN',
				array(
					'token'=>'',
					'lang'=>'zh_CN',
					'f'=>'json',
					'ajax'=>'1',
					'random'=>'',
					'uuid'=>$this->session->userdata($username.'_uuid'),
					'action'=>'json',
					'type'=>'json'
				),
				array(
					'CURLOPT_COOKIE' => $this->session->userdata($username.'_cookie'),
					'CURLOPT_REFERER' => $this->session->userdata($username.'_redirect_url')
				)
			);
			if (is_error($response)) {
				$arr['message'] = '网络繁忙，请重试！';
				echo json_encode($arr); exit();
			}
			$data = json_decode($response['content'], true);
			$arr['errcode'] = $data['errcode'];
			if ($data['errcode'] == '401') {
				echo json_encode($arr); exit();
			}elseif ($data['errcode'] == '403') {
				$arr['success'] = 2;
				$arr['message'] = '<font color="red">【您已取消登录】请重新操作！</font>';
				echo json_encode($arr); exit();
			}elseif ($data['errcode'] == '404') {
				$arr['success'] = 2;
				$arr['message'] = '<font color="green">【扫描成功】请在微信上点击确认即可！</font>';
				echo json_encode($arr); exit();
			}elseif ($data['errcode'] == '405') {
				if ($_GPC['errcode'] == '405') {
					$arr['errcode'] = 4051;
					$response = ihttp_request(WEIXIN_ROOT.'/misc/safeassistant?1=1&token=&lang=zh_CN',
						array(
							'token'=>'',
							'lang'=>'zh_CN',
							'f'=>'json',
							'ajax'=>'1',
							'random'=>'',
							'action'=>'get_uuid',
							'uuid'=>$this->session->userdata($username.'_uuid'),
							'auth'=>'ticket'
						),
						array(
							'CURLOPT_COOKIE' => $this->session->userdata($username.'_cookie'),
							'CURLOPT_REFERER' => $this->session->userdata($username.'_redirect_url')
						)
					);
					if (is_error($response)) {
						$arr['success'] = 101;
						$arr['message'] = '一键获取信息失败-1';
						echo json_encode($arr); exit();
					}
					$data = json_decode($response['content'], true);
					if ($data['base_resp']['ret'] != '0') {
						$arr['success'] = 101;
						$arr['message'] = '一键获取信息失败-2';
						echo json_encode($arr); exit();
					}
					if (!in_array($data['isadmin'], array(1,2))) {
						$arr['success'] = 101;
						$arr['message'] = '请使用指定管理员进行验证';
						echo json_encode($arr); exit();
					}
					$response = ihttp_request(WEIXIN_ROOT.'/cgi-bin/securewxverify',
						array(
							'token'=>'',
							'lang'=>'zh_CN',
							'f'=>'json',
							'ajax'=>'1',
							'random'=>'',
							'action'=>'get_uuid',
							'code'=>$this->session->userdata($username.'_uuid'),
							'account'=>$username,
							'operation_seq'=>$this->session->userdata($username.'_operation_seq')
						),
						array(
							'CURLOPT_COOKIE' => $this->session->userdata($username.'_cookie'),
							'CURLOPT_REFERER' => $this->session->userdata($username.'_redirect_url')
						)
					);
					if (is_error($response)) {
						$arr['success'] = 101;
						$arr['message'] = '一键获取信息失败-3';
						echo json_encode($arr); exit();
					}
					$data = json_decode($response['content'], true);
					if ($data['base_resp']['ret'] != 0) {
						$arr['success'] = 101;
						$arr['message'] = '一键获取信息失败-4';
						echo json_encode($arr); exit();
					}
					preg_match('/token=([0-9]+)/', $data['redirect_url'], $match);
					$_GPC['wxl'][$username]['token'] = $match[1];
					$_GPC['wxl'][$username]['cookie'] = implode('; ', _array_merge(explode('; ', $this->session->userdata($username.'_cookie')), $response['headers']['Set-Cookie']));
					$this->session->set_userdata($username.'_token', $_GPC['wxl'][$username]['token']);
					$this->session->set_userdata($username.'_cookie', $_GPC['wxl'][$username]['cookie']);
					$arr['islogin'] = true;
				}else{
					$arr['success'] = 2;
					$arr['message'] = '<font color="green">【确认成功】请稍等片刻。。。</font>';
					echo json_encode($arr); exit();
				}
			}else{
				echo json_encode($arr); exit();
			}
		}
		//
		if ($arr['islogin'] !== true) {
			$code_cookie = $_COOKIE['weixin_code_cookie'];
			$response = ihttp_request($loginurl, $post, array('CURLOPT_REFERER' => 'https://mp.weixin.qq.com/', 'CURLOPT_COOKIE' => $code_cookie));
			if (is_error($response)) {
				$arr['message'] = '获取失败-1';
				echo json_encode($arr); exit();
			}
			$data = json_decode($response['content'], true);
		}
		if ($data['base_resp']['ret'] == 0) {
			if ($arr['islogin'] !== true) {
				preg_match('/token=([0-9]+)/', $data['redirect_url'], $match);
				$_GPC['wxl'][$username]['token'] = $match[1];
				$_GPC['wxl'][$username]['cookie'] = implode('; ', $response['headers']['Set-Cookie']);
				$this->session->set_userdata($username . '_token', $_GPC['wxl'][$username]['token']);
				$this->session->set_userdata($username . '_cookie', $_GPC['wxl'][$username]['cookie']);
			}
			//
			if (strexists($data['redirect_url'], 't=user/validate_wx_tmpl')) {
				preg_match('/bindalias=(.*?)&/', $data['redirect_url'], $match);
				$arr['bindalias'] = $match[1];
				//
				$arr['message'] = "公众号已开启登录保护，无法一键获取！";
				$response = $this->account_weixin_http($username, WEIXIN_ROOT . $data['redirect_url']);
				if (is_error($response)) {
					echo json_encode($arr); exit();
				}
				$response = $this->account_weixin_http($username, WEIXIN_ROOT . '/misc/safeassistant?1=1&token=&lang=zh_CN',
					array('token'=>'','lang'=>'zh_CN','f'=>'json','ajax'=>'1','random'=>'','action'=>'get_ticket','auth'=>'ticket'));
				if (is_error($response)) {
					echo json_encode($arr); exit();
				}
				$safeassistant = json_decode($response['content'], true);
				$response = $this->account_weixin_http($username, WEIXIN_ROOT . '/safe/safeqrconnect?1=1&token=&lang=zh_CN',
					array('token'=>'','lang'=>'zh_CN','f'=>'json','ajax'=>'1','random'=>'','appid'=>'wx3a432d2dbe2442ce','scope'=>'snsapi_contact','state'=>'0','redirect_uri'=>WEIXIN_ROOT,'login_type'=>'safe_center','type'=>'json','ticket'=>$safeassistant['ticket']));
				if (is_error($response)) {
					echo json_encode($arr); exit();
				}
				$safeqrconnect = json_decode($response['content'], true);
				if (empty($safeassistant['ticket']) || empty($safeassistant['operation_seq']) || empty($safeqrconnect['uuid'])) {
					echo json_encode($arr); exit();
				}
				//
				$arr['ret'] = -10007;
				$arr['message'] = WEIXIN_ROOT . '/safe/safeqrcode?ticket='.$safeassistant['ticket'].'&uuid='.$safeqrconnect['uuid'].'&action=check&type=login&auth=ticket&msgid='.$safeassistant['operation_seq'];
				$this->session->set_userdata($username.'_uuid', $safeqrconnect['uuid']);
				$this->session->set_userdata($username.'_operation_seq', $safeassistant['operation_seq']);
				$this->session->set_userdata($username.'_redirect_url', $arr['message']);
				echo json_encode($arr); exit();
			}
			//
			$response = $this->account_weixin_http($username, WEIXIN_ROOT . '/cgi-bin/settingpage?t=setting/index&action=index&lang=zh_CN');
			if (is_error($response)) {
				return array();
			}
			$info = array();
			preg_match('/fakeid=([0-9]+)/', $response['content'], $match);
			$fakeid = $match[1];
			$image = $this->account_weixin_http($username, WEIXIN_ROOT . '/misc/getheadimg?fakeid='.$fakeid);
			if (!is_error($image) && !empty($image['content'])) {
				$info['headimg'] = $image['content'];
			}
			$image = $this->account_weixin_http($username, WEIXIN_ROOT . '/misc/getqrcode?fakeid='.$fakeid.'&style=1&action=download');
			if (!is_error($image) && !empty($image['content'])) {
				$info['qrcode'] = $image['content'];
			}else{
				$info['qrcode'] = '';
			}
			preg_match('/(gh_[a-z0-9A-Z]+)/', $response['meta'], $match);
			$info['original'] = $match[1];
			preg_match('/名称([\s\S]+?)<\/li>/', $response['content'], $match);
			$info['name'] = trim(strip_tags($match[1]));
			preg_match('/微信号([\s\S]+?)<\/li>/', $response['content'], $match);
			$info['account'] = trim(strip_tags($match[1]));
			preg_match('/功能介绍([\s\S]+?)meta_content\">([\s\S]+?)<\/li>/', $response['content'], $match);
			$info['signature'] = trim(strip_tags($match[2]));
			preg_match('/认证情况([\s\S]+?)meta_content\">([\s\S]+?)<\/li>/', $response['content'], $match);
			$info['level_tmp'] = trim(strip_tags($match[2]));

			$info['level'] = 1;
			$is_key_secret = 1;
			if(strexists($response['content'], '订阅号')) {
				if(strexists($info['level_tmp'], '微信认证')) {
					$info['level'] = 3;
				}
			} elseif(strexists($response['content'], '服务号')) {
				$info['level'] = 2;
				if(strexists($info['level_tmp'], '微信认证')) {
					$info['level'] = 4;
				}
			}
			$info['token'] = generate_password(6);
			$info['EncodingAESKey'] = generate_password(43);
			if ($is_key_secret == 1) {
				$authcontent = $this->account_weixin_http($username, WEIXIN_ROOT . '/advanced/advanced?action=dev&t=advanced/dev&lang=zh_CN');
				preg_match_all("/value\:\"(.*?)\"/", $authcontent['content'], $match);
				$info['key'] = $match[1][2];
				$info['secret'] = $match[1][3];
				preg_match("/\"wx_alias\"\:\"(.*?)\"/", $authcontent['content'], $match);
				$info['wx_alias'] = trim(strip_tags($match[1]));
				unset($match);
				if (empty($info['secret'])) {
					preg_match_all("/encode_app_key\: \"(.*?)\"/", $authcontent['content'], $match);
					$info['secret'] = $match[1][0];
				}
			}
			preg_match_all("/(?:country|province|city): '(.*?)'/", $response['content'], $match);
			$info['country'] = trim($match[1][0]);
			$info['province'] = trim($match[1][1]);
			$info['city'] = trim($match[1][2]);
			$info['qrcode_img'] = '';
			if ($info['qrcode']) {
				$_path = "uploadfiles/users/".$user['userid']."/images/".date("Y/m/");
				make_dir(FCPATH.$_path);
				$_path.= $info['original']?$info['original']:SYS_TIME;
				$_path.= '.jpg';
				file_put_contents(FCPATH.$_path, $info['qrcode']);
				$info['qrcode_img'] = $_path;
				$info['qrcode_imgurl'] = fillurl($_path);
			}
			unset($info['qrcode']);
			unset($info['headimg']);
			//
			$arr['success'] = 1;
			$arr['message'] = $info;
		}else{
			$arr['ret'] = $data['ErrCode'];
			switch ($data['ErrCode']) {
				case "-1":
					$msg = "系统错误，请稍候再试。";
					break;
				case "-2":
					$msg = "微信公众帐号或密码错误。";
					break;
				case "-3":
					$msg = "微信公众帐号密码错误，请重新输入。";
					break;
				case "-4":
					$msg = "不存在该微信公众帐户。";
					break;
				case "-5":
					$msg = "您的微信公众号目前处于访问受限状态。";
					break;
				case "-6":
					$msg = "登录受限制，需要输入验证码，稍后再试！";
					break;
				case "-7":
					$msg = "此微信公众号已绑定私人微信号，不可用于公众平台登录。";
					break;
				case "-8":
					$msg = "微信公众帐号登录邮箱已存在。";
					break;
				case "-200":
					$msg = "因您的微信公众号频繁提交虚假资料，该帐号被拒绝登录。";
					break;
				case "-94":
					$msg = "请使用微信公众帐号邮箱登陆。";
					break;
				case "10":
					$msg = "该公众会议号已经过期，无法再登录使用。";
					break;
				case "-23":
					$msg = "输入密码错误。";
					break;
				default:
					$arr['ret'] = $data['base_resp']['ret'].'';
					$msg = "未知的返回：".$data['base_resp']['ret'];
			}
			$arr['message'] = $msg;
		}
		echo json_encode($arr); exit();
	}
	
	public function doWebWeixinsecret() {
		global $_GPC;
		$this->user->getuser();
		//
		$arr = array();
		$arr['success'] = 0;
		$arr['message'] = '';
		//
		$username = $_GPC['username'];
		$_GPC['wxl'][$username]['token'] = $this->session->userdata($username.'_token');
		$_GPC['wxl'][$username]['cookie'] = $this->session->userdata($username.'_cookie');
		//
		if ($_GPC['secret_wx_tmpl'] == '1') {
			//二维码验证
			$response = $this->account_weixin_http($username, WEIXIN_ROOT.'/safe/safeuuid?timespam='.ceil(microtime(true)*1000).'&lang=zh_CN',
				array(
					'token'=>$_GPC['wxl'][$username]['token'],
					'lang'=>'zh_CN',
					'f'=>'json',
					'ajax'=>'1',
					'random'=>'',
					'uuid'=>$this->session->userdata($username.'_uuid'),
					'action'=>'json',
					'type'=>'json'
				)
			);
			if (is_error($response)) {
				$arr['message'] = '网络繁忙，请重试！';
				echo json_encode($arr); exit();
			}
			$data = json_decode($response['content'], true);
			$arr['errcode'] = $data['errcode'];
			if ($data['errcode'] == '401') {
				echo json_encode($arr); exit();
			}elseif ($data['errcode'] == '403') {
				$arr['success'] = 2;
				$arr['message'] = '<font color="red">【您已取消登录】请重新操作！</font>';
				echo json_encode($arr); exit();
			}elseif ($data['errcode'] == '404') {
				$arr['success'] = 2;
				$arr['message'] = '<font color="green">【扫描成功】请在微信上点击确认即可！</font>';
				echo json_encode($arr); exit();
			}elseif ($data['errcode'] == '405') {
				if ($_GPC['errcode'] == '405') {
					$arr['errcode'] = 4051;
					$response = $this->account_weixin_http($username, WEIXIN_ROOT.'/misc/safeassistant?1=1&lang=zh_CN',
						array(
							'token'=>$_GPC['wxl'][$username]['token'],
							'lang'=>'zh_CN',
							'f'=>'json',
							'ajax'=>'1',
							'random'=>'',
							'action'=>'get_uuid',
							'uuid'=>$this->session->userdata($username.'_uuid')
						)
					);
					if (is_error($response)) {
						$arr['success'] = 101;
						$arr['message'] = '一键获取信息失败-1';
						echo json_encode($arr); exit();
					}
					$data = json_decode($response['content'], true);
					if ($data['base_resp']['ret'] != '0') {
						$arr['success'] = 101;
						$arr['message'] = '一键获取信息失败-2';
						echo json_encode($arr); exit();
					}
					if (!in_array($data['isadmin'], array(1,2))) {
						$arr['success'] = 101;
						$arr['message'] = '请使用指定管理员进行验证';
						echo json_encode($arr); exit();
					}
					$arr['success'] = 1;
				}else{
					$arr['success'] = 2;
					$arr['message'] = '<font color="green">【确认成功】请稍等片刻。。。</font>';
				}
				echo json_encode($arr); exit();
			}
			echo json_encode($arr); exit();
		}elseif ($_GPC['secret_wx_tmpl'] == '2') {
			$response = $this->account_weixin_http($username, WEIXIN_ROOT.'/advanced/advanced?action=view_appsecret',
				array(
					'token'=>$_GPC['wxl'][$username]['token'],
					'lang'=>'zh_CN',
					'f'=>'json',
					'ajax'=>'1',
					'random'=>'',
					'pwd'=>$_GPC['password'],
					'imgcode'=>$_GPC['imgcode']
				)
			);
			if (is_error($response)) {
				$arr['message'] = '网络繁忙，请重试！';
				echo json_encode($arr); exit();
			}
			$data = json_decode($response['content'], true);
			if ($data['base_resp']['ret'] != '0') {
				$arr['message'] = $data['base_resp']['err_msg'];
				echo json_encode($arr); exit();
			}
			$arr['success'] = 1;
			$arr['message'] = $data['app_key'];
			echo json_encode($arr); exit();
		}
		$response = $this->account_weixin_http($username, WEIXIN_ROOT . '/misc/safeassistant?1=1&lang=zh_CN', array(
			'token'=>$_GPC['wxl'][$username]['token'],
			'lang'=>'zh_CN',
			'f'=>'json',
			'ajax'=>'1',
			'random'=>'',
			'action'=>'get_ticket',
		));
		$safeassistant = json_decode($response['content'], true);
		if (is_error($response)) {
			echo json_encode($arr); exit();
		}
		$response = $this->account_weixin_http($username, WEIXIN_ROOT . '/safe/safeqrconnect?1=1&token=&lang=zh_CN',
			array(
				'token'=>$_GPC['wxl'][$username]['token'],
				'lang'=>'zh_CN',
				'f'=>'json',
				'ajax'=>'1',
				'random'=>'',
				'appid'=>'wx3a432d2dbe2442ce',
				'scope'=>'snsapi_contact',
				'state'=>'0',
				'redirect_uri'=>WEIXIN_ROOT,
				'login_type'=>'safe_center',
				'type'=>'json',
				'ticket'=>$safeassistant['ticket']
			));
		if (is_error($response)) {
			echo json_encode($arr); exit();
		}
		$safeqrconnect = json_decode($response['content'], true);
		if (empty($safeassistant['ticket']) || empty($safeassistant['operation_seq']) || empty($safeqrconnect['uuid'])) {
			echo json_encode($arr); exit();
		}
		$arr['success'] = 1;
		$arr['message'] = WEIXIN_ROOT.'/safe/safeqrcode?ticket='.$safeassistant['ticket'].'&uuid='.$safeqrconnect['uuid'].'&action=check&type=showas&msgid='.$safeassistant['operation_seq'];
		$this->session->set_userdata($username.'_uuid', $safeqrconnect['uuid']);
		$this->session->set_userdata($username.'_operation_seq', $safeassistant['operation_seq']);
		echo json_encode($arr); exit();
	}

	public function account_weixin_http($username, $url, $post = '') {
		global $_GPC;
		$token = $_GPC['wxl'][$username]['token'];
		$cookie = $_GPC['wxl'][$username]['cookie'];
		return ihttp_request($url . '&token=' . $token, $post, array('CURLOPT_COOKIE' => $cookie, 'CURLOPT_REFERER' => WEIXIN_ROOT . '/advanced/advanced?action=edit&t=advanced/edit&token='.$token));
	}

	/**
	 * 微信登陆验证码
	 */
	public function doWebWxcode()
	{
		global $_GPC;
		$username = trim($_GPC['username']);
		if ($username) {
			$_url = "https://mp.weixin.qq.com/cgi-bin/verifycode?username={$username}&r=" . SYS_TIME;
		}else{
			$_url = "https://mp.weixin.qq.com/cgi-bin/verifycode?r=" . SYS_TIME;
		}
		$response = ihttp_get($_url);
		if(!is_error($response)) {
			setcookie('weixin_code_cookie', $response['headers']['Set-Cookie'], 0, BASE_DIR);
			header('Content-type: image/jpg');
			echo $response['content'];
			exit();
		}
	}

	/**
	 * 获取微信 jssdk ?al=会员id
	 */
	public function doWebWxjssdk()
	{
		global $_GPC;
		if ($_GPC['al'] > 0) {
			$this->load->library('wx');
			$this->wx->setting(intval($_GPC['al']));
			echo json_encode($this->wx->jssdkConfig(value($_GPC, 'url')));
		}
		exit();
	}

	public function doWebRequire_share()
	{
		global $_A,$_GPC;
		if ($_GPC['al'] > 0) {
			$user_agent = $_SERVER['HTTP_USER_AGENT'];
			if (strpos($user_agent, 'AlipayClient') !== false) {
				//
				$_A['browser'] = 'alipay';
			}elseif (strpos($user_agent, 'MicroMessenger') !== false) {
				$this->load->library('wx');
				$this->wx->setting(intval($_GPC['al']));
				$_A['wx_jssdkConfig'] = $this->wx->jssdkConfig(value($_GPC, 'url'), $_GPC['module']);
				$_A['browser'] = 'weixin';
			}else{
				exit();
			}
			tpl(get_defined_vars());
		}
		exit();
	}

	public function doWebRequire_photo()
	{
		global $_A,$_GPC;
		if ($_GPC['al'] > 0) {
			$_A['openid'] = $this->webgetappopenid($_GPC['al']);
			$_A['openid'] = $this->webgetappopenid($_GPC['al']);
			//
			$user_agent = $_SERVER['HTTP_USER_AGENT'];
			if ($_GPC['act'] == 'elupload') {
				$arr = array();
				$arr['success'] = 1;
				$arr['message'] = array();
				//
				$uarr = array();
				$uarr['upload_path'] = BASE_PATH.'uploadfiles/users/'.value($_A,'userid','int').'/images/_weixin/'.date('Ym/', SYS_TIME);
				$uarr['allowed_types'] = 'gif|jpg|jpeg|png';
				$uarr['file_name'] = 'el_'.SYS_TIME.rand(10,99);
				$uarr['thumb'] = true;
				$this->load->model('vupload');
				$data = $this->vupload->upfile($uarr, '__photo-upimg-input_file');
				if ($data['success']) {
					if ($_GPC['width'] || $_GPC['height']) {
						$this->vupload->img2thumb($data['upload_data']['full_path'], $data['upload_data']['full_path'], intval($_GPC['width']), intval($_GPC['height']));
					}
					$arr['message'][] = $data['upload_data']['full_path_site'];
					db_insert(table('vip_uploadfiles'), array(
						'alid'=>intval($_GPC['al']),
						'openid'=>$_A['openid'],
						'path'=>$data['upload_data']['full_path_site'],
						'indate'=>SYS_TIME
					));
				}else{
					$arr['success'] = 0;
					$arr['message'] = $data['message'];
				}
				echo json_encode($arr); exit();
			}
			if ($_GPC['act'] == 'viupload') {
				$arr = array();
				$arr['success'] = 0;
				$arr['nextpage'] = 0;
				$arr['message'] = array();
				//
				$page = max(1, $_GPC['page']);
				$lists = db_getlist(table('vip_uploadfiles'),
					array('alid'=>intval($_GPC['al']), 'openid'=>$_A['openid']), '`indate` DESC', 20, $page);
				if ($lists['totalpage'] > $lists['nowpage']) {
					$arr['nextpage'] = 1;
				}
				$list = $lists['list'];
				if ($list) {
					$arr['success'] = 1;
					foreach ($list AS $item) {
						$arr['message'][] = array(
							'ymd' => date("Y-m-d", $item['indate']),
							'path' => array($item['path']),
							'fill' => fillurl($item['path']),
							'thumb' => fillurl($item['path']."_thumb.jpg")
						);
					}
				}
				echo json_encode($arr); exit();
			}
			if (strpos($user_agent, 'AlipayClient') !== false) {
				//
				$_A['browser'] = 'alipay';
				if ($_GPC['act'] == 'alupload') {
					$arr = array();
					$arr['success'] = 1;
					$arr['message'] = array();
					//
					$photo = $_GPC['photo'];
					$_file = 'uploadfiles/users/'.value($_A,'userid','int').'/images/_alipay/'.date('Ym/', SYS_TIME);
					$nan = md5($photo).'.png';
					$_dir = BASE_PATH.$_file;
					make_dir($_dir);
					$fp2 = @fopen($_dir.$nan,'a');
					fwrite($fp2, base64_decode($photo));
					fclose($fp2);
					if ($_GPC['width'] || $_GPC['height']) {
						$this->load->model('vupload');
						$this->vupload->img2thumb($_dir.$nan, $_dir.$nan, intval($_GPC['width']), intval($_GPC['height']));
					}
					$arr['message'][] = $_file.$nan;
					echo json_encode($arr); exit();
				}
			}elseif (strpos($user_agent, 'MicroMessenger') !== false) {
				$this->load->library('wx');
				$this->wx->setting(intval($_GPC['al']));
				$_A['wx_jssdkConfig'] = $this->wx->jssdkConfig(value($_GPC, 'url'));
				$_A['browser'] = 'weixin';
				if ($_GPC['act'] == 'wxupload') {
					$arr = array();
					$arr['success'] = 1;
					$arr['message'] = array();
					//
					$photo = @json_decode($_GPC['photo'], true);
					if ($photo) {
						$this->load->helper('communication');
						foreach ($photo AS $media_id) {
							if ($this->wx->iscorp()) {
								$url = "https://qyapi.weixin.qq.com/cgi-bin/media/get?access_token=".$this->wx->token()."&media_id=".$media_id;
							}else{
								$url = "http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=".$this->wx->token()."&media_id=".$media_id;
							}
							$_html = ihttp_request($url);
							if(!is_error($_html)) {
								$rethtml = @json_decode($_html['content'], true);
								if (isset($rethtml['errmsg']) && in_array($rethtml['errmsg'], array('40001','42001'))) {
									$this->wx->error_code($rethtml['errcode']);
									$_html = ihttp_request($url);
									if(!is_error($_html)) {
										$rethtml = @json_decode($_html['content'], true);
									}
								}
								if (!isset($rethtml['errmsg'])) {
									$_file = 'uploadfiles/users/'.value($_A,'userid','int').'/images/_weixin/'.date('Ym/', SYS_TIME);
									$nan = $media_id.'.jpg';
									$_dir = BASE_PATH.$_file;
									make_dir($_dir);
									$fp2 = @fopen($_dir.$nan,'a');
									fwrite($fp2, $_html['content']);
									fclose($fp2);
									if ($_GPC['width'] || $_GPC['height']) {
										$this->load->model('vupload');
										$this->vupload->img2thumb($_dir.$nan, $_dir.$nan, intval($_GPC['width']), intval($_GPC['height']));
									}
									$arr['message'][] = $_file.$nan;
								}
							}
						}
					}
					echo json_encode($arr); exit();
				}
			}
			tpl(get_defined_vars());
		}
		exit();
	}

	public function doWebUtility()
	{
		global $_GPC;
		$callback = $_GPC['param'][2];
		tpl('utility/'.$_GPC['param'][1], get_defined_vars());
	}

	/** ************************************************************************************************ */
	/** ************************************************************************************************ */
	/** ************************************************************************************************ */

	public function doMobileWeixinauth()
	{
		global $_A,$_GPC;
		$ts = substr($_GPC['param'][1], 1);
		$tv = substr($_GPC['param'][2], 1);
		//
		$notes = db_getone(table('tmp'), array('title'=>'system_wxac_'.$ts));
		if (empty($ts) || $notes['value'] != $tv) {
			message(null, "二维码超时，请重新访问！");
		}
		$tt = SYS_TIME - $notes['indate'];
		if ($tt > 180 || $tt < -180) {
			message(null, "二维码超时，请重新访问！");
		}
		$alid = intval($_GPC['al']);
		if (empty($alid)) {
			message(null, "参数错误，请重新访问！");
		}
		if ($_A['browser'] != 'weixin') {
			message(null, "请使用微信扫描登录！");
		}
		$this->load->library('wx');
		$this->wx->setting($alid);
		//
		$_temp_isget = false;
		$get_appid = value($_A, 'al|setting|other|get_appid');
		if ($get_appid) {
			$get_appoint = value($_A, 'al|setting|other|get_appoint', true);
			if (empty($get_appoint) || in_array($_A['module'], $get_appoint)) {
				$_temp_isget = true; //微信授权 - 借用
				//
				$_A['wxget']['appid'] = trim($get_appid);
				$_A['wxget']['secret'] = trim($_A['al']['setting']['other']['get_secret']);
				$_A['wxget']['scope'] = trim($_A['al']['setting']['other']['get_scope']);
				$_A['wxget']['md5'] = substr(md5($_A['wxget']['appid'].$_A['wxget']['secret']), 8, 16);
			}
		}
		//
		if (($_A['al']['wx_appid'] && $_A['al']['wx_level'] == 4) || $_temp_isget) {
			if (isset($_GPC['weixin_system_oauth2'])) {
				if (!isset($_GPC['code'])) {
					message(null, "OAuth 2.0授权登录失败！");
				}
				if ($_temp_isget) {
					$url  = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$_A['wxget']['appid'];
					$url.= '&secret='.$_A['wxget']['secret'].'&code='.$_GPC['code'].'&grant_type=authorization_code';
				}else{
					$url  = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$_A['al']['wx_appid'];
					$url.= '&secret='.$_A['al']['wx_secret'].'&code='.$_GPC['code'].'&grant_type=authorization_code';
				}
				$content = $this->communication->ihttp_request($url);
				$_content = isset($content['content'])?json_decode($content['content'], true):'';
				$_A['openid'] = value($_content,'openid');
				if (empty($_A['openid'])) {
					message(null, "OAuth 2.0授权登录失败 - wx - openid！");
				}
				if (!$_temp_isget || $_A['wxget']['scope'] != 'snsapi_base') {
					$access_token = value($_content,'access_token');
					$url  = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$_A['openid'].'&lang=zh_CN';
					$content = $this->communication->ihttp_request($url);
					$_content = isset($content['content'])?json_decode($content['content'], true):'';
					$_A['openid'] = value($_content,'openid');
					if (empty($_A['openid'])) {
						message(null, "OAuth 2.0授权登录失败 - wx - userinfo！");
					}
				}
				$M = array(
					'openid'=>$_A['openid'],
					'alid'=>$_A['al']['id'],
					'appid'=>$_A['al']['wx_appid']
				);
				if ($_temp_isget) {
					$M['follow'] = 3;
				}
				$M['user_name'] = value($_content, 'nickname');
				$M['sex'] = str_replace(array("1","2"), array("男","女"),value($_content, 'sex'));
				$M['city'] = value($_content, 'city');
				$M['province'] = value($_content, 'province');
				$M['avatar'] = value($_content, 'headimgurl');
				$_A['_oauthweixin'] = 1; //微信oaut授权
				//
				$this->wx->exist_group($M);
				$this->wx->processor();
				//
				$openidname = '__SYS:USERID:'.intval($_A['al']['id']);
				$this->session->set_userdata($openidname, $_A['openid']);
				set_cookie($openidname, $_A['openid'], 86400);
				//
				$this->weixinauthtmp($ts, 2, $_A['openid']);
				message(null, "授权登录成功！", $notes['content']?$notes['content']:'');
			}else{
				$this->weixinauthtmp($ts, 1);
				if ($_temp_isget) {
					$_url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$_A['wxget']['appid'];
					$_url.= '&redirect_uri='.urlencode(get_link('weixin_system_oauth2|weixin_oauth2|code')."&weixin_system_oauth2=1");
					if ($_A['wxget']['scope'] != 'snsapi_base') {
						$_url.= '&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect';
					}else{
						$_url.= '&response_type=code&scope=snsapi_base&state=STATE#wechat_redirect';
					}
					gourl($_url);
				}else{
					$_url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$_A['al']['wx_appid'];
					$_url.= '&redirect_uri='.urlencode(get_link('weixin_system_oauth2|weixin_oauth2|code')."&weixin_system_oauth2=1");
					$_url.= '&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect';
					gourl($_url);
				}
			}
		}else{
			message(null, "系统未开放此登录接口！");
		}

	}

	public function doMobileWeixinauthq()
	{
		global $_GPC;
		$wxcodename = '__SYS:WXCODE:'.intval($_GPC['al']);
		$val = $this->session->userdata($wxcodename);
		if (empty($val)) { $val = get_cookie($wxcodename); }
		$notes = db_getone(table('tmp'), array('title'=>'system_wxac_q'.$val));
		if ($notes) {
			if ($notes['value'] == '2') {
				$openidname = '__SYS:USERID:'.intval($_GPC['al']);
				$this->session->set_userdata($openidname, $notes['content']);
				set_cookie($openidname, $notes['content'], 86400);
			}
			echo $notes['value'];
		}else{
			echo '0';
		}
		exit();
	}

	public function doMobileShowmedia()
	{
		global $_GPC;
		$value = $_GPC['value'];
		$stlen = strlen(BASE_PATH);
		if (substr($value, 0, $stlen) == BASE_PATH) {
			$value = substr($value, $stlen);
		}
		if ($_GPC['type'] == 'image') {
			$html = '<img src="'.fillurl($value).'" style="max-width:100%;display:block;margin:0 auto;">';
			message("查看图片", $html);
		}elseif ($_GPC['type'] == 'voice') {
			$html = '<audio src="'.fillurl($value).'" controls="controls" style="width:100%;">您的浏览器不支持 audio 标签。</audio>';
			message("查看语音", $html);
		}elseif ($_GPC['type'] == 'video') {
			$html = '<video src="'.fillurl($value).'" controls="controls" style="width:100%;">您的浏览器不支持 video 标签。</video>';
			message("查看视频", $html);
		}else{
			message(null, "参数错误！");
		}
	}

	/** ************************************************************************************************ */
	/** ************************************************************************************************ */
	/** ************************************************************************************************ */

	private function weixinauthtmp($ts, $v, $openid = '') {
		$notes = db_getone(table('tmp'), array('title'=>'system_wxac_q'.$ts));
		if (empty($notes)) {
			db_insert(table('tmp'), array('title'=>'system_wxac_q'.$ts, 'value'=>$v, 'content'=>$openid));
		}else{
			db_update(table('tmp'), array('value'=>$v, 'content'=>$openid), array('title'=>'system_wxac_q'.$ts));
		}
	}

	private function account_weixin_interface($username, $account, $num = 0) {
		global $_GPC;
		$_GPC['wxl'][$username]['token'] = $this->session->userdata($username.'_token');
		$_GPC['wxl'][$username]['cookie'] = $this->session->userdata($username.'_cookie');
		if ($_GPC['wxl'][$username]['token'] && $_GPC['wxl'][$username]['cookie']) {
			$operation_seq = $this->session->userdata($username.'_operation_seq');
			$response = $this->account_weixin_http($username, WEIXIN_ROOT.'/advanced/callbackprofile?t=ajax-response&lang=zh_CN',
				array(
					'url' => $account['url'],
					'callback_token' => $account['token'],
					'encoding_aeskey' => $account['encodingaeskey'],
					'callback_encrypt_mode' => '0',
					'operation_seq' => $operation_seq?$operation_seq:'405920941',
					'action' => 'set_callback',
					'type' => 'check_domain',
					'step' => '1',
				));
			if (!is_error($response)) {
				$response = json_decode($response['content'], true);
			}
			$response = $this->account_weixin_http($username, WEIXIN_ROOT.'/advanced/callbackprofile?t=ajax-response&lang=zh_CN',
				array(
					'url' => $account['url'],
					'callback_token' => $account['token'],
					'encoding_aeskey' => $account['encodingaeskey'],
					'callback_encrypt_mode' => '0',
					'operation_seq' => $operation_seq?$operation_seq:'405920941',
				));
			if (is_error($response)) {
				return $response;
			}
			$response = json_decode($response['content'], true);
			if (!empty($response['base_resp']['ret'])) {
				if ($response['base_resp']['ret'] == '-302' && $num < 3) {
					$num++;
					return $this->account_weixin_interface($username, $account, $num);
				}
				return error($response['base_resp']['ret'], $response['base_resp']['err_msg']);
			}
			$response = $this->account_weixin_http($username, WEIXIN_ROOT . '/misc/skeyform?form=advancedswitchform',
				array('f' => 'json', 'lang' => 'zh_CN', 'flag' => '1', 'type' => '2', 'ajax' => '1', 'random' => $this->random(5, 1)));
			if (is_error($response)) {
				//return $response;
			}
			//网页授权接口
			$this->account_weixin_http($username, WEIXIN_ROOT . '/merchant/myservice?action=set_oauth_domain&f=json',
				array('f' => 'json', 'lang' => 'zh_CN', 'domain' => $_SERVER['HTTP_HOST'], 'ajax' => '1', 'random' => $this->random(5, 1)));
			if (is_error($response)) {
				//return $response;
			}
			return true;
		}else{
			return false;
		}
	}

	private function random($length, $numeric = FALSE) {
		$seed = base_convert(md5(microtime().$_SERVER['DOCUMENT_ROOT']), 16, $numeric ? 10 : 35);
		$seed = $numeric ? (str_replace('0', '', $seed).'012340567890') : ($seed.'zZ'.strtoupper($seed));
		if($numeric) {
			$hash = '';
		} else {
			$hash = chr(rand(1, 26) + rand(0, 1) * 32 + 64);
			$length--;
		}
		$max = strlen($seed) - 1;
		for($i = 0; $i < $length; $i++) {
			$hash .= $seed{mt_rand(0, $max)};
		}
		return $hash;
	}

    private function manifest_check($id, $m, $isret = false, $detourver = false) {
        if(is_string($m)) {
			if ($isret) return '模块配置项定义错误, 具体错误内容为: <br />' . $m;
            message(null, '模块配置项定义错误, 具体错误内容为: <br />' . $m);
        }
        if(!in_array(ES_VERSION, $m['versions'])) {
            if ($isret) return '模块与系统版本不兼容. ';
			if (!$detourver) message(null, '模块与系统版本不兼容. ');
        }
        if(empty($m['application']['title'])) {
            if ($isret) return '模块名称未定义. ';
            message(null, '模块名称未定义. ');
        }
        if(empty($m['application']['title_en']) || !preg_match('/^[a-z][a-z\d_]+$/i', $m['application']['title_en'])) {
            if ($isret) return '模块标识符未定义或格式错误(仅支持字母和数字, 且只能以字母开头). ';
            message(null, '模块标识符未定义或格式错误(仅支持字母和数字, 且只能以字母开头). ');
        }
        if(strtolower($id) != strtolower($m['application']['title_en'])) {
            if ($isret) return '模块名称定义与模块路径名称定义不匹配. ';
            message(null, '模块名称定义与模块路径名称定义不匹配. ');
        }
        if(empty($m['application']['version']) || !preg_match('/^[\d\.]+$/i', $m['application']['version'])) {
            if ($isret) return '模块版本号未定义(仅支持数字和句点). ';
            message(null, '模块版本号未定义(仅支持数字和句点). ');
        }
        if(empty($m['application']['ability'])) {
            if ($isret) return '模块功能简述未定义. ';
            message(null, '模块功能简述未定义. ');
        }
        if(!is_array($m['versions'])) {
            if ($isret) return '兼容版本格式错误. ';
            message(null, '兼容版本格式错误. ');
        }
		return "";
    }

	private function set_category($modulename, $module) {
		if ($modulename && $module['title_en'] == $modulename) {
			$this->load->helper("cloud");
			$setting = string2array($module['setting']);
			$setting['category'] = cloud_category($module['title_en'], $module['version']);
			db_update(table('functions'), array('setting'=>array2string($setting)), array('title_en' => $modulename));
		}
	}

    private function set_bindings($modulename, $module)
    {
        if ($modulename && $module['title_en'] == $modulename) {
            $bindid = array(0);
            $setting = string2array($module['setting']);
            $bindings = value($setting, 'bindings', true);
            if ($bindings) {
                foreach($bindings AS $key => $val) {
                    if (empty($val)) continue;
                    foreach($val AS $item) {
                        $wheresql = array('module'=>$modulename, 'entry'=>$key, 'do'=>$item['do']);
                        $datasql = $wheresql;
                        $datasql['module_name'] = $module['title'];
                        $datasql['call'] = $item['call'];
                        $datasql['title'] = $item['title'];
                        $datasql['state'] = $item['state'];
                        $datasql['direct'] = $item['direct'];
                        $data = db_getone(table('bindings'), $wheresql);
                        if ($data) {
                            db_update(table('bindings'), $datasql, array('id'=>$data['id']));
                        }else{
                            $data['id'] = db_insert(table('bindings'), $datasql, true);
                        }
                        if ($data['id']) {
                            $bindid[] = $data['id'];
                        }
                    }
                }
            }
            db_query("DELETE FROM `".table('bindings')."` WHERE `module`='".$modulename."' AND `id` NOT IN (".implode(',', $bindid).")");
        }
    }

	private function webgetappopenid($alid) {
		global $_A,$_GPC;
		$openid = $_A['_webgetappopenid_'.$alid];
		if ($alid > 0 && $openid !== false && strlen($openid) < 10) {
			$openid = $this->session->userdata('__SYS:USERID:' . intval($alid));
			if (empty($_A['openid'])) {
				$openid = $_GPC['__SYS:USERID:' . intval($alid)];
			}
			if (empty($_A['openid'])) {
				$alrow = db_getone(table('users_al'), array('id' => intval($alid)));
				if ($alrow) {
					$alrow['setting'] = string2array($alrow['setting']);
					$get_appid = value($alrow, 'setting|other|get_appid');
					if ($get_appid) {
						$get_appoint = value($alrow, 'setting|other|get_appoint', true);
						if (empty($get_appoint) || in_array($_GPC['module'], $get_appoint)) {
							$_secret = trim($alrow['setting']['other']['get_secret']);
							$_md5 = substr(md5(trim($get_appid) . $_secret), 8, 16);
							$openid = $this->session->userdata('g_openid_' . $_md5);
							if (empty($_A['openid'])) {
								$openid = $_GPC['g_openid_' . $_md5];
							}
						}
					}
				}
			}
			$_A['_webgetappopenid_'.$alid] = $openid?$openid:false;
		}
		return $openid;
	}

    private function del_bindings($modulename)
    {
        db_delete(table('bind_setting'), array('module'=>$modulename));
        db_delete(table('bindings'), array('module'=>$modulename));
        db_delete(table('reply'), array('module'=>$modulename));
    }

	private function dgmstrftime($seconds) {
		if ($seconds > 3600) {
			$rstr = intval($seconds/3600).'时'.gmstrftime('%M分:%S', $seconds - (intval($seconds/3600)*3600));
		}else if ($seconds > 60) {
			$rstr = gmstrftime('%M分:%S', $seconds);
		}else{
			$rstr = gmstrftime('%S', $seconds);
		}
		return str_replace(array('时:','分:'), array('时','分'), $rstr).'秒';
	}

	/**
	 * 写入文本
	 * @param $text
	 * @param string $filen
	 * @param bool $istxt
	 */
	private function writesetting($text, $filen = 'config', $istxt = false)
	{
		$cache_file_path = FCPATH."caches".DIRECTORY_SEPARATOR."cache.".$filen;
		$cache_file_path .= $istxt?".txt":".php";
		$content = $istxt?"":"<?php\r\n";
		$content .= $text;
		$content .= $istxt?"":"?>";
		make_dir(dirname($cache_file_path));
		if (!file_put_contents($cache_file_path, $content, LOCK_EX))
		{
			$fp = @fopen($cache_file_path, 'wb+');
			if (!$fp)
			{
				exit('生成缓存文件失败');
			}
			if (!@fwrite($fp, trim($content)))
			{
				exit('生成缓存文件失败');
			}
			@fclose($fp);
		}
	}
}
