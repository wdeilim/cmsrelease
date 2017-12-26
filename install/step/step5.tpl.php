<?php include BASE_PATH.'install/step/header.tpl.php';?>

<div id="body" style="padding: 16px;width:auto;">
	<div style="width: 700px; margin: auto;">
		<h1>6. 安装详细过程</h1>
		<div class="div">
			<div class="header">安装过程:</div>
			<div class="body">
				<div id="installmessage" style="line-height: 25px;padding: 0 5px;">正在准备安装 ...<br /></div>
				<div id="hiddenop"></div>
			</div>
		</div>
	</div>

	<p style="text-align: center;">
		<input type="button" value=" 上一步" onClick="history.back();" class="anniu"/>
		&nbsp;&nbsp;&nbsp;
		<input type="button" id="finish" value=" 安装中.." class="anniu"/>
	</p>
	<div style="display:none;width:90%;margin:10px auto 0px auto;text-align: center;" id="sqlerrdiv">
		<a name="sqlerra"></a>
		<h2 class="box">如果可以请您将以下错误信息反馈给我们：342210020@qq.com</h2>
		<textarea name="content" style="width:100%;height:160px;padding:10px;width:680px;line-height:18px;" id="sqlerrtext"></textarea>
	</div>
</div>


<form id="install" action="install.php?" method="post">
	<input type="hidden" name="module" id="module" value="<?php echo $module?>" />
	<input type="hidden" name="testdata" id="testdata" value="<?php echo $testdata?>" />
	<input type="hidden" id="selectmod" name="selectmod" value="<?php echo $selectmod?>" />
	<input type="hidden" name="step" value="6">
</form>



<div id="footer"> Powered by Vwins (c) 2008-2016 </div>


<script language="JavaScript">
<!--
$().ready(function() {
	reloads();
})
var n = 0;
var setting =  new Array();
setting['1'] = '主模块安装成功......';
setting['2'] = '自定义菜单安装成功......';
setting['3'] = '自动回复安装成功......';
setting['4'] = '信息管理安装成功......';
setting['5'] = '素材库管理安装成功......';
setting['6'] = '会员卡系统安装成功......';
setting['7'] = '模拟测试功能安装成功......';
setting['sqlnum'] = '';

var dbhost = '<?php echo $dbhost?>';
var dbuser = '<?php echo $dbuser?>';
var dbpass = '<?php echo $dbpass?>';
var dbname = '<?php echo $dbname?>';
var pre = '<?php echo $pre?>';
var dbcharset = '<?php echo $dbcharset?>';
var pconnect = '<?php echo $pconnect?>';
var username = '<?php echo $username?>';
var password = '<?php echo $password?>';
var email = '<?php echo $email?>';
var ftp_user = '<?php echo $dbuser?>';
var password_key = '<?php echo $password_key?>';
function reloads() {
	var module = "1,2,3,4,5,6,7,sqlnum";
	m_d = module.split(',');
	$.ajax({
		   type: "POST",
		   url: 'install.php',
		   data: "step=installmodule&module="+m_d[n]+"&dbhost="+dbhost+"&dbuser="+dbuser+"&dbpass="+dbpass+"&dbname="+dbname+"&pre="+pre+"&dbcharset="+dbcharset+"&pconnect="+pconnect+"&username="+username+"&password="+password+"&email="+email+"&ftp_user="+ftp_user+"&password_key="+password_key+"&t="+Math.random()*5,
		   success: function(msg){
			   if(msg=='a') {
				   alert('指定的数据库不存在，系统也无法创建，请先通过其他方式建立好数据库！');
			   } else if(msg=='b') {
				   $('#installmessage').append("<font color='#ff0000'>"+m_d[n]+"/install/main/vwins_db.sql 数据库文件不存在</font>");
			   } else if(msg.length>20) {
				   $('#installmessage').append("<font color='#ff0000'>错误信息：</font>"+msg);
			   } else {
				   $('#installmessage').append(setting[m_d[n]] + msg + "<img src='images/correct.gif' /><br>");				   
					n++;
					if(n < m_d.length) {
						reloads();
					} else {						
						$('#installmessage').append("<font color='green'>恭喜您，网站安装完成！</font>");
						$('#finish').val('安装完成');
						$('#finish').attr('onClick','window.location=\'install.php?step=6\';');				
					}
					document.getElementById('installmessage').scrollTop = document.getElementById('installmessage').scrollHeight;
			   }	
		}	
		});
}
//-->
</script>
</body>
</html>
