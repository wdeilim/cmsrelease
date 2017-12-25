
<div id="selhtml">
    <div class="selclick" onmousemove="keytextmove(this,1,0);" onmouseout="keytextmove(this,1,1);">
        <a href="javascript:;" onclick="sellibrary(this);">从素材库中选择</a>
        <a href="javascript:;" onclick="selreply(this);">从自动回复选择</a>
        <a href="javascript:;" onclick="selmodule(this);">其他模块关键词</a>
    </div>
</div>
<div id="selhtmlurl">
    <div class="selclick" onmousemove="keytextmove(this,1,0);" onmouseout="keytextmove(this,1,1);">
        <a href="javascript:;" onclick="sellibraryurl(this);">从素材库中选择</a>
        <a href="javascript:;" onclick="selmoduleurl(this);">其他模块中选择</a>
    </div>
</div>
<div id="selhtmlshow">
    <div class="selclick">
        <div class="selshow"></div>
    </div>
</div>
<script type="text/javascript">
    function keytextkeyup(obj) {
        var tthis = $(obj);
        var keytype = tthis.parents("td").prev().find("#keytype").val();
        tthis.attr("data-"+keytype+"-val", tthis.val());
    }
    function keytextmove(obj, hi, n) {
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
        var keytype = tthis.parents("td").prev().find("#keytype").val();
        if (keytype == 'click' || keytype == 'out') {
            //发送信息
            tthis.next(".selclick").remove();
            $inhtml = $($("#selhtml").html());
            $inhtml.attr("data-name", tthis.attr("name"));
            tthis.after($inhtml);
        }else if (keytype == 'view' || keytype == 'link') {
            //跳转URL
            tthis.next(".selclick").remove();
            $inhtml = $($("#selhtmlurl").html());
            $inhtml.attr("data-name", tthis.attr("name"));
            tthis.after($inhtml);
        }else if (keytype == 'tel') {
            tthis.next(".selclick").remove();
            $inhtml = $($("#selhtmlshow").html());
            $inhtml.find(".selshow").text("提示：输入电话号码！");
            tthis.after($inhtml);
        }else if (keytype == 'alipay') {
            tthis.next(".selclick").remove();
            $inhtml = $($("#selhtmlshow").html());
            $inhtml.find(".selshow").text("提示：填写关键词，例如“酒店”！");
            tthis.after($inhtml);
        }else if (keytype == 'alipay3') {
            tthis.next(".selclick").remove();
            $inhtml = $($("#selhtmlshow").html());
            $inhtml.find(".selshow").text("提示：请填写支付宝账户名！");
            tthis.after($inhtml);
        }
    }


    function sellibrary(obj) {
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
        $("body").append(intemp);
        //
        window.__librarypage_nowsel = $("input[name='"+$(obj).parent().attr("data-name")+"']").val();
        //加载列表
        __librarypage(null, 1, '', function(obj2) {
            var v = "{素材库ID:"+$(obj2).attr("data-id")+"}";
            $("input[name='"+$(obj).parent().attr("data-name")+"']").val(v);
        });
    }

    function __librarypage(obj, page, key, callback) {
        if (callback) {
            window.__librarypage_callback = callback;
        }else{
            callback =  window.__librarypage_callback;
        }
        if (obj) {
            if (callback) callback(obj);
            $("div.jQuery-formfile").remove();
            return true;
        }
        $.ajax({
            type: "GET",
            url: "{#weburl("library/alieditlist/menu")#}&page=" + page + "&keyval=" + (key?key:''),
            dataType: "html",
            success: function (html) {
                $("div.jQuery-formfile").find(".formfile-item").html(html);
                if (window.__librarypage_nowsel) {
                    try {
                        var Id = (/\{素材库ID\:(.*?)\}.*$/g).exec(window.__librarypage_nowsel)[1];
                        if (Id == parseInt(Id)) {
                            $("#appmsg-col-" + Id).find(">div.appmsg").css({'border':'1px solid #ff0000','box-shadow':'0px 1px 8px #ff0000'});
                        }
                    }catch(e){}
                }
            },
            error: function (msg) {
                alert("加载失败");
                $("div.jQuery-formfile").remove();
            }
        });
    }

    function sellibraryurl(obj) {
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
        window.__libraryurlpage_nowsel = $("input[name='"+$(obj).parent().attr("data-name")+"']").val();
        //加载列表
        __libraryurlpage(null, 1, '', function(obj2) {
            var url = $(obj2).find("a").attr("data-href");
            $("input[name='"+$(obj).parent().attr("data-name")+"']").val(url?url:'');
        });
    }

    function __libraryurlpage(obj, page, key, callback) {
        if (callback) {
            window.__libraryurlpage_callback = callback;
        }else{
            callback =  window.__libraryurlpage_callback;
        }
        if (obj) {
            if (callback) callback(obj);
            $("div.jQuery-formfile").remove();
            return true;
        }
        $.ajax({
            type: "GET",
            url: "{#weburl("library/alieditlist/menuurl")#}&page=" + page + "&keyval=" + (key?key:''),
            dataType: "html",
            success: function (html) {
                $("div.jQuery-formfile").find(".formfile-item").html(html);
                if (window.__libraryurlpage_nowsel) {
                    try {
                        $(".appmsg-showy").each(function(){
                            if ($(this).find("a").attr("data-href") == window.__libraryurlpage_nowsel) {
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
    }

    function selreply(obj) {
        $("div.jQuery-formfile").remove();
        var intemp = $('<div class="jQuery-formfile">' +
                '<div class="jQuery-form-back"></div>' +
                '<div class="jQuery-form-content">' +
                '<div class="formfile-title"><em id="f-cancel">×</em><span class="formfile-call"></span>选择自动回复内容</div>' +
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
            alert("请点击列表内关键词选择！");
        });
        //显示
        $("body").append(intemp); //
        window.__replypage_nowsel = $("input[name='"+$(obj).parent().attr("data-name")+"']").val();
        //加载列表
        __replypage(null, 1, '', function(key) {
            $("input[name='"+$(obj).parent().attr("data-name")+"']").val(key);
        });
    }

    function __replypage(obj, page, key, callback) {
        if (callback) {
            window.__replypage_callback = callback;
        }else{
            callback =  window.__replypage_callback;
        }
        $.ajax({
            type: "GET",
            url: "{#weburl("reply/alieditlist/menu")#}&page=" + page + "&keyval=" + (key?key:''),
            dataType: "html",
            success: function (html) {
                $("div.jQuery-formfile").find(".formfile-item").html(html);
                if (window.__replypage_nowsel) {
                    try {
                        $(".replykey[title='"+window.__replypage_nowsel+"']").css({'background-color':'#F091A3'});
                    }catch(e){}
                }
                $("em.replykey").unbind().click(function(){
                    if (callback) callback($(this).attr("title"));
                    $("div.jQuery-formfile").remove();
                });
            },
            error: function (msg) {
                alert("加载失败");
                $("div.jQuery-formfile").remove();
            }
        });
    }

    function selmodule(obj) {
        $("div.jQuery-formfile").remove();
        var intemp = $('<div class="jQuery-formfile">' +
                '<div class="jQuery-form-back"></div>' +
                '<div class="jQuery-form-content">' +
                '<div class="formfile-title"><em id="f-cancel">×</em><span class="formfile-call"></span>选择其他模块关键词<small style="font-size:60%;color:#aaaaaa;padding-left:15px;">仅支持有<u>关键词走势</u>的应用模块</small></div>' +
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
            alert("请点击列表内关键词选择！");
        });
        //显示
        $("body").append(intemp); //
        window.__modulepage_nowsel = $("input[name='"+$(obj).parent().attr("data-name")+"']").val();
        //加载列表
        __modulepage(null, 1, '', function(key) {
            $("input[name='"+$(obj).parent().attr("data-name")+"']").val(key);
        });
    }

    function __modulepage(obj, page, key, callback) {
        if (callback) {
            window.__modulepage_callback = callback;
        }else{
            callback =  window.__modulepage_callback;
        }
        $.ajax({
            type: "GET",
            url: "{#weburl("menu/reply")#}&page=" + page + "&keyval=" + (key?key:''),
            dataType: "html",
            success: function (html) {
                $("div.jQuery-formfile").find(".formfile-item").html(html);
                if (window.__modulepage_nowsel) {
                    try {
                        $(".replykey[title='"+window.__modulepage_nowsel+"']").css({'background-color':'#F091A3'});
                    }catch(e){}
                }
                $("em.replykey").unbind().click(function(){
                    if (callback) callback($(this).attr("title"));
                    $("div.jQuery-formfile").remove();
                });
            },
            error: function (msg) {
                alert("加载失败");
                $("div.jQuery-formfile").remove();
            }
        });
    }

    function selmoduleurl(obj) {
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
        window.__moduleurlpage_nowsel = $("input[name='"+$(obj).parent().attr("data-name")+"']").val();
        //加载列表
        __moduleurlpage(null, 1, '', function(url) {
            $("input[name='"+$(obj).parent().attr("data-name")+"']").val(url);
        });
    }

    function __moduleurlpage(obj, page, key, callback) {
        if (callback) {
            window.__moduleurlpage_callback = callback;
        }else{
            callback =  window.__moduleurlpage_callback;
        }
        $.ajax({
            type: "GET",
            url: "{#weburl("menu/reply/url")#}&page=" + page + "&keyval=" + (key?key:''),
            dataType: "html",
            success: function (html) {
                $("div.jQuery-formfile").find(".formfile-item").html(html);
                var reply_url_tr = $(".reply_url").find("tr");
                if (window.__moduleurlpage_nowsel) {
                    try {
                        reply_url_tr.each(function(){
                            if ($(this).attr("data-url") == window.__moduleurlpage_nowsel) {
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
    }
</script>