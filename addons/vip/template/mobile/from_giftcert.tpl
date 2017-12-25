
<!DOCTYPE HTML>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta content="telephone=no" name="format-detection">
    <title>礼品券 - 会员卡</title>
    <link rel="stylesheet" href="{#$NOW_PATH#}css/inner.css"/>
    <script type="text/javascript" src="{#$JS_PATH#}jquery-1.11.0.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.alert.js"></script>
</head>

<body>

<div class="layout">
    <div class="wrap">
        <div class="bd">
            <div class="vip-action" id="vip-formcode">
                <div class="vip-sn" id="vip-sn-text">礼品券: {#$rec.sn#}</div>
                <div class="vip-form">
                    <input type="password" id="pass" placeholder="请输入管理员密码">
                    <button type="button" id="butapply" class="btn-submit vip-sbtn">确定使用</button>
                </div>
            </div>
            <div class="vip-usenum">
                兑换所需积分【<span>{#$item.int_c#}</span>】<br/>
                此礼品券剩余【<span id="inum">{#$rec.num-$rec.usenum#}</span>张】<br>
                此礼品券有效期【<span>{#date("Y-m-d",$item['startdate'])#}</span>至<span>{#date("Y-m-d",$item['enddate'])#}</span>】
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
            var pass= obj.find("input#pass").val();
            var sn= obj.parent().find("#vip-sn-text").text();
            if (pass == ""){
                $.alert("管理员密码不可留空！"); return;
            }
            var s = "dosubmit=put&pass="+pass;
            $.ajax({
                type: "POST",
                url: "{#appurl()#}",
                data: s,
                dataType: "json",
                success: function (msg) {
                    if (msg.success == "1"){
                        obj.parent().parent().find("span#inum").text(msg.tnum);
                        obj.parent().html('<div class="vip-miss">兑换成功，积分已扣除！</div>');
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