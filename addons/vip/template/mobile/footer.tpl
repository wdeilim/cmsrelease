<footer class="copyright">
    {#$_A['vip_data']['shop']['copyright']#}
</footer>

<nav class="toolbar" id="toolbar">
    <a href="{#appurl('vip/index')#}">会员卡</a>
    <a href="{#appurl('vip/privilege')#}">特权</a>
    <a href="{#appurl('vip/coupon')#}">优惠券</a>
    <a href="{#appurl('vip/giftcert')#}">兑换</a>
    <a href="{#appurl('vip/sign')#}">签到</a>
</nav>
<script type="text/javascript">
	window.firstsize = $(window).height();
	$(window).resize(function(){  
		if ($(window).height() + 10 < window.firstsize){
			$("nav#toolbar").hide();
		}else{
			$("nav#toolbar").show();
		}
	});      
	{#if isset($_item)#}
	$("nav#toolbar a:eq({#$_item#})").addClass("cur");
	{#else#}
	$("nav#toolbar").hide();
	{#/if#}
</script>