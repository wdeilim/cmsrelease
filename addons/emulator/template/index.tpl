
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
    <link rel="stylesheet" href="{#$NOW_PATH#}css/style.css"/>
    <script type="text/javascript" src="{#$JS_PATH#}jquery-1.11.0.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.alert.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.form.min.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}aliedit/jquery.aliedit.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}artdialog/artDialog.js?skin=default"></script>
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


    <div class="main cf custom-menu">
        <div class="mod">
            <div class="main-bd" id="tabmenu">
                <div class="tabmenu form-services">
                    <table class="table table-primary">
                        <thead>
                        <tr>
                            <th>模拟测试</th>
                            <th style="width:320px;">预览效果</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <div class="control-top clearfix">
                                    <div class="form form-inline">
                                        <div class="form-group">
                                            <button class="button button-primary" onclick="_send();">发送</button>
                                        </div>
                                    </div>
                                </div>
                                <table class="table table-primary emulator">
                                    <tbody>
                                    <tr>
                                        <td class="tit">服务项目</td>
                                        <td>
                                            <select class="form-control" id="accid" onchange="_select()">
                                                {#foreach from=$allist item=list name=foo#}
                                                    {#if $list.wx_appid#}
                                                        {#if $list.wx_level == 7#}
                                                            <option value="{#$list.id#}" data-type="weixin" data-level="7">ID{#$list.id#}_{#$list.wx_name#} (企业号)</option>
                                                        {#else#}
                                                            <option value="{#$list.id#}" data-type="weixin">ID{#$list.id#}_{#$list.wx_name#} (公众号)</option>
                                                        {#/if#}
                                                    {#/if#}
                                                    {#if $list.al_appid#}
                                                        <option value="{#$list.id#}" data-type="alipay">ID{#$list.id#}_{#$list.al_name#} (服务窗)</option>
                                                    {#/if#}
                                                    {#if !$smarty.foreach.foo.last#}
                                                        <option value=""  disabled="">----------</option>
                                                    {#/if#}
                                                    {#foreachelse#}
                                                    <option value="0">==没有添加==</option>
                                                {#/foreach#}
                                            </select>
                                        </td>
                                    </tr>
                                    <tr style="display:none;" id="agentid-list">
                                        <td class="tit">选择应用</td>
                                        <td>
                                            {#foreach from=$allist item=list#}
                                                {#if $list.wx_appid#}
                                                    {#if $list.wx_level == 7#}
                                                        <select class="form-control" id="agentid-{#$list.id#}" style="display:none;" onchange="_selecta()">>
                                                            {#foreach from=string2array($list.wx_corp) item=list2#}
                                                                {#if $list2.type == 1#}
                                                                    <option value="{#$list2.agentid#}">{#$list2.name#}</option>
                                                                {#/if#}
                                                                {#foreachelse#}
                                                                <option value="0">==没有应用==</option>
                                                            {#/foreach#}
                                                        </select>
                                                    {#/if#}
                                                {#/if#}
                                            {#/foreach#}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="tit">消息类型</td>
                                        <td>
                                            <div class="radio-inline">
                                                <label><input type="radio" name="type" value="text" onclick="toggle('text')"  checked="checked">&nbsp;文本</label>
                                            </div>
                                            <div class="radio-inline">
                                                <label><input type="radio" name="type" value="menu" onclick="toggle('menu')">&nbsp;菜单</label>
                                            </div>
                                            <div class="radio-inline disabled">
                                                <label><input type="radio" disabled="disabled" name="type" value="image" onclick="toggle('image')" d>&nbsp;图片</label>
                                            </div>
                                            <div class="radio-inline">
                                                <label><input type="radio" name="type" value="subscribe" onclick="toggle('subscribe')">&nbsp;模拟关注</label>
                                            </div>
                                            <div class="radio-inline">
                                                <label><input type="radio" name="type" value="unsubscribe" onclick="toggle('unsubscribe')">&nbsp;取消关注</label>
                                            </div>
                                            <div class="radio-inline disabled">
                                                <label><input type="radio" disabled="disabled" name="type" value="over" onclick="toggle('over')">&nbsp;余额改变</label>
                                            </div>
                                            <div class="radio-inline disabled">
                                                <label><input type="radio" disabled="disabled" name="type" value="integral" onclick="toggle('integral')">&nbsp;积分改变</label>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="tit">发送用户</td>
                                        <td><input class="form-control" id="user" type="text" value="emulatorUser" placeholder="填写OPENID或会员卡号"/></td>
                                    </tr>
                                    <tr id="trcontent">
                                        <td class="tit">发送内容</td>
                                        <td id="tdcontent">
                                            <textarea class="form-control" id="content" placeholder="填写：关键词、变化积分、变化余额；积分余额填写负数代表扣除。">{#$_GPC['key']#}</textarea>
                                            <span style="display:none">
                                                <input type="hidden" id="menukey" value="">
                                                <input type="hidden" id="menuname" value="">
                                                <input type="hidden" id="menutype" value="">
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="tit">接收消息</td>
                                        <td><pre id="message"></pre></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </td>
                            <td valign="top"><div id="preview" class="preview"></div></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function toggle(t) {
        if (t == 'subscribe' || t == 'unsubscribe') {
            $("#trcontent").hide();
        }else{
            $("#trcontent").show();
        }
        var tdcontent = $("#tdcontent");
        if (t == 'menu') {
            var accid = $("#accid").val();
            var agentid = $("#agentid-" + accid).val();
            var datatype = $("#accid").find("option:selected").attr("data-type");
            tdcontent.find("textarea").hide();
            if (tdcontent.find('#menu_'+accid+agentid+datatype).length > 0) {
                tdcontent.find('#menu_'+accid+agentid+datatype).show();
            }else{
                tdcontent.append('<div class="menu" id="menu_'+accid+agentid+datatype+'">Loading...</div>');
                $.ajax({
                    url: "{#weburl('emulator/menu')#}",
                    type: "POST",
                    data: "type="+datatype+"&id="+accid+"&agentid="+agentid,
                    dataType: "html",
                    success: function (data) {
                        tdcontent.find('#menu_'+accid+agentid+datatype).html(data);
                    },
                    error: function(data) {
                        tdcontent.find('#menu_'+accid+agentid+datatype).text("读取菜单失败");
                    }
                });
            }
            $("#trcontent").find('.tit').text("选择菜单");
        }else{
            tdcontent.find("textarea").show();
            tdcontent.find(">div").hide();
            $("#trcontent").find('.tit').text("发送内容");
        }
    }
    function _select() {
        $("div.radio-inline").eq(0).find("input").click();
        //
        var uobj = $("#user");
        var idtype = $("#accid").find("option:selected").attr("data-type");
        var idlevel = $("#accid").find("option:selected").attr("data-level");
        if (idtype == "weixin" && uobj.val() == "emulatorUser") {
            uobj.val("wxemulatorUser");
        }
        if (idtype == "alipay" && uobj.val() == "wxemulatorUser") {
            uobj.val("emulatorUser");
        }
        var agentid_list = $("#agentid-list");
        if (idlevel == 7) {
            agentid_list.find("select").hide();
            agentid_list.find("#agentid-" + $("#accid").val()).show();
            agentid_list.show();
        }else{
            agentid_list.hide();
        }
    }
    function _selecta() {
        $("div.radio-inline").eq(0).find("input").click();
    }
    function _selectmenu(obj) {
        var tdcontent = $("#tdcontent");
        tdcontent.find("#menukey").val($(obj).val());
        tdcontent.find("#menuname").val($(obj).find("option:selected").text());
        tdcontent.find("#menutype").val($(obj).find("option:selected").attr("data-type"));
    }
    function _send() {
        var favorite = document.getElementsByName("type");
        var myFavorite;
        for (var i=0; i < favorite.length; i++) {
            if (favorite.item(i).checked) {
                myFavorite = favorite.item(i).getAttribute("value");
                break;
            }
        }
        var s = "id="+$("#accid").val();
        s+= "&agentid="+$("#agentid-" + $("#accid").val()).val();
        s+= "&type="+myFavorite;
        s+= "&ftype="+$("#accid").find("option:selected").attr("data-type");
        s+= "&user="+encodeURIComponent($("#user").val());
        s+= "&menukey="+encodeURIComponent($("#menukey").val());
        s+= "&menuname="+encodeURIComponent($("#menuname").val());
        s+= "&menutype="+encodeURIComponent($("#menutype").val());
        s+= "&content="+encodeURIComponent($("#content").val());
        $.alert("正在发送...", 0, 1);
        $.ajax({
            url: "{#weburl('emulator/send')#}",
            type: "POST",
            data: s,
            dataType: "json",
            success: function (data) {
                $.alert(0);
                if (data != null && data.success != null && data.success) {
                    if (data.rethtml) {
                        $("#message").html(data.message);
                    }else{
                        $("#message").text(data.message);
                    }
                    $("#preview").html(data.preview);
                } else {
                    $.inModal('失败: '+data.message);
                }
            },
            error: function(data) {
                $.alertk("发送错误，请稍后再试...");
            }
        });
    }
    $(document).ready(function() {
        {#if $_GPC['al']#}
        $("#accid").val('{#$_GPC['al']#}').change();
        {#/if#}
        //初始化TAB
        $("#topmenu a").each(function(index){
            $(this).attr("d-index", index);
            $(this).click(function(){
                $("#topmenu a").removeClass("active");
                $(this).addClass("active");
                $("#tabmenu").children("div").hide().eq($(this).attr("d-index")).show();
            });
        });
        $("#topmenu a:eq(0)").click();
        _select();
    });
</script>

{#template("footer")#}

</body>
</html>