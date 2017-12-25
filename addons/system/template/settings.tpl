<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>系统设置 - {#$BASE_NAME#}</title>
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
        textarea.form-control {width:450px;height:100px;padding-top:5px;}
        .tis{color:#989898;padding-left:5px;}
        .topmenu{position:relative;width:990px;height:36px;margin:15px auto 8px;padding-left:18px;background-color:#fff;border-bottom:3px solid #09c}
        .topmenu a{display:block;float:left;line-height:36px;padding:0 12px;margin-right:12px;color:#09c}
        .topmenu a.active,.topmenu a:hover{-webkit-border-radius:3px 3px 0 0;-moz-border-radius:3px 3px 0 0;border-radius:3px 3px 0 0;color:#fff;font-weight:bold;background-color:#09c}
        .topmenu div{display:none;border:1px solid #09c;border-top:3px solid #09c;background-color:#fff;position:absolute;top:36px;left:0;z-index:9999}
        .topmenu div a{float:none;margin:0}
        .topmenu div a.active,.topmenu div a:hover{-webkit-border-radius:0;-moz-border-radius:0;border-radius:0}
        .tabmenu{display:none;}
        select.form-control {width:465px;padding:8px 5px;}
        thead th {background-color: #DDDDDD; padding: 10px;}
        table.table-setting tbody tr td:first-child {vertical-align:top;padding-top:22px;}
        #_topmenu input,#_topmenu select{width:238px;padding: 8px 5px;}
        #_regitem label {width: 160px;display: block;float: left;line-height: 70px;padding: 5px;}
        #_regitem label input {padding-right: 5px;}
    </style>
</head>
<body>

{#include file="header_admin.tpl"#}
<script>$('#head-nav-menu li:eq(2)').addClass('active');</script>


<div class="breadcrumb-wrapper">
    <div class="breadcrumb">
        <div class="items">
            <span class="description">当前位置：</span>
            <span class="current">系统管理</span>
            <i class="iconfont">&#xe621;</i>
            <span class="current">配置设置</span>
        </div>
    </div>
</div>

<div class="main-wrapper">
    <div class="main">
        <div class="row cf">
            <div class="col" style="margin-left:23px;">
                {#include file="setting_left.tpl"#}
                <script>$('#setting-nav-menu a:eq(0)').css('color','#ff6600');</script>
            </div>
            <div class="col" style="width: 1050px;">
                <div class="main-content">
                    <div class="module">
                        <div class="section">

                            <div class="topmenu" id="topmenu">
                                <a href="javascript:;">系统设置</a>
                                <a href="javascript:;">顶部菜单</a>
                                <a href="javascript:;">会员注册项</a>
                                <a href="javascript:;">关闭网站</a>
                                <a href="javascript:;" data-index="safe">安全设置</a>
                                <a href="javascript:;">其他设置</a>
                            </div>

                            <div class="tabmenu" id="tabmenu-1">
                                <form action="{#get_url()#}" method="post" id="settingform">
                                    <table class="table-setting">
                                        <tbody>
                                        <tr>
                                            <td class="al-right"><span>网站名称</span></td>
                                            <td><input class="form-control" type="text" id="BASE_NAME" name="SET_BASE_NAME"
                                                       value="{#$smarty.const.DIY_BASE_NAME#}" placeholder="{#$smarty.const.BASE_NAME#}"></td>
                                        </tr>
                                        <tr>
                                            <td class="al-right"><span>品牌名称</span></td>
                                            <td><input class="form-control" type="text" id="BRAND_NAME" name="SET_BRAND_NAME"
                                                       value="{#$smarty.const.DIY_BRAND_NAME#}" placeholder="{#$smarty.const.BRAND_NAME#}"></td>
                                        </tr>
                                        <tr>
                                            <td class="al-right"><span>品牌网址</span></td>
                                            <td><input class="form-control" type="text" id="BRAND_URL" name="SET_BRAND_URL"
                                                       value="{#$smarty.const.DIY_BRAND_URL#}" placeholder="{#$smarty.const.BRAND_URL#}"></td>
                                        </tr>
                                        <tr>
                                            <td class="al-right"><span>首页文件</span></td>
                                            <td><select class="form-control" id="BASE_IPAGE" name="SET_BASE_IPAGE">
                                                    {#if !$noweijingtai#}
                                                    <option value="">隐藏文件 (如果你的服务器支持伪静态)</option>
                                                    {#/if#}
                                                    {#if !$nomoren#}
                                                    <option value="index.php">index.php (默认的一种形式)</option>
                                                    {#/if#}
                                                    <option value="index.php?">index.php? (可能是你的服务器不支持PATH_INFO)</option>
                                                </select>
                                                {#if $smarty.const.BASE_IPAGE#}
                                                <script>
                                                    if ($("#BASE_IPAGE").find("option[value='{#$smarty.const.BASE_IPAGE#}']").length > 0) {
                                                        $("#BASE_IPAGE").val('{#$smarty.const.BASE_IPAGE#}');
                                                    }
                                                </script>
                                                {#/if#}
                                                <span class="tis">没有开启伪静态时请选择“index.php”</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="al-right"><span>客服QQ</span></td>
                                            <td><input class="form-control" type="text" id="LINKQQ_PATH" name="SET_LINKQQ_PATH"
                                                       value="{#$smarty.const.DIY_LINKQQ_PATH#}" placeholder="请填写客服QQ"></td>
                                        </tr>
                                        <tr>
                                            <td class="al-right"><span>积分名称</span></td>
                                            <td><input class="form-control" type="text" id="POINT_NAME" name="SET_POINT_NAME"
                                                       value="{#$smarty.const.DIY_POINT_NAME#}">
                                                <span class="tis">留空默认名称为“积分”</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="al-right"><span>充值比例</span></td>
                                            <td><input class="form-control" type="text" id="POINT_CONVERT" name="SET_POINT_CONVERT"
                                                       value="{#$smarty.const.DIY_POINT_CONVERT#}">
                                                <span class="tis">1元 = ?积分；留空默认:1</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="al-right"><span>短信验证</span></td>
                                            <td><select class="form-control" id="SMS_OPEN" name="SET_SMS_OPEN">
                                                    <option value="0">关闭短信验证</option>
                                                    <option value="1">开通短信验证</option>
                                                </select>
                                                <script>$("#SMS_OPEN").val('{#intval($smarty.const.DIY_SMS_OPEN)#}');</script>
                                                <span class="tis">此功能必须绑定<a href="{#$urlarr.3#}cloud/" style="color:#4596ff">微窗中心</a></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="al-right"><span>短信比例</span></td>
                                            <td><input class="form-control" type="text" id="SMS_PROPORTION" name="SET_SMS_PROPORTION"
                                                       value="{#$smarty.const.DIY_SMS_PROPORTION#}">
                                                <span class="tis">1积分 = ?短信验证；留空默认:10</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="al-right"><span>底部信息</span></td>
                                            <td><textarea class="form-control" type="text" id="BOTTOM_INFO" name="SET_BOTTOM_INFO"
                                                          placeholder="{#$smarty.const.BOTTOM_INFO|htmlspecialchars#}">{#$smarty.const.DIY_BOTTOM_INFO#}</textarea></td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td>
                                                <input class="button button-primary button-rounded" type="submit" value="保存">&nbsp;
                                                <input class="button button-primary button-rounded" type="reset" value="重置">
                                                <input type="hidden" name="dosubmit" value="1">
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <input type="hidden" name="_type" value="setting">
                                </form>
                            </div>
                            <div class="tabmenu" id="tabmenu-2">
                                <form action="{#get_url()#}" method="post" id="settingform">
                                    <table class="table-setting" style="width:908px">
                                        <thead>
                                        <tr>
                                            <th>链接名称</th>
                                            <th>链接地址</th>
                                            <th>打开方式</th>
                                            <th width="80">操作</th>
                                        </tr>
                                        </thead>
                                        <tbody id="_topmenu">
                                        {#foreach from=$setting['content']['topmenu'] item=list#}
                                            <tr>
                                                <td><input type="text" name="title[]" value="{#$list.title#}"></td>
                                                <td><input type="text" name="link[]" value="{#$list.link#}"></td>
                                                <td>
                                                    <select name="target[]">
                                                        <option value="_blank">新窗口打开</option>
                                                        <option value="_self"{#if $list.target=='_self'#} selected{#/if#}>原窗口打开</option>
                                                    </select>
                                                </td>
                                                <td class="align-center">
                                                    <a href="javascript:;" onclick="addtopmenu(this)">[增加]</a>
                                                    <a href="javascript:;" onclick="deltopmenu(this)">[删除]</a>
                                                </td>
                                            </tr>
                                        {#foreachelse#}
                                            <tr>
                                                <td><input type="text" name="title[]"></td>
                                                <td><input type="text" name="link[]"></td>
                                                <td>
                                                    <select name="target[]">
                                                        <option value="_blank">新窗口打开</option>
                                                        <option value="_self">原窗口打开</option>
                                                    </select>
                                                </td>
                                                <td class="align-center">
                                                    <a href="javascript:;" onclick="addtopmenu(this)">[增加]</a>
                                                    <a href="javascript:;" onclick="deltopmenu(this)">[删除]</a>
                                                </td>
                                            </tr>
                                        {#/foreach#}
                                        </tbody>
                                        <tbody>
                                        <tr>
                                            <td colspan="4">
                                                <input class="button button-primary button-rounded" type="submit" value="保存">&nbsp;
                                                <input class="button button-primary button-rounded" type="reset" value="重置">
                                                <input type="hidden" name="dosubmit" value="1">
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <input type="hidden" name="_type" value="topmenu">
                                </form>
                            </div>
                            <div class="tabmenu" id="tabmenu-3">
                                <form action="{#get_url()#}" method="post" id="settingform">
                                    <table class="table-setting" style="width:908px">
                                        <tbody id="_regitem">
                                        <tr>
                                            <td>
                                                <label><input{#chi($setting['content']['regitem'],'fullname')#} name="regitem[fullname]" type="checkbox" value="fullname">姓名</label>
                                                <label><input{#chi($setting['content']['regitem'],'phone')#} name="regitem[phone]" type="checkbox" value="phone">手机号码</label>
                                                <label><input{#chi($setting['content']['regitem'],'email')#} name="regitem[email]" type="checkbox" value="email">邮箱地址</label>
                                                <label><input{#chi($setting['content']['regitem'],'qqnum')#} name="regitem[qqnum]" type="checkbox" value="qqnum">QQ</label>
                                                <label><input{#chi($setting['content']['regitem'],'companyname')#} name="regitem[companyname]" type="checkbox" value="companyname">公司名称</label>
                                                <label><input{#chi($setting['content']['regitem'],'tel')#} name="regitem[tel]" type="checkbox" value="tel">电话</label>
                                                <label><input{#chi($setting['content']['regitem'],'linkaddr')#} name="regitem[linkaddr]" type="checkbox" value="linkaddr">地区</label>
                                                <label><input{#chi($setting['content']['regitem'],'address')#} name="regitem[address]" type="checkbox" value="address">详细地址</label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding-top: 30px;">
                                                <input class="button button-primary button-rounded" type="submit" value="保存">&nbsp;
                                                <input class="button button-primary button-rounded" type="reset" value="重置">
                                                <input type="hidden" name="dosubmit" value="1">
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <input type="hidden" name="_type" value="_regitem">
                                </form>
                            </div>
                            <div class="tabmenu" id="tabmenu-4">
                                <form action="{#get_url()#}" method="post" id="settingform">
                                    <table class="table-setting">
                                        <tbody>
                                        <tr>
                                            <td class="al-right"><span>关闭网站</span></td>
                                            <td>
                                                <label style="line-height:40px;"><input name="OFF_SITE_IS"{#if $smarty.const.OFF_SITE_IS#} checked{#/if#} type="checkbox" value="1"> 暂时关闭网站</label>
                                                <span class="tis"></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="al-right"><span>关闭原因</span></td>
                                            <td><textarea class="form-control" name="OFF_SITE_WHY" placeholder="例如：网站正在升级请稍后...">{#$smarty.const.OFF_SITE_WHY#}</textarea></td>
                                        </tr>
                                        <tr>
                                            <td class="al-right"><span>关闭网站注册</span></td>
                                            <td>
                                                <label style="line-height:40px;"><input name="OFF_REG_IS"{#if $smarty.const.OFF_REG_IS#} checked{#/if#} type="checkbox" value="1"> 关闭网站注册，用户将无法注册会员。</label>
                                                <span class="tis"></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td>
                                                <input class="button button-primary button-rounded" type="submit" value="保存">&nbsp;
                                                <input type="hidden" name="dosubmit" value="1">
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <input type="hidden" name="_type" value="_offsite">
                                </form>
                            </div>
                            <div class="tabmenu" id="tabmenu-5">
                                <form action="{#get_url()#}" method="post" id="settingform">
                                    <table class="table-setting">
                                        <tbody>
                                        <tr>
                                            <td class="al-right"><span>关闭SQL防注入</span></td>
                                            <td>
                                                <label style="line-height:40px;"><input name="OFF_SQL_TEMP"{#if $smarty.const.OFF_SQL_TEMP#} checked{#/if#} type="checkbox" value="1"> 暂时关闭SQL防注入</label>
                                                <span class="tis"></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="al-right"> </td>
                                            <td style="color:#ff0000">* 需要的时候可以暂时关闭，不建议长期关闭SQL防注入功能！</td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td>
                                                <input class="button button-primary button-rounded" type="submit" value="保存">&nbsp;
                                                <input type="hidden" name="dosubmit" value="1">
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <input type="hidden" name="_type" value="_sqlset">
                                </form>
                            </div>
                            <div class="tabmenu" id="tabmenu-6">
                                <form action="{#get_url()#}" method="post" id="settingform">
                                    <table class="table-setting" style="width:908px">
                                        <tbody id="_other">
                                        <tr>
                                            <td class="al-right"><span>登录页二维码</span></td>
                                            <td>
                                                {#tpl_form_image("other[logincode]", value($setting['content']['other'],'logincode'))#}
                                                <span class="tis">建议尺寸：90x90</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="al-right"><span>登录页文字</span></td>
                                            <td>
                                                <textarea class="form-control" type="text" name="other[logintext]">{#value($setting['content']['other'],'logintext')#}</textarea>
                                                <span class="tis">登录页二维码傍边的文字</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="al-right"><span>登录页背景图</span></td>
                                            <td>
                                                {#tpl_form_image("other[loginbg]", value($setting['content']['other'],'loginbg'))#}
                                                <span class="tis">建议尺寸：1920x520</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="al-right"><span>注册页提示文字</span></td>
                                            <td>
                                                <textarea class="form-control" type="text" name="other[regtext]">{#value($setting['content']['other'],'regtext')#}</textarea>
                                                <span class="tis">注册页顶部提示文字</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="al-right"><span>注册页右下角图片</span></td>
                                            <td>
                                                {#tpl_form_image("other[regimg]", value($setting['content']['other'],'regimg'))#}
                                                <span class="tis">建议尺寸：350x350</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="al-right"><span>用户注册协议</span></td>
                                            <td><script id="otherregagreement" name="other[regagreement]" type="text/plain" style="width: 770px;height: 300px;">{#value($setting['content']['other'],'regagreement')#}</script></td>
                                        </tr>
                                        <tr>
                                            <td>
                                            <td style="padding-top: 30px;">
                                                <input class="button button-primary button-rounded" type="submit" value="保存">&nbsp;
                                                <input class="button button-primary button-rounded" type="reset" value="重置">
                                                <input type="hidden" name="dosubmit" value="1">
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <input type="hidden" name="_type" value="_other">
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
    function addtopmenu(obj) {
        var tthis = $(obj);
        $intemp = $('<tr>'+tthis.parents("tr").html()+'</tr>');
        $intemp.find("input").val("");
        $("#_topmenu").append($intemp);
    }
    function deltopmenu(obj) {
        var tthis = $(obj);
        if (tthis.parents("tbody").find("tr").length <= 1) {
            tthis.parents("tr").find("input").val("");
        }else{
            tthis.parents("tr").remove();
        }
    }
    $(document).ready(function() {
        var ue = UE.getEditor('otherregagreement',{autoHeightEnabled:false});

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
                        $.showModal('保存成功。', '{#$urlarr.now#}');
                    } else {
                        $.showModal('保存失败。');
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