<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Wxopen {
    private $cache_path;

    public function __construct()
    {
        global $_A;
        $this->cache_path = BASE_PATH.'caches'.DIRECTORY_SEPARATOR.'token_ticket'.DIRECTORY_SEPARATOR;
        if (!isset($_A['openweixin'])) {
            $_A['openweixin'] = array();
            $file = BASE_PATH."caches".DIRECTORY_SEPARATOR."cache.openweixin.php";
            if (file_exists($file)) {
                $_A['openweixin'] = string2array(str_replace('<?php exit();?>', '', file_get_contents($file)));
                if ($_A['openweixin']['appid']) {
                    $this->cache_path.= $_A['openweixin']['appid'].DIRECTORY_SEPARATOR;
                    make_dir($this->cache_path);
                }
            }
        }
    }

    /**
     * 是否使用第三方
     * @param string $openweixin
     * @return bool|string
     */
    public function isopen($openweixin = '')
    {
        global $_A;
        if ($openweixin === true && $_A['openweixin']['open']) {
            return true;
        }
        $openweixin = $openweixin?$openweixin:trim($_A['al']['setting']['openweixin']);
        if ($openweixin && $_A['openweixin']['open'] && $_A['openweixin']['appid'] == $openweixin) {
            return $openweixin;
        }else{
            return false;
        }
    }

    /**
     * 获取公众号 token
     * @param $appid
     * @return mixed
     */
    public function get_accesstoken($appid)
    {
        global $_A;
        static $accesstoken = array();
        if (!is_null($accesstoken[$appid])) {
            return $accesstoken[$appid];
        }
        $file = $this->cache_path.$appid.".access_token.php";
        if (file_exists($file)) {
            include $file;
            if (isset($token_time) && $token_time + 7000 > SYS_TIME && isset($token_str)) {
                $accesstoken[$appid] = $token_str;
                return $token_str;
            }
        }
        $url = 'https://api.weixin.qq.com/cgi-bin/component/api_authorizer_token?component_access_token='.$this->get_token();
        $arg = array(
            'component_appid' => $_A['openweixin']['appid'],
            'authorizer_appid' => $appid,
            'authorizer_refresh_token' => $this->get_refresh($appid),
        );
        $CI =& get_instance();
        $CI->load->library('communication');
        $req = $CI->communication->ihttp_date($url, $arg);
        $ret = @json_decode($req['content'], true);
        if (empty($ret) || !empty($ret['errcode'])) {
            return false;
        }
        if ($ret['authorizer_refresh_token'] != $this->get_refresh($appid)) {
            $this->save_refresh($appid, $ret['authorizer_refresh_token']);
        }
        $token = $ret['authorizer_access_token'];
        if ($token) {
            file_put_contents($file,
                '<?php defined(\'BASEPATH\') OR exit(\'No direct script access allowed'.date("Y-m-d H:i:s").'\');
                $token_time='.time().';$token_str=\''.$token.'\'; ?>');
        }
        $accesstoken[$appid] = $token;
        return $token;
    }
    
    /**
     * 获取 token
     * @return bool|null
     */
    public function get_token()
    {
        global $_A;
        static $token = NULL;
        if (!is_null($token)) {
            return $token;
        }
        $file = $this->cache_path."openweixin_token.php";
        if (file_exists($file)) {
            include $file;
            if (isset($token_time) && $token_time + 7000 > SYS_TIME && isset($token_str)) {
                return $token_str;
            }
        }

        $url = 'https://api.weixin.qq.com/cgi-bin/component/api_component_token';
        $arg = array(
            'component_appid' => $_A['openweixin']['appid'],
            'component_appsecret' => $_A['openweixin']['secret'],
            'component_verify_ticket' => $this->get_ticket(),
        );
        $CI =& get_instance();
        $CI->load->library('communication');
        $req = $CI->communication->ihttp_date($url, $arg);
        $ret = @json_decode($req['content'], true);
        if ($ret && !empty($ret['component_access_token'])) {
            $token = $ret['component_access_token'];
            if ($token) {
                file_put_contents($file,
                    '<?php defined(\'BASEPATH\') OR exit(\'No direct script access allowed'.date("Y-m-d H:i:s").'\');
                $token_time='.time().';$token_str=\''.$token.'\'; ?>');
            }
            return $token;
        }
        return false;
    }

    /**
     * 获取配置
     * @return array
     */
    public function get_openweixin()
    {
        $file = BASE_PATH."caches".DIRECTORY_SEPARATOR."cache.openweixin.php";
        $openweixin = array();
        if (file_exists($file)) {
            $openweixin = string2array(str_replace('<?php exit();?>', '', file_get_contents($file)));
            $openweixin['applist'] = string2array($openweixin['applist']);
        }
        return $openweixin;
    }
    
    /**
     * 保存配置
     * @param $openweixin
     */
    public function save_openweixin($openweixin)
    {
        if (is_array($openweixin)) { array_filter($openweixin); }
        $file = BASE_PATH."caches".DIRECTORY_SEPARATOR."cache.openweixin.php";
        file_put_contents($file, '<?php exit();?>'.array2string(_Sys_Safe_Stop::addslashes_deep($openweixin)));
    }

    /**
     * 获取 ticket
     * @return string
     */
    public function get_ticket() 
    {
        $ticket = '';
        if (file_exists($this->cache_path."openweixin_ticket.php")) {
            include $this->cache_path."openweixin_ticket.php";
        }
        return $ticket;
    }

    /**
     * 保存 ticket
     * @param $ticket
     * @return bool
     */
    public function save_ticket($ticket)
    {
        file_put_contents($this->cache_path."openweixin_ticket.php",
            '<?php defined(\'BASEPATH\') OR exit(\'No direct script access allowed'.date("Y-m-d H:i:s").'\');$ticket=\''.$ticket.'\'; ?>');
        return true;
    }

    /**
     * 获取 refresh
     * @param $appid
     * @return string
     */
    public function get_refresh($appid)
    {
        $refresh = '';
        if (file_exists($this->cache_path.$appid.".access_refresh.php")) {
            include $this->cache_path.$appid.".access_refresh.php";
        }
        return $refresh;
    }

    /**
     * 保存 refresh
     * @param $appid
     * @param $refresh
     * @return bool
     */
    public function save_refresh($appid, $refresh)
    {
        file_put_contents($this->cache_path.$appid.".access_refresh.php",
            '<?php defined(\'BASEPATH\') OR exit(\'No direct script access allowed'.date("Y-m-d H:i:s").'\');$refresh=\''.$refresh.'\'; ?>');
        file_put_contents($this->cache_path.$appid.".access_token.php",
            '<?php defined(\'BASEPATH\') OR exit(\'No direct script access allowed'.date("Y-m-d H:i:s").'\');$token_time=0;$token_str=\'\'; ?>');
        return true;
    }

    /**
     * @param $arr
     * @param int $level
     * @return mixed|string
     */
    public function array2xml($arr, $level = 1) 
    {
        $s = $level == 1 ? "<xml>" : '';
        foreach ($arr as $tagname => $value) {
            if (is_numeric($tagname)) {
                $tagname = $value['TagName'];
                unset($value['TagName']);
            }
            if (!is_array($value)) {
                $s .= "<{$tagname}>" . (!is_numeric($value) ? '<![CDATA[' : '') . $value . (!is_numeric($value) ? ']]>' : '') . "</{$tagname}>";
            } else {
                $s .= "<{$tagname}>" . $this->array2xml($value, $level + 1) . "</{$tagname}>";
            }
        }
        $s = preg_replace("/([\x01-\x08\x0b-\x0c\x0e-\x1f])+/", ' ', $s);
        return $level == 1 ? $s . "</xml>" : $s;
    }

    /**
     * @param $message
     * @param string $encodingaeskey
     * @param string $appid
     * @return string
     */
    public function aes_encode($message, $encodingaeskey = '', $appid = '') {
        $key = base64_decode($encodingaeskey . '=');
        $text = $this->random(16) . pack("N", strlen($message)) . $message . $appid;

        $size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
        $module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
        $iv = substr($key, 0, 16);

        $block_size = 32;
        $text_length = strlen($text);
        $amount_to_pad = $block_size - ($text_length % $block_size);
        if ($amount_to_pad == 0) {
            $amount_to_pad = $block_size;
        }
        $pad_chr = chr($amount_to_pad);
        $tmp = '';
        for ($index = 0; $index < $amount_to_pad; $index++) {
            $tmp .= $pad_chr;
        }
        $text = $text . $tmp;
        mcrypt_generic_init($module, $key, $iv);
        $encrypted = mcrypt_generic($module, $text);
        mcrypt_generic_deinit($module);
        mcrypt_module_close($module);
        $encrypt_msg = base64_encode($encrypted);
        return $encrypt_msg;
    }

    /**
     * 随机数字
     * @param $length
     * @param bool $numeric
     * @return string
     */
    public function random($length, $numeric = FALSE) {
        $seed = base_convert(md5(microtime() . $_SERVER['DOCUMENT_ROOT']), 16, $numeric ? 10 : 35);
        $seed = $numeric ? (str_replace('0', '', $seed) . '012340567890') : ($seed . 'zZ' . strtoupper($seed));
        if ($numeric) {
            $hash = '';
        } else {
            $hash = chr(rand(1, 26) + rand(0, 1) * 32 + 64);
            $length--;
        }
        $max = strlen($seed) - 1;
        for ($i = 0; $i < $length; $i++) {
            $hash .= $seed{mt_rand(0, $max)};
        }
        return $hash;
    }
}
