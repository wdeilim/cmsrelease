
<!DOCTYPE html>
<html>
<head lang="zh-CN">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>修改密码 - {#$BASE_NAME#}</title>
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
<script>$('#head-nav-menu li:eq(4)').addClass('active');</script>



<div class="wrapper">
    <div class="breadcrumb">
        <div class="items">
            <a href="{#$urlarr.2#}">平台首页</a>
            <i class="iconfont">&#xe621;</i>
            <span>修改密码</span>
        </div>
    </div>
    <div class="main add-mp">
        <div class="hd">
            <h1 class="title">修改登录密码</h1>
            <div class="control">
                <a href="{#$urlarr.2#}" class="button button-primary button-rounded button-small">返回首页</a>
            </div>
        </div>
        <div class="bd">
            <div class="section">
                <form action="{#$urlarr.now#}" method="post" id="editform">
                    <table class="table table-form" style="margin: 60px;">
                        <tbody>
                        <tr>
                            <td class="al-right"><span style="color:#f00">*旧密码</span></td>
                            <td><input class="form-control" type="password" name="olduserpass" id="olduserpass" value=""></td>
                        </tr>
                        <tr>
                            <td class="al-right"><span>新密码</span></td>
                            <td><input class="form-control" type="password" name="userpass" id="userpass" value=""></td>
                        </tr>
                        <tr>
                            <td class="al-right"><span>确认密码</span></td>
                            <td><input class="form-control" type="password" name="reuserpass" id="reuserpass" value=""></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>
                                <div class="control-group">
                                    <input class="button button-primary button-rounded" type="submit" value="修改"/>
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
            retu = $('#olduserpass').inTips("旧密码不能为空", -1, retu);
            retu = $('#userpass').inTips("密码最少6个字符", 6, retu);
            retu = $('#reuserpass').inTips("两次密码输入不一致", $('#userpass'), retu);
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
</script>
{#include file="footer.tpl"#}

</body>
</html>