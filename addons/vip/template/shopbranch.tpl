
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
    <script type="text/javascript" src="{#$JS_PATH#}map.js"></script>
    <script type="text/javascript" src="http://api.map.baidu.com/api?v=1.1&key=KMAaKTqxQPZ6bDpp8NeKK7uu&services=true"></script>
</head>
<body>
{#template("header")#}


<div class="wrapper">

    {#include file="menu.tpl" _item=1 _itemp=1#}

	
    <div class="main cf custom-menu">
        <div class="mod">
            
			<div class="main-bd">

				<table class="table table-primary" id="menu-table">
					<thead>
					<tr>
						<th width="50">序号</th>
						<th width="150">区域名称</th>
						<th width="*">详细地址</th>
						<th width="280">经纬度</th>
						<th width="100">联系电话</th>
						<th width="60">操作</th>
					</tr>
					</thead>
                    <tbody id="fen_list" class="fen_list">

                        {#foreach from=$lists item=list name=foo#}
                        <tr data-id="{#$list.id#}">
                            <td><span id="s_i">{#$smarty.foreach.foo.index+1#}</span></td>
                            <td class="lt"><span id="s_name">{#$list.name#}</span></td>
                            <td class="lt"><span id="s_add" title="{#$list.address#}">{#$list.address#}</span></td>
                            <td>
                                <span id="s_x">{#$list.x#}</span>,<span id="s_y">{#$list.y#}</span>
                                <button class="button" onclick="fen_map(this);">地图标注</button>
                            </td>
                            <td>
                                <span id="s_phone">{#$list.phone#}</span>
                            </td>
                            <td>
                                <a href="javascript:;" onclick="fen_add(this);">修改</a>
                                <a href="javascript:;" onclick="fen_del(this);">删除</a>
                            </td>
                        </tr>
                        {#foreachelse#}
                        <tr>
                            <td colspan="6" align="center" class="align-center">
                                <div>无</div>
                            </td>
                        </tr>
                        {#/foreach#}
					
					</tbody>


                    <tbody style="display:none;" id="fen_templet">
                        <tr data-id="0">
                            <td><span id="s_i">0</span></td>
                            <td class="lt"><span id="s_name"><input type="text" name="name"/></span></td>
                            <td class="lt"><span id="s_add"><input type="text" name="address"/></span></td>
                            <td>
                                <span id="s_x"><input id="x" type="text"  name="x"/></span>,<span id="s_y"><input type="text" name="y" id="y"/></span>
                                <button style="margin-left: 20px;" class="button primary" onclick="fen_map(this);">地图标注</button>
                            </td>
                            <td><span id="s_phone"><input type="text" name="phone"/></span></td>
                            <td>
                                <a href="javascript:;" onclick="fen_add(this);">保存</a>
                                <a href="javascript:;" onclick="fen_del(this);">取消</a>
                            </td>
                        </tr>
                    </tbody>

				</table>
                <div class="button" style="margin-top:15px" onclick="add_fen();">添加分店</div>
			</div>

        </div>
    </div>
</div>

<script type="text/javascript">
    function add_fen(){
        $("#fen_list").append($("#fen_templet").html());
        fen_list();
    }
    function fen_list(){
        $("#fen_list tr td #s_i").each(function(index){
            $(this).text(index + 1);
        });
    }
    function fen_map(obj){
        var eve = $(obj).parent();
        var x,y;
        if (eve.parent().attr("data-id") > 0){
            x = eve.find("#s_x").text();
            y = eve.find("#s_y").text();
        }else{
            x = eve.find("#s_x input").val();
            y = eve.find("#s_y input").val();
        }
        art.dialog({
            title: '设置地图',
            fixed: true,
            lock: true,
            opacity: '.3',
            content: '<input type="text" class="form-control" id="setting_map">' +
                    '<button class="button" type="button" id="setting_mapbut" style="margin:-4px 0 0 4px;">搜索</button>'+
                    '<div id="setting_show"></div>'+
                    '<input id="setting_maplat" type="hidden" value="'+x+'">'+
                    '<input id="setting_maplong" type="hidden" value="'+y+'">',
            button: [{
                name: '确定',
                callback: function () {
                    var _x = $("#setting_maplat").val();
                    var _y = $("#setting_maplong").val();
                    if (eve.parent().attr("data-id") > 0){
                        var s = "id="+eve.parent().attr("data-id")+"&x="+_x+"&y="+_y;
                        $.ajax({
                            type: "POST",
                            url: "{#weburl('vip/shopbranch/map')#}",
                            data: s,
                            dataType: "json",
                            success: function (msg) {
                                if (msg.success == "1"){
                                    eve.find("#s_x").text(_x);
                                    eve.find("#s_y").text(_y);
                                }else{
                                    $.alert("更新失败！");
                                }
                            },
                            error: function (msg) {
                                $.alert("更新错误！");
                            }
                        });
                    }else{
                        eve.find("#s_x input").val(_x);
                        eve.find("#s_y input").val(_y);
                    }
                    return true;
                }
            },{
                name: '取消',
                callback: function () {
                    return true;
                }
            }]
        });
        baidu_map('','setting_show','setting_map','setting_maplat','setting_maplong','setting_mapbut', true);
    }


    function fen_del(obj){
        if ($(obj).text() == "取消"){
            if ($(obj).parent().parent().attr("data-id") > 0){
                $(obj).text("删除");
                $(obj).prev().text("修改");
                var o = $(obj).parent().parent();
                o.find("td #s_name").text(o.find("td #s_name input").attr("data-text"));
                o.find("td #s_add").text(o.find("td #s_add input").attr("data-text"));
                o.find("td #s_phone").text(o.find("td #s_phone input").attr("data-text"));
                return true;
            }else{
                $(obj).parent().parent().remove();
                fen_list();
            }
        }else{
            var _del = art.dialog({
                lock: true,
                opacity: '.3',
                content: '你确定要删除该分店吗？',
                button: [{
                    name: '确定',
                    callback: function () {
                        var s = "id="+$(obj).parent().parent().attr("data-id");
                        $.ajax({
                            type: "POST",
                            url: "{#weburl('vip/shopbranch/del')#}",
                            data: s,
                            dataType: "json",
                            success: function (msg) {
                                if (msg.success == "1"){
                                    $(obj).parent().parent().remove();
                                    fen_list();
                                    _del.close();
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
    }
    function fen_add(obj){
        if ($(obj).text() == "修改"){
            $(obj).text("保存");
            $(obj).next().text("取消");
            var o = $(obj).parent().parent();
            o.find("td #s_name").html("<input  type='text' name='name' value='" + o.find("td #s_name").text() + "' data-text='" + o.find("td #s_name").text() + "' />");
            o.find("td #s_add").html("<input  type='text' name='address' value='" + o.find("td #s_add").text() + "' data-text='" + o.find("td #s_add").text() + "' />");
            o.find("td #s_phone").html("<input  type='text' name='phone' value='" + o.find("td #s_phone").text() + "' data-text='" + o.find("td #s_phone").text() + "' />");
        }else{
            var o = $(obj).parent().parent();
            var s = "";
            s+= "name="+o.find("td #s_name input").val();
            s+= "&address="+o.find("td #s_add input").val();
            s+= "&phone="+o.find("td #s_phone input").val();
            if (o.attr("data-id") <= 0){
                s+= "&x="+o.find("td #s_x input").val();
                s+= "&y="+o.find("td #s_y input").val();
                s+= "&id=0";
            }else{
                s+= "&id="+o.attr("data-id");
            }

            $.ajax({
                type: "POST",
                url: "{#weburl('vip/shopbranch/add')#}",
                data: s,
                dataType: "json",
                success: function (msg) {
                    if (msg.success == "1"){
                        $(obj).text("修改");
                        $(obj).next().text("删除");
                        o.find("td #s_name").text(o.find("td #s_name input").val());
                        o.find("td #s_add").text(o.find("td #s_add input").val());
                        o.find("td #s_phone").text(o.find("td #s_phone input").val());
                        if (o.attr("data-id") <= 0){
                            o.find("td #s_x").text(o.find("td #s_x input").val());
                            o.find("td #s_y").text(o.find("td #s_y input").val());
                            o.attr("data-id", msg.id);
                        }
                    }else{
                        $.alert(msg.message);
                    }
                },
                error: function (msg) {
                    $.alert("保存错误！");
                }
            });
        }
    }
</script>

{#template("footer")#}

</body>
</html>