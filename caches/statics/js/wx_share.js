if (typeof(wx_shareData) != "undefined" && typeof(wx_jssdk) != "undefined" && wx_jssdk) {
	if (typeof(wx) != "undefined") wx = null;
	$.getScript("http://res.wx.qq.com/open/js/jweixin-1.0.0.js", function(){
		if (wx_shareData.imgUrl && (wx_shareData.imgUrl.substring(0,1) == '.' || wx_shareData.imgUrl.substring(0,1) == '#')) {
			if ($(wx_shareData.imgUrl).find("img:eq(0)").attr("src")) {
				wx_shareData.imgUrl = $(wx_shareData.imgUrl).find("img:eq(0)").attr("src");
			}
		}
		if (!wx_shareData.link) {
			wx_shareData.link = document.URL;
		}
		if (!wx_shareData.title) {
			wx_shareData.title = $("title:eq(0)").text();
		}
		if (!wx_shareData.desc) {
			wx_shareData.desc = $("body").text().trim();
		}
		if (wx_shareData.desc && (wx_shareData.desc.substring(0,1) == '.' || wx_shareData.desc.substring(0,1) == '#')) {
			if ($(wx_shareData.desc).text()) {
				wx_shareData.desc = $(wx_shareData.desc).text().replace(/^\s+|\s+$/g,"");
			}
		}
		// 是否启用调试
		wx_jssdk.debug = false;
		//
		wx_jssdk.jsApiList = [
			'checkJsApi',
			'onMenuShareTimeline',
			'onMenuShareAppMessage',
			'onMenuShareQQ',
			'onMenuShareWeibo'
		];
		wx.config(wx_jssdk);
		wx.ready(function () {
			wx.onMenuShareAppMessage(wx_shareData);
			wx.onMenuShareTimeline(wx_shareData);
			wx.onMenuShareQQ(wx_shareData);
			wx.onMenuShareWeibo(wx_shareData);
		});
	});
}