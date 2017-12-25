
<!DOCTYPE HTML>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta content="telephone=no" name="format-detection">
    <title>会员礼品券</title>
    <link rel="stylesheet" href="{#$NOW_PATH#}css/inner.css">
    <script type="text/javascript" src="{#$JS_PATH#}jquery.min.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.alert.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}ul-list-more.js"></script>
    <style type="text/css">
        .showMore{height:36px;width:98%;margin:12px auto;border:0px solid #e8e8e8;}
        .showMore a{color:#3d3d3d;background:#eee;font-size:16px;display:block;border:1px solid #ccc;line-height:36px;padding:3px 6px;background:-webkit-gradient(linear,0 0,0 100%,from(#ffffff),to(#e8e8e8));border-radius:5px;text-align: center;}
        .showMore a.no{color:#aaaaaa;cursor:text;background:-webkit-gradient(linear,0 0,0 100%,from(#F1F1F1),to(#C7C7C7));}
        .list .red {background-color: #FFDFDF;}
        .vnone {display:none;}
        .vip-miss {font-size: 18px;}
        .vip-qr img {width:auto; height:auto;}
    </style>
</head>

<body>
<div class="layout">
    <header>
        <img src="{#vippagebanner('giftcert', $_A['vip_data']['banner'])#}" class="hd-banner">
    </header>
    <div class="wrap">
        <ul class="list" id="ul-list">
            <!-- list start -->
            {#foreach from=$lists.list item=list#}
            <li class="item{#isto($list['view'],'0',' red')#}" data-id="{#$list['contentid']#}">
                <div class="hd dd">
                    <i class="vip-icon icon-g">礼</i>
                    <h2{#cot($list['title_color'])#}>{#$list['title']#}</h2>
                    <time>{#date("Y-m-d",$list['indate'])#}</time>
                    <span class="btn-menu btn-open">+</span>
                </div>
                <div class="bd">
                    {#if ($list.num<=$list.usenum)#}
                    <div class="vip-action">
                        <div class="vip-miss">此礼品券已使用完毕！</div>
                    </div>
                    {#elseif ($list.startdate>$TIME || $list.enddate<$TIME)#}
                    <div class="vip-action">
                        <div class="vip-miss">此礼品券不在可使用期内！</div>
                    </div>
                    {#else#}
                    <div class="vip-action" id="vip-twocode">
                        <div class="vip-sn">{#$list['sn']#}</div>
                        <div class="vip-action-desc"><i class="vip-icon icon-pointer"></i>点击或扫描二维码处理</div>
                        <div class="vip-qr"><img src="{#appurl('vip/qrcode/giftcert')#}&sn={#$list['sn']#}" alt=""></div>
                    </div>
                    <div class="vip-action vnone" id="vip-formcode">
                        <div class="vip-sn" id="vip-sn-text">{#$list['sn']#}</div>
                        <div class="vip-form">
                            <input type="password" id="pass" placeholder="请输入管理员密码">
                            <button type="button" id="butapply" class="btn-submit vip-sbtn">确定使用</button>
                        </div>
                    </div>
                    {#/if#}
                    <div class="vip-usenum">
                        兑换所需积分【<span>{#$list['int_c']#}</span>】<br/>
                        此礼品券剩余【<span id="inum">{#$list['num']-$list['usenum']#}</span>张】<br>
                        此礼品券有效期【<span>{#date("Y-m-d",$list['startdate'])#}</span>至<span>{#date("Y-m-d",$list['enddate'])#}</span>】
                    </div>
                    <h2 class="desc-t">详细说明</h2>
                    <p>{#$list.content#}</p>
                </div>
            </li>
            {#foreachelse#}
            <li class="item">
                没有任何礼品券！
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

    {#include file="./footer.tpl" _item="3"#}
</div>

<script type="text/javascript">
    var _sh = null;
    function getsn(obj, times){
        if (obj.find("span").text() == "-"){
            if (obj.parent().find(".bd #vip-twocode").is(":visible")){
                if (obj.parent().find(".bd #vip-twocode .vip-sn").length > 0){
                    var s = "sn="+obj.parent().find(".bd #vip-twocode .vip-sn").text()+"&times="+parseInt(times/1000);
                    $.ajax({
                        type: "POST",
                        url: "{#appurl('vip/getsnstatus')#}",
                        data: s,
                        dataType: "json",
                        success: function (msg) {
                            if (msg.success == "1"){
                                if (msg.tnum > 0)
                                    obj.parent().find(".bd span#inum").text(msg.tnum);
                                obj.parent().find(".bd #vip-twocode").html('<div class="vip-miss">已通过扫描二维码使用！</div>');
                                obj.parent().find(".bd #vip-twocode").unbind();
                            }
                            return ;
                        },
                        error: function (msg) {
                            return ;
                        }
                    });
                }
            }
        }
    }

    $(document).ready(function(){
        $("ul#ul-list li .hd").click(function(){
            if ($(this).find("span").text() == "-"){
                $(this).find("span").text("+");
                $(this).next(".bd").hide();
            }else{
                if (_sh != null) { clearInterval(_sh); _sh = null;}
                var _obj = $(this);
                var _times = new Date().valueOf();
                _sh = setInterval(function(){ getsn(_obj, _times); }, 3000);
                //
                $("ul#ul-list li .hd span").text("+");
                $("ul#ul-list li .bd").hide();
                $(this).find("span").text("-");
                $(this).next(".bd").show();
                if ($(this).parent().hasClass("red")){
                    $(this).parent().removeClass("red");
                    $.get("{#appurl('vip/contentclick')#}&id="+$(this).parent().attr("data-id"));
                }
            }
        });
        $("ul#ul-list li .bd #vip-twocode").click(function(){
            $(this).hide();
            $(this).next("#vip-formcode").show();
        });
        $("div#vip-sn-text").click(function(){
            $(this).parent().hide();
            $(this).parent().prev("#vip-twocode").show();
        });
        $("button#butapply").click(function(){
            var obj = $(this).parent();
            var pass= obj.find("input#pass").val();
            var sn= obj.parent().find("#vip-sn-text").text();
            if (pass == ""){
                $.alert("管理员密码不可留空！"); return;
            }
            var s = "dosubmit=sub&pass="+pass+"&sn="+sn;
            $.ajax({
                type: "POST",
                url: "{#appurl()#}",
                data: s,
                dataType: "json",
                success: function (msg) {
                    if (msg.success == "1"){
                        obj.parent().parent().find("span#inum").text(msg.tnum);
                        obj.parent().html('<div class="vip-miss">兑换成功，积分已扣除！</div>');
                    }else if (msg.success == "0"){
                        $.alert(msg.message);
                    }else{
                        $.alert("提交失败！");
                    }
                },
                error: function (msg) {
                    $.alert("提交错误！");
                }
            });
        });
    });
</script>
</body>
</html>