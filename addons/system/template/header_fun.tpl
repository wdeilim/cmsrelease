<div class="header header-fix">
    <div class="navbar cf">
        <img class="logo" src="{#$IMG_PATH#}logo.png" alt=""/>
        <h2 class="title" title="{#$_A.f.title#}(ID:{#$_A.uf.id#})">{#$_A.f.title#}<span id="_h2_al_name"></span></h2>
        <ul class="nav cf funnav" id="head-nav-menu">
            <ol id="moremenu">
                {#ddb_pc set="数据表:users_al,列表名:lists,显示数目:1" where=" `id`={#$_A.uf.alid#} "#}
                {#foreach from=$lists item=list#}
                    {#$fl = string2array($list.function)#}
                    {#foreach from=$fl item=fls#}
                        <li{#if ($fls.ida==$_A.uf.id)#} class="active"{#/if#}>
                            <a class="nav-item-link" href="{#$urlarr.1#}{#$fls.title_en#}/?al={#$fls.alid#}&uf={#$fls.ida#}" title="{#$fls.title#}"><span class="nav-item-img"><img src="{#$smarty.const.BASE_URI#}addons/{#$fls.title_en#}/icon.png" onerror="this.src='{#$IMG_PATH#}app.png'"></span><span class="nav-item-title">{#$fls.title#}</span></a>
                        </li>
                    {#/foreach#}
                {#/foreach#}
            </ol>
            <li><a class="nav-item-link" href="{#$urlarr.1#}system/">返回平台首页</a></li>
        </ul>
    </div>
</div>
<div class="header-height"></div>

<script type="text/javascript"> window._al_name_array = ['{#addslashes($_A.uf.wx_name)#}','{#addslashes($_A.uf.al_name)#}']; </script>
<script type="text/javascript" src="{#$JS_PATH#}header_fun.js"></script>