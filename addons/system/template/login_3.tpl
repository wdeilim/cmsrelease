
<!DOCTYPE html>
<html>
<head lang="zh-CN">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{#if $tem['title']#}{#$tem['title']#}{#else#}{#$BASE_NAME#}{#/if#}</title>
    <meta name="keywords" content="{#$tem['keywords']#}" />
    <meta name="description" content="{#$tem['description']#}" />
    <link rel="stylesheet" href="{#$CSS_PATH#}normalize.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}global.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}alert.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}font-awesome.css"/>
    <link rel="stylesheet" href="{#$NOW_PATH#}css/login_3/common.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}iealert.css"/>
    <script type="text/javascript" src="{#$JS_PATH#}jquery-1.11.0.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.alert.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.form.min.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}bootstrap.min.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.browser.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}iealert.js"></script>
    <script type="text/javaScript">$(document).ready(function() { $("body").iealert(); });</script>
</head>
<body>
{#include file="header.tpl"#}
<script>$('#head-nav-menu>li:eq(0)').addClass('active');</script>

<div class="wrapper-sign-in slide">
    <div id="banner" class="carousel slide" data-ride="carousel">
        <div class="carousel-inner" role="listbox">
            {#foreach from=$tem['t3']['loginbg'] name=foo item=bg#}
                <div class="item{#if $smarty.foreach.foo.first#} active{#/if#}" style="background-image:url({#fillurl($bg)#});"></div>
            {#/foreach#}
        </div>
    </div>

    <div class="main-sigh-in cf">
        <div class="main-sigh-title">{#$tem['t3']['title']#}</div>
        <div class="main-sigh-subtitle">{#$tem['t3']['subtitle']#}</div>
        <div class="sign-in-mod">
            <div class="title">
                <span>账户登录</span>
                {#if !$smarty.const.OFF_REG_IS#}
                    <span>|</span>
                    <a class="normal-link" href="{#$urlarr.2#}reg/">注册账户</a>
                {#/if#}
            </div>

            <form action="{#$urlarr.now#}" method="post" id="loginForm">
                <div class="form-item">
                    <div class="form-group">
                        <input class="form-control" type="text" name="username" id="username" placeholder="用户名"/>
                    </div>
                </div>
                <div class="form-item">
                    <div class="form-group">
                        <input class="form-control" type="password" name="userpass" id="userpass" placeholder="密码"/>
                    </div>
                </div>
                <div class="form-item">
                    <input id="subForm" type="submit" class="button button-primary button-metro" value="登录"/>
                    <input type="hidden" name="dosubmit" value="1"/>
                    <a class="normal-link" href="tencent://message/?uin={#$smarty.const.DIY_LINKQQ_PATH#}&Menu=yes">忘记密码？</a>
                </div>
                <div class="form-item remember">
                    <label><input type="checkbox" id="remember" name="remember"/>记住账号</label>
                    <label><input type="checkbox" id="autoLogin" name="autoLogin"/>下次自动登录</label>
                </div>
            </form>

            <div class="qrcode cf">
                {#if $settingother.logincode#}
                    <img class="image" width="90" src="{#$settingother.logincode|fillurl#}" alt=""/>
                {#else#}
                    <img class="image" width="90" height="90" src="{#$IMG_PATH#}qrcode.png" alt=""/>
                {#/if#}
                <div class="description">
                    {#if $settingother.logintext#}
                        {#$settingother.logintext#}
                    {#else#}
                        <p>扫描二维码，关注 <a class="normal-link" href="{#$smarty.const.BRAND_URL#}" target="_blank">{#$smarty.const.BRAND_NAME#}</a></p>
                        <p>量身定制，随需而变优质产品助您绘制移动互联网时代蓝图</p>
                    {#/if#}
                </div>
            </div>
        </div>
    </div>
</div>

{#include file="footer.tpl"#}

<script type="text/javascript">
    $(document).ready(function() {
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
</script>

</body>
</html>