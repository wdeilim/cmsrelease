
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
        body{background-color:#fff}
        .pagelist_n{position:fixed;right:-5px;bottom:0;padding:10px 1px 10px 7px;background:#FFF;border-top:1px solid #dadada;border-left:1px solid #dadada;border-right:1px solid #dadada}
        #text-tooltip{position:absolute;background-color:#fff;padding:8px;border:1px solid #cc7116}
        #text-tooltip img{display:block;margin:0;max-width:300px;max-height:300px}
        .zoomin {position:absolute;z-index:999999;cursor:-webkit-zoom-out;padding:10px;background-color:rgba(132, 132, 132, 0.8)}
        .zoomin img {max-width:100%;}
        .zoomin-bg {position:fixed;top:0;left:0;width:100%;height:100%;z-index:999998;cursor:-webkit-zoom-out;background-color: rgba(0, 0, 0, 0.3)}
    </style>
    <script type="text/javascript" src="{#$JS_PATH#}jquery-1.11.0.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.alert.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.form.min.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}aliedit/jquery.aliedit.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}artdialog/artDialog.js?skin=default"></script>
</head>
<body>


<div class="form-services">
    <table class="table table-primary messagelist">
        <tbody>
        {#ddb_pc set="数据表:message,用户表:1,列表名:lists,显示数目:10,分页显示:1,分页名:pagelist,当前页:{#$page#},分页地址:{#$pageurl#}(?)/{#get_get()#},排序:indate desc>id desc" where="{#$wheresql#}"#}
        {#foreach from=$lists item=list#}
            <tr>
                {#$g = $tthis->getgroup($list.openid,'*')#}
                {#if $list.tobe > 0#}
                    <td class="alimg"><img onmouseover="moimg(this);" src="{#$IMG_PATH#}avatarsys.jpg"/></td>
                    <td class="lt alname">
                        {#if $list.type=='weixin'#}<span class="wxmsg">微信</span>{#/if#}
                        {#if $list.type=='alipay'#}<span class="almsg">服务窗</span>{#/if#}
                        {#if $list.emulator#}{模拟}{#/if#}
                        (回复)
                        <p>{#message_text($list)#}</p>
                    </td>
                {#else#}
                    <td class="alimg"><img onmouseover="moimg(this);" src="{#$g.avatar#}" onerror="this.src='{#$IMG_PATH#}avatar.jpg?0'"/></td>
                    <td class="lt alname">
                        {#if $list.type=='weixin'#}<span class="wxmsg">微信</span>{#/if#}
                        {#if $list.type=='alipay'#}<span class="almsg">服务窗</span>{#/if#}
                        {#if $list.emulator#}{模拟}{#/if#}
                        {#$g.user_name#}
                        <span class="isreply" id="isreply_{#$list.id#}">
                    {#if $list.replydate#}<span title="{#$list.reply|get_html#}">已回复</span>{#/if#}
                </span>
                        <p>{#message_text($list)#}</p>
                    </td>
                {#/if#}
                <td class="indate">{#$list.indate|date_format:"%Y-%m-%d %H:%M:%S"#}</td>
                <td class="reply">
                    {#if $list.tobe == 0#}
                        <span class="reply" onclick="_reply('{#$list.id#}');"></span>
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
    <div id="pagelist" class="clearfix pagelist_n">
        <a href="javascript:void(0);" style="cursor:default">总数量{#$pagelist_info.total_rows#}个</a>
        <span id="jspage" data-id="messagelist-1">
            <a href="{#$urlarr.now#}{#get_get()#}">刷新</a>
            {#$pagelist#}
        </span>
    </div>
</div>
{#tpl_form_aledit()#}

<script type="text/javascript">
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
    function _reply(id) {
        var _rep = art.dialog({
            title: '回复信息',
            fixed: true,
            lock: true,
            opacity: '.3',
            content: '<textarea id="replutext" name="replutext" style="width:460px;height:200px;padding:0;"></textarea> ',
            button: [{
                name: '提交',
                focus: true,
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
                                $("#isreply_"+id).html('<span title="刷新当前页面可查看回复信息详情">已回复</span>');
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
    $(document).ready(function() {
        $(".alname").find("p").each(function(){
        });
    });
</script>

</body>
</html>