
<!DOCTYPE HTML>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta content="telephone=no" name="format-detection">
    <title>每日签到 赚积分</title>
    <link rel="stylesheet" href="{#$NOW_PATH#}css/inner.css">
    <script type="text/javascript" src="{#$JS_PATH#}jquery.min.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.alert.js"></script>
    <style>
        .btn-sign a{display: block; color: #fff; line-height: 48px;}
    </style>
</head>

<body>
<div class="layout">
    <header>
        <img src="{#vippagebanner('sign_in', $_A['vip_data']['banner'])#}" class="hd-banner">
    </header>
    <div class="wrap">
        <div class="sign-in">
            {#if $sign#}
            <button type="button" class="btn-sign btn-yes">今日已签到</button>
            {#else#}
            <button class="btn-sign btn-no"><a href="javascript:;" onclick="submit();">点击这里签到赚积分</a></button>
            {#/if#}
            {#if value($jfcl,'lianxu')#}
            <h2>每日签到可获{#value($jfcl,'meiri')#}积分，连续签到{#value($jfcl,'lianxu')#}天可获{#value($jfcl,'lianxuval')#}积分</h2>
            {#/if#}
            <div class="vip-credit">
                <table class="vip-table">
                    <tbody><tr>
                        <th>剩余积分</th>
                        <th>签到积分</th>
                        <th>消费积分</th>
                    </tr>
                    <tr>
                        <td>{#value($_A['vip'],'point')#}</td>
                        <td>{#value($_A['vip'],'inpoint')#}</td>
                        <td>{#value($_A['vip'],'outpoint')#}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <h2>签到积分详情</h2>
            <div class="vip-credit">
                <table class="vip-table">
                    <tbody>
                    <tr>
                        <th>日期</th>
                        <th>签到</th>
                        <th>积分</th>
                    </tr>
                    {#foreach from=$sublist item=list#}
                    <tr>
                        <td>{#$list.a#}</td>
                        <td><span class="yes">已签到</span></td>
                        <td>{#$list.b#}</td>
                    </tr>
                    {#foreachelse#}
                    <tr>
                        <td colspan="3" align="center" class="align-center">
                            <div>本月无签到记录</div>
                        </td>
                    </tr>
                    {#/foreach#}

                    <tr class="total">
                        <td>本月合计</td>
                        <td></td>
                        <td>{#$sublistz#}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {#include file="./footer.tpl" _item=4#}

</div>

<script type="text/javascript">
    //
    function submit(){
        var s = "dosubmit=submit";
        $.alert("正在签到...", 0, 1);
        $.ajax({
            type: "POST",
            url: "{#appurl()#}",
            data: s,
            dataType: "json",
            success: function (msg) {
                $.alert(msg.message);
                if (msg.success == "1"){
                    window.location.href = "{#appurl()#}";
                }
            },
            error: function (msg) {
                $.alert("签到错误！");
            }
        });
    }
</script>

</body>
</html>