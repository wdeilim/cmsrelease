
<!DOCTYPE html>
<html>
<head lang="zh-CN">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{#$info.title#} - 公告内容 - {#$BASE_NAME#}</title>
    <link rel="stylesheet" href="{#$CSS_PATH#}normalize.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}global.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}alert.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}font-awesome.css"/>
    <script type="text/javascript" src="{#$JS_PATH#}jquery-1.11.0.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.alert.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.form.min.js"></script>
</head>
<body>
{#include file="header_user.tpl"#}
<script>$('#head-nav-menu li:eq(-1)').addClass('active');</script>



<div class="wrapper">
    <div class="breadcrumb">
        <div class="items">
            <a href="{#$urlarr.2#}">平台首页</a>
            <i class="iconfont">&#xe621;</i>
            <span><a href="{#$urlarr.3#}">公告内容</a></span>
            <i class="iconfont">&#xe621;</i>
            <span>{#$info.title#}</span>
        </div>
    </div>
    <div class="main bulletin-list">
        <div class="hd">
            <h1 class="title">{#$info.title#}<span class="sp">{#$info.indate|date_format:"%Y-%m-%d"#}</span></h1>
            <div class="control">
                <a href="{#$urlarr.3#}" class="button button-primary button-rounded button-small">返回</a>
            </div>
        </div>
        <div class="bd">
			<div class="section">
				{#$info.content#}
			</div>
        </div>
    </div>
</div>


{#include file="footer.tpl"#}

</body>
</html>