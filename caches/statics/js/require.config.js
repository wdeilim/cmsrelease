window.vwins_path = {
	js: function (script, i, me, src) {
		for (i in script) {
			src = script[i].src + ""; src = src.replace(/\\/g, '/');
			if (src && src.indexOf('/caches/statics/js/') !== -1) me = script[i];
		}
		var _thisScript = me || script[script.length - 1];
		me = _thisScript.src.replace(/\\/g, '/');
		return (me.indexOf('/caches/statics/js/') < 0 ? '.' : me.substring(0, me.indexOf('/caches/statics/js/'))) + "/caches/statics/js/";
	}(document.getElementsByTagName('script')),
	uri: function (script, i, me, src) {
		for (i in script) {
			src = script[i].src + ""; src = src.replace(/\\/g, '/');
			if (src && src.indexOf('/caches/statics/js/') !== -1) me = script[i];
		}
		var _thisScript = me || script[script.length - 1];
		me = _thisScript.src.replace(/\\/g, '/');
		me = me.indexOf('/caches/statics/js/') < 0 ? '.' : me.substring(0, me.indexOf('/caches/statics/js/'));
		var _href = window.location.href;
		if (_href.indexOf(me + '/index.php?') !== -1) {
			me+= "/index.php?";
		}else if (_href.indexOf(me + '/index.php') !== -1) {
			me+= "/index.php";
		}
		return me + "/";
	}(document.getElementsByTagName('script')),
	module: function (script, i, me, src) {
		for (i in script) {
			src = script[i].src + ""; src = src.replace(/\\/g, '/');
			if (src && src.indexOf('/caches/statics/js/') !== -1) me = script[i];
		}
		var _thisScript = me || script[script.length - 1];
		me = _thisScript.src.replace(/\\/g, '/');
		me = me.indexOf('/caches/statics/js/') < 0 ? '.' : me.substring(0, me.indexOf('/caches/statics/js/'));
		var _href = window.location.href;
		if (_href.indexOf(me + '/index.php?') !== -1) {
			me+= "/index.php?";
		}else if (_href.indexOf(me + '/index.php') !== -1) {
			me+= "/index.php";
		}
		me = me + "/";
		if (_href.indexOf(me + 'app/') !== -1) {
			me = _href.substring((me + 'app/').length);
		}else if (_href.indexOf(me + 'web/') !== -1) {
			me = _href.substring((me + 'web/').length);
		}
		me = me.substring(0, me.indexOf('/'));
		return me;
	}(document.getElementsByTagName('script'))
};

require.config({
	baseUrl: window.vwins_path.js,
	paths: {
		'jquery': 'jquery-1.11.0',
		'jquery.ui': 'jquery-ui-1.11.3.min.js',
		'jquery.caret': 'jquery.caret',
		'jquery.jplayer': 'jplayer/jquery.jplayer.min',
		'jquery.zclip': 'zclip/jquery.zclip.min',
		'bootstrap': 'bootstrap.min',
		'bootstrap.switch': 'bootstrap-switch.min',
		'angular': 'angular.min',
		'angular.sanitize': 'angular-sanitize.min',
		'underscore': 'underscore-min',
		'chart': 'chart.min',
		'moment': 'moment',
		'filestyle': 'bootstrap-filestyle.min',
		'datetimepicker': 'datetimepicker/bootstrap-datetimepicker.min',
		'daterangepicker': 'daterangepicker/daterangepicker',
		'colorpicker': 'colorpicker/spectrum',
		'jquery.wookmark': 'jquery.wookmark.min',
		'jquery.qrcode': 'jquery.qrcode.min',
		'raty': 'raty.min',
		'css': 'css.min',
		'json2' : 'json2',
		'util' : 'util',
		'baidueditor': 'ueditor/ueditor',
		'ueditorlang': 'ueditor/lang/zh-cn/zh-cn',
		'ueditorzeroclip': 'ueditor/third-party/zeroclipboard/ZeroClipboard.min',
		'share' : window.vwins_path.uri + 'web/system/require_share/' + ((document.location.search.indexOf("?")!==-1)?document.location.search:'?') + '&module=' + window.vwins_path.module + '&url=' + encodeURIComponent(document.location.href),
		'photo' : window.vwins_path.uri + 'web/system/require_photo/' + ((document.location.search.indexOf("?")!==-1)?document.location.search:'?') + '&module=' + window.vwins_path.module + '&url=' + encodeURIComponent(document.location.href),
		'jweixin' : 'http://res.wx.qq.com/open/js/jweixin-1.0.0',
		'jalipay' : 'https://static.alipay.com/aliBridge/1.0.0/aliBridge.min',
		'map': 'http://api.map.baidu.com/getscript?v=2.0&ak=eDsGxG65jw27rKR2hGfhRIBp&services=&t=' + new Date().getTime()
	},
	shim:{
		'jquery.ui': {
			exports: "$",
			deps: ['jqueryload']
		},
		'jquery.caret': {
			exports: "$",
			deps: ['jqueryload']
		},
		'jquery.jplayer': {
			exports: "$",
			deps: ['jqueryload']
		},
		'bootstrap': {
			exports: "$",
			deps: ['jqueryload', 'css!../css/bootstrap.min.css']
		},
		'bootstrap.switch': {
			exports: "$",
			deps: ['bootstrap', 'css!../css/bootstrap-switch.min.css']
		},
		'angular': {
			exports: 'angular',
			deps: ['jqueryload']
		},
		'angular.sanitize': {
			exports: 'angular',
			deps: ['angular']
		},
		'emotion': {
			deps: ['jqueryload']
		},
		'chart': {
			exports: 'Chart'
		},
		'filestyle': {
			exports: '$',
			deps: ['bootstrap']
		},
		'daterangepicker': {
			exports: '$',
			deps: ['bootstrap', 'moment', 'css!../js/daterangepicker/daterangepicker.css']
		},
		'datetimepicker' : {
			exports : '$',
			deps: ['jqueryload', 'css!../js/datetimepicker/bootstrap-datetimepicker.min.css']
		},
		'colorpicker': {
			exports: '$',
			deps: ['css!../js/colorpicker/spectrum.css']
		},
		'map': {
			exports: 'BMap'
		},
		'util': {
			exports: 'util',
			deps: ['jqueryload']
		},
		'json2': {
			exports: 'JSON'
		},
		'jquery.wookmark': {
			exports: "$",
			deps: ['jqueryload']
		},
		'jquery.qrcode': {
			exports: "$",
			deps: ['jqueryload']
		},
		'baidueditor': {
			deps: ['ueditor/ueditor.config', 'css!ueditor/themes/default/css/ueditor']
		},
		'ueditorlang':{
			deps: ['baidueditor']
		}
	}
});