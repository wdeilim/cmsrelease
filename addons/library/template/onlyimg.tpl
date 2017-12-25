
<!DOCTYPE html>
<html>
<head lang="zh-CN">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{#$submit#}单图文素材 - {#$func.f.title#} - {#$BASE_NAME#}</title>
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


<div class="wrapper">
    <div class="breadcrumb">
        <div class="items">
            <span class="description">当前位置：</span>
            <span>{#$func.f.title#}</span>
            <i class="iconfont">&#xe621;</i>
            <span>{#$submit#}单图文素材</span>
        </div>
    </div>
    <div class="main cf custom-menu">
        <div class="mod">
            <div class="main-bd">
                <div class="control-group">
                    <a class="button" href="{#$urlarr.2#}{#get_get()#}">素材列表</a>
                    <a class="button button-hover" href="{#$urlarr.2#}onlyimg/{#get_get()#}">+{#$submit#}单图文素材</a>
                    <a class="button" href="{#$urlarr.2#}manyimg/{#get_get()#}">+新建多图文素材</a>
                </div>



                <form action="{#$urlarr.now#}/{#get_get()#}"  method="post" id="saveform">
                    <div class="media cf" id="media-box">
                        <div class="media-preview-area" data-bind="with: item">
                            <div class="appmsg editing">
                                <div class="appmsg-content">
                                    <div>
                                        <h4 class="appmsg-title only"><a href="javascript:;" id="appmsg-title">{#value($content,'title')#}</a></h4>
                                        <div class="appmsg-info">
                                            <em class="appmsg-date"></em>
                                        </div>
                                        <div class="appmsg-thumb-wrap" id="appmsg-thumb">
                                            <img class="appmsg-thumb"  alt="" src="{#value($content,'img')|fillurl#}"/>
                                            <i class="appmsg-thumb default">封面图片</i>
                                        </div>
                                        <p class="appmsg-desc" id="appmsg-desc">{#value($content,'descriptions')#}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="media-edit-area" data-bind="with: item">
                            <div class="appmsg-editor">
                                <div class="inner">
                                    <div class="appmsg-edit-item">
                                        <label class="form-label">标题</label>
                                        <div class="form-input-box">
                                            <input class="form-input" type="text" id="title" name="title" onkeyup="_intitle(this);" value="{#value($content,'title')#}"/>
                                        </div>
                                    </div>
                                    <div class="appmsg-edit-item">
                                        <label class="form-label">作者</label>

                                        <div class="form-input-box">
                                            <input class="form-input" type="text" id="author" name="author" value="{#if $content#}{#value($content,'author')#}{#else#}{#$func.uf.al_name#}{#/if#}"/>
                                        </div>
                                    </div>
                                    <div class="appmsg-edit-item">
                                        <label class="form-label">封面</label>

                                        <div class="form-input-upbox">
                                            {#tpl_form_image("img", value($content,'img'),'','','_eventimg()')#}
                                            <span class="help-inline" style="color:#ff0000;">建议大小 (宽720 高400)</span>
                                        </div>

                                        <div class="form-tips">
                                            <label><input type="checkbox" id="ishowimg" name="ishowimg"{#if value($content,'ishowimg')#} checked="true"{#/if#}/>封面图片显示在正文中</label>
                                        </div>
                                    </div>

                                    <div class="appmsg-edit-item" style="display:none">
                                        <a class="normal-link" href="javascript:;" onclick="_addnext(this);">添加摘要</a>
                                    </div>
                                    <div class="appmsg-edit-item">
                                        <label class="form-label">摘要</label>
                                        <div class="form-textarea-box">
                                            <textarea class="form-textarea" id="descriptions" name="descriptions"
                                                      onkeyup="_indesc(this);">{#value($content,'descriptions')#}</textarea>
                                        </div>
                                    </div>

                                    <div class="appmsg-edit-item">
                                        <label class="form-label">正文</label>
                                        <script id="content" name="content" type="text/plain" style="width: 100%;height: 300px;">{#value($content,'content')#}</script>
                                    </div>
                                    <div class="appmsg-edit-item">
                                        <a class="normal-link" href="javascript:;" onclick="_addnext(this);">添加原文链接</a>
                                    </div>
                                    <div class="appmsg-edit-item" style="display: none;">
                                        <label class="form-label">原文链接</label>
                                        <div class="form-input-box">
                                            <input class="form-input" type="text" id="url" name="url" value="{#value($content,'url')#}"/>
                                        </div>
                                    </div>

                                </div>
                                <i class="arrow arrow-out"></i>
                                <i class="arrow arrow-in"></i>
                            </div>
                        </div>
                    </div>
                    <div class="tool-area">
                        <div class="control-submit al-center">
                            <input class="button" type="submit" value="{#$submit#}"> &nbsp;
                            <input class="button" type="button" value="返回" onclick="location.href='{#$urlarr.2#}';">
                            <input type="hidden" name="dosubmit" value="1">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function _intitle(obj){
        var eve = $(obj);
        $("#appmsg-title").text(eve.val());
    }
    function _indesc(obj){
        var eve = $(obj);
        $("#appmsg-desc").text(eve.val());
    }
    function _eventimg() {
        $("#media-box").find("input[name='img']").each(function(index){
            $("#media-box").find("img[class='appmsg-thumb']").eq(index).attr("src", _base_uri($(this).val()));
            if ($(this).val()) {
                $("#appmsg-thumb").find("img").show();
                $("#appmsg-thumb").find("i").hide();
            }else{
                $("#appmsg-thumb").find("img").hide();
                $("#appmsg-thumb").find("i").show();
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
    function _addnext(obj){
        $(obj).parent().hide();
        $(obj).parent().next().show();
    }
    $(document).ready(function() {
        var ue = UE.getEditor('content',{autoHeightEnabled:false});
        //
        $('#select_img').click(function() {
            $('#file_img').click();
        });
        //
        var eve = $('#saveform');
        if (eve.find('input#img').val()){
            $("#appmsg-thumb").find("img").show();
            $("#appmsg-thumb").find("i").hide();
            $("#divimg").show();
        }
        /*
        if (eve.find('textarea#descriptions').val()){
            eve.find('textarea#descriptions').parent().parent().prev().hide();
            eve.find('textarea#descriptions').parent().parent().show();
        }
        */
        if (eve.find('input#url').val()){
            eve.find('input#url').parent().parent().prev().hide();
            eve.find('input#url').parent().parent().show();
        }
        eve.submit(function() {
            $.alert('正在保存...', 0);
            $(this).ajaxSubmit({
                dataType : 'json',
                success : function (data) {
                    if (data != null && data.success != null && data.success) {
                        $.alert(0);
                        $.showModal(data.message, '{#$urlarr.2#}');
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
    });
</script>

{#template("footer")#}

</body>
</html>