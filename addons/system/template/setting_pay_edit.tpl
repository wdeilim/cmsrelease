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
        .table-setting th {border-bottom: 1px solid #e1e1e1;text-align:right;padding-right:15px;}
        .table-setting td {border-bottom: 1px solid #e1e1e1}
    </style>
    <script type="text/javascript" src="{#$JS_PATH#}jquery-1.11.0.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.alert.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.form.min.js"></script>
</head>
<body>

{#include file="header_admin.tpl"#}
<script>$('#head-nav-menu>li:eq(2)').addClass('active');</script>


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
                            <div class="control-group cf">
                                <div class="control-group-left">
                                    <a style="padding:0 10px;margin-top: 3px" class="button button-primary" href="{#$urlarr.3#}pay/">返回支付方式</a>
                                </div>
                            </div>
                            <form action="{#get_url()#}" method="post" id="settingform" style="margin-top:30px">
                                <table class="table-setting">
                                    <tbody>
                                    <tr>
                                        <th class="al-right"><span>支付名称：</span></th>
                                        <td style="font-weight:600;color:#00aa28;">{#$pay.title#}</td>
                                    </tr>
                                    <tr>
                                        <th class="al-right"><span>简短描述：</span></th>
                                        <td><input class="form-control" type="text" name="data[content][content]" value="{#$pay.content.content#}"></td>
                                    </tr>
                                    {#if $pay.value == 'alipay'#}
                                        <tr>
                                            <th class="al-right"><span>合作者身份(Partner ID)：</span></th>
                                            <td><input class="form-control" type="text" name="data[content][partner]" value="{#$pay.content.partner#}"></td>
                                        </tr>
                                        <tr>
                                            <th class="al-right"><span>安全校验码(Key)：</span></th>
                                            <td><input class="form-control" type="text" name="data[content][key]" value="{#$pay.content.key#}"></td>
                                        </tr>
                                        <tr>
                                            <th class="al-right"><span>支付宝帐号：</span></th>
                                            <td><input class="form-control" type="text" name="data[content][account]" value="{#$pay.content.account#}"></td>
                                        </tr>
                                    {#/if#}
                                    <tr>
                                        <td></td>
                                        <td>
                                            <input class="button button-primary button-rounded" type="submit" value="提交">
                                            <input type="hidden" name="dosubmit" value="1">
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </form>
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