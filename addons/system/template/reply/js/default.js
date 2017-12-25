function _init_mod_tab_menu(h) {
    var saveform = $('form#saveform').eq(0);
    var right_tool = $('.right-tool');
    var mod_tab_menu = $('.mod_tab_menu');
    var mod_tab_btn = $('.mod_tab_btn');
    if (h) {
        right_tool.find(">a").each(function(){
            var tthis = $(this);
            if (!tthis.hasClass("rt-submit")) {
                var text = tthis.find("span").text(),
                    texj = tthis.attr("data-j"),
                    $mimtemp = $('<a href="javascript:void(0);" data-j="'+texj+'" title="'+text+'">'+text+'</a>');
                $mimtemp.click(function(){
                    saveform.find(">div").hide();
                    saveform.find(">div:last").show();
                    saveform.find(">div[data-modtabmenu='"+texj+"']").show();
                    mod_tab_menu.find(">a").removeClass("active");
                    $(this).addClass("active");
                    if (window._data_reply_id) {
                        $.cookie('_init_tab_click_' + window._data_reply_id, texj);
                    }
                });
                mod_tab_menu.append($mimtemp);
                tthis.unbind("click").click(function(){
                    $mimtemp.click();
                });
            }
        });
        mod_tab_menu.find("a:eq(0)").click();
        mod_tab_menu.show();
        mod_tab_btn.hide();
    }else{
        mod_tab_menu.hide().find(">a").remove();
        mod_tab_btn.show();
        saveform.find(">div").show();
        right_tool.find(">a").unbind("click").click(function(){
            $('body,html').animate({ scrollTop: saveform.find(">div[data-modtabmenu='"+$(this).attr("data-j")+"']").offset().top - 60}, 200);
        });
    }
}
function _init_flyElm() {
    $.cookie('_init_flyElm', '1', {expires:9999});
    $('body,html').animate({ scrollTop: 0}, 1500);
    var flyElm = $('<img src="data:image/gif;base64,R0lGODlhFAAUAJEAADMzMwAAAP///wAAACH5BAEHAAIALAAAAAAUABQAAAIxlI+py+0MoolgSgVCFXn3RVne9ZSms01Iiq0u9KrwwbJJHbd0fue2zwPuZrLi6WgqAAA7">');
    var mod_tab_btn = $(".mod_tab_btn");
    if ($.cookie('_init_mod_tab_menu') != '0') {
        mod_tab_btn = $(".mod_tab_menu").find(">span");
        flyElm = $('<img src="data:image/gif;base64,R0lGODlhFAAUAJEAAAAAADMzM////wAAACH5BAUUAAIALAAAAAAUABQAAAImlI+py+0PU5gRBRDM3DxbWoXis42X13USOLauUIqnlsaH/eY6UwAAOw==">');
    }
    flyElm.css({
        'opacity':'0.8',
        'z-index': 9000,
        'display': 'block',
        'position': 'absolute',
        'top': $(window).scrollTop() + ($(window).height() / 2),
        'left': '50%',
        'width': '500px',
        'height': '500px',
        'margin-top': '-250px',
        'margin-left': '-250px'
    });
    $('body').append(flyElm);
    flyElm.animate({
        top:mod_tab_btn.offset().top,
        left:mod_tab_btn.offset().left,
        width:20,
        height:20,
        marginTop:0,
        marginLeft:0
    }, 1500,function(){
        flyElm.remove();
    });
}
$(function(){
    $("#reply_key").keyauto(false, "form-control-key");
    //
    var vip_link = $(".vip_link");
    vip_link.bootstrapSwitch().on('switchChange.bootstrapSwitch', function(e, state){
        var pobj = $(this).parents(".panel-default");
        if (state) {
            pobj.removeClass("vip_link_hide");
            pobj.find(".panel-body").css({backgroundColor:'#F7F3EC'});
            setTimeout(function(){pobj.find(".panel-body").css({backgroundColor:'#FFFFFF'})}, 100);
        }else{
            pobj.addClass("vip_link_hide");
        }
    });
    if ($(".vip_link_hide").length > 0) {
        vip_link.bootstrapSwitch('state', false);
    }
    //
    var saveform = $('form#saveform').eq(0);
    var right_tool = $('.right-tool');
    saveform.submit(function() {
        var _break = false;
        $(this).find("input,select,textarea").each(function(){
            var need = $(this).attr("data-need");
            if (need && need != "" && $(this).val() == ""){
                $.alertk(need);
                var ithis = $(this).parents("div");
                while(ithis && typeof(ithis.attr("data-modtabmenu")) == "undefined") { ithis = ithis.parents("div"); }
                $('.mod_tab_menu').find("a[data-j='"+ithis.attr("data-modtabmenu")+"']").click();
                $(this).focus(); _break = true; return false;
            }
        });
        if (_break) return false;
    }).find(".panel>.panel-heading").each(function(j){
        var tthis = $(this);
        var title = tthis.html().replace(/<[^>]+>(.+?)<\/[^>]+>/gi,"");
        title = $.trim(title.replace(/<[^>]*>/g,""));
        if (title) {
            tthis.parentsUntil("form#saveform").attr("data-modtabmenu", j);
            $imtemp = $('<a href="javascript:void(0);" class="rt-text" data-j="'+j+'" title="转到【'+title+'】栏"><em><span>'+title+'</span></em></a>');
            $imtemp.attr("data-top", tthis.offset().top - 60);
            $imtemp.click(function(){ $('body,html').animate({ scrollTop: tthis.offset().top - 60}, 200); });
            right_tool.append($imtemp);
        }
    });
    saveform.find("input,textarea").each(function(){
        var tthis = $(this);
        var placeholder = tthis.attr("placeholder");
        if (placeholder) {
            tthis.focus(function(){
                var _t = tthis.offset().top + tthis.outerHeight() + 8,
                    _l = tthis.offset().left;
                $("body").append('<div class="placeholder-float-tis" style="top:'+_t+'px;left:'+_l+'px;">'+placeholder+'</div>');
            }).blur(function(){
                $("div.placeholder-float-tis").remove();
            });
        }
    });
    $imtemp = $('<a href="javascript:void(0);" class="rt-submit" title="立即提交"><em><span>提交</span></em></a>');
    $imtemp.click(function(){ $("#saveform-submit").click(); });
    right_tool.append($imtemp);
    right_tool.find(">a").mouseover(function(){
        $(this).css({width:$(this).find("span").outerWidth()+30})
    }).mouseout(function(){
        $(this).css({width:$(this).parent().outerWidth()})
    });
    right_tool.css({ left: $(window).width() / 2 + 620, display: 'block' });
    $(window).resize(function(){
        right_tool.css({
            left: $(window).width() / 2 + 620,
            display: 'block'
        });
    });
    $(".mod_tab_btn").click(function(){
        _init_mod_tab_menu(1);
        $.cookie('_init_mod_tab_menu', '1', {expires:365});
    }).tooltip();
    $(".mod_tab_menu").find(">span").click(function(){
        _init_mod_tab_menu(0);
        $.cookie('_init_mod_tab_menu', '0', {expires:365});
    }).tooltip();
    if ($.cookie('_init_mod_tab_menu') != '0') {
        _init_mod_tab_menu(1);
    }
    if ($.cookie('_init_flyElm') != '1') {
        _init_flyElm();
    }
    window._data_reply_id = $("body:eq(0)").attr("data-replyid");
    if (window._data_reply_id) {
        $("a[data-j='"+$.cookie('_init_tab_click_' + $("body").attr("data-replyid"))+"']").click();
    }
});