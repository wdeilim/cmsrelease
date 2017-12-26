<div class="formfile-content">
    {#ddb_pc set="数据表:reply,列表名:lists,显示数目:10,分页显示:1,分页名:pagelist,当前页:{#$page#},分页地址:(fun)__replypage(null\,(?));,排序:indate desc" where="{#$wheresql#}"#}
    <div style="height:35px;margin-top:-8px;margin-bottom:8px;">
        <div class="formfile-form">
            <input type="text" id='formfile-keyval' class="form-control" value="{#$keyval#}" data-val="{#$keyval#}" placeholder="输入搜索关键字" />&nbsp;
            <button class="button" onclick="__replypage(null, 0, $('#formfile-keyval').val());">搜索</button>&nbsp;
            <a class="button" href="{#weburl('reply/add')#}" target="_blank">+新建回复</a>
        </div>
        <div id="pagelist" style="margin-top:2px">{#$pagelist#}</div>
    </div>
    <div class="appmsg-list cf" id="appmsg-list" style="max-width:100%;">
        <table style="" class="table table-primary" id="menu-table">
            <thead>
            <tr>
                <th align="left">关键词</th>
                <th>匹配类型</th>
                <th>回复类型</th>
            </tr>
            </thead>
            <tbody>
            {#foreach from=$lists item=list#}
                {#$setting = string2array($list.setting)#}
                {#$content = string2array($list.content)#}
                <tr class="align-center trkey">
                    <td class="key">{#$list.key|replykey#}</td>
                    <td align="center">
                        {#if ($list.match)#}包含匹配{#else#}完全匹配{#/if#}
                    </td>
                    <td align="center">
                        {#if $setting.apitype#}
                            [自定义接口]
                        {#else#}
                            {#if $list.type=='imagetext'#}
                                [图文]
                            {#elseif $list.type=='material'#}
                                [素材]
                            {#elseif $list.type=='image'#}
                                [图片]
                            {#elseif $list.type=='voice'#}
                                [语音]
                            {#elseif $list.type=='video'#}
                                [视频]
                            {#else#}
                                文字: {#$content.text|get_html:"13"#}
                            {#/if#}
                        {#/if#}
                    </td>
                </tr>

                {#foreachelse#}
                <tr>
                    <td colspan="3" align="center" class="align-center">
                        <div>无</div>
                    </td>
                </tr>
            {#/foreach#}
            </tbody>
        </table>
    </div>
</div>
