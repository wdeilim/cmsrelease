
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

    {#include file="menu.tpl" _item=7#}


    <div class="main cf custom-menu">
        <div class="mod">

            <div class="main-bd">

                <div class="member-info">
                    <h1 class="title">会员账户详情</h1>
                    <ul class="clearfix">
                        <li>
                            <span>姓名：</span>
                            <strong>{#$user.fullname#}</strong>
                        </li>
                        <li>
                            <span>卡号：</span>
                            <strong>{#format_user($user.card,0)#}</strong>
                        </li>
                        {#*<li>
                            <span>电话：</span>
                            <strong>{#format_user($user.phone,1)#}</strong>
                        </li>
                        <li>
                            <span>地址：</span>
                            <strong>{#$user.address#}</strong>
                        </li>*#}
                    </ul>
                    <div class="control-top clearfix">
                        <a class="button" style="float: right;" href="{#weburl('vip/member')#}">返回会员</a>
                        <div class="form-group">
                            <select id="ddate" onchange="ddate(this);" class="form-control inp3">
                                {#foreach from=$ddate item=list#}
                                <option value="{#$list#}">{#$list#}</option>
                                {#/foreach#}
                                <option value="-1">历史所有</option>
                            </select>
                            <script>{#if $date#}$('#ddate').val('{#$date#}');{#/if#}</script>
                        </div>
                        <div class="form-group">
                            <span>按月查看账户情况</span>
                        </div>
                    </div>
                </div>

                <table class="table table-primary" id="menu-table">
                    <thead>
                    <tr>
                        <th>日期</th>
                        <th>消费金额</th>
                        <th>积分情况</th>
                        <th>消费名称</th>
                    </tr>
                    </thead>
                    <tbody id="fen_list" class="fen_list">

                    {#ddb_pc set="数据表:vip_point_notes,列表名:lists,显示数目:10,分页显示:1,分页名:pagelist,当前页:{#$page#},分页地址:{#$pageurl#}(?)/{#get_get()#},排序:indate desc" where="{#$wheresql#}"#}
                    {#foreach from=$lists item=list#}
                    <tr>
                        <td>{#$list.indate|date_format:"%Y-%m-%d %H:%M:%S"#}</td>
                        <td>
                            {#if $list.money>0#}
                            {#$list.money#}
                            ({#if $list.outmoney>0#}+{#/if#}{#$list.outmoney#})
                            {#else#}
                            {#if $list.outmoney>0#}+{#/if#}{#$list.outmoney#}
                            {#/if#}
                        </td>
                        <td>
                            {#$list.point#}
                            ({#if $list.outpoint>0#}+{#/if#}{#$list.outpoint#})
                        </td>
                        <td title="{#$list.content#}">{#get_html($list.content,25)#}</td>
                    </tr>
                    {#foreachelse#}
                    <tr>
                        <td colspan="4" align="center" class="align-center">
                            <div>无</div>
                        </td>
                    </tr>
                    {#/foreach#}
                    </tbody>
                </table>
                <div id="pagelist" class="clearfix">
                    {#$pagelist#}
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function ddate(obj){
        var ddv = $(obj).val();
        if (ddv != "") {
            window.location='{#weburl('vip/memberdetail')#}&card={#$user.card#}&date='+ddv;
        }
    }
</script>

{#template("footer")#}

</body>
</html>