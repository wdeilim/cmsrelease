<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Del extends CI_Model {

	public function __construct()
    {
        parent::__construct();
        $user = $this->user->getuser();
        if ($user['admin'] != 1) {
            message("没有权限");
        }
	}

    /**
     * 删除用户(公司)【删除公司 > 删除接入 > 删除功能】
     * @param int $userid
     * @return bool
     */
    public function deluser($userid = 0) {
        $urow = is_array($userid)?$userid:$this->ddb->getone("SELECT userid FROM ".table('users'), array('userid'=>intval($userid)));
        if ($urow) {
            if ($this->ddb->delete(table('users'), array('userid'=>$urow['userid']))) {
                $this->ddb->delete(table('users_point'), array('userid'=>$urow['userid']));
                $allist = $this->ddb->getall("SELECT * FROM ".table('users_al'), array('userid'=>$urow['userid']));
                foreach($allist AS $item) {
                    self::delal($item);
                }
            }else{
                return false;
            }
        }
        return true;
    }

    /**
     * 删除接入【删除公司 > 删除接入 > 删除功能】
     * @param int $alid
     * @return bool
     */
    public function delal($alid = 0) {
        $alrow = is_array($alid)?$alid:$this->ddb->getone("SELECT id FROM ".table('users_al'), array('id'=>intval($alid)));
        if ($alrow) {
            if ($this->ddb->delete(table('users_al'), array('id'=>$alrow['id']))) {
                $this->ddb->delete(table('core_paylog'), array('alid'=>$alrow['id']));
                $this->ddb->delete(table('fans'), array('alid'=>$alrow['id']));
                $uflist = $this->ddb->getall("SELECT * FROM ".table('users_functions'), array('alid'=>$alrow['id']));
                foreach($uflist AS $item) {
                    self::deluse($item);
                }
            }else{
                return false;
            }
        }
        return true;
    }

    /**
     * 删除功能【删除公司 > 删除接入 > 删除功能】
     * @param int $ufid
     * @return bool
     */
    public function deluse($ufid = 0) {
        $ufrow = is_array($ufid)?$ufid:$this->ddb->getone("SELECT id,fid,alid FROM ".table('users_functions'), array('id'=>intval($ufid)));
        if ($ufrow) {
            if ($this->ddb->delete(table('users_functions'), array('id'=>$ufrow['id']))) {
                $fun = $this->ddb->getone("SELECT title_en FROM ".table('functions'), array('id'=>intval($ufrow['fid'])));
                if ($fun) {
                    $classname = "ES_".ucfirst($fun['title_en']);
                    if (!class_exists($classname)) {
                        $this->base->inc(FCPATH.'addons/'.$fun['title_en'].'/site.php');
                    }
                    if (class_exists($classname)) {
                        $es_site = new $classname();
                        if (method_exists($es_site, 'useDeleted')) {
                            $es_site->useDeleted($ufrow['alid']);
                        }
                    }
                    $this->ddb->delete(table('reply'), array('alid'=>$ufrow['alid'], 'module'=>$fun['title_en']));
                }
            }else{
                return false;
            }
        }
        return true;
    }
}
?>