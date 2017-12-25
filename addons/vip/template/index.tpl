
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

	{#include file="menu.tpl" _item=0#}

	
    <div class="main cf custom-menu">
        <div class="mod">
            
			<div class="main-bd">

                <div class="member-info">
                    <h1 class="title">统计概况</h1>
                </div>

                <div class="summary clearfix">
                    <hr/>
                    <table>
                        <tbody>
                        <tr>
                            <td>
                                <div>
                                    <span class="text">特权(发放/使用)数量</span>
                                    <span class="value">{#$fvip_num#}/{#$vip_num#}</span>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <span class="text">礼品券(发放/使用)数量</span>
                                    <span class="value">{#$fgift_num#}/{#$gift_num#}</span>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <span class="text">优惠券(发放/使用)数量</span>
                                    <span class="value">{#$fcut_num#}/{#$cut_num#}</span>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <span class="text">今日消费次数</span>
                                    <span class="value">{#$day_num#}</span>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div class="timeline clearfix">
                    <h2>时间段</h2>
                    <hr>
                    <div class="form-inline clearfix">
                        <div class="form-group">
                            <input type="text" id="startdate" value="{#$startdate#}" placeholder="起始"
                                   onFocus="WdatePicker({maxDate:'#F{$dp.$D(\'enddate\',{d:-1});}'})" class="form-control-small">
                        </div>
                        <div class="form-group">
                            <span>至</span>
                        </div>
                        <div class="form-group">
                            <input type="text" id="enddate" value="{#$enddate#}" placeholder="结束"
                                   onFocus="WdatePicker({minDate:'#F{$dp.$D(\'startdate\',{d:1});}'})" class="form-control-small">
                        </div>
                        <div class="form-group">
                            <button class="button primary" onclick="keybut();">查询</button>
                        </div>
                    </div>
                </div>

                <div class="timeline clearfix">
                    <h2>发放明细</h2>
                    <hr>
                    <div class="table table-primary clearfix">
                        <div class="toldiv">
                            <div class="toln">
                                <table>
                                    <thead>
                                    <tr>
                                        <th class="lt"><span>礼品券</span></th>
                                        <th class="bor"><span>数量</span></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    {#foreach from=$gift_list item=list#}
                                    <tr>
                                        <td class="lt"><a class="normal-link"
                                               href="{#weburl('vip/giftcertstatistics')#}&contentid={#$list.id#}">{#$list.title#}</a></td>
                                        <td><span>{#$list.num#}</span></td>
                                    </tr>
                                    {#foreachelse#}
                                    <tr>
                                        <td colspan="2" class="align-center">没有任何信息</td>
                                    </tr>
                                    {#/foreach#}
                                    </tbody>
                                </table>
                            </div>
                            <div class="toln">
                                <table>
                                    <thead>
                                    <tr>
                                        <th class="lt"><span>优惠券</span></th>
                                        <th class="bor"><span>数量</span></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    {#foreach from=$cut_list item=list#}
                                    <tr>
                                        <td class="lt"><a class="normal-link"
                                               href="{#weburl('vip/couponstatistics')#}&contentid={#$list.id#}">{#$list.title#}</a></td>
                                        <td><span>{#$list.num#}</span></td>
                                    </tr>
                                    {#foreachelse#}
                                    <tr>
                                        <td colspan="2" class="align-center">没有任何信息</td>
                                    </tr>
                                    {#/foreach#}
                                    </tbody>
                                </table>
                            </div>
                            <div class="toln">
                                <table>
                                    <thead>
                                    <tr>
                                        <th class="lt"><span>会员特权</span></th>
                                        <th><span>数量</span></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    {#foreach from=$vip_list item=list#}
                                    <tr>
                                        <td class="lt"><a class="normal-link"
                                               href="{#weburl('vip/privilegestatistics')#}&contentid={#$list.id#}">{#$list.title#}</a></td>
                                        <td><span>{#$list.num#}</span></td>
                                    </tr>
                                    {#foreachelse#}
                                    <tr>
                                        <td colspan="2" class="align-center">没有任何信息</td>
                                    </tr>
                                    {#/foreach#}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
			</div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function keybut(){
        var startdate = $('#startdate').val();
        var enddate = $('#enddate').val();
        var _url = "{#weburl('vip/index')#}";
        if (startdate != "") _url+= "&startdate="+startdate;
        if (enddate != "") _url+= "&enddate="+enddate;
        window.location.href = _url;
    }
</script>

{#template("footer")#}

</body>
</html>