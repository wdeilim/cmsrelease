$.__aliedit_fun = {
    loadcss: false,
    styleOnload: function (node, callback) {
        // for IE6-9 and Opera
        if (node.attachEvent) {
            node.attachEvent('onload', callback);
            // NOTICE:
            // 1. "onload" will be fired in IE6-9 when the file is 404, but in
            // this situation, Opera does nothing, so fallback to timeout.
            // 2. "onerror" doesn't fire in any browsers!
        }
        // polling for Firefox, Chrome, Safari
        else {
            setTimeout(function() {
                $.__aliedit_fun.poll(node, callback);
            }, 0); // for cache
        }
    },
    poll: function (node, callback) {
        if (callback.isCalled) {
            return;
        }
        var isLoaded = false;
        if (/webkit/i.test(navigator.userAgent)) {//webkit
            if (node['sheet']) {
                isLoaded = true;
            }
        }
        // for Firefox
        else if (node['sheet']) {
            try {
                if (node['sheet'].cssRules) {
                    isLoaded = true;
                }
            } catch (ex) {
                // NS_ERROR_DOM_SECURITY_ERR
                if (ex.code === 1000) {
                    isLoaded = true;
                }
            }
        }
        if (isLoaded) {
            // give time to render.
            setTimeout(function() {
                callback();
            }, 1);
        }
        else {
            setTimeout(function() {
                $.__aliedit_fun.poll(node, callback);
            }, 1);
        }
    },
    path: window['_jquery_aliedit'] || (function (script, i, me) {
        for (i in script) {
            if (script[i].src && script[i].src.indexOf('jquery.aliedit') !== -1) me = script[i];
        };
        _thisScript = me || script[script.length - 1];
        me = _thisScript.src.replace(/\\/g, '/');
        return me.lastIndexOf('/') < 0 ? '.' : me.substring(0, me.lastIndexOf('/'));
    }(document.getElementsByTagName('script'))),
    alert: function(e, t, s) {
        $("div.jQuery-form-alert").remove();
        if (e === 0) return;
        $intemp = $('<div class="jQuery-form-alert" style="display:none;position:fixed;top:0;left:0;padding:3px 30px;min-width:100px;min-height:25px;text-align:center;color:#fff;z-index:2147483647;border-radius:0 0 4px 4px;background-color:#2378cd;font-size:14px; line-height:22px;">' + e + '</div>');
        $("body").append($intemp);
        var i = $(window).width(),
            o = $intemp.width()+20,
            l = (i - o) / 2;
        i > o && $intemp.css("left", parseInt(l)),
            i > o && $intemp.css("right", parseInt(l)),
            l < 5 && $intemp.css("margin", "0 5px");
        setTimeout(function() { $intemp.show(); }, s || 1);
        if (t === 0) return;
        setTimeout(function() { $intemp.fadeOut(); }, t || 2000)
    },
    materialshow: function(callback, callback2, paramet){
        var tthis = this;
        tthis.materialclose();
        var intemp = $('<div class="jQuery-formfile">' +
            '<div class="jQuery-form-back"></div>' +
            '<div class="jQuery-form-content">' +
            '<div class="formfile-title"><em id="f-cancel">×</em><span class="formfile-call"></span>选择图文广播素材</div>' +
            '<div class="formfile-item"><div class="formfile-load">正在加载...</div></div>' +
            '<div class="formfile-button"><p class="hover" id="f-confirm">确定</p><p id="f-cancel">取消</p></div> ' +
            '</div>' +
            '</div>');
        //背景
        intemp.find(".jQuery-form-back").css({
            "height":$(document).height()
        }).click(function(){
            tthis.materialout(callback2);
        });
        //点击关闭 取消
        intemp.find("#f-cancel").click(function(){
            tthis.materialout(callback2);
        });
        //调整宽度
        var w = $(document).width() * 0.8;
        if (w > 1220) w = 1220;
        var wl = (w / 2) * -1;
        intemp.find(".jQuery-form-content").css({width:w,marginLeft:wl});
        //点击确定
        intemp.find("#f-confirm").click(function(){
            tthis.materialcall("请点击列表选择");
        });
        //显示
        $("body").append(intemp);
        //加载列表
        tthis.materialbrowser(null, 1, callback, paramet);
    },
    materialbrowser: function(obj, page, callback, paramet){
        var tthis = this;
        var eve = $("div.jQuery-formfile").find(".formfile-item"),fileurl;
        if (eve) {
            if (callback) {
                window.aliformfile_callback = callback;
            }else{
                callback =  window.aliformfile_callback;
            }
            if (obj) {
                if (callback) callback(obj);
                tthis.materialclose();
                return true;
            }
            if (paramet) {
                fileurl = paramet.home + "web/library/alieditlist/?l="+paramet.alid;
                window.aliformfile_fileurl = fileurl;
            }else{
                fileurl =  window.aliformfile_fileurl;
            }
            if (obj === 0) {
                fileurl+= "&keyval="+page;
            }else{
                fileurl+= "&page="+page;
            }
            $.ajax({
                type: "GET",
                url: fileurl,
                dataType: "html",
                success: function (html) {
                    $intemp = $(html);
                    if (paramet.material_val) {
                        $intemp.find("#appmsg-col-" + paramet.material_val).find(">div.appmsg").css({
                            'border': '1px solid rgb(255, 0, 0)',
                            'box-shadow': 'rgb(255, 0, 0) 0px 1px 8px'
                        });
                    }
                    eve.html($intemp);
                },
                error: function (msg) {
                    tthis.alert("加载失败");
                    tthis.materialclose();
                }
            });
        }
    },
    materialcall: function(msg, hide, showk){
        var eve = $("div.jQuery-formfile").find(".formfile-call");
        if (eve) {
            if (msg) {
                eve.html(msg).show();
                if (showk) {
                    setTimeout(function(){eve.hide();}, 200);
                    setTimeout(function(){eve.show();}, 400);
                    setTimeout(function(){eve.hide();}, 600);
                    setTimeout(function(){eve.show();}, 800);
                }
                if (!hide) {
                    setTimeout(function(){
                        eve.html(msg).fadeOut();
                    }, 1000);
                }
            }else{
                eve.html("").hide();
            }
        }
    },
    materialout: function(callback2){
        var tthis = this;
        var eve = $("div.jQuery-formfile").find(".jQuery-form-content");
        if (eve.length > 0) {
            eve.animate({top:(eve.outerHeight() + 20) * -1 + "px"}, 300, '', function(){
                tthis.materialclose(callback2);
            });
        }
    },
    materialclose: function(callback2) {
        $("div.jQuery-formfile").remove();
        if (callback2) callback2();
    },
    deleteImageDialog: function(obj) {
        $(obj).prev().attr("src", "nopic.jpg");
        $(obj).parent().parent().find("input").val("");

    },
    showImageDialog: function(obj, home, paramet) {
        var tthis = this;
        if (!window.TPL_INIT_IMAGE) {
            if (window.TPL_INIT_IMAGE_B) {
                alert("正在加载..."); return false;
            }
            window.TPL_INIT_IMAGE_B = true;
            window.TPL_INIT_FORMFILE_PATH = $.__aliedit_fun.path+'/../formfile/jquery.formfile.js';
            $.getScript(window.TPL_INIT_FORMFILE_PATH, function(data, status, jqxhr) {
                window.TPL_INIT_IMAGE = true;
                tthis.showImageDialog(obj, home, paramet);
            });
            return false;
        }
        var btn = $(obj);
        var ipt = btn.prev();
        var val = ipt.val();
        var img = ipt.parent().find(".aibimg-aliedit").find("img");

        $.formfile(function(url){
            if(paramet==='local' && url.path.substring(0,12) != 'uploadfiles/') {
                alert("错误的图片地址：只能选择空间图片或上传图片！");
                ipt.val("");
                ipt.attr("path","");
                if(img.length > 0){
                    img.get(0).src = '';
                }
                return false;
            }
            if(url.path){
                ipt.val(url.path);
                ipt.attr("path",url.path);
            }
            if(url.fullpath){
                if (url.fullpath.substring(0,12) == 'uploadfiles/') {
                    url.fullpath = $.__aliedit_fun.paramet.BASE_URI + url.fullpath;
                }
                if(img.length > 0){
                    img.get(0).src = url.fullpath;
                }
                ipt.attr("url",url.fullpath);
            }
        }, val, home);
    },
    onblurImageDialog: function(obj) {
        var btn = $(obj);
        var img = btn.parent().find(".aibimg-aliedit").find("img");
        if(img.length > 0){
            var val = btn.val();
            if (val.substring(0,12) == "uploadfiles/") { val = $.__aliedit_fun.paramet.BASE_URI + val; }
            else if (val.substring(0,1) == "/" && val.substring(0,4) != "http") { val = $.__aliedit_fun.paramet.BASE_URI + val; }
            if (!val) val = "nopic.jpg";
            img.attr("src", val);
        }
    },
    /**
     *
     * @param obj
     * @param home
     * @param paramet
     * @param type 类型：images/videos/voices
     * @param allowed 格式限制
     * @param size 大小限制KB
     * @returns {boolean}
     */
    showFileDialog: function(obj, home, paramet, type, allowed, size) {
        var tthis = this;
        if (!window.TPL_INIT_IMAGE) {
            if (window.TPL_INIT_IMAGE_B) {
                alert("正在加载...");
                return false;
            }
            window.TPL_INIT_IMAGE_B = true;
            window.TPL_INIT_FORMFILE_PATH = $.__aliedit_fun.path + '/../formfile/jquery.formfile.js';
            $.getScript(window.TPL_INIT_FORMFILE_PATH, function (data, status, jqxhr) {
                window.TPL_INIT_IMAGE = true;
                tthis.showFileDialog(obj, home, paramet, type, allowed, size);
            });
            return false;
        }
        var btn = $(obj);
        var ipt = btn.parent().find("input").eq(0);
        var val = ipt.val();
        var img = ipt.parent().find(".aibimg-aliedit").find("img");

        $.formfile(function(url){
            if(paramet==='local' && url.path.substring(0,12) != 'uploadfiles/') {
                alert("错误的文件地址：只能选择空间文件或上传文件！");
                ipt.val("");
                ipt.attr("path","");
                if(img.length > 0){
                    img.get(0).src = '';
                }
                return false;
            }
            console.log(url.path);
            if(url.path){
                ipt.val(url.path);
                ipt.attr("path",url.path);
            }
            if(url.fullpath){
                if (url.fullpath.substring(0,12) == 'uploadfiles/') {
                    url.fullpath = $.__aliedit_fun.paramet.BASE_URI + url.fullpath;
                }
                if(img.length > 0){
                    img.get(0).src = url.fullpath;
                }
                ipt.attr("url",url.fullpath);
            }
        }, val, home, type, allowed, size);

    },
    clicktis: function(obj) {
        if ($(obj).attr("data-tis")) {
            $intemp = $('<div style="position:fixed;width:100%;height:100%;top:0;left:0;right:0;bottom:0;z-index:9998;background-color:rgba(0,0,0,.6)"></div>');
            $("body").append($intemp);
            alert($(obj).attr("data-tis"));
            $intemp.remove();
        }
    },
    mouseovervoice: function(obj, out) {
        var tthis = $(obj);
        if (out) {
            tthis.next(".mouseovervoice").hide();
        }else{
            if (!tthis.val()) return false;
            if (tthis.next().hasClass("mouseovervoice")) {
                tthis.next(".mouseovervoice").show();
            }else{
                tthis.after('<div class="mouseovervoice" onclick="$.__aliedit_fun.playmouse(\''+tthis.val()+'\');" onmouseover="$(this).show()" onmouseout="$(this).remove()">预览</div>');
            }
        }
    },
    mouseovervideo: function(obj, out) {
        var tthis = $(obj);
        if (out) {
            tthis.next(".mouseovervideo").hide();
        }else{
            if (!tthis.val()) return false;
            if (tthis.next().hasClass("mouseovervideo")) {
                tthis.next(".mouseovervideo").show();
            }else{
                tthis.after('<div class="mouseovervideo" onclick="$.__aliedit_fun.playmouse(\''+tthis.val()+'\');" onmouseover="$(this).show()" onmouseout="$(this).remove()">预览</div>');
            }
        }
    },
    playmouse: function(path) {
        if (path.substring(0,12) == 'uploadfiles/') {
            $intemp = $('<div class="mouseoverwin">' +
                '<div class="mouseoverbg"></div>' +
                '<div class="mouseovertis">提示：点击背景关闭预览</div>' +
                '<div class="mouseovercon"><video src="'+ $.__aliedit_fun.paramet.BASE_URI + path +'" controls="controls" autoplay="autoplay">您的浏览器不支持预览功能。</video></div>' +
                '</div>');
            $intemp.find('.mouseoverbg').click(function(){
                $(this).parent().remove();
            });
            $("body").append($intemp);
            return true;
        }
        if (path.substring(0,4) == 'http' || path.substring(0,1) == '/') {
            $intemp = $('<div class="mouseoverwin">' +
                '<div class="mouseoverbg"></div>' +
                '<div class="mouseovertis">提示：点击背景关闭预览</div>' +
                '<div class="mouseovercon"><video src="'+ path +'" controls="controls" autoplay="autoplay">您的浏览器不支持预览功能。</video></div>' +
                '</div>');
            $intemp.find('.mouseoverbg').click(function(){
                $(this).parent().remove();
            });
            $("body").append($intemp);
            return true;
        }
        alert("此文件不支持在线预览！");
    },
    moveimageurl: function(obj, hi, n) {
        var tthis = $(obj);
        if (n === 0) {
            tthis.show(); return false;
        }
        if (n === 1) {
            tthis.hide(); return false;
        }
        //
        tthis.next(".selclick").hide();
        if (hi) return false;
        tthis.next(".selclick").remove();
        $inhtml = $($.__aliedit_fun.moveimageurlhtml);
        tthis.after($inhtml);
    },
    moveimageurlhtml: '<div class="selclick" onmousemove="$.__aliedit_fun.moveimageurl(this,1,0);" onmouseout="$.__aliedit_fun.moveimageurl(this,1,1);"><a href="javascript:;" onclick="$.__aliedit_fun.sellibraryurl(this);">从素材库中选择</a><a href="javascript:;" onclick="$.__aliedit_fun.selmoduleurl(this);">其他模块中选择</a></div>',
    sellibraryurl: function(obj) {
        $("div.jQuery-formfile").remove();
        var intemp = $('<div class="jQuery-formfile">' +
            '<div class="jQuery-form-back"></div>' +
            '<div class="jQuery-form-content">' +
            '<div class="formfile-title"><em id="f-cancel">×</em><span class="formfile-call"></span>选择图文广播素材</div>' +
            '<div class="formfile-item"><div class="formfile-load">正在加载...</div></div>' +
            '<div class="formfile-button"><p class="hover" id="f-confirm">确定</p><p id="f-cancel">取消</p></div> ' +
            '</div>' +
            '</div>');
        //背景
        intemp.find(".jQuery-form-back").css({
            "height":$(document).height()
        }).click(function(){
            $("div.jQuery-formfile").remove();
        });
        //点击关闭 取消
        intemp.find("#f-cancel").click(function(){
            $("div.jQuery-formfile").remove();
        });
        //调整宽度
        var w = $(document).width() * 0.8;
        if (w > 1220) w = 1220;
        var wl = (w / 2) * -1;
        intemp.find(".jQuery-form-content").css({width:w,marginLeft:wl});
        //点击确定
        intemp.find("#f-confirm").click(function(){
            alert("请点击列表内素材选择！");
        });
        //显示
        $("body").append(intemp);//
        $.__aliedit_fun.paramet.__libraryurlpage_nowsel = $(obj).parent().prev("input").val();
        //加载列表
        $.__aliedit_fun.__libraryurlpage(null, 1, '', function(obj2) {
            var url = $(obj2).find("a").attr("data-href");
            $(obj).parent().prev("input").val(url?url:'');
        });
    },
    __libraryurlpage: function(obj, page, key, callback) {
        if (callback) {
            $.__aliedit_fun.paramet.__libraryurlpage_callback = callback;
        }else{
            callback =  $.__aliedit_fun.paramet.__libraryurlpage_callback;
        }
        if (obj) {
            if (callback) callback(obj);
            $("div.jQuery-formfile").remove();
            return true;
        }
        var url = $.__aliedit_fun.paramet.BASE_URI+'web/library/alieditlist/aliedit/?index=1';
        $.ajax({
            type: "GET",
            url: url + "&page=" + page + "&keyval=" + (key?key:''),
            dataType: "html",
            success: function (html) {
                $("div.jQuery-formfile").find(".formfile-item").html(html);
                if ($.__aliedit_fun.paramet.__libraryurlpage_nowsel) {
                    try {
                        $(".appmsg-showy").each(function(){
                            if ($(this).find("a").attr("data-href") == $.__aliedit_fun.paramet.__libraryurlpage_nowsel) {
                                $(this).css({'border':'1px solid #F091A3'});
                            }
                        });
                    }catch(e){}
                }
            },
            error: function (msg) {
                alert("加载失败");
                $("div.jQuery-formfile").remove();
            }
        });
    },
    selmoduleurl: function (obj) {
        $("div.jQuery-formfile").remove();
        var intemp = $('<div class="jQuery-formfile">' +
            '<div class="jQuery-form-back"></div>' +
            '<div class="jQuery-form-content">' +
            '<div class="formfile-title" style="padding-bottom:15px;margin-bottom:0px;"><em id="f-cancel">×</em><span class="formfile-call"></span>选择其他模块的URL<small style="font-size:60%;color:#aaaaaa;padding-left:15px;">仅支持有<u>关键词走势</u>的应用模块；<br/>' +
            '注意：如果模块需要获取粉丝身份，非认证的服务号没有网页授权的接口可能无法正常访问模块的链接URL，详细请咨询模块开发者。</small></div>' +
            '<div class="formfile-item"><div class="formfile-load">正在加载...</div></div>' +
            '<div class="formfile-button"><p class="hover" id="f-confirm">确定</p><p id="f-cancel">取消</p></div> ' +
            '</div>' +
            '</div>');
        //背景
        intemp.find(".jQuery-form-back").css({
            "height":$(document).height()
        }).click(function(){
            $("div.jQuery-formfile").remove();
        });
        //点击关闭 取消
        intemp.find("#f-cancel").click(function(){
            $("div.jQuery-formfile").remove();
        });
        //调整宽度
        var w = $(document).width() * 0.8;
        if (w > 1220) w = 1220;
        var wl = (w / 2) * -1;
        intemp.find(".jQuery-form-content").css({width:w,marginLeft:wl});
        //点击确定
        intemp.find("#f-confirm").click(function(){
            alert("请点击列表选择！");
        });
        //显示
        $("body").append(intemp); //
        $.__aliedit_fun.paramet.__moduleurlpage_nowsel = $(obj).parent().prev("input").val();
        //加载列表
        $.__aliedit_fun.__moduleurlpage(null, 1, '', function(url) {
            $(obj).parent().prev("input").val(url);
        });
    },
    __moduleurlpage: function(obj, page, key, callback) {
        if (callback) {
            $.__aliedit_fun.paramet.__moduleurlpage_callback = callback;
        }else{
            callback =  $.__aliedit_fun.paramet.__moduleurlpage_callback;
        }
        var url = $.__aliedit_fun.paramet.BASE_URI+'web/menu/reply/aliedit/?index=1';
        $.ajax({
            type: "GET",
            url: url + "&page=" + page + "&keyval=" + (key?key:''),
            dataType: "html",
            success: function (html) {
                $("div.jQuery-formfile").find(".formfile-item").html(html);
                var reply_url_tr = $(".reply_url").find("tr");
                if ($.__aliedit_fun.paramet.__moduleurlpage_nowsel) {
                    try {
                        reply_url_tr.each(function(){
                            if ($(this).attr("data-url") == $.__aliedit_fun.paramet.__moduleurlpage_nowsel) {
                                $(this).find("td").css({'background-color':'#F091A3'});
                            }
                        });
                    }catch(e){}
                }
                reply_url_tr.unbind().click(function(){
                    if (callback) callback($(this).attr("data-url"));
                    $("div.jQuery-formfile").remove();
                });
            },
            error: function (msg) {
                alert("加载失败");
                $("div.jQuery-formfile").remove();
            }
        });
    },
    /** **********************************************/
    /** **********************************************/
    /** **********************************************/
    tHTML2XHTML: function(_strers)
    {
        var tstrers = _strers;
        if (tstrers)
        {
            tstrers = tstrers.replace(/<br.*?>/ig, '<br />');
            tstrers = tstrers.replace(/(<hr\s+[^>]*[^\/])(>)/ig, '$1 />');
            tstrers = tstrers.replace(/(<img\s+[^>]*[^\/])(>)/ig, '$1 />');
        };
        return tstrers;
    },
    tHTMLClear: function(_strers)
    {
        var tstrers = _strers;
        if (tstrers)
        {
            tstrers = tstrers.replace(/<script[^>]*>[\s\S]*?<\/script[^>]*>/gim, '');
            tstrers = tstrers.replace(/<(\/?)(script|i?frame|style|html|head|body|title|link|meta|object|\?|\%)([^>]*?)>/gi, '');
            tstrers = tstrers.replace(/<([a-z]+)+\s*(?:onerror|onload|onunload|onresize|onblur|onchange|onclick|ondblclick|onfocus|onkeydown|onkeypress|onkeyup|onmousemove|onmousedown|onmouseout|onmouseover|onmouseup|onselect)[^>]*>/gi, '<$1>');
        };
        return tstrers;
    },
    tReplace: function(_strers, _reary, _ign)
    {
        var tstrers = _strers;
        var treary = _reary;
        var tign = _ign;
        var tstate1 = true;
        for (var ti = 0; ti < treary.length; ti ++)
        {
            if (!treary[ti][2]) tstrers = tstrers.replace(treary[ti][0], (tign ? '' : treary[ti][1]));
        };
        while(tstate1)
        {
            tstate1 = false;
            for (var ti = 0; ti < treary.length; ti ++)
            {
                if (treary[ti][2] && tstrers.search(treary[ti][0]) != -1)
                {
                    tstate1 = true;
                    tstrers = tstrers.replace(treary[ti][0], (tign ? '' : treary[ti][1]));
                };
            };
        };
        return tstrers;
    },
    tHTMLEncode: function(_strers)
    {
        var tstrers = _strers;
        if (tstrers)
        {
            tstrers = tstrers.replace(/&/igm, '&amp;');
            tstrers = tstrers.replace(/</igm, '&lt;');
            tstrers = tstrers.replace(/>/igm, '&gt;');
            tstrers = tstrers.replace(/\"/igm, '&quot;');
            tstrers = tstrers.replace(/ /igm, '&nbsp;');
        };
        return tstrers;
    },
    tHTMLDecode: function(_strers)
    {
        var tstrers = _strers;
        if (tstrers)
        {
            tstrers = tstrers.replace(/&lt;/igm, '<');
            tstrers = tstrers.replace(/&gt;/igm, '>');
            tstrers = tstrers.replace(/&quot;/igm, '"');
            tstrers = tstrers.replace(/&nbsp;/igm, ' ');
            tstrers = tstrers.replace(/&amp;/igm, '&');
        };
        return tstrers;
    },
    tXHTML2UBB: function(_strers)
    {
        var tstrers = _strers;
        if (tstrers)
        {
            var tReplaceAry = [
                [/<br \/>/ig, "\r\n", false],
                [/<p\s[^>]*?>([^<]*?)<\/p>/igm, function($0, $1) {
                    return "\r\n"+$1;
                }, true],
                [/<p>([^<]*?)<\/p>/igm, function($0, $1) {
                    return "\r\n"+$1;
                }, true],
                [/<div\s[^>]*?>([^<]*?)<\/div>/igm, function($0, $1) {
                    return "\r\n"+$1;
                }, true],
                [/<div>([^<]*?)<\/div>/igm, function($0, $1) {
                    return "\r\n"+$1;
                }, true],
                [/<img\s[^>]*?>/igm, function($0) {
                    $tObj1 = $($0);
                    var tString = "";
                    if ($tObj1.attr('data-txt')) tString = '/' + $tObj1.attr('data-txt');
                    return tString;
                }, true],
                [/<a\s[^>]*?>([^<]*?)<\/a>/igm, function($0, $1) {
                    $tObj1 = $($0);
                    var tString = "";
                    if ($tObj1.attr('href')) tString = '[url=' + $tObj1.attr('href') + ']' + $1 + '[/url]';
                    return tString;
                }, true],
                [/\]\[br\]\[/igm, '] [', true],
                [/\[br\]\[\/p\]/igm, '[/p]', true],
                [/\[\/p\]\[p\]/igm, '[/p]\r\n[p]', true]
            ];
            tstrers = this.tReplace(tstrers, tReplaceAry);
            tstrers = tstrers.replace(/<[^>]*>/igm, '');
            tstrers = this.tHTMLDecode(tstrers);
        };
        return tstrers;
    },
    tUBB2XHTML: function(_strers)
    {
        var tstrers = _strers;
        if (tstrers)
        {
            var tReplaceAry = [
                [/\r\n/igm, '<br />', false],
                [/\r/igm, '<br />', false],
                [/\n/igm, '<br />', false],
                [/\[url=([^\]]*)\]([^\[]*?)\[\/url\]/igm, '<a href="$1">$2</a>', true]
            ];
            tstrers = this.tHTMLEncode(tstrers);
            tstrers = this.tReplace(tstrers, tReplaceAry);
        };
        return tstrers;
    },
    insertVal: function(text){
        var tHTML = text;
        tHTML = this.tHTML2XHTML(tHTML);
        tHTML = this.tHTMLClear(tHTML);
        tHTML = this.tXHTML2UBB(tHTML);
        return tHTML;
    },
    getVal: function(text){
        var tHTML = text;
        tHTML = this.tUBB2XHTML(tHTML);
        return $.__aliedit_fun.repVal(tHTML);
    },
    repVal: function(text) {
        var texti = text, reg = '';
        $.each( $.__aliedit_fun.emotiondata, function(index, content){
            reg = new RegExp("/"+content, "gi");
            texti = texti.replace(reg, '<img src="'+$.__aliedit_fun.path+'/skins/default/qqphiz/'+index+'.gif" data-txt="'+content+'"/>');
        });
        return texti;
    },
    insertContent: function(nobj, text){
        var obj= $(nobj);
        var range, node;
        if(!obj.hasfocus) {
            obj.focus();
        }
        if (!obj.attr("data-inclick")) {
            obj.html(obj.html() + text);
        }else if (window.getSelection && window.getSelection().getRangeAt) {
            range = (window._aledit_inicon_range)?window._aledit_inicon_range:window.getSelection().getRangeAt(0);
            range.collapse(false);
            node = range.createContextualFragment(text);
            var c = node.lastChild;
            range.insertNode(node);
            if(c){
                range.setEndAfter(c);
                range.setStartAfter(c)
            }
            var j = window.getSelection();
            j.removeAllRanges();
            j.addRange(range);
        } else if (document.selection && document.selection.createRange) {
            document.selection.createRange().pasteHTML(text);
        }
        obj.blur();
    },
    emotiondata: {"0":"微笑","1":"撇嘴","2":"色","3":"发呆","4":"得意","5":"流泪","6":"害羞","7":"闭嘴","8":"睡","9":"大哭","10":"尴尬","11":"发怒","12":"调皮","13":"呲牙","14":"惊讶","15":"难过","16":"酷","17":"冷汗","18":"抓狂","19":"吐","20":"偷笑","21":"可爱","22":"白眼","23":"傲慢","24":"饥饿","25":"困","26":"惊恐","27":"流汗","28":"憨笑","29":"大兵","30":"奋斗","31":"咒骂","32":"疑问","33":"嘘","34":"晕","35":"折磨","36":"衰","37":"骷髅","38":"敲打","39":"再见","40":"擦汗","41":"抠鼻","42":"鼓掌","43":"糗大了","44":"坏笑","45":"左哼哼","46":"右哼哼","47":"哈欠","48":"鄙视","49":"委屈","50":"快哭了","51":"阴险","52":"亲亲","53":"吓","54":"可怜","55":"菜刀","56":"西瓜","57":"啤酒","58":"篮球","59":"乒乓","60":"咖啡","61":"饭","62":"猪头","63":"玫瑰","64":"凋谢","65":"示爱","66":"爱心","67":"心碎","68":"蛋糕","69":"闪电","70":"炸弹","71":"刀","72":"足球","73":"瓢虫","74":"便便","75":"月亮","76":"太阳","77":"礼物","78":"拥抱","79":"强","80":"弱","81":"握手","82":"胜利","83":"抱拳","84":"勾引","85":"拳头","86":"差劲","87":"爱你","88":"NO","89":"OK","90":"爱情","91":"飞吻","92":"跳跳","93":"发抖","94":"怄火","95":"转圈","96":"磕头","97":"回头","98":"跳绳","99":"挥手","100":"激动","101":"街舞","102":"献吻","103":"左太极","104":"右太极"},
    paramet: {}
};

$.fn.aliedit = function(paramet) {
    //加载css
    var eve = this;
    var node = document.createElement("link");
    node.setAttribute("rel", "stylesheet");
    node.setAttribute("type", "text/css");
    if ($.__aliedit_fun.loadcss) {
        node.setAttribute("href", $.__aliedit_fun.path+'/skins/none.css');
    }else{
        node.setAttribute("href", $.__aliedit_fun.path+'/skins/default.css');
        $.__aliedit_fun.loadcss = true;
    }
    $.__aliedit_fun.paramet = paramet;
    document.getElementsByTagName('head')[0].appendChild(node);
    $.__aliedit_fun.styleOnload(node, function(){
        var ew = eve.width(),
            eh = eve.height(),
            ejosn = paramet.value;
        if (ew < 520) ew = 520;
        if (eh < 220) eh = 220;
        var intemp = '';
        //
        intemp+='<div class="aliedit">';
        intemp+='<div class="aliedit-title">';
        intemp+='<div class="aliedit-title-icon wi1 hover" data-type="text"><i>文字</i></div>';
        intemp+='<div class="aliedit-title-icon wi5" data-type="imagetext"><i>图文</i></div>';
        intemp+='<div class="aliedit-title-icon wi6" data-type="material"><i>素材</i></div>';
        intemp+='<div class="aliedit-title-icon wi2" data-type="image"><i>图片</i></div>';
        intemp+='<div class="aliedit-title-icon wi4" data-type="voice"><i>语音</i></div>';
        intemp+='<div class="aliedit-title-icon wi3" data-type="video"><i>视频</i></div>';
        intemp+='</div>';
        intemp+='<div class="aliedit-content">';

        //文字
        intemp+='<div class="aliedit-content-1">';
        intemp+='<table width="100%" border="0" cellspacing="0" cellpadding="0">';
        intemp+='<tr>';
        intemp+='<td valign="top"><div class="inicontext" contentEditable="true"></div><textarea name="'+eve.attr('name')+'[text]" class="altext" style="display:none;">'+ejosn.text+'</textarea></td>';
        intemp+='</tr>';
        intemp+='<tr>';
        intemp+='<td><div class="aliedit-inicon"><div class="aliedit-inicon-emotion"></div><div class="emotion_wrp"><div class="emotion_txt">服务窗暂不支持表情！</div><ul class="emotions"></ul><span class="emotions_preview"></span></div></div></td>';
        intemp+='</tr>';
        intemp+='</table>';
        intemp+='</div>';

        //图文
        var fillimg = ejosn.imagetext.img + "";
        if (fillimg == "undefined") {
            fillimg = paramet.IMG_PATH+'nopic.jpg';
        }else{
            if (fillimg.substring(0,4)!='http' && fillimg.substring(0,1)!='/') fillimg = paramet.BASE_URI + fillimg;
        }
        intemp+='<div class="aliedit-content-2">';
        intemp+='<div class="imagetext-content">';
        intemp+='<table width="100%" border="0" cellspacing="0" cellpadding="0">';
        intemp+='<tr>';
        intemp+='    <th>标题：</th>';
        intemp+='    <td><input class="form-control-aliedit" type="text" name="'+eve.attr("name")+'[imagetext][title]" value="'+ejosn.imagetext.title+'"></div></td>';
        intemp+='</tr>';
        intemp+='<tr>';
        intemp+='    <th>封面：</th>';
        intemp+='    <td>';
        intemp+='      <div class="aliedit-inputbox">';
        intemp+='        <input class="form-control-aliedit" type="text" onblur="$.__aliedit_fun.onblurImageDialog(this)" name="'+eve.attr("name")+'[imagetext][img]" value="'+ejosn.imagetext.img+'">';
        intemp+='        <button class="btn-aliedit" type="button" onclick="$.__aliedit_fun.showImageDialog(this, \''+paramet.home+'\')">选择图片</button>';
        intemp+='        <div class="aibimg-aliedit"><img src="'+fillimg+'" onerror="this.src=\''+paramet.IMG_PATH+'nopic.jpg\'; this.title=\'图片未找到.\'"><em class="close" title="删除这张图片" onclick="$.__aliedit_fun.deleteImageDialog(this)">×</em></div>';
        intemp+='      </div>';
        intemp+='    </td>';
        intemp+='</tr>';
        intemp+='<tr id="imagetext-link" style="display:none">';
        intemp+='    <th>链接：</th>';
        intemp+='    <td class="possel"><input class="form-control-aliedit" type="text" name="'+eve.attr("name")+'[imagetext][url]" onmousemove="$.__aliedit_fun.moveimageurl(this);" onmouseout="$.__aliedit_fun.moveimageurl(this,1);" value="'+ejosn.imagetext.url+'" placeholder="链接包含 http://"></td>';
        intemp+='</tr>';
        intemp+='<tr>';
        intemp+='    <th>描述：</th>';
        intemp+='    <td><textarea class="form-control-aliedit" name="'+eve.attr("name")+'[imagetext][desc]">'+ejosn.imagetext.desc+'</textarea></td>';
        intemp+='</tr>';
        intemp+='</table>';
        intemp+='</div>';
        intemp+='</div>';

        //素材
        intemp+='<div class="aliedit-content-3">';
        intemp+='<input type="hidden" autocomplete="off" name="'+eve.attr("name")+'[material]" id="'+eve.attr("id")+'_material" value="'+ejosn.material+'">';
        intemp+='<div class="formfile-content" style="height:99%;padding:0;"><div class="appmsg-list" style="width:auto"></div></div>';
        intemp+='</div>';

        //图片
        fillimg = ejosn.image + "";
        if (fillimg == "undefined") {
            fillimg = paramet.IMG_PATH+'nopic.jpg';
        }else{
            if (fillimg.substring(0,4)!='http' && fillimg.substring(0,1)!='/') fillimg = paramet.BASE_URI + fillimg;
        }
        intemp+='<div class="aliedit-content-4">';
        intemp+='<div class="aliedit-question" onclick="$.__aliedit_fun.clicktis(this)" data-tis="支持图片格式：jpg、限制大小：1MB；\r\n已认证的微信公众号(包含:订阅好/服务号)将直接通过素材发送；\r\n不符合格式要求的或未认证公众号和支付宝服务窗通过图文形式发送。"><img src="'+$.__aliedit_fun.path+'/skins/default/question.png"></div>';
        intemp+='<div class="imagetext-content" style="margin-top:15px">';
        intemp+=' <div class="aliedit-inputbox">';
        intemp+='   <input class="form-control-aliedit" type="text" name="'+eve.attr("name")+'[image]" value="'+ejosn.image+'" readonly="true" style="cursor:no-drop" placeholder="选择或上传图片">';
        intemp+='   <button class="btn-aliedit" type="button" onclick="$.__aliedit_fun.showFileDialog(this, \''+paramet.home+'\', \'local\', \'images\',\'jpg\',\'1024\')">选择图片</button>';
        intemp+='   <div class="aibimg-aliedit"><img src="'+fillimg+'" onerror="this.src=\''+paramet.IMG_PATH+'nopic.jpg\'; this.title=\'图片未找到.\'"><em class="close" title="删除这张图片" onclick="$.__aliedit_fun.deleteImageDialog(this)">×</em></div>';
        intemp+=' </div>';
        intemp+='</div>';
        intemp+='</div>';

        //语音
        intemp+='<div class="aliedit-content-5">';
        intemp+='<div class="aliedit-question" onclick="$.__aliedit_fun.clicktis(this)" data-tis="支持语音格式：amr|mp3、限制大小：2MB；\r\n已认证的微信公众号(包含:订阅好/服务号)将直接通过素材发送；\r\n不符合格式要求的或未认证公众号和支付宝服务窗通过图文形式发送。"><img src="'+$.__aliedit_fun.path+'/skins/default/question.png"></div>';
        intemp+='<div class="imagetext-content" style="margin-top:38px">';
        intemp+=' <div class="aliedit-inputbox" style="padding-right:100px;">';
        intemp+='   <input class="form-control-aliedit" type="text" name="'+eve.attr("name")+'[voice]" value="'+ejosn.voice+'" readonly="true" style="cursor:no-drop" onmouseover="$.__aliedit_fun.mouseovervoice(this)" onmouseout="$.__aliedit_fun.mouseovervoice(this,1)" placeholder="选择或上传语音">';
        intemp+='   <button class="btn-aliedit" type="button" onclick="$.__aliedit_fun.showFileDialog(this, \''+paramet.home+'\', \'local\', \'voices\',\'amr|mp3\',\'2048\')" style="width:90px;">选择媒体文件</button>';
        intemp+=' </div>';
        intemp+='</div>';
        intemp+='</div>';

        //视频
        intemp+='<div class="aliedit-content-6">';
        intemp+='<div class="aliedit-question" onclick="$.__aliedit_fun.clicktis(this)" data-tis="支持语音格式：mp4、限制大小：10MB；\r\n已认证的微信公众号(包含:订阅好/服务号)将直接通过素材发送；\r\n不符合格式要求的或未认证公众号和支付宝服务窗通过图文形式发送。"><img src="'+$.__aliedit_fun.path+'/skins/default/question.png"></div>';
        intemp+='<div class="imagetext-content" style="margin-top:38px">';
        intemp+=' <div class="aliedit-inputbox" style="padding-right:100px;">';
        intemp+='   <input class="form-control-aliedit" type="text" name="'+eve.attr("name")+'[video]" value="'+ejosn.video+'" readonly="true" style="cursor:no-drop" onmouseover="$.__aliedit_fun.mouseovervideo(this)" onmouseout="$.__aliedit_fun.mouseovervideo(this,1)" placeholder="选择或上传视频">';
        intemp+='   <button class="btn-aliedit" type="button" onclick="$.__aliedit_fun.showFileDialog(this, \''+paramet.home+'\', \'local\', \'videos\',\'mp4\',\'10240\')" style="width:90px;">选择媒体文件</button>';
        intemp+=' </div>';
        intemp+='</div>';
        intemp+='</div>';


        intemp+='</div>';
        intemp+='<input type="hidden" autocomplete="off" name="'+eve.attr("name")+'[type]" id="'+eve.attr("id")+'_type" value="text"/>';
        intemp+='<div class="clearfix"></div>';
        intemp+='</div>';
        $inhtml = $(intemp);
        $inhtml.css({width:ew});
        if (eve.attr("data-link")) {
            $inhtml.find("#imagetext-link").show();
        }
        $inhtml.find("input,textarea").each(function(){
            if ($(this).val() == "undefined") {
                $(this).val("")
            }
        });
        var showtab = paramet.showtab.split(',');
        var titicon = $inhtml.find(".aliedit-title");
        if (showtab.length > 0) {
            titicon.find(".aliedit-title-icon").hide();
            for (i=0;i<showtab.length ;i++ ){
                titicon.find(".aliedit-title-icon[data-type='"+showtab[i]+"']").show();
            }
        }
        eve.after($inhtml).hide();
        window._aledit_icon_click = '';
        window._aledit_icon_click_last = '';
        $inhtml.find(".aliedit-title .aliedit-title-icon").each(function(index){
            $(this).attr("data-i", index);
            $(this).click(function(){
                if ($(this).attr("data-type") != window._aledit_icon_click) {
                    window._aledit_icon_click_last = window._aledit_icon_click;
                    window._aledit_icon_click = $(this).attr("data-type");
                }
                var parentobj = $(this).parents(".aliedit");
                parentobj.find(".aliedit-title .aliedit-title-icon").removeClass("hover");
                $(this).addClass("hover");
                parentobj.find(".aliedit-content > div").hide();
                parentobj.find(".aliedit-content > div").eq($(this).attr("data-i")).show();
                parentobj.find("#"+eve.attr("id")+"_type").val($(this).attr('data-type'));
                if ($(this).attr('data-type') == 'material') {
                    paramet.material_val = $("#"+eve.attr("id")+"_material").val();
                    if ($(this).attr('data-one')) {
                        $(this).attr('data-one', '');
                        parentobj.find(".appmsg-list").html("&nbsp;加载中...");
                        $.ajax({
                            type: "GET",
                            url: paramet.home + "web/library/alieditlist/?l="+paramet.alid+"&id="+paramet.material_val,
                            dataType: "html",
                            success: function (html) {
                                var tthis = $(html).find(".appmsg-col").eq(0);
                                $maintemp = $($("<p>").append(tthis.clone()).html());
                                $maintemp.attr("onclick", "").css({margin:10,position:'static'});
                                $maintemp.find(".appmsg ").css({margin:0});
                                $maintemp.find(".appmsg-bg").remove();
                                parentobj.find(".appmsg-list").html($maintemp);
                            },
                            error: function (msg) {
                                parentobj.find(".appmsg-list").html("&nbsp;加载失败");
                            }
                        });
                        return false;
                    }
                    //弹出素材库
                    $.__aliedit_fun.materialshow(function(obj){
                        //确定
                        var tthis = $(obj);
                        parentobj.find("#"+eve.attr("id")+"_material").val(tthis.attr('data-id'));
                        $maintemp = $($("<p>").append(tthis.clone()).html());
                        $maintemp.attr("onclick", "").css({margin:10,position:'static'});
                        $maintemp.find(".appmsg ").css({margin:0});
                        $maintemp.find(".appmsg-bg").remove();
                        parentobj.find(".appmsg-list").html($maintemp);
                    },function(){
                        //取消
                        if (!paramet.material_val) {
                            if (titicon.find(".aliedit-title-icon[data-type='text']").is(":visible")) {
                                if (window._aledit_icon_click_last) {
                                    parentobj.find(".aliedit-title .aliedit-title-icon[data-type='"+window._aledit_icon_click_last+"']").click();
                                }else{
                                    parentobj.find(".aliedit-title .aliedit-title-icon").eq(0).click();
                                }
                            }
                        }
                    }, paramet);
                }else if ($(this).attr('data-type') == 'imagetext') {
                    //
                }else{
                    parentobj.find(".appmsg-list").html("");
                }
            });
        });
        /*$inhtml.find(".aliedit-content-1 > textarea").css({
            height: eh - $inhtml.find(".aliedit-title").outerHeight(),
            width: ew - 20
        });*/
        var iconobj = $inhtml.find(".aliedit-content-1").find(".inicontext");
        var textobj = iconobj.next("textarea");
        iconobj.css({
            height: eh - $inhtml.find(".aliedit-title").outerHeight(),
            width: ew - 20
        }).bind("keyup blur",function(){
            if (window._aledit_inicon_paste == true) {
                window._aledit_inicon_paste = false;
                $(this).html($.__aliedit_fun.getVal($.__aliedit_fun.insertVal($(this).html())));
            }else{
                textobj.val($.__aliedit_fun.insertVal($(this).html()));
                $(this).mouseup();
            }
        }).bind("paste",function(){
            window._aledit_inicon_paste = true;
        }).bind("click",function(){
            $(this).attr("data-inclick","1");
        }).bind("mouseup change",function(){
            try{
                var selection = window.getSelection ? window.getSelection() : document.selection;
                window._aledit_inicon_range = selection.createRange ? selection.createRange() : selection.getRangeAt(0);
            }catch (e){}
        }).html($.__aliedit_fun.getVal(textobj.val())).focus();
        $inhtml.find(".aliedit-inicon-emotion").click(function(){
            if ($inhtml.find(".emotion_wrp").css('display') != 'none') {
                $inhtml.find(".emotion_wrp").hide('fast');
            }
            $inhtml.find(".emotion_wrp .emotions").html("");
            for(var i=0;i<105;i++) {
                $emotion_icon = $('<li data-i='+i+' data-txt='+$.__aliedit_fun.emotiondata[i]+'><i style="background-position:-'+(i*24)+'px 0;"></i></li>');
                $emotion_icon.hover(function(){
                    $inhtml.find(".emotion_wrp .emotions_preview").html('<img src="'+$.__aliedit_fun.path+'/skins/default/qqphiz/'+$(this).attr("data-i")+'.gif"/>').show();
                },function(){
                    $inhtml.find(".emotion_wrp .emotions_preview").hide();
                }).click(function(){
                    $.__aliedit_fun.insertContent(iconobj, '<img src="'+$.__aliedit_fun.path+'/skins/default/qqphiz/'+$(this).attr("data-i")+'.gif" data-txt="'+$(this).attr("data-txt")+'"/>')
                });
                $inhtml.find(".emotion_wrp .emotions").append($emotion_icon);
            }
            $inhtml.find(".emotion_wrp").show();
        });
        $('body').click(function (e) {
            if ($inhtml.find(".emotion_wrp").css('display') != 'none') {
                if (($(e.target).is('.aliedit-inicon-emotion') == false) && ($(e.target).parents('.emotion_wrp').length == 0)) {
                    $inhtml.find(".emotion_wrp").hide();
                }
            }
        });

        if (ejosn.type) {
            titicon.find(".aliedit-title-icon[data-type='"+ejosn.type+"']").attr("data-one",1).click();
        }else{
            titicon.find(".aliedit-title-icon").eq(0).click();
        }
        if (!titicon.find(".aliedit-title-icon.hover").is(":visible")) {
            titicon.find(".aliedit-title-icon").each(function(){
                if ($(this).css("display") == "block") {
                    $(this).click();
                    return false;
                }
            });
        }
        eve.remove();
    });
};