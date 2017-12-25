
function loadListMore(obj){
    if($(obj).attr('class') == 'no') return;
    if (total < perpage){
        $.alert('全部加载完毕');
        $(obj).html('已全部加载');
        $(obj).addClass('no');
        return;
    }
    $(obj).html("加载中，请稍等...");
    if (pageNo < 1) pageNo = 1;
    pageNo = pageNo + 1;
    $.ajax({
        type : "GET",
        url : pageUrl + pageNo,
        success:function(datas){
            if (datas.indexOf('<ul class="list" id="ul-list">') > 0){
                var ntotal = pageNo*perpage; if (total < ntotal) ntotal = total;
                var ullisthtml = get_split(datas, "<!-- list start -->", "<!-- list end -->");
                //$('#ul-list').append('<li style="background:#e8e8e8; color:#06F; font-size:x-small; text-align:center; height:14px; line-height:14px; overflow:hidden; padding:0;">第'+pageNo+'页(' + ((pageNo-1)*perpage+1) + '-' + ntotal + ')</li>');
                $('#ul-list').append(ullisthtml);
                if (total <= pageNo * perpage){
                    $.alert('全部加载完毕');
                    $(obj).html('已全部加载');
                    $(obj).addClass('no');
                }else{
                    $(obj).html('查看更多▼');
                }
            } else {
                $(obj).html('暂无数据');
                $(obj).addClass('no');
            }

        }
        //errors:
    });
}
function get_split(content, s, e){
    var index1 = content.indexOf(s) + s.length;
    content = content.substring(index1, content.length);
    var index2 = content.indexOf(e);
    content = content.substring(0, index2);
    return content;
}
