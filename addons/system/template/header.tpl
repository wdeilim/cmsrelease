<div class="header">
    <div class="navbar cf">
        <img class="logo" src="{#$IMG_PATH#}logo.png" alt=""/>
        <h2 class="title">欢迎登录{#$BASE_NAME#}</h2>
        <ul class="nav cf" id="head-nav-menu">
            <li><a class="nav-item-link" href="./">首页</a></li>
            {#foreach from=$topmenulists item=_topmenu#}
                <li><a class="nav-item-link" href="{#$_topmenu.link#}" href="{#$_topmenu.target#}">{#$_topmenu.title#}</a></li>
            {#foreachelse#}
                <li><a class="nav-item-link" href="{#$smarty.const.BRAND_URL#}" target="_blank">公司简介</a></li>
                <li><a class="nav-item-link" href="{#$smarty.const.BRAND_URL#}" target="_blank">产品介绍</a></li>
                <li><a class="nav-item-link" href="{#$smarty.const.BRAND_URL#}" target="_blank">精品案例</a></li>
                <li><a class="nav-item-link" href="{#$smarty.const.BRAND_URL#}" target="_blank">行业方案</a></li>
                <li><a class="nav-item-link" href="{#$smarty.const.BRAND_URL#}" target="_blank">渠道合作</a></li>
                <li><a class="nav-item-link" href="{#$smarty.const.BRAND_URL#}" target="_blank">联系我们</a></li>
            {#/foreach#}
        </ul>
    </div>
</div>