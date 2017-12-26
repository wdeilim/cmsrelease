<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>微信开放平台 - {#$BASE_NAME#}</title>
    <link rel="stylesheet" href="{#$CSS_PATH#}admin/normalize.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}admin/global.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}admin/detail.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}font-awesome.css"/>
    <script type="text/javascript" src="{#$JS_PATH#}jquery-1.11.0.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.alert.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.form.min.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}placeholder.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}ueditor/ueditor.config.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}ueditor/ueditor.all.min.js"></script>
    <style type="text/css">
        body{overflow-y:scroll;}
        .section{min-height:447px;}
        .topmenu{position:relative;width:990px;height:36px;margin:15px auto 8px;padding-left:18px;background-color:#fff;border-bottom:3px solid #09c}
        .topmenu a{display:block;float:left;line-height:36px;padding:0 12px;margin-right:12px;color:#09c}
        .topmenu a.active,.topmenu a:hover{-webkit-border-radius:3px 3px 0 0;-moz-border-radius:3px 3px 0 0;border-radius:3px 3px 0 0;color:#fff;font-weight:700;background-color:#09c}
        .topmenu div{display:none;border:1px solid #09c;border-top:3px solid #09c;background-color:#fff;position:absolute;top:36px;left:0;z-index:9999}
        .topmenu div a{float:none;margin:0}
        .topmenu div a.active,.topmenu div a:hover{-webkit-border-radius:0;-moz-border-radius:0;border-radius:0}
        .tabmenu{display:none;min-height:308px;}
        thead th{background-color:#DDD;padding:10px}
        .utis{padding:15px;margin-bottom:20px;border:1px solid #F2F2F2;border-radius:4px;color:#3eb74f;background-color:#F2F2F2}
        .applist em{display:block;float:left;padding-right:5px;font-style:normal;white-space:nowrap;}
        .al-label label{padding-right:10px;}
        .al-label label input{width:14px;height:14px;vertical-align:middle;margin:-2px 2px 0 0}
    </style>
</head>
<body>

{#include file="header_admin.tpl"#}
<script>$('#head-nav-menu>li:eq(2)').addClass('active');</script>


<div class="breadcrumb-wrapper main-box">
    <div class="breadcrumb">
        <div class="items">
            <span class="description">当前位置：</span>
            <span class="current">系统管理</span>
            <i class="iconfont">&#xe621;</i>
            <span class="current">微信开放平台</span>
        </div>
    </div>
</div>

<div class="main-wrapper main-box">
    <div class="main">
        <div class="row cf">
            <div class="col" style="margin-left:23px;">
                {#include file="setting_left.tpl"#}
                <script>$('#setting-nav-menu li#nav-openweixin a').css('color','#ff6600');</script>
            </div>
            <div class="col" style="width: 1050px;">
                <div class="main-content">
                    <div class="module">
                        <div class="section">
                            <div class="utis">注：开发信息（AppID、AppSecret等）需开放平台审核通过后才可以看到，填写完后才可以使用授权登录功能，<a href="https://open.weixin.qq.com/cgi-bin/frame?t=home/wx_plugin_tmpl&lang=zh_CN" target="_blank" style="color:#4596ff">了解更多公众号第三方平台！</a></div>
                            <div class="topmenu" id="topmenu">
                                <a href="javascript:;" data-index="set">公众号第三方平台</a>
                            </div>

                            <div class="tabmenu" id="tabmenu-1">
                                <form action="{#get_url()#}" method="post" id="settingform">
                                    <table class="table-setting">
                                        <tbody>
                                        <tr>
                                            <td class="al-right" width="150"><span>是否启用</span></td>
                                            <td class="al-label">
                                                <label><input type="radio" name="openweixin[open]" value="1"{#if $openweixin['open']#} checked{#/if#}>开启</label>
                                                <label><input type="radio" name="openweixin[open]" value="0"{#if !$openweixin['open']#} checked{#/if#}>关闭</label>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="al-right"><span>AppID</span></td>
                                            <td><input class="form-control" type="text" name="openweixin[appid]"
                                                       value="{#$openweixin['appid']#}"></td>
                                        </tr>
                                        <tr>
                                            <td class="al-right"><span>AppSecret</span></td>
                                            <td><input class="form-control" type="text" name="openweixin[secret]"
                                                       value="{#$openweixin['secret']#}"></td>
                                        </tr>
                                        <tr>
                                            <td class="al-right"><span>消息校验Token</span></td>
                                            <td><input class="form-control" type="text" name="openweixin[token]"
                                                       value="{#if $openweixin['token']#}{#$openweixin['token']#}{#else#}{#generate_password(16)#}{#/if#}"></td>
                                        </tr>
                                        <tr>
                                            <td class="al-right"><span>消息加解密Key</span></td>
                                            <td><input class="form-control" type="text" name="openweixin[key]"
                                                       value="{#if $openweixin['key']#}{#$openweixin['key']#}{#else#}{#generate_password(43)#}{#/if#}"></td>
                                        </tr>
                                        <tr style="display:none">
                                            <td class="al-right">
                                                <span>授权公众号(AppID)</span><br/>
                                                <a href="javascript:;" onclick="_add();" style="color:#4596ff">添加授权</a>
                                            </td>
                                            <td class="applist">
                                                {#foreach $openweixin['applist'] AS $key=>$item#}
                                                    {#if $key#}<em>{#$item#}（{#$key#}）</em>{#/if#}
                                                {#foreachelse#}
                                                    <em class="no">（无）</em>
                                                {#/foreach#}
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="al-right"><span>发起页域名</span></td>
                                            <td><input class="form-control" type="text" disabled="disabled"
                                                       value="{#$smarty.server.HTTP_HOST#}"></td>
                                        </tr>
                                        <tr>
                                            <td class="al-right"><span>网页开发域名</span></td>
                                            <td><input class="form-control" type="text" disabled="disabled"
                                                       value="{#$smarty.server.HTTP_HOST#}"></td>
                                        </tr>
                                        <tr>
                                            <td class="al-right"><span>事件接收URL</span></td>
                                            <td><input class="form-control" type="text" disabled="disabled"
                                                       value="{#$_A['url']['index']#}open/weixin/receive/"></td>
                                        </tr>
                                        <tr>
                                            <td class="al-right"><span>消息与事件接收URL</span></td>
                                            <td><input class="form-control" type="text" disabled="disabled"
                                                       value="{#$_A['url']['index']#}open/weixin/callback/$APPID$/"></td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td>
                                                <input class="button button-primary button-rounded" type="submit" value="保存配置">
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
</div>

<script type="text/javascript">
    $(document).ready(function() {
        //初始化TAB
        $("#topmenu a").each(function(index){
            $(this).attr("d-index", index);
            $(this).click(function(){
                $("#topmenu a").removeClass("active");
                $(this).addClass("active");
                $("div.tabmenu").hide().eq($(this).attr("d-index")).show();
            });
        });
        if ($("#topmenu a[data-index='{#$_GPC['index']#}']").length > 0) {
            $("#topmenu a[data-index='{#$_GPC['index']#}']").click();
        }else{
            $("#topmenu a:eq(0)").click();
        }
        //
        $('form#settingform').submit(function() {
            $.alert('正在保存...', 0);
            $(this).ajaxSubmit({
                dataType : 'json',
                success : function (data) {
                    $.alert(0);
                    if (data != null && data.success != null && data.success) {
                        $.showModal('保存成功。', '{#get_link('index')#}&index='+$("#topmenu").find("a.active").attr("data-index"));
                    } else {
                        if (data != null && data.message != null && data.message) {
                            $.showModal(data.message);
                        }else{
                            $.showModal('保存失败。');
                        }
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
{#include file="footer_admin.tpl"#}

</body>
</html>