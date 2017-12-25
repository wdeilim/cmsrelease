<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>【{#$module['title']#}】正在升级....</title>
    <link rel="stylesheet" href="{#$CSS_PATH#}admin/normalize.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}admin/global.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}admin/detail.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}font-awesome.css"/>
    <script type="text/javascript" src="{#$JS_PATH#}jquery-1.11.0.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.alert.js"></script>
</head>
<body>
<script type="text/javascript">
    $(function(){
        $.alert("准备下载升级包，安装过程请勿关闭当前页！",0,1);
        setTimeout(function(){
            _step();
        }, 500);
    });
    function _step()
    {
        var jsonfiles = {#$cloudfiles#} || {};
        $.alert("正在下载升级包...",0,1);
        _step_down(jsonfiles, 0, function(){
            $.alert("下载升级包完成，正在升级数据库...", 0, 1);
            setTimeout(function(){
                window.location.href = "{#get_url("act")#}&act=success";
            }, 500);
        });
    }
    function _step_down(json, i, func) {
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
                $.alert("正在更新文件("+i+"/"+j+"): " + path, 0, 1);
                $.ajax({
                    url: "{#get_url("act")#}&act=updatefile",
                    type: "post",
                    data: {path: path, path_type: path_type},
                    dataType: "json",
                    success: function (data) {
                        if (data != null && data.success != null && data.success) {
                            $.alert("成功更新文件("+i+"/"+j+"): " + path, 0, 1);
                            _step_down(json, i, func);
                        }else{
                            $.alert("！失败更新文件("+i+"/"+j+"): " + path, 0, 1);
                        }
                    },error : function () {
                        $.alert("！！错误更新文件("+i+"/"+j+"): " + path, 0, 1);
                    },
                    cache: false
                });
            }else{
                if (func) func();
            }
        }
    }
</script>

</body>
</html>