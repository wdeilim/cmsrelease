
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

<div id="login" class="hide" style="display:block;">
    <div class="tit">确认身份</div>
    <div class="con">
        <div class="tab" id="tablogin">
            <a href="javascript:void(0);" class="hov">账号登陆</a>
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

<div id="login2" class="hide">
    <div class="tit">确认身份</div>
    <div class="con">
        <div class="tab" id="tablogin">
            <a href="javascript:void(0);" onclick="show('login')">账号登陆</a>
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

<div id="reg" class="hide">
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

<div id="pass" class="hide">
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

<div id="pass2" class="hide">
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