
<!DOCTYPE HTML>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta content="telephone=no" name="format-detection">
    <title>最新通知</title>
    <link rel="stylesheet" href="{#$NOW_PATH#}css/inner.css">
    <script type="text/javascript" src="{#$JS_PATH#}jquery.min.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.alert.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}ul-list-more.js"></script>
    <style type="text/css">
        .showMore{height:36px;width:98%;margin:12px auto;border:0px solid #e8e8e8;}
        .showMore a{color:#3d3d3d;background:#eee;font-size:16px;display:block;border:1px solid #ccc;line-height:36px;padding:3px 6px;background:-webkit-gradient(linear,0 0,0 100%,from(#ffffff),to(#e8e8e8));border-radius:5px;text-align: center;}
        .showMore a.no{color:#aaaaaa;cursor:text;background:-webkit-gradient(linear,0 0,0 100%,from(#F1F1F1),to(#C7C7C7));}
        .list .red {background-color: #FFDFDF;}
    </style>
</head>

<body>
<div class="layout">
    <header>
        <img src="{#vippagebanner('notification', $_A['vip_data']['banner'])#}" class="hd-banner">
    </header>
    <div class="wrap">
        <ul class="list" id="ul-list">
            <!-- list start -->
            {#foreach from=$lists.list item=list#}
            <li class="item{#isto($list['view'],'0',' red')#}" data-id="{#$list['contentid']#}">
                <div class="hd dd">
                    <i class="vip-icon icon-new">NEW</i>
                    <h2{#cot($list['title_color'])#}>{#$list['title']#}</h2>
                    <time>{#date("Y-m-d",$list['indate'])#}</time>
                    <span class="btn-menu btn-open">+</span>
                </div>
                <div class="bd">
                    <p>{#$list['content']#}</p>
                </div>
            </li>
            {#foreachelse#}
            <li class="item">
                没有任何通知！
            </li>
            {#/foreach#}
            <!-- list end -->
        </ul>
    </div>

    <!-- page start -->
    <script>
        var total = parseInt("{#$lists['total']#}"), //总数量
                pageNo = parseInt("{#$lists['nowpage']#}"), //当前页
                perpage = parseInt("{#$lists['perpage']#}"), //每页显示
                pageUrl = "{#appurl(3)#}&page=";
    </script>
    <div class="showMore"><a href="javascript:;" onclick="loadListMore(this);">查看更多▼</a></div>
    <!-- page end -->

    {#include file="./footer.tpl" _item=""#}

</div>

<script type="text/javascript">
    $(document).ready(function(){
        $("ul#ul-list li").click(function(){
            if ($(this).find(".hd span").text() == "-"){
                $(this).find(".hd span").text("+");
                $(this).find(".bd").hide();
            }else{
                $("ul#ul-list li .hd span").text("+");
                $("ul#ul-list li .bd").hide();
                $(this).find(".hd span").text("-");
                $(this).find(".bd").show();
                if ($(this).hasClass("red")){
                    $(this).removeClass("red");
                    $.get("{#appurl('vip/contentclick')#}&id="+$(this).attr("data-id"));
                }
            }
        });
    });
</script>
</body>
</html>