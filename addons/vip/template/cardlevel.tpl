
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
    <script type="text/javascript" src="{#$JS_PATH#}jquery.colorpicker-title.js"></script>
</head>
<body>
{#template("header")#}


<div class="wrapper">

    {#include file="menu.tpl" _item=2 _itemp=1#}


    <div class="main cf custom-menu">
        <div class="mod">
            <div class="main-bd main-card">
                <h1 class="title">会员等级设置</h1>
                <p class="description">设置每个会员等级的积分</p>
                <div class="clearfix"></div>

                <form action="{#weburl()#}"  method="post" id="saveform" class="form-services">
                    <table class="table table-primary" id="menu-table" style="margin-top: 10px;">
                        <thead>
                        <tr>
                            <th>会员等级</th>
                            <th>积分设置</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody id="level_list" class="level_list">

                        {#foreach from=$jfdj item=list#}
                        <tr>
                            <td class="align-center">
                                <input type="text" id="name" value="{#value($list,'name')#}"/>
                                <img src="{#$IMG_PATH#}colour.png" onclick="colorpicker(this);"/>
                                <input type="hidden" id="color" value="{#value($list,'color')#}"/>
                            </td>
                            <td class="align-center">
                                <input type="text" id="lev_a" value="{#value($list,'lev_a')#}"/>
                                <span style="margin: 0 10px;">-</span>
                                <input type="text" id="lev_b" value="{#value($list,'lev_b')#}"/>
                            </td>
                            <td class="align-center"><a href="javascript:;" onclick="lev_del(this);">删除</a></td>
                        </tr>
                        {#/foreach#}

                        </tbody>


                        <tbody id="lev_templet" style="display:none;">
                            <tr>
                                <td class="align-center">
                                    <input type="text" id="name" value=""/>
                                    <img src="{#$IMG_PATH#}colour.png" onclick="colorpicker(this);"/>
                                    <input type="hidden" id="color" value=""/>
                                </td>
                                <td class="align-center">
                                    <input type="text" id="lev_a" value=""/>
                                    <span style="margin: 0 10px;">-</span>
                                    <input type="text" id="lev_b" value=""/>
                                </td>
                                <td class="align-center"><a href="javascript:;" onclick="lev_del(this);">删除</a></td>
                            </tr>
                        </tbody>

                    </table>
                    <div style="margin-top:15px">
                        <input type="hidden" id="list_n" name="list_n">
                        <div class="button" onclick="add_lev();">添加等级</div> &nbsp;
                        <input type="submit" name="dosubmit" class="button" value="保存设置" />
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    {#if $dosubmit#}$.alert("保存成功");{#/if#}

    lev_list();
    function lev_list(){
        var i = 1;
        $("#level_list tr").each(function(){
            $(this).find("input#name").attr("name", "name"+i);
            $(this).find("input#color").attr("name", "color"+i);
            $(this).find("input#lev_a").attr("name", "lev_a"+i);
            $(this).find("input#lev_b").attr("name", "lev_b"+i);
            if ($(this).find("input#color").val()){
                $(this).find("input#name").css("color", $(this).find("input#color").val());
            }
            i++;
        });
        $("#list_n").val(i-1);
    }
    function add_lev(){
        $("#level_list").append($("#lev_templet").html());
        lev_list();
    }
    function lev_del(obj){
        $(obj).parent().parent().remove();
        lev_list();
    }
</script>

{#template("footer")#}

</body>
</html>