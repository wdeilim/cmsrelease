
<!DOCTYPE html>
<html>
<head lang="zh-CN">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{#$submit#}多图文素材 - {#$func.f.title#} - {#$BASE_NAME#}</title>
    <link rel="stylesheet" href="{#$CSS_PATH#}normalize.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}global.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}library.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}alert.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}font-awesome.css"/>
    <script type="text/javascript" src="{#$JS_PATH#}jquery-1.11.0.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.alert.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.form.min.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}ajaxfileupload.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}ueditor/ueditor.config.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}ueditor/ueditor.all.min.js"></script>
</head>
<body>
{#template("header")#}


<div id="template-list" style="display:none">
    <div class="appmsg-item appmsg-box cf">
        <img class="appmsg-thumb" id="appmsg-thumb" src="">
        <i class="appmsg-thumb default">缩略图</i>
        <h4 class="appmsg-title"><a href="javascript:;" id="appmsg-title"></a></h4>
        <div class="appmsg-edit-mask">
            <a href="javascript:;" onclick="_event(this,'editlist');"><i class="iconfont">&#xe626;</i></a>
            <a href="javascript:;" onclick="_event(this,'dellist');"><i class="iconfont">&#xe61b;</i></a>
        </div>
    </div>
</div>
<div id="template-content" style="display:none">
    <div class="media-edit-area">
        <div class="appmsg-editor">
            <div class="inner">
                <div class="appmsg-edit-item">
                    <label class="form-label">标题</label>
                    <div class="form-input-box">
                        <input class="form-input" type="text" did="title" onkeyup="_event(this,'intitle');"/>
                    </div>
                </div>
                <div class="appmsg-edit-item">
                    <label class="form-label">作者</label>
                    <div class="form-input-box">
                        <input class="form-input" type="text" did="author" value="{#$func.uf.al_name#}"/>
                    </div>
                </div>
                <div class="appmsg-edit-item">
                    <label class="form-label">排序</label>
                    <div class="form-input-box" style="width:60px;">
                        <input class="form-input" type="text" did="inorder" value="0"/>
                    </div>
                </div>
                <div class="appmsg-edit-item">
                    <label class="form-label">封面</label>
                    <div class="form-input-upbox">
                        {#str_replace('name="img"','did="img"',tpl_form_image("img",'','','','_eventimg()'))#}
                        <span class="help-inline" style="color:#ff0000;">建议大小 (宽720 高400)</span>
                    </div>
                    <div class="form-tips">
                        <label><input type="checkbox" did="ishowimg"/>封面图片显示在正文中</label>
                    </div>
                </div>
                <div class="appmsg-edit-item">
                    <label class="form-label">正文</label>
                    <script did="content" type="text/plain" style="width: 100%;height: 300px;"></script>
                </div>
                <div class="appmsg-edit-item">
                    <a class="normal-link" href="javascript:;" onclick="_event(this,'link');">添加原文链接</a>
                </div>
                <div class="appmsg-edit-item" style="display: none;">
                    <label class="form-label">原文链接</label>
                    <div class="form-input-box">
                        <input class="form-input" type="text" did="url" />
                    </div>
                </div>
            </div>
            <i class="arrow arrow-out"></i><i class="arrow arrow-in"></i>
        </div>
    </div>
</div>

<!-- 正文 -->

<div class="wrapper">
    <div class="breadcrumb">
        <div class="items">
            <span class="description">当前位置：</span>
            <span>{#$func.f.title#}</span>
            <i class="iconfont">&#xe621;</i>
            <span>{#$submit#}多图文素材</span>
        </div>
    </div>
    <div class="main cf custom-menu">
        <div class="mod">
            <div class="main-bd">
                <div class="control-group">
                    <a class="button" href="{#$urlarr.2#}{#get_get()#}">素材列表</a>
                    <a class="button" href="{#$urlarr.2#}onlyimg/{#get_get()#}">+新建单图文素材</a>
                    <a class="button button-hover" href="{#$urlarr.2#}manyimg/{#get_get()#}">+{#$submit#}多图文素材</a>
                </div>



                <form action="{#$urlarr.now#}{#get_get()#}"  method="post" id="saveform">
                    <div class="media cf" id="media-box">

                        <div class="media-preview-area">
                            <div class="appmsg multi editing">
                                <div class="appmsg-content" id="appmsg-content">

                                    <div class="appmsg-box" id="box-l-0" dm="0">
                                        <div class="appmsg-info"><em class="appmsg-date"></em></div>
                                        <div class="cover-appmsg-item">
                                            <h4 class="appmsg-title"><a href="javascript:;" id="appmsg-title"></a></h4>
                                            <div class="appmsg-thumb-wrap">
                                                <img class="appmsg-thumb" id="appmsg-thumb" src="">
                                                <i class="appmsg-thumb default">封面图片</i>
                                            </div>
                                            <div class="appmsg-edit-mask">
                                                <a href="javascript:;" onclick="_event(this,'editmain');"><i class="iconfont">&#xe626;</i></a>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- list -->

                                </div>
                                <div class="appmsg-add">
                                    <a href="javascript:;" onclick="_event(this,'addlist');"><i class="iconfont">+</i></a>
                                </div>
                            </div>
                        </div>

                        <!-- content -->
                    </div>
                    <div class="tool-area">
                        <div class="control-submit al-center">
                            <input class="button" type="submit" value="{#$submit#}"> &nbsp;
                            <input class="button" type="button" value="返回" onclick="location.href='{#$urlarr.2#}{#get_get()#}';">
                            <input type="hidden" name="dosubmit" value="1">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script id="settingjson" type="text/plain" style="display:none;">{#value($content,'settingjson')#}</script>
<script type="text/javascript">
    function _event(obj, t){
        if (t == 'addlist'){
            //添加行
            if ($("div[dt=content]").length >= 10) {
                $.inModal("最多只能添加10条"); return;
            }
            var m = Math.round(Math.random() * 10000);
            var $imitate = $($('#template-list').html());
            var $content = $($('#template-content').html());
            $imitate.attr("dt", "list").attr("dm",m).attr("id","box-l-"+m);
            $content.attr("dt", "content").attr("dm",m).attr("id","box-c-"+m);
            $("#appmsg-content").append($imitate);
            $("#media-box").append($content);
            $content.css("padding-top", $imitate.offset().top - 194);
            //
            $("div[dt=content]").hide();
            $("#box-c-"+m).show();
            newname(m, obj);
            return m;
        }else if (t == 'dellist'){
            //删除行
            var eve = $(obj);
            eve.parent().parent().remove();
            $("#box-c-"+eve.parent().parent().attr("dm")).remove();
            $("div[dt=content]").hide();
            $("div[dt=content]:eq(0)").show();
        }else if (t == 'editlist'){
            //编辑行
            var eve = $(obj);
            $("div[dt=content]").hide();
            $("#box-c-"+eve.parent().parent().attr("dm")).show();
        }else if (t == 'editmain'){
            //编辑首行
            $("div[dt=content]").hide();
            $("div[dt=content]:eq(0)").show();
        }else if (t == 'intitle'){
            //输入同步
            var eve = $(obj);
            $("#box-l-"+eve.parents("div[dt=content]").attr("dm")).find("#appmsg-title").text(eve.val());
        }else if (t == 'delimg'){
            //删除封面
            var eve = $(obj);
            eve.parent().hide();
            eve.parent().next("input[did=img]").val('');
            var pvp = $("#box-l-"+eve.parents("div[dt=content]").attr("dm")).find("#appmsg-thumb");
            pvp.hide();
            pvp.next("i").show();
        }else if (t == 'selimg'){
            //弹出上传图片选择
            var eve = $(obj);
            eve.parent().prev().click();
        }else if (t == 'link'){
            //添加原文链接
            var eve = $(obj);
            eve.parent().hide();
            eve.parent().next().show();
        }else{
            //默认加载第一行
            var m = 0;
            var $content = $($('#template-content').html());
            $content.attr("dt", "content").attr("dm",m).attr("id","box-c-"+m);
            $("#media-box").append($content);
            //
            $("div[dt=content]").hide();
            $("#box-c-"+m).show();
            newname(m, obj);
            return m;
        }
    }
    function _eventimg() {
        $("#media-box").find("input[did='img']").each(function(index){
            var tthis = $("#media-box").find("img[class='appmsg-thumb']").eq(index);
            tthis.attr("src", _base_uri($(this).val()));
            if ($(this).val()) {
                tthis.show();
                tthis.next("i").hide();
            }else{
                tthis.hide();
                tthis.next("i").show();
            }
        });
    }
    function _base_uri(src) {
        if (src.substring(0,4)=='http' || src.substring(0,1)=='/') {
            return src;
        }else{
            return "{#$smarty.const.BASE_URI#}" + src;
        }
    }
    window.listi = 0;
    function newname(m, obj){
        window.listi++;
        var eve = $("#box-c-"+m);
        eve.find("input,script").each(function(){
            if ($(this).attr('did')){
                $(this).attr('id', 'many_'+window.listi+'_'+$(this).attr('did'))
                $(this).attr('name', 'many_'+window.listi+'_'+$(this).attr('did'))
            }
        });
        eve.find("input#file_img").attr("name", "file_img_"+window.listi);
        eve.find("input#file_img").attr("id", "file_img_"+window.listi);
        //自动输入排序
        var inor = 0;
        $("input[did=inorder]").each(function(){
            var _inor = parseInt($(this).val());
            if (_inor != "NaN"){
                if (_inor > inor) inor = _inor;
            }
        });
        eve.find("input[did=inorder]").val(inor + 1);
        if (obj !== null) UE.getEditor("many_"+window.listi+"_content", {autoHeightEnabled:false});
    }

    $(document).ready(function() {
        $('#saveform').submit(function() {
            $.alert('正在保存...', 0);
            $(this).ajaxSubmit({
                dataType : 'json',
                success : function (data) {
                    if (data != null && data.success != null && data.success) {
                        $.alert(0);
                        $.showModal(data.message, '{#$urlarr.2#}{#get_get()#}');
                    } else {
                        $.alert(data.message);
                    }
                },
                error : function () {
                    $.alert(0);
                    $.inModal("保存失败！");
                }
            });
            return false;
        });
        {#if value($content,'settingjson')#}
        _event(null);
        var _json = eval($("#settingjson").html());
        for(var i=0; i<_json.length; i++){
            var m = 0;
            if (i > 0) m = _event(null, 'addlist');
            var evel = $("#box-l-"+m);
            var evec = $("#box-c-"+m);
            //左边
            evel.find("#appmsg-title").text(_json[i].title);
            evel.find("#appmsg-thumb").attr('src', _base_uri(_json[i].img));
            evel.find("#appmsg-thumb").show();
            evel.find("#appmsg-thumb").next().hide();
            //右边
            evec.find("input[did=title]").val(_json[i].title);
            evec.find("input[did=author]").val(_json[i].author);
            evec.find("input[did=inorder]").val(_json[i].inorder);
            evec.find("input[did=img]").val(_json[i].img);
            evec.find("input[did=img]").parent().next().find("img").attr("src", _base_uri(_json[i].img));
            evec.find("input[did=url]").val(_json[i].url);
            evec.find('script[did=content]').html(_json[i].content);
            UE.getEditor(evec.find('script[did=content]').attr('id'), {autoHeightEnabled:false});
            //
            evec.find("div#divimg").find("img").attr('src', _json[i].img);
            evec.find("div#divimg").show();
            if (_json[i].ishowimg && _json[i].ishowimg != '' && _json[i].ishowimg != '0'){
                evec.find("input[did=ishowimg]").prop("checked",true);
            }
            if (_json[i].url){
                evec.find('input[did=url]').parent().parent().prev().hide();
                evec.find('input[did=url]').parent().parent().show();
            }
            $("div[dt=content]").hide();
            $("div[dt=content]:eq(0)").show();
        }
        if (_json.length == 0){
            var evec = $("#box-c-0");
            UE.getEditor(evec.find('script[did=content]').attr('id'), {autoHeightEnabled:false});
        }
        {#else#}
        _event();
        {#/if#}
    });
</script>

{#template("footer")#}

</body>
</html>