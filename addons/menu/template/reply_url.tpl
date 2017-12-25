<div class="formfile-content">
    {#ddb_pc set="数据表:reply,列表名:lists,显示数目:10,分页显示:1,分页名:pagelist,当前页:{#$page#},分页地址:(fun)__moduleurlpage(null\,(?));,排序:indate desc" where="{#$wheresql#}"#}
    <div style="height:35px;margin-top:-8px;margin-bottom:8px;">
        <div class="formfile-form">
            <input type="text" id='formfile-keyval' class="form-control" value="{#$keyval#}" data-val="{#$keyval#}" placeholder="输入搜索关键字" />&nbsp;
            <button class="button" onclick="__moduleurlpage(null, 0, $('#formfile-keyval').val());">搜索</button>&nbsp;
        </div>
        <div id="pagelist" style="margin-top:2px">{#$pagelist#}</div>
    </div>
    <div class="appmsg-list cf" id="appmsg-list">
        <table style="" class="table table-primary" id="menu-table">
            <thead>
            <tr>
                <th align="left">项目名称</th>
                <th align="left">模块名称 (标识)</th>
                <th>粉丝身份</th>
            </tr>
            </thead>
            <tbody class="reply_url">
            {#foreach from=$lists item=list#}
                {#$m = $_this->moduleinfo($list.module)#}
                <tr class="align-center" data-url="{#appurl(0, $list.module, 'welcome')#}&rid={#$list.id#}">
                    <td align="left">{#if $list.module=='vip'#}会员卡{#else#}{#$list.title#}{#/if#}</td>
                    <td align="left">{#$m.title#} ({#$list.module#})</td>
                    <td align="center">
                        {#if ($m.oauth)#}需要{#else#}<font color="#a9a9a9">不需要</font>{#/if#}
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
