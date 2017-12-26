
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
    <link rel="stylesheet" href="{#$NOW_PATH#}css/style.css"/>
    <style type="text/css">
        .avatar img{vertical-align:middle;width:30px;padding-right:3px}
        #text-tooltip{position:absolute;background-color:#fff;padding:8px;border:1px solid #cc7116}
        #text-tooltip img{display:block;margin:10px 0 0;max-width:300px;max-height:300px}
        .uinfonext{display:none}
        .uinfonext .listinfo{line-height:24px}
        .uinfonext .listinfo span{display:block;float:left;padding-right:30px;color:#777}
        .uinfonext .listinfo span:nth-child(even){color:#C8A99B}
        .uinfonext .listinfo span em{font-style:normal;margin-left:3px;text-decoration:underline}
        .uinfonext div.send{background:url({#$NOW_PATH#}images/send.png) no-repeat;background-size:contain;display:block;width:22px;height:22px;margin:0 auto;cursor:pointer;}
        .uinfonext div.send:hover{background-image:url({#$NOW_PATH#}images/send_hover.png);}
        .sendtitle{cursor:pointer;background:#4CBAEA;color:#fff;padding:5px 8px;font-weight:400;text-shadow:none;border-radius:3px;margin-left:10px}
        .sendtitle:hover{background:#fa0}
    </style>
    <script type="text/javascript" src="{#$JS_PATH#}jquery-1.11.0.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.alert.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.form.min.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}artdialog/artDialog.js?skin=default"></script>
    <script type="text/javascript" src="{#$JS_PATH#}datepicker/WdatePicker.js"></script>
</head>
<body>
{#template("header")#}


<div class="wrapper">

    {#include file="menu.tpl" _item=7#}


    <div class="main cf custom-menu">
        <div class="mod">

            <div class="main-bd">

                <div class="control-top clearfix">
                    <form style="float: left" action="{#$urlarr.now#}{#get_get()#}" class="form form-inline" method="post">
                        <div class="form-group"><span>序号导出</span></div>
                        <div class="form-group"><input class="form-control inp1" name="n1" type="text" /></div>
                        <div class="form-group"><span>-</span></div>
                        <div class="form-group"><input class="form-control inp1" name="n2" type="text" /></div>
                        <div class="form-group"><span>号</span></div>
                        <div class="form-group"><input type="submit" class="button" value="确认导出Excel"/></div>
                        <input type="hidden" name="type" value="export">
                        <input type="hidden" name="dosubmit" value="excel">
                    </form>

                    <div style="float: right" class="form form-inline">
                        <div class="form-group">
                            <input type="text" id='keyval' class="form-control inp2" value="{#$keyval#}" data-val="{#$keyval#}" placeholder="输入搜索关键字" />
                        </div>
                        <div class="form-group">
                            <select id='keytype' class="form-control inp3">
                                <option value="fullname">会员姓名</option>
                                <option value="card">会员卡号</option>
                                <option value="phone">电话</option>
                                <option value="address">地址</option>
                                <option value="id">UID</option>
                            </select>
                            <script>{#if $keytype#}$('#keytype').val('{#$keytype#}');{#/if#}</script>
                        </div>
                        <div class="form-group">
                            <button class="button" onclick="keybut();">搜索</button>
                        </div>
                    </div>
                </div>

                <form action="{#$urlarr.now#}{#get_get()#}"  method="post" id="saveform" class="form-services">
                    <input type="hidden" name="type" value="export">
                    <table class="table table-primary" id="menu-table">
                        <thead>
                        <tr>
                            <th><label><input type="checkbox" class="check" onclick="all_y();">序号</label></th>
                            <th style="text-align:left">卡号</th>
                            <th style="text-align:left;width:210px;">姓名</th>
                            <th>是否关注</th>
                            <th>消费金额</th>
                            <th>积分</th>
                            <th>会员级别</th>
                            <th>到期时间</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody id="fen_list" class="fen_list">

                        {#ddb_pc set="数据表:vip_users,列表名:lists,显示数目:10,分页显示:1,分页名:pagelist,当前页:{#$page#},分页地址:{#$pageurl#}(?)/{#get_get()#},排序:indate desc" where="{#$wheresql#}"#}
                        {#foreach from=$lists item=list#}
                        <tr id="user_info_{#$list.id#}" class="uinfo">
                            <td><input type="checkbox" class="check" name="y_id[]" id="y_id" value="{#$list.id#}"/>{#$list._n#}</td>
                            <td class="lt {#$list.type#} {#$list.type#}_{#$list.follow#}">
                                <a class="normal-link" href="{#weburl('vip/memberdetail')#}&card={#$list.card#}" id="u_card">{#format_user($list.card, 0)#}</a>
                            </td>
                            <td class="lt avatar">
                                {#if $list.avatar#}
                                    <img data-src="{#$list.avatar|fillurl#}" src="{#$list.avatar|avatar_fillurl:"/46"#}">
                                {#/if#}
                                <span id="u_fullname" data-title="{#$list.fullname#}">{#$list.fullname|get_html:9#}</span>
                            </td>
                            <td>
                                {#if $list.follow == 1#}
                                    <span class="follow follow-success">已关注</span>
                                {#elseif $list.follow == 0#}
                                    <span class="follow follow-warning">取消关注</span>
                                {#else#}
                                    <span class="follow follow-other">其他</span>
                                {#/if#}
                            </td>
                            <td id="u_money" d-id="{#$list.id#}">{#format_user($list.money, 2)#}</td>
                            <td id="u_point" d-id="{#$list.id#}">{#$list.point#}</td>
                            <td id="u_rank">{#get_user_rank($list.pluspoint)#}</td>
                            <td id="u_enddate_cn">{#user_status($list.enddate)#}</td>
                            <td>
                                <input type="hidden" id="u_enddate" value="{#$list.enddate|date_format:"%Y-%m-%d"#}"/>
                                <a class="normal-link" href="javascript:;" id="win" d="edit" d-id="{#$list.id#}">编辑</a>
                                <a class="normal-link" href="javascript:;" id="win" d="del" d-id="{#$list.id#}">删除</a>
                            </td>
                        </tr>
                        <tr class="uinfonext">
                            <td>
                                {#if $list.follow == 1#}
                                    <div class="send" onclick="_notes('{#$list.id#}')" title="与 {#$list.fullname|get_html:9#} 的聊天记录"></div>
                                {#/if#}
                            </td>
                            <td colspan="8" class="lt listinfo">
                                <span>性别:<em id="u_sex">{#$list.sex#}</em></span>
                                <span>电话:<em id="u_phone">{#format_user($list.phone, 1)#}</em></span>
                                <span>邮箱:<em id="u_email">{#$list.email#}</em></span>
                                <span>身份证:<em id="u_idnumber">{#$list.idnumber#}</em></span>
                                <span>地址:<em id="u_address">{#$list.address#}</em></span>
                                <span>加入:<em id="u_indate">{#date("Y-m-d H:i:s", $list.indate)#}</em></span>
                            </td>
                        </tr>
                        {#foreachelse#}
                        <tr>
                            <td colspan="9" align="center" class="align-center">
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
                            <select class="form-control inp3" name="dosubmit" id="dosubmit">
                                <option value="excel">导出EXCEL</option>
                                <option value="delete">删除会员(粉丝)</option>
                                {#if $_A['al']['wx_appid']#} <option value="updatewx">同步粉丝(微信)</option> {#/if#}
                            </select>
                        </div>
                        <div class="form-group">
                            <input type="submit" name="submit" class="button" value="确定" />
                            {#if $_A['al']['wx_appid']#}
                            <a class="button" href="javascript:;" id="win" d="downfans" style="margin-left:15px;">下载所有粉丝(微信)</a>
                            {#/if#}
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<div id="temp-body" style="display: none;">
    <div class="table table-simple">
        <table>
            <thead>
            <tr>
                <th><span>标题</span></th>
                <th><span>内容</span></th>
                <th><span>备注</span></th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td class="color-primary align-center font-bold"><span>卡号</span></td>
                <td class="align-center font-bold"><span idid="e_card">0</span></td>
                <td class="color-info"><span>卡号不可修改</span></td>
            </tr>
            <tr>
                <td class="color-primary align-center font-bold"><span>姓名</span></td>
                <td class="align-center"><input type="text" idid="e_fullname"/></td>
                <td class="color-info"><span></span></td>
            </tr>
            <tr>
                <td class="color-primary align-center font-bold"><span>性别</span></td>
                <td class="align-center"><select idid="e_sex"><option value="男">男</option><option value="女">女</option></select></td>
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
                <td class="color-primary align-center font-bold"><span>消费金额</span></td>
                <td class="align-center"><span idid="e_money">0</span></td>
                <td class="color-info"><span>消费金额不可修改</span></td>
            </tr>
            <tr>
                <td class="color-primary align-center font-bold"><span>积分</span></td>
                <td class="align-center"><span idid="e_point">0</span></td>
                <td class="color-info"><span>积分不可修改</span></td>
            </tr>
            <tr>
                <td class="color-primary align-center font-bold"><span>会员级别</span></td>
                <td class="align-center"><span idid="e_rank"></span></td>
                <td class="color-info"><span>卡片类型不可修改</span></td>
            </tr>
            <tr>
                <td class="color-primary align-center font-bold"><span>到期时间</span></td>
                <td class="align-center"><input type="text" idid="e_enddate" onFocus="WdatePicker({dateFmt:'yyyy-MM-dd'})"/></td>
                <td class="color-info"><span>留空表示永不过期</span><input idid="e_id" type="hidden"/></td>
            </tr>
            <tr>
                <td class="color-primary align-center font-bold"><span>邮箱</span></td>
                <td class="align-center"><input type="text" idid="e_email"/></td>
                <td class="color-info"><span></span></td>
            </tr>
            <tr>
                <td class="color-primary align-center font-bold"><span>身份证</span></td>
                <td class="align-center"><input type="text" idid="e_idnumber"/></td>
                <td class="color-info"><span></span></td>
            </tr>
            </tbody>
        </table>
    </div>
</div>

{#tpl_form_aledit()#}
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
    function keybut(){
        var keyval = $('#keyval').val().trim();
        var keytype = $('#keytype').val();
        var usertype = $('#usertype').val();
        if (keyval == ''){
            if ($('#keyval').attr('data-val')){
                window.location.href = "{#weburl(3)#}"; return;
            }else{
                alert("请输入搜索关键词"); $('#keyval').focus(); return;
            }
        }
        if (keytype == ''){
            alert("请选择搜索类型"); $('#keytype').focus(); return;
        }
        window.location.href = "{#weburl(3)#}&keyval="+encodeURIComponent(keyval)+"&keytype="+keytype+"&usertype="+usertype;
    }
    function _notes(id) {
        var obj = $("#user_info_" + id);
        var _h = ($(window).height()-50)*0.96;
        art.dialog({
            title: '与 '+obj.find("#u_fullname").attr("data-title")+' 的聊天 <span class="sendtitle" onclick="_reply(0,'+id+')">发送信息</span>',
            fixed: true,
            lock: true,
            width: 820,
            height: _h,
            opacity: '.3',
            content: '<iframe src="{#weburl('message/notes')#}&vipid='+id+'" style="width:750px;height:'+(_h-50)+'px;" frameborder="no" border="0" marginwidth="0" marginheight="0" scrolling="auto" allowtransparency="yes"> '
        });
    }

    function _reply(id, vipid) {
        var _url = "{#weburl('message/reply')#}&id="+id;
        if (id == 0) {
            _url = "{#weburl('message/reply')#}&vipid="+vipid;
        }
        var _rep = art.dialog({
            title: '回复信息',
            fixed: true,
            lock: true,
            opacity: '.3',
            content: '<textarea id="replutext" name="replutext" data-link="1" style="width:460px;height:200px;padding:0;"></textarea> ',
            button: [{
                name: '提交',
                focus: true,
                callback: function () {
                    $.alert('正在提交',0);
                    var dataurl = '';
                    $(".aliedit").find("textarea,input").each(function(){
                        dataurl+= $(this).attr("name")+"="+$(this).val()+"&"
                    });
                    $.ajax({
                        type: "POST",
                        url: _url,
                        data: dataurl,
                        dataType: 'json',
                        success: function (data) {
                            $.alert(data.message);
                            if (data != null && data.success != null && data.success) {
                                $("#isreply_"+id).html("<span title='刷新当前页面可查看回复信息详情'>已回复</span>");
                                _rep.close();
                            }
                        },error : function () {
                            $.alert("提交失败！");
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
        $("#replutext").tpl_form_aledit();
    }

    $(document).ready(function() {
        $("#fen_list").find("tr").each(function(index){
            if (index % 2 >= 1) $(this).css("background-color","#F4F5FB");
        });
        $("tr.uinfo").mouseover(function() {
            $("tr.uinfonext").hide();
            $(this).find("td").show();
            $(this).next("tr").show();
        });
        $(".listinfo").find("em").each(function(){
            if (!$(this).text()) $(this).text("未完善");
        });
        $("#menu-table thead th").each(function(){
            $(this).css("width", $(this).width())
        });
        $("td#u_point").click(function(){
            var id = $(this).attr("d-id");
            //操作积分
            var _point = art.dialog({
                lock: true,
                opacity: '.3',
                title: '用户积分操作',
                content: '<div class="putpoint">' +
                        '<p><span>积分变化：</span><input type="text" id="_u_point" size="10" placeholder="请输入整数"></p>' +
                        '<p class="l"><span>操作备注：</span><textarea id="_u_textarea" placeholder="备注可留空"></textarea></p>' +
                        '</div>',
                button: [{
                    name: '增加',
                    focus: true,
                    callback: function () {
                        var num = $("#_u_point").val();
                        var text = $("#_u_textarea").val();
                        var s = "dosubmit=putpoint&id="+id+"&num="+num+"&text="+text;
                        $.ajax({
                            type: "POST",
                            url: "{#weburl(3)#}",
                            dataType: "json",
                            data: s,
                            success: function (msg) {
                                $.alert(msg.message);
                                if (msg.success == "1"){
                                    var f = $("#user_info_"+id);
                                    f.find("#u_point").text(msg.point);
                                    _point.close();
                                }
                            },
                            error: function (msg) {
                                $.alert("增加错误！");
                            }
                        });
                        return false;
                    }
                },{
                    name: '扣除',
                    callback: function () {
                        var num = $("#_u_point").val();
                        var text = $("#_u_textarea").val();
                        var s = "dosubmit=cutpoint&id="+id+"&num="+num+"&text="+text;
                        $.ajax({
                            type: "POST",
                            url: "{#weburl(3)#}",
                            dataType: "json",
                            data: s,
                            success: function (msg) {
                                $.alert(msg.message);
                                if (msg.success == "1"){
                                    var f = $("#user_info_"+id);
                                    f.find("#u_point").text(msg.point);
                                    _point.close();
                                }
                            },
                            error: function (msg) {
                                $.alert("扣除错误！");
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
        });
        $("td#u_money").click(function(){
            var id = $(this).attr("d-id");
            //消费金额
            var _money = art.dialog({
                lock: true,
                opacity: '.3',
                title: '用户金额操作',
                content: '<div class="putpoint">' +
                        '<p><span>金额变化：</span><input type="text" id="_u_money" size="10" placeholder="请输入整数"></p>' +
                        '<p class="l"><span>操作备注：</span><textarea id="_u_mtextarea" placeholder="备注可留空"></textarea></p>' +
                        '</div>',
                button: [{
                    name: '增加',
                    focus: true,
                    callback: function () {
                        var num = $("#_u_money").val();
                        var text = $("#_u_mtextarea").val();
                        var s = "dosubmit=putmoney&id="+id+"&num="+num+"&text="+text;
                        $.ajax({
                            type: "POST",
                            url: "{#weburl(3)#}",
                            dataType: "json",
                            data: s,
                            success: function (msg) {
                                $.alert(msg.message);
                                if (msg.success == "1"){
                                    var f = $("#user_info_"+id);
                                    f.find("#u_money").text(msg.money);
                                    _money.close();
                                }
                            },
                            error: function (msg) {
                                $.alert("增加错误！");
                            }
                        });
                        return false;
                    }
                },{
                    name: '扣除',
                    callback: function () {
                        var num = $("#_u_money").val();
                        var text = $("#_u_mtextarea").val();
                        var s = "dosubmit=cutmoney&id="+id+"&num="+num+"&text="+text;
                        $.ajax({
                            type: "POST",
                            url: "{#weburl(3)#}",
                            dataType: "json",
                            data: s,
                            success: function (msg) {
                                $.alert(msg.message);
                                if (msg.success == "1"){
                                    var f = $("#user_info_"+id);
                                    f.find("#u_money").text(msg.money);
                                    _money.close();
                                }
                            },
                            error: function (msg) {
                                $.alert("扣除错误！");
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
        });
        $("a#win").click(function(){
            var d = $(this).attr("d");
            if (d == "edit"){
                //编辑
                var _edit = art.dialog({
                    lock: true,
                    opacity: '.3',
                    content: $("#temp-body").html().replace(/idid/g, "id"),
                    button: [{
                        name: '确定',
                        focus: true,
                        callback: function () {
                            var id = $("#e_id").val(),
                                    fullname = $("#e_fullname").val(),
                                    sex = $("#e_sex").val(),
                                    phone = $("#e_phone").val(),
                                    address = $("#e_address").val(),
                                    email = $("#e_email").val(),
                                    idnumber = $("#e_idnumber").val(),
                                    enddate = $("#e_enddate").val();
                            var s = "dosubmit=edit&id="+id+"&fullname="+fullname+"&sex="+sex+"&phone="+phone+"&address="+address+"&email="+email+"&idnumber="+idnumber+"&enddate="+enddate+"";
                            $.ajax({
                                type: "POST",
                                url: "{#weburl(3)#}",
                                dataType: "json",
                                data: s,
                                success: function (msg) {
                                    if (msg.success == "1"){
                                        var f = $("#user_info_"+id);
                                        f.find("#u_fullname").text(msg.fullname);
                                        f.next("tr").find("#u_sex").text(msg.sex);
                                        f.next("tr").find("#u_phone").text(msg.phone);
                                        f.next("tr").find("#u_address").text(msg.address);
                                        f.next("tr").find("#u_email").text(msg.email);
                                        f.next("tr").find("#u_idnumber").text(msg.idnumber);
                                        f.find("#u_enddate").val(msg.enddate);
                                        f.find("#u_enddate_cn").html(msg.enddate_cn);
                                        _edit.close();
                                        $.alert("修改成功");
                                    }else{
                                        $.alert(msg.message?msg.message:"更新失败！");
                                    }
                                },
                                error: function (msg) {
                                    $.alert("更新错误！");
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
                var p = $(this).parents(".uinfo");
                $("#e_id").val(p.find("#y_id").val());
                $("#e_card").text(p.find("#u_card").text());
                $("#e_fullname").val(p.find("#u_fullname").text());
                $("#e_sex").val(p.next("tr").find("#u_sex").text().replace("未完善",""));
                $("#e_phone").val(p.next("tr").find("#u_phone").text().replace("未完善","").replace(/[ ]/g,""));
                $("#e_address").val(p.next("tr").find("#u_address").text().replace("未完善",""));
                $("#e_email").val(p.next("tr").find("#u_email").text().replace("未完善",""));
                $("#e_idnumber").val(p.next("tr").find("#u_idnumber").text().replace("未完善",""));
                $("#e_money").text(p.find("#u_money").text());
                $("#e_point").text(p.find("#u_point").text());
                $("#e_rank").text(p.find("#u_rank").text());
                if (p.find("#u_enddate_cn").text() == '永不过期'){
                    $("#e_enddate").val("");
                }else{
                    $("#e_enddate").val(p.find("#u_enddate").val());
                }
            }else if (d == "del"){
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
                                url: "{#weburl('vip/memberdel')#}",
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
            }else if (d == "downfans"){
                art.dialog({
                    lock: true,
                    opacity: '.3',
                    title: '【微信公众号】同步/更新(下载)所有粉丝',
                    content: '同步所有：将微信公众号所有粉丝(包括未加入到平台的粉丝)信息同步到平台；<br/><br/>更新现有：将微信公众号现有的粉丝(仅在列表中看到的)信息同步到平台。',
                    button: [{
                        name: '同步所有',
                        callback: function () {
                            downfans();
                            return true;
                        }
                    },{
                        name: '仅更新现有',
                        callback: function () {
                            $.showModal('系统将开始更新粉丝数据,请不要离开页面', "{#weburl('vip/initsyncbefore')#}&indate=1&belog=0");
                            return true;
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
        $("#saveform").submit(function(){
            var s = '';
            $("input#y_id").each(function(){
                if ($(this).is(':checked')){
                    s+= $(this).val()+",";
                }
            });
            if (s == "") {
                $.alert("请选择要操作的会员！");
                return false;
            }
            var dosubmit = $("#dosubmit").val();
            if (dosubmit == "updatewx") {
                var tthis = $(this);
                art.dialog({
                    lock: true,
                    opacity: '.3',
                    content: '此功能仅支持微信粉丝暂不支持服务窗粉丝！<br/><br/>点击“确定”开始同步！',
                    button: [{
                        name: '确定',
                        focus: true,
                        callback: function () {
                            $.alert('正在同步...', 0);
                            tthis.ajaxSubmit({
                                dataType : 'json',
                                success : function (data) {
                                    $.alert(0);
                                    $.showModal("同步成功！", "{#get_url()#}");
                                },
                                error : function () {
                                    $.alert(0);
                                    $.inModal("同步失败！");
                                }
                            });
                            return true;
                        }
                    },{
                        name: '取消',
                        callback: function () {
                            return true;
                        }
                    }]
                });
                return false;
            }else if (dosubmit == "delete") {
                $.alert('正在删除...', 0);
                $(this).ajaxSubmit({
                    dataType : 'json',
                    success : function (data) {
                        $.alert(0);
                        $.showModal("删除成功！", "{#get_url()#}");
                    },
                    error : function () {
                        $.alert(0);
                        $.inModal("删除失败！");
                    }
                });
                return false;
            }
        });
        $(".avatar").find("img").mouseover(function(e){
            var _text = $(this).next("#u_fullname").attr("data-title");
            var _img = $(this).attr("data-src");
            _img = (_img)?"<img src='"+_img+"'/>":'';
            if (!_text) return;
            $tooltip = $("<div id='text-tooltip'>"+_text+_img+"</div>"); //创建 div 元素
            $("body").append($tooltip); //把它追加到文档中
            $("#text-tooltip").css({
                "top": (e.pageY+5) + "px",
                "left":  (e.pageX+10)  + "px"
            }).show("fast");   //设置x坐标和y坐标，并且显示
        }).mouseout(function(){
            $("#text-tooltip").remove();  //移除
        }).mousemove(function(e){
            $("#text-tooltip").css({
                "top": (e.pageY+5) + "px",
                "left":  (e.pageX+10)  + "px"
            });
        });
    });

    function downfans(next, count) {
        var params = {};
        params.method = 'download';
        if(next) params.next = next;
        if(!count) count = 0;
        $.alert('正在下载...',0,1);
        $.ajax({
            type: "POST",
            url: "{#weburl('vip/memberdown')#}",
            data: params,
            dataType: "json",
            success: function (dat) {
                if(dat.errno || dat.type == 'error' || dat.type == 'info') {
                    $.showModal(dat.message, location.href);
                    return false;
                }
                count += dat.count;
                if((dat.total <= count) || (!dat.count && !dat.next)) {
                    $.alert(0);
                    $.showModal('粉丝下载完成,系统将开始更新粉丝数据,请不要离开页面', "{#weburl('vip/initsync')#}");
                    return true;
                } else {
                    downfans(dat.next, count);
                }
            },
            error: function (msg) {
                $.alert("下载错误！");
            }
        });
    }
</script>

{#template("footer")#}

</body>
</html>