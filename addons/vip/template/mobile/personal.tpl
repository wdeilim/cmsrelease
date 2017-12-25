
<!DOCTYPE HTML>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta content="telephone=no" name="format-detection">
    <title>修改会员资料</title>
    <link rel="stylesheet" href="{#$NOW_PATH#}css/inner.css">
    <script type="text/javascript" src="{#$JS_PATH#}jquery.min.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.alert.js"></script>
</head>

<body>
<div class="layout">
    <header>
        <img src="{#vippagebanner('personal', $_A['vip_data']['banner'])#}" class="hd-banner">
    </header>
    <div class="personal-box">
        <ul class="">
            <li data-have="{#$_A['vip_data']['userset']['haveitem']['fullname']#}">
                <label for="">姓名</label>
                <input type="text" class="txt-input" name="fullname" id="fullname" value="{#value($_A['vip'],'fullname')#}">
            </li>
            <li data-have="{#$_A['vip_data']['userset']['haveitem']['phone']#}">
                <label for="" id="labelphone">手机</label>
                <input type="tel" class="txt-input" name="phone" id="phone" value="{#value($_A['vip'],'phone')#}">
            </li>
            <li class="codeli" {#if $_A['vip_data']['userset']['showitem']['code']#} style="display:block;" data-show="1"{#/if#}>
                <label for="">验证码<span style='color:#ff0000;font-weight:600;'>*</span></label>
                <div class="codebox">
                    <input type="text" class="txt-input" name="code" id="code" value="" placeholder="验证码必填">
                    <div class="codebtn" onclick="_codebtn(this);">获取验证码</div>
                </div>
            </li>
            {#if $_A['vip_data']['userset']['showitem']['email']#}
                <li data-have="{#$_A['vip_data']['userset']['haveitem']['email']#}">
                    <label for="">邮箱</label>
                    <input type="text" class="txt-input" name="email" id="email" value="{#value($_A['vip'],'email')#}">
                </li>
            {#/if#}
            <li>
                <label for="">性别</label>
                <select name="sex" id="sex" class="txt-select">
                    <option value="">请选择</option>
                    <option{#sel(value($_A['vip'],'sex'),'女')#}>女</option>
                    <option{#sel(value($_A['vip'],'sex'),'男')#}>男</option>
                </select>
            </li>
            {#if $_A['vip_data']['userset']['showitem']['address']#}
                <li data-have="{#$_A['vip_data']['userset']['haveitem']['address']#}">
                    <label for="">详细地址</label>
                    <input type="text" class="txt-input" name="address" id="address" value="{#value($_A['vip'],'address')#}">
                </li>
            {#/if#}
            {#if $_A['vip_data']['userset']['showitem']['idnumber']#}
                <li data-have="{#$_A['vip_data']['userset']['haveitem']['idnumber']#}">
                    <label for="">身份证</label>
                    <input type="text" class="txt-input" name="idnumber" id="idnumber" value="{#value($_A['vip'],'idnumber')#}">
                </li>
            {#/if#}
            <li>
                <button type="submit" class="btn-submit" onclick="submit();">保存</button>
            </li>
        </ul>
    </div>

    {#include file="./footer.tpl" _item=""#}

</div>


<script type="text/javascript">
    function set_refresh_code(id1,id2){if(document.getElementById(id1)){var temp_src=document.getElementById(id1).src;document.getElementById(id2).onclick=function(){document.getElementById(id1).src=temp_src;return false}}}
    set_refresh_code('yzm_img', 'yzm_img');
    //
    function submit(){
        var gotu = true;
        $(".personal-box").find("li").find("input").each(function(){
            if ($(this).attr("data-nonull") && !$(this).val()) {
                $.alert($(this).attr("placeholder"));
                $(this).focus();
                gotu = false;
                return false;
            }
        });
        if (!gotu) return false;
        var s = "dosubmit=submit&fullname="+$('#fullname').val()+
                "&phone="+$('#phone').val()+
                "&email="+$('#email').val()+
                "&captcha="+$('#captcha').val()+
                "&sex="+$('#sex').val()+
                "&address="+$('#address').val()+
                "&idnumber="+$('#idnumber').val();
        if ($(".codeli").attr("data-show") == "1") {
            if (!$("#code").val()) {
                $.alert("请输入验证码！");
                $("#code").focus();
                return false;
            }
            s+= "&code="+$("#code").val();
        }
        $.alert("正在提交...", 0, 1);
        $.ajax({
            type: "POST",
            url: "{#appurl()#}",
            data: s,
            dataType: "json",
            success: function (msg) {
                $.alert(msg.message);
                if (msg.success == "1"){
                    window.location.href = "{#if $_GPC['referer']#}{#$_GPC['referer']#}{#else#}{#appurl()#}{#/if#}";
                }else if (msg.havephone == "1"){
                    $("a.labelphone").remove();
                    $("#labelphone").append("<a href='javascript:;' onclick='phoneshow();' class='labelphone'>通过验证修改</a>");
                }
            },
            error: function (msg) {
                $.alert(0);
                alert("保存错误！");
            }
        });
    }
    //
    function phoneshow() {
        $('#phone').attr("readonly", "readonly").css({'background-color': '#F2F2F2', 'color':'#8B8B8B'});
        $(".codeli").show().attr("data-show", "1");
        $(".labelphone").remove();
    }
    //
    function _codebtn(obj) {
        var tthis = $(obj);
        if (tthis.attr("data-code") == "1") {
            return false;
        }
        $.alert("正在获取...", 0, 1);
        $.ajax({
            type: "POST",
            url: "{#appurl()#}",
            data: "dosubmit=sms&phone="+$('#phone').val(),
            dataType: "json",
            success: function (msg) {
                $.alert(msg.message+'    <br/>*点击可关闭提示！', 0);
                if (msg.success == "1"){
                    $("#code").attr("placeholder", msg.message);
                    tthis.attr("data-code", "1").css("opacity", 0.65);
                    _codeCountdown(60, tthis);
                }
            },
            error: function (msg) {
                $.alert(0);
                alert("获取错误！");
                tthis.attr("data-code", "0");
            }
        });
    }
    //
    function _codeCountdown(i, obj) {
        var tthis = $(obj);
        if (i < 1) {
            tthis.attr("data-code", "0").css("opacity", 1).text("重获验证码");
        }else{
            i--;
            tthis.text("重获验证码("+i+"秒)");
            setTimeout(function(){
                _codeCountdown(i, tthis);
            }, 1000);
        }
    }
    //
    $(function(){
        {#if $_A['vip_data']['userset']['haveinfo'] == '强制'#}
            $(".personal-box").find("li").each(function(){
                if ($(this).attr("data-have")) {
                    $(this).find("input").attr("placeholder", $(this).find("label").text() + "必填").attr("data-nonull", "1");
                    $(this).find("label").append("<span style='color:#ff0000;font-weight:600;'>*</span>");
                }
            });
            {#if $_GPC['referer']#}
                $.alert("{#if $_A['vip_data']['userset']['havetis']#}{#$_A['vip_data']['userset']['havetis']#}{#else#}请完善资料后继续访问！{#/if#}");
            {#/if#}
        {#/if#}
    });
</script>
</body>
</html>