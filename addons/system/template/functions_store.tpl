<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>功能管理 - {#$BASE_NAME#}</title>
    <link rel="stylesheet" href="{#$CSS_PATH#}admin/normalize.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}admin/global.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}admin/detail.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}font-awesome.css"/>
    <script type="text/javascript" src="{#$JS_PATH#}jquery-1.11.0.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.alert.js"></script>
    <style type="text/css">
        body {overflow-y: hidden}
        .box {position:relative}
        .box iframe {width: 100%;}
        .box .retrunlist{position:absolute;top:0;right:50px;background-color:#0099cb;color:#ffffff;padding:5px 10px;border:1px solid #FFFFFF;border-top:0;height:20px;line-height:20px;}
    </style>
</head>
<body>

{#include file="header_admin.tpl"#}
<script>$('#head-nav-menu>li:eq(1)').addClass('active');</script>

<div class="box">
<a class="retrunlist" href="{#$urlarr.3#}functions/">返回已安装的应用</a>
<iframe src="" id="storeiframe" frameborder="0" scrolling="auto" marginheight="0" marginwidth="0"></iframe>
</div>

<script type="text/javascript">
    $(function(){
        var storeiframe = $("#storeiframe");
        storeiframe.height($(window).height() - 76);
        //
        if ('{#$cloudkey#}'.length >= 32) {
            storeiframe.attr("src", "{#$smarty.const.CLOUD_URL#}store/login.php?key={#$cloudkey#}")
        }else if ('{#$contentset['cloudok']#}' != '1') {
            storeiframe.attr("src", "{#$smarty.const.CLOUD_URL#}store")
        }else{
            $.alert("加载中...",0,1);
            $.ajax({
                url: "{#$urlarr.3#}functionstore/",
                type: "POST",
                data: {cloudkey: 1},
                dataType: "json",
                success: function (data) {
                    $.alert(0);
                    if (data != null && data.success != null && data.success) {
                        storeiframe.attr("src", "{#$smarty.const.CLOUD_URL#}store/login.php?key=" + data.message)
                    }else{
                        storeiframe.attr("src", "{#$smarty.const.CLOUD_URL#}store")
                    }
                },error : function () {
                    $.alert(0);
                    storeiframe.attr("src", "{#$smarty.const.CLOUD_URL#}store")
                },
                cache: false
            });
        }
    });
</script>
{#include file="footer_admin.tpl"#}

</body>
</html>