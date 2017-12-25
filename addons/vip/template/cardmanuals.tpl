
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
    <script type="text/javascript" src="{#$JS_PATH#}jquery-1.11.0.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.alert.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.form.min.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}ueditor/ueditor.config.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}ueditor/ueditor.all.min.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}artdialog/artDialog.js?skin=default"></script>
</head>
<body>
{#template("header")#}


<div class="wrapper">

    {#include file="menu.tpl" _item=2 _itemp=2#}


    <div class="main cf custom-menu">
        <div class="mod">
            <div class="main-bd main-card">
                <h1 class="title">会员卡使用说明</h1>
                <div class="clearfix"></div>

                <form action="{#weburl()#}"  method="post" id="saveform" class="form-services">
                    <div class="form-uetext"  style="margin: 20px 0">
                        <script id="hyksm" name="hyksm" type="text/plain" style="height: 380px; width: 100%;">{#value($hyksm,'hyksm')#}</script>
                    </div>

                    <div align="center">
                        <input type="submit" name="dosubmit" class="button long" value="保存" />
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    {#if $dosubmit#}$.alert("保存成功");{#/if#}
    $(document).ready(function() {
        UE.getEditor('hyksm', {autoHeightEnabled:false});
    });
</script>

{#template("footer")#}

</body>
</html>