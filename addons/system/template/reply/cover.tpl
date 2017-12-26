
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
            <i class="iconfont">&#xe621;</i>
            <span>{#$dosetting['title']#}</span>

            {#template('reply_right')#}
        </div>
    </div>


    <div class="main cf custom-menu">
        <div class="mod_tab_btn" data-toggle="tooltip" data-original-title="切换显示"></div>
        <div class="mod_tab_menu"><span data-toggle="tooltip" data-original-title="切换显示"></span></div>

        <div class="mod">

            <div class="main-bd" id="tabmenu">
                <div class="clearfix">
                    <form action="{#get_url()#}" method="post" id="saveform" class="form-horizontal form ng-pristine ng-valid">

                        <div class="form-group">
                            <div class="col-sm-12">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        功能封面
                                        <span class="text-muted">{#$dosetting['title']#}</span>
                                    </div>
                                    <div class="panel-body">

                                        <div class="form-group">
                                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">规则名称</label>
                                            <div class="col-xs-12 col-sm-9">
                                                <input type="text" readonly="readonly" value="{#$dosetting['title']#}" class="form-control">
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
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        直接连接
                                        <span class="text-muted">直接进入的URL</span>
                                    </div>
                                    <div class="panel-body">

                                        <div class="form-group">
                                            <label class="col-xs-12 col-sm-3 col-md-2 control-label">直接URL</label>
                                            <div class="col-xs-12 col-sm-9">
                                                <input type="text" class="form-control" readonly="readonly" value="{#appurl(0, $_A['module'], $_GPC['do'])#}" style="cursor:text;">
                                                <div class="help-block">直接指向到入口的URL。您可以在自定义菜单（有oAuth权限）或是其它位置直接使用。</div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

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