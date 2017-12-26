<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>公告管理 - {#$BASE_NAME#}</title>
    <link rel="stylesheet" href="{#$CSS_PATH#}admin/normalize.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}admin/global.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}admin/detail.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}font-awesome.css"/>
    <style type="text/css">
        .form-information { border: 1px solid #ffffff; }
        .table-information tbody tr td { border-left: 1px solid #ffffff; border-top: 1px solid #ffffff; padding: 5px 10px; }
    </style>
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
            <span class="current">公告管理</span>
        </div>
    </div>
</div>

<div class="main-wrapper">
    <div class="main">
        <div class="row cf">
            <div class="col" style="margin-left:23px;">
                {#include file="setting_left.tpl"#}
                <script>$('#setting-nav-menu li#nav-information a').css('color','#ff6600');</script>
            </div>
            <div class="col" style="width: 1050px;">
                <div class="main-content">
                    <div class="module">
                        <div class="section section-minh">
                            <div class="control-group cf">
                                <div class="control-group-left">
                                    <a style="padding:0 10px;margin-top: 3px" id="win" d="add" class="button button-primary">新增公告</a>
                                </div>
                                <div class="control-group-right">
                                    <form class="form-inline" action="{#$urlarr.3#}" id="form-select" method="get">
                                        <div class="row">
                                            <div style="width: 200px;" class="col">
                                                <div class="form-control">
                                                    <input type="text" id="title" name="title" value="{#value($smarty.get,'title')#}"/>
                                                </div>
                                            </div>
                                            <div style="padding-top: 3px;" class="col"><input type="submit" class="button button-primary" value="搜索"></div>
                                            <div style="padding-top: 3px;" class="col"><input id="emptyselect" type="button" class="button button-primary" value="重置"></div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="table-wrapper">
                                <table class="table table-primary">
                                    <thead>
                                    <tr>
                                        <th>序号/排序</th>
                                        <th>标题</th>
                                        <th>摘要</th>
                                        <th>添加时间</th>
                                        <th class="align-left">IP</th>
                                        <th>操作</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    {#$wheresql = ''#}
                                    {#if (value($smarty.get,'title'))#}
                                        {#$wheresql = $wheresql|cat:"`title` like '%"|cat:value($smarty.get,'title')|cat:"%' AND "#}
                                    {#/if#}
                                    {#ddb_pc set="数据表:information,列表名:lists,显示数目:10,分页显示:1,分页名:pagelist,当前页:{#$page#},分页地址:{#$pageurl#}(?)/{#get_get()#},排序:inorder desc>indate desc" where="{#$wheresql#} 1"#}
                                    {#foreach from=$lists item=list#}
                                        <tr data-id="{#$list.id#}">
                                            <td class="align-center">{#$list._n#}/{#$list.inorder#}</td>
                                            <td class="align-center">{#$list.title#} (阅:{#$list.view#})</td>
                                            <td class="align-center">{#$list.content|get_html:"20"#}</td>
                                            <td class="align-center">{#$list.indate|date_format:"%Y-%m-%d %H:%M:%S"#}</td>
                                            <td class="align-left">{#$list.inip#}</td>
                                            <td class="align-center">
                                                <a href="javascript:;" id="win" d="edit">修改</a>
                                                <a href="javascript:;" id="win" d="del">删除</a>
                                            </td>
                                        </tr>
                                        {#foreachelse#}
                                        <tr>
                                            <td class="align-center" colspan="6">无</td>
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
    </div>
</div>


<script type="text/javascript">
    var UE, UEE=null;
    $(document).ready(function() {
        $('#emptyselect').click(function() {
            $("#form-select input[type='text']").val("");
            $("#form-select select").val("");
        });
        $('#form-select').submit(function() {
            $.alert('正在提交...', 0);
            return true;
        });
        $('a#win').click(function() {
            var _dialog = art.dialog({
                fixed: true,
                lock: true,
                zIndex: 999,
                content: '正在加载...',
                opacity: '.3'

            });
            var d = $(this).attr('d');
            if (d == 'edit'){
                _dialog.title('修改公告');
                var _id = $(this).parent().parent().attr("data-id");
                $.ajax({
                    url: "{#$urlarr.3#}informationadd/" + _id + "/",
                    type: "get",
                    dataType: "html",
                    success: function (data) {
                        _dialog.hide();
                        _dialog.content(data);
                        if (UEE != null) UE.getEditor('content').destroy();
                        UE.getEditor('content',{autoHeightEnabled:false}).ready(function() {_dialog.show()._reset()});
                        UEE = 1;
                        $('#information-edit-form').submit(function() {
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
                                $('#information-edit-form').submit();
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
            }else if (d == 'add'){
                _dialog.title('新增公告');
                $.ajax({
                    url: "{#$urlarr.3#}informationadd/",
                    type: "get",
                    dataType: "html",
                    success: function (data) {
                        _dialog.hide();
                        _dialog.content(data);
                        if (UEE != null) UE.getEditor('content').destroy();
                        UE.getEditor('content',{autoHeightEnabled:false}).ready(function() {_dialog.show()._reset()});
                        UEE = 1;
                        $('#information-edit-form').submit(function() {
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
                                $('#information-edit-form').submit();
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
            }else if (d == 'del'){
                var _funid = $(this).parent().parent().attr("data-id");
                _dialog.content('确定要删除此公告并且不可恢复吗？');
                _dialog.button({
                    name: '确定',
                    focus: true,
                    callback: function () {
                        $.ajax({
                            url: "{#$urlarr.3#}informationdel/" + _funid + "/",
                            dataType : 'json',
                            success: function (data) {
                                if (data != null && data.success != null && data.success) {
                                    _dialog.close();
                                    $.showModal(data.message, '{#$urlarr.now#}');
                                } else {
                                    $.showModal(data.message);
                                }
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
    });
</script>
{#include file="footer_admin.tpl"#}

</body>
</html>