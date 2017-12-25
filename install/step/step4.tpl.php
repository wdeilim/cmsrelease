<?php include BASE_PATH.'install/step/header.tpl.php';?>
<div id="body" style="padding: 16px;width:auto;">

<!--- 表单数据 -->
<style type="text/css">
dd {text-indent: 8px;}
div.error {height: auto;}
input.info {width:230px; padding:6px 8px;}
.ipage label {color:#888888;}
.ipage label .ra {display:block;float:left;width:140px;color:#303030;}
.ipage label .rt {display:block;float:left;}
.ipage label em {font-style:normal;color:#bb83fd;text-decoration:underline}
</style>

<form id="install" name="myform" action="install.php" method="post">
<input type="hidden" name="step" value="5"/>

<div style="width: 90%; margin: auto;line-height: 36px;">

	<h1>4. 索引页面</h1>
	<div class="div">
		<div class="header">首页文件:</div>
		<div class="body ipage">
			<dl>
				<dt>方式一: </dt>
				<dd>
					<label>
						<div class="ra"><input type="radio" <?php if ($nomoren) { echo 'disabled'; }else{ echo 'value="" name="_index" checked'; } ?>>index.php (默认)</div>
						<div class="rt">默认的一种形式。显示效果：http://xxx.com/<em>index.php</em>/products/view/</div>
					</label>
				</dd>

				<dt>方式二: </dt>
				<dd>
					<label>
						<div class="ra"><input type="radio" <?php if ($noweijingtai) { echo 'disabled'; }else{ echo 'value="hide" name="_index"'; } ?>>(隐藏索引文件)</div>
						<div class="rt">如果你的服务器支持伪静态。显示效果：http://xxx.com/products/view/</div>
					</label>
				</dd>

				<dt>方式三: </dt>
				<dd>
					<label>
						<div class="ra"><input type="radio" value="pathinfo" name="_index" <?php if ($nomoren) { echo 'checked'; } ?>>index.php?</div>
						<div class="rt">可能是你的服务器不支持 PATH_INFO 变量。显示效果：http://xxx.com/<em>index.php?</em>/products/view/</div>
					</label>
				</dd>
			</dl>
		</div>
	</div>

	<h1>5. 数据库信息</h1>
	<div class="div">
		<div class="header">数据库信息:</div>
		<div class="body">
			<dl>
				<dt>主机: </dt>
				<dd>
					<input class="info" type="text" size="24" name="dbhost" id="dbhost" value="<?php echo $__dbhost?$__dbhost:"localhost"?>" />
					<span class="grey"> 端口号格式：127.0.0.1:10123</span></dd>
				<dt>用户名: </dt>
				<dd>
					<input class="info" type="text" size="24" name="dbuser" id="dbuser" value="<?php echo $__dbuser?>" />
				</dd>
				<dt>密码: </dt>
				<dd>
					<input class="info" type="password" size="24" name="dbpass" id="dbpass" value="<?php echo $__dbpass?>" />
				</dd>
				<dt>数据库: </dt>
				<dd>
					<input class="info" type="text" size="24" name="dbname" id="dbname" value="<?php echo $__dbname?>" />
				</dd>
				<dt>表前缀: </dt>
				<dd>
					<input class="info" type="text" size="24" name="pre" id="pre" value="<?php echo $__dbpre?$__dbpre:"es_"?>" />
				</dd>
				<div style="display:none">
					<dt>字符集: </dt>
					<dd>
						<input name="dbcharset" type="radio" id="dbcharset" value="utf-8" <?php if(strtolower($__dbcharset)=='') echo ' checked="checked" '?>/>默认
						<input name="dbcharset" type="radio" id="dbcharset" value="utf-8" <?php if(strtolower($__dbcharset)=='utf-8' || strtolower($__dbcharset)=='utf8') echo '  checked="checked" '?> <?php if(strtolower($__dbcharset)=='gbk') echo 'disabled'?>/>utf-8 
						<input name="dbcharset" type="radio" id="dbcharset" value="gbk" <?php if(strtolower($__dbcharset)=='gbk') echo '  checked="checked" '?> <?php if(strtolower($__dbcharset)=='utf-8') echo 'disabled'?>/>GBK
						<input name="dbcharset" type="radio" id="dbcharset" value="latin1" <?php if(strtolower($__dbcharset)=='latin1') echo ' checked '?> />latin1 
					</dd>
				</div>
			</dl>
		</div>
	</div>
	
	<h1>6. 填写帐号信息</h1>
	<div class="div">
		<div class="header">默认账号信息:</div>
		<div class="body">
			<dl>
				<dt>默认账号: </dt>
				<dd>
					<input class="info" type="text" size="24" name="username" id="username" value="vwins" maxlength="16" />
				</dd>
				<dt>默认密码: </dt>
				<dd>
					<input class="info" type="password" size="24" name="password" id="password" maxlength="16" />
				</dd>
				<dt>确认密码: </dt>
				<dd>
					<input class="info" type="password" size="24" name="pwdconfirm" id="pwdconfirm" maxlength="16" />
				</dd>
				<dt>E-mail: </dt>
				<dd>
					<input class="info" type="text" size="24" name="email" id="email" value="admin@admin.com"/>
				</dd>
			</dl>
		</div>
	</div>

</div>
</form>

<p style="text-align: center;">
	<input type="button" value=" 上一步" onClick="history.back();" class="anniu"/>
	&nbsp;&nbsp;&nbsp;
	<input type="button" id="finish" value=" 下一步" onClick="checkdb();return false;" class="anniu"/>
</p>


</div>
<div id="footer"> Powered by Vwins (c) 2008-2015 </div>

<script language="JavaScript">
<!--
var errmsg = new Array();
errmsg[0] = '您已经安装过微窗，系统会自动删除老数据！是否继续？';
errmsg[2] = '无法连接数据库服务器，请检查配置！';
errmsg[3] = '成功连接数据库，但是指定的数据库不存在并且无法自动创建，请先通过其他方式建立数据库！';
errmsg[6] = '数据库版本低于Mysql 4.0，无法安装微窗，请升级数据库版本！';

$(document).ready(function(){
	$("input[class='info']").click(function(){
		var inid = $(this).attr('id')+'_err';
		if ($("#"+inid)){
			$("#"+inid).remove();
		}
	});
	$(".ipage").find('input[type="radio"]').each(function(){
		if ($(this).attr("disabled")) {
			$(this).parents("label").find(">div").css({"color": "#aaa"});
			$(this).parent("div").css({"text-decoration": "line-through", "color": "#888"});
		}
	});
});

function checkdb() 
{
	var d = 0;
	$("span[data-err='1']").remove();
	$("input[class='info']").each(function(){
		if ($(this).val() == '' && $(this).attr("id") != 'dbpass'){
			$(this).after('<span id="'+$(this).attr('id')+'_err" data-err="1" style="padding-left:5px; color:#F00">此项不能留空。</span>');
			if (d == 0) $(this).focus();
			d = 1;
		}
	});
	if ($("#password").val() != $("#pwdconfirm").val() && d == 0){
		$("#pwdconfirm").after('<span id="'+$(this).attr('id')+'_err" data-err="1" style="padding-left:5px; color:#F00">两次密码输入不相同。</span>');
		$("#pwdconfirm").focus();
		d = 1;
	}
	if ($("#password").val().length < 6 && d == 0){
		$("#password").after('<span id="password_err" data-err="1" style="padding-left:5px; color:#F00">密码不能小于6位数。</span>');
		$("#password").focus();
		d = 1;
	}
	if (d == 1) return false;
	
	$('#finish').val('正在提交');
	var url = 'install.php?step=dbtest&dbhost='+$('#dbhost').val()+'&dbuser='+$('#dbuser').val()+'&dbpass='+$('#dbpass').val()+'&dbname='+$('#dbname').val()+'&pre='+$('#pre').val()+'&ipage='+$("input[name='_index']:checked").val()+'&t='+Math.random()*5;
    $.get(url, function(data){
		if(data > 1) {
			alert(errmsg[data]);
			$('#finish').val(' 下一步');
			return false;
		}
		else if(data == 1 || (data == 0 && confirm(errmsg[0]))) {
			$('#install').submit();
		}
		$('#finish').val(' 下一步');
	});
    return false;
}
//-->
</script>

</body>
</html>
