
<!DOCTYPE html>
<html>
<head lang="zh-CN">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>注册信息 - {#$BASE_NAME#}</title>
    <link rel="stylesheet" href="{#$CSS_PATH#}normalize.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}global.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}alert.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}font-awesome.css"/>
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
            <span>注册信息</span>
        </div>
    </div>
    <div class="main add-mp">
        <div class="hd">
            <h1 class="title">注册信息修改</h1>
            <div class="control">
                <a href="{#$urlarr.2#}" class="button button-primary button-rounded button-small">返回首页</a>
            </div>
        </div>
        <div class="bd">
            <div class="section">
                <form action="{#$urlarr.now#}" method="post" id="editform">
                    <table class="table table-form">
                        <tbody>
                        <tr>
                            <td class="al-right"><span>用户名</span></td>
                            <td><input class="form-control" type="text" id="username" name="username" value="{#$user.username#}" disabled="disabled"/></td>
                        </tr>
                        <tr>
                            <td class="al-right"><span>{#$smarty.const.POINT_NAME#}</span></td>
                            <td>
                                <input class="form-control" type="text" value="{#$user.point#}" disabled="disabled"/>
                                <a href="{#$urlarr.2#}me_point/" class="normal-link">查看{#$smarty.const.POINT_NAME#}详情</a>
                            </td>
                        </tr>
                        <tr>
                            <td class="al-right"><span>姓名</span></td>
                            <td><input class="form-control" type="text" id="fullname" name="fullname" value="{#$user.fullname#}"/></td>
                        </tr>
                        <tr>
                            <td class="al-right"><span>手机</span></td>
                            <td><input class="form-control" type="text" id="phone" name="phone" value="{#$user.phone#}"/></td>
                        </tr>
                        <tr>
                            <td class="al-right"><span>邮箱</span></td>
                            <td><input class="form-control" type="text" id="email" name="email" value="{#$user.email#}"/></td>
                        </tr>
                        <tr>
                            <td class="al-right"><span>QQ</span></td>
                            <td><input class="form-control" type="text" id="qqnum" name="qqnum" value="{#$user.qqnum#}"/></td>
                        </tr>
                        <tr>
                            <td class="al-right"><span>公司名称</span></td>
                            <td><input class="form-control" type="text" id="companyname" name="companyname" value="{#$user.companyname#}"/></td>
                        </tr>
                        <tr>
                            <td class="al-right"><span>电话</span></td>
                            <td><input class="form-control" type="text" id="tel" name="tel" value="{#$user.tel#}"/></td>
                        </tr>
                        <tr>
                            <td class="al-right"><span>地区</span></td>
                            <td class="form-reg">
                                <input class="form-control" type="text" id="linkaddr" name="linkaddr" id="linkaddr" value="{#$user.linkaddr#}">
                            </td>
                        </tr>
                        <tr>
                            <td class="al-right"><span>详细地址</span></td>
                            <td>
                                <input class="form-control" type="text" id="address" name="address" value="{#$user.address#}"/>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>
                                <div class="control-group">
                                    <input class="button button-primary button-rounded" type="submit" value="修改"/>
                                    <input class="button button-primary button-rounded" type="reset" value="重置"/>
                                    <input type="hidden" name="dosubmit" value="1"/>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('#editform').submit(function() {
            var retu = true;
            retu = $('#fullname').inTips("", 2, retu);
            retu = $('#phone').inTips("", -2, retu);
            retu = $('#email').inTips("", -4, retu);
            retu = $('#qqnum').inTips("", -1, retu);
            retu = $('#companyname').inTips("", -1, retu);
            retu = $('#tel').inTips("", -3, retu);
            retu = $('#linkaddr').inTips("请选择地区", -1, retu, 0, $('#__linkage'));
            retu = $('#address').inTips("", -1, retu);
            if (!retu) return false;

            $.alert('正在保存...', 0);
            $(this).ajaxSubmit({
                dataType : 'json',
                success : function (data) {
                    $.alert(0);
                    if (data != null && data.success != null && data.success) {
                        $.showModal(data.message, '{#$urlarr.now#}');
                    } else {
                        $.showModal(data.message);
                    }
                },
                error : function () {
                    $.alert(0);
                    $.inModal("提交失败！");
                }
            });
            return false;
        });
    });
    linkage("linkaddr","{#$urlarr.index#}web/system/linkage/",0,0);
</script>
{#include file="footer.tpl"#}

</body>
</html>