<div class="formfile-content">
    {#ddb_pc set="数据表:reply,列表名:lists,显示数目:10,分页显示:1,分页名:pagelist,当前页:{#$page#},分页地址:(fun)__modulepage(null\,(?));,排序:indate desc" where="{#$wheresql#}"#}
    <div style="height:35px;margin-top:-8px;margin-bottom:8px;">
        <div class="formfile-form">
            <input type="text" id='formfile-keyval' class="form-control" value="{#$keyval#}" data-val="{#$keyval#}" placeholder="输入搜索关键字" />&nbsp;
            <button class="button" onclick="__modulepage(null, 0, $('#formfile-keyval').val());">搜索</button>&nbsp;
        </div>
        <div id="pagelist" style="margin-top:2px">{#$pagelist#}</div>
    </div>
    <div class="appmsg-list cf" id="appmsg-list" style="max-width:100%;">
        <table style="" class="table table-primary" id="menu-table">
            <thead>
            <tr>
                <th align="left">关键词</th>
                <th align="left">项目名称</th>
                <th align="left">模块名称 (标识)</th>
                <th>匹配类型</th>
            </tr>
            </thead>
            <tbody>
            {#foreach from=$lists item=list#}
                {#$m = $_this->moduleinfo($list.module)#}
                <tr class="align-center">
                    <td class="key">{#$list.key|replykey#}</td>
                    <td>{#if $list.module=='vip'#}会员卡{#else#}{#$list.title#}{#/if#}</td>
                    <td>{#$m.title#} ({#$list.module#})</td>
                    <td align="center">
                        {#if ($list.match)#}包含匹配{#else#}完全匹配{#/if#}
                    </td>
                </tr>

                {#foreachelse#}
                <tr>
                    <td colspan="4" align="center" class="align-center">
                        <div>无</div>
                    </td>
                </tr>
            {#/foreach#}
            </tbody>
        </table>
    </div>
</div>
