
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
        .topmenu{position:relative;width:1126px;height:36px;margin:15px auto 8px;padding-left:18px;background-color:#fff;border-bottom:3px solid #09c}
        .topmenu a{display:block;float:left;line-height:36px;padding:0 12px;margin-right:12px;color:#09c}
        .topmenu a.active,.topmenu a:hover{-webkit-border-radius:3px 3px 0 0;-moz-border-radius:3px 3px 0 0;border-radius:3px 3px 0 0;color:#fff;font-weight:bold;background-color:#09c}
        .topmenu div{display:none;border:1px solid #09c;border-top:3px solid #09c;background-color:#fff;position:absolute;top:36px;left:0;z-index:9999}
        .topmenu div a{float:none;margin:0}
        .topmenu div a.active,.topmenu div a:hover{-webkit-border-radius:0;-moz-border-radius:0;border-radius:0}
        .tabmenu{display:none}
        .backing{margin-left:10px}
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
                <option value="out">发送信息 （事件型菜单）</option>
                <option value="link">跳转 URL （链接型菜单）</option>
                <option value="tel">点击拨打电话</option>
                <option value="in" style="display:none">发送广播 （来自同步）</option>
                <option value="in2" style="display:none">查看广播正文 （来自同步）</option>
                <option value="alipay">查看地图</option>
                <option value="alipay2">消费记录</option>
                <option value="alipay3">转账</option>
            </select>
        </td>
        <td class="al-center"><div class="form-control possel"><input type="text" id="keytext" onmousemove="keytextmove(this);" onmouseout="keytextmove(this,1);" onkeyup="keytextkeyup(this);" onkeydown="keytextkeyup(this);"></div></td>
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
            <span>{#$_A.al.al_name#}</span>
        </div>
    </div>

    <div class="main cf custom-menu">
        {#if $_A.al.wx_appid#}
        <div class="topmenu" id="topmenu">
            <a href="{#weburl("menu/weixin")#}">微信公众号</a>
            <a href="javascript:;" class="active">支付宝服务窗</a>
        </div>
        {#/if#}
        <div class="mod">
            <div class="main-bd">
                <div class="tip mod mod-rounded mod-bordered">
                    <p>简要说明：</p>
                    <p>1. 菜单创建后，在支付宝钱包客户端是实时生效的；</p>
                    <p>2. 删除原有菜单后，建议开发者至少保留该删除菜单的服务七天以上；</p>
                    <p>3. 最多设置4个一级菜单，每个一级菜单最多设置5个二级菜单，当设置4个一级菜单时，左侧的发送消息按钮将被隐藏；</p>
                    <p>4. 一级菜单最多显示4个汉字，二级菜单最多显示12个汉字。</p>
                    <p>特殊触发关键词：类型选“发送信息”、事件填写“#+内容”直接回复内容信息，例如填写“#您好”用户点击此菜单时则直接返回“您好”。</p>
                </div>

                <div id="typemsg" style="display: none;">
                    <p><strong>1、发送信息</strong></p>
                    <p>用户点击之后支付宝网关向开发者网关发送事件消息，开发者可以通过自定义参数匹配的方式实现业务逻辑；</p>
                    <p><strong>2、跳转URL</strong></p>
                    <p>用户点击之后跳转到一个Web页面或者唤起一个本地服务（例如拨通电话），不向开发者网关发送消息。</p>
                    <p><strong>3、点击拨打电话</strong></p>
                    <p>用户点击之后直接拨打电话。</p>
                    <p><strong>4、查看地图</strong></p>
                    <p>用户点击该菜单后，将查看附近以下关键字的地理位置。</p>
                    <p><strong>5、消费记录</strong></p>
                    <p>用户点击该菜单后，将进入消费记录页面，查看与该服务窗之间发生的交易明细。</p>
                    <p><strong>6、转账</strong></p>
                    <p>用户点击该菜单后，可向以下支付宝账户（需已签约<a href="https://b.alipay.com/order/productDetail.htm?productId=2014110308141993" target="_blank">快捷支付(无线)</a>或<a href="https://b.alipay.com/order/productDetail.htm?productId=2014110308142133" target="_blank">手机网站支付</a>）转账。</p>
                </div>

                <div class="control-group">
                    <a class="button button-primary button-rounded" href="javascript:;" onclick="inwin(this,'addt');">+添加主菜单</a> &nbsp;
                    <a class="button button-primary button-rounded" href="javascript:;" onclick="inwin(this,'typemsg');">查看类型说明</a>
                </div>

                <form action="{#$urlarr.2#}save/{#get_get()#}"  method="post" id="menuform">
                    <table style="margin-bottom: 20px;" class="table table-primary" id="menu-table">
                        <thead>
                            <tr>
                                <th style="width: 181px;">显示顺序</th>
                                <th style="width: 210px;">主菜单名称</th>
                                <th>类型</th>
                                <th>事件、链接、电话</th>
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

                    <div class="control-group">
                        <input class="button button-primary button-rounded" style="height: 34px;" type="submit" value="保存"/>
                        <input type="hidden" name="dosubmit" value="1"/>
                        <a class="button button-primary button-rounded" href="javascript:;" onclick="inwin(this,'bemenu');">生成自定义菜单</a>
                        <a class="button button-primary button-rounded" href="javascript:;" onclick="inwin(this,'synchronous');">同步菜单结构</a>
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
            var vv = ev.attr("data-"+ov+"-val");
            ev.val(vv?vv:'');
            if (ov == 'out' || ov == 'link' || ov == 'tel' || ov == 'alipay' || ov == 'alipay3'){
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
            if ($('table#menu-table tbody').length > 4){
                $.inModal('1级菜单最多只能开启4个');
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
                            focus: true,
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
                url: "{#$urlarr.2#}savemenu/{#get_get()#}",
                type: "get",
                dataType: "json",
                success: function (data) {
                    $.alert(0);
                    if (data != null && data.success != null && data.success) {
                        $.showModal('生成成功');
                    } else {
                        $.inModal('失败: '+data.message);
                    }
                }
            });
        }else if (t == 'synchronous'){
            $.alert('正在读取...', 0);
            $.ajax({
                url: "{#$urlarr.2#}synchronous/{#get_get()#}",
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
                if (ov == 'out' || ov == 'link' || ov == 'tel' || ov == 'alipay' || ov == 'alipay3'){
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
            if (menus[i].keytype) {
                $m.find("select#keytype").find("option[value='"+menus[i].keytype+"']").show();
            }
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
                    if (child[j].keytype) {
                        $mj.find("select#keytype").find("option[value='"+child[j].keytype+"']").show();
                    }
                    $mj.find("input#keytext").val(child[j].keytext).attr("data-"+child[j].keytype+"-val", child[j].keytext);
                    if (!child[j].status) $mj.find("input#status").prop("checked",false);
                    //
                    var ov = child[j].keytype;
                    var ev = $mj.find("input#keytext");
                    if (ov == 'out' || ov == 'link' || ov == 'tel' || ov == 'alipay' || ov == 'alipay3'){
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