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
        textarea.form-control{width:450px;height:100px;padding-top:5px}
        .section{min-height:447px;}
        .tis{color:#989898;padding-left:5px}
        .topmenu{position:relative;width:990px;height:36px;margin:15px auto 8px;padding-left:18px;background-color:#fff;border-bottom:3px solid #09c}
        .topmenu a{display:block;float:left;line-height:36px;padding:0 12px;margin-right:12px;color:#09c}
        .topmenu a.active,.topmenu a:hover{-webkit-border-radius:3px 3px 0 0;-moz-border-radius:3px 3px 0 0;border-radius:3px 3px 0 0;color:#fff;font-weight:700;background-color:#09c}
        .topmenu div{display:none;border:1px solid #09c;border-top:3px solid #09c;background-color:#fff;position:absolute;top:36px;left:0;z-index:9999}
        .topmenu div a{float:none;margin:0}
        .topmenu div a.active,.topmenu div a:hover{-webkit-border-radius:0;-moz-border-radius:0;border-radius:0}
        .tabmenu{display:none;min-height:308px;}
        select.form-control{width:465px;padding:8px 5px}
        thead th{background-color:#DDD;padding:10px}
        table.table-setting tbody tr td:first-child{vertical-align:top;padding-top:22px}
        #_topmenu input,#_topmenu select{width:238px;padding:8px 5px}
        #_regitem label{width:160px;display:block;float:left;line-height:70px;padding:5px}
        #_regitem label input{padding-right:5px}
        .templettab{display:none}
        .partition{background-color:#E6F1FB;line-height:32px;text-align:left;padding-left:10px;color:#09C}
        .sqllabel{width:550px;line-height:38px;}
        .sqllabel label{display:block;float:left;width:165px;}
        .sqllabel label input{margin-right:3px;margin-bottom:1px;vertical-align:middle}
    </style>
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
                                <a href="javascript:;" data-index="set">系统设置</a>
                                <a href="javascript:;" data-index="top">顶部菜单</a>
                                <a href="javascript:;" data-index="reg">会员注册项</a>
                                <a href="javascript:;" data-index="closed">关闭网站</a>
                                <a href="javascript:;" data-index="safe">安全设置</a>
                                <a href="javascript:;" data-index="tmp">SEO、首页模板</a>
                                <a href="javascript:;" data-index="other">其他设置</a>
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
                                                <input class="button button-primary button-rounded" type="submit" value="保存本页">
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
                                                <input class="button button-primary button-rounded" type="submit" value="保存本页">
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
                                                <input class="button button-primary button-rounded" type="submit" value="保存本页">
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
                                                <input class="button button-primary button-rounded" type="submit" value="保存本页">
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
                                            <td class="sqllabel">
                                                {#if $sqltemp > 0#}
                                                    {#$smarty.const.OFF_SQL_TEMP_TIS#} (剩余<span id="sqltemp">{#$sqlcountdown#}</span>)<br/>
                                                    <label><input name="OFF_SQL_TEMP" type="checkbox" value="-1">重新开启SQL防注入</label>
                                                {#else#}
                                                    <label><input name="OFF_SQL_TEMP" type="radio" value="1">暂停保护1分钟</label>
                                                    <label><input name="OFF_SQL_TEMP" type="radio" value="5">暂停保护5分钟</label>
                                                    <label><input name="OFF_SQL_TEMP" type="radio" value="10">暂停保护10分钟</label>
                                                    <label><input name="OFF_SQL_TEMP" type="radio" value="30">暂停保护30分钟</label>
                                                    <label><input name="OFF_SQL_TEMP" type="radio" value="60">暂停保护1小时</label>
                                                    <label><input name="OFF_SQL_TEMP" type="radio" value="180">暂停保护3小时</label>
                                                    <label><input name="OFF_SQL_TEMP" type="radio" value="360">暂停保护6小时</label>
                                                    <label><input name="OFF_SQL_TEMP" type="radio" value="720">暂停保护12小时</label>
                                                    <label><input name="OFF_SQL_TEMP" type="radio" value="1440">暂停保护24小时</label>
                                                {#/if#}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="al-right"> </td>
                                            <td style="color:#ff0000">* 需要的时候可以暂时关闭，不建议长期关闭SQL防注入功能！</td>
                                        </tr>
                                        <tr>
                                            <td class="al-right"><span>验证管理员密码</span></td>
                                            <td>
                                                <input class="form-control" type="password" name="OFF_SQL_PASS" placeholder="请输入管理员密码确认">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td>
                                                <input class="button button-primary button-rounded" type="submit" value="保存本页">
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
                                        <tbody>
                                        <tr><th colspan="2" class="partition">SEO相关</th></tr>
                                        <tr>
                                            <td class="al-right" style="width:120px;"><span>title</span></td>
                                            <td><input class="form-control" type="text" name="tem[title]" value="{#$tem['title']#}"></td>
                                        </tr>
                                        <tr>
                                            <td class="al-right"><span>keywords</span></td>
                                            <td><input class="form-control" type="text" name="tem[keywords]" value="{#$tem['keywords']#}"></td>
                                        </tr>
                                        <tr>
                                            <td class="al-right"><span>description</span></td>
                                            <td><input class="form-control" type="text" name="tem[description]" value="{#$tem['description']#}"></td>
                                        </tr>
                                        <tr><th colspan="2" class="partition">模板相关</th></tr>
                                        <tr>
                                            <td class="al-right"><span>选择模板</span></td>
                                            <td>
                                                <select class="form-control" name="tem[templet]" id="tem_templet" data-val="{#$tem['templet']#}">
                                                    <option value="1"{#if $tem['templet']==1#} selected{#/if#}>模板一</option>
                                                    <option value="2"{#if $tem['templet']==2#} selected{#/if#}>模板二</option>
                                                    <option value="3"{#if $tem['templet']==3#} selected{#/if#}>模板三</option>
                                                </select>
                                            </td>
                                        </tr>
                                        </tbody>

                                        <!-- 主题1 -->
                                        <tbody id="templet_1" class="templettab">
                                        <tr>
                                            <td class="al-right"><span>幻灯片背景</span></td>
                                            <td>
                                                {#tpl_form_imagemore("tem[t1][loginbg]", $tem['t1']['loginbg'], 5)#}
                                                <span class="tis">建议尺寸：1920x480 (留空使用默认背景)</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="al-right"><span>幻灯片上的大文字</span></td>
                                            <td>
                                                <input class="form-control" type="text" name="tem[t1][title]" value="{#$tem['t1']['title']#}" placeholder="例如：微窗服务全新上线">
                                                <span class="tis">留空不显示</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="al-right"><span>幻灯片上的小文字</span></td>
                                            <td>
                                                <input class="form-control" type="text" name="tem[t1][subtitle]" value="{#$tem['t1']['subtitle']#}" placeholder="例如：全方位微营销解决方案，一站式个性化服务平台！">
                                                <span class="tis">留空不显示</span>
                                            </td>
                                        </tr>
                                        </tbody>

                                        <!-- 主题2 -->
                                        <tbody id="templet_2" class="templettab">
                                        <tr>
                                            <td class="al-right"><span>幻灯片背景</span></td>
                                            <td>
                                                {#tpl_form_imagemore("tem[t2][loginbg]", $tem['t2']['loginbg'], 5)#}
                                                <span class="tis">建议尺寸：1920x980 (留空使用默认背景)</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="al-right"><span>幻灯片上的LOGO</span></td>
                                            <td>
                                                {#tpl_form_image("tem[t2][logo]", $tem['t2']['logo'])#}
                                                <span class="tis">建议尺寸：420x95 (留空使用默认logo)</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="al-right"><span>幻灯片上的文字</span></td>
                                            <td>
                                                <input class="form-control" type="text" name="tem[t2][title]" value="{#$tem['t2']['title']#}">
                                                <span class="tis">留空显示：再小的个体，也有自己的微窗</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="al-right"><span>底部-地图显示</span></td>
                                            <td>
                                                <input class="form-control" type="text" id="temt2map" name="tem[t2][map]">
                                                {#tpl_form_coordinate('$("#temt2map")', $tem['t2']['map'])#}
                                                <span class="tis">留空默认显示</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="al-right"><span>地图上的公司名称</span></td>
                                            <td>
                                                <input class="form-control" type="text" name="tem[t2][comp]" value="{#$tem['t2']['comp']#}">
                                                <span class="tis">留空显示：广西三顾网络科技有限公司</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="al-right"><span>地图上的联系电话</span></td>
                                            <td>
                                                <input class="form-control" type="text" name="tem[t2][tel]" value="{#$tem['t2']['tel']#}">
                                                <span class="tis">留空显示：0771-5671712</span>
                                            </td>
                                        </tr>
                                        </tbody>

                                        <!-- 主题3 -->
                                        <tbody id="templet_3" class="templettab">
                                        <tr>
                                            <td class="al-right"><span>幻灯片背景</span></td>
                                            <td>
                                                {#tpl_form_imagemore("tem[t3][loginbg]", $tem['t3']['loginbg'], 5)#}
                                                <span class="tis">建议尺寸：1920x520 (留空使用默认背景)</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="al-right"><span>幻灯片上的大文字</span></td>
                                            <td>
                                                <input class="form-control" type="text" name="tem[t3][title]" value="{#$tem['t3']['title']#}" placeholder="例如：量身定制 随需而变">
                                                <span class="tis">留空不显示</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="al-right"><span>幻灯片上的小文字</span></td>
                                            <td>
                                                <input class="form-control" type="text" name="tem[t3][subtitle]" value="{#$tem['t3']['subtitle']#}" placeholder="例如：优质产品 助您绘制移动互联时代蓝图">
                                                <span class="tis">留空不显示</span>
                                            </td>
                                        </tr>
                                        </tbody>
                                        <tbody>
                                        <tr>
                                            <td>
                                            <td style="padding-top: 30px;">
                                                <input class="button button-primary button-rounded" type="submit" value="保存本页">
                                                <input type="hidden" name="dosubmit" value="1">
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <input type="hidden" name="_type" value="_templet">
                                </form>
                            </div>
                            <div class="tabmenu" id="tabmenu-7">
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
                                            <td class="form-uetext">
                                                <script id="otherregagreement" name="other[regagreement]" type="text/plain" style="width: 770px;height: 300px;">{#value($setting['content']['other'],'regagreement')#}</script>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                            <td style="padding-top: 30px;">
                                                <input class="button button-primary button-rounded" type="submit" value="保存本页">
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
    function formatSeconds(value) {
        var theTime = parseInt(value);  // 秒
        var theTime1 = 0;               // 分
        var theTime2 = 0;               // 小时
        if(theTime > 60) {
            theTime1 = parseInt(theTime/60);
            theTime = parseInt(theTime%60);
            if(theTime1 > 60) {
                theTime2 = parseInt(theTime1/60);
                theTime1 = parseInt(theTime1%60);
            }
        }
        var result = (("0"+parseInt(theTime)).substr(-2))+"秒";
        if(theTime1 > 0) {
            result = (("0"+parseInt(theTime1)).substr(-2))+"分"+result;
        }
        if(theTime2 > 0) {
            result = (("0"+parseInt(theTime2)).substr(-2))+"时"+result;
        }
        return result;
    }
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
        var sqltemp = {#$sqltemp|intval#};
        var sqltimer = setInterval(function(){
            if (sqltemp > 0) {
                sqltemp--;
                $("#sqltemp").text(formatSeconds(sqltemp));
            }else{
                if ($("#sqltemp").length > 0) {
                    $.ajax({
                        url: window.location.href,
                        type: "GET",
                        dataType: "html",
                        success: function (data) {
                            $(".sqllabel").html($(data).find(".sqllabel").html());
                        },error : function () {
                            window.location.reload();
                        }
                    });
                }
                clearTimeout(sqltimer);
            }
        }, 1000);
        //
        UE.getEditor('otherregagreement',{autoHeightEnabled:false});
        //
        $("#tem_templet").change(function(){
            var tthis = $(this);
            $(".templettab").hide();
            $("#templet_" + tthis.val()).show();
        }).change();
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