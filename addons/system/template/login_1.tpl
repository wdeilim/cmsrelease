
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
    <link rel="stylesheet" href="{#$NOW_PATH#}css/iconfont.css"/>
    <link rel="stylesheet" href="{#$NOW_PATH#}css/login_1/common.css"/>
    <script type="text/javascript" src="{#$JS_PATH#}jquery-1.11.0.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.alert.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.form.min.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}bootstrap.min.js"></script>
</head>
<body>
{#include file="header.tpl"#}
<script>$('#head-nav-menu>li:eq(0)').addClass('active');</script>

<div class="wrapper-sign-in slide">
    <div id="banner" class="carousel slide" data-ride="carousel">
        <div class="carousel-inner" role="listbox">
            {#foreach from=$tem['t1']['loginbg'] name=foo item=bg#}
                <div class="item{#if $smarty.foreach.foo.first#} active{#/if#}" style="background-image:url({#fillurl($bg)#});"></div>
            {#/foreach#}
        </div>
    </div>

    <div class="main-sigh-in cf">
        <div class="brandBox">
            <p class="slogan">{#$tem['t1']['title']#}</p>
            <p class="description">{#$tem['t1']['subtitle']#}</p>
        </div>
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
                    <div class="form-group form-group-input">
                        <label for="username">
                            <span class="ui-icon ui-icon-userDEF"><i class="iconfont-auth">&#xe617;</i></span>
                        </label>
                        <input class="form-control" type="text" name="username" id="username" placeholder="用户名"/>
                    </div>
                </div>
                <div class="form-item">
                    <div class="form-group form-group-input">
                        <label for="userpass">
                            <span class="ui-icon ui-icon-userDEF"><i class="iconfont-auth">&#xe616;</i></span>
                        </label>
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

<div class="contentSection">
    <div class="section brandList">
        <ol class="step">
            <li>
                <a href="javascript:void(0)">
                    <em><i class="iconfont-crmhome" title="微信公众号">&#xe601;</i></em>
                    <dl>
                        <dt>微信公众号</dt>
                        <dd>订阅号、服务号、企业号提供最全的解决方案</dd>
                    </dl>
                </a>
            </li>
            <li>
                <a href="javascript:void(0)">
                    <em><i class="iconfont-crmhome" title="支付宝服务窗">&#xe615;</i></em>
                    <dl>
                        <dt>支付宝服务窗</dt>
                        <dd>专为支付宝服务窗商户设计，打造双入口平台</dd>
                    </dl>
                </a>
            </li>
            <li>
                <a href="javascript:void(0)">
                    <em><i class="iconfont-crmhome" title="会员卡">&#xe610;</i></em>
                    <dl>
                        <dt>会员卡</dt>
                        <dd>特权、礼品、优惠券，新一代的移动会员管理系统</dd>
                    </dl>
                </a>
            </li>
            <li>
                <a href="javascript:void(0)">
                    <em><i class="iconfont-crmhome" title="售货机行业">&#xe60c;</i></em>
                    <dl>
                        <dt>微网站 场景</dt>
                        <dd>专注移动互联，为客户定制全套解决方案</dd>
                    </dl>
                </a>
            </li>
        </ol>
    </div>
    <div class="brandList gray">
        <div class="w1000 fn-clear">
            <div class="background bg1 fn-right"></div>
            <div class="brand fn-left ft-left">
                <p class="slogan">红包/折扣营销</p>
                <p class="description">多种营销，轻松提升客流量、交易额</p>
            </div>
        </div>
    </div>
    <div class="brandList">
        <div class="w1000 fn-clear">
            <div class="background bg2 fn-left"></div>
            <div class="brand fn-right ft-right">
                <p class="slogan">海量用户数据</p>
                <p class="description">精准平台海量数据处理的设计与实现</p>
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