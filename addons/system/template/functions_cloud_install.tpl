<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>【{#$_GPC['title']#}】正在安装....</title>
    <link rel="stylesheet" href="{#$CSS_PATH#}admin/normalize.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}admin/global.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}admin/detail.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}font-awesome.css"/>
    <script type="text/javascript" src="{#$JS_PATH#}jquery-1.11.0.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.alert.js"></script>
    <style type="text/css">
        .hide{display:none}
        .closewin {cursor: pointer;padding:3px;position:relative;height:20px;}
        .closewin .co-l {position:absolute;width:33%;top:2px;left:0;border-right: 1px solid #cccccc;}
        .closewin .co-c {position:absolute;width:34%;top:2px;left:33%;border-right: 1px solid #cccccc;}
        .closewin .co-r {position:absolute;width:33%;top:2px;left:67%;}
        .minw {min-width:280px}
    </style>
</head>
<body>

<div id="manifest" class="hide">{#$_GPC['manifest']#}</div>
<div id="cloudfiles" class="hide">{#$_GPC['cloudfiles']#}</div>

<script type="text/javascript">
    var loading2 = '<br/><img src="{#$NOW_PATH#}images/loading2.gif" class="loading2">';
    $(function(){
        _step2($("#manifest").html(), $("#cloudfiles").html());
    });
    function closewin() {
        window.opener = null;
        window.open('','_self');
        window.close();
    }
    function _step2(manifest, cloudfiles)
    {
        var jsonfiles = $.parseJSON(cloudfiles);
        $.alert("准备发送应用包..." + loading2, 0, 1);
        _step2_down(jsonfiles, 0, function(){
            $.alert("发送应用包完成，正在安装数据库..." + loading2, 0, 1);
            setTimeout(function(){
                _step3(JSON.parse(manifest));
            }, 200);
        }, 1);
    }
    function _step2_down(json, i, func, again) {
        if (typeof (json) == "object") {
            var path = "";
            var path_type = "";
            var j = 0;
            $.each(json, function(idx, obj) {
                j++;
                if (path == "") {
                    if (i == 0) {
                        path = obj.path; path_type = obj.type; i = j;
                    }else if (i > 0 && i < j) {
                        path = obj.path; path_type = obj.type; i = j;
                    }
                }
            });
            if (path != "") {
                $.alert("正在发送文件("+i+"/"+j+"): " + path + loading2, 0, 1);
                $.ajax({
                    url: "{#get_link('step|method')#}&method=install_store&step=step2",
                    type: "post",
                    data: {path: path, path_type: path_type, local: 1},
                    dataType: "json",
                    success: function (data) {
                        if (data != null && data.success != null && data.success) {
                            $.alert("成功发送文件("+i+"/"+j+"): " + path + loading2, 0, 1);
                            _step2_down(json, i, func, 1);
                        }else{
                            if (again < 3) {
                                again++;
                                _step2_down(json, i-1, func, again);
                            }else{
                                window.step2_json = json;
                                window.step2_i = i;
                                window.step2_func = func;
                                $.alertb("<div class='minw'>！失败发送文件("+i+"/"+j+"): " + path + data.message + "</div>", '<div class="closewin"><div class="co-l" onclick="_step2_again(1);">重试</div><div class="co-c" onclick="_step2_again(0);">跳过</div><div class="co-r" onclick="closewin();">关闭窗口</div></div>', 1);
                            }
                        }
                    },error : function () {
                        window.step2_json = json;
                        window.step2_i = i;
                        window.step2_func = func;
                        $.alertb("<div class='minw'>！！错误发送文件("+i+"/"+j+"): " + path + "</div>", '<div class="closewin"><div class="co-l" onclick="_step2_again(1);">重试</div><div class="co-c" onclick="_step2_again(0);">跳过</div><div class="co-r" onclick="closewin();">关闭窗口</div></div>', 1);
                    },
                    cache: false
                });
            }else{
                if (func) func();
            }
        }
    }
    function _step2_again(t) {
        if (t == 1) window.step2_i--;
        _step2_down(window.step2_json, window.step2_i, window.step2_func, 1);
    }
    function _step3(manifest)
    {
        $.ajax({
            url: "{#get_link('step|method')#}&method=install_store&step=step3",
            type: "post",
            data: {manifest: manifest},
            dataType: "json",
            success: function (data) {
                if (data != null && data.success != null && data.success) {
                    setTimeout(function(){
                        $("body").append('<div style="display:none"><iframe style="width:0;height:0;opacity:0;" src="{#$_GPC['step4']#}"></iframe></div>');
                        $("title").text("【{#$_GPC['title']#}】安装成功");
                        $.alertb("【{#$_GPC['title']#}】安装成功，可以放心关闭此页面了！", '<div class="closewin" onclick="closewin();">关闭窗口</div>', 1);
                    }, 200);
                }else{
                    $.alertb("失败-3：" + data.message, '<div class="closewin" onclick="closewin();">关闭窗口</div>', 1);
                }
            },error : function () {
                $.alertb("安装错误-3！", '<div class="closewin" onclick="closewin();">关闭窗口</div>', 1);
            },
            cache: false
        });
    }
</script>

</body>
</html>