{#ddb_pc set="数据表:information,列表名:lists,显示数目:10,排序:indate desc"#}
{#if $lists#}
<div class="bulletin-overview">
    <div class="items">
		<span>公告：</span>
		{#foreach from=$lists item=list name=placardn#}
			<span class="bulletin-item">
				<a class="normal-link" href="{#$urlarr.2#}information/show/{#$list.id#}/">{#cut_str($list.title,12,0,'...')#}</a>
				<span class="text text-muted text-extra-small">{#$list.indate|date_format:"%Y-%m-%d"#}</span>
			</span>
        {#if !$smarty.foreach.placardn.last#}<span class="text text-muted text-extra-small">|</span>{#/if#}
		{#foreachelse#}
			<span class="bulletin-item">无</span>
		{#/foreach#}
	</div>
</div>
{#/if#}