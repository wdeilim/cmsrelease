<!DOCTYPE HTML>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=2.0" />
    {#if $gotourl#}
        <meta http-equiv="refresh" content="{#$gototime#};URL={#$gotourl#} "/>
    {#/if#}
    <title>{#$title#}</title>
    <link type="text/css" rel="stylesheet" href="{#$CSS_PATH#}warning.css" />
</head>
<body>
<div class="bodymain">
    {#if $gotourl#}
        <small><a href="{#$gotourl#}" title="点击手动跳转" id="gototime_a"><span id="gototime">{#$gototime#}</span>秒后自动转跳</a></small><br/>
        <script type="text/javascript">
            setTimeout("gototime()",1000);
            function gototime(){
                var docgototime = document.getElementById("gototime").innerHTML;
                var re = new RegExp("^[0-9]+$");
                if (docgototime.search(re) != - 1) {
                    if (docgototime > 1){
                        document.getElementById("gototime").innerHTML = docgototime-1;
                        setTimeout("gototime()",1000);
                    }else if (docgototime == 1){
                        document.getElementById("gototime_a").innerHTML = "加载中，请稍后...";
                    }
                }
            }
        </script>
    {#/if#}

    <div class="war-nav">
        <div class="left" onclick="javascript:history.go(-1);"></div>
        <div class="center">{#$title#}</div>
        <div class="right" style="display:none;" onclick="window.location='{#$smarty.const.BASE_URI#}'"></div>
    </div>

    <div class="warcon">
        <div class="war-t">{#$body#}</div>
        {#$datalink = str_replace('<br/>','',$datalink)#}
        <div class="war-l" id="war-l">{#$datalink#}</div>
    </div>
    <script type="text/javascript">
        var datalink = document.getElementById("war-l").innerHTML;
        var arr = datalink.match(/<a.*?href=[\'|\"]([^\"]*?)[\'|\"]>([^\"]*?)<\/a>/ig);
        var links = '';
        for(var i=0;i<arr.length;i++){
            links += arr[i];
        }
        if (links) document.getElementById("war-l").innerHTML = links;
    </script>


    <div class="footer">
        <p>©{#$TIME|date_format:"%Y-%m-%d %H:%M"#}</p>
    </div>
</div>
</body>
</html>