<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>云中心 - {#$BASE_NAME#}</title>
    <link rel="stylesheet" href="{#$CSS_PATH#}admin/normalize.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}admin/global.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}admin/detail.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}font-awesome.css"/>
    <script type="text/javascript" src="{#$JS_PATH#}jquery-1.11.0.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.alert.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.form.min.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}datepicker/WdatePicker.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}ueditor/ueditor.config.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}ueditor/ueditor.all.min.js"></script>
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
            <span class="current">微窗云中心绑定</span>
        </div>
    </div>
</div>

<div class="main-wrapper">
    <div class="main">
        <div class="row cf">
            <div class="col" style="margin-left:23px;">
                {#include file="setting_left.tpl"#}
            </div>
            <div class="col" style="width: 1050px;">
                <div class="main-content">
                    <div class="module">
                        <div class="section section-minh">
                            <div class="control-group cf">
                                <div class="control-group-left">
                                    <a style="padding:0 10px;margin-top:3px" class="button button-primary"
                                       href="{#$smarty.const.CLOUD_URL#}" target="_blank">访问微窗云中心</a>
                                    <script>$('#setting-nav-menu li#nav-cloud a').css('color','#ff6600');</script>
                                    <a style="padding:0 10px;margin-top:3px;margin-left:10px;" class="button button-primary"
                                       href="javascript:void(0)" onclick="checkmoney();">查询剩余交易币</a>
                                </div>
                            </div>
                            <form action="{#get_url()#}" method="post" id="settingform" style="width:680px;margin:50px auto 0;">
                                <table class="table-setting">
                                    <tbody>
                                    <tr>
                                        <td class="al-right"><span>网站地址</span></td>
                                        <td><input class="form-control" type="text" disabled="disabled" value="{#$smarty.const.BASE_URL#}"></td>
                                    </tr>
                                    <tr>
                                        <td class="al-right"><span>云中心账号</span></td>
                                        <td><input class="form-control" type="text" id="cloudname" name="cloudname"
                                                   value="{#value($contentset,'cloudname')#}" disabled="disabled"></td>
                                    </tr>
                                    <tr>
                                        <td class="al-right"><span>云中心密码</span></td>
                                        <td><input class="form-control" type="password" id="cloudpass" name="cloudpass"
                                                   value="{#if $contentset['cloudok'] && $contentset['cloudpass']#}{#md52($contentset['cloudpass'])#}{#/if#}" disabled="disabled"></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td style="padding-top:20px;">
                                            {#if value($contentset,'cloudok')#}
                                                <input class="button button-primary button-rounded" type="submit" value="提交修改" style="display:none;">
                                                <a style="color:#0198cd;" id="editbind" href="javascript:void(0)">已绑定，点击这里可修改绑定。</a>
                                            {#else#}
                                                <input class="button button-primary button-rounded" type="submit" value="提交绑定">
                                                <span style="color:#9e9e9e">&nbsp;未绑定，绑定后继续享受更多服务！</span>
                                                <a style="color:#0198cd;" href="{#$smarty.const.CLOUD_BBS_URL#}member.php?mod=register" target="_blank">没有账号？立即注册</a>
                                            {#/if#}
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


<script type="text/javascript">
    $(document).ready(function() {
        $('#settingform').submit(function() {
            $.alert('正在保存...', 0);
            if (!$("#cloudname").val() || !$("#cloudpass").val()) {
                $.alert('账号密码不能为空！');
                return false;
            }
        });
        {#if value($contentset,'cloudok')#}
        $("#editbind").click(function(){
            $(this).prev("input").show();
            $(this).html("&nbsp;请输入要绑定的账号密码后点击提交修改！").unbind("click").css({
                color: '#73A4FF',
                cursor: 'default',
                textDecoration: 'none'
            });
            $("#cloudname").attr("disabled", false).val("");
            $("#cloudpass").attr("disabled", false).val("");
        });
        {#else#}
        $("#cloudname").attr("disabled", false);
        $("#cloudpass").attr("disabled", false);
        {#/if#}
    });
    function editbind() {

    }
    function checkmoney() {
        art.dialog({
            title: '查询剩余交易币',
            fixed: true,
            lock: true,
            opacity: '.3',
            content: '<div style="line-height:22px;">交易币可用于购买微窗中心<a href="{#$smarty.const.CLOUD_URL#}store/" style="color:#0198cd;" target="_blank">应用商城</a>的功能模块和短信验证码功能；<br/>交易币余额：<span class="moneyover" style="color:#E76E6E">正在查询...</span><br/>注：1交易币=10条短信验证码。</div>',
            button: [{
                name: '关闭',
                callback: function () {
                    return true;
                }
            }]
        });
        {#if value($contentset,'cloudok')#}
        $.ajax({
            url: "{#$urlarr.now#}",
            type: "post",
            data: {method: 'money_over'},
            dataType: "html",
            success: function (data) {
                $(".moneyover").html(data);
            },error : function () {
                $(".moneyover").text("查询出错");
            },
            cache: false
        });
        {#else#}
        $(".moneyover").text("请先绑定微窗中心！");
        {#/if#}
    }
</script>
{#include file="footer_admin.tpl"#}

</body>
</html>