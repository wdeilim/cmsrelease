
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
    <script type="text/javascript" src="{#$JS_PATH#}artdialog/artDialog.js?skin=default"></script>
</head>
<body>
{#template("header")#}


<div class="wrapper">

    {#include file="menu.tpl" _item=5 _itemp=0#}


    <div class="main cf custom-menu">
        <div class="mod">

            <div class="main-bd">

                <a class="button" style="margin-bottom:15px" href="{#weburl('vip/couponrelease')#}">发布优惠券</a>

                <table class="table table-primary" id="menu-table">
                    <thead>
                    <tr>
                        <th width="50"><label><input type="checkbox" class="check" onclick="all_y();"/>全选</label></th>
                        <th width="50">排序</th>
                        <th align="left">标题</th>
                        <th>类型</th>
                        <th>生效日期</th>
                        <th>过期时间</th>
                        <th>状态</th>
                        <th width="150">操作</th>
                    </tr>
                    </thead>
                    <tbody id="fen_list" class="fen_list">

                    {#ddb_pc set="数据表:vip_content,列表名:lists,显示数目:10,分页显示:1,分页名:pagelist,当前页:{#$page#},分页地址:{#$pageurl#}(?)/{#get_get()#},排序:indate desc" where="{#$wheresql#}"#}
                    {#foreach from=$lists item=list#}
                    <tr>
                        <td><input type="checkbox" class="check" name="y_id[]" id="y_id" value="{#$list.id#}"/></td>
                        <td><span id="trendedit" data-id="{#$list.id#}" data-type="inorder" data-isnum="yes">{#$list.inorder#}</span></td>
                        <td class="lt"><span id="trendedit"
                                             data-id="{#$list.id#}" data-type="title"{#cot($list.title_color)#}>{#$list.title#}</span></td>
                        <td>
                            <span>{#$list.type_b#}</span>
                            {#if $list.type_b == "打折优惠券"#}({#$list.int_a#}折){#/if#}
                            {#if $list.type_b == "现金抵用券"#}(抵{#$list.int_b#}){#/if#}
                        </td>
                        <td>{#$list.startdate|date_format:"%Y-%m-%d"#}</td>
                        <td>{#$list.enddate|date_format:"%Y-%m-%d"#}</td>
                        <td>{#vip_content_status($list.startdate,$list.enddate)#}</td>
                        <td>
                            <a class="normal-link" href="{#weburl('vip/couponstatistics')#}&contentid={#$list.id#}">发放统计</a>
                            <a class="normal-link" href="{#weburl(0,'vip/couponrelease',$list.id)#}">编辑</a>
                            <a class="normal-link" href="javascript:;" id="win" d="del" d-id="{#$list.id#}">删除</a>
                        </td>
                    </tr>
                    {#foreachelse#}
                    <tr>
                        <td colspan="8" align="center" class="align-center">
                            <div>无</div>
                        </td>
                    </tr>
                    {#/foreach#}
                    </tbody>
                </table>
                <div id="pagelist" class="clearfix">
                    {#$pagelist#}
                </div>
                <div class="control-top clearfix">
                    <div class="form-group">
                        <a type="button" id="win" d="delall" class="button">删除选中</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    window.y_id = false;
    function all_y(){
        if (window.y_id){
            window.y_id = false;
            $("input#y_id").prop("checked",false);
        }else{
            window.y_id = true;
            $("input#y_id").prop("checked",true);
        }
    }
    $(document).ready(function() {
        $("span#trendedit").click(function(){
            var e = $(this);
            var id = e.attr("data-id");
            var type = e.attr("data-type");
            var isnum = e.attr("data-isnum");
            var v = e.text();
            $intemp = $('<input data-value="'+v+'" value="'+v+'">');
            $intemp.css({
                height:e.outerHeight(),
                width:e.outerWidth(),
                minWidth:'30px',
                padding:'2px'
            });
            e.after($intemp.keydown(function(f){
                if(f.keyCode==13){
                    if ($intemp.attr('data-value') != $intemp.val()) {
                        var s = 'dosubmit=put&id='+id+'&type='+type+'&isnum='+isnum+'&val='+$intemp.val();
                        $.ajax({
                            type: "POST",
                            url: "{#weburl('vip/editval')#}",
                            data: s,
                            dataType: "json",
                            success: function (msg) {
                                if (msg.success != "1"){
                                    $.alert("修改失败！");
                                }else{
                                    $intemp.remove();
                                    e.text(msg.val);
                                    e.show();
                                }
                            },error: function (msg) {
                                $.alert("修改错误！");
                            }
                        });
                    }else{
                        $intemp.remove();
                        e.show();
                    }
                }
            }).blur(function () {
                if ($intemp.attr('data-value') != $intemp.val()) {
                    var s = 'dosubmit=put&id='+id+'&type='+type+'&isnum='+isnum+'&val='+$intemp.val();
                    $.ajax({
                        type: "POST",
                        url: "{#weburl('vip/editval')#}",
                        data: s,
                        dataType: "json",
                        success: function (msg) {
                            if (msg.success != "1"){
                                $.alert("修改失败！");
                            }else{
                                $intemp.remove();
                                e.text(msg.val);
                                e.show();
                            }
                        },error: function (msg) {
                            $.alert("修改错误！");
                        }
                    });
                }else{
                    $intemp.remove();
                    e.show();
                }
            })).hide();
            $intemp.focus();
        });
        $("a#win").click(function(){
            var d = $(this).attr("d");
            if (d == "del"){
                var s = "id=" + $(this).attr("d-id");
                art.dialog({
                    lock: true,
                    opacity: '.3',
                    content: '确定删除并且不可恢复吗？',
                    button: [{
                        name: '确定',
                        focus: true,
                        callback: function () {
                            $.ajax({
                                type: "POST",
                                url: "{#weburl('vip/coupondel')#}",
                                data: s,
                                dataType: "json",
                                success: function (msg) {
                                    if (msg.success == "1"){
                                        $.alert("删除成功！");
                                        window.location.reload();
                                    }else{
                                        $.alert("删除失败！");
                                    }
                                },
                                error: function (msg) {
                                    $.alert("删除错误！");
                                }
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
            }else if (d == "delall"){
                var s = '';
                $("input#y_id").each(function(){
                    if ($(this).is(':checked')){
                        s+= $(this).val()+",";
                    }
                });
                if (s == "") {
                    $.alert("请选择要删除的内容！");
                    return true;
                }
                art.dialog({
                    lock: true,
                    opacity: '.3',
                    content: '确定删除并且不可恢复吗？',
                    button: [{
                        name: '确定',
                        focus: true,
                        callback: function () {
                            $.ajax({
                                type: "POST",
                                url: "{#weburl('vip/coupondel')#}",
                                data: "id=" + s,
                                dataType: "json",
                                success: function (msg) {
                                    if (msg.success == "1"){
                                        $.alert("删除成功！");
                                        window.location.reload();
                                    }else{
                                        $.alert("删除失败！");
                                    }
                                },
                                error: function (msg) {
                                    $.alert("删除错误！");
                                }
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
            }
        });
    });
</script>

{#template("footer")#}

</body>
</html>