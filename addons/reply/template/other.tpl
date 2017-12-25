
<!DOCTYPE html>
<html>
<head lang="zh-CN">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>非关键词回复 - {#$func.f.title#} - {#$BASE_NAME#}</title>
    <link rel="stylesheet" href="{#$CSS_PATH#}normalize.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}global.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}reply.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}alert.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}font-awesome.css"/>
    <script type="text/javascript" src="{#$JS_PATH#}jquery-1.11.0.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.alert.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}aliedit/jquery.aliedit.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.form.min.js"></script>
</head>
<body>
{#template("header")#}


<div class="wrapper">
    <div class="breadcrumb">
        <div class="items">
            <span class="description">当前位置：</span>
            <span>{#$func.f.title#}</span>
            <i class="iconfont">&#xe621;</i>
            <span>非关键词回复</span>
        </div>
    </div>
    <div class="main cf custom-menu">
        <div class="mod">
            <div class="col-left">
                {#include file="left.tpl" _item=2 wx_level=$_A['al']['wx_level']#}
            </div>
            <div class="col-right">
                <div class="main-bd">

                    <form action="{#$urlarr.now#}{#get_get()#}"  method="post" id="saveform">
                        <table class="table table-form">
                            <tbody>
                            <tr>
                                <td class="al-right">无效关键词回复</td>
                                <td class="form-reg">
                                    <select id="status" name="status">
                                        <option value="不启用">不启用</option>
                                        <option value="启用">启用</option>
                                    </select>
                                    {#if value($setting, 'nonekey|status')#}
                                    <script>$('#status').val('{#value($setting, 'nonekey|status')#}');</script>
                                    {#/if#}
                                </td>
                            </tr>
                            <tr>
                                <td class="al-right" valign="top" style="padding-top:22px;">回复内容</td>
                                <td style="line-height: 25px;" >
                                    {#tpl_form_aledit('content', value($setting,'nonekey|content'),1)#}
                                </td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>
                                    <input class="button" type="submit" value="保存">
                                    <input class="button" type="reset" value="重置">
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
        //
        $('#saveform').submit(function() {
            $.alert('正在保存...', 0);
            $(this).ajaxSubmit({
                dataType : 'json',
                success : function (data) {
                    if (data != null && data.success != null && data.success) {
                        $.alert(data.message);
                    } else {
                        $.alert(0);
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