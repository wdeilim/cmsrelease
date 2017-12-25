
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

    {#include file="menu.tpl" _item=2 _itemp=0#}


    <div class="main cf custom-menu">
        <div class="mod">
            <div class="main-bd main-card">
                <h1 class="title">积分策略设置</h1>
                <p class="description">设置会员卡积分策略</p>
                <div class="clearfix"></div>

                <form action="{#weburl()#}"  method="post" id="saveform" class="form-services">
                    <div class="form-uetext"  style="margin: 20px 0">
                        <script id="shuoming" name="shuoming" type="text/plain" style="height: 380px; width: 100%;">{#value($jfcl,'shuoming')#}</script>
                    </div>

                    <table class="table table-primary" id="menu-table" style="margin: 20px 0;">
                        <thead>
                            <tr>
                                <th>策略名称</th>
                                <th>奖励积分</th>
                                <th>备注</th>
                            </tr>
                        </thead>
                        <tbody class="integral">
                            <tr>
                                <td><span>每日签到奖励</span></td>
                                <td class="align-center"><input type="text" name="meiri" value="{#value($jfcl,'meiri')#}"/></td>
                                <td><span>一天只能签到一次</span></td>
                            </tr>
                            <tr>
                                <td><span>连续</span><input style="width: 50px; margin: 0 10px;" type="text" name="lianxu" value="{#value($jfcl,'lianxu')#}"/><span>天签到额外奖励</span></td>
                                <td class="align-center"><input type="text" name="lianxuval" value="{#value($jfcl,'lianxuval')#}"/></td>
                                <td><span>今日如不连续签到，则重新计算</span></td>
                            </tr>
                            <tr>
                                <td><span>消费</span><input style="width: 50px; margin: 0 10px;" type="text" name="xiaofei" value="{#value($jfcl,'xiaofei')#}"/><span>元奖励</span>
                                </td>
                                <td class="align-center"><input type="text" name="jiangli" value="{#value($jfcl,'jiangli')#}"/></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>

                    <table class="table table-primary" id="menu-table" style="margin: 20px 0;">
                        <thead>
                        <tr>
                            <th colspan="3">积分关键词</th>
                        </tr>
                        </thead>
                        <tbody class="integral">
                        <tr>
                            <td width="120"><span>签到关键词</span></td>
                            <td><input type="text" name="keys" value="{#value($jfcl,'keys')#}" style="width:362px;" placeholder="留空不启用"/></td>
                            <td><span>留空不启用；多个请用英文逗号“,”隔开。</span></td>
                        </tr>
                        <tr>
                            <td><span>签到成功提示语</span></td>
                            <td><input type="text" name="oktis" value="{#value($jfcl,'oktis')#}" style="width:362px;" placeholder="留空默认：签到成功啦！你已获得#签到积分#积分。"/></td>
                            <td><span>例如：签到成功啦！你已获得#签到积分#积分。</span></td>
                        </tr>
                        <tr>
                            <td><span>签到失败提示语</span></td>
                            <td><input type="text" name="notis" value="{#value($jfcl,'notis')#}" style="width:362px;" placeholder="留空默认：您今天已经签到过了！明天再来吧！"/></td>
                            <td><span>例如：您今天已经签到过了！明天再来吧！</span></td>
                        </tr>
                        </tbody>
                    </table>

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
        UE.getEditor('shuoming', {autoHeightEnabled:false});
    });
</script>

{#template("footer")#}

</body>
</html>