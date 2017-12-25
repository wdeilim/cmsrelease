<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sms extends CI_Model {

	public function __construct()
    {
        parent::__construct();
        $this->load->helper("communication");
        $this->load->helper("cloud");

	}

    /**
     * 检查网站是否支持短信
     * @return array
     */
    public function check() {
        global $_A;
        $arr = array();
        $arr['success'] = 0;
        $arr['message'] = '';
        $arr['cloudname'] = '';
        //
        if (!intval(SMS_OPEN)) {
            $arr['message'] = '系统未开通验证码功能！';
            return $arr;
        }
        $row = db_getone(table('setting'), array('title'=>'cloud'));
        $regsetting = string2array($row['content']);
        if($regsetting['cloudok'] != "1") {
            $arr['message'] = '系统未开通验证码功能！';
            return $arr;
        }
        if (empty($_A['u']['userid']) || !isset($_A['u']['sms'])) {
            $arr['message'] = '参数错误！';
            return $arr;
        }
        if ($_A['u']['sms'] < 1) {
            if ($_A['u']['point'] < 1) {
                $arr['message'] = '无法获取系统积分不足！';
                return $arr;
            }
            $proportion = intval(SMS_PROPORTION);
            if (empty($proportion)) $proportion = 10;
            db_update(table('users'), array('point[-]'=>1, 'sms[+]'=>$proportion), array('userid'=>$_A['u']['userid']));
            $_A['u']['point'] = $_A['u']['point'] - 1;
            $_A['u']['sms'] = $_A['u']['sms'] + $proportion;
            db_insert(table('users_point'), array(
                'userid'=>$_A['u']['userid'],
                'change'=>-1,
                'point'=>$_A['u']['point'],
                'pointtxt'=>'购买短信验证',
                'indate'=>SYS_TIME
            ));
        }
        if ($_A['u']['sms'] < 1) {
            $arr['message'] = '无法获取系统积分不足-2！';
            return $arr;
        }
        $arr['success'] = 1;
        $arr['cloudname'] = $regsetting['cloudname'];
        return $arr;
    }

    /**
     * 发送
     * @param string $phone         手机号码
     * @param int $text             附加内容（比如：①登录、找回密码等；②留空或者填0自动附加编号）
     * @param string $sessionname   session附加标识
     * @return array                array(success=>,message=>,code=>,num=>)、success=1 发送成功
     */
    public function send($phone, $text = 0, $sessionname = '') {
        global $_A;
        $arr = array();
        $arr['success'] = 0;
        $arr['message'] = '';
        $arr['code'] = 0;
        $arr['text'] = $text;
        //
        $check = $this->check();
        if (empty($check['success'])) {
            $arr['message'] = $check['message'];
            return $arr;
        }
        //
        if (!isMobile($phone)) {
            $arr['message'] = '手机号码错误！';
            return $arr;
        }
        //
        $repeat_user = intval($this->session->userdata('Sms:Repeat_user')) + 60;
        if ($repeat_user > SYS_TIME) {
            $arr['message'] = '发送频繁，60秒内只能发送一次！';
            return $arr;
        }
        //
        if ($text === 0) {
            $smsnum = rand(100,999);
            $this->session->set_userdata('Sms:Num_'.$sessionname.$phone, $smsnum);
            $text = '编号:'.$smsnum;
            $arr['text'] = $text;
            $arr['text_num'] = $smsnum;
        }
        $pars = array();
        $pars['title'] = $check['cloudname'];
        $pars['phone'] = $phone;
        $pars['text'] = $text;
        $pars['url'] = get_url();
        $dat = ihttp_post(CLOUD_URL.'sms/index.php', $pars);
        if (is_error($dat)) {
            $arr['message'] = '服务器繁忙请稍后再试！';
            return $arr;
        }
        $content = json_decode($dat['content'], true);
        if ($content['fee']) {
            db_update(table('users'), array('sms[-]'=>$content['fee']), array('userid'=>$_A['u']['userid']));
        }
        if ($content['ok'] == '1') {
            $this->session->set_userdata('Sms:Repeat_user', SYS_TIME);
            $arr['success'] = 1;
            if ($pars['text']) {
                $arr['message'] = '验证码发送成功（'.$pars['text'].'）！';
            }else{
                $arr['message'] = '验证码发送成功！';
            }
            $arr['code'] = $content['code'];
        }else{
            $arr['message'] = $content['errinfo'];
        }
        return $arr;
    }

    /**
     * 验证
     * @param string $phone         手机号码
     * @param string $code          所验证的验证码
     * @param int $text             附加内容（比如：①登录、找回密码等；②留空或者填0自动附加编号）
     * @param string $sessionname   session附加标识
     * @return array                array(success=>,message=>,code=>,num=>)、success=1 验证成功
     */
    public function verify($phone, $code = '', $text = 0, $sessionname = '') {
        $arr = array();
        $arr['success'] = 0;
        $arr['message'] = '';
        $arr['code'] = $code;
        $arr['text'] = $text;
        //
        $check = $this->check();
        if (empty($check['success'])) {
            $arr['message'] = $check['message'];
            return $arr;
        }
        //
        if (!isMobile($phone)) {
            $arr['message'] = '手机号码错误！';
            return $arr;
        }
        //
        if (empty($code)) {
            $arr['message'] = '验证码错误！';
            return $arr;
        }
        //
        if ($text === 0) {
            $smsnum = $this->session->userdata('Sms:Num_'.$sessionname.$phone);
            $text = '编号:'.$smsnum;
            $arr['text'] = $text;
            $arr['text_num'] = $smsnum;
        }
        //
        $pars = array();
        $pars['title'] = $check['cloudname'];
        $pars['phone'] = $phone;
        $pars['text'] = $text;
        $pars['method'] = 'view';
        $pars['code'] = trim($code);
        $dat = ihttp_post(CLOUD_URL.'sms/index.php', $pars);
        if (is_error($dat)) {
            $arr['message'] = '服务器繁忙请稍后再试！';
            return $arr;
        }
        $content = json_decode($dat['content'], true);
        if ($content['ok'] == '1') {
            $arr['success'] = 1;
            $arr['message'] = '验证成功！';
        }else{
            $arr['message'] = $content['errinfo'];
        }
        return $arr;
    }
}
?>