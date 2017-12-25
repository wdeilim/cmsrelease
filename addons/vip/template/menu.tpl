<div class="breadcrumb">
    <div class="items" id="vipitems">
        <span class="description">当前位置：</span>
        <a href="{#weburl('vip/index')#}">{#$_A.f.title#}</a>
    </div>
</div>

<div class="vipmenu" id="vipmenu">
	<a href="{#weburl('vip/index')#}">统计概况</a>
	<a href="{#weburl('vip/shop')#}">店铺管理</a>
    <div>
        <a href="{#weburl('vip/shop')#}">商家设置</a>
        <a href="{#weburl('vip/shopbranch')#}">分店联系方式管理</a>
    </div>
	<a href="{#weburl('vip/card')#}">会员卡管理</a>
    <div>
        <a href="{#weburl('vip/cardintegral')#}">积分策略设置</a>
        <a href="{#weburl('vip/cardlevel')#}">会员等级设置</a>
        <a href="{#weburl('vip/cardmanuals')#}">会员卡使用说明</a>
        <a href="{#weburl('vip/card')#}">会员卡版面设置</a>
    </div>
	<a href="{#weburl('vip/privilege')#}">特权管理</a>
    <div>
        <a href="{#weburl('vip/privilege')#}">特权列表</a>
        <a href="{#weburl('vip/privilegerelease')#}">发布会员特权</a>
        <a href="{#weburl('vip/privilegestatistics')#}">特权发放统计</a>
    </div>
	<a href="{#weburl('vip/giftcert')#}">礼品管理</a>
    <div>
        <a href="{#weburl('vip/giftcert')#}">礼品列表</a>
        <a href="{#weburl('vip/giftcertrelease')#}">发布礼品券</a>
        <a href="{#weburl('vip/giftcertstatistics')#}">礼品券发放统计</a>
    </div>
	<a href="{#weburl('vip/coupon')#}">优惠券管理</a>
    <div>
        <a href="{#weburl('vip/coupon')#}">优惠券列表</a>
        <a href="{#weburl('vip/couponrelease')#}">发布优惠券</a>
        <a href="{#weburl('vip/couponstatistics')#}">优惠券发放统计</a>
    </div>
	<a href="{#weburl('vip/notification')#}">通知管理</a>
    <div>
        <a href="{#weburl('vip/notification')#}">通知列表</a>
        <a href="{#weburl('vip/notificationrelease')#}">发布新通知</a>
    </div>
	<a href="{#weburl('vip/member')#}">会员管理</a>
    <a href="{#weburl('vip/staff')#}">店员管理</a>
    <a href="{#weburl('vip/setting')#}">会员卡设置</a>
</div>

<script type="text/javascript">
    {#if isset($_item)#}
        var eve = $('#vipmenu').children("a").eq({#$_item#});
        eve.addClass('active');
        $('#vipitems').append('<i class="iconfont">&#xe621;</i>');
        $('#vipitems').append('<a href="' + eve.attr('href') + '">' + eve.text() + '</a>');
        {#if isset($_itemp)#}
            if (eve.next().is("div")){
                var evep = eve.next().children("a").eq({#$_itemp#});
                evep.addClass('active');
                $('#vipitems').append('<i class="iconfont">&#xe621;</i>');
                $('#vipitems').append('<a href="' + evep.attr('href') + '">' + evep.text() + '</a>');
            }
        {#/if#}
    {#/if#}

    //
    $('#vipmenu a').mousemove(function () {
        if ($(this).next().is("div")){
            $(this).next().show();
            $(this).next().css("left", $(this).position().left)
        }
    }).mouseout(function () {
        if ($(this).next().is("div")){
            $(this).next().hide();
        }
    });
    $('#vipmenu div').mousemove(function () {
        $(this).show();
    }).mouseout(function () {
        $(this).hide();
    });

</script>

