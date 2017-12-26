<!DOCTYPE html>
<html>
<head lang="zh-CN">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>会员注册 - {#$BASE_NAME#}</title>
    <link rel="stylesheet" href="{#$CSS_PATH#}normalize.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}global.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}alert.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}font-awesome.css"/>
    <script type="text/javascript" src="{#$JS_PATH#}jquery-1.11.0.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.alert.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.form.min.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}linkage.js"></script>
</head>
<body>
{#include file="header.tpl"#}
<script>$('#head-nav-menu>li:eq(0)').addClass('active');</script>

<div class="wrapper">
    <div class="main sign-up">
        <div class="hd">
            <p class="description">
                {#if $settingother.regtext#}
                    {#$settingother.regtext#}
                {#else#}
                    Hi，新朋友，欢迎注册{#$smarty.const.BRAND_NAME#}平台，一下各项均必填。如注册遇到困难，请联系我们 :)
                {#/if#}
            </p>
        </div>
        <div class="bd">
            <div class="section cf">
                <div class="left-mod">
                    <div class="sign-up-mod">
                        <form action="{#$urlarr.now#}" id="regform" method="post">
                            <table class="table table-form">
                                <tbody>
                                <tr>
                                    <td class="al-right"><span>用户名</span></td>
                                    <td><input class="form-control" type="text" name="username" id="username" value="" placeholder="注册后不可修改"></td>
                                </tr>
                                <tr>
                                    <td class="al-right"><span>密码</span></td>
                                    <td><input class="form-control" type="password" name="userpass" id="userpass" value=""></td>
                                </tr>
                                <tr>
                                    <td class="al-right"><span>确认密码</span></td>
                                    <td><input class="form-control" type="password" name="reuserpass" id="reuserpass" value=""></td>
                                </tr>
                                {#if $regitem.fullname=='fullname'#}
                                <tr>
                                    <td class="al-right"><span>姓名</span></td>
                                    <td><input class="form-control" type="text" name="fullname" id="fullname" value=""></td>
                                </tr>
                                {#/if#}
                                {#if $regitem.phone=='phone'#}
                                <tr>
                                    <td class="al-right"><span>手机</span></td>
                                    <td><input class="form-control" type="text" name="phone" id="phone" value=""></td>
                                </tr>
                                {#/if#}
                                {#if $regitem.email=='email'#}
                                <tr>
                                    <td class="al-right"><span>邮箱</span></td>
                                    <td><input class="form-control" type="text" name="email" id="email" value=""></td>
                                </tr>
                                {#/if#}
                                {#if $regitem.qqnum=='qqnum'#}
                                <tr>
                                    <td class="al-right"><span>QQ</span></td>
                                    <td><input class="form-control" type="text" name="qqnum" id="qqnum" value=""></td>
                                </tr>
                                {#/if#}
                                {#if $regitem.companyname=='companyname'#}
                                <tr>
                                    <td class="al-right"><span>公司名称</span></td>
                                    <td><input class="form-control" type="text" name="companyname" id="companyname" value=""></td>
                                </tr>
                                {#/if#}
                                {#if $regitem.tel=='tel'#}
                                <tr>
                                    <td class="al-right"><span>电话</span></td>
                                    <td><input class="form-control" type="text" name="tel" id="tel" value=""></td>
                                </tr>
                                {#/if#}
                                {#if $regitem.linkaddr=='linkaddr'#}
                                <tr>
                                    <td class="al-right"><span>地区</span></td>
                                    <td class="form-reg">
                                        <input class="form-control" type="text" name="linkaddr" id="linkaddr" value="">
                                    </td>
                                </tr>
                                {#/if#}
                                {#if $regitem.address=='address'#}
                                <tr>
                                    <td class="al-right"><span>详细地址</span></td>
                                    <td><input class="form-control" type="text" name="address" id="address" value=""></td>
                                </tr>
                                {#/if#}
                                <tr>
                                    <td></td>
                                    <td><a class="normal-link" href="javascript:;" id="regagreement">查看注册服务用户协议</a></td>
                                    <td>
                                        <div id="regagreementhtml" style="display:none;">{#$settingother.regagreement#}</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>
                                        <div class="control-group" id="sumForm">
                                            {#if $smarty.const.OFF_REG_IS#}
                                                <div style="color:#f00;padding-bottom:10px;">尚未开发注册功能！</div>
                                            {#/if#}
                                            <input style="width: 100%;" class="button button-primary button-metro" type="submit" value="接受协议并注册"/>
                                            <input type="hidden" name="dosubmit" value="1"/>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </form>
                    </div>
                </div>
                <div class="right-mod">
                    <div class="sign-in-mod">
                        <h3 class="title">已注册请登录</h3>
                        <form action="{#$urlarr.2#}login/" method="post" id="loginForm">
                            <div class="form-group">
                                <input class="form-control" type="text" name="username" id="username" placeholder="用户名"/>
                            </div>
                            <div class="form-group">
                                <input class="form-control" type="password" name="userpass" id="userpass" placeholder="密码"/>
                            </div>
                            <div class="form-group">
                                <input id="subForm" type="submit" class="button button-primary button-metro" value="登录"/>
                                <input type="hidden" name="dosubmit" value="1"/>
                                <a class="normal-link" href="tencent://message/?uin={#$smarty.const.DIY_LINKQQ_PATH#}&Menu=yes">忘记密码？</a>
                            </div>
                            <div class="form-group cf">
                                <label>
                                    <input type="checkbox">
                                    记住账号</label>
                                <label>
                                    <input type="checkbox">
                                    下次自动登录</label>
                            </div>
                        </form>
                        <div class="ad">
                            {#if $settingother.regimg#}
                                <img width="344" src="{#$settingother.regimg|fillurl#}" alt="">
                            {#/if#}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="{#$JS_PATH#}artdialog/artDialog.js?skin=default"></script>

<script type="text/javascript">
    $(document).ready(function() {
        $("#regagreement").click(function(){
            art.dialog({
                title: '用户协议',
                fixed: true,
                lock: true,
                opacity: '.3',
                content: $("#regagreementhtml").html(),
                button: [{
                    name: '确定',
                    focus: true,
                    callback: function () {
                        return true;
                    }
                }]
            });
        });
        $('#regform').submit(function() {
            var retu = true;
            retu = $('#username').inTips("用户名最少3个字符", 3, retu);
            //retu = $('#username').inTips("用户名格式错误", -5, retu);
            retu = $('#userpass').inTips("密码最少6个字符", 6, retu);
            retu = $('#reuserpass').inTips("两次密码输入不一致", $('#userpass'), retu);
            {#if $regitem.fullname=='fullname'#}retu = $('#fullname').inTips("", 2, retu);{#/if#}
            {#if $regitem.phone=='phone'#}retu = $('#phone').inTips("", -2, retu);{#/if#}
            {#if $regitem.email=='email'#}retu = $('#email').inTips("", -4, retu);{#/if#}
            {#if $regitem.qqnum=='qqnum'#}retu = $('#qqnum').inTips("", -1, retu);{#/if#}
            {#if $regitem.companyname=='companyname'#}retu = $('#companyname').inTips("", -1, retu);{#/if#}
            {#if $regitem.tel=='tel'#}retu = $('#tel').inTips("", -3, retu);{#/if#}
            {#if $regitem.linkaddr=='linkaddr'#}retu = $('#linkaddr').inTips("请选择地区", -1, retu, 0, $('#__linkage'));{#/if#}
            {#if $regitem.address=='address'#}retu = $('#address').inTips("", -1, retu);{#/if#}
            if (!retu) return false;

            $.alert('正在保存...', 0);
            $(this).ajaxSubmit({
                dataType : 'json',
                success : function (data) {
                    $.alert(0);
                    if (data != null && data.success != null && data.success) {
                        $.showModal(data.message, '{#$urlarr.2#}');
                    } else {
                        $.showModal(data.message);
                    }
                },
                error : function () {
                    $.alert(0);
                    $.inModal("提交失败！");
                }
            });
            return false;
        });
        $('#loginForm').submit(function() {

            var retu = true;
            retu = $('#loginForm #username').inTips("请输入用户名/邮箱/手机号码", -1, retu);
            retu = $('#loginForm #userpass').inTips("请输入密码", -1, retu);
            if (!retu) return false;

            $.alert('正在登录...', 0);
            $(this).ajaxSubmit({
                dataType : 'json',
                success : function (data) {
                    $.alert(0);
                    if (data != null && data.success != null && data.success) {
                        window.location.href = '{#$urlarr.2#}';
                    } else {
                        $.showModal(data.message);
                    }
                },
                error : function () {
                    $.alert(0);
                    $.inModal("登录失败！");
                }
            });
            return false;
        });
    });
    linkage("linkaddr","{#$urlarr.index#}web/system/linkage/",0,0);
</script>

{#include file="footer.tpl"#}
</body>
</html>