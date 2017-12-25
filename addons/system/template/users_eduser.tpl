<table id="funeditform" class="wineditform" border="0" cellspacing="0" cellpadding="0">
    <tbody>
    <tr>
        <td class="al-right" valign="top" style="padding-top:10px;">修改的接入服务项：</td>
        <td class="al-right">
            <select class="opense">
                <option>
                    {#if $al.wx_name#}
                        公众号:{#$al.wx_name#}
                    {#/if#}
                    {#if $al.al_name#}
                        服务窗:{#$al.al_name#}
                    {#/if#}
                    {#if !$al.wx_name && !$al.al_name#}
                        {#$al.username#}[{#$al.id#}]
                    {#/if#}
                </option>
            </select>
        </td>
    </tr>
    <tr>
        <td class="al-right" valign="top" style="padding-top:10px;">选择所属管理账号：</td>
        <td class="al-right">
            <div class="openal">
                <select class="opense" id="eduseruserid">
                    <option value="0">==请选择改为的管理账号==</option>
                    {#foreach from=$ul item=list#}
                        {#if $list.userid != $al.userid#}
                            <option value="{#$list.userid#}">
                                {#$list.username#}{#if $list.companyname#} (公司名称:{#$list.companyname#}){#/if#}
                            </option>
                        {#/if#}
                    {#/foreach#}
                </select>
            </div>
        </td>
    </tr>
    </tbody>
</table>