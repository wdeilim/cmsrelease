{#if (!$allist)#}
	<div align="center">没有添加接入，请先添加。</div>
{#elseif (!$funlist)#}
	<div align="center">没有可使用的功能模块。</div>
{#else#}
	<form action="{#$urlarr.now#}" method="post" id="openfun-edit-form">
		<table id="funeditform" class="wineditform" border="0" cellspacing="0" cellpadding="0">
			<tbody>
				<tr>
					<td class="al-right" valign="top" style="padding-top:10px;">选择服务：</td>
					<td class="al-right">
                        <select name="alid" id="alid" class="opense" onchange="_opense(this);">
                            <option value="">=请选择接入的服务=</option>
                            {#foreach from=$allist item=list#}
                                <option value="{#$list.id#}">
                                    {#if $list.wx_name#}
                                        公众号:{#$list.wx_name#}
                                    {#/if#}
                                    {#if $list.al_name#}
                                        服务窗:{#$list.al_name#}
                                    {#/if#}
                                    {#if !$list.wx_name && !$list.al_name#}
                                        {#$list.username#}[{#$list.id#}]
                                    {#/if#}
                                </option>
                            {#/foreach#}
                        </select>
                        {#foreach from=$allist item=list#}
                            <script type="text/plain" id="funcplain_{#$list.id#}">{#json_encode(string2array($list.function))#}</script>
                        {#/foreach#}
                    </td>
				</tr>
				<tr>
					<td class="al-right" valign="top">功能选择：</td>
                    <td class="al-right">
                        <div class="openal">
                            {#foreach from=$funlist item=list#}
                            <label class="open-la" title="{#$list.title#}">
                                <input type="checkbox" name="functionsid[]" value="{#$list.id#}" class="ola-l">
                                {#$list.title#}
                            </label>
                            {#/foreach#}
                        </div>
                    </td>
                </tr>
				<tr>
					<td class="al-right"></td>
					<td>
						<input class="button button-primary button-rounded" type="submit" value="开通功能">
						<input type="hidden" name="dosubmit" value="1">
					</td>
				</tr>
			</tbody>
		</table>
    </form>
    {#if intval($_GPC['openalid']) > 0#}
    <script> $(function(){ $("select#alid").val('{#$_GPC['openalid']#}').change(); }); </script>
    {#/if#}
{#/if#}