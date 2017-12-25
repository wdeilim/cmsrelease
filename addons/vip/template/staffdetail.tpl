
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

    {#include file="menu.tpl" _item=8#}


    <div class="main cf custom-menu">
        <div class="mod">

            <div class="main-bd">

                <div class="member-info">
                    <a class="button" style="float: right;" href="{#weburl('vip/staff')#}">返回店员</a>
                    <h1 class="title">操作记录</h1>
                </div>

                <table class="table table-primary" id="menu-table">
                    <thead>
                    <tr>
                        <th width="50"><label><input type="checkbox" class="check" onclick="all_y();"/>全选</label></th>
                        <th>金额</th>
                        <th>积分</th>
                        <th>会员</th>
                        <th>类型</th>
                        <th>操作时间</th>
                    </tr>
                    </thead>
                    <tbody id="fen_list" class="fen_list">

                    {#ddb_pc set="数据表:vip_content_notes,列表名:lists,显示数目:10,分页显示:1,分页名:pagelist,当前页:{#$page#},分页地址:{#$pageurl#}(?)/{#get_get()#},排序:indate desc" where="{#$wheresql#}"#}
                    {#foreach from=$lists item=list#}
                    <tr>
                        <td><input type="checkbox" class="check " name="y_id[]" id="y_id" value="{#$list.id#}"/></td>
                        <td>{#$list.money#}</td>
                        <td>{#$list.point#}</td>
                        <td>{#$list.fullname#}</td>
                        <td>{#$list.type#}</td>
                        <td>{#$list.indate|date_format:"%Y-%m-%d"#}</td>
                    </tr>
                    {#foreachelse#}
                    <tr>
                        <td colspan="6" align="center" class="align-center">
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
        $("a#win").click(function(){
            var d = $(this).attr("d");
            if (d == "delall"){
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
                        callback: function () {
                            $.ajax({
                                type: "POST",
                                url: "{#weburl(3)#}",
                                data: "dosubmit=del&id=" + s,
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