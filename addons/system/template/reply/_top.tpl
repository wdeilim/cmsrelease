
<!DOCTYPE html>
<html>
<head lang="zh-CN">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{#$_A.f.title#} - {#$BASE_NAME#}</title>
    <link rel="stylesheet" href="{#$CSS_PATH#}bootstrap.min.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}bootstrap-switch.min.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}normalize.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}global.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}functions.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}alert.css"/>
    <link rel="stylesheet" href="{#$smarty.const.BASE_URI#}addons/system/template/reply/css/default.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}font-awesome.css"/>
    <script type="text/javascript" src="{#$JS_PATH#}jquery-1.11.0.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.alert.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.cookie.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}bootstrap.min.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}bootstrap-switch.min.js"></script>
</head>
<body style="overflow-y:scroll;">
{#template("header")#}


<div class="wrapper">
    <div class="breadcrumb">
        <div class="items">
            <span class="description">当前位置：</span>
            <span class="ilink"><a href="{#weburl(0, $_A.f.title_en)#}">{#$_A.f.title#}</a></span>
            <i class="iconfont">&#xe621;</i>
            <span>{#$dosetting['title']#}</span>

            {#template('reply_right')#}
        </div>
    </div>

    <div class="topmenu" id="topmenu">
        {#foreach $bindmenu AS $binditem#}
            <a href="{#weburl(0, $_A.f.title_en, $binditem.do)#}"{#if $binditem['do'] == $_GPC['param'][0]#} class="active"{#/if#}>{#$binditem['title']#}</a>
        {#/foreach#}
    </div>

    <div class="main cf custom-menu">
        <div class="mod">
