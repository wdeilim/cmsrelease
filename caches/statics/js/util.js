(function(window) {
	var util = {};

	//JS目录地址（后面带 / ）
	util.jsUrl = function (script, i, me, src) {
		for (i in script) {
			src = script[i].src + ""; src = src.replace(/\\/g, '/');
			if (src && src.indexOf('/caches/statics/js/') !== -1) me = script[i];
		}
		var _thisScript = me || script[script.length - 1];
		me = _thisScript.src.replace(/\\/g, '/');
		return (me.indexOf('/caches/statics/js/') < 0 ? '.' : me.substring(0, me.indexOf('/caches/statics/js/'))) + "/caches/statics/js/";
	}(document.getElementsByTagName('script'));

	//网站根目录（后面带 / ）
	util.base_uri = function (baseUrl) {
		baseUrl = baseUrl.indexOf('/caches/statics/js/') < 0 ? '.' : baseUrl.substring(0, baseUrl.indexOf('/caches/statics/js/'));
		var _href = window.location.href;
		if (_href.indexOf(baseUrl + '/index.php?') !== -1) {
			baseUrl+= "/index.php?";
		}else if (_href.indexOf(baseUrl + '/index.php') !== -1) {
			baseUrl+= "/index.php";
		}
		return baseUrl + "/";
	}(util.jsUrl);

	//生成资源链接（后面带 / ）
	util.tomedia = function(src){
		if (src && (src.indexOf('http://') == 0 || src.indexOf('https://') == 0  || src.indexOf('ftp://') == 0 || src.indexOf('/') == 0)) {
			return src;
		} else {
			if (typeof src == 'undefined') { src = ""; }
			var _url = util.jsUrl;
			return (_url.indexOf('/caches/statics/js/') < 0 ? '.' : _url.substring(0, _url.indexOf('/caches/statics/js/'))) + "/" + src;
		}
	};

	//生成资源链接
	util.fillurl = function(src){
		return util.tomedia(src);
	};

	//获取对象
	util.obj = function(obj) {
		if (typeof obj == 'string' && obj != '') {
			if ($(obj).length > 0) {
				obj = $(obj);
			}else if ($("#" + obj).length > 0) {
				obj = $("#" + obj);
			}else if ($("." + obj).length > 0) {
				obj = $("." + obj);
			}
		}
		return obj;
	};

	//分享代码
	util.share = function(title, desc, img, url, callback, result) {
		if (typeof title == "object") {
			result = title.result;
		}
		require(['share'], function(){
			if (typeof jQuery.share != 'undefined') {
				$.share(title, desc, img, url, callback, result);
			}else{
				if (typeof result == 'function') {
					result(false);
				}
			}
		});
	};

	//拍照、选择照片
	util.photo = function(callback, result) {
		if (typeof callback == "object") {
			result = callback.result;
		}
		//
		util.loading();
		require(['photo'], function(photo){
			util.loaded();
			if (typeof photo == "undefined") {
				if (typeof result == 'function') {
					result(false);
				}
				return;
			}
			photo.choose(callback, result);
		});
	};

	//普通上传图片
	util.appupimg = function(callback, result) {
		util.loading();
		require(['photo'], function(photo){
			util.loaded();
			if (typeof photo == "undefined") {
				if (typeof result == 'function') {
					result(false);
				}
				return;
			}
			photo.images.maxWidth = photo.images.maxHeight = 0;
			if (typeof callback == "object") {
				result = callback.result;
				if (callback.width) {
					photo.images.maxWidth = callback.width;
				}
				if (callback.height) {
					photo.images.maxHeight = callback.height;
				}
				if (callback.maxWidth) {
					photo.images.maxWidth = callback.maxWidth;
				}
				if (callback.maxHeight) {
					photo.images.maxHeight = callback.maxHeight;
				}
				callback = callback.callback;
			}
			photo.upimg(callback);
			if (typeof result == 'function') {
				result(true);
			}
		});
	};

	//显示等待gif
	util.loading = function(text) {
		var loadingid = 'util-modal-loading';
		var modalobj = $('#' + loadingid);
		if(modalobj.length == 0) {
			$(document.body).append('<div id="' + loadingid + '" style="position:fixed;z-index:999999;top:0;left:0;width:100%;height:100%;"></div>');
			modalobj = $('#' + loadingid);
			var html =
				'<div style="position: absolute;z-index:1;top:0;left:0;width:100%;height:100%;background:rgba(0, 0, 0, 0.3);"></div>' +
				'<div style="text-align:center;background-color:rgba(0, 0, 0, 0.2);position:absolute;z-index:2;top:100px;left:0;margin:0;width:auto;padding:5px 12px;border-radius:5px;">'+
				'		<img style="width:40px;height:40px;vertical-align:middle;" src="'+util.tomedia('caches/statics/images/loading.gif')+'" title="正在努力加载...">' +
				'		<span style="margin-left:5px;color:#ffffff;display:none;"></span>' +
				'</div>';
			modalobj.html(html);
		}
		if (text) {
			modalobj.find("span").html(text).show();
		}else{
			modalobj.find("span").hide();
		}
		modalobj.show();
		modalobj.find("div").eq(1).css({top: $(window).height() / 2 - 25, left: ($(window).width() - modalobj.find("div").eq(1).width()) / 2});
		return modalobj;
	};

	//隐藏等待gif
	util.loaded = function(){
		var loadingid = 'util-modal-loading';
		var modalobj = $('#' + loadingid);
		if(modalobj.length > 0){
			modalobj.hide();
		}
	};

	util.alert = function(e, t, p, but, bk){
		require(['jquery.alert'], function(){
			if (typeof jQuery.alert != 'undefined') {
				$.alert(e, t, p, but, bk);
			}else{
				alert();
			}
		});
	};

	util.alertk = function(e, t, p, but, bk){
		require(['jquery.alert'], function(){
			if (typeof jQuery.alert != 'undefined') {
				$.alertk(e, t, p, but, bk);
			}else{
				alert();
			}
		});
	};

	util.confirm = function(e, qfun, cfun, q, c){
		require(['jquery.alert'], function(){
			$.confirm(e, qfun, cfun, q, c);
		});
	};

	util.showModal = function(msg, url, goonurl, isautohide){
		require(['jquery.alert'], function(){
			$.showModal(msg, url, goonurl, isautohide);
		});
	};

	util.keyauto = function(obj, trigger, classname) {
		require(['jquery.keyauto'], function(){
			util.obj(obj).keyauto(trigger, classname);
		});
	};

	util.ueditor = function(obj, result) {
		require(['baidueditor', 'ueditorzeroclip', 'ueditorlang'], function(UE, zcl){
			window.ZeroClipboard = zcl;
			var uedit = UE.getEditor(obj, {
				autoHeightEnabled:false,
				UEDITOR_HOME_URL: util.jsUrl + '/ueditor/',
				serverUrl: util.jsUrl + '/ueditor/php/controller.php'
			});
			uedit.ready(function() {
				if (typeof result == 'function') {
					result(uedit);
				}
			});
		});
	};

	util.clip = function(obj, str) {
		if(obj.clip) { return; }
		require(['jquery.zclip'], function(){
			util.obj(obj).zclip({
				path: util.jsUrl + '/zclip/ZeroClipboard.swf',
				copy: str,
				afterCopy: function(){
					util.alert("复制成功");
				}
			});
			obj.clip = true;
		});
	};

	util.colorpicker = function(obj, result) {
		require(['colorpicker'], function(){
			util.obj(obj).spectrum({
				className : "colorpicker",
				showInput: true,
				showInitial: true,
				showPalette: true,
				maxPaletteSize: 10,
				preferredFormat: "hex",
				change: function(color) {
					if (typeof result == 'function') {
						result(color);
					}
				},
				palette: [
					["rgb(0, 0, 0)", "rgb(67, 67, 67)", "rgb(102, 102, 102)", "rgb(153, 153, 153)","rgb(183, 183, 183)",
						"rgb(204, 204, 204)", "rgb(217, 217, 217)","rgb(239, 239, 239)", "rgb(243, 243, 243)", "rgb(255, 255, 255)"],
					["rgb(152, 0, 0)", "rgb(255, 0, 0)", "rgb(255, 153, 0)", "rgb(255, 255, 0)", "rgb(0, 255, 0)",
						"rgb(0, 255, 255)", "rgb(74, 134, 232)", "rgb(0, 0, 255)", "rgb(153, 0, 255)", "rgb(255, 0, 255)"],
					["rgb(230, 184, 175)", "rgb(244, 204, 204)", "rgb(252, 229, 205)", "rgb(255, 242, 204)", "rgb(217, 234, 211)",
						"rgb(208, 224, 227)", "rgb(201, 218, 248)", "rgb(207, 226, 243)", "rgb(217, 210, 233)", "rgb(234, 209, 220)",
						"rgb(221, 126, 107)", "rgb(234, 153, 153)", "rgb(249, 203, 156)", "rgb(255, 229, 153)", "rgb(182, 215, 168)",
						"rgb(162, 196, 201)", "rgb(164, 194, 244)", "rgb(159, 197, 232)", "rgb(180, 167, 214)", "rgb(213, 166, 189)",
						"rgb(204, 65, 37)", "rgb(224, 102, 102)", "rgb(246, 178, 107)", "rgb(255, 217, 102)", "rgb(147, 196, 125)",
						"rgb(118, 165, 175)", "rgb(109, 158, 235)", "rgb(111, 168, 220)", "rgb(142, 124, 195)", "rgb(194, 123, 160)",
						"rgb(166, 28, 0)", "rgb(204, 0, 0)", "rgb(230, 145, 56)", "rgb(241, 194, 50)", "rgb(106, 168, 79)",
						"rgb(69, 129, 142)", "rgb(60, 120, 216)", "rgb(61, 133, 198)", "rgb(103, 78, 167)", "rgb(166, 77, 121)",
						"rgb(133, 32, 12)", "rgb(153, 0, 0)", "rgb(180, 95, 6)", "rgb(191, 144, 0)", "rgb(56, 118, 29)",
						"rgb(19, 79, 92)", "rgb(17, 85, 204)", "rgb(11, 83, 148)", "rgb(53, 28, 117)", "rgb(116, 27, 71)",
						"rgb(91, 15, 0)", "rgb(102, 0, 0)", "rgb(120, 63, 4)", "rgb(127, 96, 0)", "rgb(39, 78, 19)",
						"rgb(12, 52, 61)", "rgb(28, 69, 135)", "rgb(7, 55, 99)", "rgb(32, 18, 77)", "rgb(76, 17, 48)"]
				]
			});
		});
	};

	util.map = function(obj, options, result) {
		if (typeof options == 'function') {
			result = options;
		}
		if (typeof options != 'object') {
			options = eval("(" + util.htmlspecialchars_decode(util.obj(obj).attr("data-map")) + ")") || {};
			if (typeof options != 'object') {
				options = eval("(" + util.htmlspecialchars_decode(util.obj(obj).val()) + ")") || {};
				if (typeof options != 'object') {
					options = {};
				}
			}
		}
		require(['baidu_map/jquery.baidu_map', 'map'], function(){
			var tthis = util.obj(obj);
			if (!tthis.attr("name")) tthis.attr("name", "map_" + Math.round(Math.random() * 10000));
			tthis.baidu_map(options);
			if (typeof result == 'function') {
				result(true);
			}
		});
	};

	util.htmlspecialchars_decode = function(str){
		str = str + "";
		str = str.replace(/&amp;/g, '&');
		str = str.replace(/&lt;/g, '<');
		str = str.replace(/&gt;/g, '>');
		str = str.replace(/&quot;/g, "''");
		str = str.replace(/&#039;/g, "'");
		return str;
	};

	//选择表情
	util.emotion = function(obj, targetobj, result) {
		var elm = util.obj(obj);
		var target = util.obj(targetobj);
		require(['jquery.caret', 'bootstrap', 'css!../css/emotions.css'],function($){
			$(function() {
				var emotions_html = '<table class="emotions" cellspacing="0" cellpadding="0"><tbody><tr><td><div class="eItem" style="background-position:0px 0;" data-title="微笑" data-code="微笑" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/0.gif"></div></td><td><div class="eItem" style="background-position:-24px 0;" data-title="撇嘴" data-code="撇嘴" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/1.gif"></div></td><td><div class="eItem" style="background-position:-48px 0;" data-title="色" data-code="色" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/2.gif"></div></td><td><div class="eItem" style="background-position:-72px 0;" data-title="发呆" data-code="发呆" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/3.gif"></div></td><td><div class="eItem" style="background-position:-96px 0;" data-title="得意" data-code="得意" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/4.gif"></div></td><td><div class="eItem" style="background-position:-120px 0;" data-title="流泪" data-code="流泪" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/5.gif"></div></td><td><div class="eItem" style="background-position:-144px 0;" data-title="害羞" data-code="害羞" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/6.gif"></div></td><td><div class="eItem" style="background-position:-168px 0;" data-title="闭嘴" data-code="闭嘴" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/7.gif"></div></td><td><div class="eItem" style="background-position:-192px 0;" data-title="睡" data-code="睡" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/8.gif"></div></td><td><div class="eItem" style="background-position:-216px 0;" data-title="大哭" data-code="大哭" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/9.gif"></div></td><td><div class="eItem" style="background-position:-240px 0;" data-title="尴尬" data-code="尴尬" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/10.gif"></div></td><td><div class="eItem" style="background-position:-264px 0;" data-title="发怒" data-code="发怒" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/11.gif"></div></td><td><div class="eItem" style="background-position:-288px 0;" data-title="调皮" data-code="调皮" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/12.gif"></div></td><td><div class="eItem" style="background-position:-312px 0;" data-title="呲牙" data-code="呲牙" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/13.gif"></div></td><td><div class="eItem" style="background-position:-336px 0;" data-title="惊讶" data-code="惊讶" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/14.gif"></div></td></tr><tr><td><div class="eItem" style="background-position:-360px 0;" data-title="难过" data-code="难过" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/15.gif"></div></td><td><div class="eItem" style="background-position:-384px 0;" data-title="酷" data-code="酷" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/16.gif"></div></td><td><div class="eItem" style="background-position:-408px 0;" data-title="冷汗" data-code="冷汗" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/17.gif"></div></td><td><div class="eItem" style="background-position:-432px 0;" data-title="抓狂" data-code="抓狂" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/18.gif"></div></td><td><div class="eItem" style="background-position:-456px 0;" data-title="吐" data-code="吐" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/19.gif"></div></td><td><div class="eItem" style="background-position:-480px 0;" data-title="偷笑" data-code="偷笑" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/20.gif"></div></td><td><div class="eItem" style="background-position:-504px 0;" data-title="可爱" data-code="可爱" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/21.gif"></div></td><td><div class="eItem" style="background-position:-528px 0;" data-title="白眼" data-code="白眼" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/22.gif"></div></td><td><div class="eItem" style="background-position:-552px 0;" data-title="傲慢" data-code="傲慢" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/23.gif"></div></td><td><div class="eItem" style="background-position:-576px 0;" data-title="饥饿" data-code="饥饿" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/24.gif"></div></td><td><div class="eItem" style="background-position:-600px 0;" data-title="困" data-code="困" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/25.gif"></div></td><td><div class="eItem" style="background-position:-624px 0;" data-title="惊恐" data-code="惊恐" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/26.gif"></div></td><td><div class="eItem" style="background-position:-648px 0;" data-title="流汗" data-code="流汗" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/27.gif"></div></td><td><div class="eItem" style="background-position:-672px 0;" data-title="憨笑" data-code="憨笑" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/28.gif"></div></td><td><div class="eItem" style="background-position:-696px 0;" data-title="大兵" data-code="大兵" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/29.gif"></div></td></tr><tr><td><div class="eItem" style="background-position:-720px 0;" data-title="奋斗" data-code="奋斗" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/30.gif"></div></td><td><div class="eItem" style="background-position:-744px 0;" data-title="咒骂" data-code="咒骂" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/31.gif"></div></td><td><div class="eItem" style="background-position:-768px 0;" data-title="疑问" data-code="疑问" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/32.gif"></div></td><td><div class="eItem" style="background-position:-792px 0;" data-title="嘘" data-code="嘘" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/33.gif"></div></td><td><div class="eItem" style="background-position:-816px 0;" data-title="晕" data-code="晕" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/34.gif"></div></td><td><div class="eItem" style="background-position:-840px 0;" data-title="折磨" data-code="折磨" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/35.gif"></div></td><td><div class="eItem" style="background-position:-864px 0;" data-title="衰" data-code="衰" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/36.gif"></div></td><td><div class="eItem" style="background-position:-888px 0;" data-title="骷髅" data-code=":!!!" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/37.gif"></div></td><td><div class="eItem" style="background-position:-912px 0;" data-title="敲打" data-code="敲打" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/38.gif"></div></td><td><div class="eItem" style="background-position:-936px 0;" data-title="再见" data-code="再见" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/39.gif"></div></td><td><div class="eItem" style="background-position:-960px 0;" data-title="擦汗" data-code="擦汗" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/40.gif"></div></td><td><div class="eItem" style="background-position:-984px 0;" data-title="抠鼻" data-code="抠鼻" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/41.gif"></div></td><td><div class="eItem" style="background-position:-1008px 0;" data-title="鼓掌" data-code="鼓掌" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/42.gif"></div></td><td><div class="eItem" style="background-position:-1032px 0;" data-title="糗大了" data-code="糗大了" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/43.gif"></div></td><td><div class="eItem" style="background-position:-1056px 0;" data-title="坏笑" data-code="坏笑" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/44.gif"></div></td></tr><tr><td><div class="eItem" style="background-position:-1080px 0;" data-title="左哼哼" data-code="左哼哼" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/45.gif"></div></td><td><div class="eItem" style="background-position:-1104px 0;" data-title="右哼哼" data-code="右哼哼" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/46.gif"></div></td><td><div class="eItem" style="background-position:-1128px 0;" data-title="哈欠" data-code="哈欠" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/47.gif"></div></td><td><div class="eItem" style="background-position:-1152px 0;" data-title="鄙视" data-code="鄙视" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/48.gif"></div></td><td><div class="eItem" style="background-position:-1176px 0;" data-title="委屈" data-code="委屈" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/49.gif"></div></td><td><div class="eItem" style="background-position:-1200px 0;" data-title="快哭了" data-code="快哭了" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/50.gif"></div></td><td><div class="eItem" style="background-position:-1224px 0;" data-title="阴险" data-code="阴险" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/51.gif"></div></td><td><div class="eItem" style="background-position:-1248px 0;" data-title="亲亲" data-code="亲亲" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/52.gif"></div></td><td><div class="eItem" style="background-position:-1272px 0;" data-title="吓" data-code="吓" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/53.gif"></div></td><td><div class="eItem" style="background-position:-1296px 0;" data-title="可怜" data-code="可怜" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/54.gif"></div></td><td><div class="eItem" style="background-position:-1320px 0;" data-title="菜刀" data-code="菜刀" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/55.gif"></div></td><td><div class="eItem" style="background-position:-1344px 0;" data-title="西瓜" data-code="西瓜" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/56.gif"></div></td><td><div class="eItem" style="background-position:-1368px 0;" data-title="啤酒" data-code="啤酒" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/57.gif"></div></td><td><div class="eItem" style="background-position:-1392px 0;" data-title="篮球" data-code="篮球" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/58.gif"></div></td><td><div class="eItem" style="background-position:-1416px 0;" data-title="乒乓" data-code="乒乓" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/59.gif"></div></td></tr><tr><td><div class="eItem" style="background-position:-1440px 0;" data-title="咖啡" data-code="咖啡" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/60.gif"></div></td><td><div class="eItem" style="background-position:-1464px 0;" data-title="饭" data-code="饭" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/61.gif"></div></td><td><div class="eItem" style="background-position:-1488px 0;" data-title="猪头" data-code="猪头" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/62.gif"></div></td><td><div class="eItem" style="background-position:-1512px 0;" data-title="玫瑰" data-code="玫瑰" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/63.gif"></div></td><td><div class="eItem" style="background-position:-1536px 0;" data-title="凋谢" data-code="凋谢" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/64.gif"></div></td><td><div class="eItem" style="background-position:-1560px 0;" data-title="示爱" data-code="示爱" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/65.gif"></div></td><td><div class="eItem" style="background-position:-1584px 0;" data-title="爱心" data-code="爱心" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/66.gif"></div></td><td><div class="eItem" style="background-position:-1608px 0;" data-title="心碎" data-code="心碎" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/67.gif"></div></td><td><div class="eItem" style="background-position:-1632px 0;" data-title="蛋糕" data-code="蛋糕" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/68.gif"></div></td><td><div class="eItem" style="background-position:-1656px 0;" data-title="闪电" data-code="闪电" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/69.gif"></div></td><td><div class="eItem" style="background-position:-1680px 0;" data-title="炸弹" data-code="炸弹" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/70.gif"></div></td><td><div class="eItem" style="background-position:-1704px 0;" data-title="刀" data-code="刀" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/71.gif"></div></td><td><div class="eItem" style="background-position:-1728px 0;" data-title="足球" data-code="足球" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/72.gif"></div></td><td><div class="eItem" style="background-position:-1752px 0;" data-title="瓢虫" data-code="瓢虫" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/73.gif"></div></td><td><div class="eItem" style="background-position:-1776px 0;" data-title="便便" data-code="便便" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/74.gif"></div></td></tr><tr><td><div class="eItem" style="background-position:-1800px 0;" data-title="月亮" data-code="月亮" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/75.gif"></div></td><td><div class="eItem" style="background-position:-1824px 0;" data-title="太阳" data-code="太阳" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/76.gif"></div></td><td><div class="eItem" style="background-position:-1848px 0;" data-title="礼物" data-code="礼物" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/77.gif"></div></td><td><div class="eItem" style="background-position:-1872px 0;" data-title="拥抱" data-code="拥抱" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/78.gif"></div></td><td><div class="eItem" style="background-position:-1896px 0;" data-title="强" data-code="强" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/79.gif"></div></td><td><div class="eItem" style="background-position:-1920px 0;" data-title="弱" data-code="弱" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/80.gif"></div></td><td><div class="eItem" style="background-position:-1944px 0;" data-title="握手" data-code="握手" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/81.gif"></div></td><td><div class="eItem" style="background-position:-1968px 0;" data-title="胜利" data-code="胜利" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/82.gif"></div></td><td><div class="eItem" style="background-position:-1992px 0;" data-title="抱拳" data-code="抱拳" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/83.gif"></div></td><td><div class="eItem" style="background-position:-2016px 0;" data-title="勾引" data-code="勾引" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/84.gif"></div></td><td><div class="eItem" style="background-position:-2040px 0;" data-title="拳头" data-code="拳头" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/85.gif"></div></td><td><div class="eItem" style="background-position:-2064px 0;" data-title="差劲" data-code="差劲" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/86.gif"></div></td><td><div class="eItem" style="background-position:-2088px 0;" data-title="爱你" data-code="爱你" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/87.gif"></div></td><td><div class="eItem" style="background-position:-2112px 0;" data-title="NO" data-code="NO" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/88.gif"></div></td><td><div class="eItem" style="background-position:-2136px 0;" data-title="OK" data-code="OK" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/89.gif"></div></td></tr><tr><td><div class="eItem" style="background-position:-2160px 0;" data-title="爱情" data-code="爱情" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/90.gif"></div></td><td><div class="eItem" style="background-position:-2184px 0;" data-title="飞吻" data-code="飞吻" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/91.gif"></div></td><td><div class="eItem" style="background-position:-2208px 0;" data-title="跳跳" data-code="跳跳" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/92.gif"></div></td><td><div class="eItem" style="background-position:-2232px 0;" data-title="发抖" data-code="发抖" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/93.gif"></div></td><td><div class="eItem" style="background-position:-2256px 0;" data-title="怄火" data-code="怄火" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/94.gif"></div></td><td><div class="eItem" style="background-position:-2280px 0;" data-title="转圈" data-code="转圈" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/95.gif"></div></td><td><div class="eItem" style="background-position:-2304px 0;" data-title="磕头" data-code="磕头" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/96.gif"></div></td><td><div class="eItem" style="background-position:-2328px 0;" data-title="回头" data-code="回头" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/97.gif"></div></td><td><div class="eItem" style="background-position:-2352px 0;" data-title="跳绳" data-code="跳绳" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/98.gif"></div></td><td><div class="eItem" style="background-position:-2376px 0;" data-title="挥手" data-code="挥手" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/99.gif"></div></td><td><div class="eItem" style="background-position:-2400px 0;" data-title="激动" data-code="激动" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/100.gif"></div></td><td><div class="eItem" style="background-position:-2424px 0;" data-title="街舞" data-code="街舞" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/101.gif"></div></td><td><div class="eItem" style="background-position:-2448px 0;" data-title="献吻" data-code="献吻" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/102.gif"></div></td><td><div class="eItem" style="background-position:-2472px 0;" data-title="左太极" data-code="左太极" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/103.gif"></div></td><td><div class="eItem" style="background-position:-2496px 0;" data-title="右太极" data-code="右太极" data-gifurl="http://res.mail.qq.com/zh_CN/images/mo/DEFAULT2/104.gif"></div></td></tr></tbody></table><div class="emotionsGif" style=""></div>';
				$(elm).popover({
					html: true,
					content: emotions_html,
					placement:"bottom"
				});
				$(elm).one('shown.bs.popover', function(){
					$(elm).next().mouseleave(function(){
						$(elm).popover('hide');
					});
					$(elm).next().delegate(".eItem", "mouseover", function(){
						var emo_img = '<img src="'+$(this).attr("data-gifurl")+'" alt="mo-'+$(this).attr("data-title")+'" />';
						var emo_txt = '/'+$(this).attr("data-code");
						$(elm).next().find(".emotionsGif").html(emo_img);
					});
					$(elm).next().delegate(".eItem", "click", function(){
						$(target).setCaret();
						var emo_txt = '/'+$(this).attr("data-code");
						$(target).insertAtCaret(emo_txt);
						$(elm).popover('hide');
						if($.isFunction(result)) {
							result(emo_txt, elm, target);
						}
					});
				});
			});
		});
	};


	util.emoji = function(result){
        var footer = '<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>';
        util.dialog(
            '请选择表情',
            [util.base_uri + 'web/system/utility/emoji/selectEmojiComplete/'],
            footer,
            {containerName:'icon-container'},
            function(modalobj){
                modalobj.modal({'keyboard': false});
                modalobj.find('.modal-dialog').css({'width':'70%'});
                modalobj.find('.modal-body').css({'height':'70%','overflow-y':'scroll'});
                modalobj.modal('show');
                window.selectEmojiComplete = function(emoji){
                    if($.isFunction(result)){
                        result(emoji);
                        modalobj.modal('hide');
                    }
                };
            }
        );
	};

    util.icon = function(result) {
        var footer = '<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>';
        util.dialog(
            '请选择图标',
            [util.base_uri + 'web/system/utility/icon/selectIconComplete/'],
            footer,
            {containerName:'icon-container'},
            function(modalobj){
                modalobj.modal({'keyboard': false});
                modalobj.find('.modal-dialog').css({'width':'70%'});
                modalobj.find('.modal-body').css({'height':'70%','overflow-y':'scroll'});
                modalobj.modal('show');

                window.selectIconComplete = function(ico){
                    if($.isFunction(result)){
                        result(ico);
                        modalobj.modal('hide');
                    }
                };
            }
        );
    };

    util.image = function(options, oresult) {
        if (typeof options != "object") {
            options = {val:options};
        }
        var valobj = options.valobj?options.valobj:options.val,
            btnobj = options.btnobj?options.btnobj:options.btn,
            imgobj = options.imgobj?options.imgobj:options.img,
            option = options.options?options.options:options.option,
            result = options.result?options.result:options.callback;
        if ($.isFunction(oresult)) { result = oresult;}
        if (!valobj) { return; }
        if (imgobj) {
            var ithis = util.obj(imgobj);
            if (ithis.is('input') || ithis.is('textarea') || ithis.is('select')) {
                ithis.after('<img src="">');
                imgobj = ithis.next("img");
            }else if (!ithis.is('img')){
                ithis.append('<img src="">');
                imgobj = ithis.find("img");
            }
        }
        if (!btnobj) { btnobj = valobj; }
        if (!$.isFunction(result)) { result = function() {} }
        require(['uploader'], function(uploader){
            uploader.image(util.obj(valobj), util.obj(btnobj), util.obj(imgobj), option, result);
        });
    };

	util.cookie = {
		'prefix' : '',
		// 保存 Cookie
		'set' : function(name, value, seconds) {
			expires = new Date();
			expires.setTime(expires.getTime() + (1000 * (seconds?seconds:86400)));
			document.cookie = this.name(name) + "=" + escape(value) + "; expires=" + expires.toGMTString() + "; path=/";
		},
		// 获取 Cookie
		'get' : function(name) {
			cookie_name = this.name(name) + "=";
			cookie_length = document.cookie.length;
			cookie_begin = 0;
			while (cookie_begin < cookie_length)
			{
				value_begin = cookie_begin + cookie_name.length;
				if (document.cookie.substring(cookie_begin, value_begin) == cookie_name)
				{
					var value_end = document.cookie.indexOf ( ";", value_begin);
					if (value_end == -1)
					{
						value_end = cookie_length;
					}
					return unescape(document.cookie.substring(value_begin, value_end));
				}
				cookie_begin = document.cookie.indexOf ( " ", cookie_begin) + 1;
				if (cookie_begin == 0)
				{
					break;
				}
			}
			return null;
		},
		// 清除 Cookie
		'del' : function(name) {
			var expireNow = new Date();
			document.cookie = this.name(name) + "=" + "; expires=Thu, 01-Jan-70 00:00:01 GMT" + "; path=/";
		},
		'name' : function(name) {
			return this.prefix + name;
		}
	};

	util.dialog = function(title, content, footer, options, result) {
		if (typeof content == 'function') {
			result = content;
			content = null;
		}else if (typeof footer == 'function') {
			result = footer;
			footer = null;
		}else if (typeof options == 'function') {
			result = options;
			options = null;
		}
		require(['bootstrap'], function(){
			if(!options) {
				options = {};
			}
			if(!options.containerName) {
				options.containerName = 'modal-message';
			}
			var modalobj = $('#' + options.containerName);
			if(modalobj.length == 0) {
				$(document.body).append('<div id="' + options.containerName + '" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true"></div>');
				modalobj = $('#' + options.containerName);
			}
			var html =
				'<div class="modal-dialog">'+
				'	<div class="modal-content">';
			if(title) {
				html +=
					'<div class="modal-header">'+
					'	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>'+
					'	<h3>' + title + '</h3>'+
					'</div>';
			}
			if(content) {
				if(!$.isArray(content)) {
					html += '<div class="modal-body">'+ content + '</div>';
				} else {
					html += '<div class="modal-body">正在加载中</div>';
				}
			}
			if(footer) {
				html +=
					'<div class="modal-footer">'+ footer + '</div>';
			}
			html += '	</div></div>';
			modalobj.html(html);
			if(content && $.isArray(content)) {
				var embed = function(c) {
					modalobj.find('.modal-body').html(c);
				};
				if(content.length == 2) {
					$.post(content[0], content[1]).success(embed);
				} else {
					$.get(content[0]).success(embed);
				}
			}
			if (typeof result == 'function') {
				result(modalobj);
			}
		});
	};

	util.message = function(msg, redirect, type, result){
		if (typeof redirect == 'function') {
			result = redirect;
			redirect = null;
		}else if (typeof type == 'function') {
			result = type;
			type = null;
		}
		require(['bootstrap'], function(){
			if(!redirect && !type){
				type = 'info';
			}
			if($.inArray(type, ['success', 'error', 'info', 'warning']) == -1) {
				type = '';
			}
			if(type == '') {
				type = redirect == '' ? 'error' : 'success';
			}

			var icons = {
				success : 'check-circle',
				error :'times-circle',
				info : 'info-circle',
				warning : 'exclamation-triangle'
			};
			var p = '';
			if(redirect && redirect.length > 0){
				if(redirect == 'back'){
					p = '<p>[<a href="javascript:;" onclick="history.go(-1)">返回上一页</a>] &nbsp; [<a href="./?refresh">回首页</a>]</p>';
				} else if(redirect == 'refresh') {
					redirect = location.href;
					p = '<p><a href="' + redirect + '" target="main" data-dismiss="modal" aria-hidden="true">如果你的浏览器在 <span id="timeout"></span> 秒后没有自动跳转，请点击此链接</a></p>';
				} else {
					p = '<p><a href="' + redirect + '" target="main" data-dismiss="modal" aria-hidden="true">如果你的浏览器在 <span id="timeout"></span> 秒后没有自动跳转，请点击此链接</a></p>';
				}
			}
			var content =
				'			<i class="pull-left fa fa-4x fa-'+icons[type]+'"></i>'+
				'			<div class="pull-left"><p>'+ msg +'</p>' +
				p +
				'			</div>'+
				'			<div class="clearfix"></div>';
			var footer =
				'			<button type="button" class="btn btn-default" data-dismiss="modal">确认</button>';
			util.dialog('系统提示', content, footer, {'containerName' : 'modal-message'}, function(modalobj){
				modalobj.find('.modal-content').addClass('alert alert-'+type);
				if(redirect) {
					var timer = '';
					timeout = 3;
					modalobj.find("#timeout").html(timeout);
					modalobj.on('show.bs.modal', function(){doredirect();});
					modalobj.on('hide.bs.modal', function(){timeout = 0;doredirect(); });
					modalobj.on('hidden.bs.modal', function(){modalobj.remove();});
					function doredirect() {
						timer = setTimeout(function(){
							if (timeout <= 0) {
								modalobj.modal('hide');
								clearTimeout(timer);
								window.location.href = redirect;
								return;
							} else {
								timeout--;
								modalobj.find("#timeout").html(timeout);
								doredirect();
							}
						}, 1000);
					}
				}
				modalobj.modal('show');
				if (typeof result == 'function') {
					result(modalobj);
				}
			});
		});
	};

	//
	if (typeof define === "function" && define.amd) {
		define(function(){
			return util;
		});
	} else {
		window.util = util;
	}
})(window);