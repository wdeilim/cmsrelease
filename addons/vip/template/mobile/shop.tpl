
<!DOCTYPE HTML>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta content="telephone=no" name="format-detection">
    <title>分店信息</title>
    <link rel="stylesheet" href="{#$NOW_PATH#}css/inner.css">
    <script type="text/javascript" src="{#$JS_PATH#}jquery.min.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.alert.js"></script>
    <style>
        .list-item a{display: block;}
        .list-item .t a{font-size: 16px; color: #2e2e2e;}
    </style>
</head>

<body>
<div class="layout">
    <header>
        <img src="{#vippagebanner('shoplist', $_A['vip_data']['banner'])#}" class="hd-banner">
    </header>
    <div class="wrap">
        <div class="vip-list shop-sub">
            {#foreach from=$shop item=list#}
            <h2>{#$list.name#}</h2>
            <ul class="vip-shop">
                <li class="list-item">
                    <span class="t"><a href="http://api.map.baidu.com/marker?location={#$list.x#},{#$list.y#}&title={#$list.name|urlencode#}&content={#$list.address|urlencode#}&output=html&src=ddb|ddb" target="_blank" class="addr"><i class="vip-icon icon-addr"></i>地址：{#$list.address#}</a></span>
                </li>
                <li class="list-item">
                    <span class="t"><a href="tel:{#$list.phone#}"><i class="vip-icon icon-phone"></i>电话：{#$list.phone#}</a></span>
                </li>
            </ul>
            {#foreachelse#}
            无分店
            {#/foreach#}
        </div>
    </div>

    {#include file="./footer.tpl" _item=""#}

</div>

</body>
</html>