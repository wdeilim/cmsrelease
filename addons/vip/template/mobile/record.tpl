
<!DOCTYPE HTML>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta content="telephone=no" name="format-detection">
    <title>我的账单</title>
    <link rel="stylesheet" href="{#$NOW_PATH#}css/inner.css">
    <link rel="stylesheet" href="{#$NOW_PATH#}css/select.css">
    <script type="text/javascript" src="{#$JS_PATH#}jquery.min.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.alert.js"></script>
    <style>
        .btn-sign a{display: block; color: #fff; line-height: 48px;}
        .record h2 {
            margin-bottom:5px
        }
        .record .overage {
            margin-bottom:8px
        }
        .record .overage em {
            font-style: normal;
            margin-left: 5px;
            line-height: 12px;
            font-size: 14px;
            padding: 3px 6px;
            background-color: #0E9228;
            color: #ffffff;
            border-radius: 2px;
        }
    </style>
</head>

<body>
<div class="layout">
    <div class="wrap">
        <div class="record">
            <h2>我的{#$type_cn#}账单</h2>
            <div class="overage">
                {#if $type == 'money'#}
                    账户余额：{#$_A['vip']['money']#}
                    <em onclick="$('#_charge').addClass('show');">充值</em>
                {#/if#}
                {#if $type == 'point'#}
                    账户积分：{#$_A['vip']['point']#}分
                {#/if#}
            </div>
            <div class="vip-credit">
                <table class="vip-table">
                    <tbody>
                    <tr>
                        <th>日期</th>
                        <th>{#$type_cn#}</th>
                        <th>备注</th>
                    </tr>
                    {#ddb_pc set="数据表:vip_point_notes,列表名:lists,显示数目:10,分页显示:1,分页名:pagelist,当前页:{#$page#},分页地址:{#$pageurl#}(?)/{#get_get()#},排序:indate desc" where="{#$wheresql#}"#}

                    {#foreach from=$lists item=list#}
                    <tr>
                        <td>{#$list.indate|date_format:"%Y-%m-%d %H:%M:%S"#}</td>
                        <td>
                            {#if $type_cn == '现金'#}
                                {#if $list.money>0#}
                                {#$list.money#}
                                ({#if $list.outmoney>0#}+{#/if#}{#$list.outmoney#})
                                {#else#}
                                {#if $list.outmoney>0#}+{#/if#}{#$list.outmoney#}
                                {#/if#}
                            {#else#}
                                {#$list.point#}
                                ({#if $list.outpoint>0#}+{#/if#}{#$list.outpoint#})
                            {#/if#}
                        </td>
                        <td title="{#$list.content#}">{#get_html($list.content,25)#}</td>
                    </tr>
                    {#foreachelse#}
                    <tr>
                        <td colspan="3" align="center" class="align-center">
                            <div>无记录</div>
                        </td>
                    </tr>
                    {#/foreach#}

                    {#if $pagelist#}
                    <tr>
                        <td colspan="3" style="text-align:right;padding-right:10px;">{#$pagelist#}</td>
                    </tr>
                    {#/if#}
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {#include file="./footer.tpl" _item=""#}

</div>

<div class="selectattrval" id="_charge">
    <header>
        <h1>在线充值</h1>
        <a class="back" href="javascript:$('#_charge').removeClass('show');">返回</a>
    </header>
    <section class="property_charge">
        <div>
            <span>充值金额</span>
            <input id="cha_num" type="tel" placeholder="请输入充值金额(元)">
        </div>
        <div>
            <span>选择方式</span>
            <select id="cha_type">
                <option value="alipay">支付宝在线充值</option>
                <option value="weixin">微信在线充值</option>
            </select>
        </div>
        <div class="c-btn-oran-big" onclick="submit();">下一步</div>
    </section>
</div>

<script type="text/javascript">
    $(function(){
        {#if value($_GPC, 'charge')#}
        $('#_charge').addClass('show');
        {#/if#}
    });
    function submit(){
        var num = parseFloat($('#cha_num').val());
        if (num+"" == "NaN" || num < 0.01){
            $.alert("单笔充值金额不得小于￥0.01")
            return;
        }
        window.location.href = "{#$urlarr.2#}charge/?type="+$('#cha_type').val()+"&cha_num="+num;
    }
</script>
</body>
</html>