<form action="{#$urlarr.now#}" method="post" id="com-edit-form">
	<table id="comeditform" class="wineditform" border="0" cellspacing="0" cellpadding="0">
		<tbody>
        <tr>
            <td class="al-right"><span>级别</span></td>
            <td>
                <select class="form-control" id="admin" name="admin">
                    <option value="0">会员</option>
                    <option value="1">管理员</option>
                </select>
            </td>
        </tr>
		<tr>
			<td class="al-right"><span>用户名*</span></td>
			<td><input class="form-control" type="text" id="username" name="username" placeholder="注册后不可修改"/></td>
		</tr>
        <tr>
            <td class="al-right"><span>密码*</span></td>
            <td><input class="form-control" type="password" id="userpass" name="userpass"/></td>
        </tr>
        <tr>
            <td class="al-right"><span>{#$smarty.const.POINT_NAME#}</span></td>
            <td><input class="form-control" type="text" id="point" name="point"/></td>
        </tr>
		<tr>
			<td class="al-right"><span>姓名</span></td>
			<td><input class="form-control" type="text" id="fullname" name="fullname"/></td>
		</tr>
		<tr>
			<td class="al-right"><span>手机</span></td>
			<td><input class="form-control" type="text" id="phone" name="phone"/></td>
		</tr>
		<tr>
			<td class="al-right"><span>邮箱</span></td>
			<td><input class="form-control" type="text" id="email" name="email"/></td>
		</tr>
		<tr>
			<td class="al-right"><span>QQ</span></td>
			<td><input class="form-control" type="text" id="qqnum" name="qqnum"/></td>
		</tr>
		<tr>
			<td class="al-right"><span>公司名称</span></td>
			<td><input class="form-control" type="text" id="companyname" name="companyname"/></td>
		</tr>
		<tr>
			<td class="al-right"><span>电话</span></td>
			<td><input class="form-control" type="text" id="tel" name="tel"/></td>
		</tr>
		<tr>
			<td class="al-right"><span>地区</span></td>
			<td class="form-reg">
				<input class="form-control" type="text" id="linkaddr" name="linkaddr" id="linkaddr">
			</td>
		</tr>
		<tr>
			<td class="al-right"><span>详细地址</span></td>
			<td>
				<input class="form-control" type="text" id="address" name="address"/>
			</td>
		</tr>
		<tr style="display:none;">
			<td></td>
			<td>
				<div>
					<input class="button button-primary button-rounded" type="submit" value="注册"/>
					<input class="button button-primary button-rounded" type="reset" value="重写"/>
					<input type="hidden" name="dosubmit" value="1"/>
				</div>
			</td>
		</tr>
		</tbody>
	</table>
</form>