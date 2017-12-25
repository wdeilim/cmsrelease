<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>云中心 - {#$BASE_NAME#}</title>
    <link rel="stylesheet" href="{#$CSS_PATH#}admin/normalize.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}admin/global.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}admin/detail.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}font-awesome.css"/>
    <script type="text/javascript" src="{#$JS_PATH#}jquery-1.11.0.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.alert.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.form.min.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}datepicker/WdatePicker.js"></script>
    <style>
        .utis{padding:15px;margin-bottom:20px;border:1px solid #ebccd1;border-radius:4px;color:#a94442;background-color:#f2dede}
        .button-upgrade{display:block;font-size:14px;margin:100px auto;padding:8px 16px}
        .upgradeinfo{width:800px;overflow:hidden;line-height:20px}
        .upgradeinfo p{line-height:20px;margin:0;padding:0}
        .upgradeinfo .profrom{border-bottom:1px solid #ccc;margin-bottom:10px}
        .upgradeinfo .proto{color:#ff1493;border-bottom:1px solid #ccc;margin-bottom:10px}
        .upgradeinfo .profile{border-bottom:1px solid #ccc;margin-bottom:10px}
        .upgradeinfo .profile>div{margin-top:3px;background-color:#f6ecf7;max-height:91px;overflow:auto}
        .upgradeinfo .profile>div>p{line-height:22px;padding:0 5px;margin:0;white-space:nowrap;border-bottom:1px dotted #aaa}
        .upgradeinfo .profile>div>p:last-child{border-bottom:0 dotted #aaa}
        .upgradeinfo .proinfo>div{padding:5px;margin-top:3px;background-color:#f0f7ec;max-height:200px;overflow:auto}
        .fromto{text-align:center;color:#969696}
    </style>
</head>
<body>

{#include file="header_admin.tpl"#}
<script>$('#head-nav-menu li:eq(2)').addClass('active');</script>


<div class="breadcrumb-wrapper">
    <div class="breadcrumb">
        <div class="items">
            <span class="description">当前位置：</span>
            <span class="current">系统管理</span>
            <i class="iconfont">&#xe621;</i>
            <span class="current">微窗云中心绑定</span>
        </div>
    </div>
</div>

<div class="main-wrapper">
    <div class="main">
        <div class="row cf">
            <div class="col" style="margin-left:23px;">
                {#include file="setting_left.tpl"#}
            </div>
            <div class="col" style="width: 1050px;">
                <div class="main-content">
                    <div class="module">
                        <div class="section section-minh">
                            <div class="utis">注：更新时请注意备份网站数据和相关数据库文件！官方不强制要求用户跟随官方意愿进行更新尝试！</div>
                            <div class="control-group cf">
                                <input class="button button-primary button-upgrade" type="button" id="upbtn" value="立即检查新版本">
                                <div class="fromto">当前程序版本：{#$smarty.const.ES_VERSION#}_{#$smarty.const.ES_RELEASE#}</div>
                                <script>$('#setting-nav-menu li#nav-upgrade a').css('color','#ff6600');</script>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
    function upgrade_step2(json, i, func) {
        if (typeof (json) == "object") {
            var path = "";
            var path_type = "";
            var j = 0;
            $.each(json, function(idx, obj) {
                j++;
                if (path == "") {
                    if (i == 0) {
                        path = obj.path; path_type = obj.type; i = j;
                    }else if (i > 0 && i < j) {
                        path = obj.path; path_type = obj.type; i = j;
                    }
                }
            });
            if (path != "") {
                $.alert("正在更新文件("+i+"/"+j+"): " + path, 0, 1);
                $.ajax({
                    url: "{#$urlarr.now#}",
                    type: "post",
                    data: {method: 'upgrade_step2', path: path, path_type: path_type},
                    dataType: "json",
                    success: function (data) {
                        if (data != null && data.success != null && data.success) {
                            $.alert("成功更新文件("+i+"/"+j+"): " + path, 0, 1);
                            upgrade_step2(json, i, func);
                        }else{
                            $.alert("！失败更新文件("+i+"/"+j+"): " + path, 0);
                        }
                    },error : function () {
                        $.alert("！！错误更新文件("+i+"/"+j+"): " + path, 0);
                    },
                    cache: false
                });
            }else{
                if (func) func();
            }
        }
    }
    function upgrade_step3() {
        $.ajax({
            url: "{#$urlarr.now#}",
            type: "post",
            data: {method: 'upgrade_step3'},
            dataType: "json",
            success: function (data) {
                $.alert(data.message);
                if (data != null && data.success != null && data.success) {
                    setTimeout(function(){
                        window.location.reload();
                    }, 800);
                }
            },error : function () {
                $.alert("！！升级相关数据错误，请联系管理员", 0);
            },
            cache: false
        });

    }
    $(document).ready(function() {
        $('#upbtn').click(function() {
            $.alert('正在检查新版....', 0, 1);
            $.post('{#$urlarr.now#}', {method: 'upgrade_step1'},function(dat){
                try {
                    $.alert(0);
                    var ret = $.parseJSON(dat);
                    if (ret.success == 1) {
                        art.dialog({
                            title: '检测到新版本',
                            fixed: true,
                            lock: true,
                            opacity: '.3',
                            content: "<div class='upgradeinfo'>"+ret.message+"</div>",
                            button: [{
                                name: '立即升级',
                                focus: true,
                                callback: function () {
                                    $.alert("正在更新升级文件...",0,1);
                                    upgrade_step2(ret.filelist, 0, function(){
                                        $.alert("更新升级文件完成，正在升级相关数据...", 0, 1);
                                        setTimeout(function(){
                                            upgrade_step3();
                                        }, 500);
                                    });
                                    upgrade_step2(ret.filelist);
                                    return true;
                                }
                            },{
                                name: '关闭',
                                callback: function () {
                                    return true;
                                }
                            }]
                        });
                    }else{
                        art.dialog({
                            fixed: true,
                            lock: true,
                            opacity: '.3',
                            content: ret.message,
                            button: [{
                                name: '关闭',
                                callback: function () {
                                    return true;
                                }
                            }]
                        });
                    }
                } catch(err) {
                    $.alert(0);
                }
            });
        });
        {#if $_GPC['winopen']#}
        $('#upbtn').click();
        {#/if#}
    });
</script>
{#include file="footer_admin.tpl"#}

</body>
</html>