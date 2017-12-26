
<!DOCTYPE html>
<html>
<head lang="zh-CN">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{#$_A.f.title#} - {#$BASE_NAME#}</title>
    <link rel="stylesheet" href="{#$CSS_PATH#}bootstrap.min.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}bootstrap-switch.min.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}normalize.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}global.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}functions.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}alert.css"/>
    <link rel="stylesheet" href="{#$NOW_PATH#}css/default.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}font-awesome.css"/>
    <script type="text/javascript" src="{#$JS_PATH#}jquery-1.11.0.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.alert.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.keyauto.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.cookie.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}bootstrap.min.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}bootstrap-switch.min.js"></script>
</head>
<body style="overflow-y:scroll;" data-replyid="{#$reply.id#}">
{#template("header")#}

<div class="wrapper">
    <div class="breadcrumb">
        <div class="items">
            <span class="description">当前位置：</span>
            <span class="ilink"><a href="{#weburl(0, $_A.f.title_en)#}">{#$_A.f.title#}</a></span>

            {#if $bindscount > 0#}
                <i class="iconfont">&#xe621;</i>
                <span class="ilink"><a href="{#weburl(0, $_A.f.title_en)#}&entry=reply">回复规则列表</a></span>

                {#template('reply_right')#}
            {#/if#}

            <i class="iconfont">&#xe621;</i>
            <span>{#$submit#}{#$_A.f.title#}</span>

        </div>
    </div>

    <div class="topmenu" id="topmenu">
        <a href="{#weburl(0, $_A.f.title_en)#}&entry=reply">管理{#$_A.f.title#}</a>
        {#if $submit=='修改'#}
            <a href="{#weburl(0, $_A.f.title_en)#}&entry=reply&do=add&id={#$reply.id#}" class="active">修改{#$_A.f.title#}</a>
        {#/if#}
        <a href="{#weburl(0, $_A.f.title_en)#}&entry=reply&do=add"{#if $submit=='添加'#} class="active"{#/if#}>+添加{#$_A.f.title#}</a>
    </div>

    <div class="main cf custom-menu">
        <div class="mod_tab_btn" data-toggle="tooltip" data-original-title="切换显示"></div>
        <div class="mod_tab_menu"><span data-toggle="tooltip" data-original-title="切换显示"></span></div>

        <div class="mod">
            {#if $replymenu#}
                <div class="mod_a_menu">
                    <span class="btn btn-link">功能选项:</span>
                    {#foreach from=$replymenu item=ml#}
                        <a href="{#weburl(0, $_A.f.title_en, $ml.do)#}&id={#$reply.id#}" class="btn btn-link" title="{#$ml.title#}">{#$ml.title#}</a>
                    {#/foreach#}
                </div>
            {#/if#}
            <div class="main-bd" id="tabmenu">
                <div class="clearfix">
                    <form action="{#get_url()#}" method="post" id="saveform" class="form-horizontal form ng-pristine ng-valid">

                        <div class="form-group">
                            <div class="col-sm-12">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        基本设置
                                        <span class="text-muted">删除，修改规则、关键字以及回复后，请提交以保存操作。</span>
                                    </div>
                                    <div class="panel-body">

                                        <div class="form-group">
                                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">规则名称</label>
                                            <div class="col-xs-12 col-sm-9">
                                                <input type="text" name="reply[title]" value="{#$reply['title']#}" class="form-control" placeholder="您可以给这条规则起一个名字, 方便下次修改和查看. " data-need="规则名称不能为空">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">关键词</label>
                                            <div class="col-xs-12 col-sm-9">
                                                <input type="text" id="reply_key" name="reply[key]" value="{#$reply['key']|trim:","#}" class="form-control" placeholder="多个请用英文逗号“,”隔开" data-need="关键词称不能为空">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">匹配类型</label>
                                            <div class="col-xs-12 col-sm-9">
                                                <select id="base_match" name="reply[match]" class="form-control">
                                                    <option value="0">完全匹配</option>
                                                    <option value="1">包含匹配</option>
                                                </select>
                                                {#if value($reply,'match')#}
                                                    <script>$('#base_match').val('{#value($reply,'match')#}');</script>
                                                {#/if#}
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">回复内容</label>
                                            <div class="col-xs-12 col-sm-9">
                                                {#tpl_form_aledit('reply[content]',value($reply,'content'), 'imagetext')#}
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-12">
                                <div class="panel panel-default{#if !$reply['vip_link']#} vip_link_hide{#/if#}">
                                    <div class="panel-heading">
                                        会员卡显示
                                        <span class="text-muted">这里提供了能够显示在会员卡首页的信息, 你可以选择性的自定义或显示隐藏。</span>
                                        <div class="vip_link_panel">
                                            <input type="checkbox" class="vip_link" name="reply[vip_link]" value="1" data-size="small" data-off-color="warning"{#if $reply['vip_link']#} checked{#/if#}>
                                        </div>
                                    </div>
                                    <div class="panel-body">

                                        <div class="form-group">
                                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">显示标题</label>
                                            <div class="col-xs-12 col-sm-9">
                                                <input type="text" name="reply[vip_title]" value="{#$reply['vip_title']#}" class="form-control" placeholder="请填写在会员卡首页显示的名称，留空则不显示">
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        {#$this->_reply_help('FormDisplay', $id, $reply)#}

                        <div class="form-group">
                            <div class="col-sm-12">
                                <input id="saveform-submit" name="submit" type="submit" value="提交" class="btn btn-primary col-lg-1">
                                <input type="hidden" name="dosubmit" value="1">
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
        <div class="right-tool">

        </div>
    </div>
</div>


<script type="text/javascript" src="{#$NOW_PATH#}js/default.js"></script>
{#template("footer")#}

</body>
</html>