<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ES_Library extends CI_Model {

	public function __construct()
	{
		parent::__construct();
        $this->load->helper('tpl');
	}

    /**
     * 素材列表
     */
    public function doWebIndex($parent = null)
    {
        $user = $this->user->getuser();
        $func = $this->user->functions();

        $page = value($parent, 1, 'int');
        $keyval = $this->input->get('keyval');
        $pageurl = urlencode($this->base->url[2]);
        $wheresql = " `alid`=".$func['al']['id'];
        //搜索
        if ($keyval){
            $wheresql.= " AND `title` LIKE '%".$keyval."%' ";
        }
		if ($this->input->get('id') > 0) {
			$wheresql.= " AND `id`=".intval($this->input->get('id'));
		}

        $this->cs->show(get_defined_vars());
    }


    /**
     * 单图文
     */
    public function doWebOnlyimg($parent = null)
    {
        $user = $this->user->getuser();
        $func = $this->user->functions();
        $id = value($parent, 1, 'int');
        //
        $content = array();
        $submit = '添加';
        if ($id > 0){
            $warr = array();
            $warr['type'] = 'onlyimg';
            $warr['alid'] = $func['al']['id'];
            $warr['id'] = $id;
            $content = $this->ddb->getone("SELECT * FROM ".table('library'), $warr);
            if ($content){
                $submit = '修改';
                $content['setting'] = string2array($content['setting']);
            }else{
                $id = 0;
            }
        }
        if ($this->input->post("dosubmit")){
            $fost = $this->input->post();
            $arr = $_arr = array();
            $arr['success'] = 0;
            //
            $_arr['alid'] = $func['al']['id'];
            $_arr['title'] = $fost['title'];
            $_arr['author'] = $fost['author'];
            $_arr['img'] = $fost['img'];
            $_arr['ishowimg'] = isset($fost['ishowimg'])?1:0;
            $_arr['descriptions'] = $fost['descriptions'];
            $_arr['content'] = value($fost, 'content');
            $_arr['url'] = $fost['url'];
            $_arr['update'] = SYS_TIME;
            //
            if (empty($_arr['title']) || get_strlen($_arr['title']) > 64){
                $arr['message'] = '标题不能为空且长度不能超过64字';
                echo json_encode($arr); exit();
            }
            if (empty($_arr['img'])){
                $arr['message'] = '必须插入封面图片';
                echo json_encode($arr); exit();
            }
            if (empty($_arr['content']) || strlen($_arr['content']) > 80000){
                $arr['message'] = '正文不能为空且长度不能超过80000字';
                echo json_encode($arr); exit();
            }
            //
            if ($id > 0) {
                if ($this->ddb->update(table('library'), $_arr, array('id'=>$id))){
                    $arr['success'] = 1;
                    $arr['message'] = '修改成功';
                }else{
                    $arr['message'] = '修改失败';
                }
            }else{
                $_arr['type'] = 'onlyimg';
                $_arr['indate'] = SYS_TIME;
                $cid = $this->ddb->insert(table('library'), $_arr, true);
                if ($cid){
                    $arr['success'] = 1;
                    $arr['message'] = '添加成功';
                }else{
                    $arr['message'] = '添加失败';
                }
            }
            echo json_encode($arr); exit();
        }
        //
        $this->cs->show(get_defined_vars());
    }


    /**
     * 多图文
     */
    public function doWebmanyimg($parent = null)
    {
        $user = $this->user->getuser();
        $func = $this->user->functions();
        $id = value($parent, 1, 'int');
        //
        $content = array();
        $submit = '添加';
        if ($id > 0){
            $warr = array();
            $warr['type'] = 'manyimg';
            $warr['alid'] = $func['al']['id'];
            $warr['id'] = $id;
            $content = $this->ddb->getone("SELECT * FROM ".table('library'), $warr);
            if ($content){
                $submit = '修改';
                $content['setting'] = string2array($content['setting']);
                foreach($content['setting'] AS $_k=>$_v) {
                    $content['setting'][$_k]['img'] = $_v['img'];
                }
                $content['settingjson'] = json_encode($content['setting']);
            }else{
                $id = 0;
            }
        }
        if ($this->input->post("dosubmit")){
            $fost = $this->input->post();
            $arr = $_arr = array();
            $arr['success'] = 0;
            //
            $_varr = array();
            foreach($fost as $key=>$val){
                $_n = get_subto($key,'_','_');
                $_n2 = get_subto($key,$_n.'_','_');
                if (substr($key,0,5)=='many_'){
                    $_varr[$_n][$_n2] = $val;
                }
            }
            foreach ($_varr as $key => $val) {
                if (!isset($val['title'])) {
                    unset($_varr[$key]);
                    continue;
                }
                $_varr[$key]['ishowimg'] = isset($val['ishowimg'])?1:0;
                $_varr[$key]['content'] = isset($val['content'])?$val['content']:'';
                if (empty($val['title']) || get_strlen($val['title']) > 64){
                    $arr['message'] = '所有标题不能为空且长度不能超过64字';
                    echo json_encode($arr); exit();
                }
                if (empty($val['img'])){
                    $arr['message'] = '必须插入所有封面图片';
                    echo json_encode($arr); exit();
                }
                if (empty($val['content']) || strlen($val['content']) > 80000){
                    $arr['message'] = '所有正文不能为空且长度不能超过80000字';
                    echo json_encode($arr); exit();
                }
            }
            $inorder = array();
            foreach ($_varr as $key => $val) {
                $inorder[$key] = $val['inorder'];
            }
            array_multisort($inorder, SORT_ASC, $_varr);
            //
            $_arr['alid'] = $func['al']['id'];
            $_arr['setting'] = array2string($_varr);
            $_arr['title'] = $_varr[0]['title'];
            $_arr['author'] = $_varr[0]['author'];
            $_arr['img'] = $_varr[0]['img'];
            $_arr['ishowimg'] = $_varr[0]['ishowimg'];
            $_arr['content'] = $_varr[0]['content'];
            $_arr['url'] = $_varr[0]['url'];
            $_arr['update'] = SYS_TIME;
            if ($id > 0) {
                if ($this->ddb->update(table('library'), $_arr, array('id'=>$id))){
                    $arr['success'] = 1;
                    $arr['message'] = '修改成功';
                }else{
                    $arr['message'] = '修改失败';
                }
            }else{
                $_arr['type'] = 'manyimg';
                $_arr['indate'] = SYS_TIME;
                $cid = $this->ddb->insert(table('library'), $_arr, true);
                if ($cid){
                    $arr['success'] = 1;
                    $arr['message'] = '添加成功';
                }else{
                    $arr['message'] = '添加失败';
                }
            }
            echo json_encode($arr); exit();
        }
        //
        $this->cs->show(get_defined_vars());
    }

    /**
     * 导入素材
     */
    public function doWebdownweixin()
    {
        global $_A,$_GPC;
        if (!$_A['al']['wx_appid']) {
            message(null, '发现没有开启微信功能！');
        }
        if ($_A['al']['wx_level'] == 7) {
            message(null, '企业号暂时不支持此功能！');
        }
        @set_time_limit(0);
        @ini_set("max_execution_time", 3600);

        $this->user->getuser();
        $func = $this->user->functions();

        $this->load->helper('communication');
        $this->load->library('wx');
        $this->wx->setting($func['al']['id']);
        //
        $oknum = intval($_GPC['oknum']);
        $upnum = intval($_GPC['upnum']);
        $nonum = intval($_GPC['nonum']);
        $nowpage = max(1, $_GPC['page']);
        //获取列表
        $url = "https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token=".$this->wx->token();
        $_html = ihttp_post($url, json_encode(array(
                'type'=>'news',
                'offset'=>($nowpage-1)*10,
                'count'=>10
            )));
        if(is_error($_html)) {
            message(null, '错误-2：'.$_html['message']);
        }
        $rethtml = @json_decode($_html['content'], true);
        if (isset($rethtml['errmsg'])) {
            message(null, '错误：'.$this->wx->error_code($rethtml['errcode']));
        }
        if (empty($rethtml['total_count'])) {
            message(null, '提示：公众平台图文素材列表为空。');
        }
        if (empty($rethtml['item_count'])) {
            message(null, '提示：已导入完成。');
        }
        $allpage = ceil($rethtml['total_count']/10);
        $items = $rethtml['item'];
        foreach($items AS $item) {
            $media_id = $item['media_id'];
            $news_item = $item['content']['news_item'];
            $update_time = $item['update_time'];
            //
            $j = 0;
            $data = array();
            $data['media_id'] = $media_id;
            $data['alid'] = $func['al']['id'];
            foreach($news_item AS $news) {
                $news['content'] = $this->content_src($news['content'], $update_time);
                if ($j == 0) {
                    $data['title'] = $news['title'];
                    $data['author'] = $news['author'];
                    $data['img'] = $this->media_img($news['thumb_media_id'], $update_time);
                    $data['ishowimg'] = $news['show_cover_pic'];
                    $data['descriptions'] = $news['digest'];
                    $data['content'] = $news['content'];
                    $data['url'] = $news['content_source_url'];
                    $data['media_url'] = $news['url'];
                    $data['update'] = SYS_TIME;
                }
                $data['setting'][] = array(
                    'title'=>$news['title'],
                    'author'=>$news['author'],
                    'inorder'=>$j,
                    'img'=>$this->media_img($news['thumb_media_id'], $update_time),
                    'url'=>$news['content_source_url'],
                    'content'=>$news['content'],
                    'ishowimg'=>$news['show_cover_pic'],
                    'media_url'=>$news['url'],
                );
                $j++;
            }
            if (count($news_item) > 1) {
                $data['type'] = 'manyimg';
            }else{
                $data['type'] = 'onlyimg';
            }
            $data['setting'] = array2string($data['setting']);
            $library = $this->ddb->getone(table('library'), array('media_id'=>$data['media_id'], 'alid'=>$data['alid']));
            if ($library) {
                if ($this->ddb->update(table('library'), $data, array('id'=>$library['id']))) {
                    $upnum++;
                }
            }else{
                $data['indate'] = SYS_TIME;
                if ($this->ddb->insert(table('library'), $data)) {
                    $oknum++;
                }else{
                    $nonum++;
                }
            }
        }
        //
        if ($nowpage < $allpage) {
            message('继续导入',
                '总计: '.$rethtml['total_count'].'条；成功导入: '.$oknum.'条；更新: '.$upnum.'条；导入失败: '.$nonum.'条。<br/>正在导入下一批，请稍后...',
                weburl('library/downweixin')."&page=".($nowpage+1)."&oknum=".$oknum."&upnum=".$upnum."&nonum=".$nonum
            );
        }
        message('导入结束',
            '总计: '.$rethtml['total_count'].'条；成功导入: '.$oknum.'条；更新: '.$upnum.'条；导入失败: '.$nonum.'条。',
            array('title'=>'点此返回素材列表', 'url'=>weburl('library/index'))
        );
    }

    /**
     * 删除素材
     */
    public function doWebdellib($parent = null)
    {
        $this->user->getuser();
        $func = $this->user->functions();
        $id = value($parent, 1, 'int');

        $arr = array();
        $arr['success'] = 0;
        //
        $warr = array();
        $warr['alid'] = $func['al']['id'];
        $warr['id'] = intval($id);
        $row = $this->ddb->getone("SELECT * FROM ".table('library'), $warr);
        if (!$row){
            $row['message'] = '素材不存在';
            echo json_encode($arr); exit();
        }
        if ($this->ddb->delete(table('library'), array('id'=>$row['id']))){
            //
            $arr['success'] = 1;
            $arr['message'] = '删除成功';
        }else{
            $arr['message'] = '删除失败';
        }

        echo json_encode($arr); exit();
    }

	/**
	 * 编辑器选择素材
	 */
	public function doWebalieditlist($a = null)
	{
		$user = $this->user->getuser();
		$func = $this->user->functions();
		//
		$id = $this->input->get('id');
		$page = $this->input->get('page');
		$keyval = $this->input->get('keyval');
		$pageurl = urlencode($this->base->url[2]);
		$wheresql = " `alid`=".$func['al']['id'];
		//搜索
		if ($keyval){
			$wheresql.= " AND `title` LIKE '%".$keyval."%' ";
		}
		if ($id > 0) {
			$wheresql.= " AND `id`=".intval($id);
		}
		$this->cs->show($a[1]?'alieditlist_'.$a[1]:'alieditlist', get_defined_vars());
	}

	public function doMobile()
	{
		global $_GPC;
		$id = value($_GPC['param'], 0, 'int');
		$sheet = value($_GPC['param'], 1, 'int');
		$content = $this->ddb->getone("SELECT * FROM ".table('library'), array('id'=>$id));
		if (empty($content)) {
			message("内容不存在！");
		}
		$setting = string2array($content['setting']);
		if ($content['type'] == 'manyimg') {
			if (!isset($setting[$sheet])) {
				message("内容不存在！");
			}
			$content = array_merge($content, $setting[$sheet]);
		}
		$this->cs->show('index', get_defined_vars());
	}



    /**
     * 删除功能执行
     * @param int $alid
     * @return bool
     */
    public function useDeleted($alid = 0) {
        db_delete(table("library"), array('alid' => $alid));
        return true;
    }


    /**
     * 下载素材缩略图
     * @param $media_id
     * @param int $update_time
     * @return string
     */
    private function media_img($media_id, $update_time = 0) {
        global $_A;
        if (empty($update_time)) $update_time = SYS_TIME;
        if (empty($media_id)) return "";
        $_file = 'uploadfiles/users/'.$_A['u']['userid'].'/images/'.date('Y/m/', $update_time);
        $_dir = BASE_PATH.$_file;
        $nan = $media_id.'.jpg';
        if (!file_exists($_dir.$nan)) {
            $this->load->helper('communication');
            $url = "https://api.weixin.qq.com/cgi-bin/material/get_material?access_token=".$this->wx->token();
            $_html = ihttp_post($url, json_encode(array('media_id'=>$media_id)));
            if(is_error($_html)) {
                return $media_id;
            }
            $rethtml = @json_decode($_html['content'], true);
            if (isset($rethtml['errmsg'])) {
                $this->wx->error_code($rethtml['errcode']);
                return $media_id;
            }
            make_dir($_dir);
            $fp2 = @fopen($_dir.$nan,'a');
            fwrite($fp2, $_html['content']);
            fclose($fp2);
        }
        return $_file.$nan;
    }

    /**
     * 下载图文素材内容中的图片、替换路径
     * @param $content
     * @param int $update_time
     * @return mixed|string
     */
    private function content_src($content, $update_time = 0) {
        global $_A;
        if (empty($update_time)) $update_time = SYS_TIME;
        if (empty($content)) return "";
        $this->load->model('vupload');
        //替换图片
        $_file = 'uploadfiles/users/'.$_A['u']['userid'].'/images/'.date('Y/m/', $update_time);
        $_dir = BASE_PATH.$_file;
        $str = $content;
        preg_match_all('/<img\s+([^>]*?)data-src\s*=\s*[\'|\"](.*?)[\'|\"]/is', $str, $match);
        foreach($match[0] AS $mak=>$mav) {
            $src = $match[2][$mak];
            $ext = strrchr($src, '=');
            if (in_array($ext, array('=gif','=jpg','=jpeg','=png','=bmp'))) {
                $src = substr($src, 0, strlen($ext)*-1).".".substr($ext, 1);
                $nan = md5($src).'.'.substr($ext, 1);
                if (file_exists($_dir.$nan)) {
                    $src = fillurl($_file.$nan);
                }else{
                    make_dir($_dir);
                    $srcarr = $this->vupload->getImage($src, $_dir, $nan);
                    if (isset($srcarr['error']) && empty($srcarr['error'])) {
                        $src = fillurl($_file.$srcarr['file_name']);
                    }
                }
                $str = str_replace($mav, "<img ".$match[1][$mak]."src=\"".$src."\"", $str);
            }elseif (preg_match('/^http\s*[^>]*?qpic\.cn\//', $src)) {
                $src.= ".gif";
                $nan = md5($src).'.gif';
                if (file_exists($_dir.$nan)) {
                    $src = fillurl($_file.$nan);
                }else{
                    make_dir($_dir);
                    $srcarr = $this->vupload->getImage($src, $_dir, $nan);
                    if (isset($srcarr['error']) && empty($srcarr['error'])) {
                        $src = fillurl($_file.$srcarr['file_name']);
                    }
                }
                $str = str_replace($mav, "<img ".$match[1][$mak]."src=\"".$src."\"", $str);
            }
        }
        //替换css背景
        preg_match_all('/background-image:\s*url\(\s*[\"|\']?\s*(.*?)\s*[\"|\']?\s*\)/is', $str, $match);
        foreach($match[0] AS $mak=>$mav) {
            $src = str_replace(array('\'','"'), '', $match[1][$mak]);
            $ext = strrchr($src, '=');
            if (in_array($ext, array('=gif','=jpg','=jpeg','=png','=bmp'))) {
                $src = substr($src, 0, strlen($ext)*-1).".".substr($ext, 1);
                $nan = md5($src).'.'.substr($ext, 1);
                if (file_exists($_dir.$nan)) {
                    $src = fillurl($_file.$nan);
                }else{
                    make_dir($_dir);
                    $srcarr = $this->vupload->getImage($src, $_dir, $nan);
                    if (isset($srcarr['error']) && empty($srcarr['error'])) {
                        $src = fillurl($_file.$srcarr['file_name']);
                    }
                }
                $str = str_replace($mav, "background-image: url(".$src.")", $str);
            }elseif (preg_match('/^http\s*[^>]*?qpic\.cn\//', $src)) {
                $src.= ".gif";
                $nan = md5($src).'.gif';
                if (file_exists($_dir.$nan)) {
                    $src = fillurl($_file.$nan);
                }else{
                    make_dir($_dir);
                    $srcarr = $this->vupload->getImage($src, $_dir, $nan);
                    if (isset($srcarr['error']) && empty($srcarr['error'])) {
                        $src = fillurl($_file.$srcarr['file_name']);
                    }
                }
                $str = str_replace($mav, "background-image: url(".$src.")", $str);
            }
        }
        //替换 iframe
        preg_match_all('/<iframe\s+([^>]*?)data-src\s*=\s*[\'|\"](.*?)[\'|\"]/is', $str, $match);
        foreach($match[0] AS $mak=>$mav) {
            $src = $match[2][$mak];
            if (substr($src,0,1) == '/') {
                $src = "http://mp.weixin.qq.com".$src;
            }
            $str = str_replace($mav, "<iframe ".$match[1][$mak]."src=\"".$src."\"", $str);
        }
        return $str;
    }

}
