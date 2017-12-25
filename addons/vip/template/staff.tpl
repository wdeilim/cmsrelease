
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
    <script type="text/javascript" src="{#$JS_PATH#}datepicker/WdatePicker.js"></script>
</head>
<body>
{#template("header")#}


<div class="wrapper">

    {#include file="menu.tpl" _item=8 _itemp=0#}


    <div class="main cf custom-menu">
        <div class="mod">

            <div class="main-bd">

                <a class="button" style="margin-bottom:15px" href="javascript:;" id="win" d="put" d-id="0">添加新店员</a>

                <table class="table table-primary" id="menu-table">
                    <thead>
                    <tr>
                        <th width="50"><label><input type="checkbox" class="check" onclick="all_y();"/>全选</label></th>
                        <th>编号</th>
                        <th>用户名</th>
                        <th>密码</th>
                        <th>姓名</th>
                        <th>分店</th>
                        <th width="150">操作</th>
                    </tr>
                    </thead>
                    <tbody id="fen_list" class="fen_list">

                    {#ddb_pc set="数据表:vip_shop_users,列表名:lists,显示数目:10,分页显示:1,分页名:pagelist,当前页:{#$page#},分页地址:{#$pageurl#}(?)/{#get_get()#},排序:indate desc" where="{#$wheresql#}"#}
                    {#foreach from=$lists item=list#}
                    <tr id="user_info_{#$list.id#}">
                        <td><input type="checkbox" class="check " name="y_id[]" id="y_id" value="{#$list.id#}"/></td>
                        <td><span>{#$list.id#}</span></td>
                        <td><span id="u_username">{#$list.username#}</span></td>
                        <td><span id="u_userpass">{#$list.userpass#}</span></td>
                        <td><span id="u_fullname">{#$list.fullname#}</span></td>
                        <td><span id="u_shopid">{#if $list.shopid#}{#get_shop($list.shopid, $sqlarr)#}{#else#}所有店铺{#/if#}</span></td>
                        <td>
                            <a class="normal-link" href="{#weburl('vip/staffdetail')#}&id={#$list.id#}">操作记录</a>
                            <a class="normal-link" href="javascript:;" id="win" d="put" d-id="{#$list.id#}">编辑</a>
                            <a class="normal-link" href="javascript:;" id="win" d="del" d-id="{#$list.id#}">删除</a>
                        </td>
                    </tr>
                    {#foreachelse#}
                    <tr>
                        <td colspan="7" align="center" class="align-center">
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


<div id="temp-body" style="display: none;">
    <div class="table table-simple table-staff">
        <table>
            <tbody>
            <tr>
                <td class="color-primary align-center font-bold"><span>姓名</span></td>
                <td class="align-center"><input type="text" idid="e_fullname"/></td>
                <td class="color-info"><span></span></td>
            </tr>
            <tr>
                <td class="color-primary align-center font-bold"><span>电话</span></td>
                <td class="align-center"><input type="text" idid="e_phone"/></td>
                <td class="color-info"><span></span></td>
            </tr>
            <tr>
                <td class="color-primary align-center font-bold"><span>地址</span></td>
                <td class="align-center"><input type="text" idid="e_address"/></td>
                <td class="color-info"><span></span></td>
            </tr>
            <tr>
                <td class="color-primary align-center font-bold"><span>用户名</span></td>
                <td class="align-center"><input type="text" idid="e_username"/></td>
                <td class="color-info"><span></span></td>
            </tr>
            <tr>
                <td class="color-primary align-center font-bold"><span>密码</span></td>
                <td class="align-center"><input type="text" idid="e_userpass"/></td>
                <td class="color-info"><span>密码至少6位以上</span></td>
            </tr>
            <tr>
                <td class="color-primary align-center font-bold"><span>到期时间</span></td>
                <td class="align-center"><input type="text" idid="e_enddate" onFocus="WdatePicker({dateFmt:'yyyy-MM-dd'})"/></td>
                <td class="color-info"><span>留空表示永不过期</span><input idid="e_id" type="hidden"/></td>
            </tr>
            <tr>
                <td class="color-primary align-center font-bold"><span>所属店铺</span></td>
                <td class="align-center">
                    <select idid="e_shopid">
                        <option value="0">所有店铺</option>
                        {#foreach from=$shop item=list#}
                        <option value="{#$list.id#}">{#$list.name#}({#$list.address#})</option>
                        {#/foreach#}
                </td>
                <td class="color-info"><span>不选将默认可以操作任何店铺</span></td>
            </tr>
            </tbody>
        </table>
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
            if (d == "put"){
                var did = $(this).attr("d-id");
                //添加、修改
                var _art = art.dialog({
                    title: (did > 0)?'修改店员':'添加店员',
                    lock: true,
                    opacity: '.3',
                    content: $("#temp-body").html().replace(/idid/g, "id"),
                    button: [{
                        name: '确定',
                        callback: function () {
                            var id = did,
                                    fullname = $("#e_fullname").val(),
                                    phone = $("#e_phone").val(),
                                    address = $("#e_address").val(),
                                    username = $("#e_username").val(),
                                    userpass = $("#e_userpass").val(),
                                    shopid = $("#e_shopid").val(),
                                    enddate = $("#e_enddate").val();
                            var s = "dosubmit=put&id="+id+"&fullname="+fullname+"&phone="+phone+"&address="+address+"&username="+username+"&userpass="+userpass+"&shopid="+shopid+"&enddate="+enddate+"";
                            $.ajax({
                                type: "POST",
                                url: "{#weburl(3)#}",
                                dataType: "json",
                                data: s,
                                success: function (msg) {
                                    $.alert(msg.message);
                                    if (msg.success == "1"){
                                        _art.close();
                                        window.location.reload();
                                    }
                                },
                                error: function (msg) {
                                    $.alert("添加错误！");
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
                if (did > 0){
                    $.ajax({
                        type: "POST",
                        url: "{#weburl(3)#}",
                        dataType: "json",
                        data: "dosubmit=info&id=" + did,
                        success: function (msg) {
                            if (msg.success == "1"){
                                $("#e_fullname").val(msg.fullname);
                                $("#e_phone").val(msg.phone);
                                $("#e_address").val(msg.address);
                                $("#e_username").val(msg.username);
                                $("#e_userpass").val(msg.userpass);
                                $("#e_enddate").val(msg.enddate);
                                $("#e_shopid").val(msg.shopid);
                                $("#e_id").val(msg.id);
                            }else{
                                $.alert("加载失败！");
                            }
                        },
                        error: function (msg) {
                            $.alert("加载错误！");
                        }
                    });
                }
            }else if (d == "del"){
                var s = "dosubmit=del&id=" + $(this).attr("d-id");
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