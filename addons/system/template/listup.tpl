<div class="formfile-filetitle">
    <span data-f="|" class="formfile-home" onclick="$.__formfile_fun.browser(this);"></span>
    {#foreach from=$path item=list key=key#}
    <span data-f="{#$list#}" onclick="$.__formfile_fun.browser(this);">{#trim($key,'|')#}</span>
    {#/foreach#}
</div>

<div class="formfile-content">
    <ul class="formfile-fileview">
        {#foreach from=$listarr item=list#}

        <li title="{#$list.title#}">
            <div class="fitem" data-f="{#$list.f#}" onclick="$.__formfile_fun.browser(this);">
                <input type="hidden" id="fileurl" value="{#$list.url#}">
                {#if $list.img#}<img src="{#$list.thumb#}" data-src="{#$list.img#}"/>{#/if#}
                {#if $list.class#}<div class="{#$list.class#}"></div>{#/if#}
                <div class="filename">{#$list.title#}</div>
                <div class="fselecticon"></div>
            </div>
            {#if $list.type#}<em class="del" onclick="$.__formfile_fun.del(this);">×</em>{#/if#}
        </li>

        {#foreachelse#}
        {#/foreach#}
    </ul>
</div>
{#if isset($nownane) && $nownane#}
    <script type="text/javascript">
        $(function(){$(".formfile-fileview>li[title='{#$nownane#}']").css({"border": "1px solid #B637FF"});});
    </script>
{#/if#}