
<!DOCTYPE html>
<html>
<head lang="zh-CN">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>首页 - {#$BASE_NAME#}</title>
    <link rel="stylesheet" href="{#$CSS_PATH#}normalize.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}global.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}alert.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}font-awesome.css"/>
    <link rel="stylesheet" href="{#$NOW_PATH#}css/style.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}iealert.css"/>
    <script type="text/javascript" src="{#$JS_PATH#}jquery-1.11.0.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.alert.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.cookie.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.form.min.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.browser.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}iealert.js"></script>
    <script type="text/javaScript">$(document).ready(function() { $("body").iealert(); });</script>
</head>
<body>

<div class="content-box">
    {#include file="header_user.tpl"#}
    <script>$('#head-nav-menu>li:eq(0)').addClass('active');</script>
</div>

<div class="wrapper content-box">
    {#include file="information.tpl"#}
    <div class="main">
        <div class="hd">
            <h1 class="title">公众号/服务窗首页</h1>
        </div>
        <div class="bd">
            <div class="section">
                <div class="control-group relative">
                    <a href="{#$urlarr.2#}add/" class="button button-primary button-rounded">+添加接入</a>
                    {#if isopenweixin(true)#}
                        &nbsp;
                        <a href="{#$_A['url']['index']#}open/weixin/auth/" onclick="_openweixin();"
                           class="button button-success button-rounded" target="_blank">+微信公众号授权接入</a>
                    {#/if#}
                    <p class="description">还没有公众号/服务窗？
                        <a class="normal-link" href="https://mp.weixin.qq.com/" target="_blank">点击申请公众号</a>
                        <a class="normal-link" href="https://fuwu.alipay.com/" target="_blank">点击申请服务窗</a>
                    </p>
                    {#if $user.admin && $userlist#}
                        <div class="filteral">
                            <select id="filteral" onchange="_filteral(this)">
                                <option value="0">=按用户筛选列表=</option>
                                {#foreach from=$userlist item=list#}
                                    <option value="{#$list.userid#}">
                                        {#$list.username#}{#if $list.companyname#} (公司:{#$list.companyname#}){#/if#}
                                    </option>
                                {#/foreach#}
                            </select>
                            {#if $filter#}<script>$("#filteral").val("{#$filter#}");</script>{#/if#}
                        </div>
                    {#/if#}
                </div>
                <table class="table table-primary">
                    <thead>
                    <tr>
                        <th align="left" class="heading" colspan="2">
                            公众号/服务窗列表
                            <span class="text text-primary text-extra-small">(点击下方功能模块，进入管理)</span>
                            <span class="search">
                                <input type="text" id="key" placeholder="搜索：公众号/服务窗" value="{#$key#}" data-val="{#$key#}"><span class="search_btn">搜</span>
                                <div class="search_del"></div>
                            </span>
                        </th>
                    </tr>
                    </thead>
                    <tbody id="users_al_list">
                    {#ddb_pc set="数据表:users_al,列表名:lists,显示数目:10,分页显示:1,分页名:pagelist,当前页:GET[page],分页地址:{#$pageurl#}?key={#$key#}&page=(?),排序:{#$ordersql#}" where=$wheresql#}
                    {#foreach from=$lists item=list#}
                        {#$list['setting'] = string2array($list['setting'])#}
                        <tr{#if $list.wx_level==7#} class="wx_corp"{#/if#}>
                            <td colspan="2" class="al-name" title="ID：{#$list.id#}">
                                {#if $list.id == $slastalid#}<span class="lastal">最后使用</span>{#/if#}
                                {#if $list.wx_name#}
                                    <span id="wx_name">
                                        {#if $list.wx_level==7#}企业号：{#else#}公众号：{#/if#}{#$list.wx_name#}
                                        {#if $list['setting']['other']['get_appid']#}
                                            <em class="toget" title="借用网页授权登录">借授权</em>
                                        {#/if#}
                                        {#if $list['setting']['other']['getjs_appid']#}
                                            <em class="toget" title="借用 JS 分享接口">借JSSDK</em>
                                        {#/if#}
                                        {#if $list['setting']['openweixin'] && isopenweixin($list['setting']['openweixin'])#}
                                            {#openauth_expires($list['setting']['openweixin_expires'])#}
                                        {#/if#}
                                    </span>
                                {#/if#}
                                {#if $list.al_name#}<span id="al_name">服务窗：{#$list.al_name#}</span>{#/if#}
                                {#if $user.admin#}
                                    <a class="openfunc" href="{#$urlarr.1#}system/settings/users/?openfunc={#$list.userid#}&openalid={#$list.id#}">添加功能</a>
                                    <div class="openfunc_w">
                                        <a href="{#$urlarr.1#}system/settings/users/?openfunc={#$list.userid#}&openalid={#$list.id#}">管理员身份</a>
                                        <a href="{#$urlarr.2#}openfunc/?openalid={#$list.id#}">客户身份</a>
                                    </div>
                                    <span class="subordinate"><a class="normal-link" href="{#$urlarr.1#}system/settings/users/?t=high&username={#$list.username|urlencode#}">&copy; {#if $list.companyname#}{#$list.companyname#}{#else#}<span title="用户名：{#$list.username#}">{#$list.username#}</span>{#/if#}</a></span>
                                {#else#}
                                    <a class="openfunc" href="{#$urlarr.2#}openfunc/?openalid={#$list.id#}">添加功能</a>
                                {#/if#}
                            </td>
                        </tr>
                        <tr id="list-{#$list.id#}"{#if $list.wx_level==7#} class="wx_corp"{#/if#}>
                            <td class="ifunlist" id="ifunlist" width="875">
                                <div class="button-groups">
                                    {#foreach from=string2array($list.function) item=list2#}
                                        <a href="{#$urlarr.1#}{#$list2.title_en#}/?al={#$list2.alid#}&uf={#$list2.ida#}"
                                           class="button button-primary button-rounded button-small">{#$list2.title#}</a>
                                        {#foreachelse#}
                                        无
                                    {#/foreach#}
                                    <label class="button-input">
                                        <input type="text" name="fun-search" placeholder="输入名称搜索"><em></em>
                                    </label>
                                </div>
                            </td>
                            <td class="al-center">
                                <div class="button-groups action">
                                    <a href="javascript:_del({#$list.id#});" class="button button-primary button-rounded button-extra-small">删除</a>
                                    <a href="{#$urlarr.2#}add/{#$list.id#}/" class="button button-primary button-rounded button-extra-small">编辑</a>
                                </div>
                            </td>
                        </tr>
                        {#foreachelse#}
                        <tr>
                            <td colspan="4" align="center" class="align-center">无</td>
                        </tr>
                    {#/foreach#}
                    </tbody>
                </table>
                <div id="pagelist" class="clearfix">
                    <a href="javascript:void(0);" style="cursor:default">总数量{#$pagelist_info.total_rows#}个</a>
                    {#$pagelist#}
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function _filteral(obj) {
        $.alert("加载中...", 0);
        window.location.href = '{#$urlarr.2#}?filter='+$(obj).val();
    }
    function _del(id){
        var _del = art.dialog({
            fixed: true,
            lock: true,
            opacity: '.3',
            content: '您确定要删除并且删除后不可恢复吗？',
            button: [{
                name: '确定删除',
                focus: true,
                callback: function () {
                    $.ajax({
                        url:'{#$urlarr.2#}del/'+id+'/',
                        type:"get",
                        success: function (data) {
                            _del.close();
                            $.showModal('删除成功！', '{#$urlarr.now#}');
                        },
                        cache: false
                    });
                    return false;
                }
            },{
                name: '取消',
                callback: function () {
                    return true;
                }
            }]

        });
    }
    window.__showbg = false;
    function _showbg(j) {
        if ($("div.ifunall-showbg").length == 0) {
            var $intemp = $("<div class='ifunall-showbg' id='"+Math.round(Math.random() * 10000)+"'></div>");
            $intemp.mouseover(function(){ $intemp.remove(); });
            $("body").append($intemp);
            setTimeout(function(){
                $("div.ifunall-showbg").animate({opacity:"0.3"}, 'slow');
            }, 200);

        }
    }
    $(document).ready(function() {
        $(".openfunc").mousemove(function () {
            if ($(this).next().hasClass("openfunc_w")){
                $(this).next().show();
                $(this).next().css({top:$(this).position().top, left: $(this).position().left});
            }
        }).mouseout(function () {
            if ($(this).next().hasClass("openfunc_w")){
                $(this).next().hide();
            }
        });
        $('.openfunc_w').mousemove(function () {
            $(this).show();
        }).mouseout(function () {
            $(this).hide();
        });
        //
        $("td#ifunlist div").mouseover(function(){
            var sh = $(this).outerHeight();
            $(this).addClass("ifunall");
            _showbg();
            var eh = $(this).outerHeight();
            if (eh/sh >= 2){
                $(this).addClass("ifunallb");
            }
        }).mouseout(function(){
            $(this).removeClass("ifunall");
            $(this).removeClass("ifunallb");
        });
        //
        var search_del = $(".search_del");
        var search_btn = $(".search_btn");
        var search_key = $("#key");
        search_btn.click(function(){
            var key = $.trim(search_key.val());
            if (key) {
                window.location.href = "{#$urlarr.2#}?key="+key;
            }else{
                $.cookie('_index_key', '', { expires: -1, path: '{#$smarty.const.BASE_DIR#}' });
                if (search_key.attr("data-val") != "") { window.location.href = "{#$urlarr.2#}"; }

            }
        });
        $(".search>input,.search_del").mouseover(function(){
            if ($.trim(search_key.val()) != "") { search_del.show(); }
        }).mouseout(function(){
            search_del.hide();
        });
        search_del.click(function(){
            search_key.val("");
            search_btn.click();
        });
        search_key.keydown(function(e){
            if(e.keyCode == 13){ search_btn.click(); }
        });
        if ($.trim(search_key.val()) != "") {
            if ($("#users_al_list").find("tr").length > 1) {
                $.cookie('_index_key', $.trim(search_key.val()), { expires: 7, path: '{#$smarty.const.BASE_DIR#}' });
            }else{
                $.cookie('_index_key', '', { expires: -1, path: '{#$smarty.const.BASE_DIR#}' });
            }
        }
        $("label.button-input").find("input").keyup(function(){
            var thisv = $(this).val();
            var thisg = $(this).parents(".button-groups");
            thisg.find("a").each(function(){
                var thisa = $(this).text();
                if (thisv) {
                    thisa = thisa.replace(thisv, '<font color="red">'+thisv+'</font>');
                }
                $(this).html(thisa);
            });
        });
        {#if $_A.u.admin == 1#}
        {#if $_release != "99"#}
        $.ajax({
            url:'{#$urlarr.2#}version/',
            type:"GET",
            dataType: 'json',
            success: function (data) {
                if (data != null && data.success != null && data.success) {
                    var intemp = $('<a class="indexuptis" href="{#$urlarr.2#}settings/upgrade/?winopen=1">检测到新版本：'+data.message+'，点击查看详情！</a>');
                    $("body").append(intemp);
                    intemp.css({"margin-left": intemp.outerWidth()/2 * -1}).show();
                }else{
                    _versionapp();
                }
            },
            cache: false
        });
        {#elseif $_apprele != "99"#}
        _versionapp();
        {#/if#}
        {#/if#}
    });
    function _versionapp() {
        $.ajax({
            url:'{#$urlarr.2#}versionapp/',
            type:"GET",
            dataType: 'json',
            success: function (data) {
                if (data != null && data.success != null && data.success) {
                    var intemp = $('<a class="indexuptis" href="{#$urlarr.2#}settings/functions/?winopen=1">检测到功能应用有新版本，点击查看详情！</a>');
                    $("body").append(intemp);
                    intemp.css({"margin-left": intemp.outerWidth()/2 * -1}).show();
                }
            },
            cache: false
        });
    }
    function _openweixin() {
        art.dialog({
            title: '微信公众号登录授权',
            fixed: true,
            lock: true,
            content: '请在新窗口中完成微信公众号登录授权，点击授权成功刷新列表。',
            button: [{
                name: '授权成功',
                focus: true,
                callback: function () {
                    $.alert("正在刷新...", 0, 1);
                    window.location.reload();
                    return true;
                }
            },{
                name: '关闭提示',
                callback: function () {
                    return true;
                }
            }]
        });
    }
</script>
{#include file="footer.tpl"#}

</body>
</html>