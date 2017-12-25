function h2_al_name(i,j) {
    var name_arr = window._al_name_array;
    if (name_arr[i]) {
        var tthis = $("#_h2_al_name");
        tthis.animate({ "opacity" : 0 }, j?0:500, function(){
            tthis.css({"opacity":1}).html(" - " + name_arr[i]);
            setTimeout(function(){ h2_al_name(i?0:1) }, 3000);
        });
    }else{
        h2_al_name(i?0:1);
    }
}
$(document).ready(function() {
    h2_al_name(0,1);
    var head_nav_menu = $("#head-nav-menu");
    var moremenuobj = $("#moremenu");
    var moreeveobj = moremenuobj.find("li");
    var morenum = 5;
    var cln;
    moreeveobj.each(function(index) {
        if (moreeveobj.length > morenum){
            if (index < morenum - 1) {
                var cln = $(this).attr('class') ? $(this).attr('class') : '';
                moremenuobj.before("<li class='"+cln+"'>"+$(this).html()+"</li>");
                $(this).remove();
            }else if (index == 4) {
                var $imitate = $('<li id="nav-item-more"><a class="nav-item-link" href="javascript:;">更多...</a></li>');
                moremenuobj.before($imitate);
                $imitate.mousemove(function () {
                    moremenuobj.show();head_nav_menu.addClass("moremouse");
                    if (moreeveobj.length >= 50) {
                        moremenuobj.addClass("olnbox5");
                    }else if (moreeveobj.length >= 40) {
                        moremenuobj.addClass("olnbox4");
                    }else if (moreeveobj.length >= 30) {
                        moremenuobj.addClass("olnbox3");
                    }else if (moreeveobj.length >= 20) {
                        moremenuobj.addClass("olnbox2");
                    }else if (moreeveobj.length >= 2) {
                        moremenuobj.addClass("olnbox1");
                    }
                }).mouseout(function () {
                    moremenuobj.hide();head_nav_menu.removeClass("moremouse");
                });
                moremenuobj.mousemove(function () {
                    moremenuobj.show();head_nav_menu.addClass("moremouse");
                }).mouseout(function () {
                    moremenuobj.hide();head_nav_menu.removeClass("moremouse");
                })
            }
        }else{
            cln = $(this).attr('class') ? $(this).attr('class') : '';
            moremenuobj.before("<li class='"+cln+"'>"+$(this).html()+"</li>");
            $(this).remove();
        }
    });
    var nav_item_more = $("#nav-item-more");
    var nav_item_eve = moremenuobj.find("li.active");
    if (nav_item_eve.length > 0) {
        var _item_eve = nav_item_more.prev();
        cln = _item_eve.attr('class') ? _item_eve.attr('class') : '';
        moremenuobj.prepend("<li class='"+cln+"'>"+_item_eve.html()+"</li>");
        _item_eve.remove();
        //
        cln = nav_item_eve.attr('class') ? nav_item_eve.attr('class') : '';
        nav_item_more.before("<li class='"+cln+"'>"+nav_item_eve.html()+"</li>");
        nav_item_eve.remove();
    }
    if (moremenuobj.find(">li").length > 10) {
        var nav_search = $("<li class='fun-search'><label><input type='text' placeholder='输入名称搜索'></label></li>");
        nav_search.find("input").keyup(function(){
            var thisv = $(this).val();
            var thisg = $(this).parents("ol");
            thisg.find(".nav-item-title").each(function(){
                var thisa = $(this).text();
                if (thisv) {
                    thisa = thisa.replace(thisv, '<font color="red"><b>'+thisv+'</b></font>');
                }
                $(this).html(thisa);
            });
            thisg.find(".nav-item-title").css("background-color", "transparent");
            thisg.find(".nav-item-title").find("font").each(function(){
                $(this).parent().css("background-color", "#ffff00");
            });
        });
        moremenuobj.prepend(nav_search);
    }
    var emuobj = head_nav_menu.find(".nav-item-link[title='模拟测试']");
    if (!emuobj.parent().hasClass("active")) {
        emuobj.attr("target", "_blank");
    }
});