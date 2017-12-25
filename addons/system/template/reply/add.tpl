
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
    <script type="text/javascript" src="{#$JS_PATH#}bootstrap-switch.min.js"></script>
</head>
<body>
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
                    $(this).focus(); _break = true; return false;
                }
            });
            if (_break) return false;
        }).find(".panel>.panel-heading").each(function(){
            var tthis = $(this);
            var title = tthis.html().replace(/<[^>]+>(.+?)<\/[^>]+>/gi,"");
            title = $.trim(title.replace(/<[^>]*>/g,""));
            if (title) {
                $imtemp = $('<a href="javascript:void(0);" class="rt-text" title="滚动到【'+title+'】栏"><em><span>'+title+'</span></em></a>');
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
    });
</script>

{#template("footer")#}


</body>
</html>