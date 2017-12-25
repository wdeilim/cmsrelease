
<!DOCTYPE html>
<html>
<head lang="zh-CN">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{#$func.f.title#} - {#$BASE_NAME#}</title>
    <link rel="stylesheet" href="{#$CSS_PATH#}normalize.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}global.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}functions.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}alert.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}message.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}font-awesome.css"/>
    <style type="text/css">
        #text-tooltip{position:absolute;background-color:#fff;padding:8px;border:1px solid #cc7116}
        #text-tooltip img{display:block;margin:0;max-width:300px;max-height:300px}
        .selectmenu a {display:block;float:left;color:#777777;padding:8px 16px;margin-right:-1px;background-color:#ffffff;border:1px solid #E4E4E4;}
        .selectmenu a.active {border-top:2px solid #FF9B00;padding-top:7px;}
        .selectmenu a.break {margin-left:25px;border-radius:3px 0 0 3px;padding:7px 12px;margin-top:1px;color:#FFF;background:#2AA4CD;border:1px solid #2AA4CD;}
        .selectmenu a:hover.break {background:#fa0;border:1px solid #fa0;}
        .selectmenu a.break.del {margin-left:0;border-radius:0 3px 3px 0;border-left:1px solid #E4E4E4;}
        .selectmenu a:hover.break.del {background:#f00;border:1px solid #f00;}
        .zoomin {position:absolute;z-index:999999;cursor:-webkit-zoom-out;padding:10px;background-color:rgba(132, 132, 132, 0.8)}
        .zoomin img {max-width:100%;}
        .zoomin-bg {position:fixed;top:0;left:0;width:100%;height:100%;z-index:999998;cursor:-webkit-zoom-out;background-color: rgba(0, 0, 0, 0.3)}
    </style>
    <script type="text/javascript" src="{#$JS_PATH#}jquery-1.11.0.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.alert.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.form.min.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}artdialog/artDialog.js?skin=default"></script>
</head>
<body>
{#template("header")#}





<div class="wrapper">
    <div class="breadcrumb">
        <div class="items">
            <span class="description">当前位置：</span>
            <span>{#$func.f.title#}</span>
        </div>
    </div>


    <div class="topmenu" id="topmenu">
        <a href="javascript:;">全部信息</a>
        <a href="javascript:;">标星信息</a>
        <a href="javascript:;" data-url="{#weburl('message/count')#}">统计情况</a>
    </div>

    <div class="main cf custom-menu">
        <div class="mod">
            <div class="main-bd" id="tabmenu">

                <div class="tabmenu form-services" id="messagelist-1">
                    <div class="control-top clearfix">
                        <div style="float: left" class="form form-inline selectmenu">
                            <a href="{#weburl('message/index')#}"{#isto($_GPC['msgtype'],'',' class="active"')#}>全部</a>
                            <a href="{#weburl('message/index')#}&msgtype=text"{#isto($_GPC['msgtype'],'text',' class="active"')#}>文字</a>
                            <a href="{#weburl('message/index')#}&msgtype=click"{#isto($_GPC['msgtype'],'click',' class="active"')#}>点击菜单</a>
                            <a href="{#weburl('message/index')#}&msgtype=view"{#isto($_GPC['msgtype'],'view',' class="active"')#}>跳转菜单</a>
                            <a href="{#weburl('message/index')#}&msgtype=image"{#isto($_GPC['msgtype'],'image',' class="active"')#}>图片</a>
                            <a href="{#weburl('message/index')#}&msgtype=voice"{#isto($_GPC['msgtype'],'voice',' class="active"')#}>语音</a>
                            <a href="{#weburl('message/index')#}&msgtype=video"{#isto($_GPC['msgtype'],'video',' class="active"')#}>视频</a>

                            <a href="{#$urlarr.now#}{#get_get()#}" class="break">刷新</a>
                            <a href="javascript:void(0)" class="break del" id="smdel" onclick="_delmsg(this)">删除全部数据</a>
                            <script type="text/javascript">$("#smdel").text("删除"+$(".selectmenu").find(".active").text()+"数据");</script>
                        </div>
                        <div style="float: right" class="form form-inline">
                            <div class="form-group">
                                <input type="text" id='keyval' class="form-control inp2" value="{#$keyval#}" data-val="{#$keyval#}" placeholder="输入搜索关键字" />
                            </div>
                            <div class="form-group">
                                <button class="button" onclick="keybut(1);">搜用户</button>
                                <button class="button" onclick="keybut(0);">搜内容</button>
                            </div>
                        </div>
                    </div>
                    <table class="table table-primary messagelist">
                        <thead style="display: none;">
                        <tr>
                            <th>头像</th>
                            <th class="lt">相关内容</th>
                            <th width="150">时间</th>
                            <th width="120">操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        {#ddb_pc set="数据表:message,用户表:1,列表名:lists,显示数目:10,分页显示:1,分页名:pagelist,当前页:{#$page#},分页地址:{#$pageurl#}index/(?)/{#get_get()#},排序:indate desc>id desc" where="{#$wheresql#}"#}
                        {#foreach from=$lists item=list#}
                        <tr>
                            {#$g = $tthis->getgroup($list.openid,'*')#}
                            <td class="alimg">
                                {#if $list.tobe != 0#}
                                <img src="{#$IMG_PATH#}avatarsys.jpg"/>
                                {#else#}
                                <img onmouseover="moimg(this);" src="{#$g.avatar#}" onerror="this.src='{#$IMG_PATH#}avatar.jpg?0'"/>
                                {#/if#}
                            </td>
                            <td class="lt alname">
                                {#if $list.type=='weixin'#}<span class="wxmsg">微信</span>{#/if#}
                                {#if $list.type=='alipay'#}<span class="almsg">服务窗</span>{#/if#}
                                <span onclick="_notes(this,'{#$list.id#}');" class="notes">
                                    {#if $list.emulator#}{模拟}{#/if#}
                                    {#if $list.tobe != 0#}回复：{#/if#}
                                    {#$g.user_name#}
                                </span>
                                <span class="isreply" id="isreply_{#$list.id#}">
                                    {#if $list.replydate#}<span title="{#$list.reply|get_html#}">已回复</span>{#/if#}
                                </span>
                                <p>{#message_text($list)#}</p>
                            </td>
                            <td class="indate">{#$list.indate|date_format:"%Y-%m-%d %H:%M:%S"#}</td>
                            <td class="reply">
                                <span class="reply" onclick="_reply('{#$list.id#}');"></span>
                                {#if $list.tobe == 0#}
                                {#if $list.star#}
                                <span class="star-on" onclick="_star(this,'{#$list.id#}');"></span>
                                {#else#}
                                <span class="star-off" onclick="_star(this,'{#$list.id#}');"></span>
                                {#/if#}
                                {#/if#}
                            </td>
                        </tr>
                        {#foreachelse#}
                        <tr>
                            <td colspan="4" align="center" class="align-center">
                                <div>无</div>
                            </td>
                        </tr>
                        {#/foreach#}
                        </tbody>
                    </table>
                    <div id="pagelist" class="clearfix">
                        <a href="javascript:void(0);" style="cursor:default">总数量{#$pagelist_info.total_rows#}个</a>
                        <span id="jspage" data-id="messagelist-1">
                            <a href="{#$urlarr.now#}{#get_get()#}">刷新</a>
                            {#$pagelist#}
                        </span>
                    </div>

                </div>


                <div class="tabmenu" id="messagelist-2">
                    <table class="table table-primary messagelist">
                        <thead style="display: none;">
                        <tr>
                            <th>头像</th>
                            <th class="lt">相关内容</th>
                            <th width="150">时间</th>
                            <th width="120">操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        {#ddb_pc set="数据表:message,用户表:1,列表名:lists,显示数目:10,分页显示:1,分页名:pagelist,当前页:{#$page#},分页地址:{#$pageurl#}index/(?)/{#get_get()#},排序:indate desc" where="{#$wheresqlstar#}"#}
                        {#foreach from=$lists item=list#}
                        <tr>
                            {#$g = $tthis->getgroup($list.openid,'*')#}
                            <td class="alimg"><img onmouseover="moimg(this);" src="{#$g.avatar#}" onerror="this.src='{#$IMG_PATH#}avatar.jpg?0'"/></td>
                            <td class="lt alname">
                                {#if $list.type=='weixin'#}<span class="wxmsg">微信</span>{#/if#}
                                {#if $list.type=='alipay'#}<span class="almsg">服务窗</span>{#/if#}
                                <span onclick="_notes(this,'{#$list.id#}');" class="notes">
                                    {#if $list.emulator#}{模拟}{#/if#}
                                    {#$g.user_name#}
                                </span>
                                <span class="isreply" id="isreply_{#$list.id#}">
                                    {#if $list.replydate#}<span title="{#$list.reply|get_html#}">已回复</span>{#/if#}
                                </span>
                                <p>{#message_text($list)#}</p>
                            </td>
                            <td class="indate">{#$list.indate|date_format:"%Y-%m-%d %H:%M:%S"#}</td>
                            <td class="reply">
                                <span class="reply" onclick="_reply('{#$list.id#}');"></span>
                                {#if $list.star#}
                                <span class="star-on" onclick="_star(this,'{#$list.id#}');"></span>
                                {#else#}
                                <span class="star-off" onclick="_star(this,'{#$list.id#}');"></span>
                                {#/if#}
                            </td>
                        </tr>
                        {#foreachelse#}
                        <tr>
                            <td colspan="4" align="center" class="align-center">
                                <div>无</div>
                            </td>
                        </tr>
                        {#/foreach#}
                        </tbody>
                    </table>
                    <div id="pagelist" class="clearfix">
                        <a href="javascript:void(0);" style="cursor:default">总数量{#$pagelist_info.total_rows#}个</a>
                        <span id="jspage" data-id="messagelist-2">
                            <a href="{#$urlarr.now#}{#get_get()#}" id="refreshlist">刷新</a>
                            {#$pagelist#}
                        </span>
                    </div>
                </div>

                <div class="tabmenu" id="messagelist-3">

                    <div class="form-services">
                        <div style="margin-top:100px;text-align:center">加载中，请稍等。。。</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{#tpl_form_aledit()#}
<script type="text/javascript">
    function _jspage(){
        var eve = $('span#jspage').find('a');
        eve.each(function(){
            if ($(this).attr("href") != "javascript:void(0);") {
                $(this).attr("data-url", $(this).attr("href"));
                $(this).attr("href", "javascript:void(0);");
                $(this).click(function(){
                    $.alert('正在加载...', 0);
                    $("#"+$(this).parent().attr("data-id")).load($(this).attr("data-url") + ' #'+$(this).parent().attr("data-id")+' > ', function() {
                        $.alert(0);
                        _jspage();
                    });
                });
            }
        });
    }
    function _notes(obj,id) {
        var eve = $(obj);
        var _h = ($(window).height()-50)*0.96
        art.dialog({
            title: '与 '+eve.text()+' 的聊天',
            fixed: true,
            lock: true,
            width: 820,
            height: _h,
            opacity: '.3',
            content: '<iframe src="{#$urlarr.2#}notes/?id='+id+'" style="width:750px;height:'+(_h-50)+'px;" frameborder="no" border="0" marginwidth="0" marginheight="0" scrolling="auto" allowtransparency="yes"> '
        });
    }
    function _delmsg(obj) {
        if (confirm('即将【'+$(obj).text()+'】并且无法恢复已删除的数据，确认吗？')) {
            $.alert("正在删除...", 0, 1);
            setTimeout(function(){
                window.location.href = '{#weburl('message/del')#}&msgtype={#$_GPC['msgtype']#}&mtitle='+encodeURIComponent($(obj).text());
            }, 10);
        }
    }
    function _reply(id) {
        var _rep = art.dialog({
            title: '回复信息',
            fixed: true,
            lock: true,
            opacity: '.3',
            content: '<textarea id="replutext" name="replutext" data-link="1" style="width:460px;height:200px;padding:0;"></textarea> ',
            button: [{
                name: '提交',
                callback: function () {
                    $.alert('正在提交',0);
                    var dataurl = '';
                    $(".aliedit").find("textarea,input").each(function(){
                        dataurl+= $(this).attr("name")+"="+$(this).val()+"&"
                    });
                    $.ajax({
                        type: "POST",
                        url: "{#$urlarr.2#}reply/"+id+"/{#get_get()#}",
                        data: dataurl,
                        dataType: 'json',
                        success: function (data) {
                            $.alert(data.message);
                            if (data != null && data.success != null && data.success) {
                                $("#isreply_"+id).html("<span title='刷新当前页面可查看回复信息详情'>已回复</span>");
                                _rep.close();
                            }
                        },error : function () {
                            $.alert("提交失败！");
                        },
                        cache: false
                    });
                    return false;
                }
            },{
                name: '取消',
                callback: function () {
                    return true;
                }
            }]
        });
        $("#replutext").tpl_form_aledit();
    }
    function _star(obj,id) {
        var eve = $(obj);
        $.ajax({
            url: "{#$urlarr.2#}star/"+id+"/"+(eve.hasClass("star-on")?0:1)+"/{#get_get()#}",
            dataType: 'json',
            success: function (data) {
                if (data != null && data.success != null && data.success) {
                    eve.toggleClass("star-on");
                    eve.toggleClass("star-off");
                    $("#refreshlist").click();
                }else{
                    $.alert(data.message);
                }
            },error : function () {
                $.alert("提交失败！");
            },
            cache: false
        });
    }
    function keybut(_type){
        var keyval = $('#keyval').val().trim();
        if (keyval == ''){
            if ($('#keyval').attr('data-val')){
                window.location.href = "{#$urlarr.2#}{#get_get('keyval|keytype')#}"; return;
            }else{
                alert("请输入搜索关键词"); $('#keyval').focus(); return;
            }
        }
        window.location.href = "{#$urlarr.2#}{#get_get('keyval|keytype')#}&keytype="+_type+"&keyval="+encodeURIComponent(keyval);
    }
    function zoomin(obj) {
        var tthis = $(obj);
        $imtemp = $('<div class="zoomin-bg"></div><div class="zoomin"><img src="'+tthis.attr("src")+'"></div>');
        $imtemp.eq(1).css({
            top: tthis.offset().top,
            left: tthis.offset().left,
            "max-width": $(window).width() - tthis.offset().left - 10
        });
        $imtemp.click(function(){
            $imtemp.remove();
        });
        $("body").append($imtemp);
    }
    function moimg(obj) {
        var tthis = $(obj);
        if (!tthis.attr("data-moimg")) {
            tthis.attr("data-moimg", "1");
            tthis.mouseover(function(e){
                var _img = $(this).attr("src");
                _img = (_img)?"<img src='"+_img+"'/>":'';
                $tooltip = $("<div id='text-tooltip'>"+_img+"</div>"); //创建 div 元素
                $("body").append($tooltip); //把它追加到文档中
                $("#text-tooltip").css({
                    "top": (e.pageY+5) + "px",
                    "left":  (e.pageX+10)  + "px"
                }).show("fast");   //设置x坐标和y坐标，并且显示
            }).mouseout(function(){
                $("#text-tooltip").remove();  //移除
            }).mousemove(function(e){
                $("#text-tooltip").css({
                    "top": (e.pageY+5) + "px",
                    "left":  (e.pageX+10)  + "px"
                });
            });
            tthis.mouseover();
        }
    }
    $(document).ready(function() {
        _jspage();
        //初始化TAB
        $("#topmenu a").each(function(index){
            $(this).attr("d-index", index);
            $(this).click(function(){
                $("#topmenu a").removeClass("active");
                $(this).addClass("active");
                if ($(this).attr("data-url")) {
                    window.location.href = $(this).attr("data-url");
                }else{
                    $("#tabmenu").children("div").hide().eq($(this).attr("d-index")).show();
                }
            });
        });
        $("#topmenu a:eq({#intval($_GPC['star'])#})").click();
    });
</script>

{#template("footer")#}

</body>
</html>