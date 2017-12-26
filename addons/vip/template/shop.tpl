
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
    <script type="text/javascript" src="{#$JS_PATH#}ajaxfileupload.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}ueditor/ueditor.config.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}ueditor/ueditor.all.min.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}artdialog/artDialog.js?skin=default"></script>
    <script type="text/javascript" src="{#$JS_PATH#}map.js"></script>
    <script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=eDsGxG65jw27rKR2hGfhRIBp"></script>
</head>
<body>
{#template("header")#}


<div class="wrapper">

    {#include file="menu.tpl" _item=1 _itemp=0#}


    <div class="main cf custom-menu">
        <div class="mod">
            <div class="main-bd main-page">
                <div class="mod mod-rounded mod-bordered">
                    <form action="{#weburl()#}"  method="post" id="saveform" class="form-services">
                        <table class="table table-form">
                            <tbody>
                            <tr>
                                <td><span>类型</span></td>
                                <td class="form-reg">
                                    <select id="type" name="type">
                                        <option {#sel($shop['type'], '美食餐饮')#}>美食餐饮</option>
                                        <option {#sel($shop['type'], '休闲娱乐')#}>休闲娱乐</option>
                                        <option {#sel($shop['type'], '生活服务')#}>生活服务</option>
                                        <option {#sel($shop['type'], '其他行业')#}>其他行业</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td><span>商家名称</span></td>
                                <td>
                                    <input name="name" type="text" class="form-control" id="name" value="{#$shop.name#}">
                                </td>
                            </tr>
                            <tr>
                                <td><span>商家介绍</span></td>
                                <td class="form-uetext">
                                    <script id="content" name="content" type="text/plain" style="width:900px;; height:300px;">{#$shop.content#}</script>
                                </td>
                            </tr>
                            <tr>
                                <td><span>消费密码</span></td>
                                <td>
                                    <input name="pass" type="password" class="form-control" id="pass" placeholder="不填写则不修改">
                                </td>
                            </tr>
                            <tr>
                                <td><span>再次确认密码</span></td>
                                <td>
                                    <input name="pass2" type="password" class="form-control" id="pass2" placeholder="不填写则不修改">
                                </td>
                            </tr>
                            <tr>
                                <td><span>电话地址</span></td>
                                <td>
                                    <input name="phone" type="text" class="form-control" id="phone" value="{#$shop.phone#}">
                                </td>
                            </tr>
                            <tr>
                                <td><span>联系地址</span></td>
                                <td>
                                    <input name="address" type="text" class="form-control" id="address" value="{#$shop.address#}">
                                    <div class="button" style="margin-top:-4px;" onclick="_map();">打开地图标注</div>
                                    <input name="x" id="x" type="hidden" value="{#$shop.x#}">
                                    <input name="y" id="y" type="hidden" value="{#$shop.y#}">
                                </td>
                            </tr>
                            <tr>
                                <td><span>底部版权信息</span></td>
                                <td>
                                    <textarea name="copyright" class="form-control" id="copyright" style="height:60px;" placeholder="留空不显示">{#$shop.copyright#}</textarea>
                                </td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>
                                    <div class="control-submit">
                                        <input class="button" type="submit" value="保存"> &nbsp;
                                        <input class="button" type="reset" value="重置">
                                        <input type="hidden" name="dosubmit" value="1">
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function _map(){
        art.dialog({
            title: '设置地图',
            fixed: true,
            lock: true,
            opacity: '.3',
            content: '<input type="text" class="form-control" id="setting_map" value="'+$("#address").val()+'">' +
                '<button class="button" type="button" id="setting_mapbut" style="margin:-4px 0 0 4px;">搜索</button>'+
                '<div id="setting_show"></div>'+
                '<input id="setting_maplat" type="hidden" value="'+$("#x").val()+'">'+
                '<input id="setting_maplong" type="hidden" value="'+$("#y").val()+'">',
            button: [{
                name: '确定',
                focus: true,
                callback: function () {
                    $("#address").val($("#setting_map").val());
                    $("#x").val($("#setting_maplat").val());
                    $("#y").val($("#setting_maplong").val());
                    return true;
                }
            },{
                name: '取消',
                callback: function () {
                    return true;
                }
            }]
        });
        baidu_map('','setting_show','setting_map','setting_maplat','setting_maplong','setting_mapbut');
    }
    $(document).ready(function() {
        UE.getEditor('content', {autoHeightEnabled:false});
        //添加保存数据
        $('#saveform').submit(function() {
            $.alert('正在保存...', 0);
            $(this).ajaxSubmit({
                dataType : 'json',
                success : function (data) {
                    if (data != null && data.success != null && data.success) {
                        $.alert("保存成功");
                    } else {
                        $.alert(0);
                        $.showModal(data.message);
                    }
                },
                error : function () {
                    $.alert(0);
                    $.inModal("保存失败！");
                }
            });
            return false;
        });
    });
</script>

{#template("footer")#}

</body>
</html>