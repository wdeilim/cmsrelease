<?php include BASE_PATH.'install/step/header.tpl.php';?>
<div id="body" style="padding: 16px; width:auto;">
	
	<?php echo $license?>
	<div style="text-align:center;">
		<input type="button" value="我不同意" onclick="window.close();" class="anniu"/>
		&nbsp;&nbsp;&nbsp;
		<input type="button" value="我同意" onclick="window.location='install.php?step=2'" class="anniu"/>
	</div>
</div>

<div id="footer"> Powered by Vwins (c) 2008-2015 </div>

</body>
</html>