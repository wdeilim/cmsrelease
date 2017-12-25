
<!DOCTYPE html>
<html>
<head lang="zh-CN">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{#$_A.f.title#} - {#$BASE_NAME#}</title>
    <link rel="stylesheet" href="{#$CSS_PATH#}normalize.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}global.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}vip.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}alert.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}font-awesome.css"/>
    <style type="text/css">
        .formfile-inputbox .form-control {width:210px;}
    </style>
    <script type="text/javascript" src="{#$JS_PATH#}jquery-1.11.0.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.alert.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.form.min.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}ajaxfileupload.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.colorpicker-title.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}artdialog/artDialog.js?skin=default"></script>
</head>
<body>
{#template("header")#}


<div class="wrapper">

    {#include file="menu.tpl" _item=2 _itemp=3#}


    <div class="main cf custom-menu">
        <div class="mod">
            <div class="main-bd main-card">
                <h1 class="title">会员卡版面设置</h1>
                <p class="description">开始 DIY 你的会员卡吧，logo，背景以及字体颜色都可以自由调整。</p>
                <div class="clearfix"></div>

                <div class="m-left">
                    <div class="vip-card-image">
                        <div class="vip-card-box">
                            <img src="{#value($hykbm,'_bgimg')|fillurl#}" alt="" class="vip-pic">
                            <h1 class="vip-name"{#cot(value($hykbm,'namecolor'))#}>{#value($hykbm,'name')#}</h1>
                            <img src="{#value($hykbm,'logo')|fillurl#}" alt="" class="vip-logo">
                            <div class="vip-num"><p>会员卡号</p><span{#cot(value($hykbm,'numcolor'))#}>800000000000</span></div>
                        </div>
                    </div>
                </div>
                <div class="m-right">
                    <div class="mod mod-rounded mod-bordered">
                        <form action="{#weburl()#}"  method="post" id="saveform" class="form-services">
                            <table class="table table-form">
                                <tbody>
                                <tr>
                                    <td><span>会员卡名称</span></td>
                                    <td>
                                        <input name="name" type="text" class="form-control" id="name" value="{#value($hykbm,'name')#}" onkeyup="resetcard();"/>
                                        <img src="{#$IMG_PATH#}colour.png" onclick="colorpicker(this);"/>
                                        <input type="hidden" id="namecolor" name="namecolor" value="{#value($hykbm,'namecolor')#}" onblur="resetcard();"/>
                                    </td>
                                    <script type="text/javascript">$("input#name").css("color", $("input#namecolor").val());</script>
                                </tr>
                                <tr>
                                    <td><span>卡名称位置</span></td>
                                    <td class="form-reg">
                                        <select name="position" id="position">
                                            <option value="0">右上角</option>
                                            <option value="1">左上角</option>
                                        </select>
                                        {#if value($hykbm,'position')#}
                                            <script>$("#position").val("{#value($hykbm,'position')#}");</script>
                                        {#/if#}
                                    </td>
                                </tr>
                                <tr>
                                    <td><span>会员卡 LOGO 标志</span></td>
                                    <td>
                                        {#tpl_form_image("logo", value($hykbm,'logo'), '', string2array("array('tabs'=>array('browser'=>''))"), 'resetcard()')#}
                                    </td>
                                </tr>
                                <tr>
                                    <td><span>会员卡背景图</span></td>
                                    <td class="form-reg">
                                        <select name="bgimg" id="bgimg" onchange="selcardbg(this);">
                                            <option value=""{#sel(value($hykbm,'bgimg'),'')#}>自定义</option>
                                            {#foreach from=$patharr item=item#}
                                            <option value="{#$item#}"{#sel(value($hykbm,'bgimg'),$item)#}>{#$item#}</option>
                                            {#/foreach#}
                                        </select>
                                    </td>
                                </tr>
                                <tr id="diybgimg" style="display:none;">
                                    <td><span>自定义背景图</span></td>
                                    <td>
                                        {#tpl_form_image("bgimgduv", value($hykbm,'bgimgduv'), '', string2array("array('tabs'=>array('browser'=>''))"), 'resetcard()')#}
                                    </td>
                                    <script>if (!$("#bgimg").val()) { $("#diybgimg").show() };</script>
                                </tr>
                                <tr>
                                    <td><span>会员卡号颜色</span></td>
                                    <td>
                                        <input type="text" class="form-control" id="carddisabled" style="background: #F3F3F3;" value="0000 0000 0000" readonly
                                               onclick="colorpicker($(this).next('img'));"/>
                                        <img src="{#$IMG_PATH#}colour.png" onclick="colorpicker(this);"/>
                                        <input type="hidden" id="numcolor" name="numcolor" value="{#value($hykbm,'numcolor')#}" onblur="resetcard();"/>
                                    </td>
                                    <script>$("input#carddisabled").css("color", $("input#numcolor").val());</script>
                                </tr>

                                <tr>
                                    <td><span>卡片提示文字</span></td>
                                    <td>
                                        <input name="text" type="text" class="form-control" id="text" value="{#value($hykbm,'text')#}">
                                    </td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>
                                        <div class="control-submit">
                                            <input class="button" type="submit" value="保存" style="width:120px;"> &nbsp;
                                            <input type="hidden" name="dosubmit" value="1">
                                            <input type="hidden" name="type" value="hykbm" />
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </form>
                    </div>
                </div>
                <div class="clearfix"></div>

                <div style="margin-top: 35px;"></div>
                <div class="clearfix"></div>

                <h1 class="title">各内容页头部 Banner 图片设置</h1>
                <p class="description">根据你企业的特色设计内容页头部图片，完全展示出不同的会员卡风格。</p>
                <div class="clearfix"></div>

                <p style="color: #999999; margin-bottom: 40px;">
                    备注：图片宽640px，高220px或者更高，但是不要高太多，图片类型为jpg，签到图片外与其他图片高度不同，尽量根据模板图片修改。</p>
                <form action="{#weburl()#}"  method="post" id="saveform2" class="form-services">
                    <ul class="mobile-item-list" id="item-list">
                        <li>
                            <div class="mobile-item">
                                <div class="mobile-page">
                                    <div class="ptitle">特权</div>
                                    <div class="banner">
                                        <img src="{#value($banner,'privilege')|fillurl#}" alt="" id="bannerimg1"/>
                                    </div>
                                    <div class="mobile-content"></div>
                                </div>
                                <div class="edit">
                                    <div class="control-group">
                                        <div>
                                            {#tpl_form_image("privilege", value($banner,'privilege'), '', string2array("array('tabs'=>array('browser'=>''))"), 'resetbanner()')#}
                                        </div>
                                    </div>
                                    <div style="text-align: center" class="control-group">
                                        <div class="button primary" onclick="regain('privilege','bannerimg1')">恢复默认</div>
                                    </div>
                                </div>
                            </div>
                        </li>


                        <li>
                            <div class="mobile-item">
                                <div class="mobile-page">
                                    <div class="ptitle">优惠券</div>
                                    <div class="banner">
                                        <img src="{#value($banner,'coupon')|fillurl#}" alt="" id="bannerimg2"/>
                                    </div>
                                    <div class="mobile-content"></div>
                                </div>
                                <div class="edit">
                                    <div class="control-group">
                                        <div>
                                            {#tpl_form_image("coupon", value($banner,'coupon'), '', string2array("array('tabs'=>array('browser'=>''))"))#}
                                        </div>
                                    </div>
                                    <div style="text-align: center" class="control-group">
                                        <div class="button primary" onclick="regain('coupon','bannerimg2')">恢复默认</div>
                                    </div>
                                </div>
                            </div>
                        </li>

                        <li>
                            <div class="mobile-item">
                                <div class="mobile-page">
                                    <div class="ptitle">礼品券</div>
                                    <div class="banner">
                                        <img src="{#value($banner,'giftcert')|fillurl#}" alt="" id="bannerimg3"/>
                                    </div>
                                    <div class="mobile-content"></div>
                                </div>
                                <div class="edit">
                                    <div class="control-group">
                                        <div>
                                            {#tpl_form_image("giftcert", value($banner,'giftcert'), '', string2array("array('tabs'=>array('browser'=>''))"))#}
                                        </div>
                                    </div>
                                    <div style="text-align: center" class="control-group">
                                        <div class="button primary" onclick="regain('giftcert','bannerimg3')">恢复默认</div>
                                    </div>
                                </div>
                            </div>
                        </li>

                        <li>
                            <div class="mobile-item">
                                <div class="mobile-page">
                                    <div class="ptitle">签到</div>
                                    <div class="banner">
                                        <img src="{#value($banner,'sign_in')|fillurl#}" alt="" id="bannerimg4"/>
                                    </div>
                                    <div class="mobile-content"></div>
                                </div>
                                <div class="edit">
                                    <div class="control-group">
                                        <div>
                                            {#tpl_form_image("sign_in", value($banner,'sign_in'), '', string2array("array('tabs'=>array('browser'=>''))"))#}
                                        </div>
                                    </div>
                                    <div style="text-align: center" class="control-group">
                                        <div class="button primary" onclick="regain('sign_in','bannerimg4')">恢复默认</div>
                                    </div>
                                </div>
                            </div>
                        </li>

                        <li>
                            <div class="mobile-item">
                                <div class="mobile-page">
                                    <div class="ptitle">关于会员卡</div>
                                    <div class="banner">
                                        <img src="{#value($banner,'about')|fillurl#}" alt="" id="bannerimg5"/>
                                    </div>
                                    <div class="mobile-content"></div>
                                </div>
                                <div class="edit">
                                    <div class="control-group">
                                        <div>
                                            {#tpl_form_image("about", value($banner,'about'), '', string2array("array('tabs'=>array('browser'=>''))"))#}
                                        </div>
                                    </div>
                                    <div style="text-align: center" class="control-group">
                                        <div class="button primary" onclick="regain('about','bannerimg5')">恢复默认</div>
                                    </div>
                                </div>
                            </div>
                        </li>

                        <li>
                            <div class="mobile-item">
                                <div class="mobile-page">
                                    <div class="ptitle">最新通知</div>
                                    <div class="banner">
                                        <img src="{#value($banner,'notification')|fillurl#}" alt="" id="bannerimg6"/>
                                    </div>
                                    <div class="mobile-content"></div>
                                </div>
                                <div class="edit">
                                    <div class="control-group">
                                        <div>
                                            {#tpl_form_image("notification", value($banner,'notification'), '', string2array("array('tabs'=>array('browser'=>''))"))#}
                                        </div>
                                    </div>
                                    <div style="text-align: center" class="control-group">
                                        <div class="button primary" onclick="regain('notification','bannerimg6')">恢复默认</div>
                                    </div>
                                </div>
                            </div>
                        </li>

                        <li>
                            <div class="mobile-item">
                                <div class="mobile-page">
                                    <div class="ptitle">会员资料</div>
                                    <div class="banner">
                                        <img src="{#value($banner,'personal')|fillurl#}" alt="" id="bannerimg7"/>
                                    </div>
                                    <div class="mobile-content"></div>
                                </div>
                                <div class="edit">
                                    <div class="control-group">
                                        <div>
                                            {#tpl_form_image("personal", value($banner,'personal'), '', string2array("array('tabs'=>array('browser'=>''))"))#}
                                        </div>
                                    </div>
                                    <div style="text-align: center" class="control-group">
                                        <div class="button primary" onclick="regain('personal','bannerimg7')">恢复默认</div>
                                    </div>
                                </div>
                            </div>
                        </li>

                        <li>
                            <div class="mobile-item">
                                <div class="mobile-page">
                                    <div class="ptitle">分店信息</div>
                                    <div class="banner">
                                        <img src="{#value($banner,'shoplist')|fillurl#}" alt="" id="bannerimg8"/>
                                    </div>
                                    <div class="mobile-content"></div>
                                </div>
                                <div class="edit">
                                    <div class="control-group">
                                        <div>
                                            {#tpl_form_image("shoplist", value($banner,'shoplist'), '', string2array("array('tabs'=>array('browser'=>''))"))#}
                                        </div>
                                    </div>
                                    <div style="text-align: center" class="control-group">
                                        <div class="button primary" onclick="regain('shoplist','bannerimg8')">恢复默认</div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                    <div class="clearfix"></div>
                    <div align="center">
                        <input type="hidden" name="type" value="banner" />
                        <input type="submit" name="dosubmit" class="button long" value="保存" />
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    {#if $dosubmit#}$.alert("保存成功");{#/if#}
    $("#item-list li").each(function(){
        if (!$(this).find("img").attr("src")){
            $(this).find("img").attr("src","{#$IMG_PATH#}vipbanner/"+$(this).find("input").attr("id")+".jpg");
        }
    });
    //刷新卡面
    function resetcard() {
        var eve = $(".vip-card-box");
        eve.find(".vip-name").text($("#name").val()).css("color", $("#namecolor").val());
        eve.find(".vip-num span").css("color", $("#numcolor").val());
        var pic = ($("#bgimg").val())?"{#fillurl("uploadfiles/vipcardbg/")#}"+$("#bgimg").val():$("#bgimgduv").val();
        if (!pic) pic = "{#$smarty.const.BASE_DIR#}uploadfiles/vipcardbg/1.jpg";
        if (pic.indexOf("http") == -1){
            pic = (pic)?"{#fillurl("./")#}"+pic:'';
        }
        eve.find(".vip-pic").attr("src", pic);
        var logo = $("#logo").val();
        if (logo.indexOf("http") == -1){
            logo = (logo)?"{#fillurl("./")#}"+logo:'';
        }
        eve.find(".vip-logo").attr("src", logo);
    }
    //刷新banner
    function resetbanner() {
        $("#item-list li").each(function(){
            var val = $(this).find("input").val();
            if (val) {
                if (val.substring(0,12) == 'uploadfiles/') val = "{#$smarty.const.BASE_DIR#}" + val;
                $(this).find("img").attr("src", val);
            }else{
                $(this).find("img").attr("src","{#$IMG_PATH#}vipbanner/"+$(this).find("input").attr("id")+".jpg");
            }
        });
    }
    //选择背景图
    function selcardbg(obj) {
        if ($(obj).val()) {
            $("#diybgimg").hide();
        }else{
            $("#diybgimg").show();
        }
        resetcard();
    }

    function regain(id, src){
        $('#'+id).val("");
        $('#'+src).attr("src", "{#$IMG_PATH#}vipbanner/"+id+".jpg");
    }
</script>

{#template("footer")#}

</body>
</html>