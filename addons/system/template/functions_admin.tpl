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
        .bdtitle{border:none;border-left:.3em #333 solid;padding-left:8px;margin-bottom:10px}
        .label-yun{background-color:#f0ad4e;color:#fff;display:inline;padding:2px 6px;font-size:75%;font-weight:700;text-align:center;white-space:nowrap;vertical-align:baseline;border-radius:3px}
        .label-local{background-color:#5cb85c;color:#fff;display:inline;padding:2px 6px;font-size:75%;font-weight:700;text-align:center;white-space:nowrap;vertical-align:baseline;border-radius:3px}
        .app-icon{width:30px;vertical-align:middle;border-radius:3px;margin-right:5px}
        #text-tooltip{position:absolute;background-color:#fff;padding:8px;border:1px solid #cc7116}
        #text-tooltip img{display:block;margin:10px 0 0;max-width:300px;max-height:300px}
        table.table-primary tbody tr td{height:30px}
        .upcont{border-top:1px solid #ccc;margin-top:10px;padding-top:10px;max-height:200px;max-width:500px;overflow:auto;position:relative}
        .upcont.allwin{position:fixed;left:0;top:0;background:#fff;margin:0;padding:2%;max-height:none;max-width:none;width:96%;height:96%;border:0}
        .upcont .upctit{color:red;margin-bottom:10px}
        .upcont .arrow3,.upcont .arrow4{position:absolute;top:0;right:2%;background:#fff;border:1px solid #ccc;border-top:0;font-style:normal;width:46px;height:26px;line-height:24px;text-align:center;cursor:pointer}
        .upcont .arrow4{display:none;position:fixed;right:50px}
        .upcont.allwin .arrow3{display:none}
        .upcont.allwin .arrow4{display:block}
    </style>
</head>
<body>

{#include file="header_admin.tpl"#}
<script>$('#head-nav-menu li:eq(1)').addClass('active');</script>


<div class="breadcrumb-wrapper">
    <div class="breadcrumb">
        <div class="items">
            <span class="description">当前位置：</span>
            <span class="current">功能管理</span>
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
                            <a class="button button-primary button-hover" href="{#$urlarr.3#}functions/">功能列表</a>&nbsp;
                            <a class="button button-primary" href="{#$urlarr.3#}functionsadd/">新安装功能</a>&nbsp;
                            <a class="button button-primary" href="{#$urlarr.3#}functionstore/">查找更多功能</a>
                        </div>
                    </div>
                </div>
                <div class="bd">
                    <div class="bdtitle">已安装的模块</div>
                    <div class="table-wrapper">

                        <table class="table table-primary">
                            <thead>
                            <tr>
                                <th width="60">序号</th>
                                <th class="align-left" width="160">功能名称</th>
                                <th class="align-left" width="110">标识</th>
                                <th class="align-left" width="100">版本</th>
                                <th class="align-center" width="60" title="用户自助开通此功能需要的{#$smarty.const.POINT_NAME#}。">价格</th>
                                <th class="align-left" width="80">作者</th>
                                <th class="align-left">功能介绍</th>
                                <th class="align-left">操作</th>
                            </tr>
                            </thead>
                            <tbody id="applist">
                            {#ddb_pc set="数据表:functions,列表名:lists,显示数目:1000,排序:`default`>`inorder` DESC>`id` DESC"#}
                            {#foreach from=$lists item=list#}
                                <tr data-funid="{#$list.id#}" data-default="{#$list.default#}" data-en="{#$list.title_en#}" data-version="{#$list.version#}">
                                    <td class="align-center"><span>{#$list._n#}</span></td>
                                    <td class="align-left">
                                        <img src="{#$smarty.const.BASE_URI#}addons/{#$list.title_en#}/icon.png" onerror="this.src='{#$IMG_PATH#}app.png'" class="app-icon" data-title="{#$list.title#}">
                                        {#$list.title#}
                                    </td>
                                    <td class="align-left">{#$list.title_en#}</td>
                                    <td class="align-left">
                                        {#$list.version#}
                                        <span class="hide-form" id="{#$list.title_en#}" data-cloud="{#$list.cloud#}" style="display:none"></span>
                                    </td>
                                    <td class="align-center">
                                        {#if $list.point < 0#}
                                            <span title="关闭用户自助开通功能">-</span>
                                        {#elseif $list.point == 0#}
                                            <span title="用户免费自助开通功能">免费</span>
                                        {#else#}
                                            <span title="用户需要支付{#$list.point#}{#$smarty.const.POINT_NAME#}开通功能">{#$list.point#}{#$smarty.const.POINT_NAME#}</span>
                                        {#/if#}
                                    </td>
                                    <td class="align-left">
                                        {#if $list.url#}
                                            <a href="{#$list.url#}" target="_blank">{#$list.author#}</a>
                                        {#else#}
                                            {#$list.author#}
                                        {#/if#}
                                    </td>
                                    <td class="align-left">
                                        <a href="javascript:;" id="win" d="con">{#$list.ability|get_html:25#}</a>
                                        <span style="display:none">{#$list.content#}</span>
                                    </td>
                                    <td class="align-left">
                                        {#if !$list.default#}
                                            <a href="javascript:;" id="win" d="edit">编辑</a>&nbsp;
                                            <a href="javascript:;" id="win" d="uninstall">卸载</a>
                                        {#/if#}
                                        <span class="hide-install" id="{#$list.title_en#}" style="display:none"></span>
                                        <span class="hide-install-upcontent" id="{#$list.title_en#}" style="display:none"></span>
                                    </td>
                                </tr>
                                {#foreachelse#}
                                <tr>
                                    <td colspan="8" align="center" class="align-center">无</td>
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




<div id="addfunction" style="display: none;">
    没有待安装的模块
</div>

<script type="text/javascript">
    function upgradeallwin(obj, l) {
        if (l) {
            $(obj).parent().removeClass("allwin");
            $("body").css("overflow","auto");
        }else{
            $(obj).parent().addClass("allwin");
            $("body").css("overflow","hidden");
        }
    }
    function upgrade(en) {
        var upcontent = "<div class='upcont'>" +
                "<i class='arrow3' onclick='upgradeallwin(this);'>全屏</i>" +
                "<i class='arrow4' onclick='upgradeallwin(this,1);'>关闭</i>" +
                "<div class='upctit'>更新说明：</div>" +
                "<span id='_upcontent'>说明加载中...</span>" +
                "</div>";
        var dialog = art.dialog({
            fixed: true,
            lock: true,
            content: '确认操作：点击确定立即更新此功能模块！' + upcontent,
            opacity: '.3',
            button: [{
                name: '确定',
                focus: true,
                callback: function () {
                    this.close();
                    $.alert("正在更新...", 0, 1);
                    setTimeout(function(){
                        window.location.href = "{#$urlarr.3#}functionsupgrade/?en=" + en;
                    },10);
                    return false;
                }
            },{
                name: '取消',
                callback: function () {
                    return true;
                }
            }]
        });
        if ($('.hide-install-upcontent[id="' + en + '"]').html()) {
            $intemp = $('<div>'+$('.hide-install-upcontent[id="' + en + '"]').html()+'</div>');
            $("#_upcontent").html($intemp);
            $intemp.find("img").load(function(){
                dialog.position("50%","50%");
            });
            dialog.position("50%","50%");
        }else{
            if ($('.hide-install[id="' + en + '"]').find("a").attr("data-source") == 'local') {
                $("#_upcontent").html("来自本地更新。");
                $('.hide-install-upcontent[id="' + en + '"]').html("来自本地更新。");
            }else{
                $.post('{#$urlarr.now#}', {method: 'upgrade_upcontent', module: en, version: $("tr[data-en='"+en+"']").attr("data-version")},function(dat){
                    try {
                        $intemp = $('<div>'+dat+'</div>');
                        $("#_upcontent").html($intemp);
                        $intemp.find("img").load(function(){
                            dialog.position("50%","50%");
                        });
                        $('.hide-install-upcontent[id="' + en + '"]').html(dat);
                    } catch(err) {
                        $("#_upcontent").html("加载失败！");
                    }
                });
            }
        }
    }
    function _upgrade() {
        var enlists = "";
        $("#applist").find("tr").each(function(){
            var tthis = $(this);
            if (tthis.attr("data-default") == "0") {
                enlists+= tthis.attr("data-en")+",";
            }
        });
        if (enlists) {
            {#if $_GPC['winopen']#}
            $.alert("获取更新信息中....", 0);
            {#/if#}
            $.post('{#$urlarr.now#}', {method: 'upgrade_lists',funs: enlists},function(dat){
                {#if $_GPC['winopen']#}
                $.alert(0);
                {#/if#}
                try {
                    var ret = $.parseJSON(dat);
                    var havenew = false;
                    var setcloud = "";
                    $("#applist").find("tr").each(function(){
                        if ($(this).attr("data-default") == "0") {
                            var n = $(this).attr('data-en');
                            var v = $(this).attr('data-version');
                            if(ret[n]) {
                                $('.hide-form[id="' + n + '"]').html('<span class="label-yun" title="来自云中心安装">云中心</span>').show();
                                if(ret[n].version > v) {
                                    $('.hide-install[id="' + n + '"]').html('<a href="javascript:;" onclick="upgrade(\''+n+'\');" style="color:red;padding-left:5px" title="来自云中心服务更新" data-source="cloud">更新</a>').show();
                                    havenew = true;
                                }
                                if ($('.hide-form[id="' + n + '"]').attr("data-cloud") != "1") {
                                    setcloud+= n+",";
                                }
                            } else {
                                $('.hide-form[id="' + n + '"]').html('<span class="label-local" title="来自本地安装">本地</span>').show();
                                if (ret['__LOCAL'][n].version > v) {
                                    $('.hide-install[id="' + n + '"]').html('<a href="javascript:;" onclick="upgrade(\''+n+'\');" style="color:green;padding-left:5px" title="来自本地服务更新" data-source="local">更新</a>').show();
                                }
                            }
                        }
                    });
                    if (setcloud != "") {
                        $.ajax({
                            type: "POST",
                            url: "{#$urlarr.3#}setcloud/",
                            data: {"setcloud": setcloud},
                            success: function (data) { },
                            cache: false
                        });
                    }
                    if (havenew == true) {
                        $.post('{#$urlarr.now#}', {method: 'upgrade_lists_have'});
                        $('a[data-source="cloud"]').each(function(index){
                            {#if $_GPC['winopen']#}
                            if (index == 0) {
                                $('body,html').animate({ scrollTop: $(this).parents("tr").offset().top}, 200);
                            }
                            {#/if#}
                            var trt = $(this).parents("tr").offset().top,
                                    trl = $(this).parents("tr").offset().left,
                                    trw = $(this).parents("tr").width(),
                                    intemp = $('<div style="background-color:#ff0000;opacity:0.5;width:0;height:51px;position:absolute;top:'+trt+'px;left:'+trl+'px"></div>');
                            setTimeout(function(){
                                $("body").append(intemp);
                                intemp.animate({width:trw}, 1000, function(){
                                    setTimeout(function(){ intemp.remove(); }, 300);
                                });
                            }, index * 300);
                        });
                    }
                } catch(err) {}
            });
        }
    }
    $(document).ready(function() {
        _upgrade();
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
                _dialog.content("<div style='max-width:800px;'>"+$(this).parent().find(">span").html()+"</div>");
                _dialog.button({
                    name: '关闭',
                    callback: function () {
                        return true;
                    }
                });
            }else if (d == 'status'){
                var _funid = $(this).parent().parent().attr("data-funid");
                _dialog.content('确定要修改状态吗？');
                _dialog.button({
                    name: '确定',
                    callback: function () {
                        $.ajax({
                            url: "{#$urlarr.3#}status/" + _funid + "/",
                            success: function (data) {
                                window.location.reload(true);
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
            }else if (d == 'edit'){
                var _funid = $(this).parent().parent().attr("data-funid");
                _dialog.title('编辑功能');
                $.ajax({
                    url: "{#$urlarr.3#}editfunc/" + _funid + "/",
                    dataType: 'html',
                    success: function (html) {
                        _dialog.content(html);
                        _dialog.button({
                            name: '保存',
                            callback: function () {
                                $.alert("正在保存...", 0, 1);
                                _dialog.hide();
                                $.ajax({
                                    type: "POST",
                                    url: "{#$urlarr.3#}editfunc/" + _funid + "/",
                                    data: {
                                        dosubmit: 1,
                                        edit_title: $("#edit_title").val(),
                                        edit_ability: $("#edit_ability").val(),
                                        edit_content: $("#edit_content").val(),
                                        edit_icon: $("#edit_icon").val(),
                                        edit_point: $("#edit_point").val()
                                    },
                                    dataType: "json",
                                    success: function (data) {
                                        $.alert(0);
                                        if (data != null && data.success != null && data.success) {
                                            $.alert("保存成功！", 0);
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
                    },
                    cache: false
                });
            }else if (d == 'uninstall'){
                var _funid = $(this).parent().parent().attr("data-funid");
                _dialog.title('卸载功能');
                _dialog.content('确定要删除此功能并且不可恢复吗？');
                _dialog.button({
                    name: '确定',
                    callback: function () {
                        _dialog.close();
                        $.alert("正在卸载...", 0, 1);
                        setTimeout(function(){
                            window.location.href = "{#$urlarr.3#}functionsuninstall/?id=" + _funid;
                        },10);
                        return false;
                    }
                },{
                    name: '取消',
                    callback: function () {
                        return true;
                    }
                });
            }else if (d == 'del'){
                _dialog.content('确定要删除此功能并且不可恢复吗？');
                _dialog.button({
                    name: '确定',
                    callback: function () {
                        $.ajax({
                            url: "{#$urlarr.3#}delfunc/" + _funid + "/",
                            dataType: 'json',
                            success: function (data) {
                                if (data != null && data.success != null && data.success) {
                                    window.location.reload(true);
                                } else {
                                    _dialog.close();
                                    $.alert(data.message);
                                }
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
{#include file="footer_admin.tpl"#}

</body>
</html>