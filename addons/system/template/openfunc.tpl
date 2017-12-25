
<!DOCTYPE html>
<html>
<head lang="zh-CN">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>添加功能 - {#$BASE_NAME#}</title>
    <link rel="stylesheet" href="{#$CSS_PATH#}normalize.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}global.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}alert.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}font-awesome.css"/>
    <style type="text/css">
        .align-left {text-align: left;}
        .align-center {text-align: center;}
        .addok {color:#999999; text-decoration:line-through}
        .noopen {color:#ababab; text-decoration:line-through}
        .freeopen {color: #00aa28;}
        .bdtitle {border: none;border-left: 0.3em #333 solid;padding-left: 8px;margin-bottom: 10px;}
        .label-yun {background-color: #f0ad4e;color: #ffffff;display: inline;padding: 2px 6px;font-size: 75%;font-weight: 700;text-align: center;white-space: nowrap;vertical-align: baseline;border-radius: 3px;}
        .label-local {background-color: #5cb85c;color: #ffffff;display: inline;padding: 2px 6px;font-size: 75%;font-weight: 700;text-align: center;white-space: nowrap;vertical-align: baseline;border-radius: 3px;}
        .app-icon {width: 30px;vertical-align: middle;border-radius: 3px;margin-right: 5px;}
        #text-tooltip{position:absolute;background-color:#fff;padding:8px;border:1px solid #cc7116}
        #text-tooltip img{display:block;margin:10px 0 0;max-width:300px;max-height:300px}
        .button-small-rec {height:28px;line-height:26px;font-size:14px;padding:2px 5px;margin-left:5px;}
    </style>
    <script type="text/javascript" src="{#$JS_PATH#}jquery-1.11.0.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.alert.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.form.min.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}linkage.js"></script>
</head>
<body>
{#include file="header_user.tpl"#}
<script>$('#head-nav-menu>li:eq(4)').addClass('active');</script>


<div class="wrapper">
    <div class="breadcrumb">
        <div class="items">
            <a href="{#$urlarr.2#}">平台首页</a>
            <i class="iconfont">&#xe621;</i>
            <span>添加功能</span>
        </div>
    </div>
    <div class="main add-mp">
        <div class="hd">
            <h1 class="title">
                <span id="alname">
                    {#if $info.wx_name#}<span style="padding-right:20px">{#if $info['wx_level'] == 7#}企业号：{#else#}公众号：{#/if#}{#$info.wx_name#}</span>{#/if#}
                    {#if $info.al_name#}<span style="padding-right:20px">服务窗：{#$info.al_name#}</span>{#/if#}
                </span>
                <span style="display:block;font-size:16px;padding-top:6px">
                    用户名: {#$infouser.username#}； 帐户余额: {#$infouser.point#}{#$smarty.const.POINT_NAME#}
                    <a href="{#$urlarr.2#}me_point/recharge/"
                       class="button-primary button-rounded button-small-rec">{#$smarty.const.POINT_NAME#}充值</a>
                </span>
            </h1>
            <div class="control">
                <a href="{#$urlarr.2#}" class="button button-primary button-rounded button-small">返回</a>
            </div>
        </div>
        <div class="bd">
            <div class="section">
                <div class="table-wrapper">

                    <table class="table table-primary">
                        <thead>
                        <tr>
                            <th width="60">序号</th>
                            <th class="align-left">功能名称</th>
                            <th class="align-left">价格</th>
                            <th class="align-left">功能介绍</th>
                            <th class="align-center">操作</th>
                        </tr>
                        </thead>
                        <tbody id="applist">
                        {#ddb_pc set="数据表:functions,列表名:lists,显示数目:20,分页显示:1,分页名:pagelist,当前页:GET[page],分页地址:{#$pageurl#}?openalid={#$_GPC['openalid']#}&page=(?),排序:case when `point`<0 then 1 else 0 end>`point`>`default`>`inorder` DESC>`id` DESC" where=$wheresql#}
                        {#foreach from=$lists item=list#}
                            <tr>
                                <td class="align-center"><span>{#$list._n#}</span></td>
                                <td class="align-left">
                                    <img src="{#$smarty.const.BASE_URI#}addons/{#$list.title_en#}/icon.png" onerror="this.src='{#$IMG_PATH#}app.png'" class="app-icon" data-title="{#$list.title#}">
                                    {#$list.title#}
                                </td>
                                <td class="align-left">
                                    {#if $list.point < 0#}
                                        <span title="关闭用户自助开通功能" class="noopen">未开放</span>
                                    {#elseif $list.point == 0#}
                                        <span title="免费自助开通功能" class="freeopen">免费</span>
                                    {#else#}
                                        <span title="需要支付{#$list.point#}{#$smarty.const.POINT_NAME#}开通功能">{#$list.point#}{#$smarty.const.POINT_NAME#}</span>
                                    {#/if#}
                                </td>
                                <td class="align-left">
                                    <a href="javascript:;" id="win" d="con">{#$list.ability|get_html:25#}</a>
                                    <span style="display:none">{#$list.content#}</span>
                                </td>
                                <td class="align-center">
                                    {#if $list.point < 0#}
                                        <span class="addok">未开放</span>
                                    {#elseif in_array($list['title_en'], $functions)#}
                                        <span class="addok">已添加</span>
                                    {#else#}
                                        <a href="javascript:void(0)" id="win" d="add"
                                           data-title="{#$list.title#}"
                                           data-en="{#$list.title_en#}"
                                           data-point="{#$list.point#}">添加</a>
                                    {#/if#}
                                </td>
                            </tr>
                            {#foreachelse#}
                            <tr>
                                <td colspan="5" align="center" class="align-center">无</td>
                            </tr>
                        {#/foreach#}
                        </tbody>
                    </table>
                    <div id="pagelist" class="clearfix">
                        <a href="javascript:void(0);" style="cursor:default">总数量{#$pagelist_info.total_rows#}个</a>
                        {#$pagelist#}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('a#win').click(function() {
            var _dialog = art.dialog({
                fixed: true,
                lock: true,
                title: '消息',
                content: '正在加载...',
                opacity: '.3'

            });
            var d = $(this).attr('d');
            if (d == 'con'){
                _dialog.content($(this).parent().find(">span").html());
                _dialog.button({
                    name: '关闭',
                    callback: function () {
                        return true;
                    }
                });
            }else if (d == 'add'){
                var title = $(this).attr("data-title");
                var en = $(this).attr("data-en");
                var point = $(this).attr("data-point");
                var pointtxt ="免费";
                if (point > 0) pointtxt = point+"{#$smarty.const.POINT_NAME#}";
                _dialog.title('添加功能');
                _dialog.content("功能："+title+"<br/>价格："+pointtxt+"");
                _dialog.button({
                    name: '添加',
                    callback: function () {
                        $.alert("正在添加...", 0, 1);
                        $.ajax({
                            type: "POST",
                            url: "{#$urlarr.3#}?openalid={#$_GPC['openalid']#}",
                            data: {
                                dosubmit: 1,
                                title_en: en
                            },
                            dataType: "json",
                            success: function (data) {
                                $.alert(0);
                                if (data != null && data.success != null && data.success) {
                                    $.alert("添加成功！", 0);
                                    setTimeout(function(){ window.location.reload(); },10);
                                }else{
                                    alert(data.message);
                                    _dialog.show();
                                }
                            },error : function () {
                                $.alert(0);
                                alert("服务器繁忙，请稍后再试！");
                                _dialog.show();
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
                });
            }
        });
        //
        $("img.app-icon").parent("td").mouseover(function(e){
            var _text = $(this).text();
            var _img = $(this).find(".app-icon").attr("src");
            _img = (_img)?"<img src='"+_img+"'/>":'';
            if (!_text) return;
            $tooltip = $("<div id='text-tooltip'>"+_text+_img+"</div>"); //创建 div 元素
            $("body").append($tooltip); //把它追加到文档中
            $("#text-tooltip").css({
                "top": (e.pageY-($tooltip.height()/2)) + "px",
                "left":  (e.pageX+20)  + "px"
            }).show("fast");   //设置x坐标和y坐标，并且显示
        }).mouseout(function(){
            $("#text-tooltip").remove();  //移除
        }).mousemove(function(e){
            $("#text-tooltip").css({
                "top": (e.pageY-($tooltip.height()/2)) + "px",
                "left":  (e.pageX+20)  + "px"
            });
        });
    });
</script>
{#include file="footer.tpl"#}

</body>
</html>