
<!DOCTYPE html>
<html>
<head lang="zh-CN">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{#$_A.f.title#} - {#$BASE_NAME#}</title>
    <link rel="stylesheet" href="{#$CSS_PATH#}bootstrap.min.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}normalize.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}global.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}functions.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}alert.css"/>
    <link rel="stylesheet" href="{#$NOW_PATH#}css/default.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}font-awesome.css"/>
    <script type="text/javascript" src="{#$JS_PATH#}jquery-1.11.0.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.alert.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}bootstrap.min.js"></script>
</head>
<body>
{#template("header")#}


<div class="wrapper">
    <div class="breadcrumb">
        <div class="items">
            <span class="description">当前位置：</span>
            <span>{#$_A.f.title#}</span>
        </div>
    </div>


    <div class="main cf custom-menu">
        <div class="mod">

            <div class="clearfix welcome-container">
                <div class="page-header">
                    <h4><i class="fa fa-plane"></i> 核心功能设置</h4>
                </div>
                <div class="shortcut clearfix">
                    {#if $_A['f']['reply']#}
                        <a href="{#weburl(0, $_A.f.title_en)#}&entry=reply">
                            <i class="fa fa-comments"></i>
                            <span>回复规则列表</span>
                        </a>
                    {#/if#}
                    {#foreach $_A['f']['setting']['bindings']['cover'] AS $item#}
                        <a href="{#weburl(0, $_A.f.title_en)#}&entry=cover&do={#$item['do']#}" {#$item['attr']#}>
                            <i class="fa fa-external-link-square"></i>
                            <span title="{#$item['title']#}">{#$item['title']#}</span>
                        </a>
                    {#/foreach#}
                    {#foreach $_A['f']['setting']['bindings']['setting'] AS $item#}
                        <a href="{#weburl(0, $_A.f.title_en)#}&entry=setting&do={#$item['do']#}" {#$item['attr']#}>
                            <i class="fa fa-cog"></i>
                            <span title="{#$item['title']#}">{#$item['title']#}</span>
                        </a>
                    {#/foreach#}
                </div>
                {#if $_A['f']['setting']['bindings']['menu']#}
                    <div class="page-header">
                        <h4><i class="fa fa-plane"></i> 业务功能菜单</h4>
                    </div>
                    <div class="shortcut clearfix">
                        {#foreach $_A['f']['setting']['bindings']['menu'] AS $item#}
                            <a href="{#weburl(0, $_A.f.title_en)#}&entry=menu&do={#$item['do']#}" {#$item['attr']#}>
                                <i class="fa fa-puzzle-piece"></i>
                                <span title="{#$item['title']#}">{#$item['title']#}</span>
                            </a>
                        {#/foreach#}
                    </div>
                {#/if#}
            </div>
        </div>
    </div>
</div>


{#template("footer")#}

<script type="text/javascript">
    $(function () {
        if ($(".welcome-container").find("a").length < 1) {
            window.location.href = '{#weburl(0, $_A.f.title_en)#}&entry=reply';
        }
    })
</script>

</body>
</html>