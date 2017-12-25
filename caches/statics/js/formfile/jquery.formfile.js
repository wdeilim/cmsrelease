$.__formfile_fun = {
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
                $.__formfile_fun.poll(node, callback);
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
                $.__formfile_fun.poll(node, callback);
            }, 1);
        }
    },
    path: window['_jquery_formfile'] || (function (script, i, me) {
        if (window.TPL_INIT_FORMFILE_PATH) {
            me = window.TPL_INIT_FORMFILE_PATH;
        }else{
            for (i in script) {
                if (script[i].src && script[i].src.indexOf('jquery.formfile') !== -1) me = script[i];
            };
            _thisScript = me || script[script.length - 1];
            me = _thisScript.src.replace(/\\/g, '/');
        }
        return me.lastIndexOf('/') < 0 ? '.' : me.substring(0, me.lastIndexOf('/'));
    }(document.getElementsByTagName('script'))),
    isJson: function(obj){
        var isjson = typeof(obj) == "object" && Object.prototype.toString.call(obj).toLowerCase() == "[object object]" && !obj.length
        return isjson;
    },
    loadjscssfile: function(filename, filetype, callback){
        var tthis = this, fileref;
        if(filetype == "js"){
            fileref = document.createElement('script');
            fileref.setAttribute("type","text/javascript");
            fileref.setAttribute("src",filename);
        }else if(filetype == "css"){
            fileref = document.createElement('link');
            fileref.setAttribute("rel","stylesheet");
            fileref.setAttribute("type","text/css");
            fileref.setAttribute("href",filename);
        }
        window.formfile_filename+= "";
        if(typeof fileref != "undefined" && window.formfile_filename.indexOf("|" + filename + "|") == -1){
            window.formfile_filename+= "|" + filename + "|";
            tthis.alert("正在加载",0,200);
            document.getElementsByTagName("head")[0].appendChild(fileref);
            tthis.styleOnload(fileref, function(){
                tthis.alert(0);
                if (callback) callback(1);
            });
        }else{
            if (callback) callback(0);
        }
    },
    alert: function(e, t, s) {
        $("div.jQuery-form-alert").remove();
        if (e === 0) return;
        $intemp = $('<div class="jQuery-form-alert" style="display:none;position:fixed;top:0;left:0;right:0;min-height:25px;text-align:center;color:#fff;z-index:2147483647;"><span style="padding:5px 30px;min-width:100px;text-align:center;border-radius:0 0 4px 4px;background-color:#2378cd;font-size:14px; line-height:22px;">' + e + '</span></div>');
        $("body").append($intemp);
        var i = $(window).width(),
            o = $intemp.width()+20,
            l = (i - o) / 2;
        /*i > o && $intemp.css("left", parseInt(l)),
            i > o && $intemp.css("right", parseInt(l)),
            l < 5 && $intemp.css("margin", "0 5px");*/
        setTimeout(function() { $intemp.show(); }, s || 1);
        if (t === 0) return;
        setTimeout(function() { $intemp.fadeOut(); }, t || 2000)
    },
    inimages: function(callback, imgurl, indexurl){
        var tthis = this;
        tthis.close();
        var intemp = $('<div class="jQuery-formfile">' +
            '<div class="jQuery-form-back"></div>' +
            '<div class="jQuery-form-content">' +
            '<div class="formfile-title"><em id="f-cancel">×</em><span class="formfile-call"></span>请选择图片</div>' +
            '<div class="formfile-subtitle">' +
            '<p class="hover">网络图片</p>' +
            '<p>上传图片</p>' +
            '</div> ' +
            '<div class="formfile-content">' +
            '<p class="hover">' +
            '<label>图片地址</label>' +
            '<input class="formfile_input" type="text" id="formfile_imageurl" value="" placeholder="请输入图片URL">' +
            '<button class="formfile_button" id="formfile_imagesbutton" type="button">浏览图片空间</button>' +
            '</p>' +
            '<p>' +
            '<label>上传图片</label>' +
            '<input class="formfile_input formfile_readonly" type="text" id="formfile_nourl" readonly>' +
            '<button class="formfile_button formfile_imagebut" type="button">选择上传图片</button>' +
            '<input type="file" name="formfile_images" class="formfile_images" accept="image/jpeg,image/png,image/gif">' +
            '</p>' +
            '</div>' +
            '<div class="formfile-button"><p class="hover" id="f-confirm">确定</p><p id="f-cancel">取消</p></div> ' +
            '</div>' +
            '</div>');
        if (imgurl) {
            intemp.find("#formfile_imageurl").val(imgurl);
        }
        //背景
        intemp.find(".jQuery-form-back").css({
            "height":$(document).height()
        }).click(function(){
            tthis.out();
        });
        //点击提示
        intemp.find(".formfile-call").click(function(){
            tthis.call();
        });
        //点击关闭 取消
        intemp.find("#f-cancel").click(function(){
            tthis.out();
        });
        //动画显示
        intemp.find(".jQuery-form-content").animate({top: "30px"});
        //点击选项卡
        intemp.find(".formfile-subtitle p").each(function(index){
            $(this).click(function(){
                intemp.attr("data-subtitle", index);
                intemp.find(".formfile-subtitle>p").removeClass("hover");
                intemp.find(".formfile-content>p").removeClass("hover");
                intemp.find(".formfile-subtitle>p").eq(index).addClass("hover");
                intemp.find(".formfile-content>p").eq(index).addClass("hover");
            });
        });
        //浏览图片空间
        intemp.find("#formfile_imagesbutton").click(function(){
            tthis.viewimages(callback, indexurl, $(this).prev("input").val());
        });
        //点击选择图片
        intemp.find(".formfile_imagebut").click(function(){
            var m = "formimg_" + Math.round(Math.random() * 10000);
            intemp.find(".formfile_images").attr("id", m).attr("name", m).click();
        });
        //选择上传
        intemp.find(".formfile_images").change(function(){
            var files = this.files;
            intemp.find(".formfile_readonly").val(files[0]?files[0].name:'');
        });
        //点击确定
        intemp.find("#f-confirm").click(function(){
            if (!intemp.attr("data-subtitle") || intemp.attr("data-subtitle") == 0) {
                if (intemp.find("#formfile_imageurl").val()) {
                    var reurl = intemp.find("#formfile_imageurl").val();
                    if (reurl != imgurl && callback) callback({'path':reurl, 'fullpath':reurl});
                    tthis.close();
                }else{
                    alert("请选择图片");
                }
            }else if (intemp.attr("data-subtitle") == 1) {
                if (intemp.find(".formfile_readonly").val()) {
                    var fileid = intemp.find(".formfile_images").attr("id");
                    var fileurl = indexurl?indexurl:$("body").attr("data-index");
                    var ajaxurl = fileurl + 'web/system/upfile/' + fileid + '/images/?1=1';
                    if (window.TPL_INIT_AL_USERID) { ajaxurl+= "&userid="+window.TPL_INIT_AL_USERID }
                    tthis.call("正在上传", 1);
                    $.ajaxFileUpload({
                        url: ajaxurl,
                        secureuri: false,
                        fileElementId: fileid,
                        dataType: 'json',
                        success: function (data, status) {
                            if (data != null && data.success != null && data.success) {
                                tthis.call("上传成功");
                                if (callback) callback({'path':data.upload_data.full_path_site, 'fullpath':data.upload_data.full_path_new});
                                tthis.out();
                            } else {
                                tthis.call("上传失败", 1);
                                tthis.alert("上传失败：" + data.message);
                            }
                        },error: function (data, status, e) {
                            tthis.call("上传错误", 1);
                        }
                    });
                }else{
                    alert("请选择要上传的图片");
                }
            }else{

            }
        });
        $("body").append(intemp);
    },
    viewimages: function(callback, indexurl, nowval){
        var tthis = this;
        tthis.close();
        var intemp = $('<div class="jQuery-formfile">' +
            '<div class="jQuery-form-back"></div>' +
            '<div class="jQuery-form-content">' +
            '<div class="formfile-title"><em id="f-cancel">×</em><span class="formfile-call"></span>浏览图片空间的图片</div>' +
            '<div class="formfile-item">正在加载...</div>' +
            '<div class="formfile-button"><p class="hover" id="f-confirm">确定</p><p id="f-cancel">取消</p></div> ' +
            '</div>' +
            '</div>');
        //原路径
        intemp.find(".formfile-item").attr("data-nowval", nowval);
        //背景
        intemp.find(".jQuery-form-back").css({
            "height":$(document).height()
        }).click(function(){
            tthis.out();
        });
        //点击关闭 取消
        intemp.find("#f-cancel").click(function(){
            tthis.out();
        });
        //调整宽度
        var w = $(document).width() * 0.8,
            wl = (w / 2) * -1;
        intemp.find(".jQuery-form-content").css({width:w,marginLeft:wl});
        //点击确定
        intemp.find("#f-confirm").click(function(){
            tthis.call("请点击列表选择");
        });
        //显示
        $("body").append(intemp);
        //加载列表
        tthis.browser(null, indexurl, callback, 'images');
    },
    browser: function(obj, indexurl, callback, uptype){
        var tthis = this;
        var eve = $("div.jQuery-formfile").find(".formfile-item"),fileurl;
        if (eve) {
            if (indexurl) {
                fileurl = indexurl;
                window.formfile_indexurl = fileurl;
            }else{
                fileurl =  window.formfile_indexurl;
            }
            if (callback) {
                window.formfile_callback = callback;
            }else{
                callback =  window.formfile_callback;
            }
            if (uptype) {
                window.formfile_uptype = uptype;
            }else{
                uptype =  window.formfile_uptype;
            }
            fileurl+= "web/system/listup";
            if (uptype) fileurl+= "/" + uptype;
            if (obj) {
                if ($(obj).attr("data-f")){
                    fileurl+= "?path=" + $(obj).attr("data-f");
                }else{
                    var fullpath = $(obj).find("img").attr("data-src")?$(obj).find("img").attr("data-src"):$(obj).find("img").attr("src");
                    if (callback) callback({'path':$(obj).find("#fileurl").val(), 'fullpath':fullpath});
                    tthis.close();
                }
            }
            try{
                fileurl = fileurl.replace(/\|\|/g, "|");
            }catch (e){}
            tthis.alert("加载中");
            $.ajax({
                type: "POST",
                data: {nowval: eve.attr("data-nowval")},
                url: fileurl,
                dataType: "html",
                success: function (html) {
                    tthis.alert(0);
                    eve.html(html);
                    /*var eli = eve.find("ul.formfile-fileview").find("li");
                    if (eli.length == 2 && eli.eq(0).attr("title") == '...' && eli.eq(1).find("div.folder").length > 0) {
                        eli.eq(1).find(">div.fitem").click();
                    }*/
                },
                error: function (msg) {
                    tthis.alert("加载失败");
                }
            });
        }
    },
    inupfile: function(callback, imgurl, indexurl, uptype, uptitle, allowed, size){
        if (!uptitle) {
            uptitle = '文件';
            if (uptype == 'images') uptitle = '图片';
        }
        var tthis = this;
        tthis.close();
        var allowedtis = "";
        allowedtis+= allowed?"  限制格式: "+allowed:"";
        allowedtis+= size?"  限制大小: "+size+"KB":"";
        var intemp = $('<div class="jQuery-formfile">' +
            '<div class="jQuery-form-back"></div>' +
            '<div class="jQuery-form-content">' +
            '<div class="formfile-title"><em id="f-cancel">×</em><span class="formfile-call"></span>请选择'+uptitle+'</div>' +
            '<div class="formfile-subtitle">' +
            '<p class="hover">浏览'+uptitle+'</p>' +
            '<p>上传'+uptitle+'</p>' +
            '</div> ' +
            '<div class="formfile-content">' +
            '<p class="hover">' +
            '<label>'+uptitle+'地址</label>' +
            '<input class="formfile_input" type="text" id="formfile_imageurl" value="" placeholder="请输入'+uptitle+'地址">' +
            '<button class="formfile_button" id="formfile_imagesbutton" type="button">浏览'+uptitle+'空间</button>' +
            '</p>' +
            '<p>' +
            '<label>上传'+uptitle+'</label>' +
            '<input class="formfile_input formfile_readonly" type="text" id="formfile_nourl" placeholder="'+allowedtis+'" readonly>' +
            '<button class="formfile_button formfile_imagebut" type="button">选择上传'+uptitle+'</button>' +
            '<input type="file" name="formfile_images" class="formfile_images">' +
            '</p>' +
            '</div>' +
            '<div class="formfile-button"><p class="hover" id="f-confirm">确定</p><p id="f-cancel">取消</p></div> ' +
            '</div>' +
            '</div>');
        if (imgurl) {
            intemp.find("#formfile_imageurl").val(imgurl);
        }
        //背景
        intemp.find(".jQuery-form-back").css({
            "height":$(document).height()
        }).click(function(){
            tthis.out();
        });
        //点击提示
        intemp.find(".formfile-call").click(function(){
            tthis.call();
        });
        //点击关闭 取消
        intemp.find("#f-cancel").click(function(){
            tthis.out();
        });
        //动画显示
        intemp.find(".jQuery-form-content").animate({top: "30px"});
        //点击选项卡
        intemp.find(".formfile-subtitle p").each(function(index){
            $(this).click(function(){
                intemp.attr("data-subtitle", index);
                intemp.find(".formfile-subtitle>p").removeClass("hover");
                intemp.find(".formfile-content>p").removeClass("hover");
                intemp.find(".formfile-subtitle>p").eq(index).addClass("hover");
                intemp.find(".formfile-content>p").eq(index).addClass("hover");
            });
        });
        //浏览空间
        intemp.find("#formfile_imagesbutton").click(function(){
            tthis.viewupfile(callback, indexurl, $(this).prev("input").val(), uptype, uptitle);
        });
        //点击选择
        intemp.find(".formfile_imagebut").click(function(){
            var m = "formimg_" + Math.round(Math.random() * 10000);
            intemp.find(".formfile_images").attr("id", m).attr("name", m).click();
        });
        //选择上传
        intemp.find(".formfile_images").change(function(){
            var files = this.files;
            intemp.find(".formfile_readonly").val(files[0]?files[0].name:'');
        });
        //点击确定
        intemp.find("#f-confirm").click(function(){
            if (!intemp.attr("data-subtitle") || intemp.attr("data-subtitle") == 0) {
                if (intemp.find("#formfile_imageurl").val()) {
                    var reurl = intemp.find("#formfile_imageurl").val();
                    if (reurl != imgurl && callback) callback({'path':reurl, 'fullpath':reurl});
                    tthis.close();
                }else{
                    alert("请选择"+uptitle);
                }
            }else if (intemp.attr("data-subtitle") == 1) {
                if (intemp.find(".formfile_readonly").val()) {
                    var fileid = intemp.find(".formfile_images").attr("id");
                    var fileurl = indexurl?indexurl:$("body").attr("data-index");
                    var ajaxurl = fileurl + 'web/system/upfile/' + fileid + '/' + uptype + '/?allowed=' + allowed + '&size=' + size;
                    tthis.call("正在上传", 1);
                    if (window.TPL_INIT_AL_USERID) { ajaxurl+= "&userid="+window.TPL_INIT_AL_USERID }
                    $.ajaxFileUpload({
                        url: ajaxurl,
                        secureuri: false,
                        fileElementId: fileid,
                        dataType: 'json',
                        success: function (data, status) {
                            if (data != null && data.success != null && data.success) {
                                tthis.call("上传成功");
                                if (callback) callback({'path':data.upload_data.full_path_site, 'fullpath':data.upload_data.full_path_new});
                                tthis.out();
                            } else {
                                tthis.call("上传失败", 1);
                                tthis.alert("上传失败：" + data.message);
                            }
                        },error: function (data, status, e) {
                            tthis.call("上传错误", 1);
                        }
                    });
                }else{
                    alert("请选择要上传的"+uptitle);
                }
            }else{

            }
        });
        $("body").append(intemp);
    },
    viewupfile: function(callback, indexurl, nowval, uptype, uptitle){
        var tthis = this;
        tthis.close();
        var intemp = $('<div class="jQuery-formfile">' +
            '<div class="jQuery-form-back"></div>' +
            '<div class="jQuery-form-content">' +
            '<div class="formfile-title"><em id="f-cancel">×</em><span class="formfile-call"></span>浏览'+uptitle+'空间的'+uptitle+'</div>' +
            '<div class="formfile-item">正在加载...</div>' +
            '<div class="formfile-button"><p class="hover" id="f-confirm">确定</p><p id="f-cancel">取消</p></div> ' +
            '</div>' +
            '</div>');
        //原路径
        intemp.find(".formfile-item").attr("data-nowval", nowval);
        //背景
        intemp.find(".jQuery-form-back").css({
            "height":$(document).height()
        }).click(function(){
            tthis.out();
        });
        //点击关闭 取消
        intemp.find("#f-cancel").click(function(){
            tthis.out();
        });
        //调整宽度
        var w = $(document).width() * 0.8,
            wl = (w / 2) * -1;
        intemp.find(".jQuery-form-content").css({width:w,marginLeft:wl});
        //点击确定
        intemp.find("#f-confirm").click(function(){
            tthis.call("请点击列表选择");
        });
        //显示
        $("body").append(intemp);
        //加载列表
        tthis.browser(null, indexurl, callback, uptype);
    },
    del: function(obj){
        if (confirm("确定删除并且不可恢复吗？")) {
            var tthis = this;
            var eve = $(obj).parent();
            var fileurl = window.formfile_indexurl;
            if (!fileurl) return;
            tthis.alert("正在删除...");
            $.ajax({
                type: "GET",
                url: fileurl + "web/system/delup?path=" + eve.find("#fileurl").val(),
                dataType: "html",
                success: function (html) {
                    tthis.alert("删除成功", 500);
                    eve.remove();
                },
                error: function (msg) {
                    tthis.alert("删除失败");
                }
            });
        }
    },
    call: function(msg, hide, showk){
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
    out: function(){
        var tthis = this;
        var eve = $("div.jQuery-formfile").find(".jQuery-form-content");
        if (eve.length > 0) {
            eve.animate({top:(eve.outerHeight() + 20) * -1 + "px"}, 300, '', function(){
                tthis.close();
            });
        }
    },
    close: function(){
        $("div.jQuery-formfile").remove();
    }
};

/**
 *
 * @param callback
 * @param imgurl
 * @param indexurl
 * @param uptype 类型：images/videos/voices
 * @param allowed 格式限制
 * @param size 大小限制KB
 */
$.formfile = function(callback, imgurl, indexurl, uptype, allowed, size) {
    var fthis = $.__formfile_fun;
    if (typeof $.ajaxFileUpload == "undefined") {
        fthis.loadjscssfile(fthis.path + "/ajaxfileupload.js", "js");
    }
    fthis.loadjscssfile(fthis.path + "/default.css", "css", function(){
        if (uptype) {
            fthis.inupfile(callback, imgurl, indexurl, uptype, '', allowed, size);
        }else{
            fthis.inimages(callback, imgurl, indexurl);
        }
    });
};