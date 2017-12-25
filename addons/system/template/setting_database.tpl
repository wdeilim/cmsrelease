<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>数据库备份 - {#$BASE_NAME#}</title>
    <link rel="stylesheet" href="{#$CSS_PATH#}admin/normalize.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}admin/global.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}admin/detail.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}font-awesome.css"/>
    <script type="text/javascript" src="{#$JS_PATH#}jquery-1.11.0.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.alert.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.form.min.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}placeholder.js"></script>
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
        a.normal-link {color: #00a7ff;padding-right: 10px;}
        .dateitem label {width: 44%;display: block;float: left;line-height:26px;padding: 0 3%;margin: 0;}
        .dateitem label:hover {color:#CA4E4E;cursor: pointer;}
        .dateitem label.head {background-color: #E0F4FF;line-height:40px;margin-bottom: 6px;}
        .dateitem label span {display: block;float: left;width: 25%;white-space:nowrap; overflow:hidden; text-overflow:ellipsis;}
        .dateitem label.head span {color: #88B1E7;}
        .dateitem label span:nth-child(1) {width: 40%;}
        .dateitem label span:nth-child(2) {width: 20%;}
        .dateitem label span:nth-child(3) {width: 20%;}
        .dateitem label span:nth-child(4) {width: 20%;}
        .dateitem label input {margin-right:5px;vertical-align:middle;}
        .optimizeitem label span:nth-child(1) {width: 35%;}
        .optimizeitem label span:nth-child(2) {width: 18%;}
        .optimizeitem label span:nth-child(3) {width: 18%;}
        .optimizeitem label span:nth-child(4) {width: 29%;}
        .importhead label {width: 94%;display: block;line-height:26px;padding: 0 3%;margin: 0;}
        .importhead label:hover {color:#CA4E4E;cursor: pointer;}
        .importhead label.head {background-color: #E0F4FF;line-height:40px;margin-bottom: 6px;height: 40px;}
        .importhead label span {display: block;float: left;width: 20%;white-space:nowrap; overflow:hidden; text-overflow:ellipsis;}
        .importhead label span a {color: #3CADE2;}
        .importhead label span a:hover{color:#f60}
        .importhead label.head span {color: #88B1E7;}
        .importhead label span:nth-child(1) {width: 23%;}
        .importhead label span:nth-child(2) {width: 23%;}
        .importhead label span:nth-child(3) {width: 23%;}
        .importhead label span:nth-child(4) {width: 23%;}
        .importhead label span:nth-child(5) {width: 8%;}
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
                <script>$('#setting-nav-menu li#nav-database a').css('color','#ff6600');</script>
            </div>
            <div class="col" style="width: 1050px;">
                <div class="main-content">
                    <div class="module">
                        <div class="section">

                            <div class="topmenu" id="topmenu">
                                <a href="javascript:;">备份</a>
                                <a href="javascript:;">还原</a>
                                <a href="javascript:;">优化</a>
                                <a href="javascript:;">修复</a>
                            </div>

                            <div class="tabmenu" id="tabmenu-1">
                                <form action="{#get_url()#}" method="post" id="_exportform">
                                    <table class="table-setting dateitem" style="width:968px;margin:20px 20px;">
                                        <tbody>
                                        <tr>
                                            <td class="al-right" width="80"><span>分卷大小</span></td>
                                            <td>
                                                <input class="form-control" type="text" name="sizelimit" value="2048" />
                                                <span class="tis">每个分卷文件大小（单位：KB）</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">
                                                <label class="head">
                                                    <span>数据表</span>
                                                    <span>类型</span>
                                                    <span>记录数</span>
                                                    <span>数据大小</span>
                                                </label>
                                                <label class="head">
                                                    <span>数据表</span>
                                                    <span>类型</span>
                                                    <span>记录数</span>
                                                    <span>数据大小</span>
                                                </label>
                                                <div id="datelabel"></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding-top:0;" colspan="2">
                                                <a href="javascript:void(0)" class="normal-link" onclick="_checked(1, this);">全选</a>
                                                <a href="javascript:void(0)" class="normal-link" onclick="_checked(2, this);">全不选</a>
                                                <a href="javascript:void(0)" class="normal-link" onclick="_checked(3, this);">反选</a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding-top:10px;" colspan="2">
                                                <input class="button button-primary button-rounded" type="submit" value="提交备份">&nbsp;
                                                <span class="tis">备份保存路径:/caches/bakup/</span>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <input type="hidden" name="do" value="export">
                                </form>
                            </div>

                            <div class="tabmenu" id="tabmenu-2">
                                <table class="table-setting importhead" style="width:968px;margin:20px 20px;">
                                    <tbody>
                                    <tr>
                                        <td>
                                            <label class="head">
                                                <span>备份名称</span>
                                                <span>卷数</span>
                                                <span>备份大小</span>
                                                <span>备份时间</span>
                                                <span>操作</span>
                                            </label>
                                            <div id="dateimport"></div>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="tabmenu" id="tabmenu-3">
                                <form action="{#get_url()#}" method="post" id="settingform">
                                    <table class="table-setting dateitem optimizeitem" style="width:968px;margin:20px 20px;">
                                        <tbody>
                                        <tr>
                                            <td>
                                                <label class="head">
                                                    <span>数据表</span>
                                                    <span>类型</span>
                                                    <span>记录数</span>
                                                    <span>碎片/数据</span>
                                                </label>
                                                <label class="head">
                                                    <span>数据表</span>
                                                    <span>类型</span>
                                                    <span>记录数</span>
                                                    <span>碎片/数据</span>
                                                </label>
                                                <div id="dateoptimize"></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding-top:0;">
                                                <a href="javascript:void(0)" class="normal-link" onclick="_checked(1, this);">全选</a>
                                                <a href="javascript:void(0)" class="normal-link" onclick="_checked(2, this);">全不选</a>
                                                <a href="javascript:void(0)" class="normal-link" onclick="_checked(3, this);">反选</a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding-top:10px;" colspan="2">
                                                <input class="button button-primary button-rounded" type="submit" value="执行优化">&nbsp;
                                                <span class="tis">提示:数据表优化可以去除数据文件中的碎片，使记录排列紧密，提高读写速度。 </span>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <input type="hidden" name="do" value="optimize">
                                </form>
                            </div>
                            <div class="tabmenu" id="tabmenu-4">
                                <form action="{#get_url()#}" method="post" id="settingform">
                                    <table class="table-setting dateitem" style="width:968px;margin:20px 20px;">
                                        <tbody>
                                        <tr>
                                            <td>
                                                <label class="head">
                                                    <span>数据表</span>
                                                    <span>类型</span>
                                                    <span>记录数</span>
                                                    <span>数据大小</span>
                                                </label>
                                                <label class="head">
                                                    <span>数据表</span>
                                                    <span>类型</span>
                                                    <span>记录数</span>
                                                    <span>数据大小</span>
                                                </label>
                                                <div id="daterepair"></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding-top:0;">
                                                <a href="javascript:void(0)" class="normal-link" onclick="_checked(1, this);">全选</a>
                                                <a href="javascript:void(0)" class="normal-link" onclick="_checked(2, this);">全不选</a>
                                                <a href="javascript:void(0)" class="normal-link" onclick="_checked(3, this);">反选</a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding-top:10px;" colspan="2">
                                                <input class="button button-primary button-rounded" type="submit" value="执行修复">&nbsp;
                                                <span class="tis">提示:数据表修复可以去除提示数据库错误等类提示。</span>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <input type="hidden" name="do" value="repair">
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
    function _checked(t, obj) {
        var tthis = $(obj);
        if (t == 1) {
            tthis.parents("table").find("input[type='checkbox']").prop("checked", true);
        }else if (t == 2) {
            tthis.parents("table").find("input[type='checkbox']").prop("checked", false);
        }else if (t == 3) {
            tthis.parents("table").find("input[type='checkbox']").each(function () {
                if ($(this).is(":checked")){
                    $(this).prop("checked", false);
                }else{
                    $(this).prop("checked", true);
                }
            });
        }
    }
    function _exportform(nexturl) {
        $.ajax({
            type: "GET",
            url: nexturl,
            dataType: "json",
            success: function (data) {
                if (data != null && data.success != null && data.success) {
                    if (data.nexturl) {
                        $.alert(data.message, 0, 1);
                        _exportform(data.nexturl);
                    }else{
                        $.alert(0);
                        $.showModal(data.message, function(){
                            _inidatelist();
                        }, '', 1);
                    }
                } else {
                    $.alert(0);
                    $.showModal('备份失败！','','',1);
                }
            },
            error: function () {
                $.alert(0);
                $.showModal('备份失败，请稍后再试！','','',1);
            }
        });
    }
    function _importform(nexturl, name) {
        $.ajax({
            type: "POST",
            url: nexturl,
            data: {name:name},
            dataType: "json",
            success: function (data) {
                if (data != null && data.success != null && data.success) {
                    if (data.nexturl) {
                        $.alert(data.message, 0, 1);
                        _importform(data.nexturl, name);
                    }else{
                        $.alert(0);
                        $.showModal(data.message, function(){
                            window.location.reload();
                        }, '', 1);
                    }
                } else {
                    $.alert(0);
                    $.showModal('恢复失败！','','',1);
                }
            },
            error: function () {
                $.alert(0);
                $.showModal('恢复失败，请稍后再试！','','',1);
            }
        });
    }
    function _import(name, del, release, notrel) {
        if (del) {
            if (confirm("确定删除备份包【"+name+"】吗？")) {
                $.alert('正在删除...', 0);
                $.ajax({
                    type: "POST",
                    url: "{#get_link("do")#}&do=importdel",
                    data: {name:name},
                    dataType: "json",
                    success: function (data) {
                        if (data != null && data.success != null && data.success) {
                            $.alert(0);
                            $.showModal(data.message, function(){
                                _inidatelist();
                            }, '', 1);
                        } else {
                            $.alert(data.message);
                        }
                    },
                    error: function () {
                        $.alert("删除失败，请稍后再试！");
                    }
                });
            }
        }else{
            if (confirm("确定恢复备份包【"+name+"】吗？\r\n\r\n注意*：还原后登录帐号密码将恢复到备份时的数据！")) {
                if (notrel == "1") {
                    art.dialog({
                        title: '<font color="red">特别提醒</font>',
                        lock: true,
                        opacity: '.3',
                        content: "你要恢复的备份包【"+name+"】<br/>" +
                        "<span style='color:red'>备份时与当前程序版本不一致，恢复后可能会导致数据跟程序版本不兼容，确定要恢复吗？</span><br/><br/>" +
                        "备份时程序版本：<span style='color:red'>"+release+"</span><br/>当前的程序版本：{#$smarty.const.ES_RELEASE#}",
                        button: [{
                            name: '确定恢复',
                            focus: true,
                            callback: function () {
                                $.alert('正在恢复...', 0, 1);
                                _importform("{#get_link("do")#}&do=import", name);
                                return true;
                            }
                        },{
                            name: '关闭',
                            callback: function () {
                                return true;
                            }
                        }]
                    });
                }else{
                    $.alert('正在恢复...', 0, 1);
                    _importform("{#get_link("do")#}&do=import", name);
                }
            }
        }
    }
    function _inidatelist() {
        //
        $.alert("数据加载中...",0,1);
        $.ajax({
            type: "GET",
            url: "{#get_link('do')#}&do=datalist",
            dataType: "json",
            success: function (data) {
                $.alert(0);
                $("#datelabel").html("");
                $("#dateimport").html("");
                $("#dateoptimize").html("");
                $("#daterepair").html("");
                if (data.success == "1"){
                    $.each(data.list, function(i, item) {
                        $("#datelabel").append(
                                "<label>" +
                                "<span>" +
                                "<input checked name=\"export["+item.Name+"]\" type=\"checkbox\" value=\""+item.Name+"\">" + item.Name +
                                "</span>" +
                                "<span>" + item.Engine + "</span>" +
                                "<span>" + item.Rows + "</span>" +
                                "<span>" + item.Data_length + "</span>" +
                                "</label>");
                    });
                    $.each(data.import, function(i, item) {
                        $("#dateimport").append(
                                "<label>" +
                                "<span>" + item.pre + "</span>" +
                                "<span>" + item.number + "</span>" +
                                "<span>" + item.filesize + "</span>" +
                                "<span>" + item.maketime + "</span>" +
                                "<span><a href='javascript:void(0);' onclick='_import(\""+item.pre+"\",0,\""+item.release+"\",\""+item.notrel+"\")'>恢复</a> | " +
                                "<a href='javascript:void(0);' onclick='_import(\""+item.pre+"\",1)'>删除</a></span>" +
                                "</label>");
                    });
                    if (!data.import.length) {
                        $("#dateimport").html("<label>*没有任何备份，请先备份。 </label>");
                    }
                    $.each(data.optimize, function(i, item) {
                        $("#dateoptimize").append(
                                "<label>" +
                                "<span>" +
                                "<input checked name=\"optimize["+item.Name+"]\" type=\"checkbox\" value=\""+item.Name+"\">" + item.Name +
                                "</span>" +
                                "<span>" + item.Engine + "</span>" +
                                "<span>" + item.Rows + "</span>" +
                                "<span>" + item.Data_free + "/" + item.Data_length + "</span>" +
                                "</label>");
                    });
                    if (!data.optimize.length) {
                        $("#dateoptimize").html("<label>*数据表没有碎片，不需要再优化。 </label>");
                    }
                    $.each(data.list, function(i, item) {
                        $("#daterepair").append(
                                "<label>" +
                                "<span>" +
                                "<input checked name=\"repair["+item.Name+"]\" type=\"checkbox\" value=\""+item.Name+"\">" + item.Name +
                                "</span>" +
                                "<span>" + item.Engine + "</span>" +
                                "<span>" + item.Rows + "</span>" +
                                "<span>" + item.Data_length + "</span>" +
                                "</label>");
                    });
                }else{
                    $.alert("数据加载失败！");
                }
            },
            error: function (msg) {
                alert("数据加载失败！");
                window.history.go(-1);
            }
        });
    }
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
        _inidatelist();
        //备份
        $('form#_exportform').submit(function() {
            $.alert('正在提交...', 0, 1);
            $(this).ajaxSubmit({
                dataType : 'json',
                success : function (data) {
                    if (data != null && data.success != null && data.success) {
                        if (data.nexturl) {
                            $.alert(data.message, 0, 1);
                            _exportform(data.nexturl);
                        }else{
                            $.alert(0);
                            $.showModal(data.message, function(){
                                _inidatelist();
                            }, '', 1);
                        }
                    } else {
                        $.alert('备份失败！');
                    }
                },
                error : function () {
                    $.alert("备份失败，请稍后再试！");
                }
            });
            return false;
        });
        $('form#settingform').submit(function() {
            $.alert('正在提交...', 0, 1);
            $(this).ajaxSubmit({
                dataType : 'json',
                success : function (data) {
                    $.alert(0);
                    if (data != null && data.success != null && data.success) {
                        $.showModal(data.message, function(){
                            _inidatelist();
                        }, '', 1);
                    } else {
                        $.showModal(data.message,'','',1);
                    }
                },
                error : function () {
                    $.alert(0);
                    $.inModal("提交错误！<br/>命令已发送，可能是数据过大超时而已！",'','',1);
                }
            });
            return false;
        });
    });
</script>
{#include file="footer_admin.tpl"#}

</body>
</html>