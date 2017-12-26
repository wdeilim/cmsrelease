<div class="header">
    <div class="navbar cf">
        <img class="logo" src="{#$IMG_PATH#}logo.png" alt=""/>
        <h2 class="title">欢迎使用{#$BASE_NAME#}</h2>
        <ul class="nav cf" id="head-nav-menu">
            <li><a class="nav-item-link" href="{#$urlarr.2#}">首页</a></li>
            {#*<li><a class="nav-item-link" href="{#$urlarr.2#}functions/">功能列表</a></li>*#}
            <li><a class="nav-item-link" href="{#$urlarr.2#}question/">常见问题</a></li>
            <li><a class="nav-item-link" href="{#$smarty.const.BRAND_URL#}" target="_blank">{#$smarty.const.BRAND_NAME#}</a></li>
            {#if $_A.u.admin == 1#}
                <li class="dropdown" style="position:relative;">
                    <a class="nav-item-link" href="{#$urlarr.2#}settings/">系统管理</a>
                    <ul style="width:800px;margin-right:10px;" class="dropdown-menu-nav cf nav-item-user">
                        <li class="dropdown-menu-item triangle">
                            <a class="dropdown-menu-item-link" href="{#$urlarr.2#}settings/">系统设置</a>
                        </li>
                        <li class="dropdown-menu-item">
                            <a class="dropdown-menu-item-link" href="{#$urlarr.2#}settings/upgrade/">系统更新</a>
                        </li>
                        <li class="dropdown-menu-item">
                            <a class="dropdown-menu-item-link" href="{#$urlarr.2#}settings/users/">客户管理</a>
                        </li>
                        <li class="dropdown-menu-item">
                            <a class="dropdown-menu-item-link" href="{#$urlarr.2#}settings/cloud/">微窗中心</a>
                        </li>
                        <li class="dropdown-menu-item">
                            <a class="dropdown-menu-item-link" href="{#$urlarr.2#}settings/database/">数据备份</a>
                        </li>
                        <li class="dropdown-menu-item">
                            <a class="dropdown-menu-item-link" href="{#$urlarr.2#}settings/functions/">应用管理</a>
                        </li>
                    </ul>
                </li>
            {#/if#}
            <li class="dropdown" style="position:relative;">
                <a class="nav-item-link" href="{#$urlarr.2#}me_edit/">会员中心</a>
                <ul style="width:800px;margin-right:10px;" class="dropdown-menu-nav cf nav-item-user">
                    <li class="dropdown-menu-item triangle" id="out">
                        <a class="dropdown-menu-item-link" href="javascript:void(0);">退出平台</a>
                    </li>
                    <li class="dropdown-menu-item">
                        <a class="dropdown-menu-item-link" href="{#$urlarr.2#}me_pass/">修改密码</a>
                    </li>
                    <li class="dropdown-menu-item">
                        <a class="dropdown-menu-item-link" href="{#$urlarr.2#}me_point/">我的{#$smarty.const.POINT_NAME#}</a>
                    </li>
                    <li class="dropdown-menu-item">
                        <a class="dropdown-menu-item-link" href="{#$urlarr.2#}me_edit/">注册信息</a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</div>

<script type="text/javascript" src="{#$JS_PATH#}artdialog/artDialog.js?skin=default"></script>
<script>
    $(document).ready(function() {
        var li_dropdown = $("li.dropdown");
        li_dropdown.mouseover(function(){
            $(this).find("ul").show();
            $(this).addClass("active_s");
        }).mouseout(function(){
            $(this).find("ul").hide();
            $(this).removeClass("active_s");
        });
        li_dropdown.find("ul li#out").click(function() {
            art.dialog({
                title: '退出提示',
                fixed: true,
                lock: true,
                icon: 'warning',
                opacity: '.3',
                content: '您将要退出{#$BASE_NAME#}，页面会自动跳转到登录页面',
                button: [{
                    name: '确定退出',
                    focus: true,
                    callback: function () {
                        $.ajax({
                            url: '{#$urlarr.2#}out/',
                            success: function (data) {
                                window.location.reload(true);
                            },
                            cache: false
                        });
                        return false;
                    }
                },{
                    name: '取消',
                    callback: function () {
                        return true;
                    }
                }]
            });
        });
    });
</script>
