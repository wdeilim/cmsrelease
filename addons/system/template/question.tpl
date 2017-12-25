
<!DOCTYPE html>
<html>
<head lang="zh-CN">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>常见问题 - {#$BASE_NAME#}</title>
    <link rel="stylesheet" href="{#$CSS_PATH#}normalize.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}global.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}alert.css"/>
    <link rel="stylesheet" href="{#$CSS_PATH#}font-awesome.css"/>
    <script type="text/javascript" src="{#$JS_PATH#}jquery-1.11.0.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.alert.js"></script>
    <script type="text/javascript" src="{#$JS_PATH#}jquery.form.min.js"></script>
</head>
<body>
{#include file="header_user.tpl"#}
<script>$('#head-nav-menu li:eq(1)').addClass('active');</script>



<div class="wrapper">
    <div class="breadcrumb">
        <div class="items">
            <a href="{#$urlarr.2#}">平台首页</a>
            <i class="iconfont">&#xe621;</i>
            <span>常见问题</span>
        </div>
    </div>
    <div class="main bulletin-list">
        <div class="hd">
            <h1 class="title">常见问题</h1>
            <div class="control">
                <a href="{#$urlarr.2#}" class="button button-primary button-rounded button-small">返回首页</a>
            </div>
        </div>
        <div class="bd">
            <ul>
				{#ddb_pc set="数据表:question,列表名:lists,显示数目:100,排序:inorder desc>indate desc"#}
				{#foreach from=$lists item=list#}
					<li id="question">
						<a data-id="{#$list.id#}" href="javascript:void(0);" class="cf">
							<h3>{#$list.title#}</h3>
							<span>{#$list.indate|date_format:"%Y-%m-%d"#}</span>
						</a>
						<div id="section-{#$list.id#}" class="section section-hide">
							{#$list.content#}
						</div>
					</li>
				{#foreachelse#}
					<li class="nolist">列表无</li>
				{#/foreach#}
            </ul>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
		$("#question a").click(function(){
			$("#question div").hide();
			$("#question #section-"+$(this).attr("data-id")).slideToggle();
		});
	});
</script>

{#include file="footer.tpl"#}

</body>
</html>