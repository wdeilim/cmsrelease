
<!DOCTYPE html>
<html>
<head lang="zh-CN">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{#$smarty.const.POINT_NAME#}充值 - {#$BASE_NAME#}</title>
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
        table.table-primary tbody tr td, table.table-primary thead tr th {
            border:0;
        }
        .show_val {
            padding-top: 5px;;
            color: #EC8319;
        }
        .pay_name span {
            color: #7D7D7D;
            padding-left: 10px;
        }
        .pay_name img {
            vertical-align: middle;
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
            <span><a href="{#$urlarr.3#}">我的{#$smarty.const.POINT_NAME#}记录</a></span>
            <i class="iconfont">&#xe621;</i>
            <span>{#$smarty.const.POINT_NAME#}充值</span>
        </div>
    </div>
    <div class="main add-mp">
        <div class="hd">
            <h1 class="title">帐户余额：{#$user.point#}{#$smarty.const.POINT_NAME#}</h1>
            <div class="control">
                <a href="{#$urlarr.3#}" class="button button-primary button-rounded button-small">返回</a>
            </div>
        </div>
        <div class="bd">
            <div class="section">
                <div class="table-wrapper">

                    <table class="table table-primary" style="border:0;width:720px;margin:30px auto">
                        <tbody>
                        <tr>
                            <td class="align-center" colspan="2"><img src="{#$NOW_PATH#}images/n2.jpg"></td>
                        </tr>
                        </tbody>
                        <tbody style="background-color:#F8F8F8;">
                        <tr>
                            <td class="al-right" style="width:120px;"><span>充值金额：</span></td>
                            <td>
                                <span style="color:#EC8319;">{#$_GPC['amount']#}</span>元
                            </td>
                        </tr>
                        <tr>
                            <td class="al-right"><span>充值方式：</span></td>
                            <td>
                                {#$pay.title#}
                            </td>
                        </tr>
                        <tr>
                            <td class="al-right"><span>可获{#$smarty.const.POINT_NAME#}：</span></td>
                            <td>
                                <span style="color:#EC8319;">{#$_GPC['amount']*$smarty.const.POINT_CONVERT#}</span>点
                            </td>
                        </tr>
                        <tr>
                            <td> </td>
                            <td>
                                <div class="control-group">
                                    <input class="button button-primary button-rounded" type="button"
                                           onclick="document.forms['alipaysubmit'].submit()"
                                           value="&nbsp;&nbsp;确定支付&nbsp;&nbsp;"/>&nbsp;&nbsp;
                                    <input class="button button-primary button-rounded" type="button"
                                           onclick="location.href='{#$urlarr.3#}recharge/'"
                                           value="&nbsp;&nbsp;返回&nbsp;&nbsp;"/>
                                </div>
                                <span style="display:none;">{#$payment_form#}</span>
                            </td>
                        </tr>
                        </tbody>
                    </table>

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