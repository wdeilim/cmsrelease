
<!DOCTYPE HTML>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta content="telephone=no" name="format-detection">
    <title>{#if $shopset['name']#}{#$shopset['name']#}{#else#}会员卡首页{#/if#}</title>
    <link rel="stylesheet" href="{#$NOW_PATH#}css/index.css">
    <script type="text/javascript" src="{#$JS_PATH#}jquery.min.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.alert.js"></script>
    <style>
        .list-item a {display: block;}
        .list-item .t {display: block;}
        .list-item .t a {font-size: 16px; color: #2e2e2e;padding-right:12px;}
        .list-item .n a {color: #fff;}
        .vip-name.name-left {left: 15px;}
    </style>

</head>

<body>
<div class="layout">
    <div class="vip-card">
        <div class="vip-card-box">
            <img src="{#value($hykbm,'bgimgduv')|fillurl#}" alt="" class="vip-pic">
            <h1 class="vip-name{#if value($hykbm,'position')=='1'#} name-left{#/if#}"{#cot(value($hykbm,'namecolor'))#}>{#value($hykbm,'name')#}{#get_user_rank($_A['vip']['pluspoint'])#}</h1>
            <img src="{#value($hykbm,'logo')|fillurl#}" alt="" class="vip-logo">
            <div class="vip-num"><p>会员卡号</p><span{#cot(value($hykbm,'numcolor'))#}>{#format_user($_A['vip']['card'],0)#}</span></div>
        </div>
        <p class="vip-slogan">{#value($hykbm,'text')#}</p>
        <p class="vip-tips"><i class="vip-icon icon-tips"></i>使用时请向服务员出示此卡</p>
    </div>
    <div class="vip-credit">
        <table class="vip-table">
            <tr>
                <th width="25%">剩余积分</th>
                <th width="25%">签到积分</th>
                <th width="25%">消费积分</th>
                <th width="25%">现金余额</th>
            </tr>
            <tr>
                <td><a href="{#appurl('vip/record/point')#}">{#$_A['vip']['point']#}</a></td>
                <td>{#$_A['vip']['inpoint']#}</td>
                <td>{#$_A['vip']['outpoint']#}</td>
                <td><a href="{#appurl('vip/record/money')#}">{#$_A['vip']['money']#}</td>
            </tr>
        </table>
    </div>

    {#ddb_pc set="数据表:reply,列表名:lists,排序:`inorder` desc>`update` desc" where="{#$rwheresql#}"#}
    {#if $lists || $_A['vip_data']['userset']['link']#}
        {#$o_base64 = base64_encode(authcode($_A['vip']['openid'], 'ENCODE'))#}
        <div class="vip-list vip-notice" id="reply_list">
            <ul>
                {#foreach from=$lists item=list#}
                    {#if $list['vip_title']#}
                        <li class="list-item">
                            <a href="{#appurl(0, $list['module'], 'welcome')#}&rid={#$list['id']#}&from_user={#$o_base64#}">
                                <span class="t">{#$list['vip_title']#}</span>
                            </a>
                        </li>
                    {#/if#}
                {#/foreach#}
                {#foreach from=$_A['vip_data']['userset']['link'] item=list#}
                    {#if $list['title']#}
                        <li class="list-item">
                            <a href="{#$list['url']#}">
                                <span class="t">{#$list['title']#}</span>
                            </a>
                        </li>
                    {#/if#}
                {#/foreach#}
            </ul>
        </div>
    {#/if#}

    <div class="vip-list vip-notice">
        <ul>
            <li class="list-item">
                <a href="{#appurl('vip/notification')#}">
                    <span class="t">最新通知</span>
                    <span class="n">{#$msg_num#}</span>
                </a>
            </li>
            <li class="list-item">
                <a href="{#appurl('vip/privilege')#}">
                    <span class="t">会员特权</span>
                    <span class="n">{#$vip_num#}</span>
                </a>
            </li>
            <li class="list-item">
                <a href="{#appurl('vip/coupon')#}">
                    <span class="t">会员优惠券</span>
                    <span class="n">{#$cut_num#}</span>
                </a>
            </li>
            <li class="list-item">
                <a href="{#appurl('vip/giftcert')#}">
                    <span class="t">积分换礼品</span>
                    <span class="n">{#$gift_num#}</span>
                </a>
            </li>
        </ul>
    </div>
    <div class="vip-list vip-action">
        <ul>
            <li class="list-item">
                <span class="t">签到赚积分</span>
                {#if $sign#}
                <span class="n btn-sign-already"><a href="{#appurl('vip/sign')#}">今日已签到</a></span>
                {#else#}
                <span class="n btn-sign"><a href="{#appurl('vip/sign')#}">点击去签到</a></span>
                {#/if#}
            </li>
            <li class="list-item">
                <span class="t"><a href="{#appurl('vip/personal')#}">个人资料</a></span>
            </li>
        </ul>
    </div>
    <div class="vip-list vip-desc">
        <ul>
            <li class="list-item">
                <span class="t"><a href="{#appurl('vip/about')#}">会员卡说明</a></span>
            </li>
            <li class="list-item">
                <span class="t"><a href="{#appurl('vip/shop')#}">适合门店电话及地址</a></span>
            </li>
        </ul>
    </div>
    <div class="vip-list vip-contact">
        <ul>
            <li class="list-item">
                <span class="t">
                    <a href="http://api.map.baidu.com/marker?location={#$shopset.x#},{#$shopset.y#}&title={#$shopset.name|urlencode#}&content={#$shopset.address|urlencode#}&output=html&src=ddb|ddb" target="_blank" class="addr"><i class="vip-icon icon-addr"></i>地址：{#$shopset.address#}</a>
                </span>
            </li>
            <li class="list-item">
                <span class="t">
                    <a href="tel:{#$shopset.phone#}"><i class="vip-icon icon-phone"></i>电话：{#$shopset.phone#}</a>
                </span>
            </li>
        </ul>
    </div>

    {#include file="./footer.tpl" _item=0#}

</div>

<script type="text/javascript">
    $(function(){
        var r_list = $("#reply_list");
        if (r_list.find("li.list-item").length < 1) { r_list.remove(); }
    });
</script>
</body>
</html>