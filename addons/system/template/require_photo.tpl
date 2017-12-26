{#if $_GPC['showttjs']#}
<script type="text/javascript">
    {#/if#}

    define(['util'], function(util){
        var photo = {};

        photo.images = {
            localId: [],
            serverId: [],
            serverUrl: [],
            length: 0,
            maxWidth: 0,
            maxHeight: 0
        };

        photo.jssdkconfig = {#json_encode($_A.wx_jssdkConfig)#} || {};

        photo.choose = function(callback, result) {
            util.loading("加载中...");
            //
            photo.images.maxWidth = photo.images.maxHeight = 0;
            var wx_count = 1;
            if (typeof callback == "object") {
                result = callback.result;
                if (callback.count) {
                    wx_count = parseInt(callback.count);
                }
                if (callback.width) {
                    photo.images.maxWidth = callback.width;
                }
                if (callback.height) {
                    photo.images.maxHeight = callback.height;
                }
                if (callback.maxWidth) {
                    photo.images.maxWidth = callback.maxWidth;
                }
                if (callback.maxHeight) {
                    photo.images.maxHeight = callback.maxHeight;
                }
                callback = callback.callback;
            }
            var load = true;
            //微信
            {#if $_A['browser'] == 'weixin'#}
            var jssdkconfig = photo.jssdkconfig;
            require(['jweixin'], function(wx){
                // 是否启用调试
                jssdkconfig.debug = false;
                //
                jssdkconfig.jsApiList = ['chooseImage','uploadImage'];
                wx.config(jssdkconfig);
                wx.ready(function () {
                    if (!load) { return ; }
                    load = false;
                    wx.chooseImage({
                        count: wx_count,                            // 默认9
                        sizeType: ['original', 'compressed'],       // 可以指定是原图还是压缩图，默认二者都有
                        sourceType: ['album', 'camera'],            // 可以指定来源是相册还是相机，默认二者都有
                        success: function (res) {
                            photo.images.localId = res.localIds;
                            photo.images.length = photo.images.localId.length;
                            photo.images.length = photo.images.localId.length;
                            photo.images.serverId = photo.images.serverUrl = [];
                            photo.syncUpload(0, callback, wx);
                        },cancel: function () {
                            if (typeof callback == 'function') {
                                callback(false);
                            }
                        },fail: function() {
                            if (typeof result == 'function') {
                                //加载失败 转到普通上传
                                photo.upimg(callback);
                            }
                        }
                    });
                    if (typeof result == 'function') {
                        result(true);
                    }
                });
                util.loaded();
            });
            //服务窗
            {#elseif $_A['browser'] == 'alipay'#}
            if (navigator.userAgent.indexOf("AlipayClient") !== -1){
                require(['jalipay'], function(Ali){
                    if (!load) { return ; }
                    load = false;
                    if ((Ali.alipayVersion).slice(0,3)>=8.1){
                        Ali.call('photo', {
                            dataType: 'dataURL',
                            imageFormat: 'png',
                            allowEdit: false
                        }, function (result) {
                            if (result.errorCode){
                                if (result.errorCode == 11){
                                    //操作失败（权限不够） 转到普通上传
                                    photo.upimg(callback);
                                }else{
                                    if (typeof callback == 'function') {
                                        callback(false);
                                    }
                                }
                            }else{
                                util.loading('上传中...');
                                $.ajax({
                                    type: 'POST',
                                    url: '{#get_link('act')#}',
                                    data : {
                                        'act': 'alupload',
                                        'photo': result.dataURL,
                                        'width': photo.images.maxWidth,
                                        'height': photo.images.maxHeight
                                    },
                                    dataType: 'json',
                                    success : function(data){
                                        util.loaded();
                                        if (data != null && data.success != null && data.success) {
                                            if (typeof callback == 'function') {
                                                callback(data.message);
                                            }
                                        }else{
                                            if (data.message) {
                                                alert(data.message);
                                            }
                                            if (typeof callback == 'function') {
                                                callback(false);
                                            }
                                        }
                                    },error : function () {
                                        util.loaded();
                                        if (typeof callback == 'function') {
                                            callback(false);
                                        }
                                    }
                                });
                            }
                        });
                        if (typeof result == 'function') {
                            result(true);
                        }
                    }else{
                        if (typeof result == 'function') {
                            result(false);
                        }
                    }
                    util.loaded();
                });
            }
            //其他
            {#else#}
            if (!load) { return ; }
            load = false;
            photo.upimg(callback);
            if (typeof result == 'function') {
                result(true);
            }
            util.loaded();
            {#/if#}
        };

        photo.upimg = function(callback) {
            var inputid = '__photo-upimg-input';
            var inputobj = $('#' + inputid);
            if(inputobj.length == 0) {
                $(document.body).append('<div id="' + inputid + '" style="position:fixed;z-index:999999;top:0;left:0;width:100%;height:100%;"></div>');
                inputobj = $('#' + inputid);
                var html = '' +
                        '<input type="file" id="' + inputid + '_file" name="' + inputid + '_file" accept="image/jpeg,image/png,image/gif" style="display:none;">' +
                        '<div style="position: absolute;z-index:1;top:0;left:0;width:100%;height:100%;background:rgba(0, 0, 0, 0.3);"></div>' +
                        '<div style="text-align:center;background-color:rgba(0, 0, 0, 0.2);position:absolute;z-index:2;top:100px;left:0;margin:0;width:auto;border-radius:5px;">' +
                        '	<span style="display:block;width:160px;height:60px;line-height:60px;color:#ffffff;">选择图片</span>' +
                        '	<span style="display:block;width:160px;height:60px;line-height:60px;color:#ffffff;border-top:1px solid #cccccc;">取消上传</span>' +
                        '</div>';
                inputobj.html(html);
                inputobj.find("span").eq(0).click(function(){
                    inputobj.find("input").click();
                    inputobj.hide();
                });
                inputobj.find("span").eq(1).click(function(){
                    inputobj.hide();
                });
            }
            inputobj.show();
            inputobj.find("div").eq(1).css({
                top: ($(window).height() - inputobj.find("div").eq(1).height()) / 2,
                left: ($(window).width() - inputobj.find("div").eq(1).width()) / 2
            });
            inputobj.find("input").unbind("change").change(function(){
                util.loading('上传中...');
                require(['jquery','ajaxfileupload'], function($){
                    $.ajaxFileUpload({
                        url: '{#get_link('act')#}',
                        data : {
                            'act': 'elupload',
                            'width': photo.images.maxWidth,
                            'height': photo.images.maxHeight
                        },
                        secureuri: false,
                        fileElementId: inputid + '_file',
                        dataType: 'json',
                        success: function (data) {
                            util.loaded();
                            if (data != null && data.success != null && data.success) {
                                if (typeof callback == 'function') {
                                    callback(data.message);
                                }
                            }else{
                                if (data.message) {
                                    alert(data.message);
                                }
                                if (typeof callback == 'function') {
                                    callback(false);
                                }
                            }
                        },error: function () {
                            util.loaded();
                            if (typeof callback == 'function') {
                                callback(false);
                            }
                        }
                    });
                });
            });
        };

        photo.syncUpload = function(i, callback, wx){
            util.loading('上传中...');
            wx.uploadImage({
                localId: photo.images.localId[i],
                isShowProgressTips: 0,
                success: function (res) {
                    i++;
                    photo.images.serverId.push(res.serverId);
                    if (i < photo.images.length) {
                        photo.syncUpload(i, callback, wx);
                    }else{
                        $.ajax({
                            type: 'POST',
                            url: '{#get_link('act')#}',
                            data : {
                                'act': 'wxupload',
                                'photo': JSON.stringify(photo.images.serverId),
                                'width': photo.images.maxWidth,
                                'height': photo.images.maxHeight
                            },
                            dataType: 'json',
                            success : function(data){
                                util.loaded();
                                if (data != null && data.success != null && data.success) {
                                    if (typeof callback == 'function') {
                                        callback(data.message);
                                    }
                                }else{
                                    if (data.message) {
                                        alert(data.message);
                                    }
                                    if (typeof callback == 'function') {
                                        callback(false);
                                    }
                                }
                            },error : function () {
                                util.loaded();
                                if (typeof callback == 'function') {
                                    callback(false);
                                }
                            }
                        });
                    }
                }
            });
        };

        return photo;
    });

    {#if $_GPC['showttjs']#}
</script>
{#/if#}