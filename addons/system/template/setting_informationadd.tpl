<form action="{#$urlarr.now#}" method="post" id="information-edit-form" class="form-information">
    <table id="informationeditform" class="wineditform table-information" border="0" cellspacing="0" cellpadding="0">
        <tbody>
        <tr>
            <td class="al-right"><span>标题</span></td>
            <td><input class="form-control" type="text" id="title" name="title" value="{#value($info,'title')#}"/></td>
        </tr>
        <tr>
            <td class="al-right"><span>添加时间</span></td>
            <td><input class="form-control" type="text" id="indate" name="indate" value="{#value($info,'indate')|date_format:"%Y-%m-%d %H:%M:%S"#}"
                       onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss'})"/></td>
        </tr>
        <tr>
            <td class="al-right"><span>排序</span></td>
            <td><input class="form-control" type="text" id="inorder" name="inorder" value="{#value($info,'inorder')#}"/> (越大越靠前)</td>
        </tr>
        <tr>
            <td class="al-right"><span>已阅读</span></td>
            <td><input class="form-control" type="text" id="view" name="view" value="{#value($info,'view')#}"/></td>
        </tr>
        <tr>
            <td class="al-right" valign="top"><span>内容详情</span></td>
            <td class="form-uetext"><textarea id="content" name="content" style="width: 700px; height: 320px;">{#value($info,'content')#}</textarea></td>
        </tr>
        <tr style="display:none;">
            <td></td>
            <td>
                <div>
                    <input class="button button-primary button-rounded" type="submit" value="保存"/>
                    <input class="button button-primary button-rounded" type="reset" value="重置"/>
                    <input type="hidden" name="dosubmit" value="1"/>
                </div>
            </td>
        </tr>
        </tbody>
    </table>
</form>
