
<!DOCTYPE html>
<html>
<head lang="zh-CN">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{#$func.f.title#} - {#$BASE_NAME#}</title>
    <link rel="stylesheet" href="{#$CSS_PATH#}bootstrap.min.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}normalize.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}global.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}functions.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}alert.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}message.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}font-awesome.css"/>
    <style type="text/css">
        .contentbox,.contentbox *{-webkit-box-sizing:content-box;-moz-box-sizing:content-box;box-sizing:content-box}
        .countweixin{color:#95C000}
        .countalipay{color:#97BBCD}
        .navbar-static-top .navbar-brand{font-size:16px;color:#5e5e5e;padding:12px 15px 0 0;height:auto;cursor:default}
        .navbar-static-top .navbar-nav>li>a{padding:12px 12px}
        .account-stat{overflow:hidden;color:#666;margin:20px auto}
        .account-stat .account-stat-btn{width:100%;overflow:hidden}
        .account-stat .account-stat-btn>div{text-align:center;margin-bottom:5px;margin-right:2%;float:left;width:23%;height:80px;padding-top:10px;font-size:16px;border-left:1px #DDD solid}
        .account-stat .account-stat-btn>div:first-child{border-left:0}
        .account-stat .account-stat-btn>div span{display:block;font-size:30px;font-weight:700}
        .data-header{border-left:0.3em #207749 solid;padding-left:7px;margin-bottom:5px;color:#207749;height:22px;line-height:22px;font-weight:600;font-size:15px;text-align:left;}
    </style>
    <script type="text/javascript" src="{#$JS_PATH#}jquery-1.11.0.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.alert.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}chart.min.js"></script>
</head>
<body>
{#template("header")#}

<div class="wrapper">
    <div class="breadcrumb">
        <div class="items">
            <span class="description">当前位置：</span>
            <span>{#$func.f.title#}</span>
            <i class="iconfont">&#xe621;</i>
            <span>统计情况</span>
        </div>
    </div>


    <div class="topmenu contentbox" id="topmenu">
        <a href="{#weburl('message/index')#}">全部信息</a>
        <a href="{#weburl('message/index')#}&star=1">标星信息</a>
        <a href="javascript:;" class="active">统计情况</a>
    </div>

    <div class="main cf custom-menu">
        <div class="mod">
            <div class="main-bd" id="tabmenu">

                <div class="tabmenu" style="display:block;">

                    <form action="{#$furl#}" id="form1" method="post" style="margin-bottom:20px;">
                        {#tpl_form_daterange('time', $time, 0, 30)#}
                    </form>

                    <div class="form-services">


                        <div class="data-header">用户 » 公众号(服务窗)</div>

                        <div class="panel panel-default" id="data-receive">
                            <nav role="navigation" class="navbar-default navbar-static-top">
                                <div class="container-fluid">
                                    <div class="navbar-header">
                                        <a href="javascript:;" class="navbar-brand">按类接收趋势图</a>
                                    </div>
                                    <ul class="nav navbar-nav nav-btns" style="float:left">
                                        <li id="follow" class="active"><a href="javascript:;">关注事件</a></li>
                                        <li id="unfollow"><a href="javascript:;">取消关注</a></li>
                                        <li id="text"><a href="javascript:;">发送文字</a></li>
                                        <li id="click" title="详情可查阅自定义菜单类型说明"><a href="javascript:;">发送信息菜单</a></li>
                                        <li id="view" title="详情可查阅自定义菜单类型说明"><a href="javascript:;">跳转URL菜单</a></li>
                                        <li id="image"><a href="javascript:;">发送图片</a></li>
                                        <li id="voice"><a href="javascript:;">发送语音</a></li>
                                        <li id="video"><a href="javascript:;">发送视频</a></li>
                                        <li id="other" title="包含：地理位置、扫码等事件"><a href="javascript:;">其他事件</a></li>
                                    </ul>
                                </div>
                            </nav>

                            <div class="account-stat">
                                <div class="account-stat-btn">
                                    <div>总回复规则数<span id="rule">0</span></div>
                                    <div class="countweixin">微信命中次数<span id="ruleweixin">0</span></div>
                                    <div class="countalipay">服务窗命中次数<span id="rulealipay">0</span></div>
                                    <div>
                                        <a href="{#weburl('reply')#}" style="display:block; margin:5px 0;"><i class="fa fa-search"></i> 查看回复规则</a>
                                        <a href="{#weburl('reply/add')#}" style="display:block;"><i class="fa fa-plus"></i> 新增回复规则</a>
                                    </div>
                                </div>
                            </div>

                            <div style="margin-top:20px;">
                                <canvas id="myChart" height="80"></canvas>
                            </div>
                        </div>


                        <div class="data-header">公众号(服务窗) » 用户</div>

                        <div class="panel panel-default" id="data-reply">
                            <nav role="navigation" class="navbar-default navbar-static-top">
                                <div class="container-fluid">
                                    <div class="navbar-header">
                                        <a href="javascript:;" class="navbar-brand">按类回复趋势图</a>
                                    </div>
                                    <ul class="nav navbar-nav nav-btns" style="float:left">
                                        <li id="text" class="active"><a href="javascript:;">文字回复</a></li>
                                        <li id="imagetext"><a href="javascript:;">图文回复</a></li>
                                        {#*<li id="music"><a href="javascript:;">音乐回复</a></li>*#}
                                        <li id="image"><a href="javascript:;">图片回复</a></li>
                                        <li id="voice"><a href="javascript:;">语音回复</a></li>
                                        <li id="video"><a href="javascript:;">视频回复</a></li>
                                    </ul>
                                </div>
                            </nav>

                            <div class="account-stat">
                                <div class="account-stat-btn">
                                    <div>总回复规则数<span id="rule">0</span></div>
                                    <div class="countweixin">微信命中次数<span id="ruleweixin">0</span></div>
                                    <div class="countalipay">服务窗命中次数<span id="rulealipay">0</span></div>
                                    <div>
                                        <a href="{#weburl('reply')#}" style="display:block; margin:5px 0;"><i class="fa fa-search"></i> 查看回复规则</a>
                                        <a href="{#weburl('reply/add')#}" style="display:block;"><i class="fa fa-plus"></i> 新增回复规则</a>
                                    </div>
                                </div>
                            </div>

                            <div style="margin-top:20px;">
                                <canvas id="myChart_reply" height="80"></canvas>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>
</div>


{#template("footer")#}
<script type="text/javascript">
    $(function(){
        $('.daterange').on('apply.daterangepicker', function(ev, picker) {
            $('#form1')[0].submit();
        });

        _receive();
        _reply();
    });


    function _receive() {
        //接收
        var receive = $("#data-receive");
        var myLine = new Chart(document.getElementById("myChart").getContext("2d"));
        var lineChartData = null;
        var obj = null;

        $.post(location.href, {'m_name' : 'follow', 'm_tobe' : 0, 'time': {#json_encode($time)#}}, function(data){
            data = $.parseJSON(data);

            receive.find("#rule").html(data.rule);
            receive.find("#ruleweixin").html(data.countweixin);
            receive.find("#rulealipay").html(data.countalipay);

            lineChartData = {
                labels : data.day,
                datasets : [
                    {
                        label: "微信",
                        fillColor : "rgba(149,192,0,0.1)",
                        strokeColor : "rgba(149,192,0,1)",
                        pointColor : "rgba(149,192,0,1)",
                        pointStrokeColor : "#fff",
                        pointHighlightFill : "#fff",
                        pointHighlightStroke : "rgba(149,192,0,1)",
                        data : data.weixin
                    },{
                        label: "服务窗",
                        fillColor : "rgba(151,187,205,0.2)",
                        strokeColor : "rgba(151,187,205,1)",
                        pointColor : "rgba(151,187,205,1)",
                        pointStrokeColor : "#fff",
                        pointHighlightFill : "#fff",
                        pointHighlightStroke : "rgba(151,187,205,1)",
                        data : data.alipay
                    }
                ]
            };
            obj = myLine.Line(lineChartData, {responsive: true});
        });
        //

        receive.find(".nav.nav-btns>li").click(function(){
            receive.find(".nav.nav-btns>li").removeClass("active");
            $(this).addClass("active");
            var m_name = $(this).attr('id');
            $.alert("加载中...", 0);
            $.post(location.href, {'m_name' : m_name, 'm_tobe' : 0, 'time': {#json_encode($time)#}}, function(data){
                $.alert(0);
                data = $.parseJSON(data);

                receive.find("#rule").html(data.rule);
                receive.find("#ruleweixin").html(data.countweixin);
                receive.find("#rulealipay").html(data.countalipay);

                for(var i = 0; i <= data.daynum; i++) {
                    obj.datasets[0].points[i].value = data.weixin[i];
                    obj.datasets[1].points[i].value = data.alipay[i];
                }
                obj.update();
            });
        });
    }
    function _reply() {
        //回复
        var reply = $("#data-reply");
        var myLine = new Chart(document.getElementById("myChart_reply").getContext("2d"));
        var lineChartData = null;
        var obj = null;

        $.post(location.href, {'m_name' : 'text', 'm_tobe' : 1, 'time': {#json_encode($time)#}}, function(data){
            data = $.parseJSON(data);

            reply.find("#rule").html(data.rule);
            reply.find("#ruleweixin").html(data.countweixin);
            reply.find("#rulealipay").html(data.countalipay);

            lineChartData = {
                labels : data.day,
                datasets : [
                    {
                        label: "微信",
                        fillColor : "rgba(149,192,0,0.1)",
                        strokeColor : "rgba(149,192,0,1)",
                        pointColor : "rgba(149,192,0,1)",
                        pointStrokeColor : "#fff",
                        pointHighlightFill : "#fff",
                        pointHighlightStroke : "rgba(149,192,0,1)",
                        data : data.weixin
                    },{
                        label: "服务窗",
                        fillColor : "rgba(151,187,205,0.2)",
                        strokeColor : "rgba(151,187,205,1)",
                        pointColor : "rgba(151,187,205,1)",
                        pointStrokeColor : "#fff",
                        pointHighlightFill : "#fff",
                        pointHighlightStroke : "rgba(151,187,205,1)",
                        data : data.alipay
                    }
                ]
            };
            obj = myLine.Line(lineChartData, {responsive: true});
        });
        //

        reply.find(".nav.nav-btns>li").click(function(){
            reply.find(".nav.nav-btns>li").removeClass("active");
            $(this).addClass("active");
            var m_name = $(this).attr('id');
            $.alert("加载中...", 0);
            $.post(location.href, {'m_name' : m_name, 'm_tobe' : 1, 'time': {#json_encode($time)#}}, function(data){
                $.alert(0);
                data = $.parseJSON(data);

                reply.find("#rule").html(data.rule);
                reply.find("#ruleweixin").html(data.countweixin);
                reply.find("#rulealipay").html(data.countalipay);

                for(var i = 0; i <= data.daynum; i++) {
                    obj.datasets[0].points[i].value = data.weixin[i];
                    obj.datasets[1].points[i].value = data.alipay[i];
                }
                obj.update();
            });
        });
    }
</script>
</body>
</html>