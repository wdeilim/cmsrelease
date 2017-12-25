
<!DOCTYPE HTML>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta content="telephone=no" name="format-detection">
    <title>关于VIP卡</title>
    <link rel="stylesheet" href="{#$NOW_PATH#}css/inner.css">
    <script type="text/javascript" src="{#$JS_PATH#}jquery.min.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.alert.js"></script>
</head>

<body>
<div class="layout">
    <header>
        <img src="{#vippagebanner('about', $_A['vip_data']['banner'])#}" class="hd-banner">
    </header>
    <div class="wrap">
        <ul class="list ul-list" id="ul-list">
            <li class="item">
                <div class="hd">
                    <i class="vip-icon icon-v">VIP</i>
                    <h2>会员卡使用说明</h2>
                    <time>{#value($hyksm,'auto_time_text')#}</time>
                    <span class="btn-menu btn-open">+</span>
                </div>
                <div class="bd">
                    {#value($hyksm,'hyksm')#}
                </div>
            </li>
            <li class="item">
                <div class="hd">
                    <i class="vip-icon icon-credit">分</i>
                    <h2>会员积分规则</h2>
                    <time>{#value($jfcl,'auto_time_text')#}</time>
                    <span class="btn-menu btn-open">+</span>
                </div>
                <div class="bd">
                    {#value($jfcl,'shuoming')#}
                </div>
            </li>
            <li class="item">
                <div class="hd">
                    <i class="vip-icon icon-pre">商</i>
                    <h2>商家介绍</h2>
                    <time>{#value($shopset,'auto_time_text')#}</time>
                    <span class="btn-menu btn-open">+</span>
                </div>
                <div class="bd">
                    {#value($shopset,'content')#}
                </div>
            </li>
        </ul>
    </div>

    {#include file="./footer.tpl" _item=""#}

</div>


<script type="text/javascript">
    $(document).ready(function(){
        $("ul#ul-list li").click(function(){
            if ($(this).find(".hd span").text() == "-"){
                $(this).find(".hd span").text("+");
                $(this).find(".bd").hide();
            }else{
                $("ul#ul-list li .hd span").text("+");
                $("ul#ul-list li .bd").hide();
                $(this).find(".hd span").text("-");
                $(this).find(".bd").show();
            }
        });
    });
</script>
</body>
</html>