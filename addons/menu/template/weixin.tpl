
<!DOCTYPE html>
<html>
<head lang="zh-CN">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{#$_A.f.title#} - {#$BASE_NAME#}</title>
    <link rel="stylesheet" href="{#$CSS_PATH#}normalize.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}global.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}functions.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}alert.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}font-awesome.css"/>
    <link rel="stylesheet" href="{#$NOW_PATH#}css/default.css"/>
    <script type="text/javascript" src="{#$JS_PATH#}jquery-1.11.0.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.alert.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.form.min.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}artdialog/artDialog.js?skin=default"></script>
    <script type="text/javascript" src="{#$NOW_PATH#}js/jquery.changeevent.js"></script>
    <style>
        .topmenu{position:relative;width:1126px;height:36px;margin:15px auto 8px;padding-left:24px;background-color:#fff;border-bottom:3px solid #09c}
        .topmenu a{display:block;float:left;line-height:36px;padding:0 12px;margin-right:12px;color:#09c}
        .topmenu a.active,.topmenu a:hover{-webkit-border-radius:3px 3px 0 0;-moz-border-radius:3px 3px 0 0;border-radius:3px 3px 0 0;color:#fff;font-weight:bold;background-color:#09c}
        .topmenu div{display:none;border:1px solid #09c;border-top:3px solid #09c;background-color:#fff;position:absolute;top:36px;left:0;z-index:9999}
        .topmenu div a{float:none;margin:0}
        .topmenu div a.active,.topmenu div a:hover{-webkit-border-radius:0;-moz-border-radius:0;border-radius:0}
        .tabmenu{display:none}
        .backing{margin-left:10px}
        .authdesc{margin-bottom:10px;color:#FF6600;font-weight:600;}
    </style>
</head>
<body>
{#template("header")#}

<table style="display: none;">
    <tbody id="menu-temp">
    <tr>
        <td>
            <div class="col" id="childlink">
                <span class="indent text text-muted">|----</span>
            </div>
            <div class="col">
                <input class="form-control" type="text" id="inorder">
            </div>
        </td>
        <td>
            <div class="col" id="childlink">
                <span class="indent text text-muted">|----</span>
            </div>
            <div class="col">
                <input class="form-control" type="text" id="title">
            </div>
            <div class="col" id="addlink">
                <a class="add" href="javascript:;" onclick="inwin(this,'add');"><span class="icontext">+</span></a>
            </div>
        </td>
        <td class="al-center">
            <select class="form-control" id="keytype" onchange="inwin(this,'keytype');">
                <option value="click">发送信息 （点击推事件）</option>
                <option value="view">跳转 URL （链接型菜单）</option>
                <option value="scancode_push">扫码推事件</option>
                <option value="scancode_waitmsg">扫码推事件且弹出“消息接收中”提示框</option>
                <option value="pic_sysphoto">弹出系统拍照发图</option>
                <option value="pic_photo_or_album">弹出拍照或者相册发图</option>
                <option value="pic_weixin">弹出微信相册发图器</option>
                <option value="location_select">弹出地理位置选择器</option>
            </select>
        </td>
        <td class="al-center"><div class="form-control possel"><input type="text" id="keytext" onmousemove="keytextmove(this);" onmouseout="keytextmove(this,1);"></div></td>
        <td class="al-center"><input class="form-control" type="checkbox" id="status" checked="true"></td>
        <td class="al-center"><a class="normal-link" href="javascript:;" onclick="inwin(this,'del');">删除</a></td>
    </tr>
    </tbody>
</table>



<div class="wrapper">
    <div class="breadcrumb">
        <div class="items">
            <span class="description">当前位置：</span>
            <span>{#$_A.f.title#}</span>
            <i class="iconfont">&#xe621;</i>
            <span>{#$_A.al.wx_name#}</span>
        </div>
    </div>

    <div class="main cf custom-menu">
        {#if $_A.al.wx_level == 7#}
            <div class="topmenu" id="topmenu">
                {#foreach $_A['al']['wx_corp'] AS $item#}
                    {#if $item['type'] == 1#}
                        <a href="{#weburl("menu/weixin")#}&agentid={#$item['agentid']#}"
                                {#if intval($_GPC['agentid'])==$item['agentid']#} class="active"{#/if#}>{#$item['name']#}</a>
                    {#/if#}
                {#/foreach#}
            </div>
        {#/if#}
        {#if $_A.al.al_appid#}
            <div class="topmenu" id="topmenu">
                <a href="javascript:;" class="active">微信公众号</a>
                <a href="{#weburl("menu/index")#}">支付宝服务窗</a>
            </div>
        {#/if#}
        <div class="mod">
            <div class="main-bd">
                <div class="tip mod mod-rounded mod-bordered">
                    <p>注意：1级菜单最多只能开启3个，2级子菜单最多开启5个！</p>
                    <p>生成自定义菜单，必须在已经保存的基础上进行，临时勾选启用点击生成是无效的！第一步必须先修改保存状态！第二步点击生成！</p>
                    <p>当您为自定义菜单填写链接地址时请填写以“http://”开头，这样可以保证用户手机浏览的兼容性更好</p>
                    <p>撤销自定义菜单：撤销后，您的微信公众账号上的自动应以菜单将不存在；如果您想继续在微信公众账号上使用自定义菜单，请点击“生成自定义参贷”按钮，将重新启用！</p>
                    {#if $_A.al.wx_level == 7#}
                        <p>特殊触发关键词：类型选“发送信息”、触发关键词填写“#+内容”直接回复内容信息，例如填写“#您好”用户点击此菜单时则直接返回“您好”。</p>
                    {#else#}
                        <p>特殊触发关键词①：类型选“发送信息”、触发关键词填写“#+内容”直接回复内容信息，例如填写“#您好”用户点击此菜单时则直接返回“您好”。</p>
                        <p>特殊触发关键词②：类型选“发送信息”、触发关键词填写“#多客服”系统则自动转到多客服信息服务功能、填写“#多客服+内容”时转到多客服同时回复内容。</p>
                    {#/if#}
                </div>

                <div id="typemsg" style="display: none;">
                    <p><strong>1、click：发送信息</strong></p>
                    <p>用户点击click类型按钮后，微信服务器会通过消息接口推送消息类型为event	的结构给开发者（参考消息接口指南），并且带上按钮中开发者填写的key值，开发者可以通过自定义的key值与用户进行交互；</p>
                    <p><strong>2、view：跳转URL</strong></p>
                    <p>用户点击view类型按钮后，微信客户端将会打开开发者在按钮中填写的网页URL，可与网页授权获取用户基本信息接口结合，获得用户基本信息。</p>
                    <p><strong>3、scancode_push：扫码推事件</strong></p>
                    <p>用户点击按钮后，微信客户端将调起扫一扫工具，完成扫码操作后显示扫描结果（如果是URL，将进入URL），且会将扫码的结果传给开发者，开发者可以下发消息。</p>
                    <p><strong>4、scancode_waitmsg：扫码推事件且弹出“消息接收中”提示框</strong></p>
                    <p>用户点击按钮后，微信客户端将调起扫一扫工具，完成扫码操作后，将扫码的结果传给开发者，同时收起扫一扫工具，然后弹出“消息接收中”提示框，随后可能会收到开发者下发的消息。</p>
                    <p><strong>5、pic_sysphoto：弹出系统拍照发图</strong></p>
                    <p>用户点击按钮后，微信客户端将调起系统相机，完成拍照操作后，会将拍摄的相片发送给开发者，并推送事件给开发者，同时收起系统相机，随后可能会收到开发者下发的消息。</p>
                    <p><strong>6、pic_photo_or_album：弹出拍照或者相册发图</strong></p>
                    <p>用户点击按钮后，微信客户端将弹出选择器供用户选择“拍照”或者“从手机相册选择”。用户选择后即走其他两种流程。</p>
                    <p><strong>7、pic_weixin：弹出微信相册发图器</strong></p>
                    <p>用户点击按钮后，微信客户端将调起微信相册，完成选择操作后，将选择的相片发送给开发者的服务器，并推送事件给开发者，同时收起相册，随后可能会收到开发者下发的消息。</p>
                    <p><strong>8、location_select：弹出地理位置选择器</strong></p>
                    <p>用户点击按钮后，微信客户端将调起地理位置选择工具，完成选择操作后，将选择的地理位置发送给开发者的服务器，同时收起位置选择工具，随后可能会收到开发者下发的消息。</p>
                </div>

                <div class="control-group">
                    <a class="button button-primary button-rounded" href="javascript:;" onclick="inwin(this,'addt');">+添加主菜单</a> &nbsp;
                    <a class="button button-primary button-rounded" href="javascript:;" onclick="inwin(this,'typemsg');">查看类型说明</a>
                </div>

                <form action="{#$urlarr.2#}wxsave/{#get_get()#}"  method="post" id="menuform">
                    <table style="margin-bottom: 20px;" class="table table-primary" id="menu-table">
                        <thead>
                        <tr>
                            <th style="width: 181px;">显示顺序</th>
                            <th style="width: 210px;">主菜单名称</th>
                            <th>类型</th>
                            <th>触发关键词或链接地址</th>
                            <th>启用</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody id="menu-no">
                        <tr>
                            <td colspan="6" align="center" class="align-center">
                                <div onclick="inwin(this,'addt');">无菜单，请先添加主菜单</div>
                            </td>
                        </tr>
                        </tbody>
                    </table>

                    {#if $_A.al.wx_level == 7#}
                        <div class="authdesc">企业号权限说明：管理组须拥有应用的管理权限，并且应用必须设置在回调模式。</div>
                    {#/if#}
                    <div class="control-group">
                        <input class="button button-primary button-rounded" style="height: 34px;" type="submit" value="保存"/>
                        <input type="hidden" name="dosubmit" value="1"/>
                        <a class="button button-primary button-rounded" href="javascript:;" onclick="inwin(this,'bemenu');">生成自定义菜单</a>
                        <a class="button button-primary button-rounded" href="javascript:;" onclick="inwin(this,'synchronous');">同步菜单结构</a>
                        <a class="button button-primary button-rounded" href="javascript:;" onclick="inwin(this,'delmenu');">撤销自定义菜单</a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

{#include file="bottom.tpl"#}

<script type="text/javascript">
    window.menui = 0;
    function inwin(obj, t){
        window.menui++;
        var o = $(obj);
        if (t == 'keytype'){
            var ov = o.val();
            var ev = o.parent().next().find("#keytext");
            if (ov == 'click' || ov == 'view' || ov == 'scancode_waitmsg'){
                ev.show();
            }else{
                ev.hide();
                if (!ev.val()) ev.val("0");
            }
        }else if (t == 'typemsg'){
            art.dialog({
                fixed: true,
                lock: true,
                title: '类型说明',
                content: $('#typemsg').html(),
                opacity: '.3',
                button: [{
                    name: '关闭',
                    callback: function () {
                        return true;
                    }
                }]
            });
        }else if (t == 'addt'){
            if ($('table#menu-table tbody').length > 3){
                $.inModal('1级菜单最多只能开启3个');
                return;
            }
            window.changeevent = true;
            $m = $('<tbody>' + $('#menu-temp').html() + '</tbody>');
            $('table#menu-table').append($m);
            $m.find("#childlink").hide();
            $m.attr('data-menui', window.menui);
            $m.find("input,select").each(function(){
                $(this).attr('name', 'menu_'+window.menui+'_'+$(this).attr('id'))
            });
            keyhide();
        }else if (t == 'add'){
            var p = o.parent().parent().parent().parent();
            if (p.find('tr').length > 5){
                $.inModal('2级子菜单最多开启5个');
                return;
            }
            window.changeevent = true;
            $m = $($('#menu-temp').html());
            p.append($m);
            $m.attr('data-rank','child');
            //$m.find("#inorder").hide();
            $m.find("#childlink").next().find("input").css({width:122});
            $m.find("#addlink").hide();
            $m.attr('data-menui', window.menui);
            $m.find("input,select").each(function(){
                $(this).attr('name', 'menuchild_'+p.attr('data-menui')+'_'+window.menui+'_'+$(this).attr('id'))
            });
            keyhide();
        }else if (t == 'del'){
            window.changeevent = true;
            var p = o.parent().parent();
            if (p.attr('data-rank') == 'child'){
                p.fadeOut(200);
                setTimeout(function(){p.remove();keyhide();},200);
            }else{
                if (p.parent().find('tr').length > 1){
                    art.dialog({
                        fixed: true,
                        lock: true,
                        content: '此一级菜单有子菜单，你确定要删除吗？',
                        opacity: '.3',
                        button: [{
                            name: '确定',
                            callback: function () {
                                p.parent().fadeOut(200);
                                setTimeout(function(){p.parent().remove();keyhide();},200);
                                return true;
                            }
                        },{
                            name: '取消',
                            callback: function () {
                                return true;
                            }
                        }]
                    });
                }else{
                    p.parent().fadeOut(200);
                    setTimeout(function(){p.parent().remove();keyhide();},200);
                }
            }
        }else if (t == 'bemenu'){
            if ($("#menuform").changeevent(true) || window.changeevent === true) {
                $.showModal("您已经修改了菜单！<br/>请先点击【保存】后再生成菜单！","","",true);
                return false;
            }
            $.alert('正在生成...', 0);
            $.ajax({
                url: "{#$urlarr.2#}wxsavemenu/{#get_get()#}",
                type: "get",
                dataType: "json",
                success: function (data) {
                    $.alert(0);
                    if (data != null && data.success != null && data.success) {
                        $.showModal('生成成功');
                    } else {
                        $.inModal('生成失败,'+data.errmsg+':'+data.errcode);
                    }
                }
            });
        }else if (t == 'delmenu'){
            $.alert('正在删除...', 0);
            $.ajax({
                url: "{#$urlarr.2#}wxdel/{#get_get()#}",
                type: "get",
                dataType: "json",
                success: function (data) {
                    $.alert(0);
                    if (data != null && data.success != null && data.success) {
                        $.showModal('删除成功');
                    } else {
                        $.inModal('删除失败,'+data.errmsg+':'+data.errcode);
                    }
                }
            });
        }else if (t == 'synchronous'){
            $.alert('正在读取...', 0);
            $.ajax({
                url: "{#$urlarr.2#}wxsynchronous/{#get_get()#}",
                type: "get",
                dataType: "json",
                success: function (data) {
                    $.alert(0);
                    if (data != null && data.success != null && data.success) {
                        $.showModal('同步成功！', '{#get_url()#}');
                    } else {
                        $.inModal('失败: '+data.message);
                    }
                }
            });
        }
    }
    function keyhide(){
        $('table#menu-table tbody').each(function(){
            if ($(this).find('tr').length > 1){
                $(this).find('tr:eq(0)').find("#keytype").hide();
                $(this).find('tr:eq(0)').find("#keytext").hide();
            }else{
                $(this).find('tr:eq(0)').find("#keytype").show();
                //
                var ov = $(this).find('tr:eq(0)').find("#keytype").val();
                var ev = $(this).find('tr:eq(0)').find("#keytext");
                if (ov == 'click' || ov == 'view' || ov == 'scancode_waitmsg'){
                    ev.show();
                }else{
                    ev.hide();
                    if (!ev.val()) ev.val("0");
                }
            }
        });
        if ($('table#menu-table tbody').length > 1){
            $('tbody#menu-no').hide();
        }else{
            $('tbody#menu-no').show();
        }
    }
    $(document).ready(function() {
        $('#menuform').submit(function() {
            var _empty = true;
            $("#menu-table input#title,#menu-table input#keytext").each(function(){
                if($(this).is(':visible'))
                    _empty = $(this).inTips("不可留空",-1, _empty);
            });
            if (!_empty) return false;
            $.alert('正在保存...', 0);
            $(this).ajaxSubmit({
                dataType : 'json',
                success : function (data) {
                    if (data != null && data.success != null && data.success) {
                        $("#menuform").changeevent();
                        window.changeevent = false;
                        $.alertk(data.message);
                    } else {
                        $.alert(0);
                        $.showModal(data.message);
                    }
                },
                error : function () {
                    $.alert(0);
                    $.inModal("保存失败！");
                }
            });
            return false;
        });
        //初始化菜单数据
        var menus = {#$menus#};
        for(var i in menus)
        {
            window.menui++;
            var r = window.menui;
            var $m = $('<tbody>' + $('#menu-temp').html() + '</tbody>');
            $('table#menu-table').append($m);
            $m.find("#childlink").hide();
            $m.attr('data-menui', r);
            $m.find("input,select").each(function(){
                $(this).attr('name', 'menu_'+r+'_'+$(this).attr('id'))
            });
            $m.find("input#inorder").val(menus[i].inorder);
            $m.find("input#title").val(menus[i].title);
            $m.find("select#keytype").val(menus[i].keytype);
            $m.find("input#keytext").val(menus[i].keytext);
            if (!menus[i].status) $m.find("input#status").prop("checked",false);
            //
            var child = menus[i].child;
            if (child){
                for(var j in child)
                {
                    window.menui++;
                    var rj = window.menui;
                    var $mj = $($('#menu-temp').html());
                    $m.append($mj);
                    $mj.attr('data-rank','child');
                    //$mj.find("#inorder").hide();
                    $mj.find("#childlink").next().find("input").css({width:122});
                    $mj.find("#addlink").hide();
                    $mj.attr('data-menui', rj);
                    $mj.find("input,select").each(function(){
                        $(this).attr('name', 'menuchild_'+r+'_'+rj+'_'+$(this).attr('id'))
                    });
                    $mj.find("input#inorder").val(child[j].inorder);
                    $mj.find("input#title").val(child[j].title);
                    $mj.find("select#keytype").val(child[j].keytype);
                    $mj.find("input#keytext").val(child[j].keytext);
                    if (!child[j].status) $mj.find("input#status").prop("checked",false);
                    //
                    var ov = child[j].keytype;
                    var ev = $mj.find("input#keytext");
                    if (ov == 'click' || ov == 'view' || ov == 'scancode_waitmsg'){
                        ev.show();
                    }else{
                        ev.hide();
                        if (!ev.val()) ev.val("0");
                    }
                }
            }
        }
        keyhide();
        $("#menuform").changeevent();
    });
</script>

{#template("footer")#}

</body>
</html>