
<!DOCTYPE html>
<html>
<head lang="zh-CN">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{#$submit#}公众号/服务窗 - {#$BASE_NAME#}</title>
    <link rel="stylesheet" href="{#$CSS_PATH#}normalize.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}global.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}alert.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}font-awesome.css"/>
    <link rel="stylesheet" href="{#$NOW_PATH#}css/style.css"/>
    <script type="text/javascript" src="{#$JS_PATH#}jquery-1.11.0.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.alert.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.form.min.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}linkage.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}zero/ZeroClipboard.js"></script>
</head>
<body>
{#include file="header_user.tpl"#}
<script>$('#head-nav-menu>li:eq(0)').addClass('active');</script>


<div class="wrapper">
    <div class="breadcrumb">
        <div class="items">
            <a href="{#$urlarr.2#}">平台首页</a>
            <i class="iconfont">&#xe621;</i>
            <span>公众号/服务窗管理</span>
        </div>
    </div>
    <div class="main add-mp">
        <div class="hd">
            <h1 class="title">{#$submit#}公众号/服务窗</h1>
            <div class="control">
                <a href="{#$urlarr.2#}" class="button button-primary button-rounded button-small">返回首页</a>
            </div>
        </div>
        <div class="bd">
            <div class="section" style="position:relative;">
                {#if (value($edit,'icon'))#}
                    <div class="alicon" style="position:absolute;top:35px;right:100px;">
                        <img src="{#value($edit,'icon')|fillurl#}"/>
                    </div>
                {#/if#}
                <form action="{#$urlarr.now#}"  method="post" id="addform" enctype="multipart/form-data">
                    <div class="topmenu" id="topmenu">
                        <a href="javascript:;" d-index="0">微信公众号</a>
                        <a href="{#weburl('system/addcorp')#}" d-index="-1"{#if $edit#} style="display:none;"{#/if#}>微信企业号</a>
                        <a href="javascript:;" d-index="1">支付宝服务窗</a>
                    </div>
                    <table class="table table-form tabmenu">
                        <tbody>
                        <tr>
                            <td class="al-right" width="130"><span>公众号名称</span></td>
                            <td>
                                <input class="form-control" id="wx_name" type="text" name="wx_name" value="{#value($edit,'wx_name')#}"/>
                                <a href="javascript:void(0);" class="button button-primary button-akey">一键获取</a>
                            </td>
                        </tr>
                        {#if $edit#}
                            <tr>
                                <td class="al-right"><span>URL(服务器地址)</span></td>
                                <td>
                                    <input class="form-control" id="_wxurl" type="text"
                                           value="{#$urlarr.index#}weixin/{#value($edit,'id')#}/{#if strexists($urlarr.index,'?')#}?index{#/if#}" disabled="disabled"/>
                                    <a id="_wxurlbut" class="normal-link">复制</a>
                                </td>
                            </tr>
                        {#else#}
                            <tr>
                                <td class="al-right"><span>URL(服务器地址)</span></td>
                                <td>
                                    <input class="form-control" value="添加后可查看" style="color:#BBB;" disabled="disabled"/>
                                </td>
                            </tr>
                        {#/if#}
                        <tr>
                            <td class="al-right"><span>AppID(应用ID)</span></td>
                            <td><input class="form-control" id="wx_appid" type="text" name="wx_appid" value="{#value($edit,'wx_appid')#}" placeholder="留空关闭微信功能"/></td>
                        </tr>
                        <tr>
                            <td class="al-right"><span>AppSecret(应用密钥)</span></td>
                            <td><input class="form-control" id="wx_secret" type="text" name="wx_secret" value="{#value($edit,'wx_secret')#}"/></td>
                        </tr>
                        <tr>
                            <td class="al-right"><span>Token(令牌)</span></td>
                            <td><input class="form-control" id="wx_token" type="text" name="wx_token" value="{#value($edit,'wx_token')#}"/></td>
                        </tr>
                        <tr>
                            <td class="al-right"><span>EncodingAESKey</span></td>
                            <td><input class="form-control" id="wx_aeskey" type="text" name="wx_aeskey" value="{#value($edit,'wx_aeskey')#}"/></td>
                        </tr>
                        <tr>
                            <td class="al-right"><span>认证级别</span></td>
                            <td class="form-reg">
                                <select id="wx_level" name="wx_level">
                                    <option value="1">普通订阅号</option>
                                    <option value="2">普通服务号</option>
                                    <option value="3">认证订阅号</option>
                                    <option value="4">认证服务号</option>
                                </select>
                                <input id="wx_username" name="wx_username" type="hidden" value="">
                            </td>
                        </tr>
                        {#if value($edit,'wx_level')#}
                            <script>$("#wx_level").val('{#value($edit,'wx_level')#}');</script>
                        {#/if#}
                        <tr>
                            <td class="al-right"><span>引导关注素材</span></td>
                            <td><input class="form-control" id="wx_suburl" type="text" name="wx_suburl" value="{#value($edit,'wx_suburl')#}"/></td>
                        </tr>
                        <tr>
                            <td class="al-right"><span>微信支付参数</span></td>
                            <td>
                                <a href="javascript:;" onclick="showhide(this,'payment-wx');" class="normal-link"> 点击展开设置 </a>
                                <table class="table table-form payment" id="payment-wx">
                                    <tbody>
                                    <tr>
                                        <td colspan="2" class="pmsg">
                                            你必须向微信公众平台提交企业信息以及银行账户资料，审核通过并签约后才能使用微信支付功能 <a href="https://mp.weixin.qq.com/paymch/readtemplate?t=mp/business/faq_tmpl" target="_blank">申请及详情请查看这里</a>.
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="pmsg">
                                            微信支付接口，注意你的系统访问地址一定不要写错了，这里我们用访问地址代替下面说明中出现的链接，申请微信支付的接口说明如下：<br/>
                                            <br/>
                                            JS API网页支付参数<br/>
                                            <br/>
                                            支付授权目录: {#$smarty.const.BASE_URI#}payment/weixin/<br/>
                                            支付请求实例: {#$smarty.const.BASE_URI#}payment/weixin/<br/>
                                            共享收货地址: 选择"是"<br/>
                                            <br/>
                                            Native原生支付<br/>
                                            <br/>
                                            支付回调URL: {#$smarty.const.BASE_URI#}payment/weixin/native/<br/>
                                            维权通知URL: {#$smarty.const.BASE_URI#}payment/weixin/rights/<br/>
                                            警告通知URL: {#$smarty.const.BASE_URI#}payment/weixin/warning/
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="al-right" valign="top" style="width:120px"><span>接口类型</span></td>
                                        <td>
                                            <label class="radio-inline" onclick="$('.s').show();$('.m').hide();">
                                                <input type="radio" name="payment[weixin][version]" id="weixinv1" value="1" autocomplete="off">
                                                旧版
                                            </label>
                                            <label class="radio-inline" onclick="$('.s').hide();$('.m').show();">
                                                <input type="radio" name="payment[weixin][version]" id="weixinv2" value="2" checked="checked" autocomplete="off">
                                                新版(2014年9月之后申请的)
                                            </label>
                                            <br/>
                                            <span class="help-block">由于微信支付接口调整，需要根据申请时间来区分支付接口</span>
                                        </td>
                                    </tr>
                                    {#$payappid = value($edit,'payment|weixin|appid')#}
                                    <tr>
                                        <td class="al-right" valign="top"><span>身份标识<br/>(AppID)</span></td>
                                        <td>
                                            <input class="form-control" type="text" name="payment[weixin][appid]" id="weixinappid"{#if $payappid#} value="{#$payappid#}"{#else#} value="{#value($edit,'wx_appid')#}" disabled="disabled"{#/if#}/><br/>
                                            <span class="help-block">
                                                公众号身份标识，
                                                {#if $payappid#}
                                                    <a href="javascript:void(0)" id="payedita" onclick="_payeditappid()">点击这里设为默认</a>
                                                    {#else#}
                                                    <a href="javascript:void(0)" id="payedita" onclick="_payeditappid()">点击这里修改</a>
                                                {#/if#}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="al-right" valign="top"><span>身份密钥<br/>(AppSecret)</span></td>
                                        <td>
                                            <input class="form-control" type="text" name="payment[weixin][secret]" id="weixinsecret"{#if $payappid#} value="{#value($edit,'payment|weixin|secret')#}"{#else#} value="{#value($edit,'wx_secret')#}" disabled="disabled"{#/if#}/><br/>
                                            <span class="help-block">公众平台API(参考文档API 接口部分)的权限获取所需密钥Key</span>
                                        </td>
                                    </tr>
                                    <tr class="s" style="display:none">
                                        <td class="al-right" valign="top"><span>商户身份<br/>(partnerId)</span></td>
                                        <td>
                                            <input class="form-control" type="text" name="payment[weixin][partner]" id="weixinpartner" value="{#value($edit,'payment|weixin|partner')#}"/><br/>
                                            <span class="help-block">财付通商户身份标识；公众号支付请求中用于加密的密钥Key</span>
                                        </td>
                                    </tr>
                                    <tr class="s" style="display:none">
                                        <td class="al-right" valign="top"><span>商户密钥<br/>(partnerKey)</span></td>
                                        <td>
                                            <input class="form-control" type="text" name="payment[weixin][key]" id="weixinkey" value="{#value($edit,'payment|weixin|key')#}"/><br/>
                                            <span class="help-block">财付通商户权限密钥Key</span>
                                        </td>
                                    </tr>
                                    <tr class="s" style="display:none">
                                        <td class="al-right" valign="top"><span>通信密钥<br/>(paySignKey)</span></td>
                                        <td>
                                            <input class="form-control" type="text" name="payment[weixin][signkey]" id="weixinsignkey" value="{#value($edit,'payment|weixin|signkey')#}"/><br/>
                                            <span class="help-block">公众号支付请求中用于加密的密钥Key</span>
                                        </td>
                                    </tr>
                                    <tr class="m">
                                        <td class="al-right" valign="top"><span>微信支付商户号<br/>(MchId)</span></td>
                                        <td>
                                            <input class="form-control" type="text" name="payment[weixin][mchid]" id="weixinmchid" value="{#value($edit,'payment|weixin|mchid')#}"/><br/>
                                            <span class="help-block">公众号支付请求中用于加密的密钥Key</span>
                                        </td>
                                    </tr>
                                    <tr class="m">
                                        <td class="al-right" valign="top"><span>商户支付密钥<br/>(API密钥)</span></td>
                                        <td>
                                            <input class="form-control" type="text" name="payment[weixin][apikey]" id="weixinapikey" value="{#value($edit,'payment|weixin|apikey')#}"/><br/>
                                            <span class="help-block">此值需要手动在腾讯商户后台API密钥保持一致，<a href="#" target="_blank">查看设置教程</a></span>
                                        </td>
                                    </tr>

                                    {#if value($edit,'payment|weixin|version') == 1#}
                                        <script>$("#weixinv1").click();</script>
                                    {#/if#}

                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td class="al-right"><span>借用网页授权登录</span></td>
                            <td class="get_class">
                                <a href="javascript:;" onclick="showhide(this,'payment-wxoauth');" class="normal-link">点击展开设置</a>
                                <span class="isget"></span>
                                <table class="table table-form payment" id="payment-wxoauth">
                                    <tbody>
                                    <tr>
                                        <td colspan="2" class="pmsg">
                                            开发者需要先到公众平台网站的【开发者中心】网页服务中配置授权回调页面域名为: {#$smarty.server.HTTP_HOST#}；<br/>
                                            此功能仅支持被借用的公众号必须是认证服务号。
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="al-right" style="width:160px" valign="top"><span>AppID(应用ID)：</span></td>
                                        <td>
                                            <input class="form-control gappid" type="text" name="other[get_appid]" id="get_appid" value="{#value($edit,'setting|other|get_appid')#}"/>
                                            <select class="form-control gselect">
                                                <option value="">=快速选择=</option>
                                                {#foreach from=$wxlist item=witem#}
                                                    <option value="{#$witem.wx_appid#}" data-secret="{#$witem.wx_secret#}">ID{#$witem.id#}:{#$witem.wx_name#}</option>
                                                {#/foreach#}
                                            </select>
                                            <br/>
                                            <span class="help-block">输入被借用公众号的AppID，此项留空表示不是用此功能。</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="al-right" valign="top"><span>AppSecret(应用密钥)：</span></td>
                                        <td>
                                            <input class="form-control gsecret" type="text" name="other[get_secret]" id="get_secret" value="{#value($edit,'setting|other|get_secret')#}"/><br/>
                                            <span class="help-block">输入被借用公众号的AppSecret。</span>
                                        </td>
                                    </tr>
                                    <tr class="isfunc">
                                        <td class="al-right" valign="top"><span>仅指定功能模块借用：</span></td>
                                        <td>
                                            <div class="other_label">
                                                {#foreach from=$_func item=fitem#}
                                                    {#if !$fitem.default#}
                                                        <label title="{#$fitem.title#}"><input type="checkbox" name="other[get_appoint][]" value="{#$fitem.title_en#}"{#if in_array($fitem.title_en, value($edit,'setting|other|get_appoint'))#} checked{#/if#}>{#$fitem.title#}</label>
                                                    {#else#}
                                                        <label class="label_del" title="系统预设模块,不需要身份登录"><input type="checkbox" disabled>{#$fitem.title#}</label>
                                                    {#/if#}
                                                    {#foreachelse#}
                                                    <label title="会员卡"><input type="checkbox" name="other[get_appoint][]" value="vip">会员卡</label>
                                                {#/foreach#}
                                            </div>
                                            <div style="clear:both;"></div>
                                            <span class="help-block">支持筛选，不选且填写了AppID默认所有需要身份认证的功能模块都借用。</span>
                                        </td>
                                    </tr>
                                    <tr class="isfunc">
                                        <td class="al-right" valign="top"><span>应用授权作用域：</span></td>
                                        <td>
                                            <div class="other_label">
                                                <label class="wid"><input type="checkbox" name="other[get_scope]" value="snsapi_base"{#if value($edit,'setting|other|get_scope')=='snsapi_base'#} checked{#/if#}>不弹出授权页面</label>
                                            </div>
                                            <div style="clear:both;"></div>
                                            <span class="help-block">不弹出授权页面，只能获取用户openid，无法获取昵称、头像、性别、所在地等。</span>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td class="al-right"><span>借用 JS 分享接口</span></td>
                            <td class="get_class">
                                <a href="javascript:;" onclick="showhide(this,'payment-wxjs');" class="normal-link">点击展开设置</a>
                                <span class="isget"></span>
                                <table class="table table-form payment" id="payment-wxjs">
                                    <tbody>
                                    <tr>
                                        <td colspan="2" class="pmsg">
                                            开发者需要先到公众平台网站的【公众号设置 &gt;&gt; 功能设置】中配置 【JS 接口安全域名】；<br/>
                                            应设置为本站域名: {#$smarty.server.HTTP_HOST#}。
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="al-right" style="width:160px" valign="top"><span>AppID(应用ID)：</span></td>
                                        <td>
                                            <input class="form-control gappid" type="text" name="other[getjs_appid]" id="getjs_appid" value="{#value($edit,'setting|other|getjs_appid')#}"/>
                                            <select class="form-control gselect">
                                                <option value="">=快速选择=</option>
                                                {#foreach from=$wxlist item=witem#}
                                                    <option value="{#$witem.wx_appid#}" data-secret="{#$witem.wx_secret#}">ID{#$witem.id#}:{#$witem.wx_name#}</option>
                                                {#/foreach#}
                                            </select>
                                            <br/>
                                            <span class="help-block">输入被借用公众号的AppID，此项留空表示不是用此功能。</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="al-right" valign="top"><span>AppSecret(应用密钥)：</span></td>
                                        <td>
                                            <input class="form-control gsecret" type="text" name="other[getjs_secret]" id="getjs_secret" value="{#value($edit,'setting|other|getjs_secret')#}"/><br/>
                                            <span class="help-block">输入被借用公众号的AppSecret。</span>
                                        </td>
                                    </tr>
                                    <tr class="isfunc">
                                        <td class="al-right" valign="top"><span>仅指定功能模块借用：</span></td>
                                        <td>
                                            <div class="other_label">
                                                {#foreach from=$_func item=fitem#}
                                                    {#if !in_array($fitem.title_en, array('menu','reply','message','emulator'))#}
                                                    <label><input type="checkbox" name="other[getjs_appoint][]" value="{#$fitem.title_en#}"{#if in_array($fitem.title_en, value($edit,'setting|other|getjs_appoint'))#} checked{#/if#}>{#$fitem.title#}</label>
                                                    {#/if#}
                                                {#/foreach#}
                                            </div>
                                            <div style="clear:both;"></div>
                                            <span class="help-block">支持筛选，不选且填写了AppID默认所有的功能模块都借用。</span>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td class="al-right"><span>二维码图片</span></td>
                            <td>{#tpl_form_image("wx_qrcode", value($edit,'wx_qrcode'))#}</td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td>
                                <input class="button button-primary button-rounded" type="submit" value="{#$submit#}"/>
                                <input class="button button-primary button-rounded backing" type="button" value="返回"/>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <table class="table table-form tabmenu">
                        <tbody>
                        <tr>
                            <td></td>
                            <td></td>
                        </tr>
                        </tbody>
                    </table>
                    <table class="table table-form tabmenu">
                        <tbody>
                        <tr>
                            <td class="al-right" width="130"><span>服务窗名称</span></td>
                            <td><input class="form-control" id="al_name" type="text" name="al_name" value="{#value($edit,'al_name')#}"/></td>
                        </tr>
                        {#if $edit#}
                            <tr>
                                <td class="al-right"><span>应用网关</span></td>
                                <td>
                                    <input class="form-control" id="_alurl" type="text"
                                           value="{#$urlarr.index#}alipay/{#value($edit,'id')#}/{#if strexists($urlarr.index,'?')#}?index{#/if#}" disabled="disabled"/>
                                    <a id="_alurlbut" class="normal-link">复制</a>
                                </td>
                            </tr>
                        {#else#}
                            <tr>
                                <td class="al-right"><span>应用网关</span></td>
                                <td>
                                    <input class="form-control" value="添加后可查看" style="color:#BBB;" disabled="disabled"/>
                                </td>
                            </tr>
                        {#/if#}
                        <tr>
                            <td class="al-right"><span>APPID</span></td>
                            <td><input class="form-control" id="al_appid" type="text" name="al_appid" value="{#value($edit,'al_appid')#}" placeholder="留空关闭服务窗功能"/></td>
                        </tr>
                        <tr>
                            <td class="al-right"><span>支付宝网关地址</span></td>
                            <td><input class="form-control" id="al_gateway" type="text" name="al_gateway" value="{#value($edit,'al_gateway','','https://openapi.alipay.com/gateway.do')#}"/></td>
                        </tr>
                        <tr>
                            <td class="al-right"><span>支付宝公钥（RSA)</span></td>
                            <td><textarea class="form-control" id="al_rsa" name="al_rsa">{#value($edit,'al_rsa')#}</textarea></td>
                        </tr>
                        <tr>
                            <td class="al-right"><span>应用公钥（RSA）</span></td>
                            <td><textarea class="form-control" id="al_key" name="al_key" placeholder="建议留空自动生成">{#value($edit,'al_key')#}</textarea></td>
                        </tr>
                        <tr>
                            <td class="al-right"><span>无线支付参数</span></td>
                            <td>
                                <a href="javascript:;" onclick="showhide(this,'payment');" class="normal-link"> 点击展开设置 </a>
                                <table class="table table-form payment" id="payment">
                                    <tbody>
                                    <tr>
                                        <td colspan="2" class="pmsg">
                                            您的支付宝账号必须支持手机网页即时到账接口, 才能使用手机支付功能, <a href="https://b.alipay.com/order/productDetail.htm?productId=2013080604609688" target="_blank">申请及详情请查看这里</a>.
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="al-right" valign="top" style="width:120px"><span>收款支付宝账号</span></td>
                                        <td>
                                            <input class="form-control" type="text" name="payment[alipay][account]" id="alipayaccount" value="{#value($edit,'payment|alipay|account')#}"/><br/>
                                            <span class="help-block">如果开启兑换或交易功能，请填写真实有效的支付宝账号，用于收取用户以现金兑换交易积分的相关款项。如账号无效或安全码有误，将导致用户支付后无法正确对其积分账户自动充值，或进行正常的交易对其积分账户自动充值，或进行正常的交易。 如您没有支付宝帐号，<a href="https://memberprod.alipay.com/account/reg/enterpriseIndex.htm" target="_blank">请点击这里注册</a></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="al-right" valign="top"><span>合作者身份</span></td>
                                        <td>
                                            <input class="form-control" type="text" name="payment[alipay][partner]" id="alipaypartner" value="{#value($edit,'payment|alipay|partner')#}"/><br/>
                                            <span class="help-block">支付宝签约用户请在此处填写支付宝分配给您的合作者身份，签约用户的手续费按照您与支付宝官方的签约协议为准。<br>如果您还未签约，<a href="https://memberprod.alipay.com/account/reg/enterpriseIndex.htm" target="_blank">请点击这里签约</a>；如果已签约,<a href="https://b.alipay.com/order/pidKey.htm?pid=2088501719138773&amp;product=fastpay" target="_blank">请点击这里获取PID、Key</a>;如果在签约时出现合同模板冲突，请咨询0571-88158090</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="al-right" valign="top"><span>校验密钥</span></td>
                                        <td>
                                            <input class="form-control" type="text" name="payment[alipay][secret]" id="alipaysecret" value="{#value($edit,'payment|alipay|secret')#}"/><br/>
                                            <span class="help-block">支付宝签约用户可以在此处填写支付宝分配给您的交易安全校验码，此校验码您可以到支付宝官方的商家服务功能处查看</span>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        <tr style="display:none;">
                            <td class="al-right"><span>服务所在地区</span></td>
                            <td class="form-reg">
                                <input class="form-control" type="text" name="linkaddr" id="linkaddr" value="{#value($edit,'linkaddr','',$user.linkaddr)#}"/>
                            </td>
                        </tr>
                        <tr>
                            <td class="al-right"><span>二维码图片</span></td>
                            <td>{#tpl_form_image("al_qrcode", value($edit,'al_qrcode'))#}</td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td>
                                <input class="button button-primary button-rounded" type="submit" value="{#$submit#}"/>
                                <input class="button button-primary button-rounded backing" type="button" value="返回"/>
                                <input type="hidden" name="dosubmit" value="1"/>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="akeytemp" style="display:none">
    <table class="table table-form">
        <tbody>
        <tr>
            <td class="al-right" width="50"><span>账号</span></td>
            <td><input idd="akeyusername" type="text" class="form-control" style="width:220px" placeholder="邮箱/微信号/QQ号" onblur="_verify(this);"/></td>
        </tr>
        <tr>
            <td class="al-right"><span>密码</span></td>
            <td><input idd="akeyuserpass" type="password" class="form-control" style="width:220px" placeholder="密码"/></td>
        </tr>
        <tr idd="showverify" style="display:none">
            <td class="al-right"><span>验证码</span></td>
            <td>
                <input idd="akeyverify" type="text" class="form-control" style="width:110px" placeholder="验证码"/>
                <img src="#" style="width:110px;height:37px;vertical-align:bottom;">
            </td>
        </tr>
        </tbody>
    </table>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        ZeroClipboard.setMoviePath("{#$JS_PATH#}zero/ZeroClipboard.swf");
        $("input.backing").click(function(){
            window.location.href = "{#$urlarr.2#}";
        });
        $('#topmenu').find("a").each(function(index) {
            $(this).click(function(){
                if ($(this).attr("d-index") == '-1') {
                    $.alertk("加载中...");
                }else{
                    $('#topmenu').find("a").removeClass("active");
                    $(this).addClass("active");
                    $("table.tabmenu").hide().eq(index).show();
                    {#if $edit#}
                    var clip = new ZeroClipboard.Client();
                    clip.setHandCursor(true);
                    clip.setText($("#_alurl").val());
                    clip.addEventListener('complete', function(){
                        $.alertk("复制成功！");
                    });
                    clip.glue('_alurlbut');
                    var clipwx = new ZeroClipboard.Client();
                    clipwx.setHandCursor(true);
                    clipwx.setText($("#_wxurl").val());
                    clipwx.addEventListener('complete', function(){
                        $.alertk("复制成功！");
                    });
                    clipwx.glue('_wxurlbut');
                    {#/if#}
                }
            });
        }).eq(0).click();
        $('#addform').submit(function() {
            var retu = true;
            if ($('#al_name').val()) {
                retu = $('#al_name').inTips("", 2, retu);
                retu = $('#al_appid').inTips("", -1, retu);
                retu = $('#al_gateway').inTips("", -1, retu);
                retu = $('#al_rsa').inTips("", -1, retu);
                /*retu = $('#linkaddr').inTips("请选择地区", -1, retu, 0, $('#__linkage'));*/
                if (!retu) $('a[d-index="1"]').click();
            }
            if ($('#wx_name').val()) {
                retu = $('#wx_name').inTips("", 2, retu);
                retu = $('#wx_appid').inTips("", -1, retu);
                retu = $('#wx_secret').inTips("", -1, retu);
                if (!retu) $('a[d-index="0"]').click();
            }
            if (!$('#al_name').val() && !$('#wx_name').val()) {
                $.showModal("服务窗或公众号至少要填写一个！");
                $('a[d-index="0"]').click();
                retu = false;
            }
            if (!retu) return false;
            //
            if ($('#al_name').val()) {
                {#if (!value($edit,'al_name'))#}
                var retuj = $.ajax({url:"{#$urlarr.2#}alname/?alname="+$('#al_name').val(),dataType: "json", async:false});
                if (retuj.responseText != "1") {
                    $.showModal("服务窗名称已存在，请更换一个！");
                    $('a[d-index="1"]').click();
                    return false;
                }
                {#/if#}
            }
            //
            $.alert('正在保存...', 0);
            $(this).ajaxSubmit({
                dataType : 'json',
                success : function (data) {
                    $.alert(0);
                    if (data != null && data.success != null && data.success) {
                        $.showModal(data.message, '{#$urlarr.2#}add/'+data.id+'/');
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
        $(".button-akey").click(function(){
            var akeyart = art.dialog({
                title: '一键获取微信信息',
                fixed: true,
                lock: true,
                opacity: '.3',
                content: $("#akeytemp").html().replace(/idd/g, 'id'),
                button: [{
                    name: '一键获取',
                    focus: true,
                    callback: function () {
                        $.alert("正在获取...", 0, 1);
                        $.ajax({
                            url: "{#weburl("system/weixinlogin")#}",
                            type: "POST",
                            data: {wxusername: $("#akeyusername").val(), wxpassword: $("#akeyuserpass").val(), verify: $("#akeyverify").val()},
                            dataType: "json",
                            success: function (data) {
                                $.alert(0);
                                if (data != null && data.success != null && data.success) {
                                    $("#wx_name").val(data.message.name);
                                    $("#wx_appid").val(data.message.key);
                                    $("#wx_secret").val(data.message.secret);
                                    $("#wx_token").val(data.message.token);
                                    $("#wx_aeskey").val(data.message.EncodingAESKey);
                                    $("#wx_level").val(data.message.level);
                                    $("#wx_username").val(data.username);
                                    if (data.message.qrcode_img) {
                                        $("#wx_qrcode").val(data.message.qrcode_img);
                                        $("#wx_qrcode").parent().next().find("img").attr('src', data.message.qrcode_imgurl);
                                    }
                                    akeyart.close();
                                }else{
                                    $("#wx_username").val('');
                                    if (data.ret == '-8' || data.ret == '-27') {
                                        _verify2($("#akeyusername"));
                                        $.alert(data.message);
                                    }else{
                                        alert(data.message);
                                    }
                                }
                            },error : function () {
                                $("#wx_username").val('');
                                $.alert(0);
                                alert("获取错误！");
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
                }]
            });
        });
        $("input.gappid").keyup(function(){
            var tc = $(this).parents(".get_class");
            if ($(this).val().trim() != "") {
                if ($(this).val().trim() == $("#wx_appid").val().trim()) {
                    tc.find("span.isget").text("");
                    tc.find("tr.isfunc").hide();
                    tc.find(".gappid").val("");
                    tc.find(".gsecret").val("");
                }else{
                    tc.find("span.isget").text("已启动");
                    tc.find("tr.isfunc").show();
                }
            }else{
                tc.find("span.isget").text("");
                tc.find("tr.isfunc").hide();
            }
        }).keyup();
        $("select.gselect").change(function(){
            var tc = $(this).parents(".get_class");
            tc.find(".gappid").val($(this).val()).keyup();
            tc.find(".gsecret").val($(this).find("option:selected").attr("data-secret"));
        });
        $("#wx_appid").keyup(function(){
            if ($("#payedita").text() == '点击这里修改') {
                $("#weixinappid").val($(this).val());
            }
        });
        $("#wx_secret").keyup(function(){
            if ($("#payedita").text() == '点击这里修改') {
                $("#weixinsecret").val($(this).val());
            }
        });
        {#if !value($edit,'wx_name') && value($edit,'al_name')#}
        $('a[d-index="1"]').click();
        {#/if#}
    });
    function _verify(obj) {
        var tthisval = $(obj).val();
        if (tthisval && $(obj).attr("data-val") != tthisval) {
            $("#showverify").hide();
            $(obj).attr("data-val", tthisval);
        }
    }
    function _verify2(obj) {
        var tthisval = $(obj).val();
        var _url = '{#weburl("system/wxcode")#}&username='+tthisval;
        var sv = $("#showverify");
        sv.show();
        sv.find("img").attr("src", _url+"&r="+new Date().getTime());
        sv.find("img").unbind();
        sv.find("img").click(function(){ $(this).attr("src", _url+"&r="+new Date().getTime()); });
    }
    function showhide(obj, t) {
        var tthis = $(obj);
        if (tthis.text() == "点击隐藏设置") {
            $('#'+t).hide();
            tthis.text("点击展开设置");
        }else{
            $('#'+t).show();
            tthis.text("点击隐藏设置");
        }
    }
    function _payeditappid() {
        var tthis = $("#payedita");
        var appid = $("#weixinappid");
        var secret = $("#weixinsecret");
        if (tthis.text() == '点击这里修改') {
            appid.prop("disabled", false);
            secret.prop("disabled", false);
            tthis.text('点击这里设为默认');
        }else{
            appid.prop("disabled", true).val($("#wx_appid").val());
            secret.prop("disabled", true).val($("#wx_secret").val());
            tthis.text('点击这里修改');
        }
    }
    linkage("linkaddr","{#$urlarr.index#}web/system/linkage/",0,0);
</script>


{#include file="footer.tpl"#}

</body>
</html>