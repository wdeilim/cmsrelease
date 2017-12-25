
<!DOCTYPE html>
<html>
<head lang="zh-CN">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>功能列表 - {#$BASE_NAME#}</title>
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
<script>$('#head-nav-menu>li:eq(1)').addClass('active');</script>


<div class="wrapper">
    <div class="breadcrumb">
        <div class="items">
            <a href="{#$urlarr.2#}">平台首页</a>
            <i class="iconfont">&#xe621;</i>
            <span>功能管理</span>
        </div>
    </div>
    <div class="main add-mp">
        <div class="hd">
            <h1 class="title">功能列表</h1>
            <div class="control">
                <a href="{#$urlarr.2#}" class="button button-primary button-rounded button-small">返回</a>
            </div>
        </div>
        <div class="bd">
            <div class="section">
                <table class="table table-primary">
                    <thead>
                    <tr>
                        <th>功能名称</th>
                        <th class="tabfun-a">公众号</th>
                        <th class="tabfun-b">编号</th>
                        <th class="tabfun-c">期限</th>
                    </tr>
                    </thead>
                    <tbody>
                    {#ddb_pc set="数据表:functions,列表名:lists,显示数目:100,排序:inorder desc"#}
                    {#foreach from=$lists item=list#}
                        <tr>
                            <td align="center" style="background:#FAFAFC">{#$list.title#}</td>
                            <td colspan="3" style="padding: 0;">
                                {#ddb_pc set="数据表:users_functions,列表名:lists2,显示数目:100,排序:indate desc" where=" `userid`={#$user.userid#} AND `fid`={#$list.id#} "#}
                                {#foreach from=$lists2 item=list2#}
                                    <div class="table-yes{#if $list2._n>1#} table-top{#/if#}">
                                        <div class="tabfun-a">{#if $list2.wx_name#}{#$list2.wx_name#}{#else#}{#$list2.al_name#}{#/if#}</div>
                                        <div class="tabfun-b">{#$list2.indate#}{#$list2.id#}</div>
                                        <div class="tabfun-c">{#$list2.enddate|user_status:"Y-m-d"#}</div>
                                    </div>
                                {#foreachelse#}
                                    <div class="table-no">
                                        <a href="tencent://message/?uin={#$smarty.const.DIY_LINKQQ_PATH#}&Menu=yes"
                                           class="button button-primary button-rounded button-small">联系客服开通</a>
                                    </div>
                                {#/foreach#}
                            </td>
                        </tr>
                    {#foreachelse#}
                        <tr>
                            <td colspan="4" align="center" class="align-center">无</td>
                        </tr>
                    {#/foreach#}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


{#include file="footer.tpl"#}

</body>
</html>