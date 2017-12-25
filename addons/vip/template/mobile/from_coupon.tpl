
<!DOCTYPE HTML>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta content="telephone=no" name="format-detection">
    <title>优惠券 - 会员卡</title>
    <link rel="stylesheet" href="{#$NOW_PATH#}css/inner.css"/>
    <script type="text/javascript" src="{#$JS_PATH#}jquery-1.11.0.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.alert.js"></script>
</head>

<body>

<div class="layout">
    <div class="wrap">
        <div class="bd">
            <div class="vip-action" id="vip-formcode">
                <div class="vip-sn" id="vip-sn-text">优惠券: {#$rec.sn#}</div>
                <div class="vip-form">
                    <input type="text" id="money" placeholder="请输入实际消费金额">
                    <input type="text" id="money2" placeholder="请再次输入实际消费金额">
                    <input type="password" id="pass" placeholder="请输入管理员密码">
                    <button type="button" id="butapply" class="btn-submit vip-sbtn">确定使用</button>
                </div>
            </div>
            <div class="vip-usenum">
                {#if ($item.type_b=="打折优惠券")#}
                {#$item.type_b#}【<span>{#$item.int_a#}</span>折】<br>
                {#else#}
                {#$item.type_b#}【抵<span>{#$item.int_b#}</span>元】<br>
                {#/if#}
                此优惠券剩余【<span id="inum">{#$rec.num-$rec.usenum#}</span>张】<br>
                此优惠券有效期【<span>{#date("Y-m-d",$item['startdate'])#}</span>至<span>{#date("Y-m-d",$item['enddate'])#}</span>】
            </div>
            <h2 class="desc-t">详细说明</h2>
            <p>{#$item.content#}</p>
        </div>
    </div>
</div>


<script type="text/javascript">
    $(document).ready(function(){
        $("button#butapply").click(function(){
            var obj = $(this).parent();
            var money= obj.find("input#money").val();
            var money2= obj.find("input#money2").val();
            var pass= obj.find("input#pass").val();
            var sn= obj.parent().find("#vip-sn-text").text();
            if (money == ""){
                $.alert("消费金额不可留空！"); return;
            }
            if (money != money2){
                $.alert("两次金额输入不一致！"); return;
            }
            if (pass == ""){
                $.alert("管理员密码不可留空！"); return;
            }
            var s = "dosubmit=put&money="+money+"&pass="+pass;
            $.ajax({
                type: "POST",
                url: "{#appurl()#}",
                data: s,
                dataType: "json",
                success: function (msg) {
                    if (msg.success == "1"){
                        obj.parent().parent().find("span#inum").text(msg.tnum);
                        obj.parent().html('<div class="vip-miss">获得'+msg.tpoint+'积分，还需付款【'+msg.money+'】元！</div>');
                    }else if (msg.success == "0"){
                        $.alert(msg.message);
                    }else{
                        $.alert("提交失败！");
                    }
                },
                error: function (msg) {
                    $.alert("提交错误！");
                }
            });
        });
    });
</script>
</body>
</html>