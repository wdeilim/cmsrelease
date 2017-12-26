<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>功能管理 - {#$BASE_NAME#}</title>
    <link rel="stylesheet" href="{#$CSS_PATH#}admin/normalize.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}admin/global.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}admin/detail.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}font-awesome.css"/>
    <script type="text/javascript" src="{#$JS_PATH#}jquery-1.11.0.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.alert.js"></script>
    <style type="text/css">
        .bdtitle {border: none;border-left: 0.3em #333 solid;padding-left: 8px;margin-bottom: 10px;}
        .app-icon {width: 30px;vertical-align: middle;border-radius: 3px;margin-right: 5px;}
        #text-tooltip{position:absolute;background-color:#fff;padding:8px;border:1px solid #cc7116}
        #text-tooltip img{display:block;margin:10px 0 0;max-width:300px;max-height:300px}
    </style>
</head>
<body>

{#include file="header_admin.tpl"#}
<script>$('#head-nav-menu>li:eq(1)').addClass('active');</script>


<div class="breadcrumb-wrapper">
    <div class="breadcrumb">
        <div class="items">
            <span class="description">当前位置：</span>
            <span class="current">功能管理</span>
            <i class="iconfont">&#xe621;</i>
            <span class="current">新安装功能</span>
        </div>
    </div>
</div>


<div class="main-wrapper">
    <div class="main">
        <div class="main-content">
            <div class="module">
                <div class="hd">
                    <div class="control-group cf">
                        <div class="control-group-left">
                            <a class="button button-primary" href="{#$urlarr.3#}functions/">功能列表</a>&nbsp;
                            <a class="button button-primary button-hover" href="{#$urlarr.3#}functionsadd/">新安装功能</a>&nbsp;
                            <a class="button button-primary" href="{#$urlarr.3#}functionstore/">查找更多功能</a>
                        </div>
                    </div>
                </div>
                <div class="bd">
                    <div class="bdtitle">未安装的模块(本地模块)</div>
                    <div class="table-wrapper">
                        <table class="table table-primary">
                            <thead>
                            <tr>
                                <th class="align-left">功能名称</th>
                                <th class="align-left">标识</th>
                                <th class="align-left">版本</th>
                                <th class="align-left">作者</th>
                                <th class="align-left">功能介绍</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            {#foreach from=$localUninstallModules item=list#}
                                <tr>
                                    <td class="align-left">
                                        <img src="{#$smarty.const.BASE_URI#}addons/{#$list.title_en#}/icon.png" onerror="this.src='{#$IMG_PATH#}app.png'" class="app-icon" data-title="{#$list.title#}">
                                        {#$list.title#}
                                    </td>
                                    <td class="align-left">{#$list.title_en#}</td>
                                    <td class="align-left">{#$list.version#}</td>
                                    <td class="align-left">
                                        {#if $list.url#}
                                            <a href="{#$list.url#}" target="_blank">{#$list.author#}</a>
                                        {#else#}
                                            {#$list.author#}
                                        {#/if#}
                                    </td>
                                    <td class="align-left">
                                        <a href="javascript:;" id="win" d="con">{#$list.ability#}</a>
                                        <span style="display:none">{#$list.content#}</span>
                                    </td>
                                    <td class="align-center">
                                        {#if $list.version_error#}
                                            版本不兼容
                                        {#else#}
                                            <a id="win" d="install" data-en="{#$list.title_en#}" href="javascript:;">安装</a>
                                        {#/if#}
                                    </td>
                                </tr>
                                {#foreachelse#}
                                <tr>
                                    <td colspan="6" align="center" class="align-center">目前没有未安装的本地模块</td>
                                </tr>
                            {#/foreach#}
                            </tbody>
                        </table>
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
                content: '正在加载...',
                opacity: '.3',

            });
            var d = $(this).attr('d');
            if (d == 'con'){
                _dialog.content("<div style='max-width:800px;'>"+$(this).parent().find(">span").html()+"</div>");
                _dialog.button({
                    name: '关闭',
                    callback: function () {
                        return true;
                    }
                });
            }else if (d == 'install'){
                var en = $(this).attr('data-en');
                _dialog.content('确认操作：点击确定立即安装功能模块！');
                _dialog.button({
                    name: '确定',
                    focus: true,
                    callback: function () {
                        _dialog.close();
                        $.alert("正在安装...", 0, 1);
                        setTimeout(function(){
                            window.location.href = "{#$urlarr.3#}functionsinstall/?en=" + en;
                        },10);
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
{#include file="footer_admin.tpl"#}

</body>
</html>