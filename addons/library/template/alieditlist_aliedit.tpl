<script type="text/javascript" src="{#$JS_PATH#}masonry-docs.min.js"></script>
<div class="formfile-content">
    {#ddb_pc set="数据表:library,列表名:lists,显示数目:10,分页显示:1,分页名:pagelist,当前页:{#$page#},分页地址:(fun)$.__aliedit_fun.__libraryurlpage(null\,(?));,排序:indate desc" where="{#$wheresql#}"#}
    <div style="height:35px;margin-top:-8px;margin-bottom:8px;">
        <div class="formfile-form">
            <input type="text" id='formfile-keyval' class="form-control" value="{#$keyval#}" data-val="{#$keyval#}" placeholder="输入搜索关键字" />&nbsp;
            <button class="button" onclick="$.__aliedit_fun.__libraryurlpage(null, 0, $('#formfile-keyval').val());">搜索</button>&nbsp;
            <a class="button" href="{#weburl('library/onlyimg')#}" target="_blank">+添加单图文素材</a>&nbsp;
            <a class="button" href="{#weburl('library/manyimg')#}" target="_blank">+新建多图文素材</a>
        </div>
        <div id="pagelist" style="margin-top:2px">{#$pagelist#}</div>
    </div>
    <div class="appmsg-list cf" id="appmsg-list" style="max-width:100%;">
        {#foreach from=$lists item=list#}
            {#if $list.type=='manyimg'#}
                <div id="appmsg-col-{#$list.id#}" class="appmsg-col" data-id="{#$list.id#}">
                    <div class="appmsg multi">
                        <div class="appmsg-content">
                            <div class="appmsg-info">
                                <em class="appmsg-date">{#$list.indate|date_format_cn:"%Y年%m月%d"#}</em>
                            </div>
                            {#foreach from=string2array($list.setting) item=list2 name=foo#}
                                {#if $smarty.foreach.foo.first#}
                                    <div class="cover-appmsg-item appmsg-showy" onclick="$.__aliedit_fun.__libraryurlpage(this);">
                                        <h4 class="appmsg-title"><a href="javascript:void(0)"
                                                                    data-href="{#appurl(0,'library',$list.id,$smarty.foreach.foo.index)#}"
                                                                    target="_blank">{#$list2.title#}</a></h4>
                                        <div class="appmsg-thumb-wrap">
                                            <img src="{#$list2.img|fillurl#}" alt="{#$list2.title|fillurl#}" class="appmsg-thumb">
                                        </div>
                                    </div>
                                {#else#}
                                    <div class="appmsg-item cf appmsg-showy" onclick="$.__aliedit_fun.__libraryurlpage(this);">
                                        <img src="{#$list2.img|fillurl#}" alt="{#$list2.title|fillurl#}" class="appmsg-thumb">
                                        <h4 class="appmsg-title"><a href="javascript:void(0)"
                                                                    data-href="{#appurl(0,'library',$list.id,$smarty.foreach.foo.index)#}"
                                                                    target="_blank">{#$list2.title#}</a></h4>
                                    </div>
                                {#/if#}
                            {#/foreach#}
                        </div>
                    </div>
                </div>
            {#else#}
                <div id="appmsg-col-{#$list.id#}" class="appmsg-col" data-id="{#$list.id#}">
                    <div class="appmsg appmsg-showy" onclick="$.__aliedit_fun.__libraryurlpage(this);">
                        <div class="appmsg-content">
                            <h4 class="appmsg-title"><a href="javascript:void(0)"
                                                        data-href="{#appurl(0,'library',$list.id)#}" target="_blank">{#$list.title#}</a></h4>
                            <div class="appmsg-info">
                                <em class="appmsg-date">{#$list.indate|date_format_cn:"%Y年%m月%d"#}</em>
                            </div>
                            <div class="appmsg-thumb-wrap">
                                <img class="appmsg-thumb" src="{#$list.img|fillurl#}" alt="{#$list.title#}">
                            </div>
                            <p class="appmsg-desc">{#$list.descriptions#}</p>
                        </div>
                    </div>
                </div>
            {#/if#}

            {#foreachelse#}
            <div style="text-align: center; margin: 30px;"> 无 </div>
        {#/foreach#}

    </div>
</div>
<script type="text/javascript">
    {#if $lists#}
    $(document).ready(function () {
        $('#appmsg-list').masonry({
            itemSelector: '.appmsg-col',
            gutterWidth: 20,
            isAnimated: true,
        });
    });
    {#/if#}
</script>
