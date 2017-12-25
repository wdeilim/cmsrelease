$.__baidu_map_fun = {
    styleOnload: function (node, callback) {
        // for IE6-9 and Opera
        if (node.attachEvent) {
            node.attachEvent('onload', callback);
            // NOTICE:
            // 1. "onload" will be fired in IE6-9 when the file is 404, but in
            // this situation, Opera does nothing, so fallback to timeout.
            // 2. "onerror" doesn't fire in any browsers!
        }
        // polling for Firefox, Chrome, Safari
        else {
            setTimeout(function() {
                $.__baidu_map_fun.poll(node, callback);
            }, 0); // for cache
        }
    },
    poll: function (node, callback) {
        if (callback.isCalled) {
            return;
        }
        var isLoaded = false;
        if (/webkit/i.test(navigator.userAgent)) {//webkit
            if (node['sheet']) {
                isLoaded = true;
            }
        }
        // for Firefox
        else if (node['sheet']) {
            try {
                if (node['sheet'].cssRules) {
                    isLoaded = true;
                }
            } catch (ex) {
                // NS_ERROR_DOM_SECURITY_ERR
                if (ex.code === 1000) {
                    isLoaded = true;
                }
            }
        }
        if (isLoaded) {
            // give time to render.
            setTimeout(function() {
                callback();
            }, 1);
        }
        else {
            setTimeout(function() {
                $.__baidu_map_fun.poll(node, callback);
            }, 1);
        }
    },
    path: window['_jquery_aliedit'] || (function (script, i, me) {
        for (i in script) {
            if (script[i].src && script[i].src.indexOf('jquery.baidu_map') !== -1) me = script[i];
        };
        _thisScript = me || script[script.length - 1];
        me = _thisScript.src.replace(/\\/g, '/');
        return me.lastIndexOf('/') < 0 ? '.' : me.substring(0, me.lastIndexOf('/'));
    }(document.getElementsByTagName('script'))),
    alert: function(e, t, s) {
        $("div.jQuery-form-alert").remove();
        if (e === 0) return;
        $intemp = $('<div class="jQuery-form-alert" style="display:none;position:fixed;top:0;left:0;padding:3px 30px;min-width:100px;min-height:25px;text-align:center;color:#fff;z-index:2147483647;border-radius:0 0 4px 4px;background-color:#2378cd;font-size:14px; line-height:22px;">' + e + '</div>');
        $("body").append($intemp);
        var i = $(window).width(),
            o = $intemp.width()+20,
            l = (i - o) / 2;
        i > o && $intemp.css("left", parseInt(l)),
            i > o && $intemp.css("right", parseInt(l)),
            l < 5 && $intemp.css("margin", "0 5px");
        setTimeout(function() { $intemp.show(); }, s || 1);
        if (t === 0) return;
        setTimeout(function() { $intemp.fadeOut(); }, t || 2000)
    },
    show: function(paramet, callback){
        var tthis = this;
        tthis.close();
        if (!paramet.title || paramet.title == 'undefined') paramet.title = '';
        if (!paramet.lng || paramet.lng == 'undefined') paramet.lng = '';
        if (!paramet.lat || paramet.lat == 'undefined') paramet.lat = '';
        var intemp = $('<div class="jQuery-baidumap">' +
            '<div class="jQuery-baidumap-back"></div>' +
            '<div class="jQuery-baidumap-content">' +
            '<div class="baidumap-title"><em id="f-cancel">×</em><span class="baidumap-call"></span>设置地图</div>' +
            '<div class="baidumap-show">' +
            '地址: <input type="text" class="baidumap-control" id="__baidumap-title" style="width:300px" value="'+paramet.title+'">' +
            '经度: <input type="text" class="baidumap-control" id="__baidumap-lng" value="'+paramet.lng+'">' +
            '纬度: <input type="text" class="baidumap-control" id="__baidumap-lat" value="'+paramet.lat+'">' +
            '<button class="baidumap-but" type="button" id="__baidumap-but">搜索</button>' +
            '<div id="__baidumap-load" class="baidumap-load">正在加载...</div>' +
            '</div>' +
            '<div class="baidumap-button"><p class="hover" id="f-confirm">确定</p><p id="f-cancel">取消</p></div> ' +
            '</div>' +
            '</div>');
        //背景
        intemp.find(".jQuery-form-back").css({
            "height":$(document).height()
        }).click(function(){
            tthis.close();
        });
        //点击关闭 取消
        intemp.find("#f-cancel").click(function(){
            tthis.close();
        });
        //调整宽度
        var w = $(document).width() * 0.8;
        if (w > 880) w = 880;
        var wl = (w / 2) * -1;
        intemp.find(".jQuery-baidumap-content").css({width:w,marginLeft:wl});
        //点击确定
        intemp.find("#f-confirm").click(function(){
            if (callback) callback($("#__baidumap-title").val(), $("#__baidumap-lng").val(), $("#__baidumap-lat").val());
            tthis.close();
        });
        //显示
        $("body").append(intemp);
        //
        tthis.baidu_map(paramet, '__baidumap-load', '__baidumap-title', '__baidumap-lng', '__baidumap-lat', '__baidumap-but');

    },
    close: function() {
        $("div.jQuery-baidumap").remove();
    },
    baidu_map: function(data, showobjid, addrobjid, lngobjid, longobjid, butgobjid, hiderobjid) {
        /**
         * @param data
         * @param showobjid 显示地图的ID
         * @param addrobjid 地址输入框ID
         * @param lngobjid 坐标输入框ID
         * @param longobjid 坐标输入框ID
         * @param butgobjid 搜索按钮ID
         * @param hiderobjid 自动填写到地址输入框
         */
        if (!showobjid) showobjid = 'l-map';
        if (!addrobjid) addrobjid = 'suggestId';
        if (!lngobjid) lngobjid = 'form_latitude';
        if (!longobjid) longobjid = 'form_longitude';
        if (!butgobjid) butgobjid = 'positioning';
        var self = this;
        var opt = this.option;
        var map = new BMap.Map(showobjid);
        var myGeo = new BMap.Geocoder();
        //map.addControl(new BMap.MapTypeControl({mapTypes: [BMAP_NORMAL_MAP,BMAP_HYBRID_MAP]}));	 //2D图，卫星图

        //map.addControl(new BMap.MapTypeControl({anchor: BMAP_ANCHOR_TOP_LEFT}));	//左上角，默认地图控件

        //alert(city);
        var currentPoint;
        var marker1;
        var marker2;
        map.enableScrollWheelZoom();
        if (data && data.lng && data.lat) {
            var point = new BMap.Point(data.lat, data.lng);
            marker1 = new BMap.Marker(point);		// 创建标注
            map.addOverlay(marker1);
            var opts = {
                width: 220,	 // 信息窗口宽度 220-730
                height: 60,	 // 信息窗口高度 60-650
                title: ""  // 信息窗口标题
            };
            var infoWindow = new BMap.InfoWindow("原本位置 【" + data.title + "】 ， 移动地图修改位置!", opts);  // 创建信息窗口对象
            marker1.openInfoWindow(infoWindow);	  // 打开信息窗口
            doit(point);
        } else {
            if ($("#"+lngobjid).val() && $("#"+longobjid).val()){
                var point = new BMap.Point($("#"+longobjid).val(), $("#"+lngobjid).val());
            }else{
                var point = new BMap.Point(108.370929,22.833195);
            }
            doit(point);
            window.setTimeout(function () {
                // auto(); //自动定位
            }, 100);
        }
        map.enableDragging();
        map.enableContinuousZoom();
        map.addControl(new BMap.NavigationControl());
        map.addControl(new BMap.ScaleControl());
        map.addControl(new BMap.OverviewMapControl());

        function auto() {
            var geolocation = new BMap.Geolocation();
            geolocation.getCurrentPosition(function (r) {
                if (this.getStatus() == BMAP_STATUS_SUCCESS) {
                    //var mk = new BMap.Marker(r.point);
                    //map.addOverlay(mk);
                    // point = r.point;
                    //map.panTo(r.point);

                    var point = new BMap.Point(r.point.lng, r.point.lat);
                    marker1 = new BMap.Marker(point);		// 创建标注
                    map.addOverlay(marker1);
                    var opts = {
                        width: 220,	 // 信息窗口宽度 220-730
                        height: 60,	 // 信息窗口高度 60-650
                        title: ""  // 信息窗口标题
                    };

                    var infoWindow = new BMap.InfoWindow("定位成功这是你当前的位置!,移动红点标注目标位置，你也可以直接修改上方位置,系统自动定位!", opts);  // 创建信息窗口对象
                    marker1.openInfoWindow(infoWindow);	  // 打开信息窗口
                    doit(point);

                } else {
                    //alert('failed' + this.getStatus());
                }
            })
        }
        function doit(point) {
            if (point) {
                //window.external.setlngandlat(point.lng,point.lat);
                //alert(point.lng + "  ddd " + point.lat);

                $('#'+lngobjid).val(point.lat);
                $('#'+longobjid).val(point.lng);

                map.setCenter(point);
                map.centerAndZoom(point, 15);
                map.panTo(point);

                var cp = map.getCenter();
                myGeo.getLocation(point, function (result) {
                    if (result) {
                        if (!hiderobjid && result.address) $('#'+addrobjid).val(result.address);
                    }
                });

                marker2 = new BMap.Marker(point);		// 创建标注
                var opts = {
                    width: 220,	 // 信息窗口宽度 220-730
                    height: 60,	 // 信息窗口高度 60-650
                    title: ""  // 信息窗口标题
                };
                var infoWindow = new BMap.InfoWindow("拖拽地图或红点，在地图上用红点标注您的店铺位置。", opts);  // 创建信息窗口对象
                marker2.openInfoWindow(infoWindow);	  // 打开信息窗口

                map.addOverlay(marker2);					 // 将标注添加到地图中

                marker2.enableDragging();
                marker2.addEventListener("dragend", function (e) {
                    $('#'+lngobjid).val(e.point.lat);
                    $('#'+longobjid).val(e.point.lng);
                    myGeo.getLocation(new BMap.Point(e.point.lng, e.point.lat), function (result) {
                        if (result) {
                            if (!hiderobjid && result.address) $('#'+addrobjid).val(result.address);
                            marker2.setPosition(new BMap.Point(e.point.lng, e.point.lat));
                            map.panTo(new BMap.Point(e.point.lng, e.point.lat));
                        }
                    });
                });

                map.addEventListener("dragend", function showInfo() {
                    var cp = map.getCenter();
                    myGeo.getLocation(new BMap.Point(cp.lng, cp.lat), function (result) {
                        if (result) {
                            if (!hiderobjid && result.address) $('#'+addrobjid).val(result.address);
                            $('#'+lngobjid).val(cp.lat);
                            $('#'+longobjid).val(cp.lng);
                            marker2.setPosition(new BMap.Point(cp.lng, cp.lat));
                            map.panTo(new BMap.Point(cp.lng, cp.lat));
                        }
                    });
                });

                map.addEventListener("dragging", function showInfo() {
                    var cp = map.getCenter();
                    //marker1.setPoint(new BMap.Point(cp.lng,cp.lat));		// 移动标注
                    marker2.setPosition(new BMap.Point(cp.lng, cp.lat));
                    map.panTo(new BMap.Point(cp.lng, cp.lat));
                    map.centerAndZoom(marker2.getPosition(), map.getZoom());
                });
            }
        }

        function loadmap(val) {
            var city = (typeof(val) != 'undefined')?val:$('#'+addrobjid).val();
            // 将结果显示在地图上，并调整地图视野
            myGeo.getPoint(city, function (point) {
                if (point) {
                    marker2.setPosition(new BMap.Point(point.lng, point.lat));
                    $('#'+lngobjid).val(point.lat);
                    $('#'+longobjid).val(point.lng);
                    map.panTo(new BMap.Point(marker2.getPosition().lng, marker2.getPosition().lat));
                    map.centerAndZoom(marker2.getPosition(), map.getZoom());
                }else{
                    if (typeof(val) == 'undefined') {
                        var cp = map.getCenter();
                        myGeo.getLocation(new BMap.Point(cp.lng, cp.lat), function (result) {
                            if (result) {
                                var addressC = result.addressComponents;
                                loadmap(addressC.province + addressC.city + city);
                            }
                        });
                    }
                }
            });
        }

        function initarreawithpoint(lng, lat) {
            window.setTimeout(function () {
                //marker1.setPoint(new BMap.Point(lng,lat));		// 移动标注
                marker2.setPosition(new BMap.Point(lng, lat));
                //window.external.setlngandlat(lng,lat);
                map.panTo(new BMap.Point(lng, lat));
                map.centerAndZoom(marker2.getPosition(), map.getZoom());
            }, 2000);
        }

        $("#"+addrobjid).change(function () {
            loadmap();
        });

        $("#"+butgobjid).click(function () {
            loadmap();
        });
    }
};

$.fn.baidu_map = function(paramet) {
    var tthis = this;
    var node = document.createElement("link");
    node.setAttribute("rel", "stylesheet");
    node.setAttribute("type", "text/css");
    node.setAttribute("href", $.__baidu_map_fun.path+'/skins/default.css');
    document.getElementsByTagName('head')[0].appendChild(node);
    $.__baidu_map_fun.styleOnload(node, function(){
        var tname = tthis.attr("name");
        if (!tname) return false;
        if (!tthis.attr("placeholder")) tthis.attr("placeholder", "点击打开地图标注");
        var m = Math.round(Math.random() * 10000);
        if (!paramet.title || paramet.title == 'undefined') paramet.title = '';
        if (!paramet.lng || paramet.lng == 'undefined') paramet.lng = '';
        if (!paramet.lat || paramet.lat == 'undefined') paramet.lat = '';
        tthis.attr('name', tname + '[title]').val(paramet.title);
        tthis.after('<input type="hidden" name="'+tname+'[lng]" id="map_'+m+'_lng" value="'+paramet.lng+'" placeholder="地理经度">');
        tthis.after('<input type="hidden" name="'+tname+'[lat]" id="map_'+m+'_lat" value="'+paramet.lat+'" placeholder="地理纬度">');
        tthis.click(function(){
            $.__baidu_map_fun.show(paramet, function(title,lng,lat){
                paramet.title = title;
                paramet.lng = lng;
                paramet.lat = lat;
                tthis.val(title);
                $('#map_'+m+'_lng').val(lng);
                $('#map_'+m+'_lat').val(lat);
            });
        });
    });

};