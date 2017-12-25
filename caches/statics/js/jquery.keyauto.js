$.fn.keyauto = function(trigger, classname) {
    if (this.length <= 0) {
        return false;
    }
    this.each(function(){
        var tthis = $(this);
        if (trigger === 0) {
            var temtext = '', teml = '';
            tthis.next("div").find(">div>span").find("em").each(function(){
                temtext+= teml + $(this).text();
                teml = ',';
            });
            tthis.val(temtext);
            return false;
        }
        if (trigger !== true) {
            var intemp = $('<div><div><span></span><input type="text" value="' + tthis.val() + '"></div></div>');
            if (classname) {
                intemp.addClass(classname);
            }
            intemp.css({
                'border': '1px solid #aaa',
                'width': tthis.outerWidth() - 2,
                'height': tthis.outerHeight() - 2,
                'padding': 0,
                'margin': tthis.css("margin"),
                'display': tthis.css("display"),
                'overflow': 'hidden',
                'vertical-align': 'middle',
                '-webkit-box-sizing': 'content-box',
                '-moz-box-sizing': 'content-box',
                'box-sizing': 'content-box'
            });
            intemp.find(">div").css({
                'display': '-webkit-box',
                '-webkit-box-sizing': 'content-box',
                '-moz-box-sizing': 'content-box',
                'box-sizing': 'content-box'
            });
            intemp.find(">div>span").css({
                'display': 'block',
                'height': tthis.outerHeight() - 2,
                'max-width': tthis.outerWidth() - 20,
                'overflow': 'hidden',
                'position': 'relative',
                '-webkit-box-sizing': 'content-box',
                '-moz-box-sizing': 'content-box',
                'box-sizing': 'content-box'
            });
            intemp.find(">div>input").css({
                'border': 0,
                'padding': tthis.css("padding"),
                'margin': 0,
                'width': tthis.width(),
                'height': tthis.height(),
                'line-height': tthis.css("line-height"),
                '-webkit-box-flex': '1',
                'display': 'block',
                'outline': 'none',
                '-webkit-box-sizing': 'content-box',
                '-moz-box-sizing': 'content-box',
                'box-sizing': 'content-box'
            }).keyup(function(event){
                if (event.keyCode == 13 || event.keyCode == 188) {
                    tthis.val(tthis.val() + ',' + $(this).val());
                    tthis.keyauto(true);
                    return false;
                }
                if (event.keyCode == 32 && $(this).val().substr(-1) == ' ') {
                    tthis.val(tthis.val() + ',' + $(this).val());
                    tthis.keyauto(true);
                    return false;
                }
            }).blur(function(){
                tthis.val(tthis.val() + ',' + $(this).val());
                tthis.keyauto(true);
            }).keypress(function(event) {
                if (event.keyCode == 13) {
                    if ($(this).val() != '') {
                        event.preventDefault();
                    }
                }
            });
            if (tthis.attr("placeholder")) {
                intemp.find(">div>input").attr("placeholder", tthis.attr("placeholder"));
            }
            tthis.after(intemp).hide();
        }
        //
        var tspan = tthis.next("div").find(">div>span");
        tspan.html("");
        tspan.next("input").val("");
        var text = tthis.val() + "".trim();
        text = text.replace(/，/g, ',');
        text = text.replace(/ /g, ',');
        var arr = text.split(",");
        var result = [], hash = {};
        for (var j = 0, elem; (elem = arr[j]) != null; j++) {
            if (!hash[elem]) {
                result.push(elem);
                hash[elem] = true;
            }
        }
        for(var i =0 ;i<result.length;i++) {
            if(result[i] != null && result[i].length > 0) {
                var str = result[i] + "".trim();
                var emtemp = $('<em>'+str+'<i></i></em>');
                emtemp.css({
                    'display': 'block',
                    'float': 'left',
                    'height': tspan.height() + 'px',
                    'line-height': tspan.height() + 'px',
                    'font-style': 'normal',
                    'padding': '0px 20px 0 5px',
                    'margin-right': '3px',
                    'background-color': '#eee',
                    'position': 'relative',
                    '-webkit-box-sizing': 'content-box',
                    '-moz-box-sizing': 'content-box',
                    'box-sizing': 'content-box'
                });
                emtemp.find('i').css({
                    'background': 'url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAoAAAAKBAMAAAB/HNKOAAAAA3NCSVQICAjb4U/gAAAAFVBMVEX///+jo6Ojo6Ojo6Ojo6Ojo6Ojo6OGL6m0AAAAB3RSTlMAESK7zN3/bb21YgAAAAlwSFlzAAALEgAACxIB0t1+/AAAABx0RVh0U29mdHdhcmUAQWRvYmUgRmlyZXdvcmtzIENTNui8sowAAABCSURBVAiZYzAWYGA0ZnBzZBBJYVBNFQwLYmAKC01VYGBQTQtiYGAQTQtkYGAEiggwiKYqhQUyuAUxqKYwmAB1OQMAG60JojuO45cAAAAASUVORK5CYII=) no-repeat center',
                    'position': 'absolute',
                    'top': 0,
                    'right': 3,
                    'width': 15,
                    'height': 15,
                    'cursor': 'pointer',
                    '-webkit-box-sizing': 'content-box',
                    '-moz-box-sizing': 'content-box',
                    'box-sizing': 'content-box'
                }).click(function(){
                    $(this).parents("em").remove();
                    tthis.keyauto(0);
                });
                tspan.append(emtemp)
            }
        }
        tthis.keyauto(0);
    });
    return false;
};

