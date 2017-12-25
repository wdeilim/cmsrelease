<?php include BASE_PATH.'install/step/header.tpl.php';?>
<div id="body" style="padding: 16px;width:auto;">
<style type="text/css">
#body div.ok, #body div.warn, #body div.error{border: 0px; background: none; margin: 0px; padding: 0px;}
.list .table td {padding:4px 10px;}
.b { font-weight:bold;}
</style>
	<div style="width: 90%; margin: auto;">
		<h1>3. 微窗 <?php echo $steps[$step];?></h1>
		<div class="list">
			<table align="center" class="table">
				<tr class="header">
					<td>目录文件</td>
					<td>所需状态</td>
					<td>当前状态</td>
				</tr>
				<?php foreach ($filesmod as $filemod) {?>
					<tr>
						<td><?php echo $filemod['file']?></td>
						<td class="b">可写</td>
						<td><?php echo $filemod['is_writable'] ? '<div class="ok">√可写</div>' : '<img src="images/error.gif" alt="不可写"/>'?></td>
					</tr>
				<?php } ?>

				
			</table>
		</div>
		<?php if($no_writablefile == 0) {?>
			<div class="ok" style="text-align: center;">√ 通过检测，微窗 可以正常运行在该环境下。 </div>
			<p style="text-align: center;">
				<input type="button" value=" 上一步" onClick="history.back();" class="anniu"/>
				&nbsp;&nbsp;&nbsp;
				<input type="button" value=" 下一步" onClick="window.location='install.php?step=4';" class="anniu"/>
			</p>
		<?php }else{ ?>
			<div class="error" style="text-align: center;">当前配置不满足【微窗】安装需求，无法继续安装。 </div>
			<p style="text-align: center;">
				<input type="button" value=" 上一步" onClick="history.back();" class="anniu"/>
			</p>
		<?php }?>
	</div>
</div>
<div id="footer"> Powered by Vwins (c) 2008-2015 </div>

</body>
</html>


