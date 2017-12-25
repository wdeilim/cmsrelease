/**
 *
 * @param data
 * @param showobjid 显示地图的ID
 * @param addrobjid 地址输入框ID
 * @param lngobjid 坐标输入框ID
 * @param longobjid 坐标输入框ID
 * @param butgobjid 搜索按钮ID
 * @param hiderobjid 自动填写到地址输入框
 */
function baidu_map(data, showobjid, addrobjid, lngobjid, longobjid, butgobjid, hiderobjid) {
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
	if (data) {
		var point = new BMap.Point(data.lng, data.lat);
		marker1 = new BMap.Marker(point);		// 创建标注
		map.addOverlay(marker1);
		var opts = {
			width: 220,	 // 信息窗口宽度 220-730
			height: 60,	 // 信息窗口高度 60-650
			title: ""  // 信息窗口标题
		}
		var infoWindow = new BMap.InfoWindow("原本位置 " + data.adr + " ,移动红点修改位置!你也可以直接修改上方位置系统自动定位!", opts);  // 创建信息窗口对象
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
				}

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
                    if (!hiderobjid) $('#'+addrobjid).val(result.address);
				}
			});

			marker2 = new BMap.Marker(point);		// 创建标注  
			var opts = {
				width: 220,	 // 信息窗口宽度 220-730
				height: 60,	 // 信息窗口高度 60-650
				title: ""  // 信息窗口标题
			}
			var infoWindow = new BMap.InfoWindow("拖拽地图或红点，在地图上用红点标注您的店铺位置。", opts);  // 创建信息窗口对象
			marker2.openInfoWindow(infoWindow);	  // 打开信息窗口

			map.addOverlay(marker2);					 // 将标注添加到地图中

			marker2.enableDragging();
			marker2.addEventListener("dragend", function (e) {
				$('#'+lngobjid).val(e.point.lat);
				$('#'+longobjid).val(e.point.lng);
				myGeo.getLocation(new BMap.Point(e.point.lng, e.point.lat), function (result) {
					if (result) {
                        if (!hiderobjid) $('#'+addrobjid).val(result.address);
						marker2.setPoint(new BMap.Point(e.point.lng, e.point.lat));
						map.panTo(new BMap.Point(e.point.lng, e.point.lat));
					}
				});
			});

			map.addEventListener("dragend", function showInfo() {
				var cp = map.getCenter();
				myGeo.getLocation(new BMap.Point(cp.lng, cp.lat), function (result) {
					if (result) {
                        if (!hiderobjid) $('#'+addrobjid).val(result.address);
						$('#'+lngobjid).val(cp.lat);
						$('#'+longobjid).val(cp.lng);
						marker2.setPoint(new BMap.Point(cp.lng, cp.lat));
						map.panTo(new BMap.Point(cp.lng, cp.lat));
					}
				});
			});

			map.addEventListener("dragging", function showInfo() {
				var cp = map.getCenter();
				//marker1.setPoint(new BMap.Point(cp.lng,cp.lat));		// 移动标注
				marker2.setPoint(new BMap.Point(cp.lng, cp.lat));
				map.panTo(new BMap.Point(cp.lng, cp.lat));
				map.centerAndZoom(marker2.getPoint(), map.getZoom());
			});
		}
	}

	function loadmap() {
		var city = $('#'+addrobjid).val();
		var myCity = new BMap.LocalCity();
		// 将结果显示在地图上，并调整地图视野  
		myGeo.getPoint(city, function (point) {
			if (point) {
				marker2.setPoint(new BMap.Point(point.lng, point.lat));
				$('#'+lngobjid).val(point.lat);
				$('#'+longobjid).val(point.lng);
				map.panTo(new BMap.Point(marker2.getPoint().lng, marker2.getPoint().lat));
				map.centerAndZoom(marker2.getPoint(), map.getZoom());
			}
		});
	}

	function initarreawithpoint(lng, lat) {
		window.setTimeout(function () {
			//marker1.setPoint(new BMap.Point(lng,lat));		// 移动标注
			marker2.setPoint(new BMap.Point(lng, lat));
			//window.external.setlngandlat(lng,lat);
			map.panTo(new BMap.Point(lng, lat));
			map.centerAndZoom(marker2.getPoint(), map.getZoom());
		}, 2000);
	}

	$("#"+addrobjid).change(function () {
		loadmap();
	});

	$("#"+butgobjid).click(function () {
		loadmap();
	});

}