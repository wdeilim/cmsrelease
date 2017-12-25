<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ES_Vip extends CI_Model {

	public function __construct()
	{
		parent::__construct();
        $this->load->helper('tpl');
        $this->base->apprun('vip');
	}

    /**
     * 合并条件数组
     * @param array $arr
     * @return array
     */
    private function merge($arr = array())
    {
        global $_A;
        if ($arr === 0) {
            return " `alid`='".$_A['al']['id']."' ";
        }else{
            return array_merge($arr, array('alid'=>$_A['al']['id']));
        }
    }

    /**
     * 添加通知、特权、礼品、优惠
     * @param $contentid
     */
    private function vip_content($contentid)
    {
        $row = $this->ddb->getone("SELECT * FROM ".table("vip_content"), array('id'=>intval($contentid)));
        if ($row) {
            $setting = string2array($row['setting']);
            $inarr = $this->merge();
            $inarr['view'] = 0;
            //
            $inarr['type'] = $row['type'];
            $inarr['contentid'] = $row['id'];
            $inarr['indate'] = $row['indate'];
            $inarr['num'] = 0;
            if ($row['type']=='cut') {
                $inarr['num'] = $row['int_c']; //优惠券数量
            }elseif ($row['type']=='gift'){
                $inarr['num'] = $row['int_b']; //礼品券数量
            }
            $contentnum = 0;
            if (isset($setting['vip_all'])){
                //全体会员
                $_wheresql = $this->merge(0);
				$_wheresql.= " AND (`enddate`=0 OR `enddate`>".SYS_TIME.")";
                $userlist = $this->ddb->getall("SELECT id,card,openid FROM ".table("vip_users")." WHERE ".$_wheresql);
                foreach($userlist as $item){
                    $inarr['userid'] = $item['id'];
                    $inarr['card'] = $item['card'];
                    $inarr['openid'] = $item['openid'];
                    $inarr['sn'] = getSN($item['id'], $row['id']);
                    $this->ddb->insert(table("vip_record"), $inarr);
                    $contentnum++;
                }
            }else{
                //非全体会员
                $_wheresql = $this->vip_listwhere($setting);
                if (!empty($_wheresql)){
                    $userlist = $this->ddb->getall("SELECT id,card,openid FROM ".table("vip_users").$_wheresql);
                    foreach($userlist as $item){
                        $inarr['userid'] = $item['id'];
                        $inarr['card'] = $item['card'];
                        $inarr['openid'] = $item['openid'];
                        $inarr['sn'] = getSN($item['id'], $row['id']);
                        $this->ddb->insert(table("vip_record"), $inarr);
                        $contentnum++;
                    }
                }
            }
            $this->ddb->update(table("vip_content"), array('num'=>$contentnum), array('id'=>$row['id']));
        }
    }

    /**
     * 通知条件
     * @param $setting
     * @return string
     */
    private function vip_listwhere($setting)
    {
        global $_A;
        static $rank_classes = array();
        $_wheresql = $this->merge(0);
		$_wheresql.= " AND (`enddate`=0 OR `enddate`>".SYS_TIME.")";
        $wheresql = "";
        //首次开卡会员
        if (isset($setting['vip_first'])){
            $wheresql.= " OR `id` NOT IN (SELECT DISTINCT userid from ".table("vip_record")." WHERE `type`='msg' {$_wheresql})";
        }
        //开卡从未消费的会员
        if (isset($setting['vip_no'])){
            $wheresql.= " OR `ofdate`=0";
        }
        //一个月未消费的会员
        if (isset($setting['vip_onemoon'])){
            $ttime = strtotime(date("Y-m-d", strtotime("-30 days")));
            $wheresql.= " OR `ofdate`<=".$ttime;
        }
        //累计消费满...
        $vip_val_total = intval(value($setting, 'vip_val_total'));
        if (isset($setting['vip_total']) && $vip_val_total > 0){
            $wheresql.= " OR `money`>=".$vip_val_total;
        }
        //单次消费满...
        $vip_val_one = intval(value($setting, 'vip_val_one'));
        if (isset($setting['vip_one']) && $vip_val_one > 0){
            $wheresql.= " OR `moneyone`>=".$vip_val_one;
        }
        //会员等级制度
        if (!isset($rank_classes["jfdj"])) {
            $row = $this->ddb->getone("SELECT userdata FROM ".table("users_functions"), array('id'=>$_A['uf']['id']));
            $userdata = string2array($row['userdata']);
            $jfdj = $rank_classes["jfdj"] = $userdata['jfdj'];
        }else{
            $jfdj = $rank_classes["jfdj"];
        }
        foreach($setting as $key=>$item){
            if (substr($key,0,8) == "vip_diy_"){
                $lev_a = intval(value($jfdj,substr($key,8).'|lev_a'));
                $lev_b = intval(value($jfdj,substr($key,8).'|lev_b'));
                if ($lev_a == 0 && $lev_b > 0){
                    $wheresql.= " OR `pluspoint`<=".$lev_b;
                }elseif ($lev_a > 0 && $lev_b == 0){
                    $wheresql.= " OR `pluspoint`>=".$lev_a;
                }elseif ($lev_a > 0 && $lev_b > $lev_a){
                    $wheresql.= " OR (`pluspoint`>=".$lev_a." AND `pluspoint`<=".$lev_b.")";
                }
            }
        }
        //
        $wheresql = ltrim(ltrim($wheresql),'OR');
        if ($wheresql && $_wheresql) {
            $wheresql = " (".$wheresql.") AND ".$_wheresql;
        }elseif ($_wheresql) {
            $wheresql = $_wheresql;
        }
        return " WHERE ".$wheresql;
    }

    /**
     * web前执行
     */
    public function doWeb() {
        $this->user->islogin(1);
    }

    /**
     * 首页
     */
    public function doWebIndex()
    {
        global $_GPC;
        $startdate = $_GPC['startdate'];
        $enddate = $_GPC['enddate'];
        $wheresql = "";
        if ($startdate){
            $wheresql.= " AND `indate`>=".strtotime($startdate);
        }
        if ($enddate){
            $wheresql.= " AND `indate`<=".strtotime($enddate);
        }

        $total_sql="SELECT COUNT(*) AS num FROM ".table('vip_record');
        //特权发放
        $fvip_num = $this->ddb->get_total($total_sql, $this->merge(array('type'=>'vip')));
        //礼品发放
        $fgift_num = $this->ddb->get_total($total_sql, $this->merge(array('type'=>'gift')));
        //优惠发放
        $fcut_num = $this->ddb->get_total($total_sql, $this->merge(array('type'=>'cut')));

        $total_sql="SELECT COUNT(*) AS num FROM ".table('vip_content_notes');
        //特权使用
        $vip_num = $this->ddb->get_total($total_sql, $this->merge(array('type'=>'vip')));
        //礼品使用
        $gift_num = $this->ddb->get_total($total_sql, $this->merge(array('type'=>'gift')));
        //优惠使用
        $cut_num = $this->ddb->get_total($total_sql, $this->merge(array('type'=>'cut')));

        //今日消费次数
        $total_sql="SELECT COUNT(*) AS num FROM ".table('vip_content_notes')." WHERE ".$this->merge(0)." AND `indate`>".mktime(0,0,0);
        $day_num = $this->ddb->get_total($total_sql);

        //发放明细
        $vip_list = $this->ddb->getall("SELECT * FROM ".table('vip_content')." WHERE ".$this->merge(0)." AND `type`='vip'".$wheresql);
        $gift_list = $this->ddb->getall("SELECT * FROM ".table('vip_content')." WHERE ".$this->merge(0)." AND `type`='gift'".$wheresql);
        $cut_list = $this->ddb->getall("SELECT * FROM ".table('vip_content')." WHERE ".$this->merge(0)." AND `type`='cut'".$wheresql);

        $this->cs->show(get_defined_vars());
    }


    /**
     * 店铺管理
     */
    public function doWebShop()
    {
        global $_A;
        $row = $this->ddb->getone("SELECT userdata FROM ".table("users_functions"), array('id'=>$_A['uf']['id']));
        $userdata = string2array($row['userdata']);
        $shop = $userdata['shop'];
        if ($this->input->post("dosubmit")){
            $fost = $this->input->post();
            $arr = $_arr = array();
            $arr['success'] = 0;
            //
            $_arr['type'] = $fost["type"];
            $_arr['name'] = $fost["name"];
            $_arr['content'] = value($fost, "content");
            if ($fost["pass"]) {
                if ($fost["pass"] != $fost["pass2"]){
                    $arr['message'] = '两次密码输入不一致！';
                    echo json_encode($arr); exit();
                }
                $_arr['pass'] = $fost["pass"];
            }
            $_arr['phone'] = $fost["phone"];
            $_arr['address'] = $fost["address"];
            $_arr['x'] = $fost["x"];
			$_arr['y'] = $fost["y"];
			$_arr['copyright'] = $fost["copyright"];
            $userdata['shop'] = $_arr;
            if ($this->ddb->update(table("users_functions"), array('userdata'=>array2string($userdata)), array("id"=>$_A['uf']["id"]))){
                $arr['success'] = 1;
                $arr['message'] = '修改成功';
            }else{
                $arr['message'] = '修改失败';
            }
            echo json_encode($arr); exit();
        }
        $this->cs->show(get_defined_vars());
    }

    /**
     * 分店管理
     */
    public function doWebShopbranch()
    {
        global $_GPC;
        //添加分店
        $t = $_GPC['param'][1];
        if ($t == 'add') {
            $fost = $this->input->post();
            $arr = array();
            $arr['success'] = 0;
            $arr['id'] = $fost["id"];

            $_arr = array();
            $_arr['name'] = $fost["name"];
            $_arr['address'] = $fost["address"];
            $_arr['phone'] = $fost["phone"];
            if (empty($_arr['name'])){
                $arr['message'] = '请输入区域名称';
                echo json_encode($arr);  exit();
            }
            if (empty($_arr['address'])){
                $arr['message'] = '请输入详细地址';
                echo json_encode($arr);  exit();
            }
            if (empty($_arr['phone'])){
                $arr['message'] = '请输入联系电话';
                echo json_encode($arr);  exit();
            }

            if ($fost["id"] > 0){
                $this->ddb->update(table("vip_shop"), $_arr, $this->merge(array("id"=>$fost["id"])));
            }else{
                $_arr['x'] = $fost["x"];
                $_arr['y'] = $fost["y"];
                if (empty($_arr['x']) || empty($_arr['y'])){
                    $arr['message'] = '请选择地图标注';
                    echo json_encode($arr);  exit();
                }
                $arr['id'] = $this->ddb->insert(table("vip_shop"), $this->merge($_arr), true);
            }
            //
            $arr['success'] = 1;
            echo json_encode($arr);  exit();
        }
        //删除分店
        if ($t == 'del') {
            $this->ddb->delete(table("vip_shop"), $this->merge(array("id"=>$this->input->post("id"))));
            $arr = array();
            $arr['success'] = "1";
            echo json_encode($arr); exit();
        }
        //修改地图坐标
        if ($t == 'map') {
            $arr = array();
            $arr['success'] = "1";
            $id = $this->input->post("id");
            if ($id > 0){
                $_arr = array();
                $_arr['x'] = $this->input->post("x");
                $_arr['y'] = $this->input->post("y");
                $this->ddb->update(table("vip_shop"), $this->merge($_arr), array("id"=>$id));
            }
            echo json_encode($arr); exit();
        }
        $lists = $this->ddb->getall("SELECT * FROM ".table("vip_shop"), $this->merge(), "`id` ASC");
        //
        $this->cs->show(get_defined_vars());
    }

    /**
     * 会员卡版面设置
     */
    public function doWebcard()
    {
        global $_A;
        $row = $this->ddb->getone("SELECT userdata FROM ".table("users_functions"), array('id'=>$_A['uf']['id']));
        $userdata = string2array($row['userdata']);
        //
        $dosubmit = $this->input->post("dosubmit");
        $type = $this->input->post("type");
        if ($dosubmit && $type){
            $userdata[$type] = $this->input->post();
            $this->ddb->update(table("users_functions"), array('userdata'=>array2string($userdata)), array("id"=>$_A['uf']['id']));
        }

        $hykbm = $userdata['hykbm'];
        //卡背景
        $hykbm['_bgimg'] = value($hykbm,'bgimgduv');
        if (empty($hykbm['_bgimg'])){
            $hykbm['_bgimg'] = BASE_DIR."uploadfiles/vipcardbg/1.jpg";
        }
        if (value($hykbm,'bgimg') != ""){
            $hykbm['_bgimg'] = BASE_DIR."uploadfiles/vipcardbg/".$hykbm['bgimg'];
        }

        $banner = $userdata['banner'];

        //获取文件列表
        $_patharr = glob(FCPATH."uploadfiles/vipcardbg/" . '*.{png,jpg,jpeg,gif}', GLOB_BRACE);
        foreach ($_patharr as $_key=>$_val) { $_patharr[$_key] = basename($_val); }
        $patharr = $_patharr;
        //
        $this->cs->show(get_defined_vars());
    }

    /**
     * 会员卡使用说明
     */
    public function doWebcardmanuals()
    {
        global $_A;
        $row = $this->ddb->getone("SELECT userdata FROM ".table("users_functions"), array('id'=>$_A['uf']['id']));
        $userdata = string2array($row['userdata']);
        //
        $dosubmit = $this->input->post("dosubmit");
        if ($dosubmit){
            $userdata['hyksm'] = $this->input->post();
            $this->ddb->update(table("users_functions"), array('userdata'=>array2string($userdata)), array("id"=>$_A['uf']['id']));
        }
        $hyksm = $userdata['hyksm'];
        //
        $this->cs->show(get_defined_vars());
    }


    /**
     * 会员等级设置
     */
    public function doWebcardlevel()
    {
        global $_A;
        $row = $this->ddb->getone("SELECT userdata FROM ".table("users_functions"), array('id'=>$_A['uf']['id']));
        $userdata = string2array($row['userdata']);
        //
        $dosubmit = $this->input->post("dosubmit");
        if ($dosubmit){
            $fost = $this->input->post();
            $arr = $_arr = array();
            for ($i=1; $i<=intval($fost['list_n']); $i++){
                $_name = $fost["name".$i];
                if (!empty($_name)){
                    $_arr[$i]['name'] = $_name;
                    $_arr[$i]['lev_a'] = $fost["lev_a".$i];
                    $_arr[$i]['lev_b'] = $fost["lev_b".$i];
                    $_arr[$i]['color'] = $fost["color".$i];
                }
            }
            $userdata['jfdj'] = $_arr;
            $this->ddb->update(table("users_functions"), array('userdata'=>array2string($userdata)), array("id"=>$_A['uf']['id']));
        }
        $jfdj = $userdata['jfdj'];
        //
        $this->cs->show(get_defined_vars());
    }


    /**
     * 积分策略设置
     */
    public function doWebcardintegral()
    {
        global $_A,$_GPC;
        $row = $this->ddb->getone("SELECT userdata FROM ".table("users_functions"), array('id'=>$_A['uf']['id']));
        $userdata = string2array($row['userdata']);
        //
        $dosubmit = $this->input->post("dosubmit");
        if ($dosubmit){
            $userdata['jfcl'] = $this->input->post();
            $this->ddb->update(table("users_functions"), array('userdata'=>array2string($userdata)), array("id"=>$_A['uf']['id']));
        }
        $jfcl = $userdata['jfcl'];
		//关键词签到部分
		if ($_GPC['keys']) {
			$signreply = db_getone(table('vip_setting'), array('alid'=>$_A['al']['id'], 'title'=>'signkey'));
			if ($signreply) {
				db_update(table('vip_setting'),
					array('content'=>array2string(array('keys'=>$_GPC['keys'], 'oktis'=>$_GPC['oktis'], 'notis'=>$_GPC['notis']))),
					array('alid'=>$_A['al']['id'], 'title'=>'signkey'));
			}else{
				db_insert(table('vip_setting'),
					array(
						'alid'=>$_A['al']['id'],
						'title'=>'signkey',
						'content'=>array2string(array('keys'=>$_GPC['keys'], 'oktis'=>$_GPC['oktis'], 'notis'=>$_GPC['notis']))
					));
			}
		}else{
			db_delete(table("vip_setting"), array('alid'=>$_A['al']['id'], 'title'=>'signkey'));
		}
        //
        $this->cs->show(get_defined_vars());
    }

    /**
     * 特权管理
     */
    public function doWebprivilege()
    {
        global $_GPC;
        $page = intval($_GPC['param'][1]);
        $pageurl = urlencode($this->base->url[3]);
        //
        $wheresql = $this->merge(0)." AND `type`='vip'";
        //
        $this->cs->show(get_defined_vars());
    }


    /**
     *
     * 发布（编辑）特权
     */
    public function doWebprivilegerelease()
    {
        global $_A,$_GPC;
        $ufrow = $this->ddb->getone("SELECT userdata FROM ".table("users_functions"), array('id'=>$_A['uf']['id']));
        $userdata = string2array($ufrow['userdata']);
        //
        $id = intval($_GPC['param'][1]);
        $row = array();
        $subtitle = "发布";
        if ($id > 0){
            $row = $this->ddb->getone("SELECT * FROM ".table("vip_content"), $this->merge(array('type'=>'vip', 'id'=>intval($id))));
            if (!empty($row)){
                $row['startdate'] = date('Y-m-d', $row['startdate']);
                $row['enddate'] = date('Y-m-d', $row['enddate']);
                $row['setting'] = string2array($row['setting']);
                $subtitle = "修改";
            }else{
                $id = 0;
            }
        }
        //
        if ($this->input->post("dosubmit")){
            $fost = $this->input->post();
            $arr = $_arr = array();
            $arr['success'] = 0;
            //
            $_arr['title'] = $fost["title"];
            $_arr['title_color'] = $fost["title_color"];
            $_arr['content'] = value($fost, "content");
            $_arr['img'] = $fost["img"];
            $_arr['onelogin'] = intval(value($fost, "onelogin"));
            $_arr['startdate'] = strtotime($fost["startdate"]);
            $_arr['enddate'] = strtotime($fost["enddate"]);
            $_arr['update'] = SYS_TIME;
            if (empty($_arr['title'])) {
                $arr['message'] = '标题不能为空';
                echo json_encode($arr); exit();
            }
            if (empty($_arr['content'])) {
                $arr['message'] = '使用说明不能为空';
                echo json_encode($arr); exit();
            }
            if (empty($fost["startdate"]) || empty($fost["enddate"])) {
                $arr['message'] = '请选择有效期';
                echo json_encode($arr); exit();
            }
            if ($_arr['startdate'] > $_arr['enddate']){
                $arr['message'] = '请选择正确的有效期';
                echo json_encode($arr); exit();
            }
            //会员类型
            $_arr['setting'] = array();
            $_arr['onevips'] = ",";
            foreach ($fost as $_k=>$_v){
                if  (substr($_k, 0, 4) == 'vip_'){
                    if ($_v) $_arr['setting'][$_k] = $_v;
                }elseif  (substr($_k, 0, 8) == 'onevips_'){
                    $_arr['onevips'].= $_v.",";
                }
            }
            //
            if ($id > 0){
                //修改
                unset($_arr['setting']);
                if ($this->ddb->update(table("vip_content"), $_arr, array("id"=>$id))){
                    $arr['success'] = 1;
                    $arr['message'] = '修改成功';
                    echo json_encode($arr); exit();
                }
            }else{
                //新增
                $_arr['type'] = "vip";
                $_arr['indate'] = SYS_TIME;
                if (empty($_arr['setting'])){
                    $arr['message'] = '请选择会员类型';
                    echo json_encode($arr); exit();
                }
                if (isset($_arr['setting']['vip_total']) && intval(value($_arr['setting'], 'vip_val_total')) < 1){
                    $arr['message'] = '请输入累计消费数';
                    echo json_encode($arr); exit();
                }
                if (isset($_arr['setting']['vip_one']) && intval(value($_arr['setting'], 'vip_val_one')) < 1){
                    $arr['message'] = '请输入单次消费数';
                    echo json_encode($arr); exit();
                }
                $_arr['setting'] = array2string($_arr['setting']);
                $contentid = $this->ddb->insert(table("vip_content"), $this->merge($_arr), true);
                if ($contentid > 0){
                    $this->vip_content($contentid);
                    $arr['success'] = 1;
                    $arr['message'] = '添加成功';
                    echo json_encode($arr); exit();
                }
            }
        }
        $content = $row;
        $tequan = $userdata['jfdj'];
        //
        $this->cs->show(get_defined_vars());
    }


    /**
     * 特权统计
     */
    public function doWebprivilegestatistics()
    {
        global $_A,$_GPC;
        //
        $page = intval($_GPC['param'][1]);
        $keyn = ''; $keyv = '';
        $wheresql = " ".$this->merge(0)." AND `type` = 'vip'";
        $keyarr = array('fullname','card','sn','operator','contentid','contenttitle');
        foreach($keyarr as $itme){
            if ($this->input->get($itme)){
                $keyn = $itme; $keyv = $this->input->get($itme);
                $wheresql.= ($keyn=='contentid')?" AND `{$keyn}` = '{$keyv}'":" AND `{$keyn}` LIKE '%{$keyv}%'";
            }
        }
        if ($this->input->post("dosubmit")){
            if ($this->input->post("type") == 'export'){
                //导出
                if ($this->input->post('y_id')){
                    $y_id = $this->input->post("y_id");
                    $wheresql.= " AND `id` IN (".implode(',',$y_id).")";
                    $row = $this->ddb->getall("SELECT * FROM ".table("vip_content_notes")." WHERE {$wheresql} ORDER BY `indate` DESC");
                }elseif ($this->input->post('n1')){
                    $n1 = intval($this->input->post('n1'));
                    if ($n1 > 0) $n1 = $n1 - 1;
                    $n2 = intval($this->input->post('n2'));
                    if ($n2 > 0) $n2 = $n2 - $n1;
                    $limit=" LIMIT ".$n1.','.$n2;
                    $row = $this->ddb->getall("SELECT * FROM ".table("vip_content_notes")." WHERE {$wheresql} ORDER BY `indate` DESC".$limit);
                }else{
                    message("没有可导出的数据");
                }
                //
                $text = "SN 码,会员卡号,姓名,操作员,积分,第几次,特权名称,使用时间\r\n";
                foreach($row as $item){
                    $text.= _csv($item['sn']);
                    $text.= ","._csv($item['card']);
                    $text.= ","._csv($item['fullname']);
                    $text.= ","._csv($item['operator']);
                    $text.= ","._csv($item['point']);
                    $text.= ","._csv($item['usenum']);
                    $text.= ","._csv($item['contenttitle']);
                    $text.= ",".date("Y-m-d H:i:s",$item['indate']);
                    $text.= "\r\n";
                }
                $TXTTIME = mb_convert_encoding($text, "gbk", "utf-8");
                $NAMETIME = "notes_".date('YmdHis').".csv";
                $fname = "uploadfiles/csv/".intval($_A['userid'])."/";
                make_dir($fname);
                $fname.= $NAMETIME;
                if (!file_put_contents(FCPATH.$fname, $TXTTIME, LOCK_EX)){
                    $fp = @fopen(FCPATH.$fname, 'wb+');
                    if (!$fp) exit('-1');
                    if (!@fwrite($fp, trim($TXTTIME))) exit('导出失败！');
                    @fclose($fp);
                }
                $file = fopen($fname, "r");
                Header("Content-type: application/octet-stream");
                Header("Accept-Ranges: bytes");
                Header("Accept-Length: ".filesize($fname));
                Header("Content-Disposition: attachment; filename=".$NAMETIME);
                echo fread($file, filesize($fname));
                fclose($file);
                exit();
            }
        }

        $pageurl = urlencode($this->base->url[3]);
        //
        $this->cs->show(get_defined_vars());
    }

    /**
     * 删除特权
     */
    public function doWebprivilegedel()
    {
        $arr = array();
        $arr['success'] = 1;

        $y_id = $this->input->post("id");
        if (!empty($y_id)){
            $y_id = trim($y_id, ',');
            $this->ddb->query("DELETE FROM ".table("vip_content")." WHERE ".$this->merge(0)." AND `type`='vip' AND `id` IN (".$y_id.")");
            $this->ddb->query("DELETE FROM ".table("vip_record")." WHERE ".$this->merge(0)." AND `type`='vip' AND `contentid` IN (".$y_id.")");
        }
        echo json_encode($arr); exit();
    }


    /**
     * 礼品管理
     */
    public function doWebgiftcert()
    {
        global $_GPC;
        $page = intval($_GPC['param'][1]);
        $pageurl = urlencode($this->base->url[3]);
        //
        $wheresql = $this->merge(0)." AND `type`='gift'";
        //
        $this->cs->show(get_defined_vars());
    }


    /**
     *
     * 发布（编辑）礼品
     */
    public function doWebgiftcertrelease()
    {
        global $_A,$_GPC;
        $ufrow = $this->ddb->getone("SELECT userdata FROM ".table("users_functions"), array('id'=>$_A['uf']['id']));
        $userdata = string2array($ufrow['userdata']);
        //
        $id = intval($_GPC['param'][1]);
        $row = array();
        $subtitle = "发布";
        if ($id > 0){
            $row = $this->ddb->getone("SELECT * FROM ".table("vip_content"), $this->merge(array('type'=>'gift', 'id'=>intval($id))));
            if (!empty($row)){
                $row['startdate'] = date('Y-m-d', $row['startdate']);
                $row['enddate'] = date('Y-m-d', $row['enddate']);
                $row['setting'] = string2array($row['setting']);
                $subtitle = "修改";
            }else{
                $id = 0;
            }
        }
        //
        if ($this->input->post("dosubmit")){
            $fost = $this->input->post();
            $arr = $_arr = array();
            $arr['success'] = 0;
            //
            $_arr['title'] = $fost["title"];
            $_arr['title_color'] = $fost["title_color"];
            $_arr['content'] = value($fost, "content");
            $_arr['img'] = $fost["img"];
            $_arr['onelogin'] = intval(value($fost, "onelogin"));
            $_arr['startdate'] = strtotime($fost["startdate"]);
            $_arr['enddate'] = strtotime($fost["enddate"]);
            $_arr['update'] = SYS_TIME;
            if (empty($_arr['title'])) {
                $arr['message'] = '标题不能为空';
                echo json_encode($arr); exit();
            }
            if (empty($_arr['content'])) {
                $arr['message'] = '使用说明不能为空';
                echo json_encode($arr); exit();
            }
            if (empty($fost["startdate"]) || empty($fost["enddate"])) {
                $arr['message'] = '请选择有效期';
                echo json_encode($arr); exit();
            }
            if ($_arr['startdate'] > $_arr['enddate']){
                $arr['message'] = '请选择正确的有效期';
                echo json_encode($arr); exit();
            }
            //会员类型
            $_arr['setting'] = array();
            $_arr['onevips'] = ",";
            foreach ($fost as $_k=>$_v){
                if  (substr($_k, 0, 4) == 'vip_'){
                    if ($_v) $_arr['setting'][$_k] = $_v;
                }elseif  (substr($_k, 0, 8) == 'onevips_'){
                    $_arr['onevips'].= $_v.",";
                }
            }
            //
            if ($id > 0){
                //修改
                unset($_arr['setting']);
                if ($this->ddb->update(table("vip_content"), $_arr, array("id"=>$id))){
                    $arr['success'] = 1;
                    $arr['message'] = '修改成功';
                    echo json_encode($arr); exit();
                }
            }else{
                //新增
                $_arr['int_b'] = intval($fost["int_b"]);
                $_arr['int_c'] = intval($fost["int_c"]);
                $_arr['type'] = "gift";
                $_arr['indate'] = SYS_TIME;
                if (empty($_arr['setting'])){
                    $arr['message'] = '请选择会员类型';
                    echo json_encode($arr); exit();
                }
                if (isset($_arr['setting']['vip_total']) && intval(value($_arr['setting'], 'vip_val_total')) < 1){
                    $arr['message'] = '请输入累计消费数';
                    echo json_encode($arr); exit();
                }
                if (isset($_arr['setting']['vip_one']) && intval(value($_arr['setting'], 'vip_val_one')) < 1){
                    $arr['message'] = '请输入单次消费数';
                    echo json_encode($arr); exit();
                }
                if (empty($_arr['int_c'])){
                    $arr['message'] = '请输入正确的兑换礼品所需积分';
                    echo json_encode($arr); exit();
                }
                if (empty($_arr['int_b'])){
                    $arr['message'] = '请输入正确的每个用户可以获取到张券';
                    echo json_encode($arr); exit();
                }
                $_arr['setting'] = array2string($_arr['setting']);
                $contentid = $this->ddb->insert(table("vip_content"), $this->merge($_arr), true);
                if ($contentid > 0){
                    $this->vip_content($contentid);
                    $arr['success'] = 1;
                    $arr['message'] = '添加成功';
                    echo json_encode($arr); exit();
                }
            }
        }
        $content = $row;
        $tequan = $userdata['jfdj'];
        //
        $this->cs->show(get_defined_vars());
    }


    /**
     * 礼品统计
     */
    public function doWebgiftcertstatistics()
    {
        global $_A,$_GPC;
        //
        $page = intval($_GPC['param'][1]);
        $keyn = ''; $keyv = '';
        $wheresql = " ".$this->merge(0)." AND `type` = 'gift'";
        $keyarr = array('fullname','card','sn','operator','contentid','contenttitle');
        foreach($keyarr as $itme){
            if ($this->input->get($itme)){
                $keyn = $itme; $keyv = $this->input->get($itme);
                $wheresql.= ($keyn=='contentid')?" AND `{$keyn}` = '{$keyv}'":" AND `{$keyn}` LIKE '%{$keyv}%'";
            }
        }
        if ($this->input->post("dosubmit")){
            if ($this->input->post("type") == 'export'){
                //导出
                if ($this->input->post('y_id')){
                    $y_id = $this->input->post("y_id");
                    $wheresql.= " AND `id` IN (".implode(',',$y_id).")";
                    $row = $this->ddb->getall("SELECT * FROM ".table("vip_content_notes")." WHERE {$wheresql} ORDER BY `indate` DESC");
                }elseif ($this->input->post('n1')){
                    $n1 = intval($this->input->post('n1'));
                    if ($n1 > 0) $n1 = $n1 - 1;
                    $n2 = intval($this->input->post('n2'));
                    if ($n2 > 0) $n2 = $n2 - $n1;
                    $limit=" LIMIT ".$n1.','.$n2;
                    $row = $this->ddb->getall("SELECT * FROM ".table("vip_content_notes")." WHERE {$wheresql} ORDER BY `indate` DESC".$limit);
                }else{
                    message("没有可导出的数据");
                }
                //
                $text = "SN 码,会员卡号,姓名,操作员,积分,第几次,特权名称,使用时间\r\n";
                foreach($row as $item){
                    $text.= _csv($item['sn']);
                    $text.= ","._csv($item['card']);
                    $text.= ","._csv($item['fullname']);
                    $text.= ","._csv($item['operator']);
                    $text.= ","._csv($item['point']);
                    $text.= ","._csv($item['usenum']);
                    $text.= ","._csv($item['contenttitle']);
                    $text.= ",".date("Y-m-d H:i:s",$item['indate']);
                    $text.= "\r\n";
                }
                $TXTTIME = mb_convert_encoding($text, "gbk", "utf-8");
                $NAMETIME = "notes_".date('YmdHis').".csv";
                $fname = "uploadfiles/csv/".intval($_A['userid'])."/";
                make_dir($fname);
                $fname.= $NAMETIME;
                if (!file_put_contents(FCPATH.$fname, $TXTTIME, LOCK_EX)){
                    $fp = @fopen(FCPATH.$fname, 'wb+');
                    if (!$fp) exit('-1');
                    if (!@fwrite($fp, trim($TXTTIME))) exit('导出失败！');
                    @fclose($fp);
                }
                $file = fopen($fname, "r");
                Header("Content-type: application/octet-stream");
                Header("Accept-Ranges: bytes");
                Header("Accept-Length: ".filesize($fname));
                Header("Content-Disposition: attachment; filename=".$NAMETIME);
                echo fread($file, filesize($fname));
                fclose($file);
                exit();
            }
        }

        $pageurl = urlencode($this->base->url[3]);
        //
        $this->cs->show(get_defined_vars());
    }

    /**
     * 删除礼品
     */
    public function doWebgiftcertdel()
    {
        $arr = array();
        $arr['success'] = 1;

        $y_id = $this->input->post("id");
        if (!empty($y_id)){
            $y_id = trim($y_id, ',');
            $this->ddb->query("DELETE FROM ".table("vip_content")." WHERE ".$this->merge(0)." AND `type`='gift' AND `id` IN (".$y_id.")");
            $this->ddb->query("DELETE FROM ".table("vip_record")." WHERE ".$this->merge(0)." AND `type`='gift' AND `contentid` IN (".$y_id.")");
        }
        echo json_encode($arr); exit();
    }

    /**
     * 优惠券管理
     */
    public function doWebcoupon()
    {
        global $_GPC;
        $page = intval($_GPC['param'][1]);
        $pageurl = urlencode($this->base->url[3]);
        //
        $wheresql = $this->merge(0)." AND `type`='cut'";
        //
        $this->cs->show(get_defined_vars());
    }


    /**
     *
     * 发布（编辑）优惠券
     */
    public function doWebcouponrelease()
    {
        global $_A,$_GPC;
        $ufrow = $this->ddb->getone("SELECT userdata FROM ".table("users_functions"), array('id'=>$_A['uf']['id']));
        $userdata = string2array($ufrow['userdata']);
        //
        $id = intval($_GPC['param'][1]);
        $row = array();
        $subtitle = "发布";
        if ($id > 0){
            $row = $this->ddb->getone("SELECT * FROM ".table("vip_content"), $this->merge(array('type'=>'cut', 'id'=>intval($id))));
            if (!empty($row)){
                $row['startdate'] = date('Y-m-d', $row['startdate']);
                $row['enddate'] = date('Y-m-d', $row['enddate']);
                $row['setting'] = string2array($row['setting']);
                $subtitle = "修改";
            }else{
                $id = 0;
            }
        }
        //
        if ($this->input->post("dosubmit")){
            $fost = $this->input->post();
            $arr = $_arr = array();
            $arr['success'] = 0;
            //
            $_arr['title'] = $fost["title"];
            $_arr['type_b'] = value($fost, "type_b");
            $_arr['title_color'] = $fost["title_color"];
            $_arr['content'] = value($fost, "content");
            $_arr['img'] = $fost["img"];
            $_arr['onelogin'] = intval(value($fost, "onelogin"));
            $_arr['startdate'] = strtotime($fost["startdate"]);
            $_arr['enddate'] = strtotime($fost["enddate"]);
            $_arr['int_a'] = intval($fost["int_a"]);
            $_arr['int_b'] = intval($fost["int_b"]);
            $_arr['update'] = SYS_TIME;
            if (empty($_arr['title'])) {
                $arr['message'] = '标题不能为空';
                echo json_encode($arr); exit();
            }
            if ($_arr['type_b'] == "打折优惠券" && empty($_arr['int_a'])) {
                $arr['message'] = '请在优惠券类型输入打折额度';
                echo json_encode($arr); exit();
            }elseif ($_arr['type_b'] == "现金抵用券" && empty($_arr['int_b'])) {
                $arr['message'] = '请在优惠券类型输入抵用金额';
                echo json_encode($arr); exit();
            }elseif (empty($_arr['type_b'])){
                $arr['message'] = '请选择优惠券类型';
                echo json_encode($arr); exit();
            }
            if (empty($_arr['content'])) {
                $arr['message'] = '使用说明不能为空';
                echo json_encode($arr); exit();
            }
            if (empty($fost["startdate"]) || empty($fost["enddate"])) {
                $arr['message'] = '请选择有效期';
                echo json_encode($arr); exit();
            }
            if ($_arr['startdate'] > $_arr['enddate']){
                $arr['message'] = '请选择正确的有效期';
                echo json_encode($arr); exit();
            }
            //会员类型
            $_arr['setting'] = array();
            $_arr['onevips'] = ",";
            foreach ($fost as $_k=>$_v){
                if  (substr($_k, 0, 4) == 'vip_'){
                    if ($_v) $_arr['setting'][$_k] = $_v;
                }elseif  (substr($_k, 0, 8) == 'onevips_'){
                    $_arr['onevips'].= $_v.",";
                }
            }
            //
            if ($id > 0){
                //修改
                unset($_arr['setting']);
                if ($this->ddb->update(table("vip_content"), $_arr, array("id"=>$id))){
                    $arr['success'] = 1;
                    $arr['message'] = '修改成功';
                    echo json_encode($arr); exit();
                }
            }else{
                //新增
                $_arr['int_c'] = intval($fost["int_c"]);
                $_arr['type'] = "cut";
                $_arr['indate'] = SYS_TIME;
                if (empty($_arr['setting'])){
                    $arr['message'] = '请选择会员类型';
                    echo json_encode($arr); exit();
                }
                if (isset($_arr['setting']['vip_total']) && intval(value($_arr['setting'], 'vip_val_total')) < 1){
                    $arr['message'] = '请输入累计消费数';
                    echo json_encode($arr); exit();
                }
                if (isset($_arr['setting']['vip_one']) && intval(value($_arr['setting'], 'vip_val_one')) < 1){
                    $arr['message'] = '请输入单次消费数';
                    echo json_encode($arr); exit();
                }
                if (empty($_arr['int_c'])){
                    $arr['message'] = '请输入正确的每个用户可以获取到张券';
                    echo json_encode($arr); exit();
                }
                $_arr['setting'] = array2string($_arr['setting']);
                $contentid = $this->ddb->insert(table("vip_content"), $this->merge($_arr), true);
                if ($contentid > 0){
                    $this->vip_content($contentid);
                    $arr['success'] = 1;
                    $arr['message'] = '添加成功';
                    echo json_encode($arr); exit();
                }
            }
        }
        $content = $row;
        $tequan = $userdata['jfdj'];
        //
        $this->cs->show(get_defined_vars());
    }


    /**
     * 优惠券统计
     */
    public function doWebcouponstatistics()
    {
        global $_A,$_GPC;
        //
        $page = intval($_GPC['param'][1]);
        $keyn = ''; $keyv = '';
        $wheresql = " ".$this->merge(0)." AND `type` = 'cut'";
        $keyarr = array('fullname','card','sn','operator','contentid','contenttitle');
        foreach($keyarr as $itme){
            if ($this->input->get($itme)){
                $keyn = $itme; $keyv = $this->input->get($itme);
                $wheresql.= ($keyn=='contentid')?" AND `{$keyn}` = '{$keyv}'":" AND `{$keyn}` LIKE '%{$keyv}%'";
            }
        }
        if ($this->input->post("dosubmit")){
            if ($this->input->post("type") == 'export'){
                //导出
                if ($this->input->post('y_id')){
                    $y_id = $this->input->post("y_id");
                    $wheresql.= " AND `id` IN (".implode(',',$y_id).")";
                    $row = $this->ddb->getall("SELECT * FROM ".table("vip_content_notes")." WHERE {$wheresql} ORDER BY `indate` DESC");
                }elseif ($this->input->post('n1')){
                    $n1 = intval($this->input->post('n1'));
                    if ($n1 > 0) $n1 = $n1 - 1;
                    $n2 = intval($this->input->post('n2'));
                    if ($n2 > 0) $n2 = $n2 - $n1;
                    $limit=" LIMIT ".$n1.','.$n2;
                    $row = $this->ddb->getall("SELECT * FROM ".table("vip_content_notes")." WHERE {$wheresql} ORDER BY `indate` DESC".$limit);
                }else{
                    message("没有可导出的数据");
                }
                //
                $text = "SN 码,会员卡号,姓名,操作员,积分,第几次,特权名称,使用时间\r\n";
                foreach($row as $item){
                    $text.= _csv($item['sn']);
                    $text.= ","._csv($item['card']);
                    $text.= ","._csv($item['fullname']);
                    $text.= ","._csv($item['operator']);
                    $text.= ","._csv($item['point']);
                    $text.= ","._csv($item['usenum']);
                    $text.= ","._csv($item['contenttitle']);
                    $text.= ",".date("Y-m-d H:i:s",$item['indate']);
                    $text.= "\r\n";
                }
                $TXTTIME = mb_convert_encoding($text, "gbk", "utf-8");
                $NAMETIME = "notes_".date('YmdHis').".csv";
                $fname = "uploadfiles/csv/".intval($_A['userid'])."/";
                make_dir($fname);
                $fname.= $NAMETIME;
                if (!file_put_contents(FCPATH.$fname, $TXTTIME, LOCK_EX)){
                    $fp = @fopen(FCPATH.$fname, 'wb+');
                    if (!$fp) exit('-1');
                    if (!@fwrite($fp, trim($TXTTIME))) exit('导出失败！');
                    @fclose($fp);
                }
                $file = fopen($fname, "r");
                Header("Content-type: application/octet-stream");
                Header("Accept-Ranges: bytes");
                Header("Accept-Length: ".filesize($fname));
                Header("Content-Disposition: attachment; filename=".$NAMETIME);
                echo fread($file, filesize($fname));
                fclose($file);
                exit();
            }
        }

        $pageurl = urlencode($this->base->url[3]);
        //
        $this->cs->show(get_defined_vars());
    }

    /**
     * 删除优惠券
     */
    public function doWebcoupondel()
    {
        $arr = array();
        $arr['success'] = 1;

        $y_id = $this->input->post("id");
        if (!empty($y_id)){
            $y_id = trim($y_id, ',');
            $this->ddb->query("DELETE FROM ".table("vip_content")." WHERE ".$this->merge(0)." AND `type`='cut' AND `id` IN (".$y_id.")");
            $this->ddb->query("DELETE FROM ".table("vip_record")." WHERE ".$this->merge(0)." AND `type`='cut' AND `contentid` IN (".$y_id.")");
        }
        echo json_encode($arr); exit();
    }

    /**
     * 通知管理
     */
    public function doWebnotification()
    {
        global $_GPC;
        $page = intval($_GPC['param'][1]);
        $pageurl = urlencode($this->base->url[3]);
        //
        $wheresql = $this->merge(0)." AND `type`='msg'";
        //
        $this->cs->show(get_defined_vars());
    }


    /**
     *
     * 发布（编辑）通知
     */
    public function doWebnotificationrelease()
    {
        global $_A,$_GPC;
        $ufrow = $this->ddb->getone("SELECT userdata FROM ".table("users_functions"), array('id'=>$_A['uf']['id']));
        $userdata = string2array($ufrow['userdata']);
        //
        $id = intval($_GPC['param'][1]);
        $row = array();
        $subtitle = "发布";
        if ($id > 0){
            $row = $this->ddb->getone("SELECT * FROM ".table("vip_content"), $this->merge(array('type'=>'msg', 'id'=>intval($id))));
            if (!empty($row)){
                $row['setting'] = string2array($row['setting']);
                $subtitle = "修改";
            }else{
                $id = 0;
            }
        }
        //
        if ($this->input->post("dosubmit")){
            $fost = $this->input->post();
            $arr = $_arr = array();
            $arr['success'] = 0;
            //
            $_arr['title'] = $fost["title"];
            $_arr['title_color'] = $fost["title_color"];
            $_arr['content'] = value($fost, "content");
            $_arr['img'] = $fost["img"];
            $_arr['onelogin'] = intval(value($fost, "onelogin"));
            $_arr['update'] = SYS_TIME;
            if (empty($_arr['title'])) {
                $arr['message'] = '标题不能为空';
                echo json_encode($arr); exit();
            }
            if (empty($_arr['content'])) {
                $arr['message'] = '内容不能为空';
                echo json_encode($arr); exit();
            }
            //会员类型
            $_arr['setting'] = array();
            $_arr['onevips'] = ",";
            foreach ($fost as $_k=>$_v){
                if  (substr($_k, 0, 4) == 'vip_'){
                    if ($_v) $_arr['setting'][$_k] = $_v;
                }elseif  (substr($_k, 0, 8) == 'onevips_'){
                    $_arr['onevips'].= $_v.",";
                }
            }
            //
            if ($id > 0){
                //修改
                unset($_arr['setting']);
                if ($this->ddb->update(table("vip_content"), $_arr, array("id"=>$id))){
                    $arr['success'] = 1;
                    $arr['message'] = '修改成功';
                    echo json_encode($arr); exit();
                }
            }else{
                //新增
                $_arr['type'] = "msg";
                $_arr['indate'] = SYS_TIME;
                if (empty($_arr['setting'])){
                    $arr['message'] = '请选择会员类型';
                    echo json_encode($arr); exit();
                }
                if (isset($_arr['setting']['vip_total']) && intval(value($_arr['setting'], 'vip_val_total')) < 1){
                    $arr['message'] = '请输入累计消费数';
                    echo json_encode($arr); exit();
                }
                if (isset($_arr['setting']['vip_one']) && intval(value($_arr['setting'], 'vip_val_one')) < 1){
                    $arr['message'] = '请输入单次消费数';
                    echo json_encode($arr); exit();
                }
                $_arr['setting'] = array2string($_arr['setting']);
                $contentid = $this->ddb->insert(table("vip_content"), $this->merge($_arr), true);
                if ($contentid > 0){
                    $this->vip_content($contentid);
                    $arr['success'] = 1;
                    $arr['message'] = '添加成功';
                    echo json_encode($arr); exit();
                }
            }
        }
        $content = $row;
        $tequan = $userdata['jfdj'];
        //
        $this->cs->show(get_defined_vars());
    }


    /**
     * 删除通知
     */
    public function doWebnotificationdel()
    {
        $arr = array();
        $arr['success'] = 1;

        $y_id = $this->input->post("id");
        if (!empty($y_id)){
            $y_id = trim($y_id, ',');
            $this->ddb->query("DELETE FROM ".table("vip_content")." WHERE ".$this->merge(0)." AND `type`='msg' AND `id` IN (".$y_id.")");
            $this->ddb->query("DELETE FROM ".table("vip_record")." WHERE ".$this->merge(0)." AND `type`='msg' AND `contentid` IN (".$y_id.")");
        }
        echo json_encode($arr); exit();
    }

    /**
     * 动态修改内容
     */
    public function doWebeditval()
    {
        $arr = array();
        $arr['success'] = 0;
        $arr['val'] = '';
        if ($this->input->post("dosubmit")){
            //
            $id = intval($this->input->post('id'));
            $type = $this->input->post('type');
            $isnum = $this->input->post('isnum');
            $val = $this->input->post('val');
            if ($isnum == 'yes') {
                $val = intval($val);
            }
            $row = $this->ddb->getone("SELECT * FROM ".table('vip_content'), $this->merge(array('id'=>$id)));
            if (empty($row)) {
                $arr['message'] = '内容不存在';
                echo json_encode($arr); exit();
            }
            if (!isset($row[$type])){
                $arr['message'] = '参数错误';
                echo json_encode($arr); exit();
            }
            if ($this->ddb->update(table('vip_content'), array($type=>$val), array('id'=>$id))){
                $arr['success'] = 1;
                $arr['message'] = '修改成功';
                $arr['val'] = $val;
            }else{
                $arr['message'] = '修改失败';
            }
            echo json_encode($arr); exit();
        }else{
            $arr['message'] = '非法操作';
            echo json_encode($arr); exit();
        }
    }


    /**
     * 会员管理
     */
    public function doWebmember()
    {
        global $_A,$_GPC;
        $ufrow = $this->ddb->getone("SELECT userdata FROM ".table("users_functions"), array('id'=>$_A['uf']['id']));
        $userdata = string2array($ufrow['userdata']);
        //
        $page = intval($_GPC['param'][1]);
        
        $pageurl = urlencode($this->base->url[3]);

        $keyval = $this->input->get('keyval');
        $keytype = $this->input->get('keytype');
        $usertype = $this->input->get('usertype');

        //会员组
        $userlevel = array();
        $jfdj = $userdata['jfdj'];
        foreach ($jfdj as $key=>$item){
            if (is_array($item)){
                $userlevel[$key] = $item;
            }
        }

        //搜索
        $wheresql = $this->merge(0);
        if ($keyval && $keytype){
            if ($keyval == "id") {
                $wheresql.= " AND `".$keytype."` = ".intval($keyval);
            }else{
                $wheresql.= " AND `".$keytype."` like '%".$keyval."%' ";
            }
        }
        if ($usertype){
            if (isset($userlevel[$usertype])){
                $templevel = $userlevel[$usertype];
                if ($templevel['lev_a'] > 0){
                    $wheresql.= " AND `pluspoint`>=".$templevel['lev_a'];
                }
                if ($templevel['lev_b'] > 0){
                    $wheresql.= " AND `pluspoint`<=".$templevel['lev_b'];
                }
            }
        }
        //
        if ($this->input->post("dosubmit") == 'edit'){
            //修改会员
            $fost = $this->input->post();
            $_arr = array();
            $_arr['id'] = intval($fost['id']);
            $_arr['fullname'] = $fost["fullname"];
            $_arr['sex'] = $fost["sex"];
            $_arr['phone'] = $fost["phone"];
            $_arr['address'] = $fost["address"];
            $_arr['email'] = strtolower($fost["email"]);
            $_arr['idnumber'] = $fost["idnumber"];
            $_arr['enddate'] = $fost["enddate"];
            $_arr['enddate'] = ($_arr['enddate']>0)?strtotime($_arr['enddate']):0;
            //
            $user = db_getone(table("vip_users"), $this->merge(array("id"=>intval($_arr['id']))));
            if (empty($user)){
                $_arr['success'] = 0;
                $_arr['message'] = "会员不存在！";
                echo json_encode($_arr); exit();
            }
            //检查手机
            $farr = array();
            if ($_arr['phone']) {
                $haere = " WHERE ".$this->merge(0)." AND `userphone`='".$_arr['phone']."'";
                $haere.= " AND `openid`!='".$user['openid']."'";
                $harow = db_getone(table('fans').$haere);
                if ($harow) {
                    $_arr['success'] = 0;
                    $_arr['message'] = "手机号码已存在！";
                    echo json_encode($_arr); exit();
                }
                $farr['userphone'] = $_arr['phone'];
            }
            //检查邮箱
            if ($_arr['email']) {
                $haere = " WHERE ".$this->merge(0)." AND `useremail`='".$_arr['email']."'";
                $haere.= " AND `openid`!='".$user['openid']."'";
                $harow = db_getone(table('fans').$haere);
                if ($harow) {
                    $_arr['success'] = 0;
                    $_arr['message'] = "邮箱地址已存在！";
                    echo json_encode($_arr); exit();
                }
                $farr['useremail'] = $_arr['email'];
            }
            //开始修改
            if (!$this->ddb->update(table("vip_users"), $_arr, $this->merge(array("id"=>$_arr['id'])))) {
                $_arr['success'] = 0;
                $_arr['message'] = "修改失败，请稍后再试！";
                echo json_encode($_arr); exit();
            }
            if ($farr) {
                db_update(table('fans'), $farr, array('openid'=>$user['openid']));
            }
            $_arr['phone'] = $_arr['phone']?format_user($_arr['phone'], 1):'未完善';
            $_arr['email'] = $_arr['email']?$_arr['email']:'未完善';
            $_arr['idnumber'] = $_arr['idnumber']?$_arr['idnumber']:'未完善';
            $_arr['address'] = $_arr['address']?$_arr['address']:'未完善';
            $_arr['email'] = $_arr['email']?$_arr['email']:'未完善';
            $_arr['enddate_cn'] = user_status($_arr['enddate']);
            $_arr['enddate'] = date('Y-m-d',$_arr['enddate']);
            $_arr['success'] = "1";
            echo json_encode($_arr); exit();
        }elseif ($this->input->post("dosubmit") == 'putpoint'){
            //增加积分
            $fost = $this->input->post();
            $arr = array();
            $arr['success'] = 0;
            //
            $id = intval($fost['id']);
            $num = intval($fost['num']);
            $text = value($fost, 'text');
            $content = "管理员操作";
            $content.= $text?":".$text:"";
            $ret = ES_Apprun_Vip::point(array('id'=>$id), $num, $content);
            if (isset($ret['success']) && $ret['success']) {
                $arr['success'] = 1;
                $arr['point'] = $ret['users']['point'] + $num;
                $arr['message'] = '增加成功';
                echo json_encode($arr); exit();
            }else{
                $arr['message'] = $ret;
                echo json_encode($arr); exit();
            }
        }elseif ($this->input->post("dosubmit") == 'cutpoint'){
            //扣除积分
            $fost = $this->input->post();
            $arr = array();
            $arr['success'] = 0;
            //
            $id = intval($fost['id']);
            $num = intval($fost['num']);
            $text = value($fost, 'text');
            $content = "管理员操作";
            $content.= $text?":".$text:"";
            $ret = ES_Apprun_Vip::point(array('id'=>$id), $num * -1, $content);
            if (isset($ret['success']) && $ret['success']) {
                $arr['success'] = 1;
                $arr['point'] = $ret['users']['point'] - $num;
                $arr['message'] = '扣除成功';
                echo json_encode($arr); exit();
            }else{
                $arr['message'] = $ret;
                echo json_encode($arr); exit();
            }
        }elseif ($this->input->post("dosubmit") == 'putmoney'){
            //增加金额
            $fost = $this->input->post();
            $arr = array();
            $arr['success'] = 0;
            //
            $id = intval($fost['id']);
            $num = doubleval($fost['num']);
            $text = value($fost, 'text');
            $content = "管理员操作";
            $content.= $text?":".$text:"";
            $ret = ES_Apprun_Vip::money(array('id'=>$id), $num, $content);
            if (isset($ret['success']) && $ret['success']) {
                $arr['success'] = 1;
                $arr['money'] = format_user($ret['users']['money'] + $num, 2);
                $arr['message'] = '增加成功';
                echo json_encode($arr); exit();
            }else{
                $arr['message'] = $ret;
                echo json_encode($arr); exit();
            }
        }elseif ($this->input->post("dosubmit") == 'cutmoney'){
            //扣除金额
            $fost = $this->input->post();
            $arr = array();
            $arr['success'] = 0;
            //
            $id = intval($fost['id']);
            $num = doubleval($fost['num']);
            $text = value($fost, 'text');
            $content = "管理员操作";
            $content.= $text?":".$text:"";
            $ret = ES_Apprun_Vip::money(array('id'=>$id), $num * -1, $content, false);
            if (isset($ret['success']) && $ret['success']) {
                $arr['success'] = 1;
                $arr['money'] = format_user($ret['users']['money'] - $num, 2);
                $arr['message'] = '扣除成功';
                echo json_encode($arr); exit();
            }else{
                $arr['message'] = $ret;
                echo json_encode($arr); exit();
            }
        }elseif ($this->input->post("dosubmit") == "excel"){
            //导出会员
            if ($this->input->post('y_id')){
                $y_id = $this->input->post("y_id");
                $wheresql.= " AND `id` IN (".implode(',',$y_id).")";
                $row = $this->ddb->getall("SELECT * FROM ".table("vip_users")." WHERE {$wheresql} ORDER BY `indate` DESC");
            }elseif ($this->input->post('n1')){
                $n1 = intval($this->input->post('n1'));
                if ($n1 > 0) $n1 = $n1 - 1;
                $n2 = intval($this->input->post('n2'));
                if ($n2 > 0) $n2 = $n2 - $n1;
                $limit=" LIMIT ".$n1.','.$n2;
                $row = $this->ddb->getall("SELECT * FROM ".table("vip_users")." WHERE {$wheresql} ORDER BY `indate` DESC".$limit);
            }else{
				message(null, "请选择要导出的会员！"); exit();
            }
            header("Content-Type: text/html; charset=gb2312");
            header("Content-type:application/vnd.ms-execl");
            header("Content-Disposition:filename=execl_users.xls");
            //字段
            $arr = array(
                array('en'=>'card','cn'=>'卡号'),
                array('en'=>'fullname','cn'=>'姓名'),
                array('en'=>'sex','cn'=>'性别'),
                array('en'=>'phone','cn'=>'电话'),
                array('en'=>'address','cn'=>'地址'),
                array('en'=>'money','cn'=>'消费金额'),
                array('en'=>'point','cn'=>'积分'),
                array('en'=>'pluspoint','cn'=>'会员级别'),
                array('en'=>'enddate','cn'=>'到期时间'),
                array('en'=>'email','cn'=>'邮箱'),
                array('en'=>'idnumber','cn'=>'身份证'),
                array('en'=>'type','cn'=>'会员类型'),
                array('en'=>'follow','cn'=>'是否关注'),
            );
            $i=0;
            $fieldCount=count($arr);
            $s=0;
            //thead
            foreach ($arr as $f){
                if ($s<$fieldCount-1){
                    echo iconv('utf-8','gbk',$f['cn'])."\t";
                }else {
                    echo iconv('utf-8','gbk',$f['cn'])."\n";
                }
                $s++;
            }
            if ($row){
                foreach ($row as $sn){
                    $j=0;
                    foreach ($arr as $field){
                        $fieldValue = $sn[$field['en']];
                        switch ($field['en']){
                            default:
                                break;
                            case 'money':
                                $fieldValue=format_user($fieldValue,2);
                                break;
                            case 'pluspoint':
                                $fieldValue=get_user_rank($fieldValue);
                                break;
                            case 'enddate':
                                $fieldValue=user_status($fieldValue);
                                break;
                            case 'type':
                                if ($fieldValue == 'alipay') {
                                    if ($sn['follow'] == '4') {
                                        $fieldValue = '注册会员';
                                    }else{
                                        $fieldValue = '服务窗会员';
                                    }
                                }elseif ($fieldValue == 'weixin') {
                                    if ($sn['follow'] == '3') {
                                        $fieldValue = '借用会员';
                                    }else{
                                        $fieldValue = '微信会员';
                                    }
                                }
                                break;
                            case 'follow':
                                if ($fieldValue == '1') {
                                    $fieldValue = '已关注';
                                }elseif ($fieldValue == '0') {
                                    $fieldValue = '取消关注';
                                }elseif ($fieldValue == '2') {
                                    $fieldValue = '未知';
                                }else{
                                    $fieldValue = '其他';
                                }
                                break;
                        }
                        $fieldValue = $fieldValue?iconv('utf-8','gbk',$fieldValue):$fieldValue;
                        $fieldValue = _csv($fieldValue);
                        if ($j<$fieldCount-1){
                            echo $fieldValue."\t";
                        }else {
                            echo $fieldValue."\n";
                        }
                        $j++;
                    }
                    $i++;
                }
            }
            exit();
		}elseif ($this->input->post("dosubmit") == "updatewx"){
			//同步粉丝信息(微信)
			if ($this->input->post('y_id')){
				$y_id = $this->input->post("y_id");
				$wheresql.= " AND `id` IN (".implode(',',$y_id).")";
				$row = $this->ddb->getall("SELECT * FROM ".table("vip_users")." WHERE {$wheresql} ORDER BY `indate` DESC");
			}else{
				message(null, "请选择要同步的会员！"); exit();
			}
			$this->load->helper('communication');
			$this->load->library('wx');
			$this->wx->setting(intval($_A['al']['id']));
			foreach($row as $item){
				if (strlen($item['openid']) < 20) continue;
				if ($item['type'] == 'weixin') {
					$url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$_A['wx_token']."&openid=".$item['openid']."&lang=zh_CN";
					$resp = ihttp_request($url);
					if(!is_error($resp)) {
						$rethtml = @json_decode($resp['content'], true);
						ES_Apprun_Vip::upto_wxuser($rethtml, $_A['al']['id'], $_A['al']['wx_appid']);
					}
				}
			}
			echo json_encode(array('success'=>1)); exit();
		}elseif ($this->input->post("dosubmit") == "delete"){
			//删除粉丝会员信息
			if ($this->input->post('y_id')){
				$y_id = $this->input->post("y_id");
				$wheresql.= " AND `id` IN (".implode(',',$y_id).")";
				$row = $this->ddb->getall("SELECT * FROM ".table("vip_users")." WHERE {$wheresql} ORDER BY `indate` DESC");
			}else{
				message(null, "请选择要删除的会员！"); exit();
			}
			foreach($row as $item){
				db_query("DELETE FROM ".table("vip_users")." WHERE ".$this->merge(0)." AND `id`=".intval($item['id']));
				db_query("DELETE FROM ".table("vip_record")." WHERE ".$this->merge(0)." AND `userid`=".intval($item['id']));
				db_query("DELETE FROM ".table("fans")." WHERE ".$this->merge(0)." AND `openid`='".$item['openid']."'");
			}
			echo json_encode(array('success'=>1)); exit();
        }
        //
        $this->cs->show(get_defined_vars());
    }

	/**
	 * 下载粉丝
	 */
	public function doWebmemberdown()
	{
		global $_A,$_GPC;

		$this->load->library('wx');
		$this->wx->setting(intval($_A['al']['id']));
		if(!empty($post['next'])) {
			$_GPC['next_openid'] = $post['next'];
		}else{
			db_delete(table('tmp'), array('title'=>"memberdown_".$_A['al']['id']));
		}
		$fans = $this->wx->fansAll();
		if(!is_error($fans) && is_array($fans['fans'])) {
			$count = count($fans['fans']);
			$buffSize = ceil($count / 500);
			$j = 0;
			for($i = 0; $i < $buffSize; $i++) {
				$buffer = array_slice($fans['fans'], $i * 500, 500);
				$openids = implode("','", $buffer);
				$openids = "'{$openids}'";
				$ds = db_getall("SELECT `openid` FROM ".table('fans')." WHERE ".$this->merge(0)." AND `openid` IN ({$openids})");
				$exists = array();
				foreach($ds as $row) {
					$exists[] = $row['openid'];
				}
				$sql = '';
				foreach($buffer as $openid) {
					if(!empty($exists) && in_array($openid, $exists)) {
						continue;
					}
					$j++;
					$sql.= "('memberdown_".$_A['al']['id']."', '{$openid}', '".$j."'),";
				}
				if(!empty($sql)) {
					db_query('INSERT INTO '.table('tmp').' (`title`, `value`, `indate`) VALUES '.rtrim($sql, ','));
				}
			}
			$ret = array();
			$ret['total'] = $fans['total'];
			$ret['count'] = count($fans['fans']) + 2;
			if(!empty($fans['next'])) {
				$ret['next'] = $fans['next'];
			}
			exit(json_encode($ret));
		} else {
			exit(json_encode($fans));
		}
	}

	/**
	 * 同步粉丝信息（新的粉丝）
	 */
	public function doWebinitsync()
	{
		global $_GPC,$_A;
		if(intval($_GPC['page']) == 0) {
			message(null, '正在更新粉丝数据,请不要关闭浏览器', weburl('vip/initsync', array('page' => 1)));
		}
		if(empty($_GPC['indate'])) {
			$_GPC['indate'] = SYS_TIME;
		}
		$pindex = max(1, intval($_GPC['page']));
		$psize = 50;
		$tmp = db_getlist(table('tmp'), array('title'=>"memberdown_".$_A['al']['id']), '`indate` ASC', $psize, $pindex);
		$total_page = $tmp['totalpage'];
		$ds = $tmp['list'];
		if(!empty($ds)) {
			$this->load->helper('communication');
			$this->load->library('wx');
			$this->wx->setting(intval($_A['al']['id']));
			foreach($ds as $row) {
				if (empty($row['value']) || strlen($row['value']) < 20) continue;
				$fan = ihttp_request("https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$_A['wx_token']."&openid=".$row['value']."&lang=zh_CN");
				if(!is_error($fan)) {
					$rethtml = @json_decode($fan['content'], true);
					if (!empty($rethtml['openid'])) {
						ES_Apprun_Vip::upto_wxuser($rethtml, $_A['al']['id'], $_A['al']['wx_appid']);
					}
				}
			}
		}
		$pindex++;
		$log = ($pindex - 1) * $psize;
		if($pindex > $total_page) {
			db_delete(table('tmp'), array('title'=>"memberdown_".$_A['al']['id']));
			$link = array();
			$link[0] = array(
				'title'=>'如果你的浏览器没有自动跳转，请点击此链接',
				'href'=>weburl('vip/initsyncbefore', array('indate'=>$_GPC['indate'], 'belog' => $tmp['total']))
			);
			$link[1] = array(
				'title'=>'如果想取消数据更新并返回会员列表，请点击此链接',
				'href'=>weburl('vip/member')
			);
			message(null, '正在更新粉丝数据,请不要关闭浏览器,已完成更新 '.$tmp['total'].' 条数据。', $link, $link[0]['href']);
		} else {
			$link = array();
			$link[0] = array(
				'title'=>'如果你的浏览器没有自动跳转，请点击此链接',
				'href'=>weburl('vip/initsync', array('indate'=>$_GPC['indate'],'page' => $pindex))
			);
			$link[1] = array(
				'title'=>'如果想取消数据更新并返回会员列表，请点击此链接',
				'href'=>weburl('vip/member')
			);
			message(null, '正在更新粉丝数据,请不要关闭浏览器,已完成更新 '.$log.' 条数据。', $link, $link[0]['href']);
		}
	}

	/**
	 * 同步粉丝信息 (以前的粉丝会员)
	 */
	public function doWebinitsyncbefore()
	{
		global $_GPC,$_A;
		if(empty($_GPC['indate'])) {
			message(null, '参数丢失！', weburl('vip/member'));
		}
		$belog = intval($_GPC['belog']);
		$pindex = max(1, intval($_GPC['page']));
		$psize = 50;
		$tmp = db_getlist(table('fans'), "`type`='weixin' AND ".$this->merge(0)." AND `indate`<".$_GPC['indate'], '`indate` DESC', $psize, $pindex);
		$total_page = $tmp['totalpage'];
		$ds = $tmp['list'];
		if(!empty($ds)) {
			$this->load->helper('communication');
			$this->load->library('wx');
			$this->wx->setting(intval($_A['al']['id']));
			foreach($ds as $row) {
				if (empty($row['openid']) || strlen($row['openid']) < 20) continue;
				$fan = ihttp_request("https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$_A['wx_token']."&openid=".$row['openid']."&lang=zh_CN");
				if(!is_error($fan)) {
					$rethtml = @json_decode($fan['content'], true);
					if (!empty($rethtml['openid'])) {
						ES_Apprun_Vip::upto_wxuser($rethtml, $_A['al']['id'], $_A['al']['wx_appid']);
					}
				}
			}
		}
		$pindex++;
		$log = ($pindex - 1) * $psize;
		if($pindex > $total_page) {
			message(null, '粉丝数据更新完成', weburl('vip/member'));
		} else {
			$link = array();
			$link[0] = array(
				'title'=>'如果你的浏览器没有自动跳转，请点击此链接',
				'href'=>weburl('vip/initsyncbefore', array('indate'=>$_GPC['indate'], 'page' => $pindex, 'belog' => $belog))
			);
			$link[1] = array(
				'title'=>'如果想取消数据更新并返回会员列表，请点击此链接',
				'href'=>weburl('vip/member')
			);
			message(null, '正在更新粉丝数据,请不要关闭浏览器,已完成更新 '.($log + $belog).' 条数据。', $link, $link[0]['href']);
		}
	}


    /**
     * 会员详情（查看积分详情）
     */
    public function doWebmemberdetail()
    {
        global $_GPC;
        $page = intval($_GPC['param'][1]);

        $card = $this->input->get('card');
        $date = $this->input->get('date');
        if ($card <= 0) exit("No userid");
        if (empty($date)) $date = date("Y-m",time());
        $row = $this->ddb->getone("SELECT * FROM ".table('vip_users'), $this->merge(array('card'=>$card)));
        if (!$row) exit("Err userid");
        //
        $wheresql = $this->merge(0)." AND `card`='".$row['card']."'";
        if ($date != '-1') {
            $wheresql.= "AND `indate_cn` LIKE '".$date."%'";
        }

        $user = $row;

        $ddate = array();
        for($a=date("m",time()); $a>0; $a--){
            $ddate[] = date("Y-",time()).substr('0'.$a, -2);
        }
        $pageurl = urlencode($this->base->url[3]);
        //
        $this->cs->show(get_defined_vars());
    }

    /**
     * 删除会员
     */
    public function doWebmemberdel()
    {
        $arr = array();
        $arr['success'] = 1;

        $y_id = $this->input->post("id");
        if (!empty($y_id)){
            $y_id = trim($y_id, ',');
			$row = $this->ddb->getall("SELECT * FROM ".table("vip_users")." WHERE ".$this->merge(0)." AND `id` IN (".$y_id.")");
			foreach($row as $item){
				db_query("DELETE FROM ".table("vip_users")." WHERE ".$this->merge(0)." AND `id`=".intval($item['id']));
				db_query("DELETE FROM ".table("vip_record")." WHERE ".$this->merge(0)." AND `userid`=".intval($item['id']));
				db_query("DELETE FROM ".table("fans")." WHERE ".$this->merge(0)." AND `openid`='".$item['openid']."'");
			}
        }
        echo json_encode($arr); exit();
    }

    /**
     * 店员管理
     */
    public function doWebstaff()
    {
        global $_GPC;
        $page = intval($_GPC['param'][1]);
        if ($this->input->post("dosubmit")){
            $fost = $this->input->post();
            $arr = $_arr = array();
            $arr['success'] = 0;
            if ($fost["dosubmit"] == 'put'){
                //添加店员
                $id = intval($fost["id"]);
                $_arr = array();
                $_arr['fullname'] = $fost["fullname"];
                $_arr['phone'] = $fost["phone"];
                $_arr['address'] = $fost["address"];
                $_arr['username'] = $fost["username"];
                $_arr['userpass'] = $fost["userpass"];
                $_arr['shopid'] = $fost["shopid"];
                $_arr['enddate'] = $fost["enddate"];
                $_arr['enddate'] = ($_arr['enddate']>0)?strtotime($_arr['enddate']):0;
                if (empty($_arr['fullname'])){
                    $arr['message'] = '请输入姓名';
                    echo json_encode($arr); exit();
                }
                if (empty($_arr['username'])){
                    $arr['message'] = '请输入用户名';
                    echo json_encode($arr); exit();
                }
                if (empty($_arr['userpass'])){
                    $arr['message'] = '请输入密码';
                    echo json_encode($arr); exit();
                }
                //检测密码重复
                $wheresql = " WHERE ".$this->merge(0);
                $wheresql.= " AND `userpass`='".$_arr['userpass']."'";
                $wheresql.= ($id > 0)?' AND `id`!='.$id:'';
                $row = $this->ddb->getone("SELECT * FROM ".table('vip_shop_users').$wheresql);
                if (!empty($row)){
                    $arr['message'] = '密码重复，请重新输入密码';
                    echo json_encode($arr); exit();
                }
                if ($id > 0){
                    $this->ddb->update(table('vip_shop_users'), $_arr, $this->merge(array("id"=>$id)));
                    $arr['success'] = "1";
                    $arr['message'] = '修改成功';
                }else{
                    $this->ddb->insert(table('vip_shop_users'), $this->merge($_arr));
                    $arr['success'] = "1";
                    $arr['message'] = '添加成功';
                }
                echo json_encode($arr); exit();
            }elseif ($fost["dosubmit"] == 'del'){
                //删除店员
                $y_id = $this->input->post("id");
                if (!empty($y_id)){
                    $y_id = trim($y_id, ',');
                    $this->ddb->query("DELETE FROM ".table("vip_shop_users")." WHERE ".$this->merge(0)." AND `id` IN (".$y_id.")");
                }
                $arr['success'] = 1;
                echo json_encode($arr); exit();
            }elseif ($fost["dosubmit"] == 'info'){
                //获取信息
                $row = $this->ddb->getone("SELECT * FROM ".table('vip_shop_users'), $this->merge(array('id'=>intval($this->input->post("id")))));
                if ($row){
                    $row['success'] = 1;
                    $row['enddate'] = ($row['enddate']>0)?date('Y-m-d', $row['enddate']):0;
                }else{
                    $row['success'] = 0;
                }
                echo json_encode($row); exit();
            }
        }
        $wheresql = $this->merge(0);
        $pageurl = urlencode($this->base->url[3]);
        $sqlarr = $this->merge();
        //
        $shop = $this->ddb->getall("SELECT * FROM ".table('vip_shop'), $this->merge(), "`id` desc");
        //
        $this->cs->show(get_defined_vars());
    }

    /**
     * 店员操作记录
     */
    public function doWebstaffdetail()
    {
        global $_GPC;
        $page = intval($_GPC['param'][1]);
        if ($this->input->post("dosubmit")){
            $fost = $this->input->post();
            $arr = $_arr = array();
            $arr['success'] = 0;
            if ($fost["dosubmit"] == 'del'){
                //删除记录
                $y_id = $this->input->post("id");
                if (!empty($y_id)){
                    $y_id = trim($y_id, ',');
                    $this->ddb->query("DELETE FROM ".table("vip_content_notes")." WHERE ".$this->merge(0)." AND `id` IN (".$y_id.")");
                }
                $arr['success'] = 1;
                echo json_encode($arr); exit();
            }
        }
        $id = $this->input->get('id');
        if ($id <= 0) exit("No id");
        $row = $this->ddb->getone("SELECT * FROM ".table('vip_shop_users'), $this->merge(array('id'=>$id)));
        if (!$row) exit("Err id");
        //
        $wheresql = $this->merge(0)." AND `operatorid`='".$row['id']."'";
        $user = $row;
        $pageurl = urlencode($this->base->url[3]);
        //
        $this->cs->show(get_defined_vars());
    }

    /**
     * VIP卡设置
     */
    public function doWebsetting()
    {
        global $_A;
        $warr = array();
        $warr['module'] = 'vip';
        $warr['alid'] = $_A['al']['id'];
        $row = $this->ddb->getone("SELECT * FROM ".table('reply'), $warr);
        if (empty($row)){
            $warr['status'] = '启用';
            $warr['indate'] = SYS_TIME;
            $this->ddb->insert(table('reply'), $warr);
            gourl(weburl());
        }else{
            $row['key'] = trim($row['key'], ',');
        }
        $row['content'] = string2array($row['content']);
        $row['setting'] = string2array($row['setting']);
        $row['setting']['link'] = array_values($row['setting']['link']);
        //
        if ($this->input->post("dosubmit")){
            $fost = $this->input->post();
            $arr = $_arr = array();
            $arr['success'] = 0;
            //
            $_arr['status'] = $fost['status'];
            $_arr['match'] = $fost['match'];
            $_arr['key'] = ','.str_replace(array(' ','，'), ',', $fost['key']).',';
            $_arr['title'] = value($fost, 'title');
            $_arr['content'] = array2string(value($fost, 'content', true));
            $_arr['setting'] = array2string(value($fost, 'setting', true));
            $_arr['type'] = value($fost, 'content|type');
            $_arr['update'] = SYS_TIME;
            //
            if (empty($_arr['key'])){
                $arr['message'] = '关键词不能为空';
                echo json_encode($arr); exit();
            }
            if ($this->ddb->update(table('reply'), $_arr, array('id'=>$row['id']))){
                $arr['success'] = 1;
                $arr['message'] = '保存成功';
                $row = $this->ddb->getone("SELECT userdata FROM ".table("users_functions"), array('id'=>$_A['uf']['id']));
                $userdata = string2array($row['userdata']);
                $userdata['userset'] = value($fost, 'setting', true);
                $this->ddb->update(table("users_functions"), array('userdata'=>array2string($userdata)), array("id"=>$_A['uf']["id"]));
            }else{
                $arr['message'] = '保存失败';
            }
            echo json_encode($arr); exit();
        }
        $reply = $row;
        //
        $this->cs->show(get_defined_vars());
    }


	/** *************************************************************************************** */
	/** *************************************************************************************** */
	/** *************************************************************************************** */
	/** *************************************************************************************** */

	/**
	 * app前执行
	 */
	public function doMobile() {
		global $_A;
        if (!isset($_A['vip']['card'])) { message("温馨提示", "系统繁忙-no-user！"); }
        //
        $row = $this->ddb->getone("SELECT userdata FROM ".table("users_functions"), array('id'=>$_A['uf']['id']));
        $_A['vip_data'] = string2array($row['userdata']);
        if ($_A['vip_data']['userset']['haveinfo'] == '强制' && $_A['segments']['3'] != 'personal') {
            foreach($_A['vip_data']['userset']['haveitem'] AS $key => $item) {
                if ($item != 'code' && isset($_A['vip_data']['userset']['showitem'][$key]) && $key == $item) {
                    if (empty($_A['vip'][$key])) {
                        gourl(appurl('vip/personal')."&referer=".urlencode(get_url()));
                    }
                }
            }
        }
	}

	/**
	 * 欢迎页
	 */
	public function doMobileWelcome()
	{
		//跳转至首页
		gourl(appurl('vip/index'));
	}


	/**
	 * 会员卡首页
	 */
	public function doMobileIndex()
	{
		global $_A;
		//卡面信息
		$hykbm = $_A['vip_data']['hykbm'];
        $hykbm['bgimgduv'] = value($hykbm,'bgimgduv');
        if (empty($hykbm['bgimgduv'])){
            $hykbm['bgimgduv'] = BASE_DIR."uploadfiles/vipcardbg/1.jpg";
        }
        if (value($hykbm,'bgimg') != ""){
            $hykbm['bgimgduv'] = BASE_DIR."uploadfiles/vipcardbg/".$hykbm['bgimg'];
        }
		//商店信息
		$shopset = $_A['vip_data']['shop'];
		//获取签到情况
		$sign = $this->ddb->getone("SELECT * FROM ".table('vip_point_notes'),
			$this->merge(array('type'=>'sign', 'indate_cn'=>date("Y-m-d",SYS_TIME), 'card'=>$_A['vip']['card'])));
		//获取通知数量
		$msg_num = $this->ddb->get_total("SELECT COUNT(*) AS num FROM ".table('vip_record'),
			$this->merge(array('type'=>'msg', 'view'=>'0', 'userid'=>$_A['vip']['id'])));
		//获取特权数量
		$vip_num = $this->ddb->get_total("SELECT COUNT(*) AS num FROM ".table('vip_record'),
			$this->merge(array('type'=>'vip', 'view'=>'0', 'userid'=>$_A['vip']['id'])));
		//获取优惠券数量
		$total_sql = "SELECT num,usenum FROM ".table('vip_record')." WHERE `type`='cut' AND `num`>`usenum` AND `userid`=".$_A['vip']['id']."";
		$total_sql.= " AND ".$this->merge(0);
		$giftrow = $this->ddb->getall($total_sql);
		$cut_num = 0;
		if (!empty($giftrow) && is_array($giftrow)){
			foreach($giftrow as $n){
				$cut_num+= $n['num']-$n['usenum'];
			}
		}
		//获取礼品券数量
		$gift_num = $this->ddb->get_total("SELECT COUNT(*) AS num FROM ".table('vip_record'),
			$this->merge(array('type'=>'gift', 'usenum'=>'0', 'userid'=>$_A['vip']['id'])));
		//
        $rwheresql = '`alid`='.$_A['vip']['alid'].' AND `vip_link`=1';
		$this->cs->show(get_defined_vars());
	}

    /**
     * 金额 积分 使用记录
     */
    public function doMobileRecord()
    {
        global $_A,$_GPC;
        $page = intval($_GPC['param'][2]);
        $pageurl = urlencode($this->base->url[3]);
        //
        $wheresql = $this->merge(0)." AND `card`='".$_A['vip']['card']."'";
        if ($_GPC['param'][1] == 'point') {
            $wheresql.= " AND `money`=0 AND `outmoney`=0 ";
            $type = "point";
            $type_cn = "积分";
        }else {
            $wheresql.= " AND (`money`!=0 OR `outmoney`!=0) ";
            $type = "money";
            $type_cn = "现金";
        }
        $pageurl.= $type."/";
        $this->cs->show(get_defined_vars());
    }

    /**
     * 余额充值
     */
    public function doMobileCharge()
    {
        global $_A;
        $type = $this->input->get('type');
        $cha_num = doubleval($this->input->get('cha_num'));
        if ($cha_num < 0.01) {
            message(null, "单笔充值金额不得少于0.01元！");
        }
        if ($type == "alipay") {
            $parr = array();
            $parr['userid'] = $_A['vip']['id'];
            $parr['type'] = $type;
            $parr['status'] = 0;
            $parr['num'] = $cha_num;
            $parr['indate'] = SYS_TIME;
            //删除【一个月以上的、已经完成的、跟本次一样的】单子
            $delwhere = "`userid`=".$_A['vip']['id'];
            $delwhere.= " AND (`indate`<".intval(SYS_TIME - 604800)." OR `status`=1)";
            db_query("DELETE FROM ".table('vip_pay')." WHERE ".$delwhere);
            //添加单子
            $payid = $this->ddb->insert(table('vip_pay'), $parr, true);
            if ($payid) {
                $params = array();
                $params['tid'] = $payid;
                $params['user'] = $_A['vip']['fullname'];
                $params['fee'] =$cha_num;
                $params['title'] = $_A['al']['wx_name']?$_A['al']['wx_name']:$_A['al']['al_name'];
                $params['ordersn'] = $parr['cartmd5'];
                $params['virtual'] = false;
                $params['module'] = 'vip';
                $params['tag'] = "充值 - 会员卡余额";
                //
                $pars = array();
                $pars['alid'] = $_A['al']['id'];
                $pars['module'] = 'vip';
                $pars['tid'] = $params['tid'];
                db_delete(table('core_paylog'), $pars);
                //
                gourl(systemurl('payment/alipay').'&params='.base64_encode(json_encode($params)));
            }else{
                message(null, "系统繁忙，请稍后再试...");
            }
        }elseif ($type == "weixin") {
			$parr = array();
			$parr['userid'] = $_A['vip']['id'];
			$parr['type'] = $type;
			$parr['status'] = 0;
			$parr['num'] = $cha_num;
			$parr['indate'] = SYS_TIME;
			//删除【一个月以上的、已经完成的、跟本次一样的】单子
			$delwhere = "`userid`=".$_A['vip']['id'];
			$delwhere.= " AND (`indate`<".intval(SYS_TIME - 604800)." OR `status`=1)";
			db_query("DELETE FROM ".table('vip_pay')." WHERE ".$delwhere);
			//添加单子
			$payid = $this->ddb->insert(table('vip_pay'), $parr, true);
			if ($payid) {
				$params = array();
				$params['tid'] = $payid;
				$params['user'] = $_A['vip']['fullname'];
				$params['fee'] =$cha_num;
				$params['title'] = $_A['al']['wx_name']?$_A['al']['wx_name']:$_A['al']['al_name'];
				$params['ordersn'] = $parr['cartmd5'];
				$params['virtual'] = false;
				$params['module'] = 'vip';
				$params['tag'] = "充值 - 会员卡余额";
				//
				$pars = array();
				$pars['alid'] = $_A['al']['id'];
				$pars['module'] = 'vip';
				$pars['tid'] = $params['tid'];
				db_delete(table('core_paylog'), $pars);
				//
				gourl(systemurl('payment/weixin').'&params='.base64_encode(json_encode($params)));
			}else{
				message(null, "系统繁忙，请稍后再试...");
			}
		}else{
            message("参数错误");
        }
    }

    /**
     * 充值返回
     * @param $params
     */
    public function payResult($params)
    {
        $payid = $params['tid'];
        //检测订单是否正常
        $row = db_getone("SELECT * FROM ".table('vip_pay'), array('id'=>intval($payid)));
        if (empty($row)) {
            message('订单已完成，请勿刷新。');
        }
        if ($row['status'] == 0) {
            if ($row['type'] == "alipay") {
                $content = "支付宝充值";
            }elseif ($row['type'] == "weixin") {
				$content = "微信充值";
			}else{
                $content = "充值";
            }
            ES_Apprun_Vip::money(array('id'=>$row['userid']), $row['num'], $content);
            db_update(table('vip_pay'), array('status'=>'1'), array('id'=>$row['id'])); //标记成功
        }
        //
        message('充值成功', '充值成功！', appurl('vip/record/money'));
    }
    /**
     * 签到
     */
    public function doMobileSign()
    {
        global $_A;
        //获取签到情况
        $sign = $this->ddb->getone("SELECT * FROM ".table('vip_point_notes'),
            $this->merge(array('type'=>'sign', 'indate_cn'=>date("Y-m-d",SYS_TIME), 'card'=>$_A['vip']['card'])));
        //积分策略
        $jfcl = $_A['vip_data']['jfcl'];
        //
        if ($this->input->post("dosubmit")){
            $arr = $_arr = array();
            $arr['success'] = 0;
            //
            if ($sign){
                $arr['message'] = "今日已经签到！";
                echo json_encode($arr); exit();
            }
            $_arr['type'] = 'sign'; //签到
            $_arr['card'] = $_A['vip']['card'];
            $_arr['openid'] = $_A['vip']['openid'];
            $_arr['point'] = $_A['vip']['point'];
            $_arr['outpoint'] = intval($jfcl['meiri']);
            $_arr['content'] = "每日签到奖励";
            $_arr['indate'] = SYS_TIME;
            $_arr['indate_cn'] = date("Y-m-d",SYS_TIME);
            if ($this->ddb->insert(table('vip_point_notes'), $this->merge($_arr))){
                //更新到会员信息
                $this->ddb->query("UPDATE ".table('vip_users')." SET ".point_field($_arr['outpoint'],'inpoint')." WHERE ".$this->merge(0)." AND `card`='".$_A['vip']['card']."'");
                //判断连续签到
                $lianxu = intval($jfcl['lianxu']);
                if ($lianxu > 0){
                    $ttime = strtotime(date("Y-m-d", strtotime("-".intval($lianxu-1)." days")));
                    $lxrow = $this->ddb->getone("SELECT COUNT(*) AS num FROM ".table('vip_point_notes')." WHERE ".$this->merge(0)." AND `type`='sign' AND `indate`>=".$ttime." AND `card`='".$_A['vip']['card']."'");
                    $llrow = $this->ddb->getone("SELECT COUNT(*) AS num FROM ".table('vip_point_notes')." WHERE ".$this->merge(0)." AND `type`='signs' AND `indate`>=".$ttime." AND `card`='".$_A['vip']['card']."'");
                    if ($lxrow['num'] >= $lianxu && $llrow['num'] <= 0){ // 条件：X天内签到数大于等于X条 AND X天内没有活得过连续签到奖励
                        $_arr = array();
                        $_arr['type'] = 'signs'; //连续签到
                        $_arr['card'] = $_A['vip']['card'];
                        $_arr['openid'] = $_A['vip']['openid'];
                        $_arr['point'] = $_A['vip']['point'] + $_arr['outpoint'];
                        $_arr['outpoint'] = intval($jfcl['lianxuval']);
                        $_arr['content'] = "连续签到{$jfcl['lianxu']}天奖励";
                        $_arr['indate'] = SYS_TIME;
                        $_arr['indate_cn'] = date("Y-m-d",SYS_TIME);
                        $this->ddb->insert(table('vip_point_notes'), $this->merge($_arr));
                        //更新到会员信息
                        $this->ddb->query("UPDATE ".table('vip_users')." SET ".point_field($_arr['outpoint'],'inpoint')." WHERE ".$this->merge(0)." AND `card`='".$_A['vip']['card']."'");
                    }
                }
                $arr['success'] = 1;
                $arr['message'] = "签到成功！";
            }else{
                $arr['message'] = "签到失败！";
            }
            echo json_encode($arr); exit();
        }

        //获取签到列表
        $ttime = strtotime(date('Y-m-01',SYS_TIME));
        $list = $this->ddb->getlist(table("vip_point_notes"),
            $this->merge(0)." AND `type` in ('sign','signs') AND `indate`>=".$ttime." AND `card`='".$_A['vip']['card']."'", "`indate` DESC", 31);
        $_arr = $_arrs = array();
        foreach($list['list'] as $item) {
            if ($item['type'] == 'signs'){
                $_arrs[$item['indate_cn']] = $item;
            }else{
                $_arr[$item['indate_cn']] = $item;
            }
        }
        foreach($_arrs as $item) {
            $_arr[$item['indate_cn']]['outpoint']+= $item['outpoint'];
        }
        $list['sub'] = $_arr;
        //
        $y = date("Y", SYS_TIME);
        $m = date("m", SYS_TIME);
        $d = intval(date("d", SYS_TIME));
        $z = 0;
        $sublist = array();
        for ($d; $d>0; $d--){
            $_d = substr("0".$d,-2);
            $ymd = $y."-".$m."-".$_d;
            if (isset($list['sub'][$ymd])){
                $sublist[] = array(
                    'a'=>$m."-".$_d,
                    'b'=>value($list['sub'], $ymd.'|outpoint'),
                );
                $z+= value($list['sub'], $ymd.'|outpoint');
            }
        }
        $sublistz = $z;
        //
        $this->cs->show(get_defined_vars());
    }


    /**
     * 会员卡说明
     */
    public function doMobileAbout()
    {
        global $_A;
        //商店信息
        $shopset = $_A['vip_data']['shop'];
        //积分等级
        $jfcl = $_A['vip_data']['jfcl'];
        //卡面说明
        $hyksm = $_A['vip_data']['hyksm'];
        //
        $this->cs->show(get_defined_vars());
    }

    /**
     * 分店列表
     */
    public function doMobileShop()
    {
        $shop = $this->ddb->getall("SELECT * FROM ".table('vip_shop'), $this->merge(), "`id` DESC");
        //
        $this->cs->show(get_defined_vars());
    }

    /**
     * 通知列表
     */
    public function doMobileNotification()
    {
        global $_A,$_GPC;
        $page = intval($_GPC['page']);
        $table = table("vip_content")." AS a,".table("vip_record")." AS b";
        $wheresql = " b.type='msg' AND b.userid=".$_A['vip']['id']." AND a.id=b.contentid ";
        $wheresql.= " AND a.alid=".$_A['al']['id'];
        $lists = $this->ddb->getlist($table, $wheresql, "a.inorder DESC,a.indate DESC", 10, ($page > 0)?$page:1);
        //
        $this->cs->show(get_defined_vars());
    }


    /**
     * 特权列表 (使用特权)
     */
    public function doMobilePrivilege()
    {
        global $_A,$_GPC;
        $page = intval($_GPC['page']);
        if ($this->input->post("dosubmit")){
            $arr = $_arr = array();
            $arr['success'] = 0;
            //
            $point = $this->input->post("point");
            $pass = $this->input->post("pass");
            $sn = $this->input->post("sn");

            $arr['tnum'] = 0; //返回使用次数
            $arr['message'] = "";
            $arr['tpoint'] = 0; //返回赠送积分
            $sprow = $this->ddb->getone("SELECT * FROM ".table('vip_shop_users')." WHERE ".$this->merge(0)." AND `userpass`='{$pass}'");
            if (!$sprow){
                $shopset = $_A['vip_data']['shop'];
                if ($shopset['pass'] != $pass){
                    $arr['message'] = "管理员密码错误！";
                    echo json_encode($arr); exit();
                }
            }else{
                if ($sprow['enddate'] < time() && $sprow['enddate'] > 0){
                    $arr['message'] = "管理员密码已过期！";
                    echo json_encode($arr); exit();
                }
            }
            $snrow = $this->ddb->getone("SELECT * FROM ".table('vip_record')." WHERE ".$this->merge(0)." AND `type`='vip' AND `sn`='{$sn}'");
            if (!$snrow){
                $arr['message'] = "本条(SN)特权不存在！";
                echo json_encode($arr); exit();
            }
            $conrow = $this->ddb->getone("SELECT * FROM ".table('vip_content')." WHERE ".$this->merge(0)." AND `type`='vip' AND `id`=".$snrow['contentid']);
            if (!$conrow){
                $arr['message'] = "本条(原)特权不存在！";
                echo json_encode($arr); exit();
            }
            if ($conrow['startdate'] > time() || $conrow['enddate'] < time()){
                $arr['message'] = "此特权不在可使用期内！";
                echo json_encode($arr); exit();
            }
            //计算需要的积分
            if ($point != 0){
                $arr['tpoint'] = $point;
            }
            //获取会员信息
            $userrow = $this->ddb->getone("SELECT * FROM ".table('vip_users')." WHERE ".$this->merge(0)." AND `id`=".$snrow['userid']);
            //开始提交
            $conarr = array();
            $conarr['type'] = $conrow['type'];
            $conarr['contentid'] = $conrow['id'];
            $conarr['contenttitle'] = $conrow['title'];
            $conarr['sn'] = $sn;
            $conarr['card'] = $userrow['card'];
            $conarr['fullname'] = $userrow['fullname'];
            if ($sprow){
                $conarr['operator'] = $sprow['fullname'];
                $conarr['operatorid'] = $sprow['id'];
            }else{
                $conarr['operator'] = "商家管理员";
                $conarr['operatorid'] = 0;
            }
            $conarr['money'] = 0;
            $conarr['point'] = $arr['tpoint'];
            $conarr['usenum'] = $snrow['usenum'] + 1;
            $conarr['indate'] = SYS_TIME;
            $notesid = $this->ddb->insert(table('vip_content_notes'), $this->merge($conarr));
            if ($notesid < 1){
                $arr['message'] = "提交失败，请稍后再试！";
                echo json_encode($arr); exit();
            }
            if ($arr['tpoint'] != 0){
                //增加(减少)积分到会员账号
                $unsql = "`point`=`point`+".$arr['tpoint']."";
                if ($arr['tpoint'] > 0){
                    $unsql.= ",`pluspoint`=`pluspoint`+".$arr['tpoint']."";
                    $this->session->unset_userdata('_VIP_GET_CON');
                }
                $this->ddb->query("UPDATE ".table('vip_users')." SET {$unsql} WHERE ".$this->merge(0)." AND `id`='".$userrow['id']."'");
                //增加积分到记录
                $poiarr = array();
                $poiarr['type'] = 'privilege';
                $poiarr['card'] = $userrow['card'];
                $poiarr['openid'] = $userrow['openid'];
                $poiarr['point'] = $userrow['point'];
                $poiarr['outpoint'] = $arr['tpoint'];
                $poiarr['content'] = "使用特权";
                $poiarr['indate'] = SYS_TIME;
                $poiarr['indate_cn'] = date("Y-m-d", SYS_TIME);
                $this->ddb->insert(table('vip_point_notes'), $this->merge($poiarr));
            }
            $arr['success'] = 1;
            $arr['tnum'] = $conarr['usenum'];
            $this->ddb->query("UPDATE ".table('vip_users')." SET `ofdate`=".SYS_TIME." WHERE ".$this->merge(0)." AND `id`='".$userrow['id']."'");
            $this->ddb->query("UPDATE ".table('vip_record')." SET `usenum`=`usenum`+1 WHERE ".$this->merge(0)." AND `id`=".$snrow['id']);
            $this->ddb->query("UPDATE ".table('vip_content')." SET `usenum`=`usenum`+1 WHERE ".$this->merge(0)." AND `id`=".$conrow['id']);
            echo json_encode($arr); exit();
        }
        $table = table("vip_content")." AS a,".table("vip_record")." AS b";
        $wheresql = " b.type='vip' AND b.userid=".$_A['vip']['id']." AND a.id=b.contentid ";
        $wheresql.= " AND a.alid=".$_A['al']['id'];
        $lists = $this->ddb->getlist($table, $wheresql, "a.inorder DESC,a.indate DESC", 10, ($page > 0)?$page:1);
        //
        $this->cs->show(get_defined_vars());
    }


    /**
     * 优惠券列表 (使用优惠券)
     */
    public function doMobileCoupon()
    {
        global $_A,$_GPC;
        $page = intval($_GPC['page']);
        if ($this->input->post("dosubmit")){
            $arr = $_arr = array();
            $arr['success'] = 0;
            //
            $money = $this->input->post("money");
            $pass = $this->input->post("pass");
            $sn = $this->input->post("sn");

            $arr['tnum'] = 0; //返回可用数量
            $arr['message'] = "";
            $arr['tpoint'] = 0; //返回赠送积分
            $arr['money'] = $money; //返回还需付款数量
            $sprow = $this->ddb->getone("SELECT * FROM ".table('vip_shop_users')." WHERE ".$this->merge(0)." AND `userpass`='{$pass}'");
            if (!$sprow){
                $shopset = $_A['vip_data']['shop'];
                if ($shopset['pass'] != $pass){
                    $arr['message'] = "管理员密码错误！";
                    echo json_encode($arr); exit();
                }
            }else{
                if ($sprow['enddate'] < time() && $sprow['enddate'] > 0){
                    $arr['message'] = "管理员密码已过期！";
                    echo json_encode($arr); exit();
                }
            }
            $snrow = $this->ddb->getone("SELECT * FROM ".table('vip_record')." WHERE ".$this->merge(0)." AND `type`='cut' AND `sn`='{$sn}'");
            if (!$snrow){
                $arr['message'] = "本条(SN)优惠券不存在！";
                echo json_encode($arr); exit();
            }
            $conrow = $this->ddb->getone("SELECT * FROM ".table('vip_content')." WHERE ".$this->merge(0)." AND `type`='cut' AND `id`=".$snrow['contentid']);
            if (!$conrow){
                $arr['message'] = "本条(原)优惠券不存在！";
                echo json_encode($arr); exit();
            }
            if ($conrow['startdate'] > time() || $conrow['enddate'] < time()){
                $arr['message'] = "此优惠券不在可使用期内！";
                echo json_encode($arr); exit();
            }
            if ($snrow['num'] <= $snrow['usenum']){
                $arr['message'] = "此优惠券已使用完毕！";
                echo json_encode($arr); exit();
            }
            //计算赠送的积分
            $jfcl = $_A['vip_data']['jfcl'];
            $xiaofei = $jfcl['xiaofei'];
            $jiangli = $jfcl['jiangli'];
            if ($xiaofei > 0 && $jiangli > 0){
                $arr['tpoint'] = intval(($money/$xiaofei)*$jiangli);
            }
            //计算还需付款金额
            if ($conrow['type_b']=='打折优惠券' && $conrow['int_a']>0){
                $arr['money'] = $money * ($conrow['int_a'] * 0.01);
            }elseif ($conrow['type_b']=='现金抵用券' && $conrow['int_b']>0){
                $arr['money'] = $money - $conrow['int_b'];
            }else{
                $arr['message'] = "错误的优惠券！";
                echo json_encode($arr); exit();
            }
            //获取会员信息
            $userrow = $this->ddb->getone("SELECT * FROM ".table('vip_users')." WHERE ".$this->merge(0)." AND `id`=".$snrow['userid']);
            //开始提交
            $conarr = array();
            $conarr['type'] = $conrow['type'];
            $conarr['contentid'] = $conrow['id'];
            $conarr['contenttitle'] = $conrow['title'];
            $conarr['sn'] = $sn;
            $conarr['card'] = $userrow['card'];
            $conarr['fullname'] = $userrow['fullname'];
            if ($sprow){
                $conarr['operator'] = $sprow['fullname'];
                $conarr['operatorid'] = $sprow['id'];
            }else{
                $conarr['operator'] = "商家管理员";
                $conarr['operatorid'] = 0;
            }
            $conarr['money'] = $money;
            $conarr['point'] = $arr['tpoint'];
            $conarr['usenum'] = $snrow['usenum'] + 1;
            $conarr['indate'] = SYS_TIME;
            $notesid = $this->ddb->insert(table('vip_content_notes'), $this->merge($conarr));
            if ($notesid < 1){
                $arr['message'] = "提交失败，请稍后再试！";
                echo json_encode($arr); exit();
            }
            if ($arr['tpoint'] != 0){
                //增加积分到会员账号
                $upsql = point_field($arr['tpoint'],'outpoint').",`money`=`money`+".$arr['money'];
                if ($arr['money'] > $userrow['moneyone']){
                    $upsql.= ",`moneyone`=".$arr['money'];
                }
                $this->ddb->query("UPDATE ".table('vip_users')." SET {$upsql} WHERE ".$this->merge(0)." AND `id`='".$userrow['id']."'");
                //增加积分到记录
                $poiarr = array();
                $poiarr['type'] = 'coupon';
                $poiarr['card'] = $userrow['card'];
                $poiarr['openid'] = $userrow['openid'];
                $poiarr['money'] = 0;
                $poiarr['outmoney'] = $arr['money'];
                $poiarr['point'] = $userrow['point'];
                $poiarr['outpoint'] = $arr['tpoint'];
                $poiarr['content'] = "使用优惠券消费获得积分奖励";
                $poiarr['indate'] = SYS_TIME;
                $poiarr['indate_cn'] = date("Y-m-d",SYS_TIME);
                $this->ddb->insert(table('vip_point_notes'), $this->merge($poiarr));
            }
            //
            $arr['success'] = 1;
            $arr['tnum'] = $snrow['num']-$snrow['usenum']-1;
            $this->ddb->query("UPDATE ".table('vip_users')." SET `ofdate`=".SYS_TIME." WHERE ".$this->merge(0)." AND `id`='".$userrow['id']."'");
            $this->ddb->query("UPDATE ".table('vip_record')." SET `usenum`=`usenum`+1 WHERE ".$this->merge(0)." AND `id`=".$snrow['id']);
            $this->ddb->query("UPDATE ".table('vip_content')." SET `usenum`=`usenum`+1 WHERE ".$this->merge(0)." AND `id`=".$conrow['id']);
            echo json_encode($arr); exit();
        }
        $table = table("vip_content")." AS a,".table("vip_record")." AS b";
        $wheresql = " b.type='cut' AND b.userid=".$_A['vip']['id']." AND a.id=b.contentid ";
        $wheresql.= " AND a.alid=".$_A['al']['id'];
        $lists = $this->ddb->getlist($table, $wheresql, "a.inorder DESC,a.indate DESC", 10, ($page > 0)?$page:1);
        //
        $this->cs->show(get_defined_vars());
    }

    /**
     * 礼品卷列表 (使用礼品卷)
     */
    public function doMobileGiftcert()
    {
        global $_A,$_GPC;
        $page = intval($_GPC['page']);
        if ($this->input->post("dosubmit")){
            $arr = $_arr = array();
            $arr['success'] = 0;
            //
            $pass = $this->input->post("pass");
            $sn = $this->input->post("sn");

            $arr['tnum'] = 0; //返回可用数量
            $arr['message'] = "";
            $arr['tpoint'] = 0; //返回赠送积分
            $sprow = $this->ddb->getone("SELECT * FROM ".table('vip_shop_users')." WHERE ".$this->merge(0)." AND `userpass`='{$pass}'");
            if (!$sprow){
                $shopset = $_A['vip_data']['shop'];
                if ($shopset['pass'] != $pass){
                    $arr['message'] = "管理员密码错误！";
                    echo json_encode($arr); exit();
                }
            }else{
                if ($sprow['enddate'] < time() && $sprow['enddate'] > 0){
                    $arr['message'] = "管理员密码已过期！";
                    echo json_encode($arr); exit();
                }
            }
            $snrow = $this->ddb->getone("SELECT * FROM ".table('vip_record')." WHERE ".$this->merge(0)." AND `type`='gift' AND `sn`='{$sn}'");
            if (!$snrow){
                $arr['message'] = "本条(SN)礼品券不存在！";
                echo json_encode($arr); exit();
            }
            $conrow = $this->ddb->getone("SELECT * FROM ".table('vip_content')." WHERE ".$this->merge(0)." AND `type`='gift' AND `id`=".$snrow['contentid']);
            if (!$conrow){
                $arr['message'] = "本条(原)礼品券不存在！";
                echo json_encode($arr); exit();
            }
            if ($conrow['startdate'] > time() || $conrow['enddate'] < time()){
                $arr['message'] = "此礼品券不在可使用期内！";
                echo json_encode($arr); exit();
            }
            if ($snrow['num'] <= $snrow['usenum']){
                $arr['message'] = "此礼品券已使用完毕！";
                echo json_encode($arr); exit();
            }
            //计算需要的积分
            if ($conrow['int_c'] > 0){
                $arr['tpoint'] = abs($conrow['int_c'])*-1;
            }
            //获取会员信息
            $userrow = $this->ddb->getone("SELECT * FROM ".table('vip_users')." WHERE ".$this->merge(0)." AND `id`=".$snrow['userid']);
            if ($userrow['point'] < abs($arr['tpoint'])){
                $arr['message'] = "可用积分不足！";
                echo json_encode($arr); exit();
            }
            //开始提交
            $conarr = array();
            $conarr['type'] = $conrow['type'];
            $conarr['contentid'] = $conrow['id'];
            $conarr['contenttitle'] = $conrow['title'];
            $conarr['sn'] = $sn;
            $conarr['card'] = $userrow['card'];
            $conarr['fullname'] = $userrow['fullname'];
            if ($sprow){
                $conarr['operator'] = $sprow['fullname'];
                $conarr['operatorid'] = $sprow['id'];
            }else{
                $conarr['operator'] = "商家管理员";
                $conarr['operatorid'] = 0;
            }
            $conarr['money'] = 0;
            $conarr['point'] = $arr['tpoint'];
            $conarr['usenum'] = $snrow['usenum'] + 1;
            $conarr['indate'] = SYS_TIME;
            $notesid = $this->ddb->insert(table('vip_content_notes'), $this->merge($conarr));
            if ($notesid < 1){
                $arr['message'] = "提交失败，请稍后再试！";
                echo json_encode($arr); exit();
            }
            if ($arr['tpoint'] != 0){
                //增加(减少)积分到会员账号
                $this->ddb->query("UPDATE ".table('vip_users')." SET `point`=`point`-".abs($arr['tpoint'])." WHERE ".$this->merge(0)." AND `id`='".$userrow['id']."'");
                //增加积分到记录
                $poiarr = array();
                $poiarr['type'] = 'giftcert';
                $poiarr['card'] = $userrow['card'];
                $poiarr['openid'] = $userrow['openid'];
                $poiarr['point'] = $userrow['point'];
                $poiarr['outpoint'] = $arr['tpoint'];
                $poiarr['content'] = "使用礼品券扣除积分";
                $poiarr['indate'] = SYS_TIME;
                $poiarr['indate_cn'] = date("Y-m-d",SYS_TIME);
                $this->ddb->insert(table('vip_point_notes'), $this->merge($poiarr));
            }
            //
            $arr['success'] = 1;
            $arr['tnum'] = $snrow['num']-$snrow['usenum']-1;
            $this->ddb->query("UPDATE ".table('vip_users')." SET `ofdate`=".SYS_TIME." WHERE ".$this->merge(0)." AND `id`='".$userrow['id']."'");
            $this->ddb->query("UPDATE ".table('vip_record')." SET `usenum`=`usenum`+1 WHERE ".$this->merge(0)." AND `id`=".$snrow['id']);
            $this->ddb->query("UPDATE ".table('vip_content')." SET `usenum`=`usenum`+1 WHERE ".$this->merge(0)." AND `id`=".$conrow['id']);
            echo json_encode($arr); exit();
        }
        $table = table("vip_content")." AS a,".table("vip_record")." AS b";
        $wheresql = " b.type='gift' AND b.userid=".$_A['vip']['id']." AND a.id=b.contentid ";
        $wheresql.= " AND a.alid=".$_A['al']['id'];
        $lists = $this->ddb->getlist($table, $wheresql, "a.inorder DESC,a.indate DESC", 10, ($page > 0)?$page:1);
        //
        $this->cs->show(get_defined_vars());
    }

    /**
     * 点击已读
     */
    public function doMobileContentclick()
    {
        global $_A,$_GPC;
        $id = intval($_GPC['id']);
        if ($id > 0){
            $this->ddb->update(table("vip_record"), array("view"=>1,"viewdate"=>SYS_TIME),
                array("view"=>0, "contentid"=>$id, "userid"=>$_A['vip']['id']));
            exit("1");
        }else{
            exit("0");
        }
    }

	/**
	 * 店员入口 扫描验证
	 */
	public function doMobileFrom()
	{
		global $_A,$_GPC;
		$rec = $this->ddb->getone("SELECT * FROM ".table('vip_record'), $this->merge(array('sn'=>$_GPC['param'][2])));
		if (empty($rec)) message("数据不存在！");
        $type = $_GPC['param'][1];
		//
		if ($type == 'privilege') {
			//特权
			$item = $this->ddb->getone("SELECT * FROM ".table('vip_content'), $this->merge(array('id'=>$rec['contentid'], 'type'=>'vip')));
			if (empty($item)) message("特权不存在！");
            if ($this->input->post("dosubmit")){
                $arr = $_arr = array();
                $arr['success'] = 0;
                //
                $point = $this->input->post("point");
                $pass = $this->input->post("pass");
                $sn = $rec['sn'];

                $arr['tnum'] = 0; //返回使用次数
                $arr['message'] = "";
                $arr['tpoint'] = 0; //返回赠送积分
                $sprow = $this->ddb->getone("SELECT * FROM ".table('vip_shop_users')." WHERE ".$this->merge(0)." AND `userpass`='{$pass}'");
                if (!$sprow){
                    $shopset = $_A['vip_data']['shop'];
                    if ($shopset['pass'] != $pass){
                        $arr['message'] = "管理员密码错误！";
                        echo json_encode($arr); exit();
                    }
                }else{
                    if ($sprow['enddate'] < time() && $sprow['enddate'] > 0){
                        $arr['message'] = "管理员密码已过期！";
                        echo json_encode($arr); exit();
                    }
                }
                if ($item['startdate'] > time() || $item['enddate'] < time()){
                    $arr['message'] = "此特权不在可使用期内！";
                    echo json_encode($arr); exit();
                }
                //计算需要的积分
                if ($point != 0){
                    $arr['tpoint'] = $point;
                }
                //获取会员信息
                $userrow = $this->ddb->getone("SELECT * FROM ".table('vip_users')." WHERE ".$this->merge(0)." AND `id`=".$rec['userid']);
                //开始提交
                $conarr = array();
                $conarr['type'] = $item['type'];
                $conarr['contentid'] = $item['id'];
                $conarr['contenttitle'] = $item['title'];
                $conarr['sn'] = $sn;
                $conarr['card'] = $userrow['card'];
                $conarr['fullname'] = $userrow['fullname'];
                if ($sprow){
                    $conarr['operator'] = $sprow['fullname'];
                    $conarr['operatorid'] = $sprow['id'];
                }else{
                    $conarr['operator'] = "商家管理员";
                    $conarr['operatorid'] = 0;
                }
                $conarr['money'] = 0;
                $conarr['point'] = $arr['tpoint'];
                $conarr['usenum'] = $rec['usenum'] + 1;
                $conarr['indate'] = SYS_TIME;
                $notesid = $this->ddb->insert(table('vip_content_notes'), $this->merge($conarr));
                if ($notesid < 1){
                    $arr['message'] = "提交失败，请稍后再试！";
                    echo json_encode($arr); exit();
                }
                if ($arr['tpoint'] != 0){
                    //增加(减少)积分到会员账号
                    $unsql = "`point`=`point`+".$arr['tpoint']."";
                    if ($arr['tpoint'] > 0){
                        $unsql.= ",`pluspoint`=`pluspoint`+".$arr['tpoint']."";
						$this->session->unset_userdata('_VIP_GET_CON');
                    }
                    $this->ddb->query("UPDATE ".table('vip_users')." SET {$unsql} WHERE ".$this->merge(0)." AND `id`='".$userrow['id']."'");
                    //增加积分到记录
                    $poiarr = array();
                    $poiarr['type'] = 'privilege';
                    $poiarr['card'] = $userrow['card'];
                    $poiarr['openid'] = $userrow['openid'];
                    $poiarr['point'] = $userrow['point'];
                    $poiarr['outpoint'] = $arr['tpoint'];
                    $poiarr['content'] = "使用特权";
                    $poiarr['indate'] = SYS_TIME;
                    $poiarr['indate_cn'] = date("Y-m-d", SYS_TIME);
                    $this->ddb->insert(table('vip_point_notes'), $this->merge($poiarr));
                }
                $arr['success'] = 1;
                $arr['tnum'] = $conarr['usenum'];
                $this->ddb->query("UPDATE ".table('vip_users')." SET `ofdate`=".SYS_TIME." WHERE ".$this->merge(0)." AND `id`='".$userrow['id']."'");
                $this->ddb->query("UPDATE ".table('vip_record')." SET `usenum`=`usenum`+1 WHERE ".$this->merge(0)." AND `id`=".$rec['id']);
                $this->ddb->query("UPDATE ".table('vip_content')." SET `usenum`=`usenum`+1 WHERE ".$this->merge(0)." AND `id`=".$item['id']);
                echo json_encode($arr); exit();
            }
		}
        elseif ($type == 'coupon') {
            //优惠券
            $item = $this->ddb->getone("SELECT * FROM ".table('vip_content'), $this->merge(array('id'=>$rec['contentid'], 'type'=>'cut')));
            if (empty($item)) message("优惠券不存在！");
            if ($this->input->post("dosubmit")){
                $arr = $_arr = array();
                $arr['success'] = 0;
                //
                $money = $this->input->post("money");
                $pass = $this->input->post("pass");
                $sn = $rec['sn'];

                $arr['tnum'] = 0; //返回可用数量
                $arr['message'] = "";
                $arr['tpoint'] = 0; //返回赠送积分
                $arr['money'] = $money; //返回还需付款数量
                $sprow = $this->ddb->getone("SELECT * FROM ".table('vip_shop_users')." WHERE ".$this->merge(0)." AND `userpass`='{$pass}'");
                if (!$sprow){
                    $shopset = $_A['vip_data']['shop'];
                    if ($shopset['pass'] != $pass){
                        $arr['message'] = "管理员密码错误！";
                        echo json_encode($arr); exit();
                    }
                }else{
                    if ($sprow['enddate'] < time() && $sprow['enddate'] > 0){
                        $arr['message'] = "管理员密码已过期！";
                        echo json_encode($arr); exit();
                    }
                }
                if ($item['startdate'] > time() || $item['enddate'] < time()){
                    $arr['message'] = "此优惠券不在可使用期内！";
                    echo json_encode($arr); exit();
                }
                if ($rec['num'] <= $rec['usenum']){
                    $arr['message'] = "此优惠券已使用完毕！";
                    echo json_encode($arr); exit();
                }
                //计算赠送的积分
                $jfcl = $_A['vip_data']['jfcl'];
                $xiaofei = $jfcl['xiaofei'];
                $jiangli = $jfcl['jiangli'];
                if ($xiaofei > 0 && $jiangli > 0){
                    $arr['tpoint'] = intval(($money/$xiaofei)*$jiangli);
                }
                //计算还需付款金额
                if ($item['type_b']=='打折优惠券' && $item['int_a']>0){
                    $arr['money'] = $money * ($item['int_a'] * 0.01);
                }elseif ($item['type_b']=='现金抵用券' && $item['int_b']>0){
                    $arr['money'] = $money - $item['int_b'];
                }else{
                    $arr['message'] = "错误的优惠券！";
                    echo json_encode($arr); exit();
                }
                //获取会员信息
                $userrow = $this->ddb->getone("SELECT * FROM ".table('vip_users')." WHERE ".$this->merge(0)." AND `id`=".$rec['userid']);
                //开始提交
                $conarr = array();
                $conarr['type'] = $item['type'];
                $conarr['contentid'] = $item['id'];
                $conarr['contenttitle'] = $item['title'];
                $conarr['sn'] = $sn;
                $conarr['card'] = $userrow['card'];
                $conarr['fullname'] = $userrow['fullname'];
                if ($sprow){
                    $conarr['operator'] = $sprow['fullname'];
                    $conarr['operatorid'] = $sprow['id'];
                }else{
                    $conarr['operator'] = "商家管理员";
                    $conarr['operatorid'] = 0;
                }
                $conarr['money'] = $money;
                $conarr['point'] = $arr['tpoint'];
                $conarr['usenum'] = $rec['usenum'] + 1;
                $conarr['indate'] = SYS_TIME;
                $notesid = $this->ddb->insert(table('vip_content_notes'), $this->merge($conarr));
                if ($notesid < 1){
                    $arr['message'] = "提交失败，请稍后再试！";
                    echo json_encode($arr); exit();
                }
                if ($arr['tpoint'] != 0){
                    //增加积分到会员账号
                    $upsql = point_field($arr['tpoint'],'outpoint').",`money`=`money`+".$arr['money'];
                    if ($arr['money'] > $userrow['moneyone']){
                        $upsql.= ",`moneyone`=".$arr['money'];
                    }
                    $this->ddb->query("UPDATE ".table('vip_users')." SET {$upsql} WHERE ".$this->merge(0)." AND `id`='".$userrow['id']."'");
                    //增加积分到记录
                    $poiarr = array();
                    $poiarr['type'] = 'coupon';
                    $poiarr['card'] = $userrow['card'];
                    $poiarr['openid'] = $userrow['openid'];
                    $poiarr['money'] = 0;
                    $poiarr['outmoney'] = $arr['money'];
                    $poiarr['point'] = $userrow['point'];
                    $poiarr['outpoint'] = $arr['tpoint'];
                    $poiarr['content'] = "使用优惠券消费获得积分奖励";
                    $poiarr['indate'] = SYS_TIME;
                    $poiarr['indate_cn'] = date("Y-m-d",SYS_TIME);
                    $this->ddb->insert(table('vip_point_notes'), $this->merge($poiarr));
                }
                //
                $arr['success'] = 1;
                $arr['tnum'] = $rec['num']-$rec['usenum']-1;
                $this->ddb->query("UPDATE ".table('vip_users')." SET `ofdate`=".SYS_TIME." WHERE ".$this->merge(0)." AND `id`='".$userrow['id']."'");
                $this->ddb->query("UPDATE ".table('vip_record')." SET `usenum`=`usenum`+1 WHERE ".$this->merge(0)." AND `id`=".$rec['id']);
                $this->ddb->query("UPDATE ".table('vip_content')." SET `usenum`=`usenum`+1 WHERE ".$this->merge(0)." AND `id`=".$item['id']);
                echo json_encode($arr); exit();
            }
        }
		elseif ($type == 'giftcert') {
			//礼品卷
			$item = $this->ddb->getone("SELECT * FROM ".table('vip_content'), $this->merge(array('id'=>$rec['contentid'], 'type'=>'gift')));
			if (empty($item)) message("礼品券不存在！");
            if ($this->input->post("dosubmit")){
                $arr = $_arr = array();
                $arr['success'] = 0;
                //
                $pass = $this->input->post("pass");
                $sn = $rec['sn'];

                $arr['tnum'] = 0; //返回可用数量
                $arr['message'] = "";
                $arr['tpoint'] = 0; //返回赠送积分
                $sprow = $this->ddb->getone("SELECT * FROM ".table('vip_shop_users')." WHERE ".$this->merge(0)." AND `userpass`='{$pass}'");
                if (!$sprow){
                    $shopset = $_A['vip_data']['shop'];
                    if ($shopset['pass'] != $pass){
                        $arr['message'] = "管理员密码错误！";
                        echo json_encode($arr); exit();
                    }
                }else{
                    if ($sprow['enddate'] < time() && $sprow['enddate'] > 0){
                        $arr['message'] = "管理员密码已过期！";
                        echo json_encode($arr); exit();
                    }
                }
                if ($item['startdate'] > time() || $item['enddate'] < time()){
                    $arr['message'] = "此礼品券不在可使用期内！";
                    echo json_encode($arr); exit();
                }
                if ($rec['num'] <= $rec['usenum']){
                    $arr['message'] = "此礼品券已使用完毕！";
                    echo json_encode($arr); exit();
                }
                //计算需要的积分
                if ($item['int_c'] > 0){
                    $arr['tpoint'] = abs($item['int_c'])*-1;
                }
                //获取会员信息
                $userrow = $this->ddb->getone("SELECT * FROM ".table('vip_users')." WHERE ".$this->merge(0)." AND `id`=".$rec['userid']);
                if ($userrow['point'] < abs($arr['tpoint'])){
                    $arr['message'] = "会员可用积分不足！";
                    echo json_encode($arr); exit();
                }
                //开始提交
                $conarr = array();
                $conarr['type'] = $item['type'];
                $conarr['contentid'] = $item['id'];
                $conarr['contenttitle'] = $item['title'];
                $conarr['sn'] = $sn;
                $conarr['card'] = $userrow['card'];
                $conarr['fullname'] = $userrow['fullname'];
                if ($sprow){
                    $conarr['operator'] = $sprow['fullname'];
                    $conarr['operatorid'] = $sprow['id'];
                }else{
                    $conarr['operator'] = "商家管理员";
                    $conarr['operatorid'] = 0;
                }
                $conarr['money'] = 0;
                $conarr['point'] = $arr['tpoint'];
                $conarr['usenum'] = $rec['usenum'] + 1;
                $conarr['indate'] = SYS_TIME;
                $notesid = $this->ddb->insert(table('vip_content_notes'), $this->merge($conarr));
                if ($notesid < 1){
                    $arr['message'] = "提交失败，请稍后再试！";
                    echo json_encode($arr); exit();
                }
                if ($arr['tpoint'] != 0){
                    //增加(减少)积分到会员账号
                    $this->ddb->query("UPDATE ".table('vip_users')." SET `point`=`point`-".abs($arr['tpoint'])." WHERE ".$this->merge(0)." AND `id`='".$userrow['id']."'");
                    //增加积分到记录
                    $poiarr = array();
                    $poiarr['type'] = 'giftcert';
                    $poiarr['card'] = $userrow['card'];
                    $poiarr['openid'] = $userrow['openid'];
                    $poiarr['point'] = $userrow['point'];
                    $poiarr['outpoint'] = $arr['tpoint'];
                    $poiarr['content'] = "使用礼品券扣除积分";
                    $poiarr['indate'] = SYS_TIME;
                    $poiarr['indate_cn'] = date("Y-m-d",SYS_TIME);
                    $this->ddb->insert(table('vip_point_notes'), $this->merge($poiarr));
                }
                //
                $arr['success'] = 1;
                $arr['tnum'] = $rec['num']-$rec['usenum']-1;
                $this->ddb->query("UPDATE ".table('vip_users')." SET `ofdate`=".SYS_TIME." WHERE ".$this->merge(0)." AND `id`='".$userrow['id']."'");
                $this->ddb->query("UPDATE ".table('vip_record')." SET `usenum`=`usenum`+1 WHERE ".$this->merge(0)." AND `id`=".$rec['id']);
                $this->ddb->query("UPDATE ".table('vip_content')." SET `usenum`=`usenum`+1 WHERE ".$this->merge(0)." AND `id`=".$item['id']);
                echo json_encode($arr); exit();
            }
		}
        else{
            message("参数错误-from！");
        }
		$this->cs->show('from_'.$_GPC['param'][1], get_defined_vars());
	}

    /**
     * 修改资料
     */
    public function doMobilePersonal()
    {
        global $_A;
        //提交
        if ($this->input->post("dosubmit") == 'sms'){
            $fost = $this->input->post();
            $this->load->model('sms');
            $arr = $this->sms->send($fost['phone'], 0, 'personal');
            echo json_encode($arr); exit();
        }elseif ($this->input->post("dosubmit")){
            $fost = $this->input->post();
            foreach($fost AS $k=>$v) { if ($v == 'undefined') unset($fost[$k]); }
            $arr = $_arr = array();
            $arr['success'] = 0;
            $arr['havephone'] = 0;
            //
            $_arr = array();
            $_arr['editnum'] = intval($_A['vip']['editnum']) + 1;
            $_arr['fullname'] = $fost['fullname'];
            $_arr['phone'] = $fost['phone'];
            $_arr['email'] = $fost['email'];
            $_arr['sex'] = $fost['sex'];
            $_arr['address'] = value($fost,'address');
            $_arr['idnumber'] = value($fost,'idnumber');
            if ($_A['vip_data']['userset']['haveinfo'] == '强制') {
                if ($_A['vip_data']['userset']['haveitem']['fullname'] && empty($_arr['fullname'])){
                    $arr['message'] = "请填写姓名！";
                    echo json_encode($arr); exit();
                }
                if($_A['vip_data']['userset']['haveitem']['phone'] && empty($_arr['phone'])){
                    $arr['message'] = "请填写手机号码！";
                    echo json_encode($arr); exit();
                }
                if($_A['vip_data']['userset']['haveitem']['email'] && empty($_arr['email'])){
                    $arr['message'] = "请填写邮箱地址！";
                    echo json_encode($arr); exit();
                }
                if ($_A['vip_data']['userset']['haveitem']['sex'] && empty($_arr['sex'])){
                    $arr['message'] = "请选择性别！";
                    echo json_encode($arr); exit();
                }
                if ($_A['vip_data']['userset']['haveitem']['address'] && isset($fost['address']) && strlen($_arr['address']) < 5){
                    $arr['message'] = "请填写有效的地址！";
                    echo json_encode($arr); exit();
                }
                if($_A['vip_data']['userset']['haveitem']['idnumber'] && !preg_match("/^(\d{15}$|^\d{18}$|^\d{17}(\d|X|x))$/",$_arr['idnumber'])){
                    $arr['message'] = "请填写正确的身份证号码！";
                    echo json_encode($arr); exit();
                }
            }
            //验证码
            $iscode = false;
            if (isset($fost['code'])) {
                $this->load->model('sms');
                $smsarr = $this->sms->verify($_arr['phone'], $fost['code'], 0, 'personal');
                if (empty($smsarr['success'])) {
                    $arr['message'] = $smsarr['message'];
                    echo json_encode($arr); exit();
                }
                $iscode = true;
            }
            //手机号码、邮箱
            $farr = array();
            if($_arr['phone']){
                if(!preg_match("/1[3458]{1}\d{9}$/",$_arr['phone'])){
                    $arr['message'] = "请填写正确的手机号码！";
                    echo json_encode($arr); exit();
                }
                if (!$iscode) {
                    $harow = db_getone(table('fans')." WHERE ".$this->merge(0)." AND `userphone`='".$_arr['phone']."' AND `id`!=".$_A['fans']['id']);
                    if ($harow) {
                        $arr['havephone'] = 1;
                        $arr['message'] = "填写的手机号码已存在<br/>请另外设置或通过验证修改！";
                        echo json_encode($arr); exit();
                    }
                }
                $farr['userphone'] = $_arr['phone'];
            }
            if($_arr['email']){
                if(!preg_match("/^(\w)+(\.\w+)*@(\w)+((\.\w+)+)$/",$_arr['email'])){
                    $arr['message'] = "请填写正确的邮箱地址！";
                    echo json_encode($arr); exit();
                }
                $harow = db_getone(table('fans')." WHERE ".$this->merge(0)." AND `useremail`='".$_arr['email']."' AND `id`!=".$_A['fans']['id']);
                if ($harow) {
                    $arr['message'] = "填写的邮箱地址已存在，请另外设置！";
                    echo json_encode($arr); exit();
                }
                $farr['useremail'] = $_arr['email'];
            }
            if ($this->ddb->update(table('vip_users'), $this->merge($_arr), array("id"=>$_A['vip']['id']))){
                $arr['message'] = "保存成功！";
                $arr['success'] = 1;
                //第一次进入获得的通知
                if ($_arr['editnum'] == 1){
                    $_wheres = $this->merge(0);
                    $_wheres.= " AND `onelogin`=1 ";
                    $_wheres.= " AND (`type`='msg' OR (`startdate`<".SYS_TIME." AND `enddate`>".SYS_TIME.")) ";
                    $conlist = $this->ddb->getall("SELECT * FROM ".table("vip_content")." WHERE ".$_wheres);
                    $inarr = array();
                    $inarr['userid'] = $_A['vip']['id'];
                    $inarr['view'] = 0;
                    foreach($conlist AS $item){
                        $inarr['type'] = $item['type'];
                        $inarr['contentid'] = $item['id'];
                        $inarr['indate'] = $item['indate'];
                        $inarr['num'] = 0;
                        if ($item['type']=='cut') {
                            $inarr['num'] = $item['int_c']; //优惠券数量(例外)
                        }elseif ($item['type']=='gift'){
                            $inarr['num'] = $item['int_b']; //礼品券数量(例外)
                        }
                        $inarr['sn'] = getSN($_A['vip']['id'], $item['id']);
                        $this->ddb->insert(table("vip_record"), $this->merge($inarr));
                    }
                }
                //如果是验证码把原先的手机号设为空
                if ($iscode) {
                    db_update(table('fans'), array('userphone'=>''), array('userphone'=>$_arr['phone'], "`id`!="=>$_A['fans']['id']));
                    db_update(table('vip_users'), array('phone'=>''), array("phone"=>$_arr['phone'], "`id`!="=>$_A['vip']['id']));
                }
                if ($farr) {
                    db_update(table('fans'), $farr, array('id'=>$_A['fans']['id']));
                }
            }else{
                $arr['message'] = "保存失败！";
            }
            echo json_encode($arr); exit();
        }
        if ($_A['fans']['userphone']) {
            $_A['vip']['phone'] = $_A['fans']['userphone'];
        }
        if ($_A['fans']['useremail']) {
            $_A['vip']['email'] = $_A['fans']['useremail'];
        }
        $this->cs->show(get_defined_vars());
    }

    /**
     * 获取SN状态
     */
    public function doMobileGetsnstatus()
    {
        $arr = array();
        $arr['success'] = 0;
        $arr['tnum'] = 0;
        $sn = $this->input->post("sn");
        $times = $this->input->post("times");
        if (!empty($sn) && $times > 0){
            $snrow = $this->ddb->getone("SELECT * FROM ".table('vip_content_notes')." WHERE ".$this->merge(0)." AND `sn`='".$sn."' AND `indate`>".$times." ORDER BY `indate` DESC");
            if ($snrow){
                $arr['success'] = 1;
                $rerow = $this->ddb->getone("SELECT * FROM ".table('vip_record'), $this->merge(array('sn'=>$sn)));
                if ($rerow) {
                    $arr['tnum'] = $rerow['num'] - $rerow['usenum'];
                }
            }
        }
        echo json_encode($arr);
        exit();
    }

    /**
     * 生成二维码
     */
    public function doMobileQrcode()
    {
        global $_GPC;
        include_once 'include/qrcode.php';
        $value = appurl("vip/from/".$_GPC['param'][1]."/".$_GPC['sn']);
        $errorCorrectionLevel = "Q";
        $matrixPointSize = "6";
        QRcode::png($value, false, $errorCorrectionLevel, $matrixPointSize, 0);
        exit();
    }

    /**
     * 删除功能执行
     * @param int $alid
     * @return bool
     */
    public function useDeleted($alid = 0) {
        db_delete(table("vip_content"), array('alid' => $alid));
        db_delete(table("vip_content_notes"), array('alid' => $alid));
        db_delete(table("vip_point_notes"), array('alid' => $alid));
        db_delete(table("vip_record"), array('alid' => $alid));
        db_delete(table("vip_shop"), array('alid' => $alid));
        db_delete(table("vip_shop_users"), array('alid' => $alid));
        db_delete(table("vip_users"), array('alid' => $alid));
        return true;
    }
}
