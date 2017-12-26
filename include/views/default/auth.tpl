
<!DOCTYPE html>
<html>
<head lang="zh-CN">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta content="telephone=no" name="format-detection">
    <title>{#$_A.al.al_name#}</title>
    <link type="text/css" rel="stylesheet" href="{#$NOW_PATH#}css/auth.css" />
    <script type="text/javascript" src="{#$JS_PATH#}jquery.min.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.alert.js"></script>
</head>

<body>

<div id="login" class="main-auth hide">
    <div class="tit">确认身份</div>
    <div class="con">
        <div class="tab" id="tablogin">
            {#if (($_A['al']['wx_appid'] && $_A['al']['wx_level'] == 4) || $_temp_isget) && $_A['browser'] == 'none'#}
                <a href="javascript:void(0);" onclick="login('weixin')">微信登录</a>
            {#/if#}
            {#if $_A['al']['al_appid']#}
                <a href="javascript:void(0);" onclick="login('alipay')">支付宝登录</a>
            {#/if#}
            <a href="javascript:void(0);" class="hov">账号登录</a>
            <a href="javascript:void(0);" onclick="show('login2')">验证码登录</a>
        </div>
        <div class="inp">
            <div class="put">
                <input type="text" name="username" placeholder="手机号/邮箱">
            </div>
            <div class="put">
                <input type="password" name="userpass" placeholder="密码">
            </div>
            <div class="but">
                <button type="button" class="btn">登录</button>
            </div>
        </div>
        <div class="link">
            <a href="javascript:void(0);" onclick="show('reg')" class="l-l">免费注册</a>
            <a href="javascript:void(0);" onclick="show('pass')" class="l-r">找回密码</a>
        </div>
    </div>
</div>

<div id="login2" class="main-auth hide">
    <div class="tit">确认身份</div>
    <div class="con">
        <div class="tab" id="tablogin">
            {#if (($_A['al']['wx_appid'] && $_A['al']['wx_level'] == 4) || $_temp_isget) && $_A['browser'] == 'none'#}
                <a href="javascript:void(0);" onclick="login('weixin')">微信登录</a>
            {#/if#}
            {#if $_A['al']['al_appid']#}
                <a href="javascript:void(0);" onclick="login('alipay')">支付宝登录</a>
            {#/if#}
            <a href="javascript:void(0);" onclick="show('login')">账号登录</a>
            <a href="javascript:void(0);" class="hov">验证码登录</a>
        </div>
        <div class="inp">
            <div class="put">
                <input type="text" name="username" placeholder="手机号/邮箱">
            </div>
            <div class="but" style="margin-bottom:10px;">
                <div id="codebtn" class="btn btn-warning">获取验证码</div>
            </div>
            <div class="put">
                <input type="text" id="usercode_login" name="usercode" placeholder="验证码">
            </div>
            <div class="but">
                <button type="button" class="btn">登录</button>
            </div>
        </div>
        <div class="link">
            <a href="javascript:void(0);" onclick="show('reg')" class="l-l">免费注册</a>
            <a href="javascript:void(0);" onclick="show('pass')" class="l-r">找回密码</a>
        </div>
    </div>
</div>

<div id="weixin" class="main-auth hide">
    <div class="tit">确认身份</div>
    <div class="con">
        <div class="tab" id="tablogin">
            {#if (($_A['al']['wx_appid'] && $_A['al']['wx_level'] == 4) || $_temp_isget) && $_A['browser'] == 'none'#}
                <a href="javascript:void(0);" class="hov">微信登录</a>
            {#/if#}
            {#if $_A['al']['al_appid']#}
                <a href="javascript:void(0);" onclick="login('alipay')">支付宝登录</a>
            {#/if#}
            <a href="javascript:void(0);" onclick="show('login')">账号登录</a>
            <a href="javascript:void(0);" onclick="show('login2')">验证码登录</a>
        </div>
        <div class="inp weixincode">
            <div class="codeimg"></div>
            <div class="codetis">请使用微信扫描二维码登录</div>
            <div class="codetis codeims"><span>扫描成功</span><em>请在微信中点击确认即可登录</em></div>
        </div>
    </div>
</div>

<div id="reg" class="main-auth hide">
    <div class="tit">注册用户</div>
    <div class="con">
        <div class="tab">
            <a href="javascript:void(0);" class="hov">注册用户</a>
        </div>
        <div class="inp">
            <div class="put">
                <input type="text" name="username" placeholder="手机号/邮箱">
            </div>
            <div class="put">
                <input type="password" name="userpass" placeholder="密码">
            </div>
            <div class="put">
                <input type="password" name="userpass2" placeholder="确认密码">
            </div>

            <div class="but">
                <button type="button" class="btn">立即注册</button>
            </div>
        </div>
        <div class="link">
            <a href="javascript:void(0);" onclick="show('login')" class="l-l">用户登录</a>
            <a href="javascript:void(0);" onclick="show('pass')" class="l-r">找回密码</a>
        </div>
    </div>
</div>

<div id="pass" class="main-auth hide">
    <div class="tit">找回密码</div>
    <div class="con">
        <div class="tab">
            <a href="javascript:void(0);" class="hov">找回密码</a>
        </div>
        <div class="inp">
            <div class="put">
                <input type="text" name="username" placeholder="手机号/邮箱">
            </div>

            <div class="but">
                <div id="passbtn" class="btn btn-warning">获取验证码</div>
            </div>
        </div>
        <div class="link">
            <a href="javascript:void(0);" onclick="show('reg')" class="l-l">免费注册</a>
            <a href="javascript:void(0);" onclick="show('login')" class="l-r">立即登录</a>
        </div>
    </div>
</div>

<div id="pass2" class="main-auth hide">
    <div class="tit">找回密码</div>
    <div class="con">
        <div class="tab">
            <a href="javascript:void(0);" class="hov">重置密码</a>
        </div>
        <div class="inp">
            <div class="put">
                <input type="text" name="username" value="" readonly="readonly" style="background-color:#eee;">
            </div>
            <div class="put">
                <input type="text" id="usercode_pass" name="usercode" placeholder="验证码">
            </div>
            <div class="put">
                <input type="password" name="userpass" placeholder="新密码">
            </div>
            <div class="put">
                <input type="password" name="userpass2" placeholder="确认新密码">
            </div>
            <div class="but">
                <button type="button" class="btn">立即重置</button>
            </div>
        </div>
        <div class="link">
            <a href="javascript:void(0);" onclick="show('reg')" class="l-l">免费注册</a>
            <a href="javascript:void(0);" onclick="show('login')" class="l-r">立即登录</a>
        </div>
    </div>
</div>


<script type="text/javascript">
    window.tcodetimename = 0;
    window.tcodetimenameq = 0;
    function tcode(t) {
        if (!$("#weixin").is(":visible")) { return; }
        if (t === 0) { $.alert(0); return; }
        $.alert("二维码加载中...", 0);
        var src = '{#get_link('authsend|authtype|tv')#}&authsend=1&authtype=weixincode&tv='+new Date().getTime();
        var codeimg = $(".weixincode").find(".codeimg");
        if (codeimg.find("img").length > 0) {
            codeimg.find("img").attr("src", src);
        }else{
            codeimg.html('<img src="'+src+'" onload="tcode(0)">');
        }
        $(".codeims").hide();
        $(".codetis").eq(0).show();
    }
    function tcodeq() {
        if (!$("#weixin").is(":visible")) { return; }
        $.ajax({
            type: "POST",
            url: '{#appurl('system/weixinauthq')#}',
            dataType: 'text',
            success: function (data) {
                if (data == '1') {
                    $(".codetis").hide();
                    $(".codeims").show();
                    $(".codeims").find("span").text("扫描成功");
                    $(".codeims").find("em").text("请在微信中点击确认即可登录");
                }else if (data == '2') {
                    if (window.tcodetimenameq !== 0) { clearInterval(window.tcodetimenameq); }
                    $(".codeims").find("span").text("登录成功");
                    $(".codeims").find("em").text("正在跳转，请稍后...");
                    window.location.reload();
                }
            },
            cache: false
        });
    }
    function login(type) {
        if (type == 'alipay') {
            window.location.href = 'https://openauth.alipay.com/oauth2/publicAppAuthorize.htm?app_id={#$_A['al']['al_appid']#}&scope=auth_userinfo&redirect_uri=' + encodeURIComponent('{#get_link('authsend|authtype')#}&authsend=1&authtype=alipay');
        }else if (type == 'weixin') {
            show('weixin');
            if (window.tcodetimename !== 0) { clearInterval(window.tcodetimename); }
            tcode();
            window.tcodetimename = setInterval("tcode()", 180 * 1000);
            if (window.tcodetimenameq === 0) { window.tcodetimenameq = setInterval("tcodeq()", 2 * 1000); }
        }
    }
    function show(name) {
        $(".hide").hide();
        $("#"+name).show();
    }
    function codenum(num) {
        var tthis = $("#codebtn");
        if (tthis.attr("data-click") == '1') {
            num--;
            tthis.text("重获验证码("+num+"秒)");
            if (num <= 1) {
                tthis.attr("data-click", 0);
            }
            setTimeout(function(){
                codenum(num);
            }, 1000);
        }else{
            tthis.removeClass("btn-disabled");
            tthis.text("获取验证码");
        }
    }
    $(function(){
        $("#login").find("#tablogin").find("a:eq(0)").click();
        //
        $("#codebtn").click(function(){
            var tthis = $(this);
            if (tthis.attr("data-click") == '1') return false;
            var tthisp = tthis.parents(".hide");
            var phone = tthisp.find(".inp").find("input[name='username']");
            var s = 'authsend=1&authtype='+tthisp.attr("id")+'&method=code';
            if (!phone.val()) {
                $.alertk(phone.attr("placeholder")+"不能为空");
                phone.focus();
                return false;
            }
            s+= "&" + phone.attr("name") + "=" + phone.val();
            $.ajax({
                type: "POST",
                url: '{#get_url()#}',
                data: s,
                dataType: 'json',
                success: function (data) {
                    $.alert(data.message);
                    if (data != null && data.success != null && data.success) {
                        $("#usercode_login").attr("placeholder", data.message);
                        tthis.attr("data-click", 1);
                        tthis.addClass("btn-disabled");
                        codenum(60);
                    }
                },error : function () {
                    $.alert("系统繁忙！");
                },
                cache: false
            });
        });
        $("#passbtn").click(function(){
            var tthis = $(this);
            var tthisp = tthis.parents(".hide");
            var phone = tthisp.find(".inp").find("input[name='username']");
            var s = 'authsend=1&authtype='+tthisp.attr("id")+'&method=code';
            if (!phone.val()) {
                $.alertk(phone.attr("placeholder")+"不能为空");
                phone.focus();
                return false;
            }
            s+= "&" + phone.attr("name") + "=" + phone.val();
            $.ajax({
                type: "POST",
                url: '{#get_url()#}',
                data: s,
                dataType: 'json',
                success: function (data) {
                    $.alert(data.message);
                    if (data != null && data.success != null && data.success) {
                        $("#usercode_pass").attr("placeholder", data.message);
                        $("#pass2").find("input[name='username']").val(phone.val());
                        show('pass2');
                    }
                },error : function () {
                    $.alert("系统繁忙！");
                },
                cache: false
            });
        });
        $("button").click(function(){
            var tthis = $(this);
            var tthisp = tthis.parents(".hide");
            var s = 'authsend=1&authtype='+tthisp.attr("id");
            if ((tthisp.attr("id") == "reg" || tthisp.attr("id") == "pass2") && tthisp.find("input[name='userpass']").val() != tthisp.find("input[name='userpass2']").val()) {
                $.alertk("两次密码不一致");
                tthisp.find("input[name='userpass2']").focus();
                return false;
            }
            var g = false;
            tthisp.find(".inp").find("input").each(function(){
                if (!$(this).val()) {
                    $.alertk($(this).attr("placeholder")+"不能为空");
                    $(this).focus();
                    g = true;
                    return false;
                }
                s+= "&" + $(this).attr("name") + "=" + $(this).val();
            });
            if (g) return false;
            //
            $.ajax({
                type: "POST",
                url: '{#get_url()#}',
                data: s,
                dataType: 'json',
                success: function (data) {
                    $.alert(data.message);
                    if (data != null && data.success != null && data.success) {
                        setTimeout(function(){
                            window.location.reload();
                        },800);
                    }
                },error : function () {
                    $.alert("系统繁忙！");
                },
                cache: false
            });
        });
    });
</script>
</body>
</html>