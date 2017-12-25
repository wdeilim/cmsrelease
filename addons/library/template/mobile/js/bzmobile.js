/**
 * Created with JetBrains WebStorm.
 * User: haibinzhb
 * Date: 13-10-31
 * Time: 9:36
 * To change this template use File | Settings | File Templates.
 */
$(function () {
    var getImgViewer = function (imgSrc) {
        var $imgViewer = $("#imgViewer");
        if (!$imgViewer.length) {
            $imgViewer = $("<div id='imgViewer'><div></div></div>")
                .css({
                    "position": "fixed",
                    "z-index": "999",
                    "top": "0",
                    "bottom": "0",
                    "left": "0",
                    "right": "0",
                    "background": "black"
                })
                .on("tap", function () {
                    $(this).hide();
                })
                .prependTo("body");

            var $wrapper = $imgViewer.children("div");
            $wrapper.attr("style", "display: -webkit-box;" +
                "display: -webkit-flex;" +
                "display: flex;" +
                "-webkit-box-align: center;" +
                "-webkit-align-items: center;" +
                "align-items: center;" +
                "-webkit-box-pack: center;" +
                "-webkit-justify-content: center;" +
                "justify-content: center;height:100%;");
            var $img = $("<img>")
                .css({
                    "border": "none",
                    "max-width": "100%",
                    "max-height": "100%"
                })
                .appendTo($wrapper);
            var scroller = new iScroll($imgViewer[0], {zoom: true});
            $img[0].onload = function () {
                scroller.refresh();
            }
        }
        $imgViewer.find("img").attr("src", imgSrc);
        return $imgViewer;
    }
    $("div[bz-page-type=page]").delegate("[bz-widget-type=image]", "tap", function () {
        var $t = $(this);
        if (!$t.find("a").length) {
            var imgSrc = $t.find("img").attr("src");
            var $imgViewer = getImgViewer(imgSrc);
            $imgViewer.show();
        }
    });
});