<a class="button{#if $_item==1#} button-hover{#/if#}" href="{#$urlarr.2#}{#get_get()#}">关键词回复</a>
<a class="button{#if $_item==2#} button-hover{#/if#}" href="{#$urlarr.2#}other/{#get_get()#}">非关键词回复</a>
{#if $wx_level != 7#}
    <a class="button{#if $_item==3#} button-hover{#/if#}" href="{#$urlarr.2#}attention/{#get_get()#}">关注时回复</a>
{#/if#}