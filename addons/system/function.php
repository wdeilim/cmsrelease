<?php
defined('BASEPATH') OR exit('No direct script access allowed');
error_reporting(0);

function ext_module_convert($manifest) {
    $module = array(
        'title' => $manifest['application']['title'],
        'title_en' => $manifest['application']['title_en'],
        'version' => $manifest['application']['version'],
        'ability' => $manifest['application']['ability'],
        'content' => $manifest['application']['content'],
        'author' => $manifest['application']['author'],
        'url' => $manifest['application']['url'],
        'oauth' => intval($manifest['application']['oauth']),
        'reply' => intval($manifest['application']['reply']),
        'setting' => array(
            'bindings' => $manifest['bindings'],
        ),
    );
    $module['setting'] = array2string($module['setting']);
    return $module;
}

function ext_module_manifest($modulename) {
    $filename = BASE_PATH . 'addons/' . $modulename . '/manifest.xml';
    if (!file_exists($filename)) {
        return array();
    }
    $xml = file_get_contents($filename);
    return ext_module_manifest_parse($xml);
}

function _ext_module_manifest_entries($elm) {
    $ret = array();
    if(!empty($elm)) {
        $call = $elm->getAttribute('call');
        $embed = $elm->getAttribute('embed');
        if(!empty($call)) {
            $ret[] = array('call' => $call);
        }
        $entries = $elm->getElementsByTagName('entry');
        for($i = 0; $i < $entries->length; $i++) {
            $entry = $entries->item($i);
            $row = array(
                'title' => $entry->getAttribute('title'),
                'do' => $entry->getAttribute('do'),
                'direct' => intval($entry->getAttribute('direct')),
                'state' => $entry->getAttribute('state'),
                'call' => $entry->getAttribute('call'),
                'embed' => $entry->getAttribute('embed')
            );
            if (!is_bool($row['embed']) && !in_array($row['embed'], array('true','false','1','0'))) {
                $row['embed'] = $embed;
            }
            $row['embed'] = intval(str_replace(array('true','false'), array('1','0'), $row['embed']));
            if(!empty($row['title']) && !empty($row['do'])) {
                $ret[] = $row;
            }
        }
    }
    return $ret;
}

function ext_module_manifest_parse($xml) {
    $dom = new DOMDocument();
    $dom->loadXML($xml);
    $manifest = array();
    if($dom->schemaValidateSource(ext_module_manifest_validate())) {
        $root = $dom->getElementsByTagName('manifest')->item(0);
        $vcode = explode(',', $root->getAttribute('versionCode'));
        $manifest['versions'] = array();
        if(is_array($vcode)) {
            foreach($vcode as $v) {
                $v = trim($v);
                if(!empty($v)) {
                    $manifest['versions'][] = $v;
                }
            }
            $manifest['versions'][] = '1.0';
            $manifest['versions'] = array_unique($manifest['versions']);
        }
        $manifest['install'] = $root->getElementsByTagName('install')->item(0)->textContent;
        $manifest['uninstall'] = $root->getElementsByTagName('uninstall')->item(0)->textContent;
        $manifest['upgrade'] = $root->getElementsByTagName('upgrade')->item(0)->textContent;
        $application = $root->getElementsByTagName('application')->item(0);
        $manifest['application'] = array(
            'title' => trim($application->getElementsByTagName('title')->item(0)->textContent),
            'title_en' => trim($application->getElementsByTagName('title_en')->item(0)->textContent),
            'version' => trim($application->getElementsByTagName('version')->item(0)->textContent),
            'ability' => trim($application->getElementsByTagName('ability')->item(0)->textContent),
            'content' => trim($application->getElementsByTagName('content')->item(0)->textContent),
            'author' => trim($application->getElementsByTagName('author')->item(0)->textContent),
            'url' => trim($application->getElementsByTagName('url')->item(0)->textContent),
            'oauth' => trim($application->getElementsByTagName('oauth')->item(0)->textContent),
            'reply' => trim($application->getElementsByTagName('reply')->item(0)->textContent),
        );
        if (!in_array($manifest['application']['oauth'], array('0', '1'))) {
            $manifest['application']['oauth'] = 0;
        }
        if (!in_array($manifest['application']['reply'], array('0', '1'))) {
            $manifest['application']['reply'] = 0;
        }
        $bindings = $root->getElementsByTagName('bindings')->item(0);
        if(!empty($bindings)) {
            $points = ext_module_bindings();
            if (!empty($points)) {
                $ps = array_keys($points);
                $manifest['bindings'] = array();
                foreach($ps as $p) {
                    $define = $bindings->getElementsByTagName($p)->item(0);
                    $manifest['bindings'][$p] = _ext_module_manifest_entries($define);
                }
            }
        }
    } else {
        $err = error_get_last();
        if($err['type'] == 2) {
            return $err['message'];
        }
    }
    return $manifest;
}


function ext_module_bindings() {
    static $bindings = array(
        'reply' => array(
            'name' => 'reply',
            'title' => '规则列表菜单',
            'desc' => '规则列表是定义可重复使用或者可创建多次的活动的功能入口(管理后台Web操作), 每个活动对应一条规则. 一般呈现为图文消息, 点击后进入定义好的某次活动中.'
        ),
        'cover' => array(
            'name' => 'cover',
            'title' => '功能封面',
            'desc' => '功能封面是定义微站里一个独立功能的入口(手机端操作), 将呈现为一个图文消息, 点击后进入微站系统中对应的功能.'
        ),
        'menu' => array(
            'name' => 'menu',
            'title' => '管理中心导航菜单',
            'desc' => '管理中心导航菜单将会在管理中心生成一个导航入口(管理后台Web操作), 用于对模块定义的内容进行管理.'
        ),
        'setting' => array(
            'name' => 'setting',
            'title' => '参数设置',
            'desc' => '规则的参数设置或在导航菜单中的参数设置.'
        ),
        'home' => array(
            'name' => 'home',
            'title' => '微官网首页导航',
            'desc' => '在微官网(微站)的首页上显示相关功能的链接入口(手机端操作), 一般用于通用功能的展示.'
        ),
        'vip'=> array(
            'name' => 'vip',
            'title' => '会员卡个人中心导航',
            'desc' => '在会员卡的个人中心上显示相关功能的链接入口(手机端操作), 一般用于个人信息, 或针对个人的数据的展示.'
        )
    );
    return $bindings;
}

function ext_module_manifest_validate() {
    $xsd = <<<TPL
<?xml version="1.0" encoding="utf-8"?>
<xs:schema xmlns="http://www.vwins.cn" targetNamespace="http://www.vwins.cn" xmlns:xs="http://www.w3.org/2001/XMLSchema" elementFormDefault="qualified">
    <xs:element name="entry">
		<xs:complexType>
			<xs:attribute name="title" type="xs:string" />
			<xs:attribute name="do" type="xs:string" />
			<xs:attribute name="direct" type="xs:boolean" />
			<xs:attribute name="state" type="xs:string" />
			<xs:attribute name="call" type="xs:string" />
			<xs:attribute name="embed" type="xs:string" />
		</xs:complexType>
	</xs:element>
	<xs:element name="manifest">
		<xs:complexType>
			<xs:all>
				<xs:element name="application" minOccurs="1" maxOccurs="1">
					<xs:complexType>
						<xs:all>
							<xs:element name="title" type="xs:string" minOccurs="1" maxOccurs="1" />
							<xs:element name="title_en" type="xs:string"  minOccurs="1" maxOccurs="1" />
							<xs:element name="version" type="xs:string"  minOccurs="1" maxOccurs="1" />
							<xs:element name="ability" type="xs:string"  minOccurs="1" maxOccurs="1" />
							<xs:element name="content" type="xs:string"  minOccurs="1" maxOccurs="1" />
							<xs:element name="author" type="xs:string"  minOccurs="1" maxOccurs="1" />
							<xs:element name="url" type="xs:string"  minOccurs="1" maxOccurs="1" />
							<xs:element name="oauth" type="xs:string"  minOccurs="0" maxOccurs="1" />
							<xs:element name="reply" type="xs:string"  minOccurs="0" maxOccurs="1" />
						</xs:all>
					</xs:complexType>
				</xs:element>
				<xs:element name="bindings" minOccurs="0" maxOccurs="1">
					<xs:complexType>
						<xs:all>
							<xs:element name="reply" minOccurs="0" maxOccurs="1">
								<xs:complexType>
									<xs:sequence>
										<xs:element ref="entry" minOccurs="0" maxOccurs="unbounded" />
									</xs:sequence>
									<xs:attribute name="call" type="xs:string" />
									<xs:attribute name="embed" type="xs:boolean" />
								</xs:complexType>
							</xs:element>
							<xs:element name="cover" minOccurs="0" maxOccurs="1">
								<xs:complexType>
									<xs:sequence>
										<xs:element ref="entry" minOccurs="0" maxOccurs="unbounded" />
									</xs:sequence>
									<xs:attribute name="call" type="xs:string" />
									<xs:attribute name="embed" type="xs:boolean" />
								</xs:complexType>
							</xs:element>
							<xs:element name="menu" minOccurs="0" maxOccurs="1">
								<xs:complexType>
									<xs:sequence>
										<xs:element ref="entry" minOccurs="0" maxOccurs="unbounded" />
									</xs:sequence>
									<xs:attribute name="call" type="xs:string" />
									<xs:attribute name="embed" type="xs:boolean" />
								</xs:complexType>
							</xs:element>
							<xs:element name="setting" minOccurs="0" maxOccurs="1">
								<xs:complexType>
									<xs:sequence>
										<xs:element ref="entry" minOccurs="0" maxOccurs="unbounded" />
									</xs:sequence>
									<xs:attribute name="call" type="xs:string" />
									<xs:attribute name="embed" type="xs:boolean" />
								</xs:complexType>
							</xs:element>
							<xs:element name="home" minOccurs="0" maxOccurs="1">
								<xs:complexType>
									<xs:sequence>
										<xs:element ref="entry" minOccurs="0" maxOccurs="unbounded" />
									</xs:sequence>
									<xs:attribute name="call" type="xs:string" />
									<xs:attribute name="embed" type="xs:boolean" />
								</xs:complexType>
							</xs:element>
							<xs:element name="vip" minOccurs="0" maxOccurs="1">
								<xs:complexType>
									<xs:sequence>
										<xs:element ref="entry" minOccurs="0" maxOccurs="unbounded" />
									</xs:sequence>
									<xs:attribute name="call" type="xs:string" />
									<xs:attribute name="embed" type="xs:boolean" />
								</xs:complexType>
							</xs:element>
						</xs:all>
					</xs:complexType>
				</xs:element>
				<xs:element name="install" type="xs:string" minOccurs="0" maxOccurs="1" />
				<xs:element name="uninstall" type="xs:string" minOccurs="0" maxOccurs="1" />
				<xs:element name="upgrade" type="xs:string" minOccurs="0" maxOccurs="1" />
			</xs:all>
			<xs:attribute name="versionCode" type="xs:string" />
		</xs:complexType>
	</xs:element>
</xs:schema>
TPL;
    return trim($xsd);
}

function ext_file_get_contents($path = '') {
	$content = "";
	if (file_exists($path)) {
		$content = file_get_contents($path);
	}
	return $content;
}