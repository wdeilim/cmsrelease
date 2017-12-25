
<!DOCTYPE html>
<html>
<head lang="zh-CN">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{#$_A.f.title#} - {#$BASE_NAME#}</title>
    <link rel="stylesheet" href="{#$CSS_PATH#}bootstrap.min.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}normalize.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}global.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}vip.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}alert.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}font-awesome.css"/>
    <script type="text/javascript" src="{#$JS_PATH#}jquery-1.11.0.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.alert.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.keyauto.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.form.min.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}ajaxfileupload.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}ueditor/ueditor.config.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}ueditor/ueditor.all.min.js"></script>
    <style type="text/css">
        textarea.form-control{width:500px;height:220px;padding:5px;}
        .vipmenu,.vipmenu *{-webkit-box-sizing:content-box;-moz-box-sizing:content-box;box-sizing:content-box}
        .form-control {width: 100%;border: 1px solid #ccc;padding:6px 12px;}
        .hitemc {float:none;display:block;width:auto;color:#666666;text-align:left !important;margin-bottom:5px !important;}
        #_regitem label {width: auto;display: block;float: left;line-height: 20px;padding: 5px 35px 5px 5px;font-weight: normal;}
        #_regitem label input {padding-right: 5px;vertical-align: top;}
        #_showitem label {width: auto;display: block;float: left;line-height: 20px;padding: 5px 35px 5px 5px;font-weight: normal;}
        #_showitem label input {padding-right: 5px;vertical-align: top;}
        .addlink {padding-left:10px;}
        .dellink {line-height:32px;}
        .form-keyauto {border: 1px solid #ccc !important;border-radius:3px;overflow:hidden;}
    </style>
</head>
<body>
{#template("header")#}


<div class="wrapper">

    {#include file="menu.tpl" _item=9#}


    <div class="main cf custom-menu">
        <div class="mod">
            <div class="main-bd" style="margin:0 20px;">
                <div class="mod mod-rounded mod-bordered">
                    <form action="{#weburl()#}"  method="post" id="saveform" class="form-services form-horizontal form ng-pristine ng-valid">

                        <div class="form-group">
                            <div class="col-sm-12">

                                <div class="panel panel-default">
                                    <div class="panel-heading">基本设置</div>
                                    <div class="panel-body">

                                        <div class="form-group">
                                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">触发关键词</label>
                                            <div class="col-xs-12 col-sm-9">
                                                <input class="form-control" type="text" id="key" name="key" value="{#value($reply,'key')#}" placeholder="多个请用英文逗号“,”隔开">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">状态</label>
                                            <div class="col-xs-12 col-sm-9">
                                                <select id="status" name="status" class="form-control" style="width:auto">
                                                    <option value="启用">启用</option>
                                                    <option value="不启用">不启用</option>
                                                </select>
                                                {#if value($reply,'status')#}
                                                    <script>$('#status').val('{#value($reply,'status')#}');</script>
                                                {#/if#}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">匹配类型</label>
                                            <div class="col-xs-12 col-sm-9">
                                                <select id="match" name="match" class="form-control" style="width:auto">
                                                    <option value="0">完全匹配</option>
                                                    <option value="1">包含匹配</option>
                                                </select>
                                                {#if value($reply,'match')#}
                                                    <script>$('#match').val('{#value($reply,'match')#}');</script>
                                                {#/if#}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">回复内容</label>
                                            <div class="col-xs-12 col-sm-9">
                                                {#tpl_form_aledit('content',$reply.content,'imagetext')#}
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <div class="panel panel-default">
                                    <div class="panel-heading">会员相关</div>
                                    <div class="panel-body">

                                        <div class="form-group">
                                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">显示的信息项</label>
                                            <div class="col-xs-12 col-sm-9" id="_showitem">
                                                <div class="clearfix">
                                                    <label>
                                                        <input type="checkbox" checked="checked" disabled="disabled">姓名
                                                        <input name="setting[showitem][fullname]" type="hidden" value="fullname">
                                                    </label>
                                                    <label>
                                                        <input type="checkbox" checked="checked" disabled="disabled">手机
                                                        <input name="setting[showitem][phone]" type="hidden" value="phone">
                                                    </label>
                                                    <label><input name="setting[showitem][code]" data-val="{#value($reply,'setting|showitem|code')#}"
                                                                  type="checkbox" value="code">手机验证码</label>
                                                    <label><input name="setting[showitem][email]" data-val="{#value($reply,'setting|showitem|email')#}"
                                                                  type="checkbox" value="email">邮箱</label>
                                                    <label><input name="setting[showitem][address]" data-val="{#value($reply,'setting|showitem|address')#}"
                                                                  type="checkbox" value="address">地址</label>
                                                    <label><input name="setting[showitem][idnumber]" data-val="{#value($reply,'setting|showitem|idnumber')#}"
                                                                  type="checkbox" value="idnumber">身份证</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">强制完善信息</label>
                                            <div class="col-xs-12 col-sm-9">
                                                <select id="haveinfo" name="setting[haveinfo]" class="form-control" style="width:auto">
                                                    <option value="不强制">不强制</option>
                                                    <option value="强制">强制</option>
                                                </select>
                                                {#if value($reply,'setting|haveinfo')#}
                                                    <script>$('#haveinfo').val('{#value($reply,'setting|haveinfo')#}');</script>
                                                {#/if#}
                                                <div class="help-block">用户进入会员卡强制填写及相关会员资料。</div>
                                                <div id="haveinfogroup" style="display:none">
                                                    <div class="form-group" style="margin-bottom:0;">
                                                        <label class="col-xs-12 col-sm-3 col-md-2 control-label hitemc">强制完善项</label>
                                                        <div class="col-xs-12 col-sm-9" id="_regitem">
                                                            <div class="clearfix">
                                                                <label><input name="setting[haveitem][fullname]" data-val="{#value($reply,'setting|haveitem|fullname')#}"
                                                                              type="checkbox" value="fullname">姓名</label>
                                                                <label><input name="setting[haveitem][phone]" data-val="{#value($reply,'setting|haveitem|phone')#}"
                                                                              type="checkbox" value="phone">手机</label>
                                                                <label>
                                                                    <input type="checkbox" checked="checked" disabled="disabled">手机验证码
                                                                    <input name="setting[haveitem][code]" type="hidden" value="code">
                                                                </label>
                                                                <label><input name="setting[haveitem][email]" data-val="{#value($reply,'setting|haveitem|email')#}"
                                                                              type="checkbox" value="email">邮箱</label>
                                                                <label><input name="setting[haveitem][address]" data-val="{#value($reply,'setting|haveitem|address')#}"
                                                                              type="checkbox" value="address">地址</label>
                                                                <label><input name="setting[haveitem][idnumber]" data-val="{#value($reply,'setting|haveitem|idnumber')#}"
                                                                              type="checkbox" value="idnumber">身份证</label>
                                                            </div>
                                                            <div class="help-block">不勾选任何强制项则不强制。</div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="col-xs-12 col-sm-3 col-md-2 control-label hitemc">完善资料提示语</label>
                                                        <div class="col-xs-12 col-sm-9" id="_regitem">
                                                            <input class="form-control" type="text" name="setting[havetis]" value="{#value($reply,'setting|havetis')#}">
                                                            <div class="help-block">留空默认提示：请完善资料后继续访问！</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        自定义菜单
                                        <span class="text-muted">在这里可以自定义给会员卡用户首页添加链接地址。</span>
                                    </div>
                                    <div class="panel-body">
                                        <table class="table table-hover" id="vip_link">
                                            <thead>
                                            <tr>
                                                <th width="40%">链接名称<a class="addlink" href="javascript:;" onclick="addlink();">【添加】</a></th>
                                                <th>链接地址</th>
                                                <th width="50"></th>
                                            </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-12">
                                <input name="submit" type="submit" value="提交" class="btn btn-primary col-lg-1">
                                <input type="hidden" name="dosubmit" value="1">
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<table id="setlinktemp" style="display:none;">
    <tbody>
    <tr class="setlink">
        <td><input name="setting[link][][title]" type="text" class="form-control t" id="link_title" placeholder="要显示的链接标题"></td>
        <td><input name="setting[link][][url]" type="text" class="form-control" id="link_url" placeholder="请输入完整的链接地址; 如: http://m.abc.com/"></td>
        <td><a class="dellink" href="javascript:;" onclick="dellink(this)">删除</a></td>
    </tr>
    </tbody>
</table>
<script id="settinglink" type="text/plain" style="display:none;">
{#json_encode($reply['setting']['link'])#}
</script>
<script type="text/javascript">
    var link_j = 0;
    function addlink(data) {
        $intemp = $($("#setlinktemp").html());
        if (data) {
            $intemp.find("#link_title").val(data.title);
            $intemp.find("#link_url").val(data.url);
        }
        $intemp.find("#link_title").attr("name", "setting[link]["+link_j+"][title]");
        $intemp.find("#link_url").attr("name", "setting[link]["+link_j+"][url]");
        $("#vip_link").append($intemp);
        link_j++;
    }
    function dellink(obj) {
        $(obj).parents("tbody").remove();
    }
    $(document).ready(function() {
        $("#key").keyauto(false, 'form-keyauto');
        //添加保存数据
        $('#saveform').submit(function() {
            $.alert('正在保存...', 0);
            $(this).ajaxSubmit({
                dataType : 'json',
                success : function (data) {
                    if (data != null && data.success != null && data.success) {
                        $.alert("保存成功！");
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
        //
        $("input[type='checkbox']").each(function(){
            if ($(this).attr("data-val") && $(this).attr("data-val") == $(this).val()) {
                $(this).prop("checked", true);
            }
        });
        //
        $("#haveinfo").change(function(){
            if ($(this).val() == '强制') {
                $("#haveinfogroup").show();
            }else{
                $("#haveinfogroup").hide();
            }
        }).change();
        //
        $("#_showitem").find("input[type='checkbox']").change(function(){
            if ($(this).is(":checked")) {
                $("#_regitem").find("input[value='"+$(this).val()+"']").parent().show();
            }else{
                $("#_regitem").find("input[value='"+$(this).val()+"']").parent().hide();
            }
        }).change();
        //
        var _json = eval($("#settinglink").html());
        if (_json) {
            for(var i=0; i<_json.length; i++){
                addlink(_json[i]);
            }
        }
    });
</script>

{#template("footer")#}

</body>
</html>