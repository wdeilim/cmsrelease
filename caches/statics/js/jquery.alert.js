/**
 *
 * @param e 提示内容，===0则关闭提示
 * @param t 自动关闭时间，默认2000，===0则不自动关闭
 * @param p 点击关闭，赋值则不关闭
 * @param but 赋值显示按钮名称
 */
jQuery.alert = function(e, t, p, but, bk) {
    var alertvisible = $("div.jQuery-ui-alert").is(":visible");
    $("div.jQuery-ui-alert").remove();
	$("div.jQuery-ui-alert-back").remove();
    if (e === 0) return;
	var m = Math.round(Math.random() * 10000);
    if (but) e+= but;
	var n = '<div class="jQuery-ui-alert" style="position:fixed;top:0;left:0;padding:15px 10px;min-width:100px;opacity:1;min-height:25px;text-align:center;color:#fff;display:block;z-index:2147483647;border-radius:3px;background-color: rgba(51,51,51,.9); opacity:1;font-size:14px; line-height:22px;" id="jQuery-ui-alert-' + m + '" >' + e + '</div>' +
        '<div class="jQuery-ui-alert-back" style="display:none;z-index:2147483646" id="jQuery-ui-alert-back-' + m + '"></div>';
	$("body").append(n);
	var nobjbg = $('#jQuery-ui-alert-back-' + m);
    nobjbg.css({
        "width":"100%",
        "height":$(document).height(),
        "position":"absolute",
        "top":"0px",
        "left":"0px",
        "background-color":"#cccccc",
        "opacity":"0.2"
    });
    if (!bk) nobjbg.show();
    var nobj = $('#jQuery-ui-alert-' + m);
    if (!p)	nobj.click(function(){ nobj.removeClass("jQuery-ui-alert-style").fadeOut(); nobjbg.hide(); });
    if (!p)	nobjbg.click(function(){ nobj.removeClass("jQuery-ui-alert-style").fadeOut(); nobjbg.hide(); });
	var i = $(window).width(),
	s = $(window).height(),
	o = nobj.width()+20,
	u = nobj.height(),
	l = (i - o) / 2,
    top = (s - u) / 2 - 20,
    tot = top * 0.7;
	i > o && nobj.css("left", parseInt(l-5));
	i > o && nobj.css("right", parseInt(l-5));
	s > u && nobj.css("top", top);
	l < 5 && nobj.css("margin", "0 5px");
    var style = $("style#jQuery-ui-alert-style");
    if (s > u && style.attr("data-top") != top) {
        style.remove(); $("<style id='jQuery-ui-alert-style' data-top='" + top + "'></style>").text(".jQuery-ui-alert-style{animation:jQuery_ui_alert_style 0.5s ease forwards;-moz-animation:jQuery_ui_alert_style 0.5s ease forwards;-webkit-animation:jQuery_ui_alert_style 0.5s ease forwards;-o-animation:jQuery_ui_alert_style 0.5s ease forwards}@keyframes jQuery_ui_alert_style{from{top:" + tot + "px;opacity:0}to{top:" + top + "px;opacity:1}}@-moz-keyframes jQuery_ui_alert_style{from{top:" + tot + "px;opacity:0}to{top:" + top + "px;opacity:1}}@-webkit-keyframes jQuery_ui_alert_style{from{top:" + tot + "px;opacity:0}to{top:" + top + "px;opacity:1}}@-o-keyframes jQuery_ui_alert_style{from{top:" + tot + "px;opacity:0}to{top:" + top + "px;opacity:1}}").appendTo($("head"));
    }
    if (alertvisible === true) {
        nobj.show();
    }else{
        nobj.addClass("jQuery-ui-alert-style").show();
    }
	if (t === 0) return;
	setTimeout(function() { nobj.removeClass("jQuery-ui-alert-style").fadeOut(); nobjbg.hide(); }, t || 2000)
};
jQuery.alertk = function(e, t, p, but) {
    $.alert(e, t, p, but, 1)
};
jQuery.alertb = function(e, but, diy) {
    if (!but) but = "确定";
    var _click =  (!diy)?'onclick="$.alert(0)" ':'';
    $.alert(e, 0, 1, '<div '+_click+' style="text-align:center;padding:5px;border-top:1px solid #ECECEC;margin:15px -10px -15px;">'+but+'</div>')
};
jQuery.confirm = function(e, qfun, cfun, q, c) {
    if (typeof e == "object") {
        if (e.title === undefined) {
            if (e.content !== undefined) {
                e.title = e.content;
            }
        }
        var _tempbut = '<div style="margin:15px -10px -15px;border-top:1px solid #ECECEC;display:-webkit-box;">';
        if (Object.prototype.toString.call(e.button) !== '[object Array]') {
            e.button = e.button ? [e.button] : [];
        }
        var _m = Math.round(Math.random() * 10000);
        $.each(e.button, function (i, val) {
            var attcss = '';
            if (i > 0) { attcss = 'border-left:1px solid #6F6F6F;margin-left:-1px'; }
            _tempbut+= '<div id="jQuery-ui-confirmq-'+(_m+i)+'" ' +
                'style="display:block;-webkit-box-flex:1;text-align:center;padding:5px 8px;cursor:pointer;'+attcss+'">'+val.title+'</div>';
        });
        _tempbut+= '<div style="clear: both;"></div>';
        _tempbut+= '</div>';
        $.alert(e.title, 0, 1, _tempbut);
        $.each(e.button, function (i, val) {
            $('#jQuery-ui-confirmq-'+(_m+i)).unbind('click').click(function(){
                if (val.callback && typeof val.callback == 'function') {
                    if (val.callback() === true) { return true;}
                }
                if (val.click && typeof val.click == 'function') {
                    if (val.click() === true) { return true; }
                }
                $.alert(0);
            });
        });
        return true;
    }
    if (!q) q = "确定";
    if (!c) c = "取消";
    if (!qfun && !cfun) qfun = function(){};
    var m = Math.round(Math.random() * 10000);
    var tempbut = '<div style="margin:15px -10px -15px;border-top:1px solid #ECECEC;">';
    var tempwid = '100%';
    if (qfun && cfun) {
        tempwid = '50%';
    }
    if (qfun) {
        tempbut+= '<div id="jQuery-ui-confirmq-'+m+'" style="float:left;width:'+tempwid+';text-align:center;padding:5px 0;cursor:pointer;">'+q+'</div>';
    }
    if (cfun) {
        tempbut+= '<div id="jQuery-ui-confirmc-'+m+'" style="float:left;width:'+tempwid+';text-align:center;padding:5px 0;cursor:pointer;border-left:1px solid #6F6F6F;margin-left:-1px">'+c+'</div>';
    }
    tempbut+= '<div style="clear: both;"></div>';
    tempbut+= '</div>';
    $.alert(e, 0, 1, tempbut);
    if (qfun) {
        $('#jQuery-ui-confirmq-'+m).unbind().click(function(){
            qfun();$.alert(0);
        });
    }
    if (cfun) {
        $('#jQuery-ui-confirmc-'+m).unbind().click(function(){
            cfun();$.alert(0);
        });
    }
};
/**
 *
 * @param msg 提示文本
 * @param parame -1不为空、-2手机号码规则、-3电话号码、-4邮箱规则、-5不得包含非法字符、大于0字符长度要求
 * @param retu 是否执行
 * @param parame2 字符长度要求最大不超过parame
 * @param msgobj 可选提示在此对象之后
 * @returns {boolean}
 */
$.fn.inTips = function(msg, parame, retu, parame2, msgobj) {
    if (retu === false){
        return false;
    }
    if (!isNaN(parame)){
        if (parame === -1){
            if (!msg) msg = "不可留空";
            if (this.val() != "") return true;
        }else if (parame === -2){
            if (!msg) msg = "格式错误"; //手机号码
            if (/^1\d{10}$/g.test(this.val())) return true;
        }else if (parame === -3){
            if (!msg) msg = "格式错误"; //电话号码
            if (/^0\d{2,3}-?\d{7,8}$/g.test(this.val())) return true;
        }else if (parame === -4){
            if (!msg) msg = "格式错误"; //电子邮箱
            if (/^(\w-*\.*)+@(\w-?)+(\.\w{2,})+$/g.test(this.val())) return true;
        }else if (parame === -5){
            if (!msg) msg = "格式错误"; // 用户名
            if (/^[a-zA-z]\w{1,50}$/g.test(this.val())) return true;
        }else if (parame > 0){
            if (parame2){
                if (!msg) msg = "最大" + parame + "个字符";
                if (this.val().length <= parame) return true;
            }else{
                if (!msg) msg = "最少" + parame + "个字符";
                if (this.val().length >= parame) return true;
            }
        }else if (parame < 0){
            if (!msg) msg = "最大" + (parame*-1) + "个字符";
            if (this.val().length <= (parame*-1)) return true;
        }
    }else if (parame instanceof jQuery){
        if (!msg) msg = "两次输入不一致";
        if (parame.val() == this.val()) return true;
    }
    //
	var m = Math.round(Math.random() * 10000);
	var $imithis = this;
    if (msgobj instanceof jQuery) $imithis = msgobj;
	if ($imithis.attr("data-jQueryuitips-id") > 0){
		m = $imithis.attr("data-jQueryuitips-id");
	}else{	
		$imithis.attr("data-jQueryuitips-id", m)
	}
	$("span#jQuery-ui-tips-"+m).remove();
	var $imitate = $('<span id="jQuery-ui-tips-' + m + '" style="position:absolute; display:none; overflow:hidden; color:#cccccc; height:20px;"><i>！' + msg + '</i></span>');
	$(document.body).append($imitate.click(function () {
		$imithis.trigger('focus');
		$imithis.removeClass('jQuery-ui-tips');
		$imitate.remove();
	}));
	if ($imithis.attr("data-uitips") == "jQ"){
		$imithis.unbind("click",jQUiTips);
	}
	$imithis.addClass('jQuery-ui-tips');
	$imithis.attr("data-uitips", "jQ");
	$imithis.bind("click", jQUiTips = function(){
		$imithis.removeClass('jQuery-ui-tips');
		$imitate.remove();
	});
	//定位
    var ttop  = $imithis.offset().top;     		//控件的定位点高
    var thei  = $imithis.outerHeight();  		//控件本身的高
    var twid  = $imithis.outerWidth();  		//控件本身的宽
    var tleft = $imithis.offset().left;    		//控件的定位点宽
	$("#jQuery-ui-tips-" + m).css({
        width:$("#jQuery-ui-tips-" + m).width() + 12,
        height:thei,
        top:ttop,
        left:tleft + twid + 10,
        'line-height':thei+'px'
    }).show();
	$("#jQuery-ui-tips-" + m + " i").css({
		'color':'#ff0000',
		'font-style': 'normal',
		'font-weight': '600',
		'font-size': '12px'
    }).show();
    //
    $body = (window.opera) ? (document.compatMode == "CSS1Compat" ? $('html') : $('body')) : $('html,body');
    $body.animate({scrollTop: $imithis.offset().top - 10}, 100);
    return false;
};

/**
 *
 * @param msg
 */
jQuery.inModal = function(msg) {
    $.showModal(msg, '', '', 1);
};
/**
 *
 * @param msg
 * @param url
 * @param goonurl
 * @param isautohide
 */
jQuery.showModal = function(msg, url, goonurl, isautohide) {
    $("div.jQuery-ui-myModal").remove();
    $("div.jQuery-ui-myModal-Bg").remove();
	var m = Math.round(Math.random() * 10000);
	var n = '<div class="jQuery-ui-myModal" id="jQuery-ui-myModal-' + m + '" style="display:none;">'
		+'	<div class="jqmodal-header">'
		+'		<button type="button" class="close" onclick="$(\'.jQuery-ui-myModal\').hide();$(\'.jQuery-ui-myModal-Bg\').hide();">\u00d7</button>'
		+'		<h3 id="myModalLabel">\u6d88\u606f</h3>'
		+'	</div>'
		+'	<div class="jqmodal-body">'
		+'		<p class="error-text"><i class="jqmodal-icon fa fa-exclamation-triangle"></i><span id="myModalContent">Are you sure you?</span></p>'
		+'	</div>'
		+'	<div class="jqmodal-footer">'
		+'		<a id="myModalLink" href="javascript:void(0);" class="jqmodal-btn-danger"><i class="fa fa-check">\u221a\u0020</i>\u786e\u5b9a</a>'
		+'		<a id="myModalGoon" href="javascript:void(0);" class="jqmodal-btn-info">\u7ee7\u7eed<i class="fa fa-chevron-right">\u0020\u003e</i></a>'
		+'	</div>'
		+'</div>'
		+'<div class="jQuery-ui-myModal-Bg" id="jQuery-ui-myModal-Bg-' + m + '" style="display:none;"></div>';
	$("body").append(n);
    var nobjbg = $('#jQuery-ui-myModal-Bg-' + m);
    nobjbg.css({
        "width":"100%",
        "height":$(document).height(),
        "position":"absolute",
        "top":"0px",
        "left":"0px",
        "background-color":"#cccccc",
        "opacity":"0.5",
        "z-index":"10000"
    });
    var nobj = $('#jQuery-ui-myModal-' + m);
	nobj.css({
        "background-color":"#ffffff",
        "border-radius":"6px",
        "box-shadow":"0 3px 7px rgba(0, 0, 0, 0.3)",
        "border":"1px solid rgba(0, 0, 0, 0.5)",
        "position":"fixed",
        "margin":"0px auto",
        "width":"420px",
        "z-index":"10001"
    });
    nobj.find(".jqmodal-header").css({
        "background-color":"#ffffff",
        "border-radius":"6px 6px 0 0",
        "padding": "9px 15px",
        "border-bottom": "1px solid #eee"
    });
	nobj.find(".jqmodal-header h3").css({
        "line-height": "30px"
    });
	nobj.find("button.close").css({
        "margin-top": "5px",
        "padding": "0",
        "background": "transparent",
        "border": "0",
        "-webkit-appearance": "0",
        "float": "right",
        "font-size": "12px",
        "line-height": "20px",
        "font-weight": "bold",
        "color": "#000000",
        "text-shadow": "0 1px 0 #ffffff",
        "opacity": "0.2"
    });
	nobj.find(".jqmodal-body").css({
        "font-size": "14px",
        "padding": "2em",
        "overflow-y": "auto",
        "max-height": "400px"
    });
    nobj.find(".jqmodal-body p").css({
        "line-height": "1.5em"
    });
    nobj.find(".jqmodal-body p i").css({
        "vertical-align": "middle",
        "font-size": "56px",
        "float": "left",
        "line-height": "28px",
        "margin-right": ".25em"
    });
    nobj.find(".jqmodal-footer").css({
        "background-color":"#f5f5f5",
        "border-radius":"0 0 6px 6px",
        "padding":"12px",
        "text-align":"right",
        "border-top":"1px solid #ddd",
        "box-shadow":"inset 0 1px 0 #ffffff"
    });
    nobj.find(".jqmodal-footer a i").css({"font-style": "normal"});
    if (nobj.find(".jqmodal-footer a i").css("display") == 'inline-block'){
        nobj.find(".jqmodal-footer a i").each(function(){$(this).attr("data-text", $(this).text());});
        nobj.find(".jqmodal-footer a i").text("");
        nobj.find(".jqmodal-footer a i.fa-check").css({"padding-right": "3px"});
        nobj.find(".jqmodal-footer a i.fa-chevron-right").css({"padding-left": "3px"});
    }
    nobj.find(".jqmodal-btn-danger").css({
        "background-color":"#553333",
        "border-radius":"5px",
        "border":"1px solid #452929",
        "color":"#ffffff",
        "text-shadow":"0 -1px 0 rgba(0, 0, 0, 0.25)",
        "background-image":"linear-gradient(to bottom, #955959, #553333)",
        "font-size": "14px",
        "line-height": "20px",
        "padding":"7px 14px",
        "outline":"none",
        "text-decoration":"none"
    });
    nobj.find(".jqmodal-btn-info").css({
        "margin-left": "5px",
        "display": "none",
        "background-color":"#49afcd",
        "border-radius":"5px",
        "border":"1px solid #2f96b4",
        "color":"#ffffff",
        "text-shadow":"0 -1px 0 rgba(0, 0, 0, 0.25)",
        "background-image":"linear-gradient(to bottom, #5bc0de, #2f96b4)",
        "font-size": "14px",
        "line-height": "20px",
        "padding":"7px 14px",
        "outline":"none",
        "text-decoration":"none"
    });
    var i = $(window).width(),
        s = $(window).height(),
        o = nobj.width(),
        u = nobj.height(),
        l = (i - o) / 2;
    i > o && nobj.css("left", l),
        s > u && nobj.css("top", (s - u) / 2),
        l < 5 && nobj.css("margin", "0 5px");
	nobj.show();
    nobj.find(".jqmodal-footer").find("#myModalLink").focus();

    var isIe6 = false;
    if (/msie/.test(navigator.userAgent.toLowerCase())) {
        if (jQuery.browser && jQuery.browser.version && jQuery.browser.version == '6.0') {
            isIe6 = true
        } else if (!$.support.leadingWhitespace) {
            isIe6 = true;
        }
    }
    if(isIe6){
        nobj.find(".jqmodal-body p i").hide();
        nobj.find(".jqmodal-footer a i").each(function(){$(this).text($(this).attr("data-text"));});
        nobj.css({
            "position":"absolute"
        });
        $(window).scroll(function (){
            var offsetTop = ($(window).scrollTop() + ($(window).height() - nobj.height()) / 2) +"px";
            nobj.animate({top : offsetTop },{ duration:500 , queue:false });
        });
    }else{
        nobjbg.show();
    }

    $(window).resize(function(){
        var i = $(window).width(),
            s = $(window).height(),
            o = nobj.width(),
            u = nobj.height(),
            l = (i - o) / 2;
        i > o && nobj.css("left", l),
            s > u && nobj.css("top", (s - u) / 2),
            l < 5 && nobj.css("margin", "0 5px");
        if(isIe6){
            var offsetTop = ($(window).scrollTop() + ($(window).height() - nobj.height()) / 2) +"px";
            nobj.css("top", offsetTop);
        }
    });

	if (goonurl) {
        nobj.find("#myModalGoon").show();
        if (typeof(goonurl) == "function") {
            nobj.find("#myModalGoon").click(function(){
                if (goonurl() !== true) {
                    $("div.jQuery-ui-myModal").remove();
                    $("div.jQuery-ui-myModal-Bg").remove();
                }
            });
        }else{
            nobj.find("#myModalGoon").attr("href", goonurl);
        }

	}
    nobj.find("#myModalContent").html(msg);
	if (url) {
        if (typeof(url) == "function") {
            nobj.find("#myModalLink").click(function(){
                if (url() !== true) {
                    $("div.jQuery-ui-myModal").remove();
                    $("div.jQuery-ui-myModal-Bg").remove();
                }
            });
        }else{
            nobj.find("#myModalLink").attr("href", url);
        }
        if (!isautohide) {
			setTimeout(function(){
				nobj.fadeOut(1000, function(){
                    if (typeof(url) == "function") {
                        if (url() !== true) {
                            $("div.jQuery-ui-myModal").remove();
                            $("div.jQuery-ui-myModal-Bg").remove();
                        }
                    }else{
                        window.location.href = url;
                    }
                });
			},1000);			
		}
	} else {
        nobj.find("#myModalLink").attr("href", 'javascript:void(0);');
        nobj.find("#myModalLink").click(function(){nobj.hide();nobjbg.hide();});
		if (!isautohide) {
			setTimeout(function(){
				nobj.fadeOut(1000, function(){nobjbg.hide();});
			},1000);
		}		
	}
};

jQuery.tusi = function(txt, fun, t) {
    $('.jQuery-ui-tusi').remove();
    if(txt === false || txt === 0) return;
    var div = $('<div class="jQuery-ui-tusi" style="background-color: rgba(51,51,51,.9); opacity:1;max-width: 85%;min-height: 77px;min-width: 200px;position: absolute;left: -1000px;top: -1000px;text-align: center;border-radius:10px;"><span style="color: #ffffff;line-height: 77px;font-size: 23px;">'+txt+'</span></div>');
    div.css({"background-color":"#333333","opacity":"0.9"});
    $('body').append(div);
    div.css('zIndex',9999999);
    div.css('left',parseInt(($(window).width()-div.width())/2));
    var top = parseInt($(window).scrollTop()+($(window).height()-div.height())/2);
    div.css('top',top);
    setTimeout(function(){
        div.remove();
        if(fun){
            fun();
        }
    }, t || 2000);
};

jQuery.loading = function(txt){
    if(txt === false || txt === 0){
        $('.jQuery-ui-lodediv').remove();
    }else{
        $('.jQuery-ui-lodediv').remove();
        var div = $('<div class="jQuery-ui-lodediv" style="background-color: rgba(51,51,51,.9); opacity:1;min-width:269px;max-width:85%;height: 107px;position: absolute;left: -1000px;top: -1000px;text-align: center;border-radius:10px;"><span style="color: #ffffff;line-height: 107px;font-size: 23px; white-space: nowrap;">&nbsp;&nbsp;&nbsp;<img src="data:image/gif;base64,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" style="vertical-align: middle;"/>&nbsp;&nbsp;'+txt+'&nbsp;&nbsp;</span></div>');
        div.css({"background-color":"#333333","opacity":"0.9"});
        $('body').append(div);
        div.css('zIndex',9999999);
        div.css('left',parseInt(($(window).width()-div.width())/2));
        var top = parseInt($(window).scrollTop()+($(window).height()-div.height())/2);
        div.css('top',top);
    }
};