<span class="ilink iright">
                <ul class="iright_menu">
                    <li><a href="{#weburl(0, $_A.f.title_en)#}" ><i class="fa fa-reply-all"></i>返回 {#$_A.f.title#}</a></li>
                    <li class="title">核心功能菜单</li>
                    {#if $_A['f']['reply']#}
                        <li>
                            <a href="{#weburl(0, $_A.f.title_en)#}&entry=reply">
                                <i class="fa fa-comments"></i>
                                回复规则列表
                            </a>
                        </li>
                    {#/if#}
                    {#foreach $_A['f']['setting']['bindings']['cover'] AS $item#}
                        <li>
                            <a href="{#weburl(0, $_A.f.title_en)#}&entry=cover&do={#$item['do']#}"{#if $item['target']#} target="{#$item['attr']#}"{#/if#}>
                                <i class="fa fa-external-link-square"></i>
                                {#$item['title']#}
                            </a>
                        </li>
                    {#/foreach#}
                    {#foreach $_A['f']['setting']['bindings']['setting'] AS $item#}
                        <li>
                            <a href="{#weburl(0, $_A.f.title_en)#}&entry=setting&do={#$item['do']#}"{#if $item['target']#} target="{#$item['attr']#}"{#/if#}>
                                <i class="fa fa-cog"></i>
                                {#$item['title']#}
                            </a>
                        </li>
                    {#/foreach#}
                    {#if $_A['f']['setting']['bindings']['menu']#}
                        <li class="title">业务功能菜单</li>
                        {#foreach $_A['f']['setting']['bindings']['menu'] AS $item#}
                            <li>
                                <a href="{#weburl(0, $_A.f.title_en)#}&entry=menu&do={#$item['do']#}"{#if $item['target']#} target="{#$item['attr']#}"{#/if#}>
                                    <i class="fa fa-puzzle-piece"></i>
                                    {#$item['title']#}
                                </a>
                            </li>
                        {#/foreach#}
                    {#/if#}
                </ul>
            </span>