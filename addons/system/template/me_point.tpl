
<!DOCTYPE html>
<html>
<head lang="zh-CN">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{#$titname#}{#$smarty.const.POINT_NAME#}记录 - {#$BASE_NAME#}</title>
    <link rel="stylesheet" href="{#$CSS_PATH#}normalize.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}global.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}alert.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}font-awesome.css"/>
    <style type="text/css">
        .align-left {
            text-align: left;
        }
        .align-center {
            text-align: center;
        }
        .button-recharge {
            margin-left:10px;
            background-color: #5bb75b;
            border-color: #53a653;
        }
    </style>
    <script type="text/javascript" src="{#$JS_PATH#}jquery-1.11.0.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.alert.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.form.min.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}linkage.js"></script>
</head>
<body>
{#include file="header_user.tpl"#}
<script>$('#head-nav-menu>li:eq(4)').addClass('active');</script>


<div class="wrapper">
    <div class="breadcrumb">
        <div class="items">
            <a href="{#$urlarr.2#}">平台首页</a>
            <i class="iconfont">&#xe621;</i>
            <span>{#$titname#}{#$smarty.const.POINT_NAME#}记录</span>
        </div>
    </div>
    <div class="main add-mp">
        <div class="hd">
            {#if $titname=='我的'#}
                <h1 class="title">我的帐户余额：{#$user.point#}{#$smarty.const.POINT_NAME#}</h1>
                <a href="{#$urlarr.3#}recharge/" class="button button-primary button-rounded button-small button-recharge"
                >{#$smarty.const.POINT_NAME#}充值</a>
            {#else#}
                <h1 class="title">{#$titname#}帐户余额：{#$use.point#}{#$smarty.const.POINT_NAME#}</h1>
            {#/if#}
            <div class="control">
                <a href="{#$urlarr.2#}" class="button button-primary button-rounded button-small">返回首页</a>
            </div>
        </div>
        <div class="bd">
            <div class="section">
                <div class="table-wrapper">

                    <table class="table table-primary">
                        <thead>
                        <tr>
                            <th width="60">序号</th>
                            <th class="align-left">{#$smarty.const.POINT_NAME#}变化数</th>
                            <th class="align-left">变化后剩余</th>
                            <th class="align-left">变化备注</th>
                            <th class="align-left">发生时间</th>
                        </tr>
                        </thead>
                        <tbody id="applist">
                        {#ddb_pc set="数据表:users_point,列表名:lists,显示数目:20,分页显示:1,分页名:pagelist,当前页:GET[page],分页地址:{#$pageurl#}?key={#$key#}&userid={#$userid#}&page=(?),排序:`indate` DESC" where=$wheresql#}
                        {#foreach from=$lists item=list#}
                            <tr>
                                <td class="align-center"><span>{#$list._n#}</span></td>
                                <td class="align-left">{#$list.change#}</td>
                                <td class="align-left">{#$list.point#}</td>
                                <td class="align-left">{#$list.pointtxt#}</td>
                                <td class="align-left">{#date("Y-m-d H:i:s", $list.indate)#}</td>
                            </tr>
                            {#foreachelse#}
                            <tr>
                                <td colspan="5" align="center" class="align-center">无</td>
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
</div>

<script type="text/javascript">
    $(document).ready(function() {

    });
</script>
{#include file="footer.tpl"#}

</body>
</html>