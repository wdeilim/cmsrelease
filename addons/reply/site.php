<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ES_Reply extends CI_Model {

	public function __construct()
	{
		parent::__construct();
        $this->load->helper('tpl');
	}

    /**
     * 回复列表
     */
    public function doWebIndex($parent = null)
    {
        $user = $this->user->getuser();
        $func = $this->user->functions();

        $page = value($parent, 1, 'int');
        $pageurl = urlencode($this->base->url[2]);
        $wheresql = " `module`='reply' AND `alid`=".$func['al']['id'];

        $this->cs->show(get_defined_vars());
    }


    /**
     * 添加、修改 回复
     * @param null $parent
     */
    public function doWebAdd($parent = null)
    {
        $id = value($parent, 1, 'int');
        $user = $this->user->getuser();
        $func = $this->user->functions();
        //
        $reply = array();
        $submit = '添加';
        if ($id > 0){
            $warr = array();
            $warr['module'] = 'reply';
            $warr['alid'] = $func['al']['id'];
            $warr['id'] = $id;
            $reply = $this->ddb->getone("SELECT * FROM ".table('reply'), $warr);
            if ($reply){
                $submit = '修改';
                $reply['content'] = string2array($reply['content']);
                $reply['setting'] = string2array($reply['setting']);
                $reply['key'] = trim($reply['key'], ',');
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
            $_arr['module'] = 'reply';
            $_arr['alid'] = $func['al']['id'];
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
            //
            if ($id > 0) {
                if ($this->ddb->update(table('reply'), $_arr, array('id'=>$id))){
                    $arr['success'] = 1;
                    $arr['message'] = '修改成功';
                }else{
                    $arr['message'] = '修改失败';
                }
            }else{
                $_arr['indate'] = SYS_TIME;
                $cid = $this->ddb->insert(table('reply'), $_arr, true);
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
     * 非关键词回复
     */
    public function doWebOther()
    {
        $user = $this->user->getuser();
        $func = $this->user->functions();
        //
        $setting = string2array($func['al']['setting']);
        if ($this->input->post("dosubmit")){
            $fost = $this->input->post();
            $arr = $_arr = array();
            $arr['success'] = 0;
            //
            $setting['nonekey']['status'] = $fost['status'];
            $setting['nonekey']['content'] = value($fost, 'content', true);
            $setting['nonekey']['update'] = SYS_TIME;
            if ($this->ddb->update(table('users_al'), array('setting'=>array2string($setting)), array('id'=>$func['al']['id']))){
                $arr['success'] = 1;
                $arr['message'] = '保存成功';
            }else{
                $arr['message'] = '保存失败';
            }
            echo json_encode($arr); exit();
        }
        $this->cs->show(get_defined_vars());
    }

    /**
     * 关注回复
     */
    public function doWebAttention()
    {
        $user = $this->user->getuser();
        $func = $this->user->functions();
        //
        $setting = string2array($func['al']['setting']);
        if ($this->input->post("dosubmit")){
            $fost = $this->input->post();
            $arr = $_arr = array();
            $arr['success'] = 0;
            //
            $setting['attention']['status'] = $fost['status'];
            $setting['attention']['content'] = value($fost, 'content', true);
            $setting['attention']['update'] = SYS_TIME;
            if ($this->ddb->update(table('users_al'), array('setting'=>array2string($setting)), array('id'=>$func['al']['id']))){
                $arr['success'] = 1;
                $arr['message'] = '保存成功';
            }else{
                $arr['message'] = '保存失败';
            }
            echo json_encode($arr); exit();
        }
        $this->cs->show(get_defined_vars());
    }

    /**
     * 编辑器选择
     */
    public function doWebalieditlist($a = null)
    {
        $user = $this->user->getuser();
        $func = $this->user->functions();
        //
        $page = $this->input->get('page');
        $keyval = $this->input->get('keyval');
        $wheresql = " `module`='reply' AND `status`='启用' AND `alid`=".$func['al']['id'];
        //搜索
        if ($keyval){
            $wheresql.= " AND `key` LIKE '%".$keyval."%' ";
        }
        $this->cs->show($a[1]?'alieditlist_'.$a[1]:'alieditlist', get_defined_vars());
    }


    public function doWebDel($parent = null)
    {
        $this->user->getuser();
        $func = $this->user->functions();
        //
        $id = value($parent, 1, 'int');
        $this->ddb->delete(table('reply'), array('id'=>$id, 'alid'=>$func['al']['id']));

        $arr = array();
        $arr['success'] = 1;
        $arr['message'] = '删除成功';
        echo json_encode($arr); exit();
    }


    /**
     * 删除功能执行
     * @param int $alid
     * @return bool
     */
    public function useDeleted($alid = 0) {
        db_delete(table("reply"), array('alid' => $alid));
        return true;
    }
}
