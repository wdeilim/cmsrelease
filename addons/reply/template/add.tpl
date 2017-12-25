
<!DOCTYPE html>
<html>
<head lang="zh-CN">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{#$submit#}文字回复 - {#$func.f.title#} - {#$BASE_NAME#}</title>
    <link rel="stylesheet" href="{#$CSS_PATH#}normalize.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}global.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}reply.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}alert.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}font-awesome.css"/>
    <script type="text/javascript" src="{#$JS_PATH#}jquery-1.11.0.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.alert.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}aliedit/jquery.aliedit.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.form.min.js"></script>
    <style type="text/css">
        .form-label label {padding-right:10px;}
        .form-label input {width:18px;height:18px;vertical-align:middle;}
        .radio_0,.radio_1 {display:none;background-color:#F0F5FA;}
        .radio_0 td,.radio_1 td {vertical-align:top;line-height:20px}
        .radio_0 td.al-right,.radio_1 td.al-right {padding-top:20px !important;}
    </style>
</head>
<body>
{#template("header")#}



<div class="wrapper">
    <div class="breadcrumb">
        <div class="items">
            <span class="description">当前位置：</span>
            <span>{#$func.f.title#}</span>
            <i class="iconfont">&#xe621;</i>
            <span>关键词回复</span>
            <i class="iconfont">&#xe621;</i>
            <span>{#$submit#}回复</span>
        </div>
    </div>
    <div class="main cf custom-menu">
        <div class="mod">
            <div class="col-left">
                {#include file="left.tpl" _item=1 wx_level=$_A['al']['wx_level']#}
            </div>
            <div class="col-right">
                <div class="main-bd">

                    <div class="control-group">
                        {#if $submit=='修改'#}
                            <a class="button" href="{#$urlarr.2#}add/{#get_get()#}">+新建回复</a> &nbsp;
                            <a class="button button-hover" href="{#get_url()#}">修改回复</a> &nbsp;
                        {#else#}
                            <a class="button button-hover" href="{#$urlarr.2#}add/{#get_get()#}">+新建回复</a> &nbsp;
                        {#/if#}
                        <a class="button" href="{#$urlarr.2#}{#get_get()#}">返回列表</a>
                    </div>

                    <form action="{#$urlarr.now#}{#get_get()#}"  method="post" id="saveform">
                        <table class="table table-form">
                            <tbody>
                            <tr>
                                <td class="al-right">关键词</td>
                                <td style="line-height: 25px;">
                                    <input class="form-control" type="text" id="key" name="key" value="{#value($reply,'key')#}">
                                    （设置多个关键词请用逗号分开）
                                </td>
                            </tr>
                            <tr>
                                <td class="al-right">状态</td>
                                <td class="form-reg">
                                    <select id="status" name="status">
                                        <option value="启用">启用</option>
                                        <option value="不启用">不启用</option>
                                    </select>
                                    {#if value($reply,'status')#}
                                        <script>$('#status').val('{#value($reply,'status')#}');</script>
                                    {#/if#}
                                </td>
                            </tr>
                            <tr>
                                <td class="al-right">匹配类型</td>
                                <td class="form-reg">
                                    <select id="match" name="match">
                                        <option value="0">完全匹配</option>
                                        <option value="1">包含匹配</option>
                                    </select>
                                    {#if value($reply,'match')#}
                                        <script>$('#match').val('{#value($reply,'match')#}');</script>
                                    {#/if#}
                                </td>
                            </tr>
                            <tr>
                                <td class="al-right">接口类型</td>
                                <td class="form-label">
                                    <label><input{#if !value($reply,'setting|apitype')#} checked{#/if#} type="radio" name="setting[apitype]" value="0">基本回复</label>
                                    <label><input{#if value($reply,'setting|apitype')#} checked{#/if#} type="radio" name="setting[apitype]" value="1">自定义接口(暂不支持服务窗)</label>
                                </td>
                            </tr>
                            </tbody>
                            <tbody class="radio_0">
                            <tr>
                                <td class="al-right">回复内容</td>
                                <td style="line-height: 25px;" >
                                    {#tpl_form_aledit('content', value($reply,'content'), 1)#}
                                </td>
                            </tr>
                            </tbody>
                            <tbody class="radio_1">
                            <tr>
                                <td class="al-right">远程地址</td>
                                <td>
                                    <input class="form-control" type="text" name="setting[api_url]" value="{#value($reply,'setting|api_url')#}"><br/>
                                    使用远程地址接口，你可以兼容其他的平台管理工具。<br/>
                                    你应该填写其他平台提供给你保存至公众平台的URL和Token。
                                </td>
                            </tr>
                            <tr>
                                <td class="al-right" style="color:#f00">Token</td>
                                <td>
                                    <input class="form-control" type="text" name="setting[api_token]" value="{#value($reply,'setting|api_token')#}"><br/>
                                    与目标平台接入设置值一致，必须为英文或者数字，长度为3到32个字符。
                                </td>
                            </tr>
                            </tbody>
                            <tbody>
                            <tr>
                                <td></td>
                                <td>
                                    <input class="button" type="submit" value="{#$submit#}"> &nbsp;
                                    <input class="button" type="button" value="返回" onclick="location.href='{#$urlarr.2#}{#get_get()#}';">
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

<script type="text/javascript">
    $(document).ready(function() {
        $(".form-label").find("input").change(function(){
            if ($(this).is(":checked")) {
                $(".radio_0,.radio_1").hide();
                $(".radio_"+$(this).val()).show();
            }
        }).change();
        //
        $('#saveform').submit(function() {
            $.alert('正在保存...', 0);
            $(this).ajaxSubmit({
                dataType : 'json',
                success : function (data) {
                    $.alert(0);
                    if (data != null && data.success != null && data.success) {
                        $.showModal(data.message, '{#$urlarr.2#}{#get_get()#}');
                    } else {
                        $.showModal(data.message);
                    }
                },
                error : function () {
                    $.alert(0);
                    $.inModal("保存失败！");
                }
            });
            return false;
        });
    });
</script>

{#template("footer")#}

</body>
</html>