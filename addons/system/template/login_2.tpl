
<!DOCTYPE html>
<html>
<head lang="zh-CN">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{#if $tem['title']#}{#$tem['title']#}{#else#}{#$BASE_NAME#}{#/if#}</title>
    <meta name="keywords" content="{#$tem['keywords']#}" />
    <meta name="description" content="{#$tem['description']#}" />
    <link rel="stylesheet" href="{#$CSS_PATH#}normalize.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}global.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}alert.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}font-awesome.css"/>
    <link rel="stylesheet" href="{#$NOW_PATH#}css/iconfont.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}bootstrap.min.css"/>
    <link rel="stylesheet" href="{#$NOW_PATH#}css/login_2/common.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}iealert.css"/>
    <script type="text/javascript" src="{#$JS_PATH#}jquery-1.11.0.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.alert.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.form.min.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}bootstrap.min.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.browser.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}iealert.js"></script>
    <script type="text/javaScript">$(document).ready(function() { $("body").iealert(); });</script>
</head>
<body>



<div class="home">
    <div class="main-sigh-in">
        <div class="sign-in-bg fadeIn"></div>
        <div class="sign-in-mod">
            <form action="{#$urlarr.now#}" method="post" id="loginForm">
                <div class="form-title">登录{#$smarty.const.BRAND_NAME#}</div>
                <div class="form-item">
                    <div class="form-group form-group-input">
                        <label for="username">
                            <span class="ui-icon ui-icon-userDEF"><i class="iconfont-auth">&#xe617;</i></span>
                        </label>
                        <input class="form-control" type="text" name="username" id="username" placeholder="用户名"/>
                    </div>
                </div>
                <div class="form-item">
                    <div class="form-group form-group-input">
                        <label for="userpass">
                            <span class="ui-icon ui-icon-userDEF"><i class="iconfont-auth">&#xe616;</i></span>
                        </label>
                        <input class="form-control" type="password" name="userpass" id="userpass" placeholder="密码"/>
                    </div>
                </div>
                <div class="form-item">
                    <input id="subForm" type="submit" class="button button-primary button-metro" value="登录"/>
                    <input type="hidden" name="dosubmit" value="1"/>
                    <a class="normal-link" href="tencent://message/?uin={#$smarty.const.DIY_LINKQQ_PATH#}&Menu=yes">忘记密码？</a>
                </div>
            </form>
            <div class="qrcode cf">
                {#if $settingother.logincode#}
                    <img class="image" width="90" src="{#$settingother.logincode|fillurl#}" alt=""/>
                {#else#}
                    <img class="image" width="90" height="90" src="{#$IMG_PATH#}qrcode.png" alt=""/>
                {#/if#}
                <div class="description">
                    {#if $settingother.logintext#}
                        {#$settingother.logintext#}
                    {#else#}
                        <p>扫描二维码，关注 <a class="normal-link" href="{#$smarty.const.BRAND_URL#}" target="_blank">{#$smarty.const.BRAND_NAME#}</a></p>
                        <p>量身定制，随需而变优质产品助您绘制移动互联网时代蓝图</p>
                    {#/if#}
                </div>
            </div>
        </div>
    </div>
    <div class="content clearfix">
        <div id="head">
            <div class="logo"{#if $tem['t2']['logo']#} style="background:url('{#fillurl($tem['t2']['logo'])#}') no-repeat;"{#/if#}></div>
            <div class="advertisement">
                {#if $tem['t2']['title']#}{#$tem['t2']['title']#}{#else#}再小的个体，也有自己的微窗{#/if#}
            </div>
            <div class="main-entry">
                <a href="javascript:;" class="seller-login"><span class="title"><i class="seller"></i>立即登录</span><s></s></a>
                <a href="{#$urlarr.2#}reg/" class="personal-login"><span class="title"><i class="personal"></i>我要加入</span><s></s></a>
            </div>
        </div>
        <div id="banner" class="carousel slide" data-ride="carousel">
            <div class="carousel-indicators"><i class="fa fa-angle-double-down"></i></div>
            <div class="carousel-inner" role="listbox">
                {#foreach from=$tem['t2']['loginbg'] name=foo item=bg#}
                    <div class="item{#if $smarty.foreach.foo.first#} active{#/if#}" style="background-image:url({#fillurl($bg)#});"></div>
                {#/foreach#}
            </div>
        </div>

        <div class="con container">
            <div class="panel panel-default">
                <h4>系统功能介绍</h4>
                <div class="panel-body">
                    <div class="row system-info">
                        <div class="col-xs-3">
                            <div class="icon"><i class="fa fa-tablet"></i></div>
                            <h6>微网站、微场景</h6>
                        </div>
                        <div class="col-xs-3">
                            <div class="icon"><i class="fa fa-bullseye"></i></div>
                            <h6>微信营销解决方案</h6>
                        </div>
                        <div class="col-xs-3">
                            <div class="icon"><i class="fa fa-life-ring"></i></div>
                            <h6>微信账号集中一站管理</h6>
                        </div>
                        <div class="col-xs-3">
                            <div class="icon"><i class="fa fa-sitemap"></i></div>
                            <h6>强大的商家运营管理平台</h6>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel panel-default">
                <h4>功能模块介绍</h4>
                <div class="panel-body">
                    <div class="row module-info">
                        <div class="col-xs-2">
                            <div class="icon">
                                <img src="{#$NOW_PATH#}css/login_2/icon/01.png">
                            </div>
                            <h6>微现场</h6>
                        </div>
                        <div class="col-xs-2">
                            <div class="icon">
                                <img src="{#$NOW_PATH#}css/login_2/icon/02.png">
                            </div>
                            <h6>微旅游</h6>
                        </div>
                        <div class="col-xs-2">
                            <div class="icon">
                                <img src="{#$NOW_PATH#}css/login_2/icon/03.png">
                            </div>
                            <h6>微外卖</h6>
                        </div>
                        <div class="col-xs-2">
                            <div class="icon">
                                <img src="{#$NOW_PATH#}css/login_2/icon/04.png">
                            </div>
                            <h6>微商城</h6>
                        </div>
                        <div class="col-xs-2">
                            <div class="icon">
                                <img src="{#$NOW_PATH#}css/login_2/icon/05.png">
                            </div>
                            <h6>微汽车</h6>
                        </div>
                        <div class="col-xs-2">
                            <div class="icon">
                                <img src="{#$NOW_PATH#}css/login_2/icon/06.png">
                            </div>
                            <h6>微房产</h6>
                        </div>
                        <div class="col-xs-2">
                            <div class="icon">
                                <img src="{#$NOW_PATH#}css/login_2/icon/08.png">
                            </div>
                            <h6>微点菜</h6>
                        </div>
                        <div class="col-xs-2">
                            <div class="icon">
                                <img src="{#$NOW_PATH#}css/login_2/icon/09.png">
                            </div>
                            <h6>微喜帖</h6>
                        </div>
                        <div class="col-xs-2">
                            <div class="icon">
                                <img src="{#$NOW_PATH#}css/login_2/icon/11.png">
                            </div>
                            <h6>微网站</h6>
                        </div>
                        <div class="col-xs-2">
                            <div class="icon">
                                <img src="{#$NOW_PATH#}css/login_2/icon/12.png">
                            </div>
                            <h6>微投票</h6>
                        </div>
                        <div class="col-xs-2">
                            <div class="icon">
                                <img src="{#$NOW_PATH#}css/login_2/icon/13.png">
                            </div>
                            <h6>微信自定义菜单</h6>
                        </div>
                        <div class="col-xs-2">
                            <div class="icon">
                                <img src="{#$NOW_PATH#}css/login_2/icon/14.png">
                            </div>
                            <h6>微信会员卡</h6>
                        </div>
                        <div class="col-xs-2">
                            <div class="icon">
                                <img src="{#$NOW_PATH#}css/login_2/icon/15.png">
                            </div>
                            <h6>微信营销活动</h6>
                        </div>
                        <div class="col-xs-2">
                            <div class="icon">
                                <img src="{#$NOW_PATH#}css/login_2/icon/17.png">
                            </div>
                            <h6>微信优惠券活动</h6>
                        </div>
                        <div class="col-xs-2">
                            <div class="icon">
                                <img src="{#$NOW_PATH#}css/login_2/icon/18.png">
                            </div>
                            <h6>微信LBS位置回复</h6>
                        </div>
                        <div class="col-xs-2">
                            <div class="icon">
                                <img src="{#$NOW_PATH#}css/login_2/icon/19.png">
                            </div>
                            <h6>微相册</h6>
                        </div>
                        <div class="col-xs-2">
                            <div class="icon">
                                <img src="{#$NOW_PATH#}css/login_2/icon/20.png">
                            </div>
                            <h6>微订单</h6>
                        </div>
                        <div class="col-xs-2">
                            <div class="icon">
                                <img src="{#$NOW_PATH#}css/login_2/icon/21.png">
                            </div>
                            <h6>微统计</h6>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="img" style="width:100%; height:450px; border:#eee solid 1px;" id="baidumap"></div>
    </div>
    {#include file="footer.tpl"#}
</div>



<script type="text/javascript" src="http://api.map.baidu.com/api?v=1.4"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#loginForm').submit(function() {
            var retu = true;
            retu = $('#loginForm #username').inTips("请输入用户名/邮箱/手机号码", -1, retu);
            retu = $('#loginForm #userpass').inTips("请输入密码", -1, retu);
            if (!retu) return false;

            $.alert('正在登录...', 0);
            $(this).ajaxSubmit({
                dataType : 'json',
                success : function (data) {
                    $.alert(0);
                    if (data != null && data.success != null && data.success) {
                        window.location.href = '{#$urlarr.2#}';
                    } else {
                        $.showModal(data.message);
                    }
                },
                error : function () {
                    $.alert(0);
                    $.inModal("登录失败！");
                }
            });
            return false;
        });
        $(".seller-login").click(function(){
            var sigh = $(".main-sigh-in");
            sigh.find(".sign-in-mod").css({
                top: ($(window).height() - sigh.find(".sign-in-mod").outerHeight()) / 2.3
            });
            sigh.css({
                visibility: 'visible'
            });
            $(".sign-in-bg").show();
        });
        $(".sign-in-bg").click(function(){
            $(".main-sigh-in").css({
                visibility: 'hidden'
            });
            $(this).hide();
        });
        //banner
        $('#banner, #banner .item').height($(window).height());
    });
    $(window).resize(function(){
        //banner
        $('#banner, #banner .item').height($(window).height());
    });
    //
    var markerArr = [{title:"{#$tem['t2']['comp']#}",content:"公司地址：{#$tem['t2']['map']['title']#}<br/>联系电话：{#$tem['t2']['tel']#}",point:"{#$tem['t2']['map']['lat']#}|{#$tem['t2']['map']['lng']#}",isOpen:1,icon:{w:21,h:21,l:0,t:0,x:6,lb:5}}
    ];
    function initMap(){
        createMap();
        setMapEvent();
        addMapControl();
        addMarker();
    }
    function createMap(){
        var map = new BMap.Map("baidumap");
        var point = new BMap.Point({#$tem['t2']['map']['lat']#}, {#$tem['t2']['map']['lng']#});
        map.centerAndZoom(point,15);
        window.map = map;
    }
    function setMapEvent(){
        map.enableDragging();
        map.enableScrollWheelZoom();
        map.enableDoubleClickZoom();
        map.enableKeyboard();
    }
    function addMapControl(){
        var ctrl_nav = new BMap.NavigationControl({anchor:BMAP_ANCHOR_TOP_LEFT,type:BMAP_NAVIGATION_CONTROL_LARGE});
        map.addControl(ctrl_nav);
    }
    function addMarker(){
        for(var i=0;i<markerArr.length;i++){
            var json = markerArr[i];
            var p0 = json.point.split("|")[0];
            var p1 = json.point.split("|")[1];
            var point = new BMap.Point(p0,p1);
            var iconImg = createIcon(json.icon);
            var marker = new BMap.Marker(point,{icon:iconImg});
            var label = new BMap.Label(json.title,{"offset":new BMap.Size(json.icon.lb-json.icon.x+10,-20)});
            marker.setLabel(label);
            map.addOverlay(marker);
            label.setStyle({
                borderColor:"#808080",
                color:"#333",
                cursor:"pointer"
            });
            (function(){
                var _iw = createInfoWindow(i);
                var _marker = marker;
                _marker.addEventListener("click",function(){
                    this.openInfoWindow(_iw);
                });
                _iw.addEventListener("open",function(){
                    _marker.getLabel().hide();
                });
                _iw.addEventListener("close",function(){
                    _marker.getLabel().show();
                });
                label.addEventListener("click",function(){
                    _marker.openInfoWindow(_iw);
                });
                if(!!json.isOpen){
                    label.hide();
                    _marker.openInfoWindow(_iw);
                }
            })()
        }
    }
    function createInfoWindow(i){
        var json = markerArr[i];
        return new BMap.InfoWindow("<b class='iw_poi_title' title='" + json.title + "'>" + json.title + "</b><div class='iw_poi_content'>"+json.content+"</div>");
    }
    function createIcon(json){
        return new BMap.Icon("http://app.baidu.com/map/images/us_mk_icon.png", new BMap.Size(json.w,json.h),{imageOffset: new BMap.Size(-json.l,-json.t),infoWindowOffset:new BMap.Size(json.lb+5,1),offset:new BMap.Size(json.x,json.h)})
    }
    initMap();
</script>

</body>
</html>