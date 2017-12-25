
<!DOCTYPE html>
<html>
<head lang="zh-CN">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{#$_A.f.title#} - {#$BASE_NAME#}</title>
    <link rel="stylesheet" href="{#$CSS_PATH#}normalize.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}global.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}vip.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}alert.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}font-awesome.css"/>
    <script type="text/javascript" src="{#$JS_PATH#}jquery-1.11.0.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.alert.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.form.min.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}ajaxfileupload.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.colorpicker-title.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}ueditor/ueditor.config.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}ueditor/ueditor.all.min.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}artdialog/artDialog.js?skin=default"></script>
    <script type="text/javascript" src="{#$JS_PATH#}datepicker/WdatePicker.js"></script>

</head>
<body>
{#template("header")#}


<div class="wrapper">

    {#include file="menu.tpl" _item=4 _itemp=1#}


    <div class="main cf custom-menu">
        <div class="mod">
            <div class="main-bd main-page">
                <h2 class="title">{#$subtitle#}礼品券</h2>
                <div class="clearfix"></div>
                <hr/>

                <div class="mod mod-rounded mod-bordered">
                    <form action="{#weburl()#}"  method="post" id="saveform" class="form-services">
                        <table class="table table-form">
                            <tbody>
                            <tr>
                                <td>
                                    <div class="tdtitle">标题</div>
                                    <input name="title" type="text" class="form-control" id="title" value="{#value($content,'title')#}">
                                    <img src="{#$IMG_PATH#}colour.png" onclick="colorpicker(this);"/>
                                    <input type="hidden" id="title_color" name="title_color" value="{#value($content,'title_color')#}"/>
                                </td>
                                <script type="text/javascript">$("input#title").css("color", $("input#title_color").val());</script>
                            </tr>
                            <tr>
                                <td class="form-uetext">
                                    <div class="tdtitle">使用说明</div>
                                    <script id="content" name="content" type="text/plain" style="width:90%; height:300px;">{#value($content,'content')#}</script>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="tdtitle">有效期</div>
                                    <input type="text" id="startdate" name="startdate" value="{#value($content,'startdate')#}"
                                           class="form-control-small" onFocus="WdatePicker({maxDate:'#F{$dp.$D(\'enddate\',{d:-1});}'})"/>
                                    <span>至</span>
                                    <input type="text" id="enddate" name="enddate" value="{#value($content,'enddate')#}"
                                           class="form-control-small" onFocus="WdatePicker({minDate:'#F{$dp.$D(\'startdate\',{d:1});}'})"/>
                                    <span class="color-danger">【有效期】含开始日期当天时间</span>
                                </td>
                            </tr>
                            </tbody>
                            <tbody id="onlyadd">
                            <tr>
                                <td>
                                    <div class="tdtitle">
                                        选择会员类型①
                                        <small>
                                            {#if !$content#}
                                            (添加时符合条件的会员可收到，添加后不可修改)
                                            {#else#}
                                            (添加时符合条件的会员可收到，不可修改)
                                            {#/if#}
                                        </small>
                                    </div>
                                    <div class="row clearfix">
                                        <div class="col-25">
                                            <div class="checkbox"><label><input type="checkbox" name="vip_all" id="vip_all"{#che(value($content,'setting'),'全体会员')#}/>全体会员</label></div>
                                        </div>
                                        {#foreach from=$tequan item=item key=k#}
                                        <div class="col-25">
                                            <div class="checkbox"><label><input type="checkbox" id="vip_diy_{#$k#}" name="vip_diy_{#$k#}"{#che(value($content,'setting'),value($item,'name'))#}/>{#col(value($item,'name'),value($item,'color'))#}</label></div>
                                        </div>
                                        {#/foreach#}
                                    </div>
                                    <div class="row clearfix">
                                        <div class="col-25">
                                            <div class="checkbox"><label><input type="checkbox" name="vip_first" id="vip_first"{#che(value($content,'setting'),'首次开卡会员')#}/>首次开卡会员</label></div>
                                        </div>
                                        <div class="col-25">
                                            <div class="checkbox"><label><input name="vip_no" type="checkbox" id="vip_no"{#che(value($content,'setting'),'开卡从未消费的会员')#}/>开卡从未消费的会员</label></div>
                                        </div>
                                        <div class="col-25">
                                            <div class="checkbox"><label><input name="vip_onemoon" type="checkbox" id="vip_onemoon"{#che(value($content,'setting'),'一个月未消费的会员')#}/>一个月未消费的会员</label></div>
                                        </div>
                                    </div>
                                    <div class="row clearfix">
                                        <div class="col-50">
                                            <div class="checkbox"><label><input name="vip_total" type="checkbox" id="vip_total"{#che(value($content,'setting'),'累计消费')#}/>累计消费满 <input name="vip_val_total" type="text" id="vip_val_total" style="width:30px;" value="{#value($content,'setting|vip_val_total')#}"/> 元的会员</label>
                                            </div>
                                        </div>
                                        <div class="col-50">
                                            <div class="checkbox"><label><input name="vip_one" type="checkbox" id="vip_one"{#che(value($content,'setting'),'单次消费')#}/>单次消费满 <input name="vip_val_one" type="text" id="vip_val_one" style="width:30px;" value="{#value($content,'setting|vip_val_one')#}"/> 元的会员</label>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="col-50">
                                        <div class="tdtitle">兑换礼品所需积分</div>
                                        <input type="text" name="int_c" id="int_c" class="form-control-tiny" value="{#value($content,'int_c')#}" />
                                        <span>积分</span>
                                    </div>
                                    <div class="col-50">
                                        <div class="tdtitle">礼品券数量</div>
                                        每个用户可以获取到<input type="text" name="int_b" id="int_b" class="form-control-tiny" value="{#value($content,'int_b')#}" />
                                        <span>张券</span>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                            <tbody>
                            <tr>
                                <td>
                                    <div class="tdtitle">
                                        选择会员类型②
                                        <small>
                                            (未收到过的会员在有效期内符合条件可收到)
                                        </small>
                                    </div>
                                    <div class="row clearfix">
                                        <div class="col-25">
                                            <div class="checkbox"><label><input type="checkbox" name="onelogin" id="onelogin"{#che(value($content,'onelogin'),'1')#}/>新开卡会员</label></div>
                                        </div>
                                        {#foreach from=$tequan item=item key=k#}
                                        <div class="col-25">
                                            <div class="checkbox"><label><input type="checkbox" id="onevips_{#$k#}" name="onevips_{#$k#}"{#ches(value($content,'onevips'),value($item,'name'))#}/>{#col(value($item,'name'),value($item,'color'))#}</label></div>
                                        </div>
                                        {#/foreach#}
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="tdtitle">缩略图</div>
                                    <div class="form-input-upbox">
                                        {#tpl_form_image("img", value($content,'img'))#}
                                        <span class="help-inline" style="color:#888888;">(选填项)</span>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="control-submit">
                                        <input class="button" type="submit" value="{#$subtitle#}"> &nbsp;
                                        <a class="button primary" href="{#weburl('vip/giftcert')#}">取消</a>
                                        <input type="hidden" name="dosubmit" value="1">
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
</div>

<script type="text/javascript">
    $(document).ready(function() {
        UE.getEditor('content', {autoHeightEnabled:false});
        //添加保存数据
        $('#saveform').submit(function() {
            $.alert('正在保存...', 0);
            $(this).ajaxSubmit({
                dataType : 'json',
                success : function (data) {
                    $.alert(0);
                    if (data != null && data.success != null && data.success) {
                        $.showModal(data.message, "{#weburl('vip/giftcert')#}");
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
        {#if ($content)#}
        $("#onlyadd input").each(function(){
            $(this).prop("disabled", true);
        });
        $("#onlyadd").css("background-color", "#EFEFEF");
        $("#onlyadd").css("padding", "8px");
        {#/if#}
    });
</script>

{#template("footer")#}

</body>
</html>