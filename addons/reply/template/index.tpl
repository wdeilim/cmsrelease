
<!DOCTYPE html>
<html>
<head lang="zh-CN">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>关键词回复 - {#$func.f.title#} - {#$BASE_NAME#}</title>
    <link rel="stylesheet" href="{#$CSS_PATH#}normalize.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}global.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}reply.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}alert.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}font-awesome.css"/>
    <script type="text/javascript" src="{#$JS_PATH#}jquery-1.11.0.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.alert.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.form.min.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}artdialog/artDialog.js?skin=default"></script>
</head>
<body>
{#template("header")#}


<div class="wrapper">
    <div class="breadcrumb">
        <div class="items">
            <span class="description">当前位置：</span>
            <span>{#$func.f.title#}</span>
            <i class="iconfont">&#xe621;</i>
            <span>关键词回复</span>
        </div>
    </div>
    <div class="main cf custom-menu">
        <div class="mod">
            <div class="col-left">
                {#include file="left.tpl" _item=1 wx_level=$_A['al']['wx_level']#}
            </div>
            <div class="col-right">
                <div class="main-bd">

                    <div class="control-group">
                        <a class="button" href="{#$urlarr.2#}add/{#get_get()#}">+新建回复</a> &nbsp;
                    </div>

                    <table style="margin-bottom: 20px;" class="table table-primary" id="menu-table">
                        <thead>
                        <tr>
                            <th>关键词</th>
                            <th>匹配类型</th>
                            <th>回复类型</th>
                            <th>状态</th>
                            <th width="120">操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        {#ddb_pc set="数据表:reply,列表名:lists,显示数目:10,分页显示:1,分页名:pagelist,当前页:{#$page#},分页地址:{#$urlarr.2#}index/(?)/{#get_get()#},排序:indate desc" where="{#$wheresql#}"#}
                        {#foreach from=$lists item=list#}
                        {#$setting = string2array($list.setting)#}
                        {#$content = string2array($list.content)#}
                        <tr class="align-center">
                            <td class="key">{#$list.key|replykey#}</td>
                            <td>
                                {#if ($list.match)#}包含匹配{#else#}完全匹配{#/if#}
                            </td>
                            <td>
                                {#if $setting.apitype#}
                                    [自定义接口]
                                {#else#}
                                    {#if $list.type=='imagetext'#}
                                        [图文]
                                    {#elseif $list.type=='material'#}
                                        [素材]
                                    {#elseif $list.type=='image'#}
                                        [图片]
                                    {#elseif $list.type=='voice'#}
                                        [语音]
                                    {#elseif $list.type=='video'#}
                                        [视频]
                                    {#else#}
                                        文字: {#$content.text|get_html:"13"#}
                                    {#/if#}
                                {#/if#}
                            </td>
                            <td>{#$list.status#}</td>
                            <td style="padding:13px 0">
                                <a href="{#weburl(0, $_A.f.title_en)#}&do=keychart&id={#$list.id#}&key={#urlencode($list.key|trim:",")#}&referer={#urlencode(get_url())#}" class="normal-link" title="关键词走势">走势</a>
                                <a href="{#$urlarr.2#}add/{#$list.id#}/{#get_get()#}" class="normal-link">修改</a>
                                <a href="javascript:void(0);" onclick="del({#$list.id#});" class="normal-link">删除</a>
                            </td>
                        </tr>
                        {#foreachelse#}
                        <tr>
                            <td colspan="5" align="center" class="align-center">
                                <div>无</div>
                            </td>
                        </tr>
                        {#/foreach#}
                        </tbody>
                    </table>
                    <div id="pagelist" class="clearfix">
                        {#$pagelist#}
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script type="text/javascript">
function del(id){
    var _del = art.dialog({
        title: '删除提醒',
        fixed: true,
        lock: true,
        icon: 'warning',
        opacity: '.3',
        content: '确定要删除并且不可恢复吗？',
        button: [{
            name: '确定',
            callback: function () {
                $.alert('正在删除',0);
                $.ajax({
                    url: '{#$urlarr.2#}del/'+id+'/{#get_get()#}',
                    dataType: 'json',
                    success: function (data) {
                        $.alert(0);
                        if (data != null && data.success != null && data.success) {
                            $.showModal(data.message, '{#$urlarr.now#}{#get_get()#}');
                        } else {
                            $.showModal(data.message);
                        }
                        _del.close();
                    },error : function () {
                        $.alert("删除失败！");
                    },
                    cache: false
                });
                return false;
            }
        },{
            name: '取消',
            callback: function () {
                return true;
            }
        }]
    });
}
</script>

{#template("footer")#}

</body>
</html>