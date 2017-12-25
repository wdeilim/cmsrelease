
<!DOCTYPE html>
<html>
<head lang="zh-CN">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{#$submit#}微信企业号 - {#$BASE_NAME#}</title>
    <link rel="stylesheet" href="{#$CSS_PATH#}normalize.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}global.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}alert.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}font-awesome.css"/>
    <link rel="stylesheet" href="{#$NOW_PATH#}css/style.css"/>
    <style type="text/css">
        .trcorp {

        }
        .trcorp > td {
            vertical-align: top;
        }
        .trcorp > td > a {
            display: block;
            color: #257ad0;
            padding-top: 5px;
        }
        .trcorp > td:first-child {
            padding-top: 20px !important;
        }
        .trcorp table {
            background:#ececec
        }
        .trcorp table td {
            padding: 5px 10px !important;
        }
        .trcorp table tr:last-child td {
            padding: 5px 10px 10px !important;
        }
        .trcorp table td div {
            margin-bottom: 2px;
        }
    </style>
    <script type="text/javascript" src="{#$JS_PATH#}jquery-1.11.0.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.alert.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.form.min.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}linkage.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}zero/ZeroClipboard.js"></script>
</head>
<body>
{#include file="header_user.tpl"#}
<script>$('#head-nav-menu li:eq(0)').addClass('active');</script>


<div class="wrapper">
    <div class="breadcrumb">
        <div class="items">
            <a href="{#$urlarr.2#}">平台首页</a>
            <i class="iconfont">&#xe621;</i>
            <span>微信企业号管理</span>
        </div>
    </div>
    <div class="main add-mp">
        <div class="hd">
            <h1 class="title">{#$submit#}微信企业号 (Alpha)</h1>
            <div class="control">
                {#if $submit == '添加'#}
                    <a href="{#weburl('system/add')#}" class="button button-primary button-rounded button-small">添加公众号/服务窗</a>&nbsp;&nbsp;
                {#/if#}
                <a href="{#$urlarr.2#}" class="button button-primary button-rounded button-small">返回首页</a>
            </div>
        </div>
        <div class="bd">
            <div class="section" style="position:relative;">
                {#if (value($edit,'icon'))#}
                    <div class="alicon" style="position:absolute;top:35px;right:100px;">
                        <img src="{#value($edit,'icon')|fillurl#}"/>
                    </div>
                {#/if#}
                <form action="{#$urlarr.now#}"  method="post" id="addform" enctype="multipart/form-data">
                    <div class="topmenu" id="topmenu">
                        <a href="javascript:;" d-index="0">微信企业号</a>
                    </div>
                    <table class="table table-form tabmenu">
                        <tbody>
                        <tr>
                            <td class="al-right" width="130"><span>企业名称</span></td>
                            <td><input class="form-control" id="wx_name" type="text" name="wx_name" value="{#value($edit,'wx_name')#}"/></td>
                        </tr>
                        {#if $edit#}
                            <tr class="trcorp">
                                <td class="al-right">
                                    <span>回调URL及密钥</span>
                                    <a href="javascript:void(0);" onclick="unapp();">更新应用列表</a>
                                </td>
                                <td>
                                    <table class="table table-form">
                                        <tbody>
                                        <tr>
                                            <td><div>URL (企业小助手)</div>
                                                <input class="form-control" id="_wxurl-0" type="text"
                                                       value="{#$urlarr.index#}weixin/{#value($edit,'id')#}-0/{#if strexists($urlarr.index,'?')#}?index{#/if#}" disabled="disabled"/>
                                                <a id="_wxurlbut-0" class="normal-link">复制</a></td>
                                        </tr>
                                        {#foreach $edit['wx_corp'] AS $item#}
                                            {#if $item['agentid'] > 0 && $item['type'] == 1#}
                                                <tr>
                                                    <td><div>URL ({#$item['name']#})</div>
                                                        <input class="form-control" id="_wxurl-{#$item['agentid']#}" type="text"
                                                               value="{#$urlarr.index#}weixin/{#value($edit,'id')#}-{#$item['agentid']#}/{#if strexists($urlarr.index,'?')#}?index{#/if#}" disabled="disabled"/>
                                                        <a id="_wxurlbut-{#$item['agentid']#}" class="normal-link">复制</a></td>
                                                </tr>
                                            {#/if#}
                                        {#/foreach#}
                                        <tr>
                                            <td><div>Token</div>
                                                <input class="form-control" id="wx_token" type="text" name="wx_token" value="{#value($edit,'wx_token')#}"/></td>
                                        </tr>
                                        <tr>
                                            <td><div>EncodingAESKey</div>
                                                <input class="form-control" id="wx_aeskey" type="text" name="wx_aeskey" value="{#value($edit,'wx_aeskey')#}"/></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        {#else#}
                            <tr>
                                <td class="al-right"><span>回调URL及密钥</span></td>
                                <td>
                                    <input class="form-control" value="添加后可查看" style="color:#BBB;" disabled="disabled"/>
                                </td>
                            </tr>
                        {#/if#}
                        <tr>
                            <td class="al-right"><span>CorpID</span></td>
                            <td><input class="form-control" id="wx_appid" type="text" name="wx_appid" value="{#value($edit,'wx_appid')#}"/></td>
                        </tr>
                        <tr>
                            <td class="al-right"><span>CorpSecret</span></td>
                            <td><input class="form-control" id="wx_secret" type="text" name="wx_secret" value="{#value($edit,'wx_secret')#}"/></td>
                        </tr>
                        <tr style="display:none;">
                            <td class="al-right"><span>服务所在地区</span></td>
                            <td class="form-reg">
                                <input class="form-control" type="text" name="linkaddr" id="linkaddr" value="{#value($edit,'linkaddr','',$user.linkaddr)#}"/>
                            </td>
                        </tr>
                        <tr>
                            <td class="al-right"><span>二维码图片</span></td>
                            <td>{#tpl_form_image("wx_qrcode", value($edit,'wx_qrcode'))#}</td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td>
                                <input class="button button-primary button-rounded" type="submit" value="{#$submit#}"/>
                                <input class="button button-primary button-rounded backing" type="button" value="返回"/>
                                <input type="hidden" name="dosubmit" value="1"/>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
    function unapp() {
        $.alert("正在更新...", 0, 1);
        $.ajax({
            url: "{#$urlarr.now#}",
            type: "post",
            data: {act: 'upapp'},
            dataType: "html",
            success: function (data) {
                $.alert(data);
                setTimeout(function(){
                    window.location.reload();
                }, 800);
            },error : function () {
                $.alertk("系统繁忙请稍后再试...");
            },
            cache: false
        });
    }
    $(document).ready(function() {
        ZeroClipboard.setMoviePath("{#$JS_PATH#}zero/ZeroClipboard.swf");
        $("input.backing").click(function(){
            window.location.href = "{#$urlarr.2#}";
        });
        $('#topmenu').find("a").each(function(index) {
            $(this).click(function(){
                $('#topmenu').find("a").removeClass("active");
                $(this).addClass("active");
                $("table.tabmenu").hide().eq(index).show();
                //
                var clipwx_0 = new ZeroClipboard.Client();
                clipwx_0.setHandCursor(true);
                clipwx_0.setText($("#_wxurl-0").val());
                clipwx_0.addEventListener('complete', function(){ $.alertk("复制成功！"); });
                clipwx_0.glue('_wxurlbut-0');
                {#foreach $edit['wx_corp'] AS $item#}
                {#if $item['agentid'] > 0 && $item['type'] == 1#}
                var clipwx_{#$item['agentid']#} = new ZeroClipboard.Client();
                clipwx_{#$item['agentid']#}.setHandCursor(true);
                clipwx_{#$item['agentid']#}.setText($("#_wxurl-{#$item['agentid']#}").val());
                clipwx_{#$item['agentid']#}.addEventListener('complete', function(){ $.alertk("复制成功！"); });
                clipwx_{#$item['agentid']#}.glue('_wxurlbut-{#$item['agentid']#}');
                {#/if#}
                {#/foreach#}
            });
        }).eq(0).click();
        $('#addform').submit(function() {
            var retu = true;
            retu = $('#wx_name').inTips("", 2, retu);
            retu = $('#wx_appid').inTips("", -1, retu);
            retu = $('#wx_secret').inTips("", -1, retu);
            if (!retu) $('a[d-index="0"]').click();
            if (!retu) return false;
            //
            $.alert('正在保存...', 0);
            $(this).ajaxSubmit({
                dataType : 'json',
                success : function (data) {
                    $.alert(0);
                    if (data != null && data.success != null && data.success) {
                        $.showModal(data.message, '{#$urlarr.2#}addcorp/'+data.id+'/');
                    } else {
                        $.showModal(data.message);
                    }
                },
                error : function () {
                    $.alert(0);
                    $.inModal("提交失败！");
                }
            });
            return false;
        });
        linkage("linkaddr","{#$urlarr.index#}web/system/linkage/",0,0);
    });
</script>


{#include file="footer.tpl"#}

</body>
</html>