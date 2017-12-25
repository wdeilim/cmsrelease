<!DOCTYPE html>
<html>
<head>
    <title>{#$content.title#}</title>
    <meta http-equiv=Content-Type content="text/html;charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="format-detection" content="telephone=no">
    <link rel="stylesheet" type="text/css" href="{#$NOW_PATH#}css/mobile-base.css" media="all">
    <script type="text/javascript" src="{#$JS_PATH#}jquery-1.11.0.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.alert.js"></script>
    <script type="text/javascript">
        var wx_shareData = {
            "imgUrl" : "{#$content.img|fillurl#}",
            "link" : "{#get_url()#}",
            "title" : "{#$content.title|addslashes#}",
            "desc" : "{#get_html($content.content,20)|addslashes#}"
        };
        var wx_jssdk = {#json_encode($_A.wx_jssdkConfig)#} || {};
    </script>
    <script src="{#$JS_PATH#}wx_share.js"></script>
</head>
<body>
<div class="bz-page">
    <div class="bz-page-container">
        <div class="bz-text bz-text-title">
            <h1>{#$content.title#}</h1>
            <span class="bz-title-date">{#$content.update|date_format:"%Y-%m-%d"#}</span></div>
        <div class="bz-article">
            <div class="bz-article-content">
                {#if $content.ishowimg#}
                    <p style="text-align:center"><img src="{#$content.img|fillurl#}"/></p>
                {#/if#}
                {#$content.content#}
            </div>
            <div class="rich_media_tool" id="js_toobar">
                {#if $content.url#}<a class="media_tool_meta meta_primary" id="js_view_source" href="{#$content.url#}">阅读原文</a>{#/if#}
                {#*<div id="js_read_area" class="media_tool_meta link_primary meta_primary">
                    阅读 <span id="readNum">{#if $content['rr']>100000#}100000+{#else#}{#$content['rr']|intval#}{#/if#}</span>
                </div>
                <a class="media_tool_meta meta_primary link_primary meta_praise{#if value($smarty.session, 'H_Zz_'|cat:$content.id)#} praised{#/if#}" href="javascript:void(0);" id="like" like="1">
                    <i class="icon_praise_gray"></i>
                    <span class="praise_num" id="likeNum">{#if $content['zz']>100000#}100000+{#else#}{#$content['zz']|intval#}{#/if#}</span>
                </a>*#}
            </div>
        </div>
    </div>
</body>
</html>