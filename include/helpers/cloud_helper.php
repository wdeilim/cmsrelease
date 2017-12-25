<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
define('CLOUD_URL', 'http://cloud.vwins.cn/');
define('CLOUD_GATEWAY', CLOUD_URL.'gateway.php');
define('CLOUD_BBS_URL', 'http://bbs.vwins.cn/');

function cloud_m_prepare($name) {
    $pars = cloud_get_cloud_array();
    if (is_error($pars)) { return $pars; }
    $pars['baseuri'] = BASE_URI;
    $pars['method'] = 'module_check';
    $pars['module'] = $name;
    $dat = ihttp_post(CLOUD_GATEWAY, $pars);
    if (is_error($dat)) {
        return $dat;
    }
    if (_cloud_error($dat['content'])) {
        return error('-1', _cloud_errmsg($dat['content']));
    }
    if ($dat['content'] == 'protect') {
        return error('-1', "此模块已设置版权保护，您只能通过 <a href='".CLOUD_URL."store/' target='_blank' style='color:red;text-decoration:underline;'>云应用中心</a> 来安装。");
    }
    return true;
}


function cloud_upgrade($name, $version = '') {
    $pars = cloud_get_cloud_array();
    if (is_error($pars)) { return $pars; }
    $pars['baseuri'] = BASE_URI;
    $pars['method'] = 'module_upgrade';
    $pars['module'] = $name;
    $pars['version'] = $version;
    $dat = ihttp_post(CLOUD_GATEWAY, $pars);
    if (is_error($dat)) {
        return $dat;
    }
    if (_cloud_error($dat['content'])) {
        return error('-1', _cloud_errmsg($dat['content']));
    }
    return true;
}

function cloud_uninstall($name, $version = '') {
    $pars = cloud_get_cloud_array();
    if (is_error($pars)) { return $pars; }
    $pars['baseuri'] = BASE_URI;
    $pars['method'] = 'module_uninstall';
    $pars['module'] = $name;
    $pars['version'] = $version;
    $dat = ihttp_post(CLOUD_GATEWAY, $pars);
    if (is_error($dat)) {
        return $dat;
    }
    if (_cloud_error($dat['content'])) {
        return error('-1', _cloud_errmsg($dat['content']));
    }
    return true;
}

function cloud_prepare() {
    $row = db_getone(table('setting'), array('title'=>'cloud'));
    $regsetting = string2array($row['content']);
    if($regsetting['cloudok'] != "1") {
        return error('-1', "您的程序需要在 <a href='".weburl('system/settings/cloud')."' style='color:red;text-decoration:underline;'>云中心</a> 注册你的站点资料, 来接入云服务后才能使用相应功能.");
    }
    return true;
}

function cloud_category($name, $version = '') {
    $pars = cloud_get_cloud_array();
    if (is_error($pars)) { return $pars; }
    $pars['baseuri'] = BASE_URI;
    $pars['method'] = 'get_category';
    $pars['module'] = $name;
    $pars['version'] = $version;
    $dat = ihttp_post(CLOUD_GATEWAY, $pars);
    if (is_error($dat)) {
        return array();
    }
    if (_cloud_error($dat['content'])) {
        return array();
    }
    return json_decode($dat['content'], true);
}

function cloud_namepass($name = '', $pass = '') {
    $pars = array();
    $pars['baseuri'] = BASE_URI;
    $pars['method'] = 'module_namepass';
    $pars['name'] = $name;
    $pars['pass'] = $pass;
    $pars['url'] = BASE_URL;
    $dat = ihttp_post(CLOUD_GATEWAY, $pars);
    if (is_error($dat)) {
        return $dat;
    }
    if (_cloud_error($dat['content'])) {
        return error('-1', _cloud_errmsg($dat['content']));
    }
    if ($dat['content'] == 'errurl') {
        return error('-1', "您的帐号已经绑定地址，请先解除绑定！<br/>【详情请看】：<br/>进入：<a href='".CLOUD_URL."index.php#1/12' target='_blank'>云中心&gt;&gt;账号管理&gt;&gt;绑定网址</a>。<br/><a href='".CLOUD_URL."index.php#1/12' target='_blank' style='color:red;text-decoration:underline;'>点此进入云中心绑定</a>");
    }elseif ($dat['content'] != 'ok') {
        return error('-1', '账号或密码错误');
    }
    return true;
}

function cloud_cloudkey($name = '', $pass = '') {
    $pars = cloud_get_cloud_array();
    if (is_error($pars)) { return $pars; }
    $pars['baseuri'] = BASE_URI;
    $pars['method'] = 'get_cloudkey';
    $dat = ihttp_post(CLOUD_GATEWAY, $pars);
    if (is_error($dat)) {
        return $dat;
    }
    if (_cloud_error($dat['content'])) {
        return error('-1', _cloud_errmsg($dat['content']));
    }
    return json_decode($dat['content'], true);
}

function cloud_ext_module_manifest($name, $oldversion = '', $nowversion = '') {
    $pars = cloud_get_cloud_array();
    if (is_error($pars)) { return $pars; }
    $pars['baseuri'] = BASE_URI;
    $pars['method'] = 'module_manifest';
    $pars['module'] = $name;
    $pars['version'] = $oldversion;
    $pars['nowversion'] = $nowversion;
    $dat = ihttp_post(CLOUD_GATEWAY, $pars);
    if (is_error($dat)) {
        return $dat;
    }
    if (_cloud_error($dat['content'])) {
        return error('-1', _cloud_errmsg($dat['content']));
    }
    if (empty($dat['content'])) {
        return error('-1', '读取失败');
    }
    return ext_module_manifest_parse($dat['content']);
}

function cloud_ext_module_files($name, $isupgrade = 0, $oldversion = '') {
    $pars = cloud_get_cloud_array();
    if (is_error($pars)) { return $pars; }
    $pars['baseuri'] = BASE_URI;
    $pars['method'] = 'module_files';
    $pars['module'] = $name;
    $pars['upgrade'] = $isupgrade;
    $pars['version'] = $oldversion;
    $dat = ihttp_post(CLOUD_GATEWAY, $pars);
    if (is_error($dat)) {
        return $dat;
    }
    if (_cloud_error($dat['content'])) {
        return error('-1', _cloud_errmsg($dat['content']));
    }
    if (empty($dat['content'])) {
        return error('-1', '读取失败');
    }
    return $dat['content'];
}

function cloud_ext_module_downs($files, $name) {
    $lists = json_decode($files, true);
    if ($lists) {
        @set_time_limit(0);
        @ini_set("max_execution_time", 3600);
        foreach($lists AS $f) {
            $spath = BASE_PATH.'addons/'.$name.$f['path'];
            if ($f['type'] == "dir") {
                make_dir($spath);
            }elseif ($f['type'] == "file") {
                $pars = cloud_get_cloud_array();
                if (is_error($pars)) { return $pars; }
                $pars['baseuri'] = BASE_URI;
                $pars['method'] = 'module_downs';
                $pars['module'] = $name;
                $pars['path'] = $f['path'];
                $dat = ihttp_post(CLOUD_GATEWAY, $pars);
                if (is_error($dat)) {
                    return $dat;
                }
                if (_cloud_error($dat['content'])) {
                    return error('-1', _cloud_errmsg($dat['content']));
                }
                file_put_contents($spath, $dat['content']);
            }
        }

    }
    return true;
}

function cloud_module_install_file($name, $inname = '') {
    if(!strexists($inname, '.php') && !strexists($inname, '.sql')) {
        return $inname;
    }else{
        $pars = cloud_get_cloud_array();
        if (is_error($pars)) { return $pars; }
        $pars['baseuri'] = BASE_URI;
        $pars['method'] = 'module_install_file';
        $pars['module'] = $name;
        $dat = ihttp_post(CLOUD_GATEWAY, $pars);
        if (is_error($dat)) {
            return $dat;
        }
        if (_cloud_error($dat['content'])) {
            return error('-1', _cloud_errmsg($dat['content']));
        }
        if (empty($dat['content'])) {
            return error('-1', '读取失败');
        }
        if (substr($dat['content'],0,5) == "php::" || substr($dat['content'],0,5) == "sql::") {
            make_dir(BASE_PATH.'addons/'.$name.'/');
            file_put_contents(BASE_PATH.'addons/'.$name.'/'.$inname, substr($dat['content'],5));
        }
        return true;
    }
}

function cloud_module_upgrade_file($name, $inname = '') {
    if(!strexists($inname, '.php') && !strexists($inname, '.sql')) {
        return $inname;
    }else{
        $pars = cloud_get_cloud_array();
        if (is_error($pars)) { return $pars; }
        $pars['baseuri'] = BASE_URI;
        $pars['method'] = 'module_upgrade_file';
        $pars['module'] = $name;
        $dat = ihttp_post(CLOUD_GATEWAY, $pars);
        if (is_error($dat)) {
            return $dat;
        }
        if (_cloud_error($dat['content'])) {
            return error('-1', _cloud_errmsg($dat['content']));
        }
        if (empty($dat['content'])) {
            return error('-1', '读取失败');
        }
        if (substr($dat['content'],0,5) == "php::" || substr($dat['content'],0,5) == "sql::") {
            make_dir(BASE_PATH.'addons/'.$name.'/');
            file_put_contents(BASE_PATH.'addons/'.$name.'/'.$inname, substr($dat['content'],5));
        }
        return true;
    }
}

function cloud_module_uninstall_file($name, $inname = '') {
    if(!strexists($inname, '.php') && !strexists($inname, '.sql')) {
        return $inname;
    }else{
        $pars = cloud_get_cloud_array();
        if (is_error($pars)) { return $pars; }
        $pars['baseuri'] = BASE_URI;
        $pars['method'] = 'module_uninstall_file';
        $pars['module'] = $name;
        $dat = ihttp_post(CLOUD_GATEWAY, $pars);
        if (is_error($dat)) {
            return $dat;
        }
        if (_cloud_error($dat['content'])) {
            return error('-1', _cloud_errmsg($dat['content']));
        }
        if (empty($dat['content'])) {
            return error('-1', '读取失败');
        }
        if (substr($dat['content'],0,5) == "php::" || substr($dat['content'],0,5) == "sql::") {
            make_dir(BASE_PATH.'addons/'.$name.'/');
            file_put_contents(BASE_PATH.'addons/'.$name.'/'.$inname, substr($dat['content'],5));
        }
        return true;
    }
}

function cloud_pro_upgrade($release, $version) {
    $pars = cloud_get_cloud_array();
    if (is_error($pars)) { return $pars; }
    $pars['baseuri'] = BASE_URI;
    $pars['method'] = 'pro_upgrade_release';
    $pars['release'] = $release;
    $pars['version'] = $version;
    $dat = ihttp_post(CLOUD_GATEWAY, $pars);
    if (is_error($dat)) {
        return $dat;
    }
    if (_cloud_error($dat['content'])) {
        return error('-1', _cloud_errmsg($dat['content']));
    }
    if (empty($dat['content'])) {
        return error('-1', '当前版本已经是最新版本');
    }
    return $dat['content'];
}

function cloud_pro_downs($release, $version, $files) {
    make_dir(BASE_PATH.'caches/upgrade_retain/'.ES_RELEASE.'/');
    make_dir(BASE_PATH.'caches/upgrade_retain/'.ES_RELEASE.'_newfile/');
    $lists = json_decode($files, true);
    if ($lists) {
        @set_time_limit(0);
        foreach($lists AS $f) {
            $spath = _cloud_normalizePath(BASE_PATH.'caches/upgrade_retain/'.ES_RELEASE.'_newfile/'.$f['path']);
            if ($f['type'] == "dir") {
                make_dir($spath);
            }elseif ($f['type'] == "file") {
                $pars = cloud_get_cloud_array();
                if (is_error($pars)) { return $pars; }
                $pars['baseuri'] = BASE_URI;
                $pars['method'] = 'pro_upgrade_downs';
                $pars['release'] = $release;
                $pars['version'] = $version;
                $pars['path'] = $f['path'];
                $dat = ihttp_post(CLOUD_GATEWAY, $pars);
                if (is_error($dat)) {
                    return $dat;
                }
                if (_cloud_error($dat['content'])) {
                    return error('-1', _cloud_errmsg($dat['content']));
                }
                file_put_contents($spath, $dat['content']);
            }
        }
    }
    return true;
}

function cloud_release_copys($release = ES_RELEASE) {
    $newpath = BASE_PATH.'caches/upgrade_retain/'.$release.'_newfile/';
    $lists = _cloud_file_list_empty($newpath);
    if ($lists) {
        @set_time_limit(0);
        $strp = realpath($newpath);
        foreach($lists AS $f) {
            $_path = str_replace($strp, '', realpath($f['path']));
            $spath = _cloud_normalizePath(BASE_PATH.$_path);
            $opath = _cloud_normalizePath(BASE_PATH.'caches/upgrade_retain/'.$release.'/'.$_path);
            if ($f['type'] == "dir") {
                make_dir($spath);
                make_dir($opath);
            }elseif ($f['type'] == "file") {
                if (!file_exists($opath)) {
                    @copy($spath, $opath);
                }
                $isco = copy($f['path'], $spath);
                if (!$isco) {
                    if ($content = file_get_contents($f['path'])) {
                        file_put_contents($spath, $content);
                    }
                }else{
                    @unlink($f['path']);
                }
            }
        }
    }
    _cloud_remove_Dir($newpath);
    return true;
}

function cloud_pro_sql($release, $version) {
    $pars = cloud_get_cloud_array();
    if (is_error($pars)) { return $pars; }
    $pars['baseuri'] = BASE_URI;
    $pars['method'] = 'pro_upgrade_sql';
    $pars['release'] = $release;
    $pars['version'] = $version;
    $dat = ihttp_post(CLOUD_GATEWAY, $pars);
    if (is_error($dat)) {
        return $dat;
    }
    if (_cloud_error($dat['content'])) {
        return error('-1', _cloud_errmsg($dat['content']));
    }
    if (empty($dat['content'])) {
        return error('-1', '当前版本已经是最新版本');
    }
    return $dat['content'];
}

function cloud_get_cloud_array() {
    $cloud = db_getone(table('setting'), array('title'=>'cloud'));
    $contentset = string2array($cloud['content']);
    if($contentset['cloudok'] != "1") {
        return error('-1', '您的程序未绑定云中心！');
    }
    return array(
        '_cloud_release'=>ES_RELEASE,
        '_cloud_baseurl'=>BASE_URL,
        '_cloud_name'=>$contentset['cloudname'],
        '_cloud_pass'=>$contentset['cloudpass']
    );
}

function _cloud_error($str) {
    if (leftexists($str, "error::")) {
        return true;
    }else{
        return false;
    }
}

function _cloud_errmsg($str) {
    if (leftexists($str, "error::")) {
        return substr($str, 7);
    }else{
        return $str;
    }
}

function _cloud_normalizePath($path) {
    $parts = array();
    $path = str_replace('\\', '/', $path);
    $path = preg_replace('/\/+/', '/', $path);
    $segments = explode('/', $path);
    foreach($segments as $segment)  {
        if($segment != '.') {
            $test = array_pop($parts);
            if(is_null($test)) {
                $parts[] = $segment;
            }else if($segment == '..'){
                if($test == '..'){
                    $parts[] = $test;
                }
                if($test == '..' || $test == ''){
                    $parts[] = $segment;
                }
            }else{
                $parts[] = $test;
                $parts[] = $segment;
            }
        }
    }
    return implode(DIRECTORY_SEPARATOR, $parts);
}

function _cloud_file_list_empty($path , $arr = array()){
    $lists = $arr;
    if ($handle = opendir($path)) {
        while (false !== ($file = readdir($handle))) {
            if ($file != "." && $file != "..") {
                if (is_dir($path."/".$file)) {
                    if (!_cloud_is_empty_dir($path."/".$file)) {
                        $lists[] = array(
                            'type'=>'dir',
                            'path'=>$path."/".$file
                        );
                        $lists = _cloud_file_list_empty($path."/".$file, $lists);
                    }
                } else {
                    $lists[] = array(
                        'type'=>'file',
                        'path'=>$path."/".$file
                    );
                }
            }
        }
    }
    return $lists;
}

function _cloud_is_empty_dir($path , $is_empty = true){
    if ($handle = opendir($path)) {
        while (false !== ($file = readdir($handle))) {
            if ($file != "." && $file != "..") {
                if (is_dir($path."/".$file)) {
                    $is_empty = _cloud_is_empty_dir($path."/".$file, $is_empty);
                } else {
                    $is_empty = false;
                }
                if ($is_empty == false) break;
            }
        }
    }
    return $is_empty;
}

function _cloud_remove_Dir($dirName){
    if(!is_dir($dirName)) {
        @unlink($dirName);
        return false;
    }
    $handle = @opendir($dirName);
    while(($file = @readdir($handle)) !== false) {
        if($file!='.'&&$file!='..') {
            $dir = $dirName . '/' . $file;
            is_dir($dir)?_cloud_remove_Dir($dir):@unlink($dir);
        }
    }
    closedir($handle);
    return rmdir($dirName) ;
}