<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>支付方式 - {#$BASE_NAME#}</title>
    <link rel="stylesheet" href="{#$CSS_PATH#}admin/normalize.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}admin/global.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}admin/detail.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}font-awesome.css"/>
    <style type="text/css">
        .utis{padding:15px;margin-bottom:20px;border:1px solid #ebccd1;border-radius:4px;color:#a94442;background-color:#f2dede}
        .normal-link{color:#257ad0}
        .normal-link:hover{text-decoration:underline}
    </style>
    <script type="text/javascript" src="{#$JS_PATH#}jquery-1.11.0.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.alert.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.form.min.js"></script>
</head>
<body>

{#include file="header_admin.tpl"#}
<script>$('#head-nav-menu li:eq(2)').addClass('active');</script>


<div class="breadcrumb-wrapper">
    <div class="breadcrumb">
        <div class="items">
            <span class="description">当前位置：</span>
            <span class="current">系统管理</span>
            <i class="iconfont">&#xe621;</i>
            <span class="current">支付方式</span>
        </div>
    </div>
</div>

<div class="main-wrapper">
    <div class="main">
        <div class="row cf">
            <div class="col" style="margin-left:23px;">
                {#include file="setting_left.tpl"#}
                <script>$('#setting-nav-menu li#nav-pay a').css('color','#ff6600');</script>
            </div>
            <div class="col" style="width: 1050px;">
                <div class="main-content">
                    <div class="module">
                        <div class="section section-minh">
                            <div class="utis">安装在线支付插件需申请对应在线支付服务公司的服务账户。</div>
                            <div class="table-wrapper">
                                <table class="table table-primary">
                                    <thead>
                                    <tr>
                                        <th>名称</th>
                                        <th>支付方式简短描述</th>
                                        <th>操作</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    {#ddb_pc set="数据表:pay,列表名:lists,显示数目:10"#}
                                    {#foreach from=$lists item=list#}
                                        {#$content = string2array($list.content)#}
                                        <tr data-id="{#$list.id#}">
                                            <td class="align-center">{#$list.title#}</td>
                                            <td class="align-center">{#$content.content#}</td>
                                            <td class="align-center">
                                                {#if $list.view#}
                                                    <a class="normal-link" href="{#$urlarr.3#}pay/?do=uninstall&value={#$list.value#}">卸载</a>
                                                    <a class="normal-link" href="{#$urlarr.3#}pay/?do=edit&value={#$list.value#}">编辑</a>
                                                {#else#}
                                                    <a class="normal-link" href="{#$urlarr.3#}pay/?do=install&value={#$list.value#}">安装</a>
                                                {#/if#}
                                            </td>
                                        </tr>
                                        {#foreachelse#}
                                        <tr>
                                            <td class="align-center" colspan="3">无</td>
                                        </tr>
                                    {#/foreach#}
                                    </tbody>
                                </table>
                            </div>
                            <div id="pagelist">
                                {#$pagelist#}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



{#include file="footer_admin.tpl"#}

</body>
</html>