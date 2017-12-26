<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>客户管理 - {#$BASE_NAME#}</title>
    <link rel="stylesheet" href="{#$CSS_PATH#}admin/normalize.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}admin/global.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}admin/detail.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}alert.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}font-awesome.css"/>
    <script type="text/javascript" src="{#$JS_PATH#}jquery-1.11.0.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.alert.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.form.min.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}linkage.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}datepicker/WdatePicker.js"></script>
    <style type="text/css">
        .row.users_al {margin:0;width:auto;border-bottom:1px dashed #cccccc;padding:8px 15px;}
        .row.users_al:hover {background-color: #f5f6d9;}
        .row.users_al:hover td {background-color:transparent !important}
        .row.users_al:first-child {margin-top:0;}
        .row.users_al:last-child {margin-bottom:0;border-bottom:0 dashed #cccccc;}
        table.table_al {width:100%}
        table.table_al td {padding:0;border:0 !important;}
        table.table_al td.row-l {padding-right:10px;}
        table.table_al td.row-r {width:30px;padding-left:10px;border-left: 1px solid #aeaeae !important;}
        .opense {max-width: 300px;padding:5px;margin-bottom:10px;}
        .openal {max-height:300px;max-width:388px;overflow:auto;}
        .open-la {display:block;float:left;height:20px;width:120px;overflow:hidden;margin-bottom:7px;white-space:nowrap; overflow:hidden; text-overflow:ellipsis;}
        .open-la .ola-l {display:block;float:left;margin-top:4px;margin-right:3px;}
        .open-la .ola-r {display:block;float:left;}
        .eduser{cursor:pointer;}
        .eduser:hover div{text-decoration:underline;}
    </style>
</head>
<body>

{#include file="header_admin.tpl"#}
<script>$('#head-nav-menu>li:eq(0)').addClass('active');</script>


<div class="breadcrumb-wrapper">
    <div class="breadcrumb">
        <div class="items">
            <span class="description">当前位置：</span>
            <span class="current">客户管理</span>
        </div>
    </div>
</div>

  
<div class="main-wrapper">
    <div class="main">
        <div class="main-content">
            <div class="module">
                <div class="hd">
                    <div class="control-group cf">
                        <div class="control-group-left">
                            <a class="button button-primary{#if value($smarty.get,'t')==''#} button-hover{#/if#}"
                               href="{#$urlarr.now#}">客户列表</a>
                            <a class="button button-primary{#if value($smarty.get,'t')=='month'#} button-hover{#/if#}"
                               href="{#$urlarr.now#}?t=month">本月加入</a>
                            <a class="button button-primary{#if value($smarty.get,'t')=='high'#} button-hover{#/if#}"
                               href="javascript:void(0);" id="win" d="s">高级搜索</a>
                            <a class="button button-primary" href="javascript:void(0);" id="win" d="n">新增客户</a>
                        </div>
                    </div>
                </div>
                <div class="bd">
                    <div class="table-wrapper">
                        <table class="table table-primary">
                            <thead>
                            <tr>
                                <th>序号</th>
                                <th>加入时间</th>
                                <th class="align-left">公司名称</th>
                                <th class="align-left">手机/电话</th>
                                <th>联系人</th>
                                <th class="align-left">接入服务项|拥有功能</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            {#ddb_pc set="数据表:users,列表名:lists,显示数目:10,分页显示:1,分页名:pagelist,当前页:{#$page#},分页地址:{#$pageurl#}(?)/{#get_get()#},排序:{#$orderby#}" where="{#$wheresql#} 1"#}
                            {#foreach from=$lists item=list#}
                                <tr data-userid="{#$list.userid#}">
                                    <td class="align-center"><span>{#$list._n#}</span></td>
                                    <td class="align-center"><span>{#$list.indate|date_format:"%Y-%m-%d"#}</span></td>
                                    <td class="align-left"><a id="win" d="com" href="javascript:void(0);">{#if $list.companyname#}{#$list.companyname#}{#else#}<span title="用户名：{#$list.username#}">[{#$list.username#}]</span>{#/if#}</a></td>
                                    <td class="align-left"><span>{#$list.phone#} / {#$list.tel#}</span></td>
                                    <td class="align-center"><span>{#$list.fullname#}</span></td>
                                    <td class="align-left" style="padding:0">
                                        {#ddb_pc set="数据表:users_al,列表名:lists2,显示数目:100,排序:indate desc" where=" `userid`={#$list.userid#} "#}
                                        {#foreach from=$lists2 item=list2#}
                                            {#$funarr = string2array($list2.function)#}
                                            <div class="row users_al" data-id="{#$list2.id#}">
                                                <table class="table_al">
                                                    <tr>
                                                        <td class="row-l eduser" title="点击修改所属管理账号" onclick="_eduser('{#$list2.id#}')">
                                                            {#if $list2.wx_name#}
                                                                <div>公众号:{#$list2.wx_name#}</div>
                                                            {#/if#}
                                                            {#if $list2.al_name#}
                                                                <div>服务窗:{#$list2.al_name#}</div>
                                                            {#/if#}
                                                            {#if !$list2.wx_name && !$list2.al_name#}
                                                                <div>未启用</div>
                                                            {#/if#}
                                                        </td>
                                                        <td class="row-r">
                                                            <a id="win" d="wxfun" href="javascript:void(0);">{#count($funarr)#}</a>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                            {#foreachelse#}
                                            <div class="row users_al">无</div>
                                        {#/foreach#}
                                    </td>
                                    <td class="align-center">
                                        <div class="row">
                                            <a id="win" d="addf" href="javascript:void(0);">开通功能</a> &nbsp;
                                            <a id="win" d="delf" href="javascript:void(0);">删除公司</a>
                                        </div>
                                    </td>
                                </tr>
                                {#foreachelse#}
                                <tr>
                                    <td colspan="7" align="center" class="align-center">无</td>
                                </tr>
                            {#/foreach#}
                            </tbody>
                        </table>
                    </div>
                    <div id="pagelist">
                        {#$pagelist#}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div id="search" style="display: none;">
    <table class="wineditform" border="0" cellspacing="0" cellpadding="0">
        <tbody>
        <tr>
            <td class="al-right">公司名称</td>
            <td><input class="form-control" d-id="s_companyname" value="{#value($smarty.get,'companyname')#}"></td>
        </tr>
        <tr>
            <td class="al-right">用户名</td>
            <td><input class="form-control" d-id="s_username" value="{#value($smarty.get,'username')#}"></td>
        </tr>
        <tr>
            <td class="al-right">手机</td>
            <td><input class="form-control" d-id="s_phone" value="{#value($smarty.get,'phone')#}"></td>
        </tr>
        <tr>
            <td class="al-right">电话</td>
            <td><input class="form-control" d-id="s_tel" value="{#value($smarty.get,'tel')#}"></td>
        </tr>
        <tr>
            <td class="al-right">联系人</td>
            <td><input class="form-control" d-id="s_fullname" value="{#value($smarty.get,'fullname')#}"></td>
        </tr>
        </tbody>
    </table>
</div>

<script type="text/javascript">
    function _eduser(id) {
        $.alert("加载中...", 0);
        $.ajax({
            url: "{#$urlarr.3#}eduser/" + id + "/",
            type: "get",
            dataType: "html",
            success: function (datahtml) {
                $.alert(0);
                var _edu = art.dialog({
                    fixed: true,
                    lock: true,
                    title: '修改所属管理账号',
                    content: datahtml,
                    opacity: '.3',
                    button: [{
                        name: '确定修改',
                        focus: true,
                        callback: function () {
                            $.alert("正在修改...", 0, 1);
                            $.ajax({
                                url: '{#$urlarr.3#}eduser/' + id + '/?eduseruserid=' + $("#eduseruserid").val(),
                                type: "get",
                                dataType: "json",
                                success: function (data) {
                                    $.alert(data.message);
                                    if (data != null && data.success != null && data.success) {
                                        _edu.close();
                                        setTimeout(function(){window.location.reload();}, 100);
                                    }
                                },error : function () {
                                    $.alert("修改失败！");
                                    _edu.close();
                                },
                                cache: false
                            });
                            return false;
                        }
                    },{
                        name: '取消',
                        callback: function () {
                            return true;
                        }
                    }]
                });
            },error : function () {
                $.alert("加载失败！");
            },
            cache: false
        });
    }
    function deluserfun(_id){
        var _del = art.dialog({
            fixed: true,
            lock: true,
            icon: 'warning',
            content: '确定要删除此功能吗？',
            opacity: '.3',
            button: [{
                name: '确定删除',
                focus: true,
                callback: function () {
                    $.ajax({
                        url: '{#$urlarr.3#}delfun/' + _id + "/",
                        dataType: "json",
                        success: function (data) {
                            $.alert(data.message);
                            if (data != null && data.success != null && data.success) {
                                _del.close();
                                window.location.href = '{#$urlarr.now#}';
                            }
                        },error : function () {
                            $.alert("删除失败！");
                            _del.close();
                        },
                        cache: false
                    });
                    return false;
                }
            }]
        });
    }
    function _opense(obj) {
        var alid = $(obj).val();
        var thiss = $(".openal");
        thiss.find("input").prop("checked", false).prop("disabled", false).attr("title", "");
        if (alid) {
            var tjson = JSON.parse($("#funcplain_"+alid).html());
            if (typeof(tjson) == 'object') {
                $.each(tjson, function(idx,item) {
                    if (item.id > 0) {
                        thiss.find("input[value='"+item.id+"']").prop("checked", true).prop("disabled", true).attr("title", "已开通过此功能");
                    }
                });
            }
        }
    }
    $(document).ready(function() {
        $('a#win').click(function() {
            var _dialog = art.dialog({
                fixed: true,
                lock: true,
                content: '正在加载...',
                opacity: '.3'

            });
            var d = $(this).attr('d');
            if (d == 's') {
                var _htext = $("#search").html();
                _htext = _htext.replace(/d-id/g, "id");
                _dialog.title('高级搜索');
                _dialog.content(_htext);
                _dialog.button({
                    name: '搜索',
                    focus: true,
                    callback: function () {
                        _dialog.hide();
                        $.alert("正在搜索...");
                        var _url = "&companyname=" + encodeURIComponent($("#s_companyname").val());
                        _url += "&username=" + encodeURIComponent($("#s_username").val());
                        _url += "&phone=" + encodeURIComponent($("#s_phone").val());
                        _url += "&tel=" + encodeURIComponent($("#s_tel").val());
                        _url += "&fullname=" + encodeURIComponent($("#s_fullname").val());
                        window.location.href = "{#$urlarr.now#}?t=high" + _url;
                        return false;
                    }
                }, {
                    name: '重置',
                    callback: function () {
                        window.location.href = "{#$urlarr.now#}";
                        return false;
                    }
                });
            }else if (d == 'n'){
                _dialog.title('新增客户');
                $.ajax({
                    url: "{#$urlarr.3#}infoadd/",
                    type: "get",
                    dataType: "html",
                    success: function (data) {
                        _dialog.content(data);
                        linkage("comeditform #linkaddr","{#$urlarr.index#}web/system/linkage/",0,0);
                        $('#com-edit-form').submit(function() {
                            $.alert('正在保存...', 0);
                            $(this).ajaxSubmit({
                                dataType : 'json',
                                success : function (data) {
                                    $.alert(0);
                                    if (data != null && data.success != null && data.success) {
                                        _dialog.close();
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
                        _dialog.button({
                            name: '注册',
                            focus: true,
                            callback: function () {
                                $('#com-edit-form').submit();
                                return false;
                            }
                        },{
                            name: '取消',
                            callback: function () {
                                return true;
                            }
                        });
                    },
                    cache: false
                });
            }else if (d == 'com'){
                _dialog.title('公司详情');
                $.ajax({
                    url: "{#$urlarr.3#}info/" + $(this).parent().parent().attr("data-userid") + "/",
                    type: "get",
                    dataType: "html",
                    success: function (data) {
                        _dialog.content(data);
                        linkage("comeditform #linkaddr","{#$urlarr.index#}web/system/linkage/",0,0);
                        $('#com-edit-form').submit(function() {
                            $.alert('正在保存...', 0);
                            $(this).ajaxSubmit({
                                dataType : 'json',
                                success : function (data) {
                                    $.alert(0);
                                    if (data != null && data.success != null && data.success) {
                                        _dialog.close();
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
                        _dialog.button({
                            name: '修改',
                            focus: true,
                            callback: function () {
                                $('#com-edit-form').submit();
                                return false;
                            }
                        },{
                            name: '取消',
                            callback: function () {
                                return true;
                            }
                        });
                    },
                    cache: false
                });
            }else if (d == 'wxfun'){
                _dialog.title('功能管理');
                $.ajax({
                    url: "{#$urlarr.3#}fun/" + $(this).parents(".users_al").attr("data-id") + "/",
                    type: "get",
                    dataType: "html",
                    success: function (data) {
                        _dialog.content(data);
                        $('#fun-edit-form').submit(function() {
                            $.alert('正在保存...', 0);
                            $(this).ajaxSubmit({
                                dataType : 'json',
                                success : function (data) {
                                    $.alert(0);
                                    if (data != null && data.success != null && data.success) {
                                        _dialog.close();
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
                        _dialog.button({
                            name: '关闭',
                            callback: function () {
                                return true;
                            }
                        });
                    },
                    cache: false
                });
            }else if (d == 'addf'){
                var openalid = $(this).attr('data-openalid');
                $(this).attr('data-openalid','');
                _dialog.title('开通功能');
                $.ajax({
                    url: "{#$urlarr.3#}openfun/" + $(this).parent().parent().parent().attr("data-userid") + "/?openalid=" + openalid,
                    type: "get",
                    dataType: "html",
                    success: function (data) {
                        _dialog.content(data);
                        if ($("select#alid").find("option").length == 2) {
                            $("select#alid").find("option:eq(1)").attr("selected",true).change();
                        }
                        $('#openfun-edit-form').submit(function() {
                            $.alert('正在保存...', 0);
                            $(this).ajaxSubmit({
                                dataType : 'json',
                                success : function (data) {
                                    $.alert(0);
                                    if (data != null && data.success != null && data.success) {
                                        _dialog.close();
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
                        _dialog.button({
                            name: '保存',
                            focus: true,
                            callback: function () {
                                $('#openfun-edit-form').submit();
                                return false;
                            }
                        },{
                            name: '取消',
                            callback: function () {
                                return true;
                            }
                        });
                    },
                    cache: false
                });
            }else if (d == 'delf'){
                var _userid = $(this).parent().parent().parent().attr("data-userid");
                _dialog.title('删除公司');
                _dialog.content('确定要删除该公司及该公司的所有信息并且不可恢复吗？');
                _dialog.button({
                    focus: true,
                    name: '确定删除',
                    callback: function () {
                        $.ajax({
                            url: "{#$urlarr.3#}delcompany/" + _userid + "/",
                            success: function (data) {
                                window.location.reload(true);
                            },
                            cache: false
                        });
                        return false;
                    }
                },{
                    name: '取消',
                    callback: function () {
                        return true;
                    }
                });
            }
        });
        {#if $_GPC['openfunc']#}
        var openfunc = $('tr[data-userid="{#$_GPC['openfunc']#}"]');
            openfunc.find("a[d='addf']").attr("data-openalid", "{#intval($_GPC['openalid'])#}").click();
            openfunc.find("td").css("background-color", "#FFF2F2");
        {#/if#}
    });
</script>
{#include file="footer_admin.tpl"#}

</body>
</html>