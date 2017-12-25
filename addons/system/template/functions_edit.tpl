<style>table.function-edit tbody tr td {padding: 6px 5px;}</style>
<table class="table-setting function-edit">
    <tbody>
    <tr>
        <td class="al-right" valign="top" style="padding-top:13px;width:100px"><span>功能名称</span></td>
        <td>
            <input class="form-control" type="text" id="edit_title"  value="{#value($row, 'title')#}">
            <div style="margin-top:8px;color:#737373;">功能名称, 显示在用户的模块列表中. 不要超过10个字符</div>
        </td>
    </tr>
    <tr>
        <td class="al-right" valign="top" style="padding-top:13px;"><span>功能简述</span></td>
        <td>
            <input class="form-control" type="text" id="edit_ability" value="{#value($row,'ability')#}">
            <div style="margin-top:8px;color:#737373;">功能描述, 使用简单的语言描述模块的作用, 来吸引用户</div>
        </td>
    </tr>
    <tr>
        <td class="al-right" valign="top" style="padding-top:13px;"><span>功能介绍</span></td>
        <td>
            <textarea class="form-control" id="edit_content" style="width:450px;height:60px;padding-top:5px;">{#value($row,'content')#}</textarea>
            <div style="margin-top:8px;color:#737373;">功能详细描述, 详细介绍模块的功能和使用方法</div>
        </td>
    </tr>
    <tr>
        <td class="al-right" valign="top" style="padding-top:13px;"><span>功能缩略图<br/>(留空不修改)</span></td>
        <td>
            {#tpl_form_image("edit_icon",'','','','',1)#}
            <div style="margin-top:8px;color:#737373;">用正方形图片【推荐:100*100】的图片来让你的模块更吸引眼球吧。推荐png格式</div>
        </td>
    </tr>
    <tr>
        <td class="al-right" valign="top" style="padding-top:13px;"><span>功能{#$smarty.const.POINT_NAME#}</span></td>
        <td>
            <input class="form-control" type="text" id="edit_point" value="{#value($row,'point')#}">
            <div style="margin-top:8px;color:#737373;">用户自助开通此功能需要的{#$smarty.const.POINT_NAME#}；填-1表示关闭自助开通，填0表示免费开通。</div>
            <div style="margin-top:8px;color:#737373;">注：修改功能{#$smarty.const.POINT_NAME#}不影响已拥有此功能的接入服务。</div>
        </td>
    </tr>
    </tbody>
</table>