<form action="{#$urlarr.now#}" method="post" id="com-edit-form">
	<table id="comeditform" class="wineditform" border="0" cellspacing="0" cellpadding="0">
		<tbody>
        <tr>
            <td class="al-right"><span>级别</span></td>
            <td>
                <select class="form-control" id="admin" name="admin">
                    <option value="0">会员</option>
                    <option value="1"{#if $users.admin == 1#} selected{#/if#}>管理员</option>
                </select>
            </td>
        </tr>
        <tr>
            <td class="al-right"><span>用户名</span></td>
            <td><input class="form-control" type="text" id="username" name="username" value="{#$users.username#}" disabled="disabled"/></td>
        </tr>
        <tr>
            <td class="al-right"><span>密码</span></td>
            <td><input class="form-control" type="password" id="userpass" name="userpass" placeholder="留空不修改"/></td>
        </tr>
        <tr>
            <td class="al-right"><span>{#$smarty.const.POINT_NAME#}</span><a class="pointa" href="{#$urlarr.2#}me_point/?userid={#$users.userid#}">(记录)</a></td>
            <td><input class="form-control" type="text" id="point" name="point" value="{#$users.point#}"/></td>
        </tr>
		<tr>
			<td class="al-right"><span>姓名</span></td>
			<td><input class="form-control" type="text" id="fullname" name="fullname" value="{#$users.fullname#}"/></td>
		</tr>
		<tr>
			<td class="al-right"><span>手机</span></td>
			<td><input class="form-control" type="text" id="phone" name="phone" value="{#$users.phone#}"/></td>
		</tr>
		<tr>
			<td class="al-right"><span>邮箱</span></td>
			<td><input class="form-control" type="text" id="email" name="email" value="{#$users.email#}"/></td>
		</tr>
		<tr>
			<td class="al-right"><span>QQ</span></td>
			<td><input class="form-control" type="text" id="qqnum" name="qqnum" value="{#$users.qqnum#}"/></td>
		</tr>
		<tr>
			<td class="al-right"><span>公司名称</span></td>
			<td><input class="form-control" type="text" id="companyname" name="companyname" value="{#$users.companyname#}"/></td>
		</tr>
		<tr>
			<td class="al-right"><span>电话</span></td>
			<td><input class="form-control" type="text" id="tel" name="tel" value="{#$users.tel#}"/></td>
		</tr>
		<tr>
			<td class="al-right"><span>地区</span></td>
			<td class="form-reg">
				<input class="form-control" type="text" id="linkaddr" name="linkaddr" id="linkaddr" value="{#$users.linkaddr#}">
			</td>
		</tr>
		<tr>
			<td class="al-right"><span>详细地址</span></td>
			<td>
				<input class="form-control" type="text" id="address" name="address" value="{#$users.address#}"/>
			</td>
		</tr>
		<tr>
			<td></td>
			<td>
				<div>
					<input class="button button-primary button-rounded" type="submit" value="修改"/>
					<input class="button button-primary button-rounded" type="reset" value="重置"/>
					<input type="hidden" name="dosubmit" value="1"/>
				</div>
			</td>
		</tr>
		</tbody>
	</table>
</form>