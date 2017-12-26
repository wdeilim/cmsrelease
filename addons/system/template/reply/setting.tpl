
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

            <span class="ilink iright"><a href="{#weburl(0, $_A.f.title_en)#}" >返回 {#$_A.f.title#}</a></span>
        </div>
    </div>



    <div class="main cf custom-menu">
        <div class="mod_tab_btn" data-toggle="tooltip" data-original-title="切换显示"></div>
        <div class="mod_tab_menu"><span data-toggle="tooltip" data-original-title="切换显示"></span></div>

        <div class="mod">

            <div class="main-bd" id="tabmenu">
                <div class="clearfix">
                    <form action="{#get_url()#}" method="post" id="saveform" class="form-horizontal form ng-pristine ng-valid">

                        {#$this->_setting_help($_GPC['do'])#}

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