<div class="header-fix">
    <div class="navbar cf">
        <img class="logo" src="{#$IMG_PATH#}logo.png" alt=""/>
        <h2 class="title" title="{#$_A.f.title#}(ID:{#$_A.uf.id#})">{#$_A.f.title#}<span id="_h2_al_name"></span></h2>
        <ul class="nav cf funnav" id="head-nav-menu">
            <ol id="moremenu">
                {#ddb_pc set="数据表:users_al,列表名:lists,显示数目:1" where=" `id`={#$_A.uf.alid#} "#}
                {#foreach from=$lists item=list#}
                    {#$fl = string2array($list.function)#}
                    {#foreach from=$fl item=fls#}
                        <li{#if ($fls.ida==$_A.uf.id)#} class="active"{#/if#}>
                            <a class="nav-item-link" href="{#$urlarr.1#}{#$fls.title_en#}/?al={#$fls.alid#}&uf={#$fls.ida#}" title="{#$fls.title#}">{#$fls.title#}</a>
                        </li>
                    {#/foreach#}
                {#/foreach#}
            </ol>
            <li><a class="nav-item-link" href="{#$urlarr.1#}system/">返回平台首页</a></li>
        </ul>
    </div>
</div>
<div class="header-height"></div>

<script type="text/javascript">
    function h2_al_name(i,j) {
        var name_arr = ['{#$_A.uf.wx_name#}','{#$_A.uf.al_name#}'];
        if (name_arr[i]) {
            var tthis = $("#_h2_al_name");
            tthis.animate({ "opacity" : 0 }, j?0:500, function(){
                tthis.css({"opacity":1}).html(" - " + name_arr[i]);
                setTimeout(function(){ h2_al_name(i?0:1) }, 3000);
            });
        }else{
            h2_al_name(i?0:1);
        }
    }
    $(document).ready(function() {
        h2_al_name(0,1);
        var morenum = 5;
        var moreeve = $("#moremenu li");
        moreeve.each(function(index) {
            if (moreeve.length > morenum){
                if (index < morenum - 1) {
                    var cln = $(this).attr('class') ? $(this).attr('class') : '';
                    $("#moremenu").before("<li class='"+cln+"'>"+$(this).html()+"</li>");
                    $(this).remove();
                }else if (index == 4) {
                    var $imitate = $('<li id="nav-item-more"><a class="nav-item-link" href="javascript:;">更多...</a></li>');
                    $("#moremenu").before($imitate);
                    $imitate.mousemove(function () {
                        $("#moremenu").show();
                        if (moreeve.length > 12) {
                            $("#moremenu").css({"max-width":400,"width":400})
                        }else{
                            if ($("#moremenu").width() > 300) $("#moremenu").width(300);
                        }
                    }).mouseout(function () {
                        $("#moremenu").hide();
                    });
                    $("#moremenu").mousemove(function () {
                        $("#moremenu").show();
                    }).mouseout(function () {
                        $("#moremenu").hide();
                    })
                }
            }else{
                var cln = $(this).attr('class') ? $(this).attr('class') : '';
                $("#moremenu").before("<li class='"+cln+"'>"+$(this).html()+"</li>");
                $(this).remove();
            }
        });
        var nav_item_eve = $("#moremenu li.active");
        if (nav_item_eve.length > 0) {
            var _item_eve = $("#nav-item-more").prev();
            var cln = _item_eve.attr('class') ? _item_eve.attr('class') : '';
            $("#moremenu").append("<li class='"+cln+"'>"+_item_eve.html()+"</li>");
            _item_eve.remove();
            //
            var cln = nav_item_eve.attr('class') ? nav_item_eve.attr('class') : '';
            $("#nav-item-more").before("<li class='"+cln+"'>"+nav_item_eve.html()+"</li>");
            nav_item_eve.remove();
        }
        var emuobj = $("#head-nav-menu").find(".nav-item-link[title='模拟测试']");
        if (!emuobj.parent().hasClass("active")) {
            emuobj.attr("target", "_blank");
        }
    });
</script>