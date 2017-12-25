
<!DOCTYPE html>
<html>
<head lang="zh-CN">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{#$_A.f.title#} - {#$BASE_NAME#}</title>
    <link rel="stylesheet" href="{#$CSS_PATH#}bootstrap.min.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}normalize.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}global.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}functions.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}alert.css"/>
    <link rel="stylesheet" href="{#$NOW_PATH#}css/default.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}font-awesome.css"/>
    <style type="text/css">
        .btn-del:hover {color:#ffffff;background-color: #ff4d4f;border-color: #ff1312;}
        .list-title{padding-top:6px;font-weight:700;max-width:500px;padding-right:30px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
        .list-lists a.btn-sm{margin-right:5px;float:left}
        .list-lists a.btn-sm span{max-width:72px;display:block;float:left;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
    </style>
    <script type="text/javascript" src="{#$JS_PATH#}jquery-1.11.0.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.alert.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}bootstrap.min.js"></script>
</head>
<body>
{#template("header")#}


<div class="wrapper">
    <div class="breadcrumb">
        <div class="items">
            <span class="description">当前位置：</span>
            <span>{#$_A.f.title#}</span>
        </div>
    </div>

    <div class="topmenu" id="topmenu">
        <a href="javascript:;" class="active">管理{#$_A.f.title#}</a>
        <a href="{#weburl(0, $_A.f.title_en)#}&do=add">+添加{#$_A.f.title#}</a>
    </div>

    <div class="main cf custom-menu">
        <div class="mod">
            <div class="main-bd" id="tabmenu">

                <div class="form-services">

                    {#foreach from=$lists item=list#}
                        <div class="panel panel-default panel-list">
                            <div class="panel-heading clearfix">
                                <div class="pull-left list-title" title="{#$list.title#}">{#$list.title#}</div>
                                <div class="pull-right list-lists">
                                    <a class="btn btn-default btn-sm" href="{#weburl(0, $_A.f.title_en)#}&do=add&id={#$list.id#}"><i class="fa fa-edit"></i> 编辑</a>
                                    <a class="btn btn-default btn-sm btn-del" href="{#weburl(0, $_A.f.title_en)#}&do=del&id={#$list.id#}"
                                       onclick="return confirm('删除规则将同时删除关键字与回复，确认吗？');return false;"><i class="fa fa-times"></i> 删除</a>
                                    <a class="btn btn-default btn-sm" href="{#weburl(0, $_A.f.title_en)#}&do=keychart&id={#$list.id#}&key={#urlencode($list.key|trim:",")#}&referer={#urlencode(get_url())#}"><i class="fa fa-line-chart"></i> 关键词走势</a>
                                    {#if $menu#}
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
                                                功能选项
                                                <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu" style="min-width:0;" id="list-dropdown-menu">
                                                {#foreach from=$menu item=ml#}
                                                    <li><a href="{#weburl(0, $_A.f.title_en, $ml.do)#}&id={#$list.id#}"><span title="{#$ml.title#}">{#$ml.title#}</span></a></li>
                                                {#/foreach#}
                                            </ul>
                                        </div>
                                    {#/if#}
                                </div>
                            </div>
                            <div class="panel-body panel-keylist">
                                {#$keya = explode(',', $list.key|trim:",")#}
                                {#foreach from=$keya item=kl#}
                                    <a class="label label-default" title="点击关键词模拟测试" href="{#weburl('emulator')#}&key={#$kl|urlencode#}" target="_blank">{#$kl#}</a>&nbsp;
                                {#/foreach#}
                            </div>
                        </div>
                        {#foreachelse#}
                        <div style="margin-top:100px;text-align:center">没有数据，请先添加！</div>
                    {#/foreach#}

                    {#$this->_reply_help('List', $id, $reply)#}

                </div>
            </div>
        </div>
    </div>
</div>


{#template("footer")#}

<script type="text/javascript">
    $(function () {
        $("div.panel-list").each(function(){
            var tthis = $(this);
            var dropdown_menu = tthis.find("#list-dropdown-menu");
            dropdown_menu.find("li").each(function(){
                if (tthis.find(".list-title").outerWidth() + tthis.find(".list-lists").outerWidth() + $(this).outerWidth() < 1000) {
                    tthis.find(".btn-group").before($($(this).html()).addClass("btn btn-default btn-sm")); $(this).remove();
                }
            });
            if (dropdown_menu.find("li").length == 1) {
                var dropdown_last = dropdown_menu.find("li").eq(0);
                tthis.find(".btn-group").before($(dropdown_last.html()).addClass("btn btn-default btn-sm")).remove(); dropdown_last.remove();
            }else if (dropdown_menu.find("li").length < 1) {
                tthis.find(".btn-group").remove();
            }
        });
        //
        $('[data-toggle="tooltip"]').tooltip();
        $('#select_all').click(function(){
            $('#form1 :checkbox').prop('checked', $(this).prop('checked'));
        });
        $('#form1 :checkbox').click(function(){
            if(!$(this).prop('checked')) {
                $('#select_all').prop('checked', false);
            } else {
                var flag = 0;
                $('#form1 :checkbox[name="rid[]"]').each(function(){
                    if(!$(this).prop('checked') && !flag) {
                        flag = 1;
                    }
                });
                if(flag) {
                    $('#select_all').prop('checked', false);
                } else {
                    $('#select_all').prop('checked', true);
                }
            }
        });
    })
</script>

</body>
</html>