
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

    {#include file="menu.tpl" _item=3 _itemp=2#}


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
                        <div class="form-group"><input type="submit" class="button" value="确认导出"/></div>
                        <input type="hidden" name="type" value="export">
                        <input type="hidden" name="dosubmit" value="1">
                    </form>

                    <div style="float: right" class="form form-inline">
                        <div class="form-group">
                            <input type="text" id='keyv' class="form-control inp2" value="{#$keyv#}" data-val="{#$keyv#}" placeholder="输入搜索关键字" />
                        </div>
                        <div class="form-group">
                            <select id='keyn' class="form-control inp3">
                                <option value="fullname">会员姓名</option>
                                <option value="card">会员卡号</option>
                                <option value="sn">SN码</option>
                                <option value="operator">操作员</option>
                                <option value="contentid">特权ID</option>
                                <option value="contenttitle">特权名称</option>
                            </select>
                            <script>{#if $keyn#}$('#keyn').val('{#$keyn#}');{#/if#}</script>
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
                            <th width="50">选择</th>
                            <th>序号</th>
                            <th>SN 码</th>
                            <th>会员卡号</th>
                            <th>姓名</th>
                            <th>操作员</th>
                            <th>积分</th>
                            <th>第几次</th>
                            <th>特权名称</th>
                            <th>使用时间</th>
                        </tr>
                        </thead>
                        <tbody id="fen_list" class="fen_list">

                        {#ddb_pc set="数据表:vip_content_notes,列表名:lists,显示数目:10,分页显示:1,分页名:pagelist,当前页:{#$page#},分页地址:{#$pageurl#}(?)/{#get_get()#},排序:indate desc" where="{#$wheresql#}"#}
                        {#foreach from=$lists item=list#}
                        <tr>
                            <td><input type="checkbox" class="check" name="y_id[]" id="y_id" value="{#$list.id#}"/></td>
                            <td>{#$list._n#}</td>
                            <td>{#$list.sn#}</td>
                            <td>{#$list.card#}</td>
                            <td>{#$list.fullname#}</td>
                            <td>{#$list.operator#}</td>
                            <td>{#$list.point#}</td>
                            <td>{#$list.usenum#}</td>
                            <td>{#cut_str($list.contenttitle,12)#}</td>
                            <td>{#$list.indate|date_format:"%Y-%m-%d"#}</td>
                        </tr>
                        {#foreachelse#}
                        <tr>
                            <td colspan="10" align="center" class="align-center">
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
                            <div>
                                <label>
                                    <input type="checkbox" class="check" onclick="all_y();"/>全选导出
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="submit" name="dosubmit" class="button" value="确定" />
                        </div>
                    </div>
                </form>
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
    function keybut(){
        var keyv = $('#keyv').val().trim();
        var keyn = $('#keyn').val();
        if (keyv == ''){
            if ($('#keyv').attr('data-val')){
                window.location.href = "{#weburl(3)#}"; return;
            }else{
                alert("请输入搜索关键词"); $('#keyv').focus(); return;
            }
        }
        if (keyn == ''){
            alert("请选择搜索类型"); return;
        }
        window.location.href = "{#weburl(3)#}&"+keyn+"="+encodeURIComponent(keyv);
    }
</script>

{#template("footer")#}

</body>
</html>