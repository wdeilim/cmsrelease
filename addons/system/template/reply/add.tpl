
<!DOCTYPE html>
<html>
<head lang="zh-CN">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{#$_A.f.title#} - {#$BASE_NAME#}</title>
    <link rel="stylesheet" href="{#$CSS_PATH#}bootstrap.min.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}bootstrap-switch.min.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}normalize.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}global.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}functions.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}alert.css"/>
    <link rel="stylesheet" href="{#$NOW_PATH#}css/default.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}font-awesome.css"/>
    <script type="text/javascript" src="{#$JS_PATH#}jquery-1.11.0.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.alert.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.cookie.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}bootstrap-switch.min.js"></script>
</head>
<body style="overflow-y:scroll;">
{#template("header")#}

<div class="wrapper">
    <div class="breadcrumb">
        <div class="items">
            <span class="description">当前位置：</span>
            <span>{#$_A.f.title#}</span>
        </div>
    </div>

    <div class="topmenu" id="topmenu">
        <a href="{#weburl(0, $_A.f.title_en)#}">管理{#$_A.f.title#}</a>
        {#if $submit=='修改'#}
            <a href="{#weburl(0, $_A.f.title_en)#}&do=add&id={#$reply.id#}" class="active">修改{#$_A.f.title#}</a>
        {#/if#}
        <a href="{#weburl(0, $_A.f.title_en)#}&do=add"{#if $submit=='添加'#} class="active"{#/if#}>+添加{#$_A.f.title#}</a>
    </div>

    <div class="main cf custom-menu">
        <div class="mod_tab_btn"></div>
        <div class="mod_tab_menu"><span></span></div>

        <div class="mod">
            <div class="main-bd" id="tabmenu">
                <div class="clearfix">
                    <form action="{#get_url()#}" method="post" id="saveform" class="form-horizontal form ng-pristine ng-valid">

                        <div class="form-group">
                            <div class="col-sm-12">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        基本设置
                                        <span class="text-muted">删除，修改规则、关键字以及回复后，请提交以保存操作。</span>
                                    </div>
                                    <div class="panel-body">

                                        <div class="form-group">
                                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">规则名称</label>
                                            <div class="col-xs-12 col-sm-9">
                                                <input type="text" name="reply[title]" value="{#$reply['title']#}" class="form-control" placeholder="您可以给这条规则起一个名字, 方便下次修改和查看. " data-need="规则名称不能为空">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">关键词</label>
                                            <div class="col-xs-12 col-sm-9">
                                                <input type="text" name="reply[key]" value="{#$reply['key']|trim:","#}" class="form-control" placeholder="多个请用英文逗号“,”隔开" data-need="关键词称不能为空">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">匹配类型</label>
                                            <div class="col-xs-12 col-sm-9">
                                                <select id="base_match" name="reply[match]" class="form-control">
                                                    <option value="0">完全匹配</option>
                                                    <option value="1">包含匹配</option>
                                                </select>
                                                {#if value($reply,'match')#}
                                                    <script>$('#base_match').val('{#value($reply,'match')#}');</script>
                                                {#/if#}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">回复内容</label>
                                            <div class="col-xs-12 col-sm-9">
                                                {#tpl_form_aledit('reply[content]',value($reply,'content'), 'imagetext')#}
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-12">
                                <div class="panel panel-default{#if !$reply['vip_link']#} vip_link_hide{#/if#}">
                                    <div class="panel-heading">
                                        会员卡显示
                                        <span class="text-muted">这里提供了能够显示在会员卡首页的信息, 你可以选择性的自定义或显示隐藏。</span>
                                        <div class="vip_link_panel">
                                            <input type="checkbox" class="vip_link" name="reply[vip_link]" value="1" data-size="small" data-off-color="warning"{#if $reply['vip_link']#} checked{#/if#}>
                                        </div>
                                    </div>
                                    <div class="panel-body">

                                        <div class="form-group">
                                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">显示标题</label>
                                            <div class="col-xs-12 col-sm-9">
                                                <input type="text" name="reply[vip_title]" value="{#$reply['vip_title']#}" class="form-control" placeholder="请填写在会员卡首页显示的名称，留空则不显示">
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        {#$this->_reply_help('FormDisplay', $id, $reply)#}

                        <div class="form-group">
                            <div class="col-sm-12">
                                <input id="saveform-submit" name="submit" type="submit" value="提交" class="btn btn-primary col-lg-1">
                                <input type="hidden" name="dosubmit" value="1">
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
        <div class="right-tool">

        </div>
    </div>
</div>

<script type="text/javascript">
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
        var mod_tab_btn = $(".mod_tab_btn");
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
        });
        $(".mod_tab_menu").find(">span").click(function(){
            _init_mod_tab_menu(0);
            $.cookie('_init_mod_tab_menu', '0', {expires:365});
        });
        if ($.cookie('_init_mod_tab_menu') == '1') {
            _init_mod_tab_menu(1);
        }else if ($.cookie('_init_flyElm') != '1') {
            _init_flyElm();
        }
    });
</script>

{#template("footer")#}


</body>
</html>