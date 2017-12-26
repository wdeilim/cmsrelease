{#if $_GPC['showttjs']#}
<script type="text/javascript">
    {#/if#}

    define(['util','css!{#$NOW_PATH#}css/require_photo.css'], function(util){
        var photo = {};

        photo.images = {
            localId: [],
            serverId: [],
            serverUrl: [],
            length: 0,
            maxWidth: 0,
            maxHeight: 0,
            cropper:false
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
                $(document.body).append('<div id="' + inputid + '" class="require_photo_upimg"></div>');
                inputobj = $('#' + inputid);
                var html =
                        '<input type="file" id="' + inputid + '_file" name="' + inputid + '_file" accept="image/jpeg,image/png,image/gif">' +
                        '<div class="upimg_1"></div>' +
                        '<div class="upimg_2">' +
                        '	<span class="upimg_3">添加上传图片</span>' +
                        '	<span class="upimg_4">浏览已传图片</span>' +
                        '</div>' +
                        '<div class="upimg_5"></div>';
                inputobj.html(html);
                inputobj.find("input").hide();
                inputobj.find(".upimg_3").click(function(){
                    inputobj.find("input").click();
                    inputobj.hide();
                });
                inputobj.find(".upimg_4").click(function(){
                    inputobj.hide();
                    photo.upimg_view(callback, 1);
                });
                inputobj.find(".upimg_5").click(function(){
                    inputobj.hide();
                });
                {#if empty($_A['openid'])#}
                inputobj.find(".upimg_4").hide();
                {#/if#}
            }
            inputobj.show();
            inputobj.find(".upimg_2").css({
                top: ($(window).height() - inputobj.find(".upimg_2").height()) / 2,
                left: ($(window).width() - inputobj.find(".upimg_2").width()) / 2
            });
            inputobj.find(".upimg_5").css({
                top: ($(window).height() - inputobj.find(".upimg_2").height()) / 2,
                right: ($(window).width() - inputobj.find(".upimg_2").width()) / 2
            });
            inputobj.find("input").unbind("change").change(function(){
                util.loading('上传中...');
                require(['jqueryload','ajaxfileupload'], function($){
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
                                if (photo.images.cropper === true) {
                                    photo.upimg_cropper(callback, data.message);
                                }else{
                                    if (typeof callback == 'function') {
                                        callback(data.message);
                                    }
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

        photo.upimg_cropper = function(callback, path) {
            var cropperid = 'photo-modal-upimgcropper';
            var cropperidobj = $('#' + cropperid);
            if (cropperidobj.length == 0) {
                $(document.body).append('<div id="' + cropperid + '" class="require_photo_upimgcropper"></div>');
                cropperidobj = $('#' + cropperid);
                var html =
                        '<div class="cropper_1"></div>' +
                        '<div class="cropper_2">'+
                        '	<img class="cropper_3" src="'+util.tomedia('caches/statics/images/loading.gif')+'" title="正在努力加载...">' +
                        '	<div class="cropper_4">' +
                        '       <div class="cropper_5"></div>' +
                        '       <div class="cropper_6"></div>' +
                        '   </div>' +
                        '   <div class="cropper_7" data-path=""><em>确　定</em></div>' +
                        '</div>' +
                        '<div class="cropper_8"></div>';
                cropperidobj.html(html);
                cropperidobj.find(".cropper_2").css({'height': $(window).height() - 40});
                cropperidobj.find(".cropper_4").css({'height': $(window).height() - 66 - cropperidobj.find(".cropper_7").height()});
                cropperidobj.find(".cropper_5").css({'height': cropperidobj.find(".cropper_5").width()});
                cropperidobj.find(".cropper_8").click(function(){ cropperidobj.hide(); });
            }
            var $image = $('<img style="display:none;" src="" title="正在努力加载...">');
            cropperidobj.find(".cropper_5").html($image);
            cropperidobj.show();
            //
            require(["cropper.min"], function(cropper) {
                $image.attr("src", util.tomedia(path[0]));

                var options = {
                    aspectRatio: NaN,
                    crop: function (e) {
                    }
                };
                // Cropper
                $image.on({
                    'build.cropper': function (e) { },
                    'built.cropper': function (e) { },
                    'cropstart.cropper': function (e) { },
                    'cropmove.cropper': function (e) { },
                    'cropend.cropper': function (e) { },
                    'crop.cropper': function (e) { },
                    'zoom.cropper': function (e) { }
                }).cropper(options);
                //
                var $cropusehtml = $('<div class="c_ul">'+
                        '    <div>'+
                        '        <button type="button" data-method="setDragMode" data-option="move" title="Move">移动</button>'+
                        '        <button type="button" data-method="setDragMode" data-option="crop" title="Crop">裁剪</button>'+
                        '    </div>'+
                        '    <div>'+
                        '        <button type="button" data-method="zoom" data-option="0.1" title="Zoom In">放大</button>'+
                        '        <button type="button" data-method="zoom" data-option="-0.1" title="Zoom Out">缩小</button>'+
                        '    </div>'+
                        '    <div>'+
                        '        <button type="button" data-method="move" data-option="-10" data-second-option="0" title="Move Left">左移</button>'+
                        '        <button type="button" data-method="move" data-option="10" data-second-option="0" title="Move Right">右移</button>'+
                        '        <button type="button" data-method="move" data-option="0" data-second-option="-10" title="Move Up">上移</button>'+
                        '        <button type="button" data-method="move" data-option="0" data-second-option="10" title="Move Down">下移</button>'+
                        '    </div>'+
                        '    <div>'+
                        '        <button type="button" data-method="rotate" data-option="-45" title="Rotate Left">逆转</button>'+
                        '        <button type="button" data-method="rotate" data-option="45" title="Rotate Right">顺转</button>'+
                        '    </div>'+
                        '    <div>'+
                        '        <button type="button" data-method="scaleX" data-option="-1" title="Flip Horizontal">水平翻转</button>'+
                        '        <button type="button" data-method="scaleY" data-option="-1" title="Flip Vertical">垂直翻转</button>'+
                        '    </div>'+
                        '    <div>'+
                        '        <button type="button" data-method="reset" title="Reset">重置</button>'+
                        '    </div>'+
                        '</div>');
                $cropusehtml.on('click', '[data-method]', function () {
                    var $this = $(this);
                    var data = $this.data();
                    var $target;
                    var result;

                    if ($this.prop('disabled') || $this.hasClass('disabled')) {
                        return;
                    }

                    if ($image.data('cropper') && data.method) {
                        data = $.extend({}, data); // Clone a new one

                        if (typeof data.target !== 'undefined') {
                            $target = $(data.target);

                            if (typeof data.option === 'undefined') {
                                try {
                                    data.option = JSON.parse($target.val());
                                } catch (e) {
                                    console.log(e.message);
                                }
                            }
                        }

                        result = $image.cropper(data.method, data.option, data.secondOption);

                        switch (data.method) {
                            case 'scaleX':
                            case 'scaleY':
                                $(this).data('option', -data.option);
                                break;
                        }

                        if ($.isPlainObject(result) && $target) {
                            try {
                                $target.val(JSON.stringify(result));
                            } catch (e) {
                                console.log(e.message);
                            }
                        }

                    }
                });
                cropperidobj.find(".cropper_6").html($cropusehtml);
                cropperidobj.find(".cropper_7").unbind('click').click(function(){
                    if (typeof callback == 'function') {
                        callback([$image.cropper("getCroppedCanvas").toDataURL()]);
                    }
                    cropperidobj.hide();
                });
            });
        };

        photo.upimg_view = function(callback, page) {
            //
            var viewid = 'photo-modal-upimgview';
            var viewidobj = $('#' + viewid);
            if (viewidobj.length == 0) {
                $(document.body).append('<div id="' + viewid + '" class="require_photo_upimgview"></div>');
                viewidobj = $('#' + viewid);
                var html =
                        '<div class="view_1"></div>' +
                        '<div class="view_2">'+
                        '	<img class="view_3" src="'+util.tomedia('caches/statics/images/loading.gif')+'" title="正在努力加载...">' +
                        '	<div class="view_4">' +
                        '       <div class="view_5"></div>' +
                        '       <div class="view_6">加载更多...</div>' +
                        '   </div>' +
                        '   <div class="view_7" data-path=""><em>确　定</em></div>' +
                        '</div>' +
                        '<div class="view_8"></div>';
                viewidobj.html(html);
                viewidobj.find(".view_2").css({'height': $(window).height() - 40});
                viewidobj.find(".view_4").css({'height': $(window).height() - 66 - viewidobj.find(".view_7").height()});
                viewidobj.find(".view_7").click(function(){
                    var path = $(this).attr("data-path");
                    if (path) {
                        if (photo.images.cropper === true) {
                            photo.upimg_cropper(callback, [path]);
                        }else{
                            if (typeof callback == 'function') {
                                callback([path]);
                            }
                        }
                        $(this).attr("data-path", "");
                        viewidobj.hide();
                    }else{
                        util.alertk("没有选择任何图片！");
                    }

                });
                viewidobj.find(".view_8").click(function(){ viewidobj.hide(); });
            }
            var viewimg = viewidobj.find(".view_3");
            var viewhtml = viewidobj.find(".view_5");
            viewidobj.find(".view_6").unbind("click").click(function(){
                $(this).text("加载中...");
                page++;
                photo.upimg_view(callback, page);
            });
            if (page <= 1) {
                photo.images.upimg_view_ymd = '';
                viewhtml.html("").hide();
            }
            viewidobj.show();
            $.ajax({
                type: 'POST',
                url: '{#get_link('act')#}',
                data : {
                    'act': 'viupload',
                    'page': page?page:1
                },
                dataType: 'json',
                success : function(data){
                    viewimg.hide();
                    viewhtml.show();
                    if (data != null && data.success != null && data.success) {
                        if (data.nextpage) {
                            viewidobj.find(".view_6").text("加载更多...").show();
                        }else{
                            viewidobj.find(".view_6").text("加载更多...").hide();
                        }
                        var $divtemp;
                        var viewwidth = parseInt(viewhtml.width() * 0.25);
                        $.each(data.message, function(n, value){
                            if (photo.images.upimg_view_ymd != value.ymd) {
                                photo.images.upimg_view_ymd = value.ymd;
                                $divtemp = $('<div class="v_ymd">' + value.ymd + '</div>');
                                viewhtml.append($divtemp);
                            }
                            $divtemp = $('<div class="v_li"><div><span><img src="' + value.thumb + '"></span><em></em></div></div>');
                            $divtemp.find("div").css({
                                'width':viewwidth - 12,
                                'height':viewwidth - 12
                            });
                            $divtemp.find("img").css({
                                'max-width':viewwidth - 12 - 12,
                                'max-height':viewwidth - 12 - 12
                            });
                            $divtemp.click(function(){
                                viewhtml.find(".select").removeClass('select');
                                $(this).addClass('select');
                                viewidobj.find(".view_7").attr("data-path", value.path[0]);
                            });
                            viewhtml.append($divtemp);
                        });
                    }else{
                        if (page > 1) {
                            alert("已加载所有图片！");
                        }else{
                            viewhtml.html("<div class='v_tis'>没有找到已传图片！</div>");
                        }
                    }
                },error : function () {
                    viewimg.hide();
                    viewhtml.html("<div class='v_tis'>查找已传图片失败！</div>").show();
                }
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