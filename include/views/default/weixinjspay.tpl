
<!DOCTYPE html>
<html>
<head lang="zh-CN">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta content="telephone=no" name="format-detection">
    <title>微信安全支付</title>
    <style type="text/css">body {margin: 0;}</style>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.min.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.alert.js"></script>
</head>

<body>
<script type="text/javascript">
    $(function(){
        $.alert("正在打开支付功能...",0,1);
        //
        if (typeof(wx) != "undefined") wx = null;
        var jssdkconfig = {#json_encode($_A.wx_jssdkConfig)#} || {};
        $.getScript("http://res.wx.qq.com/open/js/jweixin-1.0.0.js", function(){
            // 是否启用调试
            jssdkconfig.debug = false;
            //
            jssdkconfig.jsApiList = ['checkJsApi','chooseWXPay'];
            wx.config(jssdkconfig);
            wx.ready(function () {
                $.alert(0);
                wx.chooseWXPay({
                    timestamp: {#intval($wOpt['timeStamp'])#},
                    nonceStr: '{#$wOpt['nonceStr']#}',
                    package: '{#$wOpt['package']#}',
                    signType: '{#$wOpt['signType']#}',
                    paySign: '{#$wOpt['paySign']#}',
                    success: function (res) {
                        if(res.errMsg == 'chooseWXPay:ok') {
                            location.search += '&done=1';
                        } else {
                            //alert('启动微信支付失败, 请检查你的支付参数. 详细错误为: ' + res.err_msg);
                            history.go(-1);
                        }
                    }
                });
            });
        });
    });
    /*
    document.addEventListener('WeixinJSBridgeReady', function onBridgeReady() {
        $.alert(0);
        WeixinJSBridge.invoke('getBrandWCPayRequest', {
            'appId' : '{#$wOpt['appId']#}',
            'timeStamp': '{#$wOpt['timeStamp']#}',
            'nonceStr' : '{#$wOpt['nonceStr']#}',
            'package' : '{#$wOpt['package']#}',
            'signType' : '{#$wOpt['signType']#}',
            'paySign' : '{#$wOpt['paySign']#}'
        }, function(res) {
            if(res.err_msg == 'get_brand_wcpay_request:ok') {
                location.search += '&done=1';
            } else {
                //alert('启动微信支付失败, 请检查你的支付参数. 详细错误为: ' + res.err_msg);
                history.go(-1);
            }
        });
    }, false);
    */
</script>

</body>
</html>