{#if ($lists)#}
    <div class="table-wrapper" style="min-width:380px;max-height:480px;overflow:auto">
        <table id="funeditform" class="table table-primary wineditform" border="0" cellspacing="0" cellpadding="0">
            <thead>
            <tr>
                <th class="align-center">名称</th>
                <th class="align-center">操作</th>
            </tr>
            </thead>

            <tbody>
            {#foreach from=$lists item=list#}
                <tr id="delfun-{#$list.ida#}">
                    <td class="align-center">
                        <span id="funNameById1">{#$list.title#}</span>
                    </td>
                    <td class="align-center">
                        {#if $list.default#}
                            系统
                        {#else#}
                            <a class="normal-link operation-link" href="javascript:;" onclick="deluserfun({#$list.ida#})">删除</a>
                        {#/if#}
                    </td>
                </tr>
            {#/foreach#}
            </tbody>
        </table>
    </div>
{#else#}
    <div align="center">无</div>
{#/if#}
