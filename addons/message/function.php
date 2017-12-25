<?php

if (!function_exists('message_text'))
{
	function message_text($list) {
		global $_A;
		$rehtml = '';
		$textarr = string2array($list['text']);
		if ($list['err']) {
			$rehtml.= '<span style="color:#ff0000">【'.$list['err'].'】</span>';
		}
		switch ($list['msgtype'])
		{
			case "image":		//图片
				$rehtml.= '<img src="'.fillurl($list['text']).'" style="max-width:300px;max-height:100px;cursor:-webkit-zoom-in;" onclick="zoomin(this);">';
				break;
			case "voice":		//语音
				if ($textarr['recognition']) {
					$rehtml.= '<audio src="'.message_amr2mp3($textarr['text'], $list['alid'], $list['indate']).'" controls="controls">您的浏览器不支持 audio 标签。</audio><br/>';
					$rehtml.= '识别：'.$textarr['recognition'].'<br/>';
					$rehtml.= '<a href="'.fillurl($textarr['text']).'" class="library-link" target="_blank">下载播放内容</a>';
				}else{
					$rehtml.= '<audio src="'.message_amr2mp3($list['text'], $list['alid'], $list['indate']).'" controls="controls">您的浏览器不支持 audio 标签。</audio><br/>';
					$rehtml.= '<a href="'.fillurl($list['text']).'" class="library-link" target="_blank">下载播放内容</a>';
				}
				break;
			case "video":		//视频
				if ($textarr['recognition']) {
					$rehtml.= '<video src="'.fillurl($textarr['text']).'" controls="controls">您的浏览器不支持 video 标签。</video><br/>';
					$rehtml.= '<video src="语音识别：'.$textarr['recognition'].'<br/>';
				}else{
					$rehtml.= '<video src="'.fillurl($list['text']).'" controls="controls">您的浏览器不支持 video 标签。</video><br/>';
				}
				$rehtml.= '<a href="'.fillurl($list['text']).'" class="library-link" target="_blank">下载播放内容</a>';
				break;
			case "shortvideo":	//小视频
				$rehtml.= '<video src="'.fillurl($list['text']).'" controls="controls">您的浏览器不支持 video 标签。</video><br/>';
				$rehtml.= '<a href="'.fillurl($list['text']).'" class="library-link" target="_blank">下载播放内容</a>';
				break;
			case "location":	//地理位置
				$rehtml.= '位置: <a href="http://apis.map.qq.com/uri/v1/marker?marker=coord:'.$textarr['latitude'].','.$textarr['longitude'].';title:'.urlencode($textarr['text']).'" title="点击可查看地理位置" class="library-link" target="_blank">'.$textarr['text'].' ('.$textarr['latitude'].','.$textarr['longitude'].')</a>';
				break;
			case "location_event":	//上报地理位置
				$rehtml.= '位置: <a href="http://apis.map.qq.com/uri/v1/marker?marker=coord:'.$textarr['latitude'].','.$textarr['longitude'].';title:'.urlencode($textarr['text']).'" title="点击可查看地理位置" class="library-link" target="_blank">'.$textarr['text'].' ('.$textarr['latitude'].','.$textarr['longitude'].')</a>';
				break;
			case "enter_agent":	//进入窗口事件
				$rehtml.= $textarr['text'].', 时间: '.$textarr['time_cn'];
				break;
			case "link":		//链接地址
				$rehtml.= '链接: <a href="'.$textarr['url'].'" class="library-link" target="_blank">'.$textarr['text'].'</a><br/>';
				$rehtml.= '描述: '.$textarr['description'];
				break;
			case "click":		//点击菜单
				$rehtml.= '点击菜单: '.$list['text'];
				break;
			case "view":		//点击菜单链接
				$rehtml.= '跳转链接菜单: <a href="'.$list['text'].'" class="library-link" target="_blank">'.$list['text'].'</a>';
				break;
			case "follow":		//关注、扫描二维码关注
			  	if ($textarr['text']) {
					$rehtml.= $textarr['text']." (参数: ".$textarr['eventkey'].")";
				}else{
					$rehtml.= $list['text'];
				}
				break;
			case "scan":		//扫描二维码进入
				$rehtml.= $textarr['text']." (参数: ".$textarr['eventkey'].")";
				break;
			case "imagetext":	//图文链接
				$rehtml.= '图文链接:<br/>';
				$rehtml.= '<a href="'.value($textarr,'url').'" class="library-link" target="_blank" title="'.value($textarr,'desc').'"><img src="'.fillurl(value($textarr,'img')).'" style="max-width:300px;max-height:100px;"><br/>'.value($textarr,'title').'</a>';
				break;
			case "material":	//素材库
				$rehtml.= '<a class="library-link" href="'.$_A['url']['1'].'library/?al='.$_A['al']['id'].'&id='.$list['text'].'" target="_blank">广播素材: '.$list['text'].'</a>';
				break;
			case "scancode_push":		//扫码显示
				$rehtml.= '扫'.str_replace(array('qrcode','barcode'), array('二维码','条形码'), $textarr['scantype']).'显示 (结果: '.$textarr['scanresult'].')';
				break;
			case "scancode_waitmsg":	//扫码发送
				$rehtml.= '扫'.str_replace(array('qrcode','barcode'), array('二维码','条形码'), $textarr['scantype']).'发送 (参数: '.$textarr['text'].'; 结果: '.$textarr['scanresult'].')';
				break;
			default:			//其他
				$rehtml.= $list['text'];
		}
		$setting = string2array($list['setting']);
		if (isset($setting['corp_name'])) {
			$rehtml.= " <span style='padding-left:8px;'>(".$setting['corp_name'].")</span>";
		}
		return $rehtml;
	}
}


if (!function_exists('message_amr2mp3'))
{
	function message_amr2mp3($path, $alid = 0, $indate = 0) {
		$path = fillurl($path);
		if (substr(strtolower($path), -4) == ".amr") {
			$path = CLOUD_GATEWAY.'?method=amr2mp3&alid='.$alid.'&indate='.$indate.'&path='.urlencode($path);
		}
		return $path;
	}
}