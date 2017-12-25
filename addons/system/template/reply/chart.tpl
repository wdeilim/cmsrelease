
<!DOCTYPE html>
<html>
<head lang="zh-CN">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{#$_A.f.title#} - {#$BASE_NAME#}</title>
    <link rel="stylesheet" href="{#$CSS_PATH#}bootstrap.min.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}normalize.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}global.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}functions.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}alert.css"/>
    <link rel="stylesheet" href="{#$NOW_PATH#}css/default.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}font-awesome.css"/>
    <style type="text/css">
        .list-title{padding-top:0;font-weight:normal;width:500px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
        .topmenu .keytitle {display:block;float:left;line-height:36px;padding:0;margin-right:12px;color:#0099cc;}
        .form-services {margin-top:15px;}
        .countweixin{color:#95C000;margin-right:15px}
        .countweixin em{background-color:#95C000;display:block;float:left;width:14px;height:14px;margin-top:3px;margin-right:3px}
        .countalipay{color:#97BBCD;margin-right:15px}
        .countalipay em{background-color:#97BBCD;display:block;float:left;width:14px;height:14px;margin-top:3px;margin-right:3px}
    </style>
    <script type="text/javascript" src="{#$JS_PATH#}jquery-1.11.0.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.alert.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}bootstrap.min.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}chart.min.js"></script>
</head>
<body>
{#template("header")#}


<div class="wrapper">
    <div class="breadcrumb">
        <div class="items">
            <span class="description">当前位置：</span>
            <span>{#$_A.f.title#}</span>
        </div>
    </div>


    {#if $_GPC['referer']#}
        <div class="topmenu" id="topmenu">
            <a href="javascript:;" class="active">{#$_A.f.title#}</a>
            <a href="{#$_GPC['referer']#}">返回</a>
        </div>
    {#/if#}

    <div class="main cf custom-menu">
        <div class="mod">
            <div class="main-bd" id="tabmenu">

                <form action="{#$furl#}" id="form1" method="post">
                    {#tpl_form_daterange('time', $time, 0, 30)#}
                </form>

                <div class="form-services">

                    {#foreach from=$lists item=list#}
                        <div class="panel panel-default">
                            <div class="panel-heading clearfix">
                                <div class="pull-left" title="{#$list.key#}">关键词: {#$list.key#} </div>
                                <div class="pull-right">
                                    <div class="pull-left countweixin"><em></em>微信: {#$list.countweixin#}次</div>
                                    <div class="pull-left countalipay"><em></em>支付宝: {#$list.countalipay#}次</div>
                                    <div class="pull-left">总计: {#$list.count#}次</div>
                                </div>
                            </div>
                            <div class="panel-body">
                                <div><canvas id="{#$list.k#}" height="80"></canvas></div>
                            </div>
                        </div>
                        {#foreachelse#}
                        <div style="margin-top:100px;text-align:center">暂时没有数据！</div>
                    {#/foreach#}
                    
                </div>
            </div>
        </div>
    </div>
</div>


{#template("footer")#}

<script>
    $(function () {
        $('.daterange').on('apply.daterangepicker', function(ev, picker) {
            $('#form1')[0].submit();
        });
        {#foreach from=$lists item=list#}
        var lineChartData = {
            labels : {#json_encode($list['day'])#},
            datasets : [
                {
                    label: "微信",
                    fillColor : "rgba(149,192,0,0.1)",
                    strokeColor : "rgba(149,192,0,1)",
                    pointColor : "rgba(149,192,0,1)",
                    pointStrokeColor : "#fff",
                    pointHighlightFill : "#fff",
                    pointHighlightStroke : "rgba(149,192,0,1)",
                    data : {#json_encode($list['weixin'])#}
                },{
                    label: "服务窗",
                    fillColor : "rgba(151,187,205,0.2)",
                    strokeColor : "rgba(151,187,205,1)",
                    pointColor : "rgba(151,187,205,1)",
                    pointStrokeColor : "#fff",
                    pointHighlightFill : "#fff",
                    pointHighlightStroke : "rgba(151,187,205,1)",
                    data : {#json_encode($list['alipay'])#}
                }
            ]
        };
        new Chart(document.getElementById("{#$list.k#}").getContext("2d")).Line(lineChartData, {responsive : true});
        {#/foreach#}
    });
</script>

</body>
</html>