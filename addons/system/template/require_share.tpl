{#if $_GPC['showttjs']#}
<script type="text/javascript">
    {#/if#}

    jQuery.share = function(title, desc, img, url, callback, result) {
        var sharedebug = false;
        if (typeof title == "object") {
            desc = title.desc;
            img = title.img;
            url = title.url;
            callback = title.call?title.call:title.callback;
            result = title.result;
            if (title.debug === true) { sharedebug = true; }
            title = title.title;
        }
        if (!url) { url = document.URL; }
        if (!title) { title = $("title:eq(0)").text(); }
        if (!desc) { desc = $("body").text().trim(); }
        if (desc && (desc.substring(0,1) == '.' || desc.substring(0,1) == '#')) {
            if ($(desc).text()) { desc = $(desc).text().replace(/^\s+|\s+$/g,""); }
        }
        if (img && (img.substring(0,1) == '.' || img.substring(0,1) == '#')) {
            if ($(img).find("img:eq(0)").attr("src")) { img = $(img).find("img:eq(0)").attr("src"); }
        }
        //微信分享
        {#if $_A['browser'] == 'weixin'#}
        var jssdkconfig = {#json_encode($_A.wx_jssdkConfig)#} || {};
        require(['jweixin'], function(wx){
            // 是否启用调试
            jssdkconfig.debug = sharedebug;
            //
            jssdkconfig.jsApiList = ['checkJsApi','onMenuShareTimeline','onMenuShareAppMessage','onMenuShareQQ','onMenuShareWeibo'];
            wx.config(jssdkconfig);
            wx.ready(function () {
                var shareData = {
                    imgUrl : img,
                    link : url,
                    title : title,
                    desc : desc,
                    success: function(res) {
                        if (typeof callback == 'function') {
                            if (res.errMsg.indexOf("AppMessage:ok") !== -1) {
                                callback("AppMessage");
                            }else if (res.errMsg.indexOf("Timeline:ok") !== -1) {
                                callback("Timeline");
                            }else if (res.errMsg.indexOf("QQ:ok") !== -1) {
                                callback("QQ");
                            }else if (res.errMsg.indexOf("Weibo:ok") !== -1) {
                                callback("Weibo");
                            }else{
                                callback(false);
                            }
                        }
                    },cancel: function () {
                        if (typeof callback == 'function') {
                            callback(false);
                        }
                    },fail: function() {
                        if (typeof result == 'function') {
                            callback(false);
                        }
                    }
                };
                wx.onMenuShareAppMessage(shareData);
                wx.onMenuShareTimeline(shareData);
                wx.onMenuShareQQ(shareData);
                wx.onMenuShareWeibo(shareData);
                if (typeof result == 'function') {
                    result(true);
                }
            });
        });
        {#/if#}
        //服务窗分享
        {#if $_A['browser'] == 'alipay'#}
        if (navigator.userAgent.indexOf("AlipayClient") !== -1){
            $("meta:last").after('<meta name="Alipay:title" content="'+title+'"/>' +
                    '<meta name="Alipay:imgUrl" content="'+img+'"/>' +
                    '<meta name="Alipay:desc" content="'+desc+'"/>');
            require(['jalipay'], function(Ali){
                var param = {
                    title: title,
                    content: desc,
                    imageUrl: img,
                    captureScreen: true,
                    url: url
                };
                if ((Ali.alipayVersion).slice(0,3)>=8.1){
                    Ali.call('setToolbarMenu',{
                        menus:[
                            {name:"分享",tag:"share",icon:"{#$NOW_PATH#}images/ali-menu-share.png"},
                            {name:"复制链接",tag:"copyurl",icon:"{#$NOW_PATH#}images/ali-menu-copyurl.png"},
                            {name:"刷新",tag:"break",icon:"{#$NOW_PATH#}images/ali-menu-break.png"},
                            {name:"在浏览器中打开",tag:"openbrowser",icon:"{#$NOW_PATH#}images/ali-menu-openbrowser.png"},
                            {name:"退出",tag:"exit",icon:"{#$NOW_PATH#}images/ali-menu-exit.png"}
                        ],
                        override: true
                    }, function () {
                        document.addEventListener('toolbarMenuClick', function (e) {
                            if (e.data.tag == 'share') {
                                Ali.call('share', {
                                    'bizType':"testShareBizType",
                                    'keepOrder':false,
                                    'channels': [{
                                        name: 'ALPContact',         //支付宝联系人
                                        param: {
                                            contentType: '1002',
                                            content: desc,          //必选参数,分享描述
                                            iconUrl: img,           //必选参数,缩略图url，发送前预览使用
                                            url: url,               //必选参数，卡片跳转连接
                                            title: title,           //必选参数,分享标题(contentType为1003时不需要填)
                                            imageUrl: img,          //必选参数，大图url，和iconUrl应该是一张图的大图形式，在会话流里显示使用
                                            memo: "",               //必选参数,分享成功后，在联系人界面的通知提示。
                                            bizType: "",            //必选参数,表示业务类型，不超过16个字符，分享前联系@笑六 确认重复性。
                                            sign: "",               //必选参数,签名字符串。防止分享的数据被更改，由联系人的服务端 @笑六 生成
                                            maxSelect: "99",        //可选参数，最大分享几个人，设空的话，默认为1
                                            egg: "",                //可选参数，彩蛋ID，由 @松雪 配置
                                            otherParams:{           //可选参数，额外的分享入参
                                            }
                                        }
                                    },{
                                        name: 'Weibo',              //新浪微博
                                        param: param
                                    },{
                                        name: 'Weixin',             //微信
                                        param: param
                                    }, {
                                        name: 'WeixinTimeLine',     //微信朋友圈
                                        param: param
                                    }]
                                },function(result){
                                    if (result.errorCode){
                                        if (typeof callback == 'function') {
                                            callback(false);
                                        }
                                    }else{
                                        if (typeof callback == 'function') {
                                            callback(result.channelName);
                                        }
                                    }
                                });
                            }else if (e.data.tag == 'copyurl'){
                                Ali.call('share', {
                                    'bizType':"testShareBizType",
                                    'keepOrder':false,
                                    'channels': [{
                                        name: 'CopyLink',           //复制链接
                                        param: { url: url }
                                    }]
                                });
                            }else if (e.data.tag == 'break'){
                                window.location.reload();
                            }else if (e.data.tag == 'openbrowser'){
                                Ali.call('openInBrowser', { url: url });
                            }else if (e.data.tag == 'exit'){
                                Ali.call('exitApp');
                            }
                        }, false);
                    });
                    if (typeof result == 'function') {
                        result(true);
                    }
                }else{
                    if (typeof result == 'function') {
                        result(false);
                    }
                }
            });
        }
        {#/if#}
    };


    {#if $_GPC['showttjs']#}
</script>
{#/if#}