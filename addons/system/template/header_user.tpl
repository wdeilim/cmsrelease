<div class="header">
    <div class="navbar cf">
        <img class="logo" src="{#$IMG_PATH#}logo.png" alt=""/>
        <h2 class="title">欢迎登录{#$BASE_NAME#}</h2>
        <ul class="nav cf" id="head-nav-menu">
            <li><a class="nav-item-link" href="{#$urlarr.2#}">首页</a></li>
            {#*<li><a class="nav-item-link" href="{#$urlarr.2#}functions/">功能列表</a></li>*#}
            <li><a class="nav-item-link" href="{#$urlarr.2#}question/">常见问题</a></li>
            <li><a class="nav-item-link" href="{#$smarty.const.BRAND_URL#}" target="_blank">{#$smarty.const.BRAND_NAME#}</a></li>
            {#if $_A.u.admin == 1#}
                <li><a class="nav-item-link" href="{#$urlarr.2#}settings/">系统设置</a></li>
            {#/if#}
            <li class="dropdown" style="position: relative;">
                <a class="nav-item-link" href="javascript:void(0);">会员中心</a>
                <ul style="width:800px;" class="dropdown-menu-nav cf nav-item-user">
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
        $("li.dropdown").mouseover(function(){
            $("li.dropdown ul").show();
        }).mouseout(function(){
            $("li.dropdown ul").hide();
        });
        $("li.dropdown ul li#out").click(function() {
            var _logout = art.dialog({
                title: '<span style="font-size:16px;">退出提示</span>',
                fixed: true,
                lock: true,
                icon: 'warning',
                opacity: '.3',
                content: '您将要退出{#$BASE_NAME#}，页面会自动跳转到登录页面',
                button: [{
                    name: '确定退出',
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
                }]
            });
        });
    });
</script>
