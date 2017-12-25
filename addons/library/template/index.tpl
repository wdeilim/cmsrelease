
<!DOCTYPE html>
<html>
<head lang="zh-CN">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{#$func.f.title#} - {#$BASE_NAME#}</title>
    <link rel="stylesheet" href="{#$CSS_PATH#}normalize.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}global.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}library.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}alert.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}font-awesome.css"/>
    <script type="text/javascript" src="{#$JS_PATH#}jquery-1.11.0.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.alert.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.form.min.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.lazyload.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}masonry-docs.min.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}artdialog/artDialog.js?skin=default"></script>
</head>
<body>
{#template("header")#}


<div class="wrapper">
    <div class="breadcrumb">
        <div class="items">
            <span class="description">当前位置：</span>
            <span>{#$func.f.title#}</span>
        </div>
    </div>
    <div class="main cf custom-menu">
        <div class="mod">
            <div class="main-bd">
                <div class="control-top clearfix">
                    <div style="float: left;">
                        <a class="button button-hover" href="{#$urlarr.2#}{#get_get()#}">素材列表</a>
                        <a class="button" href="{#$urlarr.2#}onlyimg/{#get_get()#}">+新建单图文素材</a>
                        <a class="button" href="{#$urlarr.2#}manyimg/{#get_get()#}">+新建多图文素材</a>
                        {#if $_A.al.wx_appid && $_A.al.wx_level != 7#}
                            <a class="button" href="javascript:void(0)" onclick="downweixin();">↓导入微信素材</a>
                        {#/if#}
                    </div>
                    <div style="float: right;" class="form form-inline">
                        <div class="form-group">
                            <input type="text" id='keyval' class="form-control inp2" value="{#$keyval#}" data-val="{#$keyval#}" placeholder="输入搜索关键字" />
                        </div>
                        <div class="form-group">
                            <button class="button" onclick="keybut();">搜索</button>
                        </div>
                    </div>
                </div>
                {#ddb_pc set="数据表:library,列表名:lists,显示数目:30,分页显示:1,分页名:pagelist,当前页:{#$page#},分页地址:{#$pageurl#}index/(?)/{#get_get()#},排序:indate desc" where="{#$wheresql#}"#}
                {#if $pagelist#}
                    <div id="pagelist" style="height: 35px;">
                        {#$pagelist#}
                    </div>
                {#/if#}
                <div class="appmsg-list cf" id="appmsg-list">
                    {#foreach from=$lists item=list#}
                        {#if $list.type=='manyimg'#}
                            <div id="appmsg-col-{#$list.id#}" class="appmsg-col">
                                <div class="appmsg multi">
                                    <div class="appmsg-content">
                                        <div class="appmsg-info">
                                            <em class="appmsg-date">{#$list.indate|date_format_cn:"%Y年%m月%d"#}</em>
                                        </div>
                                        {#foreach from=string2array($list.setting) item=list2 name=foo#}
                                            {#if !empty($smarty.foreach.foo.first)#}
                                                <div class="cover-appmsg-item">
                                                    <h4 class="appmsg-title"><a
                                                                href="{#appurl(0,'library',$list.id,$smarty.foreach.foo.index)#}"
                                                                target="_blank">{#$list2.title#}</a></h4>
                                                    <div class="appmsg-thumb-wrap">
                                                        <img src="{#$list2.img|fillurl#}" alt="{#$list2.title|fillurl#}" class="appmsg-thumb">
                                                    </div>
                                                </div>
                                            {#else#}
                                                <div class="appmsg-item cf">
                                                    <img src="{#$list2.img|fillurl#}" alt="{#$list2.title|fillurl#}" class="appmsg-thumb">
                                                    <h4 class="appmsg-title"><a
                                                                href="{#appurl(0,'library',$list.id,$smarty.foreach.foo.index)#}"
                                                                target="_blank">{#$list2.title#}</a></h4>
                                                </div>
                                            {#/if#}
                                        {#/foreach#}
                                    </div>
                                    <div class="appmsg-operate">
                                        <ul class="cf">
                                            <li class="appmsg-operate-item">
                                                <a href="{#weburl(0,'library',$list.type,$list.id)#}" target="_blank"><i class="iconfont">&#xe626;</i></a>
                                            </li>
                                            <li class="appmsg-operate-item">
                                                <a href="javascript:;" onclick="_del({#$list.id#});">
                                                    <i class="iconfont">&#xe61b;</i>
                                                </a>
                                            </li>
                                        </ul>
                                        <div class="appmsg-line"></div>
                                    </div>
                                </div>
                            </div>
                        {#else#}
                            <div id="appmsg-col-{#$list.id#}" class="appmsg-col">
                                <div class="appmsg">
                                    <div class="appmsg-content">
                                        <h4 class="appmsg-title"><a href="{#appurl(0,'library',$list.id)#}" target="_blank">{#$list.title#}</a></h4>
                                        <div class="appmsg-info">
                                            <em class="appmsg-date">{#$list.indate|date_format_cn:"%Y年%m月%d"#}</em>
                                        </div>
                                        <div class="appmsg-thumb-wrap">
                                            <img class="appmsg-thumb" src="{#$list.img|fillurl#}" alt="{#$list.title#}">
                                        </div>
                                        <p class="appmsg-desc">{#$list.descriptions#}</p>
                                    </div>
                                    <div class="appmsg-operate">
                                        <ul class="cf">
                                            <li class="appmsg-operate-item">
                                                <a href="{#weburl(0,'library',$list.type,$list.id)#}" target="_blank"><i class="iconfont">&#xe626;</i></a>
                                            </li>
                                            <li class="appmsg-operate-item">
                                                <a href="javascript:;" onclick="_del({#$list.id#});">
                                                    <i class="iconfont">&#xe61b;</i>
                                                </a>
                                            </li>
                                        </ul>
                                        <div class="appmsg-line"></div>
                                    </div>
                                </div>
                            </div>
                        {#/if#}

                        {#foreachelse#}
                        <div style="text-align: center; margin-top: 100px;"> 无 </div>
                    {#/foreach#}

                </div>
                <div id="pagelist" class="clearfix">
                    {#$pagelist#}
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function downweixin() {
        $.alert("　", 0);
        if (confirm('确定将微信公众平台[官方]素材库中的图文素材导入到这里吗？')) {
            $.alert("正在导入, 请稍等...", 0);
            setTimeout(function(){
                window.location.href = "{#$urlarr.2#}downweixin/{#get_get()#}";
            }, 200);
        }else{
            $.alert(0);
        }
    }
    function keybut(){
        var keyval = $('#keyval').val().trim();
        if (keyval == ''){
            if ($('#keyval').attr('data-val')){
                window.location.href = "{#$urlarr.2#}index/1/{#get_get('keyval|id')#}"; return;
            }else{
                alert("请输入搜索关键词"); $('#keyval').focus(); return;
            }
        }
        window.location.href = "{#$urlarr.2#}index/{#get_get('keyval|id')#}&keyval="+encodeURIComponent(keyval);
    }
    function _del(id){
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
                        url: '{#$urlarr.2#}dellib/'+id+'/{#get_get()#}',
                        dataType: 'json',
                        success: function (data) {
                            if (data != null && data.success != null && data.success) {
                                $.alert(data.message);
                                $("#appmsg-col-"+id).fadeOut();
                                setTimeout(function(){$("#appmsg-col-"+id).remove();},3000);
                            } else {
                                $.alert(0);
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
    $(document).ready(function () {
        $('#appmsg-list').masonry({
            itemSelector: '.appmsg-col',
            gutterWidth: 20,
            isAnimated: true,
        }).find("img").lazyload({
            placeholder: "{#$IMG_PATH#}grey.gif",
            effect: "fadeIn"
        });
    });
</script>

{#template("footer")#}

</body>
</html>