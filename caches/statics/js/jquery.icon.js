jQuery.icon = function(eve, val, obj) {
    $("div.jQuery-ui-icon").remove();
    if (eve === 0) return;
    var m = Math.round(Math.random() * 10000);
    var n = '<div class="jQuery-ui-icon" id="jQuery-ui-icon-' + m + '">'+
        '	<div class="tab-content">'+
        '		<ul class="nav-icon">'+
        '			<li class="active"><a href="javascript:;" data-toggle="host">热门</a></li>'+
        '			<li class=""><a href="javascript:;" data-toggle="all">全部</a></li>'+
        '		</ul>'+
        '		<div class="tab-pane" id="ico-host">' +
        '           <ul class="icon_list" id="ico_hot"></ul> '+
        '		</div>'+
        '		<div class="tab-pane" id="ico-all" style="display:none;">' +
        '           <ul class="icon_list" id="ico_all"></ul> '+
        '		</div>'+
        '	</div>'+
        '</div>';
    $("body").append(n);
    var nobj = $('#jQuery-ui-icon-' + m);

    var list = ["&#xe626;", "&#xe752;", "&#xe627;", "&#xe64a;", "&#xe64b;", "&#xe64c;", "&#xe622;", "&#xe64d;", "&#xe6ef;", "&#xe64e;", "&#xe64f;", "&#xe650;", "&#xe651;", "&#xe6be;", "&#xe6f0;", "&#xe623;", "&#xe6bf;", "&#xe652;", "&#xe653;", "&#xe654;", "&#xe634;", "&#xe6c0;", "&#xe617;", "&#xe618;", "&#xe635;", "&#xe619;", "&#xe61a;", "&#xe61b;", "&#xe6f1;", "&#xe636;", "&#xe655;", "&#xe6f2;", "&#xe656;", "&#xe6c1;", "&#xe6f3;", "&#xe6f4;", "&#xe6f5;", "&#xe6f6;", "&#xe61c;", "&#xe6af;", "&#xe639;", "&#xe63a;", "&#xe63b;", "&#xe6f7;", "&#xe63c;", "&#xe601;", "&#xe61d;", "&#xe69d;", "&#xe686;", "&#xe615;", "&#xe657;", "&#xe687;", "&#xe6c2;", "&#xe6c3;", "&#xe628;", "&#xe6f8;", "&#xe6f9;", "&#xe6fa;", "&#xe66e;", "&#xe6fb;", "&#xe6fc;", "&#xe6fd;", "&#xe624;", "&#xe629;", "&#xe6fe;", "&#xe604;", "&#xe6c4;", "&#xe600;", "&#xe602;", "&#xe688;", "&#xe689;", "&#xe68a;", "&#xe68b;", "&#xe658;", "&#xe659;", "&#xe65a;", "&#xe65b;", "&#xe68c;", "&#xe68d;", "&#xe6c5;", "&#xe6ff;", "&#xe700;", "&#xe68e;", "&#xe68f;", "&#xe642;", "&#xe690;", "&#xe691;", "&#xe692;", "&#xe693;", "&#xe694;", "&#xe695;", "&#xe696;", "&#xe643;", "&#xe62f;", "&#xe65c;", "&#xe701;", "&#xe702;", "&#xe703;", "&#xe704;", "&#xe605;", "&#xe705;", "&#xe65d;", "&#xe697;", "&#xe63d;", "&#xe706;", "&#xe644;", "&#xe6c6;", "&#xe6c7;", "&#xe698;", "&#xe699;", "&#xe645;", "&#xe683;", "&#xe69e;", "&#xe66f;", "&#xe670;", "&#xe707;", "&#xe708;", "&#xe709;", "&#xe70a;", "&#xe671;", "&#xe70b;", "&#xe70c;", "&#xe70d;", "&#xe70e;", "&#xe70f;", "&#xe672;"];

    var list_all = ["&#xe626;", "&#xe752;", "&#xe627;", "&#xe64a;", "&#xe64b;", "&#xe64c;", "&#xe622;", "&#xe64d;", "&#xe6ef;", "&#xe64e;", "&#xe64f;", "&#xe650;", "&#xe651;", "&#xe6be;", "&#xe6f0;", "&#xe623;", "&#xe6bf;", "&#xe652;", "&#xe653;", "&#xe654;", "&#xe634;", "&#xe6c0;", "&#xe617;", "&#xe618;", "&#xe635;", "&#xe619;", "&#xe61a;", "&#xe61b;", "&#xe6f1;", "&#xe636;", "&#xe655;", "&#xe6f2;", "&#xe656;", "&#xe6c1;", "&#xe6f3;", "&#xe6f4;", "&#xe6f5;", "&#xe6f6;", "&#xe61c;", "&#xe6af;", "&#xe639;", "&#xe63a;", "&#xe63b;", "&#xe6f7;", "&#xe63c;", "&#xe601;", "&#xe61d;", "&#xe69d;", "&#xe686;", "&#xe615;", "&#xe657;", "&#xe687;", "&#xe6c2;", "&#xe6c3;", "&#xe628;", "&#xe6f8;", "&#xe6f9;", "&#xe6fa;", "&#xe66e;", "&#xe6fb;", "&#xe6fc;", "&#xe6fd;", "&#xe624;", "&#xe629;", "&#xe6fe;", "&#xe604;", "&#xe6c4;", "&#xe600;", "&#xe602;", "&#xe688;", "&#xe689;", "&#xe68a;", "&#xe68b;", "&#xe658;", "&#xe659;", "&#xe65a;", "&#xe65b;", "&#xe68c;", "&#xe68d;", "&#xe6c5;", "&#xe6ff;", "&#xe700;", "&#xe68e;", "&#xe68f;", "&#xe642;", "&#xe690;", "&#xe691;", "&#xe692;", "&#xe693;", "&#xe694;", "&#xe695;", "&#xe696;", "&#xe643;", "&#xe62f;", "&#xe65c;", "&#xe701;", "&#xe702;", "&#xe703;", "&#xe704;", "&#xe605;", "&#xe705;", "&#xe65d;", "&#xe697;", "&#xe63d;", "&#xe706;", "&#xe644;", "&#xe6c6;", "&#xe6c7;", "&#xe698;", "&#xe699;", "&#xe645;", "&#xe683;", "&#xe69e;", "&#xe66f;", "&#xe670;", "&#xe707;", "&#xe708;", "&#xe709;", "&#xe70a;", "&#xe671;", "&#xe70b;", "&#xe70c;", "&#xe70d;", "&#xe70e;", "&#xe70f;", "&#xe672;", "&#xe6c8;", "&#xe710;", "&#xe711;", "&#xe712;", "&#xe673;", "&#xe606;", "&#xe713;", "&#xe714;", "&#xe63e;", "&#xe6b0;", "&#xe6b9;", "&#xe715;", "&#xe6ba;", "&#xe716;", "&#xe6b1;", "&#xe6b2;", "&#xe6b3;", "&#xe6b4;", "&#xe607;", "&#xe674;", "&#xe717;", "&#xe718;", "&#xe608;", "&#xe69a;", "&#xe609;", "&#xe6e2;", "&#xe6e3;", "&#xe719;", "&#xe71a;", "&#xe6c9;", "&#xe675;", "&#xe60a;", "&#xe71b;", "&#xe676;", "&#xe71c;", "&#xe637;", "&#xe677;", "&#xe71d;", "&#xe60b;", "&#xe6bb;", "&#xe71e;", "&#xe71f;", "&#xe720;", "&#xe721;", "&#xe638;", "&#xe722;", "&#xe684;", "&#xe723;", "&#xe63f;", "&#xe631;", "&#xe678;", "&#xe6ca;", "&#xe6cb;", "&#xe679;", "&#xe724;", "&#xe6cc;", "&#xe6b5;", "&#xe725;", "&#xe726;", "&#xe727;", "&#xe728;", "&#xe729;", "&#xe6cd;", "&#xe62a;", "&#xe6ce;", "&#xe69b;", "&#xe6cf;", "&#xe65e;", "&#xe72a;", "&#xe72b;", "&#xe630;", "&#xe640;", "&#xe69f;", "&#xe62e;", "&#xe6a0;", "&#xe65f;", "&#xe6a1;", "&#xe660;", "&#xe661;", "&#xe662;", "&#xe6a2;", "&#xe6a3;", "&#xe663;", "&#xe6a4;", "&#xe664;", "&#xe665;", "&#xe6a5;", "&#xe6a6;", "&#xe666;", "&#xe6a7;", "&#xe6a8;", "&#xe6a9;", "&#xe6d0;", "&#xe72c;", "&#xe69c;", "&#xe6d1;", "&#xe72d;", "&#xe632;", "&#xe72e;", "&#xe6d2;", "&#xe6d3;", "&#xe67e;", "&#xe60c;", "&#xe72f;", "&#xe6aa;", "&#xe667;", "&#xe668;", "&#xe6ab;", "&#xe669;", "&#xe66a;", "&#xe6ac;", "&#xe6ad;", "&#xe66b;", "&#xe66c;", "&#xe66d;", "&#xe6d4;", "&#xe6e4;", "&#xe61e;", "&#xe61f;", "&#xe6e5;", "&#xe6e6;", "&#xe6d5;", "&#xe62b;", "&#xe6e7;", "&#xe6e8;", "&#xe620;", "&#xe730;", "&#xe731;", "&#xe732;", "&#xe67a;", "&#xe67b;", "&#xe621;", "&#xe60d;", "&#xe733;", "&#xe6e9;", "&#xe6ea;", "&#xe6eb;", "&#xe6d6;", "&#xe734;", "&#xe735;", "&#xe736;", "&#xe6b6;", "&#xe6b7;", "&#xe6ae;", "&#xe6d7;", "&#xe6d8;", "&#xe6ec;", "&#xe60e;", "&#xe6d9;", "&#xe633;", "&#xe67c;", "&#xe6b8;", "&#xe737;", "&#xe6ed;", "&#xe67d;", "&#xe60f;", "&#xe738;", "&#xe6bc;", "&#xe616;", "&#xe646;", "&#xe647;", "&#xe648;", "&#xe649;", "&#xe739;", "&#xe610;", "&#xe611;", "&#xe73a;", "&#xe73b;", "&#xe73c;", "&#xe685;", "&#xe612;", "&#xe6da;", "&#xe6db;", "&#xe6bd;", "&#xe67f;", "&#xe680;", "&#xe73d;", "&#xe73e;", "&#xe6dc;", "&#xe73f;", "&#xe641;", "&#xe740;", "&#xe741;", "&#xe742;", "&#xe743;", "&#xe744;", "&#xe6dd;", "&#xe745;", "&#xe746;", "&#xe6de;", "&#xe747;", "&#xe748;", "&#xe625;", "&#xe749;", "&#xe74a;", "&#xe74b;", "&#xe6ee;", "&#xe74c;", "&#xe603;", "&#xe74d;", "&#xe681;", "&#xe682;", "&#xe613;", "&#xe6df;", "&#xe614;", "&#xe62c;", "&#xe62d;", "&#xe6e0;", "&#xe74e;", "&#xe74f;", "&#xe750;", "&#xe751;", "&#xe6e1;"];

    var s = '';
    var s2 = '';
    $.each(list, function (k, v) {
        s += '<li class="tile-themed"><i class="iconfont">'+v+'</i></li>';
    });
    $.each(list_all, function (k, v) {
        s2 += '<li class="tile-themed"><i class="iconfont">'+v+'</i></li>';
    })
    nobj.find("#ico_hot").html(s);
    nobj.find("#ico_all").html(s2);
    nobj.find("#icon_i").click(function () {
        nobj.toggle();
    });
    nobj.find(".icon_list").find("li").click(function () {
        var $thisicon = $(this).children().html();
        if (eve instanceof jQuery) eve.text($thisicon);
        if (val instanceof jQuery) val.val($thisicon);
        nobj.find(".icons-cont").hide();
    });
    nobj.find(".nav-icon").find("a").click(function () {
        nobj.find(".nav-icon").find("li").toggleClass("active");
        nobj.find(".tab-pane").hide();
        nobj.find("#ico-"+$(this).attr("data-toggle")).show();
    });

    if (obj){
        $imithis = $(obj);
        var ttop  = $imithis.offset().top;     		//控件的定位点高
        var thei  = $imithis.outerHeight();  		//控件本身的高
        var twid  = $imithis.outerWidth();  		//控件本身的宽
        var tleft = $imithis.offset().left;    		//控件的定位点宽
        nobj.css("left", twid+tleft);
        nobj.css("top", ttop+thei-nobj.outerHeight());
    }else{
        var i = $(window).width(),
            s = $(window).height(),
            o = nobj.width(),
            u = nobj.height(),
            l = (i - o) / 2;
        i > o && nobj.css("left", l),
            s > u && nobj.css("top", (s - u) / 2),
            l < 5 && nobj.css("margin", "0 5px");
        nobj.show();
    }


    nobj.hover(function () {

    }, function () {
        nobj.fadeOut();
    });
};
